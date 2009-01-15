<?php

class UsNa {
  private $groups = array(1);
  public $userid = null;
  public $username = null;
  public $db = null;
  private $lastmessage = '';

  public function UsNa(&$db) {
    $this->db = &$db;
  }

  public function isadmin() {
    if ($this->userid == 1) return true;
    return false;
  }

  public function mltoDocument($menu, &$document, &$e) {
    $this->db->query("
      SELECT * 
      FROM `{$this->db->pref}folders` 
      WHERE `parent`=$menu
      ORDER BY `order`
    ");
    while($dbr = $this->db->fetch()) 
      if ($this->isadmin() || in_array($dbr['group'],$this->groups)) {
        $div = $document->createElement('div');
        $div->setAttribute('menu.id',$dbr['id']);
        if ($this->isadmin()) {
          $div->setAttribute('menu.fname',$dbr['filename']);
          $div->setAttribute('menu.name',$dbr['menuname']);
          $div->setAttribute('menu.group',$dbr['group']);
        }
        $a = $document->createElement('a');
        $a->appendChild($document->createTextNode($dbr['menuname']));
        $a->setAttribute('href','javascript: ;');
        $div->appendChild($a);
        $div->setAttribute('class','sm');
        $e->appendChild($div);
      } 

    if ($this->isadmin()) {
      $div = $document->createElement('div');
      $a = $document->createElement('a');
      $a->appendChild($document->createTextNode('+ menu'));
      $a->setAttribute('href','javascript: ;');
      $div->appendChild($a);
      $div->setAttribute('class','smi');
      $e->appendChild($div);
    }

    $this->db->query("
      SELECT * 
      FROM `{$this->db->pref}files` 
      WHERE `folder`=$menu
      ORDER BY `order`
    ");
    while($dbr = $this->db->fetch()) 
      if ($this->isadmin() || in_array($dbr['group'],$this->groups)) {
        $div = $document->createElement('div');
        $div->setAttribute('menu.id',$dbr['id']);
        if ($this->isadmin()) {
          $div->setAttribute('menu.fname',$dbr['filename']);
          $div->setAttribute('menu.name',$dbr['menuname']);
          $div->setAttribute('menu.group',$dbr['group']);
        }
        $a = $document->createElement('a');
        $a->appendChild($document->createTextNode($dbr['menuname']));
        $a->setAttribute('href','?page='.$dbr['id']);
        $a->setAttribute('class','new_menu');
        $div->appendChild($a);
        $div->setAttribute('class','mi');
        $e->appendChild($div);
      } 

    if ($this->isadmin()) {
      $div = $document->createElement('div');
      $a = $document->createElement('a');
      $a->appendChild($document->createTextNode('+ file'));
      $a->setAttribute('href','javascript: ;');
      $a->setAttribute('class','new_file');
      $div->appendChild($a);
      $div->setAttribute('class','smi');
      $e->appendChild($div);
    }

    if ($menu == 0) {
      $div = $document->createElement('div');
      $div->setAttribute('class','smi');
      $div->setAttribute('id','messages');
      $e->appendChild($div);
    }

    $div = $document->createElement('div');
    $div->setAttribute('class','ni');
    $e->appendChild($div);
  }

  public function createuser() {
  }

  public function creategroup() {
  }

  public function usertogroup() {
  }

  public function deleteuser($id) {
  }

  public function deletegroup() {
  }

  public function userfromgroup() {
  }

  public function userbyname($username) {
    $this->db->query("SELECT id FROM `{$this->db->pref}users` WHERE name = '".$this->db->escape($username)."'");
    if ($dbr = $this->db->fetch())
      return $dbr[0];
    return null;
  }

  public function ingroup($id) {
    if ($this->isloged()) return in_array($id, $this->groups);
    return false;
  }

  public function isloged() {
    if ($this->userid != null) return true;
    return false;
  }

  public function logout() {
    unset($this->groups);
    $this->groups = array(1);
    $this->userid = null;
    $this->username = null;
  }

  public function login($login, $password) {
    if ($this->isloged()) $this->logout();
    $this->db->query("
      SELECT DISTINCT GU.id, U.id
      FROM `{$this->db->pref}users` as U
      INNER JOIN `{$this->db->pref}groupuser` as GU on GU.value = U.id
      WHERE U.name = '".$this->db->escape($login)."' and U.password = password('".$this->db->escape($password)."')
    ");
    for($i = 0; $dbr = $this->db->fetch(); $i++) {
      $this->groups[] = $dbr[0];
      if ($i == 0) {
        $this->userid = $dbr[1];
        $this->username = $login;
      }
    }
    for($i = 0; $i < count($this->groups); $i++) {
      $this->db->query("
        SELECT parent
        FROM `{$this->db->pref}groups`
        WHERE id = {$this->groups[$i]}
      ");
      $dbr = $this->db->fetch();
      if ($dbr[0] == 0) break;
      if (!in_array($dbr[0],$this->groups)) array_push($this->groups, $dbr[0]);
    }
    if (count($this->groups) > 0) return true;
    return false;
  }

  public function addNewMenuLevel($m, $f, $g, $p) {
    $this->db->query("
      INSERT INTO `{$this->db->pref}folders`
      SELECT 
        IFNULL(MAX(`id`),0)+1 as `id`,
        $g as `group`,
        $p as `parent`,
        '$m' as `menuname`,
        '$f' as `filename`,
        IFNULL(MAX(`order`),0)+1 as `order`
      FROM `{$this->db->pref}folders`
    ");

    $this->db->query("SELECT MAX(`id`) FROM `{$this->db->pref}folders`");
    $dbr = $this->db->fetch();

    if (!file_exists($tmpname = $this->folderaddress($dbr[0]))) {
      mkdir($tmpname);
    } else {
      $this->lastmessage = 'Warning: Folder already exists!';
    }


    return $dbr[0];
  }

  public function addNewMenuFile($m, $f, $g, $p) {
    $this->db->query("
      INSERT INTO `{$this->db->pref}files`
      SELECT 
        IFNULL(MAX(`id`),0)+1 as `id`,
        $g as `group`,
        $p as `folder`,
        '$m' as `menuname`,
        '$f' as `filename`,
        IFNULL(MAX(`order`),0)+1 as `order`
      FROM `{$this->db->pref}files`
    ");

    $this->db->query("SELECT MAX(`id`) FROM `{$this->db->pref}files`");
    $dbr = $this->db->fetch();

    if (!file_exists($tmpname = $this->fileaddress($dbr[0]))) {
      $handle = fopen($tmpname, "w");
      fwrite($handle,"<?php\n  //This is automated created file \"$f\" for menu item \"$m\" \n?>\n");
      fclose($handle);
    } else {
      $this->lastmessage = 'Warning: File already exists!';
    }

    return $dbr[0];
  }

  public function remFile($id) {
    $this->db->query("
      DELETE FROM `{$this->db->pref}files`
      WHERE `id` = $id
    ");
  }

  public function remFolder($id) {
    $q = $this->db->query("SELECT `id` FROM `{$this->db->pref}folders` WHERE `parent` = $id");
    while($dbr = $this->db->fetch($q)) $this->remFolder($dbr[0]);
    $q2 = $this->db->query("SELECT `id` FROM `{$this->db->pref}files` WHERE `folder` = $id");
    while($dbr = $this->db->fetch($q2)) $this->remFile($dbr[0]);
    $q3 = $this->db->query("DELETE FROM `{$this->db->pref}folders` WHERE `id` = $id");    
  }

  public function chgFile($id, $m, $f, $g) {
    $this->db->query("SELECT * FROM `{$this->db->pref}files` WHERE `id`=$id");
    $olddata = $this->db->fetch();
    $folderadd = $this->folderaddress($olddata['folder']);

    if ($f != $olddata['filename'])
    if (!file_exists($folderadd.$f)) {
      rename($folderadd.$olddata['filename'],$folderadd.$f);
    } else {
      $this->lastmessage = 'Warning: File already exists!';
    }

    $this->db->query("
      UPDATE `{$this->db->pref}files` 
      SET `filename` = '$f', `menuname` = '$m', `group` = $g
      WHERE `id`=$id");
  }

  public function chgFolder($id, $m, $f, $g) {
    $this->db->query("SELECT * FROM `{$this->db->pref}folders` WHERE `id`=$id");
    $olddata = $this->db->fetch();
    $folderadd = $this->folderaddress($olddata['parent']);

    if ($f != $olddata['filename'])
    if (!file_exists($folderadd.$f)) {
      rename($folderadd.$olddata['filename'],$folderadd.$f);
    } else {
      $this->lastmessage = 'Warning: File already exists!';
    }

    $this->db->query("
      UPDATE `{$this->db->pref}folders` 
      SET `filename` = '$f', `menuname` = '$m', `group` = $g
      WHERE `id`=$id");
  }

  public function folderaddress($folder) {
    $res = '';
    while($folder != -1) {
      $this->db->query("SELECT * FROM `{$this->db->pref}folders` WHERE `id` = $folder");
      if ($dbr = $this->db->fetch()) {
        $res = $dbr['filename'].'/'.$res;
        $folder = $dbr['parent'];
      } else return null;
    }
    return $res;
  }

  public function fileaddress($page) {
    $this->db->query("SELECT * FROM `{$this->db->pref}files` WHERE `id` = $page");        
    if ($dbr = $this->db->fetch())
      return $this->folderaddress($dbr['folder']).$dbr['filename'];
    else return null;
  }

  public function install() {
    $this->db->query("
      CREATE TABLE  `{$this->db->pref}files` (
       `id` INT NOT NULL ,
       `group` INT NOT NULL ,
       `folder` INT NOT NULL ,
       `menuname` VARCHAR( 100 ) NOT NULL ,
       `filename` VARCHAR( 100 ) NOT NULL ,
       `order` INT NOT NULL,
      PRIMARY KEY (  `id` ) ,
      INDEX ( `folder` )
      ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_bin;
    ");
    $this->db->query("
      CREATE TABLE  `{$this->db->pref}folders` (
       `id` INT NOT NULL ,
       `group` INT NOT NULL ,
       `parent` INT NOT NULL,
       `menuname` VARCHAR( 100 ) NOT NULL ,
       `filename` VARCHAR( 100 ) NOT NULL ,
       `order` INT NOT NULL,
      PRIMARY KEY ( `id` )
      ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_bin; 
    ");
    $this->db->query("
      CREATE TABLE  `{$this->db->pref}groups` (
       `id` INT NOT NULL ,
       `parent` INT NOT NULL ,
       `name` VARCHAR( 20 ) NOT NULL ,
      PRIMARY KEY (  `id` )
      ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_bin;
    ");
    $this->db->query("
      CREATE TABLE  `{$this->db->pref}groupuser` (
       `id` INT NOT NULL ,
       `value` INT NOT NULL ,
      PRIMARY KEY (  `id`, `value` )
      ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_bin;
    ");
    $this->db->query("
      CREATE TABLE  `{$this->db->pref}users` (
        `id` INT NOT NULL ,
         `name` VARCHAR( 20 ) NOT NULL ,
         `info` TEXT,
         `password` VARCHAR( 45 ) NOT NULL,
        PRIMARY KEY (  `id` ) ,
        UNIQUE (`name`)
      ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_bin;
    ");
    $this->db->query("
      INSERT INTO `{$this->db->pref}groups` (`id`,`parent`,`name`) 
      VALUES ('1','0','Users'), ('2',  '1',  'Administrators');
    ");
    $this->db->query("
      INSERT INTO  `{$this->db->pref}users` (  `id` ,  `name` ,  `info` ,  `password` ) 
      VALUES ( '1',  'Admin', NULL , PASSWORD(  'admin' )
      );
    ");
    $this->db->query("
      INSERT INTO  `{$this->db->pref}groupuser` (  `id` ,  `value` ) 
      VALUES ('2',  '1');
    ");
    $this->db->query("
      INSERT INTO `{$this->db->pref}folders` (`id` ,`group` ,`parent` ,`menuname` ,`filename` ,`order`)
      VALUES ('0', '1', '-1', 'Main menu', 'menu', '0');
    ");
    $this->db->query("
      INSERT INTO  `{$this->db->pref}files` (  `id` ,  `group`, `folder, `menuname`, `filename`, `order` ) 
      VALUES ('0',  '1', '0','Index file', 'index.php', '0');
    ");

  }

  public function uninstall() {
    $this->db->query("DROP TABLE `{$this->db->pref}files`;");
    $this->db->query("DROP TABLE `{$this->db->pref}folders`;");
    $this->db->query("DROP TABLE `{$this->db->pref}groups`;");
    $this->db->query("DROP TABLE `{$this->db->pref}groupuser`;");
    $this->db->query("DROP TABLE `{$this->db->pref}users`;");
  }

  public function ajax(&$document) {
    if ($_GET['usna'] == 'mm') {
      if (isset($_POST['ml']) && is_numeric($_POST['ml'])) {
        $_SESSION['users']->mltoDocument($_POST['ml'], $document, $document);
      } else if ($this->isadmin() && isset($_POST['addMenu']) && 
                 isset($_POST['m']) && 
                 isset($_POST['f']) && 
                 isset($_POST['g'])  && is_numeric($_POST['g']) &&
                 isset($_POST['menuid']) && is_numeric($_POST['menuid'])) {
        $this->lastmessage = '';
        echo "{";
        echo "\"newid\":\"".$this->addNewMenuLevel($this->db->escape($_POST['m']),$this->db->escape($_POST['f']),$_POST['g'],$_POST['menuid'])."\""; 
        echo ',"message":"'.$this->lastmessage.'"';
        echo "}";
      } else if ($this->isadmin() && isset($_POST['addFile']) && 
                 isset($_POST['m']) && 
                 isset($_POST['f']) && 
                 isset($_POST['g'])  && is_numeric($_POST['g']) &&
                 isset($_POST['menuid']) && is_numeric($_POST['menuid'])) {
        $this->lastmessage = '';
        echo "{"; 
        echo "\"newid\":\"".$this->addNewMenuFile($this->db->escape($_POST['m']),$this->db->escape($_POST['f']),$_POST['g'],$_POST['menuid'])."\""; 
        echo ',"message":"'.$this->lastmessage.'"';
        echo "}";
      } else if ($this->isadmin() && isset($_POST['remFile']) && is_numeric($_POST['remFile'])) {
        $this->lastmessage = '';
        $this->remFile($_POST['remFile']);
        echo "{";
        echo '"message":"'.$this->lastmessage.'"';
        echo "}";
      } else if ($this->isadmin() && isset($_POST['remFolder']) && is_numeric($_POST['remFolder'])) {
        $this->lastmessage = '';
        $this->remFolder($_POST['remFolder']);
        echo "{";
        echo '"message":"'.$this->lastmessage.'"';
        echo "}";
      } else if ($this->isadmin() && isset($_POST['chgFile']) && is_numeric($_POST['chgFile']) &&
                 isset($_POST['m']) &&
                 isset($_POST['f']) &&
                 isset($_POST['g']) && is_numeric($_POST['g'])) {
        $this->lastmessage = '';
        $this->chgFile(
          $_POST['chgFile'], 
          $this->db->escape($_POST['m']), 
          $this->db->escape($_POST['f']), 
          $_POST['g']);
        echo "{";
        echo '"message":"'.$this->lastmessage.'"';
        echo "}";
      } else if ($this->isadmin() && isset($_POST['chgFolder']) && is_numeric($_POST['chgFolder']) &&
                 isset($_POST['m']) &&
                 isset($_POST['f']) &&
                 isset($_POST['g']) && is_numeric($_POST['g'])) {
        $this->lastmessage = '';
        $this->chgFolder($_POST['chgFolder'], $this->db->escape($_POST['m']), $this->db->escape($_POST['f']), $_POST['g']);
        echo "{";
        echo '"message":"'.$this->lastmessage.'"';
        echo "}";
      }
    }
  }
}
?>
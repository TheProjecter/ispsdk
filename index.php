<?php
include('init.php');
$document->loadHTMLFile($CONFIG['template_file']);
$menu = $document->getElementById('menu0');
$title = $document->getElementsByTagName('title')->item(0);
$title->appendChild($document->createTextNode($CONFIG['maintitle'].' - '));
$content = $document->getElementById('content');
$scriptconfig = $document->createElement('script');
$head = $document->getElementsByTagName('head')->item(0);
$head->appendChild($scriptconfig);

if (isset($_POST['login'])) {
  if (!$_SESSION['users']->login($_POST['loginname'],$_POST['password'])) 
    $_SESSION['users']->logout();
}
if (isset($_POST['logout'])) {
  $_SESSION['users']->logout();
}

$_SESSION['users']->mltoDocument(0, $document, $menu);

$menu->parentNode->setAttribute('menu.id','0');

$_SESSION['db']->query("SELECT * FROM `{$_SESSION['db']->pref}groups` ORDER BY `name`");
while($dbr = $_SESSION['db']->fetch()) {
  $option = $document->createElement('option');
  $option->setAttribute('value',$dbr['id']);
  $option->appendChild($document->createTextNode($dbr['name']));
  $document->getElementById('menuElementDialog.g')->appendChild($option);
}

$page = 0;
if (isset($_GET['page']) && is_numeric($_GET['page'])) $page = $_GET['page'];

$address = $_SESSION['users']->fileaddress($page);
if ($address != null) {
  if (file_exists($address)) include($address);
  else {
    $page = 1;
    include($_SESSION['users']->fileaddress(1));
  }
} else {  
  $page = 1;
  include($_SESSION['users']->fileaddress(1));
}

if ($_SESSION['users']->username) {
  $document->getElementById('authDialog_loginname')->setAttribute('value',$_SESSION['users']->username);
  $document->getElementById('authDialog_loginedname')->appendChild($document->createTextNode($_SESSION['users']->username));
} else {
  $document->getElementById('authDialog_loginname')->setAttribute('value','');
  $document->getElementById('authDialog_loginedname')->appendChild($document->createTextNode('Guest'));
}

$scriptconfig->appendChild($document->createTextNode('var isadmin = '.($_SESSION['users']->isAdmin()?'true;':'false;')));
$scriptconfig->appendChild($document->createTextNode('var pagenumber = '.$page.';'));


echo $document->saveHTML();
?>

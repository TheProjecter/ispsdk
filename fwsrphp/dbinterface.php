<?php

abstract class DBInterface {
  public $dbuser = "root";
  public $dbpassword = "";
  public $dbhost = "localhost";
  public $dbname = "";
  public $pref = "";
  public $query = null;
  public $dblink = null;
  public abstract function connect();
  public abstract function select_db();
  public abstract function query($query);
  public abstract function fetch($query = null);
  public abstract function fetcha($query = null);
  public abstract function escape($str);
  private $connected = false;


  public function __construct($dbhost, $dbname, $dbuser, $dbpassword, $dbpref = '') {
    $this->dbhost = $dbhost;
    $this->dbname = $dbname;
    $this->dbuser = $dbuser;
    $this->dbpassword = $dbpassword;
    $this->dbpref = $dbpref;
    $this->connected = false;
  }
}
?>

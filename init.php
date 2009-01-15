<?php
session_start();
include('config.php');
function __autoload($class_name) {
  require_once "fwsrphp/".strtolower($class_name).'.php';
}
include('bases.php');
if (!isset($_SESSION['db']) || !isset($_SESSION['users'])) {
  $_SESSION['db'] = new DBMySQL($CONFIG['dbhost'],$CONFIG['dbname'],$CONFIG['dbuser'],$CONFIG['dbpassword'], $CONFIG['dbpref']);
  $_SESSION['users'] = new UsNa($_SESSION['db']);
} else {
  $_SESSION['db']->connect();
  $_SESSION['db']->select_db();
}

$document = new DOMDocument("1.0","utf-8");
?>

<?php

class DBMySQL extends DBInterface {
  function connect() {
    $this->dblink = mysql_connect($this->dbhost, $this->dbuser, $this->dbpassword);
  }

  function select_db() {
    mysql_select_db($this->dbname, $this->dblink);
  }

  function query($query) {
    if (!$this->connected) {
      $this->connect();
      $this->select_db();
      $this->connected = true;
    }  
    $this->query = mysql_query($query, $this->dblink);
    return $this->query;
  }
   
  function fetch($query = null) {
    if ($query == null) return mysql_fetch_array($this->query);
    else return mysql_fetch_array($query);
  }

  function fetcha($query = null) {
    if ($query == null) return mysql_fetch_assoc($this->query);
    else return mysql_fetch_assoc($query);
  }

  function escape($str) {
    return mysql_escape_string($str);
  }
}
?>

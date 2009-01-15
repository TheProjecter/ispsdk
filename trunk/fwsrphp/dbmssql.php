<?php

class DBMSSQL extends DBInterface {
  public $dbenc = "CP1251";
  public $sysenc = "UTF-8";
  function connect() {
    $this->dblink = mssql_connect($this->dbhost, $this->dbuser, $this->dbpassword);
  }

  function select_db() {
    mssql_select_db($this->dbname, $this->dblink);
  }

  function query($query) {
    if (!$this->connected) {
      $this->connect();
      $this->select_db();
      $this->connected = true;
    }
    $this->query = mssql_query(iconv($this->sysenc, $this->dbenc, $query), $this->dblink);
    return $this->query;
  }

  private function convert($a) {    
    foreach($a as $i => $value) {
      $b[iconv($this->dbenc, $this->sysenc,$i)] = iconv($this->dbenc, $this->sysenc, $value);
    }
    return $b;
  }
   
  function fetch($query = null) {
    if ($query == null) return $this->convert(mssql_fetch_array($this->query));
    else return $this->convert(mssql_fetch_array($query));
  }

  function fetcha($query = null) {
    if ($query == null) return $this->convert(mssql_fetch_assoc($this->query));
    else return $this->convert(mssql_fetch_assoc($query));
  }

  function escape($str) {
    return mssql_escape_string($str);
  }
}
?>

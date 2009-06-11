<?php
class FWSRQuery extends FWSRObject {
  private $query;  
  public function makeresults() {
    $this->db->query($this->query);    
	$mask = null;
    while($dbr = $this->db->fetcha()) {	
      if (!$mask) {
        foreach($dbr as $i => $value) 
          if ($value == null) $mask[$i] = false;
          else $mask[$i] = true;		  
	  } else {
	    foreach($dbr as $i => $value) 
		  if ($value != null && $mask[$i] == false) $mask[$i] = true;
	  }
      $this->result[] = $dbr;
    }    	
    if (count($this->result) == 0) $this->ready = false;	
	foreach($this->result as $i => $iv) {
		foreach($mask as $j => $jv) {
			if (!$jv) unset($this->result[$i][$j]);
		}
	}	
  }

  public function process(&$document, &$container, &$frame) {
    $this->query = $this->depend;    
    preg_match_all("/\\$\((([a-zA-Z0-9_\-\.]+)(\>([a-zA-Z0-9_\-\.]+))?)\)/", $this->depend, $a);
    foreach($a[2] as $i => $value) {
      if ($frame->objects[$value]->ready) {
        if ($frame->objects[$value]->show || $value == $frame->AjObject) $this->show = true;
        if ($a[4][$i] == '') $this->query = str_replace($a[0][$i], $frame->objects[$value], $this->query);                
        else $this->query = str_replace($a[0][$i], $frame->objects[$value]->result[$a[4][$i]], $this->query);                
      } else {
        $this->ready = false;
        break;
      }
    }
    if ($this->ready) $this->makeresults();
  }
}
?>
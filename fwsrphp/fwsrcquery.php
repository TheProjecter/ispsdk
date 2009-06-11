<?php
class FWSRCQuery extends FWSRQuery {
  public function makeresults() {
    $this->db->query($this->query);    
	$maskx = array();
	$masky = array();
	$this->result = array();	
    while($dbr = $this->db->fetch()) {
		if (!isset($this->result[$dbr[0]])) $this->result[$dbr[0]] = array();
		$this->result[$dbr[0]][$dbr[1]] = $dbr[2];
		if (!isset($maskx[$dbr[1]])) {
			if ($dbr[2] == null) $maskx[$dbr[1]] = false;
			else $maskx[$dbr[1]] = true;
		} else {
			if (!$maskx[$dbr[1]] && $dbr[2] != null) $maskx[$dbr[1]] = true;
		}
		if (!isset($masky[$dbr[0]])) {
			if ($dbr[2] == null) $masky[$dbr[0]] = false;
			else $masky[$dbr[0]] = true;		
		} else {
			if (!$masky[$dbr[0]] && $dbr[2] != null) $masky[$dbr[0]] = true;		
		}
	}
	foreach($masky as $y => $yv) {
		if (!$yv) unset($this->result[$y]);
		else {
			foreach($maskx as $x => $xv) 
				if (!$xv) unset($this->result[$y][$x]);
		}
	}
  }
}
?>
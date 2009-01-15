<?php
class FWSRQuery extends FWSRObject {
  private $query;
  public function makeresults() {
    $this->db->query($this->query);    
    while($dbr = $this->db->fetcha()) {
      $this->result[] = $dbr;
    }    
    if (count($this->result) == 0) $this->ready = false;
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
<?php
class FWSRSelectMenu extends FWSRObject {
  const state = 1;
  function render(&$document, &$container, &$frame) {
    $select = $document->createElement('select');
    $container->appendChild($select);
    if (is_array($this->depend[0])) {
      $select->setAttribute('class','2d');
      foreach($this->depend as $i => $ar1) {
        $option = $document->createElement('option');
        $val1 = false;
        $val2 = false;
        $current = false;
        foreach($ar1 as $j => $value) {
          if (!$val1) {
            $option->setAttribute('value',$value);
            $val1 = true;
            if (!$this->result['value'] || $this->result['value'] == $value) {
              $option->setAttribute('selected','');
              $this->result['value'] = $value;
              $current = true;
            }
            continue;
          }
          if (!$val2) {
            $option->appendChild($document->createTextNode($value));
            $val2 = true;
            if ($current) 
              $this->result['valuetitle'] = $value;
            continue;
          }
          $current = false;
          break;
        }
        $select->appendChild($option);
      }      

    } else {
      $select->setAttribute('class','1d');
      foreach($this->depend as $i => $value) {
        if (!$this->result['value']) {
          $this->result['value'] = $i;
          $this->result['valuetitle'] = $value;
          $option->setAttribute('selected','');
        } else if ($this->result['value'] == $i) {
          $this->result['valuetitle'] = $value;
          $option->setAttribute('selected','');
        }

        $option = $document->createElement('option');
        $option->setAttribute('value',$i);
        $option->appendChild($document->createTextNode($value));
        $select->appendChild($option);
      }      
    }
  }
  function process(&$document, &$container, &$frame) {
    if ($frame->objects[$this->depend]->show || 
        $frame->objects[$this->depend]->getName() == $frame->AjObject) 
          $this->show = true;
    $this->ready = $frame->objects[$this->depend]->ready;
    $this->depend = &$frame->objects[$this->depend]->result;
    if ($this->ready) {      
      //when object ready to render (by depends) render it
      if (isset($_POST['fwsr_'.$this->getName()])) {
        $this->result['value'] = $_POST['fwsr_'.$this->getName()];
      } else {
        $this->result['value'] = false;
      }
      $this->render($document, $container, $frame);
    } else{
      //when object non ready to render write it
    }
  }
}
?>
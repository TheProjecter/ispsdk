<?php
class FWSRTable extends FWSRObject {
  const state = 1;
  function render(&$document, &$container, &$frame) {
    $table = $document->createElement('table');
    $container->appendChild($table);
    if (is_array($this->depend[0])) {
      $table->setAttribute('class','2d');
      $table->setAttribute('cellpadding', '0');
      $table->setAttribute('cellspacing', '0');
      $odd = true;
      $header = true;
      foreach($this->depend as $i => $ar1) {
        if ($header) {
          $tr = $document->createElement('tr');
          $tr->setAttribute('class','head');
          $table->appendChild($tr);
          foreach($ar1 as $j => $value) {
            $th = $document->createElement('th');
            $th->appendChild($document->createTextNode($j));
            $tr->appendChild($th);
          }          
          $header = false;
        }
        $tr = $document->createElement('tr');
        $tr->setAttribute('class',$odd?'odd':'even');
        $table->appendChild($tr);
        foreach($ar1 as $j => $value) {
          $td = $document->createElement('td');
          $td->appendChild($document->createTextNode($value));
          $tr->appendChild($td);
        }
        $odd = !$odd;
      }      

    } else {
      $table->setAttribute('class','1d');
      $odd = true;
      foreach($this->depend as $i => $value) {
        $tr = $document->createElement('tr');
        $tr->setAttribute('class',$odd?'odd':'even');
        $table->appendChild($tr);
        $th = $document->createElement('th');
        $th->appendChild($document->createTextNode($i));
        $tr->appendChild($th);
        $td = $document->createElement('td');
        $td->appendChild($document->createTextNode($value));
        $tr->appendChild($td);
        $odd = !$odd;
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
      if ($this->show) $this->render($document, $container, $frame);
    } else{
      //when object non ready to render write it
    }
  }
}
?>
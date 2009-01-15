<?php
class FWSRCell extends FWSRObject {
  function render(&$document, &$container, &$frame) {
    $container->appendChild($document->createTextNode($this->depend));
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
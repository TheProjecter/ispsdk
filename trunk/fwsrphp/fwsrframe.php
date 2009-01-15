<?php
class FWSRFrame {
  private $document;
  private $framenode;
  public $AjObject = null; //ajaxable object
  public $objects = array();
  public function FWSRFrame(&$document, &$framenode) {
    $this->document = &$document;
    $this->framenode = $document->createElement('form');
    $framenode->appendChild($this->framenode);
    if (isset($GLOBALS['RenderObjectByAjax'])) $this->AjObject = $GLOBALS['RenderObjectByAjax'];
  }
  public function addObject(&$object) {
    $this->objects[$object->getName()] = &$object;
    $div = $this->document->createElement('div');
    $nulldiv = $this->document->createElement('div');
    $div->setAttribute('id','fwsr.'.$object->getName());
    $div->setAttribute('class',get_class($object));    
    if ($this->AjObject != null)
      $object->show = false;
    $this->objects[$object->getName()]->process($this->document, $div, $this);
    if ($object->show) $this->framenode->appendChild($div);

  }
}
?>
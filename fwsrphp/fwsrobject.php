<?php
abstract class FWSRObject {
  public $depend;
  public $result = array();
  private $name = '';
  protected $db;
  public $ready = true;
  public $show = true;
  const state = 0; //0 - static, 1 - dynamic

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  abstract function process(&$document, &$container, &$frame);

  public function FWSRObject($name, $depend, &$db = null) {
    $this->setName($name);
    $this->depend = &$depend;
    $this->db = &$db;    
  }
}
?>
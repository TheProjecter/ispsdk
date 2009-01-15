<?php
class FWSRArray extends FWSRObject {
  function process(&$document, &$container, &$frame) {
    $this->result = &$this->depend;
  }
}
?>
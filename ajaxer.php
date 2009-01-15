<?php
include('init.php');

if (isset($_GET['usna'])) {
  $_SESSION['users']->ajax($document);
}

if (isset($_GET['page']) && isset($_GET['elem']) && is_numeric($_GET['page'])) {
  $address = $_SESSION['users']->fileaddress($_GET['page']);
  if ($address != null) {
    if (file_exists($address)) {
      $title = $document->createElement('title');
      $content = $document->createElement('content');
      $document->appendChild($content);
      $RenderObjectByAjax = $_GET['elem'];
      include($address);
    }
  }  
}
echo $document->saveHTML();
?>

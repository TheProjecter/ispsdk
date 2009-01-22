<?php
$title->appendChild($document->createTextNode('index page'));
$frame = new FWSRFrame($document, $content);
$frame->addObject(new FWSRQuery('groups_q',"SELECT id,name FROM groups ORDER BY name", $_SESSION['db']));
$frame->addObject(new FWSRSelectMenu('groups_s','groups_q'));
$frame->addObject(new FWSRQuery('users_q',"SELECT *, 1 as test FROM groupuser inner join users on groupuser.value = users.id and groupuser.id = $(groups_s>value)",$_SESSION['db']));
$frame->addObject(new FWSRTable('tabletest','users_q'));
?>
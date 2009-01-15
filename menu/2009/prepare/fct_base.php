<?php
$title->appendChild($document->createTextNode('index page'));
$frame = new FWSRFrame($document, $content);
$frame->addObject(new FWSRQuery('reg_count',"
	SELECT     Fct2009_Areas.Col005 as [АТЕ], SUM(CASE WHEN class = '11' THEN 1 ELSE 0 END), SUM(CASE WHEN class = '12' THEN 1 ELSE 0 END)
	FROM         Fct2009_Students INNER JOIN
	                      Fct2009_Schools ON Fct2009_Students.school = Fct2009_Schools.Col004 INNER JOIN
	                      Fct2009_Areas ON Fct2009_Schools.Col010 = Fct2009_Areas.Col004
	GROUP BY Fct2009_Areas.Col005
	ORDER BY Fct2009_Areas.Col005",$mymonbase));
$frame->addObject(new FWSRTable('reg_table','reg_count'));
?>

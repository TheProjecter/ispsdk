<?php
  //This is automated created file "1.php" for menu item "1" 
$title->appendChild($document->createTextNode('index page'));
$frame = new FWSRFrame($document, $content);
$frame->addObject(new FWSRQuery('stat',"
SELECT     [Общее кол-во об-ся], Round(CONVERT(real, 100) * [не явились] / [Общее кол-во об-ся], 2) AS [% не яв-ся], [Кол-во писавших], Round(CONVERT(real, 
                      100) * [5] / [Кол-во писавших], 2) AS [5], Round(CONVERT(real, 100) * [4] / [Кол-во писавших], 2) AS [4], Round(CONVERT(real, 100) 
                      * [3] / [Кол-во писавших], 2) AS [3], Round(CONVERT(real, 100) * [2] / [Кол-во писавших], 2) AS [2], Round(CONVERT(real, 100) * ([5] + [4] + [3]) 
                      / [Кол-во писавших], 2) AS [Усп-ть], Round(CONVERT(real, 100) * ([5] + [4]) / [Кол-во писавших], 2) AS [Кач-во], [Средний балл]
FROM         (SELECT     COUNT(*) AS [Общее кол-во об-ся], SUM(CASE WHEN rate IS NULL THEN 1 ELSE 0 END) AS [не явились],
                                              SUM(CASE WHEN rate IS NOT NULL THEN 1 ELSE 0 END) AS [Кол-во писавших], SUM(CASE WHEN rate = 5 THEN 1 ELSE 0 END) AS [5], 
                                              SUM(CASE WHEN rate = 4 THEN 1 ELSE 0 END) AS [4], SUM(CASE WHEN rate = 3 THEN 1 ELSE 0 END) AS [3], 
                                              SUM(CASE WHEN rate = 2 THEN 1 ELSE 0 END) AS [2], Round(AVG(rate), 2) AS [Средний балл]
                       FROM          [dbo].[Table]
                       WHERE      subject = 'Русский язык') r ",$Nine2009));
$frame->addObject(new FWSRTable('tabletest','stat'));
?>

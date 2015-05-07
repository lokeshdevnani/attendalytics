<?php

require_once 'functions/database.php';
require_once 'functions/Attendance.php';

/*
 * Some validation to do now.
 *
 */

$p = $db->prepare("SELECT firstname FROM first ORDER BY rand() LIMIT 240");
$p->execute();
$firstnames = $p->fetchAll(PDO::FETCH_COLUMN,'firstname');
sort($firstnames);

$p = $db->prepare("SELECT lastname FROM last ORDER BY rand() LIMIT 240");
$p->execute();
$lastnames = $p->fetchAll(PDO::FETCH_COLUMN,'lastname');

$p = $db->prepare("INSERT INTO students (name,classId,remarks) VALUES(?,?,?)");
$remarks = ["Very Poor","Good","Excellent","Very Good","Average Student"];
for($i=0;$i<240;$i++){
    echo $name = $firstnames[$i]." ".$lastnames[$i];
    echo "<br>";
    $p->execute(array($name,intval($i/68)+1,$remarks[rand(0,4)]));
}



$classId = 2;
$subjectId = 2;
$att = new Attendance($db);
//echo json_encode($json);
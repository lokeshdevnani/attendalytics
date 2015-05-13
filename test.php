<?php

require_once 'functions/database.php';
require_once 'functions/Attendance.php';

$p = $db->prepare("SELECT firstname FROM first ORDER BY rand() LIMIT 100");
$p->execute();
$firstnames = $p->fetchAll(PDO::FETCH_COLUMN,'firstname');

$p = $db->prepare("SELECT lastname FROM last ORDER BY rand() LIMIT 100");
$p->execute();
$lastnames = $p->fetchAll(PDO::FETCH_COLUMN,'lastname');

$r = $db->prepare("INSERT INTO teachers(name) VALUES(?)");
for($i=1;$i<=40;$i++){
    echo $name  = $firstnames[$i]." ".$lastnames[$i];
    //$r->execute(array($name));
}
$query = $db->prepare("INSERT INTO subjectteachers(classId,subjectId,teacherId) VALUES (?,?,?)");
for($c=1;$c<=4;$c++){
    for($s=1;$s<=6;$s++){
        $query->execute(array($c,$s,rand(1,10)));
    }
}

die();


die();
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
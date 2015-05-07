<?php

require_once 'functions/database.php';
require_once 'functions/Attendance.php';

$att = new Attendance($db);
$date = new DateTime('2015-03-01 08:30:00');
for($i=0;$i<300;$i++){
    $classId = rand(1,4);
    $subjectId = rand(1,6);
    $teacherId = rand(1,20);
    $abCount = rand(rand(0,10),rand(5,40));
    $absentees = array();
    for($j=0;$j<$abCount;$j++){
        $absentees[] = 68*($classId-1) + rand(1,68);
    }
    $date->add(new DateInterval('PT5H'));
    $time =  $date->format('Y-m-d H:i:s') . "\n";
    $mix = ["classId"=>$classId,'subjectId'=>$subjectId,'teacherId'=>$teacherId,"absentees"=>$absentees,"time"=>$time];
    pr($mix);

    $att->upload($classId,$subjectId,$teacherId,$time,$absentees);
}
die();
$att->upload($classId,$subjectId,$teacherId,$time,$absentees);
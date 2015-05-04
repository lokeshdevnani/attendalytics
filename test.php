<?php

require_once 'functions/database.php';
require_once 'functions/Attendance.php';

/*
 * Some validation to do now.
 *
 */


$classId = 2;
$subjectId = 2;
$att = new Attendance($db);
$result = array();
$values = array();

$lectureList = $att->getAllLecturesForClass($classId,$subjectId);
$lectureCount = count($lectureList);
$att->getRollRange($classId);

$rollStart = $att->getRollStart();
$rollEnd = $att->getRollEnd();
$totalStudents = $rollEnd - $rollStart + 1;

foreach($lectureList as $index=>$lecture){
    $lec = $att->getByLectureId2($lecture->id);
    $values[] = $lec;
    $lectureList[$index]->present = $totalStudents - $att->getAbsenteeCount();
}

//pr($values);
//die();
//echo "<pre>";
//echo (json_encode($values));
//echo "</pre>";
//pr($att->getByLectureId(27));
//$att->getByLectureId2();
//pr($att->getNamesForClass($classId));

for($i=0;$i<=$totalStudents-1;$i++)
{
    $t = array();
    $t['P'] = 0;
    foreach($values as $value) {
        $t[] = $value[$i];
        $value[$i]=='P' && $t['P']++;

    }
    $t['name'] = rand(5,99);
    $t['roll'] = $i+$rollStart;
    $result[$i]  = $t;
}

$json['data']= $result;
$json['lectureCount'] = $lectureCount;
$json['lectureList']= $lectureList;
echo json_encode($json);
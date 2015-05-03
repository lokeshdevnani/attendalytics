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

foreach($lectureList as $index=>$lecture){
    $lec =  $att->getByLectureId2($lecture->id);
    $values[] = $lec;
    $lectureList[$index]->absent= $att->getAbsenteeCount();
}
//pr($values);
//pr($values);
//pr($values);
//pr($values);

//pr($values);
//die();
//echo "<pre>";
//echo (json_encode($values));
//echo "</pre>";
//pr($att->getByLectureId(27));
//$att->getByLectureId2();


for($i=0;$i<=2&&$rollEnd-$rollStart;$i++)
{
    $t = array();
    foreach($values as $value) {
        $t[] = $value[$i];
    }
    $t['name'] = "Lokesh";
    $result[$i]  = $t;
}

$json['data']= $result;
$json['lectureCount'] = $lectureCount;
$json['lectureList']= $lectureList;
echo json_encode($json);
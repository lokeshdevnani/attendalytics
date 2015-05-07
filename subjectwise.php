<?php

require_once 'functions/database.php';
require_once 'functions/Attendance.php';

/*
 * Some validation to do now.
 *
 */
if(isset($_GET['classId']) && isset($_GET['subjectId'])){
    if(!empty($_GET['classId']) && !empty ($_GET['subjectId'])){
        $class = $_GET['class'];
        $subject= $_GET['subject'];
        echo "<span class=hidden id=class>$class</span>";
        echo "<span class=hidden id=subject>$subject</span>";
    }
}


$classId = 2;
$subjectId = 2;
$att = new Attendance($db);
$result = array();
$dateColumns = array();
$summary = array();


$lectureList = $att->getAllLecturesForClass($classId,$subjectId);
$lectureCount = count($lectureList);

$att->getRollRange($classId);
$rollStart = $att->getRollStart();
$rollEnd = $att->getRollEnd();
$totalStudents = $rollEnd - $rollStart + 1;

foreach($lectureList as $index=>$lecture){
    $dateColumns[] = $att->getByLectureId2($lecture->id);
    $lectureList[$index]->present = $totalStudents - $att->getAbsenteeCount();
}
$names = $att->getStudentNames($classId);

for($i=0;$i<$totalStudents;$i++)
{
    // filling a record with student's name,rollno and {P/A} for all dates in LectureList
    $t = array();
    $t['P'] = 0;
    foreach($dateColumns as $value) {
        $t[] = $value[$i];
        $value[$i]=='P' && $t['P']++;      // if present... increment the present counter for that student
        // the above line tracks the no. of P's of the $i'th student
    }
    $t['name'] = $names[$i];
    $t['roll'] = $i+$rollStart;
    $result[$i]  = $t;   // result for i'th student stored in $result array.
}


$json['summary'] = $att->getSummary($classId,$subjectId);
$json['data']= $result;
$json['lectureList']= $lectureList;
echo json_encode($json);
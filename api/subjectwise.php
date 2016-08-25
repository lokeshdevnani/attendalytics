<?php

require_once 'functions/database.php';
require_once 'functions/Attendance.php';
require_once 'functions/Auth.php';

/*
 * Some validation to do now.
 *
 */
$classId=0;
$subjectId=0;

$att = new Attendance($db);
$auth = new Auth($db);
$err = array();
$result = array();
$dateColumns = array();

if( isset($_GET['classId']) && isset($_GET['subjectId']) && !empty($_GET['classId']) && !empty($_GET['subjectId'])){
    $classId = $_GET['classId'];
    $subjectId= $_GET['subjectId'];
    if(is_numeric($classId) && is_numeric($subjectId)) {
        $summary = $att->getSummary($classId,$subjectId);
        if(!$summary)
            $err['error'] = "Class or subject is incorrect";
    } else {
        $err['error'] = "Invalid Data.";
    }
} else {
    $err['error'] = "Sorry, No data recieved";
}
if(!empty($err)) die(json_encode($err));

$login = $auth->isOK();

if(!$login || !$auth->isAllowed($classId,$subjectId))
    $err['error'] = "Sorry, you are not allowed to view this attendance sheet";

if(!empty($err)) die(json_encode($err));

$lectureList = $att->getAllLecturesForClass($classId,$subjectId);
$lectureCount = count($lectureList);
//if(count)

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

$json['login'] = $login;
$json['summary'] = $summary;
$json['data']= $result;
$json['lectureList']= $lectureList;
echo json_encode($json);
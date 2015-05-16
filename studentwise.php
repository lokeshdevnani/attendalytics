<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';

$att = new Attendance($db);

$classId = 0;
$rollno = 0;
$info = 0;

if(isset($_GET['class']) && !empty($_GET['class']) && is_numeric($_GET['class'])
&&(isset($_GET['rollno']) && !empty($_GET['rollno']) && is_numeric($_GET['rollno'])))
{
    $classId = $_GET['class'];
    $rollno = $_GET['rollno'];
    $info = $att->getStudentInfo($classId,$rollno);
    if(empty($info)) dje("Student information not available");
} else {
    dje("Student could not be found.");
}

$subjectNames = $att->getSubjectNames($classId);
($lectures = $att->studentReport($rollno,$classId));
$dates = array();

$k = 0;
$dates[$k][$lectures[0]['subjectId']] = $lectures[0];
$dates[$k]['date'] = $lectures[0]['date'];

for($i=1;$i<count($lectures);$i++){
    if($lectures[$i-1]['date']==$lectures[$i]['date']){
        $dates[$k][$lectures[$i]['subjectId']] = $lectures[$i];
    } else {
        $k++;
        $dates[$k][$lectures[$i]['subjectId']] = $lectures[$i];
        $dates[$k]['date'] = $lectures[$i]['date'];
    }
}



//pr($dates);
$json['info'] = $info;
$json['subjects'] = $subjectNames;
$json['dates'] = $dates;
//echo "<script>console.log(";
echo json_encode($json);
//echo ")</script>";

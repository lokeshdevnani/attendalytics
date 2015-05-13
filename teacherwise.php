<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';

$att = new Attendance($db);
$teacherId = 0;
if(isset($_GET['teacher']) && !empty($_GET['teacher']) && is_numeric($_GET['teacher'])){
    $teacherId = $_GET['teacher'];
} else{
    dje("Unknown");
}
$info = $att->getTeacherInfo($teacherId);
if(empty($info)){
    dje("No details found");
}
$report = $att->teacherReport($teacherId);
$json['info'] = $info;
$json['classes'] = $att->getTeacherClasses($teacherId);
$json['lectures'] = $report;
die(json_encode($json));
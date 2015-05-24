<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';
require_once 'functions/Auth.php';

$att = new Attendance($db);
$auth = new Auth($db);
$teacherId = 0;
$login = 0;
if(isset($_GET['teacher']) && !empty($_GET['teacher']) && is_numeric($_GET['teacher'])){
    $teacherId = $_GET['teacher'];
    $login = $auth->isLogged();
    if($login && $auth->isAllowedTeacherwise($teacherId));else
        dje("Sorry, you are not allowed to view this record");
} else{
    dje("Unknown");
}
$info = $att->getTeacherInfo($teacherId);
if(empty($info)){
    dje("No details found");
}
$report = $att->teacherReport($teacherId);
$json['login'] = $login;
$json['info'] = $info;
$json['classes'] = $att->getTeacherClasses($teacherId);
$json['lectures'] = $report;
die(json_encode($json));
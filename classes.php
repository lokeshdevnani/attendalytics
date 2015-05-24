<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';
require_once 'functions/Auth.php';

$att = new Attendance($db);
$auth = new Auth($db);
$sem=0;
if(isset($_GET['sem']) && !empty($_GET['sem']) && is_numeric($_GET['sem'])&& isset($_GET['branch']) && !empty($_GET['branch']) ){
    $sem = $_GET['sem'];
    $branch = $_GET['branch'];
} else{
    dje("Please specify a correct sem and branch");
}
$classes = $att->getAllClasses($sem,$branch);

if(empty($classes)) dje("Please specify a correct sem and branch");

$login = $auth->isLogged();
if(!$login || !$auth->isAllowedClasses($branch))
    dje("Sorry, you are not allowed to view this record");

$subjects = $att->getSubjectNames($classes[0]['classId']);
foreach($classes as $classIndex=>$class){
    $classes[$classIndex]['info'] =$att->getLecturesCounts($class['classId']);
}
$json['subjects'] = $subjects;
$json['classes'] = $classes;
die(json_encode($json));
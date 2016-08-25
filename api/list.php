<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';
require_once 'functions/Auth.php';

$att = new Attendance($db);
$auth = new Auth($db);

if(isset($_GET['type']) && !empty($_GET['type']) ){
    $type = $_GET['type'];
    $login = $auth->isOK();
    if(!$login || !$auth->isAllowedList())
        dje("Sorry, you are not allowed to view this list");

    if($type=="classes"){
      $data = $att->getWholeClassList();
    } else if($type == "subjects" && isset($_GET['class']) && !empty($_GET['class'])){
      $data = $att->getSubjectNames($_GET['class']);
    } else if($type == "students" && isset($_GET['class']) && !empty($_GET['class'])){
      $data = $att->getClassStudents($_GET['class']);
    } else {
        dje("Please specify correct type and information");
    }
    die(json_encode(array(
      "login" => $login,
      "data" => $data
    )));
} else{
    dje("Please specify correct type and information");
}

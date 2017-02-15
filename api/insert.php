<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';
require_once 'functions/Auth.php';
session_destroy();

$att = new Attendance($db);
$auth = new Auth($db);

if(isset($_POST)){
    // echo "<pre>" , print_r($_POST,true) , "</pre>";
    extract($_POST);
    $att->logInsertRequest(json_encode($_POST));

    if(isset($classId) && isset($subjectId) && isset($teacherId) && isset($time) && isset($absentees)){
        if(!empty($classId) && !empty($subjectId) && !empty($teacherId) && !empty($time) && !empty($absentees)){
            //validation
            //FIXME: CHECK FOR ABSENTEE ARRAY(SORT) AND DUPLICACY ISSUE
            
            //validation ends

            $login = $auth->isOK();
            if($login && $auth->isAllowedToUpload());else
                dje("Sorry, you are not allowed to upload any record");
            
            $absentees = explode(",",$absentees);
            $time = "2015-04-09 00:00:00";
            $recordId = $att->upload($classId,$subjectId,$teacherId,$time,$absentees);
            if($recordId){
                die(json_encode(array(
                    "success" => true,
                    "id"      => $recordId
                )));
            } else {
                dje("Internal Server Error");
            }
            
        } else {
            dje("Bad Request");
        }
    }  else {
        dje("Bad Request");
    }
}

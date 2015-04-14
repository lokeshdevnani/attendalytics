<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';

$att = new Attendance($db);

if(isset($_REQUEST)){
    echo "<pre>" , print_r($_REQUEST,true) , "</pre>";
    extract($_REQUEST);
    if(isset($classId) && isset($subjectId) && isset($teacherId) && isset($time) && isset($absentees)){
        //upload_time -> NOW()
        $att->display();
        $absentees = explode(",",$absentees);
        $time = "2015-04-08 00:00:00";
        $att->upload($classId,$subjectId,$teacherId,$time,$absentees);


    }
}
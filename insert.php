<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';

$att = new Attendance($db);

$lectureList = $att->getAllLecturesForClass();
echo "<pre>" , print_r($lectureList,true) , "</pre>";
die();

if(isset($_REQUEST)){
    echo "<pre>" , print_r($_REQUEST,true) , "</pre>";
    extract($_REQUEST);
    if(isset($classId) && isset($subjectId) && isset($teacherId) && isset($time) && isset($absentees)){
        //upload_time -> NOW()
        $att->getByLectureId(1);
        $absentees = explode(",",$absentees);
        $time = "2015-04-09 00:00:00";
        echo $att->upload($classId,$subjectId,$teacherId,$time,$absentees);


    }
}
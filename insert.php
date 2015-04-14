<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';

$att = new Attendance($db);
$att->display();

if(isset($_REQUEST)){
    echo "<pre>" , print_r($_REQUEST,true) , "</pre>";
    extract($_REQUEST);

    if(isset($classId) && isset($subjectId) && isset($teacherId) && isset($time) && isset($absentees)){
        //upload_time -> NOW()


    }
}
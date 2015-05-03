<?php

require_once 'functions/database.php';
require_once 'functions/Attendance.php';

$att = new Attendance($db);

//$arr = $att->getByLectureId();
//echo "<pre>",print_r($arr),"</pre>";



/*
rollno
name: lokesh
absents
date1
date2
date3
date4
*/
// get all absentees and do something with their roll numbers
$l = $att->getAllLecturesForClass();

$lectureList['data']['sc'] = $l['id'];

echo json_encode($lectureList);

/*
{
"data": [
{
"name": "Tiger Nixon",
"position": "System Architect",
"salary": "$320,800"
},
{
"name": "Garrett Winters",
"position": "Accountant",
"salary": "$170,750"
}]
}

*/

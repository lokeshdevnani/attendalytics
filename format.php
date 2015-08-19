<h1>API format</h1>

<h2>Uploading a lecture attendance</h2>
<p><strong>WEBSITEADDRESS</strong>/insert.php?classId=<strong>CLASSID</strong>&subjectId=<strong>SUBJECTID</strong>&teacherId=<strong>TEACHERID</strong>&time=<strong>TIME(YYYY-MM-DD hh:mm:ss)</strong>&absentees=<strong>COMMA SEPERATED roll numbers</strong></p>
Example:
http://www.lokeshd.com/attendance/insert.php?classId=2&subjectId=2&teacherId=10&time=2015-04-09 11:40:00&absentees=70,71,75,76,79,80</p>


<h2>Viewing the lecture list</h2>
http://lokeshd.com/attendance/showclasses.php?sem=4&branch=CS

<p>In this table, you can see the no. of lectures incremented in the class and subject in which you have uploaded the attendance.</p>


<h2>Viewing the class-subject register</h2>
Example:
http://lokeshd.com/attendance/show.php?class=2&subject=2
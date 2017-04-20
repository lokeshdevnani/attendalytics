<?php
include_once 'api/functions/database.php';
include_once 'api/functions/Auth.php';

$auth = new Auth($db);
if(!$auth->isLogged()){
  redir('login.php');
};
?>

<html>
<head>
    <title>JECRC Attendance Register</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="css/ion.rangeSlider.css" rel="stylesheet" />
    <link href="css/ion.rangeSlider.skinHTML5.css" rel="stylesheet" />

</head>
<body>
<div class="container">
  <?php
    require_once 'functions/header.php';
  ?>
  <div class="container">
    <div class="col-md-10 col-md-offset-1" style="margin-top: 2em;">
      <div class="panel-group">
        <div class="panel panel-success">
          <div class="panel-heading">Class Register</div>
          <div class="panel-body">
            <form class="form-inline">
              <div class="form-group col-md-3">
                <label for="email">Class:</label>
                <select name="class" id="register_class" class="form-control">
                  <option value="1">Select Class</option>
                </select>
              </div>
              <div class="form-group col-md-5">
                <label for="email">Subject:</label>
                <select name="class" id="register_subject" class="form-control">
                  <option value="1">Select Subject</option>
                </select>
              </div>
              <button type="submit" id="register_view" class="btn btn-success">View Register</button>
            </form>
          </div>
        </div>
        <div class="panel panel-success">
          <div class="panel-heading">Student Report</div>
          <div class="panel-body">
            <form class="form-inline">
              <div class="form-group col-md-3">
                <label for="email">Class:</label>
                <select name="class" id="student_class" class="form-control">
                  <option value="1">Select Class</option>
                </select>
              </div>
              <div class="form-group col-md-5">
                <label for="student_student">Student:</label>
                <select name="student" id="student_student" class="form-control">
                  <option value="1">Select Student</option>
                </select>
              </div>
              <button type="submit" id="student_view" class="btn btn-success">View Student Report</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="table-responsive" id="tableContainer">
     <table class="table table-bordered table-condensed table-hover" cellspacing="0" width="100%" id="sheet">
        <thead>
            <tr>
            <a href="logout.php">Logout</a>
                <th rowspan="2"><i class="glyphicon glyphicon-chevron-down"></i></th>
                <th rowspan="2">Name</th>
                <th rowspan="2">Present</th>
            </tr>
            <tr>
            </tr>
        </thead>
     </table>
  </div>
</div>
<!-- js includes -->
<script src="js/jquery.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/dataTables.bootstrap.js"></script>
<script src="js/ion.rangeSlider.min.js"></script>
<script src="js/custom.js"></script>
<!-- js includes end -->

<link rel="stylesheet" href="css/customize.css" />
</body>
</html>

<script>
    var wholeData;
    var api;
    var classId = 0;

    $(document).ready(function(){

      $.ajax({
        url: "api/list.php",
        dataType : "json",
        data: {
          type: 'classes'
        },
        success : function(result){
          result = result.data;
          for(i=0; i<result.length; i++) {
            $("#register_class").append("<option value='"+result[i].classId+"'>" + result[i].name + "</option>")
            $("#student_class").append("<option value='"+result[i].classId+"'>" + result[i].name + "</option>")
          }
        }
      });

      $("#register_class").change(function(){
        console.log( $(this).val() );
        $.ajax({
          url: "api/list.php",
          dataType : "json",
          data: {
            type: 'subjects',
            class: $(this).val()
          },
          success : function(result){
            result = result.data;
            $("#register_subject").empty();
            for(i=0; i<result.length; i++) {
              $("#register_subject").append("<option value='"+result[i].id+"'>" + result[i].name + "</option>")
            }
          }
        });

        $("#register_view").click(function(e){
          e.preventDefault();
          var $subject = $("#register_subject").val(),
              $class = $("#register_class").val();
          window.location = `show.php?class=${$class}&subject=${$subject}`;
          
          return false;
        });
      });

      $("#student_class").change(function(){
        console.log( $(this).val() );
        $.ajax({
          url: "api/list.php",
          dataType : "json",
          data: {
            type: 'students',
            class: $(this).val()
          },
          success : function(result){
            result = result.data;
            console.log(result);
            $("#student_student").empty();
            for(i=0; i<result.length; i++) {
              $("#student_student").append("<option value='"+result[i].rollno+"'>" + result[i].name + " (" + result[i].rollno + ")</option>")
            }
          }
        });

        $("#student_view").click(function(e){
          e.preventDefault();
          var $student = $("#student_student").val(),
              $class = $("#student_class").val();
          window.location = `bystudent.php?class=${$class}&rollno=${$student}`;
          
          return false;
        });
      });
        




        var Class = $("#class").html();
        var subject = $("#subject").html();
        var params = {classId: Class, subjectId: subject};
        $.ajax({
            url: "api/list.php",
            dataType : "json",
            data: params,
            success : function(result){
                console.log(result);
                if(result.error){
                    $("#tableContainer").addClass('noData').html("<div class=error>"+result.error+"</div>");
                }
                showLoginInfo(result.login);
                // adding the thead headers
                var lectureDates = [];
                var tr2data = "";
                var tr3data = "";
                var totalAtt=0;
                $.each(result.lectureList,function(index,lecture){

                    tr2data += ("<th><a data-id='" + lecture.id + "'>" + formatTime(lecture.time)+" " + "</a></th>");
                    tr3data += ("<th><a>" +lecture.present+ "</a></th>");
                    lectureDates.push(formatTime(lecture.time));
                    totalAtt += lecture.present;
                });
                $("#sheet > thead > tr:first-of-type").append(tr2data);
                $("#sheet > thead > tr:nth-of-type(2)").html(tr3data);

                //return;
                //done !! now just load the rows
                //return;
                wholeData = result;
                loadSummary(totalAtt);
                loadData(result.data);
                createRanger(lectureDates);
            }
        });
    });

</script>


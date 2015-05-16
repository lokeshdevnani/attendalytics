<html>
<head>
    <title>JECRC Attendance Register</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="css/toggle-switch.css" rel="stylesheet" />
</head>
<body>
<div class="container">
    <div class="row headRow">
        <div class="container">
            <h1 class="mainHeading">ATTENDANCE</h1>
            <?php
            if(isset($_GET['teacher']) && !empty($_GET['teacher'])){
                $teacherId = $_GET['teacher'];
                echo "<span class='hidden' id='teacherId'>$teacherId</span>";
            }
            ?>
        </div>
    </div>
    <div class="row detailsRow">
        <div class="container">
            <div class="col-md-4 leftportion">
                <label>Name: </label><span class="teachername"></span>
                <br><label>Teacher id:</label><span class="teacherId"></span>
                <br><label>Remarks:</label><span class="teacherRemarks"></span>
            </div>
            <div class="col-md-4 rightportion">
                <label>Regular Lectures: </label><span class="regularL"></span>
                <br><label title="Lectures taken by teacher which are not part of their regular time table" data-toggle="tooltip">Extra substitutions:</label><span class="takenL"></span>
                <br><label title="Lectures in which the teacher was not present and substituted by another teacher" data-toggle="tooltip">Substituted:</label><span class="subsL"></span>
            </div>
        </div>
    </div>
    <div class="table-responsive" id="tableContainer">
        <table class="table table-bordered table-condensed table-hover" cellspacing="0" width="100%" id="teacherReport">
            <thead>
            <tr>
                <th>View</th>
                <th>Class</th>
                <th>Time</th>
                <th>Subject</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<!-- js includes -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.js"></script>
<script src="js/custom.js"></script>
<!-- js includes end -->
</body>
</html>

<script>
    var wholeData;
    var api;

    // on error scale the tableContainer div from its starting point to bottom by using such a class


    $(document).ready(function(){
        var teacherId = $("#teacherId").html();
        $.ajax({
            url: "teacherwise.php",
            dataType : "json",
            data: {'teacher': teacherId},
            success : function(result){
                console.log(result);
                if(result.error){
                    $("#tableContainer").addClass('noData').html("<div class=error>"+result.error+"</div>");
                    return;
                }
                wholeData = result;
                loadTeacherReport();

                return;
            }
        });
    });


    function loadTeacherReport(){
        var lectures = wholeData.lectures;
        var teacherId = wholeData.info.id;
        var tdata="";
        var subs= 0, taken= 0,regular=0;
        for(i=0;i<lectures.length;i++){
            tdata+="<tr>";

            tdata += ("<td><a href='show.php?class="+lectures[i].classId+"&subject="+lectures[i].subjectId+"'><i class='glyphicon glyphicon-eye-open'>" + "" + "</i></a></td>");
            tdata += ("<td><a>" + lectures[i].className + "</a></td>");
            tdata += ("<td><a class=lecturetime>" + lectures[i].time + " , " + getWeekDay(lectures[i].day) + "</a></td>");
            tdata += ("<td><a>" + lectures[i].subjectName + "</a></td>");
            if(lectures[i].takenBy == lectures[i].takenOf){
                tdata += ("<td><a>" + "Regular Lecture" + "</a></td>");
                regular++;
            } else if(lectures[i].takenBy == teacherId) { //lecture taken by THE_TEACHER
                tdata += ("<td><i class='sfor'>Substituted for</i> <a href='byteacher.php?teacher=" + lectures[i].takenOf + "'>" + lectures[i].teacherName + "</a></td>");
                taken++;
            } else {  // lecture not taken [lecture taken by anyone else ]
                tdata += ("<td><i class='sby'>Taken by</i> <a href='byteacher.php?teacher=" + lectures[i].takenBy + "'>" + lectures[i].teacherName + "</a></td>");
                subs++;
            }
            tdata+="</tr>";
        }

        $("#teacherReport tbody").html(tdata);

        info = wholeData.info;
        var dr = $(".detailsRow");
        dr.find(".teachername").html(info.name);
        dr.find(".teacherId").html(info.id);
        dr.find(".teacherRemarks").html(info.remarks);

        dr.find(".regularL").html(regular);
        dr.find(".takenL").html(taken);
        dr.find(".subsL").html(subs);


        $("#teacherReport tr td:first-of-type a").tooltip({
            title: "Click to view class register",
            container: "body",
            placement: "right"
        });
        $("#teacherReport").dataTable({
            paging : false,
            sDom: "t",
            "columnDefs": [ {
                "targets": 0,
                "width": "50px",
                "bSortable": false
            } ]

        });

        $("[data-toggle=tooltip]").tooltip();
    }

</script>
<link rel="stylesheet" href="css/customize.css" />
<style>
    .sfor{
        color: #a00;
    }
    .sby{
        color: #0a0;
    }
</style>
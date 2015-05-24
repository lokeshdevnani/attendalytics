<html>
<head>
    <title>JECRC Attendance Register</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="css/toggle-switch.css" rel="stylesheet" />
</head>
<body>
<div class="container">
            <?php
            require 'functions/header.php';
            if(isset($_GET['rollno']) && !empty($_GET['class'])){
                $class = $_GET['class'];
                $rollno = $_GET['rollno'];
                echo "<span class='hidden' id='class'>$class</span>";
                echo "<span class='hidden' id='rollno'>$rollno</span>";
            }
            ?>
    </div>
    <div class="row detailsRow">
        <div class="container">
            <div class="col-md-4 leftportion">
                <label>Name: </label><span class="studentname"></span>
                <br><label>Roll No:</label><span class="rollno"></span>
                <br><label>Class:</label><span class="class"></span>
            </div>
            <div class="col-md-4 leftportion">
                <label>Total Classes: </label><span class="classesTotal"></span>
                <br><label>Classes Attended:</label><span class="classesAttended"></span>
                <br><label>Attendance:</label><span class="classesPercent"></span>%
            </div>
            <div class="col-md-4 leftportion">
                <label>Remarks: </label><span class="studentRemarks"></span>
            </div>
        </div>
    </div>
    <div class="table-responsive" id="tableContainer">
        <table class="table table-bordered table-condensed table-hover" cellspacing="0" width="100%" id="studentTable">
            <thead>
            <tr>
                <th rowspan="2">Date</th>
            </tr>
            <tr>

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


    $(document).ready(function(){
        var classId = $("#class").html();
        var rollno = $("#rollno").html();
        $.ajax({
            url: "studentwise.php",
            dataType : "json",
            data: {'class': classId, 'rollno': rollno},
            success : function(result){
                console.log(result);
                if(result.login)
                    showLoginInfo(result.login);
                if(result.error){
                    $("#tableContainer").addClass('noData').html("<div class=error>"+result.error+"</div>");
                }

                var mainth;
                $.each(result.subjects,function(index,subject){
                    mainth += ("<th><a data-id='" + subject.id + "'>" + subject.name + "</a></th>");
                    result.subjects[index].total = 0;
                    result.subjects[index].present = 0;
                });

                $("#studentTable > thead > tr:first-of-type").append(mainth);
                wholeData = result;
                loadStudentReport();

                return;
            }
        });
    });

    function loadStudentReport(){
        var subjects = wholeData.subjects;
        var dates = wholeData.dates;
        var tdata="";
        for(i=0;i<dates.length;i++){
            tdata+="<tr>";

            tdata += ("<td><a class=date href='#'>" + dates[i].date + "</a></td>");
            for(j=0;j<subjects.length;j++){
                tdata+="<td>";
                if(subjects[j].id in dates[i]){
                    subjects[j].total++;

                    if(dates[i][subjects[j].id].status == 1){
                        tdata+= ('<a class=studentStatus href="lecture.php?id=' +dates[i][subjects[j].id].lectureId+ '" data-time="'+dates[i][subjects[j].id].time+'" ><span class="glyphicon glyphicon-ok"></span></a>') ;
                        subjects[j].present++;
                    } else
                        tdata+= ('<a class=studentStatus href="lecture.php?id=' +dates[i][subjects[j].id].lectureId+ '" data-time="'+dates[i][subjects[j].id].time+'" ><span class="glyphicon glyphicon-remove"></span></a>') ;
                }
                tdata+="</td>";
            }
            tdata+="</tr>";
        }
        thdata = "";
        total = 0;
        present = 0;
        for(i=0;i<subjects.length;i++){
            thdata+= ("<th>"+subjects[i].present+"/"+subjects[i].total+" ("+(subjects[i].present*100/subjects[i].total).toFixed(1)+"%)"+"</th>");
            present += subjects[i].present;
            total  += subjects[i].total;
        }

        percent = (present*100/total).toFixed(1);
        info = wholeData.info;
        var dr = $(".detailsRow");
        dr.find(".studentName").html(info.name);
        dr.find(".rollno").html(info.rollno);
        dr.find(".class").html(info.className);
        dr.find(".classesTotal").html(total);
        dr.find(".classesAttended").html(present);
        dr.find(".classesPercent").html(percent);
        dr.find(".studentRemarks").html(info.remarks);


        $("#studentTable thead tr:last-of-type").append(thdata);
        $("#studentTable tbody").html(tdata);
        $("#studentTable").dataTable({
            paging : false,
            sDom: "t",
            "columnDefs": [ {
                "targets": 0,
                "width": "20%"
            } ]

        });
        $("#studentTable .studentStatus").tooltip({
            title: function(){ return $(this).data('time');}
        });

    }

</script>
<link rel="stylesheet" href="css/customize.css" />
<style>
    #sheet .nameRow{
        padding-left: 2.5%;
        text-align: left;
    }
    .classname{
        font-weight: bold;
    }
</style>
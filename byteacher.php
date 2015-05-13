<html>
<head>
    <title>JECRC Attendance Register</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="css/toggle-switch.css" rel="stylesheet" />
</head>
<body>
<div class="container">
    <div class="row" style="background:#08c;">
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
    <div class="row headSubjectR" style="background:#08c;">
        <div class="container">
            <div class="col-md-4 rightportion summaryData variableData">

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
<!-- js includes end -->
</body>
</html>

<script>
    var wholeData;
    var api;

    function formatTime(time){
        var shortMonths= ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var t = time.split(/[- :]/);
        var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
        var dateString = shortMonths[t[1]-1] +" "+ t[2];
        return dateString;
    }

    function formatTimeY(time){
        var shortMonths= ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var t = time.split(/[- :]/);
        var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
        var dateString = t[2]+ " " + shortMonths[t[1]-1] +" "+ t[0];
        return dateString;
    }
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

    function getWeekDay(n){
        n = parseInt(n);
        switch (n){
            case 1: return "Sunday";
            case 2: return "Monday";
            case 3: return "Tuesday";
            case 4: return "Wednesday";
            case 5: return "Thursday";
            case 6: return "Friday";
            case 7: return "Saturday";
        }
    }

    function loadTeacherReport(){
        var lectures = wholeData.lectures;
        var teacherId = wholeData.info.id;
        var tdata="";
        for(i=0;i<lectures.length;i++){
            tdata+="<tr>";

            tdata += ("<td><a href='show.php?class="+lectures[i].classId+"&subject="+lectures[i].subjectId+"'><i class='glyphicon glyphicon-eye-open'>" + "" + "</i></a></td>");
            tdata += ("<td><a>" + lectures[i].className + "</a></td>");
            tdata += ("<td><a class=lecturetime>" + lectures[i].time + " , " + getWeekDay(lectures[i].day) + "</a></td>");
            tdata += ("<td><a>" + lectures[i].subjectName + "</a></td>");
            if(lectures[i].takenBy == lectures[i].takenOf)
                tdata += ("<td><a>" + "Regular Lecture" + "</a></td>");
            else if(lectures[i].takenBy == teacherId)
              tdata += ("<td><a><i>Substituted for</i> " + lectures[i].teacherName + "</a></td>");
            else
                tdata += ("<td><a><i>Substituted by</i> " + lectures[i].teacherName + "</a></td>");
            tdata+="</tr>";
        }

        $("#teacherReport tbody").html(tdata);

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

        return;

    }

    function applyEffects(){

        var tbody = $('#sheet tbody');
        var highlightClass = 'info';

        tbody.on( 'click', 'tr', function () {
            api.$('tr.'+highlightClass).removeClass(highlightClass);
            $(this).addClass(highlightClass);
        }).on('click','td',function(){

            var colIdx = api.cell(this).index().column;
            var rowIdx = api.cell(this).index().row;
            if(colIdx != null && rowIdx != null){
                var columnSet = $(api.column(colIdx).nodes());
                $(api.cells().nodes()).removeClass(highlightClass);
                $(api.column(colIdx).nodes()).addClass(highlightClass);

                //console.log($(this).css('color','white').css('backgroundColor','blue'));
            }
        });
        $(".dataTables_filter input[type=search]").css('margin','0');
    }


    // show classes between rangestopper




</script>
<style>
    #sheet > thead > tr > th[class*="sort"]::after{display: none}
    table.dataTable.table-condensed thead > tr > th {  padding-right: 5px;  }
    body > .container{
        width:auto;
        margin:0;
        padding:0;
    }
    #tableContainer{
        padding:0 15px;
        margin:0 -15px;
    }
    #sheet td, #sheet th{
        font-size: 13px;
        text-align:center;
    }
    #sheet .nameRow{
        padding-left: 2.5%;
        text-align: left;
    }
    .glyphicon-ok{
        color:green;
        font-size: 12px;
    }
    .glyphicon-remove{
        color:red;
        font-size: 12px;
    }
    a{
        color:black;
    }
    .controlSearch,.controlPageLength{
        margin-top:18px;
    }
    .controlsRow{
        padding: 5px;
        margin-bottom: -6px;
        background: #556;
    }
    .custom-menu {
        z-index:1000;
        position: absolute;
        background-color:#C0C0C0;
        border: 1px solid black;
        padding: 2px;
    }
    html{
        overflow-x:hidden;
    }
    .headSubjectR{
        font-size: 15px;
        color: #cccccc;
        padding: 10px 0;
    }
    .headSubjectR span{
        font-weight: bold;
    }
    .mainHeading{
        font-size: 25px;
        text-align: center;
    }
    #tableContainer.noData{
    //position: fixed;
        bottom: 0;
        padding: 20px;
        color:red;

    //height: 100%;
        background: black;
        width:100%;
        margin:0;
    }
    .error{
        top: 50%;
    }





    .classname{
        font-weight: bold;
    }
</style>
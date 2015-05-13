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
            if(isset($_GET['rollno']) && !empty($_GET['class'])){
                $class = $_GET['class'];
                $rollno = $_GET['rollno'];
                echo "<span class='hidden' id='class'>$class</span>";
                echo "<span class='hidden' id='rollno'>$rollno</span>";
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

    $(document).ready(function(){
        var classId = $("#class").html();
        var rollno = $("#rollno").html();
        $.ajax({
            url: "studentwise.php",
            dataType : "json",
            data: {'class': classId, 'rollno': rollno},
            success : function(result){
                console.log(result);
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
                        tdata+= ('<a class=studentStatus href="' +dates[i][subjects[j].id].lectureId+ '" data-time="'+dates[i][subjects[j].id].time+'" ><span class="glyphicon glyphicon-ok"></span></a>') ;
                        subjects[j].present++;
                    } else
                        tdata+= ('<a class=studentStatus href="' +dates[i][subjects[j].id].lectureId+ '" data-time="'+dates[i][subjects[j].id].time+'" ><span class="glyphicon glyphicon-remove"></span></a>') ;
                }
                tdata+="</td>";
                /*
                tdata += ("<td>" +
                    "<div><a class=tname href='" + classes[i].info[j].teacherId +"'>" + classes[i].info[j].teacherName + "</a></div>" +
                    "<div><a class=lcount href='show.php?class="+classes[i].classId+"&subject="+subjects[j].id+"'>"+ classes[i].info[j].lcount +" lectures</a></div></td>");
                */
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
        console.log(present);
        console.log(total);
        console.log(percent);
        $("#studentTable thead tr:last-of-type").append(thdata);


        $("#studentTable tbody").html(tdata);
        $("#studentTable .studentStatus").tooltip({
            title: function(){ return $(this).data('time');}
        });
        $("#studentTable").dataTable({
            paging : false,
            sDom: "t",
            "columnDefs": [ {
                "targets": 0,
                "width": "20%"
            } ]

        });
        $("input[name=teacherToggle]").change(function(){
            val = $(this).val();
            if(val == 'showTeacher'){
                $(".lcount").slideUp().fadeOut('slow');
                $(".tname").slideDown('slow');
            }
            else if(val == 'showLcount'){
                $(".lcount").slideDown('slow');
                $(".tname").slideUp('slow');
            } else {
                $(".lcount").slideDown('slow');
                $(".tname").slideDown('slow');
            }

        });

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
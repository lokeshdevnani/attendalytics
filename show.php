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
  <div class="row headRow">
     <div class="container">
        <h1 class="mainHeading">ATTENDANCE</h1>
         <?php
            if(isset($_GET['class']) && isset($_GET['subject'])){
                if(!empty($_GET['class']) && !empty ($_GET['subject'])){
                    $class = $_GET['class'];
                    $subject= $_GET['subject'];
                    echo "<span class=hidden id=class>$class</span>";
                    echo "<span class=hidden id=subject>$subject</span>";
                }
            }
         ?>
     </div>
  </div>
  <div class="row detailsRow">
      <div class="container">
        <div class="col-md-4 leftportion summaryData">
            Class: <span class="className"></span>
            <br>Subject :<span class="subjectName"></span>
            <br>Teacher: <span class="teacherName"></span>
        </div>
        <div class="col-md-4 middleportion summaryData constantData">
            From <span class="fromDate"></span> to <span class="toDate"></span>
            <br>Total Lectures : <span class="totalDates"></span>
            <br>Total Percentage: <span class="percentAttendance"></span>%
        </div>
        <div class="col-md-4 rightportion summaryData variableData">
            From <span class="fromDate"></span> to <span class="toDate"></span>
            <br>Total Lectures : <span class="totalDates"></span>
            <br>Total Percentage: <span class="percentAttendance"></span>%
        </div>
      </div>
  </div>
  <div class="table-responsive" id="tableContainer">
     <table class="table table-bordered table-condensed table-hover" cellspacing="0" width="100%" id="sheet">
        <thead>
            <tr>
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
<script src="js/dataTables.bootstrap.js"></script>
<script src="js/ion.rangeSlider.min.js"></script>
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
        var Class = $("#class").html();
        var subject = $("#subject").html();
        var params = {classId: Class, subjectId: subject};
        $.ajax({
            url: "subjectwise.php",
            dataType : "json",
            data: params,
            success : function(result){
                console.log(result);
                if(result.error){
                    $("#tableContainer").addClass('noData').html("<div class=error>"+result.error+"</div>");
                }
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
                wholeData = result;
                loadData(result.data);
                loadSummary(totalAtt);
                createRanger(lectureDates);
            }
        });
    });

    function loadSummary(totalAtt){
        var s = wholeData.summary;
        var totalStudents = wholeData.data.length;
        totalDates = wholeData.lectureList.length;
        fromDate = wholeData.lectureList[0].time;
        toDate = wholeData.lectureList[totalDates-1].time;
        avgAttendancePerDay = totalAtt/totalDates;
        var percent = avgAttendancePerDay/totalStudents * 100;
        percent =  percent.toFixed(1);

        $(".summaryData .className").html(s.className);
        $(".summaryData .subjectName").html(s.subjectName);
        $(".summaryData .teacherName").html(s.teacherName);

        $(".summaryData .fromDate").html(formatTimeY(fromDate));
        $(".summaryData .toDate").html(formatTimeY(toDate));
        $(".summaryData .totalDates").html(totalDates);
        $(".summaryData .percentAttendance").html(percent);

    }

    function loadData(data){
        lectureCount = wholeData.lectureList.length;
        var columns = [
            { data: 'roll' },
            { data: 'name',width:"25%",sClass:"nameRow" },
            { data: 'P',"orderDataType": "dom-text-numeric" }
        ];
        for(i=0;i<lectureCount;i++){
            columns.push({data: [i]});
        }
        $.fn.dataTable.ext.order['dom-text-numeric'] = function  ( settings, col )
        {
            return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
                return $(td).html();
            } );
        }
        $('#sheet').dataTable( {
            data: data,
            columns: columns,
            pageLength: 23,
            lengthMenu: [ [10, 23, 50, -1], [10, 23, 50, "All"] ],
            "fnInitComplete": function(oSettings, json) {

            },
            "fnDrawCallback": function( oSettings ) {
                        applyEffects();
            },
            'language': {
                search: '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>',
                searchPlaceholder: 'Search here',
                lengthMenu: '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-list"></span></span>_MENU_</div>'
            },
            columnDefs : [
                {
                    "targets": 2,
                    "render": function(data,type,row,meta){
                        return "<span class='present" + meta.row + "'>" + data + "</span>";
                    }
                },
                {
                "targets": '_all',
                "render": function (data, type, row) {
                    if(data == "P") return '<span class="glyphicon glyphicon-ok"></span>' ;
                    if(data == "A") return '<span class="glyphicon glyphicon-remove"></span>' ;
                    return data;
                }
            } ]
            ,
            "sDom": "<'row controlsRow'<'col-md-4 controlPageLength'l><'col-md-4'<'container dateRanger'>><'col-md-4 controlSearch'f>r>t<'row-fluid'<'span6'i><'span6'p>>"
            //"sDom": '<"H"lfr>t<"F"<"testbuttonarea">ip>'
        });
        api = $("#sheet").DataTable();
        $( "div.testbuttonarea" ).html('<button id="testbutton">Test</button>');
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

    function toggleColumnVisibility(num,show){
        // show-> { show:1,hide:0 }
        var column = api.column(num);
        column.visible(show,false);
    }
    function createRanger(lectureDates){

        $("#dateRanger,.dateRanger").ionRangeSlider({
            type: "double",
            values: lectureDates,
            values_separator: "&nbsp;  to &nbsp;",
            onStart: function (data) {
                //console.log(data);
            },
            onChange : function (data) {

            },
            onFinish : function (data) {
                from = data.from;
                to = data.to;
                console.log("changed from"+from+" to "+to);
//                for(i=3;i<from+3;i++) toggleColumnVisibility(i,false);
//                for(i=from+3;i<=to+3;i++) toggleColumnVisibility(i,true);
//                for(i=to+1+3;i<lectureDates.length+3;i++) toggleColumnVisibility(i,false);
                visiOn = [];
                visiOff = [];
                for(i=3;i<lectureDates.length+3;i++){
                    if(i>=from+3 && i<=to+3) visiOn.push(i);
                    else visiOff.push(i);
                }
                api.columns(visiOff).visible(false,true);
                api.columns(visiOn).visible(true,true);
                api.columns.adjust().draw(true); // improves performance

                updateAttended(from,to);
                updateSummary(from,to);
            },
            onUpdate : function (data) {
                //console.log(data);
            }
        });
    }
    function getUpdatedAttendedValues(from,to){
        var results = wholeData.data;
        var newSum = [];
        for(r=0;r<results.length;r++){
            sum=0;
            for(i=from;i<=to;i++){
                if(results[r][i]=='P') sum++;
            }
            newSum[r]=sum;
        }
        return newSum;
    }

    function updateAttended(from,to){
        var myColumn = api.column(2);
        var newAttended = getUpdatedAttendedValues(from,to);
        /*
        nodes = myColumn.nodes().to$().each(function(index,value){
            $(this).html(newAttended[index]);
        });
        */
        var presents = myColumn.nodes().to$();
        console.log(presents);
        $.each(newAttended,function(index,value){
            presents.find('.present'+index).html(value);
        });
    }

    function getTotalPercentage(from,to){
        var total = 0;
        var totalStudents = wholeData.data.length;
        for(i=from;i<=to;i++)
            total += wholeData.lectureList[i].present;
        var avgAttendancePerDay = total/(to-from+1);
        var percent = avgAttendancePerDay/totalStudents * 100;
        return percent.toFixed(1);
    }

    function updateSummary(from,to){
        totalDates = to-from+1;
        fromDate = wholeData.lectureList[from].time;
        toDate = wholeData.lectureList[to].time;
        percentAttendance = getTotalPercentage(from,to);

        $(".summaryData.variableData .fromDate").html(formatTimeY(fromDate));
        $(".summaryData.variableData .toDate").html(formatTimeY(toDate));
        $(".summaryData.variableData .totalDates").html(totalDates);
        $(".summaryData.variableData .percentAttendance").html(percentAttendance);

    }


// show classes between rangestopper




</script>
<style>
    body > .container{
        width:auto;
        margin:0;
        padding:0;
    }
    .headRow, .detailsRow{
        background:#25253C;
    }
    table.dataTable.table-condensed thead > tr > th {  padding-right: 5px;  }

    #sheet > thead > tr > th[class*="sort"]::after{display: none}

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
    .detailsRow{
        font-size: 15px;
        color: #cccccc;
        padding: 10px 0;
    }
    .detailsRow span{
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
</style>
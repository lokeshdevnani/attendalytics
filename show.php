<body>
<div class="container">

  <div class="row">
     <div class="container">
        <h1>ATTENDANCE</h1>
     </div>
  </div>
  <div class="row">
     <div class="container">
        <div id="dateRanger"></div>
     </div>
  </div>
  <div class="row headSubjectR">
      <div class="container">
        <div class="col-md-3 leftportion summaryData">
            <span class="className"></span>
            <span class="subjectName"></span>
            <span class="teacherName"></span>
        </div>
        <div class="col-md-3 middleportion summaryData constantData">
            <span class="fromDate"></span> to <span class="toDate"></span>
            <span class="totalDates"></span>
            <span class="percentAttendance"></span>
        </div>
        <div class="col-md-3 rightportion summaryData variableData">
            <span class="fromDate"></span> to <span class="toDate"></span>
            <span class="totalDates"></span>
            <span class="percentAttendance"></span>
        </div>
      </div>
  </div>
  <div class="table-responsive">
     <table class="table table-striped table-bordered table-condensed table-hover" cellspacing="0" width="100%" id="sheet">
        <thead>
            <tr>
                <th rowspan="2">Roll</th>
                <th rowspan="2">Name</th>
                <th rowspan="2">Present</th>
            </tr>
            <tr>

            </tr>
        </thead>
     </table>
  </div>
</div>
</body>

<link href="css/bootstrap.min.css" rel="stylesheet" />
<link href="css/dataTables.bootstrap.css" rel="stylesheet" />
<link href="css/ion.rangeSlider.css" rel="stylesheet" />
<link href="css/ion.rangeSlider.skinHTML5.css" rel="stylesheet" />
<script src="js/jquery.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.js"></script>
<script src="js/ion.rangeSlider.min.js"></script>

<script>
    var wholeData;

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
        $.ajax({
            url: "test.php",
            dataType : "json",
            success : function(result){
                console.log(result);
                // adding the thead headers
                var lectureDates = [];
                var tr2data = "";
                var tr3data = "";
                var totalAtt=0;
                $.each(result.lectureList,function(index,lecture){

                    tr2data += ("<th><a>" + formatTime(lecture.time)+" "+ index + "</a></th>");
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
        totalDates = wholeData.lectureList.length;
        fromDate = wholeData.lectureList[0].time;
        toDate = wholeData.lectureList[totalDates-1].time;
        percentAttendance = totalAtt/totalDates;

        $(".summaryData .className").html(s.className);
        $(".summaryData .subjectName").html(s.subjectName);
        $(".summaryData .teacherName").html(s.teacherName);

        $(".summaryData .fromDate").html(formatTimeY(fromDate));
        $(".summaryData .toDate").html(formatTimeY(toDate));
        $(".summaryData .totalDates").html(totalDates);
        $(".summaryData .percentAttendance").html(percentAttendance);

    }

    function loadData(data){
        lectureCount = wholeData.lectureList.length;
        var columns = [
            { data: 'roll' },
            { data: 'name' },
            { data: 'P' }
        ];
        for(i=0;i<lectureCount;i++){
            columns.push({data: [i]});
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
            }
        });
    }

    function applyEffects(){

        var api = $("#sheet").DataTable();
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
    }

    function toggleColumnVisibility(num,show){
        // show-> { show:1,hide:0 }
        var api = $("#sheet").DataTable();
        var column = api.column(num);
        column.visible(show);
    }
    function createRanger(lectureDates){

        $("#dateRanger").ionRangeSlider({
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
                for(i=3;i<from+3;i++) toggleColumnVisibility(i,false);
                for(i=from+3;i<=to+3;i++) toggleColumnVisibility(i,true);
                for(i=to+1+3;i<lectureDates.length+3;i++) toggleColumnVisibility(i,false);
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
        var api = $("#sheet").DataTable();
        var myColumn = api.column(2);
        var newAttended = getUpdatedAttendedValues(from,to);
        nodes = myColumn.nodes().to$().each(function(index,value){
            $(this).html(newAttended[index]);
        });
    }

    function getTotalPercentage(from,to){
        var total = 0;
        for(i=from;i<=to;i++)
            total += wholeData.lectureList[i].present;
        var avgAttendancePerDay = total/(to-from+1);
        var percent = avgAttendancePerDay;
        return percent;
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
    #sheet > thead > tr > th[class*="sort"]::after{display: none}
    table.dataTable.table-condensed thead > tr > th {  padding-right: 5px;  }
</style>
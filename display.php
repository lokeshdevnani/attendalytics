<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';

$att = new Attendance($db);

$lectureList = $att->getAllLecturesForClass();
echo "<pre>",print_r($lectureList),"</pre>";

?>
<table id="example" class="display" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Id</th>
        <th>Teacher Id</th>
        <th>Time</th>
    </tr>
    </thead>

    <tfoot>
    <tr>
        <th>Id</th>
        <th>Teacher Id</th>
        <th>Time</th>
    </tr>
    </tfoot>
</table>

<script src="js/jquery.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<link href="css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="css/jquery.dataTables_themeroller.css" rel="stylesheet" />

<script>
    $(document).ready(function() {
        var columns = [
            {"sTitle": "Label", "sClass" : "header1"},
            {"sTitle": "Type","sClass" : "header2"},
            {"sTitle": "Typed","sClass" : "header2"},
            {"sTitle": "Types","sClass" : "header2"},
            {"bVisible" : false}
        ];
        var dataset = [["P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P"],["P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P","P"]];
        $.ajax({
           url: "test.php",
           dataType: 'json',
           success: function(data){
               dataset = data;
           }
        });

        $("#example thead tr").each(function () {
            //$(this).append('<th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Time: activate to sort column ascending" style="width: 378px;">Time</th>')
        });
        $('#example').on('preXhr.dt',function(e,settings){
            console.log(dataset);
            $("#example thead tr").each(function () {
                $(this).append('<th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Time: activate to sort column ascending" style="width: 378px;">Time</th>')
            });
        }).dataTable( {

             /*
            "columns": [
                { "data": "a" },
                { "data": "b" },
                { "data": "c" }
            ],*/
            data: dataset,
            paging: false

        })
        ;
    } );
</script>

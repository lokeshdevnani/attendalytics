<table cellpadding="0" cellspacing="0" border="0" class="display" id="sheet">
    <thead>
        <tr>
            <th rowspan="3">Roll</th>
            <th rowspan="3">Name</th>
            <th colspan="1" id="th-absent">Absents</th>
        </tr>
        <tr>

        </tr>
        <tr>

        </tr>
    </thead>
</table>

<script src="js/jquery.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<link href="css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="css/jquery.dataTables_themeroller.css" rel="stylesheet" />

<script>
    $(document).ready(function(){
        $.ajax({
            url: "test.php",
            dataType : "json",
            success : function(result){
                console.log(result);
                // add the thead info
                var tr2data = "";
                var tr3data = "";
                $.each(result.lectureList,function(index,lecture){
                    //console.log(lecture);
                    // Split timestamp into [ Y, M, D, h, m, s ]
                    var shortMonths= ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    var t = lecture.time.split(/[- :]/);
                    var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                    var dateString = shortMonths[t[1]-1] +" "+ t[2];

                    tr2data += ("<th><a>" + dateString + "</a></th>");
                    tr3data += ("<th><a>" +lecture.absent+ "</a></th>");
                });
                $("#sheet > thead > tr:first-of-type #th-absent").attr('colspan', result.lectureList.length );
                $("#sheet > thead > tr:nth-of-type(2)").html(tr2data);
                $("#sheet > thead > tr:last-of-type").html(tr3data);

                //done editing the layout
                loadData(result);
            }

        });
    });

    function loadData(data){
        console.log(data);
        $('#sheet').dataTable( {
            data: data.data,
            columns:[
                { data: 'roll' },
                { data: 'name' },
                { data: 'absent' },
                { data: [1]}
            ]

        });
    }



</script>
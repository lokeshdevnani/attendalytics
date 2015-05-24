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
            if(isset($_GET['sem']) && !empty($_GET['sem']) && isset($_GET['branch']) && !empty($_GET['branch'])){
                $sem= $_GET['sem'];
                $branch= $_GET['branch'];
                echo "<span class=hidden id=sem>$sem</span>";
                echo "<span class=hidden id=branch>$branch</span>";
            }
            ?>
    </div>
    <div class="row detailsRow">
        <div class="container">
            <div class="col-md-4 switcher">
                <div class="switch-toggle switch-3 well">
                    <input id="showTeacher" name="teacherToggle" type="radio" value="showTeacher" >
                    <label for="showTeacher">Teacher</label>

                    <input id="showLcount" name="teacherToggle" type="radio" value="showLcount">
                    <label for="showLcount">Lecture Count</label>

                    <input id="showBoth" name="teacherToggle" type="radio" value="showBoth" checked>
                    <label for="showBoth">Both</label>

                    <a class="btn btn-primary"></a>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive" id="tableContainer">
        <table class="table table-bordered table-condensed table-hover" cellspacing="0" width="100%" id="classesTable">
            <thead>
            <tr>
                <th>Class</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<!-- js includes -->
<script src="js/jquery.min.js"></script>
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
    var sem = $("#sem").html();
    var branch = $("#branch").html();
    $.ajax({
        url: "classes.php",
        dataType : "json",
        data: {'sem': sem,'branch': branch},
        success : function(result){
            console.log(result);
            if(result.error){
                $("#tableContainer").addClass('noData').html("<div class=error>"+result.error+"</div>");
            }
            var mainth;
            $.each(result.subjects,function(index,subject){
                mainth += ("<th><a data-id='" + subject.id + "'>" + subject.name + "</a></th>");
            });

            $("#classesTable > thead > tr:first-of-type").append(mainth);
            wholeData = result;
            loadData();
       }
    });
});

function loadData(){
    var subjects = wholeData.subjects;
    var classes = wholeData.classes;
    var tdata="";
    for(i=0;i<classes.length;i++){
        tdata+="<tr>";
        tdata += ("<td><a class=classname href='" + classes[i].classId+"'>" + classes[i].name + "</a></td>");
        for(j=0;j<subjects.length;j++){
            tdata += ("<td>" +
                "<div><a class=tname href='" + classes[i].info[j].teacherId +"'>" + classes[i].info[j].teacherName + "</a></div>" +
                "<div><a class=lcount href='show.php?class="+classes[i].classId+"&subject="+subjects[j].id+"'>"+ classes[i].info[j].lcount +" lectures</a></div></td>");
        }
        tdata+="</tr>";
    }
    $("#classesTable tbody").hide().html(tdata).show('slow');
    $("#classesTable").dataTable({
        paging : false,
        "sDom": "<'row controlsRow'<'col-md-4 controlPageLength'l><'col-md-4'<'dateRanger'>><'col-md-4 controlSearch'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        'language': {
            search: '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>',
            searchPlaceholder: 'Search here',
            lengthMenu: '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-list"></span></span>_MENU_</div>'
        }
    });
    $(".dataTables_filter input[type=search]").css('margin','0');
    $(".dateRanger").html($('.switcher').html());
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



</script>
<link href="css/customize.css" rel="stylesheet" />
<style>
.lcount{color: #666;}
</style>
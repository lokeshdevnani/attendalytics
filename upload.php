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
    echo "<pre>",print_r($_REQUEST),"</pre>";
    if(isset($_GET['teacher']) && !empty($_GET['teacher'])){
        $teacherId = $_GET['teacher'];
        echo "<span class='hidden' id='teacherId'>$teacherId</span>";
    }
    ?>
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
        <div class="col-md-8 col-md-offset-2">
            <form class="form-horizontal" method="post" role="form">
                <div class="form-group">
                    <div class="form-group col-md-6">
                        <label class="col-md-3" for="sem">Sem</label>
                        <div class="col-md-9">
                            <select class="form-control" name="sem" id="sem">
                                <?php
                                for($i=1;$i<=8;$i++){
                                    echo "<option value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="col-md-3" for="branch">Branch</label>
                        <div class="col-md-9">
                            <select class="form-control" id="branch" name="branch">
                                <option value="CS">CS</option>
                                <option value="IT">IT</option>
                                <option value="EC">EC</option>
                                <option value="EE">EE</option>
                                <option value="ME">ME</option>
                                <option value="CE">CE</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group col-md-6">
                        <label class="col-md-3" for="class">Section</label>
                        <div class="col-md-9">
                            <select class="form-control" name="class" id="class">

                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="col-md-3" for="branch">Branch</label>
                        <div class="col-md-9">
                            <select class="form-control" name="branch">
                                <option value="CS">CS</option>
                                <option value="IT">IT</option>
                                <option value="EC">EC</option>
                                <option value="EE">EE</option>
                                <option value="ME">ME</option>
                                <option value="CE">CE</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="checkboxArea">

                </div>
                <div class="form-group">
                    <input class="form-input" type="submit" />
                </div>
            </form>
        </div>
    </div>
</div>
<!-- js includes -->
<link rel="stylesheet" href="css/bootstrap-checkbox.css" />

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.js"></script>
<script src="js/bootstrap-checkbox.js"></script>
<script src="js/custom.js"></script>

<!-- js includes end -->
</body>
</html>

<script>
    var wholeData;
    var api;

    var sectionData;
    sectionData = {
        1: {'CS':['A','B','C','D'],'IT':['E','F'],'EC':['G','H','I','K'],'EE':['L','M'],'ME':['N','O'],'CE':['P','Q']},
        2: {'CS':['A','B','C','D'],'IT':['E','F'],'EC':['G','H','I','K'],'EE':['L','M'],'ME':['N','O'],'CE':['P','Q']},
        3: {'CS':['A','B','C','D'],'IT':['A','B'],'EC':['A','B','C','D'],'EE':['A','B'],'ME':['A','B'],'CE':['A','B']},
        4: {'CS':['A','B','C','D'],'IT':['A','B'],'EC':['A','B','C','D'],'EE':['A','B'],'ME':['A','B'],'CE':['A','B']},
        5: {'CS':['A','B','C','D'],'IT':['A','B'],'EC':['A','B','C','D'],'EE':['A','B'],'ME':['A','B'],'CE':['A','B']},
        6: {'CS':['A','B','C','D'],'IT':['A','B'],'EC':['A','B','C','D'],'EE':['A','B'],'ME':['A','B'],'CE':['A','B']},
        7: {'CS':['A','B','C','D'],'IT':['A','B'],'EC':['A','B','C','D'],'EE':['A','B'],'ME':['A','B'],'CE':['A','B']},
        8: {'CS':['A','B','C','D'],'IT':['A','B'],'EC':['A','B','C','D'],'EE':['A','B'],'ME':['A','B'],'CE':['A','B']}
    };

    // on error scale the tableContainer div from its starting point to bottom by using such a class


    $(document).ready(function(){

        var x = function(){
            var sem = $("#sem").val();
            var branch = $("#branch").val();
            var classPrefix = sem+branch;
            var sectionArray = sectionData[sem][branch];
            var classElement = $("#class");
            classElement.html('');
            //$("#class").appendChild("<option value='hello'>Hello</option>");
            $.each(sectionArray,function(i,item){
                classElement.append($('<option>',{
                    value: classPrefix+item,
                    text: item
                }));
            });
        };
        x();
        $("#sem,#branch").change(x);
        $("#class").change(function(){
            //getRollRange();
            var area  = $("#checkboxArea");
            area.html("");
            rollStart = 1;
            rollEnd = 75;
            for(i=rollStart;i<=rollEnd;i++){
                area.append("<div class='form-group col-md-4'><label><input type='checkbox' class='checkbox glyphicon' name='' id='' />"+i+"</label></div>");
            }
            $('input[type="checkbox"].checkbox').checkbox({
                buttonStyle: 'btn-link',
                buttonStyleChecked: 'btn-link',
                checkedClass: 'glyphicon-ok',
                uncheckedClass: 'glyphicon-remove'
            });

        });

        /*
        var teacherId = $("#teacherId").html();
        $.ajax({
            url: "teacherwise.php",
            dataType : "json",
            data: {'teacher': teacherId},
            success : function(result){
                console.log(result);
                if(result.login)
                    showLoginInfo(result.login);
                if(result.error){

                }
                wholeData = result;
                loadTeacherReport();

                return;
            }
        });
        */
    });



</script>
<link rel="stylesheet" href="css/customize.css" />
<style>
    .form-group label{
        vertical-align: middle;
        line-height: 34px;
        font-size: 16px;
    }
    .bootstrap-checkbox > button.btn {
        padding:2px 0;
        margin:0 8px 4px 4px;
        border: 1px solid deepskyblue;
    }
    #checkboxArea .form-group{
        margin-bottom: 5px;
    }

</style>
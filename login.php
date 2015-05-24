<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
</head>
<body>
<div class="container-fluid">
<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';
require_once 'functions/Auth.php';

$auth = new Auth($db);
$loggedAs = $auth->isLogged();
if($loggedAs){
    echo "Logged in as ".$loggedAs['type']." (".$loggedAs['name'].")";
    echo "<a href='logout.php'>Logout</a>";
    die();
}
$err = array();
if(isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['type'])){
    if(!empty($_POST['username']) && ($_POST['password']!="") && !empty($_POST['type'])){
        echo "GOT ya";
        $x = $auth->login($_POST['username'],$_POST['password'],$_POST['type']);
        if(!empty($x)){
            pr($x);
            die();
        } else {
            $err[] = "Access Denied! Invalid username password combination";
        }
    } else $err[] = "Enter your login credentials to continue";
} else {
    $err[] = "Please enter username and password";
}
pr($err);

?>
    <div class="col-md-offset-4 col-md-4 loginbox">
        <div class="loginContainer">
            <div class="loginInnerContainer">
                <div class="login-image">

                </div>
                <div class="login-form">
                    <form action="" method="post" class="form" role="form">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="username" type="text" class="form-control" placeholder="Username">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input name="password" type="text" class="form-control" placeholder="Password">
                        </div>
                        <div class="input-group" style="border: none;">
                            <label class="login-type col-md-6"><input name="type" type="radio" value="Admin">HOD</label>
                            <label class="login-type col-md-6 selected-type"><input name="type" type="radio" value="HOD" checked>Teacher</label></label>
                            <label class="login-type col-md-6"><input name="type" type="radio" value="teacher">Teacher</label></label>
                            <label class="login-type col-md-6"><input name="type" type="radio" value="student">Teacher</label></label>
                            <div class="clearfix"></div>
                        </div>
                        <div class="text-center">
                            <input type="submit" value="login" class="login-button" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<form id="myform" method="post" class="">
    <input type="text" name="username" placeholder="username">
    <input type="text" name="password" placeholder="password">
    <input type="radio" name="type" value="hod">
    <input type="radio" name="type" value="teacher">
    <input type="radio" name="type" value="student">
    <input type="submit" value="login"name="login"  />
</form>
</div>
<style>
    body{
        background: #25253C;
    }
    .loginbox{
        height: 100%;
        position: absolute;
        top:0;
    }
    .loginContainer{
        height: 100%;
        background: linear-gradient(to bottom, rgba(146, 135, 187, 0.8) 0%, rgba(0, 0, 0, 0.6) 100%);
        margin: 0 auto;
        width: 350px;
    }
    .loginInnerContainer{
        padding: 15px;
    }
    .login-image{
        height: 350px;
        background: url("images/sort_both.png");
        background-size: 100%;
    }
    .login-form .input-group{
        padding: 5px;
        margin: 10px;
        font-size: 20px;
        color: white;
        border-bottom: 1px solid #888;
    }
    .login-form input{
        background: transparent;
        border: none;
        font-size:inherit;
        color:inherit;
    }
    .login-form .form-control:focus{
        border:none;
        box-shadow: none;
        -webkit-box-shadow:none;
    }
    .login-form .input-group-addon{
        background: transparent;
        border:none;
        font-size:inherit;
        color: inherit;
    }
    .login-form label{
        margin: 0;
        border: 1px solid #555;
        font-weight: normal;
    }
    .login-form input[type=radio]{
        display:none;
    }
    .selected-type{
        background: #555;
    }
    .login-form .login-button{
        background: #0088cc;
        font: inherit;
        width: 80%;
        margin-top: 10px;
        padding: 10px 0;
        border-radius: 20px;
        color: white;

    }
</style>
<script src="js/jquery.min.js">s</script>
<script>
    $(".login-type input").change(function(){
        $(".login-type").removeClass("selected-type");
        $(this).parent().addClass("selected-type");
    });
</script>
</body>
</html>
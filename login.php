<?php
include_once 'api/functions/database.php';
include_once 'api/functions/Auth.php';

$auth = new Auth($db);
if($auth->isLogged()){
  redir('dashboard.php');
};
?>
<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="col-md-4"></div>
    <div class="col-md-4 loginbox">
        <div class="loginContainer">
            <div class="loginInnerContainer">
                <div class="login-image">
                  <img src="images/jecrc.png" class="img-responsive"/>
                </div>
                <div class="main-heading">
                    <h3>ATTENDANCE PORTAL</h3>
                </div>
                <div class="login-form">
                    <form action="" method="post" class="form" role="form" id="loginform">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="username" type="text" class="form-control" placeholder="Username" autocomplete="off">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input name="password" type="password" class="form-control" placeholder="Password" autocomplete="off">
                        </div>
                        <div class="input-group" style="border: none;">
                            <label class="login-type col-md-6"><input name="type" type="radio" value="superuser">Admin</label>
                            <label class="login-type col-md-6 selected-type"><input name="type" type="radio" value="hod" checked>HOD</label></label>
                            <label class="login-type col-md-6"><input name="type" type="radio" value="teacher">Teacher</label></label>
                            <label class="login-type col-md-6"><input name="type" type="radio" value="student">Student</label></label>
                            <div class="clearfix"></div>
                        </div>
                        <div class="text-center error-message">

                        </div>
                        <div class="text-center">
                            <input id="login-button" type="submit" value="LOGIN" class="login-button" name="login" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>
<style>
    body{
        background: #444;
        background: url(images/background.png);
        font-family: "Droid Sans", "Helvetica", Arial;

    }
    body .container-fluid{
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background: rgba(0,0,0, 0.7);
    }
    .loginbox{
        position: static;
        display: flex;
        align-items: center;
    }

    .loginContainer{
        height: 100%;
        background: white;
        margin: 0 auto;
        width: 350px;
        box-shadow: 5px 5px 30px 1px black;
    }
    .loginInnerContainer{
        padding: 15px;
    }
    .login-image{
        margin: 2em 1em;
    }
    .main-heading {
        text-align: center;
        margin: 2em -15px;
        padding: 1em 0;
        background: #00897b;
        color: white;
    }
    .main-heading h3{
      margin: 0;
    }
    .login-form .input-group{
        padding: 5px;
        margin: 10px;
        font-size: 18px;
        color: #444;
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
        border: 1px solid #444;
        font-weight: normal;
        padding-top: 4px;
        padding-bottom: 4px;
        border-radius: 20px;
        margin: 5px 2.5%;
        width:45%;
        font-size: 14px;
        text-align: center;
        color: #00897b;
    }
    .login-form input[type=radio]{
        display:none;
    }
    .selected-type{
        background: #444;
        color: white!important;
    }
    .login-form .login-button{
        background: #00897b;
        font: inherit;
        width: 80%;
        padding: 10px 0;
        border-radius: 20px;
        color: white;
    }
    .login-form .login-button-wait{
        background-color: #00aa00;
        color: white;
        width: 80px;
        border-radius: 40px;
        transform: rotate(720deg);
        transition: all 1s ease-in 0.2s;
    }
    .error-message{
      color: red;
      margin: 0 0 15px 0;
    }
    pre{
        display: none;

    }
</style>
<script src="js/jquery.min.js"></script>
<script>
    $(".login-type input").change(function(){
        $(".login-type").removeClass("selected-type");
        $(this).parent().addClass("selected-type");
    });

    $("#loginform").submit(function(e){
        e.preventDefault();
        params = $(this).serialize();
        $.ajax({
            url: "api/login.php",
            dataType : "json",
            data: params,
            success : function(result){
              if(result.status && result.status == true) {
                location = 'dashboard.php'
              } else {
                $(".error-message").hide().html(result.error).slideDown();
              }
            }
        });
    });

</script>
</body>
</html>

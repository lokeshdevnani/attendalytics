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
<form id="myform" method="post">
    <input type="text" name="username" placeholder="username">
    <input type="text" name="password" placeholder="password">
    <input type="radio" name="type" value="hod">
    <input type="radio" name="type" value="teacher">
    <input type="radio" name="type" value="student">
    <input type="submit" value="login"name="login"  />
</form>
</form>
<?php

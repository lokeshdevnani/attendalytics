<?php
require_once 'functions/database.php';
require_once 'functions/Attendance.php';
require_once 'functions/Auth.php';
/*
 * Implement a token based login system...
 * successful login does ( set session array, enters this data to token_db with a random token key,
 * returns this key using json output data)
 * when the token sent with any request is given to a API file, it checks whether the session is set,
 * if not token is provided or not, if provided, checks whether its valid or not. If it is , sets session with the token data.
*/

$_POST = $_REQUEST;
$att = new Attendance($db);
$auth = new Auth($db);
$login = $auth->isLogged();

if($login){     // if session is set already
    $json['info'] = $login;
    $json['status'] = true;
    die(json_encode($json));
}
if(isset($_POST['token'])){ //if token is valid
    $info = $auth->isTokenValid($_POST['token']);
    if($info){
        $json['info'] = $info;
        $json['status'] = true;
    } else {
        $json['status'] = false;
        $json['error'] = "Invalid Token. Please authenticate again.";
    }
    die(json_encode($json));
}
// if has to be verified using login credentials and generate a token..
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['type'])){
    if(!empty($_POST['username']) && ($_POST['password']!="") && !empty($_POST['type'])){
        $login = $auth->login($_POST['username'],$_POST['password'],$_POST['type']);
        if(!empty($login)){
            $token = $auth->generateToken(json_encode($login));
            die(json_encode(array(
              'info'    => $login,
              'status'  => true,
              'token'   => $token
            )));
        } else {
            $err = "Access Denied! Invalid username password combination";
        }
    } else $err = "Enter your login credentials to continue";
} else {
    $err = "Please enter username and password";
}
$json['status'] = false;
$json['error'] = $err;
die(json_encode($json));

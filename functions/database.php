<?php
$db_host       = "127.0.0.1";
$db_name       = "attendance";
$db_user       = "Lokesh";
$db_pass       = "password";
try{
    $db = new PDO(
        "mysql:host={$db_host};dbname={$db_name}",
        $db_user,
        $db_pass
    );
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo $e->getMessage();
    echo 'Sorry, Could not connect to the database this moment. Try again later';
    exit;
}

session_start();

function pr($arr){
    echo "<pre>",print_r($arr,1),"</pre>";
}
function dje($str){
    die(json_encode(array("error"=>$str)));
}
function redir($str){
    header("location: ".$str);
}
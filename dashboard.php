<?php
include_once 'api/functions/database.php';
include_once 'api/functions/Auth.php';

$auth = new Auth($db);
if(!$auth->isLogged()){
  redir('login.php');
};
?>

<?php
require_once 'api/functions/database.php';
require_once 'api/functions/Auth.php';

$auth = new Auth($db);
$auth->logout();
redir('login.php');

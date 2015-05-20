<?php
require_once 'functions/database.php';
require_once 'functions/Auth.php';

$auth = new Auth($db);
$auth->logout();
redir('login.php');
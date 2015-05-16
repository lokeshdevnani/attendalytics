<?php
require_once 'functions/database.php';

if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
    $id = $_GET['id'];
    $q = $db->prepare("SELECT classId,subjectId FROM lectures WHERE id = ?");
    $q->execute(array($id));
    $r = $q->fetch(PDO::FETCH_OBJ);
    if(!empty($r)){
        redir("show.php?class=".$r->classId."&subject=".$r->subjectId);
    } else {
        echo "Lecture not found";
    }
}
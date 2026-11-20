<?php require 'includes/auth.php';
if(logged())logAct($conn,'logout');
$_SESSION=[];session_destroy();header('Location: login.php');

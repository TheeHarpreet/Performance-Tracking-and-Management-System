<?php 
session_start();
$user_id = 0;
try {
    $userID = $_SESSION["user_id"];
}
catch (Exception $ex) {
    
}
if ($user_id == 0) {
    //header("Location: login.php");
}
?>
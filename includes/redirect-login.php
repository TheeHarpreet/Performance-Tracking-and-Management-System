<?php 
session_start();
$userID = 0;
try {
    $userID = $_SESSION['user_id'];
}
catch (Exception $ex) {
    
}
if ($userID == 0) {
    header("Location: login.php");
}
?>
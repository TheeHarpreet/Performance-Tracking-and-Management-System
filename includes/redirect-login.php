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

// Include this on all pages but login.php and signup.php. Redirects the user to login.php if they're not logged into an account.
?>
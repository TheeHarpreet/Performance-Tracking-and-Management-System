<?php
    $id=$_GET['userID'];
    include('config.php');
    mysqli_query($mysqli, "DELETE FROM 'users' WHERE userID='$id'");
    header("Location: index.php");
?>
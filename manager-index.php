<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $query->fetch_object();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php include_once("includes/header.php") ?>
        <div class="container">
            <div class="managers-user-section">
                <p>Search user</p>
                <input type="text">
                <div class="user-search-results">

                </div>
            </div>
            <div class="managers-work-section">
                <p>Search</p>
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
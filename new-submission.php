<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$thing = $_SESSION['newSubmission'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Submission</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php include_once("includes/header.php") ?>
        <div class="container">
            <?php
            echo "<p style='color: black;'>$thing</p>";
            ?>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
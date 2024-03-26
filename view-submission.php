<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$submissionID = $_SESSION['viewSubmission'];
$submissionQuery = $mysqli->query("SELECT * FROM submission WHERE submissionID = $submissionID");
$submission = $submissionQuery->fetch_object();
$coauthorsQuery = $mysqli->query("SELECT * FROM submissioncoauthor WHERE submissionID = $submissionID");
$coauthors = $coauthorsQuery->fetch_object();
$authorQuery = $mysqli->query("SELECT * FROM users WHERE userID = $submission->author");
$author = $authorQuery->fetch_object();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submission</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php include_once("includes/header.php") ?>
        <div class="container">
            <?php
                echo "<h1 class='submission-title'>$submission->title</h1>";
                echo "<h2>$author->fname $author->lname</h2>";
                echo "<h1>$submission->title</h1>";
                echo "<h1>$submission->title</h1>";
            ?>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$submissionID = $_SESSION['viewSubmission'];

// Get submission, coauthors, and the author.
$submissionQuery = $mysqli->query("SELECT * FROM submission WHERE submissionID = $submissionID");
$submission = $submissionQuery->fetch_object();
$coauthorsQuery = $mysqli->query("SELECT * FROM submissioncoauthor WHERE submissionID = $submissionID");
$authorQuery = $mysqli->query("SELECT * FROM users WHERE userID = $submission->author");
$author = $authorQuery->fetch_object();
$rejectedQuery = $mysqli->query("SELECT * FROM submissionreturn WHERE submissionID = $submissionID ORDER BY returnDate DESC");

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
                // status code
                if ($submission->approved > 0) {
                    $status = "Approved";
                } else if ($submission->submitted == 1 && $submission->approved == 0) {
                    $status = "Needing Manager approval";
                } else if ($submission->submitted == 0 && mysqli_num_rows($rejectedQuery) > 0) {
                    $status = "Rejected";
                    $recent = $mysqli->query("SELECT * FROM submissionreturn, submission WHERE submission.dateSubmitted > submissionreturn.returnDate");
                    if (mysqli_num_rows($recent) > 0) {
                        $status = "Needing Supervisor approval";
                    }
                } else {
                    $status = "Needing Supervisor approval";
                }
            
                // datetime code
                $datetime = strval($submission->dateSubmitted);
                $date = explode(" ", $datetime);
                $dateValues = explode("-", $date[0]);
                $time = $date[1];
                $timeValues = explode(":", $time);
                $timeOfDay = " AM";
                $hour = intval($timeValues[0]);
                $hour = $hour % 12;
                if ($hour == 0) {
                    $hour = 12;
                }
                if ($timeValues[0] >= 12) {
                    $timeOfDay = " PM";
                }
                $dateTimeOutput = $dateValues[2] . "/" . $dateValues[1] . "/" . $dateValues[0] . " at " . $hour . ":" . $timeValues[1] . ":" . $timeValues[2] . $timeOfDay;

                echo "
                <h1 class='submission-title'>$submission->title</h1>
                <h2>By $author->fname $author->lname ($author->jobRole)</h2>
                <p><span style='font-weight: bold'>Date Submitted: </span> $dateTimeOutput </p>
                <h2 class='submission-description'>$submission->comments</h2>
                <h2>Status: $status</h2>
                <div class='coauthors'>
                <h1>Coauthors</h1>
                ";
                while ($obj = $coauthorsQuery->fetch_object()) {
                    $coauthorQuery = $mysqli->query("SELECT * FROM users where userID = $obj->coauthor");
                    $coauthor = $coauthorQuery->fetch_object();
                    echo "$coauthor->fname $coauthor->lname";
                }
                echo "
                </div>
                <div class='files'>
                </div>
                <div class='manager-review'>

                </div>
                ";
            ?>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
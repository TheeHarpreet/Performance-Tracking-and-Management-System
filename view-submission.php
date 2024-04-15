<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$submissionID = $_SESSION['viewSubmission'];

$userID = $_SESSION['user_id'];
$userQuery = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $userQuery->fetch_object();

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['lang'])) {
    if (isset($_POST['approve'])) {
        $approveQuery = $mysqli->prepare("UPDATE submission SET submitted = 1 WHERE submissionID = ?");
        $approveQuery->bind_param("s", $submissionID);
        $approveQuery->execute();
    } else if (isset($_POST['return'])) {
        $returnQuery = $mysqli->prepare("INSERT INTO submissionreturn (submissionID, returner, comments) VALUES (?, ?, ?)");
        $returnQuery->bind_param("sss", $submissionID, $userID, $_POST['return-comments']);
        $returnQuery->execute();

        $updateStatusQuery = $mysqli->query("UPDATE submission SET submitted = 0 AND approved = 0");
    } else if (isset($_POST['manager-approve'])) {
        if ($submission->sectionID == 1) {

        } else if ($submission->sectionID == 2 || 5) {

        } else if ($submission->sectionID == 3) {

        } else if ($submission->sectionID == 4) {

        } else if ($submission->sectionID == 6) {

        } else if ($submission->sectionID == 7) {

        }
    }
}

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
                    $recent = $mysqli->query("SELECT * FROM submissionreturn, submission WHERE submission.dateSubmitted > submissionreturn.returnDate AND submission.submissionID = submissionreturn.submissionID");
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
                ";
                if (mysqli_num_rows($coauthorsQuery)) {
                    echo "
                    <div class='coauthors'>
                    <h1>Coauthors</h1>
                    ";
                    while ($obj = $coauthorsQuery->fetch_object()) {
                        $coauthorQuery = $mysqli->query("SELECT * FROM users where userID = $obj->coauthor");
                        $coauthor = $coauthorQuery->fetch_object();
                        echo "$coauthor->fname $coauthor->lname";
                    }
                    echo "</div>";
                }
                echo "
                <div class='files'>
                </div>
                ";
                if ($user->jobRole == "Supervisor") {
                    $supervisorQuery = $mysqli->query("SELECT * FROM researcherssupervisor WHERE researcherID = $author->userID AND supervisorID = $user->userID");
                    if (mysqli_num_rows($supervisorQuery) > 0 ) {
                        if ($status == "Needing Supervisor approval") { // Checks for $status instead of ($submission->submitted = 0) as the latter would immediately allow the supervisor to resubmit after rejected.
                            echo "
                            <h2>Please review work</h2>
                            <form method='post'>
                            <button name='approve'>Approve</button>
                            </form>
                            <form method='post'>
                                <div class='decline-div'>
                                    <input type='text' placeholder='Comments (For declines only)' name='return-comments' required>
                                    <button name='return'>Return</button>
                                </div>
                            </form>
                            ";
                        }
                    } else {
                        echo "<h2>You can only view details of this task</h2>";
                    }
                } else if ($user->jobRole == "Researcher") {
                    $coauthorQuery = $mysqli->query("SELECT * FROM submissioncoauthor WHERE coauthor = $userID AND submissionID = $submissionID");
                    if ($submission->author != $userID && mysqli_num_rows($coauthorQuery) == 0) {
                        if ($status == "Approved" && $submission->section == 3 || $submission->section == 4 || $submission->section == 5) {
                            echo "<h2>You can only view the details of this task</h2>";
                        } else {
                            header("Location: index.php");
                        }
                    } else {
                        if ($status == "Rejected") {
                            echo "<button name='resubmit'>Resubmit</button>";
                        }
                    }
                } else if ($user->jobRole == "Manager") {
                    if ($status = "Needing Manager approval") {
                        // Section A - No coauthor. Approve or deny, 1 point.
                        // Section B, E - B has no coauthor. MIROS - 1 point. National - 2 points. International - 3 points.
                        // Section C - Internal - 1 point. Operation - 2 points. External - 3 points.
                        // Section D - Approve or deny, 1 point.
                        // Section F - Supervision - 2 points. Local - 1 point. National - 2 points. International - 3 points.
                        // Section G - Institute - 1 point. District - 2 points. State - 2 points. National - 3 points. International 4 points.
                        echo "
                        <form method='post'>
                        <div>
                        ";
                        if ($submission->sectionID == 1) {
                            echo "
                            ";
                        } else if ($submission->sectionID == 2 || 5) {
                            echo "
                            ";
                        } else if ($submission->sectionID == 3) {

                        } else if ($submission->sectionID == 4) {

                        } else if ($submission->sectionID == 6) {

                        } else if ($submission->sectionID == 7) {

                        }
                        echo "
                        <button name='manager-approve'>Approve</button>
                        </div>
                        </form>
                        <form method='post'>
                            <div class='decline-div'>
                                <input type='text' placeholder='Comments (For declines only)' name='return-comments' required>
                                <button name='return'>Return</button>
                            </div>
                        </form>
                        ";
                    }
                }
            ?>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>

<?php include("includes/lang-config.php");
function translate($key) {
    $translations = array(
        "en" => array(
            // Things
        ),
        "bm" => array(
            // Things
        )
    );

    $language = $_SESSION['language'];
    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
} ?>
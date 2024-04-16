<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$submissionID = $_SESSION['viewSubmission'];

$userID = $_SESSION['user_id'];
$userQuery = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $userQuery->fetch_object();

// Get submission, coauthors, and the author.
$submissionQuery = $mysqli->query("SELECT * FROM submission WHERE submissionID = $submissionID");
$submission = $submissionQuery->fetch_object();
$coauthorsQuery = $mysqli->query("SELECT * FROM submissioncoauthor WHERE submissionID = $submissionID");
$authorQuery = $mysqli->query("SELECT * FROM users WHERE userID = $submission->author");
$author = $authorQuery->fetch_object();
$rejectedQuery = $mysqli->query("SELECT * FROM submissionreturn WHERE submissionID = $submissionID ORDER BY returnDate DESC");

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
        $assignedPoints = 1;
        if ($submission->sectionID != 1 && $submission->sectionID != 4) {
            $assignedPoints = $_POST['type-select'];
        }
        $assignQuery = $mysqli->prepare("UPDATE submission SET approved = ? WHERE submissionID = ?");
        $assignQuery->bind_param("ss", $assignedPoints, $submissionID);
        $assignQuery->execute();
    }
    header("Location: view-submission.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate("View Submission"); ?></title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php include_once("includes/header.php") ?>
        <div class="container">
            <?php
                // status code
                if ($submission->approved > 0) {
                    $status = translate("Approved");
                } else if ($submission->submitted == 1 && $submission->approved == 0) {
                    $status = translate("Needing Manager approval");
                } else if ($submission->submitted == 0 && mysqli_num_rows($rejectedQuery) > 0) {
                    $status = translate("Rejected");
                    $recent = $mysqli->query("SELECT * FROM submissionreturn, submission WHERE submission.dateSubmitted > submissionreturn.returnDate AND submission.submissionID = submissionreturn.submissionID");
                    if (mysqli_num_rows($recent) > 0) {
                        $status = translate("Needing Supervisor approval");
                    }
                } else {
                    $status = translate("Needing Supervisor approval");
                }
            
                // datetime code
                $timeToTranslate = $submission->dateSubmitted;
                include("includes/format-date.php");

                echo "
                <h1 class='submission-title'>$submission->title</h1>
                <h2>". translate("By") . " $author->fname $author->lname (". translate($author->jobRole) .")</h2>
                <p><span style='font-weight: bold'>". translate("Date Submitted") . ": </span> $dateTimeOutput </p>
                <h2 class='submission-description'>$submission->comments</h2>
                <h2>". translate("Status") . ": $status</h2>
                ";
                if (mysqli_num_rows($coauthorsQuery)) {
                    echo "
                    <div class='coauthors'>
                    <h1>". translate("Coauthors") . "</h1>
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
                        if ($status == translate("Needing Supervisor approval")) { // Checks for $status instead of ($submission->submitted = 0) as the latter would immediately allow the supervisor to resubmit after rejected.
                            echo "
                            <h2>". translate("Please review work") . "</h2>
                            <form method='post'>
                            <button name='approve'>". translate("Approve") . "</button>
                            </form>
                            <form method='post'>
                                <div class='decline-div'>
                                    <input type='text' placeholder='". translate("Comments (For declines only)") . "' name='return-comments' required>
                                    <button name='return'>". translate("Return") . "</button>
                                </div>
                            </form>
                            ";
                        }
                    } else {
                        echo "<h2>". translate("You can only view details of this task") . "</h2>";
                    }
                } else if ($user->jobRole == "Researcher") {
                    $coauthorQuery = $mysqli->query("SELECT * FROM submissioncoauthor WHERE coauthor = $userID AND submissionID = $submissionID");
                    if ($submission->author != $userID && mysqli_num_rows($coauthorQuery) == 0) {
                        if ($status == translate("Approved") && $submission->section == 3 || $submission->section == 4 || $submission->section == 5) {
                            echo "<h2>". translate("You can only view the details of this task") . "</h2>";
                        } else {
                            header("Location: index.php");
                        }
                    } else {
                        if ($status == translate("Rejected")) {
                            echo "<button name='resubmit'>". translate("Resubmit") . "</button>";
                        }
                    }
                } else if ($user->jobRole == "Manager") {
                    if ($status == translate("Needing Manager approval")) {
                        // Section A, D - A has no coauthor. Approve or deny, 1 point.
                        // Section B, E - B has no coauthor. Internal - 1 point. National - 2 points. International - 3 points.
                        // Section C - Internal - 1 point. Operation - 2 points. External - 3 points.
                        // Section F - Supervision - 2 points. Local - 1 point. National - 2 points. International - 3 points.
                        // Section G - Institute - 1 point. District - 2 points. State - 2 points. National - 3 points. International 4 points.
                        echo "
                        <form method='post'>
                        <div>
                        ";
                        if ($submission->sectionID != 1 && $submission->sectionID != 4) {
                            echo "<select name='type-select' id='type-select'>";
                            if ($submission->sectionID == 2 || $submission->sectionID == 5) {  // NeedsTranslation All the values in the option tags, some repeat
                                echo "
                                <option value='1'>Internal</option>  
                                <option value='2'>National</option>
                                <option value='3'>International</option>
                                ";
                            } else if ($submission->sectionID == 3) {
                                echo "
                                <option value='1'>Internal Project</option>
                                <option value='3'>External Project</option>
                                <option value='2'>Operations</option>
                                ";
                            } else if ($submission->sectionID == 6) {
                                echo "
                                <option value='2'>Project Supervision</option>
                                <option value='1'>Local</option>
                                <option value='2'>National</option>
                                <option value='3'>International</option>
                                ";
                            } else if ($submission->sectionID == 7) {
                                echo "
                                <option value='1'>Institute</option>
                                <option value='2'>District</option>
                                <option value='2'>State</option>
                                <option value='3'>National</option>
                                <option value='4'>International</option>
                                ";
                            }
                            echo "</select>";
                        }
                        echo "
                        <button name='manager-approve'>". translate("Approve") . "</button>
                        </div>
                        </form>
                        <form method='post'>
                            <div class='decline-div'>
                                <input type='text' placeholder='". translate("Comments (For declines only)") . "' name='return-comments' required>
                                <button name='return'>". translate("Return") . "</button>
                            </div>
                        </form>
                        ";
                    }
                }
                if (mysqli_num_rows($rejectedQuery) > 0) {
                    echo "<h1>Rejection History:</h1>";
                    while ($rejection = $rejectedQuery->fetch_object()) {
                        $returnerQuery = $mysqli->query("SELECT * FROM users WHERE userID = $rejection->returner");
                        $returner = $returnerQuery->fetch_object();
                        $timeToTranslate = $rejection->returnDate;
                        include("includes/format-date.php");
                        echo "
                            <div class='return-div'>
                                <p>Returned by $returner->fname $returner->lname</p>
                                <p>Date returned: $dateTimeOutput</p>
                                <p>Reason: $rejection->comments</p>
                            </div>
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
        /*
        "en" => array(
            "View Submission" => "View Submission",
            "By" => "By",
            "Date Submitted" => "Date Submitted",
            "Status" => "Status",
            "Approved" => "Approved",
            "Needing Manager approval" => "Needing Manager approval",
            "Rejected" => "Rejected",
            "Needing Supervisor approval" => "Needing Supervisor approval",
            "Coauthors" => "Coauthors",
            "Please review work" => "Please review work",
            "Approve" => "Approve",
            "Comments (For declines only)" => "Comments (For declines only)",
            "Return" => "Return",
            "You can only view details of this task" => "You can only view details of this task",
            "Resubmit" => "Resubmit",
            "Researcher" => "Researcher",
            "Supervisor" => "Supervisor",
            "Manager" => "Manager",
        ),
        */
        "bm" => array(
            "View Submission" => "Lihat Penyerahan",
            "By" => "Oleh",
            "Date Submitted" => "Tarikh Penyerahan",
            "Status" => "Status",
            "Approved" => "Diluluskan",
            "Needing Manager approval" => "Memerlukan kelulusan Pengurus",
            "Rejected" => "Ditolak",
            "Needing Supervisor approval" => "Memerlukan kelulusan Penyelia",
            "Coauthors" => "Penulis Bersama",
            "Please review work" => "Sila semak kerja",
            "Approve" => "Luluskan",
            "Comments (For declines only)" => "Komen (Hanya untuk penolakan)",
            "Return" => "Kembali",
            "You can only view details of this task" => "Anda hanya boleh melihat butiran tugasan ini",
            "Resubmit" => "Serah semula",
            "Researcher" => "Penyelidik",
            "Supervisor" => "Penyelia",
            "Manager" => "Pengurus",
        )
    );

    $language = $_SESSION['language'];
    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
} ?>

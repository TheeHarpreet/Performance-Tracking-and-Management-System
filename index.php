<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $query->fetch_object();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['new-submission'];
    $_SESSION['newSubmission'] = $_POST['new-submission'];
    header("Location: new-submission.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
    <script src="js/main.js" defer></script>
</head>
<body>
    <?php include_once("includes/header.php") ?>
        <div class="container">
            <?php
                if ($user->jobRole == "None") {
                    echo "<p class='invalid-role'>Your account doesn't have a role assigned. Please speak to an admin to assign you one.</p>";
                    echo "</div>";
                    include_once("includes/footer.php");
                    exit();
                } else if ($user->jobRole == "Admin") {
                    header("Location: admin-index.php");
                }
            ?>
            <?php
                if ($user->jobRole == "Supervisor") {
                    echo "<h1>Select a researcher to view their work</h1>";
                    echo "<div class='supervisor-user-selection'>";
                    echo "<p><a href='index.php'>View your own work</a></p>";
                    $results = $mysqli->query("SELECT * FROM users, researcherssupervisor WHERE supervisorID = $userID and researcherID = userID");
                    echo "<div class='researchers-names'>";
                    while ($researcher = $results->fetch_object()) {
                        echo "<p><a href='index.php?user_override=$researcher->userID'>$researcher->fname $researcher->lname</a></p>";
                    }
                    echo "</div>";
                    echo "</div>";
                }
                if (isset($_GET['user_override'])) {
                    $userID = $_GET['user_override'];
                }
            ?>
            <div class="performance">
                <h1>Performance Overview</h1>
                <div class="performance-overview">
                    <p>*Performance Container Here*</p>
                </div>
            </div>
            <div class="tasks">
                <?php
                    $sectionTitles = array ("Personal Particulars", "Professional Achievements", "Research And Development", "Professional Consultations", "Research Outcomes", "Professional Recognition", "Service To Community");
                    $sectionTypes = array ("A", "B", "C", "D", "E", "F", "G");
                    $i = 0;

                    while ($i < 7) {
                        echo "<div class='section-container'>";
                        echo "<h1 class='section-header'>$sectionTitles[$i]</h1>";
                        $type = $sectionTypes[$i];
                        $query = $mysqli->query("SELECT * FROM submission WHERE author = $userID AND type = '$type'");
                        while ($obj = $query->fetch_object()) {
                            echo "<div class='submission-preview-box'>";
                            $isAuthor = true;
                            include("includes/submission-preview-fill.php");
                        }
                        $query = $mysqli->query("SELECT * FROM submission, submissioncoauthor WHERE submissioncoauthor.userID = $userID AND submissioncoauthor.submissionID = submission.submissionID AND submission.type = '$type'");
                        while ($obj = $query->fetch_object()) {
                            echo "<div class='submission-preview-box'>";
                            $isAuthor = false;
                            include("includes/submission-preview-fill.php");
                        }
                        echo "<div>";
                        echo "<form method='post'>";
                        echo "<button class='new-submission' name='new-submission' value='$sectionTypes[$i]'>+ Add New Submission</button>";
                        echo "</form>";
                        echo "</div>";
                        echo "</div>";
                        $i++;
                    }
                ?>
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
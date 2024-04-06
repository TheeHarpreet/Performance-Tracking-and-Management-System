<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $query->fetch_object();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new-submission'])) {
        $_SESSION['newSubmission'] = $_POST['new-submission'];
        header("Location: new-submission.php");
    } else {
        $_SESSION['viewSubmission'] = $_POST['submission-id'];
        header("Location: view-submission.php");
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
    <script src="js/index.js"></script>
    <script src="js/performance-bars.js" defer></script>
</head>
<body>
    <?php include_once("includes/header.php") ?>
        <div class="index-container">
            <?php
                if ($user->jobRole == "None") {
                    echo "<p class='invalid-role'>Your account doesn't have a role assigned. Please speak to an admin to assign you one.</p>";
                    echo "</div>";
                    include_once("includes/footer.php");
                    exit();
                } else if ($user->jobRole == "Admin") {
                    header("Location: admin-index.php");
                } else if ($user->jobRole == "Manager") {
                    header("Location: manager-index.php");
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
                    <div class="performance-section">
                        <?php
                        $sectionTitles = array ("Personal Particulars", "Professional Achievements", "Research And Development", "Professional Consultations", "Research Outcomes", "Professional Recognition", "Service To Community");
                        $sectionTypes = array ("A", "B", "C", "D", "E", "F", "G");
                        $author = $userID;
                        $pointsTotal = 0;
                        $pointsArray = array ();
                        for ($loop = 0; $loop < 7; $loop++) {
                            $section = $sectionTypes[$loop];
                            include("includes/calculate-score.php");
                            $pointsTotal += $points;
                            if ($points == 0){
                                echo "<p>$sectionTitles[$loop]: Not enough data to calculate scores</p>";
                                array_push($pointsArray, 0);
                            }
                            else {
                                $percent = (($points-$minPoints)*100)/($maxPoints-$minPoints);
                                echo "
                                <p>$sectionTitles[$loop]:</p>
                                <div class='percent-bar'>
                                <p class='point-boundary'>$minPoints</p>
                                <div class='progress-bar-container'>
                                    <div id='myBar' class='progress-bar' style='width: $percent%;'>"; if ($percent >= 10) { echo "<p style='padding: 4px 7px 0px 0px; margin: 0px; border: 0px; text-align: right;'>$points</p>"; } echo"</div>";
                                    if ($percent < 10) { echo "<p style='padding: 4px 0px 0px 3px; margin: 0px; border: 0px; text-align: right;'>$points</p>"; }
                                echo "</div>
                                <p class='point-boundary'>$maxPoints</p>
                                </div>
                                ";
                                array_push($pointsArray, $percent);
                            }
                        }
                        ?>
                    </div>
                    <div class="performance-section">
                    <p><?php echo "$pointsTotal"; ?> / 55</p> <!-- 42 is the minimum if you have something in all categories -->
                    <?php $total = 0 ?>
                    <div id="arc"></div>
                    <div id="arc6"></div>
                    <div id="arc5"></div>
                    <div id="arc4"></div>
                    <div id="arc3"></div>
                    <div id="arc2"></div>
                    <div id="arc1"></div>
                    <?php $total = 0;  ?>
                    <style> #arc1::before { transform: rotate(-90deg); } </style>
                    <style> #arc2::before { transform: rotate(-90deg); } </style>
                    <style> #arc3::before { transform: rotate(-90deg); } </style>
                    <style> #arc4::before { transform: rotate(-90deg); } </style>
                    <style> #arc5::before { transform: rotate(-90deg); } </style>
                    <style> #arc6::before { transform: rotate(-90deg); } </style>
                    </div>
                </div>
            </div>
            <div class="tasks">
                <?php
                    $i = 0;

                    echo "<h1>Submissions</h1>";

                    while ($i < 7) {
                        echo "<div class='section-container'>";
                        echo "<div class='section-name-bar'>";
                        echo "<h2 class='section-header'>$sectionTitles[$i]</h2>";
                        echo "<button onclick='hideSection($i)' id='toggle-button$i'>Hide</button>";
                        echo "</div>";
                        
                        echo "<div id='section-hide$i'>";
                        $type = $sectionTypes[$i];
                        $query = $mysqli->query("SELECT * FROM submission WHERE author = $userID AND type = '$type'");
                        while ($obj = $query->fetch_object()) {
                            $isAuthor = true;
                            include("includes/submission-preview-fill.php");
                        }
                        $query = $mysqli->query("SELECT * FROM submission, submissioncoauthor WHERE submissioncoauthor.coauthor = $userID AND submissioncoauthor.submissionID = submission.submissionID AND submission.type = '$type'");
                        while ($obj = $query->fetch_object()) {
                            $isAuthor = false;
                            include("includes/submission-preview-fill.php");
                        }
                        echo "<div>";
                        echo "<form method='post'>";
                        echo "<button class='new-submission' name='new-submission' value='$sectionTypes[$i]'>+ Add New Submission</button>";
                        echo "</form>";
                        echo "</div>";
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
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
    } else if (isset($_POST['new-password-button'])) { // change password. Add the code to check the conditions here. I'd recommend doing the conditions as an include and use that here and in signup.
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = $mysqli->prepare("UPDATE users SET password = ? WHERE userID = $userID");
        $query->bind_param("s", $passwordHash);
        $query->execute();
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
                    echo "
                    <p class='invalid-role'>Your account doesn't have a role assigned. Please speak to an admin to assign you one.</p>
                    </div>";
                    include_once("includes/footer.php");
                    exit();
                } else if ($user->jobRole == "Admin") { // Redirects for admin.
                    header("Location: admin-index.php");
                } else if ($user->jobRole == "Manager" && !isset($_GET['user_override'])) { // Redirects for managers who aren't viewing another users work.
                    header("Location: manager-index.php");
                }
            ?>
            <?php
                if ($user->jobRole == "Supervisor") { // Options for supervisors to view the accounts of users they supervise.
                    echo "
                    <h1>Select a researcher to view their work</h1>
                    <div class='supervisor-user-selection'>
                    <p><a href='index.php'>View your own work</a></p>
                    ";
                    $results = $mysqli->query("SELECT * FROM users, researcherssupervisor WHERE supervisorID = $userID and researcherID = userID");
                    echo "<div class='researchers-names'>";
                    while ($researcher = $results->fetch_object()) {
                        echo "<p><a href='index.php?user_override=$researcher->userID'>$researcher->fname $researcher->lname</a></p>";
                    }
                    echo "
                    </div>
                    </div>
                    ";
                }
                if (isset($_GET['user_override']) && $user->jobRole != "Researcher") {
                    // Updates and displays information is another account is being used
                    $userID = $_GET['user_override'];
                    $userQuery = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
                    $user = $userQuery->fetch_object();
                    echo "<h1>$user->fname $user->lname </h1>";
                } else if (isset($_GET['user_override']) && $user->jobRole == "Researcher") {
                    header("Location: index.php");
                }
            ?>
            <div class="performance">
                <h1>Performance Overview</h1>
                <div class="performance-overview">
                    <div class="performance-section">
                        <?php
                        $sectionQuery = $mysqli->query("SELECT * FROM sections");
                        $author = $userID;
                        $pointsTotal = 0;
                        $pointsArray = array ();
                        for ($loop = 0; $loop < 7; $loop++) { // A loop for each section in the performance overview.
                            $section = $sectionQuery->fetch_object();
                            $minPoints = $section->minPoints;
                            $maxPoints = $section->maxPoints;
                            $minRange = $section->minRange;
                            $maxRange = $section->maxRange;
                            $title = $section->sectionName;
                            $sectionID = $loop + 1;

                            $query = $mysqli->query("SELECT SUM(`approved`) AS amount FROM `submission` WHERE `author` = $author AND sectionID = $sectionID");
                            $result = $query->fetch_object();
                            $currentAmount = $result->amount;

                            if ($currentAmount == 0){
                                echo "<p>$title: Not enough data to calculate scores</p>";
                                array_push($pointsArray, 0);
                            } else {
                                if ($minRange != $currentAmount && $minRange != $maxRange) {
                                    $points = $minPoints + (($maxPoints - $minPoints) * (($currentAmount - $minRange) / ($maxRange - $minRange))); // Points calculation.
                                } else {
                                    $points = $maxPoints; // Points calculation doesn't work properly if the user has the minimum and maximum amount, so this is a workaround.
                                }
                                $pointsTotal += $points;
                                $percent = (($points-$minPoints)*100)/($maxPoints-$minPoints);
                                echo "
                                <p>$title:</p>
                                <div class='percent-bar'>
                                <p class='point-boundary'>$minPoints</p>
                                <div class='progress-bar-container'>
                                <div id='myBar' class='progress-bar' style='width: $percent%;'>"; if ($percent >= 10) { echo "<p style='padding: 4px 7px 0px 0px; margin: 0px; border: 0px; text-align: right;'>$points</p>"; } echo"</div>";
                                if ($percent < 10) { echo "<p style='padding: 4px 0px 0px 3px; margin: 0px; border: 0px; text-align: right;'>$points</p>"; }
                                echo "</div>
                                <p class='point-boundary'>$maxPoints</p>
                                </div>
                                ";
                                array_push($pointsArray, $points);
                            }
                        }
                        ?>
                    </div>
                    <div class="performance-section">
                    <p class="performance-points"><?php echo "$pointsTotal"; ?> / 55</p> <?php // 42 is the minimum if you have something in all categories. ?>
                    <?php $total = 0; $deg = "deg"; ?>
                    <div id="arc"></div>
                    <div id="arc7"></div>
                    <div id="arc6"></div>
                    <div id="arc5"></div>
                    <div id="arc4"></div>
                    <div id="arc3"></div>
                    <div id="arc2"></div>
                    <div id="arc1"></div>
                    <?php //Sets the arc length for each section. ?>
                    <style> #arc1::before { transform: rotate(<?php $total = $pointsArray[0]; $points = 180 - ($total * (180/55)); echo "-$points$deg" ?>); } </style>
                    <style> #arc2::before { transform: rotate(<?php $total += $pointsArray[1]; $points = 180 - ($total * (180/55)); echo "-$points$deg" ?>); } </style>
                    <style> #arc3::before { transform: rotate(<?php $total += $pointsArray[2]; $points = 180 - ($total * (180/55)); echo "-$points$deg" ?>); } </style>
                    <style> #arc4::before { transform: rotate(<?php $total += $pointsArray[3]; $points = 180 - ($total * (180/55)); echo "-$points$deg" ?>); } </style>
                    <style> #arc5::before { transform: rotate(<?php $total += $pointsArray[4]; $points = 180 - ($total * (180/55)); echo "-$points$deg" ?>); } </style>
                    <style> #arc6::before { transform: rotate(<?php $total += $pointsArray[5]; $points = 180 - ($total * (180/55)); echo "-$points$deg" ?>); } </style>
                    <style> #arc7::before { transform: rotate(<?php $total += $pointsArray[1]; $points = 180 - ($total * (180/55)); echo "-$points$deg" ?>); } </style>
                    </div>
                </div>
            </div>
            <div class="tasks">
                <?php
                    $i = 0;
                    $sectionQuery = $mysqli->query("SELECT * FROM sections");

                    echo "<h1>Submissions</h1>";

                    while ($i < 7) { // A loop for each section in the submissions view.
                        $section = $sectionQuery->fetch_object();
                        echo "
                        <div class='section-container'>
                        <div class='section-name-bar'>
                        <h2 class='section-header'>$section->sectionName</h2>
                        <button onclick='hideSection($i)' id='toggle-button$i'>Hide</button>
                        </div>
                        ";
                        
                        echo "<div id='section-hide$i'>";
                        $sectionID = $i + 1;
                        $query = $mysqli->query("SELECT * FROM submission WHERE author = $userID AND sectionID = $sectionID");
                        while ($obj = $query->fetch_object()) { // Outputs all submissions where the user is the author.
                            $isAuthor = true;
                            include("includes/submission-preview-fill.php");
                        }
                        $query = $mysqli->query("SELECT * FROM submission, submissioncoauthor WHERE submissioncoauthor.coauthor = $userID AND submissioncoauthor.submissionID = submission.submissionID AND submission.sectionID = $sectionID");
                        while ($obj = $query->fetch_object()) { // Outputs all submissions where the user is the coauthor.
                            $isAuthor = false;
                            include("includes/submission-preview-fill.php");
                        }
                        echo "
                        <div>
                        <form method='post'>
                        ";
                        if (!isset($_GET['user_override'])) { // Only gives the option for new submission if you're logged into the account being displayed.
                            echo "<button class='new-submission' name='new-submission' value='$section->sectionID'>+ Add New Submission</button>";
                        }
                        echo "
                        </form>
                        </div>
                        </div>
                        </div>
                        ";
                        $i++;
                    }
                ?>
            </div>
            <div class="new-password">
                <?php 
                if (!isset($_GET['user_override'])) {
                echo "
                <form method='post'>
                <input type='password' placeholder='New Password' name='password'>
                <button type='submit' name='new-password-button'>Change Password</button>
                </form>
                ";
                }
                ?>
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
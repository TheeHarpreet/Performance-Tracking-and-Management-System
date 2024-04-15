<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$userQuery = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $userQuery->fetch_object();

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['lang'])) {
    if (isset($_POST['new-submission'])) {
        $_SESSION['newSubmission'] = $_POST['new-submission'];
        header("Location: new-submission.php");
    } else if (isset($_POST['new-password-button'])) { // change password. Add the code to check the conditions here. I'd recommend doing the conditions as an include and use that here and in signup.
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $changePasswordQuery = $mysqli->prepare("UPDATE users SET password = ? WHERE userID = $userID");
        $changePasswordQuery->bind_param("s", $passwordHash);
        $changePasswordQuery->execute();
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
    <title><?php echo translate("Home"); ?> | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
    <script src="js/index.js"></script>
</head>
<body>
    <?php include_once("includes/header.php") ?>
        <div class="index-container">
            <?php
                if ($user->jobRole == "None") {
                    echo "
                    <p class='invalid-role'>" . translate("Your account doesn't have a role assigned. Please speak to an admin to assign you one.") . "</p>
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
                    <h1>" . translate("Select a researcher to view their work") . "</h1>
                    <div class='supervisor-user-selection'>
                    <p><a href='index.php'>" . translate("View your own work") . "</a></p>
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
                <h1><?php echo translate("Performance Overview"); ?></h1>
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

                            $approvedQuery = $mysqli->query("SELECT SUM(`approved`) AS amount FROM `submission` WHERE `author` = $author AND sectionID = $sectionID");
                            $result = $approvedQuery->fetch_object();
                            $currentAmount = $result->amount;

                            if ($currentAmount == 0){
                                echo "<p>" . translate($title) . ": " . translate("Not enough data to calculate scores") . "</p>";
                                array_push($pointsArray, 0);
                            } else {
                                if ($maxRange == $currentAmount) {
                                    $points = $maxPoints;
                                } else if ($minRange == $currentAmount) {
                                    $points = $minPoints;
                                } else  {
                                    $points = $minPoints + (($maxPoints - $minPoints) * (($currentAmount - $minRange) / ($maxRange - $minRange))); // Points calculation.
                                }
                                $pointsTotal += $points;
                                $percent = (($points-$minPoints)*100)/($maxPoints-$minPoints);
                                echo 
                                "<p>" . translate($title) . ":</p>
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

                    echo "<h1>" . translate("Submissions") . "</h1>";

                    while ($i < 7) { // A loop for each section in the submissions view.
                        $section = $sectionQuery->fetch_object();
                        echo "
                        <div class='section-container'>
                        <div class='section-name-bar'>
                        <h2 class='section-header'>".translate($section->sectionName)."</h2>
                        <button onclick='hideSection($i)' id='toggle-button$i'>" . translate("Hide") . "</button>
                        </div>
                        ";
                        
                        echo "<div id='section-hide$i'>";
                        $sectionID = $i + 1;
                        $submissionsQuery = $mysqli->query("SELECT * FROM submission WHERE author = $userID AND sectionID = $sectionID");
                        while ($obj = $submissionsQuery->fetch_object()) { // Outputs all submissions where the user is the author.
                            $isAuthor = true;
                            include("includes/submission-preview-fill.php");
                        }
                        $submissionsQuery = $mysqli->query("SELECT * FROM submission, submissioncoauthor WHERE submissioncoauthor.coauthor = $userID AND submissioncoauthor.submissionID = submission.submissionID AND submission.sectionID = $sectionID");
                        while ($obj = $submissionsQuery->fetch_object()) { // Outputs all submissions where the user is the coauthor.
                            $isAuthor = false;
                            include("includes/submission-preview-fill.php");
                        }
                        echo "
                        <div>
                        <form method='post'>
                        ";
                        if (!isset($_GET['user_override'])) { // Only gives the option for new submission if you're logged into the account being displayed.
                            echo "<button class='new-submission' name='new-submission' value='$section->sectionID'>+ " . translate("Add New Submission") . "</button>";
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
                <input type='password' placeholder='" . translate("New Password") . "' name='password'>
                <button type='submit' class='new-password-btn' name='new-password-button'>" . translate("Change Password") . "</button>
                </form>
                ";
                }
                ?>
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>

<?php include("includes/lang-config.php");
function translate($key) {
    $translations = array(
        /*
        "en" => array(
            "Home" => "Home",
            "Your account doesn't have a role assigned. Please speak to an admin to assign you one." => "Your account doesn't have a role assigned. Please speak to an admin to assign you one.",
            "Select a researcher to view their work" => "Select a researcher to view their work",
            "Not enough data to calculate scores" => "Not enough data to calculate scores",
            "Performance Overview" => "Performance Overview",
            "Submissions" => "Submissions",
            "Hide" => "Hide",
            "Add New Submission" => "Add New Submission",
            "New Password" => "New Password",
            "Change Password" => "Change Password",
            "Personal Particulars"=>"Personal Particulars",
            "Research And Development"=>"Research And Development",
            "Professional Consultations"=>"Professional Consultations",
            "Research Outcomes"=>"Research Outcomes",
            "Professional Recognition"=>"Professional Recognition",
            "Service To Community"=>"Service To Community"
        ),
        */
        "bm" => array(
            "Home" => "Laman Utama",
            "Your account doesn't have a role assigned. Please speak to an admin to assign you one." => "Akaun anda tidak mempunyai peranan yang diberikan. Sila bercakap dengan pentadbir untuk memberikan anda satu.",
            "Select a researcher to view their work" => "Pilih penyelidik untuk melihat kerja mereka",
            "Not enough data to calculate scores" => "Tidak cukup data untuk mengira skor",
            "Performance Overview" => "Gambaran Prestasi",
            "Submissions" => "Penyerahan",
            "Hide" => "Sembunyi",
            "Add New Submission" => "Tambah Penyerahan Baru",
            "New Password" => "Kata Laluan Baru",
            "Change Password" => "Tukar Kata Laluan",
            "Personal Particulars"=>"Khusus Peribadi", 
            "Professional Achievements"=>"Pencapaian Peribadi",
            "Research And Development"=>"Pembangun Penyilidik", 
            "Professional Consultations"=>"Perundingan profesional",
            "Research Outcomes"=>"Hasil Penyelidik",
            "Professional Recognition"=>"Pengiktirafan profesional", 
            "Service To Community"=>"Servis kepada Komuniti"
        )
    );

    $language = $_SESSION['language'];
    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
} ?>

<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $query->fetch_object();

if (isset($_GET["orderby"])) {
    $orderBy = $_GET["orderby"];
} else {
    $orderBy = "fname";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['lang'])) {
    if (isset($_POST['submission-id'])) {
        $_SESSION['viewSubmission'] = $_POST['submission-id'];
        header("Location: view-submission.php");
    } else {
        // Reloads the page with search contraints for the work.
        $name = "";
        $nameText = $_POST['submissionName'];
        if ($nameText != "") {
            $name = "&name=$nameText";
        }
        if ($_POST['status'] == "both") {
            $status = "1";
        } else if ($_POST['status'] == "accepted") {
            $status = "2";
        } else {
            $status = "3";
        }
        header("Location: manager-index.php?status=$status$name");
    }

}

?>
<?php include("includes/lang-config.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate("Home"); ?> | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
    <script src="js/hide-sections.js"></script>
</head>
<body id="manager-index">
    <?php include_once("includes/header.php") ?>
        <div class="container">
            <h1 class="segment-header"><?php echo translate("View Users Work"); ?></h1>
            <div class="segment-container" id="all-user-display">
                <table id="all-user-display-table">
                <tr class="accounts-table">
                    <th><?php echo translate("First name"); ?> <a class="sort" href="manager-index.php"><?php echo translate("Sort by"); ?></a></th>
                    <th><?php echo translate("Last name"); ?> <a class="sort" href="manager-index.php?orderby=lname"><?php echo translate("Sort by"); ?></a></th>
                    <th><?php echo translate("Job role"); ?> <a class="sort" href="manager-index.php?orderby=jobRole"><?php echo translate("Sort by"); ?></a></th>
                </tr>
                <?php 
                $user_accounts = mysqli_query($mysqli, "SELECT * FROM users WHERE jobRole = 'Supervisor' OR jobRole = 'Researcher' ORDER BY $orderBy");
                
                while ($obj = $user_accounts->fetch_object()) { // Outputs the list of supervisors and researchers. The hyperlink could be changed to cover the entire row.
                    $rowUserID = $obj->userID;
                    echo "
                        <tr>
                            <td><a href='index.php?user_override=$rowUserID'>$obj->fname</a></td>
                            <td><a href='index.php?user_override=$rowUserID'>$obj->lname</a></td>
                            <td><a href='index.php?user_override=$rowUserID'>$obj->jobRole</a></td>
                        </tr>
                    ";
                }
                ?>
                </table>
            </div>
            <div class="managers-work-section">
                <h1 class="segment-header"><?php echo translate("Search For Work"); ?></h1>
                <div class="segment-container">
                    <form method="post" class="search-paramaters-form">
                        <input type="text" name="submissionName" placeholder="Search for work by title">
                        <div class="status-div">
                            <p style="margin: 4px;"><?php echo translate("Status") . ":"; ?> </p>
                            <select name="status" id="work-type-select">
                                <option value="both" class="both-translate"><?php echo translate("Both"); ?></option> <!-- Class name used for translation. Add another class or change to a better name if you add css -->
                                <option value="not-accepted" class="needing-review-translate"><?php echo translate("Needing Review"); ?></option> <!-- Class name used for translation. Add another class or change to a better name if you add css -->
                                <option value="accepted" class="accepted-translate"><?php echo translate("Accepted"); ?></option> <!-- Class name used for translation. Add another class or change to a better name if you add css -->
                            </select>
                        </div>

                        <button name="search" class="search-translate" id="manager-user-search"><?php echo translate("Search"); ?></button> <!-- Class name used for translation. Add another class or change to a better name if you add css -->
                    </form>
                </div>
                <?php
                $nameMessage = "";
                $sectionMessage = " AND (approved > 0 OR (approved = 0 AND submitted = 1))";
                if (isset($_GET['status'])) {
                    $section = $_GET['status'];
                    if ($section == "1") { // Both.
                        $sectionMessage = " AND (approved > 0 OR (approved = 0 AND submitted = 1))";
                    } else if ($section == "2") { // Approved.
                        $sectionMessage = " AND approved > 0";
                    } else { // Waiting for approval.
                        $sectionMessage = " AND (approved = 0 AND submitted = 1)";
                    }
                }
                if (isset($_GET['name'])) {
                    $nameText = $_GET['name'];
                    $name = '%' . $nameText . '%';
                    $nameMessage = " AND  title LIKE '$name'";
                }
                $sectionQuery = $mysqli->query("SELECT * FROM sections");
                $i = 1;
                while ($section = $sectionQuery->fetch_object()) {
                    $submissionQuery = $mysqli->query("SELECT * FROM submission WHERE sectionID = $section->sectionID $sectionMessage $nameMessage");
                    if (mysqli_num_rows($submissionQuery) > 0) { // This makes sure the header is only output if there are results in that section.
                        echo "
                        <div class='section-container'>
                        <div class='section-name-bar'>
                        <h1 class='section-header'>$section->sectionName</h1>
                        <button onclick='hideSection($i)' id='toggle-button$i' class='hide'>" . translate("Hide") . "</button>
                        </div>";
                        echo "<div id ='section-hide$i'>";
                        while ($obj = $submissionQuery->fetch_object()) {
                            $isAuthor = true;
                            include("includes/submission-preview-fill.php");
                        }
                        echo "
                        </div>
                        </div>
                        </div>
                        </div>
                        ";
                    }
                    $i++;
                }
                ?>
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>


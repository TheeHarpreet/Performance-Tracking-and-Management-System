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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php include_once("includes/header.php") ?>
        <div class="container">
            <div class="managers-user-section">
                <div class="users-list">
                    <table>
                    <tr class="accounts-table">
                        <th>First Name <a class="sort" href="manager-index.php">Sort by</a></th>
                        <th>Last Name <a class="sort" href="manager-index.php?orderby=lname">Sort by</a></th>
                        <th>Job Role <a class="sort" href="manager-index.php?orderby=jobRole">Sort by</a></th>
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
            </div>
            <div class="managers-work-section">
                <p>Search work</p>
                <form method="post" class="search-paramaters">
                    <input type="text" name="submissionName" placeholder="Search work">
                    <p>Status</p>
                    <select name="status" id="work-type-select">
                        <option value="both">Both</option>
                        <option value="not-accepted">Needing Review</option>
                        <option value="accepted">Accepted</option>
                    </select>
                    <button name="search">Search</button>
                </form>
                <?php
                $sectionMessage = "";
                $nameMessage = "";
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
                while ($section = $sectionQuery->fetch_object()) {
                    $query = $mysqli->query("SELECT * FROM submission WHERE sectionID = $section->sectionID $sectionMessage $nameMessage");
                    if (mysqli_num_rows($query) > 0) { // This makes sure the header is only output if there are results in that section.
                        echo "
                        <div class='section-container'>
                        <div class='section-name-bar'>
                        <h2 class='section-header'>$section->sectionName</h2>
                        </div>
                        ";
                        while ($obj = $query->fetch_object()) {
                            $isAuthor = true;
                            include("includes/submission-preview-fill.php");
                        }
                        echo "
                        </div>
                        </div>
                        </div>
                        ";
                    }
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
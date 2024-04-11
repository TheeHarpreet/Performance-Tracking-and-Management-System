<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $query->fetch_object();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = "";
    if (isset($_POST['submissionName'])) {
        $nameText = $_POST['submissionName'];
        $name = "&name='$nameText'";
    }
    if ($_POST['status'] = "both") {
        $status = "1";
    } else if ($_POST['status'] = "accepted") {
        $status = "2";
    } else {
        $status = "3";
    }

    header("Location: manager-index.php?status='$status'$name");
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
                <p>Search user</p>
                <input type="text">
                <div class="user-search-results">
                    <tr class="accounts-table">
                        <th>First Name <a class="sort" href="admin-index.php?orderby=fname">Sort by</a></th>
                        <th>Last Name <a class="sort" href="admin-index.php?orderby=lname">Sort by</a></th>
                        <th>Job Role <a class="sort" href="admin-index.php?orderby=jobRole">Sort by</a></th>
                    </tr>
                    <tr>
                    <?php 
                    $user_accounts = mysqli_query($mysqli, "SELECT * FROM users WHERE jobRole = 'Supervisor' OR jobRole = 'Researcher'");
                            
                    while ($row = mysqli_fetch_assoc($user_accounts))
                    {
                    ?>
                    <?php $userID = $row['userID']; ?>
                        <a href="index.php?user_override=<?php  echo "$userID"; ?>">
                            <td><?php echo $row['fname']; ?></td>
                            <td><?php echo $row['lname']; ?></td>
                            <td><?php echo $row['jobRole']; ?></td>
                        </a>
                    </tr>
                    <?php
                        }
                    ?>                        
                    </tr>
                </div>
            </div>
            <div class="managers-work-section">
                <p>Search work</p>
                <form method="post" class="search-paramaters">
                    <input type="text" name="submissionName">
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
                    if ($section = "1") {
                        $sectionMessage = " AND (accepted > 0 OR (accepted = 0 AND submitted = 1))";
                    } else if ($section = "2") {
                        $sectionMessage = " AND accepted > 0";
                    } else {
                        $sectionMessage = " AND (accepted = 0 AND submitted = 1)";
                    }
                }
                if (isset($_GET['name'])) {
                    $name = $_GET['name'];
                    $nameMessage = " AND  name = '$name'";
                }
                $sectionQuery = $mysqli->query("SELECT * FROM sections $sectionMessage $nameMessage");
                while ($section = $sectionQuery->fetch_object()) {
                    echo "<div class='section-container'>";
                    echo "<div class='section-name-bar'>";
                    echo "<h2 class='section-header'>$section->sectionName</h2>";
                    echo "</div>";
                    $query = $mysqli->query("SELECT * FROM submission WHERE sectionID = $section->sectionID");
                    while ($obj = $query->fetch_object()) {
                        $isAuthor = true;
                        include("includes/submission-preview-fill.php");
                    }
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
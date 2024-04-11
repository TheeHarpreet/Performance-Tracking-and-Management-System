<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $query->fetch_object();

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
                <input type="text">
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $query->fetch_object();

if ($user->jobRole != "Admin") {
    header("Location: index.php");
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
        <div class="admin-container">
            <div class="account-list">
                <h2>List Of User Accounts</h2>
                <table>
                    <thead>
                        <tr>
                            <th>UserID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Job Role</th>
                        </tr>
                        <tr>
                        <?php
                            while ($user)
                            {
                        ?>
                            <td><?php echo $row['userID']; ?></td>
                            <td><?php echo $row['fname']; ?></td>
                            <td><?php echo $row['lname']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['password']; ?></td>
                            <td><?php echo $row['jobRole']; ?></td>
                            <td><a href="admin-index.php" class="edit-button">Edit</a></td>
                            <td><a href="admin-index.php?userID=<?php echo $row['userID']; ?>" class="delete-button">Delete</a></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </thead>
                </table>
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
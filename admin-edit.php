<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$userID = $_GET['userID'];
$query = $mysqli->query("SELECT * FROM `users` WHERE userID = $userID");
$user = $query->fetch_object();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php include_once("includes/simplified-header.php") ?>
        <div class="login-container">
            <form action="update.php?userID=<?php echo $userID; ?>" method="post">
                <label>Admin UserID:</label>
                <input type="number" name="admin-userID" value="<?php echo $row['userID']; ?>" required>   

                <label>FirstName:</label>
                <input type="text" name="fname" value="<?php echo $row['fname']; ?>" required>
                
                <label>LastName:</label>
                <input type="text" name="lname" value="<?php echo $row['lname']; ?>" required>
                
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $row['email']; ?>" required>
                
                <label>Job Role:</label>
                <input type="text" name="jobRole" value="<?php echo $row['jobRole']; ?>" required>

                <input type="submit" class="btn" name="submit" required>
            </form>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
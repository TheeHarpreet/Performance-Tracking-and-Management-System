<?php
require_once("includes/config.php");
session_start();
$_SESSION["user_id"] = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
    <script src="js/main.js" defer></script>
</head>
<body>
<?php include_once("includes/header.php") ?>
        <div class="container">
            <h1>Log In</h1>
            <h3>Email</h3>
            <input type="text" name="email">
            <h3>Password</h3>
            <input type="password" name="password">
            <button>Login</button>
            <p>Don't have an account? <a href="signup.php">Register here</a></p>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
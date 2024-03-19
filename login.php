<?php
require_once("includes/config.php");
session_start();
$_SESSION['user_id'] = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = $mysqli->prepare('SELECT userID, fname, lname FROM users WHERE email = ? AND password = ?'); 
    $query->bind_param('ss', $email, $password); 
    $query->execute();
    $result = $query->get_result();
    $obj = $result->fetch_object();
    
    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user_id'] = $obj->userID;
        $_SESSION['login'] = "successful";

        header("Location: index.php");
    }
}
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
    <?php include_once("includes/simplified-header.php") ?>
        <div class="login-container">
            <form class="login-form" method="post">
                <h1>Log In</h1>
                <h3>Email</h3>
                <input type="text" name="email" required>
                <h3>Password</h3>
                <input type="password" name="password" required>
                <button type="submit" class="login-button">Login</button>
                <p>Don't have an account? <a href="signup.php">Register here</a></p>
            </form>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
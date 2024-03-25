<?php
require_once("includes/config.php");
session_start();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // make sure email and password are not empty
    if (empty($email) || empty($password)) {
        $error = "Both email and password are required";
    } else {

        $sql = "SELECT userID, password FROM users WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        
        // Bind parameters and execute statement
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $userID = $row['userID'];
            $passwordHash = $row['password'];

            // Verify password
            if (password_verify($password, $passwordHash)) {
                $_SESSION['user_id'] = $userID; 
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password. <a href='../login.php'>Go back to login</a>";
            }
        } else {
            $error = "User not found. <a href='../login.php'>Go back to login</a>";
        }
        
        // Close statement
        $stmt->close();
    }
    if (isset($error)) {
        echo "<div>$error</div>";
    }
}
?>
<!--     
    // $email = $_POST['email'];
    // $password = $_POST['password'];
    
    // $query = $mysqli->prepare('SELECT userID, fname, lname FROM users WHERE email = ? AND password = ?'); 
    // $query->bind_param('ss', $email, $password); 
    // $query->execute();
    // $result = $query->get_result();
    // $obj = $result->fetch_object();
    
    // if (mysqli_num_rows($result) == 1) {
    //     $_SESSION['user_id'] = $obj->userID;
    //     $_SESSION['login'] = "successful";

    //     header("Location: index.php");
    // } -->

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
            <form class="login-form" method="post">
                <h1>Log In</h1>
                <h3>Email</h3>
                <input type="text" name="email" required>
                <h3>Password</h3>
                <input type="password" name="password" required>
                <button type="submit"  id="signup-button">Login</button>
                <p>Don't have an account? <a href="signup.php" class="login-change">Register here</a></p>
            </form>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
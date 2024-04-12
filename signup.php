<?php
require_once("includes/config.php");
session_start();

$errors = array();
$_SESSION['user_id'] = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['fname'];
    $surname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password1'];
    $passwordConfirm = $_POST['password2'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // I removed a check to see if the email and passwords aren't empty as there is already a check with "required" in the html.
    // I removed a check to see if the email is in the correct format as there is already a check with type="email".

    // Password length validation
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long.");
    }

    // Password confirmation check
    if ($password !== $passwordConfirm) {
        array_push($errors, "Passwords do not match.");
    }

    $emailCheck = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $emailCheck->bind_param('s', $email);
    $emailCheck->execute();
    $emailResult = $emailCheck->get_result();

    // Email in use check
    if (mysqli_num_rows($emailResult) > 0) {
        array_push($errors, "Email is already in use");
    }

    if (count($errors) == 0) {
        $sql = "INSERT INTO users (fname, lname, email, password, jobRole) VALUES (?, ?, ?, ?, 'None')";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $firstname, $surname, $email, $passwordHash);
        $stmt->execute();

        $_SESSION['user_id'] = mysqli_insert_id($mysqli);
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php include_once("includes/simplified-header.php") ?>
        <div class="signup-container">
            <form class="signup-form" method="post">
                <h1>Sign Up</h1>
                <div class="signup-div">
                    <div class="first-column">
                        <h3>First name</h3>
                        <input type="text" name="fname" required>
                        <h3>Surname</h3>
                        <input type="text" name="lname" required>
                        <h3>Email</h3>
                        <input type="email" name="email" required>
                    </div>
                    <div class="second-column">
                        <h3>Password</h3>
                        <input type="password" name="password1" required>
                        <h3>Confirm Password</h3>
                        <input type="password" name="password2" required>
                        <button type="submit" id="signup-button">Signup</button>
                        <p><a href="login.php" class="login-change">Have an account? Login</a></p>
                    </div>
                </div>
                <?php
                if (count($errors) > 0) { // Output errors
                    foreach ($errors as $error) {
                        echo "<div class='error-message'>$error</div>";
                    }
                }
                ?>
            </form>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
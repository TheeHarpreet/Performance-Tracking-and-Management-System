<?php
require_once("includes/config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['fname'];
    $surname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password1'];
    $passwordConfirm = $_POST['password2'];

    // Hashing algorithm
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $errors = array();

    // Basic validation
    if (empty($firstname) || empty($surname) || empty($email) || empty($password) || empty($passwordConfirm)) {
        array_push($errors, "All fields are required");
    }

    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }

    // Password length validation
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }

    // Password confirmation check
    if ($password !== $passwordConfirm) {
        array_push($errors, "Passwords do not match");
    }

    if (count($errors) == 0) {
        $sql = "INSERT INTO users (fname, lname, email, password, jobRole) VALUES (?, ?, ?, ?, 'None')";
        $stmt = $mysqli->prepare($sql);
        
        if (!$stmt) {
            die("SQL statement preparation failed: " . $mysqli->error);
        }

        $stmt->bind_param("ssss", $firstname, $surname, $email, $passwordHash);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>You are registered successfully.</div>";
            header("Location: index.php");
            exit();
        } else {
            die("Error executing statement: " . $stmt->error);
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<div>$error</div>";
        }
    }
}
?>

<!-- // if ($_SERVER['REQUEST_METHOD'] == "POST") {
//     $firstname = $_POST['fname'];
//     $surname = $_POST['lname'];
//     $email = $_POST['email'];
//     $password = $_POST['password1'];
//     $passwordConfirm = $_POST['password2'];

//     if ($password == $passwordConfirm) {
//         $emailCheck = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
//         $emailCheck->bind_param('s', $email);
//         $emailCheck->execute();
//         $emailResult = $emailCheck->get_result();
    
//         if (mysqli_num_rows($emailResult) == 0) {
//             $query = $mysqli->prepare("INSERT INTO users (fname, lname, email, password, jobRole) VALUES (?, ?, ?, ?, 'None')");
//             $query->bind_param('ssss', $firstname, $surname, $email, $password);
//             $query->execute();
//             $_SESSION['user_id'] = mysqli_insert_id($mysqli);
//             $_SESSION['signup'] = "successful";
            
//             header("Location: index.php");
//         }
//     }

// } -->

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
                <h3>First name</h3>
                <input type="text" name="fname" required>
                <h3>Surname</h3>
                <input type="text" name="lname" required>
                <h3>Email</h3>
                <input type="text" name="email" required>
                <h3>Password</h3>
                <input type="password" name="password1" required>
                <h3>Confirm Password</h3>
                <input type="password" name="password2" required>
                <button type="submit" id="signup-button">Signup</button>
                <p>Have an account? <a href="login.php" class="login-change">Login</a></p>
            </form>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
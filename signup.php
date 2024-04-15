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

    // Hashing algorithm
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Basic validation
    if (empty($firstname) || empty($surname) || empty($email) || empty($password) || empty($passwordConfirm)) {
        array_push($errors, "All fields are required.");
    }

    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid.");
    }

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
    if (mysqli_num_rows($emailResult) > 0) {
        array_push($errors, "Email is already in use");
    }

    if (count($errors) == 0) {
        $sql = "INSERT INTO users (fname, lname, email, password, jobRole) VALUES (?, ?, ?, ?, 'None')";
        $stmt = $mysqli->prepare($sql);
        
        if (!$stmt) {
            die("SQL statement preparation failed: " . $mysqli->error);
        }

        $stmt->bind_param("ssss", $firstname, $surname, $email, $passwordHash);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>You have registered successfully.</div>";
            $_SESSION['user_id'] = mysqli_insert_id($mysqli);
            header("Location: index.php");
            exit();
        } else {
            die("Error executing statement: " . $stmt->error);
        }
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
                <h1><?php echo translate("Sign up"); ?></h1>
                <div class="signup-div">
                    <div>
                        <h3><?php echo translate("First name"); ?></h3>
                        <input type="text" name="fname" required>
                        <h3><?php echo translate("Surname"); ?></h3>
                        <input type="text" name="lname" required>
                        <h3><?php echo translate("Email"); ?></h3>
                        <input type="text" name="email" required>
                        <h3><?php echo translate("Password"); ?></h3>
                        <input type="password" name="password1" required>
                        <h3><?php echo translate("Confirm Password"); ?></h3>
                        <input type="password" name="password2" required>
                        <button type="submit" id="signup-button"><?php echo translate("Signup"); ?></button>
                    </div>
                </div>
                <?php
                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        echo "<div class='error-message'>$error</div>";
                    }
                }
                ?>
                <p class="account-link"><?php echo translate("Have an account?"); ?> <a href="login.php" class="login-change"><?php echo translate("Login"); ?></a></p>
            </form>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>

<?php include("includes/lang-config.php");
function translate($key) {
    $translations = array(
        "en" => array(
            "Log In" => "Log In",
            "Email" => "Email",
            "Password" => "Password",
            "Invalid password" => "Invalid password",
            "User not found" => "User not found",
            "Login" => "Login",
            "Don't have an account?" => "Don't have an account?",
            "Register here" => "Register here",
            "Language" => "Language",
            "English" => "English",
            "BM" => "BM",
            "All fields are required." => "All fields are required.",
            "Email is not valid." => "Email is not valid.",
            "Password must be at least 8 characters long." => "Password must be at least 8 characters long.",
            "Passwords do not match." => "Passwords do not match.",
            "Email is already in use" => "Email is already in use",
            "You have registered successfully." => "You have registered successfully.",
            "Sign up" => "Sign up",
            "First name" => "First name",
            "Surname" => "Surname",
            "Confirm Password" => "Confirm Password",
            "Have an account?" => "Have an account?",
            "Login" => "Login"

        ),
        "bm" => array(
            "Log In" => "Log Masuk",
            "Email" => "Emel",
            "Password" => "Kata Laluan",
            "Invalid password" => "Kata Laluan tidak sah",
            "User not found" => "Pengguna tidak dijumpai",
            "Login" => "Log Masuk",
            "Don't have an account?" => "Tiada akaun?",
            "Register here" => "Daftar di sini",
            "Language" => "Bahasa",
            "BM" => "BM",
            "All fields are required." => "Semua medan diperlukan.",
            "Email is not valid." => "Emel tidak sah.",
            "Password must be at least 8 characters long." => "Kata laluan mesti sekurang-kurangnya 8 suku kata.",
            "Passwords do not match." => "Kata laluan tidak sepadan.",
            "Email is already in use" => "Emel telah digunakan",
            "You are registered successfully." => "Anda telah berdaftar dengan berjaya.",
            "Sign up" => "Daftar",
            "First name" => "Nama pertama",
            "Surname" => "Nama keluarga",
            "Confirm Password" => "Sahkan Kata Laluan",
            "Have an account?" => "Sudah mempunyai akaun?",
            "Login" => "Log Masuk"
        )
    );

    $language = $_SESSION['language'];
    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
} ?>
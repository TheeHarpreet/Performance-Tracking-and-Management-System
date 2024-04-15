<?php
require_once("includes/config.php");
session_start();

$errors = array();
$_SESSION['user_id'] = 0;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!isset($_POST['lang'])) {
        $email = $_POST["email"];
        $password = $_POST["password"];
    
        // make sure email and password are not empty
        if (empty($email) || empty($password)) {
            array_push($errors, "Both email and password are required");
        }
         else {
    
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
                } 
                else {
                    array_push($errors, "Invalid password");
                }
            } else {
                array_push($errors, "User not found");
            }
            
            // Close statement
            $stmt->close();
        }
    }
}
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
        <form class="login-form" method="post">
            <div class="login-input">
                <h1><?php echo translate("Log In"); ?></h1>
                <h3><?php echo translate("Email"); ?></h3>
                <input type="email" name="email" required>
                <h3><?php echo translate("Password"); ?></h3>
                <input type="password" name="password" required>
                <button type="submit" id="signup-button"><?php echo translate("Login"); ?></button>
            </div>
            <p class="account-link"><?php echo translate("Don't have an account?"); ?> <a href="signup.php" class="login-change"><?php echo translate("Register here"); ?></a></p>
        </form>
    </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>



<?php include("includes/lang-config.php");
function translate($key) {
    $translations = array(
        /*
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
            "BM" => "BM"
        ),
        */
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
            "English" => "Inggeris",
            "BM" => "BM"
        )
    );

    $language = $_SESSION['language'];
    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
} ?>
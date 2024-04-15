<?php
session_start();

// Function to set language session variable
function setLanguage($lang) {
    $_SESSION['language'] = $lang;
}

// Check if language is set in session, default to English if not set
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}

// Function to translate text based on selected language
function translate($key) {
    // Define translations
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
            "BM" => "BM"
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
            "English" => "Inggeris",
            "BM" => "BM"
        )
    );

    // Get selected language from session
    $language = $_SESSION['language'];

    // Return translation if available, otherwise return key itself
    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
}

// Check if language selection button is clicked
if(isset($_POST['lang'])) {
    setLanguage($_POST['lang']);
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
    <?php include("includes/simplified-header.php"); ?>
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
</body>
</html>

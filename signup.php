<?php
session_start();


function setLanguage($lang) {
    $_SESSION['language'] = $lang;
}

if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}


function translate($key) {
    // translation
    $translations = array(
        "en" => array(
            "Sign Up" => "Sign Up",
            "First name" => "First name",
            "Surname" => "Surname",
            "Email" => "Email",
            "Password" => "Password",
            "Confirm Password" => "Confirm Password",
            "Signup" => "Signup",
            "Have an account? Login" => "Have an account? Login",
            "Password must be at least 8 characters long." => "Password must be at least 8 characters long.",
            "Passwords do not match." => "Passwords do not match.",
            "Email is already in use" => "Email is already in use",
            "Language" => "Language",
            "English" => "English",
            "BM" => "BM"
        ),
        "bm" => array(
            "Sign Up" => "Daftar",
            "First name" => "Nama Pertama",
            "Surname" => "Nama Akhir",
            "Email" => "Emel",
            "Password" => "Kata Laluan",
            "Confirm Password" => "Sahkan Kata Laluan",
            "Signup" => "Daftar masuk",
            "Have an account? Login" => "Sudah mempunyai akaun? Log Masuk",
            "Password must be at least 8 characters long." => "Kata laluan mesti sekurang-kurangnya 8 suku kata.",
            "Passwords do not match." => "Kata laluan tidak sama.",
            "Email is already in use" => "Emel sudah digunakan",
            "Language" => "Bahasa",
            "English" => "Inggeris",
            "BM" => "BM"
        )
    );


    $language = $_SESSION['language'];

    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
}

if(isset($_POST['lang'])) {
    setLanguage($_POST['lang']);
}

$errors = array();
$_SESSION['user_id'] = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['fname'];
    $surname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password1'];
    $passwordConfirm = $_POST['password2'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Password length validation
    if (strlen($password) < 8) {
        array_push($errors, translate("Password must be at least 8 characters long."));
    }

    // Password confirmation check
    if ($password !== $passwordConfirm) {
        array_push($errors, translate("Passwords do not match."));
    }

    $emailCheck = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $emailCheck->bind_param('s', $email);
    $emailCheck->execute();
    $emailResult = $emailCheck->get_result();

    // Email in use check
    if (mysqli_num_rows($emailResult) > 0) {
        array_push($errors, translate("Email is already in use"));
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
    <title><?php echo translate("Sign up"); ?> | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <div class="language-switcher">
        <form method="post">
            <button type="submit" name="lang" value="en"><?php echo translate("English"); ?></button>
            <button type="submit" name="lang" value="bm"><?php echo translate("BM"); ?></button>
        </form>
    </div>

    <?php include_once("includes/simplified-header.php") ?>
        <div class="signup-container">
            <form class="signup-form" method="post">
                <h1><?php echo translate("Sign Up"); ?></h1>
                <div class="signup-div">
                    <div class="first-column">
                        <h3><?php echo translate("First name"); ?></h3>
                        <input type="text" name="fname" required>
                        <h3><?php echo translate("Surname"); ?></h3>
                        <input type="text" name="lname" required>
                        <h3><?php echo translate("Email"); ?></h3>
                        <input type="email" name="email" required>
                    </div>
                    <div class="second-column">
                        <h3><?php echo translate("Password"); ?></h3>
                        <input type="password" name="password1" required>
                        <h3><?php echo translate("Confirm Password"); ?></h3>
                        <input type="password" name="password2" required>
                        <button type="submit" id="signup-button"><?php echo translate("Signup"); ?></button>
                        <p><a href="login.php" class="login-change"><?php echo translate("Have an account? Login"); ?></a></p>
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

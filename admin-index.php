<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$errors = array();
$query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $query->fetch_object();

if (isset($_GET["orderby"])) {
    $orderBy = $_GET["orderby"];
} else {
    $orderBy = "userID";
}

if (isset($_POST['new-password-button'])) {
    $password = $_POST['password1'];
    $passwordConfirm = $_POST['password2'];

    // Password length validation
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long.");  // NeedsTranslation
    }

    // Password confirmation check
    if ($password !== $passwordConfirm) {
        array_push($errors, "Passwords do not match.");  // NeedsTranslation
    }

    if (count($errors) == 0) {
        $passwordHash = password_hash($_POST['password1'], PASSWORD_DEFAULT);
        $changePasswordQuery = $mysqli->prepare("UPDATE users SET password = ? WHERE userID = $userID");
        $changePasswordQuery->bind_param("s", $passwordHash);
        $changePasswordQuery->execute();
    }
}

// block query 
if (isset($_GET['block'])){ // Setting the password to nothing stops the account from being accessible.
    $stmt = $mysqli->prepare("UPDATE users SET `password` = '' WHERE userID = ?");
    $stmt->bind_param('s', $_GET['block'] );
    $stmt->execute();
    header("Location: admin-index.php");
}

// unblock query
if (isset($_GET['unblock'])){
    $passwordHash = password_hash("katalaluan123", PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("UPDATE users SET `password` = ? WHERE userID = ?");
    $stmt->bind_param('ss', $passwordHash, $_GET['unblock'] );
    $stmt->execute();
    header("Location: admin-index.php");
}

// redirects the user if they're not an admin.
if ($user->jobRole != "Admin") {
    header("Location: index.php");
}
?>
<?php include("includes/lang-config.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate("Home"); ?> | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php include_once("includes/header.php") ?>
        <div class="admin-container">
        <?php
            $resetQuery = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
            $passwordCheck = $resetQuery->fetch_object();
            if (password_verify("katalaluan123", $passwordCheck->password)) {
                echo "
                <div class='change-password'>
                <h1>Please reset your password</h1>
                <p>Your password has been reset, your account is not secure until the password has been changed</p>
                <form method='post'>
                <p>Password</p>
                <input type='password' class='new-password-input' placeholder='" . translate("New Password") . "' name='password1'>
                <p>Confirm Password</p>
                <input type='password' class='new-password-input' placeholder='" . translate("New Password") . "' name='password2'>
                ";
                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        echo "<div class='error-message'>$error</div>";
                    }
                }
                echo "
                <button type='submit' class='new-password-btn' name='new-password-button'>" . translate("Change Password") . "</button>
                </form>
                </div>
                ";
            }
            ?>
            <div class="create-account">
                <?php
                    if($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['new-password-button'])){
                        $fname = $_POST['fname'];
                        $lname = $_POST['lname'];
                        $email = $_POST['email'];
                        $password = $_POST['password'];

                    $email_verify = mysqli_query($mysqli, "SELECT `email` FROM `users` WHERE `email`='$email'");


                    // verifying if the email is already in use or not.
                    if (mysqli_num_rows($email_verify) !=0){
                        echo "<div class='message'
                                <p>" . translate("This email is already in use, please try another email.") . "</p>
                            </div> <br>";
                    }
                    else {
                        mysqli_query($mysqli, "INSERT INTO users(fname, lname, email, password) VALUES ('$fname', '$lname', '$email', '$password')");
                    }
                    }
                ?>
                <h1 class="segment-header"><?php echo translate("Create Admin Account"); ?></h1>
                <div class="segment-container">
                    <form action="admin-index.php?userID=<?php echo $userID; ?>" method="post" class="new-admin-form">
                        <div class="new-admin-inputs">
                            <div>
                                <p><?php echo translate("First Name"); ?></p>
                                <input type="text" name="fname" id="fname" required>
                                <p><?php echo translate("Last Name"); ?></p>
                                <input type="text" name="lname" id="lname" required>
                            </div>
                            <div>
                                <p><?php echo translate("Email"); ?></p>
                                <input type="email" name="email" id="email" required>
                                <p><?php echo translate("Password"); ?></p>
                                <input type="password" name="password" id="password" required>
                            </div>
                        </div>
                        <input type="submit" class="btn" name="submit" value="<?php echo translate("Create Account"); ?>" required>
                    </form>
                </div>

            </div>
            <div class="account-list">
                <h1 class="segment-header"><?php echo translate("List Of User Accounts"); ?></h1>
                <table class="segment-container">
                    <thead>
                        <tr class="accounts-table">
                            <th><div><?php echo translate("UserID"); ?> <a class="sort" href="admin-index.php"><?php echo translate("Sort by"); ?></a></div></th>
                            <th><div><?php echo translate("First Name"); ?> <a class="sort" href="admin-index.php?orderby=fname"><?php echo translate("Sort by"); ?></a></div></th>
                            <th><div><?php echo translate("Last Name"); ?> <a class="sort" href="admin-index.php?orderby=lname"><?php echo translate("Sort by"); ?></a></div></th>
                            <th><div><?php echo translate("Email"); ?> <a class="sort" href="admin-index.php?orderby=email"><?php echo translate("Sort by"); ?></a></div></th>
                            <th><div><?php echo translate("Job Role"); ?> <a class="sort" href="admin-index.php?orderby=jobRole"><?php echo translate("Sort by"); ?></a></div></th>
                            <th><?php echo translate("Edit"); ?></th>
                            <th><?php echo translate("Delete"); ?></th>
                        </tr>
                        <tr>
                        <?php
                        $userAccounts = mysqli_query($mysqli, "SELECT * FROM users ORDER BY $orderBy");
                        while ($row = $userAccounts->fetch_object())
                        {
                            echo "
                            <tr>
                            <td>$row->userID</td>
                            <td>$row->fname</td>
                            <td>$row->lname</td>
                            <td>$row->email</td>
                            <td>$row->jobRole</td>
                            <td><a href='admin-edit.php?userID=$row->userID' class='edit-button'>" . translate("Edit") . "</a></td>
                            <td><a href='admin-index.php?" . ($row->password == '' ? 'unblock=' . $row->userID . "' class='unblock-button'>" . translate("Unblock") : 'block=' . $row->userID . "' class='delete-button'>" . translate("Block")) . "</a></td>
                            </tr>
                            ";
                        }
                        ?>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>



<?php
$resetQuery = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$passwordCheck = $resetQuery->fetch_object();
if (password_verify("katalaluan123", $passwordCheck->password)) {
    echo "
    <h1 class='segment-header'>" . translate("Please reset your password") . "</h1>
    <div class='segment-container'>
    <h2 style='text-align: center;'>" . translate("Your password has been reset, your account is not secure until the password has been changed") . "</h2>
    <form method='post' class='update-password-form'>
    <p>" . translate("Password") . "</p>
    <input type='password' class='new-password-input' placeholder='" . translate("New Password") . "' name='password1'>
    <p>" . translate("Confirm Password") . "</p>
    <input type='password' class='new-password-input' placeholder='" . translate("New Password") . "' name='password2'>
    ";
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='error-message'>$error</div>";
        }
    }
    echo "
    <button type='submit' id='update-password-button' name='new-password-button'>" . translate("Change Password") . "</button>
    </form>
    </div>
    ";
}
?>
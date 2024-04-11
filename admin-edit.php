<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$userID = $_GET['userID'];
$query = $mysqli->query("SELECT * FROM `users` WHERE userID = $userID");
$user = $query->fetch_object();

if (isset($_GET['reset'])) {
    $passwordHash = password_hash("Password123", PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("UPDATE users SET `password` = ? WHERE userID = ?");
    $stmt->bind_param('ss', $passwordHash, $_GET['userID'] );
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    if ($user->jobRole != "Admin") {
        $jobRole = $_POST['jobRole'];
        $stmt = $mysqli->prepare("UPDATE users SET fname = ?, lname = ?, email = ?, jobRole = ? WHERE userID = ?");
        $stmt->bind_param("sssss", $fname, $lname, $email, $jobRole, $userID);
    } else {
        $stmt = $mysqli->prepare("UPDATE users SET fname = ?, lname = ?, email = ? WHERE userID = ?");
        $stmt->bind_param("ssss", $fname, $lname, $email, $userID);
    }
    $stmt->execute();
    header("Location: admin-index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User Details | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php include_once("includes/simplified-header.php") ?>
        <div class="login-container">
            <form method="post">

                <label>FirstName:</label>
                <input type="text" name="fname" value="<?php echo $user->fname; ?>" required>
                
                <label>LastName:</label>
                <input type="text" name="lname" value="<?php echo $user->lname; ?>" required>
                
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $user->email; ?>" required>
                
                <?php if ($user->jobRole != "Admin") {
                    echo "
                    <label>Job Role:</label>
                    <select id='select' name='jobRole'>
                        <option value='None'"; if ($user->jobRole == 'None') { echo 'selected'; } echo ">None</option>
                        <option value='Researcher'"; if ($user->jobRole == 'Researcher') { echo 'selected'; } echo ">Researcher</option>
                        <option value='Supervisor'"; if ($user->jobRole == 'Supervisor') { echo 'selected'; } echo ">Supervisor</option>
                        <option value='Manager'"; if ($user->jobRole == 'Manager') { echo 'selected'; } echo ">Manager</option>
                    </select>
                    ";
                }
                ?>
                <button type="submit" class="submit-button">Update</button>
                <p><a href="admin-edit.php?userID=<?php echo $userID; ?>&reset=1" class="reset-link">Reset Password</a></p>
                 <p>Passwords are reset to "Password123"</p>
                
                
            </form>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
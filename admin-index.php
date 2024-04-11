<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $query->fetch_object();

if (isset($_GET["orderby"])) {
    $orderBy = $_GET["orderby"];
} else {
    $orderBy = "userID";
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
    $passwordHash = password_hash("password123", PASSWORD_DEFAULT);
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | MIROS</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php include_once("includes/header.php") ?>
        <div class="admin-container">
            <div class="create-account">
                <?php
                    if($_SERVER["REQUEST_METHOD"] == "POST"){
                        $fname = $_POST['fname'];
                        $lname = $_POST['lname'];
                        $email = $_POST['email'];
                        $password = $_POST['password'];

                    $email_verify = mysqli_query($mysqli, "SELECT `email` FROM `users` WHERE `email`='$email'");


                    // verifying if the email is already in use or not.
                    if (mysqli_num_rows($email_verify) !=0){
                        echo "<div class='message'
                                <p>This email is already in use, please try another email.</p>
                            </div> <br>";
                    }
                    else {
                        mysqli_query($mysqli, "INSERT INTO users(fname, lname, email, password) VALUES ('$fname', '$lname', '$email', '$password')");
                    }
                    }
                ?>
                <form action="admin-index.php?userID=<?php echo $userID; ?>" method="post" class="new-admin-form">
                <h2>Create User Account</h2>
                <div class="new-admin-inputs">
                    <div>
                        <p>First Name</p>
                        <input type="text" name="fname" id="fname" required>
                        <p>Last Name</p>
                        <input type="text" name="lname" id="lname" required>
                    </div>
                    <div>
                        <p>Email</p>
                        <input type="email" name="email" id="email" required>
                        <p>Password</p>
                        <input type="password" name="password" id="password" required>
                    </div>
                </div>
                <input type="submit" class="btn" name="submit" value="Create Account" required>
                </form>
            </div>
            <div class="account-list">
                <h2>List Of User Accounts</h2>
                <table>
                    <thead>
                        <tr class="accounts-table">
                            <th><div>UserID <a class="sort" href="admin-index.php">Sort by</a></div></th>
                            <th><div>First Name <a class="sort" href="admin-index.php?orderby=fname">Sort by</a></div></th>
                            <th><div>Last Name <a class="sort" href="admin-index.php?orderby=lname">Sort by</a></div></th>
                            <th><div>Email <a class="sort" href="admin-index.php?orderby=email">Sort by</a></div></th>
                            <th><div>Job Role <a class="sort" href="admin-index.php?orderby=jobRole">Sort by</a></div></th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        <tr>
                        <?php
                        $user_accounts = mysqli_query($mysqli, "SELECT * FROM users ORDER BY $orderBy");
                            
                            while ($row = mysqli_fetch_assoc($user_accounts))
                            {
                        ?>
                            <td><?php echo $row['userID']; ?></td>
                            <td><?php echo $row['fname']; ?></td>
                            <td><?php echo $row['lname']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['jobRole']; ?></td>
                            <td><a href="admin-edit.php?userID=<?php echo $row['userID']; ?>" class="edit-button">Edit</a></td>
                            <?php
                            if ($row['password'] == "") { // a blank password means the account is blocked.
                                echo "<td><a href='admin-index.php?unblock="; echo $row['userID']; echo "' class='unblock-button'>Unblock</a></td>";
                            } else {
                                echo "<td><a href='admin-index.php?block="; echo $row['userID']; echo "' class='delete-button'>Block</a></td> ";
                            }
                            ?>
                        </tr>
                        <?php
                            }
                        ?>
                    </thead>
                </table>
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>
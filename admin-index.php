<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $query->fetch_object();

// edit query
if (isset($_GET['userID'])){
    $id=$_GET['userID'];
    $firstname=$_POST['fname'];
    $lastname=$_POST['lname'];
    $email=$_POST['email'];
    $jobRole=$_POST['jobRole'];
    mysqli_query($mysqli, "UPDATE `users` SET `fname`='$firstname', `lname`='$lastname', `email`='$email', `jobRole`='$jobRole' WHERE `userID`='$id'");
    header("Location: index.php");
}

// delete query 
if (isset($_GET['userID'])){
    $id=$_GET['userID'];
    mysqli_query($mysqli, "DELETE FROM `users` WHERE `userID`='$id'");
    header("Location: index.php");
}

// creating an admin account
if(isset($_POST['submit'])){
    $admin_userID = $_POST['userID'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $jobRole = $_POST['jobRole'];


// verifying if the email is already in use or not
$email_verify = mysqli_query($mysqli, "SELECT `email` FROM `users` WHERE `email`='$email'");

if (mysqli_num_rows($email_verify) !=0){
    echo "<div class='message'
            <p>This email is already in use, please try another email.</p>
        </div> <br>";
    echo "<a href='javascript:script.history.back()'><button class = 'btn'>Go Back</button>";
}
else{
    mysqli_query($mysqli, "INSERT INTO `user_accounts(userID, fname, lname, email, password, jobRole) VALUES ('$admin_userID', '$fname', '$lname', '$email', '$password', '$jobRole')");
    echo "<div class='message'
            <p> Account has been created.</p>
        </div> <br>";
    echo "<a href='admin-index.php'><button class = 'btn'>Go Back</button>";
}
}else{

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
                <form action="admin-update.php?userID=<?php echo $id; ?>" method="post" class="basic">
                    <label for="admin-userID">Admin UserID</label>
                    <input type="number" name="admin-userID" id="<?php echo $admin_userID; ?>" required>
                    
                    <label for="fname">FirstName</label>
                    <input type="text" name="fname" id="fname" required>
                    
                    <label for="lname">LastName</label>
                    <input type="text" name="lname" id="lname" required>
                    
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                    
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>

                    <input type="submit" class="btn" name="submit" value="create account" required>
                </form>
            </div>
            <div class="account-list">
                <h2>List Of User Accounts</h2>
                <table>
                    <thead>
                        <tr>
                            <th>UserID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Job Role</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        <tr>
                        <?php
                        $user_accounts = mysqli_query($mysqli, "SELECT * FROM users");
                            
                            while ($row = mysqli_fetch_assoc($user_accounts))
                            {
                        ?>
                            <td><?php echo $row['userID']; ?></td>
                            <td><?php echo $row['fname']; ?></td>
                            <td><?php echo $row['lname']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['jobRole']; ?></td>
                            <td><a href="admin-edit.php?userID=<?php echo $row['userID']; ?>" class="edit-button">Edit</a></td>
                            <td><a href="admin-index.php?userID=<?php echo $row['userID']; ?>" class="delete-button">Delete</a></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </thead>
                </table>
            </div>
        </div>
    <?php include_once("includes/footer.php") ?>
    <?php } ?>
</body>
</html>
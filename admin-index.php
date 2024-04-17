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
                                <p>" . translate("This email is already in use, please try another email.") . "</p>
                            </div> <br>";
                    }
                    else {
                        mysqli_query($mysqli, "INSERT INTO users(fname, lname, email, password) VALUES ('$fname', '$lname', '$email', '$password')");
                    }
                    }
                ?>
                <form action="admin-index.php?userID=<?php echo $userID; ?>" method="post" class="new-admin-form">
                <h2><?php echo translate("Create User Account"); ?></h2>
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
            <div class="account-list">
                <h2><?php echo translate("List Of User Accounts"); ?></h2>
                <table>
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

<?php include("includes/lang-config.php");
function translate($key) {
    $translations = array(
        /*
        "en" => array(
            "Home" => "Home",
            "Create User Account" => "Create User Account",
            "This email is already in use, please try another email." => "This email is already in use, please try another email.",
            "First Name" => "First Name",
            "Last Name" => "Last Name",
            "Email" => "Email",
            "Password" => "Password",
            "Create Account" => "Create Account",
            "List Of User Accounts" => "List Of User Accounts",
            "UserID" => "UserID",
            "Sort by" => "Sort by",
            "Job Role" => "Job Role",
            "Edit" => "Edit",
            "Delete" => "Delete",
            "Unblock" => "Unblock",
            "Block" => "Block"
        ),
        */
        "bm" => array(
            "Home" => "Halaman Utama",
            "Create User Account" => "Cipta Akaun Pengguna",
            "This email is already in use, please try another email." => "Emel ini sudah digunakan, sila cuba emel yang lain.",
            "First Name" => "Nama Pertama",
            "Last Name" => "Nama Akhir",
            "Email" => "Emel",
            "Password" => "Kata Laluan",
            "Create Account" => "Cipta Akaun",
            "List Of User Accounts" => "Senarai Akaun Pengguna",
            "UserID" => "ID Pengguna",
            "Sort by" => "Disusun mengikut",
            "Job Role" => "Peranan Pekerjaan",
            "Edit" => "Edit",  // NeedsTranslation
            "Delete" => "Padam",
            "Unblock" => "Buka Kunci",
            "Block" => "Kunci"
        )
    );

    $language = $_SESSION['language'];
    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
} ?>

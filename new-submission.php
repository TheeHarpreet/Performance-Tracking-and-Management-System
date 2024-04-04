<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");

$author = $_SESSION['user_id'];
$type = $_SESSION['newSubmission'];
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $comments = $_POST["comments"];
    $dateSubmitted = date("Y-m-d H:i:s");

    if (empty($title) || empty($comments) || empty($dateSubmitted) || empty($type)) {
        $error = "All fields are required";
    }
    else{

    // File upload handling
    $fileUploaded = false;
    $fileCount = count($_FILES['file']['name']);

    // need to work on this
    $uploadDir = "submissionfiles/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    for ($i = 0; $i < $fileCount; $i++) { 
        $fileType = pathinfo($_FILES["file"]["name"][$i], PATHINFO_EXTENSION);
        $customFileName = "upload" . $submissionID . "." . $fileType;
        $fileTmpName = $_FILES["file"]["tmp_name"][$i];

        $filePath = $uploadDir . $customFileName;
        if (move_uploaded_file($fileTmpName, $filePath)) {
            $fileUploaded = true;

            // Insert details for submission
            $stmt = $mysqli->prepare("INSERT INTO submission (title, author, dateSubmitted, type, comments, submitted, approved) VALUES (?, ?, ?, ?, ?, 0, 0)");
            $stmt->bind_param("sssss", $title, $author, $dateSubmitted, $type, $comments);
            $stmt->execute();
            $submissionID = $stmt->insert_id;

            // Insert file details into the file table
            $stmt = $mysqli->prepare("INSERT INTO file (fileType, name, author) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fileType, $customFileName, $author);
            $stmt->execute();
            $fileID = $stmt->insert_id;

            // Insert details into submissionfile table
            $stmt = $mysqli->prepare("INSERT INTO submissionfile (submissionID, fileID) VALUES (?, ?)");
            $stmt->bind_param("ii", $submissionID, $fileID);
            $stmt->execute();
        } else {
            $error = "File upload failed.";
            break;
        }
    }

    if ($fileUploaded) {
        $successMessage = "Files uploaded successfully.";
    }
}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Form</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
<?php include_once("includes/header.php") ?>
    <div class="container">
        <h2>Submission Form</h2>
        <?php if (!empty($error)) : ?>
            <div><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($successMessage)) : ?>
            <div><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" placeholder="Title" required>
            </div>
            <div>
                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments"  placeholder="Comments" rows="3"></textarea>
            </div>
            <div>
                <label for="file">Upload File:</label>
                <input type="file" id="file" name="file[]" multiple required>
            </div>
            <!-- <div>
                <label for="type">Type:</label>
                <select name="type" id="type" required>
                    <option value="">Select Type</option>
                    <option value="A">Personal Particulars</option>
                    <option value="B">Professional Achievements</option>
                    <option value="C">Research and Development</option>
                    <option value="D">Professional Consultations</option>
                    <option value="E">Research Outcomes</option>
                    <option value="F">Professional Recognition</option>
                    <option value="G">Services to Community</option>
                </select>
            </div> -->
            <button type="submit">Submit</button>
        </form>
    </div>
    <?php include_once("includes/footer.php") ?>
</body>
</html>






<?php
// require_once("includes/config.php");
// require_once("includes/redirect-login.php");
// ob_clean();

// $query = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
// $user = $query->fetch_object();
// $section = $_SESSION['newSubmission'];

?>
<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Submission</title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)"/>
</head>
<body>
    <?php //include_once("includes/header.php") ?>
        <div class="container">
            <?php
            //echo "<p style='color: black;'>$thing</p>";
            ?>
        </div>
    <?php //include_once("includes/footer.php") ?>
</body>
</html> -->
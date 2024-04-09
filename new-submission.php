<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$error = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $author = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $type = isset($_SESSION['newSubmission']) ? $_SESSION['newSubmission'] : null;
    $title = $_POST["title"];
    $comments = $_POST["comments"];
    $dateSubmitted = date("Y-m-d H:i:s");

    if (empty($title) || empty($comments) || empty($type)) {
        $error = "Title, comments, and file are required fields.";
    } else {
        $fileUploaded = false;
        $uploadDir = "submissionfiles/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Insert details for submission
        $stmt = $mysqli->prepare("INSERT INTO submission (title, author, dateSubmitted, type, comments, submitted, approved) VALUES (?, ?, ?, ?, ?, 0, 0)");
        $stmt->bind_param("sisss", $title, $author, $dateSubmitted, $type, $comments);
        $stmt->execute();
        $submissionID = $stmt->insert_id;

        $fileCount = count($_FILES['file']['name']);

        // Loop through each uploaded file
        for ($i = 0; $i < $fileCount; $i++) { 
            $fileType = pathinfo($_FILES["file"]["name"][$i], PATHINFO_EXTENSION);
            $customFileName = "upload" . uniqid() . "." . $fileType;
            $fileTmpName = $_FILES["file"]["tmp_name"][$i];
            $filePath = $uploadDir . $customFileName;

            if (move_uploaded_file($fileTmpName, $filePath)) {
                // Insert file details into the file table
                $stmt = $mysqli->prepare("INSERT INTO file (fileType, name, author) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $fileType, $customFileName, $author);
                $stmt->execute();
                $fileID = $stmt->insert_id;

                // Insert details into submissionfile table
                $stmt = $mysqli->prepare("INSERT INTO submissionfile (submissionID, fileID) VALUES (?, ?)");
                $stmt->bind_param("ii", $submissionID, $fileID);
                $stmt->execute();

                $fileUploaded = true;
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
<link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width : 790px)" />

</head>
<body>


<?php include_once("includes/header.php") ?>
    <div class="submission-form-container"> <!-- Updated class for submission form -->
        <h2>Submission Form</h2>
        <?php if (!empty($error)) : ?>
            <div class="error-message"><?php echo $error; ?></div> <!-- Class added for styling -->
        <?php endif; ?>
        <?php if (isset($successMessage)) : ?>
            <div class="success-message"><?php echo $successMessage; ?></div> <!-- Class added for styling -->
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group"> <!-- Added class for styling -->
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" placeholder="Title" required>
            </div>
            <div class="form-group"> <!-- Added class for styling -->
                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" placeholder="Comments" rows="3"></textarea>
            </div>
            <div class="form-group"> <!-- Added class for styling -->
                <label for="file">Upload File:</label>
                <input type="file" id="file" name="file[]" multiple required>
            </div>
            <button type="submit" class="submit-button">Submit</button> <!-- Class added for styling -->
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



<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$error = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $author = $_SESSION['user_id'];
    $sectionID = $_SESSION['newSubmission'];
    $title = $_POST["title"];
    $comments = $_POST["comments"];
    $dateSubmitted = date("Y-m-d H:i:s");

    // I removed a check to see if the email and passwords aren't empty as there is already a check with "required" in the html.

    $fileUploaded = false;

    // Insert details for submission
    $stmt = $mysqli->prepare("INSERT INTO submission (title, author, dateSubmitted, sectionID, comments, submitted, approved) VALUES (?, ?, ?, ?, ?, 0, 0)");
    $stmt->bind_param("sisis", $title, $author, $dateSubmitted, $sectionID, $comments);
    $stmt->execute();

    // Check for errors during query execution
    if ($stmt->error) {
        $error = "Database error: " . $stmt->error;
    } else {
        $submissionID = $stmt->insert_id;
        $fileCount = count($_FILES['file']['name']);

        // Loop through each uploaded file
        for ($i = 0; $i < $fileCount; $i++) {
            $target_dir = "submissionfiles/"; // Relative path for the upload directory
            $target_file = $target_dir . basename($_FILES["file"]["name"][$i]);
            $extension = pathinfo($_FILES["file"]["name"][$i], PATHINFO_EXTENSION);
            $fileName = $submissionID . "." . $extension; // Use submission ID as part of the file name

            if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $target_dir . $fileName)) {
                // Insert file details into the file table
                $stmt = $mysqli->prepare("INSERT INTO file (fileType, name, author) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $extension, $fileName, $author);
                $stmt->execute();

                // Check for errors during file insertion
                if ($stmt->error) {
                    $error = "Database error: " . $stmt->error;
                    break;
                } 
                else {
                    $fileID = $stmt->insert_id;

                    // Insert details into submissionfile table
                    $stmt = $mysqli->prepare("INSERT INTO submissionfile (submissionID, fileID) VALUES (?, ?)");
                    $stmt->bind_param("ii", $submissionID, $fileID);
                    $stmt->execute();

                    // Check for errors during submissionfile insertion
                    if ($stmt->error) {
                        $error = "Database error: " . $stmt->error;
                        break;
                    } else {
                        $fileUploaded = true;
                    }
                }
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
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width: 790px)" />
</head>
<body>

<?php include_once("includes/header.php") ?>

<div class="submission-form-container">
    <h2>Submission Form</h2>
    <?php if (!empty($error)) : ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($successMessage)) : ?>
        <div class="success-message"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" placeholder="Title" required>
        </div>
        <div class="form-group">
            <label for="comments">Comments:</label>
            <textarea id="comments" name="comments" placeholder="Comments" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="file">Upload File:</label>
            <input type="file" id="file" name="file[]" multiple required>
        </div>
        <button type="submit" class="submit-button">Submit</button>
    </form>
</div>

<?php include_once("includes/footer.php") ?>

</body>
</html>





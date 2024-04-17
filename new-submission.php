<?php
require_once("includes/config.php");
require_once("includes/redirect-login.php");
ob_clean();

$error = "";
$successMessage = "";
$userQuery = $mysqli->query("SELECT * FROM users WHERE userID = $userID");
$user = $userQuery->fetch_object();

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['lang'])) {
    $author = $_SESSION['user_id'];
    $sectionID = $_SESSION['newSubmission'];
    $title = $_POST["title"];
    $comments = $_POST["comments"];
    $dateSubmitted = date("Y-m-d H:i:s");
    ($user->jobRole == "Supervisor" ? $submitted = 1 : $submitted = 0);

    // I removed a check to see if the email and passwords aren't empty as there is already a check with "required" in the html.

    $fileUploaded = false;

    // Insert details for submission
    $stmt = $mysqli->prepare("INSERT INTO submission (title, author, dateSubmitted, sectionID, comments, submitted, approved) VALUES (?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("sisiss", $title, $author, $dateSubmitted, $sectionID, $comments, $submitted);
    $stmt->execute();
    $submissionID = $stmt->insert_id;
    $_SESSION['viewSubmission'] = $submissionID;

    if (isset($_POST['coauthors'])) {
        $i = 1;
        $queryText = "INSERT INTO submissioncoauthor VALUES ";
        foreach ($_POST['coauthors'] as $coauthor) {
            $queryText .= "($submissionID, $coauthor)";
            $queryText .= ($i == count($_POST['coauthors']) ? ";" : ",");
            $i++;
        }
        $coauthorInsert = $mysqli->query($queryText);
    }

    // Check for errors during query execution
    if ($stmt->error) {
        $error = translate("Database error: ") . $stmt->error;
    } else {
        $fileCount = count($_FILES['file']['name']);

        // Loop through each uploaded file
        for ($i = 0; $i < $fileCount; $i++) {
            $target_dir = "submissionfiles/"; // upload directory
            $extension = pathinfo($_FILES["file"]["name"][$i], PATHINFO_EXTENSION);
            $address = "upload-" . $submissionID . "-" . ($i + 1) . "." . $extension; 
            $fileName = $_FILES["file"]["name"][$i];

            if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $target_dir . $address)) {
                // Insert file details into the file table
                $stmt = $mysqli->prepare("INSERT INTO file (address, name, author) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $address, $fileName, $author);
                $stmt->execute();

                // Check for errors during file insertion
                if ($stmt->error) {
                    $error = translate("Database error: ") . $stmt->error;
                    break;
                } else {
                    $fileID = $stmt->insert_id;

                    // Insert details into submissionfile table
                    $stmt = $mysqli->prepare("INSERT INTO submissionfile (submissionID, fileID) VALUES (?, ?)");
                    $stmt->bind_param("ii", $submissionID, $fileID);
                    $stmt->execute();

                    // Check for errors during submissionfile insertion
                    if ($stmt->error) {
                        $error = translate("Database error: ") . $stmt->error;
                        break;
                    } else {
                        $fileUploaded = true;
                    }
                }
            } else {
                $error = translate("File upload failed.");
                break;
            }
        }

        if ($fileUploaded) {
            $successMessage = translate("Files uploaded successfully.");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo translate("Submission Form"); ?></title>
    <link rel="stylesheet" href="css/mobile.css" />
    <link rel="stylesheet" href="css/desktop.css" media="only screen and (min-width: 790px)" />
</head>
<body>

<?php include_once("includes/header.php") ?>

<div class="submission-form-container">
    <?php echo "<h2>" . translate("Submission Form") . "</h2>";?>
    <?php if (!empty($successMessage)) {
        header("Location: view-submission.php");
    } ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title"><?php echo translate("Title"); ?>:</label>
            <input type="text" id="title" name="title" placeholder="<?php echo translate("Title"); ?>" required>
        </div>
        <div class="form-group">
            <label for="comments"><?php echo translate("Comments"); ?>:</label>
            <textarea id="comments" name="comments" placeholder="<?php echo translate("Comments"); ?>" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="file"><?php echo translate("Upload File"); ?>:</label>
            <input type="file" id="file" name="file[]" multiple required>  <!-- NeedsTranslation for defaults like "Choose files", "No file chosen", and "4 files" -->
        </div>
        <button type="submit" class="submit-button"><?php echo translate("Submit"); ?></button>
        <table>
            <tr class="add-coauthors-table">
                <th>First name</th>
                <th>Last name</th>
                <th>Email</th>
                <th>Coauthor</th>
            </tr>
            <?php
            $possibleCoauthorsQuery = $mysqli->query("SELECT * FROM users WHERE userID != $userID AND jobRole != 'Admin' AND jobRole != 'Manager'");
            echo "<h1>Add Coauthors</h1>";
            while ($coauthor = $possibleCoauthorsQuery->fetch_object()) {
                echo "
                <tr>
                <td>$coauthor->fname</td>
                <td>$coauthor->lname</td>
                <td>$coauthor->email</td>
                <td><input type='checkbox' name='coauthors[]' value='" . $coauthor->userID . "'></td>
                </tr>
                ";
            }
            ?>
        </table>
    </form>
</div>

<?php include_once("includes/footer.php") ?>

</body>
</html>

<?php include("includes/lang-config.php");
function translate($key) {
    $translations = array(
        /*
        "en" => array(
            "Database error: " => "Database error: ",
            "File upload failed." => "File upload failed.",
            "Files uploaded successfully." => "Files uploaded successfully.",
            "Submission Form" => "Submission Form",
            "Title" => "Title",
            "Comments" => "Comments",
            "Upload File" => "Upload File",
            "Submit" => "Submit",
            "Logout" =>"Logout"
        ),
        */
        "bm" => array(
            "Database error: " => "Ralat pangkalan data: ",
            "File upload failed." => "Muat naik fail gagal.",
            "Files uploaded successfully." => "Fail-fail dimuat naik dengan berjaya.",
            "Submission Form" => "Borang Penyerahan",
            "Title" => "Tajuk",
            "Comments" => "Komen",
            "Upload File" => "Muat Naik Fail",
            "Submit" => "Hantar",
            "Logout" =>"Log Keluar"
        )
    );

    $language = $_SESSION['language'];
    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
} ?>

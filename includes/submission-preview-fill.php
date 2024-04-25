<?php
    echo "<div class='submission-preview'>";
    echo "<div class='segment-container submission-preview-box'>";
    echo "<div class='submission-preview-text'>";
    echo "<div>";
    echo "<p>$obj->title</p>";
    $query = $mysqli->query("SELECT * FROM users WHERE userID = $obj->author");
    $author = $query->fetch_object();
    echo "<p>By $author->fname $author->lname</p>";

    $rejectedQuery = $mysqli->query("SELECT * FROM submissionreturn WHERE submissionID = $obj->submissionID ORDER BY returnDate DESC");
    if ($obj->approved > 0) {
        $status = "Approved";
    } else if ($obj->submitted == 1 && $obj->approved == 0) {
        $status = "Waiting for manager approval";
    } else if ($obj->submitted == 0 && mysqli_num_rows($rejectedQuery) > 0) {
        $status = "Rejected";
        $recent = $mysqli->query("SELECT * FROM submission, submissionreturn WHERE submission.submissionID = submissionreturn.submissionID AND submissionreturn.returnDate > submission.dateSubmitted AND submission.submissionID = $obj->submissionID");
        if (mysqli_num_rows($recent) == 0) {
            $status = "Waiting for supervisor approval";
        }
    } else {
        $status = "Waiting for supervisor approval";
    }

    echo "<p class='$status'>Status: $status</p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "<div>";
    echo "<form method='post'>";
    echo "<button class='view-details' name='submission-id' value='$obj->submissionID'>View Details</button>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
?>
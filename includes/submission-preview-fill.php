<?php
    echo "<div class='submission-preview'>";
    echo "<div class='segment-container submission-preview-box'>";
    if ($isAuthor == false) { // $isAuthor is set before the include is called. Just adds a gray indicator to the left.
        echo "<div class='coauthor-indicator' style='background-color: gray;'>";
        echo "</div>";
    }
    echo "<div class='submission-preview-text'>";
    echo "<div>";
    echo "<p>$obj->title</p>";
    $query = $mysqli->query("SELECT * FROM users WHERE userID = $obj->author");
    $author = $query->fetch_object();
    echo "<p>By $author->fname $author->lname</p>";  // NeedsTranslation
    echo "</div>";
    echo "</div>";

    $rejectedQuery = $mysqli->query("SELECT * FROM submissionreturn WHERE submissionID = $obj->submissionID ORDER BY returnDate DESC");
    if ($obj->approved > 0) {
        $backgroundColour = "green";
    } else if ($obj->submitted == 1 && $obj->approved == 0) {
        $backgroundColour = "orange";
    } else if ($obj->submitted == 0 && mysqli_num_rows($rejectedQuery) > 0) {
        $backgroundColour = "red";
        $recent = $mysqli->query("SELECT * FROM submission, submissionreturn WHERE submission.submissionID = submissionreturn.submissionID AND submissionreturn.returnDate > submission.dateSubmitted AND submission.submissionID = $obj->submissionID");
        if (mysqli_num_rows($recent) == 0) {
            $backgroundColour = "yellow";
        }
    } else {
        $backgroundColour = "yellow";
    }
    
    echo "<div class='colour-bar' style='background-color: $backgroundColour';>"; 
    echo "</div>";
    echo "</div>";
    echo "<div>";
    echo "<form method='post'>";
    echo "<button class='view-details' name='submission-id' value='$obj->submissionID'>View Details</button>";  // NeedsTranslation
    echo "</form>";
    echo "</div>";
    echo "</div>";
?>
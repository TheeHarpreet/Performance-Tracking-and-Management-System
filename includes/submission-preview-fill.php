<?php
    echo "<div class='submission-preview'>";
    echo "<div class='submission-preview-box'>";
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

    $backgroundColour = "gray"; // gray = submitted to supervisor.
    $returns = $mysqli->query ("SELECT * FROM submissionreturn WHERE submissionID = $obj->submissionID ORDER BY returnDate ASC");
    if (mysqli_num_rows($returns) > 0 && $obj->submitted == 0) {
        $latestReturn = $returns->fetch_object();
        $backgroundColour = "red"; // red = rejected.
    }
    if ($obj->submitted == 1) {
        $backgroundColour = "orange"; // orange = submitted to manager.
    }
    if ($obj->approved > 0 ) {
        $backgroundColour = "green"; // green = accepted.
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
<?php
    echo "<div class='submission-preview'>";
    echo "<div class='submission-preview-box'>";
    if ($isAuthor == false) {
        echo "<div class='coauthor-indicator' style='background-color: gray;'>";
        echo "<p>‎ ‎ ‎ ‎ ‎ ‎</p>";
        echo "</div>";
    }
    echo "<div class='submission-preview-text'>";
    echo "<div>";
    echo "<p>$obj->title</p>";
    $query = $mysqli->query("SELECT * FROM users WHERE userID = $obj->author");
    $author = $query->fetch_object();
    echo "<p>By $author->fname $author->lname</p>";
    echo "</div>";
    echo "</div>";

    $backgroundColour = "gray";
    $returns = $mysqli->query ("SELECT * FROM submissionreturn WHERE submissionID = $obj->submissionID ORDER BY returnDate ASC");
    if (mysqli_num_rows($returns) > 0) {
        $latestReturn = $returns->fetch_object();
        $backgroundColour = "red";
    }
    if ($obj->submitted == 1) {
        $backgroundColour = "orange";
    }
    if ($obj->approved == 1) {
        $backgroundColour = "green";
    }
    
    echo "<div class='colour-bar' style='background-color: $backgroundColour';>";
    echo "<p>‎ ‎ ‎ ‎ ‎ ‎</p>";
    echo "</div>";
    echo "</div>";
    echo "<div>";
    echo "<form method='post'>";
    echo "<button class='view-details' name='submission-id' value='$obj->submissionID'>View Details</button>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
?>
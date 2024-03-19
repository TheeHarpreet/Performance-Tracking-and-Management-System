<?php
    if ($isAuthor == false) {
        echo "<div class='coauthor-indicator' style='background-color: gray;'>";
        echo "<p>‎ ‎ ‎ ‎ ‎ ‎</p>";
        echo "</div>";
    }
    echo "<div>";
    echo "<p>$obj->title</p>";
    $query = $mysqli->query("SELECT * FROM users WHERE userID = $obj->author");
    $author = $query->fetch_object();
    echo "<p>Author: $author->fname $author->lname</p>";
    echo "<p>$obj->dateSubmitted</p>";
    echo "<p>$obj->comments";
    echo "</div>";
    $coauthors = $mysqli->query("SELECT fname, lname FROM users, submissioncoauthor WHERE users.userID = submissioncoauthor.userID AND submissioncoauthor.submissionID = $obj->submissionID");
    if (mysqli_num_rows($coauthors) > 0) {
        echo "<div>";
        echo "<p>Co-authors:</p>";
        if (mysqli_num_rows($coauthors) < 4) {
            while ($obj2 = $coauthors->fetch_object()) {
                echo "<p>$obj2->fname $obj2->lname</p>";
            }
        } else {
            for ($x = 0; $x < 2; $x++) {
                $name = $coauthors->fetch_object();
                echo "<p>$name->fname $name->lname</p>";
            }
            $amount = mysqli_num_rows($coauthors) - 2;
            echo "<p>+ $amount more</p>";
        }
        echo "</div>";
    }
    $files = $mysqli->query("SELECT name FROM file, submissionfile WHERE file.fileID = submissionfile.fileID AND submissionfile.submissionID = $obj->submissionID");
    if (mysqli_num_rows($files) > 0) {
        echo "<div>";
        echo "<p>Files:</p>";
        if (mysqli_num_rows($files) < 4) {
            while ($obj2 = $files->fetch_object()) {
                echo "<p>$obj2->name</p>";
            }
        } else {
            for ($x = 0; $x < 2; $x++) {
                $filename = $file->fetch_object();
                echo "<p>$filename->name</p>";
            }
            $amount = mysqli_num_rows($files) - 2;
            echo "<p>+ $amount more</p>";
        }
        echo "</div>";
    }

    $backgroundColour = "orange";
    $textColour = "black";
    if ($obj->approved == 1) {
        $backgroundColour = "green";
        $textColour = "white";
    }

    $returns = $mysqli->query ("SELECT * FROM submissionreturn WHERE submissionID = $obj->submissionID ORDER BY returnDate ASC");
    if (mysqli_num_rows($returns) > 0) {
        $latestReturn = $returns->fetch_object();
        $backgroundColour = "red";
        $textColour = "white";
    }
    
    echo "<div class='colour-bar' style='background-color: $backgroundColour';>";
    echo "<p style='color: $textColour;'>‎ ‎ ‎ ‎ ‎ ‎</p>";
    echo "</div>";
    echo "</div>";
?>
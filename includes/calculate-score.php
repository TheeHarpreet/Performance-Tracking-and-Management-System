<?php

$query = $mysqli->query("SELECT SUM(`approved`) AS amount FROM `submission` WHERE `author` = $author AND type = '$section'");
$result = $query->fetch_object();
$currentAmount = $result->amount;

// getting the min points for a section
function getMinPoint($mysqli, $section){
    mysqli_query($mysqli, "SELECT MIN(`approved`) AS minPoint FROM `submission` WHERE type = '$section' AND approved > 0");
}

// getting the max points for a section
function getMaxPoint($mysqli, $section){
    mysqli_query($mysqli, "SELECT MAX(`approved`) AS maxPoint FROM `submission` WHERE type = '$section'");
}

if ($currentAmount == 0) {
    $points = 0;
}
else {
    $minRange = getMinPoint($mysqli, $section);
    $maxRange = getMaxPoint($mysqli, $section);
    if ($section == 'A') {
        $minPoints = 1;
        $maxPoints = 2;
    } else if ($section == 'B') {
        $minPoints = 6;
        $maxPoints = 8; 
    } else if ($section == 'C') {
        $minPoints = 14;
        $maxPoints = 16;
    } else if ($section == 'D' || $section == 'F') {
        $minPoints = 5;
        $maxPoints = 7;
    } else if ($section == 'E') {
        $minPoints = 8;
        $maxPoints = 10;
    } else if ($section == 'G') {
        $minPoints = 3;
        $maxPoints = 5;
    }
    if ($minRange == $maxRange) {
        $points = $maxPoints;
    }
    else {
        $points = $minPoints + (($maxPoints - $minPoints) * (($currentAmount - $minRange) / ($maxRange - $minRange)));
    }
    $points = round($points, 2);
}
?>
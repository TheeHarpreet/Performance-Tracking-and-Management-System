<?php
$query = $mysqli->query("SELECT SUM(approved) AS amount FROM submission WHERE author = $author AND type = '$section'");
$result = $query->fetch_object();
$currentAmount = $result->amount;
if ($currentAmount == 0) {
    $points = 1.5;
    
    $minPoints = 1;
    $maxPoints = 2;
}
else {
    $minRange = 1;
    $maxRange = 5;
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
    $points = $minPoints + (($maxPoints - $minPoints) * (($currentAmount - $minRange) / ($maxRange - $minRange)));
}
?>
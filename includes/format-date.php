<?php
    $datetime = strval($timeToTranslate);
    $date = explode(" ", $datetime);
    $dateValues = explode("-", $date[0]);
    $time = $date[1];
    $timeValues = explode(":", $time);
    $timeOfDay = " AM";
    $hour = intval($timeValues[0]);
    $hour = $hour % 12;
    if ($hour == 0) {
        $hour = 12;
    }
    if ($timeValues[0] >= 12) {
        $timeOfDay = " PM";
    }
    $dateTimeOutput = $dateValues[2] . "/" . $dateValues[1] . "/" . $dateValues[0] . " at " . $hour . ":" . $timeValues[1] . ":" . $timeValues[2] . $timeOfDay;  // NeedsTranslation
?>
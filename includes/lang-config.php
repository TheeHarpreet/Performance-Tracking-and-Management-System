<?php

if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}

if(isset($_POST['lang'])) {
    $_SESSION['language'] = $_POST['lang'];
}

?>
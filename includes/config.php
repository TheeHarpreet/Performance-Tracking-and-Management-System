<?php
define("DB_SERVER", "127.0.0.1");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "performance-tracker");
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Include this in any file that needs a connection to the database. Database connection called "$mysqli".
?>
<?php
include 'customFunctions.php';

// Database configuration
require_once "config.php";

// Create a connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>

<?php
// ============================================
// DATABASE CONNECTION FILE
// This file connects our website to the database.
// Every file that needs the database will include this file.
// ============================================

// --- Database settings (fill in your own when moving the app) ---
$serverName = "localhost";      // where the database lives (this PC)
$dbUser     = "root";           // username to log into the database
$dbPassword = "";               // password (empty = no password in XAMPP)
$dbName     = "cemetery_db";    // the name of our database

// --- Try to connect to the database ---
// mysqli is PHP's way of talking to a MySQL/MariaDB database.
$conn = new mysqli($serverName, $dbUser, $dbPassword, $dbName);

// If the connection failed, stop the page and show the error.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If we get here, the connection worked!
?>


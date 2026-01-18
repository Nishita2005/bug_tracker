<?php
$host = "localhost";
$user = "root";      // Default XAMPP username
$pass = "";          // Default XAMPP password (empty)
$dbname = "bug_tracker"; // Based on your phpMyAdmin screenshots

// Create connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
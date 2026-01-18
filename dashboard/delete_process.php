<?php
session_start();
include('../db/db.php');

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $type = $_GET['type'];

    if ($type == 'user') {
        // Prevent deleting the currently logged-in admin
        if ($id == $_SESSION['user_id']) {
            die("You cannot delete your own account.");
        }
        $sql = "DELETE FROM users WHERE id = '$id'";
    } elseif ($type == 'project') {
        $sql = "DELETE FROM projects WHERE id = '$id'";
    }

    if (mysqli_query($conn, $sql)) {
        // Redirect back to the previous page
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>
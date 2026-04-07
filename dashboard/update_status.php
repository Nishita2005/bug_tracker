<?php
session_start();
include('../db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['issue_id'])) {
    // Trim and lowercase the input to ensure it matches your CSS classes
    $issue_id = mysqli_real_escape_string($conn, $_POST['issue_id']);
    $new_status = strtolower(trim(mysqli_real_escape_string($conn, $_POST['new_status'])));
    $user_id = $_SESSION['user_id'];

    // Update query
    $sql = "UPDATE issues SET status = '$new_status' WHERE id = '$issue_id'";
    
    if (mysqli_query($conn, $sql)) {
        // Success: Redirect back
        header("Location: developer.php?success=1");
        exit();
    } else {
        // This will tell you EXACTLY why it failed (e.g., column name typo)
        die("Database Error: " . mysqli_error($conn));
    }
}
?>
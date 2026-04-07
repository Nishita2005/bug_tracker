<?php
session_start();
include('../db/db.php');

// Security: Only Admin should be able to permanently close a bug
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $issue_id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. Update the status to 'closed'
    $query = "UPDATE issues SET status = 'closed' WHERE id = '$issue_id'";
    
    if (mysqli_query($conn, $query)) {
        // 2. Log the action for the audit trail
        $admin_id = $_SESSION['user_id'];
        $log_action = "Admin closed the issue permanently.";
        mysqli_query($conn, "INSERT INTO bug_history (issue_id, action_taken, changed_by) 
                             VALUES ('$issue_id', '$log_action', '$admin_id')");

        // 3. Redirect back to admin dashboard so you don't see a white screen
        header("Location: admin.php?closed_success=1");
        exit();
    } else {
        // Display error if the database update fails
        die("Database Error: " . mysqli_error($conn));
    }
} else {
    // If no ID was provided, just go back
    header("Location: admin.php");
    exit();
}
?>
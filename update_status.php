<?php
session_start();
include('../db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_id = mysqli_real_escape_string($conn, $_POST['issue_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $user_id = $_SESSION['user_id'];

    // 1. Update the status in the issues table
    $update_sql = "UPDATE issues SET status = '$new_status' WHERE id = '$issue_id'";
    
    if (mysqli_query($conn, $update_sql)) {
        // 2. Record this action in the history table (Audit Log)
        $action = "Status changed to " . $new_status;
        $history_sql = "INSERT INTO bug_history (issue_id, action_taken, changed_by) 
                        VALUES ('$issue_id', '$action', '$user_id')";
        mysqli_query($conn, $history_sql);

        header("Location: developer.php?success=1");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
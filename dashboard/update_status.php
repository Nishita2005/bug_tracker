<?php
session_start();
include('../db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $issue_id = mysqli_real_escape_string($conn, $_POST['issue_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $user_id = $_SESSION['user_id'];

    // 1. Update the status in the issues table
    $sql = "UPDATE issues SET status = '$new_status' WHERE id = '$issue_id'";
    
    if (mysqli_query($conn, $sql)) {
        // 2. Record this in the Activity Log (Bug History)
        $action = "Status updated to: " . strtoupper($new_status);
        $history_sql = "INSERT INTO bug_history (issue_id, action_taken, changed_by) 
                        VALUES ('$issue_id', '$action', '$user_id')";
        mysqli_query($conn, $history_sql);

        // 3. Redirect back to the developer dashboard
        header("Location: developer.php?success=1");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    header("Location: developer.php");
    exit();
}
?>
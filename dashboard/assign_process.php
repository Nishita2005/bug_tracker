<?php
session_start();
include('../db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_id = mysqli_real_escape_string($conn, $_POST['issue_id']);
    $dev_id = mysqli_real_escape_string($conn, $_POST['dev_id']);
    $admin_id = $_SESSION['user_id'];

    // 1. Update the Issue
    $sql = "UPDATE issues SET assigned_to = '$dev_id' WHERE id = '$issue_id'";
    
    if (mysqli_query($conn, $sql)) {
        // 2. Audit Log entry
        $dev_query = mysqli_query($conn, "SELECT name FROM users WHERE id = '$dev_id'");
        $dev_name = mysqli_fetch_assoc($dev_query)['name'];
        $action = "Issue assigned to " . $dev_name;
        
        mysqli_query($conn, "INSERT INTO bug_history (issue_id, action_taken, changed_by) VALUES ('$issue_id', '$action', '$admin_id')");
        
        header("Location: admin.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
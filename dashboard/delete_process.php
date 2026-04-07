<?php
session_start();
include('../db/db.php');

if (isset($_GET['id'])) {
    $issue_id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Update status to 'closed'
    $sql = "UPDATE issues SET status = 'closed' WHERE id = '$issue_id'";
    
    if (mysqli_query($conn, $sql)) {
        // Log the final closure in history
        $action = "Bug officially CLOSED by Admin";
        $history_sql = "INSERT INTO bug_history (issue_id, action_taken, changed_by) 
                        VALUES ('$issue_id', '$action', '$user_id')";
        mysqli_query($conn, $history_sql);

        header("Location: admin.php?closed=1");
        exit();
    }
}
header("Location: admin.php");
?>
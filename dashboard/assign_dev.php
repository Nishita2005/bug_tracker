<?php
session_start();
include('../db/db.php');

// Security: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_id = mysqli_real_escape_string($conn, $_POST['issue_id']);
    $dev_id = mysqli_real_escape_string($conn, $_POST['dev_id']);
    $admin_id = $_SESSION['user_id'];

    // 1. Update the Issue and set status to 'inprogress'
    $sql = "UPDATE issues SET assigned_to = '$dev_id', status = 'inprogress' WHERE id = '$issue_id'";
    
    if (mysqli_query($conn, $sql)) {
        // 2. Audit Log entry - Using 'name' column as per your error
        $dev_query = mysqli_query($conn, "SELECT name FROM users WHERE id = '$dev_id'");
        $dev_data = mysqli_fetch_assoc($dev_query);
        $dev_name = $dev_data['name'] ?? 'Developer';
        
        $action = "Issue assigned to " . $dev_name;
        
        mysqli_query($conn, "INSERT INTO bug_history (issue_id, action_taken, changed_by) 
                             VALUES ('$issue_id', '$action', '$admin_id')");
        
        // 3. Redirect back with ID so the green tick shows up
        header("Location: admin.php?assigned=1&id=" . $issue_id);
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
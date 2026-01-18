<?php
session_start();
include('../db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from form
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $project_id = $_POST['project_id'];
    $priority = $_POST['priority'];
    
    // Get the ID of the logged-in Tester from the session
    $created_by = $_SESSION['user_id']; 
    
    // Set default status for new bugs
    $status = 'open';

    $sql = "INSERT INTO issues (title, description, status, priority, project_id, created_by) 
            VALUES ('$title', '$description', '$status', '$priority', '$project_id', '$created_by')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Bug reported successfully!'); window.location.href='tester.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<?php
session_start();
include('../db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_id = $_POST['issue_id'];
    
    // Update status to 'closed'
    $sql = "UPDATE issues SET status = 'closed' WHERE id = '$issue_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: tester.php?msg=Closed");
    }
}
?>
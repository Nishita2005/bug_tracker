<?php
session_start();
include('../db/db.php');

if (isset($_GET['id']) && $_SESSION['role'] === 'tester') {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Update status to closed
    $sql = "UPDATE issues SET status = 'closed' WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: tester.php?msg=closed");
    }
}
?>
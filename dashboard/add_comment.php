<?php
session_start();
include('../db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_id = $_POST['issue_id'];
    $user_id = $_SESSION['user_id'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    $sql = "INSERT INTO comments (issue_id, user_id, comment) VALUES ('$issue_id', '$user_id', '$comment')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: view_issue.php?id=" . $issue_id);
    }
}
?>
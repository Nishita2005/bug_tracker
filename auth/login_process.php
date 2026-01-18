<?php
session_start();
include('../db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to find the user
    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Set Session Variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: ../dashboard/admin.php");
        } elseif ($user['role'] == 'developer') {
            header("Location: ../dashboard/developer.php");
        } elseif ($user['role'] == 'tester') {
            header("Location: ../dashboard/tester.php");
        } else {
            echo "Role not recognized: " . $user['role'];
        }
        exit();
    } else {
        echo "<script>alert('Invalid Email or Password'); window.location.href='login.php';</script>";
    }
} else {
    // If someone tries to access this file directly without posting a form
    echo "Access Denied. Please use the login form.";
}
?>
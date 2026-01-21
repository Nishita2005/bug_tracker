<?php
session_start();
include('../db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if connection is still alive
    if (!$conn || mysqli_connect_errno()) {
        die("Database connection failed. Please restart MySQL in XAMPP.");
    }

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    
    // Using @ to suppress the system fatal error so we can handle it manually
    $result = @mysqli_query($conn, $query);

    if (!$result) {
        die("MySQL Error: " . mysqli_error($conn) . ". Try restarting the XAMPP MySQL module.");
    }

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if ($user['role'] == 'admin') {
            header("Location: ../dashboard/admin.php");
        } elseif ($user['role'] == 'developer') {
            header("Location: ../dashboard/developer.php");
        } elseif ($user['role'] == 'tester') {
            header("Location: ../dashboard/tester.php");
        }
        exit();
    } else {
        echo "<script>alert('Invalid Email or Password'); window.location.href='login.php';</script>";
    }
}
?>
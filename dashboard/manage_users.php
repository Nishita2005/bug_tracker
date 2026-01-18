<?php
session_start();
include('../db/db.php');

if ($_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit(); }

if (isset($_POST['add_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $role = $_POST['role'];
    mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$pass', '$role')");
    header("Location: manage_users.php");
}

$users = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .btn-back { display: inline-block; padding: 8px 15px; background: #95a5a6; color: white; text-decoration: none; border-radius: 5px; font-size: 0.9em; margin-bottom: 20px; transition: 0.3s; }
        .btn-back:hover { background: #7f8c8d; }
        .btn-delete { color: #e74c3c; text-decoration: none; font-weight: bold; }
        .btn-delete:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="main-content" style="margin: 0 auto; max-width: 900px;">
        <a href="admin.php" class="btn-back">&larr; Back to Dashboard</a>
        <h2>User Management</h2>
        
        <div class="card">
            <h3>Add New User</h3>
            <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="name" placeholder="Full Name" required style="padding: 8px;">
                <input type="email" name="email" placeholder="Email" required style="padding: 8px;">
                <input type="password" name="password" placeholder="Password" required style="padding: 8px;">
                <select name="role" style="padding: 8px;">
                    <option value="developer">Developer</option>
                    <option value="tester">Tester</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit" name="add_user" class="btn-small">Create User</button>
            </form>
        </div>

        <div class="card" style="margin-top: 20px;">
            <table>
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php while($u = mysqli_fetch_assoc($users)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo $u['role']; ?></td>
                        <td>
                            <?php if($u['role'] != 'admin') { ?>
                                <a href="delete_process.php?id=<?php echo $u['id']; ?>&type=user" class="btn-delete" onclick="return confirm('Delete this user?')">Delete</a>
                            <?php } else { echo "<small style='color:#ccc'>Locked</small>"; } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
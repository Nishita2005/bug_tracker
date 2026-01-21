<?php
session_start();
include('../db/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$issues = mysqli_query($conn, "SELECT i.*, p.project_name, u.name as dev_name 
                               FROM issues i 
                               LEFT JOIN projects p ON i.project_id = p.id 
                               LEFT JOIN users u ON i.assigned_to = u.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Force display for badges */
        .badge { padding: 5px 10px; border-radius: 4px; color: white; font-size: 10px; font-weight: bold; }
        .open { background: #f39c12 !important; }
        .fixed { background: #27ae60 !important; }
        .high { background: #e74c3c !important; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BugTracker</h2>
        <p>Welcome, Admin</p><hr>
        <a href="admin.php" class="active">Dashboard</a>
        <a href="manage_users.php">Users</a>
        <a href="../auth/logout.php" style="color: #ff4d4d;">Logout</a>
    </div>

    <div class="main-content">
        <h1>Administrator Dashboard</h1>
        <div class="card">
            <table>
                <thead>
                    <tr><th>ID</th><th>Project</th><th>Title</th><th>Priority</th><th>Status</th><th>Assigned To</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($issues)) { ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $row['project_name']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><span class="badge <?php echo $row['priority']; ?>"><?php echo strtoupper($row['priority']); ?></span></td>
                        <td><span class="badge <?php echo $row['status']; ?>"><?php echo strtoupper($row['status'] ?: 'OPEN'); ?></span></td>
                        <td><?php echo $row['dev_name'] ?: 'Unassigned'; ?></td>
                        <td>
                            <form action="assign_process.php" method="POST">
                                <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                                <select name="dev_id">
                                    <option value="2">Dev User</option>
                                </select>
                                <button type="submit" class="btn-small">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
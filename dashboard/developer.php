<?php
session_start();
include('../db/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'developer') {
    header("Location: ../auth/login.php");
    exit();
}

$developer_id = $_SESSION['user_id']; // This must match assigned_to in the DB

$query = "SELECT i.*, p.project_name 
          FROM issues i 
          LEFT JOIN projects p ON i.project_id = p.id 
          WHERE i.assigned_to = '$developer_id' 
          AND i.status != 'closed' 
          ORDER BY i.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Developer Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>BugTracker</h2>
        <p>Dev ID: <?php echo $developer_id; ?></p><hr>
        <a href="developer.php" class="active">My Tasks</a>
        <a href="../auth/logout.php" style="color: #ff4d4d;">Logout</a>
    </div>

    <div class="main-content">
        <h1>My Assigned Tasks</h1>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Project</th><th>Issue</th><th>Priority</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0) { 
                        while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><span class="badge <?php echo $row['priority']; ?>"><?php echo strtoupper($row['priority']); ?></span></td>
                            <td style="font-weight:bold; color:#2c3e50;"><?php echo strtoupper($row['status']); ?></td>
                            <td>
                                <form action="update_status.php" method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                                    <select name="new_status">
                                        <option value="open" <?php if($row['status'] == 'open') echo 'selected'; ?>>Open</option>
                                        <option value="in progress" <?php if($row['status'] == 'in progress') echo 'selected'; ?>>In Progress</option>
                                        <option value="fixed" <?php if($row['status'] == 'fixed') echo 'selected'; ?>>Fixed</option>
                                    </select>
                                    <button type="submit" class="btn-small">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php } 
                    } else { echo "<tr><td colspan='6'>No active tasks assigned to you.</td></tr>"; } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php
session_start();
include('../db/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'developer') {
    header("Location: ../auth/login.php");
    exit();
}

$developer_id = $_SESSION['user_id'];

// Fetch issues assigned to this dev that aren't closed
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
    <meta charset="UTF-8">
    <title>Developer Dashboard | BugTracker</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .status-badge { padding: 4px 10px; border-radius: 4px; color: white; font-size: 0.85em; font-weight: bold; display: inline-block; min-width: 80px; text-align: center; }
        .bg-open { background-color: #f39c12; }
        .bg-inprogress { background-color: #3498db; }
        .bg-fixed { background-color: #27ae60; }
        .success-msg { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #c3e6cb; }
    </style>
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
        
        <?php if(isset($_GET['success'])): ?>
            <div class="success-msg">✔ Status updated successfully!</div>
        <?php endif; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Project</th><th>Issue</th><th>Priority</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0) { 
                        while($row = mysqli_fetch_assoc($result)) { 
                            // Normalize status for logic and CSS
                            $db_status = !empty($row['status']) ? strtolower(trim($row['status'])) : 'open';
                            $status_class = "bg-" . str_replace(' ', '', $db_status);
                        ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['project_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><span class="badge <?php echo $row['priority']; ?>"><?php echo strtoupper($row['priority']); ?></span></td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo strtoupper($db_status); ?>
                                </span>
                            </td>
                            <td>
                                <form action="update_status.php" method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                                    <select name="new_status" style="padding: 4px;">
                                        <option value="open" <?php echo ($db_status == 'open') ? 'selected' : ''; ?>>Open</option>
                                        <option value="inprogress" <?php echo ($db_status == 'inprogress') ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="fixed" <?php echo ($db_status == 'fixed') ? 'selected' : ''; ?>>Fixed</option>
                                    </select>
                                    <button type="submit" class="btn-small" style="cursor:pointer;">Update</button>
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
<?php
session_start();
include('../db/db.php');

// Security Check: Only Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// 1. DYNAMIC COLUMN CHECK: Try to find the correct name column
$check_cols = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'username'");
$column_to_use = (mysqli_num_rows($check_cols) > 0) ? 'username' : 'name';

// Fetch Developers using the correct column
$dev_list_query = mysqli_query($conn, "SELECT id, $column_to_use AS display_name FROM users WHERE role = 'developer'");
$developers = [];
if ($dev_list_query) {
    while($dev = mysqli_fetch_assoc($dev_list_query)) {
        $developers[] = $dev;
    }
}

// 2. Fetch all issues with project names
$query = "SELECT i.*, p.project_name FROM issues i 
          LEFT JOIN projects p ON i.project_id = p.id 
          ORDER BY i.id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | BugTracker</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .admin-table th, .admin-table td { vertical-align: middle; padding: 12px 8px; border-bottom: 1px solid #eee; }
        .status-badge { padding: 5px 12px; border-radius: 4px; color: white; font-size: 0.8em; font-weight: bold; display: inline-block; min-width: 90px; text-align: center; }
        .bg-open { background-color: #f39c12; }
        .bg-inprogress { background-color: #3498db; }
        .bg-fixed { background-color: #27ae60; }
        .bg-closed { background-color: #2c3e50; }
        .action-flex { display: flex; align-items: center; gap: 8px; justify-content: flex-start; }
        .btn-set { background: #3498db; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85em; }
        .btn-close-action { background: #000; color: #fff; padding: 6px 10px; text-decoration: none; border-radius: 4px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BugTracker</h2>
        <p>Admin Panel</p><hr>
        <a href="admin.php" class="active">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="../auth/logout.php" style="color: #ff4d4d;">Logout</a>
    </div>

    <div class="main-content">
        <h1>System Issues Overview</h1>
        <div class="card">
            <table class="admin-table" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>ID</th><th>Project</th><th>Issue</th><th>Priority</th><th>Status</th><th>Assignment</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) { 
                        $status_clean = !empty($row['status']) ? strtolower(trim($row['status'])) : 'open';
                        $status_class = "bg-" . str_replace(' ', '', $status_clean);
                    ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['project_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><span class="badge <?php echo $row['priority']; ?>"><?php echo strtoupper($row['priority']); ?></span></td>
                        <td><span class="status-badge <?php echo $status_class; ?>"><?php echo strtoupper($status_clean); ?></span></td>
                        <td>
                            <form action="assign_dev.php" method="POST" class="action-flex">
                                <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                                <select name="dev_id" style="padding: 5px; border-radius: 4px; border: 1px solid #ddd;" required>
                                    <option value="">-- select --</option>
                                    <?php foreach($developers as $dev) { 
                                        $selected = ($row['assigned_to'] == $dev['id']) ? 'selected' : '';
                                        echo "<option value='".$dev['id']."' $selected>".htmlspecialchars($dev['display_name'])."</option>";
                                    } ?>
                                </select>
                                <button type="submit" class="btn-set">Set</button>
                                <?php if(isset($_GET['assigned']) && $_GET['id'] == $row['id']): ?>
                                    <span style="color: #27ae60; font-weight: bold; margin-left: 5px;">✔</span>
                                <?php endif; ?>
                            </form>
                        </td>
                        <td>
                            <div class="action-flex">
                                <?php if($status_clean == 'fixed'): ?>
                                    <a href="close_process.php?id=<?php echo $row['id']; ?>" class="btn-close-action" onclick="return confirm('Close bug?')">CLOSE</a>
                                <?php elseif($status_clean == 'closed'): ?>
                                    <span style="color: #95a5a6; font-size: 0.85em; font-weight: bold;">CLOSED</span>
                                <?php else: ?>
                                    <span style="color: #ccc; font-size: 0.85em;">--</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
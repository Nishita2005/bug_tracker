<?php
session_start();
include('../db/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$dev_result = mysqli_query($conn, "SELECT id, name FROM users WHERE role = 'developer'");
$developers = mysqli_fetch_all($dev_result, MYSQLI_ASSOC);

$issue_query = "SELECT i.*, p.project_name, u.name as dev_name 
                FROM issues i 
                LEFT JOIN projects p ON i.project_id = p.id 
                LEFT JOIN users u ON i.assigned_to = u.id 
                ORDER BY i.created_at DESC";
$issues = mysqli_query($conn, $issue_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
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
        <header><h1>System Issues Overview</h1></header>
        
        <div class="card">
            <table>
                <colgroup>
                    <col style="width: 50px;">
                    <col style="width: 150px;">
                    <col style="width: 250px;">
                    <col style="width: 100px;">
                    <col style="width: 120px;">
                    <col style="width: 200px;">
                </colgroup>
                <thead>
                    <tr>
                        <th>ID</th><th>Project</th><th>Issue Title</th><th>Priority</th><th>Status</th><th>Assignment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($issues)) { 
                        $status_class = "bg-" . (str_replace(' ', '', $row['status']) ?: 'open');
                    ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                        <td>
                            <a href="view_issue.php?id=<?php echo $row['id']; ?>" class="issue-link">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </a>
                        </td>
                        <td><span class="badge <?php echo $row['priority']; ?>"><?php echo strtoupper($row['priority']); ?></span></td>
                        <td><span class="status-badge <?php echo $status_class; ?>"><?php echo strtoupper($row['status']); ?></span></td>
                        <td>
                            <form action="assign_process.php" method="POST" class="inline-form">
                                <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                                <select name="dev_id" style="width: 100px; padding: 4px;">
                                    <option value="">Dev</option>
                                    <?php foreach($developers as $dev) { ?>
                                        <option value="<?php echo $dev['id']; ?>" <?php if($row['assigned_to'] == $dev['id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($dev['name']); ?>
                                        </option>
                                    <?php } ?>
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
<?php
session_start();
include('../db/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Stats Logic
$total_bugs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM issues"))['count'];
$open_bugs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM issues WHERE status = 'open'"))['count'];
$high_priority = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM issues WHERE priority = 'high'"))['count'];

// Fetch Developers for dropdown
$dev_result = mysqli_query($conn, "SELECT id, name FROM users WHERE role = 'developer'");
$developers = mysqli_fetch_all($dev_result, MYSQLI_ASSOC);

// Fetch Issues with Search Filter
$issue_query = "SELECT i.*, p.project_name, u.name as dev_name 
                FROM issues i 
                LEFT JOIN projects p ON i.project_id = p.id 
                LEFT JOIN users u ON i.assigned_to = u.id 
                WHERE i.title LIKE '%$search%' OR i.id LIKE '%$search%'
                ORDER BY i.created_at DESC";
$issues = mysqli_query($conn, $issue_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .btn-back { display: inline-block; padding: 8px 15px; background: #95a5a6; color: white; text-decoration: none; border-radius: 5px; font-size: 0.9em; margin-bottom: 20px; transition: 0.3s; }
        .btn-back:hover { background: #7f8c8d; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BugTracker</h2>
        <p>Welcome, Admin</p>
        <hr>
        <a href="admin.php">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_projects.php">Manage Projects</a>
        <a href="../auth/logout.php" style="color: #ff4d4d;">Logout</a>
    </div>

    <div class="main-content">
        <header><h1>Administrator Dashboard</h1></header>

        <div class="stats-container">
            <div class="stat-card"><h3><?php echo $total_bugs; ?></h3><p>Total Issues</p></div>
            <div class="stat-card" style="border-left: 5px solid #f39c12;"><h3><?php echo $open_bugs; ?></h3><p>Open Issues</p></div>
            <div class="stat-card" style="border-left: 5px solid #e74c3c;"><h3><?php echo $high_priority; ?></h3><p>High Priority</p></div>
        </div>

        <div class="card" style="margin-bottom: 20px;">
            <form action="admin.php" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Search issues..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                <button type="submit" class="btn-small">Search</button>
            </form>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Project</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($issues)) { ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                        <td><a href="view_issue.php?id=<?php echo $row['id']; ?>"><strong><?php echo htmlspecialchars($row['title']); ?></strong></a></td>
                        <td><span class="badge <?php echo $row['priority']; ?>"><?php echo $row['priority']; ?></span></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['dev_name'] ?? 'Unassigned'; ?></td>
                        <td>
                            <form action="assign_process.php" method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                                <select name="dev_id" required>
                                    <option value="">Assign</option>
                                    <?php foreach($developers as $dev) { ?>
                                        <option value="<?php echo $dev['id']; ?>" <?php if($row['assigned_to'] == $dev['id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($dev['name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <button type="submit" class="btn-small" style="background:#3498db; color:white;">Update</button>
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
<?php
session_start();
include('../db/db.php');

// Security Check: Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Logic to add a new project
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_project'])) {
    $project_name = mysqli_real_escape_string($conn, $_POST['project_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $sql = "INSERT INTO projects (project_name, description) VALUES ('$project_name', '$description')";
    if (mysqli_query($conn, $sql)) {
        $success_msg = "Project created successfully!";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
}

// Fetch existing projects
$projects_query = "SELECT * FROM projects ORDER BY created_at DESC";
$projects_res = mysqli_query($conn, $projects_query);
?>
<td>
    <a href="delete_process.php?id=<?php echo $row['id']; ?>&type=project" 
       onclick="return confirm('Are you sure?')" 
       style="color:red; text-decoration:none;">Delete</a>
</td>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Projects | BugTracker</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>BugTracker</h2>
        <p>Admin Panel</p>
        <hr>
        <a href="admin.php">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_projects.php" class="active">Manage Projects</a>
        <a href="../auth/logout.php" style="color: #ff4d4d;">Logout</a>
    </div>

    <div class="main-content">
        <header>
            <h1>Project Management</h1>
            <?php if(isset($success_msg)) echo "<p style='color:green;'>$success_msg</p>"; ?>
        </header>

        <div class="card">
            <h3>Create New Project</h3>
            <form method="POST">
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <input type="text" name="project_name" placeholder="Project Name (e.g., E-commerce Site)" required style="padding: 10px;">
                    <textarea name="description" placeholder="Project Description..." style="padding: 10px; height: 60px;"></textarea>
                    <button type="submit" name="add_project" class="btn-small" style="width: 150px; background: #27ae60;">Add Project</button>
                </div>
            </form>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>Active Projects</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Project Name</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($p = mysqli_fetch_assoc($projects_res)) { ?>
                    <tr>
                        <td>#<?php echo $p['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($p['project_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($p['description']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($p['created_at'])); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
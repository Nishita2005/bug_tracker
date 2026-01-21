<?php
session_start();
include('../db/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tester') {
    header("Location: ../auth/login.php");
    exit();
}

$tester_id = $_SESSION['user_id'];

// Handle Bug Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_bug'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $proj_id = $_POST['project_id'];
    $priority = $_POST['priority'];

    // Now including reported_by
    $sql = "INSERT INTO issues (project_id, title, description, priority, status, reported_by) 
            VALUES ('$proj_id', '$title', '$desc', '$priority', 'open', '$tester_id')";
    mysqli_query($conn, $sql);
}

// Fetch only this tester's bugs
$my_bugs = mysqli_query($conn, "SELECT i.*, p.project_name FROM issues i 
                                LEFT JOIN projects p ON i.project_id = p.id 
                                WHERE i.reported_by = '$tester_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tester Panel</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>BugTracker</h2>
        <p>Tester: <?php echo $tester_id; ?></p><hr>
        <a href="tester.php" class="active">My Reports</a>
        <a href="../auth/logout.php" style="color: #ff4d4d;">Logout</a>
    </div>

    <div class="main-content">
        <h1>Tester Panel</h1>
        <div class="card">
            <form method="POST">
                <input type="text" name="title" placeholder="Short Title" required style="width:100%; margin-bottom:10px;">
                <textarea name="description" placeholder="Steps to reproduce..." required style="width:100%; height:80px; margin-bottom:10px;"></textarea>
                <select name="project_id" required>
                    <option value="1">Bug Tracker System</option>
                    <option value="2">Student Portal</option>
                </select>
                <select name="priority">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
                <button type="submit" name="report_bug" class="btn-small" style="background:#27ae60; color:white;">Submit Bug</button>
            </form>
        </div>

        <div class="card" style="margin-top:20px;">
            <h3>My Reported Bugs</h3>
            <table>
                <thead><tr><th>Project</th><th>Issue</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($my_bugs)) { ?>
                    <tr>
                        <td><?php echo $row['project_name']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><span class="badge <?php echo $row['status']; ?>"><?php echo strtoupper($row['status']); ?></span></td>
                        <td>
                            <?php if($row['status'] == 'fixed') { ?>
                                <a href="close_process.php?id=<?php echo $row['id']; ?>" class="btn-small" style="background:#2c3e50; color:white;">Close Bug</a>
                            <?php } else { echo "Pending"; } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
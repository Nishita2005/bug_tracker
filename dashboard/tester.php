<?php
session_start();
include('../db/db.php');

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tester') {
    header("Location: ../auth/login.php");
    exit();
}

$tester_id = $_SESSION['user_id'];

// 1. DYNAMIC FETCH: Projects for dropdown
$all_projects = mysqli_query($conn, "SELECT id, project_name FROM projects");

// Handle Bug Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_bug'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $proj_id = $_POST['project_id'];
    $priority = $_POST['priority'];

    // Ensure status is inserted as 'open'
    $sql = "INSERT INTO issues (project_id, title, description, priority, status, reported_by) 
            VALUES ('$proj_id', '$title', '$desc', '$priority', 'open', '$tester_id')";
    mysqli_query($conn, $sql);
    
    header("Location: tester.php?submitted=1");
    exit();
}

// 2. FETCH: Get this tester's bugs
$my_bugs = mysqli_query($conn, "SELECT i.*, p.project_name FROM issues i 
                                LEFT JOIN projects p ON i.project_id = p.id 
                                WHERE i.reported_by = '$tester_id'
                                ORDER BY i.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tester Panel | BugTracker</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Professional Badges for Presentation */
        .status-badge { padding: 4px 10px; border-radius: 4px; color: white; font-size: 0.85em; font-weight: bold; display: inline-block; min-width: 80px; text-align: center; }
        .bg-open { background-color: #f39c12; }
        .bg-inprogress { background-color: #3498db; }
        .bg-fixed { background-color: #27ae60; }
        .bg-closed { background-color: #2c3e50; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>BugTracker</h2>
        <p>Tester ID: <?php echo htmlspecialchars($tester_id); ?></p><hr>
        <a href="tester.php" class="active">My Reports</a>
        <a href="../auth/logout.php" style="color: #ff4d4d;">Logout</a>
    </div>

    <div class="main-content">
        <h1>Report New Issue</h1>
        
        <div class="card">
            <form method="POST">
                <input type="text" name="title" placeholder="Short Title (e.g., Login Crash)" required style="width:100%; margin-bottom:10px; padding:8px;">
                <textarea name="description" placeholder="Steps to reproduce..." required style="width:100%; height:80px; margin-bottom:10px; padding:8px;"></textarea>
                
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <select name="project_id" required style="flex: 1; padding: 8px;">
                        <option value="">-- Select Project --</option>
                        <?php while($proj = mysqli_fetch_assoc($all_projects)) { ?>
                            <option value="<?php echo $proj['id']; ?>"><?php echo htmlspecialchars($proj['project_name']); ?></option>
                        <?php } ?>
                    </select>

                    <select name="priority" style="flex: 1; padding: 8px;">
                        <option value="low">Low Priority</option>
                        <option value="medium">Medium Priority</option>
                        <option value="high">High Priority</option>
                    </select>
                </div>

                <button type="submit" name="report_bug" class="btn-small" style="background:#27ae60; color:white; width:100%; padding:10px; cursor:pointer;">Submit Bug Report</button>
            </form>
        </div>

        <div class="card" style="margin-top:20px;">
            <h3>My Reported Bugs</h3>
            <table>
                <thead>
                    <tr>
                        <th>Project</th><th>Issue</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($my_bugs) > 0) { 
                        while($row = mysqli_fetch_assoc($my_bugs)) { 
                            // FIX: Handle blank statuses
                            $db_status = !empty($row['status']) ? strtolower(trim($row['status'])) : 'open';
                            $status_class = "bg-" . str_replace(' ', '', $db_status);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo strtoupper($db_status); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($db_status == 'fixed') { ?>
                                    <a href="close_process.php?id=<?php echo $row['id']; ?>" class="btn-close" style="background:#2c3e50; color:white; text-decoration:none; padding:5px 10px; border-radius:3px; font-size: 0.8em;">Close Bug</a>
                                <?php } else { ?>
                                    <span style="color: #7f8c8d; font-size: 0.9em;">Pending Fix</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } 
                    } else { ?>
                        <tr><td colspan="4" style="text-align:center;">No bugs reported yet.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
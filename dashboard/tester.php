<?php
session_start();
include('../db/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tester') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch projects for the report form
$projects = mysqli_query($conn, "SELECT id, project_name FROM projects");

// Fetch bugs reported by THIS tester
$my_bugs = mysqli_query($conn, "SELECT i.*, p.project_name 
                                FROM issues i 
                                JOIN projects p ON i.project_id = p.id 
                                WHERE i.created_by = '$user_id' 
                                ORDER BY i.created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tester Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>BugTracker</h2>
        <p>Tester: <?php echo $_SESSION['user_id']; ?></p>
        <hr>
        <a href="tester.php">My Reports</a>
        <a href="../auth/logout.php" style="color: #ff4d4d;">Logout</a>
    </div>

    <div class="main-content">
        <header><h1>Tester Panel</h1></header>

        <div class="card">
            <h3>Report New Bug</h3>
            <form action="submit_issue.php" method="POST">
                <input type="text" name="title" placeholder="Short Title" required style="width: 100%; margin-bottom: 10px;">
                <textarea name="description" placeholder="Steps to reproduce the bug..." required style="width: 100%; height: 60px;"></textarea>
                
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <select name="project_id" required>
                        <option value="">Select Project</option>
                        <?php while($p = mysqli_fetch_assoc($projects)) { ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo $p['project_name']; ?></option>
                        <?php } ?>
                    </select>
                    
                    <select name="priority">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <button type="submit" class="btn-small" style="background: #27ae60;">Submit Bug</button>
                </div>
            </form>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>My Reported Bugs</h3>
            <table>
                <tr>
                    <th>Project</th>
                    <th>Issue</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while($row = mysqli_fetch_assoc($my_bugs)) { ?>
                <tr>
                    <td><?php echo $row['project_name']; ?></td>
                    <td><a href="view_issue.php?id=<?php echo $row['id']; ?>"><strong><?php echo $row['title']; ?></strong></a></td>
                    <td><span class="badge <?php echo $row['priority']; ?>"><?php echo $row['status']; ?></span></td>
                    <td>
                        <?php if($row['status'] == 'fixed') { ?>
                            <form action="close_issue.php" method="POST">
                                <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-small" style="background: #8e44ad;">Close & Verify</button>
                            </form>
                        <?php } else { echo "Waiting for Fix"; } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
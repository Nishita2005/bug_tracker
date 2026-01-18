<?php
session_start();
include('../db/db.php');

// Security: Only allow Developers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'developer') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch bugs assigned specifically to this developer
$query = "SELECT i.*, p.project_name 
          FROM issues i 
          JOIN projects p ON i.project_id = p.id 
          WHERE i.assigned_to = '$user_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Developer Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>BugTracker</h2>
        <p>Dev: <?php echo $_SESSION['user_id']; ?></p>
        <hr>
        <a href="../auth/logout.php">Logout</a>
    </div>

    <div class="main-content">
        <header><h1>My Assigned Tasks</h1></header>
        
        <div class="card">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Project</th>
                    <th>Issue</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo $row['project_name']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><span class="badge <?php echo $row['priority']; ?>"><?php echo $row['priority']; ?></span></td>
                    <td><strong><?php echo $row['status']; ?></strong></td>
                    <td>
                        <form action="update_status.php" method="POST">
                            <input type="hidden" name="issue_id" value="<?php echo $row['id']; ?>">
                            <select name="new_status">
                                <option value="in-progress">In Progress</option>
                                <option value="fixed">Fixed</option>
                            </select>
                            <button type="submit" class="btn-small">Update</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
<?php
session_start();
include('../db/db.php');

// Security Check: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get the Issue ID from the URL
if (!isset($_GET['id'])) {
    echo "Issue ID missing.";
    exit();
}

$issue_id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. FETCH ISSUE DETAILS
$issue_query = "SELECT i.*, p.project_name, u.name as creator 
                FROM issues i 
                LEFT JOIN projects p ON i.project_id = p.id 
                LEFT JOIN users u ON i.created_by = u.id 
                WHERE i.id = '$issue_id'";
$issue_res = mysqli_query($conn, $issue_query);
$issue_data = mysqli_fetch_assoc($issue_res);

if (!$issue_data) {
    echo "Issue not found.";
    exit();
}

// 2. FETCH COMMENTS
$comment_query = "SELECT c.*, u.name 
                  FROM comments c 
                  JOIN users u ON c.user_id = u.id 
                  WHERE c.issue_id = '$issue_id' 
                  ORDER BY c.created_at DESC";
$comments = mysqli_query($conn, $comment_query);

// 3. FETCH ACTIVITY LOG (Audit Trail)
$history_query = "SELECT bh.*, u.name as user_name 
                  FROM bug_history bh 
                  JOIN users u ON bh.changed_by = u.id 
                  WHERE bh.issue_id = '$issue_id' 
                  ORDER BY bh.created_at DESC";
$history_res = mysqli_query($conn, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue #<?php echo $issue_id; ?> | Details</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .log-item { padding: 8px; border-bottom: 1px solid #eee; font-size: 0.9em; }
        .comment-box { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="main-content" style="margin: 0 auto; max-width: 900px; width: 95%;">
        <div style="margin-bottom: 20px;">
            <a href="javascript:history.back()" style="text-decoration: none; color: #3498db;">&larr; Back to Dashboard</a>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span class="badge <?php echo $issue_data['priority']; ?>"><?php echo $issue_data['priority']; ?></span>
                <span style="font-weight: bold; color: #7f8c8d;">ID: #<?php echo $issue_id; ?></span>
            </div>
            <h1 style="margin: 10px 0;"><?php echo htmlspecialchars($issue_data['title']); ?></h1>
            
            <div class="detail-grid">
                <div>
                    <p><strong>Project:</strong> <?php echo htmlspecialchars($issue_data['project_name']); ?></p>
                    <p><strong>Status:</strong> <span style="text-transform: uppercase; font-weight:bold;"><?php echo $issue_data['status']; ?></span></p>
                </div>
                <div>
                    <p><strong>Reported By:</strong> <?php echo htmlspecialchars($issue_data['creator']); ?></p>
                    <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($issue_data['created_at'])); ?></p>
                </div>
            </div>
            <hr>
            <h3>Description</h3>
            <p style="line-height: 1.6; color: #2c3e50;"><?php echo nl2br(htmlspecialchars($issue_data['description'])); ?></p>
        </div>

        <div class="card" style="margin-top: 20px; border-left: 5px solid #3498db;">
            <h3>Activity Log</h3>
            <div style="max-height: 200px; overflow-y: auto;">
                <?php if(mysqli_num_rows($history_res) > 0) { 
                    while($h = mysqli_fetch_assoc($history_res)) { ?>
                    <div class="log-item">
                        <strong><?php echo htmlspecialchars($h['user_name']); ?>:</strong> 
                        <?php echo htmlspecialchars($h['action_taken']); ?> 
                        <span style="color: #95a5a6; float: right;"><?php echo $h['created_at']; ?></span>
                    </div>
                <?php } } else { echo "<p>No history recorded yet.</p>"; } ?>
            </div>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3>Discussion</h3>
            
            <form action="add_comment.php" method="POST" style="margin-bottom: 30px;">
                <input type="hidden" name="issue_id" value="<?php echo $issue_id; ?>">
                <textarea name="comment" placeholder="Add a comment or update..." required 
                          style="width: 100%; height: 80px; padding: 10px; border-radius: 5px; border: 1px solid #ddd;"></textarea>
                <button type="submit" class="btn-small" style="margin-top: 10px; padding: 10px 20px;">Post Comment</button>
            </form>

            <?php while($c = mysqli_fetch_assoc($comments)) { ?>
                <div class="comment-box">
                    <strong><?php echo htmlspecialchars($c['name']); ?></strong>
                    <span style="color: #95a5a6; font-size: 0.8em; margin-left: 10px;"><?php echo $c['created_at']; ?></span>
                    <p style="margin-top: 8px;"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
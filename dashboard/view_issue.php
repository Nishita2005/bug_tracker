<?php
session_start();
include('../db/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$issue_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// 1. Handle New Comment Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_comment'])) {
    $comment = mysqli_real_escape_string($conn, $_POST['comment_text']);
    $sql = "INSERT INTO comments (issue_id, user_id, comment) VALUES ('$issue_id', '$user_id', '$comment')";
    mysqli_query($conn, $sql);
}

// 2. Fetch Issue Details
$issue_query = mysqli_query($conn, "SELECT i.*, p.project_name FROM issues i JOIN projects p ON i.project_id = p.id WHERE i.id = '$issue_id'");
$issue = mysqli_fetch_assoc($issue_query);

// 3. Fetch Comments
$comments_query = mysqli_query($conn, "SELECT c.*, u.name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.issue_id = '$issue_id' ORDER BY c.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Issue Details #<?php echo $issue_id; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>BugTracker</h2>
        <p>User ID: <?php echo $_SESSION['user_id']; ?></p>
        <hr>
        <a href="javascript:history.back()">← Back</a>
        <a href="../auth/logout.php" style="color: #ff4d4d; margin-top: 20px; display: block;">Logout</a>
    </div>

    <div class="main-content">
        <header>
            <h1>Issue #<?php echo $issue_id; ?>: <?php echo htmlspecialchars($issue['title']); ?></h1>
            <p>Project: <strong><?php echo htmlspecialchars($issue['project_name']); ?></strong> | Status: <span class="badge <?php echo $issue['status']; ?>"><?php echo strtoupper($issue['status']); ?></span></p>
        </header>

        <div class="card" style="margin-bottom: 30px;">
            <h3>Description</h3>
            <p style="line-height: 1.6; color: #444;"><?php echo nl2br(htmlspecialchars($issue['description'])); ?></p>
        </div>

        <div class="card">
            <h3>Discussion / Comments</h3>
            
            <form method="POST" style="margin-bottom: 30px;">
                <textarea name="comment_text" placeholder="Add an update or comment..." required style="width: 100%; height: 80px; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;"></textarea>
                <button type="submit" name="add_comment" class="btn-small" style="margin-top: 10px; background: #3498db; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px;">Post Update</button>
            </form>

            <div id="comment-list">
                <?php if(mysqli_num_rows($comments_query) > 0): ?>
                    <?php while($c = mysqli_fetch_assoc($comments_query)) { ?>
                        <div style="background: #fdfdfd; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #eee; border-left: 5px solid #3498db;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <strong><?php echo htmlspecialchars($c['name']); ?></strong>
                                <small style="color: #888;"><?php echo date('M d, Y H:i', strtotime($c['created_at'])); ?></small>
                            </div>
                            <p style="margin: 0; color: #555;"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
                        </div>
                    <?php } ?>
                <?php else: ?>
                    <p style="color: #999; text-align: center;">No comments yet. Start the conversation!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
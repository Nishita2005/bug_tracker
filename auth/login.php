<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | BugTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-card h2 { color: #333; margin-bottom: 10px; font-weight: 700; }
        .login-card p { color: #777; margin-bottom: 30px; font-size: 0.9em; }
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; margin-bottom: 5px; color: #555; font-weight: 500; }
        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: border 0.3s;
        }
        .input-group input:focus { border-color: #764ba2; }
        button {
            width: 100%;
            padding: 12px;
            background: #764ba2;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover { background: #5a3782; }
        .footer-text { margin-top: 20px; font-size: 0.8em; color: #999; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>BugTracker</h2>
    <p>Sign in to manage your workflow</p>
    
    <form action="login_process.php" method="POST">
        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="admin@bug.com" required>
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit">Login to Dashboard</button>
    </form>

    <div class="footer-text">
        &copy; 2026 BugTracker Systems v1.0
    </div>
</div>

</body>
</html>
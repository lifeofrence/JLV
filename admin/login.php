<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - JenniferLamiVisuals</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="style.css" rel="stylesheet">
</head>
<body class="admin-login-body">
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="text-center mb-4">
                <img src="../images/logo.png" alt="JLV Logo" style="width: 80px;">
                <h3 class="mt-3">Admin Login</h3>
            </div>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <?php
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
                    $_SESSION['admin_logged_in'] = true;
                    header('Location: index.php');
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Invalid username or password.</div>';
                }
                ?>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-admin w-100">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
require_once 'php/config.php';
require_once 'php/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new Auth($mysqli);
    $result = $auth->login($_POST['username'] ?? '', $_POST['password'] ?? '');
    
    if ($result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = $result['message'];
    }
}

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="margin-top: 60px;">
        <div class="card" style="max-width: 400px; margin: 0 auto;">
            <h2 style="text-align: center;">Login</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
            
            <div style="margin-top: 30px; padding: 15px; background: #f0f0f0; border-radius: 5px;">
                <p><strong>Demo Credentials:</strong></p>
                <p>Username: <code>admin</code><br>Password: <code>admin123</code></p>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>

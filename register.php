<?php
require_once 'php/config.php';
require_once 'php/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new Auth($mysqli);
    $result = $auth->register(
        $_POST['username'] ?? '',
        $_POST['email'] ?? '',
        $_POST['password'] ?? '',
        $_POST['first_name'] ?? '',
        $_POST['last_name'] ?? ''
    );
    
    if ($result['success']) {
        $success = $result['message'];
        $_POST = array();
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
    <title>Register - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="margin-top: 40px;">
        <div class="card" style="max-width: 500px; margin: 0 auto;">
            <h2 style="text-align: center;">Create Account</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <p style="margin-top: 10px;"><a href="login.php">Login Now</a></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" onsubmit="return validateForm('registerForm')">
                <div id="registerForm">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        <small>3-100 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <small>Minimum 6 characters</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
                </div>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>

<?php
require_once 'php/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task 3 - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <div class="container">
            <h1>🔐 User Management System</h1>
            <ul>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <li>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></li>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                    <?php if ($_SESSION['role_id'] == 2): ?>
                        <li><a href="admin_panel.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="php/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2>Welcome to Task 3</h2>
            <p>Backend Development & Database Integration - Full Stack Project</p>
            
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <p>This system demonstrates:</p>
                <ul style="margin-left: 20px; margin-top: 15px;">
                    <li>✅ Database Design with ER Diagram</li>
                    <li>✅ CRUD Operations (Create, Read, Update, Delete)</li>
                    <li>✅ Authentication System with Sessions</li>
                    <li>✅ Role-Based Login (User/Admin)</li>
                    <li>✅ Security with Prepared Statements</li>
                    <li>✅ Password Encryption (Bcrypt)</li>
                    <li>✅ Profile Picture Upload</li>
                    <li>✅ Activity Logging</li>
                </ul>
                <p style="margin-top: 20px;">
                    <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                </p>
            <?php else: ?>
                <p>Please login or register to access the system.</p>
                <p>
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                </p>
                
                <h3 style="margin-top: 30px;">Demo Credentials</h3>
                <p>
                    <strong>Username:</strong> admin<br>
                    <strong>Password:</strong> admin123
                </p>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>

<?php
require_once 'php/config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

$demo_mode = isset($_SESSION['demo_mode']) && $_SESSION['demo_mode'];
$success = '';
$error = '';

if ($demo_mode) {
    // Demo user profiles
    $demo_users = [
        1 => ['user_id' => 1, 'username' => 'admin', 'email' => 'admin@example.com', 'first_name' => 'Admin', 'last_name' => 'User', 'phone' => '+1-555-0100', 'role_name' => 'Administrator', 'is_active' => 1, 'created_at' => '2025-01-01 10:00:00', 'profile_picture' => null],
        2 => ['user_id' => 2, 'username' => 'user', 'email' => 'user@example.com', 'first_name' => 'Regular', 'last_name' => 'User', 'phone' => '+1-555-0101', 'role_name' => 'User', 'is_active' => 1, 'created_at' => '2025-01-05 10:00:00', 'profile_picture' => null],
        3 => ['user_id' => 3, 'username' => 'john_doe', 'email' => 'john@example.com', 'first_name' => 'John', 'last_name' => 'Doe', 'phone' => '+1-555-0102', 'role_name' => 'User', 'is_active' => 1, 'created_at' => '2025-01-10 10:00:00', 'profile_picture' => null],
    ];
    
    $user_id = $_GET['id'] ?? $_SESSION['user_id'];
    $user = $demo_users[$user_id] ?? $demo_users[1];
    
    // Handle profile updates in demo mode
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
        $user['first_name'] = $_POST['first_name'];
        $user['last_name'] = $_POST['last_name'];
        $user['phone'] = $_POST['phone'];
        $success = 'Profile updated successfully (Demo Mode)';
    }
} else {
    require_once 'php/users.php';

    $userManager = new UserManager($mysqli);
    $user_id = $_GET['id'] ?? $_SESSION['user_id'];

    // Check permissions - users can only view their own profile unless admin
    if ($user_id != $_SESSION['user_id'] && $_SESSION['role_id'] != 2) {
        header('Location: dashboard.php');
        exit;
    }

    $user = $userManager->getUserById($user_id);

    if (!$user) {
        header('Location: dashboard.php');
        exit;
    }

    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
        $result = $userManager->updateProfile($user_id, $_POST['first_name'], $_POST['last_name'], $_POST['phone']);
        if ($result['success']) {
            $success = $result['message'];
            $user = $userManager->getUserById($user_id);
        } else {
            $error = $result['message'];
        }
    }

    // Handle profile picture upload
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
        $result = $userManager->uploadProfilePicture($user_id, $_FILES['profile_picture']);
        if ($result['success']) {
            $success = $result['message'];
            $user = $userManager->getUserById($user_id);
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <div class="container">
            <h1>🔐 User Management System</h1>
            <ul>
                <li>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <?php if ($_SESSION['role_id'] == 2): ?>
                    <li><a href="admin_panel.php">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>'s Profile</h2>
            
            <?php if ($demo_mode): ?>
                <div style="padding: 15px; background: #e8f4f8; border-left: 4px solid #0066cc; margin-bottom: 20px; border-radius: 4px;">
                    <strong>📌 Demo Mode:</strong> You are viewing and editing sample user data.
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="profile-header">
                <?php if ($user['profile_picture']): ?>
                    <img src="uploads/profiles/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile" class="profile-picture">
                <?php else: ?>
                    <div class="profile-picture" style="background: #e0e0e0; display: flex; align-items: center; justify-content: center; color: #999;">
                        No Photo
                    </div>
                <?php endif; ?>
                
                <div class="profile-info">
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role_name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></p>
                    <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                    <p><strong>Status:</strong> <span style="color: <?php echo $user['is_active'] ? 'green' : 'red'; ?>;">
                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span></p>
                </div>
            </div>
            
            <!-- Edit Profile Form (Only for own profile or admin) -->
            <?php if ($user_id == $_SESSION['user_id'] || $_SESSION['role_id'] == 2): ?>
                <form method="POST" style="margin-top: 30px;">
                    <h3>Edit Profile</h3>
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
                
                <!-- Profile Picture Upload -->
                <form method="POST" enctype="multipart/form-data" style="margin-top: 30px;">
                    <h3>Profile Picture</h3>
                    
                    <div class="form-group">
                        <label for="profile_picture">Upload Photo (Max 5MB, JPG/PNG/GIF)</label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="handleFileUpload(event)" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Upload Picture</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>

<?php
require_once 'php/config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// DEMO MODE: Create fake user data if no database
$demo_mode = isset($_SESSION['demo_mode']) && $_SESSION['demo_mode'];

if ($demo_mode) {
    // Demo data for testing without database
    $demo_users = [
        ['user_id' => 1, 'username' => 'admin', 'email' => 'admin@example.com', 'first_name' => 'Admin', 'last_name' => 'User', 'role_name' => 'Administrator', 'is_active' => 1],
        ['user_id' => 2, 'username' => 'user', 'email' => 'user@example.com', 'first_name' => 'Regular', 'last_name' => 'User', 'role_name' => 'User', 'is_active' => 1],
        ['user_id' => 3, 'username' => 'john_doe', 'email' => 'john@example.com', 'first_name' => 'John', 'last_name' => 'Doe', 'role_name' => 'User', 'is_active' => 1],
        ['user_id' => 4, 'username' => 'jane_smith', 'email' => 'jane@example.com', 'first_name' => 'Jane', 'last_name' => 'Smith', 'role_name' => 'User', 'is_active' => 1],
        ['user_id' => 5, 'username' => 'bob_wilson', 'email' => 'bob@example.com', 'first_name' => 'Bob', 'last_name' => 'Wilson', 'role_name' => 'User', 'is_active' => 1],
    ];
    $result = ['users' => $demo_users, 'pages' => 1, 'current_page' => 1];
} else {
    require_once 'php/users.php';
    $userManager = new UserManager($mysqli);
    $page = $_GET['page'] ?? 1;
    $result = $userManager->getAllUsers($page, 10);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <div class="container">
            <h1>🔐 User Management System</h1>
            <ul>
                <li>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> <?php if ($demo_mode) echo '(Demo Mode)'; ?></li>
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
            <h2>Dashboard</h2>
            <?php if ($demo_mode): ?>
                <div style="padding: 15px; background: #e8f4f8; border-left: 4px solid #0066cc; margin-bottom: 20px; border-radius: 4px;">
                    <strong>📌 Demo Mode:</strong> You are viewing sample data. Install MySQL for full functionality.
                </div>
            <?php endif; ?>
            
            <div style="margin-bottom: 20px;">
                <input type="text" id="searchInput" placeholder="Search users..." 
                       onkeyup="debounce(function() { searchUsers(document.getElementById('searchInput').value); }, 300)();"
                       style="width: 300px; padding: 10px; border: 2px solid #e0e0e0; border-radius: 5px;">
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['users'] as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                            <td>
                                <a href="profile.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                <?php if ($_SESSION['role_id'] == 2 && $_SESSION['user_id'] != $user['user_id']): ?>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">Delete</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $result['pages']; $i++): ?>
                    <?php if ($i == $result['current_page']): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="dashboard.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>

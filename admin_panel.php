<?php
require_once 'php/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['role_id'] != 2) {
    header('Location: dashboard.php');
    exit;
}

$demo_mode = isset($_SESSION['demo_mode']) && $_SESSION['demo_mode'];

if ($demo_mode) {
    // Demo data for admin panel
    $demo_users = [
        ['user_id' => 1, 'username' => 'admin', 'email' => 'admin@example.com', 'first_name' => 'Admin', 'last_name' => 'User', 'role_name' => 'Administrator', 'is_active' => 1, 'created_at' => '2025-01-01 10:00:00', 'role_id' => 2],
        ['user_id' => 2, 'username' => 'user', 'email' => 'user@example.com', 'first_name' => 'Regular', 'last_name' => 'User', 'role_name' => 'User', 'is_active' => 1, 'created_at' => '2025-01-05 10:00:00', 'role_id' => 1],
        ['user_id' => 3, 'username' => 'john_doe', 'email' => 'john@example.com', 'first_name' => 'John', 'last_name' => 'Doe', 'role_name' => 'User', 'is_active' => 1, 'created_at' => '2025-01-10 10:00:00', 'role_id' => 1],
        ['user_id' => 4, 'username' => 'jane_smith', 'email' => 'jane@example.com', 'first_name' => 'Jane', 'last_name' => 'Smith', 'role_name' => 'User', 'is_active' => 1, 'created_at' => '2025-01-12 10:00:00', 'role_id' => 1],
        ['user_id' => 5, 'username' => 'bob_wilson', 'email' => 'bob@example.com', 'first_name' => 'Bob', 'last_name' => 'Wilson', 'role_name' => 'User', 'is_active' => 1, 'created_at' => '2025-01-15 10:00:00', 'role_id' => 1],
    ];
    
    $result = ['users' => $demo_users, 'pages' => 1, 'current_page' => 1];
    $stats = [
        ['role_name' => 'Administrator', 'count' => 1],
        ['role_name' => 'User', 'count' => 4]
    ];
} else {
    require_once 'php/users.php';

    $userManager = new UserManager($mysqli);
    $page = $_GET['page'] ?? 1;
    $result = $userManager->getAllUsers($page, 10);

    // Get role statistics
    $role_stats = $mysqli->query("SELECT r.role_name, COUNT(u.user_id) as count FROM roles r LEFT JOIN users u ON r.role_id = u.role_id GROUP BY r.role_id");
    $stats = $role_stats->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <div class="container">
            <h1>🔐 User Management System - Admin Panel</h1>
            <ul>
                <li>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="admin_panel.php">Admin Panel</a></li>
                <li><a href="php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php if ($demo_mode): ?>
            <div style="padding: 15px; background: #e8f4f8; border-left: 4px solid #0066cc; margin-bottom: 20px; border-radius: 4px;">
                <strong>📌 Demo Mode:</strong> Admin panel displaying sample user data and statistics.
            </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="grid">
            <?php foreach ($stats as $stat): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($stat['role_name']); ?>s</h3>
                    <p style="font-size: 2em; color: #667eea; font-weight: bold;">
                        <?php echo $stat['count']; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Users Management -->
        <div class="card">
            <h2>User Management</h2>
            
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
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['users'] as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td>
                                <form method="POST" action="php/update_role.php" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <select name="role_id" onchange="this.form.submit();">
                                        <option value="1" <?php echo $user['role_id'] == 1 ? 'selected' : ''; ?>>User</option>
                                        <option value="2" <?php echo $user['role_id'] == 2 ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td><span style="color: <?php echo $user['is_active'] ? 'green' : 'red'; ?>;">
                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="profile.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                <?php if ($_SESSION['user_id'] != $user['user_id']): ?>
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
                        <a href="admin_panel.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>

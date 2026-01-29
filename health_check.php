<?php
// phpinfo();
// Uncomment above to check PHP configuration

// Project health check
echo "=== Task 3: User Management System - Health Check ===\n\n";

// 1. Check PHP Version
echo "✅ PHP Version: " . phpversion() . "\n";

// 2. Check Required Extensions
$required_extensions = ['mysqli', 'json', 'gd'];
echo "\n📦 Required Extensions:\n";
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✅" : "❌";
    echo "  $status $ext\n";
}

// 3. Check File Permissions
echo "\n📁 File Permissions:\n";
$paths_to_check = [
    'uploads/profiles' => 'Upload directory',
    'php' => 'PHP directory',
    'css' => 'CSS directory',
    'js' => 'JS directory'
];

foreach ($paths_to_check as $path => $desc) {
    $full_path = __DIR__ . '/' . $path;
    $exists = is_dir($full_path) ? "✅" : "❌";
    $writable = is_writable($full_path) ? "(writable)" : "(read-only)";
    echo "  $exists $desc: $path $writable\n";
}

// 4. Check Database Connection
echo "\n🗄️  Database Connection:\n";
require_once 'php/config.php';

if ($mysqli->connect_error) {
    echo "  ❌ Connection Failed: " . $mysqli->connect_error . "\n";
    echo "  Please configure php/config.php with correct database credentials\n";
} else {
    echo "  ✅ Connected to: " . DB_NAME . "\n";
    
    // Check tables
    $tables_result = $mysqli->query("SHOW TABLES");
    $table_count = $tables_result->num_rows;
    echo "  ✅ Tables: $table_count found\n";
    
    // Check users
    $users_result = $mysqli->query("SELECT COUNT(*) as count FROM users");
    $users = $users_result->fetch_assoc();
    echo "  ✅ Users: " . $users['count'] . " registered\n";
}

echo "\n=== Setup Complete ===\n";
echo "Access: http://localhost/task3/\n";
echo "Login: admin / admin123\n";
?>

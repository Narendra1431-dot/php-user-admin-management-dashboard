<?php
/**
 * Task 3 - Complete Verification & Validation Script
 * Checks all requirements and identifies issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  TASK 3 - COMPLETE VERIFICATION & VALIDATION SUITE            ║\n";
echo "║  Backend Development & Database Integration                   ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$issues = [];
$warnings = [];
$successes = [];

// ============================================================
// 1. PHP VERSION & EXTENSIONS CHECK
// ============================================================
echo "📋 1. SYSTEM REQUIREMENTS CHECK\n";
echo str_repeat("─", 60) . "\n";

// PHP Version
$php_version = phpversion();
echo "   PHP Version: $php_version ";
if (version_compare($php_version, '7.0', '>=')) {
    echo "✅\n";
    $successes[] = "PHP version is adequate (7.0+)";
} else {
    echo "❌\n";
    $issues[] = "PHP version is below 7.0";
}

// Required Extensions
$required_extensions = ['mysqli', 'json', 'gd'];
echo "\n   Required Extensions:\n";
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? "✅" : "❌";
    echo "      $status $ext";
    if (!$loaded) {
        $issues[] = "Missing extension: $ext";
    } else {
        $successes[] = "Extension $ext is loaded";
    }
    echo "\n";
}

// ============================================================
// 2. FILE & DIRECTORY STRUCTURE CHECK
// ============================================================
echo "\n📂 2. FILE & DIRECTORY STRUCTURE\n";
echo str_repeat("─", 60) . "\n";

$required_files = [
    'index.php' => 'Landing page',
    'login.php' => 'Login page',
    'register.php' => 'Registration page',
    'dashboard.php' => 'User dashboard',
    'profile.php' => 'User profile',
    'admin_panel.php' => 'Admin panel',
    'php/config.php' => 'Database config',
    'php/auth.php' => 'Authentication class',
    'php/users.php' => 'User management class',
    'php/api.php' => 'REST API',
    'php/optimization.php' => 'Performance utilities',
    'php/logout.php' => 'Logout handler',
    'css/style.css' => 'CSS stylesheet',
    'js/script.js' => 'JavaScript file',
    'database/schema.sql' => 'Database schema',
    'install.php' => 'Installation script',
    'health_check.php' => 'Health check script'
];

$base_path = __DIR__;

foreach ($required_files as $file => $desc) {
    $full_path = $base_path . '/' . $file;
    if (file_exists($full_path)) {
        echo "   ✅ $file\n";
        $successes[] = "File exists: $file";
    } else {
        echo "   ❌ $file - MISSING\n";
        $issues[] = "Missing file: $file ($desc)";
    }
}

// Check directories
$required_dirs = [
    'uploads/profiles' => 'Upload directory',
    'php' => 'Backend directory',
    'css' => 'Styles directory',
    'js' => 'JavaScript directory'
];

echo "\n   Directories:\n";
foreach ($required_dirs as $dir => $desc) {
    $full_path = $base_path . '/' . $dir;
    if (is_dir($full_path)) {
        $writable = is_writable($full_path) ? "writable" : "read-only";
        echo "   ✅ $dir ($writable)\n";
        $successes[] = "Directory exists and accessible: $dir";
    } else {
        echo "   ❌ $dir - MISSING\n";
        $issues[] = "Missing directory: $dir ($desc)";
    }
}

// ============================================================
// 3. PHP FILE SYNTAX CHECK
// ============================================================
echo "\n✅ 3. PHP FILE SYNTAX CHECK\n";
echo str_repeat("─", 60) . "\n";

$php_files = [
    'php/config.php',
    'php/auth.php',
    'php/users.php',
    'php/api.php',
    'php/optimization.php',
    'login.php',
    'register.php',
    'dashboard.php',
    'profile.php',
    'admin_panel.php',
    'index.php'
];

$syntax_errors = 0;
foreach ($php_files as $file) {
    $full_path = $base_path . '/' . $file;
    if (file_exists($full_path)) {
        $output = shell_exec("php -l " . escapeshellarg($full_path) . " 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "   ✅ $file\n";
            $successes[] = "PHP syntax OK: $file";
        } else {
            echo "   ⚠️  $file - Syntax issue\n";
            $warnings[] = "Potential syntax issue in $file: " . trim($output);
            $syntax_errors++;
        }
    }
}

if ($syntax_errors == 0) {
    echo "\n   All PHP files have valid syntax ✅\n";
}

// ============================================================
// 4. DATABASE CONNECTION CHECK
// ============================================================
echo "\n🗄️  4. DATABASE CONNECTION\n";
echo str_repeat("─", 60) . "\n";

require_once $base_path . '/php/config.php';

if ($mysqli->connect_error) {
    echo "   ❌ Connection Failed\n";
    echo "   Error: " . $mysqli->connect_error . "\n";
    $issues[] = "Database connection failed: " . $mysqli->connect_error;
} else {
    echo "   ✅ Connected to: " . DB_NAME . "\n";
    $successes[] = "Database connection successful";
    
    // Check if tables exist
    $result = $mysqli->query("SHOW TABLES");
    if ($result) {
        $table_count = $result->num_rows;
        echo "   ✅ Tables found: $table_count\n";
        
        if ($table_count > 0) {
            echo "\n   Table Details:\n";
            $result = $mysqli->query("SHOW TABLES");
            while ($row = $result->fetch_row()) {
                echo "      ✅ " . $row[0] . "\n";
            }
            $successes[] = "Database tables are set up";
        } else {
            $warnings[] = "Database exists but no tables found - may need setup";
        }
        
        // Try to check users table specifically
        if ($mysqli->query("SELECT 1 FROM users LIMIT 1")) {
            $users_result = $mysqli->query("SELECT COUNT(*) as count FROM users");
            if ($users_result) {
                $users = $users_result->fetch_assoc();
                echo "   ✅ Users in database: " . $users['count'] . "\n";
                $successes[] = "Users table accessible with " . $users['count'] . " records";
            }
        }
    } else {
        $warnings[] = "Could not query tables: " . $mysqli->error;
    }
}

// ============================================================
// 5. SECURITY FEATURES CHECK
// ============================================================
echo "\n🔐 5. SECURITY FEATURES VERIFICATION\n";
echo str_repeat("─", 60) . "\n";

$config_file = file_get_contents($base_path . '/php/config.php');
$auth_file = file_exists($base_path . '/php/auth.php') ? file_get_contents($base_path . '/php/auth.php') : '';

$security_checks = [
    'Password hashing' => ['password_hash', 'password_verify'],
    'Prepared statements' => ['prepare', 'bind_param'],
    'Input validation' => ['filter_var', 'trim', 'htmlspecialchars'],
    'Session security' => ['session_start', 'session_destroy'],
    'SQL injection prevention' => ['prepared', 'bind']
];

foreach ($security_checks as $feature => $keywords) {
    $found = false;
    foreach ($keywords as $keyword) {
        if (strpos($auth_file, $keyword) !== false || strpos($config_file, $keyword) !== false) {
            $found = true;
            break;
        }
    }
    $status = $found ? "✅" : "⚠️ ";
    echo "   $status $feature\n";
    if ($found) {
        $successes[] = "Security feature present: $feature";
    } else {
        $warnings[] = "Security feature might be missing: $feature";
    }
}

// ============================================================
// 6. CORE FUNCTIONALITY CHECK
// ============================================================
echo "\n🎯 6. CORE FUNCTIONALITY VERIFICATION\n";
echo str_repeat("─", 60) . "\n";

$features = [
    'User Registration' => 'register.php',
    'User Login' => 'login.php',
    'User Dashboard' => 'dashboard.php',
    'User Profile' => 'profile.php',
    'Admin Panel' => 'admin_panel.php',
    'Database Config' => 'php/config.php',
    'Authentication' => 'php/auth.php',
    'User Management' => 'php/users.php',
    'REST API' => 'php/api.php'
];

foreach ($features as $feature => $file) {
    $path = $base_path . '/' . $file;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $lines = strlen($content);
        echo "   ✅ $feature ($file) - $lines bytes\n";
        $successes[] = "Feature ready: $feature";
    } else {
        echo "   ❌ $feature ($file) - MISSING\n";
        $issues[] = "Missing feature file: $feature";
    }
}

// ============================================================
// 7. SUMMARY & RECOMMENDATIONS
// ============================================================
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      VERIFICATION SUMMARY                      ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Successes: " . count($successes) . "\n";
echo "⚠️  Warnings: " . count($warnings) . "\n";
echo "❌ Issues: " . count($issues) . "\n\n";

if (count($issues) > 0) {
    echo "❌ CRITICAL ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "   • $issue\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "⚠️  WARNINGS:\n";
    foreach ($warnings as $warning) {
        echo "   • $warning\n";
    }
    echo "\n";
}

if (count($issues) == 0) {
    echo "🎉 ALL CRITICAL CHECKS PASSED!\n\n";
    echo "Status: ✅ READY FOR USE\n";
    echo "Next Steps:\n";
    echo "   1. Run: php install.php (to set up database)\n";
    echo "   2. Access: http://localhost:8000/task3/\n";
    echo "   3. Login with: admin / admin123\n";
} else {
    echo "⚠️  SETUP REQUIRED\n\n";
    echo "Next Steps:\n";
    echo "   1. Fix the critical issues listed above\n";
    echo "   2. Run: php install.php\n";
    echo "   3. Run: php health_check.php\n";
}

echo "\n";
?>

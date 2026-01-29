<?php
// Database Configuration with Fallback for Demo Mode
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'task3_userdb');

// Error Reporting (suppress during connection attempt)
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

// Create connection with graceful fallback
$mysqli = null;
$db_connected = false;

try {
    $mysqli = @new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli && !$mysqli->connect_error) {
        $db_connected = true;
        $mysqli->set_charset("utf8");
    } else {
        $mysqli = null;
        $db_connected = false;
    }
} catch (Exception $e) {
    $mysqli = null;
    $db_connected = false;
}

// Re-enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security Headers
if (!headers_sent()) {
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
}

// Session configuration
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

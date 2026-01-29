<?php
require_once 'config.php';

// Handle logout for both demo and normal modes
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    // If in demo mode, just clear session
    if (isset($_SESSION['demo_mode']) && $_SESSION['demo_mode']) {
        $_SESSION = [];
        session_destroy();
    } else {
        // For normal mode, use Auth class to log activity
        require_once 'auth.php';
        $auth = new Auth($mysqli);
        $auth->logout();
    }
}

// Redirect to login page
header('Location: ../login.php');
exit;
?>

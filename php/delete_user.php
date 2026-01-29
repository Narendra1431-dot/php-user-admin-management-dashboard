<?php
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['role_id'] != 2) {
    header('Location: ../dashboard.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ../admin_panel.php');
    exit;
}

require_once 'users.php';
$userManager = new UserManager($mysqli);
$result = $userManager->deleteUser($_GET['id']);

header('Location: ../admin_panel.php');
exit;
?>

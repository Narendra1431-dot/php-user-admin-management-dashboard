<?php
require_once 'config.php';

// Check if user is admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['role_id'] != 2) {
    header('Location: ../admin_panel.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id']) && isset($_POST['role_id'])) {
    require_once 'users.php';
    $userManager = new UserManager($mysqli);
    $result = $userManager->updateUserRole($_POST['user_id'], $_POST['role_id']);
}

header('Location: ../admin_panel.php');
exit;
?>

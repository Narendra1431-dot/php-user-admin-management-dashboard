<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['users' => []]);
    exit;
}

require_once 'users.php';
$userManager = new UserManager($mysqli);

$search_term = $_GET['q'] ?? '';

if (strlen($search_term) >= 2) {
    $users = $userManager->searchUsers($search_term);
} else {
    $users = [];
}

header('Content-Type: application/json');
echo json_encode(['users' => $users]);
?>

<?php
/**
 * Task 3: REST API Endpoints
 * Provides JSON API for frontend AJAX calls
 */

header('Content-Type: application/json');

require_once 'config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($method == 'OPTIONS') {
    exit(0);
}

// Check authentication
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once 'users.php';
require_once 'auth.php';

$userManager = new UserManager($mysqli);
$auth = new Auth($mysqli);

// API Routes
switch ($action) {
    case 'get_user':
        $user_id = $_GET['id'] ?? null;
        if (!$user_id) {
            $user_id = $_SESSION['user_id'];
        }
        
        // Check permission
        if ($user_id != $_SESSION['user_id'] && $_SESSION['role_id'] != 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }
        
        $user = $userManager->getUserById($user_id);
        echo json_encode($user ? ['success' => true, 'data' => $user] : ['error' => 'User not found']);
        break;
    
    case 'get_users':
        if ($_SESSION['role_id'] != 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Admin only']);
            exit;
        }
        
        $page = $_GET['page'] ?? 1;
        $result = $userManager->getAllUsers($page, 10);
        echo json_encode(['success' => true, 'data' => $result]);
        break;
    
    case 'search':
        $query = $_GET['q'] ?? '';
        if (strlen($query) < 2) {
            echo json_encode(['data' => []]);
            exit;
        }
        
        $results = $userManager->searchUsers($query);
        echo json_encode(['success' => true, 'data' => $results]);
        break;
    
    case 'update_profile':
        if ($method != 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }
        
        $user_id = $_POST['user_id'] ?? $_SESSION['user_id'];
        if ($user_id != $_SESSION['user_id'] && $_SESSION['role_id'] != 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }
        
        $result = $userManager->updateProfile(
            $user_id,
            $_POST['first_name'] ?? '',
            $_POST['last_name'] ?? '',
            $_POST['phone'] ?? ''
        );
        
        echo json_encode($result);
        break;
    
    case 'upload_picture':
        if ($method != 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }
        
        if (!isset($_FILES['file'])) {
            echo json_encode(['error' => 'No file provided']);
            exit;
        }
        
        $user_id = $_POST['user_id'] ?? $_SESSION['user_id'];
        if ($user_id != $_SESSION['user_id'] && $_SESSION['role_id'] != 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }
        
        $result = $userManager->uploadProfilePicture($user_id, $_FILES['file']);
        echo json_encode($result);
        break;
    
    case 'delete_user':
        if ($method != 'POST' || $_SESSION['role_id'] != 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Admin only']);
            exit;
        }
        
        $user_id = $_POST['user_id'] ?? null;
        $result = $userManager->deleteUser($user_id);
        echo json_encode($result);
        break;
    
    case 'change_role':
        if ($method != 'POST' || $_SESSION['role_id'] != 2) {
            http_response_code(403);
            echo json_encode(['error' => 'Admin only']);
            exit;
        }
        
        $result = $userManager->updateUserRole(
            $_POST['user_id'] ?? null,
            $_POST['role_id'] ?? null
        );
        echo json_encode($result);
        break;
    
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Unknown action']);
}

?>

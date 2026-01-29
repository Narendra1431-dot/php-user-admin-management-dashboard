<?php
require_once 'config.php';

class UserManager {
    private $mysqli;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
    // Get all users with pagination
    public function getAllUsers($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $count_result = $this->mysqli->query("SELECT COUNT(*) as total FROM users");
        $count_row = $count_result->fetch_assoc();
        $total = $count_row['total'];
        
        // Get paginated users
        $stmt = $this->mysqli->prepare("
            SELECT u.user_id, u.username, u.email, u.first_name, u.last_name, 
                   u.phone, u.profile_picture, u.is_active, r.role_name, u.created_at
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.role_id
            ORDER BY u.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return [
            'users' => $result->fetch_all(MYSQLI_ASSOC),
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }
    
    // Get user by ID
    public function getUserById($user_id) {
        $stmt = $this->mysqli->prepare("
            SELECT u.*, r.role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.role_id
            WHERE u.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Update user profile
    public function updateProfile($user_id, $first_name, $last_name, $phone) {
        // Validate input
        if (empty($first_name) || empty($last_name)) {
            return ['success' => false, 'message' => 'Name fields are required'];
        }
        
        if (!preg_match('/^[a-zA-Z\s]+$/', $first_name) || !preg_match('/^[a-zA-Z\s]+$/', $last_name)) {
            return ['success' => false, 'message' => 'Name can only contain letters'];
        }
        
        $stmt = $this->mysqli->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, phone = ?, updated_at = NOW()
            WHERE user_id = ?
        ");
        $stmt->bind_param("sssi", $first_name, $last_name, $phone, $user_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Update failed'];
        }
    }
    
    // Upload profile picture
    public function uploadProfilePicture($user_id, $file) {
        // Validate file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowed_types)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF allowed'];
        }
        
        if ($file['size'] > $max_size) {
            return ['success' => false, 'message' => 'File size exceeds 5MB'];
        }
        
        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
        $upload_dir = __DIR__ . '/../uploads/profiles/';
        $filepath = $upload_dir . $filename;
        
        // Create directory if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update database
            $stmt = $this->mysqli->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
            $stmt->bind_param("si", $filename, $user_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Profile picture uploaded successfully'];
            }
        }
        
        return ['success' => false, 'message' => 'Upload failed'];
    }
    
    // Delete user (Admin only)
    public function deleteUser($user_id) {
        // Prevent deleting the only admin
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) as admin_count FROM users WHERE role_id = 2");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['admin_count'] <= 1) {
            return ['success' => false, 'message' => 'Cannot delete the last admin'];
        }
        
        $stmt = $this->mysqli->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Delete failed'];
        }
    }
    
    // Search users
    public function searchUsers($search_term) {
        $search = "%{$search_term}%";
        $stmt = $this->mysqli->prepare("
            SELECT u.*, r.role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.role_id
            WHERE u.username LIKE ? OR u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?
            ORDER BY u.created_at DESC
        ");
        $stmt->bind_param("ssss", $search, $search, $search, $search);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Update user role (Admin only)
    public function updateUserRole($user_id, $role_id) {
        $stmt = $this->mysqli->prepare("UPDATE users SET role_id = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $role_id, $user_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User role updated'];
        } else {
            return ['success' => false, 'message' => 'Update failed'];
        }
    }
}
?>

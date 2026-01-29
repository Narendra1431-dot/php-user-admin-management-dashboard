<?php
require_once 'config.php';

class Auth {
    private $mysqli;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
    // Register new user with validation
    public function register($username, $email, $password, $first_name, $last_name) {
        // Input validation
        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        if (strlen($username) < 3 || strlen($username) > 100) {
            return ['success' => false, 'message' => 'Username must be 3-100 characters'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        // Check if username or email already exists
        $stmt = $this->mysqli->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        
        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert new user
        $stmt = $this->mysqli->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("sssss", $username, $email, $password_hash, $first_name, $last_name);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration successful'];
        } else {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    // Login user with prepared statement
    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Username and password are required'];
        }
        
        // DEMO MODE: Allow login without database
        if (!$this->mysqli) {
            // Demo credentials for testing without database
            $demo_users = [
                'admin' => ['password' => 'admin123', 'role_id' => 2, 'email' => 'admin@example.com'],
                'user' => ['password' => 'user123', 'role_id' => 1, 'email' => 'user@example.com']
            ];
            
            if (isset($demo_users[$username]) && $demo_users[$username]['password'] === $password) {
                // Set session variables for demo user
                $_SESSION['user_id'] = 999;  // Demo user ID
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $demo_users[$username]['email'];
                $_SESSION['role_id'] = $demo_users[$username]['role_id'];
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();
                $_SESSION['demo_mode'] = true;
                
                return ['success' => true, 'message' => 'Login successful (Demo Mode)'];
            } else {
                return ['success' => false, 'message' => 'Invalid credentials. Try: admin/admin123 or user/user123'];
            }
        }
        
        // Normal database login
        // Use prepared statement to prevent SQL injection
        $stmt = $this->mysqli->prepare("SELECT user_id, username, email, password_hash, role_id, is_active FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Check if account is active
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Account is inactive'];
            }
            
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();
                
                // Log activity
                $this->logActivity($user['user_id'], 'Login', 'User logged in successfully');
                
                return ['success' => true, 'message' => 'Login successful'];
            } else {
                return ['success' => false, 'message' => 'Invalid password'];
            }
        } else {
            return ['success' => false, 'message' => 'Username not found'];
        }
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    // Check if user is admin
    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['role_id'] == 2;
    }
    
    // Logout user
    public function logout() {
        if ($this->isLoggedIn()) {
            $this->logActivity($_SESSION['user_id'], 'Logout', 'User logged out');
        }
        
        $_SESSION = [];
        session_destroy();
        return true;
    }
    
    // Log user activity
    public function logActivity($user_id, $action, $description) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $this->mysqli->prepare("INSERT INTO activity_log (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $action, $description, $ip_address, $user_agent);
        return $stmt->execute();
    }
}
?>

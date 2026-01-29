-- Task 3: User Management System Database Schema

-- Create Database
CREATE DATABASE IF NOT EXISTS task3_userdb;
USE task3_userdb;

-- Create Roles Table
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    profile_picture VARCHAR(255),
    role_id INT NOT NULL DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id),
    INDEX (username),
    INDEX (email),
    INDEX (role_id)
) ENGINE=InnoDB;

-- Create Activity Log Table
CREATE TABLE IF NOT EXISTS activity_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100),
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX (user_id),
    INDEX (created_at)
) ENGINE=InnoDB;

-- Insert Default Roles
INSERT INTO roles (role_name) VALUES ('User');
INSERT INTO roles (role_name) VALUES ('Admin');

-- Insert Sample Admin User (password: admin123)
INSERT INTO users (username, email, password_hash, first_name, last_name, role_id) 
VALUES ('admin', 'admin@example.com', '$2y$10$8N8cYN8qQqQQqQQqQQqQqeDQqQqQqQqQqQqQqQqQqQqQqQqQqQqQq', 'Admin', 'User', 2);

-- Create VIEW for user list with role names
CREATE VIEW user_list_view AS
SELECT 
    u.user_id,
    u.username,
    u.email,
    u.first_name,
    u.last_name,
    u.phone,
    u.profile_picture,
    u.is_active,
    r.role_name,
    u.created_at,
    u.updated_at
FROM users u
LEFT JOIN roles r ON u.role_id = r.role_id;

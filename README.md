# Task 3: Backend Development & Database Integration
## User Management System with PHP & MySQL

A comprehensive full-stack web application demonstrating backend development, database integration, authentication, CRUD operations, security best practices, and role-based access control.

**Status:** Demo Mode enabled (database optional). See `VERIFICATION_REPORT.html` and `COMPLETE_FEATURE_LIST.txt` for details.

## 🎯 Objectives
- ✅ Implement dynamic features with PHP and MySQL
- ✅ Create a secure authentication system with sessions
- ✅ Implement CRUD operations for user management
- ✅ Use prepared statements to prevent SQL injection
- ✅ Encrypt passwords using bcrypt hashing
- ✅ Implement role-based login (User/Admin)
- ✅ Profile picture upload functionality
- ✅ Activity logging system
- ✅ Responsive UI with CSS

## 📋 Project Structure

```
task3/
├── index.php                    # Landing page
├── login.php                    # Login page
├── register.php                 # Registration page
├── dashboard.php                # User dashboard with user list
├── profile.php                  # User profile with edit & upload
├── admin_panel.php             # Admin panel with user management
│
├── php/
│   ├── config.php              # Database configuration & connection
│   ├── auth.php                # Authentication class
│   ├── users.php               # User management class
│   ├── logout.php              # Logout handler
│   ├── delete_user.php         # Delete user handler
│   ├── update_role.php         # Update user role handler
│   └── search_users.php        # Search API endpoint
│
├── css/
│   └── style.css               # Responsive styling
│
├── js/
│   └── script.js               # Client-side JavaScript
│
├── database/
│   └── schema.sql              # Database schema & initial data
│
├── uploads/
│   └── profiles/               # User profile pictures directory
│
└── README.md                   # Project documentation
```

## 🗄️ Database Schema

### Tables
- **roles**: Role definitions (User, Admin)
- **users**: User information with encrypted passwords
- **activity_log**: User activity tracking

### Features
- Foreign key relationships
- Proper indexing for performance
- Timestamp tracking (created_at, updated_at)
- Views for simplified queries

## 🔐 Security Features

1. **Password Encryption**: Using bcrypt (password_hash/password_verify)
2. **SQL Injection Prevention**: Prepared statements with parameter binding
3. **Input Validation**: Server-side validation for all user inputs
4. **Session Security**: Secure session configuration
5. **XSS Prevention**: HTML entity encoding for output
6. **File Upload Security**: File type and size validation
7. **Access Control**: Role-based permission checks

## 🔑 Authentication System

### Features
- User registration with validation
- Secure login with session management
- Password hashing with bcrypt
- Session-based authentication
- Role-based login (User/Admin)
- Activity logging on login/logout

### Admin Account (Default)
- **Username**: admin
- **Password**: admin123
- **Email**: admin@example.com

## 📝 CRUD Operations

### Create
- User registration
- Admin can create users

### Read
- View user list with pagination
- View user profile
- Search users
- Activity logs

### Update
- Update profile information
- Upload profile picture
- Change user role (Admin only)

### Delete
- Delete user account (Admin only)
- Safety checks to prevent admin deletion

## 🎨 Features

### User Dashboard
- View all registered users
- Search users in real-time
- View user profiles
- Pagination support

### User Profile
- View profile information
- Edit personal information
- Upload/change profile picture
- View role and membership date

### Admin Panel
- User management interface
- Statistics dashboard (user count by role)
- Change user roles
- Delete user accounts
- Search users
- Pagination

## 🚀 Installation & Setup

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web Server (Apache/Nginx)

### Steps

1. **Clone/Extract Project**
```bash
cd task3
```

2. **Create Database**
```bash
mysql -u root -p
mysql> source database/schema.sql
```

3. **Configure Database**
Edit `php/config.php`:
```php
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'task3_userdb');
```

4. **Set Permissions**
```bash
chmod 755 uploads/profiles
```

5. **Access Application**
```
Quick Start (without installing MySQL):

Start PHP built-in server from project root (PowerShell):
```powershell
& "C:\\Users\\LENOVO\\AppData\\Local\\Microsoft\\WinGet\\Packages\\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\\php.exe" -S localhost:8000 -t .
```
Or if `php` is on your PATH:
```powershell
php -S localhost:8000 -t .
```

Then open:

```
http://localhost:8000/
```

Note: The application supports a demo mode so you can run and explore features without a MySQL server. Use the demo credentials shown below.
```

## 💡 Usage

### User Registration
1. Click "Register" on login page
2. Fill in all required fields
3. Password must be at least 6 characters
4. Username must be unique

### User Login
1. Enter username and password
2. Click "Login"
3. Redirected to dashboard

**Demo Credentials** (works without database):
- Admin: `admin` / `admin123`
- User: `user` / `user123`

### Profile Management
1. Click "My Profile"
2. Edit personal information
3. Upload profile picture (Max 5MB, JPG/PNG/GIF)
4. Changes are saved immediately

### Admin Functions
1. Login as admin
2. Click "Admin Panel"
3. View user statistics
4. Search users
5. Change user roles
6. Delete user accounts

## 🔍 Key Code Examples

### Secure Password Verification
```php
if (password_verify($password, $user['password_hash'])) {
    // Password is correct
    $_SESSION['logged_in'] = true;
}
```

### Prepared Statement (SQL Injection Prevention)
```php
$stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
```

### Input Validation
```php
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return ['success' => false, 'message' => 'Invalid email format'];
}
```

### File Upload Security
```php
if (!in_array($file['type'], $allowed_types)) {
    return ['success' => false, 'message' => 'Invalid file type'];
}
```

## 📊 Database Diagram

```
users (PK: user_id)
├── user_id (INT)
├── username (VARCHAR) UNIQUE
├── email (VARCHAR) UNIQUE
├── password_hash (VARCHAR)
├── first_name (VARCHAR)
├── last_name (VARCHAR)
├── phone (VARCHAR)
├── profile_picture (VARCHAR)
├── role_id (INT) FK→ roles
├── is_active (BOOLEAN)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)

roles (PK: role_id)
├── role_id (INT)
├── role_name (VARCHAR) UNIQUE
└── created_at (TIMESTAMP)

activity_log (PK: log_id)
├── log_id (INT)
├── user_id (INT) FK→ users
├── action (VARCHAR)
├── description (TEXT)
├── ip_address (VARCHAR)
├── user_agent (VARCHAR)
└── created_at (TIMESTAMP)
```

## 🧪 Testing

### Test Scenarios
1. **Registration**: Create new user account
2. **Login**: Test valid and invalid credentials
3. **CRUD**: Add, read, update, delete user profiles
4. **Search**: Search users by username/email
5. **Upload**: Upload profile picture with validation
6. **Admin**: Test role-based access control
7. **Security**: Test SQL injection prevention
8. **Pagination**: Test page navigation

### Demo Credentials
```
Username: admin
Password: admin123
```

## 🛡️ Security Best Practices Implemented

- [x] Prepared statements for all database queries
- [x] Password hashing with bcrypt
- [x] Server-side input validation
- [x] HTML entity encoding for output
- [x] Session security headers
- [x] File upload validation
- [x] Role-based access control
- [x] Activity logging
- [x] HTTPS ready (headers configured)
- [x] CSRF token ready structure

## 📈 Performance Features

- Database indexing on frequently queried columns
- Pagination for large datasets
- AJAX search for real-time filtering
- Database views for simplified queries
- Efficient query optimization

## 🎓 Learning Outcomes

This project demonstrates:
1. Full-stack PHP/MySQL development
2. OOP principles with classes
3. Database design and relationships
4. Authentication and authorization
5. Security best practices
6. CRUD operations
7. File handling and uploads
8. Session management
9. API design (AJAX endpoints)
10. Responsive web design

## 📝 Notes

- All passwords are encrypted using bcrypt
- Sessions expire after browser closure
- Admin account cannot be deleted if it's the only admin
- Profile pictures stored in uploads/profiles directory
- All user inputs are validated server-side
- Activity is logged for security auditing

## 🔗 Related Files

- Database Schema: [database/schema.sql](database/schema.sql)
- Configuration: [php/config.php](php/config.php)
- Authentication: [php/auth.php](php/auth.php)
- User Management: [php/users.php](php/users.php)

## 👨‍💻 Developer Notes

- Use prepared statements for all database operations
- Never store plain text passwords
- Validate all user input
- Implement proper error handling
- Log security-relevant events
- Follow the principle of least privilege
- Use HTTPS in production
- Regularly update dependencies

## 📞 Support

For issues or questions, refer to the documentation or contact support.

---

**Project Status**: ✅ Complete
**Last Updated**: January 24, 2026
**Version**: 1.0

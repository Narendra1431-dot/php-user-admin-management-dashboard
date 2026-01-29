# DEPLOYMENT GUIDE

## Quick Deployment (5 Minutes)

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Web Server (Apache, Nginx) OR PHP CLI

### Option 1: PHP Built-in Server (Fastest)
```bash
cd task3
php -S localhost:8000
```
Then open: http://localhost:8000/task3/

### Option 2: XAMPP/WAMP/LAMP Stack
1. Copy project to htdocs/ folder
2. Create database: `task3_userdb`
3. Run: `php install.php`
4. Access: http://localhost/task3/

### Option 3: Docker
```bash
docker-compose up -d
```

---

## Installation Steps

### Step 1: Extract Project
```bash
unzip task3.zip
cd task3
```

### Step 2: Configure Database
Edit `php/config.php`:
```php
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'task3_userdb');
```

### Step 3: Run Installer
```bash
php install.php
```

This will:
- Check PHP configuration
- Create database and tables
- Add default admin user
- Verify file permissions
- Display setup summary

### Step 4: Start Server
```bash
# Windows
start.bat

# Linux/Mac
bash start.sh

# Or manually
php -S localhost:8000
```

### Step 5: Access Application
Open browser: http://localhost:8000/task3/

Default credentials:
- Username: `admin`
- Password: `admin123`

---

## Directory Structure After Setup

```
task3/
├── 📄 index.php                    # Landing page
├── 📄 login.php                    # Login
├── 📄 register.php                 # Register
├── 📄 dashboard.php                # Dashboard
├── 📄 profile.php                  # Profile
├── 📄 admin_panel.php              # Admin
│
├── 📂 php/                         # Backend
│   ├── config.php                  # DB config
│   ├── auth.php                    # Auth class
│   ├── users.php                   # User class
│   ├── api.php                     # REST API
│   ├── optimization.php            # Performance
│   └── *.php                       # Other handlers
│
├── 📂 css/                         # Styles
│   └── style.css
│
├── 📂 js/                          # JavaScript
│   └── script.js
│
├── 📂 database/                    # Database
│   └── schema.sql
│
├── 📂 uploads/                     # User files
│   └── profiles/                   # Profile pictures
│
└── 📂 docs/                        # Documentation
    ├── README.md
    ├── API.md
    └── DEPLOYMENT.md
```

---

## Server Configuration

### Apache (.htaccess)
```apache
RewriteEngine On
RewriteBase /task3/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/task3;
    index index.php;

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Database Setup

### Using PHP Script
```bash
php install.php
```

### Manual Setup
```sql
-- Create database
CREATE DATABASE task3_userdb;
USE task3_userdb;

-- Import schema
SOURCE database/schema.sql;
```

### Reset Database
```bash
php install.php  # Re-runs the schema
```

---

## Configuration

### Environment Variables (Optional)
Create `.env` file:
```
DB_SERVER=localhost
DB_USER=root
DB_PASS=password
DB_NAME=task3_userdb
```

### Update php/config.php:
```php
$env = file_exists('.env') ? parse_ini_file('.env') : [];

define('DB_SERVER', $env['DB_SERVER'] ?? 'localhost');
define('DB_USER', $env['DB_USER'] ?? 'root');
define('DB_PASS', $env['DB_PASS'] ?? '');
define('DB_NAME', $env['DB_NAME'] ?? 'task3_userdb');
```

---

## Performance Tuning

### MySQL Optimization
```sql
-- Add indexes
ALTER TABLE users ADD INDEX (username);
ALTER TABLE users ADD INDEX (email);
ALTER TABLE users ADD INDEX (role_id);
ALTER TABLE activity_log ADD INDEX (user_id);
ALTER TABLE activity_log ADD INDEX (created_at);
```

### PHP Configuration (php.ini)
```ini
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300

; Enable Opcache
opcache.enable = 1
opcache.memory_consumption = 128

; Session optimization
session.gc_maxlifetime = 3600
session.use_strict_mode = 1
```

### Web Server Optimization
```apache
# Enable Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/css text/javascript
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access 1 month"
    ExpiresByType application/javascript "access 1 month"
    ExpiresByType image/jpeg "access 1 month"
</IfModule>
```

---

## SSL/HTTPS Setup

### Let's Encrypt (Free)
```bash
certbot certonly --webroot -w /var/www/html/task3 -d yourdomain.com
```

### Update .htaccess
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

---

## Backup & Recovery

### Backup Database
```bash
mysqldump -u root -p task3_userdb > backup.sql
```

### Backup Files
```bash
zip -r task3_backup.zip task3/
```

### Restore Database
```bash
mysql -u root -p task3_userdb < backup.sql
```

---

## Troubleshooting

### 1. Database Connection Error
```
Error: Connection failed
```
**Solution:**
- Check php/config.php
- Verify MySQL is running
- Test credentials: `mysql -u root -p`

### 2. Permission Denied on uploads/
```
Error: Failed to move uploaded file
```
**Solution:**
```bash
chmod 755 uploads/profiles
chown www-data:www-data uploads/profiles  # Linux
```

### 3. PHP Not Found
```
'php' is not recognized
```
**Solution:**
- Install PHP
- Add PHP to PATH
- Restart terminal

### 4. Port Already in Use
```
Failed to listen on port 8000
```
**Solution:**
```bash
php -S localhost:8001
```

### 5. Blank Page
**Solution:**
- Check error logs: `tail -f /var/log/apache2/error.log`
- Enable debug: `php -d display_errors=1 install.php`
- Check php.ini: `display_errors = On`

---

## Testing

### Health Check
```bash
php health_check.php
```

### Setup Verification
```bash
php install.php
```

### User Test
1. Register new account
2. Login with credentials
3. Update profile
4. Upload picture
5. Check admin panel

---

## Production Checklist

- [ ] Database backed up
- [ ] SSL/HTTPS enabled
- [ ] Error logging configured
- [ ] Permissions set correctly
- [ ] Firewall rules applied
- [ ] Regular backups scheduled
- [ ] Monitoring enabled
- [ ] Security headers set
- [ ] Rate limiting enabled
- [ ] Access logging configured

---

## Support

For issues:
1. Check QUICKSTART.html
2. Review README.md
3. Check API.md
4. Run health_check.php
5. Check server logs

---

**Last Updated:** January 24, 2026
**Version:** 1.0

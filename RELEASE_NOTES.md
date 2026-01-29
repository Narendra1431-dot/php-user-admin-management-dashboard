# Release Notes - Demo Release (2026-01-29)

This release enables a full demo mode so the application can be run and evaluated without a MySQL server installed.

## Highlights
- Demo login credentials: `admin` / `admin123` and `user` / `user123`
- Dashboard, Profile, and Admin Panel populated with sample data
- Graceful DB fallback implemented in `php/config.php`
- Logout, session handling, and UI flows verified

## How to run the demo
1. From the project root, start the PHP built-in server:

```powershell
php -S localhost:8000 -t .
```

(If `php` is not on PATH, use the full path shown in the README.)

2. Open `http://localhost:8000/` in your browser
3. Click Login and use the demo credentials

## Enabling full DB mode
1. Install MySQL and create the database
2. Update `php/config.php` with DB credentials
3. Run `php install.php` (if provided) or import `database/schema.sql`

## Notes
- Demo mode sets a `demo_mode` flag in the session. When a real database is configured, full persistence and admin actions will operate against the DB.
- For production, ensure `display_errors` is disabled and secure DB credentials are used.


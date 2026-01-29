# Changelog

All notable changes to this project are documented in this file.

## [Unreleased] - 2026-01-29
### Added
- Demo mode authentication (admin/user demo accounts) when MySQL is unavailable
- Demo data for Dashboard, Profile, and Admin Panel (5 sample users)
- `RELEASE_NOTES.md` and `CHANGELOG.md`
- `COMPLETE_FEATURE_LIST.txt` updated with current feature set

### Changed
- `php/config.php` updated to gracefully fallback to demo mode when DB connection fails
- `php/auth.php` updated to support demo login and set `demo_mode` session flag
- `dashboard.php`, `profile.php`, `admin_panel.php` updated to display demo data when demo mode active
- `php/logout.php` updated to properly destroy session in demo mode

### Fixed
- Redirects and access checks for demo mode
- UI display issues and missing demo badges

## [2025-..] Previous
- Initial project files and schema



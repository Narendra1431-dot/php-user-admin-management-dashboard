@echo off
echo ========================================
echo Task 3: Quick Start Setup
echo ========================================

echo.
echo Step 1: Checking if PHP is installed...
where php >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo ✓ PHP found
    php --version | find "PHP"
) else (
    echo ✗ PHP not found in PATH
    echo Please ensure PHP is installed and added to PATH
    pause
    exit /b 1
)

echo.
echo Step 2: Checking database connection...
REM Note: You need to configure php/config.php first

echo.
echo Step 3: Starting PHP built-in server...
echo.
echo Server starting on http://localhost:8000/
echo Access the application at: http://localhost:8000/task3/
echo.
echo Press Ctrl+C to stop the server
echo.

cd /d "%~dp0"
php -S localhost:8000

pause

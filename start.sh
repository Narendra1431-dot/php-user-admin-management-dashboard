#!/bin/bash

echo "========================================"
echo "Task 3: Quick Start Setup"
echo "========================================"

echo ""
echo "Step 1: Checking if PHP is installed..."
if command -v php &> /dev/null; then
    echo "✓ PHP found"
    php --version | head -1
else
    echo "✗ PHP not found"
    echo "Please install PHP first"
    exit 1
fi

echo ""
echo "Step 2: Starting PHP built-in server..."
echo ""
echo "Server starting on http://localhost:8000/"
echo "Access the application at: http://localhost:8000/task3/"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

cd "$(dirname "$0")"
php -S localhost:8000


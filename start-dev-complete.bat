@echo off
echo Starting CV Maker Development Environment...
echo.

echo 1. Starting Laravel development server...
start "Laravel Server" cmd /k "php artisan serve --host=127.0.0.1 --port=8000"

echo 2. Starting Vite development server...
start "Vite Server" cmd /k "npm run dev"

echo.
echo Development servers are starting...
echo Laravel: http://127.0.0.1:8000
echo Vite: Check the Vite terminal for the port
echo.
echo Make sure XAMPP MySQL is running!
echo.
pause 
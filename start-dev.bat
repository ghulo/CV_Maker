@echo off
echo Starting CV Maker Development Environment...
echo.

echo Starting Vite Development Server...
start "Vite Dev Server" cmd /k "npm run dev"

echo.
echo Starting Laravel Development Server...
start "Laravel Server" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"

echo.
echo Development servers are starting...
echo Vite: http://localhost:5173
echo Laravel: http://localhost:8000
echo.
echo Press any key to exit...
pause >nul 
@echo off
echo ========================================
echo   Starting Asetik v2 Application
echo ========================================
echo.

echo Checking if port 8000 is available...
netstat -ano | findstr :8000
if %errorlevel% equ 0 (
    echo WARNING: Port 8000 is already in use!
    echo Please close the application using port 8000 or use a different port.
    echo.
    set /p port="Enter a different port number (e.g., 8080): "
) else (
    set port=8000
)

echo.
echo Starting PHP development server on port %port%...
echo.
echo ========================================
echo   Server is running!
echo ========================================
echo.
echo Open your browser and visit:
echo   http://localhost:%port%/
echo.
echo Login credentials:
echo   Username: admin
echo   Password: admin
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

php -S localhost:%port%

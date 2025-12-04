@echo off
echo ========================================
echo   Asetik v2 - Quick Setup Script
echo ========================================
echo.

echo [1/4] Checking PHP installation...
php -v
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH!
    pause
    exit /b 1
)
echo.

echo [2/4] Checking PDO PostgreSQL extension...
php -m | findstr pdo_pgsql
if %errorlevel% neq 0 (
    echo ERROR: PDO PostgreSQL extension is not enabled!
    echo Please enable it in php.ini:
    echo   extension=pdo_pgsql
    pause
    exit /b 1
)
echo OK: PDO PostgreSQL is enabled
echo.

echo [3/4] Checking Composer dependencies...
if not exist "vendor\" (
    echo Installing Composer dependencies...
    composer install
) else (
    echo OK: Vendor folder exists
)
echo.

echo [4/4] Testing database connection...
php test_connection.php
echo.

echo ========================================
echo Setup complete!
echo ========================================
echo.
echo Next steps:
echo 1. Make sure your .env file has correct Supabase credentials
echo 2. Import database/supabase_schema.sql to Supabase
echo 3. Start your web server (XAMPP/WAMP or php -S localhost:8000)
echo 4. Open http://localhost:8000/ in your browser
echo.
pause

@echo off
REM Import database script for XAMPP MySQL

echo Importing database...
cd /d C:\xampp\mysql\bin

mysql -u root -e "CREATE DATABASE IF NOT EXISTS asetik CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root asetik < "c:\xampp\htdocs\asetik_v2\database\asetik (9).sql"

echo.
echo Database import completed!
echo.
pause

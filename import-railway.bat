@echo off
echo Importing database to Railway MySQL...
echo.

REM Use Railway CLI to get environment variables and import database
railway run --service web cmd /c "echo Connecting to MySQL..." 

echo.
echo Please run this command manually:
echo railway run --service MySQL bash -c "mysql -u $MYSQLUSER -p$MYSQLPASSWORD -h $MYSQLHOST -P $MYSQLPORT $MYSQLDATABASE < /app/database/asetik\ (9).sql"
echo.
pause

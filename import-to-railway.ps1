# Railway Database Import Script
# This script imports your local MySQL database to Railway

Write-Host "=== Railway Database Import ===" -ForegroundColor Cyan
Write-Host ""

# Read the SQL file
$sqlFile = "database\asetik (9).sql"
Write-Host "Reading SQL file: $sqlFile" -ForegroundColor Yellow

if (!(Test-Path $sqlFile)) {
    Write-Host "Error: SQL file not found!" -ForegroundColor Red
    exit 1
}

# Copy SQL file to a temp location without spaces in the name
$tempSql = "database\asetik_import.sql"
Copy-Item $sqlFile $tempSql -Force
Write-Host "Created temporary SQL file: $tempSql" -ForegroundColor Green

Write-Host ""
Write-Host "Now run this command in your terminal:" -ForegroundColor Cyan
Write-Host "railway run --service web php -r `"``$pdo = new PDO('mysql:host=' . getenv('MYSQLHOST') . ';port=' . getenv('MYSQLPORT') . ';dbname=' . getenv('MYSQLDATABASE'), getenv('MYSQLUSER'), getenv('MYSQLPASSWORD')); ``$sql = file_get_contents('database/asetik_import.sql'); ``$pdo->exec(``$sql); echo 'Database imported successfully!';`"" -ForegroundColor Yellow

Write-Host ""
Write-Host "Or use this simpler method:" -ForegroundColor Cyan
Write-Host "1. Go to Railway dashboard" -ForegroundColor White
Write-Host "2. Click on your MySQL service" -ForegroundColor White
Write-Host "3. Click 'Data' tab" -ForegroundColor White
Write-Host "4. Click 'Query' and paste the SQL content" -ForegroundColor White

# Asetik v2 - Folder Reorganization Script
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Asetik v2 - Folder Reorganization" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Create new directory structure
Write-Host "[1/5] Creating new directory structure..." -ForegroundColor Yellow

$directories = @(
    "public",
    "public\assets",
    "public\assets\css",
    "public\assets\js",
    "public\assets\images",
    "public\uploads",
    "src",
    "src\config",
    "src\modules",
    "src\modules\auth",
    "src\modules\products",
    "src\modules\users",
    "src\modules\records",
    "src\includes",
    "docs",
    "scripts"
)

foreach ($dir in $directories) {
    New-Item -ItemType Directory -Force -Path $dir | Out-Null
    Write-Host "  Created: $dir" -ForegroundColor Green
}

Write-Host ""
Write-Host "[2/5] Moving documentation files..." -ForegroundColor Yellow

# Move documentation
$docFiles = @("DEPLOY.md", "HOW_TO_RUN.md", "MIGRATION_GUIDE.md", "QUICK_START.md", "RAILWAY_DEPLOYMENT.md", "RAILWAY_FIX.md", "README.md")
foreach ($file in $docFiles) {
    if (Test-Path $file) {
        Move-Item -Path $file -Destination "docs\" -Force
        Write-Host "  Moved: $file -> docs\" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "[3/5] Moving script files..." -ForegroundColor Yellow

# Move scripts
$scriptFiles = @("setup.bat", "start.bat", "start.sh", "test_connection.php", "health.php")
foreach ($file in $scriptFiles) {
    if (Test-Path $file) {
        Move-Item -Path $file -Destination "scripts\" -Force
        Write-Host "  Moved: $file -> scripts\" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "[4/5] Moving configuration files..." -ForegroundColor Yellow

# Move config
if (Test-Path "dbConnection.php") {
    Move-Item -Path "dbConnection.php" -Destination "src\config\" -Force
    Write-Host "  Moved: dbConnection.php -> src\config\" -ForegroundColor Green
}

Write-Host ""
Write-Host "[5/5] Moving module files..." -ForegroundColor Yellow

# Move auth module
if (Test-Path "login\login.php") {
    Move-Item -Path "login\login.php" -Destination "src\modules\auth\" -Force
    Write-Host "  Moved: login\login.php -> src\modules\auth\" -ForegroundColor Green
}
if (Test-Path "logout.php") {
    Move-Item -Path "logout.php" -Destination "src\modules\auth\" -Force
    Write-Host "  Moved: logout.php -> src\modules\auth\" -ForegroundColor Green
}

# Move products module
if (Test-Path "crud_products") {
    Get-ChildItem "crud_products\*" | Move-Item -Destination "src\modules\products\" -Force
    Write-Host "  Moved: crud_products\* -> src\modules\products\" -ForegroundColor Green
}

# Move users module
if (Test-Path "new_crud_admin") {
    Get-ChildItem "new_crud_admin\*" | Move-Item -Destination "src\modules\users\" -Force
    Write-Host "  Moved: new_crud_admin\* -> src\modules\users\" -ForegroundColor Green
}

# Move records module
if (Test-Path "records") {
    Get-ChildItem "records\*" | Move-Item -Destination "src\modules\records\" -Force
    Write-Host "  Moved: records\* -> src\modules\records\" -ForegroundColor Green
}

# Move main application files
$mainFiles = @("index.php", "apply_fix.php", "approve.php", "showdata.php", "take_back.php")
foreach ($file in $mainFiles) {
    if (Test-Path $file) {
        Move-Item -Path $file -Destination "public\" -Force
        Write-Host "  Moved: $file -> public\" -ForegroundColor Green
    }
}

# Move logo to assets
if (Test-Path "logo") {
    Get-ChildItem "logo\*" | Move-Item -Destination "public\assets\images\" -Force
    Write-Host "  Moved: logo\* -> public\assets\images\" -ForegroundColor Green
}

# Move uploads
if (Test-Path "upload_image\uploads") {
    Get-ChildItem "upload_image\uploads\*" | Move-Item -Destination "public\uploads\" -Force
    Write-Host "  Moved: upload_image\uploads\* -> public\uploads\" -ForegroundColor Green
}
if (Test-Path "upload_image\uploads_products") {
    Get-ChildItem "upload_image\uploads_products\*" | Move-Item -Destination "public\uploads\" -Force
    Write-Host "  Moved: upload_image\uploads_products\* -> public\uploads\" -ForegroundColor Green
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Reorganization Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Review the new structure" -ForegroundColor White
Write-Host "2. Update file paths in your code" -ForegroundColor White
Write-Host "3. Delete empty old directories" -ForegroundColor White
Write-Host "4. Test your application" -ForegroundColor White
Write-Host ""

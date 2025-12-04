# Path Update Script - Updates all file paths after reorganization
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Updating File Paths" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$filesUpdated = 0

# Function to update file content
function Update-FilePaths {
    param($FilePath)
    
    $content = Get-Content $FilePath -Raw
    $originalContent = $content
    
    # Update dbConnection paths for modules (2 levels deep)
    $content = $content -replace 'require_once\("\.\.\/dbConnection\.php"\)', 'require_once(__DIR__ . "/../../config/dbConnection.php")'
    $content = $content -replace 'require_once\(\"\.\.\/\.\.\/dbConnection\.php\"\)', 'require_once(__DIR__ . "/../../../src/config/dbConnection.php")'
    
    # Update index.php redirects
    $content = $content -replace "Location: \.\.\/index\.php", "Location: ../../../public/index.php"
    $content = $content -replace "Location: \.\.\/\.\.\/index\.php", "Location: ../../../public/index.php"
    
    # Update login redirects
    $content = $content -replace "Location: login\/login\.php", "Location: ../auth/login.php"
    $content = $content -replace "Location: \.\.\/login\/login\.php", "Location: ../auth/login.php"
    
    # Update logout redirects
    $content = $content -replace 'href="logout\.php"', 'href="../auth/logout.php"'
    $content = $content -replace 'href="\.\.\/logout\.php"', 'href="../auth/logout.php"'
    
    # Update logo paths
    $content = $content -replace 'src="\.\.\/logo\/logo\.png"', 'src="../../../public/assets/images/logo.png"'
    $content = $content -replace 'src="\.\.\/\.\.\/logo\/logo\.png"', 'src="../../../public/assets/images/logo.png"'
    
    # Update upload paths in move_uploaded_file
    $content = $content -replace '\.\.\/upload_image\/uploads\/', '../../../public/uploads/'
    $content = $content -replace '\.\.\/\.\.\/upload_image\/uploads\/', '../../../public/uploads/'
    $content = $content -replace '\.\.\/upload_image\/uploads_products\/', '../../../public/uploads/'
    $content = $content -replace '\.\.\/\.\.\/upload_image\/uploads_products\/', '../../../public/uploads/'
    
    # Update image src paths
    $content = $content -replace 'src="\.\.\/upload_image\/uploads\/', 'src="../../../public/uploads/'
    $content = $content -replace 'src="\.\.\/\.\.\/upload_image\/uploads\/', 'src="../../../public/uploads/'
    
    # Only write if content changed
    if ($content -ne $originalContent) {
        Set-Content -Path $FilePath -Value $content -NoNewline
        return $true
    }
    return $false
}

Write-Host "[1/4] Updating auth module files..." -ForegroundColor Yellow
Get-ChildItem -Path "src\modules\auth\*.php" -ErrorAction SilentlyContinue | ForEach-Object {
    if (Update-FilePaths $_.FullName) {
        Write-Host "  Updated: $($_.Name)" -ForegroundColor Green
        $filesUpdated++
    }
}

Write-Host ""
Write-Host "[2/4] Updating products module files..." -ForegroundColor Yellow
Get-ChildItem -Path "src\modules\products\*.php" -ErrorAction SilentlyContinue | ForEach-Object {
    if (Update-FilePaths $_.FullName) {
        Write-Host "  Updated: $($_.Name)" -ForegroundColor Green
        $filesUpdated++
    }
}

Write-Host ""
Write-Host "[3/4] Updating users module files..." -ForegroundColor Yellow
Get-ChildItem -Path "src\modules\users\*.php" -ErrorAction SilentlyContinue | ForEach-Object {
    if (Update-FilePaths $_.FullName) {
        Write-Host "  Updated: $($_.Name)" -ForegroundColor Green
        $filesUpdated++
    }
}

Write-Host ""
Write-Host "[4/4] Updating records module files..." -ForegroundColor Yellow
Get-ChildItem -Path "src\modules\records\*.php" -ErrorAction SilentlyContinue | ForEach-Object {
    if (Update-FilePaths $_.FullName) {
        Write-Host "  Updated: $($_.Name)" -ForegroundColor Green
        $filesUpdated++
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Path Update Complete!" -ForegroundColor Green
Write-Host "  Files Updated: $filesUpdated" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next: Start your server with:" -ForegroundColor Yellow
Write-Host "  php -S localhost:8000 -t public" -ForegroundColor White
Write-Host ""

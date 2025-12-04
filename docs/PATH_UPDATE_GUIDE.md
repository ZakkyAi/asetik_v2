# Path Update Reference Guide

## After Reorganization - Path Changes

### For files in `public/` folder:

**Database Connection:**
```php
// OLD
require_once("dbConnection.php");

// NEW
require_once(__DIR__ . "/../src/config/dbConnection.php");
```

**Login/Logout:**
```php
// OLD
header('Location: login/login.php');
header('Location: logout.php');

// NEW
header('Location: ../src/modules/auth/login.php');
header('Location: ../src/modules/auth/logout.php');
```

**Module Links:**
```php
// OLD
href="crud_products/index.php"
href="new_crud_admin/index.php"
href="records/index.php"

// NEW
href="../src/modules/products/index.php"
href="../src/modules/users/index.php"
href="../src/modules/records/index.php"
```

**Assets:**
```php
// OLD
src="logo/logo.png"
src="upload_image/uploads/file.jpg"

// NEW
src="assets/images/logo.png"
src="uploads/file.jpg"
```

### For files in `src/modules/*/` folders:

**Database Connection:**
```php
// OLD
require_once("../dbConnection.php");
require_once("../../dbConnection.php");

// NEW
require_once(__DIR__ . "/../../../src/config/dbConnection.php");
// OR
require_once(__DIR__ . "/../../config/dbConnection.php");
```

**Back to Home:**
```php
// OLD
header('Location: ../index.php');
header('Location: ../../index.php');

// NEW
header('Location: ../../../public/index.php');
```

**Login Redirect:**
```php
// OLD
header('Location: login/login.php');
header('Location: ../login/login.php');

// NEW
header('Location: ../auth/login.php');
```

**Upload Paths:**
```php
// OLD
move_uploaded_file($tmp, "../upload_image/uploads/" . $filename);

// NEW
move_uploaded_file($tmp, __DIR__ . "/../../../public/uploads/" . $filename);
```

**Image Display:**
```php
// OLD
<img src="../upload_image/uploads/<?php echo $file; ?>">

// NEW
<img src="../../../public/uploads/<?php echo $file; ?>">
```

### For files in `src/modules/auth/` folder:

**After Login Success:**
```php
// OLD
header('Location: ../index.php');

// NEW
header('Location: ../../../public/index.php');
```

**Logo in Login Page:**
```php
// OLD
<img src="../logo/logo.png">

// NEW
<img src="../../../public/assets/images/logo.png">
```

## Quick Find & Replace Commands

For PowerShell (run in project root):

```powershell
# Update dbConnection paths in src/modules
Get-ChildItem -Path "src\modules" -Recurse -Filter "*.php" | ForEach-Object {
    (Get-Content $_.FullName) -replace 'require_once\("\.\.\/dbConnection\.php"\)', 'require_once(__DIR__ . "/../../config/dbConnection.php")' | Set-Content $_.FullName
}

# Update index.php redirects
Get-ChildItem -Path "src\modules" -Recurse -Filter "*.php" | ForEach-Object {
    (Get-Content $_.FullName) -replace "Location: \.\.\/index\.php", "Location: ../../../public/index.php" | Set-Content $_.FullName
}
```

## Testing Checklist

- [ ] Login page loads
- [ ] Login redirects to dashboard
- [ ] Logo displays correctly
- [ ] All menu links work
- [ ] Product CRUD operations work
- [ ] User CRUD operations work
- [ ] Records CRUD operations work
- [ ] File uploads work
- [ ] Images display correctly
- [ ] Logout works

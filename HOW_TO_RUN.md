# How to Run Asetik v2 Project

## 🚀 Complete Setup Guide

Follow these steps to get your project running locally with Supabase.

---

## Prerequisites

### 1. Software Requirements
- ✅ **PHP 7.4 or higher** with PDO extension
- ✅ **Apache/XAMPP/WAMP** (or any PHP web server)
- ✅ **Composer** (PHP package manager)
- ✅ **Supabase Account** (free tier available)

### 2. Check PHP Installation
```bash
# Check PHP version
php -v

# Check if PDO PostgreSQL extension is installed
php -m | grep pdo_pgsql
```

If `pdo_pgsql` is not listed, you need to enable it:
- **Windows (XAMPP/WAMP)**: Edit `php.ini` and uncomment:
  ```ini
  extension=pdo_pgsql
  ```
- **Linux/Mac**: Install via package manager:
  ```bash
  # Ubuntu/Debian
  sudo apt-get install php-pgsql
  
  # Mac (Homebrew)
  brew install php-pgsql
  ```

---

## Step-by-Step Setup

### Step 1: Install Dependencies

Open terminal in the project directory and run:

```bash
cd d:\PostgreSQL\asetik_v2
composer install
```

This will install:
- `vlucas/phpdotenv` - For loading environment variables

---

### Step 2: Set Up Supabase Database

#### 2.1 Create Supabase Project
1. Go to https://supabase.com
2. Sign up or log in
3. Click **"New Project"**
4. Fill in:
   - **Name**: asetik_v2 (or any name you prefer)
   - **Database Password**: Choose a strong password (save this!)
   - **Region**: Choose closest to you
5. Wait for project to be created (~2 minutes)

#### 2.2 Import Database Schema
1. In Supabase Dashboard, go to **SQL Editor** (left sidebar)
2. Click **"New Query"**
3. Open the file `database/supabase_schema.sql` from your project
4. Copy ALL the contents
5. Paste into the SQL Editor
6. Click **"Run"** or press `Ctrl+Enter`
7. You should see: "Success. No rows returned"

This will create:
- ✅ 4 tables (users, products, records, repair)
- ✅ 2 ENUM types (user_level, record_status)
- ✅ Sample data (users, products, records)

#### 2.3 Get Database Credentials
1. Go to **Project Settings** (gear icon in sidebar)
2. Click **Database** tab
3. Scroll to **Connection String** section
4. You'll need these values:
   - **Host**: `db.xxxxxxxxxxxxx.supabase.co`
   - **Database name**: `postgres`
   - **User**: `postgres`
   - **Password**: (the password you set in step 2.1)
   - **Port**: `5432`

---

### Step 3: Configure Environment Variables

1. Open the `.env` file in your project root
2. Update with your Supabase credentials:

```env
DB_HOST=db.xxxxxxxxxxxxx.supabase.co
DB_NAME=postgres
DB_USER=postgres
DB_PASS=your_database_password_here
DB_PORT=5432
```

**Example:**
```env
DB_HOST=db.abcdefghijklmnop.supabase.co
DB_NAME=postgres
DB_USER=postgres
DB_PASS=MyStr0ngP@ssw0rd!
DB_PORT=5432
```

⚠️ **Important**: Replace the values with YOUR actual Supabase credentials!

---

### Step 4: Set Up Web Server

#### Option A: Using XAMPP (Windows)

1. **Copy project to htdocs**:
   ```
   Copy d:\PostgreSQL\asetik_v2
   To: C:\xampp\htdocs\asetik_v2
   ```

2. **Start XAMPP**:
   - Open XAMPP Control Panel
   - Click **Start** for Apache

3. **Access the project**:
   ```
   http://localhost/asetik_v2/
   ```

#### Option B: Using WAMP (Windows)

1. **Copy project to www**:
   ```
   Copy d:\PostgreSQL\asetik_v2
   To: C:\wamp64\www\asetik_v2
   ```

2. **Start WAMP**:
   - Start WAMP server
   - Wait for icon to turn green

3. **Access the project**:
   ```
   http://localhost/asetik_v2/
   ```

#### Option C: Using PHP Built-in Server (Any OS)

1. **Navigate to project**:
   ```bash
   cd d:\PostgreSQL\asetik_v2
   ```

2. **Start PHP server**:
   ```bash
   php -S localhost:8000
   ```

3. **Access the project**:
   ```
   http://localhost:8000/
   ```

---

### Step 5: Test Database Connection

Before accessing the main application, test the database connection:

#### Method 1: Command Line
```bash
cd d:\PostgreSQL\asetik_v2
php test_connection.php
```

**Expected Output:**
```
Testing Supabase Connection...
DB_HOST: db.xxxxxxxxxxxxx.supabase.co
DB_NAME: postgres
DB_USER: postgres
DB_PORT: 5432

✓ Connection successful!
PostgreSQL version: PostgreSQL 15.x.x ...
```

#### Method 2: Browser
Visit: `http://localhost/asetik_v2/test.php`

You should see:
- ✓ Connection Successful! (in green)
- PostgreSQL version information

---

### Step 6: Create Upload Directories

Make sure these directories exist and are writable:

```bash
# Create directories if they don't exist
mkdir -p upload_image/uploads
mkdir -p upload_image/uploads_products

# Set permissions (Linux/Mac)
chmod 755 upload_image/uploads
chmod 755 upload_image/uploads_products
```

**Windows**: Right-click folders → Properties → Security → Make sure your user has write permissions

---

### Step 7: Access the Application

1. **Open your browser**
2. **Navigate to**:
   ```
   http://localhost/asetik_v2/
   ```
   or
   ```
   http://localhost:8000/
   ```

3. **Login with default admin account**:
   - **Username**: `admin`
   - **Password**: `admin` (default password from sample data)

---

## 🧪 Testing the Application

### Test Admin Features

1. **Login as Admin**:
   - Username: `admin`
   - Password: `admin`

2. **Test User Management**:
   - Go to **User** menu
   - Try viewing, adding, editing users

3. **Test Product Management**:
   - Go to **Peripheral** menu
   - Try adding a new product with image upload
   - Edit an existing product
   - Delete a product

4. **Test Records Management**:
   - Go to **Records** menu
   - Create a new record
   - Edit a record
   - Delete a record

5. **Test Repair Approval**:
   - Go to **Approve Repair** menu
   - View repair requests

### Test Normal User Features

1. **Logout** from admin account

2. **Login as Normal User**:
   - Username: `Im0somn1s`
   - Password: `123` (from sample data)

3. **Test User Features**:
   - View **Show Data**
   - Submit **Apply for Repair** request

---

## 🔧 Troubleshooting

### Problem 1: "Connection failed: could not find driver"

**Solution**: Enable PDO PostgreSQL extension

**Windows (XAMPP/WAMP)**:
1. Find `php.ini` file (usually in `C:\xampp\php\php.ini`)
2. Open with text editor
3. Find this line: `;extension=pdo_pgsql`
4. Remove the semicolon: `extension=pdo_pgsql`
5. Save and restart Apache

**Linux/Mac**:
```bash
# Ubuntu/Debian
sudo apt-get install php-pgsql
sudo systemctl restart apache2

# Mac
brew install php-pgsql
brew services restart php
```

---

### Problem 2: "Connection failed: SQLSTATE[08006]"

**Solution**: Check your `.env` credentials

1. Verify Supabase credentials are correct
2. Make sure there are no extra spaces in `.env`
3. Check if Supabase project is active (not paused)
4. Test connection from Supabase dashboard

---

### Problem 3: "Failed to load .env file"

**Solution**: Install Composer dependencies

```bash
cd d:\PostgreSQL\asetik_v2
composer install
```

---

### Problem 4: Images not uploading

**Solution**: Check directory permissions

**Windows**:
1. Right-click `upload_image` folder
2. Properties → Security
3. Edit → Add your user → Full Control

**Linux/Mac**:
```bash
chmod -R 755 upload_image/
chown -R www-data:www-data upload_image/  # For Apache
```

---

### Problem 5: "Invalid input value for enum"

**Solution**: Status values are case-sensitive

Make sure you're using:
- ✅ `good`, `broken`, `not taken`, `pending`, `fixing`, `decline`
- ❌ NOT: `Good`, `Broken`, `Decline`

---

### Problem 6: Page shows blank/white screen

**Solution**: Enable error reporting

Add to the top of `index.php`:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

Then check what error appears.

---

## 📁 Project Structure

```
asetik_v2/
├── .env                          # Database credentials (DO NOT commit to git!)
├── dbConnection.php              # Database connection
├── index.php                     # Main dashboard
├── composer.json                 # PHP dependencies
├── test_connection.php           # Connection test (CLI)
├── test.php                      # Connection test (Browser)
│
├── database/
│   ├── supabase_schema.sql       # PostgreSQL schema + sample data
│   └── asetik (9).sql            # Original MySQL schema (backup)
│
├── login/
│   └── login.php                 # Login page
│
├── crud_products/                # Product management
│   ├── index.php                 # List products
│   ├── add_product.php           # Add product
│   ├── update_product.php        # Edit product
│   └── delete_product.php        # Delete product
│
├── records/                      # Records management
│   ├── index.php                 # List records
│   ├── create.php                # Create record
│   ├── edit.php                  # Edit record
│   └── delete.php                # Delete record
│
├── new_crud_admin/               # User management
│   ├── index.php                 # List users
│   ├── add_user.php              # Add user
│   ├── edit_user.php             # Edit user
│   └── delete_user.php           # Delete user
│
├── upload_image/
│   ├── uploads/                  # User profile photos
│   └── uploads_products/         # Product photos
│
└── logo/
    └── logo.png                  # Application logo
```

---

## 🔐 Default Login Credentials

### Admin Account
- **Username**: `admin`
- **Password**: `admin`
- **Email**: admin@gmail.com

### Normal User Accounts
| Username | Password | Email |
|----------|----------|-------|
| Im0somn1s | 123 | Im0somn1s@gmail.com |
| 123 | 123 | harjay@gmail.com |
| elda | elda | elda@gmail.com |
| arif | arif | arif@gmail.com |

⚠️ **Security Note**: Change these passwords in production!

---

## 🚀 Quick Start Commands

```bash
# 1. Install dependencies
composer install

# 2. Test connection
php test_connection.php

# 3. Start PHP server (if not using XAMPP/WAMP)
php -S localhost:8000

# 4. Open browser
# Visit: http://localhost:8000/
```

---

## 📝 Important Notes

1. **Security**:
   - Change default passwords before deploying to production
   - Never commit `.env` file to version control
   - Use HTTPS in production

2. **File Uploads**:
   - Maximum upload size depends on `php.ini` settings
   - Check `upload_max_filesize` and `post_max_size`

3. **Database**:
   - Supabase free tier has limits (500MB database, 2GB bandwidth/month)
   - Upgrade if you need more resources

4. **Backup**:
   - Regularly backup your database from Supabase dashboard
   - Keep a copy of uploaded images

---

## 🆘 Need Help?

1. **Check the logs**:
   - Apache error log: `C:\xampp\apache\logs\error.log`
   - PHP error log: Check `php.ini` for `error_log` location

2. **Test connection**: Run `php test_connection.php`

3. **Verify Supabase**: Check if project is active in Supabase dashboard

4. **Review documentation**:
   - `MIGRATION_GUIDE.md` - Full migration details
   - `QUICK_START.md` - Quick reference

---

## ✅ Success Checklist

- [ ] PHP 7.4+ installed
- [ ] PDO PostgreSQL extension enabled
- [ ] Composer dependencies installed
- [ ] Supabase project created
- [ ] Database schema imported
- [ ] `.env` file configured
- [ ] Upload directories created
- [ ] Connection test successful
- [ ] Can access login page
- [ ] Can login as admin
- [ ] Can view/add/edit/delete products
- [ ] Can view/add/edit/delete records
- [ ] Image uploads working

---

**Project Status**: ✅ Ready to Run
**Last Updated**: December 3, 2025

Happy coding! 🎉

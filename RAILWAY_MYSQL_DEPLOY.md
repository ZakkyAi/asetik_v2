# 🚂 Railway MySQL Deployment Guide

## ✅ MySQL Conversion Complete!

Your application has been converted from PostgreSQL to MySQL for Railway deployment.

## 📋 What Changed

### 1. Database Schema
- ✅ Created `database/mysql_schema.sql` (MySQL compatible)
- ✅ Converted PostgreSQL ENUM types to MySQL ENUM
- ✅ Changed SERIAL to AUTO_INCREMENT
- ✅ Updated timestamp defaults

### 2. Database Connection
- ✅ Updated `src/config/dbConnection.php` for MySQL
- ✅ Supports both Railway and local XAMPP
- ✅ Auto-detects Railway environment variables

### 3. Environment Variables
- ✅ Created `.env.example` with MySQL config
- ✅ Supports Railway's MySQL variable names

## 🚀 Deploy to Railway

### Step 1: Create MySQL Database on Railway

1. Go to [Railway.app](https://railway.app/)
2. Click "New Project"
3. Select "Provision MySQL"
4. Wait for database to be created

### Step 2: Import Database Schema

1. In Railway, click on your MySQL service
2. Go to "Data" tab
3. Click "Query"
4. Copy and paste the contents of `database/mysql_schema.sql`
5. Click "Execute"

### Step 3: Deploy Your Application

1. In Railway, click "New" → "GitHub Repo"
2. Select your `asetik_v2` repository
3. Railway will auto-detect it's a PHP app

### Step 4: Configure Environment

Railway automatically sets these variables for MySQL:
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLDATABASE`
- `MYSQLUSER`
- `MYSQLPASSWORD`

Your `dbConnection.php` will automatically use them!

### Step 5: Set Start Command (if needed)

If Railway doesn't auto-detect, add this in Settings → Deploy:

**Start Command:**
```bash
php -S 0.0.0.0:$PORT -t public
```

### Step 6: Deploy!

1. Push your code to GitHub
2. Railway will automatically deploy
3. Visit your Railway URL

## 🏠 Local Development with XAMPP

### 1. Create Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create new database: `asetik_v2`
3. Import `database/mysql_schema.sql`

### 2. Update .env

Copy `.env.example` to `.env` and update:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=asetik_v2
DB_USER=root
DB_PASSWORD=
```

### 3. Access Application

- **XAMPP**: `http://localhost/asetik_v2/`
- **PHP Server**: `php -S localhost:8000 -t public` → `http://localhost:8000/`

## 🔧 Troubleshooting

### "Access denied for user"
- Check your MySQL username and password
- Make sure the user has permissions on the database

### "Unknown database"
- Make sure you created the database
- Check the database name in `.env`

### "Could not find driver"
- Enable `pdo_mysql` extension in `php.ini`
- Restart Apache/PHP

### Railway Connection Issues
- Make sure MySQL service is running
- Check that environment variables are set
- View logs in Railway dashboard

## 📊 Default Login Credentials

After importing the schema:

**Admin:**
- Username: `admin`
- Password: `admin` (hash is already in database)

**Test User:**
- Username: `Im0somn1s`
- Password: `password` (hash is already in database)

## 🎯 Next Steps

1. ✅ Test locally with XAMPP
2. ✅ Push to GitHub
3. ✅ Deploy to Railway
4. ✅ Import database schema
5. ✅ Test your live application!

## 📝 Files Modified

- ✅ `src/config/dbConnection.php` - MySQL connection
- ✅ `database/mysql_schema.sql` - MySQL schema
- ✅ `.env.example` - MySQL environment template

## 🌐 Railway MySQL Variables

Railway automatically provides:
```
MYSQLHOST=containers-us-west-xxx.railway.app
MYSQLPORT=6379
MYSQLDATABASE=railway
MYSQLUSER=root
MYSQLPASSWORD=xxxxxxxxxxxxx
```

Your app will automatically use these! No manual configuration needed.

---

**Ready to deploy!** 🚀

Push to GitHub and let Railway handle the rest!

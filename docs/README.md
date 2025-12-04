# Asetik v2 - Asset Management System

A comprehensive asset management system built with PHP and PostgreSQL (Supabase), designed for managing peripheral devices, repairs, and inventory records.

## 🚀 Features

- **User Management**: Admin and user role-based access control
- **Product Management**: CRUD operations for peripheral devices with image uploads
- **Records Management**: Track device status and history
- **Repair System**: Submit and approve repair requests
- **Image Upload**: Support for product and user profile photos
- **Supabase Integration**: Cloud PostgreSQL database

## 🛠️ Tech Stack

- **Backend**: PHP 7.4+
- **Database**: PostgreSQL (Supabase)
- **Dependencies**: Composer (phpdotenv)
- **Deployment**: Railway.app

## 📋 Prerequisites

- PHP 7.4 or higher
- Composer
- PostgreSQL PDO extension (`pdo_pgsql`)
- Supabase account

## 🔧 Local Development

### 1. Clone the repository

```bash
git clone <your-repo-url>
cd asetik_v2
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment variables

Create a `.env` file in the root directory:

```env
DB_HOST=db.xxxxxxxxxxxxx.supabase.co
DB_NAME=postgres
DB_USER=postgres
DB_PASSWORD=your_database_password
DB_PORT=5432
```

### 4. Set up Supabase database

1. Create a Supabase project at https://supabase.com
2. Go to SQL Editor
3. Run the schema from `database/supabase_schema.sql`

### 5. Run the application

```bash
php -S localhost:8000
```

Visit: `http://localhost:8000`

## 🔐 Default Login Credentials

**Admin Account:**
- Username: `admin`
- Password: `admin`

**Normal User:**
- Username: `Im0somn1s`
- Password: `123`

⚠️ **Change these passwords in production!**

## 🚂 Deploy to Railway

### Quick Deploy

1. **Fork/Push this repo to GitHub**

2. **Sign up at Railway**: https://railway.app

3. **Create New Project**:
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Choose your repository

4. **Add Environment Variables**:
   Go to Variables tab and add:
   ```
   DB_HOST=db.xxxxxxxxxxxxx.supabase.co
   DB_NAME=postgres
   DB_USER=postgres
   DB_PASSWORD=your_database_password
   DB_PORT=5432
   ```

5. **Deploy**: Railway will automatically detect PHP and deploy!

### Manual Configuration

If Railway doesn't auto-detect, add these settings:

**Build Command**: (leave empty, Nixpacks handles it)

**Start Command**:
```bash
php -S 0.0.0.0:$PORT
```

## 📁 Project Structure

```
asetik_v2/
├── .env                      # Environment variables (not in git)
├── dbConnection.php          # Database connection
├── index.php                 # Main dashboard
├── composer.json             # PHP dependencies
├── railway.json              # Railway configuration
├── database/
│   └── supabase_schema.sql   # Database schema
├── crud_products/            # Product management
├── records/                  # Records management
├── new_crud_admin/           # User management
├── login/                    # Authentication
└── upload_image/             # File uploads
    ├── uploads/              # User photos
    └── uploads_products/     # Product photos
```

## 🧪 Testing

Test database connection:
```bash
php test_connection.php
```

## 📝 Documentation

- [HOW_TO_RUN.md](HOW_TO_RUN.md) - Detailed setup guide
- [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md) - MySQL to Supabase migration
- [QUICK_START.md](QUICK_START.md) - Quick reference

## 🔒 Security Notes

1. Never commit `.env` file to version control
2. Change default passwords before production deployment
3. Use HTTPS in production
4. Regularly update dependencies
5. Enable Supabase Row Level Security (RLS) for production

## 🆘 Troubleshooting

### Connection Issues

```bash
# Check PHP version
php -v

# Check PDO PostgreSQL extension
php -m | grep pdo_pgsql

# Test database connection
php test_connection.php
```

### Common Issues

1. **"could not find driver"**: Enable `pdo_pgsql` extension in `php.ini`
2. **Connection timeout**: Check Supabase project is active
3. **Upload errors**: Ensure `upload_image/` directories are writable

## 📄 License

This project is for educational purposes.

## 👥 Contributors

- Your Name

## 🙏 Acknowledgments

- Supabase for database hosting
- Railway for deployment platform

---

**Last Updated**: December 3, 2025

# Supabase Migration Guide for Asetik v2

## Migration Status: ✅ COMPLETE

Your application has been successfully migrated from MySQL to Supabase (PostgreSQL). All database queries have been converted to use PDO with prepared statements.

## What Was Fixed

### 1. Database Connection
- ✅ Created `dbConnection.php` with PDO connection to Supabase
- ✅ Uses environment variables from `.env` file
- ✅ Configured for PostgreSQL with SSL mode

### 2. Database Schema
- ✅ Created `database/supabase_schema.sql` with PostgreSQL-compatible schema
- ✅ Converted MySQL data types to PostgreSQL equivalents
- ✅ Created ENUM types for `user_level` and `record_status`
- ✅ Migrated all sample data
- ✅ Set up proper foreign key constraints

### 3. Code Migration
All PHP files have been updated to use PDO instead of mysqli:

#### Products CRUD (`crud_products/`)
- ✅ `index.php` - List products
- ✅ `add_product.php` - Add new product (fixed upload path)
- ✅ `update_product.php` - Update product (fixed upload path)
- ✅ `delete_product.php` - Delete product

#### Records CRUD (`records/`)
- ✅ `index.php` - List records
- ✅ `create.php` - Create new record
- ✅ `edit.php` - Edit record (fixed status enum)
- ✅ `delete.php` - Delete record

### 4. Bug Fixes Applied
1. **File Upload Paths**: Fixed product image upload paths in `add_product.php` and `update_product.php`
   - Changed from: `upload_image/uploads/`
   - Changed to: `../upload_image/uploads_products/`

2. **Status Enum**: Fixed status value in `records/edit.php`
   - Changed from: `Decline` (uppercase D)
   - Changed to: `decline` (lowercase) to match PostgreSQL ENUM

## Setup Instructions

### 1. Supabase Configuration

1. **Create a Supabase Project** at https://supabase.com
2. **Run the Schema**:
   - Go to Supabase Dashboard → SQL Editor
   - Copy and paste the contents of `database/supabase_schema.sql`
   - Execute the SQL to create tables and insert data

3. **Get Database Credentials**:
   - Go to Project Settings → Database
   - Copy the connection details

### 2. Environment Variables

Create/update your `.env` file with Supabase credentials:

```env
DB_HOST=db.xxxxxxxxxxxxx.supabase.co
DB_NAME=postgres
DB_USER=postgres
DB_PASS=your_database_password
DB_PORT=5432
```

### 3. Install Dependencies

Make sure you have the required PHP dependencies:

```bash
composer install
```

This will install:
- `vlucas/phpdotenv` - For loading environment variables

### 4. Test the Connection

Run the test connection script:

```bash
php test_connection.php
```

Or access via browser:
```
http://localhost/your-project/test.php
```

You should see:
- ✓ Connection successful!
- PostgreSQL version information

## Database Schema Overview

### Tables

1. **users**
   - Stores user information
   - Fields: id, name, age, divisi, email, description, photo, password_user, username, badge, level
   - Level ENUM: 'admin', 'normal_user'

2. **products**
   - Stores peripheral/product information
   - Fields: id, name, photo, description

3. **records**
   - Stores repair/maintenance records
   - Fields: id_records, id_users, id_products, status, no_serial, no_inventaris, note_record, record_time
   - Status ENUM: 'good', 'broken', 'not taken', 'pending', 'fixing', 'decline'
   - Foreign keys to users and products

4. **repair**
   - Stores repair request details
   - Fields: id_repair, id_user, id_record, note, created_at
   - Foreign keys to users and records

### Key Differences from MySQL

1. **Auto-increment**: Uses `SERIAL` instead of `AUTO_INCREMENT`
2. **ENUM Types**: Created as separate types, not inline
3. **Timestamps**: Uses `TIMESTAMP` with `CURRENT_TIMESTAMP`
4. **String Types**: Uses `VARCHAR` instead of `VARCHAR` (same syntax)
5. **Text Types**: Uses `TEXT` (same as MySQL)

## Testing Checklist

Test each functionality to ensure everything works:

- [ ] Login/Logout
- [ ] User Management (Admin)
  - [ ] View users
  - [ ] Add user
  - [ ] Edit user
  - [ ] Delete user
- [ ] Product Management (Admin)
  - [ ] View products
  - [ ] Add product (test image upload)
  - [ ] Edit product (test image upload)
  - [ ] Delete product
- [ ] Records Management (Admin)
  - [ ] View records
  - [ ] Create record
  - [ ] Edit record
  - [ ] Delete record
- [ ] Repair Requests
  - [ ] Apply for repair (Normal User)
  - [ ] Approve repair (Admin)

## Common Issues & Solutions

### Issue 1: Connection Failed
**Error**: "Connection failed: could not find driver"
**Solution**: Enable PostgreSQL PDO extension in php.ini:
```ini
extension=pdo_pgsql
```

### Issue 2: SSL Connection Error
**Error**: "SSL connection error"
**Solution**: Ensure `sslmode=require` is in the DSN string (already configured)

### Issue 3: ENUM Value Error
**Error**: "invalid input value for enum"
**Solution**: Ensure all status values match the ENUM definition exactly (case-sensitive)

### Issue 4: Image Upload Not Working
**Error**: Images not appearing
**Solution**: 
- Check that `upload_image/uploads_products/` directory exists
- Verify directory permissions (should be writable)
- Check that the path is correct relative to the PHP file

## File Structure

```
asetik_v2/
├── .env                          # Environment variables (Supabase credentials)
├── dbConnection.php              # PDO database connection
├── test_connection.php           # Connection test script
├── test.php                      # Browser-based connection test
├── database/
│   ├── supabase_schema.sql       # PostgreSQL schema + data
│   └── asetik (9).sql            # Original MySQL schema (backup)
├── crud_products/
│   ├── index.php                 # ✅ Migrated
│   ├── add_product.php           # ✅ Migrated + Fixed
│   ├── update_product.php        # ✅ Migrated + Fixed
│   └── delete_product.php        # ✅ Migrated
├── records/
│   ├── index.php                 # ✅ Migrated
│   ├── create.php                # ✅ Migrated
│   ├── edit.php                  # ✅ Migrated + Fixed
│   └── delete.php                # ✅ Migrated
└── upload_image/
    ├── uploads/                  # User photos
    └── uploads_products/         # Product photos
```

## Next Steps

1. **Deploy to Production**:
   - Update `.env` with production Supabase credentials
   - Ensure all file upload directories exist and are writable
   - Test all functionality in production environment

2. **Security Enhancements**:
   - Review password hashing (currently using bcrypt - good!)
   - Add CSRF protection to forms
   - Implement rate limiting for login attempts
   - Sanitize all user inputs (already using prepared statements - good!)

3. **Performance Optimization**:
   - Add database indexes for frequently queried columns
   - Implement caching for static data
   - Optimize image uploads (resize, compress)

4. **Backup Strategy**:
   - Set up automated backups in Supabase
   - Export data regularly
   - Keep schema file updated

## Support

If you encounter any issues:
1. Check the error logs in your web server
2. Verify Supabase connection in the dashboard
3. Test with `test_connection.php` or `test.php`
4. Review the PostgreSQL error messages (they're usually very descriptive)

---

**Migration completed on**: December 3, 2025
**Database**: Supabase (PostgreSQL)
**PHP Version Required**: 7.4+
**Extensions Required**: PDO, pdo_pgsql

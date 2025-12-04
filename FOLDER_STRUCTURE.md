# Asetik v2 - New Folder Structure

## Overview
This document describes the reorganized folder structure for the Asetik v2 project.

## Directory Structure

```
asetik_v2/
├── public/                      # Public-facing files (web root)
│   ├── index.php               # Main entry point
│   ├── apply_fix.php           # Repair application page
│   ├── approve.php             # Approval page
│   ├── showdata.php            # Data display page
│   ├── take_back.php           # Return processing page
│   ├── assets/                 # Static assets
│   │   ├── css/               # Stylesheets
│   │   ├── js/                # JavaScript files
│   │   └── images/            # Images and logos
│   └── uploads/               # User-uploaded files
│
├── src/                        # Application source code
│   ├── config/                # Configuration files
│   │   └── dbConnection.php   # Database connection
│   ├── modules/               # Feature modules
│   │   ├── auth/             # Authentication
│   │   │   ├── login.php
│   │   │   └── logout.php
│   │   ├── products/         # Product/Peripheral management
│   │   │   ├── index.php
│   │   │   ├── add_product.php
│   │   │   ├── update_product.php
│   │   │   └── delete_product.php
│   │   ├── users/            # User management
│   │   │   ├── index.php
│   │   │   ├── add_user.php
│   │   │   ├── edit_user.php
│   │   │   └── delete_user.php
│   │   └── records/          # Records management
│   │       ├── index.php
│   │       ├── create.php
│   │       ├── edit.php
│   │       └── delete.php
│   └── includes/             # Shared includes/components
│
├── database/                   # Database files
│   ├── supabase_schema.sql    # Supabase schema
│   └── asetik (9).sql         # Legacy schema
│
├── docs/                       # Documentation
│   ├── README.md
│   ├── QUICK_START.md
│   ├── HOW_TO_RUN.md
│   ├── MIGRATION_GUIDE.md
│   ├── DEPLOY.md
│   ├── RAILWAY_DEPLOYMENT.md
│   └── RAILWAY_FIX.md
│
├── scripts/                    # Utility scripts
│   ├── setup.bat              # Setup script
│   ├── start.bat              # Start script (Windows)
│   ├── start.sh               # Start script (Unix)
│   ├── test_connection.php    # Database connection test
│   └── health.php             # Health check endpoint
│
├── .env                        # Environment variables
├── .gitignore                  # Git ignore rules
├── Procfile                    # Railway deployment
├── railway.json                # Railway config
└── nixpacks.toml              # Nixpacks config

```

## Benefits of New Structure

### 1. **Separation of Concerns**
- **public/**: Only publicly accessible files
- **src/**: Protected application logic
- **docs/**: All documentation in one place
- **scripts/**: Utility and setup scripts

### 2. **Security**
- Database config and business logic in `src/` (not directly accessible)
- Only `public/` folder should be web-accessible
- Uploaded files isolated in `public/uploads/`

### 3. **Maintainability**
- Modular structure makes it easy to find files
- Each module is self-contained
- Clear separation between different features

### 4. **Scalability**
- Easy to add new modules
- Can implement autoloading
- Ready for PSR-4 structure if needed

## Migration Notes

After reorganization, you'll need to update:

1. **Include paths** - Update all `require` and `include` statements
2. **Asset paths** - Update CSS, JS, and image references
3. **Upload paths** - Update file upload destinations
4. **Web server config** - Point document root to `public/` folder

## Web Server Configuration

### PHP Built-in Server (Development)
```bash
php -S localhost:8000 -t public
```

### Apache (.htaccess in public/)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Nginx
```nginx
root /path/to/asetik_v2/public;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Next Steps

1. Run the reorganization script: `.\reorganize.ps1`
2. Update all file paths in your code
3. Test each module thoroughly
4. Update deployment configuration
5. Delete empty old directories

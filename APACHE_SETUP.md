# Apache/XAMPP Configuration Guide

## ✅ Quick Fix Applied

I've added:
1. **Root `index.php`** - Redirects to `public/index.php`
2. **Root `.htaccess`** - Rewrites all requests to `public/` folder
3. **Public `.htaccess`** - Security headers and clean URLs

## Using XAMPP Apache

### Option 1: Access via Redirect (Easiest)
Just visit: **http://localhost/asetik_v2/**

The root `index.php` will automatically redirect you to `public/index.php`

### Option 2: Direct Public Access
Visit: **http://localhost/asetik_v2/public/**

### Option 3: Configure Virtual Host (Recommended for Production)

1. **Edit Apache Config**
   - Open: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
   
2. **Add Virtual Host**
   ```apache
   <VirtualHost *:80>
       ServerName asetik.local
       DocumentRoot "D:/PostgreSQL/asetik_v2/public"
       
       <Directory "D:/PostgreSQL/asetik_v2/public">
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog "logs/asetik-error.log"
       CustomLog "logs/asetik-access.log" common
   </VirtualHost>
   ```

3. **Edit Hosts File**
   - Open: `C:\Windows\System32\drivers\etc\hosts` (as Administrator)
   - Add: `127.0.0.1 asetik.local`

4. **Restart Apache**
   - In XAMPP Control Panel, click "Stop" then "Start" for Apache

5. **Access**
   - Visit: **http://asetik.local/**

## Current URLs

### With XAMPP (No Virtual Host):
- **Home**: `http://localhost/asetik_v2/` or `http://localhost/asetik_v2/public/`
- **Login**: `http://localhost/asetik_v2/public/modules/auth/login.php`
- **Products**: `http://localhost/asetik_v2/public/modules/products/index.php`

### With Virtual Host (asetik.local):
- **Home**: `http://asetik.local/`
- **Login**: `http://asetik.local/modules/auth/login.php`
- **Products**: `http://asetik.local/modules/products/index.php`

### With PHP Built-in Server:
```bash
php -S localhost:8000 -t public
```
- **Home**: `http://localhost:8000/`
- **Login**: `http://localhost:8000/modules/auth/login.php`

## Troubleshooting

### Directory Listing Shows Instead of App
✅ **Fixed!** The `.htaccess` files now prevent directory listing.

### 404 Errors
- Make sure `mod_rewrite` is enabled in Apache
- Check that `AllowOverride All` is set in your Apache config

### Permission Errors
- Ensure Apache has read access to your project folder
- Check file permissions (especially on uploads folder)

## Files Created

1. **`/index.php`** - Root redirect file
2. **`/.htaccess`** - Root Apache config
3. **`/public/.htaccess`** - Public folder Apache config

## Testing

1. Visit: **http://localhost/asetik_v2/**
2. You should be redirected to the login page or dashboard
3. All navigation should work correctly

---

**Recommended**: Use the Virtual Host setup for a cleaner URL structure!

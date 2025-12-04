# 🚂 Complete Railway Deployment Guide - Step by Step

## Prerequisites
- ✅ GitHub account
- ✅ Railway account (sign up at https://railway.app)
- ✅ Your code is pushed to GitHub (already done!)

---

## Part 1: Create MySQL Database on Railway

### Step 1: Login to Railway
1. Go to **https://railway.app**
2. Click **"Login"** or **"Start a New Project"**
3. Sign in with your GitHub account

### Step 2: Create New Project
1. Click **"New Project"** button (top right)
2. You'll see options like:
   - Deploy from GitHub repo
   - Provision PostgreSQL
   - Provision MySQL
   - Empty Project

### Step 3: Provision MySQL
1. Click **"Provision MySQL"**
2. Wait 10-30 seconds for MySQL to be created
3. You'll see a new MySQL service appear in your project

### Step 4: Get MySQL Connection Details
1. Click on the **MySQL service** (the database icon)
2. Go to **"Variables"** tab
3. You'll see these variables (Railway auto-creates them):
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`
   
   **Don't copy these - they're automatically available to your app!**

---

## Part 2: Import Database Schema

### Step 5: Open MySQL Query Interface
1. Still in the MySQL service, click **"Data"** tab
2. Click **"Query"** button
3. You'll see a SQL query editor

### Step 6: Import Schema
1. Open your local file: `d:\PostgreSQL\asetik_v2\database\mysql_schema.sql`
2. **Copy ALL the contents** (Ctrl+A, Ctrl+C)
3. **Paste** into Railway's query editor
4. Click **"Run Query"** or **"Execute"**
5. Wait for success message (should take 2-5 seconds)

### Step 7: Verify Data
1. In the same "Data" tab, click **"Tables"**
2. You should see 4 tables:
   - `products`
   - `users`
   - `records`
   - `repair`
3. Click on `users` table to verify data is there

---

## Part 3: Deploy Your Application

### Step 8: Add GitHub Repository
1. Go back to your project dashboard (click project name at top)
2. Click **"New"** button
3. Select **"GitHub Repo"**
4. You'll see a list of your GitHub repositories

### Step 9: Select Your Repository
1. Find **"asetik_v2"** in the list
2. Click on it
3. Railway will start deploying automatically

### Step 10: Wait for Deployment
1. You'll see deployment logs scrolling
2. Railway will:
   - Detect it's a PHP project
   - Install dependencies
   - Build your app
   - Start the server
3. Wait 1-3 minutes for first deployment

### Step 11: Check Deployment Status
1. Look for **"Success"** or **"Deployed"** status
2. If you see errors, check the logs (scroll down in the deployment view)

---

## Part 4: Configure and Access

### Step 12: Generate Public URL
1. Click on your **app service** (not MySQL)
2. Go to **"Settings"** tab
3. Scroll to **"Networking"** section
4. Click **"Generate Domain"**
5. Railway will create a URL like: `your-app-name.up.railway.app`

### Step 13: Access Your Application
1. Click on the generated URL
2. Your application should load!
3. Try logging in with:
   - **Username**: `admin`
   - **Password**: `admin`

---

## Part 5: Troubleshooting (If Needed)

### If Deployment Fails:

#### Check 1: Build Logs
1. Click on your app service
2. Go to **"Deployments"** tab
3. Click on the failed deployment
4. Read the error logs

#### Check 2: Environment Variables
1. Go to **"Variables"** tab in your app service
2. Railway should automatically link MySQL variables
3. You should see:
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`

If not visible, click **"New Variable"** → **"Add Reference"** → Select MySQL service

#### Check 3: Start Command
1. Go to **"Settings"** tab
2. Scroll to **"Deploy"** section
3. Check **"Custom Start Command"**
4. It should be: `php -S 0.0.0.0:$PORT -t public`
5. If not, add it and redeploy

### If App Loads but Shows Errors:

#### Database Connection Error
1. Check that MySQL service is running (green status)
2. Verify schema was imported correctly
3. Check app logs for specific error

#### 404 Errors
1. Make sure the start command includes `-t public`
2. Redeploy if needed

---

## Part 6: Post-Deployment

### Step 14: Test Your Application
1. **Login**: Try admin/admin
2. **Navigate**: Click through all menu items
3. **CRUD Operations**: Try adding/editing/deleting
4. **File Uploads**: Test image uploads

### Step 15: Monitor Your App
1. Go to **"Metrics"** tab to see:
   - CPU usage
   - Memory usage
   - Request count
2. Go to **"Logs"** tab to see real-time logs

### Step 16: Custom Domain (Optional)
1. Go to **"Settings"** → **"Networking"**
2. Click **"Custom Domain"**
3. Add your domain (requires DNS configuration)

---

## Quick Reference

### Your Services
- **MySQL**: Database service (auto-configured)
- **App**: Your PHP application

### Important URLs
- **Railway Dashboard**: https://railway.app/dashboard
- **Your App**: `https://your-app-name.up.railway.app`

### Default Credentials
- **Admin**: username: `admin`, password: `admin`
- **Test User**: username: `Im0somn1s`, password: `password`

---

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| "Connection failed" | Check MySQL service is running, verify variables |
| "404 Not Found" | Add `-t public` to start command |
| "500 Internal Error" | Check logs for PHP errors |
| "No such file" | Verify file paths are correct |
| Slow loading | Normal for first request, Railway cold starts |

---

## Cost Information

**Railway Free Tier:**
- $5 free credit per month
- Enough for small projects
- Sleeps after inactivity (wakes on request)

**If you need more:**
- Upgrade to Hobby plan ($5/month)
- No sleep, better performance

---

## Need Help?

1. **Check Logs**: Railway Dashboard → Your App → "Logs" tab
2. **Railway Docs**: https://docs.railway.app
3. **Railway Discord**: https://discord.gg/railway

---

## Summary Checklist

- [ ] Created Railway account
- [ ] Created new project
- [ ] Provisioned MySQL database
- [ ] Imported `mysql_schema.sql`
- [ ] Deployed GitHub repository
- [ ] Generated public domain
- [ ] Tested login
- [ ] Verified all features work

**Once all checked, you're done! 🎉**

---

## Next Steps After Deployment

1. **Change Admin Password**: Login and update from default
2. **Add Real Data**: Start using your application
3. **Monitor Usage**: Check Railway dashboard regularly
4. **Backup Data**: Export database periodically
5. **Update Code**: Push to GitHub, Railway auto-deploys

---

**Your app is now live on the internet! 🚀**

Share your Railway URL with anyone to access your application!

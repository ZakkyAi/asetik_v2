# 🚂 Railway Deployment Guide

Complete guide to deploy Asetik v2 to Railway.app

---

## 📋 Prerequisites

- ✅ GitHub account
- ✅ Railway account (sign up at https://railway.app)
- ✅ Supabase project with database set up
- ✅ Your code pushed to GitHub

---

## Step 1: Push to GitHub

### 1.1 Create GitHub Repository

1. Go to https://github.com/new
2. Repository name: `asetik_v2` (or any name you prefer)
3. Description: "Asset Management System with PHP and Supabase"
4. Choose **Public** or **Private**
5. **DO NOT** initialize with README (we already have one)
6. Click **Create repository**

### 1.2 Push Your Code

Copy the commands from GitHub and run in your terminal:

```bash
# Add remote repository
git remote add origin https://github.com/YOUR_USERNAME/asetik_v2.git

# Push to GitHub
git branch -M main
git push -u origin main
```

Replace `YOUR_USERNAME` with your actual GitHub username.

---

## Step 2: Deploy to Railway

### 2.1 Sign Up / Login to Railway

1. Go to https://railway.app
2. Click **Login** or **Start a New Project**
3. Sign in with GitHub (recommended)
4. Authorize Railway to access your repositories

### 2.2 Create New Project

1. Click **New Project**
2. Select **Deploy from GitHub repo**
3. Choose your repository: `asetik_v2`
4. Railway will start deploying automatically

### 2.3 Configure Environment Variables

1. Click on your deployed service
2. Go to **Variables** tab
3. Click **+ New Variable**
4. Add these variables one by one:

```
DB_HOST=db.xxxxxxxxxxxxx.supabase.co
DB_NAME=postgres
DB_USER=postgres
DB_PASSWORD=your_actual_supabase_password
DB_PORT=5432
```

**Important**: Replace with your actual Supabase credentials!

### 2.4 Configure Build Settings (if needed)

Railway should auto-detect PHP, but if it doesn't:

1. Go to **Settings** tab
2. Under **Build**, set:
   - **Builder**: Nixpacks (default)
3. Under **Deploy**, set:
   - **Start Command**: `php -S 0.0.0.0:$PORT`

### 2.5 Redeploy

After adding environment variables:

1. Go to **Deployments** tab
2. Click **Redeploy** on the latest deployment
3. Wait for deployment to complete (~2-3 minutes)

---

## Step 3: Get Your Live URL

1. Go to **Settings** tab
2. Scroll to **Domains** section
3. Click **Generate Domain**
4. Railway will give you a URL like: `asetik-v2-production.up.railway.app`
5. Click on the URL to open your live site!

---

## Step 4: Test Your Deployment

1. **Visit your Railway URL**
2. **Test login**:
   - Username: `admin`
   - Password: `admin`
3. **Test features**:
   - View products
   - Add/edit products
   - Upload images
   - Manage users

---

## 🎯 Post-Deployment Checklist

- [ ] Site is accessible via Railway URL
- [ ] Login page loads correctly
- [ ] Can login as admin
- [ ] Database connection works
- [ ] Can view products/records
- [ ] Image uploads work
- [ ] All CRUD operations work

---

## 🔧 Troubleshooting

### Issue 1: "Application failed to respond"

**Solution**: Check environment variables are set correctly
1. Go to Variables tab
2. Verify all 5 variables are present
3. Check for typos in variable names
4. Redeploy

### Issue 2: "Connection failed"

**Solution**: Verify Supabase credentials
1. Go to Supabase Dashboard
2. Settings → Database
3. Copy exact values
4. Update Railway variables
5. Redeploy

### Issue 3: "500 Internal Server Error"

**Solution**: Check deployment logs
1. Go to Deployments tab
2. Click on latest deployment
3. Check **Build Logs** and **Deploy Logs**
4. Look for error messages

### Issue 4: Images not uploading

**Solution**: Railway's filesystem is ephemeral
- Files uploaded will be lost on redeploy
- **Recommended**: Use Supabase Storage for images
- Alternative: Use Cloudinary or AWS S3

---

## 🔄 Updating Your Deployment

When you make changes to your code:

```bash
# 1. Make your changes
# 2. Commit changes
git add .
git commit -m "Your commit message"

# 3. Push to GitHub
git push origin main

# 4. Railway will automatically redeploy!
```

---

## 💰 Railway Pricing

**Free Tier** (Hobby Plan):
- $5 free credit per month
- Enough for small projects
- No credit card required initially

**Pro Plan** ($20/month):
- $20 credit included
- Pay only for what you use
- Better performance

---

## 🔒 Security Best Practices

1. **Change default passwords** immediately after deployment
2. **Use strong passwords** for database
3. **Enable HTTPS** (Railway provides this automatically)
4. **Don't commit .env** to GitHub (already in .gitignore)
5. **Regularly update** dependencies

---

## 📊 Monitoring

### View Logs

1. Go to your Railway project
2. Click on your service
3. Go to **Deployments** tab
4. Click on active deployment
5. View real-time logs

### Metrics

1. Go to **Metrics** tab
2. View:
   - CPU usage
   - Memory usage
   - Network traffic

---

## 🆘 Need Help?

- **Railway Docs**: https://docs.railway.app
- **Railway Discord**: https://discord.gg/railway
- **Supabase Docs**: https://supabase.com/docs

---

## ✅ Success!

Your application is now live on Railway! 🎉

**Your URL**: `https://your-app.up.railway.app`

Share it with your team and start using your asset management system!

---

**Deployment Date**: December 3, 2025
**Platform**: Railway.app
**Database**: Supabase PostgreSQL

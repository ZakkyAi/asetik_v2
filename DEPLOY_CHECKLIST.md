# 🚀 Railway Deployment - Quick Checklist

## Before You Start
- ✅ Code is on GitHub (DONE!)
- ✅ Railway account created
- ✅ Logged into Railway

---

## Step-by-Step Deployment

### 1️⃣ Create MySQL Database (2 minutes)
```
Railway Dashboard → New Project → Provision MySQL
```
**Wait for green status ✓**

### 2️⃣ Import Database Schema (1 minute)
```
MySQL Service → Data Tab → Query
```
**Copy/paste from:** `database/mysql_schema.sql`
**Click:** Run Query

### 3️⃣ Deploy Your App (3 minutes)
```
Project Dashboard → New → GitHub Repo → Select "asetik_v2"
```
**Wait for deployment to complete ✓**

### 4️⃣ Generate Public URL (30 seconds)
```
App Service → Settings → Networking → Generate Domain
```
**Copy the URL!**

### 5️⃣ Test Your App (1 minute)
```
Visit your Railway URL
Login: admin / admin
```
**Try navigating around ✓**

---

## Total Time: ~7 minutes

---

## Troubleshooting Quick Fixes

### ❌ Deployment Failed?
**Fix:** Settings → Deploy → Start Command:
```
php -S 0.0.0.0:$PORT -t public
```

### ❌ Database Connection Error?
**Fix:** Variables tab → Add Reference → MySQL

### ❌ 404 Not Found?
**Fix:** Check start command has `-t public`

---

## You're Done! 🎉

**Your app is live at:** `https://your-app.up.railway.app`

**Login with:**
- Username: `admin`
- Password: `admin`

---

**See `RAILWAY_DEPLOY_GUIDE.md` for detailed instructions!**

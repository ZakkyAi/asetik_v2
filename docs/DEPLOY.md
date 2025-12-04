# 🚀 Quick Deployment Steps

## 1️⃣ Push to GitHub

```bash
# Create a new repository on GitHub first, then run:
git remote add origin https://github.com/YOUR_USERNAME/asetik_v2.git
git branch -M main
git push -u origin main
```

## 2️⃣ Deploy to Railway

1. Go to https://railway.app
2. Login with GitHub
3. Click **New Project** → **Deploy from GitHub repo**
4. Select your `asetik_v2` repository
5. Add environment variables:
   ```
   DB_HOST=db.jadwfkeagcceroypuqer.supabase.co
   DB_NAME=postgres
   DB_USER=postgres
   DB_PASSWORD=your_password_here
   DB_PORT=5432
   ```
6. Click **Generate Domain** in Settings
7. Done! 🎉

## 3️⃣ Access Your App

Visit your Railway URL and login:
- Username: `admin`
- Password: `admin`

---

**Full Guide**: See [RAILWAY_DEPLOYMENT.md](RAILWAY_DEPLOYMENT.md)

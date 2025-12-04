# Railway Database Import Script

## Step 1: Install Railway CLI
npm i -g @railway/cli

## Step 2: Login
railway login

## Step 3: Link Project
railway link
# Select your project when prompted

## Step 4: Import Database
# Make sure you're in the asetik_v2 directory
cd d:\PostgreSQL\asetik_v2

# Import the schema
railway run --service MySQL mysql -u root -p$(railway variables get MYSQLPASSWORD) $(railway variables get MYSQLDATABASE) < database/mysql_schema.sql

# Or connect interactively
railway connect MySQL
# Then paste the SQL from mysql_schema.sql

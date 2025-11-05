# 🚀 CV Analyzer Deployment Guide

## Prerequisites
- PostgreSQL installed with pgAdmin
- Ngrok account (free tier)
- Vercel account (free tier)

---

## PART 1: Setup PostgreSQL Database Server

### 1. Configure PostgreSQL for Remote Access

**A. Edit `postgresql.conf`**
   - Location: `C:\Program Files\PostgreSQL\16\data\postgresql.conf` (adjust version)
   - Find and change:
     ```
     listen_addresses = '*'
     ```

**B. Edit `pg_hba.conf`**
   - Location: `C:\Program Files\PostgreSQL\16\data\pg_hba.conf`
   - Add at the bottom:
     ```
     host    all             all             0.0.0.0/0               md5
     ```

**C. Restart PostgreSQL Service**
   - Open Services (Win+R → `services.msc`)
   - Find "postgresql-x64-16" (or your version)
   - Right-click → Restart

### 2. Install & Setup Ngrok

**A. Download Ngrok**
   - Go to: https://ngrok.com/download
   - Download for Windows
   - Extract to `C:\ngrok`

**B. Sign Up & Get Auth Token**
   - Sign up at: https://dashboard.ngrok.com/signup
   - Copy your authtoken from: https://dashboard.ngrok.com/get-started/your-authtoken

**C. Configure Ngrok**
   ```powershell
   cd C:\ngrok
   .\ngrok config add-authtoken YOUR_AUTH_TOKEN_HERE
   ```

**D. Start Ngrok Tunnel**
   ```powershell
   .\ngrok tcp 5432
   ```
   
   **You'll see output like:**
   ```
   Forwarding   tcp://0.tcp.ngrok.io:12345 -> localhost:5432
   ```
   
   **SAVE THIS INFO:**
   - Host: `0.tcp.ngrok.io`
   - Port: `12345`

**⚠️ IMPORTANT:** Keep this terminal running! If you close it, the tunnel stops.

---

## PART 2: Deploy Laravel to Vercel

### 1. Prepare Project for Vercel

**A. Update `.env` for Production**

Create `.env.production` with ngrok database credentials:

```env
APP_NAME="CV Analyzer"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app.vercel.app

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=0.tcp.ngrok.io
DB_PORT=12345
DB_DATABASE=cv_analyzer
DB_USERNAME=postgres
DB_PASSWORD=z

GEMINI_API_KEY=AIzaSyCwLD0FyIt_1C3OccYZd_Cv7znOD1tDVPs
GEMINI_MODEL=gemini-2.0-flash-exp

SESSION_DRIVER=cookie
SESSION_LIFETIME=120

CACHE_DRIVER=array
QUEUE_CONNECTION=sync
```

**B. Check `vercel.json` exists** (already in project)

### 2. Deploy to Vercel

**A. Install Vercel CLI**
```powershell
npm install -g vercel
```

**B. Login to Vercel**
```powershell
vercel login
```

**C. Deploy**
```powershell
# First deployment
vercel

# Follow prompts:
# - Set up and deploy? Yes
# - Which scope? Your account
# - Link to existing project? No
# - Project name? re-cv-analyzer (or your choice)
# - Directory? ./
# - Override settings? No

# Deploy to production
vercel --prod
```

**D. Set Environment Variables in Vercel Dashboard**

1. Go to: https://vercel.com/dashboard
2. Select your project
3. Go to Settings → Environment Variables
4. Add these variables:
   - `APP_KEY` → Your base64 app key
   - `DB_HOST` → Your ngrok host (e.g., 0.tcp.ngrok.io)
   - `DB_PORT` → Your ngrok port (e.g., 12345)
   - `DB_DATABASE` → cv_analyzer
   - `DB_USERNAME` → postgres
   - `DB_PASSWORD` → z
   - `GEMINI_API_KEY` → AIzaSyCwLD0FyIt_1C3OccYZd_Cv7znOD1tDVPs

5. Redeploy after adding env vars

---

## PART 3: Alternative - Deploy to Railway (Easier!)

Railway supports Laravel better than Vercel and is also free tier.

### 1. Install Railway CLI
```powershell
npm install -g @railway/cli
```

### 2. Login & Deploy
```powershell
railway login
railway init
railway up
```

### 3. Add Environment Variables
```powershell
railway variables set DB_HOST=0.tcp.ngrok.io
railway variables set DB_PORT=12345
railway variables set DB_DATABASE=cv_analyzer
railway variables set DB_USERNAME=postgres
railway variables set DB_PASSWORD=z
railway variables set GEMINI_API_KEY=AIzaSyCwLD0FyIt_1C3OccYZd_Cv7znOD1tDVPs
railway variables set APP_KEY=YOUR_APP_KEY
```

### 4. Generate Domain
- Go to Railway dashboard
- Click your project
- Settings → Generate Domain

---

## PART 4: Alternative Platforms (FREE TIER)

### Option 1: **Heroku** (Free Tier - Best for Laravel)
- Supports PHP natively
- Easy PostgreSQL connection
- Free dynos available
- Guide: https://devcenter.heroku.com/articles/getting-started-with-laravel

### Option 2: **Fly.io** (Free Tier)
- Great for full-stack apps
- Supports PostgreSQL connections
- Free tier: 3 VMs
- Guide: https://fly.io/docs/laravel/

### Option 3: **Cloudflare Pages + Workers** (Free)
- For frontend only
- Use Workers for API
- Connect to external DB

---

## 🔥 RECOMMENDED: Best Free Setup

**For Your Use Case (PostgreSQL from your PC):**

### **Setup A: Railway + Ngrok** ⭐ EASIEST
1. Keep PostgreSQL on your PC
2. Expose with Ngrok (free tier)
3. Deploy app to Railway (free tier)
4. Railway connects to your DB via Ngrok

**Pros:**
- ✅ Free completely
- ✅ No database hosting needed
- ✅ Easy deployment
- ✅ pgAdmin access maintained

**Cons:**
- ⚠️ Ngrok tunnel must stay running
- ⚠️ If PC off, database offline

### **Setup B: Fly.io + Ngrok** ⭐ GOOD ALTERNATIVE
Same as Railway but on Fly.io

### **Setup C: Free PostgreSQL Hosting** (Recommended Long-term)

Instead of using local PostgreSQL, use free PostgreSQL hosting:

1. **Neon.tech** (Free Tier)
   - Free PostgreSQL database
   - 0.5GB storage
   - No credit card required
   - Always online
   - https://neon.tech

2. **Supabase** (Free Tier)
   - Free PostgreSQL database
   - 500MB storage
   - No credit card required
   - https://supabase.com

3. **ElephantSQL** (Free Tier - Tiny Turtle)
   - 20MB storage
   - Limited connections
   - https://www.elephantsql.com

**With free DB hosting + Railway/Fly.io = Fully Free & Always Online!**

---

## 🎯 QUICK START (Recommended for You)

### **I recommend: Railway + Neon (Both Free)**

**Why?**
- ✅ No need to run ngrok 24/7
- ✅ No need PC always on
- ✅ Professional setup
- ✅ 100% free
- ✅ Can use pgAdmin to connect to Neon

### **Steps:**

1. **Setup Database (5 minutes)**
   ```
   1. Go to https://neon.tech
   2. Sign up (free)
   3. Create new project: "cv-analyzer"
   4. Copy connection string
   ```

2. **Deploy App (5 minutes)**
   ```powershell
   npm install -g @railway/cli
   railway login
   railway init
   railway up
   ```

3. **Configure (2 minutes)**
   - Add Neon database URL to Railway env vars
   - Add Gemini API key
   - Done!

---

## 📱 Maintenance

### Keep Ngrok Running (if using local DB)
- Ngrok free tier resets URL every 2 hours when tunnel restarts
- Upgrade to paid ($8/month) for permanent URL
- OR use Neon/Supabase for permanent solution

### Monitor Usage
- Railway free tier: 500 hours/month
- Vercel free tier: 100GB bandwidth
- Neon free tier: 0.5GB storage

---

## 🆘 Troubleshooting

### Database Connection Failed
```powershell
# Test connection locally first
php artisan tinker
DB::connection()->getPdo();
```

### Ngrok Tunnel Died
- Restart ngrok
- Update env vars with new URL
- Redeploy app

### File Upload Not Working on Vercel
- Vercel doesn't support file storage
- Use Cloudinary or AWS S3 for file storage
- Update `.env` with cloud storage credentials

---

## 📞 Need Help?

1. Check Railway logs: `railway logs`
2. Check Vercel logs: `vercel logs`
3. Test locally first: `php artisan serve`

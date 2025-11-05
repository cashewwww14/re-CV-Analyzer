# 🚀 QUICK DEPLOY: Railway + Neon (100% FREE)

## ⏱️ Total Time: ~15 minutes

---

## STEP 1: Setup Free PostgreSQL Database (5 min)

### A. Create Neon Account
1. Go to: https://neon.tech
2. Click "Sign Up" (free, no credit card)
3. Sign up with GitHub/Google/Email

### B. Create Database
1. Click "Create a project"
2. Project name: `cv-analyzer`
3. Region: **Singapore** (closest to Indonesia)
4. Click "Create project"

### C. Get Connection Details
You'll see connection details like:
```
Host: ep-xyz-123.ap-southeast-1.aws.neon.tech
Database: neondb
User: your_username
Password: your_password
Port: 5432
```

**Connection String:**
```
postgresql://your_username:your_password@ep-xyz-123.ap-southeast-1.aws.neon.tech/neondb?sslmode=require
```

### D. Connect with pgAdmin (Optional)
1. Open pgAdmin
2. Right-click "Servers" → Create → Server
3. General Tab:
   - Name: `Neon CV Analyzer`
4. Connection Tab:
   - Host: `ep-xyz-123.ap-southeast-1.aws.neon.tech` (from Neon)
   - Port: `5432`
   - Database: `neondb`
   - Username: (from Neon)
   - Password: (from Neon)
5. SSL Tab:
   - SSL Mode: `Require`
6. Save

### E. Migrate Database
Update `.env` with Neon credentials:
```env
DB_CONNECTION=pgsql
DB_HOST=ep-xyz-123.ap-southeast-1.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=your_neon_username
DB_PASSWORD=your_neon_password
```

Run migrations:
```powershell
php artisan migrate:fresh --seed
```

---

## STEP 2: Deploy to Railway (10 min)

### A. Install Railway CLI
```powershell
npm install -g @railway/cli
```

### B. Login to Railway
```powershell
railway login
```
- Browser will open
- Sign up with GitHub (recommended)

### C. Initialize Project
```powershell
# In your project directory
railway init

# Answer prompts:
# "Starting Point": Empty Project
# "Project name": re-cv-analyzer
```

### D. Link to Railway
```powershell
railway link
```

### E. Add Environment Variables
```powershell
# App settings
railway variables set APP_NAME="CV Analyzer"
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false
railway variables set APP_KEY=base64:YOUR_APP_KEY_FROM_ENV_FILE

# Database (from Neon)
railway variables set DB_CONNECTION=pgsql
railway variables set DB_HOST=ep-xyz-123.ap-southeast-1.aws.neon.tech
railway variables set DB_PORT=5432
railway variables set DB_DATABASE=neondb
railway variables set DB_USERNAME=your_neon_username
railway variables set DB_PASSWORD=your_neon_password

# Gemini AI
railway variables set GEMINI_API_KEY=AIzaSyCwLD0FyIt_1C3OccYZd_Cv7znOD1tDVPs
railway variables set GEMINI_MODEL=gemini-2.0-flash-exp

# Session & Cache
railway variables set SESSION_DRIVER=cookie
railway variables set CACHE_DRIVER=array
railway variables set QUEUE_CONNECTION=sync
```

### F. Deploy!
```powershell
railway up
```

Wait for deployment to complete...

### G. Generate Public URL
```powershell
railway domain
```

OR go to Railway dashboard:
1. https://railway.app/dashboard
2. Click your project
3. Click "Settings"
4. Click "Generate Domain"

Your app will be at: `https://re-cv-analyzer.up.railway.app` (or similar)

---

## STEP 3: Post-Deploy Setup

### A. Run Migrations (if needed)
```powershell
# SSH into Railway
railway run php artisan migrate:fresh --seed
```

### B. Generate App Key (if needed)
```powershell
railway run php artisan key:generate
```

### C. Test Your App
Visit your Railway URL and test:
- ✅ Login works
- ✅ Upload CV works
- ✅ Analysis works
- ✅ Admin dashboard works

---

## 🎉 DONE!

Your app is now LIVE and accessible by anyone!

**Free Tier Limits:**
- **Neon:** 0.5GB storage, 3GB data transfer
- **Railway:** 500 hours/month, $5 free credit

**Your Setup:**
- ✅ PostgreSQL database (always online)
- ✅ Web app (always online)
- ✅ No need to keep PC running
- ✅ Can manage DB with pgAdmin
- ✅ 100% FREE

---

## 🔧 Common Commands

### View Logs
```powershell
railway logs
```

### SSH into Container
```powershell
railway run bash
```

### Update Environment Variable
```powershell
railway variables set VARIABLE_NAME=value
```

### Redeploy
```powershell
railway up
```

### Check Status
```powershell
railway status
```

---

## 📊 Monitor Your App

**Railway Dashboard:**
- https://railway.app/dashboard
- View deployments
- Check logs
- Monitor usage

**Neon Dashboard:**
- https://console.neon.tech
- View database metrics
- Run SQL queries
- Monitor storage

---

## 🚨 Troubleshooting

### App shows 500 error
```powershell
# Check logs
railway logs

# Common fixes:
railway variables set APP_KEY=base64:YOUR_KEY
railway run php artisan config:clear
railway up
```

### Database connection failed
- Check Neon credentials in Railway env vars
- Ensure SSL mode is correct
- Test connection from local first

### File upload not working
Vercel/Railway filesystem is read-only for /storage.

**Solution: Use Cloudinary (free tier)**
1. Sign up: https://cloudinary.com
2. Get API credentials
3. Install package:
   ```powershell
   composer require cloudinary-labs/cloudinary-laravel
   ```
4. Update `.env`:
   ```env
   CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name
   ```

---

## 💡 Tips

1. **Custom Domain (Free)**
   - Railway allows custom domains
   - Add your domain in Settings

2. **Auto Deploy from GitHub**
   - Connect Railway to GitHub repo
   - Auto-deploy on push to main branch

3. **Backup Database**
   - Neon has automatic backups
   - Download via pgAdmin if needed

4. **Scale Up (if needed)**
   - Railway: Upgrade plan for more hours
   - Neon: Upgrade for more storage

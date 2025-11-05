# 🏠 Deploy with LOCAL PostgreSQL (Your PC as Database Server)

## ⚠️ Requirements
- Your PC must stay ON 24/7
- Stable internet connection
- PostgreSQL + pgAdmin already installed

---

## STEP 1: Configure PostgreSQL for Remote Access

### A. Find PostgreSQL Installation Path
Common locations:
- `C:\Program Files\PostgreSQL\16\data\`
- `C:\Program Files\PostgreSQL\15\data\`

Check in Services (Win+R → `services.msc`) for exact version.

### B. Edit postgresql.conf
1. Open: `C:\Program Files\PostgreSQL\16\data\postgresql.conf`
2. Find line: `#listen_addresses = 'localhost'`
3. Change to: `listen_addresses = '*'`
4. Save file

### C. Edit pg_hba.conf
1. Open: `C:\Program Files\PostgreSQL\16\data\pg_hba.conf`
2. Add this line at the END:
   ```
   host    all             all             0.0.0.0/0               md5
   ```
3. Save file

### D. Restart PostgreSQL Service
1. Open Services (Win+R → `services.msc`)
2. Find `postgresql-x64-16` (or your version)
3. Right-click → **Restart**

---

## STEP 2: Install & Setup Ngrok

### A. Download Ngrok
1. Go to: https://ngrok.com/download
2. Download Windows version
3. Extract to `C:\ngrok\`

### B. Sign Up & Get Auth Token
1. Sign up: https://dashboard.ngrok.com/signup
2. Verify email
3. Go to: https://dashboard.ngrok.com/get-started/your-authtoken
4. Copy your authtoken

### C. Setup Auth Token
```powershell
cd C:\ngrok
.\ngrok config add-authtoken YOUR_AUTH_TOKEN_HERE
```

Example:
```powershell
.\ngrok config add-authtoken 2abcdefghijklmnopqrstuvwxyz1234567890
```

---

## STEP 3: Start Ngrok Tunnel

### A. Open PowerShell in Ngrok Directory
```powershell
cd C:\ngrok
```

### B. Start TCP Tunnel for PostgreSQL
```powershell
.\ngrok tcp 5432
```

### C. Copy Connection Info
You'll see output like:
```
Session Status                online
Account                       Your Name (Plan: Free)
Version                       3.x.x
Region                        Asia Pacific (ap)
Latency                       -
Web Interface                 http://127.0.0.1:4040
Forwarding                    tcp://0.tcp.ap.ngrok.io:12345 -> localhost:5432
```

**SAVE THESE:**
- Host: `0.tcp.ap.ngrok.io`
- Port: `12345`

⚠️ **IMPORTANT:** Keep this PowerShell window open! If you close it, tunnel stops.

---

## STEP 4: Update .env for Production

Copy Ngrok connection info to `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=0.tcp.ap.ngrok.io
DB_PORT=12345
DB_DATABASE=cv_analyzer
DB_USERNAME=postgres
DB_PASSWORD=z
```

---

## STEP 5: Test Connection Locally

```powershell
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connected successfully!';"
```

If successful, you'll see: `Connected successfully!`

---

## STEP 6: Deploy to Railway

### A. Install Railway CLI
```powershell
npm install -g @railway/cli
```

### B. Login & Initialize
```powershell
railway login
railway init
```

### C. Set Environment Variables
```powershell
# Copy from your .env
railway variables set APP_KEY=base64:YOUR_APP_KEY

# Ngrok database connection
railway variables set DB_HOST=0.tcp.ap.ngrok.io
railway variables set DB_PORT=12345
railway variables set DB_DATABASE=cv_analyzer
railway variables set DB_USERNAME=postgres
railway variables set DB_PASSWORD=z

# Gemini AI
railway variables set GEMINI_API_KEY=AIzaSyCwLD0FyIt_1C3OccYZd_Cv7znOD1tDVPs

# Other settings
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false
railway variables set SESSION_DRIVER=cookie
railway variables set CACHE_DRIVER=array
```

### D. Deploy
```powershell
railway up
```

### E. Generate Domain
```powershell
railway domain
```

---

## 🔄 Daily Operations

### Starting Your Setup (Every Day)
1. **Turn on your PC**
2. **Start PostgreSQL** (usually auto-starts)
3. **Start Ngrok:**
   ```powershell
   cd C:\ngrok
   .\ngrok tcp 5432
   ```
4. **If Ngrok URL changed:**
   - Update Railway env vars:
     ```powershell
     railway variables set DB_HOST=new-ngrok-host.ngrok.io
     railway variables set DB_PORT=new-port
     ```
   - Redeploy:
     ```powershell
     railway up
     ```

### Keeping Ngrok Running
- Don't close the PowerShell window
- OR run ngrok as background service (advanced)

---

## ⚡ Upgrade Ngrok (Optional)

### Free Tier Limitations:
- ❌ URL changes every restart
- ❌ 1 tunnel at a time
- ❌ Random subdomain

### Paid Tier ($8/month):
- ✅ **Static domain** (no URL changes!)
- ✅ Multiple tunnels
- ✅ Custom subdomain
- ✅ Better performance

**To upgrade:**
1. Go to: https://dashboard.ngrok.com/billing/plan
2. Choose "Personal" plan
3. Reserve a domain: `your-name-db.ngrok.io`
4. Start tunnel with reserved domain:
   ```powershell
   .\ngrok tcp --region=ap --domain=your-name-db.ngrok.io 5432
   ```

Now your DB URL never changes! 🎉

---

## 🛠️ Run Ngrok as Windows Service (Advanced)

To auto-start Ngrok on boot:

### Using NSSM (Non-Sucking Service Manager)

1. **Download NSSM:**
   - https://nssm.cc/download
   - Extract to `C:\nssm\`

2. **Install Service:**
   ```powershell
   cd C:\nssm\win64
   .\nssm install NgrokPostgres "C:\ngrok\ngrok.exe" "tcp 5432"
   ```

3. **Start Service:**
   ```powershell
   .\nssm start NgrokPostgres
   ```

4. **Set to Auto-Start:**
   - Open Services (Win+R → `services.msc`)
   - Find "NgrokPostgres"
   - Right-click → Properties
   - Startup type: **Automatic**
   - Apply

Now Ngrok starts automatically when PC boots! 💪

---

## 📊 Monitor Ngrok

### View Active Connections
Open browser: http://localhost:4040

You can see:
- Active connections
- Request/response logs
- Connection stats

---

## 🚨 Troubleshooting

### Ngrok tunnel disconnected
- Check internet connection
- Restart ngrok
- Update Railway DB_HOST/DB_PORT if URL changed

### PostgreSQL connection refused
- Check PostgreSQL service is running
- Check firewall allows port 5432
- Verify postgresql.conf and pg_hba.conf edits

### Railway can't connect to database
- Verify ngrok is running
- Test connection locally first
- Check Railway env vars are correct

---

## ⚖️ Pros & Cons

### ✅ Pros:
- Free (no DB hosting cost)
- Use existing pgAdmin setup
- Full control over database
- No storage limits

### ❌ Cons:
- PC must stay on 24/7
- If PC off, website can't access data
- Ngrok free tier changes URL
- Higher latency (users → Railway → Ngrok → Your PC)

---

## 💡 Recommendation

For production/public use, I recommend:
1. **Neon.tech** (free PostgreSQL hosting) + **Railway** (free app hosting)
   - Always online
   - No PC needed
   - Better performance
   - See `QUICK_DEPLOY.md`

For development/testing:
- Local PostgreSQL + Ngrok is perfect! ✅

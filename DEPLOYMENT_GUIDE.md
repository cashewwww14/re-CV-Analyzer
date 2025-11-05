# 🚀 Deployment Guide - CV Analyzer

## Stack yang Digunakan (100% FREE):
- **Hosting:** Vercel (Laravel + Frontend)
- **Database:** Supabase PostgreSQL (500MB free)
- **AI Service:** Google Gemini API (already configured)

---

## 📦 STEP 1: Setup Database PostgreSQL di Supabase

### 1.1 Buat Akun Supabase
1. Buka: https://supabase.com
2. Klik **"Start your project"**
3. Sign up dengan GitHub (gratis)

### 1.2 Buat Project Database
1. Klik **"New Project"**
2. Isi form:
   - **Name:** `cv-analyzer-db` (bebas)
   - **Database Password:** Buat password kuat (SIMPAN INI!)
   - **Region:** Southeast Asia (Singapore) - paling dekat ke Indonesia
3. Klik **"Create new project"** (tunggu 2 menit setup)

### 1.3 Get Database Credentials
1. Di dashboard Supabase, klik **"Settings"** (⚙️) di sidebar
2. Klik **"Database"**
3. Scroll ke bagian **"Connection string"**
4. Pilih tab **"URI"**
5. Copy connection string yang bentuknya:
   ```
   postgresql://postgres:[YOUR-PASSWORD]@db.xxxxx.supabase.co:5432/postgres
   ```
6. SIMPAN connection string ini!

---

## 🔧 STEP 2: Update Environment Variables

### 2.1 Update `.env` untuk Production
Buat file `.env.production` atau update `.env`:

```env
APP_NAME="CV Analyzer"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-project.vercel.app

# Database Supabase
DB_CONNECTION=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your_supabase_password

# Gemini AI
GEMINI_API_KEY=AIzaSyCwLD0FyIt_1C3OccYZd_Cv7znOD1tDVPs

# Session & Cache
SESSION_DRIVER=cookie
CACHE_DRIVER=array

# Timezone
APP_TIMEZONE=Asia/Jakarta
```

---

## 🌐 STEP 3: Deploy ke Vercel

### 3.1 Install Vercel CLI (jika belum)
```bash
npm install -g vercel
```

### 3.2 Login ke Vercel
```bash
vercel login
```
Pilih GitHub untuk login.

### 3.3 Deploy Project
```bash
# Di folder project (re-CV-Analyzer)
vercel
```

Jawab pertanyaan:
- **Set up and deploy?** → Y
- **Which scope?** → Your account
- **Link to existing project?** → N
- **Project name?** → cv-analyzer (atau bebas)
- **Directory?** → ./
- **Override settings?** → N

### 3.4 Set Environment Variables di Vercel Dashboard
1. Setelah deploy, buka: https://vercel.com/dashboard
2. Pilih project **cv-analyzer**
3. Klik **"Settings"** → **"Environment Variables"**
4. Tambahkan satu per satu:

```
APP_NAME=CV Analyzer
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-project.vercel.app

DB_CONNECTION=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your_supabase_password

GEMINI_API_KEY=AIzaSyCwLD0FyIt_1C3OccYZd_Cv7znOD1tDVPs

SESSION_DRIVER=cookie
CACHE_DRIVER=array
APP_TIMEZONE=Asia/Jakarta
```

5. Klik **"Save"** untuk setiap variable

---

## 🗄️ STEP 4: Run Database Migrations di Supabase

### Option A: Via Supabase SQL Editor (RECOMMENDED)
1. Buka Supabase Dashboard → **"SQL Editor"**
2. Klik **"New query"**
3. Copy semua migration files dari folder `database/migrations/`
4. Paste dan jalankan satu per satu (urut dari tanggal lama ke baru)

### Option B: Via Terminal (Butuh Akses ke Production)
```bash
# Set production database URL
export DATABASE_URL="postgresql://postgres:[PASSWORD]@db.xxxxx.supabase.co:5432/postgres"

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force
```

---

## 🌱 STEP 5: Seed Data Awal

### 5.1 Via Supabase SQL Editor
Jalankan query ini untuk insert admin user dan job descriptions:

```sql
-- Insert Admin User
INSERT INTO users (name, email, email_verified_at, password, role, created_at, updated_at)
VALUES (
    'Admin',
    'admin@cvanalyzer.com',
    NOW(),
    '$2y$12$LLkQvPHjW4dZkqXYpqQVyO3y9zH4Ek.Z7Y3qXf6aZ8kQvL1dZ2YZy', -- password: admin123
    'admin',
    NOW(),
    NOW()
);

-- Insert Departments
INSERT INTO departments (name, description, created_at, updated_at) VALUES
('Technology', 'IT and Software Development', NOW(), NOW()),
('Marketing', 'Marketing and Communications', NOW(), NOW()),
('Human Resources', 'HR Management', NOW(), NOW());

-- Insert Sample Job Descriptions (copy from JobDescriptionSeeder.php)
```

---

## ✅ STEP 6: Redeploy Vercel

```bash
vercel --prod
```

Atau push ke GitHub (jika sudah connect auto-deploy):
```bash
git add .
git commit -m "Add production config"
git push origin main
```

---

## 🧪 STEP 7: Test Deployment

1. Buka URL Vercel: `https://your-project.vercel.app`
2. Login dengan:
   - **Email:** admin@cvanalyzer.com
   - **Password:** admin123
3. Test:
   - ✅ Upload CV
   - ✅ Analyze dengan existing job
   - ✅ Add new job
   - ✅ Check admin dashboard

---

## 🔐 STEP 8: Security Checklist

### 8.1 Change Admin Password
Login → Profile → Change Password

### 8.2 Update APP_KEY
```bash
php artisan key:generate --show
```
Copy output dan update di Vercel Environment Variables.

### 8.3 Set APP_DEBUG=false
Di Vercel Environment Variables, pastikan:
```
APP_DEBUG=false
```

---

## 📊 Monitoring & Maintenance

### Database Usage (Supabase)
- Dashboard: https://supabase.com/dashboard
- Check usage: Settings → Billing
- Free tier: 500MB database, unlimited API requests

### Vercel Usage
- Dashboard: https://vercel.com/dashboard
- Free tier: 100GB bandwidth/month, unlimited deploys

### Logs
- Vercel logs: Project → Deployments → View logs
- Supabase logs: Dashboard → Logs

---

## 🆘 Troubleshooting

### Error: "SQLSTATE[08006] Connection refused"
- ✅ Check Supabase database is running
- ✅ Verify DB_HOST, DB_PASSWORD di environment variables
- ✅ Whitelist IP di Supabase (Settings → Database → Connection pooling)

### Error: "419 Page Expired" (CSRF)
- ✅ Set SESSION_DRIVER=cookie di environment variables
- ✅ Pastikan APP_URL sesuai dengan domain Vercel

### Error: "500 Internal Server Error"
- ✅ Check Vercel logs: `vercel logs`
- ✅ Set APP_DEBUG=true temporarily untuk lihat error detail

---

## 🎯 Alternative: Deploy ke Railway (Simpler Setup)

Railway lebih simple karena satu platform untuk app + database:

1. **Buat akun:** https://railway.app
2. **New Project** → **Deploy from GitHub**
3. **Add PostgreSQL database** (klik + → Database → PostgreSQL)
4. **Set Environment Variables** (otomatis generate dari .env)
5. **Deploy!**

Railway free tier: 500 jam/bulan (cukup untuk demo/small traffic)

---

## 📝 Notes

- **Vercel.json** sudah configured untuk handle PHP/Laravel routing
- **CV files** tidak disimpan permanent (processed in-memory) - cocok untuk Vercel serverless
- **Database** di Supabase bisa diakses dari mana saja (public accessible dengan credentials)
- **Scaling:** Untuk production dengan traffic tinggi, consider upgrade ke paid tier atau Railway Pro

---

## 🚀 Quick Deploy Commands

```bash
# 1. Setup Supabase database (web dashboard)
# 2. Set environment variables di Vercel
# 3. Deploy:
vercel --prod

# 4. Run migrations via Supabase SQL Editor
# 5. Done! 🎉
```

---

**Need Help?**
- Vercel Docs: https://vercel.com/docs
- Supabase Docs: https://supabase.com/docs
- Laravel Deployment: https://laravel.com/docs/deployment

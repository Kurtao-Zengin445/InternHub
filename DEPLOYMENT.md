# Deployment Guide - Railway & Replit

## Persiapan Sebelum Deploy

### 1. Push Code ke GitHub
```bash
git add .
git commit -m "Prepare for Railway deployment"
git push origin main
```

### 2. Setup Railway Account
1. Kunjungi [railway.app](https://railway.app)
2. Login dengan GitHub account
3. Klik "New Project"
4. Pilih "Deploy from GitHub repo"
5. Pilih repository project ini

### 3. Setup Database
1. Di Railway dashboard, klik "Add Plugin"
2. Pilih "Database" → "MySQL"
3. Database akan otomatis dibuat

### 4. Konfigurasi Environment Variables
Di Railway project settings, tambahkan environment variables berikut:

```env
APP_NAME="Sistem Manajemen Magang"
APP_ENV=production
APP_KEY= # akan digenerate otomatis
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://your-project-name.up.railway.app

GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=https://your-project-name.up.railway.app/auth/google/callback

DB_CONNECTION=mysql
DB_HOST=${{ MYSQLHOST }}
DB_PORT=${{ MYSQLPORT }}
DB_DATABASE=${{ MYSQLDATABASE }}
DB_USERNAME=${{ MYSQLUSER }}
DB_PASSWORD=${{ MYSQLPASSWORD }}

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

### 5. Setup Domain (Opsional)
1. Di Railway dashboard, klik "Settings" → "Domains"
2. Tambahkan custom domain jika diperlukan

## Troubleshooting

### Error: "No application encryption key has been specified"
- Pastikan `APP_KEY` sudah di-set di environment variables
- Atau generate key baru: `php artisan key:generate`

### Error: Database connection failed
- Pastikan semua DB environment variables sudah benar
- Check Railway database credentials di "Variables" tab

### Error: Storage permissions
- Storage folder permissions akan diatur otomatis oleh Dockerfile

### Error: Assets not loading
- Pastikan `npm run build` berhasil dijalankan
- Check `public/build` folder ada file assets

## Post-Deployment Checklist

- [ ] Aplikasi dapat diakses di Railway URL
- [ ] Database migrations berhasil dijalankan
- [ ] File storage dapat diakses
- [ ] Google OAuth redirect URI sudah diupdate
- [ ] Email notifications (jika ada) sudah dikonfigurasi
- [ ] SSL certificate otomatis dari Railway

## Monitoring

Railway menyediakan:
- Real-time logs di dashboard
- Metrics dan analytics
- Automatic scaling
- Backup database

## Cost Estimation

**Railway:**
- Free tier: $5/month credit
- MySQL Database: $0/month (free)
- Application hosting: tergantung usage
- Estimasi: $5-15/month

**Replit:**
- Free tier tersedia (limited CPU/RAM)
- Database: Gunakan external MySQL/PostgreSQL (Neon free tier)
- Pro: $10/month untuk production



## Deployment Guide - Replit

### Persiapan Sebelum Deploy
1. **Push ke GitHub** (jika belum):
   ```
   git add .
   git commit -m \"Prepare for Replit deployment\"
   git push origin main
   ```

### 2. Setup Replit Account
1. Kunjungi [replit.com](https://replit.com)
2. Login dengan GitHub/Google
3. Klik \"+ Create Repl\" → Pilih **PHP** template

### 3. Import Project
1. Upload ZIP project atau import dari GitHub repo
2. Pastikan semua files termasuk `.replit`, `composer.json`, `package.json`

### 4. Setup Database
**Opsi 1: External MySQL (Recommended)**
- Gunakan Neon.tech, PlanetScale, atau Railway MySQL (free tier)
- Copy connection details

**Opsi 2: Replit Database (SQLite/PostgreSQL)**
- Replit built-in DB (limited untuk production)

### 5. Konfigurasi Environment Variables (Secrets)
Di Replit → Tools → Secrets, tambahkan:

```env
APP_NAME=\"Sistem Manajemen Magang\"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://your-repl-name.yourusername.repl.co
APP_KEY=base64:your-generated-key

GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=https://your-repl-name.yourusername.repl.co/auth/google/callback

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-pass

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

**Generate APP_KEY:** Di Replit Shell: `php artisan key:generate --show`

### 6. Build & Run
- Replit otomatis run build dari `.replit`
- Jika error: Shell → `composer install`, `npm run build`
- Run migrations: `php artisan migrate`

### 7. Custom Domain (Pro)
- Replit Pro: Bind custom domain

## Replit Troubleshooting

### Error: \"No application encryption key\"
```
php artisan key:generate --force
```

### Assets tidak load
```
npm run build
```

### Database connection failed
- Check Secrets DB vars
- Test: `php artisan tinker` → `DB::connection()->getPdo()`

### Port binding error
- `.replit` sudah handle `$PORT`

### Storage permissions
```
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

## Post-Deployment Checklist (Replit)

- [ ] App accessible di Replit URL
- [ ] Migrations run (`php artisan migrate`)
- [ ] `npm run build` success, assets load
- [ ] Google OAuth callback URL updated
- [ ] Storage writable
- [ ] SSL automatic (https://*.repl.co)

## Replit Monitoring
- Real-time console logs
- Built-in metrics (Pro)
- Database explorer (built-in DB)

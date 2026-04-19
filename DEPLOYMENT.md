# Deployment Guide - Railway

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

Railway menawarkan:
- Free tier: $5/month credit
- MySQL Database: $0/month (free)
- Application hosting: tergantung usage

Untuk production, estimasi biaya sekitar $5-15/month tergantung traffic.
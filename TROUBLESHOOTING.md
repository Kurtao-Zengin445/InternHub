# Troubleshooting Guide - Railway Deployment

## Common Issues & Solutions

### ❌ PROBLEM: "No application encryption key has been specified"
**SOLUTION:**
- Pastikan APP_KEY sudah di-set di Railway variables
- Railway akan generate otomatis saat deploy
- Atau generate manual: `php artisan key:generate --show`

### ❌ PROBLEM: "Database connection failed"
**SOLUTION:**
- Check semua DB environment variables sudah benar:
  - DB_HOST: ${{MYSQLHOST}}
  - DB_PORT: ${{MYSQLPORT}}
  - DB_DATABASE: ${{MYSQLDATABASE}}
  - DB_USERNAME: ${{MYSQLUSER}}
  - DB_PASSWORD: ${{MYSQLPASSWORD}}
- Pastikan MySQL plugin sudah ter-install di Railway

### ❌ PROBLEM: "Assets not loading (CSS/JS broken)"
**SOLUTION:**
- npm run build gagal saat deployment
- Check Railway build logs untuk error details
- Pastikan package.json dependencies lengkap
- Node.js version compatibility

### ❌ PROBLEM: "Route not found" atau 404 errors
**SOLUTION:**
- Route caching gagal
- Check untuk duplicate route names
- Pastikan semua middleware ter-install
- Verify route definitions di web.php

### ❌ PROBLEM: "Storage permissions error"
**SOLUTION:**
- Storage folder permissions sudah diatur di Dockerfile
- Laravel storage:link mungkin perlu dijalankan
- Check file upload paths

### ❌ PROBLEM: "Class not found" errors
**SOLUTION:**
- Composer autoload bermasalah
- Jalankan: `composer dump-autoload`
- Pastikan semua dependencies ter-install

### ❌ PROBLEM: "Migration errors"
**SOLUTION:**
- Database belum di-migrate
- Jalankan: `php artisan migrate --force`
- Check database connection
- Verify migration files syntax

## Debugging Steps

### 1. Check Railway Logs
- Buka Railway dashboard > Your Project > Logs tab
- Cari error messages dan stack traces
- Logs akan show build dan runtime errors

### 2. Test Locally First
```bash
# Test production build locally
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Environment Variables
- Pastikan semua required variables sudah di-set
- Check variable names (case-sensitive)
- Verify Railway variable syntax: `${{VARIABLE_NAME}}`

### 4. Database Issues
- Test connection dengan Railway MySQL
- Jalankan migrations manual jika perlu
- Check database credentials di Railway plugins

### 5. File Permissions
- Laravel storage dan bootstrap/cache perlu writable
- Dockerfile sudah mengatur permissions
- Check file ownership di container

## Useful Commands

```bash
# Check Laravel status
php artisan about

# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Test database connection
php artisan tinker
DB::connection()->getPdo();

# Check routes
php artisan route:list

# Run migrations
php artisan migrate --force
php artisan migrate:status
```

## Getting Help

1. **Railway Documentation**: https://docs.railway.app/
2. **Laravel Documentation**: https://laravel.com/docs
3. **Check Railway Status**: https://railway.app/status
4. **Community Support**: Railway Discord atau GitHub issues

## Prevention Tips

- ✅ Test deployment di staging environment dulu
- ✅ Backup database sebelum major changes
- ✅ Monitor logs regularly
- ✅ Keep dependencies updated
- ✅ Use environment-specific configurations
- ✅ Document all custom configurations
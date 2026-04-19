@echo off
echo 🚀 Preparing Laravel project for Railway deployment...

REM Install dependencies
echo 📦 Installing PHP dependencies...
composer install --no-dev --optimize-autoloader --no-interaction

REM Install Node dependencies and build assets
echo 🎨 Building frontend assets...
npm install
npm run build

REM Create production .env if not exists
if not exist .env (
    echo 📝 Creating production environment file...
    copy .env.example .env
)

REM Generate application key
echo 🔑 Generating application key...
php artisan key:generate

REM Clear any existing cache
echo 🧹 Clearing caches...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo ✅ Production preparation complete!
echo.
echo Next steps:
echo 1. Push this code to GitHub
echo 2. Create new project on Railway.app
echo 3. Connect your GitHub repository
echo 4. Add MySQL database plugin
echo 5. Configure environment variables
echo 6. Deploy!
echo.
echo 📖 See DEPLOYMENT.md for detailed instructions
pause
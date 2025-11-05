# Script to migrate database to Neon
Write-Host "🚀 Migrating database to Neon..." -ForegroundColor Green

# Clear cache
Write-Host "`n📦 Clearing cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear

# Run migrations
Write-Host "`n🔄 Running migrations..." -ForegroundColor Yellow
php artisan migrate:fresh --seed

Write-Host "`n✅ Migration complete!" -ForegroundColor Green
Write-Host "Database is now on Neon! 🎉" -ForegroundColor Cyan

sh '''
cp .env.example .env
php artisan key:generate
sed -i 's/^LOG_LEVEL=.*/LOG_LEVEL=debug/' .env
sed -i 's/^DB_HOST=.*/DB_HOST=localhost/' .env
sed -i 's/^DB_DATABASE=.*/DB_DATABASE=citynexus/' .env
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=root/' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=/' .env

# Install dependencies and build assets
npm ci
npm run build

# Clear Laravel caches
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
'''
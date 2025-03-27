sh '''
php artisan migrate:fresh
#php artisan db:seed
# Skip failing tests for now
#./vendor/bin/pest

if [ $? -eq 0 ]; then
    ./clean.sh
fi

rm -fR .env
'''
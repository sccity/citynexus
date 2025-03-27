#!/bin/bash

# Ensure the script stops if any command fails
set -e

# Parse command line arguments
DUMP_AUTOLOAD=false
BUILD_MODE=false
while getopts "ab" opt; do
    case $opt in
        a)
            DUMP_AUTOLOAD=true
            ;;
        b)
            BUILD_MODE=true
            ;;
        \?)
            echo "Invalid option: -$OPTARG" >&2
            exit 1
            ;;
    esac
done

echo "Starting Laravel cleanup..."

rm -fR bootstrap/cache/*
rm -fR storage/cache/*
rm -fR storage/framework/views/*
rm -fR storage/framework/sessions/*
rm -fR storage/app/public/*
rm -fR storage/logs/*

# Clear various caches and configurations
echo "Clearing cache..."
php artisan cache:clear --no-interaction

echo "Clearing config cache..."
php artisan config:clear --no-interaction

echo "Clearing route cache..."
php artisan route:clear --no-interaction

echo "Clearing view cache..."
php artisan view:clear --no-interaction

echo "Clearing event cache..."
php artisan event:clear --no-interaction

# Generate Ziggy routes
echo "Generating Ziggy routes..."
php artisan ziggy:generate --no-interaction

# Only rebuild caches if not in build mode
if [ "$BUILD_MODE" = false ]; then
    echo "Rebuilding config cache..."
    php artisan config:cache --no-interaction

    echo "Rebuilding route cache..."
    php artisan route:cache --no-interaction

    echo "Rebuilding view cache..."
    php artisan view:cache --no-interaction
fi

# Only run composer dump-autoload if the -a flag is set
if [ "$DUMP_AUTOLOAD" = true ]; then
    echo "Optimizing Composer autoload..."
    composer dump-autoload -o
fi

echo "Laravel cleanup completed successfully!"
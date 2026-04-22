#!/bin/bash

# Ensure database exists if it doesn't
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi

# Set permissions for storage and bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Run migrations
php artisan migrate --force

# Execution of CMD
exec apache2-foreground

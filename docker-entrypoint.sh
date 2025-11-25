#!/bin/bash
set -e

echo "Starting application setup..."

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if nc -z mysql 3306 2>/dev/null; then
        echo "MySQL port is open!"
        sleep 5  # Give MySQL a few more seconds to fully initialize
        break
    fi
    attempt=$((attempt + 1))
    echo "Waiting for MySQL... (attempt $attempt/$max_attempts)"
    sleep 2
done

if [ $attempt -eq $max_attempts ]; then
    echo "WARNING: MySQL did not become ready in time. Continuing anyway..."
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, continuing..."

# Create storage symlink if it doesn't exist
if [ ! -L public/storage ]; then
    echo "Creating storage symlink..."
    php artisan storage:link || echo "Storage link failed, continuing..."
fi

# Clear and cache config for Docker environment
echo "Optimizing application..."
php artisan config:clear
php artisan config:cache || echo "Config cache failed, continuing..."

echo "Application is ready! Starting server..."

# Execute the main command (php artisan serve)
exec "$@"
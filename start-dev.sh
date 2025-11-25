#!/bin/bash
# A single script to launch the entire Serbizyu development environment.

# --- Step 1: Stop any previously running containers ---
echo "Stopping any old containers..."
bash vendor/laravel/sail/bin/sail down


# --- Step 2: Start all Docker services in the background ---
echo "Starting all Docker services (Laravel, MySQL, Mailpit, etc.)..."
bash vendor/laravel/sail/bin/sail up -d


# --- Step 3: Run database migrations ---
# Ensures the database schema is up-to-date.
echo "Running database migrations..."
echo "Clear env and config"
bash vendor/laravel/sail/bin/sail artisan config:clear
bash vendor/laravel/sail/bin/sail artisan migrate --force
# Optionally seed database
bash vendor/laravel/sail/bin/sail artisan db:seed

# --- Step 4: Link storage directory ---
# This makes your uploaded files publicly accessible. It's safe to run this every time.
echo "Linking storage directory..."
bash vendor/laravel/sail/bin/sail artisan storage:link
# --- Step 5: Install NPM dependencies ---
# This is the most important step to prevent Windows/Linux conflicts.
# It deletes old dependencies and reinstalls them fresh inside the container.
echo "Reinstalling NPM dependencies inside the container for consistency..."
bash vendor/laravel/sail/bin/sail artisan clear-compiled
bash vendor/laravel/sail/bin/sail artisan optimize
bash vendor/laravel/sail/bin/sail npm install

# --- Step 6: Start the Vite development server ---
# This will run in the foreground and show you the Vite output.
# Your environment is ready when you see the Vite URL.
echo "Starting Vite development server... Your environment is ready when you see the Vite output."
bash vendor/laravel/sail/bin/sail npm run dev

# --- Step 7: Start the Laravel development server ---
# This will run in the foreground and show you the Laravel output.
# Your environment is ready when you see the Laravel output.
echo "Starting Laravel development server... Your environment is ready when you see the Laravel output."
bash vendor/laravel/sail/bin/sail artisan serve
#!/bin/bash

# Escuela Presente Deployment Script
# Usage: ./deploy.sh

set -e  # Exit on error

echo "🚀 Starting deployment..."

# Navigate to project directory (modify if needed)
cd /var/www/escuela-presente || {
    echo "❌ Project directory not found. Please check the path."
    exit 1
}

# Configure Git safe directory
git config --global --add safe.directory /var/www/escuela-presente

# Put application in maintenance mode
echo "📦 Enabling maintenance mode..."
php artisan down || true

# Fetch latest changes from Git
echo "📥 Fetching latest changes from Git..."
git reset --hard
git clean -fd
git pull origin main

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Install NPM dependencies and build
echo "📦 Installing NPM dependencies and building..."
npm ci --legacy-peer-deps
npm run build

# Run database migrations (uncomment if needed)
# echo "🗄️ Running database migrations..."
# php artisan migrate --force

# Clear cache and optimize
echo "🧹 Clearing cache..."
php artisan optimize:clear

# Bring application back online
echo "✅ Bringing application back online..."
php artisan up

# Set file permissions
echo "🔐 Setting file permissions..."
chown -R www-data:www-data .

echo "✅ Deployment completed successfully!"

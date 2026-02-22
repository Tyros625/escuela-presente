#!/bin/bash

# Remote deployment script - Run from local machine
# Usage: ./deploy-remote.sh [server_user] [server_ip] [server_path]

set -e

SERVER_USER=${1:-"root"}
SERVER_IP=${2:-"your-server-ip"}
SERVER_PATH=${3:-"/var/www/escuela-presente"}

echo "🚀 Deploying to ${SERVER_USER}@${SERVER_IP}:${SERVER_PATH}..."

# Push changes to git first
echo "📤 Pushing changes to Git..."
git push origin main

# SSH into server and run deployment
ssh ${SERVER_USER}@${SERVER_IP} << EOF
    cd ${SERVER_PATH}
    git config --global --add safe.directory ${SERVER_PATH}
    php artisan down || true
    git fetch origin
    git reset --hard origin/main
    git clean -fd
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
    php artisan migrate --force
    npm ci --legacy-peer-deps
    echo "📦 Building frontend (Vite)..."
    npm run build || { echo "❌ npm run build FAILED - frontend will show OLD menu"; exit 1; }
    echo "📦 Build done: $(ls -la public/build/assets/ 2>/dev/null | head -5)"
    php artisan optimize:clear
    php artisan up
    chown -R www-data:www-data .
    echo "✅ Deployment completed successfully!"
EOF

echo "✅ Remote deployment completed!"

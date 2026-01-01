#!/bin/bash

# ========================================
# Laravel Auto-Deployment Script
# ========================================
# This script pulls latest code from git and updates the Laravel application
# Execute this script via GitHub webhook or manually

set -e  # Exit on any error

# Configuration
REPO_DIR="/path/to/your/realto"  # Update this to your actual server path
LOG_FILE="$REPO_DIR/storage/logs/deployment.log"
DATE=$(date '+%Y-%m-%d %H:%M:%S')

# Function to log messages
log_message() {
    echo "[$DATE] $1" | tee -a "$LOG_FILE"
}

log_message "====== Deployment Started ======"

# Navigate to repository directory
cd "$REPO_DIR" || exit 1
log_message "Changed directory to: $REPO_DIR"

# Put application in maintenance mode
log_message "Putting application in maintenance mode..."
php artisan down || true

# Pull latest code from git
log_message "Pulling latest code from repository..."
git fetch origin
git reset --hard origin/main  # Change 'main' to your branch name if different
log_message "Code updated successfully"

# Install/update Composer dependencies
log_message "Installing Composer dependencies..."
composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
log_message "Composer dependencies updated"

# Install/update NPM dependencies and build assets
log_message "Installing NPM dependencies..."
npm ci --production
log_message "Building frontend assets..."
npm run build
log_message "Frontend assets built successfully"

# Run database migrations
log_message "Running database migrations..."
php artisan migrate --force
log_message "Migrations completed"

# Clear and cache configuration
log_message "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
log_message "Application optimized"

# Set proper permissions
log_message "Setting permissions..."
chown -R www-data:www-data "$REPO_DIR/storage" "$REPO_DIR/bootstrap/cache"
chmod -R 775 "$REPO_DIR/storage" "$REPO_DIR/bootstrap/cache"
log_message "Permissions set"

# Restart queue workers (if you're using queues)
log_message "Restarting queue workers..."
php artisan queue:restart || true

# Clear application cache
log_message "Clearing application cache..."
php artisan cache:clear
php artisan view:clear

# Bring application back up
log_message "Bringing application back online..."
php artisan up

log_message "====== Deployment Completed Successfully ======"

exit 0

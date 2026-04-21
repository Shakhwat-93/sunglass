#!/bin/bash
# =============================================================
# cPanel Laravel Setup Script — Run ONCE after uploading files
# =============================================================
# HOW TO USE:
#   1. Upload all files to your cPanel public_html folder
#   2. SSH into cPanel and run: bash cpanel_setup.sh
# =============================================================

echo "========================================"
echo " Laravel cPanel Setup — Chinatownbd"
echo "========================================"

# Step 1: Copy production env
echo "[1/7] Copying .env.production → .env ..."
cp .env.production .env

# Step 2: Update DB path with real cPanel username
CPANEL_USER=$(whoami)
echo "[2/7] Setting SQLite DB path for user: $CPANEL_USER ..."
sed -i "s|/home/YOUR_CPANEL_USER/|/home/$CPANEL_USER/|g" .env

# Step 3: Generate app key
echo "[3/7] Generating application key ..."
php artisan key:generate --force

# Step 4: Create SQLite database file if missing
echo "[4/7] Ensuring SQLite database exists ..."
mkdir -p database
touch database/database.sqlite

# Step 5: Run migrations
echo "[5/7] Running database migrations ..."
php artisan migrate --force

# Step 6: Set proper permissions
echo "[6/7] Setting folder permissions ..."
chmod -R 775 storage bootstrap/cache
chmod 664 database/database.sqlite

# Step 7: Optimize for production
echo "[7/7] Optimizing for production ..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "========================================"
echo " ✅ Setup Complete! Site is now LIVE."
echo "========================================"
echo ""
echo " ⚠️  IMPORTANT — Do this in cPanel File Manager:"
echo "    Edit .env and set APP_URL=https://yourdomain.com"
echo ""

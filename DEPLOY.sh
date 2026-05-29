#!/usr/bin/env bash
# ============================================================
#  JN Nazareth School — Live Server Deployment Script
#  Run this INSIDE your server (SSH) after pulling code.
#  Safe to run: only applies new migrations, no data loss.
# ============================================================
set -e

# ── CHANGE THIS to your project root on the server ──────────
APP_ROOT="/var/www/html/jn-nazareth"   # <-- update this path

cd "$APP_ROOT"

echo "==> [1/5] Running database migrations..."
php artisan migrate --force

echo "==> [2/5] Clearing config cache..."
php artisan config:clear

echo "==> [3/5] Clearing compiled views..."
php artisan view:clear

echo "==> [4/5] Clearing route cache..."
php artisan route:clear

echo "==> [5/5] Clearing application cache..."
php artisan cache:clear

echo ""
echo "✅  Deployment complete. No existing data was modified."
echo "    Sitemap URL: https://jn-nazareth/sitemap.xml"

#!/bin/bash

# cPanel Deployment Script for KHB Events Booth System
# Run this script via SSH after pulling code from GitHub

echo "=========================================="
echo "KHB Events - cPanel Deployment Script"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Get the script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo -e "${GREEN}Current directory: $SCRIPT_DIR${NC}"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}Warning: .env file not found!${NC}"
    if [ -f .env.example ]; then
        echo "Copying .env.example to .env..."
        cp .env.example .env
        echo -e "${YELLOW}Please edit .env file with your production settings!${NC}"
        echo ""
    else
        echo -e "${RED}Error: .env.example not found!${NC}"
        exit 1
    fi
fi

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo -e "${RED}Error: Composer is not installed!${NC}"
    echo "Please install Composer or use cPanel's PHP Selector"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
echo -e "${GREEN}PHP Version: $PHP_VERSION${NC}"

if php -r "exit(version_compare(PHP_VERSION, '8.1.0', '<') ? 1 : 0);"; then
    echo -e "${RED}Error: PHP 8.1 or higher is required!${NC}"
    exit 1
fi

echo ""
echo "=========================================="
echo "Step 1: Installing Dependencies"
echo "=========================================="
composer install --no-dev --optimize-autoloader

if [ $? -ne 0 ]; then
    echo -e "${RED}Error: Composer install failed!${NC}"
    exit 1
fi

echo ""
echo "=========================================="
echo "Step 2: Generating Application Key"
echo "=========================================="
php artisan key:generate --force

echo ""
echo "=========================================="
echo "Step 3: Setting File Permissions"
echo "=========================================="
chmod -R 755 storage
chmod -R 755 bootstrap/cache
echo -e "${GREEN}Permissions set for storage and bootstrap/cache${NC}"

echo ""
echo "=========================================="
echo "Step 4: Running Database Migrations"
echo "=========================================="
read -p "Do you want to run database migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    if [ $? -ne 0 ]; then
        echo -e "${YELLOW}Warning: Migrations failed. Please check your database configuration.${NC}"
    fi
fi

echo ""
read -p "Do you want to seed the database? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
fi

echo ""
echo "=========================================="
echo "Step 5: Clearing and Caching Configuration"
echo "=========================================="
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "=========================================="
echo "Deployment Complete!"
echo "=========================================="
echo ""
echo -e "${GREEN}✓ Dependencies installed${NC}"
echo -e "${GREEN}✓ Application key generated${NC}"
echo -e "${GREEN}✓ File permissions set${NC}"
echo -e "${GREEN}✓ Configuration cached${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Verify document root points to: $(pwd)/public"
echo "2. Test the application at your domain"
echo "3. Change default admin password"
echo "4. Set APP_DEBUG=false in .env (if not already)"
echo ""
echo -e "${GREEN}Deployment script completed successfully!${NC}"

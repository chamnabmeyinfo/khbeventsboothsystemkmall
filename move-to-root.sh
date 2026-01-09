#!/bin/bash

# Script to move all files from kmallxmas-laravel/ to root folder
# Run this from the parent directory (booths.khbevents.com)

echo "=========================================="
echo "Moving Project to Root Folder"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check if we're in the right directory
if [ ! -d "kmallxmas-laravel" ]; then
    echo -e "${RED}Error: kmallxmas-laravel folder not found!${NC}"
    echo "Please run this script from the parent directory."
    exit 1
fi

# Backup check
echo -e "${YELLOW}⚠️  WARNING: This will move all files from kmallxmas-laravel/ to current directory${NC}"
read -p "Do you want to continue? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Cancelled."
    exit 0
fi

# Check for existing files that might conflict
echo ""
echo "Checking for existing files..."
CONFLICTS=0

if [ -f ".htaccess" ] && [ -f "kmallxmas-laravel/.htaccess" ]; then
    echo -e "${YELLOW}Warning: .htaccess exists in both locations${NC}"
    CONFLICTS=1
fi

if [ -f "index.php" ] && [ -f "kmallxmas-laravel/index.php" ]; then
    echo -e "${YELLOW}Warning: index.php exists in both locations${NC}"
    CONFLICTS=1
fi

if [ -f "composer.json" ] && [ -f "kmallxmas-laravel/composer.json" ]; then
    echo -e "${YELLOW}Warning: composer.json exists in both locations${NC}"
    CONFLICTS=1
fi

if [ $CONFLICTS -eq 1 ]; then
    echo ""
    echo -e "${YELLOW}Conflicts detected!${NC}"
    read -p "Do you want to backup existing files first? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        BACKUP_DIR="backup_$(date +%Y%m%d_%H%M%S)"
        mkdir -p "$BACKUP_DIR"
        echo "Creating backup in $BACKUP_DIR..."
        [ -f ".htaccess" ] && cp .htaccess "$BACKUP_DIR/"
        [ -f "index.php" ] && cp index.php "$BACKUP_DIR/"
        [ -f "composer.json" ] && cp composer.json "$BACKUP_DIR/"
        [ -d "app" ] && cp -r app "$BACKUP_DIR/" 2>/dev/null || true
        [ -d "routes" ] && cp -r routes "$BACKUP_DIR/" 2>/dev/null || true
        echo -e "${GREEN}Backup created in $BACKUP_DIR${NC}"
    fi
fi

echo ""
echo "=========================================="
echo "Moving files..."
echo "=========================================="

# Move all files (including hidden files)
echo "Moving files from kmallxmas-laravel/ to root..."
find kmallxmas-laravel -mindepth 1 -maxdepth 1 -exec mv {} . \;

# Remove empty kmallxmas-laravel directory
if [ -d "kmallxmas-laravel" ]; then
    if [ -z "$(ls -A kmallxmas-laravel)" ]; then
        rmdir kmallxmas-laravel
        echo -e "${GREEN}Removed empty kmallxmas-laravel directory${NC}"
    else
        echo -e "${YELLOW}Warning: kmallxmas-laravel directory is not empty${NC}"
        echo "Please check and remove manually if needed."
    fi
fi

echo ""
echo "=========================================="
echo "Setting permissions..."
echo "=========================================="

# Set permissions
if [ -d "storage" ]; then
    chmod -R 755 storage
    echo -e "${GREEN}Set permissions for storage/${NC}"
fi

if [ -d "bootstrap/cache" ]; then
    chmod -R 755 bootstrap/cache
    echo -e "${GREEN}Set permissions for bootstrap/cache/${NC}"
fi

echo ""
echo "=========================================="
echo "Verification..."
echo "=========================================="

# Verify key files
MISSING=0

if [ ! -f ".htaccess" ]; then
    echo -e "${RED}✗ .htaccess not found${NC}"
    MISSING=1
else
    echo -e "${GREEN}✓ .htaccess found${NC}"
fi

if [ ! -f "index.php" ]; then
    echo -e "${RED}✗ index.php not found${NC}"
    MISSING=1
else
    echo -e "${GREEN}✓ index.php found${NC}"
fi

if [ ! -f "composer.json" ]; then
    echo -e "${RED}✗ composer.json not found${NC}"
    MISSING=1
else
    echo -e "${GREEN}✓ composer.json found${NC}"
fi

if [ ! -d "public" ]; then
    echo -e "${RED}✗ public/ directory not found${NC}"
    MISSING=1
else
    echo -e "${GREEN}✓ public/ directory found${NC}"
fi

if [ ! -d "app" ]; then
    echo -e "${RED}✗ app/ directory not found${NC}"
    MISSING=1
else
    echo -e "${GREEN}✓ app/ directory found${NC}"
fi

echo ""
if [ $MISSING -eq 0 ]; then
    echo "=========================================="
    echo -e "${GREEN}Migration Complete!${NC}"
    echo "=========================================="
    echo ""
    echo "Next steps:"
    echo "1. Update document root in cPanel to: /home/khbevents/booths.khbevents.com"
    echo "2. Verify .env file exists and is configured"
    echo "3. Run: php artisan config:clear"
    echo "4. Test the application at https://booths.khbevents.com"
    echo ""
else
    echo -e "${RED}Some files are missing. Please check the migration.${NC}"
    exit 1
fi

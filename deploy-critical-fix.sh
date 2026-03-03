#!/bin/bash
# 🚨 CRITICAL FIX DEPLOYMENT SCRIPT
# Deploy booking protection fix to production
# Run this on PRODUCTION SERVER

echo "=================================================="
echo "🚨 CRITICAL FIX DEPLOYMENT"
echo "Protecting booking data when deleting booths"
echo "=================================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="$HOME/floorplan.khbevents.com"
BACKUP_DIR="$PROJECT_DIR/backups"
DB_NAME="khbevents"
DB_USER="your_db_user"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/backup_before_critical_fix_$TIMESTAMP.sql"

echo -e "${YELLOW}Step 1: Creating backup directory...${NC}"
mkdir -p "$BACKUP_DIR"
echo -e "${GREEN}✓ Backup directory ready${NC}"
echo ""

echo -e "${YELLOW}Step 2: Backing up database...${NC}"
echo "This may take a few minutes..."
read -sp "Enter database password: " DB_PASS
echo ""

mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database backup created: $BACKUP_FILE${NC}"
    BACKUP_SIZE=$(ls -lh "$BACKUP_FILE" | awk '{print $5}')
    echo "  Backup size: $BACKUP_SIZE"
else
    echo -e "${RED}✗ Database backup FAILED!${NC}"
    echo "DEPLOYMENT ABORTED - Fix backup issue first"
    exit 1
fi
echo ""

echo -e "${YELLOW}Step 3: Navigating to project directory...${NC}"
cd "$PROJECT_DIR" || {
    echo -e "${RED}✗ Failed to navigate to project directory${NC}"
    exit 1
}
echo -e "${GREEN}✓ In project directory: $(pwd)${NC}"
echo ""

echo -e "${YELLOW}Step 4: Pulling latest code from repository...${NC}"
git pull origin main
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Code updated successfully${NC}"
else
    echo -e "${RED}✗ Git pull FAILED!${NC}"
    exit 1
fi
echo ""

echo -e "${YELLOW}Step 5: Clearing Laravel caches...${NC}"
/opt/alt/php82/usr/bin/php artisan config:clear
/opt/alt/php82/usr/bin/php artisan cache:clear
/opt/alt/php82/usr/bin/php artisan route:clear
/opt/alt/php82/usr/bin/php artisan view:clear
echo -e "${GREEN}✓ All caches cleared${NC}"
echo ""

echo "=================================================="
echo -e "${GREEN}✓ DEPLOYMENT COMPLETE!${NC}"
echo "=================================================="
echo ""
echo "📋 NEXT STEPS:"
echo "1. Test booth deletion on production"
echo "2. Verify booked booths are protected"
echo "3. Check Laravel logs for errors"
echo "4. Monitor for 24 hours"
echo ""
echo "📁 Backup saved to:"
echo "   $BACKUP_FILE"
echo ""
echo "⚠️  If issues occur, run rollback script:"
echo "   ./rollback-critical-fix.sh $TIMESTAMP"
echo ""
echo "=================================================="

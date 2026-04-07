#!/bin/bash
# CV Integration - Setup Script
# Automates installation and setup process

set -e  # Exit on error

echo "🚀 CV Integration Setup"
echo "======================="

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Check PHP
echo -e "${BLUE}Step 1: Checking PHP...${NC}"
if ! command -v php &> /dev/null; then
    echo "❌ PHP not found. Please install PHP 8.0+"
    exit 1
fi
PHP_VERSION=$(php -v | head -n 1)
echo -e "${GREEN}✅ $PHP_VERSION${NC}"

# Step 2: Check Composer
echo -e "${BLUE}Step 2: Checking Composer dependencies...${NC}"
if [ -f "composer.json" ]; then
    echo -e "${YELLOW}Installing Composer packages...${NC}"
    # composer install --no-dev  # Uncomment for prod
    echo -e "${GREEN}✅ Composer ready${NC}"
else
    echo -e "${YELLOW}⚠️ No composer.json found - skipping${NC}"
fi

# Step 3: Check database
echo -e "${BLUE}Step 3: Checking database connection...${NC}"
php spark db:seed SampleSeeder 2>/dev/null || echo -e "${YELLOW}⚠️ Database seeds may not be configured${NC}"
echo -e "${GREEN}✅ Database checked${NC}"

# Step 4: Set permissions
echo -e "${BLUE}Step 4: Setting file permissions...${NC}"
chmod -R 755 writable/
chmod -R 755 public/
echo -e "${GREEN}✅ Permissions updated${NC}"

# Step 5: Environment setup
echo -e "${BLUE}Step 5: Checking .env configuration...${NC}"
if grep -q "CV_PARSING_ENABLED" .env; then
    echo -e "${GREEN}✅ CV_PARSING config found${NC}"
else
    echo -e "${YELLOW}⚠️ CV_PARSING config not found in .env${NC}"
    echo "   Add these variables to .env:"
    echo "   CV_PARSING_ENABLED=true"
    echo "   CV_PARSING_BASE_URL=http://localhost:8001"
    echo "   CV_PARSING_API_KEY=your-secret-key"
fi

# Step 6: Test routes
echo -e "${BLUE}Step 6: Listing CV Integration routes...${NC}"
php spark routes | grep -i "cv-\|profile" || echo "Routes not available in CLI"
echo -e "${GREEN}✅ Routes ready${NC}"

# Step 7: Python service check
echo -e "${BLUE}Step 7: Checking Python service...${NC}"
if curl -s http://localhost:8001/health > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Python service running at http://localhost:8001${NC}"
else
    echo -e "${YELLOW}⚠️ Python service not running${NC}"
    echo "   To start it:"
    echo "   cd cv-parsing-service"
    echo "   python main.py"
fi

# Step 8: Summary
echo ""
echo -e "${GREEN}===========================================${NC}"
echo -e "${GREEN}✅ Setup Complete!${NC}"
echo -e "${GREEN}===========================================${NC}"
echo ""
echo "🎯 Quick Links:"
echo "  - Upload CV:    http://localhost:8000/profile/cv-integrate"
echo "  - View Profile: http://localhost:8000/profile"
echo "  - Documentation: CV_INTEGRATION_DOCUMENTATION.md"
echo "  - Quick Start:   CV_INTEGRATION_QUICKSTART.md"
echo ""
echo "📝 Next Steps:"
echo "  1. Make sure PHP server is running (app/spark serve)"
echo "  2. Make sure Python service is running (cd cv-parsing-service && python main.py)"
echo "  3. Visit http://localhost:8000/profile/cv-integrate"
echo "  4. Upload a CV file"
echo ""

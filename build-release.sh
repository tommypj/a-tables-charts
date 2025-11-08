#!/bin/bash
#
# A-Tables and Charts - Release Package Builder
#
# This script creates a clean, production-ready ZIP package
# excluding development files, tests, and documentation.
#
# Usage: bash build-release.sh
#

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PLUGIN_SLUG="a-tables-charts"
VERSION="1.0.4"
BUILD_DIR="build"
RELEASE_DIR="$BUILD_DIR/$PLUGIN_SLUG"
PACKAGE_NAME="$PLUGIN_SLUG-$VERSION.zip"

echo -e "${BLUE}================================${NC}"
echo -e "${BLUE}A-Tables & Charts Release Builder${NC}"
echo -e "${BLUE}Version: $VERSION${NC}"
echo -e "${BLUE}================================${NC}"
echo ""

# Step 1: Clean previous build
echo -e "${YELLOW}Step 1: Cleaning previous build...${NC}"
if [ -d "$BUILD_DIR" ]; then
    rm -rf "$BUILD_DIR"
    echo -e "${GREEN}✓ Previous build cleaned${NC}"
else
    echo -e "${GREEN}✓ No previous build found${NC}"
fi
echo ""

# Step 2: Create build directory
echo -e "${YELLOW}Step 2: Creating build directory...${NC}"
mkdir -p "$RELEASE_DIR"
echo -e "${GREEN}✓ Build directory created: $RELEASE_DIR${NC}"
echo ""

# Step 3: Copy production files
echo -e "${YELLOW}Step 3: Copying production files...${NC}"

# Copy main plugin file
cp a-tables-charts.php "$RELEASE_DIR/"
echo -e "${GREEN}✓ Main plugin file copied${NC}"

# Copy readme.txt
cp readme.txt "$RELEASE_DIR/"
echo -e "${GREEN}✓ readme.txt copied${NC}"

# Copy LICENSE (if exists)
if [ -f "LICENSE" ] || [ -f "LICENSE.txt" ]; then
    cp LICENSE* "$RELEASE_DIR/" 2>/dev/null || true
    echo -e "${GREEN}✓ LICENSE copied${NC}"
fi

# Copy source directory
echo -e "${BLUE}  Copying src/ directory...${NC}"
cp -r src "$RELEASE_DIR/"
echo -e "${GREEN}✓ src/ copied${NC}"

# Copy assets directory (CSS, JS, images)
echo -e "${BLUE}  Copying assets/ directory...${NC}"
cp -r assets "$RELEASE_DIR/"
echo -e "${GREEN}✓ assets/ copied${NC}"

# Copy vendor directory (Composer dependencies)
if [ -d "vendor" ]; then
    echo -e "${BLUE}  Copying vendor/ directory...${NC}"
    cp -r vendor "$RELEASE_DIR/"
    echo -e "${GREEN}✓ vendor/ copied${NC}"
else
    echo -e "${YELLOW}⚠ vendor/ not found - run 'composer install --no-dev' first${NC}"
fi

# Copy languages directory
if [ -d "languages" ]; then
    echo -e "${BLUE}  Copying languages/ directory...${NC}"
    cp -r languages "$RELEASE_DIR/"
    echo -e "${GREEN}✓ languages/ copied${NC}"
fi

echo ""

# Step 4: Clean up development files from copied directories
echo -e "${YELLOW}Step 4: Cleaning development files...${NC}"

# Remove .git directories
find "$RELEASE_DIR" -type d -name ".git" -exec rm -rf {} + 2>/dev/null || true
echo -e "${GREEN}✓ .git directories removed${NC}"

# Remove .gitignore files
find "$RELEASE_DIR" -name ".gitignore" -delete 2>/dev/null || true
echo -e "${GREEN}✓ .gitignore files removed${NC}"

# Remove node_modules
find "$RELEASE_DIR" -type d -name "node_modules" -exec rm -rf {} + 2>/dev/null || true
echo -e "${GREEN}✓ node_modules removed${NC}"

# Remove development markdown files (keep README if in subdirectories)
find "$RELEASE_DIR/src" -name "*.md" -type f -delete 2>/dev/null || true
echo -e "${GREEN}✓ Development .md files removed${NC}"

# Remove composer.json and composer.lock from vendor
rm -f "$RELEASE_DIR/vendor/composer.json" 2>/dev/null || true
rm -f "$RELEASE_DIR/vendor/composer.lock" 2>/dev/null || true

# Remove .DS_Store (Mac)
find "$RELEASE_DIR" -name ".DS_Store" -delete 2>/dev/null || true
echo -e "${GREEN}✓ .DS_Store files removed${NC}"

# Remove Thumbs.db (Windows)
find "$RELEASE_DIR" -name "Thumbs.db" -delete 2>/dev/null || true
echo -e "${GREEN}✓ Thumbs.db files removed${NC}"

# Remove empty directories
find "$RELEASE_DIR" -type d -empty -delete 2>/dev/null || true
echo -e "${GREEN}✓ Empty directories removed${NC}"

echo ""

# Step 5: Set proper permissions
echo -e "${YELLOW}Step 5: Setting file permissions...${NC}"
find "$RELEASE_DIR" -type f -exec chmod 644 {} \;
find "$RELEASE_DIR" -type d -exec chmod 755 {} \;
echo -e "${GREEN}✓ File permissions set (644 for files, 755 for directories)${NC}"
echo ""

# Step 6: Create ZIP package
echo -e "${YELLOW}Step 6: Creating ZIP package...${NC}"
cd "$BUILD_DIR"
zip -r "../$PACKAGE_NAME" "$PLUGIN_SLUG" -q
cd ..
echo -e "${GREEN}✓ ZIP package created: $PACKAGE_NAME${NC}"
echo ""

# Step 7: Calculate package size and statistics
echo -e "${YELLOW}Step 7: Package statistics...${NC}"
PACKAGE_SIZE=$(du -h "$PACKAGE_NAME" | cut -f1)
FILE_COUNT=$(find "$RELEASE_DIR" -type f | wc -l)
PHP_COUNT=$(find "$RELEASE_DIR" -name "*.php" -type f | wc -l)
JS_COUNT=$(find "$RELEASE_DIR" -name "*.js" -type f | wc -l)
CSS_COUNT=$(find "$RELEASE_DIR" -name "*.css" -type f | wc -l)

echo -e "${BLUE}Package: $PACKAGE_NAME${NC}"
echo -e "${BLUE}Size: $PACKAGE_SIZE${NC}"
echo -e "${BLUE}Total files: $FILE_COUNT${NC}"
echo -e "${BLUE}PHP files: $PHP_COUNT${NC}"
echo -e "${BLUE}JS files: $JS_COUNT${NC}"
echo -e "${BLUE}CSS files: $CSS_COUNT${NC}"
echo ""

# Step 8: Verify package contents
echo -e "${YELLOW}Step 8: Verifying package contents...${NC}"

ERRORS=0

# Check for required files
if [ ! -f "$RELEASE_DIR/a-tables-charts.php" ]; then
    echo -e "${RED}✗ Main plugin file missing!${NC}"
    ERRORS=$((ERRORS + 1))
fi

if [ ! -f "$RELEASE_DIR/readme.txt" ]; then
    echo -e "${RED}✗ readme.txt missing!${NC}"
    ERRORS=$((ERRORS + 1))
fi

if [ ! -d "$RELEASE_DIR/src" ]; then
    echo -e "${RED}✗ src/ directory missing!${NC}"
    ERRORS=$((ERRORS + 1))
fi

if [ ! -d "$RELEASE_DIR/assets" ]; then
    echo -e "${RED}✗ assets/ directory missing!${NC}"
    ERRORS=$((ERRORS + 1))
fi

# Check for files that should NOT be included
if [ -d "$RELEASE_DIR/.git" ]; then
    echo -e "${RED}✗ .git directory found (should be excluded)${NC}"
    ERRORS=$((ERRORS + 1))
fi

if [ -d "$RELEASE_DIR/tests" ]; then
    echo -e "${RED}✗ tests/ directory found (should be excluded)${NC}"
    ERRORS=$((ERRORS + 1))
fi

if [ -d "$RELEASE_DIR/node_modules" ]; then
    echo -e "${RED}✗ node_modules/ found (should be excluded)${NC}"
    ERRORS=$((ERRORS + 1))
fi

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✓ All required files present${NC}"
    echo -e "${GREEN}✓ No development files included${NC}"
    echo -e "${GREEN}✓ Package structure verified${NC}"
else
    echo -e "${RED}✗ $ERRORS error(s) found in package${NC}"
fi

echo ""

# Step 9: Create checksums
echo -e "${YELLOW}Step 9: Creating checksums...${NC}"
MD5_CHECKSUM=$(md5sum "$PACKAGE_NAME" | cut -d' ' -f1)
echo "$MD5_CHECKSUM  $PACKAGE_NAME" > "$PACKAGE_NAME.md5"
echo -e "${GREEN}✓ MD5: $MD5_CHECKSUM${NC}"

SHA256_CHECKSUM=$(sha256sum "$PACKAGE_NAME" | cut -d' ' -f1)
echo "$SHA256_CHECKSUM  $PACKAGE_NAME" > "$PACKAGE_NAME.sha256"
echo -e "${GREEN}✓ SHA256: $SHA256_CHECKSUM${NC}"
echo ""

# Final summary
echo -e "${BLUE}================================${NC}"
echo -e "${GREEN}✓ Build Complete!${NC}"
echo -e "${BLUE}================================${NC}"
echo ""
echo -e "${GREEN}Release package: $PACKAGE_NAME${NC}"
echo -e "${GREEN}Package size: $PACKAGE_SIZE${NC}"
echo -e "${GREEN}Build directory: $BUILD_DIR/$PLUGIN_SLUG/${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo -e "  1. Test the package by installing it on a fresh WordPress site"
echo -e "  2. Upload to WordPress.org or your distribution channel"
echo -e "  3. Clean up: rm -rf $BUILD_DIR (optional)"
echo ""
echo -e "${BLUE}Happy launching! 🚀${NC}"

# 📋 STEP-BY-STEP GUIDE: Building A-Tables & Charts LITE

## 🎯 Overview
This guide will walk you through creating the FREE (LITE) version from your PRO plugin.
Follow each step carefully and check them off as you complete them.

---

## ⚠️ IMPORTANT: Before You Start

1. **Backup your PRO plugin** - Make sure you have a backup
2. **Don't modify the PRO version** - We're creating a COPY
3. **Work in order** - Follow steps sequentially
4. **Test as you go** - Verify each major step works

**Estimated Time:** 2-3 hours

---

## 📁 PHASE 1: CREATE LITE FOLDER STRUCTURE

### Step 1.1: Copy Plugin Folder

**Location:** `C:\Users\Tommy\Local Sites\my-wordpress-site\app\public\wp-content\plugins\`

**Actions:**
1. ✅ Navigate to the plugins folder
2. ✅ Find `a-tables-charts` folder
3. ✅ Copy the entire folder
4. ✅ Paste it in the same directory
5. ✅ Rename the copy to: `a-tables-charts-lite`

**Result:** You should now have:
```
plugins/
├── a-tables-charts/          (PRO - keep as is)
└── a-tables-charts-lite/     (NEW - we'll modify this)
```

---

## 📝 PHASE 2: RENAME MAIN PLUGIN FILE

### Step 2.1: Rename Main File

**Location:** `plugins/a-tables-charts-lite/`

**Actions:**
1. ✅ Find file: `a-tables-charts.php`
2. ✅ Rename to: `a-tables-charts-lite.php`

### Step 2.2: Update Plugin Header

**File:** `a-tables-charts-lite.php`

**Find this block at the top:**
```php
/**
 * Plugin Name: A-Tables & Charts
 * Plugin URI: https://a-tables-charts.com
 * Description: Create beautiful, responsive tables and interactive charts from CSV, JSON, Excel, and XML files.
 * Version: 1.0.4
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: A-Tables Team
 * Author URI: https://a-tables-charts.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: a-tables-charts
 * Domain Path: /languages
 */
```

**Replace with:**
```php
/**
 * Plugin Name: A-Tables & Charts Lite
 * Plugin URI: https://a-tables-charts.com
 * Description: Create beautiful, responsive tables from CSV files. Upgrade to PRO for Excel, JSON, XML import, charts, and more!
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: A-Tables Team
 * Author URI: https://a-tables-charts.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: a-tables-charts-lite
 * Domain Path: /languages
 */
```

### Step 2.3: Update Constants

**Still in:** `a-tables-charts-lite.php`

**Find these constants (around line 30-40):**
```php
define( 'ATABLES_VERSION', '1.0.4' );
define( 'ATABLES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ATABLES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ATABLES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'ATABLES_SLUG', 'a-tables-charts' );
```

**Replace with:**
```php
define( 'ATABLES_LITE_VERSION', '1.0.0' );
define( 'ATABLES_LITE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ATABLES_LITE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ATABLES_LITE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'ATABLES_LITE_SLUG', 'a-tables-charts-lite' );
define( 'ATABLES_LITE_IS_PRO', false ); // NEW CONSTANT!
```

**⚠️ IMPORTANT:** Add the new constant `ATABLES_LITE_IS_PRO` - this tells the plugin it's the LITE version!

### Step 2.4: Update Namespace

**Still in:** `a-tables-charts-lite.php`

**Find:**
```php
use ATablesCharts\Core\Plugin;
```

**Replace with:**
```php
use ATablesChartsLite\Core\Plugin;
```

**Save the file!** ✅

---

## 🔧 PHASE 3: UPDATE FEATURES.PHP FOR LITE VERSION

### Step 3.1: Modify Features Helper

**File:** `src/shared/utils/Features.php`

**Find the `is_pro()` method (around line 122):**
```php
public static function is_pro() {
    // This will be different in lite version
    // PRO version: return true
    // LITE version: return false
    return true; // PRO version
}
```

**Replace with:**
```php
public static function is_pro() {
    // LITE version always returns false
    return false;
}
```

**Save the file!** ✅

---

## 🗑️ PHASE 4: DELETE PRO MODULES

### Step 4.1: Delete Charts Module

**Location:** `src/modules/`

**Actions:**
1. ✅ Navigate to `src/modules/`
2. ✅ Find `charts` folder
3. ✅ **DELETE the entire folder**

### Step 4.2: Delete Database Module

**Location:** `src/modules/`

**Actions:**
1. ✅ Find `database` folder
2. ✅ **DELETE the entire folder**

### Step 4.3: Delete Import Module (Excel/XML)

**Location:** `src/modules/`

**Actions:**
1. ✅ Find `import` folder
2. ✅ **DELETE the entire folder**

### Step 4.4: Delete Advanced Parsers

**Location:** `src/modules/dataSources/parsers/`

**Actions:**
1. ✅ Navigate to `src/modules/dataSources/parsers/`
2. ✅ Find and **DELETE** `JsonParser.php`
3. ✅ Keep `CsvParser.php` (this is for FREE version)
4. ✅ Keep `ParserInterface.php` (needed)
5. ✅ Keep `PHPArrayParser.php` (needed)

### Step 4.5: Delete Advanced Exporters

**Location:** `src/modules/export/exporters/`

**Actions:**
1. ✅ Navigate to `src/modules/export/exporters/`
2. ✅ Find and **DELETE** `ExcelExporter.php`
3. ✅ Find and **DELETE** `PdfExporter.php`
4. ✅ Keep `CSVExporter.php` (this is for FREE version)

### Step 4.6: Delete Advanced Export Services

**Location:** `src/modules/export/services/`

**Actions:**
1. ✅ Navigate to `src/modules/export/services/`
2. ✅ Find and **DELETE** `ExcelExportService.php`
3. ✅ Find and **DELETE** `PdfExportService.php`
4. ✅ Keep `CSVExportService.php` (this is for FREE version)

---

## 🔍 PHASE 5: GLOBAL FIND & REPLACE

**⚠️ CRITICAL:** You need to do a global find and replace in ALL PHP files in the `a-tables-charts-lite` folder.

### Option A: Using Visual Studio Code (Recommended)

1. ✅ Open VS Code
2. ✅ Open the `a-tables-charts-lite` folder
3. ✅ Press `Ctrl+Shift+H` (Find and Replace in Files)
4. ✅ Make sure "Files to include" shows `**/*.php`

**Perform these replacements IN ORDER:**

#### Replace 1: Text Domain
- **Find:** `'a-tables-charts'`
- **Replace:** `'a-tables-charts-lite'`
- **Files:** `*.php`
- **Click:** Replace All
- **Expected:** ~200-300 replacements

#### Replace 2: Namespace
- **Find:** `namespace ATablesCharts\\`
- **Replace:** `namespace ATablesChartsLite\\`
- **Files:** `*.php`
- **Click:** Replace All
- **Expected:** ~50-80 replacements

#### Replace 3: Use Statements
- **Find:** `use ATablesCharts\\`
- **Replace:** `use ATablesChartsLite\\`
- **Files:** `*.php`
- **Click:** Replace All
- **Expected:** ~100-150 replacements

#### Replace 4: Constants (if any ATABLES_ without LITE)
- **Find:** `ATABLES_VERSION`
- **Replace:** `ATABLES_LITE_VERSION`
- **Files:** `*.php`
- **Click:** Replace All

- **Find:** `ATABLES_PLUGIN_DIR`
- **Replace:** `ATABLES_LITE_PLUGIN_DIR`
- **Files:** `*.php`
- **Click:** Replace All

- **Find:** `ATABLES_PLUGIN_URL`
- **Replace:** `ATABLES_LITE_PLUGIN_URL`
- **Files:** `*.php`
- **Click:** Replace All

- **Find:** `ATABLES_PLUGIN_BASENAME`
- **Replace:** `ATABLES_LITE_PLUGIN_BASENAME`
- **Files:** `*.php`
- **Click:** Replace All

- **Find:** `ATABLES_SLUG`
- **Replace:** `ATABLES_LITE_SLUG`
- **Files:** `*.php`
- **Click:** Replace All

### Option B: Using Search and Replace Tool

If you have another text editor or search tool, use it to do the same replacements above.

**⚠️ IMPORTANT:** Make sure you ONLY edit files in the `a-tables-charts-lite` folder!

---

## 🎨 PHASE 6: ADD PRO BADGES TO CREATE TABLE PAGE

### Step 6.1: Modify create-table.php

**File:** `src/modules/core/views/create-table.php`

**Find the data source cards section (around line 50-100).** Look for the CSV card which looks like:

```php
<div class="atables-source-card" data-source="csv">
    <div class="atables-source-icon">📄</div>
    <h3><?php _e( 'CSV File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Upload a CSV or TXT file', 'a-tables-charts-lite' ); ?></p>
</div>
```

**After the CSV card, you'll see other cards (JSON, Excel, XML).** 

**Wrap each PRO card with the `atables-pro-feature` class and add a PRO badge:**

#### JSON Card:
**Find:**
```php
<div class="atables-source-card" data-source="json">
    <div class="atables-source-icon">{ }</div>
    <h3><?php _e( 'JSON File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Import JSON data', 'a-tables-charts-lite' ); ?></p>
</div>
```

**Replace with:**
```php
<div class="atables-source-card atables-pro-feature" data-source="json" data-feature-name="JSON Import" data-feature-description="Import data from JSON files">
    <div class="atables-pro-badge">PRO</div>
    <div class="atables-source-icon">{ }</div>
    <h3><?php _e( 'JSON File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Import JSON data', 'a-tables-charts-lite' ); ?></p>
</div>
```

#### Excel Card:
**Find:**
```php
<div class="atables-source-card" data-source="excel">
    <div class="atables-source-icon">📊</div>
    <h3><?php _e( 'Excel File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Import XLS or XLSX files', 'a-tables-charts-lite' ); ?></p>
</div>
```

**Replace with:**
```php
<div class="atables-source-card atables-pro-feature" data-source="excel" data-feature-name="Excel Import" data-feature-description="Import data from Excel files (XLS, XLSX)">
    <div class="atables-pro-badge">PRO</div>
    <div class="atables-source-icon">📊</div>
    <h3><?php _e( 'Excel File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Import XLS or XLSX files', 'a-tables-charts-lite' ); ?></p>
</div>
```

#### XML Card:
**Find:**
```php
<div class="atables-source-card" data-source="xml">
    <div class="atables-source-icon"></>></div>
    <h3><?php _e( 'XML File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Import XML data', 'a-tables-charts-lite' ); ?></p>
</div>
```

**Replace with:**
```php
<div class="atables-source-card atables-pro-feature" data-source="xml" data-feature-name="XML Import" data-feature-description="Import data from XML files">
    <div class="atables-pro-badge">PRO</div>
    <div class="atables-source-icon"></>></div>
    <h3><?php _e( 'XML File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Import XML data', 'a-tables-charts-lite' ); ?></p>
</div>
```

**Save the file!** ✅

---

## 🎯 PHASE 7: UPDATE PLUGIN.PHP MENU

### Step 7.1: Add Upgrade Menu Item

**File:** `src/modules/core/Plugin.php`

**Find the `register_admin_menu()` method.** Look for where menu items are added.

**After the existing menu items, ADD this new upgrade menu item:**

```php
// Add Upgrade menu item (with orange sparkle)
add_submenu_page(
    'a-tables-charts-lite',
    __( 'Upgrade to PRO', 'a-tables-charts-lite' ),
    '<span style="color:#f18500">✨ ' . __( 'Upgrade to PRO', 'a-tables-charts-lite' ) . '</span>',
    'manage_options',
    'atables-lite-upgrade',
    array( $this, 'render_upgrade_page' )
);
```

### Step 7.2: Add Upgrade Page Render Method

**Still in Plugin.php, find the section with render methods** (like `render_dashboard_page`, `render_create_table_page`, etc.)

**Add this new method:**

```php
/**
 * Render upgrade page
 */
public function render_upgrade_page() {
    require_once ATABLES_LITE_PLUGIN_DIR . 'src/modules/core/views/upgrade.php';
}
```

**Save the file!** ✅

---

## 📜 PHASE 8: ENQUEUE UPGRADE JAVASCRIPT

### Step 8.1: Add Upgrade Script

**File:** `src/modules/core/Plugin.php`

**Find the `enqueue_admin_assets()` method.** It should have wp_enqueue_script calls.

**Add this line to enqueue the upgrade JavaScript:**

```php
// Enqueue upgrade modal script
wp_enqueue_script(
    'atables-admin-upgrade',
    ATABLES_LITE_PLUGIN_URL . 'assets/js/admin-upgrade.js',
    array( 'jquery', 'atables-modal' ),
    ATABLES_LITE_VERSION,
    true
);
```

**Save the file!** ✅

---

## 📋 PHASE 9: UPDATE README.TXT

### Step 9.1: Move readme-lite.txt

**Actions:**
1. ✅ Find `readme-lite.txt` in the PRO plugin root (we created it earlier)
2. ✅ **Copy** it to `a-tables-charts-lite/` folder
3. ✅ Rename it to `readme.txt` (remove the -lite suffix)

**Result:** You should have `a-tables-charts-lite/readme.txt`

---

## ⚙️ PHASE 10: UPDATE COMPOSER/AUTOLOADER (If Needed)

### Step 10.1: Check composer.json

**File:** `composer.json` (in root of lite plugin)

**Find the autoload section:**
```json
"autoload": {
    "psr-4": {
        "ATablesCharts\\": "src/"
    }
}
```

**Replace with:**
```json
"autoload": {
    "psr-4": {
        "ATablesChartsLite\\": "src/"
    }
}
```

### Step 10.2: Regenerate Autoloader

**If you have Composer installed:**
1. ✅ Open terminal/command prompt
2. ✅ Navigate to `a-tables-charts-lite` folder
3. ✅ Run: `composer dump-autoload`

**If you DON'T have Composer:**
- The plugin should still work, but you may need to manually update the autoloader file later if issues arise

---

## 🧪 PHASE 11: TESTING

### Step 11.1: Activate LITE Plugin

1. ✅ Go to WordPress admin
2. ✅ Navigate to Plugins
3. ✅ **Deactivate** the PRO version (if active)
4. ✅ Find "A-Tables & Charts Lite"
5. ✅ Click "Activate"

### Step 11.2: Check for Errors

**Look for:**
- ✅ No PHP errors on activation
- ✅ Plugin menu appears in sidebar
- ✅ "Upgrade to PRO" menu item shows (with orange sparkle ✨)

### Step 11.3: Test Create Table Page

1. ✅ Go to A-Tables & Charts → Create Table
2. ✅ Verify CSV card is clickable
3. ✅ Verify JSON, Excel, XML cards show PRO badge
4. ✅ Click on a PRO card - should show upgrade modal

### Step 11.4: Test CSV Import

1. ✅ Click CSV card
2. ✅ Upload a CSV file
3. ✅ Import the file
4. ✅ Save the table
5. ✅ Verify table appears in dashboard

### Step 11.5: Test Upgrade Page

1. ✅ Click "✨ Upgrade to PRO" in menu
2. ✅ Verify pricing cards display
3. ✅ Verify features grid displays
4. ✅ Test "View Pricing" buttons

### Step 11.6: Test Frontend Display

1. ✅ Create a page/post
2. ✅ Add shortcode: `[atable id="1"]` (use your table ID)
3. ✅ View the page
4. ✅ Verify table displays correctly

---

## ✅ COMPLETION CHECKLIST

### Core Files Updated:
- [ ] Plugin folder copied and renamed to `a-tables-charts-lite`
- [ ] Main file renamed to `a-tables-charts-lite.php`
- [ ] Plugin header updated (name, description, version, text domain)
- [ ] Constants updated (added _LITE suffix)
- [ ] `ATABLES_LITE_IS_PRO` constant added (set to false)
- [ ] Features.php `is_pro()` method returns false

### Modules Deleted:
- [ ] `src/modules/charts/` folder deleted
- [ ] `src/modules/database/` folder deleted
- [ ] `src/modules/import/` folder deleted
- [ ] `src/modules/dataSources/parsers/JsonParser.php` deleted
- [ ] `src/modules/export/exporters/ExcelExporter.php` deleted
- [ ] `src/modules/export/exporters/PdfExporter.php` deleted
- [ ] `src/modules/export/services/ExcelExportService.php` deleted
- [ ] `src/modules/export/services/PdfExportService.php` deleted

### Global Replacements Done:
- [ ] Text domain: `'a-tables-charts'` → `'a-tables-charts-lite'`
- [ ] Namespace: `ATablesCharts\\` → `ATablesChartsLite\\`
- [ ] Use statements: `use ATablesCharts\\` → `use ATablesChartsLite\\`
- [ ] All ATABLES_ constants updated to ATABLES_LITE_

### UI Updates:
- [ ] PRO badges added to JSON card in create-table.php
- [ ] PRO badges added to Excel card in create-table.php
- [ ] PRO badges added to XML card in create-table.php
- [ ] Upgrade menu item added to Plugin.php
- [ ] Upgrade page render method added
- [ ] Upgrade JavaScript enqueued

### Files Added:
- [ ] readme.txt moved to lite plugin root
- [ ] admin-upgrade.js is in assets/js/
- [ ] upgrade.php is in src/modules/core/views/
- [ ] upgrade-notice.php is in src/modules/core/views/components/

### Testing Complete:
- [ ] Plugin activates without errors
- [ ] Menu shows correctly with upgrade item
- [ ] Create table page loads
- [ ] CSV import works
- [ ] PRO features show upgrade modal
- [ ] Upgrade page displays
- [ ] Frontend table displays

---

## 🎉 CONGRATULATIONS!

If all checkboxes are checked, your LITE version is ready!

## 🚀 NEXT STEPS

1. **Test Thoroughly** - Create several tables, test all FREE features
2. **Take Screenshots** - For WordPress.org submission (1920×1080)
3. **Create Assets** - Banner (1544×500) and Icon (256×256)
4. **Prepare for Submission** - Review WordPress.org guidelines
5. **Submit to WordPress.org** - Upload your plugin!

---

## ⚠️ TROUBLESHOOTING

### Common Issues:

**Issue:** PHP Fatal Error about class not found
**Solution:** Check namespace replacements, regenerate autoloader

**Issue:** Upgrade modal doesn't show
**Solution:** Check that admin-upgrade.js is enqueued and no JS errors in console

**Issue:** PRO features are accessible
**Solution:** Check Features.php is_pro() returns false, check ATABLES_LITE_IS_PRO constant

**Issue:** Text domain not working
**Solution:** Make sure all `'a-tables-charts'` are replaced with `'a-tables-charts-lite'`

**Issue:** Constants not defined errors
**Solution:** Check all ATABLES_ constants have _LITE added

---

## 📞 NEED HELP?

If you encounter any issues:

1. **Check Console** - Browser console for JavaScript errors
2. **Enable Debug** - In wp-config.php: `define('WP_DEBUG', true);`
3. **Check Error Log** - Look in wp-content/debug.log
4. **Verify Files** - Make sure all files are in correct locations

---

**Good luck! You've got this! 🎯**

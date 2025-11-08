# 📸 VISUAL REFERENCE GUIDE - Before & After Examples

Quick reference showing exactly what to change in key files.

---

## 📄 FILE: a-tables-charts-lite.php (Main Plugin File)

### BEFORE (PRO):
```php
/**
 * Plugin Name: A-Tables & Charts
 * Plugin URI: https://a-tables-charts.com
 * Description: Create beautiful, responsive tables and interactive charts from CSV, JSON, Excel, and XML files.
 * Version: 1.0.4
 * Text Domain: a-tables-charts
 */

define( 'ATABLES_VERSION', '1.0.4' );
define( 'ATABLES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ATABLES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ATABLES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'ATABLES_SLUG', 'a-tables-charts' );

use ATablesCharts\Core\Plugin;
```

### AFTER (LITE):
```php
/**
 * Plugin Name: A-Tables & Charts Lite
 * Plugin URI: https://a-tables-charts.com
 * Description: Create beautiful, responsive tables from CSV files. Upgrade to PRO for Excel, JSON, XML import, charts, and more!
 * Version: 1.0.0
 * Text Domain: a-tables-charts-lite
 */

define( 'ATABLES_LITE_VERSION', '1.0.0' );
define( 'ATABLES_LITE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ATABLES_LITE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ATABLES_LITE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'ATABLES_LITE_SLUG', 'a-tables-charts-lite' );
define( 'ATABLES_LITE_IS_PRO', false ); // ← NEW!

use ATablesChartsLite\Core\Plugin; // ← Changed namespace
```

**Key Changes:**
- ✅ Plugin Name: Added "Lite"
- ✅ Description: Mentions CSV only + upgrade
- ✅ Version: Reset to 1.0.0
- ✅ Text Domain: Added "-lite"
- ✅ All constants: Added "_LITE"
- ✅ New constant: `ATABLES_LITE_IS_PRO` = false
- ✅ Namespace: Changed to `ATablesChartsLite`

---

## 🔧 FILE: src/shared/utils/Features.php

### BEFORE (PRO):
```php
public static function is_pro() {
    // This will be different in lite version
    // PRO version: return true
    // LITE version: return false
    return true; // PRO version
}
```

### AFTER (LITE):
```php
public static function is_pro() {
    // LITE version always returns false
    return false;
}
```

**Key Change:**
- ✅ Return value: `true` → `false`

---

## 🎨 FILE: src/modules/core/views/create-table.php

### BEFORE (PRO) - JSON Card:
```php
<div class="atables-source-card" data-source="json">
    <div class="atables-source-icon">{ }</div>
    <h3><?php _e( 'JSON File', 'a-tables-charts' ); ?></h3>
    <p><?php _e( 'Import JSON data', 'a-tables-charts' ); ?></p>
</div>
```

### AFTER (LITE) - JSON Card:
```php
<div class="atables-source-card atables-pro-feature" data-source="json" data-feature-name="JSON Import" data-feature-description="Import data from JSON files">
    <div class="atables-pro-badge">PRO</div>
    <div class="atables-source-icon">{ }</div>
    <h3><?php _e( 'JSON File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Import JSON data', 'a-tables-charts-lite' ); ?></p>
</div>
```

**Key Changes:**
- ✅ Added class: `atables-pro-feature`
- ✅ Added data attributes: `data-feature-name`, `data-feature-description`
- ✅ Added PRO badge: `<div class="atables-pro-badge">PRO</div>`
- ✅ Updated text domain: `'a-tables-charts'` → `'a-tables-charts-lite'`

### BEFORE (PRO) - Excel Card:
```php
<div class="atables-source-card" data-source="excel">
    <div class="atables-source-icon">📊</div>
    <h3><?php _e( 'Excel File', 'a-tables-charts' ); ?></h3>
    <p><?php _e( 'Import XLS or XLSX files', 'a-tables-charts' ); ?></p>
</div>
```

### AFTER (LITE) - Excel Card:
```php
<div class="atables-source-card atables-pro-feature" data-source="excel" data-feature-name="Excel Import" data-feature-description="Import data from Excel files (XLS, XLSX)">
    <div class="atables-pro-badge">PRO</div>
    <div class="atables-source-icon">📊</div>
    <h3><?php _e( 'Excel File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Import XLS or XLSX files', 'a-tables-charts-lite' ); ?></p>
</div>
```

### BEFORE (PRO) - XML Card:
```php
<div class="atables-source-card" data-source="xml">
    <div class="atables-source-icon"></>></div>
    <h3><?php _e( 'XML File', 'a-tables-charts' ); ?></h3>
    <p><?php _e( 'Import XML data', 'a-tables-charts' ); ?></p>
</div>
```

### AFTER (LITE) - XML Card:
```php
<div class="atables-source-card atables-pro-feature" data-source="xml" data-feature-name="XML Import" data-feature-description="Import data from XML files">
    <div class="atables-pro-badge">PRO</div>
    <div class="atables-source-icon"></>></div>
    <h3><?php _e( 'XML File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Import XML data', 'a-tables-charts-lite' ); ?></p>
</div>
```

### CSV Card (Keep as-is, just update text domain):
```php
<div class="atables-source-card" data-source="csv">
    <div class="atables-source-icon">📄</div>
    <h3><?php _e( 'CSV File', 'a-tables-charts-lite' ); ?></h3>
    <p><?php _e( 'Upload a CSV or TXT file', 'a-tables-charts-lite' ); ?></p>
</div>
```

---

## 🎯 FILE: src/modules/core/Plugin.php

### ADD - Upgrade Menu Item (in register_admin_menu method):

**Add this AFTER existing menu items:**

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

### ADD - Upgrade Page Render Method:

**Add this AFTER other render methods (like render_dashboard_page):**

```php
/**
 * Render upgrade page
 */
public function render_upgrade_page() {
    require_once ATABLES_LITE_PLUGIN_DIR . 'src/modules/core/views/upgrade.php';
}
```

### ADD - Enqueue Upgrade Script (in enqueue_admin_assets method):

**Add this AFTER other script enqueues:**

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

---

## 📦 FILE: composer.json

### BEFORE (PRO):
```json
{
    "name": "atables/charts",
    "description": "A-Tables & Charts",
    "type": "wordpress-plugin",
    "autoload": {
        "psr-4": {
            "ATablesCharts\\": "src/"
        }
    }
}
```

### AFTER (LITE):
```json
{
    "name": "atables/charts-lite",
    "description": "A-Tables & Charts Lite",
    "type": "wordpress-plugin",
    "autoload": {
        "psr-4": {
            "ATablesChartsLite\\": "src/"
        }
    }
}
```

**Key Changes:**
- ✅ Name: Added "-lite"
- ✅ Description: Added "Lite"
- ✅ Namespace: `ATablesCharts\\` → `ATablesChartsLite\\`

---

## 🗂️ FOLDER STRUCTURE COMPARISON

### PRO VERSION:
```
a-tables-charts/
├── assets/
├── src/
│   ├── modules/
│   │   ├── cache/           ✅ Keep
│   │   ├── charts/          ❌ DELETE
│   │   ├── core/            ✅ Keep
│   │   ├── database/        ❌ DELETE
│   │   ├── dataSources/     ✅ Keep (but delete JsonParser.php)
│   │   ├── export/          ✅ Keep (but delete Excel/PDF)
│   │   ├── filters/         ✅ Keep
│   │   ├── frontend/        ✅ Keep
│   │   ├── import/          ❌ DELETE
│   │   ├── settings/        ✅ Keep
│   │   └── tables/          ✅ Keep
│   └── shared/              ✅ Keep
├── vendor/
└── a-tables-charts.php
```

### LITE VERSION:
```
a-tables-charts-lite/
├── assets/
│   └── js/
│       └── admin-upgrade.js    ← NEW!
├── src/
│   ├── modules/
│   │   ├── cache/           ✅
│   │   ├── core/            ✅
│   │   │   └── views/
│   │   │       ├── components/
│   │   │       │   └── upgrade-notice.php  ← NEW!
│   │   │       └── upgrade.php             ← NEW!
│   │   ├── dataSources/     ✅ (only CsvParser)
│   │   ├── export/          ✅ (only CSV)
│   │   ├── filters/         ✅
│   │   ├── frontend/        ✅
│   │   ├── settings/        ✅
│   │   └── tables/          ✅
│   └── shared/
│       └── utils/
│           └── Features.php    ← Modified
├── vendor/
├── readme.txt               ← NEW!
└── a-tables-charts-lite.php ← Renamed & Modified
```

---

## 🔍 NAMESPACE EXAMPLES

### BEFORE (PRO):
```php
namespace ATablesCharts\Core;
namespace ATablesCharts\Modules\Tables;
namespace ATablesCharts\Shared\Utils;

use ATablesCharts\Core\Plugin;
use ATablesCharts\Modules\Tables\TableService;
use ATablesCharts\Shared\Utils\Helpers;
```

### AFTER (LITE):
```php
namespace ATablesChartsLite\Core;
namespace ATablesChartsLite\Modules\Tables;
namespace ATablesChartsLite\Shared\Utils;

use ATablesChartsLite\Core\Plugin;
use ATablesChartsLite\Modules\Tables\TableService;
use ATablesChartsLite\Shared\Utils\Helpers;
```

**Pattern:**
- Every `ATablesCharts\` becomes `ATablesChartsLite\`
- This applies to BOTH `namespace` and `use` statements

---

## 📝 TEXT DOMAIN EXAMPLES

### BEFORE (PRO):
```php
__( 'Create Table', 'a-tables-charts' )
_e( 'Dashboard', 'a-tables-charts' )
esc_html__( 'Import', 'a-tables-charts' )
```

### AFTER (LITE):
```php
__( 'Create Table', 'a-tables-charts-lite' )
_e( 'Dashboard', 'a-tables-charts-lite' )
esc_html__( 'Import', 'a-tables-charts-lite' )
```

**Pattern:**
- Every `'a-tables-charts'` becomes `'a-tables-charts-lite'`
- Always include the single quotes in your search

---

## 🎨 VISUAL RESULT

### Create Table Page - Before & After:

**BEFORE (PRO):**
```
┌─────────────────────────────────────────┐
│  CSV File        JSON File        Excel  │
│  📄              { }              📊    │
│  Upload CSV      Import JSON      Import │
│                                          │
│  [All clickable and functional]          │
└─────────────────────────────────────────┘
```

**AFTER (LITE):**
```
┌─────────────────────────────────────────┐
│  CSV File        JSON File 🔒     Excel 🔒│
│  📄              { }     PRO      📊  PRO│
│  Upload CSV      Import JSON      Import │
│  [Clickable]     [Shows Modal]    [Shows Modal]
└─────────────────────────────────────────┘
```

### Admin Menu - After:
```
A-Tables & Charts
├── Dashboard
├── Create Table
├── All Tables
├── Settings
└── ✨ Upgrade to PRO  ← Orange sparkle!
```

---

## ✅ VERIFICATION POINTS

After making changes, verify these visually:

### Admin Area:
1. **Menu:** Orange sparkle on "Upgrade to PRO"
2. **Create Table:** PRO badges on JSON/Excel/XML
3. **Click PRO Card:** Modal pops up with pricing info
4. **Upgrade Page:** Pricing cards display nicely

### Frontend:
1. **Shortcode:** `[atable id="1"]` displays table
2. **Responsive:** Table works on mobile
3. **Features:** Search, sort, pagination work

### Console:
1. **No JavaScript Errors:** F12 → Console tab
2. **No PHP Errors:** Check debug.log
3. **Assets Load:** F12 → Network tab

---

## 🎯 QUICK VISUAL CHECKLIST

Look for these visual indicators of success:

- ✅ Plugin name says "Lite" in plugins list
- ✅ Version shows 1.0.0
- ✅ Menu has orange ✨ on upgrade item
- ✅ PRO badges visible on cards
- ✅ PRO cards slightly faded (opacity: 0.75)
- ✅ Clicking PRO card shows beautiful modal
- ✅ Upgrade page has gradient header
- ✅ CSV import still works perfectly

---

**Use this guide as a quick reference while following the step-by-step guide!** 📚

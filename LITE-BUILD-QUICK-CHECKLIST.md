# ✅ LITE VERSION BUILD CHECKLIST

Quick reference checklist for building A-Tables & Charts Lite

---

## 📁 FOLDER SETUP
- [ ] Copy `a-tables-charts` folder
- [ ] Rename copy to `a-tables-charts-lite`
- [ ] Rename `a-tables-charts.php` to `a-tables-charts-lite.php`

---

## 📝 MAIN FILE UPDATES (a-tables-charts-lite.php)
- [ ] Update plugin name to "A-Tables & Charts Lite"
- [ ] Update description (mention CSV only, upgrade for more)
- [ ] Change version to 1.0.0
- [ ] Change text domain to 'a-tables-charts-lite'
- [ ] Update all constants (add _LITE suffix)
- [ ] Add `ATABLES_LITE_IS_PRO` constant = false
- [ ] Update namespace to `ATablesChartsLite`

---

## 🔧 FEATURES.PHP UPDATE
- [ ] Open `src/shared/utils/Features.php`
- [ ] Change `is_pro()` to return `false`

---

## 🗑️ DELETE PRO MODULES
- [ ] Delete `src/modules/charts/` (entire folder)
- [ ] Delete `src/modules/database/` (entire folder)
- [ ] Delete `src/modules/import/` (entire folder)
- [ ] Delete `src/modules/dataSources/parsers/JsonParser.php`
- [ ] Delete `src/modules/export/exporters/ExcelExporter.php`
- [ ] Delete `src/modules/export/exporters/PdfExporter.php`
- [ ] Delete `src/modules/export/services/ExcelExportService.php`
- [ ] Delete `src/modules/export/services/PdfExportService.php`

---

## 🔍 GLOBAL FIND & REPLACE (in order)
- [ ] `'a-tables-charts'` → `'a-tables-charts-lite'` (~200-300 hits)
- [ ] `namespace ATablesCharts\` → `namespace ATablesChartsLite\`
- [ ] `use ATablesCharts\` → `use ATablesChartsLite\`
- [ ] `ATABLES_VERSION` → `ATABLES_LITE_VERSION`
- [ ] `ATABLES_PLUGIN_DIR` → `ATABLES_LITE_PLUGIN_DIR`
- [ ] `ATABLES_PLUGIN_URL` → `ATABLES_LITE_PLUGIN_URL`
- [ ] `ATABLES_PLUGIN_BASENAME` → `ATABLES_LITE_PLUGIN_BASENAME`
- [ ] `ATABLES_SLUG` → `ATABLES_LITE_SLUG`

---

## 🎨 ADD PRO BADGES (create-table.php)
- [ ] Add PRO badge to JSON card
- [ ] Add PRO badge to Excel card
- [ ] Add PRO badge to XML card
- [ ] Add `atables-pro-feature` class to each
- [ ] Add data attributes (feature-name, feature-description)

---

## 🎯 PLUGIN.PHP UPDATES
- [ ] Add upgrade menu item (with orange sparkle ✨)
- [ ] Add `render_upgrade_page()` method
- [ ] Enqueue `admin-upgrade.js` script

---

## 📋 README & COMPOSER
- [ ] Copy `readme-lite.txt` to lite plugin root
- [ ] Rename to `readme.txt`
- [ ] Update composer.json namespace (if exists)
- [ ] Run `composer dump-autoload` (if Composer installed)

---

## 🧪 TESTING
- [ ] Deactivate PRO plugin
- [ ] Activate LITE plugin
- [ ] No PHP errors on activation
- [ ] Menu appears with upgrade item
- [ ] CSV import works
- [ ] PRO features show upgrade modal
- [ ] Upgrade page displays
- [ ] Frontend table displays
- [ ] Create 2-3 test tables successfully

---

## 📊 FINAL VERIFICATION
- [ ] All FREE features work
- [ ] All PRO features blocked with modals
- [ ] No errors in browser console
- [ ] No PHP errors in debug.log
- [ ] Shortcode works on frontend
- [ ] DataTables loads correctly

---

## 🚀 READY FOR WORDPRESS.ORG
- [ ] Plugin tested thoroughly
- [ ] Screenshots ready (1920×1080)
- [ ] Banner created (1544×500)
- [ ] Icon created (256×256)
- [ ] readme.txt complete
- [ ] No security issues
- [ ] Code follows WordPress standards

---

**Status:** [ ] Not Started  [ ] In Progress  [ ] Complete ✅

**Notes:**
_______________________________________________________
_______________________________________________________
_______________________________________________________

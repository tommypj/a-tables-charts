# Manual Dependency Loading Analysis
**Issue:** Manual require_once statements circumvent PSR-4 autoloading
**Severity:** LOW
**Status:** Requires Review

---

## Current State

### PSR-4 Autoloading Configuration
**File:** `composer.json`

```json
"autoload": {
  "psr-4": {
    "ATablesCharts\\": "src/"
  }
}
```

✅ PSR-4 autoloading is properly configured
✅ Namespace: `ATablesCharts\` maps to `src/` directory

---

## Files with Manual require_once for Module Loading

### Controller/Service Files (Can be optimized):

1. **src/modules/tables/controllers/TableController.php:584**
   ```php
   require_once ATABLES_PLUGIN_DIR . 'src/modules/cache/index.php';
   $cache_service = new \ATablesCharts\Cache\Services\CacheService();
   ```
   **Fix:** Add `use ATablesCharts\Cache\Services\CacheService;` at top, remove require_once

2. **src/modules/core/SettingsController.php:193, 229**
   ```php
   require_once ATABLES_PLUGIN_DIR . 'src/modules/cache/index.php';
   $cache_service = new \ATablesCharts\Cache\Services\CacheService();
   ```
   **Fix:** Same as above

3. **src/modules/core/TableViewAjaxController.php:55, 138, 251, 263**
   ```php
   require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';
   require_once ATABLES_PLUGIN_DIR . 'src/modules/filters/index.php';
   require_once ATABLES_PLUGIN_DIR . 'src/modules/cache/index.php';
   ```
   **Fix:** Add proper use statements, remove require_once

4. **src/modules/import/controllers/ImportController.php:414**
   ```php
   require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';
   ```
   **Fix:** Add proper use statements

5. **src/modules/templates/TemplateService.php:145**
   ```php
   require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';
   ```
   **Fix:** Add proper use statements

### View Files (May need manual loading):

View files (edit-table.php, dashboard.php, etc.) may require manual loading because:
- They are not classes/namespaces
- They execute in a specific context
- They may need to load services/repositories directly

**Decision needed:** View files might be exempt from this optimization.

### Bootstrap Files (Intentional):

**src/modules/core/Plugin.php** - Main plugin bootstrap
- Lines 254-397: Manual loading of all modules
- **Rationale:** This is the main plugin initialization
- **Status:** ✅ Intentional and appropriate

---

## Example Fix

### Before (TableController.php):
```php
<?php
namespace ATablesCharts\Tables\Controllers;

use ATablesCharts\Tables\Services\TableService;
use ATablesCharts\Shared\Utils\Logger;

class TableController {

    private function clear_table_cache( $table_id ) {
        try {
            require_once ATABLES_PLUGIN_DIR . 'src/modules/cache/index.php';
            $cache_service = new \ATablesCharts\Cache\Services\CacheService();

            $cache_service->delete( 'table_all_data_' . $table_id );
        } catch ( \Exception $e ) {
            // Error handling
        }
    }
}
```

### After (TableController.php):
```php
<?php
namespace ATablesCharts\Tables\Controllers;

use ATablesCharts\Tables\Services\TableService;
use ATablesCharts\Shared\Utils\Logger;
use ATablesCharts\Cache\Services\CacheService;  // ← ADDED

class TableController {

    private function clear_table_cache( $table_id ) {
        try {
            // require_once REMOVED - PSR-4 autoloader handles it
            $cache_service = new CacheService();  // ← Simplified (no leading backslash needed)

            $cache_service->delete( 'table_all_data_' . $table_id );
        } catch ( \Exception $e ) {
            // Error handling
        }
    }
}
```

---

## Benefits of Fixing

1. **Cleaner Code**: Explicit dependencies via use statements
2. **Better IDE Support**: Proper autocompletion and navigation
3. **Follows PSR-4 Standard**: Industry best practice
4. **Easier Refactoring**: Dependencies visible at file top
5. **No Performance Impact**: Autoloader is efficient

---

## Module index.php Files

These files exist to bootstrap modules when loaded manually:

### Example: src/modules/cache/index.php
```php
// Load Cache Service.
require_once __DIR__ . '/services/CacheService.php';

// Load Cache Controller.
require_once __DIR__ . '/controllers/CacheController.php';
```

**When PSR-4 is used:** These index.php files become unnecessary for class loading. The autoloader will load classes on-demand.

**Current usage:**
- ✅ Loaded once in Plugin.php during initialization
- ❌ Unnecessarily loaded again in various controllers

---

## Recommendation

### Phase 1: Quick Fix (Controllers & Services)
Remove manual require_once from:
- ✅ TableController.php:584
- ✅ SettingsController.php:193, 229
- ✅ TableViewAjaxController.php (multiple)
- ✅ ImportController.php:414
- ✅ TemplateService.php:145

Add proper `use` statements instead.

### Phase 2: Review (View Files)
Evaluate if view files can also use autoloading or if manual loading is appropriate.

### Phase 3: Cleanup (index.php files)
After Phase 1-2, consider if module index.php files are still needed or can be simplified.

---

## Files to Modify

| File | Lines | Class to Import |
|------|-------|-----------------|
| src/modules/tables/controllers/TableController.php | 584 | `use ATablesCharts\Cache\Services\CacheService;` |
| src/modules/core/SettingsController.php | 193, 229 | `use ATablesCharts\Cache\Services\CacheService;` |
| src/modules/core/TableViewAjaxController.php | 55, 138, 251, 263 | Multiple (see file) |
| src/modules/import/controllers/ImportController.php | 414 | `use ATablesCharts\Tables\Services\TableService;` |
| src/modules/templates/TemplateService.php | 145 | `use ATablesCharts\Tables\Services\TableService;` |

---

## Testing

After fixing, verify:
1. ✅ All admin pages load correctly
2. ✅ AJAX operations work (table CRUD, settings, etc.)
3. ✅ Cache clearing functions properly
4. ✅ Import/export operations function
5. ✅ Templates load correctly

---

## Impact Assessment

**Risk Level:** LOW
- Changes are straightforward
- Autoloader is already configured and working
- Easy to revert if issues arise

**Testing Required:** Moderate
- Test all affected features
- Verify no ClassNotFound errors
- Check error logs after deployment

---

## Status: Ready for Implementation

All necessary information gathered. Ready to proceed with fixes if requested.

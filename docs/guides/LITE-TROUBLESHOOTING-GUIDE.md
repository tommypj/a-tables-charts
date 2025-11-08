# 🔧 TROUBLESHOOTING GUIDE - A-Tables & Charts Lite

Common issues and solutions when building the LITE version.

---

## ⚠️ ACTIVATION ERRORS

### Error: "Fatal error: Class 'ATablesCharts\Core\Plugin' not found"

**Cause:** Namespace wasn't updated properly

**Solution:**
1. Open `a-tables-charts-lite.php`
2. Find: `use ATablesCharts\Core\Plugin;`
3. Replace with: `use ATablesChartsLite\Core\Plugin;`
4. Deactivate and reactivate plugin

### Error: "Fatal error: Uncaught Error: Undefined constant ATABLES_PLUGIN_DIR"

**Cause:** Constants weren't updated

**Solution:**
1. Open `a-tables-charts-lite.php`
2. Make sure ALL constants have `_LITE` added:
   - `ATABLES_LITE_VERSION`
   - `ATABLES_LITE_PLUGIN_DIR`
   - `ATABLES_LITE_PLUGIN_URL`
   - etc.
3. Do global find/replace for any remaining `ATABLES_` (without LITE)

### Error: "Plugin file does not exist"

**Cause:** Main file not renamed correctly

**Solution:**
1. Make sure file is named exactly: `a-tables-charts-lite.php`
2. Check it's in the root of `a-tables-charts-lite/` folder
3. Deactivate and reactivate

---

## 🎨 UPGRADE MODAL ISSUES

### Issue: Clicking PRO feature doesn't show modal

**Cause:** JavaScript not loaded or error in console

**Solution:**
1. Open browser console (F12)
2. Check for JavaScript errors
3. Verify `admin-upgrade.js` is enqueued in Plugin.php
4. Check Dependencies: Modal script should load before upgrade script
5. Clear browser cache and hard refresh (Ctrl+Shift+R)

### Issue: Modal shows but clicking "View Pricing" doesn't open URL

**Cause:** URL might be blocked or JavaScript error

**Solution:**
1. Check browser console for errors
2. Verify upgrade URL in Features.php
3. Test URL manually: `https://a-tables-charts.com/pricing/`
4. Check popup blocker settings

### Issue: PRO badge not showing on cards

**Cause:** CSS not loaded or class not applied

**Solution:**
1. Verify you added `atables-pro-feature` class to cards
2. Check that `<div class="atables-pro-badge">PRO</div>` exists inside card
3. Clear browser cache
4. Check that admin CSS is loading
5. Inspect element to verify classes are present

---

## 📊 TABLE IMPORT/DISPLAY ISSUES

### Issue: CSV import fails

**Cause:** Parser not found or file too large

**Solution:**
1. Verify `CsvParser.php` exists in `src/modules/dataSources/parsers/`
2. Check file size (max 10MB)
3. Try smaller CSV file first
4. Check browser console and PHP error log

### Issue: Table doesn't display on frontend

**Cause:** Shortcode issue or DataTables not loading

**Solution:**
1. Verify shortcode: `[atable id="1"]` (use correct ID)
2. Open browser console - check for JS errors
3. Verify DataTables CSS/JS are enqueued
4. Check that table exists in database
5. Try different theme to rule out conflicts

### Issue: "Table not found" error

**Cause:** Wrong table ID or table doesn't exist

**Solution:**
1. Go to A-Tables & Charts dashboard
2. Verify table exists and note the ID
3. Use correct ID in shortcode
4. Check database table `wp_atables_tables`

---

## 🔍 TEXT DOMAIN ISSUES

### Issue: Text not translating or showing wrong domain

**Cause:** Text domain not updated everywhere

**Solution:**
1. Do global search for `'a-tables-charts'` (with quotes)
2. Replace ALL with `'a-tables-charts-lite'`
3. Check in:
   - PHP files (all)
   - JavaScript files (if any translation strings)
4. Clear cache and test again

### Issue: Some strings still show old text domain

**Cause:** Incomplete find/replace

**Solution:**
1. Use VS Code or similar with "Find in Files"
2. Search exact string: `'a-tables-charts'`
3. Exclude nothing - search ALL files
4. Replace ALL occurrences
5. Verify with second search

---

## 🎯 MENU ISSUES

### Issue: Upgrade menu item doesn't show

**Cause:** Menu not registered or method missing

**Solution:**
1. Open `src/modules/core/Plugin.php`
2. Find `register_admin_menu()` method
3. Verify upgrade menu item code is present
4. Verify `render_upgrade_page()` method exists
5. Deactivate/reactivate plugin

### Issue: Upgrade page shows blank

**Cause:** View file not found or error in view

**Solution:**
1. Verify `src/modules/core/views/upgrade.php` exists
2. Check PHP error log for errors
3. Make sure Features class is loaded
4. Check browser console for JS errors

### Issue: Menu shows but orange sparkle (✨) doesn't appear

**Cause:** HTML being escaped or CSS conflict

**Solution:**
1. Check menu item HTML in Plugin.php
2. Verify: `'<span style="color:#f18500">✨ ' . __( 'Upgrade to PRO' ) . '</span>'`
3. Some WordPress setups escape HTML - this is OK, sparkle still works
4. Try different emoji if needed

---

## 💾 EXPORT ISSUES

### Issue: CSV export doesn't work

**Cause:** Exporter deleted or not found

**Solution:**
1. Verify `CSVExporter.php` exists in `src/modules/export/exporters/`
2. Verify `CSVExportService.php` exists in `src/modules/export/services/`
3. Check Export controller is loaded
4. Test with small table first

### Issue: Excel/PDF export buttons show in LITE

**Cause:** Export buttons not removed or hidden

**Solution:**
1. These should be hidden via Features::is_available() check
2. Verify Features.php `is_pro()` returns false
3. Check export view template filters PRO features
4. May need to add conditional checks in export views

---

## 🔄 AUTOLOADER ISSUES

### Error: "Class not found" for various classes

**Cause:** Autoloader not updated for new namespace

**Solution:**
1. Open `composer.json` in plugin root
2. Update autoload section:
   ```json
   "autoload": {
       "psr-4": {
           "ATablesChartsLite\\": "src/"
       }
   }
   ```
3. Run: `composer dump-autoload`
4. If no Composer, manually update `vendor/autoload.php`

### Issue: Autoloader keeps loading old namespace

**Cause:** Cached autoloader files

**Solution:**
1. Delete `vendor/` folder
2. Run `composer install`
3. Or manually clear autoload cache
4. Deactivate/reactivate plugin

---

## 🗄️ DATABASE ISSUES

### Issue: Tables not created on activation

**Cause:** Activation hook not firing or DB error

**Solution:**
1. Check `src/modules/core/Activator.php` exists
2. Verify activation hook in main plugin file
3. Check database for tables:
   - `wp_atables_tables`
   - `wp_atables_charts`
   - `wp_atables_filter_presets`
4. Try manual deactivate/activate

### Issue: "Database error" when saving table

**Cause:** Table structure mismatch or permissions

**Solution:**
1. Check WordPress database user has CREATE/ALTER permissions
2. Verify database tables exist
3. Check error log for specific SQL error
4. Try creating table manually via phpMyAdmin

---

## 🎨 STYLING ISSUES

### Issue: PRO badges don't look right

**Cause:** CSS not loaded or conflicts

**Solution:**
1. Verify `admin-upgrade.js` injects CSS (it does - built-in)
2. Check browser console for CSS errors
3. Clear browser cache
4. Inspect element to see if styles are applied
5. Check for CSS conflicts with theme

### Issue: Upgrade page looks broken

**Cause:** CSS not loading or theme conflict

**Solution:**
1. Verify `upgrade.php` includes its CSS
2. Test with default WordPress theme (Twenty Twenty-Four)
3. Check for JavaScript errors blocking rendering
4. Ensure proper HTML structure in upgrade.php

---

## 🧪 TESTING TIPS

### Before Final Testing:

1. **Clear All Caches:**
   - Browser cache (Ctrl+Shift+Delete)
   - WordPress cache (if using cache plugin)
   - Opcache (if PHP opcache enabled)

2. **Test in Incognito/Private Window:**
   - Rules out browser extension conflicts
   - Fresh JavaScript/CSS load

3. **Check Console:**
   - Browser console (F12) - check for JS errors
   - Network tab - verify assets loading
   - PHP error log - check for PHP errors

4. **Test Order:**
   - Fresh activation
   - Create table (CSV only)
   - View table in dashboard
   - Display on frontend
   - Click PRO features
   - Test upgrade page

---

## 📝 DEBUGGING CHECKLIST

When something doesn't work:

- [ ] Clear all caches
- [ ] Check browser console (F12)
- [ ] Check PHP error log
- [ ] Verify file exists
- [ ] Check permissions
- [ ] Test with default theme
- [ ] Deactivate other plugins
- [ ] Enable WP_DEBUG
- [ ] Check database tables exist
- [ ] Verify correct file paths
- [ ] Test in incognito window

---

## 🆘 STILL STUCK?

### Enable Full Debug Mode:

Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
define('SCRIPT_DEBUG', true);
```

Check error log at: `wp-content/debug.log`

### Common Debug Points:

1. **Plugin Loading:**
   - Add `error_log('Lite plugin loaded');` in main file

2. **Feature Detection:**
   - Add `error_log('Is PRO: ' . (Features::is_pro() ? 'yes' : 'no'));`

3. **Menu Registration:**
   - Add `error_log('Menu registered');` in register_admin_menu()

4. **AJAX Calls:**
   - Check Network tab in browser DevTools
   - Verify nonce is correct
   - Check response for errors

### File Verification:

Make sure these files exist:
- `a-tables-charts-lite.php` (main file)
- `src/shared/utils/Features.php`
- `src/modules/core/Plugin.php`
- `src/modules/core/views/upgrade.php`
- `src/modules/core/views/components/upgrade-notice.php`
- `assets/js/admin-upgrade.js`
- `readme.txt`

---

## ✅ SUCCESS INDICATORS

Your LITE version is working correctly if:

- ✅ Plugin activates without errors
- ✅ Menu shows with upgrade item (✨)
- ✅ CSV import works perfectly
- ✅ Clicking JSON/Excel/XML shows upgrade modal
- ✅ Upgrade page displays with pricing
- ✅ Frontend tables display correctly
- ✅ No JavaScript errors in console
- ✅ No PHP errors in debug log

---

## 📞 FINAL RESORT

If nothing works:

1. **Start Fresh:**
   - Delete `a-tables-charts-lite` folder
   - Re-copy from PRO version
   - Follow guide from step 1 again

2. **Check Original PRO:**
   - Activate PRO version
   - Verify it still works
   - This confirms source is good

3. **Compare Files:**
   - Use a diff tool (WinMerge, Beyond Compare)
   - Compare LITE vs PRO
   - Identify unexpected differences

---

**Remember:** Most issues are simple fixes like:
- Wrong namespace
- Missing constant
- File not found
- Cache not cleared

Take a deep breath and check the basics first! 💪

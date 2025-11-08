# 🚨 Settings Troubleshooting Quick Reference

## Quick Diagnostic Checklist

Run through these checks if settings aren't working:

### ✅ 1. Are Settings Saving?

**Test:** Change a setting → Save → Refresh page → Check if value persists

**If NO:**
- Check browser console for JavaScript errors
- Verify you're logged in as administrator
- Check if form has `<?php settings_fields('atables_settings'); ?>`
- Verify WordPress nonce is valid

### ✅ 2. Are Settings Loading?

**Test:** Add this to dashboard.php temporarily:

```php
<?php
$settings = get_option('atables_settings', array());
echo '<div style="background:#fff;padding:20px;margin:20px;border:2px solid #0073aa;">';
echo '<h3>Current Settings:</h3>';
echo '<pre>';
print_r($settings);
echo '</pre>';
echo '</div>';
?>
```

**If EMPTY:**
- Settings haven't been saved yet (use defaults)
- Save settings once to initialize

### ✅ 3. Is Dashboard Using Settings?

**Test:** Change rows per page to 5 → Save → Check dashboard

**If NO CHANGE:**
- Check if `$rows_per_page` variable is being used in query
- Verify `get_option('atables_settings')` is being called
- Clear any WordPress object cache

**Current Implementation:**
```php
// In dashboard.php
$settings = get_option('atables_settings', array());
$rows_per_page = isset($settings['rows_per_page']) ? (int) $settings['rows_per_page'] : 10;

$result = $table_service->get_all_tables(array(
    'per_page' => $rows_per_page, // ← This should use the setting
));
```

### ✅ 4. Is Frontend Using Settings?

**Test:** Disable search in settings → View table with `[atable id="1"]`

**If SEARCH STILL SHOWS:**
- Check if shortcode has explicit `search="true"` (overrides setting)
- Verify `TableShortcode.php` loads settings correctly
- Clear browser cache (Ctrl+Shift+R)
- Clear WordPress cache if using caching plugin

**Current Implementation:**
```php
// In TableShortcode.php
$settings = get_option('atables_settings', array());
$atts = shortcode_atts(
    array(
        'search' => $settings['enable_search'] ? 'true' : 'false', // ← Should use setting
    ),
    $atts,
    'atable'
);
```

### ✅ 5. Are JavaScript Features Working?

**Test:** Open browser console (F12) → Check for errors

**Common Errors:**
```
DataTables is not defined
→ jQuery or DataTables not loaded

Cannot read property 'data' of undefined
→ Data attribute missing on table element

Uncaught TypeError: $(...).DataTable is not a function
→ DataTables loaded after your script
```

**Solutions:**
- Ensure scripts load in correct order: jQuery → DataTables → public-tables.js
- Check if DataTables CDN is accessible
- Verify table has correct class: `atables-interactive`

---

## 🔍 Common Issues

### Issue #1: Settings Save But Don't Take Effect

**Symptoms:**
- Settings page shows saved values
- Frontend still uses defaults
- Dashboard ignores settings

**Diagnosis:**
```bash
# Check if settings are in database
wp option get atables_settings

# Should output something like:
# {"rows_per_page":15,"enable_search":true,...}
```

**Causes & Solutions:**

| Cause | Solution |
|-------|----------|
| Caching plugin active | Clear all caches |
| Shortcode has explicit attributes | Remove `search`, `pagination`, etc. from shortcode |
| Browser cache | Hard refresh (Ctrl+Shift+R) |
| Code not loading settings | Check `get_option()` is called |

### Issue #2: Only Some Settings Work

**Symptoms:**
- Rows per page works ✅
- Search enable/disable doesn't work ❌

**Diagnosis:**
Check shortcode attributes in your post/page:

```
[atable id="1" search="true"]
         ↑
This overrides the setting!
```

**Solution:**
Use basic shortcode without attributes to let settings apply:
```
[atable id="1"]
```

### Issue #3: Settings Reset After Save

**Symptoms:**
- Change setting to 25
- Click save
- Value goes back to 10

**Diagnosis:**
Value failed validation. Check `Plugin::sanitize_settings()`:

```php
// Rows must be 1-100
$validated['rows_per_page'] = max(1, min(100, intval($input['rows_per_page'])));

// If you enter 150, it becomes 100
// If you enter -5, it becomes 1
```

**Solution:**
- Use values within allowed range
- Check for JavaScript validation errors

### Issue #4: "Settings Updated" But Form Shows Old Values

**Symptoms:**
- Success message appears
- Form fields still show old values
- Need to manually refresh page

**Diagnosis:**
JavaScript not reloading form values after save.

**Solution:**
Already handled by WordPress - redirects with `?settings-updated=true`

If issue persists:
- Clear browser cache
- Disable browser extensions
- Try different browser

---

## 🔧 Debug Mode

### Enable WordPress Debug Mode

Add to `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

### Check Debug Log

Location: `wp-content/debug.log`

Look for errors like:
```
[13-Oct-2025 10:30:15 UTC] PHP Warning: Invalid argument supplied for foreach()
[13-Oct-2025 10:30:15 UTC] PHP Notice: Undefined index: rows_per_page
```

### Browser Console Debug

Add to `public-tables.js`:

```javascript
jQuery(document).ready(function($) {
    console.log('A-Tables: Initializing tables...');
    
    $('.atables-interactive').each(function() {
        var $table = $(this);
        var config = {
            searching: $table.data('search') === true,
            paging: $table.data('pagination') === true,
            pageLength: parseInt($table.data('page-length')) || 10,
            ordering: $table.data('sorting') === true,
            info: $table.data('info') === true
        };
        
        console.log('Table config:', config);
        
        try {
            $table.DataTable(config);
            console.log('Table initialized successfully');
        } catch(e) {
            console.error('DataTable initialization failed:', e);
        }
    });
});
```

---

## 📋 Settings Validation Rules

### Numeric Settings

| Setting | Type | Min | Max | Default |
|---------|------|-----|-----|---------|
| rows_per_page | int | 1 | 100 | 10 |
| cache_expiration | int | 0 | ∞ | 3600 |

### String Settings

| Setting | Type | Validation | Default |
|---------|------|------------|---------|
| default_table_style | string | Whitelist: default, striped, bordered, hover | default |
| date_format | string | Any PHP date format | Y-m-d |
| time_format | string | Any PHP time format | H:i:s |
| decimal_separator | string | Single character | . |
| thousands_separator | string | Single character | , |

### Boolean Settings

All boolean settings default to `true` (enabled):
- enable_responsive
- enable_search
- enable_sorting
- enable_pagination
- enable_export
- cache_enabled
- google_charts_enabled
- chartjs_enabled

**Unchecked = false, Checked = true**

---

## 🧹 Cache Clearing

### WordPress Object Cache

```bash
# WP-CLI
wp cache flush

# Code
wp_cache_flush();
```

### Transients

```bash
# WP-CLI
wp transient delete --all

# Code
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
```

### Browser Cache

**Chrome/Firefox:**
- Windows: `Ctrl + Shift + R`
- Mac: `Cmd + Shift + R`

**Manually:**
- Chrome: Settings → Privacy → Clear browsing data
- Firefox: Options → Privacy → Clear Data

---

## 🔄 Reset Everything

### Reset Plugin Settings

**Method 1: Via Settings Page**
1. Go to Settings
2. Click "Reset to Defaults"
3. Confirm

**Method 2: Via WP-CLI**
```bash
wp option delete atables_settings
```

**Method 3: Via Database**
```sql
DELETE FROM wp_options WHERE option_name = 'atables_settings';
```

### Reset Single Table

If a specific table is problematic:

```sql
UPDATE wp_atables_tables 
SET status = 'draft' 
WHERE id = 123;
```

---

## 📞 Getting Help

### Before Asking for Help

Gather this information:

1. **WordPress Version:** `<?php echo get_bloginfo('version'); ?>`
2. **Plugin Version:** `<?php echo ATABLES_VERSION; ?>`
3. **PHP Version:** `<?php echo phpversion(); ?>`
4. **Active Theme:** Name and version
5. **Other Active Plugins:** List all
6. **Browser:** Chrome/Firefox/Safari + version
7. **Settings Values:** Screenshot of settings page
8. **Browser Console:** Screenshot of any errors (F12)
9. **Steps to Reproduce:** Exact steps that cause the issue

### Useful WP-CLI Commands

```bash
# Get plugin info
wp plugin list --fields=name,status,version

# Get settings
wp option get atables_settings

# Get all tables count
wp db query "SELECT COUNT(*) FROM wp_atables_tables"

# Get PHP info
wp eval 'phpinfo();'

# Test database connection
wp db check
```

---

## ✅ Quick Fix Checklist

When settings aren't working, try these in order:

- [ ] Hard refresh browser (Ctrl+Shift+R)
- [ ] Clear browser cache completely
- [ ] Disable caching plugins temporarily
- [ ] Check browser console for errors (F12)
- [ ] Remove all shortcode attributes, use basic `[atable id="1"]`
- [ ] Save settings again
- [ ] Log out and log back in
- [ ] Try different browser
- [ ] Disable other plugins temporarily
- [ ] Switch to default WordPress theme temporarily
- [ ] Enable WP_DEBUG and check debug.log
- [ ] Run: `wp cache flush`
- [ ] Check if settings exist: `wp option get atables_settings`

---

## 🎯 Expected Behavior Reference

### Dashboard

| Setting | Value | Expected Behavior |
|---------|-------|-------------------|
| rows_per_page | 5 | Dashboard shows 5 tables max |
| rows_per_page | 25 | Dashboard shows 25 tables max |

### Frontend Table (Basic Shortcode: `[atable id="1"]`)

| Setting | State | Expected Frontend |
|---------|-------|------------------|
| enable_search | ✅ | Search box visible |
| enable_search | ❌ | No search box |
| enable_pagination | ✅ | Page numbers visible |
| enable_pagination | ❌ | All rows show |
| enable_sorting | ✅ | Arrows on headers |
| enable_sorting | ❌ | No arrows, no sorting |
| default_table_style | striped | Alternating row colors |
| default_table_style | bordered | Cell borders visible |

### Frontend Table (With Overrides: `[atable id="1" search="false"]`)

Shortcode attribute **always wins** over settings.

---

## 🚀 Performance Checklist

If tables load slowly:

- [ ] Enable cache in settings
- [ ] Set cache duration to 3600 (1 hour)
- [ ] Reduce rows per page (fewer rows = faster)
- [ ] Disable features you don't need
- [ ] Check database indexes
- [ ] Optimize database tables
- [ ] Use caching plugin (WP Super Cache, W3 Total Cache)

---

**Last Updated:** October 13, 2025
**Quick Support:** Check SETTINGS-TESTING-GUIDE.md for comprehensive testing

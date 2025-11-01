# Troubleshooting Guide

Quick solutions to common problems with A-Tables & Charts.

---

## 🚨 Emergency Checklist

**Before anything else, try these:**

1. ✅ **Clear ALL caches**
   - Plugin cache: Settings → Performance → Clear Cache
   - Browser cache: Ctrl+Shift+Del (Chrome/Firefox)
   - WordPress cache: If using caching plugin, clear it
   - Hosting cache: Check with your host

2. ✅ **Disable other plugins temporarily**
   - Go to Plugins → Deactivate all except A-Tables & Charts
   - Test if issue persists
   - Re-enable plugins one by one to find conflict

3. ✅ **Switch to default theme temporarily**
   - Appearance → Themes → Activate Twenty Twenty-Three
   - Test if issue persists
   - This identifies theme conflicts

4. ✅ **Check browser console for errors**
   - Press F12 (or Cmd+Option+I on Mac)
   - Go to Console tab
   - Look for red error messages
   - Screenshot and send to support

---

## 📊 Table Issues

### ❌ Table doesn't display at all

**Symptoms:** Shortcode shows as text or nothing appears

**Solutions:**

1. **Check shortcode syntax**
   ```
   WRONG: [atables_id=1]
   WRONG: [wptable id=1]
   RIGHT: [atables id=1]
   ```

2. **Verify table ID**
   - Go to A-Tables & Charts → All Tables
   - Check the ID number matches your shortcode
   - Try `[atables id=1]` first to test

3. **Check table status**
   - Table must be "Published" not "Draft"
   - Edit table → Status → Publish

4. **Check user permissions**
   - Are you logged in as Administrator?
   - Some tables might have visibility restrictions

5. **JavaScript not loading**
   ```
   Check: Is jQuery loaded?
   Fix: Settings → Advanced → Force jQuery Load → Enable
   ```

---

### ❌ Table shows but data is missing

**Symptoms:** Headers show but rows are empty, or some columns missing

**Diagnose:**

1. **Check import log**
   - Edit table → Import Log tab
   - Look for error messages
   - Check how many rows imported vs expected

2. **Check data format**
   - CSV encoding must be UTF-8
   - No empty rows at start of file
   - Column count matches in all rows

3. **Check character encoding**
   - Special characters (é, ñ, ü) might not import correctly
   - Re-save your CSV as UTF-8 encoding
   - In Excel: Save As → CSV UTF-8

4. **Check for hidden characters**
   - Open CSV in text editor (Notepad++, VS Code)
   - Look for weird characters, especially at start
   - Remove BOM (Byte Order Mark) if present

**Fix:**

```
Solution: Re-import with UTF-8 encoding
1. Open your CSV in Excel
2. File → Save As
3. Choose "CSV UTF-8 (Comma delimited)"
4. Re-import in plugin
```

---

### ❌ Pagination not working

**Symptoms:** All rows show at once, or pagination buttons don't appear

**Check:**

1. **Is pagination enabled?**
   - Edit table → Display Settings
   - Scroll to Pagination section
   - ✅ Enable pagination checkbox

2. **Rows per page setting**
   - Must be less than total rows
   - Example: If table has 50 rows, set to 10, 20, or 25

3. **JavaScript error**
   - F12 → Console
   - Look for "pagination is not a function" or similar
   - Often caused by jQuery conflicts

**Fix:**

```javascript
// Add this to Settings → Custom JavaScript:
jQuery.noConflict();
```

---

### ❌ Sorting doesn't work

**Symptoms:** Clicking column headers doesn't sort data

**Solutions:**

1. **Enable sorting**
   - Edit table → Display Settings
   - ✅ Enable sorting checkbox

2. **Check column data types**
   - Numbers must be stored as numbers, not text
   - Dates must be in valid format (YYYY-MM-DD)
   - Mixed data types in column prevents correct sorting

3. **Re-import data with correct types**
   - Edit table → Data Import
   - Set column types manually:
     - Number: For numeric data
     - Text: For text data
     - Date: For dates

---

### ❌ Search not working

**Symptoms:** Search box appears but doesn't filter results

**Diagnose:**

1. **Check console errors**
   ```
   F12 → Console
   Common error: "search() is not defined"
   ```

2. **Check settings**
   - Edit table → Display Settings
   - ✅ Enable search checkbox

3. **JavaScript conflict**
   - Another plugin is conflicting
   - Disable plugins one by one

**Fix:**

```
Quick fix:
1. Settings → Advanced → Load Scripts in Footer → Disable
2. Clear cache
3. Test again
```

---

### ❌ Table not responsive on mobile

**Symptoms:** Table extends beyond screen width on phones

**Solutions:**

1. **Enable responsive mode**
   - Edit table → Display Settings
   - ✅ Enable responsive
   - Choose mode: Stack or Scroll

2. **Check theme CSS conflicts**
   ```css
   /* Add to Settings → Custom CSS */
   .atables-wrapper {
       max-width: 100%;
       overflow-x: auto;
   }
   .atables-wrapper table {
       width: 100% !important;
   }
   ```

3. **Column widths too wide**
   - Edit columns
   - Set max-width for each column
   - Or let columns auto-size (remove fixed widths)

---

### ❌ Table styling is broken

**Symptoms:** No borders, wrong colors, overlapping text

**Common causes:**

1. **Theme CSS override**
   ```css
   /* Add to Settings → Custom CSS */
   .atables-wrapper table {
       border: 1px solid #ddd !important;
       border-collapse: collapse !important;
   }
   .atables-wrapper table td,
   .atables-wrapper table th {
       border: 1px solid #ddd !important;
       padding: 8px !important;
   }
   ```

2. **Cache not cleared**
   - Clear plugin cache
   - Clear browser cache (Ctrl+Shift+Delete)
   - Hard refresh page (Ctrl+F5)

3. **CSS not loading**
   - Check if `/wp-content/plugins/a-tables-charts/assets/css/` exists
   - Check file permissions (should be 644)
   - Check for 404 errors in browser console

---

## 📈 Chart Issues

### ❌ Chart doesn't display at all

**Symptoms:** Empty space where chart should be, or shortcode visible

**Solutions:**

1. **Wrong shortcode**
   ```
   WRONG: [atables id=1]        (This shows table, not chart!)
   WRONG: [chart id=1]
   RIGHT: [atables_chart id=1]
   ```

2. **Chart ID doesn't exist**
   - Go to A-Tables & Charts → Charts
   - Verify chart ID matches shortcode
   - Chart must be Published, not Draft

3. **Google Charts library not loading**
   ```
   F12 → Network tab → Look for:
   https://www.gstatic.com/charts/loader.js

   If blocked (403, 404):
   - Your firewall might block Google CDN
   - Or you're offline
   - Or CSP (Content Security Policy) blocking it
   ```

4. **Add Google Charts to CSP (if needed)**
   ```html
   <!-- Add to theme header.php -->
   <meta http-equiv="Content-Security-Policy"
         content="script-src 'self' https://www.gstatic.com;">
   ```

---

### ❌ Chart shows but no data

**Symptoms:** Chart renders but is empty, or shows "No data"

**Diagnose:**

1. **Check data columns**
   - Edit chart → Data Configuration
   - X-axis and Y-axis must be selected
   - Columns must have data (not empty)

2. **Check data types**
   - Y-axis must be NUMERIC data
   - Text values won't render in most chart types
   - Check source table for proper data types

3. **Data range issue**
   - If you set a custom data range, it might exclude all rows
   - Reset to "All data" and test

**Fix:**

```
Solution: Verify source data
1. Go to the source table
2. Check that numeric columns have numbers, not text
3. Example: "100" (good) vs "100 items" (bad for charts)
4. Re-import if needed with correct data types
```

---

### ❌ Chart looks wrong on mobile

**Symptoms:** Chart too small, cut off, or unreadable on phones

**Solutions:**

1. **Set responsive width**
   - Edit chart → Display Settings
   - Width: 100% (not fixed pixels)
   - Height: Auto or 300-400px

2. **Adjust font sizes for mobile**
   ```javascript
   // Add to chart config (Advanced Settings):
   {
       "fontSize": 12,
       "legend": {
           "textStyle": {"fontSize": 10}
       }
   }
   ```

3. **Hide legend on mobile**
   ```javascript
   {
       "legend": {
           "position": "none"  // On mobile
       }
   }
   ```

---

### ❌ Chart colors are wrong

**Symptoms:** All bars/lines are same color, or colors don't match settings

**Solutions:**

1. **Set colors explicitly**
   - Edit chart → Styling
   - Colors section
   - Specify color for each data series

2. **Theme CSS override**
   - Your theme might force its own colors
   - Use !important in color settings

3. **Clear browser cache**
   - Old color settings cached
   - Hard refresh (Ctrl+F5)

---

### ❌ Multiple charts on same page conflict

**Symptoms:** Only first chart shows, or charts override each other

**This is usually NOT an issue** - plugin handles multiple charts automatically.

If you do experience issues:

```javascript
// Add to Settings → Custom JavaScript:
(function($) {
    $(document).ready(function() {
        // Force sequential loading
        $('.atables-chart').each(function(i, chart) {
            setTimeout(function() {
                // Chart initialization code
            }, i * 100);
        });
    });
})(jQuery);
```

---

## 💾 Import/Export Issues

### ❌ CSV import fails

**Symptoms:** Error message, or only partial data imports

**Common causes:**

1. **File encoding not UTF-8**
   ```
   Fix:
   1. Open CSV in Excel/Google Sheets
   2. Save As → CSV UTF-8
   3. Re-upload
   ```

2. **Delimiter issues**
   - Standard CSV uses comma (,)
   - Some use semicolon (;) or tab
   - Settings → Import → CSV Delimiter → Check setting

3. **Quotes in data**
   - Data with commas must be quoted: "Smith, John"
   - Or escape commas properly

4. **File too large**
   ```
   Check: PHP upload_max_filesize
   Fix: Contact hosting to increase limit
   Or: Split CSV into smaller files
   ```

5. **Hidden characters**
   ```
   Fix:
   1. Open CSV in Notepad++ or VS Code
   2. View → Show Symbol → Show All Characters
   3. Remove BOM (if present)
   4. Remove extra line breaks
   ```

---

### ❌ Excel import fails

**Symptoms:** "Invalid file format" or import hangs

**Solutions:**

1. **File format**
   - Use .xlsx (not .xls old format)
   - Or save as .csv and import as CSV

2. **Excel file corrupted**
   - Try opening in Excel to verify it works
   - Copy data to new Excel file
   - Save As .xlsx

3. **Memory limit**
   - Large Excel files need more memory
   - Contact hosting to increase PHP memory_limit
   - Or convert to CSV first

4. **Formula cells**
   - Excel formulas won't import (only results)
   - Copy sheet → Paste Special → Values
   - Save as new file

---

### ❌ Export doesn't download

**Symptoms:** Click export, nothing happens or blank page

**Check:**

1. **Popup blocker**
   - Browser might block download popup
   - Look for blocked popup icon in address bar
   - Allow popups for your site

2. **File permissions**
   ```bash
   # Check WordPress uploads folder:
   /wp-content/uploads/ should be writable (755 or 775)
   ```

3. **Memory limit**
   - Large tables need more memory to export
   - Try exporting filtered data (fewer rows)
   - Or increase PHP memory limit

4. **Console errors**
   ```
   F12 → Console
   Look for: "Failed to load resource"
   Or: "Out of memory"
   ```

**Fix:**

```
Quick fix:
1. Filter table to show fewer rows
2. Export filtered data
3. Repeat for other data ranges
4. Combine files externally
```

---

## ⚙️ Settings & Configuration Issues

### ❌ Settings don't save

**Symptoms:** Save button clicked but settings revert

**Solutions:**

1. **Nonce expired**
   - You've had settings page open too long
   - Refresh page and try again

2. **Server timeout**
   - Large settings taking too long to save
   - Contact hosting to increase max_execution_time

3. **Plugin conflict**
   - Disable other plugins temporarily
   - Test save settings
   - Re-enable plugins one by one

4. **Permissions issue**
   - User must be Administrator
   - Check user role: Users → Your Profile

---

### ❌ Custom CSS doesn't apply

**Symptoms:** Added CSS but no visual changes

**Debug steps:**

1. **Check CSS is loading**
   - F12 → Elements tab
   - Find your table element
   - Check if custom CSS classes exist

2. **Specificity issue**
   ```css
   /* If CSS doesn't apply, increase specificity: */

   /* Weak */
   table { color: red; }

   /* Stronger */
   .atables-wrapper table { color: red; }

   /* Strongest */
   .atables-wrapper table { color: red !important; }
   ```

3. **Cache not cleared**
   - Clear plugin cache
   - Clear browser cache
   - Hard refresh (Ctrl+F5)

4. **Syntax error**
   - Check CSS for typos
   - Missing semicolon or bracket
   - Use CSS validator: https://jigsaw.w3.org/css-validator/

---

## 🔌 Compatibility Issues

### ❌ Conflict with page builder

**Symptoms:** Tables don't show in Elementor, Divi, etc.

**Solutions:**

1. **Elementor**
   - Use "Shortcode" widget
   - Paste: `[atables id=1]`
   - Update preview

2. **Divi**
   - Add "Code" module
   - Paste shortcode
   - Save

3. **Beaver Builder**
   - Add "HTML" module
   - Paste shortcode
   - Save

4. **WPBakery**
   - Add "Raw HTML" element
   - Paste shortcode
   - Update

**Still not working?**
- Clear page builder cache
- Regenerate CSS in page builder settings
- Test with default WordPress editor first

---

### ❌ Conflict with caching plugins

**Common caching plugins:** WP Rocket, W3 Total Cache, WP Super Cache

**Symptoms:**
- Tables show old data
- Changes not visible after save
- Shortcodes not rendering

**Solutions:**

1. **Exclude plugin from cache**
   ```
   WP Rocket:
   Settings → Advanced Rules → Never Cache URL
   Add: /wp-admin/admin.php?page=a-tables-charts

   W3 Total Cache:
   Performance → Page Cache → Never cache these pages
   Add: admin.php?page=a-tables-charts
   ```

2. **Clear all caches after changes**
   - Plugin cache
   - Hosting cache
   - CDN cache (if applicable)
   - Browser cache

3. **Disable JavaScript minification/combination**
   - Caching plugins often break JavaScript
   - Settings → Exclude from JS optimization
   - Add: `a-tables-charts`

---

### ❌ Theme conflicts

**Symptoms:** Tables look broken, buttons don't work, styling is off

**Common themes with issues:**
- Very old themes
- Themes that override WordPress defaults
- Themes with aggressive CSS

**Solutions:**

1. **Test with default theme first**
   ```
   1. Activate Twenty Twenty-Three
   2. Test if table works
   3. If yes, it's a theme conflict
   ```

2. **Add theme compatibility CSS**
   ```css
   /* Add to Settings → Custom CSS */

   /* Reset theme overrides */
   .atables-wrapper * {
       box-sizing: border-box !important;
   }

   .atables-wrapper table {
       width: 100% !important;
       margin: 0 !important;
       border-collapse: collapse !important;
   }

   .atables-wrapper button {
       line-height: normal !important;
       font-size: 14px !important;
   }
   ```

3. **Contact theme author**
   - If theme breaks multiple plugins
   - They might need to fix compatibility

---

## 🚀 Performance Issues

### ❌ Tables load very slowly

**Symptoms:** Page takes 5+ seconds to load, or times out

**Diagnose:**

1. **How many rows?**
   - < 100: Should be fast
   - 100-1,000: Use pagination
   - 1,000-10,000: Use server-side processing
   - > 10,000: Use database connection

2. **Check if images in cells**
   - Images slow down loading significantly
   - Optimize images before importing
   - Or load images lazily

3. **Check server resources**
   - WordPress → Site Health → Info
   - Check available memory
   - Check PHP version (8.0+ is faster)

**Solutions:**

```
Quick wins:
1. Enable pagination (limit to 25 rows per page)
2. Enable caching (Settings → Performance)
3. Disable search if not needed
4. Disable sorting if not needed
5. Use lighter color scheme
```

**Advanced fixes:**
```
1. Enable server-side processing:
   - Edit table → Performance
   - ✅ Enable AJAX pagination

2. Use database connection:
   - Instead of importing, connect directly to MySQL
   - Data loads on-demand, not all at once

3. Optimize database:
   - Add indexes to frequently queried columns
   - Use proper data types
```

---

### ❌ High memory usage

**Symptoms:** "Out of memory" error, or server crashes

**Temporary fix:**
```
Increase memory limit temporarily:
1. Edit wp-config.php
2. Add: define('WP_MEMORY_LIMIT', '256M');
3. Save file
```

**Permanent solution:**
```
1. Reduce table size (import fewer rows)
2. Use pagination (don't load all rows)
3. Enable caching
4. Contact hosting for more memory
```

---

## 🆘 Emergency Recovery

### Plugin won't deactivate

**If plugin breaks your site:**

1. **Via FTP:**
   ```
   1. Connect via FTP
   2. Go to /wp-content/plugins/
   3. Rename folder: a-tables-charts → a-tables-charts-disabled
   4. Plugin is now deactivated
   ```

2. **Via phpMyAdmin:**
   ```sql
   UPDATE wp_options
   SET option_value = 'a:0:{}'
   WHERE option_name = 'active_plugins';
   ```

### Database tables corrupted

**If tables are corrupted:**

```sql
-- Run in phpMyAdmin:
REPAIR TABLE wp_atables_tables;
REPAIR TABLE wp_atables_charts;
REPAIR TABLE wp_atables_cache;
REPAIR TABLE wp_atables_rows;
```

### Complete reset

**Start fresh (WARNING: Deletes all data!):**

1. Deactivate plugin
2. Delete plugin
3. Run in phpMyAdmin:
```sql
DROP TABLE IF EXISTS wp_atables_tables;
DROP TABLE IF EXISTS wp_atables_charts;
DROP TABLE IF EXISTS wp_atables_cache;
DROP TABLE IF EXISTS wp_atables_rows;
DELETE FROM wp_options WHERE option_name LIKE 'atables_%';
```
4. Reinstall plugin

---

## 📞 Getting Help

### Before contacting support, collect this info:

1. **System Info**
   ```
   WordPress version: (Dashboard → Updates)
   Plugin version: (Plugins page)
   PHP version: (Site Health → Info → Server)
   Theme: (Appearance → Themes)
   ```

2. **Active Plugins**
   ```
   Go to Plugins page
   List all active plugins
   ```

3. **Error Messages**
   ```
   Any error messages you see
   Screenshot is helpful
   ```

4. **Console Errors**
   ```
   F12 → Console
   Screenshot any red errors
   ```

5. **Steps to Reproduce**
   ```
   1. I did this...
   2. Then I clicked...
   3. Then this error happened...
   ```

### Support Channels

- **Documentation**: https://your-docs-site.com
- **FAQ**: Check FAQ.md first
- **Support Forum**: https://codecanyon.net/item/support
- **Email**: support@yourdomain.com

**Response time:** Within 24 hours (business days)

---

## 💡 Prevention Tips

**Avoid common issues:**

1. ✅ Always backup before major changes
2. ✅ Test on staging site first
3. ✅ Keep plugin updated
4. ✅ Keep WordPress updated
5. ✅ Use quality hosting
6. ✅ Clear cache after changes
7. ✅ Test in incognito mode (rules out cache issues)
8. ✅ Don't edit plugin files directly
9. ✅ Read documentation before asking
10. ✅ Check compatibility before updating

---

**Still stuck? Contact our support team with the information above! We're here to help.**

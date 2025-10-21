# 🧪 Settings Testing Guide

## Overview
This guide will help you test that all settings are working correctly and affecting the plugin's behavior as expected.

---

## ✅ Pre-Testing Checklist

Before you start testing, ensure:
- [ ] WordPress site is running
- [ ] Plugin is activated
- [ ] At least one table exists in the system
- [ ] You have a page/post where you can add shortcodes

---

## 🎯 Test #1: Default Rows Per Page

### **What it controls:**
- Number of rows displayed per page in dashboard
- Default `page_length` attribute in shortcodes

### **How to test:**

1. **Go to Settings:**
   - Navigate to `a-tables-charts` → `Settings`
   - Find "Default Rows per Page" setting

2. **Change the value:**
   - Current value: `10`
   - Change to: `15`
   - Click "Save All Settings"
   - Wait for "Settings saved successfully!" message

3. **Test Dashboard:**
   - Go to `a-tables-charts` → `All Tables` (Dashboard)
   - Check if the table list shows exactly 15 tables per page
   - If you have fewer than 15 tables, this will show all of them

4. **Test Frontend Shortcode:**
   - Create a page/post with shortcode: `[atable id="1"]`
   - View the page on the frontend
   - The table should show 15 rows per page by default
   - You can still override with: `[atable id="1" page_length="25"]`

### **Expected Result:**
- ✅ Dashboard shows 15 tables
- ✅ Frontend table shows 15 rows per page
- ✅ Settings persist after page refresh

### **If it doesn't work:**
- Check if settings are saved: Go back to Settings page and verify the value is still 15
- Clear WordPress cache if using a caching plugin
- Try a different number (e.g., 5) to see if changes are detected

---

## 🎯 Test #2: Enable/Disable Search

### **What it controls:**
- Whether search box appears on frontend tables
- Shortcode default for `search` attribute

### **How to test:**

1. **Go to Settings:**
   - Navigate to `Settings`
   - Find "Frontend Features" → "Search Functionality"

2. **Disable Search:**
   - Uncheck "Search Functionality"
   - Click "Save All Settings"

3. **Test Frontend:**
   - Go to a page with table shortcode: `[atable id="1"]`
   - Refresh the page
   - **Search box should NOT appear** above the table

4. **Enable Search:**
   - Go back to Settings
   - Check "Search Functionality"
   - Save settings

5. **Test Frontend Again:**
   - Refresh the page with the table
   - **Search box SHOULD appear** above the table

### **Expected Result:**
- ✅ When disabled: No search box on frontend
- ✅ When enabled: Search box appears on frontend
- ✅ Search box is functional when enabled
- ✅ Override still works: `[atable id="1" search="true"]` forces search even if disabled

---

## 🎯 Test #3: Enable/Disable Pagination

### **What it controls:**
- Whether pagination controls appear on frontend tables
- Whether tables show all rows or paginated rows

### **How to test:**

1. **Disable Pagination:**
   - Go to Settings
   - Uncheck "Pagination"
   - Save settings

2. **Test Frontend:**
   - View table with shortcode: `[atable id="1"]`
   - **All rows should display at once** (no page numbers)
   - No "Showing 1 to X of Y entries" text
   - No page length selector (10, 25, 50, etc.)

3. **Enable Pagination:**
   - Go back to Settings
   - Check "Pagination"
   - Save settings

4. **Test Frontend Again:**
   - Refresh the page
   - **Pagination controls should appear** at bottom
   - "Showing 1 to X of Y entries" text appears
   - Page length selector appears

### **Expected Result:**
- ✅ When disabled: All rows show, no pagination
- ✅ When enabled: Rows are paginated with controls
- ✅ Override works: `[atable id="1" pagination="false"]`

---

## 🎯 Test #4: Enable/Disable Sorting

### **What it controls:**
- Whether columns can be sorted by clicking headers
- Whether sort arrows appear on column headers

### **How to test:**

1. **Disable Sorting:**
   - Go to Settings
   - Uncheck "Column Sorting"
   - Save settings

2. **Test Frontend:**
   - View table with shortcode
   - Click on any column header
   - **Nothing should happen** (no sorting)
   - **No sort arrows** on headers

3. **Enable Sorting:**
   - Go back to Settings
   - Check "Column Sorting"
   - Save settings

4. **Test Frontend Again:**
   - Refresh the page
   - **Sort arrows should appear** on headers
   - Click a column header
   - **Table should sort** (ascending/descending)

### **Expected Result:**
- ✅ When disabled: No sorting, no arrows
- ✅ When enabled: Sorting works, arrows appear
- ✅ Override works: `[atable id="1" sorting="false"]`

---

## 🎯 Test #5: Default Table Style

### **What it controls:**
- Visual appearance of tables (default, striped, bordered, hover)

### **How to test:**

1. **Change Style:**
   - Go to Settings
   - Change "Default Table Style" to "Striped Rows"
   - Save settings

2. **Test Frontend:**
   - View table with shortcode: `[atable id="1"]`
   - **Rows should have alternating colors** (striped appearance)

3. **Try Other Styles:**
   - Go back to Settings
   - Try "Bordered"
   - Save and check frontend (table should have borders around cells)
   - Try "Hover Effect"
   - Save and check frontend (rows should highlight on hover)

### **Expected Result:**
- ✅ Striped: Alternating row colors
- ✅ Bordered: Cell borders visible
- ✅ Hover: Row highlights on mouse hover
- ✅ Default: Clean, minimal styling
- ✅ Override works: `[atable id="1" style="bordered"]`

---

## 🎯 Test #6: Data Formatting (Date/Time)

### **What it controls:**
- How dates and times are displayed in settings page examples
- Future use in table rendering

### **How to test:**

1. **Change Date Format:**
   - Go to Settings
   - Find "Date Format" field
   - Current: `Y-m-d` (2025-10-13)
   - Change to: `m/d/Y` (10/13/2025)
   - **Look at the example below the field**
   - It should update to show: 10/13/2025

2. **Change Time Format:**
   - Find "Time Format" field
   - Current: `H:i:s` (14:30:45)
   - Change to: `g:i A` (2:30 PM)
   - **Look at the example**
   - It should update to show: 2:30 PM

3. **Save Settings:**
   - Click "Save All Settings"
   - Refresh page
   - Examples should still show new formats

### **Expected Result:**
- ✅ Examples update in real-time (after save)
- ✅ Settings persist after refresh

---

## 🎯 Test #7: Cache Settings

### **What it controls:**
- Whether table data is cached for performance
- How long cached data is kept

### **How to test:**

1. **Enable Cache:**
   - Go to Settings
   - Check "Enable data caching"
   - Set "Cache Duration" to 60 seconds (for testing)
   - Save settings

2. **Test Cache Behavior:**
   - View a table on frontend
   - Note the load time
   - Edit the table data in admin
   - Immediately view frontend again
   - **Old data should show** (cached for 60 seconds)
   - Wait 61 seconds
   - Refresh frontend
   - **New data should show** (cache expired)

3. **Disable Cache:**
   - Go back to Settings
   - Uncheck "Enable data caching"
   - Save settings
   - Edit table data
   - Refresh frontend immediately
   - **New data should show right away**

### **Expected Result:**
- ✅ When enabled: Data is cached for specified duration
- ✅ When disabled: Data updates immediately
- ✅ Cache expiration works correctly

---

## 🎯 Test #8: Reset to Defaults

### **What it controls:**
- Resets all settings to their default values

### **How to test:**

1. **Change Multiple Settings:**
   - Rows per page: 25
   - Disable search
   - Disable pagination
   - Change style to "Bordered"
   - Save settings

2. **Click "Reset to Defaults":**
   - You should see a confirmation dialog
   - Click "OK"
   - Page should reload
   - All settings should return to:
     - Rows per page: 10
     - Search: Enabled
     - Pagination: Enabled
     - Style: Default

3. **Verify Reset:**
   - Check each setting value
   - Test frontend to confirm default behavior

### **Expected Result:**
- ✅ Confirmation dialog appears
- ✅ All settings reset to defaults
- ✅ Frontend behavior returns to defaults

---

## 🎯 Test #9: Settings Persistence

### **What it controls:**
- Ensuring settings don't get lost

### **How to test:**

1. **Change Several Settings:**
   - Rows per page: 20
   - Enable all features
   - Style: Striped
   - Save settings

2. **Navigate Away:**
   - Go to Dashboard
   - Go to Create Table
   - Go to Charts
   - Come back to Settings

3. **Check Settings:**
   - All values should still be as you set them
   - Nothing should be reset

4. **Log Out and Log In:**
   - Log out of WordPress
   - Log back in
   - Go to Settings
   - All values should still be there

### **Expected Result:**
- ✅ Settings persist across page navigation
- ✅ Settings persist across logout/login
- ✅ No unexpected resets

---

## 🐛 Common Issues & Solutions

### Issue: Settings save but don't affect frontend

**Possible causes:**
1. **Caching plugin active** → Clear all caches
2. **Old shortcode attributes** → Remove specific attributes and let defaults apply
3. **Browser cache** → Hard refresh (Ctrl+Shift+R)
4. **Theme CSS conflicts** → Check browser console for errors

**Solution:**
```bash
# Clear WordPress cache
wp cache flush

# Or manually delete transients
wp transient delete --all
```

---

### Issue: Settings reset after save

**Possible causes:**
1. **Validation rejecting values** → Check if values are within allowed ranges
2. **Insufficient permissions** → Ensure you're logged in as admin
3. **Plugin conflict** → Deactivate other plugins temporarily

**Solution:**
- Check WordPress debug log for errors
- Ensure rows_per_page is between 1-100
- Ensure style is one of: default, striped, bordered, hover

---

### Issue: Some settings work, others don't

**Possible causes:**
1. **JavaScript not loaded** → Check browser console
2. **DataTables not initialized** → Check for JS errors
3. **Specific feature issue** → Test each feature individually

**Solution:**
- Open browser DevTools (F12)
- Check Console for errors
- Check Network tab for failed requests
- Report specific errors

---

## 📊 Quick Test Checklist

Use this checklist for a comprehensive settings test:

### General Settings
- [ ] Rows per page changes dashboard display
- [ ] Rows per page changes frontend default
- [ ] Table style changes visual appearance
- [ ] Responsive tables work on mobile

### Frontend Features
- [ ] Search can be enabled/disabled
- [ ] Sorting can be enabled/disabled
- [ ] Pagination can be enabled/disabled
- [ ] Export options work (when enabled)

### Data Formatting
- [ ] Date format changes examples
- [ ] Time format changes examples
- [ ] Decimal separator works
- [ ] Thousands separator works

### Performance
- [ ] Cache can be enabled/disabled
- [ ] Cache duration affects behavior
- [ ] Cache clears after expiration

### System
- [ ] Settings persist after save
- [ ] Settings persist after logout
- [ ] Reset to defaults works
- [ ] System info displays correctly

---

## 🎉 Success Criteria

Your settings are working correctly if:

1. ✅ All settings save without errors
2. ✅ Settings persist across page loads
3. ✅ Dashboard respects rows per page setting
4. ✅ Frontend tables respect all default settings
5. ✅ Shortcode attributes can override defaults
6. ✅ Disabling features removes them from frontend
7. ✅ Table style changes are visible
8. ✅ Reset to defaults restores original values

---

## 🔧 Debugging Tools

### Check Current Settings (WordPress Admin)

1. **Via Settings Page:**
   - Just view the Settings page
   - Current values are shown in all fields

2. **Via Database:**
   ```sql
   SELECT * FROM wp_options WHERE option_name = 'atables_settings';
   ```

3. **Via WP-CLI:**
   ```bash
   wp option get atables_settings
   ```

### View Settings in Code

Add this temporarily to `dashboard.php` to debug:

```php
<?php
$settings = get_option('atables_settings', array());
echo '<pre>';
print_r($settings);
echo '</pre>';
?>
```

---

## 📝 Reporting Issues

If settings still don't work after following this guide:

1. **Note which specific setting isn't working**
2. **Document the steps you took**
3. **Check browser console for errors** (F12)
4. **Export your settings:** Go to Settings → (future: Export button)
5. **Take screenshots** of the issue

---

## 🚀 Next Steps

Once all settings are working:

1. **Create documentation** for end users
2. **Add settings import/export** feature
3. **Add per-table settings** override
4. **Add role-based permissions** for settings

---

**Last Updated:** October 13, 2025
**Plugin Version:** 1.0.0

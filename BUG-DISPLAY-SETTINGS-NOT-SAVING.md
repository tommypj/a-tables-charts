# 🐛 BUG FOUND: Edit Table Display Settings Not Saving

## The Problem

When you toggle display settings (Search, Sorting, Pagination) to OFF and save:
1. ✅ The AJAX request works
2. ✅ The data IS saved to database
3. ❌ When you refresh, the toggle shows ON again
4. ❌ Frontend doesn't reflect the changes

## Root Cause

**File:** `src/modules/core/views/edit-table.php`
**Lines:** 185-226

The PHP logic for checking the radio buttons is WRONG:

```php
// WRONG LOGIC:
<?php checked( ! isset( $table_display_settings['enable_search'] ) || $table_display_settings['enable_search'] === true ); ?>
```

This says: "Check ON if the setting doesn't exist OR if it's true"

**Problem:** When the setting IS set to `false`, it still fails the condition!

## The Fix

Change the logic to properly handle three states:
1. Setting doesn't exist → use global default
2. Setting is explicitly `true` → check ON
3. Setting is explicitly `false` → check OFF

### Current Code (BROKEN):
```php
<input type="radio" 
       name="display_settings[enable_search]" 
       value="1" 
       id="search-on"
       <?php checked( ! isset( $table_display_settings['enable_search'] ) || $table_display_settings['enable_search'] === true ); ?>>
```

### Fixed Code:
```php
<?php
// Get the value: use table setting if exists, otherwise use global default
$search_enabled = isset( $table_display_settings['enable_search'] ) 
    ? $table_display_settings['enable_search'] 
    : $global_settings['enable_search'];
?>
<input type="radio" 
       name="display_settings[enable_search]" 
       value="1" 
       id="search-on"
       <?php checked( $search_enabled === true || $search_enabled === 1 || $search_enabled === '1' ); ?>>
```

## Files That Need Fixing

### 1. edit-table.php (Lines 185-226)

**Search Toggle - Line 185:**
```php
// BEFORE (WRONG):
<?php checked( ! isset( $table_display_settings['enable_search'] ) || $table_display_settings['enable_search'] === true ); ?>

// AFTER (CORRECT):
<?php
$search_enabled = isset( $table_display_settings['enable_search'] ) 
    ? $table_display_settings['enable_search'] 
    : $global_settings['enable_search'];
checked( $search_enabled === true || $search_enabled === 1 || $search_enabled === '1' );
?>
```

**Search OFF - Line 192:**
```php
// BEFORE (WRONG):
<?php checked( isset( $table_display_settings['enable_search'] ) && $table_display_settings['enable_search'] === false ); ?>

// AFTER (CORRECT):
<?php checked( $search_enabled === false || $search_enabled === 0 || $search_enabled === '0' ); ?>
```

**Sorting Toggle - Same pattern for lines 199, 206**

**Pagination Toggle - Same pattern for lines 213, 220**

## Why This Happens

### The Data Flow:

1. **JavaScript (edit-table.php line 468-472):**
   ```javascript
   const searchValue = $('input[name="display_settings[enable_search]"]:checked').val();
   displaySettings.enable_search = searchValue === '1';  // Converts to boolean
   ```
   ✅ This correctly sends `true` or `false`

2. **PHP Controller (TableController.php lines 511-521):**
   ```php
   if ( $input[ $key ] === 'false' || $input[ $key ] === '0' || ... ) {
       $sanitized[ $key ] = false;
   } elseif ( $input[ $key ] === 'true' || $input[ $key ] === '1' || ... ) {
       $sanitized[ $key ] = true;
   }
   ```
   ✅ This correctly converts to boolean

3. **Database:**
   ```json
   {"enable_search": false, "enable_sorting": true, "enable_pagination": true}
   ```
   ✅ Stored correctly as JSON

4. **PHP Reading (Table.php lines 285-291):**
   ```php
   public function get_display_settings() {
       if ( empty( $this->display_settings ) ) {
           return array();
       }
       $settings = json_decode( $this->display_settings, true );
       return is_array( $settings ) ? $settings : array();
   }
   ```
   ✅ Returns correct array with `false` values

5. **PHP Template (edit-table.php line 185):**
   ```php
   <?php checked( ! isset( ... ) || ... === true ); ?>
   ```
   ❌ **BUG HERE!** Wrong logic doesn't handle `false` correctly

## Test Plan

After fixing:

1. **Set Search to OFF**
   - Click OFF radio button
   - Click Save
   - **Expected:** Toast shows "Table updated successfully!"

2. **Refresh the page**
   - **Expected:** Search toggle shows OFF ✅
   - **Before fix:** Search toggle shows ON ❌

3. **Check frontend**
   - View the table on frontend with shortcode
   - **Expected:** No search box visible ✅
   - **Before fix:** Search box still visible ❌

4. **Set Search back to ON**
   - Click ON radio button
   - Click Save
   - Refresh
   - **Expected:** Search toggle shows ON ✅
   - **Frontend:** Search box visible ✅

## Impact

This same bug affects:
- ✅ Search toggle
- ✅ Sorting toggle  
- ✅ Pagination toggle

All three need the same fix!

## Summary

**The bug is NOT in:**
- ❌ JavaScript (works correctly)
- ❌ AJAX (works correctly)
- ❌ Controller (works correctly)
- ❌ Database (saves correctly)

**The bug IS in:**
- ✅ The PHP template logic for displaying radio button checked states

**Fix:** Change the `checked()` logic to properly evaluate boolean values.

---

## Ready to Fix?

I can apply these fixes now. Should I proceed?

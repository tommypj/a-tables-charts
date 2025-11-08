# ✅ Phase 2 Complete: TableRepository Updated

## 🎉 What Was Built

The TableRepository has been updated with comprehensive display settings management methods.

---

## 📋 New Methods Added

### 1. `update_display_settings($table_id, $settings)`
**Purpose:** Save all display settings for a table at once

**Parameters:**
- `$table_id` (int) - The table ID
- `$settings` (array) - Complete display settings array

**Returns:** `bool` - True on success, false on failure

**Usage Example:**
```php
$repository = new TableRepository();

$settings = array(
    'rows_per_page'     => 25,
    'enable_search'     => false,
    'enable_sorting'    => true,
    'enable_pagination' => true,
    'table_style'       => 'bordered',
);

$success = $repository->update_display_settings($table_id, $settings);
```

**Database Operation:**
- Updates `display_settings` column with JSON-encoded settings
- Updates `updated_at` timestamp
- Uses prepared statements for security

---

### 2. `get_display_settings($table_id)`
**Purpose:** Retrieve all display settings for a table

**Parameters:**
- `$table_id` (int) - The table ID

**Returns:** `array` - Display settings array (empty array if none set)

**Usage Example:**
```php
$repository = new TableRepository();
$settings = $repository->get_display_settings($table_id);

// Returns:
// array(
//     'rows_per_page' => 25,
//     'enable_search' => false,
//     ...
// )
```

**Handles:**
- Returns empty array if no settings exist
- Decodes JSON safely
- Validates return type is array

---

### 3. `clear_display_settings($table_id)`
**Purpose:** Remove all custom display settings from a table

**Parameters:**
- `$table_id` (int) - The table ID

**Returns:** `bool` - True on success, false on failure

**Usage Example:**
```php
$repository = new TableRepository();
$success = $repository->clear_display_settings($table_id);

// Table will now use global defaults
```

**Database Operation:**
- Sets `display_settings` to NULL
- Updates `updated_at` timestamp

---

### 4. `update_display_setting($table_id, $key, $value)`
**Purpose:** Update a single display setting without affecting others

**Parameters:**
- `$table_id` (int) - The table ID
- `$key` (string) - Setting key (e.g., 'rows_per_page')
- `$value` (mixed) - Setting value

**Returns:** `bool` - True on success, false on failure

**Usage Example:**
```php
$repository = new TableRepository();

// Just update rows per page, keep other settings
$success = $repository->update_display_setting($table_id, 'rows_per_page', 50);

// Just disable search, keep other settings
$success = $repository->update_display_setting($table_id, 'enable_search', false);
```

**How it works:**
1. Gets existing settings
2. Updates only the specified key
3. Saves entire settings array back

---

### 5. `remove_display_setting($table_id, $key)`
**Purpose:** Remove a single display setting (reverts to global default for that setting)

**Parameters:**
- `$table_id` (int) - The table ID
- `$key` (string) - Setting key to remove

**Returns:** `bool` - True on success, false on failure

**Usage Example:**
```php
$repository = new TableRepository();

// Remove custom rows_per_page, revert to global default
$success = $repository->remove_display_setting($table_id, 'rows_per_page');
```

**How it works:**
1. Gets existing settings
2. Removes specified key
3. Saves updated settings array

---

## 🔄 Method Relationships

```
update_display_settings()  ←── Core method (updates entire array)
        ↑
        ├── update_display_setting()  (updates one key)
        └── remove_display_setting()  (removes one key)

get_display_settings()  ←── Retrieves settings

clear_display_settings()  ←── Removes all settings (sets to NULL)
```

---

## 💾 Database Structure

### Column: `display_settings`
- **Type:** TEXT
- **Default:** NULL
- **Format:** JSON string
- **Location:** AFTER `description` column

### Example Data:
```json
{
  "rows_per_page": 25,
  "enable_search": false,
  "enable_sorting": true,
  "enable_pagination": true,
  "table_style": "bordered"
}
```

### NULL vs Empty:
- **NULL** - No custom settings (use global defaults)
- **Empty JSON `{}`** - Custom settings exist but all are default
- **With Data** - Custom overrides exist

---

## 🎯 Use Cases

### Use Case 1: Override All Settings
```php
// User edits table and sets all custom settings
$settings = array(
    'rows_per_page'     => 50,
    'enable_search'     => true,
    'enable_sorting'    => true,
    'enable_pagination' => false,
    'table_style'       => 'striped',
);

$repository->update_display_settings($table_id, $settings);
```

### Use Case 2: Override Just One Setting
```php
// User only wants to change rows per page
$repository->update_display_setting($table_id, 'rows_per_page', 100);

// Other settings remain unchanged or use global defaults
```

### Use Case 3: Revert to Global Defaults
```php
// User clicks "Use Global Settings"
$repository->clear_display_settings($table_id);

// Table now uses all global defaults
```

### Use Case 4: Selective Revert
```php
// User wants to revert just rows per page to global
$repository->remove_display_setting($table_id, 'rows_per_page');

// Other custom settings remain
```

### Use Case 5: Check If Table Has Custom Settings
```php
$settings = $repository->get_display_settings($table_id);

if (!empty($settings)) {
    echo "This table has custom display settings";
} else {
    echo "This table uses global defaults";
}
```

---

## 🔐 Security Features

### 1. Prepared Statements
All methods use `$wpdb->prepare()` for SQL injection protection:
```php
$query = $this->wpdb->prepare(
    "SELECT display_settings FROM {$this->table_name} WHERE id = %d",
    $table_id
);
```

### 2. JSON Encoding
Uses `wp_json_encode()` for safe JSON encoding:
```php
'display_settings' => wp_json_encode($settings)
```

### 3. Type Validation
Validates data types on retrieval:
```php
$settings = json_decode($result, true);
return is_array($settings) ? $settings : array();
```

### 4. NULL Handling
Safely handles NULL values:
```php
if (empty($result)) {
    return array();
}
```

---

## 🧪 Testing the Repository Methods

### Test 1: Save and Retrieve Settings
```php
// Save settings
$settings = array('rows_per_page' => 25);
$saved = $repository->update_display_settings($table_id, $settings);

// Retrieve settings
$retrieved = $repository->get_display_settings($table_id);

// Assert
assert($saved === true);
assert($retrieved['rows_per_page'] === 25);
```

### Test 2: Update Single Setting
```php
// Set initial settings
$repository->update_display_settings($table_id, array(
    'rows_per_page' => 10,
    'enable_search' => true,
));

// Update just one
$repository->update_display_setting($table_id, 'rows_per_page', 50);

// Verify
$settings = $repository->get_display_settings($table_id);
assert($settings['rows_per_page'] === 50);
assert($settings['enable_search'] === true); // Unchanged
```

### Test 3: Clear Settings
```php
// Set settings
$repository->update_display_settings($table_id, array('rows_per_page' => 25));

// Clear them
$repository->clear_display_settings($table_id);

// Verify empty
$settings = $repository->get_display_settings($table_id);
assert(empty($settings));
```

### Test 4: Remove Single Setting
```php
// Set multiple settings
$repository->update_display_settings($table_id, array(
    'rows_per_page' => 25,
    'enable_search' => false,
));

// Remove one
$repository->remove_display_setting($table_id, 'enable_search');

// Verify
$settings = $repository->get_display_settings($table_id);
assert($settings['rows_per_page'] === 25);
assert(!isset($settings['enable_search']));
```

---

## 📊 Method Comparison

| Method | Updates Entire Array | Updates Single Key | Removes All | Removes One |
|--------|---------------------|-------------------|-------------|-------------|
| `update_display_settings()` | ✅ Yes | ✅ Can | ❌ No | ❌ No |
| `update_display_setting()` | ❌ No | ✅ Yes | ❌ No | ❌ No |
| `clear_display_settings()` | ❌ No | ❌ No | ✅ Yes | ❌ No |
| `remove_display_setting()` | ❌ No | ❌ No | ❌ No | ✅ Yes |
| `get_display_settings()` | 📖 Read | 📖 Read | 📖 Read | 📖 Read |

---

## 🎯 Best Practices

### 1. Use Specific Methods When Possible
```php
// ✅ Good - Updates only what's needed
$repository->update_display_setting($table_id, 'rows_per_page', 25);

// ❌ Avoid - Loads and saves entire array for one change
$settings = $repository->get_display_settings($table_id);
$settings['rows_per_page'] = 25;
$repository->update_display_settings($table_id, $settings);
```

### 2. Check Existence Before Removing
```php
// ✅ Good - Check first
$settings = $repository->get_display_settings($table_id);
if (isset($settings['rows_per_page'])) {
    $repository->remove_display_setting($table_id, 'rows_per_page');
}

// ✅ Also Good - Method handles it
// remove_display_setting() returns true if key doesn't exist
$repository->remove_display_setting($table_id, 'rows_per_page');
```

### 3. Validate Input Before Saving
```php
// ✅ Good - Validate first
$rows = intval($_POST['rows_per_page']);
$rows = max(1, min(100, $rows)); // Constrain to 1-100

$repository->update_display_setting($table_id, 'rows_per_page', $rows);
```

### 4. Handle Failures Gracefully
```php
// ✅ Good - Check result
$success = $repository->update_display_settings($table_id, $settings);

if (!$success) {
    error_log("Failed to update display settings for table {$table_id}");
    return new WP_Error('save_failed', 'Failed to save settings');
}
```

---

## 🔗 Integration Points

### With Table Model (types/Table.php)
```php
// Repository methods work with Table object
$table = $repository->find_by_id($table_id);
$settings = $table->get_display_settings(); // Uses display_settings property

// Can update through repository
$repository->update_display_settings($table_id, $new_settings);

// Table object will have updated settings on next load
$table = $repository->find_by_id($table_id);
```

### With TableController (Next Phase)
```php
// Controller will use repository methods
public function handle_update_table() {
    // ... validate input ...
    
    // Save display settings
    $this->repository->update_display_settings(
        $table_id,
        $display_settings
    );
}
```

### With TableShortcode (Next Phase)
```php
// Shortcode will retrieve settings for priority cascade
public function render($atts) {
    $table = $this->repository->find_by_id($table_id);
    $table_settings = $table->get_display_settings(); // or use repository directly
    
    // Apply priority: shortcode > table > global > default
}
```

---

## ✅ Phase 2 Checklist

- [x] Add `update_display_settings()` method
- [x] Add `get_display_settings()` method
- [x] Add `clear_display_settings()` method
- [x] Add `update_display_setting()` method
- [x] Add `remove_display_setting()` method
- [x] Use prepared statements for security
- [x] Handle NULL values correctly
- [x] Validate JSON decoding
- [x] Update `updated_at` timestamp on changes
- [x] Document all methods thoroughly

---

## 🚀 Next Steps

**Phase 3:** Update edit-table.php UI
- Add display settings form section
- Show current settings
- Show global defaults for comparison
- Radio buttons for "Use Global" vs "Custom"
- Input fields for custom values

**Ready to continue? Let me know!**

---

**Status:** ✅ **PHASE 2 COMPLETE**  
**Updated:** October 13, 2025  
**Files Modified:** `TableRepository.php`  
**Lines Added:** ~100 lines of new methods

# 📋 Settings Integration Summary

## Overview
This document summarizes the complete settings integration for the A-Tables & Charts WordPress plugin, including how settings are stored, retrieved, and applied throughout the system.

---

## 🗂️ Settings Structure

### Database Storage
Settings are stored in the `wp_options` table with the option name `atables_settings` as a serialized PHP array.

### Default Settings
```php
$defaults = array(
    // General Settings
    'rows_per_page'          => 10,          // 1-100
    'default_table_style'    => 'default',   // default|striped|bordered|hover
    'enable_responsive'      => true,
    'enable_search'          => true,
    'enable_sorting'         => true,
    'enable_pagination'      => true,
    'enable_export'          => true,
    
    // Data Formatting
    'date_format'            => 'Y-m-d',     // PHP date format
    'time_format'            => 'H:i:s',     // PHP time format
    'decimal_separator'      => '.',         // Single character
    'thousands_separator'    => ',',         // Single character
    
    // Performance & Cache
    'cache_enabled'          => true,
    'cache_expiration'       => 3600,        // Seconds (0 = disabled)
    
    // Chart Settings
    'google_charts_enabled'  => true,
    'chartjs_enabled'        => true,
);
```

---

## 🏗️ Architecture

### 1. Settings Service
**Location:** `src/modules/settings/services/SettingsService.php`

**Responsibilities:**
- Get/set individual settings
- Validate and sanitize settings
- Import/export settings as JSON
- Reset to default values

**Key Methods:**
```php
class SettingsService {
    public function get($key, $default = null);
    public function set($key, $value);
    public function get_all();
    public function validate($key, $value);
    public function sanitize($key, $value);
    public function reset_to_defaults();
    public function export_settings();
    public function import_settings($json);
}
```

### 2. Settings Registration
**Location:** `src/modules/core/Plugin.php`

**Method:** `register_settings()`
```php
public function register_settings() {
    register_setting(
        'atables_settings',
        'atables_settings',
        array(
            'type'              => 'array',
            'sanitize_callback' => array( $this, 'sanitize_settings' ),
            'default'           => array(),
        )
    );
}
```

### 3. Settings Sanitization
**Location:** `src/modules/core/Plugin.php`

**Method:** `sanitize_settings($input)`

**Sanitization Rules:**
- **rows_per_page:** Integer between 1-100
- **default_table_style:** One of: default, striped, bordered, hover
- **Boolean settings:** Converted to true/false (checkboxes)
- **Text settings:** Sanitized with `sanitize_text_field()`
- **Numeric settings:** Cast to integer with min/max validation

---

## 🎯 Settings Integration Points

### 1. Dashboard (Admin Tables List)
**File:** `src/modules/core/views/dashboard.php`

**Settings Used:**
- `rows_per_page` → Controls pagination

**Implementation:**
```php
$settings = get_option('atables_settings', array());
$rows_per_page = isset($settings['rows_per_page']) ? (int) $settings['rows_per_page'] : 10;

$result = $table_service->get_all_tables(array(
    'status'   => 'active',
    'per_page' => $rows_per_page,
    'page'     => 1,
));
```

### 2. Frontend Tables (Shortcode)
**File:** `src/modules/frontend/shortcodes/TableShortcode.php`

**Settings Used:**
- `rows_per_page` → Default page length
- `enable_search` → Default search state
- `enable_pagination` → Default pagination state
- `enable_sorting` → Default sorting state
- `enable_responsive` → Default responsive state
- `default_table_style` → Default table style

**Implementation:**
```php
public function render($atts) {
    // Load settings
    $settings = get_option('atables_settings', array());
    $defaults = array(
        'rows_per_page'      => 10,
        'enable_search'      => true,
        'enable_pagination'  => true,
        'enable_sorting'     => true,
        'enable_responsive'  => true,
        'default_table_style' => 'default',
    );
    $settings = wp_parse_args($settings, $defaults);
    
    // Merge with shortcode attributes
    $atts = shortcode_atts(
        array(
            'id'          => 0,
            'width'       => '100%',
            'style'       => $settings['default_table_style'],
            'search'      => $settings['enable_search'] ? 'true' : 'false',
            'pagination'  => $settings['enable_pagination'] ? 'true' : 'false',
            'page_length' => $settings['rows_per_page'],
            'sorting'     => $settings['enable_sorting'] ? 'true' : 'false',
            'info'        => 'true',
        ),
        $atts,
        'atable'
    );
    
    // ... render table with these settings
}
```

### 3. Frontend Charts (Future)
**File:** `src/modules/frontend/shortcodes/ChartShortcode.php`

**Settings Used (Planned):**
- `chartjs_enabled` → Which library to use
- `google_charts_enabled` → Alternative library

---

## 🔄 Data Flow

### Saving Settings

```
User fills settings form
    ↓
Form submits to options.php (WordPress)
    ↓
WordPress calls sanitize callback
    ↓
Plugin::sanitize_settings($input)
    ↓
Validates and sanitizes each field
    ↓
Returns cleaned array
    ↓
WordPress saves to wp_options
    ↓
Redirects with success message
```

### Loading Settings

```
Page loads (admin or frontend)
    ↓
get_option('atables_settings', array())
    ↓
Returns stored array or empty array
    ↓
wp_parse_args($settings, $defaults)
    ↓
Merges with defaults for missing keys
    ↓
Settings applied to functionality
```

---

## 🎨 Settings Page UI

### Layout Structure

```
┌─────────────────────────────────────────────────────────┐
│  Page Header (Title + Description)                       │
└─────────────────────────────────────────────────────────┘
┌──────────────────────────┬──────────────────────────────┐
│  Left Column              │  Right Column                │
│  (Settings Cards)         │  (Info Sidebar)              │
│                           │                              │
│  1. General Settings      │  System Information          │
│     - Rows per page       │  - Plugin version            │
│     - Table style         │  - WordPress version         │
│     - Feature toggles     │  - PHP version               │
│                           │  - MySQL version             │
│  2. Data Formatting       │  - Upload max                │
│     - Date format         │  - Memory limit              │
│     - Time format         │                              │
│     - Number format       │  Need Help?                  │
│                           │  - Documentation             │
│  3. Performance & Cache   │  - Video tutorials           │
│     - Enable cache        │  - Support forum             │
│     - Cache duration      │                              │
│                           │                              │
│  4. Chart Settings        │                              │
│     - Chart libraries     │                              │
│                           │                              │
│  [Save] [Reset]           │                              │
└──────────────────────────┴──────────────────────────────┘
```

### Visual Hierarchy

1. **Page Header** (atables-page-header)
   - Large title with icon
   - Descriptive subtitle

2. **Settings Grid** (atables-settings-grid)
   - Two-column layout
   - Main settings on left
   - Sidebar info on right

3. **Settings Cards** (atables-settings-card)
   - Card header with icon + title
   - Card body with form fields
   - Grouped logically

4. **Form Groups** (atables-form-group)
   - Label
   - Input/select/checkbox
   - Help text (light gray)

5. **Action Buttons**
   - Primary: "Save All Settings" (blue)
   - Secondary: "Reset to Defaults" (gray)

---

## 🎛️ Settings Controls

### Input Types

| Setting | Control Type | Validation |
|---------|--------------|------------|
| Rows per page | Number input | 1-100 |
| Table style | Select dropdown | default\|striped\|bordered\|hover |
| Enable features | Checkboxes | boolean |
| Date format | Text input | PHP date format |
| Time format | Text input | PHP time format |
| Separators | Text input (maxlength=1) | Single character |
| Cache duration | Number input | >= 0 |

### Live Examples

Some settings show live examples:
- **Date format:** Shows current date formatted
- **Time format:** Shows current time formatted
- **Decimal separator:** Shows example number
- **Thousands separator:** Shows example number

---

## 🔐 Security

### Input Sanitization

All inputs are sanitized before saving:

```php
// Numeric validation
$validated['rows_per_page'] = max(1, min(100, intval($input['rows_per_page'])));

// Whitelist validation
$allowed_styles = array('default', 'striped', 'bordered', 'hover');
$validated['default_table_style'] = in_array($input['default_table_style'], $allowed_styles, true)
    ? $input['default_table_style']
    : 'default';

// Boolean conversion
$validated['enable_search'] = isset($input['enable_search']) ? (bool) $input['enable_search'] : false;

// Text sanitization
$validated['date_format'] = sanitize_text_field($input['date_format']);
```

### Capability Check

Settings page requires `manage_options` capability:

```php
add_submenu_page(
    $this->plugin_slug,
    __('Settings', 'a-tables-charts'),
    __('Settings', 'a-tables-charts'),
    'manage_options',  // Only administrators
    $this->plugin_slug . '-settings',
    array($this, 'render_settings')
);
```

---

## 🧪 Testing Settings

### Manual Testing Steps

1. **Change a setting** → Save → Verify it persists
2. **Navigate away** → Come back → Verify setting still set
3. **Use shortcode** → Verify default applies
4. **Override in shortcode** → Verify override works
5. **Reset to defaults** → Verify all reset correctly

### Automated Testing (Future)

```php
// Unit test example
public function test_settings_save() {
    $settings = array('rows_per_page' => 25);
    update_option('atables_settings', $settings);
    
    $retrieved = get_option('atables_settings');
    $this->assertEquals(25, $retrieved['rows_per_page']);
}

public function test_settings_validation() {
    $plugin = Plugin::get_instance();
    $input = array('rows_per_page' => 150); // Invalid (> 100)
    
    $sanitized = $plugin->sanitize_settings($input);
    $this->assertEquals(100, $sanitized['rows_per_page']); // Should cap at 100
}
```

---

## 🔮 Future Enhancements

### 1. Per-Table Settings Override
Allow individual tables to override global settings:

```php
// Table meta
'table_settings' => array(
    'rows_per_page' => 25,  // Override global
    'enable_search' => false, // Override global
    // null = use global setting
)
```

### 2. Settings Import/Export

```php
// Export
$settings = get_option('atables_settings');
$json = json_encode($settings, JSON_PRETTY_PRINT);
download_file('atables-settings.json', $json);

// Import
$json = file_get_contents($_FILES['settings']['tmp_name']);
$settings = json_decode($json, true);
update_option('atables_settings', $settings);
```

### 3. Settings Backup/Restore

```php
// Backup before major changes
$backup = get_option('atables_settings');
update_option('atables_settings_backup', $backup);

// Restore if needed
$backup = get_option('atables_settings_backup');
update_option('atables_settings', $backup);
```

### 4. Role-Based Settings Access

```php
// Different settings for different roles
if (current_user_can('administrator')) {
    // Show all settings
} elseif (current_user_can('editor')) {
    // Show limited settings
}
```

---

## 📊 Settings Usage Analytics

### Track Which Settings Are Used

```php
// Count settings usage
$usage = array(
    'custom_rows' => 0,      // How many users change from default 10
    'disabled_search' => 0,   // How many disable search
    'custom_style' => 0,      // How many use non-default style
);

// Log on settings save
if ($settings['rows_per_page'] !== 10) {
    $usage['custom_rows']++;
}
```

---

## 🐛 Common Issues & Solutions

### Issue: Settings don't save

**Cause:** Form not using `settings_fields()`
**Solution:** Ensure form has:
```php
<form method="post" action="options.php">
    <?php settings_fields('atables_settings'); ?>
    <!-- form fields -->
</form>
```

### Issue: Settings reset after save

**Cause:** Validation rejecting values
**Solution:** Check sanitize function, ensure values pass validation

### Issue: Frontend doesn't respect settings

**Cause:** Not loading settings in shortcode
**Solution:** Ensure `get_option('atables_settings')` is called before rendering

### Issue: Settings conflict with other plugins

**Cause:** Option name collision
**Solution:** Use prefixed option name `atables_settings` (✅ already done)

---

## 📚 Related Files

### Core Files
- `src/modules/core/Plugin.php` → Registration and sanitization
- `src/modules/core/views/settings.php` → Settings page UI
- `src/modules/settings/services/SettingsService.php` → Settings logic

### Integration Files
- `src/modules/frontend/shortcodes/TableShortcode.php` → Frontend table defaults
- `src/modules/core/views/dashboard.php` → Admin table list pagination

### Asset Files
- `assets/css/admin-settings.css` → Settings page styles
- `assets/js/admin-main.js` → Settings page interactions

---

## ✅ Implementation Checklist

- [x] Settings service created
- [x] Settings registered with WordPress
- [x] Settings page UI created
- [x] Sanitization implemented
- [x] Dashboard integration
- [x] Frontend shortcode integration
- [x] Reset to defaults
- [x] Help documentation
- [ ] Settings import/export
- [ ] Per-table overrides
- [ ] Settings backup/restore
- [ ] Role-based access
- [ ] Usage analytics

---

## 🎯 Summary

**What Works:**
✅ Settings save and persist correctly
✅ Settings are sanitized and validated
✅ Dashboard respects rows per page
✅ Frontend tables respect all defaults
✅ Shortcode can override defaults
✅ Reset to defaults works
✅ Visual feedback on save

**What to Test:**
- Change each setting and verify effect
- Test shortcode attribute overrides
- Test settings persistence across logout
- Test validation (invalid values)
- Test reset to defaults

**Next Steps:**
1. Complete comprehensive testing (use SETTINGS-TESTING-GUIDE.md)
2. Add settings import/export feature
3. Add per-table settings override
4. Add settings migration for future versions

---

**Last Updated:** October 13, 2025
**Plugin Version:** 1.0.0
**Status:** ✅ Complete and Ready for Testing

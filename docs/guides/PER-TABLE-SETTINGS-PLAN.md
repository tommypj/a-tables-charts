# 📋 Per-Table Settings - Implementation Plan

## Overview
Add per-table display settings to allow customization of individual tables, overriding global settings.

---

## 🎯 Goals

1. **Add settings section** to table edit page
2. **Store settings** in table metadata
3. **Allow overrides** of global settings per table
4. **Maintain priority cascade**: Shortcode → Table → Global → Defaults
5. **Keep it simple** - only override what's needed

---

## 🏗️ Database Structure

### Option 1: JSON in existing table (Recommended)
```sql
ALTER TABLE wp_atables_tables 
ADD COLUMN display_settings TEXT DEFAULT NULL AFTER description;

-- Stores JSON like:
{
  "rows_per_page": 25,           -- Override global
  "enable_search": false,         -- Override global
  "enable_pagination": null,      -- Use global (null = use global)
  "table_style": "bordered",      -- Override global
  "enable_sorting": null          -- Use global
}
```

### Option 2: Separate Meta Table (More Flexible)
```sql
CREATE TABLE wp_atables_table_meta (
  meta_id bigint(20) NOT NULL AUTO_INCREMENT,
  table_id bigint(20) NOT NULL,
  meta_key varchar(255) NOT NULL,
  meta_value longtext,
  PRIMARY KEY (meta_id),
  KEY table_id (table_id),
  KEY meta_key (meta_key)
);

-- Stores like WordPress post meta:
table_id | meta_key           | meta_value
---------|-------------------|------------
1        | rows_per_page     | 25
1        | enable_search     | 0
1        | table_style       | bordered
```

**Recommendation:** Use **Option 1** (JSON column) for simplicity since we only need display settings.

---

## 🎨 UI Design

### Location
**File:** `src/modules/core/views/edit-table.php`

**Position:** Add new section after "Table Information" and before data grid

### Layout

```
┌─────────────────────────────────────────────────────────────┐
│  Edit Table                                                  │
├─────────────────────────────────────────────────────────────┤
│  📊 Table Information                                        │
│  Title: [My Table                                    ]       │
│  Description: [                                      ]       │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│  🎨 Display Settings                                         │
│                                                              │
│  ℹ️ Override global settings for this specific table.       │
│     Leave empty to use global defaults.                     │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ Frontend Display                                      │  │
│  │                                                       │  │
│  │ Rows per Page                                        │  │
│  │ ( ) Use Global Default (10)                          │  │
│  │ (•) Custom: [25]                                     │  │
│  │                                                       │  │
│  │ Table Style                                          │  │
│  │ ( ) Use Global Default (Striped)                     │  │
│  │ (•) Custom: [Bordered ▼]                            │  │
│  │                                                       │  │
│  │ Features                                             │  │
│  │ Search:     ( ) Use Global  (•) Enable  ( ) Disable │  │
│  │ Sorting:    (•) Use Global  ( ) Enable  ( ) Disable │  │
│  │ Pagination: (•) Use Global  ( ) Enable  ( ) Disable │  │
│  │                                                       │  │
│  │ Preview: [Show Preview]                              │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│  📝 Table Data                                               │
│  [Data grid here...]                                         │
└─────────────────────────────────────────────────────────────┘
```

### Alternative: Compact Toggle Design

```
┌─────────────────────────────────────────────────────────────┐
│  🎨 Display Settings                                         │
│                                                              │
│  ┌─────────────────┬──────────────┬─────────────────────┐  │
│  │ Setting         │ Global       │ This Table          │  │
│  ├─────────────────┼──────────────┼─────────────────────┤  │
│  │ Rows per Page   │ 10           │ [•] Override: [25 ] │  │
│  │ Table Style     │ Striped      │ [•] Override: [▼]   │  │
│  │ Search          │ Enabled      │ [ ] Use Global      │  │
│  │ Sorting         │ Enabled      │ [•] Use Global      │  │
│  │ Pagination      │ Enabled      │ [•] Use Global      │  │
│  └─────────────────┴──────────────┴─────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

---

## 💻 Implementation Steps

### Step 1: Database Migration
```php
// src/modules/tables/migrations/AddDisplaySettings.php

public function up() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'atables_tables';
    
    $wpdb->query("
        ALTER TABLE {$table_name}
        ADD COLUMN display_settings TEXT DEFAULT NULL
        AFTER description
    ");
}
```

### Step 2: Update Table Model
```php
// src/modules/tables/models/Table.php

class Table {
    public $display_settings; // Add new property
    
    public function get_display_settings() {
        if (empty($this->display_settings)) {
            return array();
        }
        return json_decode($this->display_settings, true);
    }
    
    public function set_display_settings($settings) {
        $this->display_settings = json_encode($settings);
    }
    
    public function get_display_setting($key, $default = null) {
        $settings = $this->get_display_settings();
        return isset($settings[$key]) ? $settings[$key] : $default;
    }
}
```

### Step 3: Update TableRepository
```php
// src/modules/tables/repositories/TableRepository.php

public function update_display_settings($table_id, $settings) {
    global $wpdb;
    
    $result = $wpdb->update(
        $wpdb->prefix . 'atables_tables',
        array(
            'display_settings' => json_encode($settings),
            'updated_at' => current_time('mysql')
        ),
        array('id' => $table_id),
        array('%s', '%s'),
        array('%d')
    );
    
    return $result !== false;
}
```

### Step 4: Update TableShortcode
```php
// src/modules/frontend/shortcodes/TableShortcode.php

public function render($atts) {
    // 1. Load global settings
    $global_settings = get_option('atables_settings', array());
    
    // 2. Get table
    $table = $this->repository->find_by_id($table_id);
    
    // 3. Get table-specific settings
    $table_settings = $table->get_display_settings();
    
    // 4. Merge priority: shortcode > table > global > defaults
    $defaults = array(
        'rows_per_page' => 10,
        'enable_search' => true,
        // ... more defaults
    );
    
    // Apply global settings
    $settings = wp_parse_args($global_settings, $defaults);
    
    // Apply table-specific overrides (only non-null values)
    foreach ($table_settings as $key => $value) {
        if ($value !== null) {
            $settings[$key] = $value;
        }
    }
    
    // 5. Apply shortcode attributes (highest priority)
    $atts = shortcode_atts(
        array(
            'id' => 0,
            'page_length' => $settings['rows_per_page'],
            'search' => $settings['enable_search'] ? 'true' : 'false',
            'style' => $settings['default_table_style'],
            // ... more attributes
        ),
        $atts,
        'atable'
    );
    
    // ... render table
}
```

### Step 5: Update Edit Page UI
```php
// src/modules/core/views/edit-table.php

$table_settings = $table->get_display_settings();
$global_settings = get_option('atables_settings', array());

?>
<div class="atables-settings-section">
    <h2>🎨 Display Settings</h2>
    <p class="description">
        Override global settings for this table. Leave as "Use Global" to inherit from plugin settings.
    </p>
    
    <table class="form-table">
        <tr>
            <th scope="row">Rows per Page</th>
            <td>
                <label>
                    <input type="radio" 
                           name="display_settings[rows_per_page_mode]" 
                           value="global" 
                           <?php checked(empty($table_settings['rows_per_page'])); ?>>
                    Use Global (<?php echo esc_html($global_settings['rows_per_page'] ?? 10); ?>)
                </label>
                <br>
                <label>
                    <input type="radio" 
                           name="display_settings[rows_per_page_mode]" 
                           value="custom"
                           <?php checked(!empty($table_settings['rows_per_page'])); ?>>
                    Custom: 
                    <input type="number" 
                           name="display_settings[rows_per_page]" 
                           value="<?php echo esc_attr($table_settings['rows_per_page'] ?? ''); ?>"
                           min="1" 
                           max="100"
                           class="small-text">
                </label>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Table Style</th>
            <td>
                <label>
                    <input type="radio" 
                           name="display_settings[table_style_mode]" 
                           value="global"
                           <?php checked(empty($table_settings['table_style'])); ?>>
                    Use Global (<?php echo esc_html($global_settings['default_table_style'] ?? 'default'); ?>)
                </label>
                <br>
                <label>
                    <input type="radio" 
                           name="display_settings[table_style_mode]" 
                           value="custom"
                           <?php checked(!empty($table_settings['table_style'])); ?>>
                    Custom: 
                    <select name="display_settings[table_style]">
                        <option value="">--</option>
                        <option value="default" <?php selected($table_settings['table_style'] ?? '', 'default'); ?>>Default</option>
                        <option value="striped" <?php selected($table_settings['table_style'] ?? '', 'striped'); ?>>Striped</option>
                        <option value="bordered" <?php selected($table_settings['table_style'] ?? '', 'bordered'); ?>>Bordered</option>
                        <option value="hover" <?php selected($table_settings['table_style'] ?? '', 'hover'); ?>>Hover</option>
                    </select>
                </label>
            </td>
        </tr>
        
        <tr>
            <th scope="row">Features</th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text">Search</legend>
                    <label>Search: </label>
                    <label>
                        <input type="radio" name="display_settings[enable_search]" value="" <?php checked(!isset($table_settings['enable_search'])); ?>>
                        Use Global
                    </label>
                    <label>
                        <input type="radio" name="display_settings[enable_search]" value="1" <?php checked($table_settings['enable_search'] ?? null, 1); ?>>
                        Enable
                    </label>
                    <label>
                        <input type="radio" name="display_settings[enable_search]" value="0" <?php checked($table_settings['enable_search'] ?? null, 0); ?>>
                        Disable
                    </label>
                </fieldset>
                
                <!-- Repeat for sorting, pagination, etc. -->
            </td>
        </tr>
    </table>
</div>
<?php
```

### Step 6: Handle Save
```php
// src/modules/tables/controllers/TableController.php

public function handle_update_table() {
    // ... existing validation ...
    
    // Handle display settings
    if (isset($_POST['display_settings'])) {
        $display_settings = array();
        
        // Rows per page
        if (isset($_POST['display_settings']['rows_per_page_mode']) && 
            $_POST['display_settings']['rows_per_page_mode'] === 'custom') {
            $display_settings['rows_per_page'] = max(1, min(100, intval($_POST['display_settings']['rows_per_page'])));
        }
        
        // Table style
        if (isset($_POST['display_settings']['table_style_mode']) && 
            $_POST['display_settings']['table_style_mode'] === 'custom') {
            $allowed_styles = array('default', 'striped', 'bordered', 'hover');
            $style = sanitize_text_field($_POST['display_settings']['table_style']);
            if (in_array($style, $allowed_styles)) {
                $display_settings['table_style'] = $style;
            }
        }
        
        // Features (search, sorting, pagination)
        $features = array('enable_search', 'enable_sorting', 'enable_pagination');
        foreach ($features as $feature) {
            if (isset($_POST['display_settings'][$feature])) {
                $value = $_POST['display_settings'][$feature];
                if ($value !== '') {
                    $display_settings[$feature] = (bool) $value;
                }
            }
        }
        
        // Save display settings
        $this->repository->update_display_settings($table_id, $display_settings);
    }
    
    // ... rest of update logic ...
}
```

---

## 🎯 User Workflow

### Scenario 1: Use Global Settings (Default)
1. User creates/edits table
2. Doesn't touch display settings (all "Use Global")
3. Table inherits from plugin settings
4. Easy and automatic

### Scenario 2: Override for Specific Table
1. User has global setting: rows = 10
2. User edits Table #5 (large dataset)
3. User selects "Custom: 50" for rows per page
4. Only Table #5 shows 50 rows, others show 10

### Scenario 3: Quick Override in Shortcode
1. Table has custom settings: search = disabled
2. User needs search for one page only
3. User adds: `[atable id="5" search="true"]`
4. Search shows only on that page

---

## 🔄 Settings Resolution Flow

```php
function resolve_table_settings($table_id, $shortcode_atts = array()) {
    // 1. Plugin defaults
    $settings = array(
        'rows_per_page' => 10,
        'enable_search' => true,
        'enable_sorting' => true,
        'enable_pagination' => true,
        'table_style' => 'default',
    );
    
    // 2. Apply global settings
    $global = get_option('atables_settings', array());
    $settings = array_merge($settings, $global);
    
    // 3. Apply table-specific settings (only non-null)
    $table = get_table($table_id);
    $table_settings = $table->get_display_settings();
    foreach ($table_settings as $key => $value) {
        if ($value !== null && $value !== '') {
            $settings[$key] = $value;
        }
    }
    
    // 4. Apply shortcode attributes (highest priority)
    $settings = array_merge($settings, $shortcode_atts);
    
    return $settings;
}
```

---

## ✅ Benefits

1. **Flexibility** - Different tables, different settings
2. **Simplicity** - Optional; defaults work fine
3. **Power User Friendly** - Fine-grained control when needed
4. **Maintainable** - Clear priority cascade
5. **Scalable** - Easy to add more settings later

---

## 📋 Implementation Checklist

- [ ] Add database column for display_settings
- [ ] Update Table model
- [ ] Update TableRepository
- [ ] Create migration script
- [ ] Update edit-table.php UI
- [ ] Update TableController save logic
- [ ] Update TableShortcode resolution logic
- [ ] Add CSS styling for settings section
- [ ] Add JavaScript for toggle interactions
- [ ] Test priority cascade
- [ ] Update documentation
- [ ] Add unit tests

---

## 🎨 Design Mockup (Simplified)

```
Display Settings
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Override global defaults for this table only.

┌─────────────────────────────────────────┐
│ Setting          │ Mode                 │
├─────────────────────────────────────────┤
│ Rows/Page        │ ⚪ Global (10)       │
│                  │ ⚫ Custom [25    ]   │
├─────────────────────────────────────────┤
│ Style            │ ⚫ Global (Striped)  │
│                  │ ⚪ Custom [Bordered▼]│
├─────────────────────────────────────────┤
│ Search           │ ⚫ Global ⚪ On ⚪ Off│
│ Sorting          │ ⚫ Global ⚪ On ⚪ Off│
│ Pagination       │ ⚫ Global ⚪ On ⚪ Off│
└─────────────────────────────────────────┘

[Preview Table]
```

---

**Status:** Ready for Implementation
**Estimated Time:** 4-6 hours
**Priority:** Medium-High (good UX improvement)

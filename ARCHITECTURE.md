# A-Tables & Charts - Clean Architecture v2.0

## Overview

This is a complete rebuild with clean architecture, built for maintainability, testability, and dual-version support (Free + Pro).

## Core Principles

1. **Separation of Concerns** - Each module is independent
2. **Direct Communication** - No event-based save coordination, use direct AJAX
3. **Database First** - Separate tables from day 1, no JSON columns for settings
4. **License Ready** - Built with free/pro separation from the start
5. **Simple is Better** - Fewer abstractions, clearer code flow

---

## Directory Structure

```
a-tables-charts/
├── src/
│   ├── Core/                      # Core plugin functionality
│   │   ├── Plugin.php             # Main plugin class
│   │   ├── Activator.php          # Activation/deactivation
│   │   ├── Database.php           # Database schema manager
│   │   └── Loader.php             # Hooks loader
│   │
│   ├── Licensing/                 # License management
│   │   ├── LicenseManager.php     # License validation
│   │   ├── EnvatoAPI.php          # Envato integration (pro only)
│   │   └── UpgradePrompts.php     # Free → Pro upgrade UI
│   │
│   ├── Features/                  # Feature modules
│   │   ├── Tables/                # Table management
│   │   │   ├── TableController.php
│   │   │   ├── TableRepository.php
│   │   │   ├── TableService.php
│   │   │   └── views/
│   │   │
│   │   ├── Upload/                # File upload & parsing
│   │   │   ├── UploadController.php
│   │   │   ├── ExcelParser.php
│   │   │   └── CSVParser.php
│   │   │
│   │   ├── Display/               # Frontend rendering
│   │   │   ├── ShortcodeHandler.php
│   │   │   ├── BlockRenderer.php
│   │   │   └── views/
│   │   │
│   │   ├── Validation/            # PRO: Data validation
│   │   │   ├── ValidationController.php
│   │   │   ├── ValidationRepository.php
│   │   │   └── views/
│   │   │
│   │   ├── ConditionalFormatting/ # PRO: Conditional formatting
│   │   │   └── ...
│   │   │
│   │   ├── Charts/                # PRO: Charts
│   │   │   └── ...
│   │   │
│   │   └── Formulas/              # PRO: Formula support
│   │       └── ...
│   │
│   └── Shared/                    # Shared utilities
│       ├── Sanitizer.php
│       ├── Validator.php
│       └── Logger.php
│
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   ├── js/
│   │   ├── admin.js
│   │   └── frontend.js
│   └── images/
│
├── templates/                     # PHP templates
│   ├── admin/
│   │   ├── tables-list.php
│   │   └── table-edit.php
│   └── frontend/
│       └── table-display.php
│
├── languages/                     # Translations
├── vendor/                        # Composer dependencies
├── a-tables-charts.php           # Main plugin file
└── composer.json
```

---

## Database Schema

### Core Tables (Always Present)

#### `wp_atables_tables`
```sql
CREATE TABLE wp_atables_tables (
  id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  source_type VARCHAR(50) NOT NULL DEFAULT 'upload',  -- upload, manual, api
  row_count INT(11) DEFAULT 0,
  column_count INT(11) DEFAULT 0,
  status VARCHAR(20) DEFAULT 'active',  -- active, draft, trash
  created_by BIGINT(20) UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_status (status),
  INDEX idx_created_by (created_by)
);
```

#### `wp_atables_columns`
```sql
CREATE TABLE wp_atables_columns (
  id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  table_id BIGINT(20) UNSIGNED NOT NULL,
  column_name VARCHAR(255) NOT NULL,
  column_type VARCHAR(50) DEFAULT 'text',  -- text, number, date, currency, etc.
  column_order INT(11) NOT NULL,
  is_visible TINYINT(1) DEFAULT 1,

  INDEX idx_table_id (table_id),
  FOREIGN KEY (table_id) REFERENCES wp_atables_tables(id) ON DELETE CASCADE
);
```

#### `wp_atables_rows`
```sql
CREATE TABLE wp_atables_rows (
  id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  table_id BIGINT(20) UNSIGNED NOT NULL,
  row_order INT(11) NOT NULL,
  row_data LONGTEXT NOT NULL,  -- JSON: {"col1": "value", "col2": "value"}

  INDEX idx_table_id (table_id),
  INDEX idx_row_order (row_order),
  FOREIGN KEY (table_id) REFERENCES wp_atables_tables(id) ON DELETE CASCADE
);
```

#### `wp_atables_display_settings`
```sql
CREATE TABLE wp_atables_display_settings (
  id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  table_id BIGINT(20) UNSIGNED NOT NULL,
  theme VARCHAR(50) DEFAULT 'default',
  enable_search TINYINT(1) DEFAULT 1,
  enable_sorting TINYINT(1) DEFAULT 1,
  enable_pagination TINYINT(1) DEFAULT 1,
  rows_per_page INT(11) DEFAULT 10,
  custom_css LONGTEXT,

  UNIQUE KEY unique_table (table_id),
  FOREIGN KEY (table_id) REFERENCES wp_atables_tables(id) ON DELETE CASCADE
);
```

### Pro Tables (License Required)

#### `wp_atables_validation_rules`
```sql
CREATE TABLE wp_atables_validation_rules (
  id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  table_id BIGINT(20) UNSIGNED NOT NULL,
  column_name VARCHAR(255) NOT NULL,
  rule_type VARCHAR(50) NOT NULL,  -- required, email, number, min, max, etc.
  rule_config LONGTEXT,  -- JSON for rule parameters
  error_message VARCHAR(500),

  INDEX idx_table_id (table_id),
  FOREIGN KEY (table_id) REFERENCES wp_atables_tables(id) ON DELETE CASCADE
);
```

#### `wp_atables_conditional_formatting`
```sql
CREATE TABLE wp_atables_conditional_formatting (
  id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  table_id BIGINT(20) UNSIGNED NOT NULL,
  rule_name VARCHAR(255),
  column_name VARCHAR(255) NOT NULL,
  condition_type VARCHAR(50) NOT NULL,  -- equals, greater_than, contains, etc.
  condition_value VARCHAR(255),
  style_config LONGTEXT,  -- JSON: {"color": "#fff", "backgroundColor": "#f00"}
  priority INT(11) DEFAULT 0,

  INDEX idx_table_id (table_id),
  FOREIGN KEY (table_id) REFERENCES wp_atables_tables(id) ON DELETE CASCADE
);
```

#### `wp_atables_charts`
```sql
CREATE TABLE wp_atables_charts (
  id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  table_id BIGINT(20) UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  chart_type VARCHAR(50) NOT NULL,  -- bar, line, pie, etc.
  chart_config LONGTEXT NOT NULL,  -- JSON configuration
  status VARCHAR(20) DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  INDEX idx_table_id (table_id),
  FOREIGN KEY (table_id) REFERENCES wp_atables_tables(id) ON DELETE CASCADE
);
```

#### `wp_atables_licenses`
```sql
CREATE TABLE wp_atables_licenses (
  id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  license_key VARCHAR(255) NOT NULL UNIQUE,
  purchase_code VARCHAR(255),  -- Envato purchase code
  license_type VARCHAR(50) NOT NULL,  -- regular, extended
  status VARCHAR(20) DEFAULT 'active',  -- active, expired, suspended
  activated_at DATETIME,
  expires_at DATETIME,
  last_checked DATETIME,
  site_url VARCHAR(255),

  INDEX idx_license_key (license_key),
  INDEX idx_status (status)
);
```

---

## Feature Organization

### Free Features
- ✅ Upload Excel/CSV files
- ✅ Manual table creation
- ✅ Basic table editing (add/remove/edit rows and columns)
- ✅ Simple display with shortcode
- ✅ Basic themes (3 included)
- ✅ Pagination
- ✅ Basic search

### Pro Features (Requires License)
- 🔒 Data validation rules
- 🔒 Conditional formatting
- 🔒 Advanced sorting
- 🔒 Advanced filtering
- 🔒 Formulas & calculations
- 🔒 Cell merging
- 🔒 Charts & graphs
- 🔒 Export to Excel/CSV/PDF
- 🔒 API access
- 🔒 Premium themes (10+)
- 🔒 Priority support

---

## License System

### Architecture

```php
// Check if pro feature is available
if ( ATables_License::is_pro_active() ) {
    // Show pro feature
    $validation_controller = new ValidationController();
    $validation_controller->render();
} else {
    // Show upgrade prompt
    ATables_Upgrade::show_prompt( 'validation' );
}
```

### License Flow

1. **Free Version (WordPress.org)**
   - User installs from WordPress.org
   - All free features work
   - Pro features show "Upgrade to Pro" prompts
   - Link to purchase on your site

2. **Pro Version (Direct Purchase or Envato)**
   - User purchases license
   - Downloads pro version OR enters license key in free version
   - License validated via API
   - Pro features unlock
   - Auto-updates enabled

### License Validation
- **Online Check**: Every 24 hours
- **Offline Grace**: 7 days grace period
- **Caching**: Store validation result locally
- **Fallback**: If API down, use cached result

---

## AJAX Architecture

### Principle: Direct, Independent Saves

Each module has its own save endpoint. No coordination needed.

**Example: Validation Rules Save**

```javascript
// frontend: validation-tab.js
$('#save-validation').on('click', function() {
    $.ajax({
        url: ajaxurl,
        method: 'POST',
        data: {
            action: 'atables_save_validation',
            table_id: tableId,
            rules: JSON.stringify(validationRules),
            nonce: nonce
        },
        success: function(response) {
            // Show success message
        }
    });
});
```

```php
// backend: ValidationController.php
public function save_validation() {
    check_ajax_referer('atables_nonce', 'nonce');

    $table_id = intval($_POST['table_id']);
    $rules = json_decode(stripslashes($_POST['rules']), true);

    $this->repository->save_rules($table_id, $rules);

    wp_send_json_success(['message' => 'Saved!']);
}
```

**No Events. No Coordination. Simple.**

---

## Module Independence

Each feature module is:
- **Self-contained**: Own controller, repository, views
- **Independently saveable**: Direct AJAX to own endpoint
- **Independently loadable**: Load data on tab switch
- **Testable**: Can test without other modules

**Example: Validation Module**

```
Features/Validation/
├── ValidationController.php     # AJAX handlers
├── ValidationRepository.php     # Database operations
├── ValidationService.php        # Business logic
└── views/
    └── validation-tab.php       # UI
```

---

## Development Phases

### Phase 1: Foundation (Complete this first)
- [x] Plugin structure
- [x] Database schema
- [x] License manager framework
- [x] Basic admin UI shell

### Phase 2: Core Features
- [ ] Upload Excel/CSV
- [ ] Manual table creation
- [ ] Basic table editing
- [ ] Shortcode rendering
- [ ] Pagination & search

### Phase 3: Pro Features
- [ ] Validation rules
- [ ] Conditional formatting
- [ ] Charts
- [ ] Advanced filtering/sorting
- [ ] Export functionality

### Phase 4: Polish
- [ ] Gutenberg blocks
- [ ] Premium themes
- [ ] Documentation
- [ ] WordPress.org submission
- [ ] Envato submission

---

## Code Style Guidelines

### 1. Keep It Simple
```php
// ❌ Over-engineered
$result = $this->service->execute(
    $this->factory->create($data)
)->transform()->toArray();

// ✅ Simple
$result = $this->repository->save($table_id, $data);
```

### 2. Direct Communication
```php
// ❌ Event-based
do_action('atables_save_validation', $data);

// ✅ Direct
$this->validation_repository->save_rules($table_id, $rules);
```

### 3. Clear Naming
```php
// ❌ Unclear
public function process() {}

// ✅ Clear
public function save_validation_rules() {}
```

### 4. Fail Fast
```php
// ❌ Nested conditions
if ($table_id) {
    if ($rules) {
        if ($valid) {
            // do something
        }
    }
}

// ✅ Guard clauses
if (!$table_id) {
    wp_send_json_error('Invalid table ID');
}
if (!$rules) {
    wp_send_json_error('No rules provided');
}
if (!$valid) {
    wp_send_json_error('Invalid rules');
}

// do something
```

---

## Testing Strategy

### Unit Tests
- Repository methods
- Service logic
- Validators & sanitizers

### Integration Tests
- AJAX endpoints
- Database operations
- License validation

### Manual Testing Checklist
- [ ] Upload Excel file
- [ ] Create manual table
- [ ] Edit table data
- [ ] Save validation rules
- [ ] Apply conditional formatting
- [ ] Display table via shortcode
- [ ] License activation/deactivation
- [ ] Free → Pro upgrade

---

## Migration from Old Version

For existing users with the old plugin:

1. **Detect old version** on activation
2. **Run migration script** to:
   - Create new tables
   - Migrate data from old structure
   - Convert JSON settings to separate tables
3. **Mark as migrated** to prevent re-running

```php
// In Activator.php
public static function activate() {
    if (self::needs_migration()) {
        self::run_migration();
    }

    self::create_tables();
}
```

---

## Next Steps

1. ✅ Create this architecture document
2. ⏳ Build plugin foundation
3. ⏳ Implement license system
4. ⏳ Build core table features
5. ⏳ Add pro features one by one
6. ⏳ Test & polish
7. ⏳ Submit to marketplaces

---

**Let's build this right.**

# A-Tables & Charts - Developer Examples

This directory contains working examples demonstrating how to extend A-Tables & Charts using the developer hooks system.

## Available Examples

### 1. REST API Data Source (`rest-api-data-source-example.php`)

**Purpose:** Demonstrates how to add support for importing data from REST APIs

**Features:**
- Custom parser for REST API responses
- Support for multiple API response formats
- Data transformation and validation
- Import lifecycle tracking
- Auto-table creation from API data
- Admin settings page

**Use Cases:**
- Import products from WooCommerce REST API
- Fetch data from third-party services (Stripe, HubSpot, etc.)
- Pull data from internal company APIs
- Scheduled data synchronization

**Installation:**
1. Copy `rest-api-data-source-example.php` to `wp-content/plugins/`
2. Rename to `atables-rest-api-example.php`
3. Activate from WordPress admin plugins page

**Usage:**
```php
// In your theme or plugin:

// Example 1: Import from public API
$parser = new ATablesExample_RestApiParser();
$result = $parser->parse_string('https://api.example.com/products', array(
    'data_path' => 'data.products'
));

// Example 2: Import with authentication
$result = $parser->parse_string('https://api.example.com/orders', array(
    'api_key' => 'your-api-key-here'
));
```

## Available Developer Hooks

### Data Source & Parser Hooks

#### `atables_register_parsers`
Register custom data parsers for new file formats or data sources.

```php
add_filter('atables_register_parsers', function($parsers) {
    $parsers['mongodb'] = new MyMongoDBParser();
    return $parsers;
});
```

#### `atables_supported_extensions`
Add support for custom file extensions.

```php
add_filter('atables_supported_extensions', function($allowed_types, $extension) {
    if ($extension === 'parquet') {
        $allowed_types[] = 'parquet';
    }
    return $allowed_types;
}, 10, 2);
```

### Data Transformation Hooks

#### `atables_parse_data`
Transform imported data after parsing.

```php
add_filter('atables_parse_data', function($data, $extension, $options) {
    // Transform each row
    foreach ($data as $i => $row) {
        // Clean HTML, format numbers, etc.
        $data[$i] = array_map('sanitize_text_field', $row);
    }
    return $data;
}, 10, 3);
```

#### `atables_import_headers`
Modify column headers during import.

```php
add_filter('atables_import_headers', function($headers, $extension, $options) {
    // Convert snake_case to Title Case
    return array_map(function($h) {
        return ucwords(str_replace('_', ' ', $h));
    }, $headers);
}, 10, 3);
```

#### `atables_import_options`
Filter import options before processing.

```php
add_filter('atables_import_options', function($options, $extension, $file) {
    // Add default options
    $options['has_header'] = true;
    $options['encoding'] = 'UTF-8';
    return $options;
}, 10, 3);
```

### Validation Hooks

#### `atables_validate_import_data`
Add custom validation rules for import data.

```php
add_filter('atables_validate_import_data', function($result, $data) {
    if (count($data) < 5) {
        $result['valid'] = false;
        $result['errors'][] = 'Must have at least 5 rows';
    }
    return $result;
}, 10, 2);
```

### Lifecycle Action Hooks

#### `atables_before_import`
Triggered before import processing starts.

```php
add_action('atables_before_import', function($extension, $options, $file) {
    error_log("Import started: {$extension}");
    // Send notifications, log events, etc.
}, 10, 3);
```

#### `atables_after_import`
Triggered after import completes.

```php
add_action('atables_after_import', function($result, $extension, $options) {
    if ($result->success) {
        // Process successful import
        update_option('last_import_stats', array(
            'rows' => $result->row_count,
            'time' => current_time('mysql')
        ));
    }
}, 10, 3);
```

#### `atables_before_table_create_from_import`
Triggered before table is created from import data.

```php
add_action('atables_before_table_create_from_import', function($title, $import_result, $source_type) {
    // Modify title, add metadata, etc.
    error_log("Creating table: {$title} from {$source_type}");
}, 10, 3);
```

#### `atables_after_table_create_from_import`
Triggered after table is created from import data.

```php
add_action('atables_after_table_create_from_import', function($table_id, $import_result, $source_type) {
    // Post-processing: create charts, set permissions, notify users
    update_post_meta($table_id, '_source_type', $source_type);
    update_post_meta($table_id, '_import_date', current_time('mysql'));
}, 10, 3);
```

## Common Integration Patterns

### Pattern 1: WooCommerce Product Import

```php
// Register WooCommerce data source
add_filter('atables_register_parsers', function($parsers) {
    $parsers['woocommerce'] = new WooCommerceParser();
    return $parsers;
});

// Transform WooCommerce data
add_filter('atables_parse_data', function($data, $extension) {
    if ($extension !== 'woocommerce') return $data;

    foreach ($data as $i => $row) {
        // Format prices
        if (isset($row[2])) {
            $data[$i][2] = wc_price($row[2]);
        }
    }
    return $data;
}, 10, 2);
```

### Pattern 2: Scheduled Data Refresh

```php
// Import data on a schedule
add_action('init', function() {
    if (!wp_next_scheduled('atables_daily_import')) {
        wp_schedule_event(time(), 'daily', 'atables_daily_import');
    }
});

add_action('atables_daily_import', function() {
    // Fetch fresh data from API
    $parser = new ATablesExample_RestApiParser();
    $result = $parser->parse_string('https://api.example.com/daily-stats');

    // Import into existing table or create new one
    // ...
});
```

### Pattern 3: Data Quality Monitoring

```php
// Track import quality metrics
add_action('atables_after_import', function($result, $extension, $options) {
    if (!$result->success) {
        // Alert on failures
        wp_mail(get_option('admin_email'),
            'Import Failed',
            $result->message
        );
    }

    // Track import statistics
    $stats = get_option('atables_import_stats', array());
    $stats[] = array(
        'date' => current_time('mysql'),
        'type' => $extension,
        'rows' => $result->row_count ?? 0,
        'success' => $result->success
    );
    update_option('atables_import_stats', array_slice($stats, -100));
}, 10, 3);
```

### Pattern 4: Multi-Source Data Aggregation

```php
// Combine data from multiple sources
add_action('atables_after_table_create_from_import', function($table_id, $import_result, $source_type) {
    if ($source_type === 'api_source_1') {
        // Fetch complementary data from second source
        $parser2 = new SecondaryApiParser();
        $additional_data = $parser2->fetch_related_data($import_result->data);

        // Merge into existing table
        // ...
    }
}, 10, 3);
```

## Development Tips

### Debugging Hooks

Enable WordPress debug mode to see hook execution:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Add logging to your hooks
add_action('atables_before_import', function($extension) {
    error_log('Import starting: ' . print_r($extension, true));
});
```

### Testing Custom Parsers

Create a simple test file:

```php
// test-parser.php
require_once 'wp-load.php';

$parser = new MyCustomParser();
$result = $parser->parse_string($test_data);

var_dump($result);
```

### Performance Considerations

- Use `max_rows` option for large datasets
- Implement batch processing for very large imports
- Cache API responses when possible
- Use WordPress transients for temporary data storage

```php
add_filter('atables_import_options', function($options) {
    // Limit initial imports
    $options['max_rows'] = 1000;
    return $options;
});
```

### Error Handling

Always wrap custom code in try-catch blocks:

```php
add_filter('atables_parse_data', function($data, $extension, $options) {
    try {
        // Your transformation code
        return transform_data($data);
    } catch (Exception $e) {
        error_log('Transform error: ' . $e->getMessage());
        return $data; // Return original data on error
    }
}, 10, 3);
```

## Support & Documentation

For more information:
- See `DEVELOPER-HOOKS-REFERENCE.php` in the plugin root
- Visit plugin documentation: [link]
- Report issues: [link]

## Contributing

To contribute your own examples:
1. Create a well-documented example file
2. Follow WordPress Coding Standards
3. Include usage instructions
4. Test with multiple scenarios
5. Submit a pull request

## License

All examples are licensed under GPL v2 or later, same as the main plugin.

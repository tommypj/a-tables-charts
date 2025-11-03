<?php
/**
 * Developer Hooks System
 *
 * This file provides comprehensive documentation and examples for all
 * developer hooks available in the A-Tables & Charts plugin.
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

/*
 * ============================================================================
 * TABLE OF CONTENTS
 * ============================================================================
 *
 * 1. DATA SOURCE REGISTRATION HOOKS
 * 2. DATA PARSING HOOKS
 * 3. IMPORT LIFECYCLE HOOKS
 * 4. DATA MANIPULATION HOOKS
 * 5. VALIDATION HOOKS
 * 6. TABLE CREATION HOOKS
 * 7. EXPORT HOOKS
 * 8. EXAMPLE IMPLEMENTATIONS
 *
 * ============================================================================
 */

// ============================================================================
// 1. DATA SOURCE REGISTRATION HOOKS
// ============================================================================

/**
 * Register custom data source types
 *
 * Allows developers to add custom data sources beyond CSV, Excel, JSON, etc.
 *
 * @since 1.0.0
 *
 * @param array $sources Registered data sources
 * @return array Modified data sources array
 *
 * @example
 * add_filter('atables_register_data_sources', function($sources) {
 *     $sources['mongodb'] = array(
 *         'label' => 'MongoDB Database',
 *         'icon'  => 'dashicons-database',
 *         'class' => 'MyPlugin\\MongoDBSource',
 *     );
 *     return $sources;
 * });
 */
// apply_filters( 'atables_register_data_sources', $sources );

/**
 * Register custom data parsers
 *
 * Add parsers for custom file formats or data types.
 *
 * @since 1.0.0
 *
 * @param array $parsers Registered parsers (extension => parser instance)
 * @return array Modified parsers array
 *
 * @example
 * add_filter('atables_register_parsers', function($parsers) {
 *     $parsers['yaml'] = new YamlParser();
 *     $parsers['xml']  = new CustomXmlParser();
 *     return $parsers;
 * });
 */
// apply_filters( 'atables_register_parsers', $parsers );

/**
 * Extend file type support
 *
 * Map file extensions to parser types.
 *
 * @since 1.0.0
 *
 * @param array $extensions Supported extensions (extension => parser_key)
 * @return array Modified extensions array
 *
 * @example
 * add_filter('atables_supported_extensions', function($extensions) {
 *     $extensions['tsv']  = 'csv'; // Tab-separated values use CSV parser
 *     $extensions['yaml'] = 'yaml';
 *     $extensions['yml']  = 'yaml';
 *     return $extensions;
 * });
 */
// apply_filters( 'atables_supported_extensions', $extensions );

// ============================================================================
// 2. DATA PARSING HOOKS
// ============================================================================

/**
 * Filter parsed data from any source
 *
 * Modify data after parsing but before validation.
 *
 * @since 1.0.0
 *
 * @param array  $data        Parsed data array
 * @param string $source_type Source type (csv, excel, json, etc.)
 * @param array  $options     Import options
 * @return array Modified data
 *
 * @example
 * add_filter('atables_parse_data', function($data, $source_type, $options) {
 *     // Add row numbers to all rows
 *     foreach ($data as $index => &$row) {
 *         $row['row_number'] = $index + 1;
 *     }
 *     return $data;
 * }, 10, 3);
 */
// apply_filters( 'atables_parse_data', $data, $source_type, $options );

/**
 * Filter parsed data from specific source type
 *
 * Source-specific data manipulation.
 *
 * @since 1.0.0
 *
 * @param array $data    Parsed data array
 * @param array $options Import options
 * @return array Modified data
 *
 * @example
 * // Handle CSV data specifically
 * add_filter('atables_parse_data_csv', function($data, $options) {
 *     // Remove empty rows from CSV
 *     return array_filter($data, function($row) {
 *         return !empty(array_filter($row));
 *     });
 * }, 10, 2);
 *
 * // Handle JSON data specifically
 * add_filter('atables_parse_data_json', function($data, $options) {
 *     // Flatten nested JSON structure
 *     return array_map(function($item) {
 *         return flatten_array($item);
 *     }, $data);
 * }, 10, 2);
 */
// apply_filters( "atables_parse_data_{$source_type}", $data, $options );

/**
 * Parse custom data source
 *
 * Handle parsing for custom data sources not built into the plugin.
 *
 * @since 1.0.0
 *
 * @param mixed  $result      Parsing result (null by default)
 * @param mixed  $source      Data source (file path, URL, connection string, etc.)
 * @param string $source_type Type identifier
 * @param array  $options     Parsing options
 * @return ImportResult|null Return ImportResult object or null to use default
 *
 * @example
 * add_filter('atables_parse_custom_source', function($result, $source, $type, $options) {
 *     if ($type === 'rest_api') {
 *         $response = wp_remote_get($source);
 *         $data = json_decode(wp_remote_retrieve_body($response), true);
 *
 *         return new ImportResult([
 *             'success' => true,
 *             'data' => $data,
 *             'headers' => array_keys($data[0]),
 *             'row_count' => count($data)
 *         ]);
 *     }
 *     return $result;
 * }, 10, 4);
 */
// apply_filters( 'atables_parse_custom_source', null, $source, $source_type, $options );

// ============================================================================
// 3. IMPORT LIFECYCLE HOOKS
// ============================================================================

/**
 * Before import starts
 *
 * Runs before any import processing begins.
 *
 * @since 1.0.0
 *
 * @param string $source_type Source type
 * @param array  $options     Import options
 *
 * @example
 * add_action('atables_before_import', function($source_type, $options) {
 *     // Log import start
 *     error_log("Starting import from: {$source_type}");
 *
 *     // Send notification
 *     wp_mail(get_option('admin_email'), 'Import Started', '...');
 * }, 10, 2);
 */
// do_action( 'atables_before_import', $source_type, $options );

/**
 * After import completes
 *
 * Runs after import processing completes (success or failure).
 *
 * @since 1.0.0
 *
 * @param ImportResult $result Import result object
 * @param string       $source_type Source type
 * @param array        $options Import options
 *
 * @example
 * add_action('atables_after_import', function($result, $source_type, $options) {
 *     if ($result->success) {
 *         // Trigger webhook
 *         wp_remote_post('https://api.example.com/webhook', [
 *             'body' => json_encode([
 *                 'event' => 'import_complete',
 *                 'rows' => $result->row_count
 *             ])
 *         ]);
 *     }
 * }, 10, 3);
 */
// do_action( 'atables_after_import', $result, $source_type, $options );

/**
 * Before table creation from import
 *
 * Runs before a table is created from imported data.
 *
 * @since 1.0.0
 *
 * @param array $table_data Table data to be created
 * @param array $import_result Import result
 *
 * @example
 * add_action('atables_before_table_create_from_import', function($table_data, $import_result) {
 *     // Log table creation
 *     do_action('my_custom_log', 'Creating table: ' . $table_data['title']);
 * }, 10, 2);
 */
// do_action( 'atables_before_table_create_from_import', $table_data, $import_result );

/**
 * After table creation from import
 *
 * Runs after a table is successfully created from imported data.
 *
 * @since 1.0.0
 *
 * @param int   $table_id Table ID
 * @param array $import_result Import result
 *
 * @example
 * add_action('atables_after_table_create_from_import', function($table_id, $import_result) {
 *     // Auto-create chart from imported table
 *     $chart_service = new ChartService();
 *     $chart_service->create_from_table($table_id, 'line');
 * }, 10, 2);
 */
// do_action( 'atables_after_table_create_from_import', $table_id, $import_result );

// ============================================================================
// 4. DATA MANIPULATION HOOKS
// ============================================================================

/**
 * Filter import data before table creation
 *
 * Modify or transform data before it's saved to a table.
 *
 * @since 1.0.0
 *
 * @param array $data    Import data
 * @param array $headers Column headers
 * @param array $options Import options
 * @return array Modified data
 *
 * @example
 * add_filter('atables_import_data', function($data, $headers, $options) {
 *     // Convert all prices to integers (remove $ and decimals)
 *     foreach ($data as &$row) {
 *         if (isset($row['price'])) {
 *             $row['price'] = (int) preg_replace('/[^0-9]/', '', $row['price']);
 *         }
 *     }
 *     return $data;
 * }, 10, 3);
 */
// apply_filters( 'atables_import_data', $data, $headers, $options );

/**
 * Filter column headers during import
 *
 * Modify column headers (rename, add, remove).
 *
 * @since 1.0.0
 *
 * @param array  $headers     Column headers
 * @param string $source_type Source type
 * @param array  $options     Import options
 * @return array Modified headers
 *
 * @example
 * add_filter('atables_import_headers', function($headers, $source_type, $options) {
 *     // Normalize header names
 *     return array_map(function($header) {
 *         return strtolower(str_replace(' ', '_', $header));
 *     }, $headers);
 * }, 10, 3);
 */
// apply_filters( 'atables_import_headers', $headers, $source_type, $options );

/**
 * Filter individual cell value during import
 *
 * Transform individual cell values as they're imported.
 *
 * @since 1.0.0
 *
 * @param mixed  $value      Cell value
 * @param string $column     Column name
 * @param int    $row_index  Row index
 * @param array  $row_data   Complete row data
 * @return mixed Modified value
 *
 * @example
 * add_filter('atables_import_cell_value', function($value, $column, $row_index, $row_data) {
 *     // Convert dates to Y-m-d format
 *     if ($column === 'date' && !empty($value)) {
 *         return date('Y-m-d', strtotime($value));
 *     }
 *     return $value;
 * }, 10, 4);
 */
// apply_filters( 'atables_import_cell_value', $value, $column, $row_index, $row_data );

// ============================================================================
// 5. VALIDATION HOOKS
// ============================================================================

/**
 * Validate import data
 *
 * Add custom validation rules to import data.
 *
 * @since 1.0.0
 *
 * @param array $validation  Validation result ['valid' => bool, 'errors' => array]
 * @param array $data        Import data
 * @param array $headers     Column headers
 * @param array $options     Import options
 * @return array Modified validation result
 *
 * @example
 * add_filter('atables_validate_import_data', function($validation, $data, $headers, $options) {
 *     // Require 'email' column
 *     if (!in_array('email', $headers)) {
 *         $validation['valid'] = false;
 *         $validation['errors'][] = 'Email column is required';
 *     }
 *
 *     // Validate email formats
 *     foreach ($data as $row) {
 *         if (!empty($row['email']) && !is_email($row['email'])) {
 *             $validation['valid'] = false;
 *             $validation['errors'][] = "Invalid email: {$row['email']}";
 *         }
 *     }
 *
 *     return $validation;
 * }, 10, 4);
 */
// apply_filters( 'atables_validate_import_data', $validation, $data, $headers, $options );

/**
 * Filter import options
 *
 * Modify or validate import options before processing.
 *
 * @since 1.0.0
 *
 * @param array  $options     Import options
 * @param string $source_type Source type
 * @return array Modified options
 *
 * @example
 * add_filter('atables_import_options', function($options, $source_type) {
 *     // Force specific delimiter for CSV
 *     if ($source_type === 'csv') {
 *         $options['delimiter'] = '|';
 *     }
 *
 *     // Set default values
 *     $options['skip_empty_rows'] = $options['skip_empty_rows'] ?? true;
 *
 *     return $options;
 * }, 10, 2);
 */
// apply_filters( 'atables_import_options', $options, $source_type );

// ============================================================================
// 6. TABLE CREATION HOOKS
// ============================================================================

/**
 * Filter table data before creation
 *
 * Modify table properties before the table is created.
 *
 * @since 1.0.0
 *
 * @param array $table_data Table data array
 * @param array $source_data Import source data
 * @return array Modified table data
 *
 * @example
 * add_filter('atables_before_table_create', function($table_data, $source_data) {
 *     // Auto-generate title from filename
 *     if (empty($table_data['title']) && !empty($source_data['filename'])) {
 *         $table_data['title'] = pathinfo($source_data['filename'], PATHINFO_FILENAME);
 *     }
 *
 *     // Set default status
 *     $table_data['status'] = 'draft';
 *
 *     return $table_data;
 * }, 10, 2);
 */
// apply_filters( 'atables_before_table_create', $table_data, $source_data );

/**
 * Modify table after creation
 *
 * Perform additional operations after table is created.
 *
 * @since 1.0.0
 *
 * @param int   $table_id   Created table ID
 * @param array $table_data Table data used for creation
 *
 * @example
 * add_action('atables_after_table_create', function($table_id, $table_data) {
 *     // Auto-assign category
 *     update_post_meta($table_id, 'table_category', 'imported');
 *
 *     // Create backup
 *     do_action('my_backup_plugin_save', $table_id);
 * }, 10, 2);
 */
// do_action( 'atables_after_table_create', $table_id, $table_data );

// ============================================================================
// 7. EXPORT HOOKS
// ============================================================================

/**
 * Filter export data
 *
 * Modify data before export.
 *
 * @since 1.0.0
 *
 * @param array  $data        Export data
 * @param string $format      Export format (csv, excel, pdf)
 * @param int    $table_id    Table ID
 * @param array  $options     Export options
 * @return array Modified data
 *
 * @example
 * add_filter('atables_export_data', function($data, $format, $table_id, $options) {
 *     // Add watermark row for PDF exports
 *     if ($format === 'pdf') {
 *         $data[] = ['Generated by MyCompany', '', '', ''];
 *     }
 *     return $data;
 * }, 10, 4);
 */
// apply_filters( 'atables_export_data', $data, $format, $table_id, $options );

/**
 * Customize export filename
 *
 * Change the filename used for exports.
 *
 * @since 1.0.0
 *
 * @param string $filename  Default filename
 * @param int    $table_id  Table ID
 * @param string $format    Export format
 * @return string Modified filename
 *
 * @example
 * add_filter('atables_export_filename', function($filename, $table_id, $format) {
 *     // Include date in filename
 *     return 'export_' . $table_id . '_' . date('Y-m-d') . '.' . $format;
 * }, 10, 3);
 */
// apply_filters( 'atables_export_filename', $filename, $table_id, $format );

// ============================================================================
// 8. EXAMPLE IMPLEMENTATIONS
// ============================================================================

/**
 * EXAMPLE 1: Import from REST API
 *
 * This example shows how to add support for importing data from a REST API.
 */
/*
add_filter('atables_register_data_sources', function($sources) {
    $sources['rest_api'] = array(
        'label' => 'REST API',
        'icon'  => 'dashicons-cloud',
    );
    return $sources;
});

add_filter('atables_parse_custom_source', function($result, $source, $type, $options) {
    if ($type !== 'rest_api') {
        return $result;
    }

    // Fetch data from API
    $response = wp_remote_get($source, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $options['api_key'],
        ),
    ));

    if (is_wp_error($response)) {
        return ImportResult::error($response->get_error_message());
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($data)) {
        return ImportResult::error('No data received from API');
    }

    $headers = array_keys($data[0]);

    return new ImportResult(array(
        'success'      => true,
        'data'         => $data,
        'headers'      => $headers,
        'row_count'    => count($data),
        'column_count' => count($headers),
    ));
}, 10, 4);
*/

/**
 * EXAMPLE 2: WooCommerce Product Table
 *
 * Automatically import WooCommerce products as a table.
 */
/*
add_filter('atables_register_data_sources', function($sources) {
    $sources['woocommerce'] = array(
        'label' => 'WooCommerce Products',
        'icon'  => 'dashicons-cart',
    );
    return $sources;
});

add_filter('atables_parse_custom_source', function($result, $source, $type, $options) {
    if ($type !== 'woocommerce') {
        return $result;
    }

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    $products = get_posts($args);
    $data = array();

    foreach ($products as $product) {
        $wc_product = wc_get_product($product->ID);
        $data[] = array(
            'ID'          => $product->ID,
            'Name'        => $product->post_title,
            'SKU'         => $wc_product->get_sku(),
            'Price'       => $wc_product->get_price(),
            'Stock'       => $wc_product->get_stock_quantity(),
            'Categories'  => strip_tags(get_the_term_list($product->ID, 'product_cat', '', ', ')),
        );
    }

    return new ImportResult(array(
        'success'      => true,
        'data'         => $data,
        'headers'      => array('ID', 'Name', 'SKU', 'Price', 'Stock', 'Categories'),
        'row_count'    => count($data),
        'column_count' => 6,
    ));
}, 10, 4);
*/

/**
 * EXAMPLE 3: Data Transformation Pipeline
 *
 * Apply multiple transformations to imported data.
 */
/*
// Normalize phone numbers
add_filter('atables_import_cell_value', function($value, $column, $row_index, $row_data) {
    if ($column === 'phone' && !empty($value)) {
        // Remove all non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $value);
        // Format as (XXX) XXX-XXXX
        if (strlen($clean) === 10) {
            return sprintf('(%s) %s-%s',
                substr($clean, 0, 3),
                substr($clean, 3, 3),
                substr($clean, 6, 4)
            );
        }
    }
    return $value;
}, 10, 4);

// Auto-geocode addresses
add_filter('atables_import_data', function($data, $headers, $options) {
    if (in_array('address', $headers)) {
        foreach ($data as &$row) {
            if (!empty($row['address']) && empty($row['lat']) && empty($row['lng'])) {
                $coords = geocode_address($row['address']); // Your geocoding function
                $row['lat'] = $coords['lat'];
                $row['lng'] = $coords['lng'];
            }
        }
    }
    return $data;
}, 10, 3);
*/

/**
 * EXAMPLE 4: Custom Validation Rules
 *
 * Enforce business rules on imported data.
 */
/*
add_filter('atables_validate_import_data', function($validation, $data, $headers, $options) {
    // Require specific columns
    $required_columns = array('name', 'email', 'status');
    $missing = array_diff($required_columns, $headers);

    if (!empty($missing)) {
        $validation['valid'] = false;
        $validation['errors'][] = 'Missing required columns: ' . implode(', ', $missing);
        return $validation;
    }

    // Validate data integrity
    $seen_emails = array();
    foreach ($data as $index => $row) {
        // Check for duplicate emails
        if (in_array($row['email'], $seen_emails)) {
            $validation['valid'] = false;
            $validation['errors'][] = "Duplicate email on row {$index}: {$row['email']}";
        }
        $seen_emails[] = $row['email'];

        // Validate status values
        $valid_statuses = array('active', 'inactive', 'pending');
        if (!in_array($row['status'], $valid_statuses)) {
            $validation['valid'] = false;
            $validation['errors'][] = "Invalid status on row {$index}: {$row['status']}";
        }
    }

    return $validation;
}, 10, 4);
*/

/**
 * EXAMPLE 5: Automated Post-Import Actions
 *
 * Trigger actions after successful imports.
 */
/*
add_action('atables_after_table_create_from_import', function($table_id, $import_result) {
    // Send notification email
    $admin_email = get_option('admin_email');
    $subject = 'New Table Imported';
    $message = sprintf(
        'Table #%d was created with %d rows and %d columns.',
        $table_id,
        $import_result['row_count'],
        $import_result['column_count']
    );
    wp_mail($admin_email, $subject, $message);

    // Create automatic backup
    if (function_exists('my_backup_table')) {
        my_backup_table($table_id);
    }

    // Publish to Slack
    if (defined('SLACK_WEBHOOK_URL')) {
        wp_remote_post(SLACK_WEBHOOK_URL, array(
            'body' => json_encode(array(
                'text' => "New table imported: {$import_result['title']} ({$import_result['row_count']} rows)",
            )),
        ));
    }
}, 10, 2);
*/

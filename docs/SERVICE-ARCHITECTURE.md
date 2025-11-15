# Service Architecture Documentation

**Date:** 2025-11-15
**Status:** ✅ **COMPLETE - ALL SERVICES IMPLEMENTED AND INTEGRATED**

---

## Overview

The plugin follows a **Service-Oriented Architecture** pattern where business logic is separated from HTTP/AJAX handling. This improves:

- **Code Reusability**: Services can be used from multiple controllers or contexts
- **Testability**: Business logic can be tested independently of WordPress
- **Maintainability**: Clear separation of concerns makes code easier to understand
- **Scalability**: New features can be added by creating services and thin controllers

---

## Architecture Pattern

```
┌────────────────────────────────────────────────────────┐
│                    HTTP Request                        │
│              (AJAX / WordPress Admin)                  │
└───────────────────────┬────────────────────────────────┘
                        │
                        ▼
┌────────────────────────────────────────────────────────┐
│                   CONTROLLER                           │
│  - Verify nonce & permissions                          │
│  - Sanitize & validate input                           │
│  - Call service methods                                │
│  - Format & send JSON response                         │
└───────────────────────┬────────────────────────────────┘
                        │
                        ▼
┌────────────────────────────────────────────────────────┐
│                    SERVICE                             │
│  - Business logic                                      │
│  - Data processing                                     │
│  - Validation rules                                    │
│  - Return structured results                           │
└───────────────────────┬────────────────────────────────┘
                        │
                        ▼
┌────────────────────────────────────────────────────────┐
│                  REPOSITORY                            │
│  - Database operations                                 │
│  - Data persistence                                    │
│  - Query building                                      │
└────────────────────────────────────────────────────────┘
```

---

## Implemented Services

### 1. BulkActionsService

**Location:** `src/modules/bulk/services/BulkActionsService.php`
**Controller:** `BulkActionsController`
**Created:** 2025-11-15

#### Responsibilities

- Process bulk delete operations on table rows
- Process bulk duplicate operations
- Process bulk edit operations
- Validate bulk operation data
- Generate operation statistics and previews

#### Public Methods

```php
bulk_delete( $table_data, $row_indices )
    - Removes selected rows from table data
    - Returns: array with success, message, updated data, count

bulk_duplicate( $table_data, $row_indices )
    - Duplicates selected rows
    - Returns: array with success, message, updated data, count

bulk_edit( $table_data, $headers, $row_indices, $column, $value )
    - Updates column value for selected rows
    - Returns: array with success, message, updated data, count

validate_bulk_operation( $table_data, $row_indices, $operation )
    - Validates operation before execution
    - Returns: array with valid boolean and errors

get_operation_stats( $original_data, $updated_data, $operation )
    - Generates statistics about the operation
    - Returns: array with row counts and changes

preview_operation( $table_data, $row_indices, $operation, $options )
    - Previews what will happen without applying changes
    - Returns: array with affected rows and action description
```

#### Usage Example

```php
$service = new BulkActionsService();

// Delete rows
$result = $service->bulk_delete( $table_data, [1, 3, 5] );
if ( $result['success'] ) {
    $updated_data = $result['data'];
    // Save to database...
}

// Duplicate rows
$result = $service->bulk_duplicate( $table_data, [0, 2] );

// Edit rows
$result = $service->bulk_edit(
    $table_data,
    $headers,
    [1, 2, 3],
    'status',
    'active'
);
```

---

### 2. MySQLQueryService

**Location:** `src/modules/database/MySQLQueryService.php`
**Controller:** `MySQLQueryController`
**Status:** Already implemented

#### Responsibilities

- Execute custom MySQL queries safely
- Validate queries for security (SELECT-only, whitelist, complexity limits)
- Convert query results to table format
- Provide sample queries and query building

#### Security Features

- **6-Layer Validation System:**
  1. SELECT-only enforcement (blocks DROP, DELETE, INSERT, etc.)
  2. SQL comment stripping (prevents comment-based bypasses)
  3. Dangerous keyword detection (30+ keywords blocked)
  4. Table whitelist validation (WordPress core + plugin tables only)
  5. Query complexity limits (max JOINs, subquery depth)
  6. Character set validation (ASCII-only)

- **Rate Limiting:** 10 queries per minute per admin user
- **Result Size Limits:** Max 10,000 rows, 100 columns
- **Execution Time Limits:** 30 seconds maximum
- **Comprehensive Logging:** All queries and violations logged

#### Public Methods

```php
execute_query( $query )
    - Executes SELECT query with security validation
    - Returns: array with success, headers, data, row/column counts

validate_query( $query )
    - Validates query against security rules
    - Returns: array with valid boolean and errors

test_query( $query, $limit = 5 )
    - Tests query with automatic LIMIT
    - Returns: same as execute_query

get_available_tables()
    - Lists all database tables
    - Returns: array of table names

get_table_columns( $table_name )
    - Gets column information for a table
    - Returns: array of column details

build_query( $table, $columns, $where, $order, $limit )
    - Safely builds SELECT query
    - Returns: SQL query string

get_sample_queries()
    - Provides sample query templates
    - Returns: array of query examples
```

---

### 3. CellMergingService

**Location:** `src/modules/cellmerging/CellMergingService.php`
**Controller:** `CellMergingController`
**Status:** Already implemented

#### Responsibilities

- Apply cell merging configurations to table data
- Validate merge configurations (bounds, overlaps)
- Generate HTML with colspan/rowspan attributes
- Auto-merge identical adjacent cells
- Create merge patterns (headers, groups, summaries)

#### Public Methods

```php
apply_merging( $data, $merge_config )
    - Applies merge configuration to table data
    - Returns: array with processed data and validated merges

validate_merge_config( $merge_config, $data )
    - Validates merge configuration
    - Returns: array of valid merges

generate_html_with_merges( $data, $headers, $classes )
    - Generates HTML table with merged cells
    - Returns: HTML string

create_merge_pattern( $pattern, $options )
    - Creates predefined merge patterns
    - Returns: merge configuration array

validate_no_overlap( $new_merge, $all_merges )
    - Checks if new merge overlaps existing merges
    - Returns: boolean

auto_merge_identical( $data, $column_config )
    - Automatically merges cells with same values
    - Returns: array of merge configurations

get_merge_presets()
    - Gets available merge pattern presets
    - Returns: array of preset configurations
```

---

### 4. ValidationService

**Location:** `src/modules/validation/ValidationService.php`
**Controller:** `ValidationController`
**Status:** Already implemented

#### Responsibilities

- Validate table data against configured rules
- Field-level validation (type, format, range, etc.)
- Row-level and full table validation
- Provide validation rule presets

#### Supported Validation Rules

- **required**: Field must not be empty
- **type**: Data type validation (string, number, email, url, date, etc.)
- **min/max**: Numeric value range
- **min_length/max_length**: Text length constraints
- **pattern**: Regular expression matching
- **email**: Valid email format
- **url**: Valid URL format
- **date**: Valid date format
- **enum**: Must be one of allowed values
- **unique**: No duplicate values
- **custom**: Custom validation function

#### Public Methods

```php
validate( $data, $validation_rules )
    - Validates full table data
    - Returns: array with valid boolean, errors, warnings

validate_row( $row, $validation_rules, $row_index )
    - Validates single row
    - Returns: array of validation errors

validate_field( $value, $rules, $column, $row_index )
    - Validates single field
    - Returns: array of validation errors

get_rule_presets()
    - Gets predefined validation rule sets
    - Returns: array of rule presets

get_available_rules()
    - Lists all available validation rules
    - Returns: array of rules with descriptions
```

---

## Service Integration Status

| Module | Controller | Service | Status | Integration |
|--------|-----------|---------|--------|-------------|
| **Bulk Actions** | BulkActionsController | BulkActionsService | ✅ Complete | Lines 14, 39, 135, 174, 216 |
| **MySQL Query** | MySQLQueryController | MySQLQueryService | ✅ Complete | Lines 60, 87, 202, 283, 358, 393 |
| **Cell Merging** | CellMergingController | CellMergingService | ✅ Complete | Lines 13, 26, 39, 165 |
| **Validation** | ValidationController | ValidationService | ✅ Complete | Lines 13, 24, 39, 90 |

---

## Controller Refactoring Summary

### BulkActionsController Changes

**Before:** Business logic mixed with HTTP handling
```php
private function bulk_delete( $table, $data ) {
    // 50+ lines of data manipulation logic
    // Mixed with database update logic
}
```

**After:** Clean separation using service
```php
private function bulk_delete( $table, $data ) {
    $row_indices = isset( $data['rows'] ) ? array_map( 'intval', $data['rows'] ) : array();
    $table_data = $table->get_data();

    // Use service for business logic
    $result = $this->service->bulk_delete( $table_data, $row_indices );

    if ( ! $result['success'] ) {
        return $result;
    }

    // Controller handles only persistence
    $table->source_data['data'] = $result['data'];
    $table->row_count = count( $result['data'] );
    $updated = $this->repository->update( $table );

    return array(
        'success' => $updated,
        'message' => $result['message'],
    );
}
```

---

## Benefits Achieved

### 1. Code Reusability ✅

Services can be used from multiple contexts:
```php
// From controller
$service->bulk_delete( $data, $indices );

// From CLI command
$service->bulk_delete( $data, $indices );

// From cron job
$service->bulk_delete( $data, $indices );

// From REST API
$service->bulk_delete( $data, $indices );
```

### 2. Testability ✅

Business logic can be unit tested without WordPress:
```php
public function test_bulk_delete() {
    $service = new BulkActionsService();
    $data = [ ['id' => 1], ['id' => 2], ['id' => 3] ];
    $result = $service->bulk_delete( $data, [1] );

    $this->assertTrue( $result['success'] );
    $this->assertEquals( 2, count( $result['data'] ) );
}
```

### 3. Maintainability ✅

Clear separation of concerns:
- **Controllers**: HTTP/AJAX, nonce, permissions, input sanitization
- **Services**: Business logic, calculations, validations
- **Repositories**: Database queries, persistence

### 4. Security ✅

- Input validation centralized in services
- Security rules enforced consistently
- Easier to audit and review

### 5. Documentation ✅

- Service methods are self-documenting
- Return types are consistent
- Public APIs are clearly defined

---

## Best Practices

### Creating New Services

1. **Create Service File**
   ```bash
   src/modules/{module}/services/{ServiceName}Service.php
   ```

2. **Define Clear Responsibilities**
   - One service per domain concept
   - Methods should be focused and single-purpose
   - Avoid coupling between services

3. **Consistent Return Format**
   ```php
   return array(
       'success' => true/false,
       'message' => 'User-friendly message',
       'data'    => $processed_data,
       // Additional context...
   );
   ```

4. **Input Validation**
   - Validate all inputs at the start
   - Return errors early
   - Use type hints where possible

5. **Error Handling**
   - Return structured error arrays
   - Include context for debugging
   - Use translatable error messages

### Using Services in Controllers

1. **Instantiate in Constructor**
   ```php
   public function __construct() {
       $this->service = new MyService();
   }
   ```

2. **Delegate Business Logic**
   ```php
   public function ajax_handler() {
       // Controller: validate input, check permissions
       check_ajax_referer( 'nonce', 'nonce' );
       $input = sanitize_text_field( $_POST['input'] );

       // Service: process data
       $result = $this->service->process( $input );

       // Controller: send response
       if ( $result['success'] ) {
           wp_send_json_success( $result );
       } else {
           wp_send_json_error( $result );
       }
   }
   ```

3. **Keep Controllers Thin**
   - No business logic in controllers
   - Only HTTP/AJAX handling
   - Minimal data transformation

---

## Future Enhancements

### Potential New Services

1. **ExportService** - Handle table exports (CSV, JSON, Excel)
2. **ImportService** - Handle file imports with validation
3. **TemplateService** - Manage table templates and presets
4. **CacheService** - Handle caching strategies for large tables
5. **AnalyticsService** - Generate statistics and insights
6. **PermissionsService** - Centralize permission checks
7. **NotificationService** - Handle user notifications

### Service Layer Improvements

1. **Dependency Injection**
   - Use DI container for service instantiation
   - Improve testability with mock services

2. **Service Interfaces**
   - Define interfaces for services
   - Enable strategy patterns and alternatives

3. **Event System**
   - Services emit events for actions
   - Plugins can hook into service events

4. **Service Registry**
   - Central registry for service discovery
   - Enable service swapping and decoration

---

## Testing

### Unit Testing Services

Services are designed to be unit-testable:

```php
class BulkActionsServiceTest extends WP_UnitTestCase {

    private $service;

    public function setUp() {
        $this->service = new BulkActionsService();
    }

    public function test_bulk_delete_removes_rows() {
        $data = [
            ['name' => 'Alice'],
            ['name' => 'Bob'],
            ['name' => 'Charlie']
        ];

        $result = $this->service->bulk_delete( $data, [1] );

        $this->assertTrue( $result['success'] );
        $this->assertEquals( 2, count( $result['data'] ) );
        $this->assertEquals( 'Alice', $result['data'][0]['name'] );
        $this->assertEquals( 'Charlie', $result['data'][1]['name'] );
    }

    public function test_bulk_delete_validates_empty_selection() {
        $data = [['name' => 'Alice']];
        $result = $this->service->bulk_delete( $data, [] );

        $this->assertFalse( $result['success'] );
        $this->assertContains( 'No rows selected', $result['message'] );
    }
}
```

---

## Conclusion

The service architecture is **fully implemented and integrated** across all major modules. The plugin now follows best practices for:

- **Separation of Concerns**: Clear boundaries between HTTP, business logic, and data
- **Code Reusability**: Services can be used from any context
- **Testability**: Business logic can be tested independently
- **Maintainability**: Consistent patterns make code easier to understand
- **Scalability**: New features can be added following established patterns

**All Services Status:** ✅ **PRODUCTION READY**

---

**Last Updated:** 2025-11-15
**Architecture Version:** 1.0.0
**Next Review:** When adding new modules or major features

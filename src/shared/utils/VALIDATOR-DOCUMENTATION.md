# Validator Utility - Documentation

## Overview
The Validator class provides comprehensive validation functionality for the Tables and Charts WordPress plugin. It follows best practices for input validation and provides detailed error messages.

## Features

### ✅ **Enhanced Validation Methods**
- Email validation
- URL validation
- Integer and Float validation with min/max constraints
- String length validation
- Required field validation
- Alphanumeric validation
- Slug validation
- Enum/Choice validation
- JSON validation
- Date format validation
- Array structure validation
- File upload validation
- Nonce validation

### ✅ **New Advanced Features**
1. **Error Collection System** - Collects all validation errors with field names
2. **Detailed Error Messages** - Provides user-friendly, translatable error messages
3. **Table-Specific Validators** - Custom validators for table titles and data
4. **Multi-Field Validation** - Validate multiple fields at once with rules
5. **Security Enhancements** - File name safety checks, directory traversal prevention

---

## Usage Examples

### Basic Validation

```php
use ATablesCharts\Shared\Utils\Validator;

// Simple boolean validation
if ( Validator::email( $email_address ) ) {
    // Valid email
}

// Validation with error collection
Validator::clear_errors();
Validator::required( $title, 'title' );
Validator::string_length( $title, 3, 200, 'title' );

if ( ! empty( Validator::get_errors() ) ) {
    $errors = Validator::get_errors();
    // Display errors
}
```

### Table Title Validation

```php
$validation = Validator::table_title( $title );

if ( ! $validation['valid'] ) {
    foreach ( $validation['errors'] as $field => $errors ) {
        foreach ( $errors as $error ) {
            echo esc_html( $error );
        }
    }
}
```

### Table Data Validation

```php
$data = array(
    array( 'Name', 'Age', 'City' ),
    array( 'John', '25', 'NYC' ),
    array( 'Jane', '30', 'LA' ),
);

$validation = Validator::table_data( $data );

if ( $validation['valid'] ) {
    // Data is valid
} else {
    // Show errors
    print_r( $validation['errors'] );
}
```

### Multi-Field Validation

```php
$data = array(
    'title' => 'My Table',
    'email' => 'user@example.com',
    'age' => 25,
);

$rules = array(
    'title' => array( 'required', 'string_length:3:200' ),
    'email' => array( 'required', 'email' ),
    'age' => array( 'integer:18:100' ),
);

$validation = Validator::validate_fields( $data, $rules );

if ( ! $validation['valid'] ) {
    // Handle errors
    print_r( $validation['errors'] );
}
```

### File Upload Validation

```php
$allowed_types = array( 'text/csv', 'application/json' );
$max_size = 5 * 1024 * 1024; // 5MB

$validation = Validator::file_upload( $_FILES['upload'], $allowed_types, $max_size );

if ( ! $validation['valid'] ) {
    wp_die( esc_html( $validation['error'] ) );
}
```

### Integer with Range Validation

```php
Validator::clear_errors();

// Validate page number (must be 1-9999)
if ( ! Validator::integer( $page, 1, 9999, 'page' ) ) {
    $errors = Validator::get_errors();
    // Handle error
}
```

### Enum/Choice Validation

```php
$allowed_statuses = array( 'active', 'inactive', 'archived' );

Validator::clear_errors();

if ( ! Validator::in_array( $status, $allowed_statuses, 'status' ) ) {
    $errors = Validator::get_errors();
    // Error: "Status must be one of: active, inactive, archived"
}
```

---

## Method Reference

### `required( $value, $field_name = 'field' )`
Validates that a field is not empty.

**Parameters:**
- `$value` (mixed) - Value to validate
- `$field_name` (string) - Field name for error messages

**Returns:** `bool` - True if valid

---

### `email( $email, $field_name = 'email' )`
Validates email format.

**Parameters:**
- `$email` (string) - Email to validate
- `$field_name` (string) - Field name for error messages

**Returns:** `bool` - True if valid

---

### `integer( $value, $min = null, $max = null, $field_name = 'value' )`
Validates integer with optional min/max constraints.

**Parameters:**
- `$value` (mixed) - Value to validate
- `$min` (int|null) - Minimum value
- `$max` (int|null) - Maximum value
- `$field_name` (string) - Field name for error messages

**Returns:** `bool` - True if valid

---

### `string_length( $value, $min = null, $max = null, $field_name = 'field' )`
Validates string length.

**Parameters:**
- `$value` (string) - String to validate
- `$min` (int|null) - Minimum length
- `$max` (int|null) - Maximum length
- `$field_name` (string) - Field name for error messages

**Returns:** `bool` - True if valid

---

### `table_title( $title )`
Validates table title (required, 3-200 characters).

**Parameters:**
- `$title` (string) - Title to validate

**Returns:** `array` - Array with 'valid' (bool) and 'errors' (array)

---

### `table_data( $data )`
Validates table data structure (array of arrays, consistent column count).

**Parameters:**
- `$data` (array) - Table data to validate

**Returns:** `array` - Array with 'valid' (bool) and 'errors' (array)

---

### `validate_fields( $data, $rules )`
Validates multiple fields at once using rule definitions.

**Parameters:**
- `$data` (array) - Data to validate
- `$rules` (array) - Validation rules

**Returns:** `array` - Array with 'valid' (bool) and 'errors' (array)

**Rule Format:**
```php
$rules = array(
    'field_name' => array( 'rule1', 'rule2:param1:param2' ),
);
```

---

## Error Handling

### Clear Errors
```php
Validator::clear_errors();
```

### Get All Errors
```php
$errors = Validator::get_errors();
// Returns: array( 'field_name' => array( 'error 1', 'error 2' ) )
```

### Display Errors
```php
foreach ( Validator::get_errors() as $field => $field_errors ) {
    foreach ( $field_errors as $error ) {
        echo '<p class="error">' . esc_html( $error ) . '</p>';
    }
}
```

---

## Best Practices

### 1. Always Clear Errors Before Validation
```php
Validator::clear_errors();
// Then validate...
```

### 2. Use Field Names for Better Error Messages
```php
// Bad
Validator::required( $value );

// Good
Validator::required( $title, 'table_title' );
```

### 3. Check Errors After Validation
```php
Validator::clear_errors();
Validator::required( $title, 'title' );
Validator::string_length( $title, 3, 200, 'title' );

if ( ! empty( Validator::get_errors() ) ) {
    // Handle errors
}
```

### 4. Use Specialized Validators for Common Patterns
```php
// Instead of multiple validators
$validation = Validator::table_title( $title );

// Instead of
Validator::required( $title );
Validator::string_length( $title, 3, 200 );
```

---

## Security Considerations

### File Upload Safety
The validator includes security checks for:
- ✅ Directory traversal attempts (`../`)
- ✅ Null byte injection
- ✅ File type validation
- ✅ File size limits
- ✅ Malicious file names

### Nonce Validation
Always validate nonces for form submissions:
```php
if ( ! Validator::nonce( $_POST['_wpnonce'], 'create_table_action' ) ) {
    wp_die( 'Security check failed' );
}
```

---

## Testing

### Unit Test Example
```php
public function test_email_validation() {
    Validator::clear_errors();
    
    $this->assertTrue( Validator::email( 'test@example.com', 'email' ) );
    $this->assertEmpty( Validator::get_errors() );
    
    Validator::clear_errors();
    $this->assertFalse( Validator::email( 'invalid-email', 'email' ) );
    $this->assertNotEmpty( Validator::get_errors() );
}
```

---

## Changelog

### Version 1.1.0 (Current)
- ✅ Added error collection system
- ✅ Added field name parameters for better error messages
- ✅ Added `alphanumeric()` validation
- ✅ Added `slug()` validation
- ✅ Added `in_array()` validation for enums
- ✅ Added `table_title()` specialized validator
- ✅ Added `table_data()` specialized validator
- ✅ Added `validate_fields()` for multi-field validation
- ✅ Enhanced file upload security
- ✅ Added detailed error messages with translations
- ✅ Added `safe_filename()` security check

### Version 1.0.0
- Initial release with basic validation methods

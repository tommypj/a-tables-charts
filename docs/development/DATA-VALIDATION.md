# Data Validation - User Guide

## 📋 Overview
Data Validation ensures data quality and integrity by enforcing rules on table data. Catch errors before they cause problems!

## 🎯 Key Features

### **✅ 13 Validation Rules**
Comprehensive rule set for all data types
- Required fields
- Type validation
- Min/max values
- Length limits
- Pattern matching
- Email/URL validation
- Date validation
- Enum (allowed values)
- Unique values
- Custom functions

### **✅ 10 Preset Configurations**
Ready-to-use validation profiles
- Required Field
- Email Address
- Website URL
- Positive Number
- Integer Only
- Percentage (0-100)
- Phone Number
- ZIP Code
- Date Field
- Short Text

### **✅ Real-time Validation**
Instant feedback on data quality
- Validate on import
- Validate on edit
- Batch validation
- Error reports

---

## 🔒 Validation Rules

### **1. Required**
Field must not be empty

**Configuration:**
```php
'required' => true
```

**Examples:**
```
✅ "John Doe"
✅ "0"
❌ "" (empty)
❌ null
```

**Use Cases:**
- Name fields
- Required selections
- Mandatory information

---

### **2. Type**
Validate data type

**Supported Types:**
- `string` - Text
- `number`/`numeric` - Any number
- `integer` - Whole numbers only
- `float`/`decimal` - Decimal numbers
- `boolean` - True/false
- `date` - Date format
- `email` - Email format
- `url` - URL format

**Configuration:**
```php
'type' => 'numeric'
```

**Examples:**
```
Type: 'numeric'
✅ 123
✅ 45.67
✅ "89" (numeric string)
❌ "abc"

Type: 'integer'
✅ 123
❌ 45.67
❌ "abc"

Type: 'email'
✅ "user@example.com"
❌ "not-an-email"
```

---

### **3. Min (Minimum Value)**
Minimum numeric value

**Configuration:**
```php
'min' => 0
```

**Examples:**
```
Min: 0
✅ 0
✅ 10
✅ 100
❌ -5
❌ -1
```

**Use Cases:**
- Prices (min 0)
- Ages (min 0)
- Quantities (min 1)
- Ratings (min 1)

---

### **4. Max (Maximum Value)**
Maximum numeric value

**Configuration:**
```php
'max' => 100
```

**Examples:**
```
Max: 100
✅ 50
✅ 100
❌ 101
❌ 150
```

**Use Cases:**
- Percentages (max 100)
- Age limits (max 120)
- Ratings (max 5)
- Scores (max 100)

---

### **5. Min Length**
Minimum text length

**Configuration:**
```php
'min_length' => 3
```

**Examples:**
```
Min Length: 3
✅ "abc"
✅ "hello"
❌ "ab"
❌ "x"
```

**Use Cases:**
- Passwords (min 8)
- Names (min 2)
- Descriptions (min 10)

---

### **6. Max Length**
Maximum text length

**Configuration:**
```php
'max_length' => 100
```

**Examples:**
```
Max Length: 100
✅ "Short text"
✅ (95 characters)
❌ (105 characters)
```

**Use Cases:**
- Tweet-like fields (max 280)
- Titles (max 100)
- Short descriptions (max 200)

---

### **7. Pattern (Regex)**
Regular expression matching

**Configuration:**
```php
'pattern' => '/^[A-Z]{3}-\d{4}$/'
```

**Examples:**
```
Pattern: /^[A-Z]{3}-\d{4}$/
✅ "ABC-1234"
✅ "XYZ-5678"
❌ "abc-1234" (lowercase)
❌ "AB-1234" (too short)
❌ "ABCD-1234" (too long)
```

**Common Patterns:**
```php
// Phone: (123) 456-7890
'/^\(\d{3}\) \d{3}-\d{4}$/'

// ZIP Code: 12345 or 12345-6789
'/^\d{5}(-\d{4})?$/'

// Product Code: PRD-001
'/^PRD-\d{3}$/'

// License Plate: ABC 1234
'/^[A-Z]{3} \d{4}$/'
```

---

### **8. Email**
Valid email address

**Configuration:**
```php
'email' => true
```

**Examples:**
```
✅ "user@example.com"
✅ "name+tag@domain.co.uk"
❌ "not-an-email"
❌ "@example.com"
❌ "user@"
```

---

### **9. URL**
Valid URL format

**Configuration:**
```php
'url' => true
```

**Examples:**
```
✅ "https://example.com"
✅ "http://site.org/page"
✅ "ftp://files.com"
❌ "not a url"
❌ "example.com" (no protocol)
```

---

### **10. Date**
Valid date format

**Configuration:**
```php
'date' => true
```

**Examples:**
```
✅ "2025-01-15"
✅ "January 15, 2025"
✅ "15/01/2025"
✅ "Jan 15, 2025"
❌ "not-a-date"
❌ "99/99/9999"
```

---

### **11. Enum (Allowed Values)**
Must be one of specified values

**Configuration:**
```php
'enum' => array('Active', 'Pending', 'Inactive')
```

**Examples:**
```
Allowed: ['Active', 'Pending', 'Inactive']
✅ "Active"
✅ "Pending"
✅ "Inactive"
❌ "Archived"
❌ "active" (case sensitive)
```

**Use Cases:**
- Status fields
- Categories
- Dropdown selections
- Fixed options

---

### **12. Unique**
No duplicate values allowed

**Configuration:**
```php
'unique' => true
```

**Examples:**
```
Column: Email
✅ First occurrence: "user1@example.com"
❌ Duplicate: "user1@example.com"
```

**Use Cases:**
- Email addresses
- Usernames
- Product SKUs
- IDs

---

### **13. Custom**
Custom validation function

**Configuration:**
```php
'custom' => function($value, $column, $row_index) {
    if ($value < 18) {
        return "Must be 18 or older";
    }
    return true;
}
```

**Use Cases:**
- Complex business rules
- Multi-field validation
- Custom logic

---

## 🎨 Preset Configurations

### **1. Required Field**
```php
array(
    'required' => true
)
```

### **2. Email Address**
```php
array(
    'type' => 'email',
    'email' => true
)
```

### **3. Website URL**
```php
array(
    'type' => 'url',
    'url' => true
)
```

### **4. Positive Number**
```php
array(
    'type' => 'numeric',
    'min' => 0.01
)
```

### **5. Integer Only**
```php
array(
    'type' => 'integer'
)
```

### **6. Percentage (0-100)**
```php
array(
    'type' => 'numeric',
    'min' => 0,
    'max' => 100
)
```

### **7. Phone Number**
```php
array(
    'pattern' => '/^[\d\s\-\+\(\)]+$/'
)
```

### **8. ZIP Code (US)**
```php
array(
    'pattern' => '/^\d{5}(-\d{4})?$/'
)
```

### **9. Date Field**
```php
array(
    'type' => 'date',
    'date' => true
)
```

### **10. Short Text (max 100)**
```php
array(
    'type' => 'string',
    'max_length' => 100
)
```

---

## 💡 Use Cases & Examples

### **Example 1: User Registration Form**

**Validation Rules:**
```php
array(
    'Name' => array(
        'required' => true,
        'type' => 'string',
        'min_length' => 2,
        'max_length' => 50
    ),
    'Email' => array(
        'required' => true,
        'type' => 'email',
        'email' => true,
        'unique' => true
    ),
    'Age' => array(
        'required' => true,
        'type' => 'integer',
        'min' => 18,
        'max' => 120
    ),
    'Phone' => array(
        'pattern' => '/^\(\d{3}\) \d{3}-\d{4}$/'
    )
)
```

---

### **Example 2: Product Inventory**

**Validation Rules:**
```php
array(
    'SKU' => array(
        'required' => true,
        'pattern' => '/^PRD-\d{6}$/',
        'unique' => true
    ),
    'Name' => array(
        'required' => true,
        'max_length' => 100
    ),
    'Price' => array(
        'required' => true,
        'type' => 'numeric',
        'min' => 0.01
    ),
    'Stock' => array(
        'required' => true,
        'type' => 'integer',
        'min' => 0
    ),
    'Category' => array(
        'required' => true,
        'enum' => array('Electronics', 'Clothing', 'Food', 'Books')
    )
)
```

---

### **Example 3: Employee Directory**

**Validation Rules:**
```php
array(
    'Employee ID' => array(
        'required' => true,
        'pattern' => '/^EMP\d{5}$/',
        'unique' => true
    ),
    'First Name' => array(
        'required' => true,
        'min_length' => 2
    ),
    'Last Name' => array(
        'required' => true,
        'min_length' => 2
    ),
    'Email' => array(
        'required' => true,
        'type' => 'email',
        'unique' => true
    ),
    'Department' => array(
        'required' => true,
        'enum' => array('HR', 'IT', 'Sales', 'Marketing', 'Finance')
    ),
    'Start Date' => array(
        'required' => true,
        'type' => 'date'
    )
)
```

---

### **Example 4: Order Management**

**Validation Rules:**
```php
array(
    'Order ID' => array(
        'required' => true,
        'pattern' => '/^ORD-\d{8}$/',
        'unique' => true
    ),
    'Customer Email' => array(
        'required' => true,
        'type' => 'email'
    ),
    'Total' => array(
        'required' => true,
        'type' => 'numeric',
        'min' => 0.01
    ),
    'Status' => array(
        'required' => true,
        'enum' => array('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled')
    ),
    'Tracking Number' => array(
        'pattern' => '/^[A-Z0-9]{10,20}$/'
    )
)
```

---

## 🔧 Implementation

### **Define Validation Rules:**
```php
$validation_rules = array(
    'Email' => array(
        'required' => true,
        'email' => true
    ),
    'Age' => array(
        'type' => 'integer',
        'min' => 18,
        'max' => 120
    )
);
```

### **Validate Data:**
```php
$validation_service = new ValidationService();
$result = $validation_service->validate($data, $validation_rules);

if ($result['valid']) {
    // Data is valid
} else {
    // Handle errors
    foreach ($result['errors'] as $error) {
        echo $error . "\n";
    }
}
```

---

## 📊 Error Messages

### **Format:**
```
Row {number}: {column} {error message}
```

### **Examples:**
```
Row 1: Email is required.
Row 2: Age must be at least 18.
Row 3: Price must be of type numeric.
Row 4: Phone format is invalid.
Row 5: Status must be one of: Active, Pending, Inactive.
```

---

## 🎓 Best Practices

### **1. Start Simple**
Begin with basic rules, add complexity as needed
```php
// Start with
'Email' => array('required' => true)

// Then add
'Email' => array('required' => true, 'email' => true)

// Finally add
'Email' => array('required' => true, 'email' => true, 'unique' => true)
```

### **2. Use Presets**
Leverage preset configurations for common scenarios

### **3. Combine Rules**
Multiple rules work together
```php
'Password' => array(
    'required' => true,
    'type' => 'string',
    'min_length' => 8,
    'max_length' => 50,
    'pattern' => '/^(?=.*[A-Z])(?=.*\d)/' // At least 1 uppercase, 1 digit
)
```

### **4. Clear Error Messages**
Custom messages help users understand issues

### **5. Validate Early**
Check data on import before saving

### **6. Document Rules**
Keep track of validation requirements

---

## 🐛 Troubleshooting

### **Problem: Too many errors**
**Solution:** Review rules - might be too strict

### **Problem: Regex not working**
**Solution:** Test pattern separately, escape special characters

### **Problem: Type mismatch**
**Solution:** Ensure data type matches validation type

### **Problem: Unique not detecting duplicates**
**Solution:** Ensure exact value match (case-sensitive)

---

## 🚀 Coming Soon

- **Visual Rule Builder** - UI for creating rules
- **Rule Templates** - Industry-specific presets
- **Conditional Validation** - Rules based on other fields
- **Warning Levels** - Soft warnings vs hard errors
- **Auto-correction** - Suggest fixes for errors
- **Batch Validation** - Validate multiple tables at once
- **Validation History** - Track validation over time

---

**Data Validation ensures your data is always high quality! ✅**

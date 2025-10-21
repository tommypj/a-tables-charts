# A-Tables & Charts - Unit Tests

Comprehensive unit test suite for the A-Tables & Charts WordPress plugin.

## 📋 **Overview**

This test suite ensures code quality, reliability, and maintainability through comprehensive unit testing.

---

## 🧪 **Test Coverage**

### **Entities**
- ✅ Table entity validation
- ✅ Data structure integrity
- ✅ Type conversions

### **Repositories**
- ✅ CRUD operations
- ✅ Search and filter
- ✅ Sorting and pagination
- ✅ Data retrieval

### **Services**
- ✅ CSV export generation
- ✅ File parsing
- ✅ Data validation

### **Parsers**
- ✅ CSV parsing
- ✅ Header detection
- ✅ Special character handling

---

## 🚀 **Setup**

### **1. Install Dependencies**

```bash
cd wp-content/plugins/a-tables-charts
composer install
```

### **2. Install WordPress Test Library**

```bash
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

Replace `root` and `''` with your MySQL username and password.

---

## ▶️ **Running Tests**

### **Run All Tests**

```bash
composer test
```

Or:

```bash
vendor/bin/phpunit
```

### **Run Specific Test File**

```bash
vendor/bin/phpunit tests/unit/TableTest.php
```

### **Run with Coverage Report**

```bash
composer test-coverage
```

This generates an HTML coverage report in the `coverage/` directory.

---

## 📊 **Test Structure**

```
tests/
├── bootstrap/
│   ├── bootstrap.php          # Test environment setup
│   └── TestCase.php            # Base test class
├── unit/
│   ├── TableTest.php           # Table entity tests
│   ├── TableRepositoryTest.php # Repository tests
│   ├── CSVExportServiceTest.php # Export service tests
│   └── CSVParserTest.php       # Parser tests
└── fixtures/
    ├── sample.csv              # Sample test data
    └── products.csv            # Product test data
```

---

## ✅ **Test Cases**

### **TableTest** (15 tests)
- ✅ Create table with valid data
- ✅ Validate required fields
- ✅ Validate source types
- ✅ Header and data retrieval
- ✅ Array/database conversions
- ✅ Empty table detection
- ✅ Size calculation

### **TableRepositoryTest** (14 tests)
- ✅ Create table
- ✅ Find by ID
- ✅ Find all with pagination
- ✅ Update table
- ✅ Delete table
- ✅ Count tables
- ✅ Search tables
- ✅ Get filtered data
- ✅ Sort data
- ✅ Paginate results

### **CSVExportServiceTest** (12 tests)
- ✅ Generate CSV string
- ✅ Handle special characters
- ✅ Validate export data
- ✅ Generate safe filenames
- ✅ Handle empty data
- ✅ Maintain column order
- ✅ Handle missing fields
- ✅ Process numeric values

### **CSVParserTest** (13 tests)
- ✅ Parse valid CSV
- ✅ Detect headers
- ✅ Custom delimiters
- ✅ Validate files
- ✅ Handle special characters
- ✅ Count accuracy
- ✅ Data structure validation
- ✅ Empty file handling

---

## 🎯 **Writing Tests**

### **Basic Test Structure**

```php
<?php
namespace ATablesCharts\Tests\Unit;

use ATablesCharts\Tests\TestCase;

class MyFeatureTest extends TestCase {
    
    public function setUp(): void {
        parent::setUp();
        // Setup code
    }
    
    public function test_feature_works() {
        // Arrange
        $data = $this->get_sample_table_data();
        
        // Act
        $result = someFunction($data);
        
        // Assert
        $this->assertTrue($result);
    }
}
```

### **Naming Conventions**

- Test files: `*Test.php`
- Test methods: `test_*` or `testFeatureName`
- Use descriptive names: `test_validate_with_invalid_data`

### **Assertions**

```php
// Type checks
$this->assertIsInt($value);
$this->assertIsArray($data);
$this->assertInstanceOf(Table::class, $table);

// Value checks
$this->assertEquals('expected', $actual);
$this->assertTrue($condition);
$this->assertNull($value);

// Array checks
$this->assertCount(3, $array);
$this->assertArrayHasKey('key', $array);
$this->assertContains('value', $array);

// String checks
$this->assertStringContainsString('needle', $haystack);
```

---

## 🔧 **Configuration**

### **phpunit.xml**

Key settings:
- Bootstrap: `tests/bootstrap/bootstrap.php`
- Test suite: `tests/unit`
- Colors: Enabled for better readability
- Code coverage: Includes `src/` directory

### **Custom Assertions**

The base `TestCase` class provides custom assertions:

```php
// Assert array has multiple keys
$this->assertArrayHasKeys(['key1', 'key2'], $array);

// Assert valid table object
$this->assertIsValidTable($table);
```

---

## 🐛 **Debugging Tests**

### **View Errors**

```bash
vendor/bin/phpunit --verbose
```

### **Stop on First Failure**

```bash
vendor/bin/phpunit --stop-on-failure
```

### **Run Specific Test Method**

```bash
vendor/bin/phpunit --filter test_create_table
```

### **Debug Output**

Add to your test:

```php
var_dump($variable);
print_r($array);
$this->expectOutputString('expected output');
```

---

## 📈 **Code Coverage**

### **Generate Coverage Report**

```bash
composer test-coverage
```

### **View Coverage**

Open `coverage/index.html` in your browser.

### **Coverage Goals**

- **Overall:** 80%+
- **Critical paths:** 90%+
- **Services:** 85%+
- **Repositories:** 90%+

---

## 🎨 **Best Practices**

### **1. Test Independence**

Each test should be independent:

```php
public function test_feature_a() {
    // Complete test - doesn't depend on other tests
}

public function test_feature_b() {
    // Complete test - doesn't depend on test_feature_a
}
```

### **2. Arrange-Act-Assert**

Structure tests clearly:

```php
public function test_something() {
    // Arrange: Set up test data
    $data = $this->get_sample_table_data();
    
    // Act: Execute the code
    $result = $service->process($data);
    
    // Assert: Verify the outcome
    $this->assertTrue($result->success);
}
```

### **3. Test One Thing**

Each test should verify one behavior:

```php
// ✅ Good
public function test_creates_table() { }
public function test_validates_title() { }

// ❌ Bad
public function test_table_creation_and_validation() { }
```

### **4. Use Descriptive Names**

```php
// ✅ Good
public function test_create_table_fails_without_title() { }

// ❌ Bad
public function test_table() { }
```

### **5. Clean Up Resources**

```php
public function tearDown(): void {
    // Clean up files, database, etc.
    parent::tearDown();
}
```

---

## 🚨 **Continuous Integration**

### **GitHub Actions Example**

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
          
      - name: Install dependencies
        run: composer install
        
      - name: Run tests
        run: composer test
```

---

## 📝 **Common Issues**

### **Database Connection Error**

Make sure WordPress test library is installed:

```bash
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

### **Class Not Found**

Run composer autoload:

```bash
composer dump-autoload
```

### **Permission Denied**

Make tests directory writable:

```bash
chmod -R 755 tests/
```

---

## 🎯 **Next Steps**

1. **Run the tests** - `composer test`
2. **Check coverage** - `composer test-coverage`
3. **Write new tests** for new features
4. **Keep coverage high** - Aim for 80%+
5. **Run tests before commits** - Ensure quality

---

## 📚 **Resources**

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [WordPress Plugin Testing](https://make.wordpress.org/cli/handbook/plugin-unit-tests/)
- [Test-Driven Development](https://en.wikipedia.org/wiki/Test-driven_development)

---

## ✅ **Status**

- ✅ **54 Tests** covering core functionality
- ✅ **4 Test Suites** for different components
- ✅ **Fixtures** for realistic test data
- ✅ **Base TestCase** with helper methods
- ✅ **PHPUnit Config** optimized for WordPress

**All systems ready for testing!** 🚀

# Running PHPUnit Tests with Local by Flywheel

## 🎯 Quick Start - The Easiest Way

### Step 1: Open Local's Site Shell

1. **Open Local by Flywheel** application
2. **Select your site** (`my-wordpress-site`) from the left sidebar
3. **Click "Open Site Shell"** button (terminal icon in the top right)
   - Or right-click the site → "Open Site Shell"

This opens a terminal with PHP already configured! ✅

### Step 2: Navigate to Plugin Directory

```bash
cd wp-content/plugins/a-tables-charts
```

### Step 3: Run Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run a specific test file
vendor/bin/phpunit tests/unit/Shared/ValidatorTest.php

# Run with verbose output
vendor/bin/phpunit --verbose

# Run with colors (better readability)
vendor/bin/phpunit --colors=always

# Run with detailed test information
vendor/bin/phpunit --testdox
```

---

## 📋 Available Test Files

### Current Test Suite

```
tests/
├── unit/
│   ├── CSVExportServiceTest.php       # CSV export functionality
│   ├── CSVParserTest.php              # CSV parsing
│   ├── TableRepositoryTest.php        # Database operations
│   ├── TableTest.php                  # Table entity
│   └── Shared/
│       └── ValidatorTest.php          # Input validation
└── fixtures/
    ├── sample.csv                     # Test CSV files
    ├── products.csv
    ├── sample-books.xml
    ├── sample-employees.xml
    └── sample-products.xml
```

---

## 🧪 Test Examples

### 1. Run All Tests

```bash
vendor/bin/phpunit
```

**Expected Output:**
```
PHPUnit 9.5.x by Sebastian Bergmann and contributors.

Runtime:       PHP 8.0.x
Configuration: /path/to/phpunit.xml

.....................................................  57 / 57 (100%)

Time: 00:01.234, Memory: 18.00 MB

OK (57 tests, 150 assertions)
```

### 2. Run Validator Tests Only

```bash
vendor/bin/phpunit tests/unit/Shared/ValidatorTest.php
```

**Expected Output:**
```
PHPUnit 9.5.x by Sebastian Bergmann and contributors.

.......................                             23 / 23 (100%)

Time: 00:00.156, Memory: 10.00 MB

OK (23 tests, 45 assertions)
```

### 3. Run Tests with Detailed Output

```bash
vendor/bin/phpunit --testdox
```

**Expected Output:**
```
Validator (ATablesCharts\Tests\Unit\Shared\Validator)
 ✔ Email validation with valid email
 ✔ Email validation with invalid email
 ✔ Email validation with empty value
 ✔ Integer validation with valid integer
 ✔ Integer validation with minimum value
 ✔ Integer validation with maximum value
 ✔ Integer validation with range
 ✔ Required validation with value
 ✔ Required validation with empty string
 ✔ Required validation with whitespace
 ✔ Required validation with array
 ✔ String length validation
 ✔ Url validation
 ✔ Json validation
 ✔ Multiple validation errors
 ✔ Clear errors
```

### 4. Run Tests with Coverage Report

```bash
# Generate coverage report (requires Xdebug)
vendor/bin/phpunit --coverage-html coverage-report

# View the report
# Open: coverage-report/index.html in browser
```

---

## 🔍 Understanding Test Output

### Success Output

```
.....
```
Each dot (`.`) represents a **passed test**.

### Failure Output

```
F....
```
`F` = **Failed test**

### Error Output

```
E....
```
`E` = **Error in test**

### Skipped Output

```
S....
```
`S` = **Skipped test**

---

## 📊 Current Test Coverage

### Validator Tests (ValidatorTest.php)

✅ **23 Tests Covering:**

1. **Email Validation** (3 tests)
   - Valid email
   - Invalid email
   - Empty email

2. **Integer Validation** (4 tests)
   - Valid integer
   - Minimum value check
   - Maximum value check
   - Range validation

3. **Required Field Validation** (4 tests)
   - Non-empty value
   - Empty string
   - Whitespace only
   - Array validation

4. **String Length Validation** (1 test)
   - Min/max length checks

5. **URL Validation** (1 test)
   - Valid and invalid URLs

6. **JSON Validation** (1 test)
   - Valid and invalid JSON

7. **Error Management** (2 tests)
   - Multiple errors accumulation
   - Clear errors functionality

---

## 🐛 Troubleshooting

### Issue: "Command not found: vendor/bin/phpunit"

**Solution:**
```bash
# Make sure you're in the plugin directory
pwd
# Should show: /path/to/wp-content/plugins/a-tables-charts

# If vendor doesn't exist, run:
composer install
```

### Issue: "PHP Warning: include_once..."

**Solution:**
```bash
# Regenerate autoload files
composer dump-autoload
```

### Issue: Tests fail with "Class not found"

**Solution:**
```bash
# Check if bootstrap is working
cat tests/bootstrap/bootstrap.php

# Verify namespace structure
cat src/shared/utils/Validator.php | grep namespace
```

### Issue: "Cannot find WordPress functions"

This is expected in unit tests! We're testing pure PHP logic without WordPress dependencies.

**For integration tests that need WordPress:**
1. Install WordPress test suite
2. Configure WP_TESTS_DIR in phpunit.xml
3. Use WP_UnitTestCase instead of TestCase

---

## ⚙️ PHPUnit Configuration

Our `phpunit.xml` configuration:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="tests/bootstrap/bootstrap.php"
    colors="true"
    verbose="true"
    stopOnFailure="false"
>
    <testsuites>
        <testsuite name="Unit Tests">
            <directory>tests/unit</directory>
        </testsuite>
    </testsuites>

    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">src</directory>
        </include>
        <exclude>
            <directory>src/modules/core/views</directory>
        </exclude>
    </coverage>
</phpunit>
```

**Key Settings:**
- ✅ Colors enabled for better readability
- ✅ Verbose output for detailed info
- ✅ Continues on failures to see all issues
- ✅ Coverage tracking for code quality

---

## 🎯 Writing New Tests

### Test Template

```php
<?php
namespace ATablesCharts\Tests\Unit\YourModule;

use ATablesCharts\Tests\Bootstrap\TestCase;
use ATablesCharts\YourModule\YourClass;

class YourClassTest extends TestCase {

    /**
     * Set up before each test
     */
    public function setUp(): void {
        parent::setUp();
        // Initialize test data
    }

    /**
     * Test your functionality
     */
    public function test_your_feature() {
        // Arrange
        $input = 'test data';
        
        // Act
        $result = YourClass::your_method($input);
        
        // Assert
        $this->assertEquals('expected', $result);
    }

    /**
     * Clean up after each test
     */
    public function tearDown(): void {
        parent::tearDown();
        // Clean up test data
    }
}
```

### Assertion Methods

```php
// Equality
$this->assertEquals($expected, $actual);
$this->assertSame($expected, $actual); // Strict comparison

// Boolean
$this->assertTrue($condition);
$this->assertFalse($condition);

// Arrays
$this->assertArrayHasKey('key', $array);
$this->assertContains('value', $array);
$this->assertCount(5, $array);

// Strings
$this->assertStringContainsString('needle', $haystack);
$this->assertMatchesRegularExpression('/pattern/', $string);

// Empty/Null
$this->assertEmpty($value);
$this->assertNull($value);
$this->assertNotEmpty($value);

// Exceptions
$this->expectException(Exception::class);
$this->expectExceptionMessage('Error message');
```

---

## 📈 Running Tests During Development

### Watch Mode (Continuous Testing)

While PHPUnit doesn't have built-in watch mode, you can use:

```bash
# Install file watcher (one time)
npm install -g watch-cli

# Watch and run tests on file changes
watch "vendor/bin/phpunit" src tests
```

### Quick Test Iteration

```bash
# Test single file during development
vendor/bin/phpunit tests/unit/Shared/ValidatorTest.php --colors=always

# Test specific method
vendor/bin/phpunit tests/unit/Shared/ValidatorTest.php --filter test_email_validation_with_valid_email
```

---

## 🚀 Next Steps

### 1. Run Current Tests

```bash
# Open Site Shell in Local
cd wp-content/plugins/a-tables-charts
vendor/bin/phpunit --testdox
```

### 2. Verify All Tests Pass

All 23 Validator tests should pass! ✅

### 3. Add More Tests

Consider adding tests for:
- CSVParser edge cases
- TableRepository CRUD operations
- Chart generation logic
- Export functionality

### 4. Continuous Integration

Future: Set up GitHub Actions to run tests automatically on every commit.

---

## 📚 Resources

- **PHPUnit Documentation**: https://phpunit.de/documentation.html
- **WordPress Testing**: https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/
- **Local by Flywheel**: https://localwp.com/help-docs/

---

## ✅ Quick Checklist

Before running tests:
- [ ] Local by Flywheel is running
- [ ] Site Shell is open
- [ ] In plugin directory: `wp-content/plugins/a-tables-charts`
- [ ] `vendor/` directory exists
- [ ] `tests/` directory exists
- [ ] `phpunit.xml` is present

Run tests:
- [ ] `vendor/bin/phpunit` - All tests pass
- [ ] `vendor/bin/phpunit --testdox` - Readable output
- [ ] Individual test files can run successfully

---

## 🎉 You're Ready!

Your test environment is fully set up. Just open Local's Site Shell and start testing!

```bash
# The magic command:
vendor/bin/phpunit --testdox --colors=always
```

Happy testing! 🧪✨

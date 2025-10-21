# 🚀 Quick Testing Commands - Cheat Sheet

## Open Site Shell First!
**Local by Flywheel → Select Site → Click "Open Site Shell"**

---

## Navigate to Plugin
```bash
cd wp-content/plugins/a-tables-charts
```

---

## Essential Commands

### Run All Tests
```bash
vendor/bin/phpunit
```

### Run with Pretty Output
```bash
vendor/bin/phpunit --testdox --colors=always
```

### Run Specific Test File
```bash
# Validator tests
vendor/bin/phpunit tests/unit/Shared/ValidatorTest.php

# CSV Parser tests
vendor/bin/phpunit tests/unit/CSVParserTest.php

# Table Repository tests
vendor/bin/phpunit tests/unit/TableRepositoryTest.php
```

### Run Specific Test Method
```bash
vendor/bin/phpunit tests/unit/Shared/ValidatorTest.php --filter test_email_validation
```

---

## Useful Options

### Verbose Output
```bash
vendor/bin/phpunit --verbose
```

### Stop on First Failure
```bash
vendor/bin/phpunit --stop-on-failure
```

### Coverage Report (requires Xdebug)
```bash
vendor/bin/phpunit --coverage-html coverage-report
```

### Test Summary
```bash
vendor/bin/phpunit --testdox
```

---

## Troubleshooting

### Vendor directory missing?
```bash
composer install
```

### Autoload issues?
```bash
composer dump-autoload
```

### Check PHP version
```bash
php --version
```

### Verify Composer
```bash
composer --version
```

---

## Test Structure

```
tests/
├── bootstrap/          # Test setup files
├── fixtures/          # Test data (CSV, XML)
└── unit/              # Unit tests
    ├── CSVExportServiceTest.php
    ├── CSVParserTest.php
    ├── TableRepositoryTest.php
    ├── TableTest.php
    └── Shared/
        └── ValidatorTest.php
```

---

## Expected Results

### All Tests Pass
```
.....................................................  57 / 57 (100%)

OK (57 tests, 150 assertions)
```

### Test Failed
```
F....................................................  57 / 57 (100%)

FAILURES!
Tests: 57, Assertions: 149, Failures: 1.
```

---

## Quick Verification

```bash
# 1. Check you're in right directory
pwd
# Should show: .../wp-content/plugins/a-tables-charts

# 2. List test files
ls tests/unit

# 3. Run tests
vendor/bin/phpunit --testdox
```

---

## Color Legend

- 🟢 **Green dots (`.`)** = Tests passed ✅
- 🔴 **Red 'F'** = Test failed ❌
- 🟡 **Yellow 'E'** = Error in test ⚠️
- 🔵 **Blue 'S'** = Test skipped ⏭️

---

## Pro Tips

1. **Always use `--testdox`** for readable output
2. **Always use `--colors=always`** for better visibility  
3. **Run tests before committing** code
4. **Fix tests immediately** when they fail
5. **Write tests first** (TDD) when adding features

---

## Copy-Paste Ready Commands

```bash
# Complete test run with nice output
vendor/bin/phpunit --testdox --colors=always

# Quick validator test
vendor/bin/phpunit tests/unit/Shared/ValidatorTest.php --colors=always

# Stop on first problem
vendor/bin/phpunit --stop-on-failure --colors=always

# Detailed verbose
vendor/bin/phpunit --verbose --colors=always
```

---

## 🎯 REMEMBER

**Open Local's Site Shell FIRST, then run these commands!**

You can't run these commands in regular Windows CMD/PowerShell because PHP isn't in your PATH. Local's Site Shell has everything configured automatically! ✅

---

## Need Help?

See full guide: `TESTING-WITH-LOCAL-SHELL.md`

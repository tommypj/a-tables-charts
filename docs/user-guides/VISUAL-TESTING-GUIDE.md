# 📸 Visual Guide: Running Tests in Local by Flywheel

## Step-by-Step with Visual Instructions

---

## 🎯 Step 1: Open Local by Flywheel

### What to Look For:

```
┌─────────────────────────────────────────┐
│  LOCAL                          ☰  🔍   │
├─────────────────────────────────────────┤
│                                         │
│  ○ my-wordpress-site      [RUNNING] ✓  │
│     localhost:10080                     │
│     PHP 8.0.2 • MySQL 8.0               │
│                                         │
└─────────────────────────────────────────┘
```

**Actions:**
1. Launch **Local by Flywheel** application
2. Look for your site in the left sidebar: `my-wordpress-site`
3. Make sure site status shows **[RUNNING]** (green indicator)
4. If stopped, click the site and press **Start Site**

---

## 🎯 Step 2: Open Site Shell

### Method A: Using the Button

Look at the top right of the Local window after selecting your site:

```
┌─────────────────────────────────────────┐
│  my-wordpress-site                      │
├─────────────────────────────────────────┤
│                                         │
│  [Admin]  [Open Site]  [Open Shell] ← CLICK THIS  │
│                                         │
│  Overview                               │
│  Database                               │
│  SSL                                    │
│  Tools                                  │
│                                         │
└─────────────────────────────────────────┘
```

### Method B: Right-Click Menu

Right-click on `my-wordpress-site` in the left sidebar:

```
Right-click menu:
┌──────────────────────┐
│ Open Site Shell      │ ← Click here
│ Stop Site            │
│ Restart              │
│ Clone                │
│ Delete               │
│ Change PHP Version   │
└──────────────────────┘
```

### What Happens:

A terminal window opens with a prompt like:

```bash
user@localhost:~/Local Sites/my-wordpress-site/app/public$
```

**This is your Site Shell! ✅**

---

## 🎯 Step 3: Navigate to Plugin Directory

### In the Site Shell, type:

```bash
cd wp-content/plugins/a-tables-charts
```

### Press Enter

Your prompt should now show:

```bash
user@localhost:~/Local Sites/my-wordpress-site/app/public/wp-content/plugins/a-tables-charts$
```

### Verify you're in the right place:

```bash
ls
```

You should see output like:

```
a-tables-charts.php
assets/
composer.json
phpunit.xml
src/
tests/
vendor/
README.md
...
```

**Perfect! You're ready to run tests! ✅**

---

## 🎯 Step 4: Run Tests

### Basic Test Run

Type this command:

```bash
vendor/bin/phpunit
```

### Expected Terminal Output:

```
PHPUnit 9.5.28 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.0.2
Configuration: /Users/tommy/Local Sites/my-wordpress-site/app/public/wp-content/plugins/a-tables-charts/phpunit.xml

.....................................................  57 / 57 (100%)

Time: 00:01.234, Memory: 18.00 MB

OK (57 tests, 150 assertions)
```

### What the Output Means:

```
.....   ← Green dots = passing tests ✅
F....   ← Red F = failed test ❌
E....   ← Yellow E = error ⚠️
S....   ← Blue S = skipped test ⏭️
```

---

## 🎯 Step 5: Run Tests with Better Output

### Recommended Command:

```bash
vendor/bin/phpunit --testdox --colors=always
```

### Expected Terminal Output:

```
PHPUnit 9.5.28 by Sebastian Bergmann and contributors.

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

CSV Parser (ATablesCharts\Tests\Unit\CSVParser)
 ✔ Parses simple csv correctly
 ✔ Handles empty cells
 ✔ Handles quotes in values
 ✔ Detects headers correctly
 ... and more

Time: 00:01.456, Memory: 18.00 MB

OK (57 tests, 150 assertions)
```

**Much more readable! ✅**

---

## 🎯 Step 6: Test Specific Files

### Test Only Validator

```bash
vendor/bin/phpunit tests/unit/Shared/ValidatorTest.php --colors=always
```

### Terminal Output:

```
PHPUnit 9.5.28 by Sebastian Bergmann and contributors.

.......................                             23 / 23 (100%)

Time: 00:00.156, Memory: 10.00 MB

OK (23 tests, 45 assertions)
```

---

## 🎯 Common Terminal Views

### All Tests Pass ✅

```
$ vendor/bin/phpunit --testdox --colors=always

PHPUnit 9.5.28 by Sebastian Bergmann and contributors.

Validator
 ✔ Email validation with valid email
 ✔ Integer validation with valid integer
 ... (all tests shown with ✔)

Time: 00:01.234, Memory: 18.00 MB

OK (57 tests, 150 assertions)
```

### Test Failure ❌

```
$ vendor/bin/phpunit

PHPUnit 9.5.28 by Sebastian Bergmann and contributors.

....F.................................................  57 / 57 (100%)

Time: 00:01.234, Memory: 18.00 MB

FAILURES!
Tests: 57, Assertions: 149, Failures: 1.

There was 1 failure:

1) ValidatorTest::test_email_validation_with_valid_email
Failed asserting that false is true.

/path/to/ValidatorTest.php:25

FAILURES!
Tests: 57, Assertions: 149, Failures: 1.
```

---

## 🎯 Troubleshooting Visual Guide

### Issue: Terminal Says "command not found: vendor/bin/phpunit"

**What You See:**
```
$ vendor/bin/phpunit
-bash: vendor/bin/phpunit: No such file or directory
```

**Solution:**

1. Check you're in the right directory:
```bash
pwd
```

Should show: `.../wp-content/plugins/a-tables-charts`

2. Check if vendor exists:
```bash
ls -la
```

Look for `vendor/` directory in the list.

3. If no vendor directory:
```bash
composer install
```

Wait for installation to complete, then try again.

---

### Issue: PHP Not Found

**What You See:**
```
$ php --version
-bash: php: command not found
```

**Problem:** You're NOT in Local's Site Shell!

**Solution:**
1. Close current terminal
2. Go back to Local by Flywheel
3. Click **"Open Site Shell"** button again
4. Make sure you see Local's Site Shell prompt

---

### Issue: Wrong Directory

**What You See:**
```
user@localhost:~/Local Sites/my-wordpress-site/app/public$
```

**Problem:** You're in the wrong directory (public root instead of plugin)

**Solution:**
```bash
cd wp-content/plugins/a-tables-charts
```

**Correct Prompt:**
```
user@localhost:~/Local Sites/my-wordpress-site/app/public/wp-content/plugins/a-tables-charts$
```

---

## 🎯 Quick Reference Terminal Commands

### Check Where You Are
```bash
pwd
```

### List Files
```bash
ls
# or for detailed view:
ls -la
```

### Go Back to Plugin Root
```bash
cd ~/Local Sites/my-wordpress-site/app/public/wp-content/plugins/a-tables-charts
```

### Check PHP Version
```bash
php --version
```

Should show: `PHP 8.0.2` or similar

### Check Composer
```bash
composer --version
```

Should show: `Composer version 2.x.x`

---

## 🎯 Complete Workflow Visualization

```
1. Launch Local by Flywheel
   │
   ↓
2. Select Site: my-wordpress-site
   │
   ↓
3. Click "Open Site Shell"
   │
   ↓
4. Terminal Opens
   │
   ↓
5. Navigate: cd wp-content/plugins/a-tables-charts
   │
   ↓
6. Run: vendor/bin/phpunit --testdox --colors=always
   │
   ↓
7. View Results
   │
   ├─→ ✅ All Pass → Continue coding!
   │
   └─→ ❌ Some Fail → Fix the issues and re-run
```

---

## 🎯 What Your Screen Should Look Like

### Local by Flywheel Window:

```
┌──────────────────────────────────────────────────┐
│  LOCAL                             [minimize] [×] │
├────────┬─────────────────────────────────────────┤
│        │  my-wordpress-site                      │
│  Sites │                                          │
│        │  [Admin] [Open Site] [Open Shell]       │
│  ○ my- │                                          │
│  word  │  ┌────────────────────────────────────┐ │
│  press │  │ Overview                           │ │
│  -site │  │                                    │ │
│        │  │ Domain: localhost:10080            │ │
│        │  │ PHP: 8.0.2                         │ │
│        │  │ Web Server: nginx                  │ │
│        │  │ Database: MySQL 8.0                │ │
│        │  └────────────────────────────────────┘ │
│        │                                          │
└────────┴─────────────────────────────────────────┘
```

### Terminal Window (Site Shell):

```
┌──────────────────────────────────────────────────┐
│  Terminal - Site Shell                    [×] │
├──────────────────────────────────────────────────┤
│                                                  │
│  user@localhost:~/.../a-tables-charts$ ls       │
│  a-tables-charts.php  composer.json  src/       │
│  assets/              phpunit.xml    tests/      │
│  vendor/              README.md                  │
│                                                  │
│  user@localhost:~/.../a-tables-charts$           │
│  vendor/bin/phpunit --testdox --colors=always    │
│                                                  │
│  PHPUnit 9.5.28 by Sebastian Bergmann           │
│                                                  │
│  Validator                                       │
│   ✔ Email validation with valid email           │
│   ✔ Integer validation with valid integer       │
│   ✔ Required validation with value              │
│   ...                                            │
│                                                  │
│  OK (57 tests, 150 assertions)                   │
│                                                  │
│  user@localhost:~/.../a-tables-charts$ ▋        │
│                                                  │
└──────────────────────────────────────────────────┘
```

---

## ✅ Success Indicators

You know everything is working when:

1. ✅ **Local shows site as RUNNING** (green status)
2. ✅ **Terminal prompt shows plugin directory**
3. ✅ **PHP version displays correctly** (`php --version`)
4. ✅ **Tests run and show results**
5. ✅ **All tests pass** (green dots or checkmarks)

---

## 🚀 Ready to Test!

Follow these visual guides and you'll be running tests in minutes!

**Remember the golden rule:**
Always use **Local's Site Shell**, never regular terminal! ✨

# A-Tables & Charts - Comprehensive Test Plan

**Version:** 1.0
**Date:** 2025-11-03
**Features Under Test:**
- Scheduled Data Refresh (Cron)
- WP-CLI Commands
- Basic Performance Monitor

---

## 1. Test Scope

### 1.1 Features to Test
1. **Scheduled Data Refresh (Cron)**
   - Schedule CRUD operations
   - Data source refresh (MySQL, Google Sheets, CSV URL, REST API)
   - WP-Cron integration
   - Schedule activation/deactivation
   - Error handling and logging

2. **WP-CLI Commands**
   - Table commands (list, get, create, update, delete, duplicate)
   - Schedule commands (list, get, activate, deactivate, run, delete, stats)
   - Cache commands (clear, clear-table, stats, list, warmup)
   - Export/Import commands (all formats)

3. **Performance Monitor**
   - Timer start/stop functionality
   - Metrics collection (duration, memory, queries)
   - Statistics generation
   - Slow operation detection
   - Optimization recommendations

### 1.2 Test Types
- **Unit Tests:** Individual service/method testing
- **Integration Tests:** Multi-component workflows
- **CLI Tests:** WP-CLI command execution
- **Manual Tests:** UI-based functionality

---

## 2. Test Environment

### 2.1 Requirements
- WordPress 6.0+
- PHP 8.0+
- MySQL/MariaDB
- WP-CLI installed
- Test data fixtures

### 2.2 Test Data
- Sample tables (small, medium, large)
- Sample schedules (all source types)
- Mock API responses
- Sample CSV/JSON files

---

## 3. Test Cases

### 3.1 Scheduled Data Refresh

#### TC-CRON-001: Create MySQL Schedule
- **Objective:** Verify schedule creation with MySQL source
- **Steps:**
  1. Create schedule with valid MySQL query
  2. Verify schedule stored in database
  3. Verify WP-Cron event scheduled
- **Expected:** Schedule created, cron event registered

#### TC-CRON-002: Trigger Scheduled Refresh
- **Objective:** Verify manual refresh execution
- **Steps:**
  1. Create schedule for test table
  2. Trigger refresh manually
  3. Verify table data updated
- **Expected:** Table refreshed successfully

#### TC-CRON-003: Deactivate Schedule
- **Objective:** Verify schedule deactivation
- **Steps:**
  1. Create active schedule
  2. Deactivate schedule
  3. Verify cron event unscheduled
- **Expected:** Schedule inactive, cron stopped

#### TC-CRON-004: Delete Schedule
- **Objective:** Verify schedule deletion
- **Steps:**
  1. Create schedule
  2. Delete schedule
  3. Verify removed from database and cron
- **Expected:** Schedule deleted completely

#### TC-CRON-005: Google Sheets Refresh
- **Objective:** Verify Google Sheets integration
- **Steps:**
  1. Create schedule with Google Sheets config
  2. Trigger refresh
  3. Verify data fetched and table updated
- **Expected:** Data imported from Google Sheets

#### TC-CRON-006: CSV URL Refresh
- **Objective:** Verify CSV URL import
- **Steps:**
  1. Create schedule with CSV URL
  2. Trigger refresh
  3. Verify CSV parsed and table updated
- **Expected:** CSV data imported successfully

#### TC-CRON-007: REST API Refresh
- **Objective:** Verify REST API integration
- **Steps:**
  1. Create schedule with API endpoint
  2. Trigger refresh
  3. Verify JSON parsed and table updated
- **Expected:** API data imported successfully

### 3.2 WP-CLI Commands

#### TC-CLI-001: wp atables table list
- **Objective:** List all tables
- **Command:** `wp atables table list --format=json`
- **Expected:** JSON array of tables returned

#### TC-CLI-002: wp atables table create
- **Objective:** Create new table via CLI
- **Command:** `wp atables table create "CLI Test Table" --porcelain`
- **Expected:** Table ID returned

#### TC-CLI-003: wp atables table get
- **Objective:** Get table details
- **Command:** `wp atables table get {id} --format=json`
- **Expected:** Table details in JSON

#### TC-CLI-004: wp atables table update
- **Objective:** Update table via CLI
- **Command:** `wp atables table update {id} --title="Updated Title"`
- **Expected:** Table updated successfully

#### TC-CLI-005: wp atables table delete
- **Objective:** Delete table via CLI
- **Command:** `wp atables table delete {id} --yes`
- **Expected:** Table deleted

#### TC-CLI-006: wp atables table duplicate
- **Objective:** Duplicate table via CLI
- **Command:** `wp atables table duplicate {id} --title="Duplicated Table"`
- **Expected:** New table created as duplicate

#### TC-CLI-007: wp atables schedule list
- **Objective:** List all schedules
- **Command:** `wp atables schedule list --format=json`
- **Expected:** JSON array of schedules

#### TC-CLI-008: wp atables schedule run
- **Objective:** Trigger schedule via CLI
- **Command:** `wp atables schedule run {id} --verbose`
- **Expected:** Schedule executed, verbose output shown

#### TC-CLI-009: wp atables cache clear
- **Objective:** Clear all cache via CLI
- **Command:** `wp atables cache clear --yes`
- **Expected:** Cache cleared successfully

#### TC-CLI-010: wp atables cache warmup
- **Objective:** Warm up cache via CLI
- **Command:** `wp atables cache warmup --verbose`
- **Expected:** Cache warmed, progress shown

#### TC-CLI-011: wp atables export
- **Objective:** Export table to CSV
- **Command:** `wp atables export {id} --format=csv --file=/tmp/export.csv`
- **Expected:** CSV file created

#### TC-CLI-012: wp atables import
- **Objective:** Import CSV file
- **Command:** `wp atables import /tmp/export.csv --title="Imported Table"`
- **Expected:** New table created from CSV

### 3.3 Performance Monitor

#### TC-PERF-001: Start/Stop Timer
- **Objective:** Verify timer functionality
- **Steps:**
  1. Start timer for operation
  2. Perform operation
  3. Stop timer
  4. Verify metrics recorded
- **Expected:** Duration, memory, queries tracked

#### TC-PERF-002: Slow Operation Detection
- **Objective:** Verify slow operation detection
- **Steps:**
  1. Simulate slow operation (>threshold)
  2. Verify marked as slow
  3. Check logged as warning
- **Expected:** Slow operation detected and logged

#### TC-PERF-003: Statistics Generation
- **Objective:** Verify statistics accuracy
- **Steps:**
  1. Record multiple operations
  2. Get statistics
  3. Verify avg/min/max calculations
- **Expected:** Accurate statistics returned

#### TC-PERF-004: Recommendations
- **Objective:** Verify recommendation generation
- **Steps:**
  1. Create performance conditions (slow ops, high queries)
  2. Get recommendations
  3. Verify appropriate suggestions
- **Expected:** Relevant recommendations provided

#### TC-PERF-005: Clear Metrics
- **Objective:** Verify metrics clearing
- **Steps:**
  1. Record metrics
  2. Clear metrics
  3. Verify transient deleted
- **Expected:** All metrics cleared

---

## 4. Test Execution

### 4.1 Execution Order
1. Unit tests (automated)
2. WP-CLI tests (scripted)
3. Integration tests (scripted)
4. Manual UI tests (checklist)

### 4.2 Success Criteria
- All unit tests pass (100%)
- All CLI tests execute successfully (100%)
- All integration tests pass (>95%)
- No critical bugs found

---

## 5. Test Results Template

### 5.1 Result Format
```
Test ID: TC-XXX-NNN
Status: PASS/FAIL/SKIP
Execution Time: X.XX seconds
Notes: [Any relevant notes]
Issues: [Bug IDs if any]
```

### 5.2 Summary Metrics
- Total Tests: X
- Passed: X (XX%)
- Failed: X (XX%)
- Skipped: X (XX%)
- Code Coverage: XX%

---

## 6. Bug Reporting

### 6.1 Severity Levels
- **Critical:** Feature completely broken
- **High:** Major functionality affected
- **Medium:** Minor functionality affected
- **Low:** Cosmetic or edge case

### 6.2 Bug Template
```
Bug ID: BUG-XXX
Title: [Short description]
Severity: [Critical/High/Medium/Low]
Component: [Cron/CLI/Performance]
Steps to Reproduce:
1. [Step 1]
2. [Step 2]
Expected: [Expected behavior]
Actual: [Actual behavior]
```

---

## 7. Test Deliverables

1. Test plan document (this file)
2. Unit test suite
3. CLI test scripts
4. Integration test scripts
5. Test execution report
6. Bug report (if any)
7. Test coverage report

---

## 8. Schedule

- Test Planning: Completed
- Test Development: In Progress
- Test Execution: Pending
- Results Analysis: Pending
- Report Generation: Pending

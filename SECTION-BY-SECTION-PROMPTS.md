# 🔍 Individual Section Verification Prompts

**Purpose:** Check and debug each feature section ONE AT A TIME

---

## 📋 HOW TO USE THESE PROMPTS

1. Copy the prompt for the section you want to check
2. Paste it into a new conversation with me (Claude)
3. I will examine the code and report findings
4. We'll fix any issues found before moving to the next section
5. Mark sections as complete as you go

---

## SECTION 1.1: CREATE TABLE (7 Methods)

```
Please verify the "Create Table" functionality by examining the plugin code.

SECTION: 1.1 Create Table (7 Methods)

CLAIMED FEATURES:
- ✅ CSV Import - Upload CSV files with custom delimiters
- ✅ JSON Import - Import JSON data with nested support
- ✅ Excel Import (.xlsx, .xls) - Multi-sheet support
- ✅ XML Import - Parse XML with node selection
- ✅ Manual Creation - Build tables from scratch
- ✅ MySQL Query - Visual query builder with test mode
- ✅ Google Sheets - Import from public sheets

FILES TO CHECK:
- src/modules/core/views/create-table.php (main creation page)
- src/modules/dataSources/controllers/ImportController.php
- src/modules/import/controllers/ImportController.php
- src/modules/database/MySQLQueryController.php
- src/modules/googlesheets/GoogleSheetsController.php
- src/modules/core/views/manual-table.php

VERIFICATION CHECKLIST:
For each import method, verify:
1. ✓ Menu option exists in create-table.php
2. ✓ Route/handler exists
3. ✓ View file exists
4. ✓ Parser/service file exists
5. ✓ AJAX endpoint registered
6. ✓ JavaScript handler exists
7. ✓ Form validation works
8. ✓ Error handling works

QUESTIONS TO ANSWER:
1. Are all 7 creation methods accessible from the UI?
2. Which methods are fully implemented?
3. Which methods are partially implemented?
4. Which methods are missing?
5. Are there any bugs in the implemented methods?

DELIVERABLE:
Provide a status report with:
- ✅ WORKING - Feature fully implemented and tested
- ⚠️ PARTIAL - Feature exists but has issues
- ❌ MISSING - Feature not implemented
- 🐛 BUGGY - Feature implemented but broken

Then list any bugs found and suggest fixes.
```

---

## SECTION 1.2: VIEW TABLES DASHBOARD

```
Please verify the "View Tables Dashboard" functionality by examining the plugin code.

SECTION: 1.2 View Tables Dashboard

CLAIMED FEATURES:
- ✅ Dashboard/list page with grid view
- ✅ Search tables by name
- ✅ Filter by source type (CSV, Excel, Manual, etc.)
- ✅ Sort by name, date, rows, columns
- ✅ Pagination for large lists
- ✅ Quick actions menu (view, edit, delete, duplicate)
- ✅ Bulk selection with checkboxes
- ✅ Bulk delete operation
- ✅ Table statistics (row/column count, size, last modified)
- ✅ Copy shortcode button

FILES TO CHECK:
- src/modules/core/views/dashboard.php
- src/modules/tables/controllers/TableController.php
- assets/js/admin-main.js
- assets/js/admin-table-filters.js (if exists)
- assets/css/admin/*.css

VERIFICATION CHECKLIST:
1. ✓ Dashboard view renders list of tables
2. ✓ Search box exists in HTML
3. ✓ Filter dropdown exists for source_type
4. ✓ Sort options exist (name, date, rows, cols)
5. ✓ Pagination controls exist
6. ✓ Quick actions menu on each table
7. ✓ Bulk select checkboxes exist
8. ✓ Bulk delete button exists
9. ✓ Table stats display (rows, cols, date)
10. ✓ Copy shortcode button exists

SPECIFIC CHECKS:
1. Open dashboard.php - does it have a search input field?
2. Open dashboard.php - does it have a filter dropdown for source_type?
3. Open admin-main.js - is there a search event handler?
4. Open admin-main.js - is there a filter event handler?
5. Check TableController - is there a search/filter method?

KNOWN ISSUE (from user):
- Search tables by name: MISSING from frontend
- Filter by source type: MISSING from frontend

DELIVERABLE:
1. Status report for each feature (✅/⚠️/❌/🐛)
2. List of missing features with exact locations to add them
3. Code snippets to implement missing features
4. Step-by-step implementation guide

Please check dashboard.php first and report what you find.
```

---

## SECTION 1.3: VIEW SINGLE TABLE (Admin)

```
Please verify the "View Single Table" functionality by examining the plugin code.

SECTION: 1.3 View Single Table (Admin)

CLAIMED FEATURES:
- ✅ Server-side pagination - Handle millions of rows
- ✅ Server-side search - Fast search across all data
- ✅ Server-side sorting - Sort by any column
- ✅ Rows per page selector (10/25/50/100/All)
- ✅ Column visibility toggle
- ✅ Export buttons (CSV, PDF)
- ✅ Edit table button
- ✅ Delete table button
- ✅ Copy shortcode with one click
- ✅ Table metadata display
- ✅ Advanced filter panel integration
- ✅ Real-time row count
- ✅ Navigation breadcrumbs

FILES TO CHECK:
- src/modules/core/views/view-table.php
- src/modules/core/TableViewAjaxController.php
- assets/js/admin-table-view.js
- src/modules/tables/repositories/TableRepository.php

VERIFICATION CHECKLIST:
1. ✓ View renders table data
2. ✓ DataTables.js initialized with serverSide: true
3. ✓ AJAX endpoint for data loading
4. ✓ Search box functional
5. ✓ Sort by clicking columns
6. ✓ Pagination controls functional
7. ✓ Rows per page dropdown
8. ✓ Export CSV button
9. ✓ Export PDF button
10. ✓ Filter panel accessible

SPECIFIC CHECKS:
1. Look for wp_ajax_atables_get_table_data action
2. Check if TableViewAjaxController handles pagination params
3. Verify search parameter is processed
4. Verify orderBy/orderDir parameters are processed
5. Check if queries use LIMIT and OFFSET
6. Test with large dataset (1000+ rows)

PERFORMANCE TEST:
- Create a test table with 5000+ rows
- Verify page loads quickly (<2 seconds)
- Verify search is instant
- Verify sorting is instant
- Verify pagination is instant

DELIVERABLE:
1. Status report for each feature
2. Performance test results
3. Any bottlenecks found
4. Optimization suggestions
```

---

## SECTION 1.4: EDIT TABLE (8 Tabs)

```
Please verify the "Edit Table Enhanced Interface" functionality by examining the plugin code.

SECTION: 1.4 Edit Table (Enhanced Tabbed Interface)

CLAIMED FEATURES:
8 Specialized Tabs:
- ✅ Tab 1: Basic Info (name, description, metadata)
- ✅ Tab 2: Data (inline editing, add/remove rows/cols)
- ✅ Tab 3: Display (themes, responsive modes, features)
- ✅ Tab 4: Conditional Formatting (visual rule builder)
- ✅ Tab 5: Formulas (13 functions, presets)
- ✅ Tab 6: Validation (data quality rules)
- ✅ Tab 7: Merging (cell merge configuration)
- ✅ Tab 8: Advanced (JSON editor, danger zone)

FILES TO CHECK:
- src/modules/core/views/edit-table-enhanced.php (main container)
- src/modules/core/views/tabs/basic-info-tab.php
- src/modules/core/views/tabs/data-tab.php
- src/modules/core/views/tabs/display-tab.php
- src/modules/core/views/tabs/conditional-tab.php
- src/modules/core/views/tabs/formulas-tab.php
- src/modules/core/views/tabs/validation-tab.php
- src/modules/core/views/tabs/merging-tab.php
- src/modules/core/views/tabs/advanced-tab.php
- assets/js/admin-edit-tabs.js

VERIFICATION CHECKLIST:
For EACH tab:
1. ✓ Tab file exists
2. ✓ Tab is rendered in edit-table-enhanced.php
3. ✓ Tab navigation works (click to switch)
4. ✓ Tab content loads correctly
5. ✓ Form fields are functional
6. ✓ JavaScript handlers exist
7. ✓ Save functionality works
8. ✓ Validation works

TAB-BY-TAB CHECKS:
1. Basic Info Tab:
   - Text inputs for name/description
   - Readonly fields for ID, source_type
   - Date display for created/updated
   
2. Data Tab:
   - Inline cell editing (click to edit)
   - Add row button
   - Delete row button
   - Add column button
   - Delete column button
   - Find & replace modal
   
3. Display Tab:
   - Template gallery (8 templates)
   - Theme selector (6 themes)
   - Responsive mode selector (3 modes)
   - Feature toggles (search, sort, pagination)
   - Rows per page input
   
4. Conditional Tab:
   - Add rule button
   - Rule list display
   - Edit rule modal
   - Color pickers
   - Operator dropdown (15+ operators)
   - Preview cell
   
5. Formulas Tab:
   - Formula input field
   - Function list (13 functions)
   - Formula presets (10+)
   - Cell reference picker
   - Error display
   
6. Validation Tab:
   - Add validation button
   - Rule type checkboxes
   - Min/max inputs
   - Type dropdown
   - Preset buttons
   
7. Merging Tab:
   - Add merge button
   - Merge list
   - Start position inputs
   - Span inputs
   - Delete merge button
   
8. Advanced Tab:
   - JSON editor textarea
   - Format JSON button
   - Validate JSON button
   - Apply JSON button
   - Export settings button
   - Import settings button
   - Reset button
   - Clear cache button
   - Delete table button

DELIVERABLE:
1. Status report for each tab (✅/⚠️/❌/🐛)
2. List of broken features per tab
3. Missing JavaScript handlers
4. Broken AJAX endpoints
5. Fix recommendations with code snippets
```

---

## SECTION 2.1: CSV IMPORT

```
Please verify the "CSV Import" functionality by examining the plugin code.

SECTION: 2.1 CSV Import (Most Advanced)

CLAIMED FEATURES:
**File Upload:**
- ✅ Drag & drop interface
- ✅ File picker browse
- ✅ File size validation (1-100MB)
- ✅ MIME type verification
- ✅ Progress bar

**Parsing Options:**
- ✅ 10+ Delimiters (comma, semicolon, tab, pipe, etc.)
- ✅ Enclosure character (" or ')
- ✅ Escape character (\ or ")
- ✅ 3 Encodings (UTF-8, ISO-8859-1, Windows-1252)
- ✅ Auto-encoding detection
- ✅ BOM handling

**Import Configuration:**
- ✅ Header row detection (auto/manual)
- ✅ Skip rows option
- ✅ Column mapping interface
- ✅ Data type auto-detection
- ✅ Preview first 10-50 rows
- ✅ Error highlighting
- ✅ Malformed CSV recovery

**Data Processing:**
- ✅ Empty row handling
- ✅ Trim whitespace
- ✅ Remove empty columns
- ✅ Date format conversion
- ✅ Number format conversion
- ✅ Boolean conversion

FILES TO CHECK:
- src/modules/dataSources/parsers/CsvParser.php
- src/modules/dataSources/services/ImportService.php
- src/modules/dataSources/controllers/ImportController.php
- assets/js/admin-import-csv.js (if exists)
- View file for CSV import form

VERIFICATION CHECKLIST:
1. ✓ File upload form exists
2. ✓ Drag & drop zone exists
3. ✓ File validation checks size
4. ✓ File validation checks MIME type
5. ✓ Delimiter dropdown with 10+ options
6. ✓ Encoding dropdown with 3 options
7. ✓ Header row checkbox
8. ✓ Skip rows input field
9. ✓ Data preview table
10. ✓ Column mapping interface
11. ✓ Import button
12. ✓ Progress indicator

SPECIFIC CHECKS IN CsvParser.php:
1. Does parse() method exist?
2. Does it accept delimiter parameter?
3. Does it accept enclosure parameter?
4. Does it accept escape parameter?
5. Does it detect encoding?
6. Does it handle BOM?
7. Does it validate CSV structure?
8. Does it return structured data?

TEST SCENARIOS:
1. Import CSV with comma delimiter
2. Import CSV with semicolon delimiter
3. Import CSV with tab delimiter
4. Import CSV with UTF-8 encoding
5. Import CSV with ISO-8859-1 encoding
6. Import CSV with header row
7. Import CSV without header row
8. Import CSV with empty rows
9. Import CSV with malformed data
10. Import large CSV (10,000+ rows)

DELIVERABLE:
1. Feature status report
2. List of supported delimiters (actual vs claimed)
3. List of supported encodings (actual vs claimed)
4. Test results for each scenario
5. Bugs found
6. Missing features to implement
```

---

## SECTION 2.3: EXCEL IMPORT

```
Please verify the "Excel Import" functionality by examining the plugin code.

SECTION: 2.3 Excel Import (PhpSpreadsheet)

CLAIMED FEATURES:
**File Support:**
- ✅ .xlsx (Office 2007+)
- ✅ .xls (Office 97-2003)
- ✅ .xlsm (macro-enabled)
- ✅ .csv (as Excel format)

**Sheet Management:**
- ✅ Multi-sheet workbooks
- ✅ Sheet selection dropdown
- ✅ Sheet preview
- ✅ Import single sheet
- ✅ Import multiple sheets
- ✅ Sheet name preservation

**Data Handling:**
- ✅ Cell formatting preservation
- ✅ Number formats (currency, %, decimal)
- ✅ Date/time formats
- ✅ Formula detection
- ✅ Formula to value conversion
- ✅ Merged cell handling
- ✅ Empty cell handling
- ✅ Hidden row/column detection

FILES TO CHECK:
- src/modules/import/parsers/ExcelParser.php
- src/modules/import/services/ExcelImportService.php
- composer.json (verify PhpSpreadsheet dependency)
- vendor/phpoffice/phpspreadsheet/ (verify library installed)

VERIFICATION CHECKLIST:
1. ✓ PhpSpreadsheet is in composer.json
2. ✓ PhpSpreadsheet is installed in vendor/
3. ✓ ExcelParser.php exists
4. ✓ ExcelParser uses PhpSpreadsheet\IOFactory
5. ✓ Parser detects file format (.xlsx, .xls)
6. ✓ Parser lists available sheets
7. ✓ Parser can read specific sheet
8. ✓ Parser handles merged cells
9. ✓ Parser converts formulas to values
10. ✓ Parser preserves date formats

SPECIFIC CHECKS IN ExcelParser.php:
Look for these methods:
- parse($file, $options)
- getWorksheets($file)
- readSheet($file, $sheetName)
- getCellValue($cell, $formatted = true)
- handleMergedCells($sheet)
- detectHeaderRow($sheet)

TEST SCENARIOS:
1. Import .xlsx file (1 sheet)
2. Import .xlsx file (multiple sheets)
3. Import .xls file (legacy format)
4. Import file with formulas
5. Import file with merged cells
6. Import file with date columns
7. Import file with currency columns
8. Import file with hidden columns
9. Import large Excel file (5000+ rows)

DELIVERABLE:
1. Feature status report
2. PhpSpreadsheet version installed
3. File format support (actual vs claimed)
4. Test results for each scenario
5. Issues with merged cells
6. Issues with formula conversion
7. Missing features to implement
```

---

## SECTION 3.2: PDF EXPORT

```
Please verify the "PDF Export" functionality by examining the plugin code.

SECTION: 3.2 PDF Export (Professional TCPDF Integration)

CLAIMED FEATURES:
**Page Configuration:**
- ✅ Orientation: Auto-detect, Portrait, Landscape
- ✅ Auto-detect logic: Landscape if >6 columns
- ✅ Page size: A4, Letter, Legal, A3
- ✅ Margins: Top, bottom, left, right

**Styling:**
- ✅ Font size: 6-14 points (default 9pt)
- ✅ Font family: DejaVu Sans (UTF-8 support)
- ✅ Header: WordPress branding with logo
- ✅ Footer: Page numbers, table name, date
- ✅ Table styling: Professional borders
- ✅ Zebra striping: Alternating row colors
- ✅ Column width optimization: Auto-fit

**Features:**
- ✅ UTF-8 support: International chars, emojis
- ✅ Automatic page breaks: Smart splitting
- ✅ Header row repeat: On every page
- ✅ Long text wrapping: Multi-line cells
- ✅ Cell alignment: Left, center, right
- ✅ Number formatting: Preserved

**Limitations:**
- ✅ Max rows: 100-10,000 (default 5,000)
- ✅ Timeout: 60 seconds
- ✅ Memory: Adaptive based on RAM
- ✅ Large dataset warning

FILES TO CHECK:
- src/modules/export/services/PdfExportService.php
- src/modules/export/exporters/PdfExporter.php
- composer.json (verify TCPDF dependency)
- vendor/tecnickcom/tcpdf/ (verify library installed)

VERIFICATION CHECKLIST:
1. ✓ TCPDF in composer.json
2. ✓ TCPDF installed in vendor/
3. ✓ PdfExportService exists
4. ✓ Auto-detect orientation (>6 cols = landscape)
5. ✓ Page size configuration
6. ✓ Margin configuration
7. ✓ Font size configuration
8. ✓ UTF-8 character support
9. ✓ Header with logo
10. ✓ Footer with page numbers
11. ✓ Zebra striping
12. ✓ Column auto-width
13. ✓ Row limit enforcement
14. ✓ Timeout handling
15. ✓ Memory limit check

SPECIFIC CHECKS IN PdfExportService.php:
Look for these methods:
- export($tableId, $options)
- detectOrientation($columnCount)
- configurePageSetup($pdf, $options)
- addHeader($pdf)
- addFooter($pdf)
- addTableData($pdf, $data, $headers)
- applyZebraStriping($pdf, $rowIndex)
- calculateColumnWidths($headers, $data)
- handlePageBreak($pdf)
- enforceRowLimit($data, $maxRows)

TEST SCENARIOS:
1. Export table with 3 columns (portrait)
2. Export table with 10 columns (landscape)
3. Export table with UTF-8 chars (émojis, ñ, ü)
4. Export table with long text (wrapping)
5. Export table with 1000 rows (pagination)
6. Export table with 6000 rows (near limit)
7. Export filtered data
8. Export sorted data
9. Export with custom margins
10. Export with custom font size

DELIVERABLE:
1. Feature status report
2. TCPDF version installed
3. Test results for each scenario
4. UTF-8 support verification
5. Performance test results (time to generate)
6. Memory usage for large exports
7. Issues found
8. Missing features to implement
```

---

## SECTION 5.1: FILTER OPERATORS (19 Total)

```
Please verify the "Filter Operators" functionality by examining the plugin code.

SECTION: 5.1 Filter Operators (19 Total!)

CLAIMED FEATURES:
**Comparison Operators:**
1. ✅ equals (=)
2. ✅ not_equals (!=)
3. ✅ greater_than (>)
4. ✅ greater_than_or_equal (>=)
5. ✅ less_than (<)
6. ✅ less_than_or_equal (<=)

**Text Operators:**
7. ✅ contains
8. ✅ not_contains
9. ✅ starts_with
10. ✅ ends_with

**Range Operators:**
11. ✅ between
12. ✅ not_between

**List Operators:**
13. ✅ in
14. ✅ not_in

**Empty Operators:**
15. ✅ is_empty
16. ✅ is_not_empty

**Advanced Operators:**
17. ✅ regex
18. ✅ date_equals
19. ✅ date_range

FILES TO CHECK:
- src/modules/filters/types/FilterOperator.php
- src/modules/filters/services/FilterService.php
- src/modules/filters/controllers/FilterController.php

VERIFICATION CHECKLIST:
For EACH operator:
1. ✓ Operator constant/enum exists
2. ✓ Operator has a label
3. ✓ Operator has an apply() method
4. ✓ Operator validation works
5. ✓ Operator handles edge cases
6. ✓ Operator works with different data types

SPECIFIC CHECKS IN FilterOperator.php:
1. Open the file
2. Count the operators defined
3. List operator names and their implementations
4. Check if each operator has:
   - Constant definition
   - Label for UI display
   - Implementation method
   - Validation logic

SPECIFIC CHECKS IN FilterService.php:
1. Does applyFilter() method exist?
2. Does it support all 19 operators?
3. Does it handle SQL generation correctly?
4. Does it prevent SQL injection?
5. Does it handle data type conversion?

TEST SCENARIOS:
For each operator, test:
1. Filter with text column
2. Filter with number column
3. Filter with date column
4. Filter with NULL values
5. Filter with empty strings
6. Filter with special characters
7. Filter with multiple conditions (AND)

DELIVERABLE:
1. List of operators actually implemented (count them)
2. Status report for each operator (✅/❌)
3. Missing operators to implement
4. Operators that are buggy
5. SQL injection vulnerabilities found
6. Test results for each operator
7. Code snippets to implement missing operators
```

---

## SECTION 6.1: FORMULA FUNCTIONS (13 Total)

```
Please verify the "Formula Functions" functionality by examining the plugin code.

SECTION: 6.1 Formula Functions (13 Total!)

CLAIMED FEATURES:
**Mathematical Functions:**
1. ✅ SUM(range)
2. ✅ AVERAGE / AVG(range)
3. ✅ MEDIAN(range)
4. ✅ MIN(range)
5. ✅ MAX(range)
6. ✅ COUNT(range)
7. ✅ PRODUCT(range)
8. ✅ POWER / POW(base, exponent)
9. ✅ SQRT(number)
10. ✅ ROUND(number, decimals)
11. ✅ ABS(number)

**Text Functions:**
12. ✅ CONCAT / CONCATENATE(text1, text2, ...)

**Logical Functions:**
13. ✅ IF(condition, value_if_true, value_if_false)

FILES TO CHECK:
- src/modules/formulas/FormulaService.php
- src/modules/formulas/FormulaController.php

VERIFICATION CHECKLIST:
For EACH function:
1. ✓ Function name registered
2. ✓ Function has implementation
3. ✓ Function validates parameters
4. ✓ Function handles errors
5. ✓ Function supports cell references (A1, B2)
6. ✓ Function supports ranges (A1:A10)
7. ✓ Function supports column references (A:A)

SPECIFIC CHECKS IN FormulaService.php:
1. Open the file
2. Count the functions defined
3. List function names
4. Check if each function has:
   - Registration in function map
   - Implementation method
   - Parameter validation
   - Error handling
5. Check for:
   - Cell reference parser (A1, B2, etc.)
   - Range parser (A1:A10, B:B, etc.)
   - Circular reference detection
   - Dependency tracking

FORMULA SYNTAX CHECKS:
1. Does it parse =SUM(A1:A10)?
2. Does it parse =AVERAGE(B:B)?
3. Does it parse =IF(A1>100, "High", "Low")?
4. Does it parse =SUM(A1:A10) + AVERAGE(B1:B10)?
5. Does it handle nested functions: =ROUND(AVERAGE(A1:A10), 2)?
6. Does it handle arithmetic: =(A1 + B1) * C1?

TEST SCENARIOS:
For each function, test:
1. Valid formula
2. Invalid formula (syntax error)
3. Cell reference (A1)
4. Range reference (A1:A10)
5. Column reference (A:A)
6. Empty cells in range
7. Non-numeric values
8. Division by zero
9. Circular references
10. Large datasets (1000+ rows)

DELIVERABLE:
1. List of functions actually implemented (count them)
2. Status report for each function (✅/❌)
3. Missing functions to implement
4. Functions that are buggy
5. Parser issues found
6. Test results for each function
7. Code snippets to implement missing functions
8. Formula syntax documentation
```

---

## SECTION 7.5: CONDITIONAL FORMATTING

```
Please verify the "Conditional Formatting" functionality by examining the plugin code.

SECTION: 7.5 Conditional Formatting

CLAIMED FEATURES:
**Rule Types:**
- ✅ Cell value rules
- ✅ Column rules
- ✅ Row rules (future)

**Condition Operators (15+):**
- ✅ Greater than
- ✅ Less than
- ✅ Equals
- ✅ Not equals
- ✅ Between
- ✅ Contains text
- ✅ Empty
- ✅ Not empty
- ✅ Starts with
- ✅ Ends with
- ✅ Is number
- ✅ Is text
- ✅ Is date
- ✅ Regex match
- ✅ Custom formula (future)

**Visual Styling:**
- ✅ Background color: Full spectrum picker
- ✅ Text color: Full spectrum picker
- ✅ Font weight: normal, bold
- ✅ Font style: normal, italic
- ✅ Border (future)
- ✅ Icon (future)

**Rule Management:**
- ✅ Visual rule builder with live preview
- ✅ Add unlimited rules per column
- ✅ Rule priority (first match wins)
- ✅ Reorder rules (future)
- ✅ Enable/disable rules
- ✅ Delete rules
- ✅ Duplicate rules
- ✅ Rule templates

**Presets:**
- ✅ Traffic lights (red/yellow/green)
- ✅ Heat map
- ✅ Above/below average
- ✅ Top 10
- ✅ Bottom 10
- ✅ Positive/negative
- ✅ Status indicators

FILES TO CHECK:
- src/modules/conditional/ConditionalFormattingService.php
- src/modules/core/views/tabs/conditional-tab.php
- assets/js/admin-conditional-formatting.js

VERIFICATION CHECKLIST:
1. ✓ ConditionalFormattingService exists
2. ✓ Conditional tab exists in edit interface
3. ✓ Add rule button exists
4. ✓ Rule list displays existing rules
5. ✓ Edit rule modal exists
6. ✓ Column dropdown populated
7. ✓ Operator dropdown has 15+ options
8. ✓ Value input field exists
9. ✓ Color pickers exist (bg, text)
10. ✓ Font weight dropdown exists
11. ✓ Live preview cell exists
12. ✓ Save rule functionality works
13. ✓ Delete rule functionality works
14. ✓ Rules apply to table display
15. ✓ Preset buttons exist

SPECIFIC CHECKS IN ConditionalFormattingService.php:
Look for methods:
- get_available_operators() - returns 15+ operators
- get_presets() - returns preset configurations
- apply_formatting($rules, $data) - applies rules to data
- evaluate_condition($value, $operator, $compareValue)
- validate_rule($rule)

SPECIFIC CHECKS IN conditional-tab.php:
Look for HTML elements:
- #atables-add-cf-rule button
- .atables-rule-item containers
- #atables-cf-modal modal
- #cf-column dropdown
- #cf-operator dropdown with all operators
- #cf-value input
- #cf-bg-color color picker
- #cf-text-color color picker
- #cf-font-weight dropdown
- #cf-preview preview cell

TEST SCENARIOS:
1. Add rule: value > 100, background red
2. Add rule: value contains "active", background green
3. Add rule: value empty, background gray
4. Add multiple rules to same column (test priority)
5. Test each operator with appropriate data
6. Test color picker selection
7. Test preview updates in real-time
8. Test rule save/load
9. Test rule delete
10. Test preset application (traffic lights)
11. Verify rules apply on frontend

DELIVERABLE:
1. Count of operators implemented (target: 15+)
2. Status report for each operator
3. List of presets available
4. Rule builder UI status
5. Color picker functionality
6. Live preview functionality
7. Frontend application status
8. Missing features
9. Bugs found
10. Implementation guide for missing features
```

---

## SECTION 8.1: CHART TYPES (8 Types)

```
Please verify the "Chart Types" functionality by examining the plugin code.

SECTION: 8.1 Chart Types (8 Types)

CLAIMED FEATURES:
1. ✅ Line Chart - Trends over time
2. ✅ Bar Chart - Horizontal bars
3. ✅ Column Chart - Vertical bars
4. ✅ Pie Chart - Parts of whole
5. ✅ Doughnut Chart - Like pie with center hole
6. ✅ Area Chart - Filled line chart
7. ✅ Scatter Chart - XY plot
8. ✅ Radar Chart - Multi-variable (future)

FILES TO CHECK:
- src/modules/charts/services/ChartService.php
- src/modules/charts/renderers/GoogleChartsRenderer.php (if exists)
- src/modules/frontend/renderers/ChartRenderer.php
- src/modules/frontend/shortcodes/ChartShortcode.php
- assets/js/public-charts.js

VERIFICATION CHECKLIST:
For EACH chart type:
1. ✓ Chart type constant exists
2. ✓ Chart type in creation dropdown
3. ✓ Chart rendering method exists
4. ✓ Chart.js configuration for type
5. ✓ Google Charts configuration for type
6. ✓ Data format validation for type
7. ✓ Frontend rendering works
8. ✓ Chart is responsive

SPECIFIC CHECKS IN ChartService.php:
1. Open the file
2. Count chart types defined
3. List chart type names
4. Check methods:
   - get_chart_types() - returns all types
   - validate_chart_type($type)
   - get_chart_config($type, $data, $options)
   - render_chart($chartId, $library)

SPECIFIC CHECKS IN public-charts.js:
1. Does it initialize Chart.js?
2. Does it initialize Google Charts?
3. Does it handle each chart type?
4. Does it handle responsive sizing?
5. Does it handle data updates?

TEST SCENARIOS:
For each chart type, test:
1. Create chart via wizard
2. Configure chart options
3. Preview chart in admin
4. Save chart
5. Display chart via shortcode [achart id="X"]
6. Test responsive behavior
7. Test with small dataset (10 rows)
8. Test with large dataset (1000 rows)
9. Test with empty data
10. Test with invalid data

DELIVERABLE:
1. Count of chart types implemented (target: 8)
2. Status report for each chart type (✅/❌/🐛)
3. Chart.js integration status
4. Google Charts integration status
5. Shortcode rendering status
6. Responsive behavior status
7. Test results for each chart type
8. Screenshots of each chart type
9. Missing chart types to implement
10. Bugs found in existing charts
```

---

## 🎯 QUICK START GUIDE

**Recommended Testing Order:**

1. **Start Here (Most Critical):**
   - Section 1.2: View Tables Dashboard ← YOU FOUND ISSUES HERE
   - Section 1.4: Edit Table (8 Tabs)
   - Section 1.3: View Single Table

2. **Then Test Core Features:**
   - Section 2.1: CSV Import
   - Section 3.2: PDF Export
   - Section 5.1: Filter Operators
   - Section 6.1: Formula Functions

3. **Then Test Advanced:**
   - Section 7.5: Conditional Formatting
   - Section 8.1: Chart Types

4. **Finally Test Remaining:**
   - All other sections in order

---

## 📝 TEMPLATE FOR NEW SECTIONS

If you need a prompt for a section not listed above, use this template:

```
Please verify the "[SECTION NAME]" functionality by examining the plugin code.

SECTION: [Section Number]: [Section Name]

CLAIMED FEATURES:
[Paste the bullet points from COMPLETE-FUNCTIONALITY-LIST.md]

FILES TO CHECK:
[List expected file locations]

VERIFICATION CHECKLIST:
[List what to check]

SPECIFIC CHECKS:
[Detailed inspection steps]

TEST SCENARIOS:
[How to test each feature]

DELIVERABLE:
1. Feature status report (✅/⚠️/❌/🐛)
2. Missing features list
3. Bugs found
4. Implementation guide for missing features
```

---

**USAGE INSTRUCTIONS:**

1. Pick a section from above
2. Copy the entire prompt for that section
3. Paste it in a new message to Claude
4. Wait for detailed analysis
5. Implement fixes based on findings
6. Mark section as complete
7. Move to next section

Each prompt is self-contained and can be used independently!

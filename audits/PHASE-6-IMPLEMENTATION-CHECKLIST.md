# ✅ PHASE 6 - FIX IMPLEMENTATION CHECKLIST
## Charts & Visualization System

**Last Updated:** October 31, 2025  
**Status:** Not Started  
**Progress:** 0/15 bugs fixed (0%)

---

## 🎯 WEEK 1: CRITICAL FIXES (P0)

### ⏱️ Day 1: Quick Wins (3.5 hours)

#### ✅ Task 1.1: Fix Chart Type Validation Bug (30 minutes)
**Bug:** BUG-001  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Sub-tasks:**
- [ ] Open `src/modules/charts/types/Chart.php`
- [ ] Find line 150 with `$allowed_types` array
- [ ] Change from: `array( 'bar', 'line', 'pie', 'doughnut' )`
- [ ] Change to: `array( 'bar', 'line', 'pie', 'doughnut', 'column', 'area', 'scatter' )`
- [ ] Save file

**Testing:**
- [ ] Try to create column chart → Should succeed
- [ ] Try to create area chart → Should succeed
- [ ] Try to create scatter chart → Should succeed (will fail render, that's OK for now)
- [ ] Verify validation accepts all 7 types

**Files Changed:**
- [ ] `src/modules/charts/types/Chart.php` (1 line)

**Estimated Time:** 30 minutes  
**Actual Time:** _____ minutes

---

#### ✅ Task 1.2: Add Row Limit to Data Loading (3 hours)
**Bug:** BUG-003  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Sub-tasks:**

**Part A: Update ChartService (1.5 hours)**
- [ ] Open `src/modules/charts/services/ChartService.php`
- [ ] Find `get_chart_data()` method (around line 193)
- [ ] Add `$max_rows = 1000` parameter
- [ ] Update `get_table_data()` call to pass limit
- [ ] Add truncation detection logic
- [ ] Add metadata to return array (total_rows, displayed_rows, was_truncated)
- [ ] Save file

**Part B: Update TableRepository (1 hour)**
- [ ] Open `src/modules/tables/repositories/TableRepository.php`
- [ ] Find `get_table_data()` method
- [ ] Add `$args` parameter with defaults
- [ ] Add LIMIT and OFFSET to SQL query
- [ ] Test query syntax
- [ ] Save file

**Part C: Add Warning Message (30 minutes)**
- [ ] Open `src/modules/frontend/renderers/ChartRenderer.php`
- [ ] Find render method
- [ ] Add truncation warning HTML before chart
- [ ] Style warning with CSS
- [ ] Save file

**Testing:**
- [ ] Create chart from 50-row table → No warning, all data shown
- [ ] Create chart from 500-row table → No warning, all data shown
- [ ] Create chart from 2000-row table → Warning shown, 1000 rows displayed
- [ ] Create chart from 10,000-row table → Warning shown, 1000 rows displayed
- [ ] Check memory usage stays reasonable
- [ ] Check page doesn't freeze

**Files Changed:**
- [ ] `src/modules/charts/services/ChartService.php` (~15 lines)
- [ ] `src/modules/tables/repositories/TableRepository.php` (~10 lines)
- [ ] `src/modules/frontend/renderers/ChartRenderer.php` (~8 lines)

**Estimated Time:** 3 hours  
**Actual Time:** _____ hours

---

### ⏱️ Day 2-3: Edit Chart Interface (16 hours)

#### ✅ Task 1.3: Create Edit Chart View (8 hours)
**Bug:** BUG-002 Part 1  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Sub-tasks:**
- [ ] Create new file: `src/modules/core/views/edit-chart.php`
- [ ] Copy structure from create-chart.php
- [ ] Add chart ID retrieval from URL parameter
- [ ] Load existing chart data via ChartService
- [ ] Populate form fields with existing values:
  - [ ] Title input
  - [ ] Type dropdown (with selected value)
  - [ ] Label column dropdown (with selected value)
  - [ ] Data columns checkboxes (with checked values)
  - [ ] Status dropdown (new field)
- [ ] Add preview canvas
- [ ] Add Chart.js script loading
- [ ] Add preview generation JavaScript
- [ ] Add form submission JavaScript
- [ ] Add cancel button linking back to charts list
- [ ] Test form population with sample chart

**Files Changed:**
- [ ] `src/modules/core/views/edit-chart.php` (NEW FILE, ~250 lines)

**Estimated Time:** 8 hours  
**Actual Time:** _____ hours

---

#### ✅ Task 1.4: Update ChartController (2 hours)
**Bug:** BUG-002 Part 2  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Sub-tasks:**
- [ ] Open `src/modules/charts/controllers/ChartController.php`
- [ ] Find `handle_update_chart()` method (around line 150)
- [ ] Add config parameter handling:
  - [ ] Check if `$_POST['config']` exists
  - [ ] Sanitize label_column
  - [ ] Sanitize data_columns array
  - [ ] Merge into $data array
- [ ] Test AJAX endpoint with Postman/curl
- [ ] Verify config updates in database

**Testing:**
- [ ] Call update endpoint with config → Saves correctly
- [ ] Call update endpoint without config → Doesn't break
- [ ] Verify config JSON in database table

**Files Changed:**
- [ ] `src/modules/charts/controllers/ChartController.php` (~20 lines added)

**Estimated Time:** 2 hours  
**Actual Time:** _____ hours

---

#### ✅ Task 1.5: Add Edit Button to Charts List (1 hour)
**Bug:** BUG-002 Part 3  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Sub-tasks:**
- [ ] Open `src/modules/core/views/charts.php`
- [ ] Find chart actions section (around line 55)
- [ ] Add Edit button before Delete button:
  ```php
  <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-edit-chart&chart_id=' . $chart->id ) ); ?>" 
     class="button button-small">
      <?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
  </a>
  ```
- [ ] Save file
- [ ] Test button appears for each chart
- [ ] Test button links to correct chart ID

**Files Changed:**
- [ ] `src/modules/core/views/charts.php` (~5 lines)

**Estimated Time:** 1 hour  
**Actual Time:** _____ hours

---

#### ✅ Task 1.6: Register Edit Page Route (1 hour)
**Bug:** BUG-002 Part 4  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Sub-tasks:**
- [ ] Find where admin pages are registered (likely main plugin file or admin class)
- [ ] Add submenu page registration:
  ```php
  add_submenu_page(
      null, // Hidden from menu
      __( 'Edit Chart', 'a-tables-charts' ),
      __( 'Edit Chart', 'a-tables-charts' ),
      'manage_options',
      'a-tables-charts-edit-chart',
      array( $this, 'render_edit_chart_page' )
  );
  ```
- [ ] Create render method:
  ```php
  public function render_edit_chart_page() {
      require_once ATABLES_PLUGIN_DIR . 'src/modules/core/views/edit-chart.php';
  }
  ```
- [ ] Test page loads at correct URL
- [ ] Test without chart_id parameter → Shows error
- [ ] Test with invalid chart_id → Shows error
- [ ] Test with valid chart_id → Shows edit form

**Files Changed:**
- [ ] Main admin class file (~15 lines)

**Estimated Time:** 1 hour  
**Actual Time:** _____ hours

---

#### ✅ Task 1.7: End-to-End Edit Testing (4 hours)
**Bug:** BUG-002 Testing  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Test Scenarios:**

**Scenario 1: Edit Chart Title**
- [ ] Navigate to charts list
- [ ] Click Edit on a chart
- [ ] Change title to "Updated Title"
- [ ] Click Update
- [ ] Verify redirect to charts list
- [ ] Verify title updated in list
- [ ] Verify title updated in database

**Scenario 2: Change Chart Type**
- [ ] Edit a bar chart
- [ ] Change type to line chart
- [ ] Preview updates correctly
- [ ] Click Update
- [ ] View chart on frontend
- [ ] Verify displays as line chart

**Scenario 3: Change Data Columns**
- [ ] Edit chart
- [ ] Uncheck one data column
- [ ] Check a different data column
- [ ] Preview updates
- [ ] Click Update
- [ ] Verify chart reflects new columns

**Scenario 4: Change Status to Inactive**
- [ ] Edit chart
- [ ] Change status to Inactive
- [ ] Click Update
- [ ] Verify chart no longer shows in list (if filtered to active)
- [ ] Try to view via shortcode → Shows error

**Scenario 5: Cancel Editing**
- [ ] Edit chart
- [ ] Make changes
- [ ] Click Cancel
- [ ] Verify returns to charts list
- [ ] Verify changes not saved

**Scenario 6: Invalid Data**
- [ ] Edit chart
- [ ] Clear title
- [ ] Try to update
- [ ] Verify validation error

**Bug Testing:**
- [ ] Edit with deleted source table → Shows error
- [ ] Edit with deleted column → Handles gracefully
- [ ] Rapid-fire edits → No race conditions
- [ ] Edit while another user views → No conflicts

**Performance Testing:**
- [ ] Edit chart with 1000 rows → Loads quickly
- [ ] Edit chart with 5000 rows → Still performant
- [ ] Multiple concurrent edits → Handles well

**Estimated Time:** 4 hours  
**Actual Time:** _____ hours

---

## 📊 WEEK 1 SUMMARY

**Total Tasks:** 7  
**Total Estimated Time:** 19.5 hours  
**Total Actual Time:** _____ hours

**Completion:**
- [ ] Task 1.1: Fix validation (30 min)
- [ ] Task 1.2: Add row limit (3 hrs)
- [ ] Task 1.3: Create edit view (8 hrs)
- [ ] Task 1.4: Update controller (2 hrs)
- [ ] Task 1.5: Add edit button (1 hr)
- [ ] Task 1.6: Register route (1 hr)
- [ ] Task 1.7: Testing (4 hrs)

**Blockers:** _____________________  
**Notes:** _____________________

---

## 🎯 WEEK 2: HIGH PRIORITY FIXES (P1)

### ⏱️ Day 4-5: ChartJsRenderer Class (8 hours)

#### ✅ Task 2.1: Create ChartJsRenderer Class (8 hours)
**Bug:** BUG-004  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Sub-tasks:**
- [ ] Create file: `src/modules/charts/renderers/ChartJsRenderer.php`
- [ ] Add class declaration and namespace
- [ ] Implement render() method
- [ ] Implement get_chartjs_config() method
- [ ] Implement map_chart_type() method
- [ ] Implement prepare_data() method
- [ ] Implement get_chart_options() method
- [ ] Implement get_dataset_color() method
- [ ] Add Chart.js CDN loading logic
- [ ] Add unique ID generation
- [ ] Add JavaScript rendering code
- [ ] Add retry mechanism for library loading
- [ ] Add error handling
- [ ] Update ChartRenderer.php to use new class
- [ ] Test all 4 working chart types render correctly
- [ ] Test responsive behavior
- [ ] Test multiple charts on same page

**Files Changed:**
- [ ] `src/modules/charts/renderers/ChartJsRenderer.php` (NEW FILE, ~200 lines)
- [ ] `src/modules/frontend/renderers/ChartRenderer.php` (~10 lines modified)

**Estimated Time:** 8 hours  
**Actual Time:** _____ hours

---

### ⏱️ Day 6-8: Missing Chart Types (20 hours)

#### ✅ Task 2.2: Implement Column Chart (4 hours)
**Bug:** Part of missing types  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Note:** After fixing BUG-001, column type is already validated. Just need to:

**Sub-tasks:**
- [ ] Update create-chart.php dropdown to show "Column Chart (Vertical)"
- [ ] Verify GoogleChartsRenderer already handles 'column' type (it does)
- [ ] Add to ChartJsRenderer map_chart_type():
  ```php
  case 'column':
      return 'bar'; // Chart.js bar chart with indexAxis: 'x' is column
  ```
- [ ] Add special config for column in get_chart_options():
  ```php
  if ( $chart->type === 'column' ) {
      $options['indexAxis'] = 'x'; // Vertical bars
  }
  ```
- [ ] Test column chart creation
- [ ] Test column chart rendering (Chart.js)
- [ ] Test column chart rendering (Google Charts)
- [ ] Test responsive behavior

**Files Changed:**
- [ ] `src/modules/core/views/create-chart.php` (~1 line)
- [ ] `src/modules/charts/renderers/ChartJsRenderer.php` (~5 lines)

**Estimated Time:** 4 hours  
**Actual Time:** _____ hours

---

#### ✅ Task 2.3: Complete Area Chart (4 hours)
**Bug:** Part of missing types  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Sub-tasks:**
- [ ] Verify GoogleChartsRenderer already handles 'area' (it does, line 116-124)
- [ ] Add to create-chart.php dropdown
- [ ] Add to ChartJsRenderer map_chart_type():
  ```php
  case 'area':
      return 'line'; // Chart.js line chart with fill
  ```
- [ ] Add special config for area in prepare_data():
  ```php
  if ( $chart->type === 'area' ) {
      foreach ( $datasets as &$dataset ) {
          $dataset['fill'] = true; // Fill area under line
          $dataset['backgroundColor'] = 'rgba(54, 162, 235, 0.2)'; // Semi-transparent
      }
  }
  ```
- [ ] Test area chart creation
- [ ] Test area chart rendering (both libraries)
- [ ] Test with multiple data series (stacked areas)

**Files Changed:**
- [ ] `src/modules/core/views/create-chart.php` (~1 line)
- [ ] `src/modules/charts/renderers/ChartJsRenderer.php` (~10 lines)

**Estimated Time:** 4 hours  
**Actual Time:** _____ hours

---

#### ✅ Task 2.4: Implement Scatter Chart (6 hours)
**Bug:** BUG-005  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Sub-tasks:**

**Part A: Data Transformation (3 hours)**
- [ ] Add to ChartService::get_chart_data() special handling for scatter
- [ ] Scatter needs {x, y} format, not {label, value}
- [ ] First data column = X values
- [ ] Second data column = Y values
- [ ] Transform data accordingly:
  ```php
  if ( $chart->type === 'scatter' ) {
      $scatter_data = array();
      for ( $i = 0; $i < count( $data_columns[0]['data'] ); $i++ ) {
          $scatter_data[] = array(
              'x' => $data_columns[0]['data'][$i],
              'y' => $data_columns[1]['data'][$i] ?? 0,
          );
      }
  }
  ```

**Part B: Renderer Updates (2 hours)**
- [ ] Update ChartJsRenderer to handle scatter data format
- [ ] Update GoogleChartsRenderer scatter case (already has partial support)
- [ ] Add to create-chart.php dropdown

**Part C: Testing (1 hour)**
- [ ] Create scatter chart with numeric X and Y
- [ ] Verify points render correctly
- [ ] Test hover tooltips show (x, y) coordinates
- [ ] Test with non-numeric values → Should show error or filter
- [ ] Test Chart.js version
- [ ] Test Google Charts version

**Files Changed:**
- [ ] `src/modules/charts/services/ChartService.php` (~20 lines)
- [ ] `src/modules/charts/renderers/ChartJsRenderer.php` (~15 lines)
- [ ] `src/modules/charts/renderers/GoogleChartsRenderer.php` (~5 lines)
- [ ] `src/modules/core/views/create-chart.php` (~1 line)

**Estimated Time:** 6 hours  
**Actual Time:** _____ hours

---

#### ✅ Task 2.5: Implement Radar Chart (6 hours)
**Bug:** Part of missing types  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Sub-tasks:**
- [ ] Research radar chart requirements (multi-variable, circular display)
- [ ] Add to Chart validation allowed_types
- [ ] Add to ChartJsRenderer (Chart.js has native 'radar' type)
- [ ] Add to GoogleChartsRenderer (might not be supported, use alternative)
- [ ] Add to create-chart.php dropdown
- [ ] Create sample radar chart with 5-6 variables
- [ ] Test rendering (both libraries if supported)
- [ ] Document if Google Charts doesn't support (use Chart.js only)

**Files Changed:**
- [ ] Multiple renderer files (~30 lines total)

**Estimated Time:** 6 hours  
**Actual Time:** _____ hours

---

### ⏱️ Day 9-10: Customization Options UI (16 hours)

#### ✅ Task 2.6: Add Customization Options Panel (16 hours)
**Bug:** BUG-005 (Customization)  
**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Done

**Part A: Color Pickers (4 hours)**
- [ ] Add WordPress Color Picker to create/edit pages
- [ ] Enqueue wp-color-picker script and style
- [ ] Add color picker for each dataset:
  ```html
  <div class="color-picker-wrapper">
      <label>Dataset 1 Color:</label>
      <input type="text" class="color-picker" value="#3498db">
  </div>
  ```
- [ ] Initialize color pickers with JavaScript
- [ ] Save colors to chart config
- [ ] Apply colors in renderers

**Part B: Legend Configuration (3 hours)**
- [ ] Add legend position selector (radio buttons or dropdown):
  - Top
  - Bottom  
  - Left
  - Right
- [ ] Add show/hide legend checkbox
- [ ] Save to chart config
- [ ] Apply in both renderers

**Part C: Axis Labels (4 hours)**
- [ ] Add X-axis label input field
- [ ] Add Y-axis label input field
- [ ] Only show for non-pie charts
- [ ] Save to chart config
- [ ] Apply in renderers (already partially supported)

**Part D: Color Schemes (3 hours)**
- [ ] Create 6-8 predefined color schemes:
  - Default (blue/red/yellow)
  - Pastel
  - Vibrant
  - Monochrome
  - Earth tones
  - Ocean
  - Sunset
- [ ] Add scheme selector with visual preview
- [ ] Apply scheme to all datasets
- [ ] Allow override per dataset

**Part E: Animation Controls (2 hours)**
- [ ] Add enable/disable animation checkbox
- [ ] Add animation duration slider (500-2000ms)
- [ ] Save to config
- [ ] Apply in renderers

**Testing:**
- [ ] Select color for dataset → Applied correctly
- [ ] Change legend position → Moves correctly
- [ ] Hide legend → Legend hidden
- [ ] Set axis labels → Labels shown
- [ ] Apply color scheme → All colors updated
- [ ] Disable animations → Charts load instantly
- [ ] Change animation duration → Takes effect

**Files Changed:**
- [ ] `src/modules/core/views/create-chart.php` (~80 lines)
- [ ] `src/modules/core/views/edit-chart.php` (~80 lines)
- [ ] `assets/js/admin-chart-options.js` (NEW FILE, ~150 lines)
- [ ] `assets/css/admin-charts.css` (NEW FILE, ~50 lines)
- [ ] Both renderer classes (~30 lines each)

**Estimated Time:** 16 hours  
**Actual Time:** _____ hours

---

## 📊 WEEK 2 SUMMARY

**Total Tasks:** 6  
**Total Estimated Time:** 40 hours  
**Total Actual Time:** _____ hours

**Completion:**
- [ ] Task 2.1: ChartJsRenderer (8 hrs)
- [ ] Task 2.2: Column chart (4 hrs)
- [ ] Task 2.3: Area chart (4 hrs)
- [ ] Task 2.4: Scatter chart (6 hrs)
- [ ] Task 2.5: Radar chart (6 hrs)
- [ ] Task 2.6: Customization UI (16 hrs)

**Blockers:** _____________________  
**Notes:** _____________________

---

## 🎯 WEEK 3: MEDIUM PRIORITY & POLISH (P2)

### Tasks 3.1 - 3.10: Search, Filter, Sort, Pagination, Bulk Ops, etc.

_(Detailed checklist available if needed - 30 hours total)_

---

## 🎯 WEEK 4: TESTING & DOCUMENTATION (P2)

### Tasks 4.1 - 4.5: Unit Tests, Integration Tests, Docs

_(Detailed checklist available if needed - 20 hours total)_

---

## 📈 OVERALL PROGRESS TRACKER

### Bugs Fixed
- [ ] BUG-001: Type validation ✅ (30 min)
- [ ] BUG-002: Edit interface ✅ (16 hrs)
- [ ] BUG-003: Row limits ✅ (3 hrs)
- [ ] BUG-004: ChartJsRenderer ✅ (8 hrs)
- [ ] BUG-005: Scatter chart ✅ (6 hrs)
- [ ] BUG-006+: Additional bugs

### Time Tracking
| Week | Estimated | Actual | Variance |
|------|-----------|--------|----------|
| 1 | 19.5 hrs | ___ | ___ |
| 2 | 40 hrs | ___ | ___ |
| 3 | 30 hrs | ___ | ___ |
| 4 | 20 hrs | ___ | ___ |
| **Total** | **109.5 hrs** | **___** | **___** |

### Quality Gates
- [ ] All P0 bugs fixed
- [ ] All P1 bugs fixed
- [ ] All chart types working
- [ ] Edit functionality complete
- [ ] Performance tested
- [ ] Security audit passed
- [ ] Documentation updated
- [ ] User testing completed

---

## 🚀 RELEASE READINESS

### Pre-Release Checklist
- [ ] All critical bugs fixed (P0)
- [ ] All high-priority bugs fixed (P1)
- [ ] User guide written
- [ ] Known issues documented
- [ ] Performance benchmarks recorded
- [ ] Security review complete
- [ ] Browser compatibility tested
- [ ] Mobile responsive tested
- [ ] Accessibility (WCAG) checked
- [ ] Final code review
- [ ] Changelog updated
- [ ] Version number bumped

### Release Types

**Option 1: Beta Release** (After Week 1)
- [ ] P0 fixes complete
- [ ] Mark as "Beta" in readme
- [ ] Document limitations
- [ ] Gather feedback

**Option 2: Production Release** (After Week 2)
- [ ] P0 + P1 fixes complete
- [ ] Feature complete
- [ ] Production quality
- [ ] Full launch

**Option 3: Polish Release** (After Week 4)
- [ ] Everything complete
- [ ] Comprehensive testing
- [ ] Premium quality
- [ ] Marketing push

---

**Current Status:** ⬜ Not Started  
**Next Task:** Task 1.1 (30 minutes)  
**Estimated Completion:** ___________

**Start Date:** ___________  
**Target Completion:** ___________  
**Actual Completion:** ___________

---

*Use this checklist to track daily progress. Update status and actual times as you complete tasks.*
# Cell Merging - User Guide

## 📋 Overview
Cell Merging allows you to combine adjacent cells horizontally or vertically for better table presentation and organization.

## 🎯 Key Features

### **✅ Horizontal Merging**
Merge cells across columns (colspan)
- Merge header titles
- Merge summary rows
- Span multiple columns

### **✅ Vertical Merging**
Merge cells down rows (rowspan)
- Group categories
- Merge identical values
- Consolidate data

### **✅ Auto-Merge**
Automatically merge identical adjacent cells
- Vertical auto-merge
- Horizontal auto-merge
- Smart grouping

### **✅ Preset Patterns**
Ready-to-use merge configurations
- Header title row
- Group rows by category
- Summary footer row

---

## 🔧 Merge Types

### **1. Single Cell Merge**
Manually merge specific cells

**Configuration:**
```php
array(
    'start_row' => 0,    // Starting row (0-based)
    'start_col' => 0,    // Starting column (0-based)
    'row_span'  => 2,    // Number of rows to span
    'col_span'  => 3,    // Number of columns to span
)
```

**Example:**
```
Merge cells from Row 0, Col 0 spanning 2 rows and 3 columns

Before:
┌─────┬─────┬─────┐
│  A  │  B  │  C  │
├─────┼─────┼─────┤
│  D  │  E  │  F  │
└─────┴─────┴─────┘

After:
┌───────────────────┐
│         A         │
│                   │
└───────────────────┘
```

---

### **2. Header Title Merge**
Merge first row across all columns for a title

**Use Case:**
```
Table Title spanning all columns

┌─────────────────────────────────┐
│        Q4 Sales Report          │
├──────────┬──────────┬───────────┤
│  Region  │  Revenue │   Growth  │
├──────────┼──────────┼───────────┤
│   East   │ $50,000  │   +15%    │
└──────────┴──────────┴───────────┘
```

**Code:**
```php
$service->create_merge_pattern('header_row', array(
    'title'        => 'Q4 Sales Report',
    'column_count' => 3
));
```

---

### **3. Group Rows Merge**
Merge cells with identical values vertically

**Use Case:**
```
Group products by category

┌──────────┬─────────────┬────────┐
│          │   Product   │  Price │
│ Electronics                     │
│          ├─────────────┼────────┤
│          │   Laptop    │  $999  │
│          ├─────────────┼────────┤
│          │   Mouse     │  $29   │
├──────────┼─────────────┼────────┤
│          │   T-Shirt   │  $19   │
│ Clothing                        │
│          ├─────────────┼────────┤
│          │   Jeans     │  $49   │
└──────────┴─────────────┴────────┘
```

**Code:**
```php
$service->create_merge_pattern('group_column', array(
    'data'         => $table_data,
    'column_index' => 0  // Category column
));
```

---

### **4. Summary Footer Merge**
Merge footer for totals/summary

**Use Case:**
```
Summary row at bottom

┌────────┬────────┬────────┐
│  Item  │  Qty   │  Price │
├────────┼────────┼────────┤
│ Item A │   5    │  $50   │
├────────┼────────┼────────┤
│ Item B │   3    │  $30   │
├────────┴────────┼────────┤
│     Total:      │  $80   │
└─────────────────┴────────┘
```

**Code:**
```php
$service->create_merge_pattern('summary_footer', array(
    'row_count'    => 5,
    'column_count' => 3,
    'label'        => 'Total:'
));
```

---

## 🎨 Common Use Cases

### **Example 1: Report Header**

**Scenario:** Add a title spanning entire table

```php
$merges = array(
    array(
        'start_row' => 0,
        'start_col' => 0,
        'row_span'  => 1,
        'col_span'  => 5,  // All 5 columns
        'content'   => 'Monthly Performance Report - January 2025'
    )
);
```

**Result:**
```
┌────────────────────────────────────────────┐
│   Monthly Performance Report - Jan 2025   │
├──────────┬─────────┬─────────┬─────┬──────┤
│   Name   │  Sales  │  Target │  %  │ Rank │
└──────────┴─────────┴─────────┴─────┴──────┘
```

---

### **Example 2: Grouped Data**

**Scenario:** Group employees by department

```php
$merges = $service->auto_merge_identical($data, array(
    0 => array('direction' => 'vertical')  // Merge column 0 vertically
));
```

**Result:**
```
┌────────────┬──────────────┬────────────┐
│            │     Name     │    Role    │
│            ├──────────────┼────────────┤
│     HR     │  John Doe    │  Manager   │
│            ├──────────────┼────────────┤
│            │  Jane Smith  │  Recruiter │
├────────────┼──────────────┼────────────┤
│            │  Bob Wilson  │  Developer │
│     IT     ├──────────────┼────────────┤
│            │  Alice Brown │  Designer  │
└────────────┴──────────────┴────────────┘
```

---

### **Example 3: Comparison Table**

**Scenario:** Feature comparison with merged headers

```php
$merges = array(
    // Merge "Features" header
    array(
        'start_row' => 0,
        'start_col' => 0,
        'row_span'  => 2,
        'col_span'  => 1
    ),
    // Merge "Plans" across 3 columns
    array(
        'start_row' => 0,
        'start_col' => 1,
        'row_span'  => 1,
        'col_span'  => 3
    )
);
```

**Result:**
```
┌─────────────┬─────────────────────────────┐
│             │           Plans             │
│             ├─────────┬─────────┬─────────┤
│  Features   │  Basic  │   Pro   │ Premium │
├─────────────┼─────────┼─────────┼─────────┤
│   Storage   │  10GB   │  50GB   │  100GB  │
└─────────────┴─────────┴─────────┴─────────┘
```

---

### **Example 4: Calendar/Schedule**

**Scenario:** Merge events spanning multiple time slots

```php
$merges = array(
    // Morning meeting: 9-11am (2 rows)
    array(
        'start_row' => 1,
        'start_col' => 1,
        'row_span'  => 2,
        'col_span'  => 1,
        'content'   => 'Team Meeting'
    ),
    // Lunch: 12-1pm across all days (1 row, 5 cols)
    array(
        'start_row' => 3,
        'start_col' => 1,
        'row_span'  => 1,
        'col_span'  => 5,
        'content'   => 'Lunch Break'
    )
);
```

---

## 💡 Auto-Merge Feature

### **How Auto-Merge Works:**
Automatically detects and merges identical adjacent cells

**Vertical Auto-Merge:**
```php
$service->auto_merge_identical($data, array(
    0 => array('direction' => 'vertical')  // Column 0
));
```

**Before:**
```
┌──────────┬─────────┐
│  Monday  │  Task A │
├──────────┼─────────┤
│  Monday  │  Task B │
├──────────┼─────────┤
│  Monday  │  Task C │
├──────────┼─────────┤
│ Tuesday  │  Task D │
└──────────┴─────────┘
```

**After:**
```
┌──────────┬─────────┐
│          │  Task A │
│          ├─────────┤
│  Monday  │  Task B │
│          ├─────────┤
│          │  Task C │
├──────────┼─────────┤
│ Tuesday  │  Task D │
└──────────┴─────────┘
```

**Horizontal Auto-Merge:**
```php
$service->auto_merge_identical($data, array(
    0 => array('direction' => 'horizontal')  // Row 0
));
```

---

## 🔒 Merge Validation

### **Overlap Prevention:**
System prevents overlapping merges

**Invalid:**
```php
// These would overlap - only first is applied
$merges = array(
    array('start_row' => 0, 'start_col' => 0, 'row_span' => 2, 'col_span' => 2),
    array('start_row' => 1, 'start_col' => 1, 'row_span' => 2, 'col_span' => 2)
    // ❌ Overlap at (1,1)
);
```

**Valid:**
```php
$merges = array(
    array('start_row' => 0, 'start_col' => 0, 'row_span' => 2, 'col_span' => 2),
    array('start_row' => 0, 'start_col' => 2, 'row_span' => 2, 'col_span' => 2)
    // ✅ No overlap
);
```

---

## 🎓 Best Practices

### **1. Plan Your Merges**
Sketch out table structure before merging
- Identify which cells to merge
- Check for overlaps
- Consider responsive behavior

### **2. Use Presets When Possible**
Leverage built-in patterns
- Header title row
- Group rows
- Summary footer

### **3. Test on Desktop and Mobile**
Merged cells may behave differently
- Test scroll behavior
- Check stack/card modes
- Ensure readability

### **4. Keep It Simple**
Don't over-merge
- Too many merges = confusion
- Maintain scanability
- Preserve data structure

### **5. Document Merge Logic**
Especially for complex merges
- Save merge configurations
- Comment non-obvious patterns
- Version control merge configs

---

## 🔧 Technical Implementation

### **PHP Usage:**
```php
use ATablesCharts\CellMerging\Services\CellMergingService;

$service = new CellMergingService();

// Define merges
$merges = array(
    array(
        'start_row' => 0,
        'start_col' => 0,
        'row_span'  => 1,
        'col_span'  => 3
    )
);

// Apply merging
$result = $service->apply_merging($data, $merges);
$processed_data = $result['data'];

// Generate HTML
$html = $service->generate_html_with_merges(
    $processed_data,
    $headers,
    'atables-table'
);
```

### **AJAX Save:**
```javascript
$.ajax({
    url: ajaxurl,
    type: 'POST',
    data: {
        action: 'atables_save_cell_merges',
        nonce: atables_nonce,
        table_id: tableId,
        merges: JSON.stringify(merges)
    },
    success: function(response) {
        console.log('Merges saved!');
    }
});
```

---

## 📊 HTML Output

### **Generated HTML:**
```html
<table class="atables-merged-table">
    <thead>
        <tr>
            <th>Col 1</th>
            <th>Col 2</th>
            <th>Col 3</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3">Title Row</td>
        </tr>
        <tr>
            <td rowspan="2">Group 1</td>
            <td>Item A</td>
            <td>$10</td>
        </tr>
        <tr>
            <!-- First cell hidden (covered by rowspan) -->
            <td>Item B</td>
            <td>$20</td>
        </tr>
    </tbody>
</table>
```

---

## 🎨 CSS Styling

### **Basic Styles:**
```css
.atables-merged-table {
    border-collapse: collapse;
}

.atables-merged-table td[colspan],
.atables-merged-table td[rowspan] {
    text-align: center;
    font-weight: bold;
    background: #f5f5f5;
}

/* Header merges */
.atables-merged-table thead td[colspan] {
    font-size: 1.2em;
    padding: 15px;
}

/* Group merges */
.atables-merged-table tbody td[rowspan] {
    vertical-align: middle;
    background: #e9ecef;
}
```

---

## 🐛 Troubleshooting

### **Problem: Cells not merging**
**Solution:** Check start_row/start_col are correct (0-based index)

### **Problem: Overlap errors**
**Solution:** Use `validate_no_overlap()` to check conflicts

### **Problem: Mobile display issues**
**Solution:** Test responsive modes - some merges work better in scroll mode

### **Problem: Content not centered**
**Solution:** Add CSS: `text-align: center; vertical-align: middle;`

---

## 🚀 Coming Soon

- **Visual Merge Builder** - UI for creating merges
- **Merge Templates Library** - Industry-specific patterns
- **Smart Auto-Detect** - Automatically suggest merges
- **Merge Presets Per Table Type** - Different presets for different tables
- **Conditional Merges** - Merge based on data values
- **Export with Merges** - Maintain merges in Excel/PDF export
- **Undo/Redo** - Easily revert merge changes

---

## 📝 Quick Reference

### **Merge Structure:**
```php
array(
    'start_row' => 0,      // Starting row (0-based)
    'start_col' => 0,      // Starting column (0-based)
    'row_span'  => 1,      // Rows to span (min 1)
    'col_span'  => 1,      // Columns to span (min 1)
    'content'   => ''      // Optional content override
)
```

### **Preset Patterns:**
- `header_row` - Title across all columns
- `group_column` - Group by category
- `summary_footer` - Footer with totals

### **Auto-Merge:**
- `vertical` - Merge down identical cells
- `horizontal` - Merge across identical cells

---

**Cell Merging makes your tables professional and organized! 📊**

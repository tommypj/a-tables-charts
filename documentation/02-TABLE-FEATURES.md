# Table Features Guide

Complete guide to all table features and functionality in A-Tables & Charts.

---

## 📋 Table of Contents

1. [Creating Tables](#creating-tables)
2. [Data Sources](#data-sources)
3. [Column Configuration](#column-configuration)
4. [Display Settings](#display-settings)
5. [Styling Options](#styling-options)
6. [Search & Filtering](#search-filtering)
7. [Sorting](#sorting)
8. [Pagination](#pagination)
9. [Responsive Design](#responsive-design)
10. [Export Options](#export-options)
11. [Advanced Features](#advanced-features)

---

## 📊 Creating Tables

### Three Ways to Create Tables

**Method 1: Import from File** (Recommended)
- Fastest method for existing data
- Supports CSV, Excel, JSON, XML
- Automatic column detection
- Preserves data formatting

**Method 2: Connect to Database**
- For frequently updated data
- Real-time data synchronization
- No manual re-importing needed
- Requires MySQL credentials

**Method 3: Manual Entry**
- For small datasets
- Full control over data
- Build table row-by-row
- Good for custom tables

---

## 📁 Data Sources

### CSV Files

**Supported:**
- ✅ Standard CSV (comma-separated)
- ✅ TSV (tab-separated)
- ✅ Custom delimiters (semicolon, pipe)
- ✅ UTF-8 and other encodings
- ✅ Headers in first row

**Best Practices:**
```csv
Product,Price,Stock
Laptop,999.99,50
Mouse,29.99,200
Keyboard,79.99,150
```

**Tips:**
- Save as UTF-8 to preserve special characters
- Use quotes for values containing commas: `"Smith, John"`
- First row should contain column headers
- Keep consistent data types per column

**Common Issues:**
- **Problem:** Special characters display as �
  - **Fix:** Save CSV as UTF-8 encoding
- **Problem:** Commas in data break columns
  - **Fix:** Wrap values in quotes: `"$1,000"`

### Excel Files

**Supported Formats:**
- ✅ `.xlsx` (Excel 2007+)
- ✅ `.xls` (Excel 97-2003)
- ✅ Multiple sheets (import specific sheet)
- ✅ Formulas (imported as calculated values)
- ✅ Formatted numbers (preserved)

**What's Imported:**
- Cell values (formulas calculated)
- Text formatting (bold, italic preserved as data)
- Numbers and dates
- First row as headers

**What's NOT Imported:**
- Cell colors/backgrounds
- Charts/graphs
- Merged cells (use top-left value)
- Macros/VBA code
- Conditional formatting

**Tips:**
- Clean data before import (remove empty rows)
- Use first row for clear column names
- Avoid merged cells (will cause issues)
- Save complex formulas as values first

### MySQL Database

**Connection Settings:**

```
Host: localhost (or your server IP)
Database: your_database_name
Username: db_user
Password: db_password
Table: your_table_name
```

**Setup Steps:**

1. **Get Database Credentials**
   - From your hosting control panel
   - Or ask your developer/host

2. **Test Connection**
   - Plugin will verify credentials
   - Shows error if connection fails

3. **Select Table**
   - Choose from available tables
   - Preview data before import

4. **Auto-Sync** (Optional)
   - Enable automatic updates
   - Set sync interval (hourly, daily)
   - Data stays current automatically

**Security Note:** Credentials are encrypted and stored securely. Only admin users can access database settings.

### JSON Files

**Supported Structures:**

**Array of Objects:**
```json
[
  {"name": "Laptop", "price": 999.99, "stock": 50},
  {"name": "Mouse", "price": 29.99, "stock": 200}
]
```

**Object with Array:**
```json
{
  "products": [
    {"name": "Laptop", "price": 999.99},
    {"name": "Mouse", "price": 29.99}
  ]
}
```

**Tips:**
- Use consistent key names across objects
- Plugin auto-detects structure
- Nested objects supported (flattened to columns)

### XML Files

**Supported Structure:**

```xml
<?xml version="1.0"?>
<products>
  <product>
    <name>Laptop</name>
    <price>999.99</price>
    <stock>50</stock>
  </product>
  <product>
    <name>Mouse</name>
    <price>29.99</price>
    <stock>200</stock>
  </product>
</products>
```

**Requirements:**
- Valid XML syntax
- Consistent element structure
- Root element (ignored)
- Repeating elements become rows

### Google Sheets

**How to Import:**

1. **Make Sheet Public**
   - File → Share → "Anyone with link can view"

2. **Get Share Link**
   - Click "Share" button
   - Copy link (looks like: `https://docs.google.com/spreadsheets/d/XXXXX/edit`)

3. **Paste in Plugin**
   - Go to Create Table → Google Sheets
   - Paste URL
   - Select sheet tab (if multiple)
   - Click Import

**Auto-Sync:**
- ✅ Enable auto-sync to keep data current
- ✅ Updates hourly/daily (configurable)
- ✅ No manual re-importing needed

**Limitations:**
- Sheet must be public or "anyone with link"
- Private sheets require API key (advanced)
- Formula results imported (not formulas themselves)

### WooCommerce Products

**Built-in Integration:**

Import WooCommerce products directly:

1. Go to Create Table → WooCommerce
2. Select product data to include:
   - ✅ Product name
   - ✅ Price
   - ✅ Stock status
   - ✅ Categories
   - ✅ SKU
   - ✅ Custom fields
3. Choose product filters:
   - Published products only
   - Specific categories
   - Price range
   - In stock only
4. Click Import

**Auto-Update:**
- Enable sync to keep product data current
- Updates when products change
- Perfect for product catalogs

---

## 🎛️ Column Configuration

### Column Settings

Each column can be configured individually:

**Column Name**
- Display name (shown in table header)
- Can differ from original import name
- Example: `product_name` → `Product Name`

**Data Type**
- Text (default)
- Number (right-aligned, formatted)
- Currency ($ symbol, 2 decimals)
- Date (formatted display)
- URL (clickable links)
- Image (display images)
- Boolean (Yes/No, checkmarks)

**Width**
- Auto (default, based on content)
- Fixed pixels: `150px`
- Percentage: `25%`
- Min/Max width constraints

**Alignment**
- Left (default for text)
- Center (good for images, short values)
- Right (default for numbers)

**Visibility**
- Show (default)
- Hide (column exists but not displayed)
- Responsive hide (hide on mobile only)

**Sortable**
- ✅ Enable (users can click to sort)
- ❌ Disable (column not sortable)

**Searchable**
- ✅ Include in search (default)
- ❌ Exclude from search

### Column Types in Detail

**Text Column**
```
Type: Text
Format: Plain text
Example: "Laptop Computer"
Features: Search, sort alphabetically
```

**Number Column**
```
Type: Number
Format: 1,234.56
Decimals: 0-4 (configurable)
Thousands separator: ✅
Features: Sort numerically
```

**Currency Column**
```
Type: Currency
Format: $1,234.56
Symbol: $ (configurable)
Position: Before/after value
Features: Right-aligned, formatted
```

**Date Column**
```
Type: Date
Input: YYYY-MM-DD or MM/DD/YYYY
Display: October 31, 2025
Format: Configurable
Features: Sort chronologically
```

**URL Column**
```
Type: URL
Display: Clickable link
Target: _blank (new tab)
Text: Custom link text or URL
Features: Auto-detects http/https
```

**Image Column**
```
Type: Image
Input: Image URL
Display: Thumbnail (configurable size)
Lightbox: ✅ Click to enlarge
Size: 50px, 100px, 150px, custom
```

**Boolean Column**
```
Type: Boolean
Input: true/false, 1/0, yes/no
Display: ✓/✗ or Yes/No
Style: Checkmarks or text
Features: Filter by true/false
```

### Reordering Columns

**Drag & Drop:**
1. Go to table edit page
2. Hover over column header
3. Drag to new position
4. Drop
5. Save table

**Or use arrows:**
- ← Move left
- → Move right
- ↑ Move to start
- ↓ Move to end

### Adding/Removing Columns

**Add Column:**
1. Click "Add Column" button
2. Enter column name
3. Select data type
4. Choose position
5. Save

**Remove Column:**
1. Click column settings (⚙️)
2. Click "Delete Column"
3. Confirm deletion
4. Save table

**Rename Column:**
1. Click column header
2. Edit name inline
3. Press Enter
4. Auto-saves

---

## 🎨 Display Settings

### Pagination

**Enable/Disable:**
- ✅ Enable: Show X rows per page with navigation
- ❌ Disable: Show all rows at once (not recommended for large tables)

**Rows Per Page:**
- 10 (default)
- 25
- 50
- 100
- Custom value

**Pagination Style:**
- Numbers: `1 2 3 4 5`
- Previous/Next: `← Previous | Next →`
- Both: `← 1 2 3 4 5 →`
- Jump to page: Input box

**Position:**
- Top only
- Bottom only (default)
- Both top and bottom

**Info Display:**
- Show: "Showing 1 to 10 of 100 entries"
- Hide info text

### Search Box

**Enable/Disable:**
- ✅ Enable: Show search box above table
- ❌ Disable: No search functionality

**Search Settings:**
- Position: Top left, top right, top center
- Placeholder text: "Search..."
- Search delay: Instant or 300ms delay
- Case sensitive: Yes/No
- Partial match: Yes/No

**Search Scope:**
- All columns (default)
- Specific columns only
- Exclude certain columns

### Row Selection

**Enable Selection:**
- ✅ Checkboxes in first column
- Select individual rows
- Select all rows
- Bulk actions available

**Bulk Actions:**
- Export selected rows
- Delete selected (if editing enabled)
- Custom actions (via hooks)

### Info Display

**Table Information:**
- Total rows count: "100 entries"
- Filtered count: "Showing 25 of 100"
- Current page: "Page 1 of 10"
- Position: Above/below table

### Row Hover Effects

**Hover Style:**
- Background color change
- Border highlight
- Shadow effect
- Scale slightly
- Custom CSS

---

## 🎨 Styling Options

### Pre-built Themes

**Default Theme**
- Clean white background
- Light gray headers
- Subtle borders
- Good for most sites

**Dark Theme**
- Dark background
- Light text
- Accent colors
- Modern look

**Minimal Theme**
- No borders
- Subtle separators
- Lots of whitespace
- Clean design

**Striped Theme**
- Alternating row colors
- Easy to read
- Classic table look

**Corporate Theme**
- Professional colors
- Bold headers
- Business-friendly

**Custom Theme**
- Create your own
- Save for reuse
- Export/import themes

### Color Customization

**Header:**
- Background color
- Text color
- Border color
- Font weight

**Rows:**
- Even row background
- Odd row background (for striping)
- Text color
- Border color

**Hover State:**
- Hover background
- Hover text color
- Transition speed

**Active/Selected:**
- Selected row background
- Selected row border
- Checkbox colors

### Typography

**Font Family:**
- System default
- Google Fonts integration
- Custom web fonts
- Fallback fonts

**Font Sizes:**
- Header: 14px-24px
- Body: 12px-18px
- Footer: 10px-14px

**Font Weights:**
- Light: 300
- Normal: 400
- Medium: 500
- Bold: 700

**Text Transform:**
- None (default)
- Uppercase headers
- Capitalize first letter
- Lowercase

### Borders & Spacing

**Borders:**
- All borders
- Horizontal only
- Vertical only
- Outer border only
- No borders

**Border Style:**
- Solid (default)
- Dashed
- Dotted
- Double

**Border Width:**
- Thin: 1px
- Medium: 2px
- Thick: 3px

**Cell Padding:**
- Compact: 5px
- Normal: 10px (default)
- Comfortable: 15px
- Custom

**Table Margin:**
- Top/bottom spacing
- Left/right spacing
- Center alignment

### Advanced Styling

**Custom CSS Classes:**

Add to shortcode:
```
[atables id="1" class="my-custom-table"]
```

Then in Custom CSS:
```css
.my-custom-table table {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.my-custom-table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
}

.my-custom-table tbody tr:hover {
    background-color: #f0f4ff;
    transform: scale(1.01);
    transition: all 0.2s ease;
}
```

**Conditional Formatting:**

Highlight rows based on values (requires Pro version or custom CSS):

```css
/* Highlight high-value products */
.my-table tr[data-price-above-1000] {
    background-color: #fff3cd;
}

/* Low stock warning */
.my-table tr[data-stock-below-10] {
    background-color: #f8d7da;
    color: #721c24;
}
```

---

## 🔍 Search & Filtering

### Basic Search

**How It Works:**
- User types in search box
- Table filters in real-time
- Shows matching rows only
- Searches across all columns (by default)

**Search Features:**
- Case-insensitive by default
- Partial word matching
- Multi-word search (AND logic)
- Search highlighting (optional)

**Example:**
```
Search: "laptop"
Matches: "Laptop", "Gaming Laptop", "laptop computer"

Search: "gaming laptop"
Matches: Rows containing BOTH "gaming" AND "laptop"
```

### Column-Specific Filters

**Enable Column Filters:**
1. Edit table
2. Display Settings → Enable Column Filters
3. Select filter type per column

**Filter Types:**

**Text Filter:**
- Dropdown with unique values
- Or text input box
- Select multiple values (OR logic)

**Number Range:**
- Min/max inputs
- Slider (visual)
- Predefined ranges

**Date Range:**
- Date picker (from/to)
- Predefined ranges (Last 7 days, This month)
- Custom range

**Boolean Filter:**
- Checkbox: Show true only
- Radio: True/False/All
- Toggle switch

### Advanced Filtering

**Multiple Filters:**
- Combine column filters
- AND logic (all must match)
- Clear all filters button

**Filter Presets:**
- Save common filter combinations
- Quick filter buttons
- Example: "In Stock", "Sale Items", "New Products"

**URL Parameters:**

Pre-filter via URL:
```
https://yoursite.com/products?category=laptops&price_min=500
```

Map to table filters in settings.

---

## ↕️ Sorting

### Basic Sorting

**Click Column Headers:**
- First click: Ascending ↑
- Second click: Descending ↓
- Third click: Remove sort (back to original order)

**Visual Indicators:**
- ↑ arrow: Sorted ascending
- ↓ arrow: Sorted descending
- ↕ both arrows: Column is sortable
- No arrows: Column not sortable

### Multi-Column Sorting

**How to Use:**
1. Click first column (primary sort)
2. Hold Shift + click second column (secondary sort)
3. Hold Shift + click third column (tertiary sort)

**Example:**
```
Sort by: Category (ascending) → Price (descending)
Result: Categories A-Z, within each category prices high-to-low
```

### Default Sorting

**Set Default Sort:**
1. Edit table
2. Display Settings → Default Sort
3. Select column
4. Select order (asc/desc)
5. Save

**Shortcode Override:**
```
[atables id="1" sort_column="price" sort_order="desc"]
```

### Sort Data Types

**Text:**
- A-Z alphabetical
- Case-insensitive
- Special characters at end

**Numbers:**
- Numerical order (not alphabetical)
- 1, 2, 10, 20 (NOT 1, 10, 2, 20)
- Negative numbers supported

**Dates:**
- Chronological order
- Supports multiple date formats
- Invalid dates sorted last

**Currency:**
- Sorted as numbers (ignoring $, commas)
- $1,000 comes after $100

---

## 📱 Responsive Design

### Mobile Optimization

**Responsive Modes:**

**Mode 1: Horizontal Scroll** (Default)
- Table scrolls horizontally on small screens
- All columns visible
- Native scroll feel
- Best for: Tables with many columns

**Mode 2: Stacked Rows**
- Columns stack vertically on mobile
- Each row becomes a card
- Labels repeat for each value
- Best for: Tables with 3-6 columns

**Mode 3: Column Hiding**
- Hide less important columns on mobile
- Show toggle to reveal hidden columns
- Prioritize key information
- Best for: Tables with many columns but only few are critical

**Mode 4: Hybrid**
- Combine stacking + column hiding
- Most flexible
- Customizable per breakpoint

### Breakpoints

**Default Breakpoints:**
- Desktop: > 1024px (all columns)
- Tablet: 768px - 1024px (some columns hidden)
- Mobile: < 768px (stacked or scrolling)

**Custom Breakpoints:**
```css
/* Custom breakpoint at 900px */
@media screen and (max-width: 900px) {
    .atables-wrapper .column-description {
        display: none;
    }
}
```

### Mobile-Specific Settings

**Font Size:**
- Auto-scale (recommended)
- Fixed size (12px-16px)
- Relative size (90% of desktop)

**Touch Targets:**
- Larger tap areas (44px minimum)
- Increased row height
- Bigger buttons

**Gestures:**
- Swipe to scroll
- Pull to refresh (if data synced)
- Pinch to zoom (optional)

---

## 💾 Export Options

### Export to CSV

**What's Exported:**
- All visible columns
- Filtered rows (if filter active)
- Or all rows (if no filter)
- Formatted values

**CSV Options:**
- Delimiter: Comma, semicolon, tab
- Encoding: UTF-8 (default), Windows-1252
- Include headers: Yes/No
- Quote all values: Yes/No

**Use Case:**
- Open in Excel/Google Sheets
- Import to other systems
- Data backup
- Lightweight format

### Export to Excel

**What's Exported:**
- All visible columns
- Formatted values (colors, bold, etc.)
- Column widths preserved
- Multiple sheets (if configured)

**Excel Options:**
- Format: .xlsx (modern) or .xls (legacy)
- Sheet name: Custom or table title
- Include filters: Excel filter dropdowns
- Include formulas: If applicable

**Use Case:**
- Professional reports
- Formatted documents
- Further Excel analysis
- Preserve styling

### Export to PDF

**What's Exported:**
- Table with current styling
- Page title
- Date/time stamp
- Page numbers

**PDF Options:**
- Page size: A4, Letter, Legal, custom
- Orientation: Portrait or Landscape
- Margins: Normal, narrow, wide
- Font size: Auto-scale to fit

**Styling:**
- Preserve colors
- Preserve fonts
- Table borders
- Header/footer

**Use Case:**
- Printable reports
- Share via email
- Archive/records
- Professional documents

### Export Buttons

**Display Export Buttons:**
```
[atables id="1" export="true"]
```

**Button Position:**
- Above table (default)
- Below table
- Both top and bottom
- Floating action button

**Customization:**
```css
.atables-export-buttons button {
    background-color: #0073aa;
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
}
```

---

## 🚀 Advanced Features

### Inline Editing

**Enable Inline Editing:**
- Settings → Enable Inline Editing
- Only for Admin users (security)
- Edit cells directly in table
- Changes save automatically

**How to Use:**
1. Click cell
2. Edit value
3. Press Enter or click away
4. Auto-saves to database

**Security:**
- Only admin/editor roles
- Sanitized before saving
- Audit log of changes

### Conditional Formatting

**Highlight Cells:**
- Based on value (>1000, <10, etc.)
- Based on text ("Sale", "New")
- Based on date (past/future)
- Custom rules

**Example Rules:**
```
IF Price > $1000 THEN background = yellow
IF Stock < 10 THEN text color = red, bold
IF Status = "Sale" THEN badge = red
```

### Calculated Columns

**Add Calculated Column:**
- Based on other columns
- Formulas supported
- Auto-updates when data changes

**Examples:**
```
Total = Price × Quantity
Discount = Price - SalePrice
Percentage = (Value / Total) × 100
FullName = FirstName + " " + LastName
```

### Table Relationships

**Link Tables:**
- Foreign key relationships
- Display related data
- Dropdown selects from another table

**Example:**
```
Products Table → Category ID
Categories Table → Category ID (primary key)
Result: Show category name instead of ID
```

### Caching

**Performance Optimization:**
- Enable caching: Settings → Performance
- Cache lifetime: 1 hour, 6 hours, 24 hours
- Manual cache clear
- Auto-refresh on data change

**Benefits:**
- Faster page loads
- Reduced database queries
- Better for high-traffic sites

### Custom CSS Per Table

**Table-Specific Styling:**

Edit table → Advanced → Custom CSS:

```css
/* Only affects this table */
#atables-table-5 {
    background: #f9f9f9;
}

#atables-table-5 thead {
    background: linear-gradient(90deg, #667eea, #764ba2);
    color: white;
}
```

### Hooks & Filters (Developers)

**Modify Table Output:**
```php
// Before table render
add_filter( 'atables_before_render', function( $html, $table_id ) {
    return '<div class="custom-wrapper">' . $html;
}, 10, 2 );

// After table render
add_filter( 'atables_after_render', function( $html, $table_id ) {
    return $html . '</div>';
}, 10, 2 );
```

**Modify Data Before Display:**
```php
add_filter( 'atables_table_data', function( $data, $table_id ) {
    // Add custom column
    foreach ( $data as &$row ) {
        $row['custom'] = 'Custom Value';
    }
    return $data;
}, 10, 2 );
```

---

## 📚 Related Documentation

- [Chart Features](03-CHART-FEATURES.md)
- [Import Guide](04-IMPORT-GUIDE.md)
- [Shortcode Reference](SHORTCODE-REFERENCE.md)
- [FAQ](FAQ.md)
- [Troubleshooting](TROUBLESHOOTING.md)

---

**Updated:** October 2025
**Version:** 1.0.0

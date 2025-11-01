# Shortcode Reference Guide

Complete reference for all A-Tables & Charts shortcodes.

---

## 📋 Table of Contents

1. [Table Shortcode](#table-shortcode)
2. [Chart Shortcode](#chart-shortcode)
3. [Parameters Reference](#parameters)
4. [Advanced Usage](#advanced-usage)
5. [PHP Integration](#php-integration)
6. [Examples](#examples)

---

## 📊 Table Shortcode

### Basic Usage

Display a table using its ID:

```
[atables id="1"]
```

**Required:** `id` parameter

---

### Table Parameters

| Parameter | Type | Default | Description | Example |
|-----------|------|---------|-------------|---------|
| `id` | integer | **Required** | Table ID to display | `id="1"` |
| `rows` | integer | From settings | Rows per page | `rows="25"` |
| `page` | integer | 1 | Starting page number | `page="2"` |
| `search` | string | empty | Pre-fill search box | `search="apple"` |
| `sort_column` | string | none | Column to sort by | `sort_column="name"` |
| `sort_order` | string | asc | Sort order (asc/desc) | `sort_order="desc"` |
| `responsive` | boolean | true | Enable responsive mode | `responsive="false"` |
| `pagination` | boolean | true | Show pagination | `pagination="false"` |
| `search_box` | boolean | true | Show search box | `search_box="false"` |
| `export` | boolean | false | Show export buttons | `export="true"` |
| `info` | boolean | true | Show "Showing X of Y" | `info="false"` |
| `class` | string | empty | Custom CSS class | `class="my-table"` |

---

### Table Examples

**Basic table:**
```
[atables id="1"]
```

**Table with custom rows per page:**
```
[atables id="1" rows="50"]
```

**Table starting on page 2:**
```
[atables id="1" page="2"]
```

**Table with pre-filled search:**
```
[atables id="1" search="laptop"]
```

**Table sorted by column (descending):**
```
[atables id="1" sort_column="price" sort_order="desc"]
```

**Table without search and pagination:**
```
[atables id="1" search_box="false" pagination="false"]
```

**Table with export buttons:**
```
[atables id="1" export="true"]
```

**Table with custom CSS class:**
```
[atables id="1" class="my-custom-table"]
```

**Combined parameters:**
```
[atables id="1" rows="25" search="product" sort_column="name" sort_order="asc" export="true" class="featured-table"]
```

---

## 📈 Chart Shortcode

### Basic Usage

Display a chart using its ID:

```
[atables_chart id="1"]
```

**Required:** `id` parameter

---

### Chart Parameters

| Parameter | Type | Default | Description | Example |
|-----------|------|---------|-------------|---------|
| `id` | integer | **Required** | Chart ID to display | `id="1"` |
| `width` | string | 100% | Chart width | `width="800px"` or `width="100%"` |
| `height` | string | 400px | Chart height | `height="600px"` |
| `title` | string | From chart | Override chart title | `title="Sales 2024"` |
| `type` | string | From chart | Override chart type | `type="bar"` |
| `colors` | string | From chart | Custom color scheme | `colors="#FF0000,#00FF00"` |
| `legend` | boolean | true | Show legend | `legend="false"` |
| `legend_position` | string | bottom | Legend position | `legend_position="top"` |
| `tooltip` | boolean | true | Show tooltips on hover | `tooltip="false"` |
| `animation` | boolean | true | Enable animation | `animation="false"` |
| `class` | string | empty | Custom CSS class | `class="my-chart"` |

---

### Chart Types

Valid values for `type` parameter:

- `bar` - Horizontal bar chart
- `column` - Vertical bar chart (columns)
- `line` - Line chart
- `area` - Area chart (filled line chart)
- `pie` - Pie chart
- `doughnut` - Doughnut chart (pie with hole)
- `scatter` - Scatter plot
- `radar` - Radar/spider chart

---

### Legend Positions

Valid values for `legend_position`:

- `top` - Above the chart
- `bottom` - Below the chart (default)
- `left` - Left side
- `right` - Right side
- `none` - No legend

---

### Chart Examples

**Basic chart:**
```
[atables_chart id="1"]
```

**Chart with custom size:**
```
[atables_chart id="1" width="800px" height="600px"]
```

**Chart with custom title:**
```
[atables_chart id="1" title="Q4 Sales Report"]
```

**Override chart type:**
```
[atables_chart id="1" type="pie"]
```

**Custom colors (comma-separated hex codes):**
```
[atables_chart id="1" colors="#FF6384,#36A2EB,#FFCE56"]
```

**No legend:**
```
[atables_chart id="1" legend="false"]
```

**Legend on top:**
```
[atables_chart id="1" legend_position="top"]
```

**No animation (faster rendering):**
```
[atables_chart id="1" animation="false"]
```

**Responsive full-width chart:**
```
[atables_chart id="1" width="100%" height="400px"]
```

**Combined parameters:**
```
[atables_chart id="1" width="100%" height="500px" type="line" colors="#FF0000,#0000FF" legend_position="top" class="sales-chart"]
```

---

## 🎯 Advanced Usage

### Conditional Display

Display different tables based on user role:

```php
<?php
if ( current_user_can( 'administrator' ) ) {
    echo do_shortcode( '[atables id="1"]' ); // Admin table
} else {
    echo do_shortcode( '[atables id="2"]' ); // Public table
}
?>
```

---

### Dynamic Table ID

Display table based on query parameter:

```php
<?php
$table_id = isset( $_GET['table'] ) ? intval( $_GET['table'] ) : 1;
echo do_shortcode( '[atables id="' . $table_id . '"]' );
?>
```

**Usage:** `https://yoursite.com/page?table=5`

---

### Multiple Tables on Same Page

Yes, you can display multiple tables/charts:

```
[atables id="1"]

Some text here...

[atables id="2"]

More content...

[atables_chart id="1"]
```

**Performance tip:** Use pagination for large tables to avoid slow page loads.

---

### Filtering Tables

**Pre-filter by search term:**
```
[atables id="1" search="category:electronics"]
```

**Show only specific page:**
```
[atables id="1" page="3"]
```

**Combine filters:**
```
[atables id="1" search="laptop" sort_column="price" sort_order="asc"]
```

---

### Responsive Breakpoints

Tables automatically adapt to mobile devices. Override responsive behavior:

```
[atables id="1" responsive="false"]
```

Or customize via CSS (add to Settings → Custom CSS):

```css
/* Custom breakpoint for tablets */
@media screen and (max-width: 768px) {
    .atables-wrapper table {
        font-size: 12px;
    }
}

/* Mobile phones */
@media screen and (max-width: 480px) {
    .atables-wrapper table {
        font-size: 10px;
    }
}
```

---

## 💻 PHP Integration

### Display Table in PHP

**Method 1: Using shortcode**
```php
<?php echo do_shortcode( '[atables id="1"]' ); ?>
```

**Method 2: Using function (if available)**
```php
<?php
if ( function_exists( 'atables_display_table' ) ) {
    atables_display_table( 1 );
}
?>
```

---

### Display Chart in PHP

```php
<?php echo do_shortcode( '[atables_chart id="1"]' ); ?>
```

---

### Get Table Data Programmatically

```php
<?php
// Get table object
$table_id = 1;
$table = atables_get_table( $table_id );

if ( $table ) {
    echo '<h2>' . esc_html( $table->title ) . '</h2>';

    // Get table data
    $data = atables_get_table_data( $table_id );

    // Loop through rows
    foreach ( $data as $row ) {
        echo '<p>' . esc_html( $row['column_name'] ) . '</p>';
    }
}
?>
```

---

### Check if Table Exists

```php
<?php
$table_id = 1;

if ( atables_table_exists( $table_id ) ) {
    echo do_shortcode( '[atables id="' . $table_id . '"]' );
} else {
    echo '<p>Table not found.</p>';
}
?>
```

---

### Display Table in Widget

Add this to your theme's `functions.php`:

```php
<?php
// Register custom widget area
function my_atables_widget_init() {
    register_sidebar( array(
        'name'          => 'Table Sidebar',
        'id'            => 'table-sidebar',
        'before_widget' => '<div class="table-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'my_atables_widget_init' );
?>
```

Then add to your theme template:

```php
<?php
if ( is_active_sidebar( 'table-sidebar' ) ) {
    dynamic_sidebar( 'table-sidebar' );
}
?>
```

In WordPress Admin → Appearance → Widgets, add a **Text** or **HTML** widget with your shortcode.

---

## 📚 Examples

### Example 1: Product Comparison Table

```
[atables id="5" search_box="true" sort_column="price" sort_order="asc" export="true" class="product-comparison"]
```

**Result:** Sortable product table, pre-sorted by price (lowest first), with search and export.

---

### Example 2: Sales Dashboard

```
<h2>Monthly Sales</h2>
[atables id="10" rows="12" pagination="false" info="false"]

<h2>Sales Trend</h2>
[atables_chart id="3" type="line" height="400px" colors="#4CAF50"]

<h2>Product Distribution</h2>
[atables_chart id="4" type="pie" width="600px" legend_position="right"]
```

**Result:** Complete dashboard with table and two charts.

---

### Example 3: Event Schedule

```
[atables id="8" search_box="true" sort_column="date" sort_order="desc" rows="10" class="event-schedule"]
```

**Result:** Event table sorted by date (newest first), 10 events per page, searchable.

---

### Example 4: Pricing Table

```
[atables id="15" pagination="false" search_box="false" responsive="true" class="pricing-table"]
```

**Result:** Clean pricing table with no pagination or search (for short tables), fully responsive.

---

### Example 5: Embedded in Page Builder

**Elementor:**
1. Add **Shortcode** widget
2. Paste: `[atables id="1"]`
3. Style with Elementor controls

**Divi:**
1. Add **Code** module
2. Paste: `[atables id="1"]`
3. Save

**Beaver Builder:**
1. Add **HTML** module
2. Paste: `[atables id="1"]`
3. Save

---

### Example 6: Conditional Display

Show different tables for logged-in vs logged-out users:

```php
<?php if ( is_user_logged_in() ) : ?>
    [atables id="20" title="Member Pricing"]
<?php else : ?>
    [atables id="21" title="Public Pricing"]
<?php endif; ?>
```

---

### Example 7: Archive Page

Create a custom page template showing all tables:

```php
<?php
/* Template Name: All Tables */
get_header();

$tables = atables_get_all_tables();

foreach ( $tables as $table ) {
    echo '<h2>' . esc_html( $table->title ) . '</h2>';
    echo do_shortcode( '[atables id="' . $table->id . '"]' );
}

get_footer();
?>
```

---

## 🎨 Styling Shortcodes

### Custom CSS Classes

Add custom class to shortcode:

```
[atables id="1" class="my-custom-table"]
```

Then style in Settings → Custom CSS:

```css
.my-custom-table table {
    border: 2px solid #0073aa;
    background-color: #f5f5f5;
}

.my-custom-table thead {
    background-color: #0073aa;
    color: white;
}

.my-custom-table tbody tr:hover {
    background-color: #e6f2ff;
}
```

---

### Wrapper Styling

All tables/charts are wrapped in `.atables-wrapper`:

```css
.atables-wrapper {
    margin: 20px 0;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
```

---

## ⚠️ Common Mistakes

### ❌ Wrong Shortcode Name

```
WRONG: [wptable id="1"]
WRONG: [table id="1"]
WRONG: [atables-chart id="1"]

RIGHT: [atables id="1"]
RIGHT: [atables_chart id="1"]
```

---

### ❌ Mixing Table and Chart Shortcodes

```
WRONG: [atables id="1"] for a chart
WRONG: [atables_chart id="1"] for a table

RIGHT: [atables id="1"] for tables
RIGHT: [atables_chart id="1"] for charts
```

---

### ❌ Wrong Parameter Format

```
WRONG: [atables id=1] (missing quotes)
WRONG: [atables id='1'] (wrong quote type)
WRONG: [atables ID="1"] (wrong case)

RIGHT: [atables id="1"]
```

---

### ❌ Invalid Parameter Values

```
WRONG: [atables id="1" rows="abc"] (not a number)
WRONG: [atables id="1" sort_order="high"] (invalid value)
WRONG: [atables_chart id="1" type="graph"] (invalid type)

RIGHT: [atables id="1" rows="25"]
RIGHT: [atables id="1" sort_order="desc"]
RIGHT: [atables_chart id="1" type="bar"]
```

---

## 🔍 Debugging Shortcodes

### Shortcode Not Rendering?

1. **Check if shortcode is correct:**
   - View page source (Ctrl+U)
   - Search for your shortcode text
   - If visible as text, shortcode isn't being processed

2. **Check if plugin is active:**
   - Plugins → Ensure A-Tables & Charts is activated

3. **Check if table/chart exists:**
   - Verify ID number
   - Go to All Tables/Charts and confirm ID

4. **Check for conflicts:**
   - Try with default WordPress theme
   - Disable other plugins temporarily

---

### Test in Different Contexts

```
Test 1: Post content
Test 2: Page content
Test 3: Widget (Text widget)
Test 4: Theme template (PHP)
Test 5: Page builder
```

If works in some places but not others, it's a compatibility issue with that specific context.

---

## 📖 Related Documentation

- [Getting Started Guide](01-GETTING-STARTED.md)
- [Table Features](02-TABLE-FEATURES.md)
- [Chart Features](03-CHART-FEATURES.md)
- [Troubleshooting](TROUBLESHOOTING.md)
- [FAQ](FAQ.md)

---

## 💬 Need Help?

**Support Resources:**
- Documentation: https://your-docs-site.com
- Support Forum: https://codecanyon.net/item/support
- Email: support@yourdomain.com
- Video Tutorials: https://youtube.com/your-channel

**Include this info when asking for help:**
1. Full shortcode you're using
2. Where you're using it (post, page, widget, etc.)
3. Screenshot of the issue
4. Browser console errors (F12 → Console)

---

**Updated:** October 2025
**Version:** 1.0.0

# Quick Reference Card

**A-Tables & Charts for WordPress**
**Version 1.0.0**

Quick reference for common tasks and shortcuts.

---

## 📦 Installation

```
1. Download installable WordPress file (.zip)
2. WordPress Admin → Plugins → Add New → Upload
3. Choose file → Install → Activate
4. Look for "A-Tables & Charts" in sidebar
```

**System Requirements:**
- WordPress 5.8+
- PHP 7.4+
- MySQL 5.6+

---

## 🚀 Quick Start

### Create First Table (5 steps)

```
1. A-Tables & Charts → Create New Table
2. Upload CSV/Excel file (or choose other source)
3. Configure settings (rows per page, search, etc.)
4. Save table → Copy shortcode
5. Paste shortcode in page: [atables id="1"]
```

### Create First Chart (4 steps)

```
1. A-Tables & Charts → Charts → Add New
2. Select table as data source
3. Choose chart type (bar, line, pie, etc.)
4. Save chart → Add shortcode: [atables_chart id="1"]
```

---

## 📊 Shortcodes

### Table Shortcode

**Basic:**
```
[atables id="1"]
```

**With Options:**
```
[atables id="1" rows="25" search="laptop" sort_column="price" sort_order="desc" export="true"]
```

**Common Parameters:**
| Parameter | Values | Example |
|-----------|--------|---------|
| `id` | Table ID (required) | `id="1"` |
| `rows` | 10, 25, 50, 100 | `rows="25"` |
| `search` | Pre-fill search | `search="laptop"` |
| `sort_column` | Column name | `sort_column="price"` |
| `sort_order` | asc, desc | `sort_order="desc"` |
| `pagination` | true, false | `pagination="false"` |
| `search_box` | true, false | `search_box="true"` |
| `export` | true, false | `export="true"` |
| `class` | CSS class | `class="my-table"` |

### Chart Shortcode

**Basic:**
```
[atables_chart id="1"]
```

**With Options:**
```
[atables_chart id="1" width="100%" height="500px" type="pie" legend_position="top"]
```

**Common Parameters:**
| Parameter | Values | Example |
|-----------|--------|---------|
| `id` | Chart ID (required) | `id="1"` |
| `width` | px or % | `width="800px"` |
| `height` | px | `height="500px"` |
| `type` | Chart type | `type="bar"` |
| `colors` | Hex colors | `colors="#FF0000,#00FF00"` |
| `legend_position` | top, bottom, left, right, none | `legend_position="top"` |
| `animation` | true, false | `animation="false"` |

---

## 📁 Data Sources

### CSV Import

```
1. Prepare: Save as UTF-8, first row = headers
2. Upload: A-Tables & Charts → Create Table → Upload CSV
3. Configure: Set delimiter (comma, semicolon, tab)
4. Map columns: Set names and types
5. Import → Done!
```

**Tips:**
- Quote values with commas: `"Smith, John"`
- UTF-8 encoding for special characters
- Remove empty rows before import

### Excel Import

```
1. Prepare: .xlsx or .xls file, data in first sheet
2. Upload: Create Table → Upload Excel
3. Select sheet (if multiple)
4. Map columns
5. Import
```

**Tips:**
- Save formulas as values first
- Avoid merged cells
- Files < 10MB recommended

### Google Sheets

```
1. Make sheet public: Share → Anyone with link
2. Copy sheet URL
3. Create Table → Google Sheets → Paste URL
4. Enable auto-sync (optional)
5. Import
```

**Auto-Sync:** Data updates automatically!

### MySQL Database

```
1. Get credentials: Host, database, username, password
2. Create Table → MySQL → Enter credentials
3. Test connection
4. Select table
5. Import
```

**Enable auto-sync for live data!**

---

## 📈 Chart Types

| Type | Best For | Example Use |
|------|----------|-------------|
| **Bar** | Compare categories | Sales by product |
| **Column** | Trends over time | Monthly revenue |
| **Line** | Time series | Website traffic |
| **Area** | Cumulative totals | Total sales |
| **Pie** | Parts of whole | Market share |
| **Doughnut** | Parts of whole (modern) | Budget breakdown |
| **Scatter** | Correlations | Price vs quality |
| **Radar** | Multi-variable | Skills assessment |

---

## 🎨 Styling

### Custom CSS (Global)

```
Settings → Custom CSS → Add:

.atables-wrapper table {
    border: 2px solid #0073aa;
}

.atables-wrapper thead {
    background-color: #0073aa;
    color: white;
}
```

### Custom CSS (Per Table)

```
[atables id="1" class="my-custom-table"]

Then in Custom CSS:
.my-custom-table table {
    background-color: #f9f9f9;
}
```

---

## 💾 Export

### Enable Export Buttons

```
[atables id="1" export="true"]
```

**Formats:**
- CSV (spreadsheet-compatible)
- Excel (.xlsx format)
- PDF (printable)
- Print (browser print)

**Exports filtered data if search/filter active!**

---

## 🔍 Search & Sort

### Search

**Shortcode with pre-filled search:**
```
[atables id="1" search="laptop"]
```

**Users can search in real-time from search box**

### Sort

**Shortcode with default sort:**
```
[atables id="1" sort_column="price" sort_order="desc"]
```

**Users can click column headers to sort**

---

## 📱 Responsive

### Enable/Disable

```
[atables id="1" responsive="true"]  (default)
[atables id="1" responsive="false"] (disable)
```

### Responsive Modes

1. **Horizontal Scroll** (default) - Table scrolls on mobile
2. **Stacked Rows** - Columns stack vertically
3. **Column Hiding** - Hide less important columns on mobile

**Configure in table settings**

---

## 🔧 Common Tasks

### Re-Import Data

```
1. Edit table
2. Click "Re-import Data"
3. Upload new file
4. Choose: Replace, Merge, or Update
5. Import
```

### Duplicate Table

```
1. All Tables → Hover over table
2. Click "Duplicate"
3. Modify copy as needed
```

### Delete Table

```
1. All Tables → Hover over table
2. Click "Trash"
3. Trash → Permanently Delete (or Restore)
```

---

## ⚠️ Troubleshooting

### Table Doesn't Display

```
✓ Check shortcode ID matches table
✓ Table status = Published (not Draft)
✓ Clear cache (browser + plugin)
✓ Check for JavaScript errors (F12 → Console)
```

### Chart Doesn't Render

```
✓ Use [atables_chart id=X] not [atables id=X]
✓ Chart needs numeric data
✓ Clear cache
✓ Check browser console for errors
```

### Import Fails

```
✓ File size < server limit (check Site Health)
✓ CSV saved as UTF-8
✓ Excel file < 10MB
✓ Remove empty rows/columns
```

### Slow Performance

```
✓ Enable caching: Settings → Performance
✓ Use pagination (don't show 1000 rows at once)
✓ Disable features you don't need
✓ Optimize data source
```

---

## 🆘 Quick Fixes

### Clear All Caches

```
1. Settings → Performance → Clear Cache
2. Ctrl+Shift+R (hard reload browser)
3. Clear hosting cache (if applicable)
4. Purge CDN cache (if applicable)
```

### Reset Table Settings

```
1. Edit table
2. Display Settings → Reset to Defaults
3. Save table
```

### Check System Status

```
Dashboard → Site Health → Info → Server
Check: PHP version, memory limit, upload limit
```

---

## 💻 PHP Integration

### Display Table in PHP

```php
<?php echo do_shortcode('[atables id="1"]'); ?>
```

### Display Chart in PHP

```php
<?php echo do_shortcode('[atables_chart id="1"]'); ?>
```

### Get Table Data

```php
<?php
$table_id = 1;
$data = atables_get_table_data($table_id);
foreach ($data as $row) {
    echo $row['column_name'];
}
?>
```

---

## ⌨️ Keyboard Shortcuts (Admin)

| Shortcut | Action |
|----------|--------|
| `Ctrl+S` / `Cmd+S` | Save table/chart |
| `Ctrl+N` / `Cmd+N` | New table |
| `Esc` | Close modal/cancel |

---

## 📞 Support

**Documentation:** Full guides in `/documentation/` folder

**Common Issues:**
- Check [Troubleshooting Guide](TROUBLESHOOTING.md)
- Check [FAQ](FAQ.md)

**Contact Support:**
- Forum: https://codecanyon.net/item/support
- Email: support@example.com
- Response: Within 24 hours

**Include in Support Request:**
- WordPress version
- PHP version
- Plugin version
- Screenshots
- Error messages

---

## 🔗 Useful Links

- **Getting Started:** [01-GETTING-STARTED.md](01-GETTING-STARTED.md)
- **All Features:** [02-TABLE-FEATURES.md](02-TABLE-FEATURES.md)
- **Chart Guide:** [03-CHART-FEATURES.md](03-CHART-FEATURES.md)
- **Import Guide:** [04-IMPORT-GUIDE.md](04-IMPORT-GUIDE.md)
- **Shortcodes:** [SHORTCODE-REFERENCE.md](SHORTCODE-REFERENCE.md)
- **FAQ:** [FAQ.md](FAQ.md)
- **Troubleshooting:** [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

## 📝 Cheat Sheet

### Most Common Shortcodes

```
Basic table:
[atables id="1"]

Table with all options:
[atables id="1" rows="25" search="product" sort_column="price" sort_order="desc" export="true" class="my-table"]

Basic chart:
[atables_chart id="1"]

Chart with all options:
[atables_chart id="1" width="100%" height="500px" type="bar" legend_position="top" colors="#FF0000,#00FF00"]
```

### File Format Quick Check

```
CSV: first-row-headers.csv (UTF-8)
Excel: data-in-sheet1.xlsx (< 10MB)
JSON: array-of-objects.json (valid syntax)
XML: repeating-elements.xml (valid structure)
```

### Quick Workflow

```
Import → Configure → Display → Test → Customize
```

---

**Print this page for quick reference!**

**Last Updated:** October 31, 2025
**Version:** 1.0.0

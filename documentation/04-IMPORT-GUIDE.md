# Import Guide

Complete guide to importing data from various sources into A-Tables & Charts.

---

## 📋 Table of Contents

1. [Import Overview](#import-overview)
2. [CSV Import](#csv-import)
3. [Excel Import](#excel-import)
4. [MySQL Database](#mysql-database)
5. [JSON Import](#json-import)
6. [XML Import](#xml-import)
7. [Google Sheets](#google-sheets)
8. [WooCommerce Integration](#woocommerce-integration)
8. [Re-importing & Updating](#re-importing-updating)
10. [Troubleshooting Imports](#troubleshooting-imports)

---

## 📊 Import Overview

### Supported Data Sources

| Source | File Types | Auto-Sync | Best For |
|--------|------------|-----------|----------|
| **CSV** | .csv, .txt | ❌ | Simple data, Excel exports |
| **Excel** | .xlsx, .xls | ❌ | Formatted spreadsheets |
| **MySQL** | Database | ✅ | Large datasets, live data |
| **JSON** | .json | ❌ | API data, web apps |
| **XML** | .xml | ❌ | Structured documents |
| **Google Sheets** | URL | ✅ | Collaborative data |
| **WooCommerce** | Built-in | ✅ | Product catalogs |

### Import Process

**All imports follow these steps:**

1. **Select Source** → Choose file type or connection method
2. **Upload/Connect** → Upload file or enter credentials
3. **Configure** → Set encoding, delimiters, mapping
4. **Preview** → Verify data looks correct
5. **Import** → Create table with imported data
6. **Verify** → Check all data imported correctly

---

## 📄 CSV Import

### What is CSV?

**CSV = Comma-Separated Values**

Simple text format where:
- Each line = one row
- Commas separate columns
- First row = headers (recommended)

**Example CSV:**
```csv
Product,Price,Stock,Category
Laptop,999.99,50,Electronics
Mouse,29.99,200,Accessories
Keyboard,79.99,150,Accessories
```

### Step-by-Step Import

**Step 1: Prepare CSV File**

Best practices:
- ✅ Save as UTF-8 encoding
- ✅ Use first row for column headers
- ✅ Keep data consistent (same format per column)
- ✅ Remove empty rows
- ✅ Quote values containing commas: `"Smith, John"`

**Step 2: Upload File**

1. Go to **A-Tables & Charts → Add New Table**
2. Click **Upload CSV**
3. Drag file or click **Choose File**
4. Select your `.csv` file
5. Click **Open**

**Step 3: Configure Settings**

**Delimiter:**
- Comma (default): `Product,Price,Stock`
- Semicolon: `Product;Price;Stock`
- Tab: `Product    Price    Stock`
- Custom: Any character

**Encoding:**
- UTF-8 (recommended) - Supports all characters
- Windows-1252 - For older Windows files
- ISO-8859-1 - Latin characters
- Auto-detect - Plugin guesses

**First Row:**
- ✅ Has headers - Use first row as column names
- ❌ No headers - Auto-generate: Column 1, Column 2...

**Step 4: Map Columns**

Preview shows first 10 rows:

```
┌──────────┬────────┬────────┬─────────────┐
│ Product  │ Price  │ Stock  │ Category    │
├──────────┼────────┼────────┼─────────────┤
│ Laptop   │ 999.99 │ 50     │ Electronics │
│ Mouse    │ 29.99  │ 200    │ Accessories │
└──────────┴────────┴────────┴─────────────┘
```

For each column, set:
- **Name:** Display name
- **Type:** Text, Number, Currency, Date, etc.
- **Include:** ✅ Yes or ❌ Skip this column

**Step 5: Import**

1. Review settings
2. Click **Import Data**
3. Wait for progress bar (shows X rows imported)
4. Click **Continue to Table Editor**

### Common CSV Issues

**Issue 1: Special Characters Display as �**

**Cause:** Wrong encoding
**Fix:**
1. Open CSV in Notepad++ or similar
2. Encoding → Convert to UTF-8
3. Save and re-import

**Issue 2: Commas Break Columns**

**Example:**
```csv
WRONG: John Smith, Age: 25, Location: New York, NY
```
Looks like 4 columns but meant to be 1.

**Fix:**
```csv
RIGHT: "John Smith, Age: 25, Location: New York, NY"
```
Use quotes around values containing commas.

**Issue 3: Excel Opens CSV Wrong**

**Cause:** Excel uses system locale for CSV
**Fix:**
1. Don't double-click CSV
2. Open Excel first
3. File → Import → Text File
4. Choose delimiters manually

**Issue 4: Numbers Treated as Text**

**Example:** "001234" becomes "1234" or sorting is alphabetical

**Fix in CSV:**
- Keep as text: `="001234"` (Excel formula)
- Or fix in plugin: Set column type to "Number" during import

### Advanced CSV Options

**Skip Rows:**
- Skip first X rows (for headers/notes)
- Skip last X rows (for totals/notes)

**Row Limit:**
- Import only first 1000 rows
- Good for testing large files

**Date Parsing:**
- Auto-detect dates: 2024-10-31, 10/31/2024, Oct 31 2024
- Set format explicitly: YYYY-MM-DD, MM/DD/YYYY, DD/MM/YYYY

**Empty Values:**
- Leave blank
- Replace with: "N/A", "0", "-"

---

## 📗 Excel Import

### Supported Formats

- ✅ `.xlsx` - Excel 2007 and newer (recommended)
- ✅ `.xls` - Excel 97-2003 (legacy)
- ✅ `.xlsm` - Excel with macros (macros ignored)

### What Gets Imported

**Imported:**
- ✅ Cell values
- ✅ Formula results (not formulas themselves)
- ✅ Numbers, dates, text
- ✅ Merged cells (uses top-left value)

**NOT Imported:**
- ❌ Cell formatting (colors, bold, etc.)
- ❌ Charts/graphs
- ❌ Formulas (only calculated values)
- ❌ Macros/VBA code
- ❌ Conditional formatting rules
- ❌ Data validation rules
- ❌ Comments/notes

### Step-by-Step Import

**Step 1: Prepare Excel File**

Best practices:
- ✅ Put data in first sheet (or specify sheet name)
- ✅ Use first row for headers
- ✅ Remove empty rows/columns
- ✅ Avoid merged cells if possible
- ✅ Keep formatting simple
- ✅ Save complex formulas as values

**Step 2: Upload File**

1. Go to **A-Tables & Charts → Add New Table**
2. Click **Upload Excel**
3. Choose your `.xlsx` or `.xls` file
4. Click **Open**

**Step 3: Select Sheet**

If workbook has multiple sheets:
```
┌─────────────────────────┐
│ Select Sheet to Import  │
├─────────────────────────┤
│ ○ Sheet1 (100 rows)     │
│ ● Sales Data (500 rows) │ ← Selected
│ ○ Archive (200 rows)    │
└─────────────────────────┘
```

**Step 4: Configure Import**

**Data Range:**
- All rows: Import entire sheet
- Custom range: A1:F100
- Start from row: Skip header rows

**Column Detection:**
- Auto-detect types (numbers, dates, text)
- Or manually set per column

**Step 5: Preview & Import**

Same as CSV import (map columns, set types, import)

### Excel-Specific Features

**Date Handling:**

Excel stores dates as numbers (e.g., 45231 = 2023-10-31)

Plugin auto-converts:
- Excel number → Human date
- Preserves date format
- Timezone support

**Formula Handling:**

```
Excel: =SUM(A1:A10)
Imported: 1234.56 (calculated result)
```

**Merged Cells:**

```
Excel:
┌─────────────┬───────┐
│ Q1 Revenue  │ 10000 │
│             │       │ ← Empty (merged)
└─────────────┴───────┘

Imported:
┌─────────────┬───────┐
│ Q1 Revenue  │ 10000 │
│ Q1 Revenue  │ 10000 │ ← Duplicated
└─────────────┴───────┘
```

**Large Files:**

- Files > 50 MB: Might timeout
- Recommended: < 10,000 rows per import
- For huge files: Use MySQL import instead

### Troubleshooting Excel Import

**Error: "File too large"**
- **Fix:** Increase PHP upload limit (ask hosting)
- **Or:** Split into smaller files
- **Or:** Save as CSV (smaller file size)

**Error: "Invalid file format"**
- **Fix:** Make sure it's .xlsx or .xls
- **Not:** .xlsb, .xlsm, or other formats
- **Try:** Save As → Excel Workbook (.xlsx)

**Dates Import as Numbers**
- **Fix:** Set column type to "Date" during import
- **Or:** Reformat in Excel first

---

## 🗄️ MySQL Database

### When to Use MySQL Import

**Perfect for:**
- ✅ Large datasets (10,000+ rows)
- ✅ Frequently updated data
- ✅ Real-time synchronization
- ✅ Existing database integrations
- ✅ Multiple tables with relationships

**Advantages:**
- No file size limits
- Auto-updates (no re-importing)
- Fast performance
- Live data always current

### Prerequisites

**You'll need:**
1. **Database credentials:**
   - Host (usually `localhost` or IP address)
   - Database name
   - Username
   - Password
   - Port (usually 3306)

2. **Permissions:**
   - `SELECT` permission (required)
   - `SHOW TABLES` permission (helpful)

3. **Network access:**
   - Plugin must reach database server
   - Firewall rules allow connection

### Step-by-Step Connection

**Step 1: Get Credentials**

**From cPanel:**
1. Login to cPanel
2. MySQL Databases
3. Find database name
4. Create user (if needed)
5. Add user to database
6. Note credentials

**From hosting provider:**
- Contact support
- Ask for MySQL credentials
- They'll provide host, username, password, database

**Step 2: Connect in Plugin**

1. Go to **A-Tables & Charts → Add New Table**
2. Click **Connect to MySQL**
3. Enter credentials:

```
┌──────────────────────────────┐
│ Database Connection          │
├──────────────────────────────┤
│ Host:     localhost          │
│ Port:     3306               │
│ Database: my_database        │
│ Username: db_user            │
│ Password: ●●●●●●●●           │
└──────────────────────────────┘
[Test Connection] [Connect]
```

**Step 3: Test Connection**

Click **Test Connection**:
- ✅ Success: "Connected to database"
- ❌ Failed: Error message (see troubleshooting)

**Step 4: Select Table**

```
┌────────────────────────────────┐
│ Available Tables               │
├────────────────────────────────┤
│ ○ users (1,234 rows)           │
│ ○ orders (5,678 rows)          │
│ ● products (890 rows)          │ ← Selected
│ ○ customers (2,345 rows)       │
└────────────────────────────────┘
[Preview Data] [Import]
```

**Step 5: Configure Query**

**Basic Mode:**
- Import entire table
- All columns included

**Advanced Mode (SQL Query):**
```sql
SELECT
  product_name,
  price,
  stock,
  category
FROM products
WHERE active = 1
  AND stock > 0
ORDER BY product_name ASC
```

**Step 6: Map Columns**

Same as CSV/Excel:
- Preview first 10 rows
- Set column names
- Set data types
- Import

### Auto-Sync Feature

**Enable Automatic Updates:**

After import:
1. Edit table settings
2. Enable "Auto-Sync with Database"
3. Set interval:
   - Every 5 minutes
   - Every hour
   - Every 6 hours
   - Daily
   - Custom cron

**How it works:**
- Plugin runs query periodically
- Compares with current table data
- Updates changed rows
- Adds new rows
- Optionally deletes removed rows

**Perfect for:**
- Product inventories (stock updates)
- Real-time dashboards
- Live leaderboards
- Always-current data

### Security Best Practices

**1. Use Read-Only User:**
```sql
CREATE USER 'readonly_user'@'localhost' IDENTIFIED BY 'password';
GRANT SELECT ON database.* TO 'readonly_user'@'localhost';
```

**2. Limit Access:**
- Only grant SELECT (not INSERT, UPDATE, DELETE)
- Limit to specific tables if possible

**3. Secure Credentials:**
- Plugin encrypts credentials
- Stored in WordPress options (secure)
- Not exposed to frontend

**4. Use Localhost:**
- If WordPress and database on same server
- Faster + more secure than remote

### Troubleshooting MySQL

**Error: "Access denied"**
- **Cause:** Wrong username/password or no permission
- **Fix:** Verify credentials, check user permissions

**Error: "Can't connect to MySQL server"**
- **Cause:** Wrong host, port, or firewall blocking
- **Fix:** Verify host/port, check firewall rules, ask hosting

**Error: "Unknown database"**
- **Cause:** Database name wrong or doesn't exist
- **Fix:** Double-check database name (case-sensitive)

**Error: "Table doesn't exist"**
- **Cause:** Table name wrong or in different database
- **Fix:** Verify table name, check you're connected to right database

---

## 📝 JSON Import

### What is JSON?

**JSON = JavaScript Object Notation**

Structured data format used by APIs and web apps.

**Example JSON:**
```json
[
  {
    "product": "Laptop",
    "price": 999.99,
    "stock": 50,
    "category": "Electronics"
  },
  {
    "product": "Mouse",
    "price": 29.99,
    "stock": 200,
    "category": "Accessories"
  }
]
```

### Supported Structures

**Structure 1: Array of Objects** (Most Common)
```json
[
  {"name": "John", "age": 30, "city": "NYC"},
  {"name": "Jane", "age": 25, "city": "LA"}
]
```
→ 2 rows, 3 columns

**Structure 2: Object with Array**
```json
{
  "data": [
    {"name": "John", "age": 30},
    {"name": "Jane", "age": 25}
  ]
}
```
→ Plugin auto-detects "data" array

**Structure 3: Nested Objects**
```json
[
  {
    "name": "John",
    "address": {
      "city": "NYC",
      "zip": "10001"
    }
  }
]
```
→ Flattened to: name, address_city, address_zip

### Step-by-Step Import

**Step 1: Get JSON Data**

**From API:**
```bash
https://api.example.com/products
```
1. Visit URL in browser
2. Copy JSON response
3. Save as `.json` file

**From File:**
- Already have `.json` file
- From export, API download, etc.

**Step 2: Upload JSON**

1. Go to **A-Tables & Charts → Add New Table**
2. Click **Upload JSON**
3. Choose `.json` file
4. Click **Open**

**Or paste JSON directly:**
```
┌────────────────────────────────┐
│ Paste JSON:                    │
├────────────────────────────────┤
│ [{"name":"John","age":30}...]  │
│                                │
└────────────────────────────────┘
[Parse JSON]
```

**Step 3: Configure Structure**

Plugin auto-detects structure:

**If auto-detect succeeds:**
```
✓ Found array with 100 objects
✓ Detected 5 columns: name, email, phone, city, status
```

**If multiple arrays found:**
```
Select array to import:
○ users (100 items)
● products (50 items) ← Selected
○ orders (200 items)
```

**Step 4: Flatten Nested Data**

If JSON has nested objects:

```json
{
  "product": "Laptop",
  "specs": {
    "cpu": "i7",
    "ram": "16GB"
  }
}
```

**Flatten options:**
- ● Flatten: product, specs_cpu, specs_ram (3 columns)
- ○ Keep nested: product, specs (2 columns, specs as text)

**Step 5: Map & Import**

Same as other imports:
- Preview data
- Set column types
- Import

### API Integration

**Import from Live API:**

Instead of file upload:
1. Click **Import from URL**
2. Enter API endpoint:
   ```
   https://api.example.com/products.json
   ```
3. Optional: Add headers (for authentication)
   ```
   Authorization: Bearer your-api-key
   ```
4. Fetch & import

**Auto-Sync:**
- Enable periodic updates
- API data stays current
- Interval: 5min - 24hrs

### Troubleshooting JSON

**Error: "Invalid JSON"**
- **Cause:** Syntax error in JSON
- **Fix:** Validate at jsonlint.com
- **Common issues:**
  - Missing comma: `{"a": 1 "b": 2}` → `{"a": 1, "b": 2}`
  - Extra comma: `{"a": 1,}` → `{"a": 1}`
  - Single quotes: `{'a': 1}` → `{"a": 1}`

**Error: "No array found"**
- **Cause:** JSON is object not array
- **Fix:** Look for nested array, or wrap in array: `[{...}]`

**Nested Data Too Deep**
- **Cause:** Objects nested 3+ levels
- **Fix:** Flatten in external tool first, or use advanced options

---

## 📋 XML Import

### What is XML?

**XML = eXtensible Markup Language**

Structured format for documents and data.

**Example XML:**
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

### Supported Structures

**Structure 1: Repeating Elements**
```xml
<root>
  <item>
    <name>Value 1</name>
  </item>
  <item>
    <name>Value 2</name>
  </item>
</root>
```
→ Each `<item>` becomes a row

**Structure 2: Attributes**
```xml
<products>
  <product name="Laptop" price="999.99" stock="50"/>
  <product name="Mouse" price="29.99" stock="200"/>
</products>
```
→ Attributes become columns

**Structure 3: Mixed**
```xml
<product id="1">
  <name>Laptop</name>
  <price currency="USD">999.99</price>
</product>
```
→ Elements + attributes combined

### Step-by-Step Import

**Step 1: Upload XML File**

1. Go to **A-Tables & Charts → Add New Table**
2. Click **Upload XML**
3. Choose `.xml` file
4. Click **Open**

**Step 2: Select Repeating Element**

Plugin scans XML structure:

```
Found repeating elements:
○ <products> (1 instance)
● <product> (100 instances) ← Select this for rows
○ <category> (5 instances)
```

**Step 3: Map Elements to Columns**

```
XML Element     → Table Column
<name>          → product_name
<price>         → price
<stock>         → stock
@id (attribute) → product_id
```

**Step 4: Handle Nested Elements**

```xml
<product>
  <name>Laptop</name>
  <specs>
    <cpu>i7</cpu>
    <ram>16GB</ram>
  </specs>
</product>
```

**Options:**
- Flatten: name, cpu, ram (3 columns)
- Concatenate: name, specs (2 columns, specs = "i7, 16GB")
- Skip: Only import name (1 column)

**Step 5: Import**

Click Import, verify data.

### Common XML Sources

**RSS Feeds:**
```xml
<rss>
  <channel>
    <item>
      <title>Blog Post Title</title>
      <description>Post excerpt...</description>
      <link>https://...</link>
    </item>
  </channel>
</rss>
```
→ Import blog posts as table

**Product Feeds:**
- Shopping feeds (Google Merchant, etc.)
- Inventory exports
- Price lists

**Data Exports:**
- System reports
- Legacy data
- API responses

---

## 📊 Google Sheets

### Why Google Sheets?

**Advantages:**
- ✅ Collaborative editing (multiple people)
- ✅ Auto-sync (data updates automatically)
- ✅ No file uploads needed
- ✅ Easy for non-technical users
- ✅ Version history

**Perfect for:**
- Team-maintained data
- Frequently changing data
- External collaborators
- Client-provided data

### Step-by-Step Import

**Step 1: Prepare Google Sheet**

1. **Make sheet public:**
   - Click "Share" button
   - Change to: "Anyone with the link can view"
   - Or: Share with specific Google account

2. **Get shareable link:**
   - Click "Share" → "Copy link"
   - URL looks like:
     ```
     https://docs.google.com/spreadsheets/d/1ABC...XYZ/edit
     ```

**Step 2: Import in Plugin**

1. Go to **A-Tables & Charts → Add New Table**
2. Click **Google Sheets**
3. Paste shareable link:
   ```
   ┌─────────────────────────────────────────┐
   │ Google Sheets URL:                      │
   ├─────────────────────────────────────────┤
   │ https://docs.google.com/spreadsheets... │
   └─────────────────────────────────────────┘
   [Fetch Data]
   ```

**Step 3: Select Sheet Tab**

If workbook has multiple sheets:
```
Select sheet:
○ Main Data
● Product List ← Selected
○ Archive
```

**Step 4: Configure Range**

**Import entire sheet:**
- Default option

**Import specific range:**
- Range: `A1:F100`
- Sheet1!A1:F100
- Named ranges supported

**Step 5: Enable Auto-Sync**

```
☑ Auto-update from Google Sheets
Update interval: [Every hour ▼]
```

**Intervals:**
- Every 15 minutes (Premium)
- Every hour (default)
- Every 6 hours
- Daily

**Step 6: Import**

Click Import, preview, verify.

### Auto-Update Behavior

**How it works:**

1. Plugin checks Google Sheet every X hours
2. Compares with table data
3. Updates changed cells
4. Adds new rows
5. Optionally deletes removed rows

**Update Options:**

```
When Google Sheet changes:
☑ Update existing rows
☑ Add new rows
☐ Delete removed rows (careful!)
☑ Show update notification
```

### Private Sheets (Advanced)

**For private/business sheets:**

Need Google API key:

1. **Create API project:**
   - Google Cloud Console
   - Enable Google Sheets API

2. **Create credentials:**
   - Create API key or OAuth 2.0

3. **Enter in plugin:**
   - Settings → Integrations → Google Sheets
   - Enter API key
   - Authorize access

### Troubleshooting Google Sheets

**Error: "Access denied"**
- **Fix:** Make sheet public or share with plugin

**Error: "Sheet not found"**
- **Fix:** Check URL is correct, sheet not deleted

**Data not updating**
- **Fix:** Check auto-sync is enabled
- **Fix:** Manually click "Sync Now"
- **Fix:** Check update interval

---

## 🛒 WooCommerce Integration

### Built-In Product Import

**Import WooCommerce products directly:**

No file needed! Plugin connects to WooCommerce database.

### Step-by-Step Import

**Step 1: Start WooCommerce Import**

1. Go to **A-Tables & Charts → Add New Table**
2. Click **WooCommerce Products**
3. Plugin scans products

**Step 2: Select Data Fields**

Choose which product data to include:

```
Product Information:
☑ Product Name
☑ SKU
☑ Price
☑ Regular Price
☑ Sale Price
☐ Short Description
☐ Long Description
☑ Stock Status
☑ Stock Quantity
☑ Categories
☑ Tags
☐ Featured Image
☐ Gallery Images
☐ Weight
☐ Dimensions
☐ Custom Fields
```

**Step 3: Filter Products**

```
Filter by:
☑ Published products only
☐ Include drafts
☐ Include private

Product Type:
☑ Simple products
☑ Variable products (parent)
☐ Variations (individual)
☐ Grouped products
☐ External products

Categories:
☑ All categories
Or select specific: [Electronics ▼]

Stock Status:
○ All products
● In stock only
○ Out of stock only
○ On backorder
```

**Step 4: Import**

Click Import → Preview → Confirm

### Auto-Sync with WooCommerce

**Keep product table current:**

```
☑ Auto-update when products change
Trigger: [On product save ▼]
```

**Update triggers:**
- On product save (real-time)
- Every hour (scheduled)
- Daily (scheduled)
- Manual only

**What updates:**
- Price changes
- Stock quantity changes
- New products added
- Deleted products removed

### Product Variations

**Variable Products:**

```
Product: T-Shirt
Variations:
- T-Shirt (Small, Red)   $19.99
- T-Shirt (Medium, Blue) $19.99
- T-Shirt (Large, Green) $19.99
```

**Import options:**
- Parent only (1 row: "T-Shirt")
- All variations (3 rows, one per variation)
- Parent + variations (4 rows total)

### Custom Fields

**Import product meta:**

```
Custom Fields:
☑ _custom_field_1
☑ _manufacturer
☐ _internal_note
```

Useful for:
- Manufacturer
- Model number
- Custom attributes
- Internal SKUs

### Use Cases

**Product Catalog:**
```
[atables id="1" search_box="true" sort_column="price"]
```
→ Searchable, sortable product list

**Stock Dashboard:**
```
[atables id="2" sort_column="stock" sort_order="asc"]
```
→ Show low-stock items first

**Price List:**
```
[atables id="3" export="true"]
```
→ Exportable price list for customers

---

## 🔄 Re-importing & Updating

### Update Existing Table

**Scenario:** You have a table, data changed, need to update

**Method 1: Re-Import File**

1. Edit existing table
2. Click "Re-import Data"
3. Upload new file
4. Choose update method:
   - **Replace:** Delete old data, import new
   - **Merge:** Keep old + add new (based on key column)
   - **Update:** Update matching rows, add new

**Method 2: Auto-Sync** (MySQL, Google Sheets, WooCommerce)

- Enable auto-sync
- Data updates automatically
- No manual re-import needed

### Merge vs Replace

**Replace:**
```
Old data: [Row 1, Row 2, Row 3]
New data: [Row 4, Row 5]
Result:   [Row 4, Row 5]
```
→ Old data completely removed

**Merge:**
```
Old data: [Row 1, Row 2]
New data: [Row 3, Row 4]
Result:   [Row 1, Row 2, Row 3, Row 4]
```
→ Both old and new kept

**Update (requires key column):**
```
Old data: [ID:1, Name:John], [ID:2, Name:Jane]
New data: [ID:1, Name:Johnny], [ID:3, Name:Bob]
Result:   [ID:1, Name:Johnny], [ID:2, Name:Jane], [ID:3, Name:Bob]
```
→ Row 1 updated, Row 2 kept, Row 3 added

### Scheduled Imports

**Automate re-imports:**

1. Save import configuration
2. Set schedule (daily, weekly, etc.)
3. Plugin runs import automatically

**Perfect for:**
- Daily sales reports
- Weekly inventory updates
- Monthly analytics
- Automated data pipelines

---

## 🔧 Troubleshooting Imports

### General Issues

**Import Times Out**
- **Cause:** File too large or server timeout
- **Fix:** Increase PHP max_execution_time (ask hosting)
- **Or:** Split file into smaller chunks
- **Or:** Use MySQL import for large data

**Only Partial Data Imported**
- **Cause:** Memory limit reached
- **Fix:** Increase PHP memory_limit (ask hosting)
- **Or:** Import in batches

**Special Characters Look Wrong**
- **Cause:** Encoding mismatch
- **Fix:** Save file as UTF-8
- **Or:** Set encoding during import

### File-Specific Issues

**CSV:**
- Commas in data break columns → Use quotes
- Wrong delimiter → Try semicolon, tab
- Encoding issues → Save as UTF-8

**Excel:**
- File too large → Save as CSV instead
- Dates wrong → Set column type to Date
- Formulas not calculating → Save values only

**JSON:**
- Invalid JSON → Validate at jsonlint.com
- Can't find data → Check nested structure
- Too complex → Flatten first

**XML:**
- Can't find repeating element → Check structure
- Attributes missing → Enable attribute import
- Namespaces → May need to specify

### Performance Tips

**For Large Imports:**

1. **Increase limits:**
   - PHP memory_limit: 256M+
   - max_execution_time: 300+
   - post_max_size: 100M+
   - upload_max_filesize: 100M+

2. **Optimize data:**
   - Remove unnecessary columns
   - Clean empty rows
   - Simplify formulas (Excel)
   - Reduce file size

3. **Use right method:**
   - < 1,000 rows: CSV/Excel
   - 1,000-10,000 rows: CSV/Excel or MySQL
   - > 10,000 rows: MySQL (best performance)

---

## 📚 Related Documentation

- [Table Features](02-TABLE-FEATURES.md)
- [Getting Started](01-GETTING-STARTED.md)
- [FAQ](FAQ.md)
- [Troubleshooting](TROUBLESHOOTING.md)

---

**Updated:** October 2025
**Version:** 1.0.0

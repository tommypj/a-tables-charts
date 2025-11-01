# Getting Started with A-Tables & Charts

Welcome to A-Tables & Charts! This guide will help you get up and running in minutes.

---

## 📋 What You'll Learn

By the end of this guide, you'll be able to:
- ✅ Install and activate the plugin
- ✅ Create your first table
- ✅ Display the table on your website
- ✅ Create a chart from your table data
- ✅ Customize the appearance

**Estimated Time:** 15 minutes

---

## 1️⃣ Installation

### Method 1: Install from CodeCanyon (Recommended)

1. **Download the Plugin**
   - Log into your CodeCanyon account
   - Go to your Downloads page
   - Find "A-Tables & Charts"
   - Click "Download" → Select "Installable WordPress file only"
   - Save the `a-tables-charts.zip` file

2. **Upload to WordPress**
   - Log into your WordPress admin panel
   - Navigate to **Plugins → Add New**
   - Click **Upload Plugin** button (top of page)
   - Click **Choose File** and select `a-tables-charts.zip`
   - Click **Install Now**
   - Wait for installation to complete
   - Click **Activate Plugin**

3. **Verify Installation**
   - ✅ Look for "A-Tables & Charts" in your WordPress admin sidebar
   - ✅ You should see a welcome message
   - ✅ Database tables are created automatically

### Method 2: Manual Installation via FTP

1. **Extract the ZIP file**
   - Unzip `a-tables-charts.zip` on your computer
   - You'll see a folder named `a-tables-charts`

2. **Upload via FTP**
   - Connect to your site via FTP (FileZilla, etc.)
   - Navigate to `/wp-content/plugins/`
   - Upload the `a-tables-charts` folder
   - Ensure all files are uploaded

3. **Activate**
   - Go to WordPress Admin → Plugins
   - Find "A-Tables & Charts"
   - Click **Activate**

### Troubleshooting Installation

**Problem:** "The plugin does not have a valid header"
- **Solution:** Make sure you downloaded the "Installable WordPress file only" version, not the "All files & documentation"

**Problem:** "Upload: failed to write file to disk"
- **Solution:** Check your `wp-content/uploads/` folder permissions (should be 755)

**Problem:** White screen after activation
- **Solution:** Your server might not meet minimum requirements. Check PHP version (needs 7.4+)

---

## 2️⃣ System Requirements

Before installation, ensure your server meets these requirements:

### Minimum Requirements

| Component | Minimum Version |
|-----------|----------------|
| **PHP** | 7.4 or higher |
| **WordPress** | 5.8 or higher |
| **MySQL** | 5.6 or higher |
| **Memory Limit** | 64 MB (128 MB recommended) |
| **Max Upload Size** | 10 MB (50 MB recommended) |

### Recommended Requirements

| Component | Recommended Version |
|-----------|-------------------|
| **PHP** | 8.0 or higher |
| **WordPress** | 6.0 or higher |
| **MySQL** | 5.7 or higher |
| **Memory Limit** | 256 MB |
| **Max Upload Size** | 100 MB |

### PHP Extensions Required

- ✅ `json` - For data handling
- ✅ `mbstring` - For UTF-8 support
- ✅ `xml` - For XML imports
- ✅ `zip` - For Excel imports

### How to Check Your Server

1. Go to **WordPress Admin → Tools → Site Health**
2. Click **Info** tab
3. Expand **Server** section
4. Check PHP version and extensions

---

## 3️⃣ First Time Setup

### Welcome Screen

After activation, you'll see the Welcome screen:

![Welcome Screen](screenshots/welcome-screen.png)

**Options:**
1. **Take a Quick Tour** - 2-minute walkthrough
2. **Create Your First Table** - Jump right in
3. **View Documentation** - You're here!
4. **Watch Video Tutorial** - Visual guide

💡 **Tip:** We recommend taking the Quick Tour if this is your first time!

### Dashboard Overview

The main dashboard shows:

1. **Statistics**
   - Total Tables: 0
   - Total Charts: 0
   - Total Views: 0

2. **Quick Actions**
   - Create New Table
   - Import from File
   - View All Tables
   - Settings

3. **Recent Tables**
   - Your most recently edited tables
   - Quick access to edit/delete

![Dashboard](screenshots/dashboard.png)

---

## 4️⃣ Creating Your First Table

Let's create a simple table in 5 minutes!

### Step 1: Choose "Create Table"

1. Click **A-Tables & Charts** in the sidebar
2. Click **Create New Table** button
3. You'll see the Table Creation Wizard

### Step 2: Select Data Source

Choose how you want to add data:

**Option A: Upload CSV File** (Easiest for beginners)
- Click **Upload CSV**
- Drag and drop your CSV file
- Or click **Choose File** to browse

**Option B: Upload Excel File**
- Supports `.xlsx` and `.xls` formats
- Click **Upload Excel**
- Select your file

**Option C: Manual Entry**
- Click **Create Empty Table**
- Add rows manually
- Good for small datasets

**Option D: Connect to MySQL**
- For advanced users
- Connect to external databases
- Requires database credentials

💡 **Tip:** Start with CSV for your first table. It's the easiest!

### Step 3: Configure Your Table

After uploading, you'll configure:

**Table Name**
- Give your table a descriptive name
- Example: "Sales Report 2024"
- Used internally only (not shown on frontend)

**Table Description** (Optional)
- Add notes about the data
- Helps you remember what this table is for

**Column Headers**
- Automatically detected from your file
- You can rename them
- Example: "product_name" → "Product Name"

**Data Preview**
- See the first 10 rows
- Verify data imported correctly
- Check for formatting issues

### Step 4: Display Settings

Configure how the table looks on your website:

**Pagination**
- ✅ Enable pagination (recommended for large tables)
- Rows per page: 10, 25, 50, 100
- Show/hide "Showing X of Y entries"

**Search**
- ✅ Enable search box
- Let users filter data
- Real-time search (instant results)

**Sorting**
- ✅ Enable column sorting
- Click headers to sort
- Ascending/descending

**Responsive Design**
- ✅ Enable responsive (highly recommended)
- Tables adapt to mobile screens
- Columns stack on small devices

**Styling**
- Choose color scheme
- Stripe rows (alternating colors)
- Border style
- Hover effects

### Step 5: Save & Get Shortcode

1. Click **Save Table** button
2. Wait for "Table saved successfully!" message
3. Copy the shortcode: `[atables id=1]`
4. You're done! 🎉

---

## 5️⃣ Displaying Your Table

### Add to a Post or Page

**Method 1: Shortcode (Classic Editor)**

1. Edit any post or page
2. Paste the shortcode: `[atables id=1]`
3. Click **Update** or **Publish**
4. View your page - table will appear!

**Method 2: Gutenberg Block Editor**

1. Edit any post or page
2. Click **(+)** to add a block
3. Search for "Shortcode"
4. Select **Shortcode** block
5. Paste: `[atables id=1]`
6. Click **Publish**

**Method 3: Widget (Sidebar/Footer)**

1. Go to **Appearance → Widgets**
2. Add **Text** or **HTML** widget
3. Paste shortcode: `[atables id=1]`
4. Save widget

**Method 4: PHP Template**

Add to your theme files:

```php
<?php echo do_shortcode('[atables id=1]'); ?>
```

### Verify It Works

1. Visit your page in a new browser tab
2. You should see your table
3. Test sorting (click column headers)
4. Test search (if enabled)
5. Test pagination (if enabled)

✅ **Success Indicators:**
- Table displays with all data
- Styling looks professional
- Search works (if enabled)
- Sorting works (click headers)
- Pagination works (if enabled)
- Mobile-responsive (test on phone)

⚠️ **Troubleshooting:**
- **Table doesn't show:** Check shortcode ID matches your table
- **Styling is broken:** Clear your cache (plugin + browser)
- **Some data is missing:** Check original file for errors
- **Search not working:** JavaScript might be disabled/conflicting

---

## 6️⃣ Creating Your First Chart

Now let's visualize your data with a chart!

### Step 1: Open Your Table

1. Go to **A-Tables & Charts → All Tables**
2. Find your table
3. Click **Create Chart** button

### Step 2: Select Chart Type

Choose from 8 chart types:

**1. Bar Chart** 📊
- Compare values across categories
- Good for: Sales by product, scores by student
- Horizontal bars

**2. Column Chart** 📊
- Like bar chart but vertical
- Good for: Monthly sales, yearly comparisons
- Vertical bars

**3. Line Chart** 📈
- Show trends over time
- Good for: Website traffic, stock prices
- Connected points

**4. Area Chart** 📈
- Like line chart with filled area below
- Good for: Cumulative data, volume over time
- Shaded area

**5. Pie Chart** 🥧
- Show proportions of a whole
- Good for: Market share, budget allocation
- Circular slices

**6. Doughnut Chart** 🍩
- Like pie chart with hole in center
- Good for: Same as pie, modern look
- Ring shape

**7. Scatter Chart** 📉
- Show relationship between two variables
- Good for: Correlation analysis, data distribution
- Point plot

**8. Radar Chart** 🎯
- Compare multiple variables
- Good for: Skill assessments, product comparisons
- Spider web shape

💡 **Tip:** Not sure? Start with **Bar Chart** - it's the most versatile!

### Step 3: Configure Chart Data

**Chart Title**
- Give your chart a name
- Example: "2024 Sales by Product"
- This WILL show on the frontend

**Select Data Columns**
- **X-Axis:** Categories (Product names, months, etc.)
- **Y-Axis:** Values (Sales numbers, scores, etc.)
- Preview updates automatically

**Data Range** (Optional)
- Include all rows, or select specific rows
- Filter out outliers
- Focus on recent data only

### Step 4: Styling Options

**Colors**
- Choose color scheme
- Preset palettes available
- Or pick custom colors
- Each data series can have different color

**Size**
- Width: 100% (responsive) or fixed pixels
- Height: 400px default (adjustable)

**Legend**
- Show/hide legend
- Position: top, bottom, left, right
- Legend labels

**Grid Lines**
- Show/hide grid
- Customize grid color
- Adjust spacing

### Step 5: Save Chart

1. Click **Save Chart** button
2. Copy the shortcode: `[atables_chart id=1]`
3. Add to any post/page (same method as tables)

### Display Your Chart

Same methods as displaying tables:

```
[atables_chart id=1]
```

✅ **Chart displays correctly:**
- Renders on page load
- Data is accurate
- Colors look good
- Legend is readable
- Responsive on mobile
- Interactive (hover shows values)

---

## 7️⃣ Next Steps

Congratulations! You've successfully:
- ✅ Installed the plugin
- ✅ Created your first table
- ✅ Displayed it on your website
- ✅ Created your first chart

### What to Learn Next

**Beginner Level:**
- [Customizing Table Styles](02-TABLE-FEATURES.md)
- [Adding Filters to Tables](03-SEARCH-FILTER.md)
- [Export Your Tables](04-EXPORT.md)

**Intermediate Level:**
- [Import from Excel](05-IMPORT-EXCEL.md)
- [Connect to MySQL Database](06-MYSQL.md)
- [Advanced Chart Options](07-ADVANCED-CHARTS.md)

**Advanced Level:**
- [Custom CSS Styling](08-CUSTOM-CSS.md)
- [Performance Optimization](09-PERFORMANCE.md)
- [Developer Hooks](10-DEVELOPER-GUIDE.md)

### Get Help

**Need assistance?**
- 📖 Read the [FAQ](FAQ.md)
- 🔍 Check [Troubleshooting Guide](TROUBLESHOOTING.md)
- 💬 Visit [Support Forum](https://codecanyon.net/item/support)
- 📧 Email: support@yourdomain.com
- 📹 Watch [Video Tutorials](https://youtube.com/your-channel)

---

## Quick Reference

### Common Shortcodes

```
Display table:          [atables id=1]
Display chart:          [atables_chart id=1]
Table with custom rows: [atables id=1 rows=20]
Chart with custom size: [atables_chart id=1 width=800 height=600]
```

### Keyboard Shortcuts (Admin)

| Shortcut | Action |
|----------|--------|
| `Ctrl/Cmd + S` | Save table/chart |
| `Ctrl/Cmd + N` | New table |
| `Esc` | Close modal |

---

**Ready to explore more features? Continue to [Table Features Guide](02-TABLE-FEATURES.md) →**

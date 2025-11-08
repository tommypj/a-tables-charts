# Gutenberg Blocks User Guide
**A-Tables & Charts for WordPress**

This guide explains how to use the custom Gutenberg blocks for displaying tables and charts in the WordPress block editor.

---

## Overview

The A-Tables & Charts plugin provides two custom Gutenberg blocks:

1. **A-Tables Table Block** - Display any table created in the plugin
2. **A-Tables Chart Block** - Display any chart created in the plugin

These blocks provide a modern, visual way to add tables and charts to your content without manually typing shortcodes.

---

## Table of Contents

1. [Installation & Activation](#installation--activation)
2. [Adding a Table Block](#adding-a-table-block)
3. [Adding a Chart Block](#adding-a-chart-block)
4. [Block Settings](#block-settings)
5. [Alignment Options](#alignment-options)
6. [Live Preview](#live-preview)
7. [Troubleshooting](#troubleshooting)
8. [Technical Details](#technical-details)

---

## Installation & Activation

The Gutenberg blocks are automatically available once you activate the A-Tables & Charts plugin. No additional setup required!

**Requirements:**
- WordPress 5.0 or higher (Gutenberg editor)
- A-Tables & Charts plugin activated
- At least one table or chart created in the plugin

---

## Adding a Table Block

### Method 1: Using the Block Inserter

1. **Open the Block Editor** on any post or page
2. **Click the (+) icon** to open the block inserter
3. **Search for "table"** in the search box
4. **Click "A-Tables Table"** to add the block

### Method 2: Using Slash Command

1. Type `/table` in the editor
2. Select **"A-Tables Table"** from the suggestions

### Method 3: Using the Widgets Category

1. Click the (+) icon to open the block inserter
2. Click the **"Widgets"** category
3. Find and click **"A-Tables Table"**

### Selecting Your Table

Once the block is added:

1. You'll see a **dropdown menu** with all your available tables
2. **Select the table** you want to display
3. The table will appear as a **live preview** in the editor

**Example:**
```
┌─────────────────────────────────┐
│  Select a table...              │
│  ▼ Product Catalog              │
│     Employee Directory          │
│     Sales Data Q1 2024          │
└─────────────────────────────────┘
```

---

## Adding a Chart Block

### Method 1: Using the Block Inserter

1. **Open the Block Editor** on any post or page
2. **Click the (+) icon** to open the block inserter
3. **Search for "chart"** in the search box
4. **Click "A-Tables Chart"** to add the block

### Method 2: Using Slash Command

1. Type `/chart` in the editor
2. Select **"A-Tables Chart"** from the suggestions

### Selecting Your Chart

Once the block is added:

1. You'll see a **dropdown menu** with all your available charts
2. **Select the chart** you want to display
3. The chart will appear as a **live preview** in the editor

**Example:**
```
┌─────────────────────────────────┐
│  Select a chart...              │
│  ▼ Sales Trend 2024             │
│     Revenue by Region           │
│     Customer Growth             │
└─────────────────────────────────┘
```

---

## Block Settings

Each block has settings available in the **Inspector Panel** (right sidebar).

### Accessing Block Settings

1. **Click on the block** in the editor
2. Look at the **right sidebar** (Inspector Panel)
3. You'll see the **Table Settings** or **Chart Settings** panel

### Available Settings

**Table Block Settings:**
- **Select Table** - Dropdown to change which table is displayed
- Help text: "Choose which table to display in this block."

**Chart Block Settings:**
- **Select Chart** - Dropdown to change which chart is displayed
- Help text: "Choose which chart to display in this block."

---

## Alignment Options

Both blocks support WordPress alignment options:

### Available Alignments

| Alignment | Description | CSS Class |
|-----------|-------------|-----------|
| **None** | Default width (follows content width) | - |
| **Wide** | Wider than content, respects theme's wide width | `alignwide` |
| **Full Width** | Full width of the screen | `alignfull` |

### How to Change Alignment

1. **Select the block**
2. Click the **alignment icon** in the block toolbar (top)
3. Choose: **Wide width** or **Full width**

**Visual Example:**
```
┌────────────────────────────────────┐
│ [=] [≡≡] [≡≡≡]  ← Alignment options
└────────────────────────────────────┘
   ↑    ↑     ↑
  None Wide  Full
```

---

## Live Preview

### What is Live Preview?

The blocks show a **real-time preview** of your table or chart as it will appear on the front-end.

**Features:**
- ✅ See exact table/chart appearance
- ✅ Updates when you select a different table/chart
- ✅ Matches front-end styling
- ✅ Preview is "locked" (can't interact) to prevent accidental edits

### Preview Behavior

**Table Preview:**
- Shows actual table data
- Displays headers and rows
- Applies table styling
- Shows search/pagination controls (not functional in editor)

**Chart Preview:**
- Shows actual chart visualization
- Displays correct chart type (bar, line, pie, etc.)
- Renders with Google Charts library
- Shows chart title and data

**Note:** The preview is slightly faded and can't be clicked - this is intentional to prevent confusion.

---

## Troubleshooting

### Block doesn't appear in inserter

**Problem:** Can't find "A-Tables Table" or "A-Tables Chart" blocks

**Solutions:**
1. ✅ Make sure the plugin is activated
2. ✅ Refresh the page editor (Ctrl+R or Cmd+R)
3. ✅ Try searching for "atables" instead
4. ✅ Check that you're using the **Block Editor** (not Classic Editor)

### Dropdown is empty

**Problem:** "Select a table..." dropdown has no options

**Solutions:**
1. ✅ Create at least one table in **A-Tables → All Tables**
2. ✅ Make sure the table is **saved** (not draft)
3. ✅ Refresh the editor page
4. ✅ Check that the table exists in the database

### Preview shows "Please select a table"

**Problem:** Preview doesn't show the table

**Solutions:**
1. ✅ Select a table from the dropdown
2. ✅ Make sure the selected table still exists
3. ✅ Try selecting a different table
4. ✅ Save and refresh the page

### Block shows error on front-end

**Problem:** Front-end shows error instead of table/chart

**Solutions:**
1. ✅ Verify the table/chart exists (check admin)
2. ✅ Re-select the table/chart in the block
3. ✅ Save the post again
4. ✅ Clear any caching plugins

---

## Technical Details

### Block Names

- **Table Block:** `atables/table`
- **Chart Block:** `atables/chart`

### Shortcode Compatibility

The blocks use the existing shortcodes internally:

| Block | Equivalent Shortcode |
|-------|---------------------|
| Table Block | `[atables id="X"]` |
| Chart Block | `[atables_chart id="X"]` |

This means:
- ✅ Blocks and shortcodes can be used together
- ✅ Switching from shortcode to block is seamless
- ✅ All shortcode features work in blocks
- ✅ Existing content with shortcodes still works

### Server-Side Rendering

Both blocks use **server-side rendering**:

**Benefits:**
- ✅ Preview matches front-end exactly
- ✅ No JavaScript required on front-end
- ✅ SEO friendly
- ✅ Works with caching plugins

**What this means:**
- The block saves the table/chart ID only
- WordPress renders the actual content on each page load
- This ensures consistency between editor and front-end

### Browser Compatibility

The blocks work in all modern browsers:

| Browser | Version | Status |
|---------|---------|--------|
| Chrome | Latest | ✅ Supported |
| Firefox | Latest | ✅ Supported |
| Safari | Latest | ✅ Supported |
| Edge | Latest | ✅ Supported |
| Internet Explorer | Any | ❌ Not supported |

### Block Attributes

**Table Block:**
```json
{
  "tableId": {
    "type": "number",
    "default": 0
  }
}
```

**Chart Block:**
```json
{
  "chartId": {
    "type": "number",
    "default": 0
  }
}
```

### CSS Classes

**Block Wrapper:**
- `.atables-block-wrapper` - Main container
- `.atables-block-notice` - Notice when no table/chart selected

**Alignment Classes:**
- `.alignwide` - Wide alignment
- `.alignfull` - Full width alignment

---

## Example Use Cases

### Use Case 1: Product Catalog Page

**Goal:** Display product table on a landing page

**Steps:**
1. Create product table in **A-Tables → All Tables**
2. Create new page: **Products**
3. Add **A-Tables Table** block
4. Select **Product Catalog** from dropdown
5. Set alignment to **Wide width**
6. Add other content blocks around it
7. Publish page

### Use Case 2: Sales Report Post

**Goal:** Show sales chart in monthly report

**Steps:**
1. Create chart in **A-Tables → Charts**
2. Create new post: **March 2024 Sales Report**
3. Add intro paragraph
4. Add **A-Tables Chart** block
5. Select **Monthly Sales** from dropdown
6. Add analysis paragraphs below
7. Publish post

### Use Case 3: Dashboard Page

**Goal:** Multiple tables and charts on one page

**Steps:**
1. Create page: **Dashboard**
2. Add **heading** "Sales Overview"
3. Add **A-Tables Chart** block → Select sales chart
4. Add **heading** "Product Performance"
5. Add **A-Tables Table** block → Select product table
6. Add **heading** "Customer Data"
7. Add **A-Tables Table** block → Select customer table
8. Publish page

---

## FAQ

### Q: Can I use both blocks and shortcodes?
**A:** Yes! They work side-by-side. Use whichever method you prefer.

### Q: Will blocks work with Classic Editor?
**A:** No, blocks require the Gutenberg block editor. Use shortcodes with Classic Editor.

### Q: Can I customize block styles?
**A:** Yes, via CSS. Target `.atables-block-wrapper` or the specific table/chart classes.

### Q: Do blocks work with page builders?
**A:** Depends on the page builder. Most modern builders support Gutenberg blocks.

### Q: What happens if I delete a table used in a block?
**A:** The block will show "Table not found" message. Select a different table.

### Q: Can I nest blocks inside other blocks?
**A:** Yes, the blocks work inside columns, groups, and other container blocks.

---

## Support

If you encounter issues:

1. **Check this guide** for troubleshooting tips
2. **Check the plugin documentation** at https://docs.your-plugin-site.com
3. **Contact support** via the plugin support forum

---

**Last Updated:** 2025-11-01
**Plugin Version:** 1.0.0
**WordPress Version:** 5.0+

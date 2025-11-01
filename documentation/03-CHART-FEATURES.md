# Chart Features Guide

Complete guide to creating and customizing charts in A-Tables & Charts.

---

## 📋 Table of Contents

1. [Chart Types Overview](#chart-types-overview)
2. [Creating Charts](#creating-charts)
3. [Bar Charts](#bar-charts)
4. [Column Charts](#column-charts)
5. [Line Charts](#line-charts)
6. [Area Charts](#area-charts)
7. [Pie Charts](#pie-charts)
8. [Doughnut Charts](#doughnut-charts)
9. [Scatter Charts](#scatter-charts)
10. [Radar Charts](#radar-charts)
11. [Chart Styling](#chart-styling)
12. [Colors & Themes](#colors-themes)
13. [Legends & Labels](#legends-labels)
14. [Animations](#animations)
15. [Responsive Charts](#responsive-charts)
16. [Advanced Options](#advanced-options)

---

## 📊 Chart Types Overview

### Quick Comparison

| Chart Type | Best For | Data Structure | Use Cases |
|------------|----------|----------------|-----------|
| **Bar** | Comparing categories | Categories + values | Sales by product, scores by student |
| **Column** | Showing trends | Time periods + values | Monthly revenue, yearly growth |
| **Line** | Time series data | Sequential data | Stock prices, website traffic |
| **Area** | Cumulative trends | Sequential data | Total sales over time, volume |
| **Pie** | Part-to-whole | Categories + percentages | Market share, budget breakdown |
| **Doughnut** | Part-to-whole (modern) | Categories + percentages | Same as pie, with center space |
| **Scatter** | Correlations | X/Y coordinates | Height vs weight, price vs quality |
| **Radar** | Multi-variable comparison | Multiple metrics | Skills assessment, product features |

### Choosing the Right Chart Type

**Ask yourself:**

1. **Am I comparing categories?** → Bar or Column
2. **Am I showing change over time?** → Line or Area
3. **Am I showing parts of a whole?** → Pie or Doughnut
4. **Am I showing relationships between two variables?** → Scatter
5. **Am I comparing multiple variables across categories?** → Radar

---

## 🎨 Creating Charts

### Step-by-Step Process

**Step 1: Choose Source Table**
1. Go to **A-Tables & Charts → Charts → Add New**
2. Select existing table as data source
3. Or create new table first

**Step 2: Select Chart Type**
- Choose from 8 chart types
- Preview updates in real-time
- Can change type later

**Step 3: Configure Data**
- Select X-axis column (labels/categories)
- Select Y-axis column(s) (values)
- Filter rows (optional)
- Select data range

**Step 4: Customize Appearance**
- Choose colors
- Set chart title
- Configure legend
- Adjust size

**Step 5: Save & Display**
- Click Save
- Copy shortcode: `[atables_chart id=X]`
- Add to any page/post

---

## 📊 Bar Charts

### When to Use

**Perfect for:**
- ✅ Comparing values across categories
- ✅ Ranking items (top 10 products)
- ✅ Survey results
- ✅ Side-by-side comparisons

**Example Use Cases:**
- Sales by product category
- Test scores by student
- Population by country
- Revenue by sales rep

### Configuration Options

**Orientation:**
- Horizontal bars (default)
- Left-to-right layout
- Categories on Y-axis
- Values on X-axis

**Bar Spacing:**
- Narrow: 10% gap
- Normal: 20% gap (default)
- Wide: 30% gap
- Custom percentage

**Value Axis:**
- Start from zero: Yes/No
- Max value: Auto or custom
- Grid lines: Show/hide
- Axis label: Custom text

**Category Axis:**
- Labels: Full text, truncated, rotated
- Order: Original, alphabetical, by value
- Reverse: Highest first

### Multiple Data Series

**Compare Multiple Datasets:**

```
Product Sales vs Target
━━━━━━━━━━━━━━━━━━━━
Laptops    ████████████ Actual: 120
           ███████░░░░░ Target: 100

Phones     ██████░░░░░░ Actual: 80
           ████████████ Target: 120
```

**Configuration:**
- Grouped bars (side-by-side)
- Stacked bars (on top of each other)
- 100% stacked (proportional)

### Styling Options

**Bar Colors:**
- Single color (all bars same)
- Gradient (light to dark)
- Multi-color (each bar different)
- Conditional (based on value)

**Bar Borders:**
- Color
- Width (1-5px)
- Rounded corners

**Grid Lines:**
- Horizontal: Yes/No
- Vertical: Yes/No
- Color and opacity
- Dashed or solid

---

## 📊 Column Charts

### When to Use

**Perfect for:**
- ✅ Showing trends over time
- ✅ Monthly/yearly comparisons
- ✅ Sequential data
- ✅ Progress tracking

**Example Use Cases:**
- Monthly sales 2024
- Quarterly revenue
- Daily website visitors
- Yearly growth rates

### Configuration Options

**Orientation:**
- Vertical columns
- Bottom-to-top layout
- Categories on X-axis
- Values on Y-axis

**Column Width:**
- Thin: 40% of category width
- Normal: 60% (default)
- Wide: 80%
- Custom percentage

**Value Axis:**
- Start from zero: Recommended
- Max value: Auto-scale or fixed
- Grid lines: Horizontal
- Axis title: "Revenue", "Count", etc.

**Category Axis:**
- Labels: "Jan", "Feb", etc.
- Rotation: 0°, 45°, 90°
- Font size: Auto or custom

### Time Series Data

**Ideal for Date-Based Data:**

```
Monthly Revenue 2024
━━━━━━━━━━━━━━━━━━━━
  ┃     █
$5K┃     █   █
  ┃ █   █   █   █
$3K┃ █   █   █   █
  ┃ █ █ █ █ █ █ █
$1K┃ █ █ █ █ █ █ █
  ┗━┻━┻━┻━┻━┻━┻━┻━
   Jan Feb Mar Apr
```

**Features:**
- Auto-detect dates
- Format: Month, Quarter, Year
- Skip missing periods
- Trend lines (optional)

### Multiple Series

**Compare Multiple Metrics:**
- Revenue vs Expenses
- This Year vs Last Year
- Actual vs Forecast
- Product A vs Product B

**Display Options:**
- Grouped (side-by-side)
- Stacked (cumulative)
- 100% stacked (percentage)

---

## 📈 Line Charts

### When to Use

**Perfect for:**
- ✅ Continuous data over time
- ✅ Trends and patterns
- ✅ Forecasting
- ✅ Stock prices, analytics

**Example Use Cases:**
- Website traffic over time
- Stock prices
- Temperature changes
- Sales trends

### Configuration Options

**Line Style:**
- Straight lines (angular)
- Smooth curves (bezier)
- Stepped (staircase)
- Dotted or dashed

**Line Width:**
- Thin: 1px
- Normal: 2px (default)
- Thick: 3-5px

**Point Markers:**
- Show: Circle, square, triangle
- Hide: Line only
- Size: 2-10px
- Hollow or filled

**Fill Below Line:**
- No fill (line only)
- Gradient fill
- Solid color (becomes area chart)
- Opacity: 0-100%

### Multiple Lines

**Compare Multiple Trends:**

```
Website Traffic 2024
━━━━━━━━━━━━━━━━━━━━
5K┃        ╱╲
  ┃    ╱╲ ╱  ╲  Desktop
3K┃   ╱  ╲╱    ╲╱
  ┃  ╱
1K┃ ╱    ┌─┐
  ┃╱   ┌─┘ └─┐  Mobile
  ┗━━━━┻━━━━┻━━━━
   Jan  Mar  May
```

**Features:**
- Multiple datasets
- Different colors per line
- Different line styles
- Independent Y-axes (optional)

### Time-Based Features

**Zoom & Pan:**
- Zoom into date range
- Pan left/right
- Reset to full view

**Date Formatting:**
- Auto-format dates
- Custom date labels
- Skip weekends (optional)
- Timezone support

---

## 📊 Area Charts

### When to Use

**Perfect for:**
- ✅ Cumulative totals
- ✅ Volume over time
- ✅ Stacked categories
- ✅ Emphasizing magnitude

**Example Use Cases:**
- Total sales accumulation
- Market share over time
- Resource usage
- Budget allocation

### Configuration Options

**Fill Style:**
- Solid color
- Gradient (top to bottom)
- Pattern (for print)
- Opacity: 20-80%

**Stacking:**
- Individual areas (overlapping)
- Stacked areas (cumulative)
- 100% stacked (proportional)

**Border:**
- Show line on top: Yes/No
- Line color: Same as fill or different
- Line width: 1-3px

### Stacked vs Overlapping

**Stacked (Recommended):**
```
Total Traffic
━━━━━━━━━━━━━━
    ╱▀▀▀▀▀▀▀▀▀╲  Desktop
   ╱▓▓▓▓▓▓▓▓▓▓▓╲
  ╱▓▓▓▓▓▓▓▓▓▓▓▓▓╲ Mobile
 ╱▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒╲
╱▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒╲ Tablet
```
- Shows total + breakdown
- No overlapping
- Clear proportions

**Overlapping:**
```
Separate Metrics
━━━━━━━━━━━━━━
    ╱▀▀▀▀╲      Desktop
   ╱░░░░░░▀▀╲
  ╱░░░░░░░░░░╲  Mobile
 ╱▒▒▒▒▒▒▒▒▒▒▒▒╲
```
- Independent metrics
- Can overlap
- Good for comparisons

### Time Range Selection

**Interactive Features:**
- Brush to select range
- Show only selected period
- Compare time periods
- Export selected range

---

## 🥧 Pie Charts

### When to Use

**Perfect for:**
- ✅ Part-to-whole relationships
- ✅ Percentages and proportions
- ✅ Simple categorical data (3-7 categories)
- ✅ Budget breakdowns

**Example Use Cases:**
- Market share by company
- Budget by department
- Sales by region
- Survey responses

**⚠️ Avoid pie charts when:**
- More than 7 categories (use bar chart)
- Precise values needed (use table)
- Comparing multiple datasets

### Configuration Options

**Slice Settings:**
- Start angle: 0° (top), 90° (right)
- Direction: Clockwise or counterclockwise
- Spacing: Gap between slices
- Exploded slices: Pull out for emphasis

**Labels:**
- Position: Inside, outside, or both
- Show: Percentage, value, label, or all
- Font size: Auto or custom
- Connector lines: For outside labels

**Visual Style:**
- 2D (flat, modern)
- 3D (depth effect, legacy)
- Shadow: Yes/No
- Border: Show/hide

### Slice Actions

**Interactive Features:**
- Click to explode slice
- Hover to highlight
- Tooltip shows details
- Click to filter table

**Example:**
```
Market Share 2024
━━━━━━━━━━━━━━━━
     ╱─────╲
    │ Comp A │ 35%
    │───────│
   │ Comp B  │ 25%
   │─────────│
  │  Comp C   │ 40%
  ╲──────────╱
```

### Data Requirements

**Minimum:**
- 2 categories
- Values must be positive
- Values will be converted to percentages

**Maximum:**
- 10 categories (but 3-7 recommended)
- Small slices (<5%) grouped into "Other"

---

## 🍩 Doughnut Charts

### When to Use

**Same as Pie Charts, but:**
- ✅ Modern, clean look
- ✅ Center space for additional info
- ✅ Less visual weight
- ✅ Better for multiple rings

**Example Use Cases:**
- Same as pie charts
- Plus: Progress indicators (75% complete)
- Multi-level categories (outer ring = subcategories)

### Configuration Options

**Hole Size:**
- Small: 20% (thick ring)
- Medium: 40% (default)
- Large: 60% (thin ring)
- Custom percentage

**Center Content:**
- Empty (default)
- Total value: "$12,500"
- Custom text: "Total Sales"
- Icon or image
- Multiple lines

**Example:**
```
Total: $12,500
━━━━━━━━━━━━━━
   ╱───────╲
  │ ▓▓▓▓▓ │  Product A
  │▓▓   ▓▓│
  │▓     ▓│  Product B
  │▓▓   ▓▓│
  │ ▓▓▓▓▓ │  Product C
   ╲───────╱
```

### Multi-Level Doughnuts

**Nested Rings:**
- Inner ring: Main categories
- Outer ring: Subcategories
- Example:
  - Inner: Sales by Region
  - Outer: Sales by Product within Region

**Configuration:**
- Up to 3 rings
- Different colors per ring
- Sync or independent rotation

---

## 📉 Scatter Charts

### When to Use

**Perfect for:**
- ✅ Correlation analysis
- ✅ Distribution patterns
- ✅ Outlier detection
- ✅ Regression analysis

**Example Use Cases:**
- Height vs Weight
- Price vs Quality
- Study Hours vs Test Scores
- Temperature vs Sales

### Configuration Options

**Point Settings:**
- Shape: Circle, square, triangle, cross
- Size: 3-15px
- Color: Single, by series, by value
- Opacity: 50-100%

**Axes:**
- X-axis: Continuous values
- Y-axis: Continuous values
- Both: Linear or logarithmic scale
- Grid lines: Major and minor

**Trend Lines:**
- Linear regression
- Polynomial
- Exponential
- Moving average

**Example:**
```
Price vs Quality
━━━━━━━━━━━━━━━━
High┃        ●
    ┃      ●   ●
    ┃    ●   ●
    ┃  ●   ●
Low ┃●   ●
    ┗━━━━━━━━━━━━
    Low    High
       Price
```

### Multiple Series

**Compare Groups:**
- Different products
- Different time periods
- Different categories

**Visual Distinction:**
- Different colors
- Different shapes
- Different sizes
- Legends for clarity

### Data Requirements

**Format:**
- X value (number)
- Y value (number)
- Optional: Category (for color)
- Optional: Size value (bubble chart)

**Minimum:**
- 5-10 points (for meaningful analysis)
- Recommended: 20+ points

---

## 🎯 Radar Charts

### When to Use

**Perfect for:**
- ✅ Multi-dimensional comparisons
- ✅ Skill assessments
- ✅ Product feature comparisons
- ✅ Performance reviews

**Example Use Cases:**
- Employee skills (communication, teamwork, technical)
- Product comparison (price, quality, features, support)
- Student assessments (math, reading, writing, science)

### Configuration Options

**Web Shape:**
- Circular (equal spacing)
- Polygonal (straight lines)
- Number of axes: 3-10

**Axes:**
- Label position: Outside/inside
- Max value: Auto or custom
- Grid levels: 3, 5, or 10
- Grid style: Circles or polygons

**Data Points:**
- Show markers: Yes/No
- Connect with lines
- Fill area: Yes/No
- Opacity: 30-80%

**Example:**
```
Skills Assessment
━━━━━━━━━━━━━━━━
    Technical
        ●
       ╱│╲
      ╱ │ ╲
Team ●──●──● Problem
    ╱   │   ╲ Solving
   ╱    │    ╲
  ●─────●─────●
Comm.   Lead.
```

### Multiple Entities

**Compare Side-by-Side:**
- Employee A vs Employee B
- Product X vs Product Y
- This Year vs Last Year

**Visual:**
- Different colors
- Overlapping areas
- Legends for clarity

### Data Requirements

**Format:**
- 3-10 metrics (axes)
- Values on same scale (0-10, 0-100)
- Multiple subjects (optional)

**Example Data:**
```
Metric         | Employee A | Employee B
---------------|------------|------------
Communication  | 8          | 9
Technical      | 9          | 7
Leadership     | 7          | 8
Problem Solving| 8          | 8
```

---

## 🎨 Chart Styling

### Chart Size

**Width:**
- Percentage: `width="100%"` (responsive)
- Fixed pixels: `width="800px"`
- Auto: Fills container

**Height:**
- Fixed pixels: `height="400px"` (default)
- Aspect ratio: 16:9, 4:3, 1:1
- Min/max constraints

**Shortcode:**
```
[atables_chart id="1" width="100%" height="500px"]
```

### Chart Title

**Title Settings:**
- Text: Custom title
- Position: Top, bottom, left, right, none
- Alignment: Left, center, right
- Font: Family, size, weight, color

**Subtitle:**
- Optional subtitle
- Smaller font
- Different color

**Example:**
```css
title: "Monthly Sales Report"
subtitle: "January - December 2024"
```

### Background & Borders

**Background:**
- Color: White, transparent, custom
- Gradient: Top-to-bottom
- Image: Background image URL

**Chart Area:**
- Background color (different from outer)
- Padding: Space around chart
- Width/height: Percentage of total

**Borders:**
- Outer border: Width, color, style
- Shadow: Drop shadow effect
- Rounded corners: 0-20px

---

## 🎨 Colors & Themes

### Color Schemes

**Pre-built Palettes:**

**Default:**
```
Colors: #4285F4, #EA4335, #FBBC05, #34A853
Use: General purpose
```

**Pastel:**
```
Colors: #FFB6C1, #87CEEB, #98FB98, #FFD700
Use: Soft, friendly charts
```

**Bold:**
```
Colors: #FF0000, #00FF00, #0000FF, #FF00FF
Use: High contrast, attention-grabbing
```

**Corporate:**
```
Colors: #003366, #336699, #6699CC, #99CCFF
Use: Professional, business reports
```

**Earth Tones:**
```
Colors: #8B4513, #CD853F, #DEB887, #F4A460
Use: Natural, warm feel
```

### Custom Colors

**Manual Selection:**
```
[atables_chart id="1" colors="#FF6384,#36A2EB,#FFCE56"]
```

**Color Picker:**
- Visual color picker in editor
- Hex codes: #RRGGBB
- RGB values: rgb(255, 99, 132)
- Named colors: "red", "blue"

**Gradients:**
```javascript
colors: {
  gradient: true,
  start: "#667eea",
  end: "#764ba2"
}
```

### Color Mapping

**By Value:**
- Green: Good/high values
- Red: Bad/low values
- Yellow: Warning/medium values

**Example:**
```
Revenue: > $5000 = Green
         $3000-5000 = Yellow
         < $3000 = Red
```

**By Category:**
- Each product = Different color
- Consistent across charts
- Brand color matching

---

## 🏷️ Legends & Labels

### Legend Configuration

**Position:**
- Top (above chart)
- Bottom (below chart, default)
- Left (left side)
- Right (right side)
- None (hide legend)

**Shortcode:**
```
[atables_chart id="1" legend_position="top"]
```

**Layout:**
- Horizontal (items side-by-side)
- Vertical (items stacked)
- Auto (based on position)

**Styling:**
- Font size: 10-16px
- Font weight: Normal or bold
- Colors: Match data or custom
- Border: Yes/No

### Legend Items

**Content:**
- ■ Color box + label (default)
- ● Point marker + label
- ─ Line + label (for line charts)
- Custom icons

**Interactions:**
- Click to hide/show series
- Hover to highlight series
- Drag to reorder (advanced)

### Data Labels

**Show Values on Chart:**
- Position: Above bars, inside slices, etc.
- Format: Numbers, percentages, currency
- Font: Size, color, weight
- Background: Box around label

**Examples:**

**Bar Chart:**
```
Product A ████████ 120
Product B ██████ 80
```

**Pie Chart:**
```
  ╱─────╲
 │ 35%  │ Category A
```

**Line Chart:**
```
   ●─────●
   50    75
```

### Axis Labels

**X-Axis:**
- Label text: "Month", "Product", etc.
- Rotation: 0°, 45°, 90°
- Skip: Every 2nd, every 5th
- Format: Abbreviate long labels

**Y-Axis:**
- Label text: "Revenue ($)", "Count"
- Format: 1K, 1M, $1,000
- Grid lines: Show/hide
- Min/max values

---

## ✨ Animations

### Animation Settings

**On Load:**
- ✅ Enable: Smooth appearance
- ❌ Disable: Instant display
- Duration: 500ms - 2000ms
- Easing: Linear, ease-in, ease-out, bounce

**Shortcode:**
```
[atables_chart id="1" animation="true"]
```

**Animation Types:**

**Bar/Column:**
- Grow from bottom
- Slide from left
- Fade in

**Line/Area:**
- Draw line path
- Reveal left-to-right
- Fade in

**Pie/Doughnut:**
- Rotate in
- Grow from center
- Slice-by-slice

### Hover Effects

**On Mouse Over:**
- Highlight element
- Scale up slightly
- Show tooltip
- Dim others

**Tooltip:**
- Show value
- Show label
- Show percentage
- Custom format

**Example:**
```
Hover over bar:
┌──────────────┐
│ Product A    │
│ Sales: $1,200│
│ 35% of total │
└──────────────┘
```

### Update Animations

**When Data Changes:**
- Smooth transition
- No jarring jumps
- Maintain context
- Duration: 300-500ms

---

## 📱 Responsive Charts

### Mobile Optimization

**Auto-Resize:**
- Width: 100% of container
- Height: Maintains aspect ratio
- Font sizes: Scale proportionally
- Touch-friendly: Larger tap targets

**Mobile-Specific Settings:**

**Font Sizes:**
- Desktop: 14px
- Tablet: 12px
- Mobile: 10px

**Legend:**
- Desktop: Bottom, horizontal
- Mobile: Bottom, vertical (scrollable)

**Labels:**
- Desktop: Full labels
- Mobile: Abbreviated or rotated

### Breakpoints

**Customize by Screen Size:**

```css
/* Desktop */
@media (min-width: 1024px) {
  .atables-chart { height: 500px; }
}

/* Tablet */
@media (min-width: 768px) and (max-width: 1023px) {
  .atables-chart { height: 400px; }
}

/* Mobile */
@media (max-width: 767px) {
  .atables-chart { height: 300px; }
}
```

### Touch Interactions

**Mobile Gestures:**
- Tap: Show tooltip
- Pinch: Zoom in/out
- Swipe: Pan chart (if zoomed)
- Long press: Show details

**Optimizations:**
- Larger point markers (easier to tap)
- Simplified tooltips
- Larger legend items
- Scrollable legends

---

## 🚀 Advanced Options

### Real-Time Updates

**Live Data:**
- Connect to API
- Auto-refresh interval (30s, 1m, 5m)
- Smooth transitions
- Show "Live" indicator

**Use Cases:**
- Stock prices
- Server monitoring
- Live analytics
- Real-time sales

### Interactive Features

**Zoom & Pan:**
- Mouse wheel to zoom
- Drag to pan
- Reset button
- Zoom to selection

**Click Events:**
- Click bar/point → Filter table
- Click slice → Show details
- Click legend → Toggle series

**Drill-Down:**
- Click category → Show breakdown
- Breadcrumb navigation
- Back to overview

### Export Chart

**Export as Image:**
- PNG (default)
- JPG
- SVG (vector)
- PDF

**Export Data:**
- CSV (chart data)
- Excel
- JSON

**Print:**
- Print-optimized layout
- Remove interactions
- High DPI for quality

### Chart Combinations

**Multiple Chart Types:**
- Bar + Line (combo chart)
- Column + Area
- Show different metrics together

**Example:**
```
Revenue (Bars) + Profit Margin % (Line)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
      █          ●────●────●  30%
      █      ●───
  █   █  ●───               20%
  █   █
  █ █ █ █                  10%
━━┻━┻━┻━┻━━━━━━━━━━━━━━━━━━
 Q1 Q2 Q3 Q4
```

### Dynamic Data

**Filter from Table:**
- Chart shows filtered table data
- Sync with table filters
- Real-time updates

**Date Range Selector:**
- Show last 7 days, 30 days, year
- Custom date range
- Compare periods

**Category Selector:**
- Dropdown to select category
- Multi-select
- Chart updates dynamically

---

## 📚 Related Documentation

- [Table Features](02-TABLE-FEATURES.md)
- [Getting Started](01-GETTING-STARTED.md)
- [Shortcode Reference](SHORTCODE-REFERENCE.md)
- [FAQ](FAQ.md)
- [Troubleshooting](TROUBLESHOOTING.md)

---

**Updated:** October 2025
**Version:** 1.0.0

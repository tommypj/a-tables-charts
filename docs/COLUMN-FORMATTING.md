# Column Formatting - User Guide

## 📋 Overview
The Column Formatting feature allows you to customize how columns appear in your tables using CSS classes and HTML attributes.

## 🎯 How to Use Column Formatting

### Method 1: Using CSS Classes in Shortcode Attributes (Coming Soon - UI)
You'll be able to apply formatting through the admin interface.

### Method 2: Using CSS Classes Directly (Available Now)
You can wrap column headers with custom CSS classes when creating manual tables.

## 📝 Available CSS Classes

### Alignment Classes
- `atables-align-left` - Align text to left
- `atables-align-center` - Align text to center  
- `atables-align-right` - Align text to right
- `atables-align-justify` - Justify text

### Text Styling
- `atables-bold` - Make text bold
- `atables-italic` - Make text italic
- `atables-uppercase` - TRANSFORM TO UPPERCASE
- `atables-lowercase` - transform to lowercase
- `atables-capitalize` - Capitalize First Letters
- `atables-nowrap` - Prevent text wrapping

### Column Width
- `atables-col-narrow` - 80px width
- `atables-col-small` - 120px width
- `atables-col-medium` - 200px width
- `atables-col-large` - 300px width
- `atables-col-auto` - Automatic width

### Highlighting
- `atables-highlight` - Yellow highlight
- `atables-highlight-success` - Green highlight
- `atables-highlight-danger` - Red highlight
- `atables-highlight-warning` - Orange highlight
- `atables-highlight-info` - Blue highlight

### Number Formatting
- `atables-number` - Right-aligned monospace numbers
- `atables-currency` - Adds $ sign automatically
- `atables-percentage` - Adds % sign automatically

### Badge/Pill Styling
- `atables-badge` - Basic badge (gray)
- `atables-badge-primary` - Blue badge
- `atables-badge-success` - Green badge
- `atables-badge-danger` - Red badge
- `atables-badge-warning` - Yellow badge
- `atables-badge-info` - Cyan badge

### Vertical Alignment
- `atables-valign-top` - Align content to top
- `atables-valign-middle` - Align content to middle
- `atables-valign-bottom` - Align content to bottom

### Responsive Priority (Hide on Mobile)
- `atables-priority-1` - Hide on screens < 768px
- `atables-priority-2` - Hide on screens < 992px
- `atables-priority-3` - Hide on screens < 1200px

## 🎨 Example Usage

### Example 1: Price Table with Right-Aligned Numbers
```
Product | Price | Stock
iPhone  | 999   | 50
Samsung | 899   | 30
```

Add these CSS classes to the Price column: `atables-currency atables-align-right`

### Example 2: Status Column with Badges
Wrap status values in badge spans:
```html
<span class="atables-badge atables-badge-success">Active</span>
<span class="atables-badge atables-badge-danger">Inactive</span>
```

### Example 3: Hide Less Important Columns on Mobile
Add `atables-priority-1` class to columns that should hide on mobile devices.

## 🚀 Coming Soon
- Visual column formatting editor in admin
- Save column settings per table
- More formatting options (borders, gradients, etc.)
- Conditional formatting based on values

## 💡 Tips
1. **Combine Classes**: You can use multiple classes together
   - Example: `atables-align-right atables-bold atables-highlight-success`

2. **Responsive Design**: Use priority classes to hide less important columns on small screens

3. **Consistency**: Apply the same formatting to similar columns across different tables

4. **Performance**: CSS classes are much faster than inline styles

## 📚 Learn More
For advanced customization, you can also add custom CSS in your theme's style.css file.

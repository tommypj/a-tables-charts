# Conditional Formatting - User Guide

## 📋 Overview
Conditional Formatting automatically highlights cells or rows based on their values, making it easy to spot trends, outliers, and important data at a glance.

## 🎨 Available Presets

### 1. **Positive/Negative**
Automatically highlights positive values in green and negative values in red.
- ✅ Perfect for: Financial data, profit/loss, changes
- Colors: Green for positive, Red for negative

### 2. **Traffic Light (Red/Yellow/Green)**
Three-color scale for performance indicators.
- ✅ Perfect for: Scores, ratings, completion percentages
- Red: < 33%
- Yellow: 33-66%
- Green: > 66%

### 3. **Status Badges**
Color-coded badges for status values.
- ✅ Perfect for: Order status, approval workflows, task completion
- Green: active, approved, completed, success, yes
- Yellow: pending, in progress, processing
- Red: inactive, rejected, failed, error, no

### 4. **High/Low Values**
Emphasizes extreme values.
- ✅ Perfect for: Sales figures, inventory levels, metrics
- Blue (bold): Values > 1000
- Red (bold): Values < 100

### 5. **Priority Levels**
Color-codes priority indicators.
- ✅ Perfect for: Task management, support tickets, issues
- Red: critical, urgent, high
- Yellow: medium
- Cyan: low, minor

### 6. **Percentage Scale**
Progressive coloring from 0-100%.
- ✅ Perfect for: Completion rates, scores, percentages
- Red: 0-25%
- Yellow: 25-50%
- Cyan: 50-75%
- Green: 75-100%

## 🔧 Supported Operators

### Numeric Comparisons
- `equals` (=) - Exact match
- `not_equals` (≠) - Not equal to
- `greater_than` (>) - Greater than
- `greater_than_or_equal` (≥) - Greater than or equal
- `less_than` (<) - Less than
- `less_than_or_equal` (≤) - Less than or equal
- `between` - Between two values (e.g., "10,50")

### Text Comparisons
- `contains` - Contains text
- `not_contains` - Does not contain text
- `starts_with` - Starts with text
- `ends_with` - Ends with text
- `in` - Matches any in comma-separated list
- `not_in` - Does not match any in list

### Special
- `is_empty` - Cell is empty
- `is_not_empty` - Cell has value

## 💡 Usage Examples

### Example 1: Sales Performance Table
```
Product | Revenue | Status
iPhone  | $15000  | High
Samsung | $8000   | Medium  
Google  | $3000   | Low
```

**Apply "High/Low" preset to Revenue column:**
- Values > $10,000 → Blue background, bold
- Values < $5,000 → Red background, bold

### Example 2: Task Management
```
Task | Priority | Completion
Fix bug | Critical | 85%
Add feature | High | 45%
Update docs | Low | 20%
```

**Apply "Priority Levels" preset to Priority column:**
- Critical/High → Red background
- Medium → Yellow background
- Low → Cyan background

**Apply "Percentage Scale" preset to Completion column:**
- Progressive coloring from red (low) to green (high)

### Example 3: Order Status
```
Order ID | Status | Amount
#1001 | Completed | $599
#1002 | Pending | $299
#1003 | Failed | $899
```

**Apply "Status Badges" preset to Status column:**
- Completed → Green badge
- Pending → Yellow badge
- Failed → Red badge

## 🎯 CSS Classes Reference

### Traffic Light Classes
- `atables-traffic-red` - Red indicator
- `atables-traffic-yellow` - Yellow indicator
- `atables-traffic-green` - Green indicator

### Status Classes
- `atables-status-success` - Success status with left border
- `atables-status-warning` - Warning status with left border
- `atables-status-danger` - Danger status with left border
- `atables-status-info` - Info status with left border

### Priority Classes
- `atables-priority-critical` - Critical priority badge
- `atables-priority-high` - High priority
- `atables-priority-medium` - Medium priority
- `atables-priority-low` - Low priority

### Value Indicators
- `atables-positive` - Positive value styling
- `atables-negative` - Negative value styling
- `atables-neutral` - Neutral value styling

### Icons
- `atables-icon-success` - ✓ Checkmark
- `atables-icon-error` - ✗ X mark
- `atables-icon-warning` - ⚠ Warning symbol
- `atables-icon-info` - ℹ Info symbol
- `atables-icon-star` - ★ Star
- `atables-icon-arrow-up` - ↑ Up arrow
- `atables-icon-arrow-down` - ↓ Down arrow

### Row-Level Classes
- `atables-row-highlight` - Highlight entire row (yellow)
- `atables-row-success` - Success row (green)
- `atables-row-danger` - Danger row (red)
- `atables-row-info` - Info row (blue)

## 🚀 Coming Soon

### Visual Rule Builder (Admin UI)
- Drag-and-drop rule creation
- Live preview of formatting
- Save rules per column
- Template library

### Advanced Features
- Color gradients
- Data bars (inline progress bars)
- Icon sets
- Custom formulas
- Multiple rule sets
- Row-level conditions

## 📝 Best Practices

1. **Don't Overuse Colors**
   - Limit to 3-4 colors per table
   - Use consistent color meanings

2. **Choose Appropriate Presets**
   - Match preset to data type
   - Test with actual data values

3. **Consider Accessibility**
   - Don't rely solely on color
   - Use icons or text indicators
   - Ensure sufficient contrast

4. **Performance**
   - Rules are evaluated client-side
   - Use simple conditions for large tables

## 🎓 Advanced Tips

### Combining Multiple Rules
Rules are evaluated in order. Use `stop_if_true` to prevent further evaluation once a condition is met.

### Custom Colors
You can define custom colors for any condition:
- Background: Any valid CSS color
- Text: Any valid CSS color
- Font Weight: normal, bold, 600, 700, etc.

### Responsive Behavior
All conditional formatting is mobile-friendly and works with responsive table modes (scroll, stack, cards).

# Advanced Sorting Options - User Guide

## 📋 Overview
Advanced Sorting provides powerful, intelligent sorting capabilities for your tables with multi-column sorting, natural alphanumeric ordering, and custom sort sequences.

## 🎯 Key Features

### **✅ Multi-Column Sorting**
Sort by multiple columns with priority order
- Primary sort column
- Secondary sort (tiebreaker)
- Tertiary sort and beyond
- Up to 5 sort columns

### **✅ Type-Aware Sorting**
Automatic detection and intelligent sorting
- **Numeric**: Numbers sorted mathematically (1, 2, 10, 100)
- **Date**: Chronological sorting
- **Natural**: Alphanumeric (Item1, Item2, Item10)
- **String**: Alphabetical sorting

### **✅ Custom Sort Orders**
Define your own sorting sequences
- Days of week (Monday-Sunday)
- Months (January-December)
- Status priorities (Active, Pending, Inactive)
- Custom business logic

---

## 🔢 Sort Types Explained

### **1. Automatic (Auto)**
Let the system detect the best sort method

**How it works:**
- Checks if values are numeric → Numeric sort
- Checks if values are dates → Date sort
- Checks if values contain numbers → Natural sort
- Otherwise → String sort

**Best for:** Mixed content columns

---

### **2. Numeric Sort**
Mathematical number sorting

**Examples:**
```
Before: 100, 20, 3, 1
After:  1, 3, 20, 100
```

**Best for:**
- Prices
- Quantities
- IDs
- Ages
- Scores

---

### **3. Date Sort**
Chronological date sorting

**Supported formats:**
- `2025-01-15`
- `January 15, 2025`
- `15/01/2025`
- `Jan 15, 2025`

**Examples:**
```
Before: Dec 25 2024, Jan 1 2024, Mar 15 2025
After:  Jan 1 2024, Dec 25 2024, Mar 15 2025
```

**Best for:**
- Created dates
- Due dates
- Birth dates
- Event dates

---

### **4. Natural Sort (Alphanumeric)**
Human-friendly sorting of text with numbers

**The Problem:**
Standard sort: `Item1, Item10, Item2, Item20`  
❌ Not intuitive!

**Natural Sort:**
`Item1, Item2, Item10, Item20`  
✅ Makes sense!

**Examples:**
```
Standard:   Item1, Item10, Item2, Item20, Item3
Natural:    Item1, Item2, Item3, Item10, Item20

Standard:   File1.txt, File10.txt, File2.txt
Natural:    File1.txt, File2.txt, File10.txt

Standard:   v1.10, v1.2, v1.20
Natural:    v1.2, v1.10, v1.20
```

**Best for:**
- Product codes (PRD001, PRD002, PRD010)
- Version numbers (v1.0, v1.1, v1.10)
- File names (Doc1, Doc2, Doc10)
- Room numbers (Room1, Room2, Room10)

---

###  **5. String Sort (Alphabetical)**
Standard alphabetical sorting

**Examples:**
```
Before: zebra, apple, banana, cherry
After:  apple, banana, cherry, zebra
```

**Features:**
- Case-insensitive
- A-Z ordering
- Special characters last

**Best for:**
- Names
- Titles
- Categories
- Text-only columns

---

## 📊 Multi-Column Sorting

### **How It Works:**
1. **Primary Sort:** First column determines main order
2. **Secondary Sort:** When primary values match, use second column
3. **Tertiary Sort:** When first two match, use third column
4. And so on...

### **Example:**

**Sort by:** `Last Name` (asc), then `First Name` (asc)

| First Name | Last Name | Age |
|------------|-----------|-----|
| John       | Doe       | 30  |
| Jane       | Doe       | 25  |
| Bob        | Smith     | 35  |

**Result:** Bob Smith, Jane Doe (comes before John Doe - same last name, Jane < John)

---

## 🎨 Custom Sort Orders

### **What are Custom Sort Orders?**
Define specific sequences for non-alphabetical sorting

### **Built-in Presets:**

#### **1. Days of Week**
```
Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday
```

#### **2. Months**
```
January, February, March, April, May, June, 
July, August, September, October, November, December
```

#### **3. Status (Priority)**
```
Active, Pending, Inactive, Archived
```

#### **4. Priority Levels**
```
Critical, High, Medium, Low
```

#### **5. Sizes**
```
XS, S, M, L, XL, XXL
```

### **How to Use:**
```php
// In table display settings
'sort_config' => array(
    array(
        'column' => 'Day',
        'direction' => 'asc',
        'type' => 'custom_order',
        'custom_order' => array('Monday', 'Tuesday', 'Wednesday', ...)
    )
)
```

---

## 💡 Use Cases & Examples

### **Example 1: E-commerce Product Table**

**Scenario:** Sort products by price, then by rating

**Sort Configuration:**
```
Primary:   Price (numeric, asc) - Cheapest first
Secondary: Rating (numeric, desc) - Highest rated first
```

**Result:** Products ordered by price, with highest-rated items first when prices match.

---

### **Example 2: Task Management**

**Scenario:** Show tasks by priority, then due date

**Sort Configuration:**
```
Primary:   Priority (custom order: Critical, High, Medium, Low)
Secondary: Due Date (date, asc) - Earliest first
```

**Result:** Critical tasks first, ordered by due date, then High priority tasks, etc.

---

### **Example 3: Employee Directory**

**Scenario:** Alphabetical by department, then name

**Sort Configuration:**
```
Primary:   Department (string, asc)
Secondary: Last Name (string, asc)
Tertiary:  First Name (string, asc)
```

**Result:** Departments A-Z, with employees sorted alphabetically within each department.

---

### **Example 4: Class Schedule**

**Scenario:** Sort by day of week, then time

**Sort Configuration:**
```
Primary:   Day (custom order: Monday-Sunday)
Secondary: Time (string, asc)
```

**Result:** Classes shown Monday through Sunday, with earliest classes first each day.

---

### **Example 5: File Listing**

**Scenario:** Natural sort for version numbers

**Sort Configuration:**
```
Primary: Version (natural)
```

**Examples:**
```
v1.0.0
v1.0.1
v1.0.10    ← Correctly placed after v1.0.9
v1.0.9
v1.1.0
v1.10.0    ← Correctly placed after v1.9.0
```

---

## 🔧 Technical Implementation

### **Sorting Service Methods:**

#### **1. Multi-Sort**
```php
$sorted_data = $sorting_service->multi_sort($data, array(
    array('column' => 'Price', 'direction' => 'asc', 'type' => 'numeric'),
    array('column' => 'Name', 'direction' => 'asc', 'type' => 'string')
));
```

#### **2. Single Column Sort**
```php
$sorted_data = $sorting_service->sort_by_column(
    $data, 
    'Price', 
    'asc', 
    'numeric'
);
```

#### **3. Custom Order Sort**
```php
$sorted_data = $sorting_service->sort_by_custom_order(
    $data,
    'Status',
    array('Active', 'Pending', 'Inactive')
);
```

---

## 📈 Performance Notes

### **Best Practices:**

**✅ Fast Sorting:**
- Tables under 1,000 rows: Instant
- Tables under 10,000 rows: Under 1 second
- Tables over 10,000 rows: Use pagination

**✅ Optimization Tips:**
1. Use specific sort types when possible (not always 'auto')
2. Limit multi-column sorts to 3 columns max
3. Sort on indexed columns when using database
4. Cache sorted results for repeated access

---

## 🎓 Pro Tips

### **1. Choose the Right Sort Type**
- **Numbers with decimals?** → Numeric
- **Dates in any format?** → Date
- **Text with numbers (Item1, Item2)?** → Natural
- **Pure text?** → String
- **Not sure?** → Auto

### **2. Multi-Column Strategy**
Think about logical grouping:
- Group by category, sort within by name
- Group by status, sort within by priority
- Group by date, sort within alphabetically

### **3. Custom Orders for Business Logic**
Create custom orders for:
- Workflow stages
- Product categories
- Priority levels
- Any non-alphabetical sequence

### **4. Natural Sort is Underused**
Many situations benefit from natural sort:
- Invoice numbers
- Product SKUs  
- File names
- Version numbers
- Room/seat numbers

---

## 🐛 Troubleshooting

### **Problem: Numbers sorting as text**
**Symptom:** `1, 10, 2, 20, 3`  
**Solution:** Use `numeric` type instead of `auto` or `string`

### **Problem: Dates sorting incorrectly**
**Symptom:** Dates out of order  
**Solution:**
1. Ensure consistent date format
2. Use `date` type
3. Convert to ISO format (YYYY-MM-DD) if possible

### **Problem: Mixed content column**
**Symptom:** Some numbers, some text  
**Solution:** Use `natural` sort type

### **Problem: Custom order not working**
**Symptom:** Items not in specified order  
**Solution:** Ensure exact match of values in custom order array

---

## ⚡ Advanced Features

### **Combining with Other Features:**

**1. Sort + Filter**
```
Filter Status = "Active"
Then sort by Priority (custom order)
```

**2. Sort + Search**
```
Search for "Product"
Then sort results by Price (numeric)
```

**3. Sort + Conditional Formatting**
```
Sort by Sales (numeric, desc)
Highlight top 10 with conditional formatting
```

---

## 📊 Sort Type Decision Tree

```
Start
  |
  ├─ Pure numbers (123, 456)? → Numeric
  |
  ├─ Dates? → Date
  |
  ├─ Text with numbers (Item1, Item10)? → Natural
  |
  ├─ Special order needed (Mon-Sun)? → Custom Order
  |
  └─ Pure text (Names, Titles)? → String
```

---

## 🚀 Coming Soon

- **Saved Sort Presets** - Save favorite sort configurations
- **UI Sort Builder** - Visual interface for complex sorts
- **Sort Indicators** - Show current sort in table headers
- **Click-to-Sort** - Click column headers to sort
- **Sort History** - Undo/redo sorting
- **Export with Sort** - Keep sort order when exporting

---

## 📝 Summary

### **Quick Reference:**

| Sort Type | Best For | Example |
|-----------|----------|---------|
| Auto | Mixed content | General use |
| Numeric | Numbers, IDs | 1, 2, 10, 100 |
| Date | Dates, timestamps | Jan 1, Jan 2, Feb 1 |
| Natural | Alphanumeric | Item1, Item2, Item10 |
| String | Text, names | Apple, Banana, Cherry |
| Custom | Business logic | Mon, Tue, Wed |

### **Multi-Column Tips:**
1. Most important column first
2. Use tiebreakers for precision
3. Limit to 3 columns for clarity
4. Test with real data

---

**Advanced Sorting makes your data instantly understandable! 🎯**

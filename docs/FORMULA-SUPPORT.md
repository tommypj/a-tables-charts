# Formula Support - User Guide

## 📋 Overview
Formula Support brings spreadsheet-like calculations to your tables with **13 powerful functions**, cell references, and mathematical operations!

## 🎯 Key Features

### **✅ 13 Built-in Functions**
Powerful calculation functions for all your needs

**Statistical Functions:**
- SUM - Add values together
- AVERAGE/AVG - Calculate mean
- MIN - Find minimum value
- MAX - Find maximum value
- COUNT - Count values
- **MEDIAN - Find middle value** ⭐ NEW!

**Mathematical Functions:**
- ROUND - Round numbers to decimals
- ABS - Absolute value
- **SQRT - Square root** ⭐ NEW!
- **POWER/POW - Raise to power** ⭐ NEW!
- **PRODUCT - Multiply values** ⭐ NEW!

**Logic Functions:**
- IF - Conditional logic

**Text Functions:**
- **CONCAT/CONCATENATE - Combine text** ⭐ NEW!

### **✅ Cell References**
Reference data like Excel
- Single cells (A1, B2)
- Cell ranges (A1:A10)
- Entire columns (A:A)
- Cross-cell calculations

### **✅ Mathematical Operations**
Standard math operators
- Addition (+)
- Subtraction (-)
- Multiplication (*)
- Division (/)
- Parentheses for order

---

## 🔢 All Supported Functions

### **1. SUM**
Add all values together

**Syntax:**
```
=SUM(range)
=SUM(value1, value2, ...)
```

**Examples:**
```
=SUM(A1:A10)           → Sum column A, rows 1-10
=SUM(A:A)              → Sum entire column A
=SUM(A1, B1, C1)       → Sum specific cells
=SUM(A1:C1)            → Sum across row
=SUM(100, 200, 300)    → Sum literal values = 600
```

**Use Cases:**
- Total sales revenue
- Sum expenses
- Add quantities
- Calculate totals

---

### **2. AVERAGE (AVG)**
Calculate mean/average

**Syntax:**
```
=AVERAGE(range)
=AVG(range)
```

**Examples:**
```
=AVERAGE(B1:B10)       → Average of B1-B10
=AVERAGE(A:A)          → Average entire column
=AVG(10, 20, 30)       → Average = 20
```

**Use Cases:**
- Average score
- Mean price
- Average rating
- Performance metrics

---

### **3. MEDIAN** ⭐ NEW!
Find middle value (statistical median)

**Syntax:**
```
=MEDIAN(range)
```

**Examples:**
```
=MEDIAN(C1:C10)        → Middle value in C1-C10
=MEDIAN(5, 1, 9, 3, 7) → Returns 5
=MEDIAN(10, 20, 30, 40)→ Returns 25 (avg of 20 & 30)
```

**Use Cases:**
- More accurate "typical" value than average
- Remove outlier impact
- Real estate prices analysis
- Salary benchmarking

**Why MEDIAN vs AVERAGE?**
MEDIAN ignores extreme outliers:
- Salaries: $30K, $35K, $40K, $1M
  - Average: $276K (misleading!)
  - Median: $37.5K (more accurate)

---

### **4. MIN**
Find minimum (smallest) value

**Syntax:**
```
=MIN(range)
```

**Examples:**
```
=MIN(C1:C10)           → Lowest value in C1-C10
=MIN(A:A)              → Lowest in column A
=MIN(5, 10, 15)        → Returns 5
```

**Use Cases:**
- Lowest price
- Minimum score
- Smallest quantity
- Best time

---

### **5. MAX**
Find maximum (largest) value

**Syntax:**
```
=MAX(range)
```

**Examples:**
```
=MAX(D1:D10)           → Highest value in D1-D10
=MAX(A:A)              → Highest in column A
=MAX(5, 10, 15)        → Returns 15
```

**Use Cases:**
- Highest price
- Maximum score
- Largest quantity
- Peak value

---

### **6. COUNT**
Count number of numeric values

**Syntax:**
```
=COUNT(range)
```

**Examples:**
```
=COUNT(E1:E10)         → Count numeric values E1-E10
=COUNT(A:A)            → Count all numbers in column A
```

**Use Cases:**
- Number of entries
- Count products
- Total records
- Item count

---

### **7. PRODUCT** ⭐ NEW!
Multiply all values together

**Syntax:**
```
=PRODUCT(range)
=PRODUCT(value1, value2, ...)
```

**Examples:**
```
=PRODUCT(A1:A3)        → A1 × A2 × A3
=PRODUCT(5, 4, 2)      → 40
=PRODUCT(A1, B1)       → Price × Quantity
```

**Use Cases:**
- **E-commerce totals** (Price × Quantity)
- **Volume calculations** (Length × Width × Height)
- **Compound growth** (Growth rate over multiple periods)
- **Scaling factors**

**Common E-commerce Example:**
```
Product Table:
┌─────────┬────────┬──────────┬─────────┐
│ Product │ Price  │ Quantity │  Total  │
├─────────┼────────┼──────────┼─────────┤
│ Widget  │  $25   │    3     │ [calc]  │
└─────────┴────────┴──────────┴─────────┘

Formula for D2:
=PRODUCT(B2, C2)

Result: $75
```

---

### **8. POWER (POW)** ⭐ NEW!
Raise number to a power (exponentiation)

**Syntax:**
```
=POWER(base, exponent)
=POW(base, exponent)
```

**Examples:**
```
=POWER(2, 3)           → 2³ = 8
=POWER(10, 2)          → 10² = 100
=POW(1.05, 10)         → 1.05¹⁰ = 1.629 (compound interest)
```

**Use Cases:**
- **Compound interest calculations**
- **Growth projections**
- **Exponential growth modeling**
- **Scientific calculations**

**Financial Example:**
```
Investment Growth:
┌─────────┬────────────┬────────┬──────────────┐
│  Year   │  Initial   │  Rate  │  Final Value │
├─────────┼────────────┼────────┼──────────────┤
│   10    │  $10,000   │  5%    │   [calc]     │
└─────────┴────────────┴────────┴──────────────┘

Formula for D2:
=PRODUCT(B2, POWER(1.05, A2))

Result: $16,289 (10 years @ 5%)
```

---

### **9. SQRT** ⭐ NEW!
Calculate square root

**Syntax:**
```
=SQRT(value)
```

**Examples:**
```
=SQRT(16)              → 4
=SQRT(100)             → 10
=SQRT(A1)              → Square root of A1
```

**Use Cases:**
- **Distance calculations** (Pythagorean theorem)
- **Standard deviation** (statistical analysis)
- **Area to side length** (square = √area)
- **Physics formulas**

**Distance Example:**
```
Calculate Distance:
┌────────┬────────┬──────────┐
│   X    │   Y    │ Distance │
├────────┼────────┼──────────┤
│   3    │   4    │  [calc]  │
└────────┴────────┴──────────┘

Formula for C2:
=SQRT(POWER(A2,2) + POWER(B2,2))

Result: 5 (3² + 4² = 25, √25 = 5)
```

---

### **10. ROUND**
Round number to specified decimals

**Syntax:**
```
=ROUND(value, decimals)
```

**Examples:**
```
=ROUND(3.14159, 2)     → 3.14
=ROUND(A1, 0)          → Round A1 to integer
=ROUND(SUM(A1:A10), 2) → Round sum to 2 decimals
```

**Use Cases:**
- Currency (2 decimals)
- Percentages (1 decimal)
- Clean display numbers
- Remove floating point errors

---

### **11. ABS**
Absolute value (remove negative sign)

**Syntax:**
```
=ABS(value)
```

**Examples:**
```
=ABS(-50)              → 50
=ABS(A1-B1)            → Positive difference
=ABS(-3.14)            → 3.14
```

**Use Cases:**
- Distance/variance calculations
- Positive differences
- Error margins
- Deviation analysis

---

### **12. IF**
Conditional value based on condition

**Syntax:**
```
=IF(condition, true_value, false_value)
```

**Examples:**
```
=IF(A1>100, "High", "Low")
=IF(B1>=50, "Pass", "Fail")
=IF(C1="Active", "✓", "✗")
```

**Use Cases:**
- Status indicators
- Pass/fail grades
- Categorization
- Conditional display

---

### **13. CONCAT (CONCATENATE)** ⭐ NEW!
Combine text values together

**Syntax:**
```
=CONCAT(text1, text2, ...)
=CONCATENATE(text1, text2, ...)
```

**Examples:**
```
=CONCAT("Hello", " ", "World")  → "Hello World"
=CONCAT(A1, " ", B1)            → First name + Last name
=CONCAT("$", A1)                → Add $ prefix
```

**Use Cases:**
- **Combine first + last names**
- **Create full addresses**
- **Add prefixes/suffixes** (currency symbols, units)
- **Generate labels/codes**

**Name Combination Example:**
```
User Table:
┌────────────┬────────────┬──────────────┐
│ First Name │ Last Name  │  Full Name   │
├────────────┼────────────┼──────────────┤
│   John     │   Smith    │   [calc]     │
└────────────┴────────────┴──────────────┘

Formula for C2:
=CONCAT(A2, " ", B2)

Result: "John Smith"
```

---

## 📍 Cell References

### **Single Cell**
Reference one specific cell

**Format:** Column Letter + Row Number

**Examples:**
```
A1  → First column, first row
B5  → Second column, fifth row
Z99 → 26th column, 99th row
```

---

### **Cell Range**
Reference multiple cells

**Format:** Start:End

**Examples:**
```
A1:A10  → Column A, rows 1-10
B2:E2   → Row 2, columns B-E
A1:C5   → Rectangle from A1 to C5
```

---

### **Entire Column**
Reference all rows in a column

**Format:** Letter:Letter

**Examples:**
```
A:A     → Entire column A
B:B     → Entire column B
Z:Z     → Entire column Z
```

---

## 🧮 Mathematical Operations

### **Basic Operators**
```
+  Addition        =A1+B1
-  Subtraction     =A1-B1
*  Multiplication  =A1*B1
/  Division        =A1/B1
```

### **Order of Operations**
Use parentheses to control order

**Examples:**
```
=A1+B1*C1          → B1*C1 happens first
=(A1+B1)*C1        → A1+B1 happens first

=10+5*2            → 20 (5*2=10, then 10+10)
=(10+5)*2          → 30 ((10+5)=15, then 15*2)
```

---

## 💡 Real-World Use Cases

### **Example 1: E-commerce Order Total**

**Scenario:** Calculate order totals

```
Table:
┌─────────┬────────┬──────────┬─────────┐
│ Product │ Price  │ Quantity │  Total  │
├─────────┼────────┼──────────┼─────────┤
│ Item A  │  $25   │    3     │ [calc]  │
│ Item B  │  $50   │    2     │ [calc]  │
│ Item C  │  $15   │    5     │ [calc]  │
├─────────┼────────┼──────────┼─────────┤
│ TOTAL:  │        │          │ [calc]  │
└─────────┴────────┴──────────┴─────────┘

Formulas:
D2: =PRODUCT(B2, C2)  → $75
D3: =PRODUCT(B3, C3)  → $100
D4: =PRODUCT(B4, C4)  → $75
D5: =SUM(D2:D4)       → $250
```

---

### **Example 2: Investment Growth Calculator**

**Scenario:** Calculate compound interest

```
Table:
┌───────────┬──────────┬────────┬──────────────┐
│  Initial  │  Years   │  Rate  │ Final Value  │
├───────────┼──────────┼────────┼──────────────┤
│  $10,000  │    10    │   5%   │   [calc]     │
└───────────┴──────────┴────────┴──────────────┘

Formula for D2:
=ROUND(PRODUCT(A2, POWER(1.05, B2)), 2)

Result: $16,288.95
```

---

### **Example 3: Sales Statistics Dashboard**

**Scenario:** Analyze sales data

```
Table:
┌───────────┬────────┐
│  Metric   │ Value  │
├───────────┼────────┤
│ Sale 1    │  $450  │  ← B2
│ Sale 2    │ $1,200 │  ← B3
│ Sale 3    │  $750  │  ← B4
│ Sale 4    │  $600  │  ← B5
│ Sale 5    │  $920  │  ← B6
├───────────┼────────┤
│ Total     │ [calc] │  =SUM(B2:B6)     → $3,920
│ Average   │ [calc] │  =AVERAGE(B2:B6) → $784
│ Median    │ [calc] │  =MEDIAN(B2:B6)  → $750
│ Highest   │ [calc] │  =MAX(B2:B6)     → $1,200
│ Lowest    │ [calc] │  =MIN(B2:B6)     → $450
└───────────┴────────┘
```

---

### **Example 4: Customer Database with Full Names**

**Scenario:** Combine first and last names

```
Table:
┌────────────┬────────────┬──────────────┬───────────────────┐
│ First Name │ Last Name  │  Full Name   │      Email        │
├────────────┼────────────┼──────────────┼───────────────────┤
│   John     │   Smith    │   [calc]     │     [calc]        │
└────────────┴────────────┴──────────────┴───────────────────┘

Formulas:
C2: =CONCAT(A2, " ", B2)           → "John Smith"
D2: =CONCAT(A2, ".", B2, "@co.com")→ "John.Smith@co.com"
```

---

### **Example 5: Pythagorean Distance Calculator**

**Scenario:** Calculate distance between two points

```
Table:
┌────────┬────────┬──────────┐
│   X    │   Y    │ Distance │
├────────┼────────┼──────────┤
│   3    │   4    │  [calc]  │
│   6    │   8    │  [calc]  │
└────────┴────────┴──────────┘

Formula for C2:
=ROUND(SQRT(POWER(A2,2) + POWER(B2,2)), 2)

Results:
C2: 5.00   (√(3² + 4²) = √25 = 5)
C3: 10.00  (√(6² + 8²) = √100 = 10)
```

---

### **Example 6: Volume and Area Calculations**

**Scenario:** Calculate box volumes

```
Table:
┌────────┬────────┬────────┬────────┐
│ Length │ Width  │ Height │ Volume │
├────────┼────────┼────────┼────────┤
│   10   │   5    │   3    │ [calc] │
└────────┴────────┴────────┴────────┘

Formula for D2:
=PRODUCT(A2, B2, C2)

Result: 150 cubic units
```

---

### **Example 7: Performance Grading**

**Scenario:** Pass/Fail with statistical analysis

```
Table:
┌─────────┬────────┬────────┐
│ Student │ Score  │ Grade  │
├─────────┼────────┼────────┤
│ Alice   │   85   │ [calc] │
│ Bob     │   45   │ [calc] │
│ Carol   │   92   │ [calc] │
│ Dave    │   78   │ [calc] │
├─────────┼────────┼────────┤
│ Average │ [calc] │        │
│ Median  │ [calc] │        │
└─────────┴────────┴────────┘

Formulas:
C2-C5: =IF(B2>=60, "Pass", "Fail")
B6: =AVERAGE(B2:B5)  → 75
B7: =MEDIAN(B2:B5)   → 81.5
```

---

## 🔧 Formula Configuration

### **PHP Implementation:**
```php
use ATablesCharts\Formulas\Services\FormulaService;

$service = new FormulaService();

// Define formulas
$formulas = array(
    array(
        'target_row' => -1,        // -1 = new row, 0+ = existing row
        'target_col' => 'Total',   // Column name
        'formula'    => '=PRODUCT(B2, C2)'
    )
);

// Process formulas
$result = $service->process_formulas($data, $headers, $formulas);
```

### **AJAX Calculate:**
```javascript
$.ajax({
    url: ajaxurl,
    type: 'POST',
    data: {
        action: 'atables_calculate_formula',
        nonce: atables_nonce,
        table_id: tableId,
        formula: '=PRODUCT(A1, B1)'
    },
    success: function(response) {
        console.log('Result:', response.data.result);
    }
});
```

---

## 🎓 Best Practices

### **1. Always Start with =**
```
✅ =SUM(A1:A10)
❌ SUM(A1:A10)
```

### **2. Use ROUND for Currency**
```
✅ =ROUND(PRODUCT(A1, B1), 2)
❌ =PRODUCT(A1, B1)  // Might have many decimals
```

### **3. Use MEDIAN for Statistical Analysis**
```
✅ =MEDIAN(A1:A100)  // Better for data with outliers
⚠️ =AVERAGE(A1:A100) // Can be skewed by extremes
```

### **4. Check Cell References**
```
✅ =SUM(B2:B10)  // Correct range
❌ =SUM(B1:B10)  // Includes header
```

### **5. Use Parentheses for Clarity**
```
✅ =(A1+B1)*C1   // Clear order
❌ =A1+B1*C1     // Ambiguous
```

### **6. Combine Functions**
```
✅ =ROUND(PRODUCT(A1, POWER(1.05, B1)), 2)
   // Clean compound interest calculation
```

---

## 🐛 Troubleshooting

### **#ERROR: Invalid expression**
**Cause:** Syntax error in formula  
**Fix:** Check parentheses, operators, function names

### **#ERROR: Calculation error**
**Cause:** Division by zero or invalid operation  
**Fix:** Check for zero values, verify data types

### **Result is 0 unexpectedly**
**Cause:** Cell reference might be empty or non-numeric  
**Fix:** Verify data in referenced cells

### **Function not working**
**Cause:** Unknown function name  
**Fix:** Check spelling, use supported functions only

### **SQRT returns 0**
**Cause:** Negative number input  
**Fix:** SQRT of negative numbers returns 0 (not an error)

---

## ⚡ Performance Tips

### **Optimize Formulas:**
1. Use specific ranges vs entire columns when possible
2. Minimize nested functions
3. Cache complex calculations
4. Use simple formulas for better performance

### **Large Tables:**
- Process formulas server-side
- Consider pre-calculating
- Use pagination
- Limit formula complexity

---

## 📝 Quick Reference

### **All Functions:**
| Function | Purpose | Example |
|----------|---------|---------|
| SUM | Add values | =SUM(A1:A10) |
| AVERAGE | Mean value | =AVERAGE(B:B) |
| MEDIAN | Middle value | =MEDIAN(C1:C5) |
| MIN | Smallest | =MIN(D1:D5) |
| MAX | Largest | =MAX(E1:E5) |
| COUNT | Count values | =COUNT(F:F) |
| PRODUCT | Multiply | =PRODUCT(A1,B1) |
| POWER | Exponent | =POWER(2, 3) |
| SQRT | Square root | =SQRT(16) |
| ROUND | Round number | =ROUND(A1, 2) |
| ABS | Absolute | =ABS(A1-B1) |
| IF | Conditional | =IF(A1>10, "Y", "N") |
| CONCAT | Combine text | =CONCAT(A1, " ", B1) |

### **Operators:**
| Symbol | Operation | Example |
|--------|-----------|---------|
| + | Add | =A1+B1 |
| - | Subtract | =A1-B1 |
| * | Multiply | =A1*B1 |
| / | Divide | =A1/B1 |
| () | Group | =(A1+B1)*C1 |

### **References:**
| Format | Meaning | Example |
|--------|---------|---------|
| A1 | Single cell | =A1*2 |
| A1:A10 | Range | =SUM(A1:A10) |
| A:A | Column | =AVERAGE(A:A) |
| A1:C1 | Row range | =SUM(A1:C1) |

---

## 🎉 What's New in v1.0.5

### **5 NEW Functions Added!**

1. **MEDIAN** - Statistical middle value
   - Better than AVERAGE for skewed data
   - Perfect for salary analysis, price comparisons

2. **PRODUCT** - Multiply values
   - Essential for e-commerce (price × quantity)
   - Volume calculations (L × W × H)

3. **POWER** - Exponentiation
   - Compound interest calculations
   - Growth projections
   - Scientific formulas

4. **SQRT** - Square root
   - Distance calculations
   - Statistical analysis
   - Geometry problems

5. **CONCAT** - Text concatenation
   - Combine names (first + last)
   - Create full addresses
   - Generate labels/codes

---

**Formula Support brings Excel-level power to your WordPress tables! 🧮**

**13 professional functions** | **Cell references** | **Math operations** | **Easy to use**

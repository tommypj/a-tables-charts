# Formula Support - User Guide

## 📋 Overview
Formula Support brings spreadsheet-like calculations to your tables with functions, cell references, and mathematical operations!

## 🎯 Key Features

### **✅ 8 Built-in Functions**
Powerful calculation functions
- SUM - Add values
- AVERAGE/AVG - Calculate mean
- MIN - Find minimum
- MAX - Find maximum
- COUNT - Count values
- ROUND - Round numbers
- ABS - Absolute value
- IF - Conditional logic

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

## 🔢 Supported Functions

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
- Total sales
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

### **3. MIN**
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

### **4. MAX**
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

### **5. COUNT**
Count number of values

**Syntax:**
```
=COUNT(range)
```

**Examples:**
```
=COUNT(E1:E10)         → Count values E1-E10
=COUNT(A:A)            → Count all in column A
```

**Use Cases:**
- Number of entries
- Count products
- Total records
- Item count

---

### **6. ROUND**
Round number to decimals

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
- Clean numbers
- Display formatting

---

### **7. ABS**
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
- Distance calculations
- Variance/deviation
- Positive values
- Error margins

---

### **8. IF**
Conditional value based on condition

**Syntax:**
```
=IF(condition, true_value, false_value)
```

**Examples:**
```
=IF(A1>100, "High", "Low")
=IF(B1>=50, 1, 0)
=IF(C1="Active", "✓", "✗")
```

**Use Cases:**
- Status indicators
- Pass/fail
- Categorization
- Conditional display

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

### **Column Naming**
Understanding column letters

```
A = Column 1    (First column)
B = Column 2
C = Column 3
...
Z = Column 26
AA = Column 27
AB = Column 28
...
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

## 💡 Common Use Cases

### **Example 1: Sales Report Totals**

**Scenario:** Calculate total sales

```
Table:
┌─────────┬────────┐
│ Product │ Sales  │
├─────────┼────────┤
│ Item A  │ $100   │  ← Row 2
│ Item B  │ $200   │  ← Row 3
│ Item C  │ $150   │  ← Row 4
├─────────┼────────┤
│ TOTAL:  │ [calc] │  ← Row 5
└─────────┴────────┘

Formula for Row 5, Column B:
=SUM(B2:B4)

Result: $450
```

---

### **Example 2: Average Score**

**Scenario:** Calculate class average

```
Table:
┌─────────┬────────┐
│ Student │ Score  │
├─────────┼────────┤
│ Alice   │   85   │
│ Bob     │   92   │
│ Carol   │   78   │
├─────────┼────────┤
│ Average │ [calc] │
└─────────┴────────┘

Formula:
=AVERAGE(B2:B4)

Result: 85
```

---

### **Example 3: Price with Tax**

**Scenario:** Calculate total with 10% tax

```
Table:
┌─────────┬────────┬──────────┐
│ Item    │ Price  │   Total  │
├─────────┼────────┼──────────┤
│ Widget  │  $100  │  [calc]  │
└─────────┴────────┴──────────┘

Formula for C2:
=B2*1.10

Or with ROUND:
=ROUND(B2*1.10, 2)

Result: $110.00
```

---

### **Example 4: Profit Calculation**

**Scenario:** Revenue minus Cost

```
Table:
┌──────────┬─────────┬────────┬─────────┐
│ Product  │ Revenue │  Cost  │ Profit  │
├──────────┼─────────┼────────┼─────────┤
│ Product A│  $500   │  $300  │ [calc]  │
└──────────┴─────────┴────────┴─────────┘

Formula for D2:
=B2-C2

Result: $200
```

---

### **Example 5: Growth Percentage**

**Scenario:** Calculate % growth

```
Table:
┌───────┬─────────┬────────────┬─────────┐
│ Month │ Current │  Previous  │ Growth% │
├───────┼─────────┼────────────┼─────────┤
│  Jan  │  $1200  │   $1000    │ [calc]  │
└───────┴─────────┴────────────┴─────────┘

Formula for D2:
=ROUND(((B2-C2)/C2)*100, 1)

Result: 20.0%
```

---

### **Example 6: Conditional Status**

**Scenario:** Pass/Fail based on score

```
Table:
┌─────────┬────────┬────────┐
│ Student │ Score  │ Status │
├─────────┼────────┼────────┤
│ Alice   │   85   │ [calc] │
│ Bob     │   45   │ [calc] │
└─────────┴────────┴────────┘

Formula for C2:
=IF(B2>=60, "Pass", "Fail")

Results: "Pass", "Fail"
```

---

### **Example 7: Grand Total Summary**

**Scenario:** Multiple column totals

```
Table:
┌────────┬────────┬────────┬────────┐
│ Region │   Q1   │   Q2   │  Total │
├────────┼────────┼────────┼────────┤
│ East   │  $100  │  $150  │ [calc] │
│ West   │  $200  │  $180  │ [calc] │
├────────┼────────┼────────┼────────┤
│ TOTAL  │ [calc] │ [calc] │ [calc] │
└────────┴────────┴────────┴────────┘

Formulas:
D2: =SUM(B2:C2)    → $250
D3: =SUM(B3:C3)    → $380
B4: =SUM(B2:B3)    → $300
C4: =SUM(C2:C3)    → $330
D4: =SUM(D2:D3)    → $630
```

---

### **Example 8: Range Analysis**

**Scenario:** Min, Max, Average

```
Table:
┌───────────┬────────┐
│ Metric    │ Value  │
├───────────┼────────┤
│ Data 1    │   45   │  ← B2
│ Data 2    │   87   │  ← B3
│ Data 3    │   62   │  ← B4
│ Data 4    │   73   │  ← B5
├───────────┼────────┤
│ Minimum   │ [calc] │
│ Maximum   │ [calc] │
│ Average   │ [calc] │
└───────────┴────────┘

Formulas:
B6: =MIN(B2:B5)    → 45
B7: =MAX(B2:B5)    → 87
B8: =AVG(B2:B5)    → 66.75
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
        'formula'    => '=SUM(B2:B10)'
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
        formula: '=SUM(A1:A10)'
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
✅ =ROUND(SUM(A1:A10), 2)
❌ =SUM(A1:A10)  // Might have many decimals
```

### **3. Check Cell References**
```
✅ =SUM(B2:B10)  // Correct range
❌ =SUM(B1:B10)  // Includes header
```

### **4. Use Parentheses for Clarity**
```
✅ =(A1+B1)*C1   // Clear order
❌ =A1+B1*C1     // Ambiguous
```

### **5. Test Formulas Separately**
- Test each formula individually
- Verify results manually
- Check edge cases

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

## 🚀 Coming Soon

- **More Functions** - MEDIAN, MODE, STDEV, VAR
- **Text Functions** - CONCAT, LEFT, RIGHT, UPPER, LOWER
- **Date Functions** - TODAY, NOW, DATE, DATEDIF
- **Lookup Functions** - VLOOKUP, HLOOKUP, INDEX, MATCH
- **Formula Builder UI** - Visual formula creation
- **Formula Autocomplete** - Intelligent suggestions
- **Array Formulas** - Multi-cell calculations
- **Named Ranges** - Use names instead of cell refs
- **Relative References** - Copy formulas easily
- **Error Handling** - IFERROR, ISERROR

---

## 📝 Quick Reference

### **Functions:**
| Function | Purpose | Example |
|----------|---------|---------|
| SUM | Add values | =SUM(A1:A10) |
| AVERAGE | Mean value | =AVERAGE(B:B) |
| MIN | Smallest | =MIN(C1:C5) |
| MAX | Largest | =MAX(D1:D5) |
| COUNT | Count values | =COUNT(E:E) |
| ROUND | Round number | =ROUND(A1, 2) |
| ABS | Absolute | =ABS(A1-B1) |
| IF | Conditional | =IF(A1>10, "Y", "N") |

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

**Formula Support brings spreadsheet power to your tables! 🧮**

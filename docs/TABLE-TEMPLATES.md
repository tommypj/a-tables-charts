# Table Templates - User Guide

## 📋 Overview
Table Templates allow you to quickly apply pre-configured styles and settings to your tables. Save time by reusing proven table configurations!

## 🎨 Built-in Templates

### 1. **Pricing Table** 🏷️
Perfect for displaying pricing plans and packages.

**Features:**
- Modern green theme
- No search, pagination, or sorting
- Focused, clean presentation
- Ideal for: Pricing plans, subscription tiers, service packages

**Usage:**
```
[atable id="1" template="pricing_table"]
```

---

### 2. **Comparison Table** ⚖️
Compare products, services, or features side-by-side.

**Features:**
- Minimal bordered theme
- Sorting enabled (compare easily)
- No search or pagination
- Ideal for: Product comparisons, feature matrices, specifications

**Usage:**
```
[atable id="1" template="comparison_table"]
```

---

### 3. **Financial Report** 💰
Professional presentation for financial data.

**Features:**
- Striped theme for readability
- Full interactivity (search, sort, paginate)
- 25 rows per page
- Ideal for: Financial statements, accounting data, budgets

**Usage:**
```
[atable id="1" template="financial_report"]
```

---

### 4. **Product Catalog** 🛍️
Showcase products with images and details.

**Features:**
- Material design theme
- Cards mode (mobile-friendly)
- Full search and filtering
- 12 items per page
- Ideal for: Product listings, catalogs, portfolios

**Usage:**
```
[atable id="1" template="product_catalog"]
```

---

### 5. **Data Dashboard** 📊
Interactive data table with all features enabled.

**Features:**
- Classic blue theme
- All interactive features
- Export buttons visible
- 25 rows per page
- Ideal for: Analytics, reports, dashboards, large datasets

**Usage:**
```
[atable id="1" template="data_dashboard"]
```

---

### 6. **Simple List** 📝
Clean, minimal table for basic lists.

**Features:**
- Minimal theme
- Stack mode (vertical on mobile)
- No search, sort, or pagination
- Ideal for: Contact lists, simple data, small tables

**Usage:**
```
[atable id="1" template="simple_list"]
```

---

### 7. **Contact Directory** 📇
Display contact information beautifully.

**Features:**
- Material design
- Cards mode for mobile
- Search and sort enabled
- 20 contacts per page
- Auto-detects emails and phone numbers
- Ideal for: Team directories, contact lists, member databases

**Usage:**
```
[atable id="1" template="contact_directory"]
```

---

### 8. **Sales Report** 📈
Track sales performance with conditional formatting.

**Features:**
- Striped theme
- Full interactivity
- 50 rows per page
- Works great with conditional formatting
- Ideal for: Sales data, performance metrics, KPI tracking

**Usage:**
```
[atable id="1" template="sales_report"]
```

---

## 🎯 Template Categories

### **Business** 💼
- Product Catalog
- Contact Directory  
- Sales Report

### **Finance** 💵
- Financial Report

### **Pricing** 🏷️
- Pricing Table

### **Comparison** ⚖️
- Comparison Table

### **Data** 📊
- Data Dashboard

### **Custom** ⚙️
- Simple List

---

## 🔧 Combining Templates with Other Options

Templates set defaults, but you can override any setting:

### Example: Pricing Table with Custom Theme
```
[atable id="1" template="pricing_table" style="dark"]
```
Uses Pricing Table settings but applies Dark theme.

### Example: Product Catalog with Different Layout
```
[atable id="1" template="product_catalog" responsive_mode="stack"]
```
Uses Product Catalog but stacks vertically instead of cards.

### Example: Financial Report with More Rows
```
[atable id="1" template="financial_report" page_length="50"]
```
Uses Financial Report but shows 50 rows per page.

---

## 📊 Template Configuration Details

Each template configures these settings:

### **Theme/Style**
- Which visual theme to use (classic, modern, dark, etc.)

### **Responsive Mode**
- How table behaves on mobile (scroll, stack, cards)

### **Interactive Features**
- Search: Yes/No
- Pagination: Yes/No
- Sorting: Yes/No
- Export: Yes/No (where applicable)

### **Display Options**
- Rows per page
- Default sort order
- Column visibility

---

## 💡 Best Practices

### **Choose the Right Template**
1. **Pricing/Plans?** → Pricing Table
2. **Comparing Options?** → Comparison Table
3. **Financial Data?** → Financial Report
4. **Products with Images?** → Product Catalog
5. **Large Dataset?** → Data Dashboard
6. **Simple List?** → Simple List
7. **Contact Info?** → Contact Directory
8. **Sales/Performance?** → Sales Report

### **Customize When Needed**
- Start with a template
- Override specific settings as needed
- Templates save time, not creativity!

### **Test on Mobile**
- Each template has mobile optimizations
- Preview on phone/tablet
- Adjust responsive_mode if needed

---

## 🚀 Coming Soon

### **Custom Templates**
Save your own template configurations:
- Save current table settings as template
- Name and categorize your templates
- Share templates across tables
- Import/export template library

### **Template Gallery**
Browse and preview all templates visually:
- Live previews
- Filter by category
- Quick apply from gallery
- Template recommendations

### **Advanced Templates**
More specialized templates:
- Event Schedules
- Restaurant Menus
- Class Timetables
- Product Specifications
- Comparison Charts
- FAQ Tables

---

## 📚 Quick Reference

### **Minimal Effort Templates**
(No search, sort, pagination)
- `pricing_table`
- `comparison_table`
- `simple_list`

### **Full Featured Templates**
(Everything enabled)
- `data_dashboard`
- `financial_report`
- `sales_report`

### **Mobile-Optimized Templates**
(Cards/Stack mode)
- `product_catalog`
- `contact_directory`
- `simple_list`

---

## 🎓 Pro Tips

1. **Stack Similar Tables**: Use the same template for consistency
2. **Template First**: Choose template before customizing
3. **Mix Features**: Combine templates with conditional formatting
4. **Responsive Testing**: Each template has different mobile behavior
5. **Export Friendly**: Data Dashboard best for export-heavy use

---

## 📞 Need Help?

### **Template Not Right?**
- Try different category
- Override specific settings
- Combine with custom styling

### **Want New Template?**
- Custom templates coming soon!
- Suggest templates you need

### **Performance Issues?**
- Use pagination for large tables
- Disable features you don't need
- Choose appropriate rows_per_page

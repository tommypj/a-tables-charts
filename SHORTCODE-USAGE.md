# 🎉 Shortcode Working - Usage Guide

## ✅ Shortcode is Now Working!

The `[atable]` shortcode is fully functional with customization options!

---

## 📝 Basic Usage

```
[atable id="1"]
```

---

## 🎨 Customization Options

### **Control Table Width**

```
[atable id="1" width="800px"]
[atable id="1" width="50%"]
[atable id="1" width="1200px"]
```

### **Add Table Styles**

```
[atable id="1" style="striped"]
[atable id="1" style="bordered"]
[atable id="1" style="hover"]
```

### **Combine Options**

```
[atable id="1" width="1000px" style="striped"]
```

---

## 📊 Available Attributes

| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `id` | number | **required** | Table ID |
| `width` | CSS value | 100% | Table container width (800px, 50%, etc.) |
| `style` | default/striped/bordered/hover | default | Table visual style |

---

## 💡 Examples

### Example 1: Narrow Table
```
[atable id="1" width="600px"]
```

### Example 2: Half Width with Stripes
```
[atable id="2" width="50%" style="striped"]
```

### Example 3: Full Width with Hover
```
[atable id="3" width="100%" style="hover"]
```

### Example 4: Fixed Width with Border
```
[atable id="4" width="900px" style="bordered"]
```

---

## 🎯 Recommended Widths

- **Small tables (3-5 columns):** 600-800px
- **Medium tables (6-8 columns):** 900-1200px
- **Large tables (9+ columns):** 100% or 1400px+

---

## ✨ Current Features

- ✅ Display tables on frontend
- ✅ Custom table width
- ✅ Multiple visual styles
- ✅ Responsive design
- ✅ Auto word-wrapping
- ✅ Clean, professional styling

---

## 🚀 Coming Soon (Optional)

- Search functionality
- Pagination
- Sorting
- Column-specific widths

---

Enjoy your working shortcode! 🎉

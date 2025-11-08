# 🎨 Chart Shortcode - Complete Guide

## ✅ **Chart Shortcode is Working!**

Display beautiful charts on your website using the `[achart]` shortcode!

---

## 📝 **Basic Usage**

```
[achart id="1"]
```

---

## 🎨 **Customization Options**

### **Control Chart Size**

```
[achart id="1" width="800px" height="400px"]
```

### **Responsive Charts**

```
[achart id="1" width="100%" height="500px"]
```

---

## 📊 **Available Attributes**

| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `id` | number | **required** | Chart ID |
| `width` | CSS value | 100% | Chart container width |
| `height` | CSS value | 400px | Chart height |

---

## 💡 **Examples**

### **Example 1: Default Chart**
```
[achart id="1"]
```
Full width chart with 400px height.

### **Example 2: Fixed Size**
```
[achart id="2" width="600px" height="300px"]
```
600px wide, 300px tall chart.

### **Example 3: Tall Chart**
```
[achart id="3" height="600px"]
```
Full width with extra height for better visibility.

### **Example 4: Half Width**
```
[achart id="4" width="50%" height="400px"]
```
Takes up 50% of container width.

---

## 🎯 **How to Use**

### **Step 1: Create a Chart**
1. Go to **a-tables-charts → Charts**
2. Click **"Create New Chart"**
3. Select a table
4. Choose chart type
5. Save chart

### **Step 2: Get Shortcode**
1. Go to **a-tables-charts → Charts**
2. Click **"Shortcode"** button on your chart
3. Shortcode is automatically copied!

### **Step 3: Add to Page**
1. Edit any page or post
2. Add a **Shortcode block**
3. Paste: `[achart id="1"]`
4. Publish and view!

---

## 📈 **Supported Chart Types**

All chart types work with the shortcode:

- ✅ **Bar Charts** - Compare values
- ✅ **Line Charts** - Show trends over time
- ✅ **Pie Charts** - Show proportions
- ✅ **Doughnut Charts** - Modern pie alternative

---

## 🎨 **Recommended Sizes**

### **Bar Charts**
```
[achart id="1" width="800px" height="400px"]
```

### **Line Charts**
```
[achart id="2" width="100%" height="350px"]
```

### **Pie/Doughnut Charts**
```
[achart id="3" width="500px" height="500px"]
```

---

## 🔧 **Advanced Usage**

### **Multiple Charts on One Page**
```
[achart id="1" width="48%" height="300px"]
[achart id="2" width="48%" height="300px"]
```

Use CSS to float them side-by-side if needed.

### **Responsive Dashboard**
```
[achart id="1" width="100%" height="300px"]
[achart id="2" width="100%" height="300px"]
[achart id="3" width="100%" height="300px"]
```

Stacks nicely on mobile!

---

## ✨ **Features**

- ✅ **Responsive** - Works on all devices
- ✅ **Interactive** - Hover to see values
- ✅ **Customizable** - Control size and appearance
- ✅ **Fast Loading** - Chart.js CDN
- ✅ **Professional** - Beautiful, modern charts
- ✅ **Copy Button** - Easy shortcode copying

---

## 🎯 **Best Practices**

1. **Use appropriate height** - Don't make charts too short
2. **Match chart type to data** - Bar for comparisons, Line for trends
3. **Keep it simple** - Don't overcrowd with too many data points
4. **Test on mobile** - Ensure charts display well on small screens
5. **Use consistent sizes** - Makes pages look more professional

---

## 🚫 **Common Issues**

### **Chart Not Displaying?**
- Check chart ID is correct
- Make sure chart status is "Active"
- Verify Chart.js is loading (check browser console)

### **Chart Too Small?**
- Increase the `height` parameter
- Try `height="500px"` or more

### **Chart Not Responsive?**
- Use `width="100%"` for full responsiveness
- Test on different screen sizes

---

## 📱 **Mobile Optimization**

Charts automatically adjust for mobile devices:
- Touch interactions work
- Legends adjust position
- Responsive sizing
- Fast rendering

---

## 🎉 **You're Ready!**

Start displaying beautiful charts on your website:

1. **Create charts** in admin
2. **Copy shortcode** from charts page
3. **Paste into pages** where you want charts
4. **Publish** and enjoy!

---

**Example Full Usage:**

```
[achart id="1" width="800px" height="400px"]
```

This creates a beautiful, interactive chart that your visitors will love! 📊✨

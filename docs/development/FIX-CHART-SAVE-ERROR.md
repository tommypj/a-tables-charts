# 🔧 Fix for "Failed to save chart" Error

## Problem
You're getting "Failed to save chart" error because the `wp_atables_charts` table is missing the `type` column.

## ✅ Quick Fix - Choose ONE Method:

---

### **Method 1: Automatic Fix (RECOMMENDED)** ⭐

1. **Refresh your WordPress admin dashboard**
2. You should see a **yellow warning notice** that says:
   ```
   A-Tables & Charts: Database update required. [Update Database]
   ```
3. **Click the "Update Database" button**
4. Wait for success message
5. **Try creating a chart again** - it should work now!

---

### **Method 2: Manual Script**

If the automatic notice doesn't appear, run the update script manually:

1. **Open your browser** and go to:
   ```
   http://your-site.local/wp-content/plugins/a-tables-charts/update-charts-table.php
   ```
   
2. You should see: **"✅ Success! Charts table updated successfully!"**

3. **Delete the file** after running it:
   ```
   wp-content/plugins/a-tables-charts/update-charts-table.php
   ```

4. **Try creating a chart again** - it should work now!

---

### **Method 3: Deactivate/Reactivate Plugin**

1. Go to **Plugins** in WordPress admin
2. **Deactivate** "A-Tables & Charts"
3. **Activate** it again
4. This will run the complete database setup
5. **Try creating a chart again** - it should work now!

---

## ✅ After Fixing

Once you've used one of the methods above:

1. Go back to **a-tables-charts → Create Chart**
2. Select a table
3. Configure your chart:
   - Enter a title
   - Select chart type (Bar, Line, Pie, or Doughnut)
   - Choose label column (X-axis)
   - Select at least one data column (Y-axis)
4. Click **"Preview Chart"**
5. You should see **Step 3** with the **"Save Chart"** button
6. Click **"Save Chart"**
7. Success! 🎉

---

## 🎯 What Changed

The fix adds the missing `type` column to the `wp_atables_charts` table:

```sql
ALTER TABLE wp_atables_charts 
ADD COLUMN `type` varchar(50) NOT NULL DEFAULT 'bar' 
AFTER `title`
```

This column stores the chart type (bar, line, pie, doughnut).

---

## 💡 Why This Happened

The charts table was created before the `type` column was added to the migration script. This sometimes happens during development when features are added incrementally.

---

## 🆘 Still Having Issues?

If none of the methods work:

1. Check the browser console for JavaScript errors
2. Check WordPress debug.log for PHP errors
3. Verify the table structure in your database:
   - Open phpMyAdmin
   - Find the `wp_atables_charts` table
   - Check if the `type` column exists

Let me know if you need help! 😊

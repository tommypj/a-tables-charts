# Bulk Edit Rows - User Guide

## 📋 Overview
Bulk Edit Rows allows you to perform operations on multiple rows at once, saving time when managing large tables.

## 🎯 Features

### **1. Select Multiple Rows**
- Check individual row checkboxes
- Use "Select All" to select all visible rows
- Visual highlight shows selected rows

### **2. Bulk Delete** 🗑️
Delete multiple rows in one action.

**Steps:**
1. Select rows using checkboxes
2. Click "Delete" button in bulk actions bar
3. Confirm deletion
4. Rows are removed permanently

**Use Cases:**
- Remove outdated data
- Clean up test entries
- Delete duplicate rows

---

### **3. Bulk Duplicate** 📋
Create copies of selected rows.

**Steps:**
1. Select rows to duplicate
2. Click "Duplicate" button
3. Confirm action
4. Duplicates are added at the bottom of the table

**Use Cases:**
- Create template rows
- Duplicate similar entries
- Quick data entry

---

### **4. Bulk Edit** ✏️
Change a specific column value for all selected rows.

**Steps:**
1. Select rows to edit
2. Click "Edit" button
3. Choose column to update
4. Enter new value
5. Apply to all selected rows

**Use Cases:**
- Update prices across multiple products
- Change status for multiple items
- Bulk categorization
- Mass updates

---

## 🎨 User Interface

### **Selection Controls**
- **Checkbox Column:** First column with checkboxes
- **Select All:** Master checkbox in header
- **Selected Count:** Shows number of selected rows

### **Bulk Actions Bar**
Fixed bar at bottom of screen showing:
- Number of selected rows
- Action buttons (Delete, Duplicate, Edit)
- Clear Selection button

**Colors:**
- Blue bar: Indicates active selection
- Delete button: Red (destructive action)
- Duplicate button: Cyan (copy action)
- Edit button: Green (modify action)
- Clear button: Gray (neutral action)

---

## ⚡ Keyboard Shortcuts

- `Ctrl/Cmd + Click`: Select multiple individual rows
- `Shift + Click`: Select range of rows
- `Escape`: Clear selection
- `Ctrl/Cmd + A`: Select all rows (when table focused)

---

## 💡 Best Practices

### **Before Bulk Delete:**
✅ Always double-check selection
✅ Consider exporting data first as backup
✅ Remember: deletion is permanent!

### **When Bulk Editing:**
✅ Preview changes on one row first
✅ Edit one column at a time
✅ Verify the new value is correct

### **For Large Operations:**
✅ Work in batches (50-100 rows)
✅ Save frequently between operations
✅ Test on copy of table first

---

## 🔒 Safety Features

### **Confirmation Prompts**
- Delete action requires confirmation
- Shows exact number of affected rows
- Cancel anytime before confirmation

### **Undo Support**
- No automatic undo (coming soon!)
- Use browser back button immediately after action
- Keep backups of important data

### **Permission Checks**
- Only administrators can use bulk edit
- All actions are logged
- Security nonces prevent unauthorized access

---

## 📊 Performance Notes

### **Optimal Batch Sizes:**
- **Small tables (< 100 rows):** Select all is fine
- **Medium tables (100-1000 rows):** Batch of 100-200
- **Large tables (1000+ rows):** Batch of 50-100

### **Loading Times:**
- Bulk delete: ~1-2 seconds per 100 rows
- Bulk duplicate: ~2-3 seconds per 100 rows
- Bulk edit: ~1-2 seconds per 100 rows

---

## 🐛 Troubleshooting

### **Checkboxes Not Appearing?**
- Clear browser cache
- Refresh page
- Check if bulk edit is enabled for your role

### **Bulk Actions Bar Not Showing?**
- Select at least one row
- Ensure JavaScript is enabled
- Check browser console for errors

### **Action Failed?**
- Check internet connection
- Verify table permissions
- Try with fewer rows
- Contact support if persists

---

## 🚀 Coming Soon

### **Advanced Features:**
- Undo/Redo support
- Bulk edit multiple columns at once
- Bulk find and replace
- Bulk conditional edit (IF-THEN rules)
- Bulk import/export selected rows
- History of bulk operations
- Scheduled bulk operations
- Bulk validation checks

### **UI Improvements:**
- Drag-to-select rows
- Right-click context menu
- Bulk edit preview mode
- Progress bar for large operations
- Bulk edit templates

---

## 📝 Examples

### **Example 1: Update Product Prices**
```
Scenario: Increase all product prices by changing them to $29.99

Steps:
1. Select all product rows
2. Click "Edit"
3. Select "Price" column
4. Enter "$29.99"
5. Apply changes
```

### **Example 2: Remove Test Data**
```
Scenario: Delete all rows marked as "Test"

Steps:
1. Filter or search for "Test" status
2. Select visible test rows
3. Click "Delete"
4. Confirm deletion
```

### **Example 3: Duplicate Templates**
```
Scenario: Create 5 copies of a template row

Steps:
1. Select the template row
2. Click "Duplicate" 
3. Repeat 4 more times
(Future: Specify duplicate count!)
```

---

## 🎓 Pro Tips

1. **Use Filters First:** Filter data before bulk selecting for precision
2. **Sort Before Select:** Sort by column to group similar data
3. **Export First:** Always export before bulk delete
4. **Test Small:** Try bulk edit on 2-3 rows first
5. **Incremental Saves:** Don't select too many rows at once
6. **Column Visibility:** Hide unnecessary columns for clarity
7. **Keyboard Efficiency:** Learn keyboard shortcuts
8. **Backup Strategy:** Keep regular table backups

---

## 📞 Need Help?

- **Feature Request?** Suggest new bulk operations
- **Bug Found?** Report with steps to reproduce
- **Training Needed?** Video tutorials coming soon

---

## ⚠️ Important Notes

- **Bulk delete is permanent** - no undo available yet
- **Large operations** may take time - be patient
- **Browser limits** may affect very large selections
- **Export before major changes** - safety first!
- **Test on copy** for important data

---

**Bulk Edit Rows makes table management fast and efficient! 🚀**

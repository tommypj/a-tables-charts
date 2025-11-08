# Google Sheets Import - User Guide

## 📋 Overview
Import data directly from Google Sheets into your WordPress tables with live sync support. Keep your tables updated automatically when your Google Sheet changes!

## 🚀 Quick Start

### **Step 1: Prepare Your Google Sheet**
1. Open your Google Sheet
2. Click **Share** button (top-right)
3. Click "Change to anyone with the link"
4. Set permission to **Viewer**
5. Click **Copy link**

### **Step 2: Import to WordPress**
1. Go to **A-Tables → Create Table**
2. Click **Google Sheets** option
3. Paste your Google Sheets URL
4. Click **Test Connection** (optional but recommended)
5. Enter table name (optional)
6. Enable **Auto-Sync** if you want to keep data updated
7. Click **Import from Google Sheets**

### **That's it!** Your table is created! 🎉

---

## 🎯 Key Features

### **✅ Simple URL Import**
- Just paste the Google Sheets URL
- No API keys or authentication needed
- Works with public sheets instantly

### **✅ Test Connection**
- Verify access before importing
- Get instant feedback
- Catch sharing issues early

### **✅ Auto-Sync Support**
- Keep tables in sync with Google Sheets
- Manual sync on demand
- Shows last sync time

### **✅ Multiple Sheet Support**
- Import specific tabs by GID
- Works with published sheets
- Handles different URL formats

---

## 📊 Supported URL Formats

### **Standard URL:**
```
https://docs.google.com/spreadsheets/d/SHEET_ID/edit
```

### **With Specific Tab (GID):**
```
https://docs.google.com/spreadsheets/d/SHEET_ID/edit#gid=123456
```

### **Published URL:**
```
https://docs.google.com/spreadsheets/d/SHEET_ID/pubhtml
```

**All formats work!** Just paste the URL.

---

## 🔒 Sharing Requirements

### **Required Permissions:**
Your Google Sheet **MUST** be set to:
- **"Anyone with the link can view"**

### **How to Check:**
1. Open your Google Sheet
2. Click **Share**
3. Under "Get link", check the sharing setting
4. Should say "Anyone with the link"

### **Common Errors:**
❌ **"Access Denied"** → Sheet is private  
✅ **Solution:** Change sharing to "Anyone with the link"

❌ **"Could not fetch data"** → Wrong URL or network issue  
✅ **Solution:** Check URL and internet connection

---

## 🔄 Auto-Sync Feature

### **What is Auto-Sync?**
When enabled, your WordPress table remembers the Google Sheets source and allows manual syncing to update data.

### **How to Use:**
1. **During Import:** Check "Keep this table synced"
2. **After Import:** Click **Sync** button in table view
3. **Result:** Table data updates from Google Sheets

### **When to Use Auto-Sync:**
✅ Data changes frequently  
✅ Multiple people update the sheet  
✅ Want to maintain single source of truth  
✅ Need real-time updates

### **When NOT to Use:**
❌ One-time import only  
❌ Data won't change  
❌ You'll edit in WordPress directly

---

## 💡 Best Practices

### **1. Data Structure**
✅ **First row = Headers**
- Use clear column names
- Avoid special characters
- Keep headers short

✅ **Clean Data**
- Remove empty rows/columns
- Consistent formatting
- No merged cells (they may cause issues)

### **2. Sheet Organization**
✅ **Simple is Better**
- Flat tables work best
- Avoid complex formulas in display cells
- Use separate sheets for different tables

### **3. Performance**
✅ **Size Considerations**
- Works with sheets up to 10,000 rows
- Larger sheets may be slower
- Consider pagination for big datasets

---

## 🎨 Use Cases

### **1. Price Lists** 💰
Keep product pricing always current
- Team updates Google Sheet
- Prices sync to website
- No manual WordPress updates needed

### **2. Event Schedules** 📅
Dynamic event calendars
- Add/update events in Sheet
- Sync to display on site
- Real-time accuracy

### **3. Team Directories** 👥
Maintain contact lists
- HR updates Google Sheet
- Directory syncs automatically
- Always up-to-date

### **4. Inventory Tracking** 📦
Live stock levels
- Update quantities in Sheet
- Website shows current stock
- Prevent overselling

### **5. Data Dashboards** 📊
Analytics and reports
- Pull data from Google Sheets
- Display with conditional formatting
- Auto-refresh for accuracy

---

## 🔧 Troubleshooting

### **Problem: "Access Denied"**
**Cause:** Sheet is not publicly accessible  
**Fix:**
1. Open Google Sheet
2. Click Share → "Anyone with the link can view"
3. Try import again

### **Problem: "Invalid URL"**
**Cause:** Wrong URL format  
**Fix:**
- Copy URL from browser address bar
- Must contain "docs.google.com/spreadsheets"
- Include the full URL

### **Problem: "Sheet is Empty"**
**Cause:** No data in sheet or wrong tab  
**Fix:**
- Check sheet has data
- Try different tab (GID)
- Ensure first row has headers

### **Problem: "Connection Timeout"**
**Cause:** Network or server issue  
**Fix:**
- Check internet connection
- Try again in a few moments
- Verify Google Sheets is accessible

### **Problem: "Data Not Syncing"**
**Cause:** Sheet URL changed or permissions changed  
**Fix:**
- Check original sheet still exists
- Verify sharing permissions
- Re-import if needed

---

## ⚡ Advanced Tips

### **1. Using Multiple Tabs**
To import specific tab:
1. Open the tab you want
2. Copy URL (includes #gid=NUMBER)
3. Import using this URL

### **2. Data Formatting**
- Numbers: Detect automatically
- Dates: Parsed automatically  
- Currency: Detected and formatted
- URLs: Become clickable links
- Emails: Become mailto links

### **3. Large Sheets**
For sheets with 1000+ rows:
- Enable pagination
- Use search/filter
- Consider splitting into multiple tables

### **4. Regular Updates**
Best practices:
- Sync during off-peak hours
- Test sync after sheet changes
- Monitor for errors

---

## 🚨 Limitations

### **Current Limitations:**
1. Sheet must be publicly accessible
2. Manual sync (not automatic on schedule)
3. CSV export format (some formatting may be lost)
4. No real-time updates (sync on demand only)
5. Single sheet at a time

### **Coming Soon:**
- Scheduled automatic sync
- Private sheet access (OAuth)
- Multi-sheet import
- Column mapping
- Data transformation rules

---

## 📚 Examples

### **Example 1: Product Catalog**
```
Google Sheet Structure:
Product Name | Price | Stock | Description

Import Process:
1. Share sheet publicly
2. Copy URL
3. Import to WordPress
4. Enable auto-sync
5. Sync whenever products change
```

### **Example 2: Restaurant Menu**
```
Google Sheet Structure:
Dish Name | Category | Price | Description | Image URL

Benefits:
- Update menu in Google Sheets
- Sync to website
- Images display automatically
- Always current
```

### **Example 3: Class Schedule**
```
Google Sheet Structure:
Course | Time | Instructor | Room | Status

Workflow:
- Admin updates in Google Sheets
- Click sync in WordPress
- Students see latest schedule
- No coding required
```

---

## ❓ FAQ

### **Q: Do I need a Google account?**
A: You need a Google account to create/edit the sheet, but importing doesn't require login.

### **Q: Is my data secure?**
A: Data is fetched via Google's public export. Keep sensitive data in private sheets (don't import those).

### **Q: How often should I sync?**
A: Depends on update frequency. Manual sync whenever the sheet changes significantly.

### **Q: Can I edit data in WordPress?**
A: Yes! After import, edit in WordPress. But syncing will overwrite changes with Google Sheets data.

### **Q: What happens if I delete the Google Sheet?**
A: Your WordPress table keeps the last imported data. Sync will fail until you update the URL.

### **Q: Can I import from Excel?**
A: Upload Excel file to Google Sheets first, then import from there. Or use direct Excel import option.

### **Q: Is there a file size limit?**
A: Google Sheets export is limited by Google. Generally up to 10,000 rows work well.

---

## 🎓 Pro Tips

1. **Test First:** Always use "Test Connection" before importing
2. **Name Your Tables:** Use descriptive names for easy management
3. **Enable Auto-Sync:** For data that changes frequently
4. **Clean Your Sheet:** Remove unnecessary columns/rows before import
5. **Use Templates:** Apply table templates after import
6. **Bookmark URL:** Save your Google Sheets URL for future reference
7. **Document Changes:** Add notes when syncing to track updates
8. **Regular Backups:** Export tables periodically

---

## 🔗 Related Features

Combine Google Sheets Import with:
- **Table Templates** - Apply professional styling
- **Conditional Formatting** - Highlight important data
- **Column Formatting** - Customize appearance
- **Mobile Responsive** - Perfect display on all devices
- **Export Options** - Share data in multiple formats

---

**Google Sheets Import makes table management effortless! 🚀**

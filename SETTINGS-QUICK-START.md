# ⚡ Settings Quick Start Guide

## 🎯 5-Minute Settings Test

Follow these steps to quickly verify your settings are working:

---

## Step 1: Change Rows Per Page (30 seconds)

1. Go to: **WordPress Admin → a-tables-charts → Settings**
2. Find: "Default Rows per Page"
3. Change from `10` to `5`
4. Click: "**Save All Settings**"
5. Wait for success message

**✅ Expected:** "Settings saved successfully!" message appears

---

## Step 2: Check Dashboard (30 seconds)

1. Go to: **a-tables-charts → All Tables** (Dashboard)
2. Look at the table list

**✅ Expected:** You should see exactly **5 tables per page** (or fewer if you have less than 5 tables)

**❌ If not working:** 
- Settings might not have saved properly
- Go back to settings and verify the value is still 5
- Try clicking save again

---

## Step 3: Disable Search (1 minute)

1. Go back to: **Settings**
2. Find: "Frontend Features" section
3. **Uncheck**: "Search Functionality"
4. Click: "**Save All Settings**"

**✅ Expected:** Checkbox is unchecked, success message appears

---

## Step 4: Test Frontend (1 minute)

### Create a test page:

1. Go to: **Pages → Add New**
2. Title: "Test Table"
3. Add shortcode in content: `[atable id="1"]` (use your actual table ID)
4. Click: "**Publish**"
5. Click: "**View Page**"

**✅ Expected:** 
- Table displays on page
- **NO search box** above the table
- Table shows **5 rows per page**
- Pagination controls visible at bottom

**❌ If search box still shows:**
- Clear browser cache (Ctrl+Shift+R)
- Make sure shortcode doesn't have `search="true"`
- Use basic shortcode: `[atable id="1"]` with no extra attributes

---

## Step 5: Re-enable Search (30 seconds)

1. Go back to: **Settings**
2. **Check**: "Search Functionality"
3. Click: "**Save All Settings**"
4. **Refresh** your test page

**✅ Expected:** 
- Search box now appears above table
- You can type in it to filter rows

---

## Step 6: Change Table Style (1 minute)

1. Go to: **Settings**
2. Find: "Default Table Style"
3. Change to: **"Striped Rows"**
4. Click: "**Save All Settings**"
5. **Refresh** your test page

**✅ Expected:** 
- Table rows have alternating background colors (striped appearance)

Try other styles:
- **Bordered:** Shows borders around each cell
- **Hover:** Highlights rows when you mouse over them

---

## 🎉 Success!

If all 6 steps worked, your settings integration is working perfectly!

---

## 🚨 If Something Didn't Work

### Quick Fixes:

1. **Clear all caches:**
   - Browser: `Ctrl + Shift + R` (Windows) or `Cmd + Shift + R` (Mac)
   - WordPress: Install and activate "WP Super Cache" → Clear cache
   - Or disable any caching plugins temporarily

2. **Check your shortcode:**
   - Remove all attributes: `[atable id="1"]`
   - Don't use: `[atable id="1" search="true" pagination="true"]` (these override settings)

3. **Verify settings saved:**
   - Go back to Settings page
   - Check if your changes are still there
   - If values reset, there might be a validation issue

4. **Check for JavaScript errors:**
   - Press `F12` to open browser console
   - Look for red error messages
   - Common error: "DataTables is not defined"

5. **Try different browser:**
   - Sometimes browser extensions interfere
   - Try in Chrome Incognito or Firefox Private mode

---

## 📋 Settings Cheat Sheet

### What Each Setting Does:

| Setting | Controls |
|---------|----------|
| **Rows per Page** | How many rows show per page (1-100) |
| **Table Style** | Visual appearance (default/striped/bordered/hover) |
| **Responsive** | Tables adapt to mobile screens |
| **Search** | Shows/hides search box |
| **Sorting** | Can click headers to sort |
| **Pagination** | Shows page numbers or all rows |
| **Export** | Shows export buttons (future feature) |

### Shortcode Override:

Settings are **defaults**. Shortcode attributes can override:

```
Settings: search = disabled
Shortcode: [atable id="1"]  ← Uses setting (no search)
Shortcode: [atable id="1" search="true"]  ← Overrides setting (shows search)
```

---

## 🔄 Reset to Start Over

If you want to start fresh:

1. Go to **Settings**
2. Scroll to bottom
3. Click "**Reset to Defaults**"
4. Confirm when prompted
5. All settings return to:
   - Rows per page: 10
   - Style: Default
   - All features: Enabled

---

## 📊 Visual Reference

### Before Settings Change:
```
Dashboard: Shows 10 tables per page
Frontend:  Shows 10 rows per page
           Search box: ✅ Visible
           Pagination: ✅ Visible
           Style: Plain (default)
```

### After Settings Change (rows=5, search=off, style=striped):
```
Dashboard: Shows 5 tables per page
Frontend:  Shows 5 rows per page
           Search box: ❌ Hidden
           Pagination: ✅ Visible
           Style: Striped (alternating colors)
```

---

## 🎯 Next Steps

Once basic settings work:

1. **Try all styles:** Default, Striped, Bordered, Hover
2. **Test each feature:** Search, Sorting, Pagination
3. **Test different values:** 10, 25, 50 rows per page
4. **Test shortcode overrides:** Add attributes to shortcode
5. **Test on mobile:** Check responsive tables
6. **Test caching:** Enable cache, change data, see cache effect

---

## 📞 Need More Help?

Check these documents in order:

1. **SETTINGS-TESTING-GUIDE.md** → Comprehensive testing of all settings
2. **SETTINGS-INTEGRATION-SUMMARY.md** → Technical details of how it works
3. **SETTINGS-TROUBLESHOOTING.md** → Solutions to common problems

---

## ✅ Quick Verification

Your settings are working if:

- [ ] Changing rows per page affects dashboard
- [ ] Changing rows per page affects frontend default
- [ ] Disabling search removes search box on frontend
- [ ] Enabling search shows search box on frontend
- [ ] Changing table style changes visual appearance
- [ ] Settings persist after page reload
- [ ] Settings persist after logout/login
- [ ] Shortcode attributes can override settings
- [ ] Reset to defaults works

**All checked?** 🎉 **You're all set!**

---

**Total Time:** 5 minutes
**Difficulty:** Easy
**Result:** Fully working settings system!

**Last Updated:** October 13, 2025

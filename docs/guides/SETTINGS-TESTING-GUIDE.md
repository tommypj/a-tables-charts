# Settings Page Testing Guide

## 🧪 Quick Testing Checklist

### Pre-Testing Setup
1. Navigate to: **WordPress Admin → A-Tables & Charts → Settings**
2. Have a backup of your database (just in case)
3. Open browser console (F12) to check for errors

---

## ✅ Test 1: Visual Inspection (2 minutes)

**Goal:** Verify all sections display correctly

### Steps:
1. ✅ Scroll through entire page
2. ✅ Verify all 7 sections are visible:
   - General Settings
   - Data Formatting
   - Import Settings (NEW!)
   - Export Settings (NEW!)
   - Performance & Cache
   - Chart Settings
   - Security Settings (NEW!)
3. ✅ Check sidebar displays:
   - System Information
   - Need Help section
4. ✅ Verify no layout issues
5. ✅ Check responsive design (resize browser)

**Expected:** Clean, professional layout with no overlapping elements

---

## ✅ Test 2: Default Values (2 minutes)

**Goal:** Verify all fields have correct defaults

### Check These Values:
- [ ] Rows per page: **10**
- [ ] Table style: **Default**
- [ ] All frontend features: **Checked**
- [ ] Date format: **Y-m-d**
- [ ] Time format: **H:i:s**
- [ ] Decimal separator: **.**
- [ ] Thousands separator: **,**
- [ ] Max import size: **10 MB**
- [ ] CSV delimiter: **,**
- [ ] CSV enclosure: **"**
- [ ] CSV escape: **\\**
- [ ] Export filename: **table-export**
- [ ] Export date format: **Y-m-d**
- [ ] Export encoding: **UTF-8**
- [ ] Cache enabled: **Checked**
- [ ] Cache duration: **3600**
- [ ] Lazy load: **Unchecked**
- [ ] Async loading: **Unchecked**
- [ ] Chart.js: **Checked**
- [ ] Google Charts: **Checked**
- [ ] Default library: **Chart.js**
- [ ] All file types: **Checked** (CSV, JSON, Excel, XML)
- [ ] Sanitize HTML: **Checked**

---

## ✅ Test 3: Save Settings (3 minutes)

**Goal:** Verify settings save correctly

### Steps:
1. Change several values:
   - Rows per page → **25**
   - Table style → **Striped Rows**
   - Disable search → **Uncheck**
   - Date format → **m/d/Y**
   - Max import size → **20 MB**
   - Export filename → **my-export**

2. Click **"Save All Settings"**

3. Look for success message: **"Settings saved successfully!"**

4. Refresh the page (F5)

5. Verify all changes persisted

**Expected:** Settings save and persist after page refresh

---

## ✅ Test 4: Validation Tests (5 minutes)

**Goal:** Test input validation

### Test Invalid Values:

#### Rows Per Page:
- [ ] Try **0** → Should save as **1** (minimum)
- [ ] Try **999** → Should save as **100** (maximum)
- [ ] Try **-5** → Should save as **1** (minimum)

#### Max Import Size:
- [ ] Try **0** → Should save as **1 MB** (minimum)
- [ ] Try **999** → Should save as **100 MB** (maximum)

#### Cache Duration:
- [ ] Try **-100** → Should save as **0** (minimum)
- [ ] Try any positive number → Should save correctly

**Expected:** Invalid values are automatically corrected

---

## ✅ Test 5: File Type Security (3 minutes)

**Goal:** Ensure at least one file type is always allowed

### Steps:
1. Go to Security Settings section
2. **Uncheck ALL file types** (CSV, JSON, Excel, XML)
3. Click **"Save All Settings"**
4. Refresh page
5. Check Security Settings section

**Expected:** At least CSV should be re-checked (can't have zero file types)

---

## ✅ Test 6: Reset to Defaults (2 minutes)

**Goal:** Test reset functionality

### Steps:
1. Make several changes to various settings
2. Click **"Reset to Defaults"** button
3. Confirm the prompt
4. Wait for page reload
5. Verify all settings are back to defaults

**Expected:** All settings restored to original defaults

---

## ✅ Test 7: Cache Management (3 minutes)

**Goal:** Test cache controls

### Cache Statistics:
- [ ] Verify stats display correctly
- [ ] Check cache size shows KB value
- [ ] Verify hit rate percentage displays

### Clear Cache:
1. Click **"Clear All Cache"** button
2. Confirm the prompt
3. Wait for success message
4. Page should reload
5. Cache size should be **0 KB** or very small

### Reset Statistics:
1. Click **"Reset Statistics"** button
2. Confirm the prompt
3. Wait for success message
4. Page should reload
5. All stats should be **0**

**Expected:** Both buttons work without errors

---

## ✅ Test 8: UI/UX Elements (2 minutes)

**Goal:** Test interactive elements

### Badges:
- [ ] Info badges (blue) display correctly
- [ ] Success badges (green) display correctly
- [ ] Warning badges (yellow) display correctly
- [ ] Primary badges (blue) display correctly

### Help Text:
- [ ] All help text is readable
- [ ] Examples display correctly (dates, numbers)
- [ ] Links work (PHP date() documentation)

### Icons:
- [ ] All Dashicons display properly
- [ ] No missing icons

**Expected:** Professional, polished appearance

---

## ✅ Test 9: System Information (1 minute)

**Goal:** Verify system info displays

### Check Sidebar:
- [ ] Plugin version shows correctly
- [ ] WordPress version displays
- [ ] PHP version displays
- [ ] MySQL version displays
- [ ] Upload max size shows
- [ ] Memory limit shows

**Expected:** All system info displays real values

---

## ✅ Test 10: Browser Compatibility (5 minutes)

**Goal:** Test in different browsers

### Test in:
- [ ] **Chrome** - All features work
- [ ] **Firefox** - All features work
- [ ] **Safari** (if available) - All features work
- [ ] **Edge** - All features work

### What to Check:
- Layout displays correctly
- Settings save properly
- Buttons work
- Cache functions work
- No console errors

**Expected:** Works consistently across browsers

---

## ✅ Test 11: Mobile Responsiveness (3 minutes)

**Goal:** Test on mobile devices

### Steps:
1. Open responsive design mode (F12 → Toggle device)
2. Test at different widths:
   - [ ] **320px** (mobile)
   - [ ] **768px** (tablet)
   - [ ] **1024px** (small desktop)

### Check:
- [ ] Layout adapts properly
- [ ] No horizontal scrolling
- [ ] Buttons are tappable
- [ ] Text is readable
- [ ] Forms are usable

**Expected:** Fully responsive design

---

## ✅ Test 12: Advanced Scenarios (5 minutes)

**Goal:** Test edge cases

### Scenario 1: Special Characters
1. Set export filename to: **my-täble_éxpört-123**
2. Save settings
3. Verify it saves correctly

### Scenario 2: Long Values
1. Set export filename to very long name (100+ chars)
2. Save settings
3. Verify it handles gracefully

### Scenario 3: CSV Delimiters
1. Try different CSV delimiters:
   - Semicolon (;)
   - Tab (\t)
   - Pipe (|)
2. Save each and verify

### Scenario 4: Date Formats
1. Try different date formats:
   - `d/m/Y` (European)
   - `m/d/Y` (US)
   - `F j, Y` (Full month)
2. Verify example updates

**Expected:** Handles all inputs gracefully

---

## 🐛 Bug Reporting Template

If you find any issues, document them like this:

```
**Bug:** [Brief description]
**Location:** Settings Page → [Section Name]
**Steps to Reproduce:**
1. [Step 1]
2. [Step 2]
3. [Step 3]

**Expected Behavior:** [What should happen]
**Actual Behavior:** [What actually happens]
**Browser:** [Chrome/Firefox/Safari/Edge]
**Console Errors:** [Any JS errors from console]
**Screenshots:** [If applicable]
```

---

## ✅ Testing Summary Checklist

After completing all tests:

- [ ] All sections display correctly
- [ ] All defaults are correct
- [ ] Settings save successfully
- [ ] Validation works properly
- [ ] File type security enforced
- [ ] Reset to defaults works
- [ ] Cache management works
- [ ] UI/UX is polished
- [ ] System info displays
- [ ] Works in all browsers
- [ ] Mobile responsive
- [ ] Edge cases handled

---

## 🎉 Success Criteria

✅ **Settings Page is Ready for Production if:**
- All tests pass
- No console errors
- Settings persist correctly
- Validation works as expected
- UI is professional and polished
- Works in all major browsers
- Fully responsive

---

## 📝 Quick Test (5 minutes)

If you only have 5 minutes, do this quick test:

1. ✅ Open settings page
2. ✅ Change 3-4 different settings
3. ✅ Save
4. ✅ Refresh page
5. ✅ Verify changes persisted
6. ✅ Click "Reset to Defaults"
7. ✅ Verify defaults restored
8. ✅ Test cache clear button
9. ✅ Check for console errors
10. ✅ Resize browser window

**If all of these pass, the settings page is working correctly!**

---

## 🚀 Next Steps After Testing

Once testing is complete:

1. ✅ Mark Settings as 100% complete
2. ✅ Document any bugs found
3. ✅ Fix critical issues
4. ✅ Update checklist
5. ✅ Move to next phase (Documentation or Full Plugin Testing)

---

**Good luck with testing!** 🎉

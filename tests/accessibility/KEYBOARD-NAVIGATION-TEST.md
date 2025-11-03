# Keyboard Navigation Test Protocol
# A-Tables & Charts - WCAG 2.2 Phase 3

**Version:** 1.0.0
**Date:** 2025-11-03
**Purpose:** Keyboard-only accessibility testing (no mouse, no screen reader)

---

## Overview

This test verifies that **all functionality is accessible using only a keyboard**, meeting WCAG 2.2 Success Criteria:
- **2.1.1 Keyboard (Level A)** - All functionality available via keyboard
- **2.1.2 No Keyboard Trap (Level A)** - No focus traps
- **2.1.4 Character Key Shortcuts (Level A)** - No conflicts
- **2.4.3 Focus Order (Level A)** - Logical tab order
- **2.4.7 Focus Visible (Level AA)** - Focus indicators visible

---

## Test Setup

### Requirements

- ✅ **Physical keyboard** (not virtual/on-screen)
- ✅ **NO MOUSE** - Disconnect or move away
- ✅ **NO SCREEN READER** - Disable all assistive tech
- ✅ **Visible focus indicators** - Watch for focus ring
- ✅ **Note-taking app** - Document failures

### Browser Testing Matrix

Test in **all** of these browsers:

| Browser | Version | Priority |
|---------|---------|----------|
| Chrome | 120+ | High |
| Firefox | 121+ | High |
| Safari | 17+ | Medium |
| Edge | 120+ | Medium |

### Test Data

Use a table with:
- **At least 20 rows** (to test pagination)
- **4-6 columns** (to test horizontal scrolling)
- **Search enabled**
- **Pagination enabled** (10 rows per page)
- **Sorting enabled**
- **All features active**

---

## Keyboard Reference

### Essential Keys

| Key | Function |
|-----|----------|
| **Tab** | Move to next focusable element |
| **Shift + Tab** | Move to previous focusable element |
| **Enter** | Activate button/link |
| **Space** | Activate button, check checkbox |
| **Arrow Keys** | Navigate within components (lists, tables) |
| **Escape** | Close dialogs/dropdowns |
| **Home** | Jump to start of line/list |
| **End** | Jump to end of line/list |
| **Page Up** | Scroll up one page |
| **Page Down** | Scroll down one page |

### Testing Keyboard Only

**Rules:**
1. ❌ **NO MOUSE USAGE** - Not even for scrolling
2. ✅ **Use keyboard exclusively** - Tab, arrows, Enter, Space
3. ✅ **Watch for focus ring** - Must be visible on all elements
4. ✅ **Test all paths** - Don't skip any interactive elements
5. ✅ **Document everything** - Note working and broken items

---

## Test Protocol

### Test 1: Initial Page Load & Skip Link

**Objective:** Verify skip link is first focusable element

**Steps:**
1. Load page
2. Press **Tab** (first tab press after page load)
3. Verify skip link has visible focus
4. Press **Enter** to activate skip link
5. Verify focus moves to table

**Expected Behavior:**
```
Tab #1 → Skip link focused (visible outline)
Enter → Focus jumps to table
```

**Pass Criteria:**
- ✅ Skip link is first in tab order
- ✅ Skip link has visible focus indicator
- ✅ Skip link text is visible (or on focus)
- ✅ Activating skip link moves focus to table
- ✅ Can continue tabbing from table

**Fail Indicators:**
- ❌ Skip link not first element
- ❌ Skip link has no focus indicator
- ❌ Skip link doesn't work
- ❌ Focus doesn't move to table

---

### Test 2: Toolbar Button Navigation

**Objective:** All toolbar buttons keyboard accessible

**Steps:**
1. Tab through toolbar buttons:
   - Copy button
   - Print button
   - Excel link
   - CSV link
   - PDF link
2. Verify each button:
   - Has visible focus
   - Can activate with **Enter**
   - Can activate with **Space** (buttons only)
   - Provides visual feedback on activation

**Expected Tab Order:**
```
Tab → Copy button (focus visible)
Tab → Print button (focus visible)
Tab → Excel link (focus visible)
Tab → CSV link (focus visible)
Tab → PDF link (focus visible)
```

**Activation Test:**
```
Focus Copy button
Press Enter → Should copy table (check clipboard)
Press Space → Should copy table (check clipboard)
```

**Pass Criteria:**
- ✅ All 5 buttons/links reachable via Tab
- ✅ Each has distinct, visible focus indicator
- ✅ Enter activates all buttons/links
- ✅ Space activates buttons (not links - this is correct)
- ✅ Visual feedback on activation (button press effect)
- ✅ Focus remains on button after activation (doesn't disappear)

**Fail Indicators:**
- ❌ Buttons skipped in tab order
- ❌ No focus indicator
- ❌ Enter/Space don't activate
- ❌ Focus disappears after activation

---

### Test 3: Search Input Accessibility

**Objective:** Search input fully keyboard accessible

**Steps:**
1. Tab to search input
2. Verify input has visible focus
3. Type search term (e.g., "laptop")
4. Verify table updates
5. Press Escape to clear (if applicable)
6. Tab away from input

**Expected Behavior:**
```
Tab → Search input focused (visible outline)
Type "laptop" → Table filters to matching rows
Escape → Input cleared (optional)
Tab → Move to next element
```

**Pass Criteria:**
- ✅ Search input in logical tab order
- ✅ Clear focus indicator when focused
- ✅ Can type immediately after focus (no extra step)
- ✅ Search filters table as you type (or on Enter)
- ✅ Can Tab away from search input
- ✅ Label or placeholder indicates purpose

**Fail Indicators:**
- ❌ Input not reachable via Tab
- ❌ No focus indicator
- ❌ Must click to type
- ❌ Can't Tab away (keyboard trap)

---

### Test 4: Length Selector (Rows Per Page)

**Objective:** Dropdown keyboard accessible

**Steps:**
1. Tab to length selector dropdown
2. Verify it has visible focus
3. Press **Space** or **Enter** to open
4. Use **Arrow keys** to navigate options:
   - Down arrow → Next option
   - Up arrow → Previous option
5. Press **Enter** to select option
6. Press **Escape** to close without selection (test both paths)
7. Verify table updates to selected length

**Expected Behavior:**
```
Tab → Length selector focused
Space/Enter → Dropdown opens
Down Arrow → Highlight "25"
Down Arrow → Highlight "50"
Enter → Select "50", dropdown closes
[Table shows 50 rows]
```

**Pass Criteria:**
- ✅ Dropdown in tab order
- ✅ Visible focus on closed state
- ✅ Space or Enter opens dropdown
- ✅ Arrow keys navigate options
- ✅ Enter selects option
- ✅ Escape closes dropdown
- ✅ Selected value updates visibly
- ✅ Table updates to match selection

**Fail Indicators:**
- ❌ Dropdown not in tab order
- ❌ Can't open with keyboard
- ❌ Arrow keys don't work
- ❌ Stuck in dropdown (keyboard trap)
- ❌ Selection doesn't update

---

### Test 5: Table Column Headers (Sorting)

**Objective:** Column sorting keyboard accessible

**Steps:**
1. Tab to first column header (or use skip link)
2. Verify header has focus indicator
3. Press **Enter** to sort ascending
4. Verify:
   - Table reorders
   - Visual sort indicator appears (arrow icon)
5. Press **Enter** again to sort descending
6. Verify sort direction changes
7. Tab to next column header
8. Press **Enter** to sort by different column
9. Test all columns

**Expected Behavior:**
```
Tab → "Product Name" header focused
Enter → Sort ascending (↑ icon appears)
Enter → Sort descending (↓ icon appears)
Tab → "Price" header focused
Enter → Sort by Price ascending
```

**Pass Criteria:**
- ✅ All column headers in tab order
- ✅ Clear focus indicator on headers
- ✅ Enter triggers sort
- ✅ Space also triggers sort (optional, some implementations)
- ✅ Visual sort indicator visible
- ✅ Table actually reorders
- ✅ Can sort all columns
- ✅ Toggle between asc/desc works

**Fail Indicators:**
- ❌ Headers not focusable
- ❌ Enter doesn't sort
- ❌ No visual feedback
- ❌ Some columns can't be sorted

---

### Test 6: Pagination Controls

**Objective:** All pagination buttons keyboard accessible

**Steps:**
1. Tab to pagination area
2. Verify first button ("First" or "Previous") has focus
3. Tab through all pagination buttons:
   - First
   - Previous
   - Page 1
   - Page 2
   - Page 3...
   - Next
   - Last
4. Test activation:
   - Press **Enter** on "Next" button
   - Verify page changes to page 2
   - Press **Enter** on specific page number (e.g., page 3)
   - Verify page changes
5. Test disabled states:
   - On page 1, "Previous" should be disabled
   - On last page, "Next" should be disabled
6. Verify visual indication of current page

**Expected Behavior:**
```
Tab → "First" button focused
Tab → "Previous" button focused (disabled on page 1)
Tab → "1" button focused (highlighted as current)
Tab → "2" button focused
Enter on "2" → Navigate to page 2
[Current page indicator moves to "2"]
Tab → "Next" button
Enter → Navigate to page 3
```

**Pass Criteria:**
- ✅ All pagination buttons in tab order
- ✅ Clear focus indicator on each button
- ✅ Enter activates buttons
- ✅ Page actually changes
- ✅ Current page visually distinct
- ✅ Disabled buttons indicated (greyed out)
- ✅ Can reach last page
- ✅ Can return to first page

**Fail Indicators:**
- ❌ Buttons not in tab order
- ❌ Enter doesn't work
- ❌ Can't tell which page is current
- ❌ Disabled buttons still active
- ❌ Keyboard trap in pagination

---

### Test 7: Column Visibility Toggle

**Objective:** Column toggle dropdown keyboard accessible

**Steps:**
1. Tab to "Columns" button
2. Verify focus indicator
3. Press **Enter** or **Space** to open dropdown
4. Verify focus moves into dropdown
5. Tab through checkboxes
6. Press **Space** to toggle checkbox
7. Verify column shows/hides
8. Press **Escape** to close dropdown
9. Verify focus returns to toggle button

**Expected Behavior:**
```
Tab → "Columns" button focused
Enter → Dropdown opens, focus on first checkbox
Space → Toggle "Price" column off
[Price column disappears from table]
Escape → Dropdown closes, focus returns to "Columns" button
Tab → Next element after "Columns" button
```

**Pass Criteria:**
- ✅ Toggle button in tab order
- ✅ Enter/Space opens dropdown
- ✅ Focus moves into dropdown
- ✅ Can Tab through all checkboxes
- ✅ Space toggles checkboxes
- ✅ Columns actually show/hide
- ✅ Escape closes dropdown
- ✅ Focus returns to button on close
- ✅ No keyboard trap

**Fail Indicators:**
- ❌ Can't open dropdown with keyboard
- ❌ Focus doesn't enter dropdown
- ❌ Can't toggle checkboxes
- ❌ Escape doesn't work
- ❌ Focus doesn't return
- ❌ Trapped in dropdown

---

### Test 8: Export Links

**Objective:** Export download links keyboard accessible

**Steps:**
1. Tab to Excel link
2. Verify focus indicator
3. Press **Enter**
4. Verify file download starts
5. Repeat for CSV and PDF links

**Expected Behavior:**
```
Tab → "Excel" link focused
Enter → File download initiates
Tab → "CSV" link focused
Enter → File download initiates
```

**Pass Criteria:**
- ✅ All export links in tab order
- ✅ Clear focus indicators
- ✅ Enter triggers download
- ✅ Downloads actually work

**Fail Indicators:**
- ❌ Links skipped in tab order
- ❌ Enter doesn't trigger download
- ❌ Download fails

---

### Test 9: No Keyboard Traps

**Objective:** Can exit all components

**Steps:**
1. Navigate to each interactive component
2. Enter the component (activate/focus)
3. Attempt to exit using:
   - Tab (to next element)
   - Shift+Tab (to previous)
   - Escape (close dropdown/dialog)
4. Verify you can always escape

**Components to Test:**
- Search input
- Length selector dropdown
- Column toggle dropdown
- Table (if using special navigation mode)
- Pagination controls

**Pass Criteria:**
- ✅ Can Tab into each component
- ✅ Can Tab out of each component
- ✅ Escape closes dropdowns/dialogs
- ✅ Never stuck in any component
- ✅ Focus always visible
- ✅ Can reach browser address bar (Ctrl+L or F6)

**Fail Indicators:**
- ❌ Can't Tab out of component
- ❌ Escape doesn't work
- ❌ Focus disappears
- ❌ Can't reach address bar

---

### Test 10: Complete Tab Order

**Objective:** Logical, complete tab order

**Steps:**
1. From page load, press Tab continuously
2. Document every focusable element in order
3. Verify order is logical (top to bottom, left to right)
4. Continue until Tab cycles back to beginning

**Expected Tab Order:**
```
1.  Skip link
2.  Copy button
3.  Print button
4.  Excel link
5.  CSV link
6.  PDF link
7.  Search input
8.  Length selector
9.  Column toggle button
10. Table (or first column header)
11. Column header: Product Name
12. Column header: Price
13. Column header: Stock
14. Column header: Category
15. Pagination: First
16. Pagination: Previous
17. Pagination: 1
18. Pagination: 2
19. Pagination: 3
20. Pagination: Next
21. Pagination: Last
[Then cycles back to #1]
```

**Pass Criteria:**
- ✅ All interactive elements reachable
- ✅ Order is logical (visual order matches tab order)
- ✅ No unexpected focus jumps
- ✅ Nothing skipped
- ✅ Tab cycles back to start
- ✅ Can Shift+Tab backwards through entire order

**Fail Indicators:**
- ❌ Elements skipped
- ❌ Illogical order (jumps around)
- ❌ Can't reach some elements
- ❌ Order doesn't match visual layout

---

### Test 11: Focus Visibility

**Objective:** Focus indicator always visible

**Steps:**
1. Tab through all elements
2. For each element, verify:
   - Focus indicator is visible
   - Indicator has sufficient contrast
   - Indicator is not obscured by other content
3. Test focus visibility:
   - On light backgrounds
   - On dark backgrounds
   - When element is active/pressed
   - When dropdown is open

**Visual Inspection:**
- Measure focus indicator thickness (should be ≥2px)
- Check color contrast (should be ≥3:1 against background)
- Verify indicator completely surrounds element

**Pass Criteria:**
- ✅ All focusable elements have visible indicator
- ✅ Indicator has sufficient contrast
- ✅ Indicator ≥2px thick (or equivalent)
- ✅ Indicator not obscured
- ✅ Indicator visible on all themes
- ✅ Indicator visible in all states (hover, active, disabled)

**Fail Indicators:**
- ❌ No focus indicator
- ❌ Indicator too faint/thin
- ❌ Indicator same color as background
- ❌ Indicator hidden by other elements
- ❌ Indicator disappears on some themes

---

### Test 12: Touch/Mobile Keyboard

**Objective:** Keyboard works on mobile (iPad keyboard, Android keyboard)

**Steps:**
1. Open page on tablet with physical keyboard
2. Connect Bluetooth keyboard
3. Repeat all tests above
4. Verify no differences in behavior

**Pass Criteria:**
- ✅ All functionality works with tablet keyboard
- ✅ Tab order same as desktop
- ✅ Enter/Space/Escape work correctly
- ✅ Focus indicators visible on tablet

---

## Common Issues & Solutions

### Issue 1: Focus Indicator Not Visible

**Symptom:** Tab moves focus but nothing visible

**Check:**
```css
/* Bad - outline removed */
:focus {
  outline: none;
}

/* Good - custom outline */
:focus {
  outline: 2px solid #2563eb;
  outline-offset: 2px;
}
```

**Solution:** Add visible focus styles to all interactive elements

---

### Issue 2: Keyboard Trap in Dropdown

**Symptom:** Can't Tab out of dropdown

**Check:**
- Escape key closes dropdown
- Tab from last item exits dropdown
- Focus returns to trigger button

**Solution:** Implement proper focus management

---

### Issue 3: Buttons Don't Respond to Enter/Space

**Symptom:** Must click button, keyboard doesn't work

**Check:**
```html
<!-- Bad - div with click handler -->
<div onclick="doSomething()">Click me</div>

<!-- Good - proper button -->
<button type="button" onclick="doSomething()">Click me</button>
```

**Solution:** Use semantic `<button>` elements

---

### Issue 4: Illogical Tab Order

**Symptom:** Tab jumps around randomly

**Check:**
```html
<!-- Bad - positive tabindex -->
<button tabindex="5">First</button>
<button tabindex="1">Second</button>

<!-- Good - natural order or tabindex="0" -->
<button>First</button>
<button>Second</button>
```

**Solution:** Remove positive tabindex values, use DOM order

---

### Issue 5: Focus Disappears After Interaction

**Symptom:** After clicking button, focus lost

**Check:**
```javascript
// Bad - focus removed
button.addEventListener('click', function() {
  button.blur(); // Don't do this!
  doSomething();
});

// Good - maintain focus
button.addEventListener('click', function() {
  doSomething();
  // Focus stays on button
});
```

**Solution:** Don't programmatically blur elements after activation

---

## Test Results Template

### Keyboard Navigation Test Results

**Date:** _______________
**Tester:** _______________
**Browser:** _______________
**Browser Version:** _______________
**OS:** _______________

| Test | Status | Notes |
|------|--------|-------|
| **1. Skip Link** | ⬜ Pass ⬜ Fail | |
| - Skip link first in tab order | ⬜ Pass ⬜ Fail | |
| - Visible focus indicator | ⬜ Pass ⬜ Fail | |
| - Skip link functions | ⬜ Pass ⬜ Fail | |
| **2. Toolbar Buttons** | ⬜ Pass ⬜ Fail | |
| - All buttons in tab order | ⬜ Pass ⬜ Fail | |
| - Enter activates buttons | ⬜ Pass ⬜ Fail | |
| - Space activates buttons | ⬜ Pass ⬜ Fail | |
| - Visible focus indicators | ⬜ Pass ⬜ Fail | |
| **3. Search Input** | ⬜ Pass ⬜ Fail | |
| - Input in tab order | ⬜ Pass ⬜ Fail | |
| - Can type immediately | ⬜ Pass ⬜ Fail | |
| - Can Tab away | ⬜ Pass ⬜ Fail | |
| **4. Length Selector** | ⬜ Pass ⬜ Fail | |
| - In tab order | ⬜ Pass ⬜ Fail | |
| - Opens with Space/Enter | ⬜ Pass ⬜ Fail | |
| - Arrow keys navigate | ⬜ Pass ⬜ Fail | |
| - Escape closes | ⬜ Pass ⬜ Fail | |
| **5. Column Sorting** | ⬜ Pass ⬜ Fail | |
| - Headers in tab order | ⬜ Pass ⬜ Fail | |
| - Enter triggers sort | ⬜ Pass ⬜ Fail | |
| - Visual sort indicator | ⬜ Pass ⬜ Fail | |
| **6. Pagination** | ⬜ Pass ⬜ Fail | |
| - All buttons in tab order | ⬜ Pass ⬜ Fail | |
| - Enter changes page | ⬜ Pass ⬜ Fail | |
| - Current page indicated | ⬜ Pass ⬜ Fail | |
| - Disabled states clear | ⬜ Pass ⬜ Fail | |
| **7. Column Toggle** | ⬜ Pass ⬜ Fail | |
| - Button in tab order | ⬜ Pass ⬜ Fail | |
| - Opens with Enter/Space | ⬜ Pass ⬜ Fail | |
| - Checkboxes accessible | ⬜ Pass ⬜ Fail | |
| - Escape closes | ⬜ Pass ⬜ Fail | |
| - Focus returns | ⬜ Pass ⬜ Fail | |
| **8. Export Links** | ⬜ Pass ⬜ Fail | |
| - All links in tab order | ⬜ Pass ⬜ Fail | |
| - Enter triggers download | ⬜ Pass ⬜ Fail | |
| **9. No Keyboard Traps** | ⬜ Pass ⬜ Fail | |
| - Can exit all components | ⬜ Pass ⬜ Fail | |
| - Escape works everywhere | ⬜ Pass ⬜ Fail | |
| **10. Tab Order** | ⬜ Pass ⬜ Fail | |
| - All elements reachable | ⬜ Pass ⬜ Fail | |
| - Logical order | ⬜ Pass ⬜ Fail | |
| - Cycles correctly | ⬜ Pass ⬜ Fail | |
| **11. Focus Visibility** | ⬜ Pass ⬜ Fail | |
| - Always visible | ⬜ Pass ⬜ Fail | |
| - Sufficient contrast | ⬜ Pass ⬜ Fail | |
| - Appropriate thickness | ⬜ Pass ⬜ Fail | |

**Overall Result:** ⬜ Pass ⬜ Fail
**Pass Rate:** _____ / 40 checks (_____ %)

**Critical Issues:**
1.
2.
3.

**Recommendations:**
1.
2.
3.

---

## Quick Test (5 Minutes)

For rapid verification, run this abbreviated test:

1. **Tab from page top to bottom** - Verify logical order
2. **Enter on Copy button** - Verify keyboard activation
3. **Tab to search, type** - Verify search works
4. **Tab to pagination, Enter on Next** - Verify page change
5. **Tab to column header, Enter** - Verify sort
6. **Shift+Tab backwards** - Verify reverse tab works
7. **Check focus visibility** - Verify all elements have visible focus

**Quick Test Pass:** All 7 steps work correctly
**Quick Test Fail:** Any step fails

---

## Next Steps

1. **Document all failures** with screenshots
2. **Assign severity** (Critical, High, Medium, Low)
3. **Create fix tickets** for development team
4. **Retest after fixes**
5. **Update VPAT** with final results

---

## References

- [WCAG 2.2 Guideline 2.1 - Keyboard Accessible](https://www.w3.org/WAI/WCAG22/quickref/#keyboard-accessible)
- [WebAIM: Keyboard Accessibility](https://webaim.org/techniques/keyboard/)
- [A11Y Project: Keyboard Testing](https://www.a11yproject.com/posts/how-to-test-for-keyboard-accessibility/)

---

**Last Updated:** 2025-11-03
**Version:** 1.0.0
**Next Review:** After Phase 3 completion

# Screen Reader Testing Guide
# A-Tables & Charts - WCAG 2.2 Phase 3

**Version:** 1.0.0
**Date:** 2025-11-03
**Purpose:** Manual screen reader testing protocol for WCAG 2.2 Level AA compliance

---

## Table of Contents

1. [Setup Instructions](#setup-instructions)
2. [NVDA Testing (Windows)](#nvda-testing-windows)
3. [JAWS Testing (Windows)](#jaws-testing-windows)
4. [VoiceOver Testing (macOS)](#voiceover-testing-macos)
5. [Narrator Testing (Windows)](#narrator-testing-windows)
6. [Expected Outputs](#expected-outputs)
7. [Common Issues](#common-issues)
8. [Test Results Template](#test-results-template)

---

## Setup Instructions

### Prerequisites

**Required:**
- Test environment with sample table data
- Keyboard (no mouse usage during tests)
- Headphones or speakers
- Note-taking application

**Recommended:**
- Screen recording software (OBS Studio, QuickTime)
- Second monitor for note-taking
- Multiple browsers (Chrome, Firefox, Edge, Safari)

### Test Data Setup

1. Create a WordPress page or post
2. Add the shortcode: `[atables id="1"]`
3. Ensure table has:
   - At least 10 rows of data
   - 4-6 columns
   - Search enabled
   - Pagination enabled (10 items per page)
   - Sorting enabled
   - All export buttons visible

---

## NVDA Testing (Windows)

### Installation

1. Download NVDA from https://www.nvaccess.org/download/
2. Install latest version (2023.3 or newer recommended)
3. Launch NVDA (Ctrl + Alt + N)
4. Configure speech rate to comfortable level

### Browser Compatibility

**Recommended:** Firefox (best NVDA compatibility)
**Also test:** Chrome, Edge

### NVDA Keyboard Commands Reference

| Action | Command |
|--------|---------|
| Start/Stop NVDA | Ctrl + Alt + N |
| Stop Speech | Ctrl |
| Next Element | Down Arrow |
| Previous Element | Up Arrow |
| Next Heading | H |
| Next Link | K |
| Next Button | B |
| Next Form Field | F |
| Next Table | T |
| Enter Table | Ctrl + Alt + Down |
| Exit Table | Ctrl + Alt + Up |
| Next Table Cell | Right Arrow |
| Next Table Row | Down Arrow |
| Read Current Line | NVDA + Up Arrow |
| Read All | NVDA + Down Arrow |

**Note:** NVDA key is typically Insert or Caps Lock

### Test Protocol

#### Test 1: Page Load & Initial Announcement

**Steps:**
1. Navigate to page with table
2. Let NVDA announce the page
3. Press `H` to navigate to table heading

**Expected Output:**
```
"Heading level 3, Sample Products Table"
"Product inventory and pricing information"
```

**Pass Criteria:**
- ✅ Table title announced
- ✅ Table description announced
- ✅ Heading level appropriate (h2 or h3)

---

#### Test 2: Toolbar Buttons

**Steps:**
1. Press `B` to find first button
2. Listen to announcement
3. Press Enter to activate
4. Listen for feedback

**Expected Output:**
```
"Copy, button, Copy table to clipboard"
[After activation]
"Table copied to clipboard"
```

**Test all buttons:**
- Copy button
- Print button
- Excel link
- CSV link
- PDF link

**Pass Criteria:**
- ✅ Button role announced
- ✅ Descriptive label provided
- ✅ Action feedback announced
- ✅ Keyboard accessible (Enter/Space)

---

#### Test 3: Table Structure

**Steps:**
1. Press `T` to navigate to table
2. Press Ctrl + Alt + Down to enter table navigation mode
3. Use arrow keys to navigate cells

**Expected Output:**
```
"Table with 4 columns and 10 rows"
"Column 1, Product Name"
"Column 2, Price"
"Row 1, Column 1, Laptop Pro"
"Row 1, Column 2, $1,299.00"
```

**Pass Criteria:**
- ✅ Table announced with row/column count
- ✅ Column headers announced
- ✅ Row and column position announced
- ✅ Cell content read correctly
- ✅ `scope="col"` attributes working

---

#### Test 4: Search Functionality

**Steps:**
1. Press `F` to navigate to search input
2. Listen to label
3. Type search term (e.g., "laptop")
4. Wait for announcement

**Expected Output:**
```
"Search table, edit, blank"
[After typing and searching]
"3 results found"
```

**Pass Criteria:**
- ✅ Search input labeled
- ✅ Edit role announced
- ✅ Search results count announced
- ✅ Announcement via ARIA live region

---

#### Test 5: Pagination

**Steps:**
1. Tab to pagination controls
2. Navigate through page buttons
3. Activate "Next page" button
4. Listen for announcement

**Expected Output:**
```
"First page, button"
"Previous page, button"
"Current page, page 1, button"
"Go to page 2, button"
"Next page, button"
[After activating Next]
"Showing page 2 of 5"
```

**Pass Criteria:**
- ✅ Each button has unique label
- ✅ Current page indicated with "Current page"
- ✅ Page number included in label
- ✅ Page change announced
- ✅ `aria-current="page"` working

---

#### Test 6: Column Sorting

**Steps:**
1. Navigate to table header (press T, then Ctrl+Alt+Down)
2. Find column header
3. Press Enter to sort
4. Listen for announcement

**Expected Output:**
```
"Product Name, column header, sortable"
[After activating sort]
"Table sorted by Product Name, ascending order"
[Header updates to:]
"Product Name, sorted ascending, column header"
```

**Pass Criteria:**
- ✅ Column header announced
- ✅ "sortable" indicated
- ✅ Sort action announced
- ✅ Sort direction announced
- ✅ Header updates after sort
- ✅ `aria-sort` attribute working

---

#### Test 7: Length Selector

**Steps:**
1. Press `F` to navigate to length selector
2. Listen to label
3. Press Space to open dropdown
4. Use arrows to select option
5. Press Enter

**Expected Output:**
```
"Number of rows to display, combobox, 10"
[After opening]
"10, selected"
"25"
"50"
"100"
```

**Pass Criteria:**
- ✅ Combobox labeled
- ✅ Current value announced
- ✅ Options announced
- ✅ Selection confirmed

---

#### Test 8: Skip Link

**Steps:**
1. Navigate to page
2. Press Tab (should focus skip link first)
3. Press Enter
4. Verify focus moves to table

**Expected Output:**
```
"Skip to table content, link"
[After activation - focus moves to table]
```

**Pass Criteria:**
- ✅ Skip link first in tab order
- ✅ Link purpose clear
- ✅ Actually skips to table

---

#### Test 9: ARIA Live Regions

**Steps:**
1. Perform various actions (sort, search, paginate)
2. Listen for announcements
3. Verify timing (should not interrupt)

**Expected Announcements:**
- Sort: "Table sorted by [Column], [direction] order"
- Search: "[X] results found"
- Pagination: "Showing page [X] of [Y]"
- Copy: "Table copied to clipboard"

**Pass Criteria:**
- ✅ Announcements timely (not too fast/slow)
- ✅ Announcements clear and descriptive
- ✅ Using `aria-live="polite"` (doesn't interrupt)
- ✅ Announcements auto-clear after ~3 seconds

---

### NVDA Test Checklist

- [ ] Page load announces table heading
- [ ] Table description announced
- [ ] All buttons have descriptive labels
- [ ] Button activation provides feedback
- [ ] Table structure announced (rows/columns)
- [ ] Column headers announced when navigating
- [ ] Cell position announced in table navigation
- [ ] Search input properly labeled
- [ ] Search results announced
- [ ] Pagination buttons have unique labels
- [ ] Current page indicated
- [ ] Page changes announced
- [ ] Column headers indicate sortability
- [ ] Sort actions announced
- [ ] Sort direction announced
- [ ] Length selector properly labeled
- [ ] Skip link functions correctly
- [ ] ARIA live regions working
- [ ] No unexpected interruptions
- [ ] All content keyboard accessible

---

## JAWS Testing (Windows)

### Installation

1. Download JAWS from https://www.freedomscientific.com/
2. Install latest version (2023 or newer)
3. Launch JAWS
4. May require license (40-minute demo available)

### JAWS Keyboard Commands Reference

| Action | Command |
|--------|---------|
| Next Element | Down Arrow |
| Previous Element | Up Arrow |
| Next Heading | H |
| Next Link | K (or Tab) |
| Next Button | B |
| Next Form Field | F |
| Next Table | T |
| Table Layer | Ctrl + Alt + Keypad 5 |
| Next Cell | Alt + Ctrl + Right |
| Previous Cell | Alt + Ctrl + Left |
| Next Row | Alt + Ctrl + Down |
| Previous Row | Alt + Ctrl + Up |
| Read Current Line | Insert + Up Arrow |
| Say All | Insert + Down Arrow |
| Forms Mode | Enter (on form field) |

### Test Protocol

Follow the same test protocol as NVDA, but note JAWS-specific differences:

**JAWS Differences:**
1. **Table Navigation:** Enter "table layer" mode with Ctrl+Alt+Keypad 5
2. **Forms Mode:** JAWS automatically enters forms mode on form fields
3. **Verbosity:** JAWS may provide more verbose output
4. **Cell Navigation:** Use Alt+Ctrl+Arrows for cell navigation

**Expected JAWS Output Differences:**
```
NVDA: "Search table, edit, blank"
JAWS: "Search table, edit, type in text"

NVDA: "Table with 4 columns and 10 rows"
JAWS: "Table with 4 columns and 10 rows, use Table Layer for navigation"

NVDA: "Current page, page 1, button"
JAWS: "Current page, page 1, button, press spacebar to activate"
```

### JAWS Test Checklist

- [ ] Table layer mode accessible
- [ ] Forms mode activates properly
- [ ] Table structure announced
- [ ] Column headers read in cells
- [ ] All buttons accessible
- [ ] Search input labeled
- [ ] Pagination controls clear
- [ ] Sort announcements working
- [ ] ARIA live regions functioning
- [ ] No verbosity issues

---

## VoiceOver Testing (macOS)

### Setup

1. System Preferences → Accessibility → VoiceOver
2. Enable VoiceOver (Cmd + F5)
3. Open Safari (best compatibility)
4. Navigate to test page

### VoiceOver Keyboard Commands Reference

| Action | Command |
|--------|---------|
| Enable/Disable VO | Cmd + F5 |
| VO Key | Ctrl + Option |
| Next Item | VO + Right Arrow |
| Previous Item | VO + Left Arrow |
| Interact | VO + Shift + Down |
| Stop Interacting | VO + Shift + Up |
| Next Heading | VO + Cmd + H |
| Next Link | VO + Cmd + L |
| Next Table | VO + Cmd + T |
| Web Rotor | VO + U |
| Read Next | VO + A |
| Read All | VO + A (twice) |

### Test Protocol

**VoiceOver-Specific Tests:**

#### Test 1: Web Rotor Navigation

**Steps:**
1. Press VO + U to open Web Rotor
2. Use Left/Right arrows to select category:
   - Headings
   - Links
   - Form Controls
   - Tables
3. Navigate list with Up/Down arrows
4. Press Enter to jump to item

**Expected:**
- Headings rotor shows table title
- Links rotor shows export links
- Form Controls shows search input
- Tables rotor shows main table

**Pass Criteria:**
- ✅ All major elements appear in rotor
- ✅ Labels are descriptive
- ✅ Navigation jumps correctly

---

#### Test 2: Table Interaction

**Steps:**
1. Press VO + Cmd + T to find table
2. Press VO + Shift + Down to interact with table
3. Use arrow keys to navigate cells
4. Press VO + Shift + Up to stop interacting

**Expected Output:**
```
"Table, 4 columns, 10 rows"
"Column 1 of 4, Product Name"
"Row 1, Column 1, Laptop Pro"
```

**Pass Criteria:**
- ✅ Table structure announced
- ✅ Can enter/exit table interaction
- ✅ Row and column position clear
- ✅ Headers announced with cells

---

#### Test 3: Button and Link Interaction

**Steps:**
1. Navigate to Copy button
2. Listen to announcement
3. Press VO + Space to activate
4. Listen for feedback

**Expected Output:**
```
"Copy, button, Copy table to clipboard"
[After activation]
"Table copied to clipboard"
```

**Pass Criteria:**
- ✅ Button/link role announced
- ✅ Label descriptive
- ✅ VO + Space activates
- ✅ Feedback provided

---

### VoiceOver Test Checklist

- [ ] Web Rotor contains all elements
- [ ] Headings category shows table title
- [ ] Form Controls shows search input
- [ ] Tables category shows main table
- [ ] Can interact with table
- [ ] Row/column position announced
- [ ] Buttons activate with VO + Space
- [ ] Links work correctly
- [ ] Search input labeled
- [ ] Pagination controls accessible
- [ ] Sort announcements working
- [ ] ARIA live regions functioning

---

## Narrator Testing (Windows)

### Setup

1. Press Win + Ctrl + Enter to enable Narrator
2. Open Edge browser (best compatibility)
3. Navigate to test page

### Narrator Keyboard Commands Reference

| Action | Command |
|--------|---------|
| Enable/Disable | Win + Ctrl + Enter |
| Stop Reading | Ctrl |
| Next Item | Caps Lock + Right Arrow |
| Previous Item | Caps Lock + Left Arrow |
| Next Heading | H |
| Next Link | K |
| Next Button | B |
| Next Form Field | F |
| Next Table | T |
| Scan Mode On/Off | Caps Lock + Space |
| Read From Here | Caps Lock + R |

### Test Protocol

Follow similar protocol to NVDA, but note Narrator-specific features:

**Narrator-Specific Features:**
1. **Scan Mode:** Automatically enabled on web pages
2. **Natural Voices:** More conversational tone
3. **Integrated with Windows:** Better touch screen support

**Expected Narrator Output:**
```
"Heading level 3, Sample Products Table"
"Link, Copy table to clipboard"
"Button, Copy"
"Edit, Search table"
```

### Narrator Test Checklist

- [ ] Scan mode enables automatically
- [ ] Headings navigable with H key
- [ ] Buttons navigable with B key
- [ ] Links navigable with K key
- [ ] Form fields navigable with F key
- [ ] Table accessible
- [ ] All labels announced
- [ ] ARIA attributes working
- [ ] Live regions functioning

---

## Expected Outputs

### Complete Table Announcement Example

**Initial Table Discovery:**
```
Screen Reader: NVDA
Browser: Firefox
Output:
"Heading level 3, Sample Products Table"
"Product inventory and pricing information"
"Table with 4 columns and 10 rows"
"Use table navigation commands to explore"
```

### Column Header Navigation

```
"Column 1 of 4, Product Name, column header, sortable, press Enter to sort"
"Column 2 of 4, Price, column header, sortable"
"Column 3 of 4, Stock, column header, sortable"
"Column 4 of 4, Category, column header, sortable"
```

### Cell Content Reading

```
"Row 1, Column 1, Laptop Pro"
"Row 1, Column 2, $1,299.00"
"Row 2, Column 1, Wireless Mouse"
"Row 2, Column 2, $29.99"
```

### Interactive Element Announcements

**Search Input:**
```
"Search table, edit, type in text"
```

**Length Selector:**
```
"Number of rows to display, combobox, 10, collapsed"
[After opening]
"Number of rows to display, combobox, 10, expanded"
"10, selected, 1 of 4"
"25, 2 of 4"
```

**Pagination:**
```
"First page, button"
"Previous page, button, unavailable" (on first page)
"Current page, page 1, button"
"Go to page 2, button"
"Next page, button"
"Last page, button"
```

**Copy Button:**
```
"Copy, button, Copy table to clipboard"
[After activation]
"Table copied to clipboard" (via live region)
```

### Status Announcements (ARIA Live Region)

**After Sorting:**
```
"Table sorted by Product Name, ascending order"
```

**After Searching:**
```
"5 results found"
```

**After Pagination:**
```
"Showing page 2 of 5"
```

---

## Common Issues

### Issue 1: Table Structure Not Announced

**Symptoms:**
- "Table" not announced
- Row/column count missing
- Headers not associated with cells

**Diagnosis:**
```html
<!-- Check for proper markup -->
<table>  <!-- Should be <table> not <div role="table"> -->
  <thead>
    <tr>
      <th scope="col">Name</th>  <!-- Must have scope="col" -->
    </tr>
  </thead>
</table>
```

**Fix:**
- Ensure `<table>`, `<thead>`, `<tbody>` tags used
- Add `scope="col"` to headers
- Don't use `role="table"` on real tables

---

### Issue 2: Buttons Not Labeled

**Symptoms:**
- "Button" announced without description
- Icon-only buttons silent

**Diagnosis:**
```html
<!-- Bad -->
<button><span class="icon"></span></button>

<!-- Good -->
<button aria-label="Copy table to clipboard">
  <span class="icon" aria-hidden="true"></span>
  <span class="sr-only">Copy</span>
</button>
```

**Fix:**
- Add `aria-label` to buttons
- Use visually hidden text
- Mark icons with `aria-hidden="true"`

---

### Issue 3: Live Region Not Announcing

**Symptoms:**
- Status changes not announced
- Updates happen silently

**Diagnosis:**
```html
<!-- Bad - created dynamically -->
<div id="status"></div>
<script>
  document.getElementById('status').setAttribute('aria-live', 'polite');
  document.getElementById('status').textContent = 'Updated';
</script>

<!-- Good - exists on page load -->
<div id="status" aria-live="polite" aria-atomic="false"></div>
<script>
  document.getElementById('status').textContent = 'Updated';
</script>
```

**Fix:**
- Add live region to page HTML (not JavaScript)
- Must exist before content changes
- Use `aria-live="polite"` for non-urgent updates
- Use `aria-atomic="false"` for incremental updates

---

### Issue 4: Sort State Not Indicated

**Symptoms:**
- Sorted column not announced
- Sort direction unclear

**Diagnosis:**
```html
<!-- Bad -->
<th>Product Name</th>

<!-- Good -->
<th aria-sort="ascending">Product Name (sorted ascending)</th>
```

**Fix:**
- Add `aria-sort` attribute
- Update header text to include state
- Remove `aria-sort` from non-sorted columns

---

### Issue 5: Pagination Unclear

**Symptoms:**
- All buttons announce same
- Current page not indicated

**Diagnosis:**
```html
<!-- Bad -->
<button>1</button>
<button>2</button>

<!-- Good -->
<button aria-label="Current page, page 1" aria-current="page">1</button>
<button aria-label="Go to page 2">2</button>
```

**Fix:**
- Unique `aria-label` for each button
- Use `aria-current="page"` for current page
- Include page number in label

---

## Test Results Template

### NVDA Test Results

**Date:** _______________
**Tester:** _______________
**NVDA Version:** _______________
**Browser:** _______________
**Browser Version:** _______________

| Test | Expected | Actual | Status | Notes |
|------|----------|--------|--------|-------|
| Page load announcement | Table heading announced | | ⬜ Pass ⬜ Fail | |
| Table description | Description announced | | ⬜ Pass ⬜ Fail | |
| Button labels | All buttons labeled | | ⬜ Pass ⬜ Fail | |
| Button activation | Feedback provided | | ⬜ Pass ⬜ Fail | |
| Table structure | Row/column count announced | | ⬜ Pass ⬜ Fail | |
| Column headers | Headers announced | | ⬜ Pass ⬜ Fail | |
| Cell navigation | Position announced | | ⬜ Pass ⬜ Fail | |
| Search input label | "Search table" announced | | ⬜ Pass ⬜ Fail | |
| Search results | Result count announced | | ⬜ Pass ⬜ Fail | |
| Pagination labels | Unique labels for each | | ⬜ Pass ⬜ Fail | |
| Current page | "Current page" indicated | | ⬜ Pass ⬜ Fail | |
| Page changes | Page change announced | | ⬜ Pass ⬜ Fail | |
| Sort indication | "sortable" announced | | ⬜ Pass ⬜ Fail | |
| Sort action | Sort announced | | ⬜ Pass ⬜ Fail | |
| Sort direction | Direction announced | | ⬜ Pass ⬜ Fail | |
| Length selector | Properly labeled | | ⬜ Pass ⬜ Fail | |
| Skip link | Functions correctly | | ⬜ Pass ⬜ Fail | |
| ARIA live regions | Status messages announced | | ⬜ Pass ⬜ Fail | |

**Overall Result:** ⬜ Pass ⬜ Fail
**Pass Rate:** _____ / 18 tests (_____ %)

**Critical Issues:**
1.
2.
3.

**Minor Issues:**
1.
2.
3.

**Recommendations:**
1.
2.
3.

---

## Recording Test Sessions

### Video Recording

**Windows (OBS Studio):**
1. Download OBS Studio (free)
2. Add "Display Capture" source
3. Add "Audio Output Capture" for screen reader
4. Add "Microphone" for tester commentary
5. Record at 1080p, 30fps

**macOS (QuickTime):**
1. Open QuickTime Player
2. File → New Screen Recording
3. Click Options → Show Mouse Clicks
4. Enable microphone
5. Start recording

### Audio Recording

**Purpose:** Capture screen reader output clearly

**Tools:**
- Audacity (free, cross-platform)
- Windows Voice Recorder
- macOS Voice Memos

**Settings:**
- Sample rate: 44.1 kHz
- Bit depth: 16-bit
- Format: WAV or MP3

---

## Next Steps After Testing

1. **Document all failures** in issue tracker
2. **Prioritize** by severity (Critical > High > Medium > Low)
3. **Create fix tickets** for each issue
4. **Retest** after fixes implemented
5. **Update VPAT** with final results
6. **Create user documentation** based on findings

---

## References

- [NVDA User Guide](https://www.nvaccess.org/files/nvda/documentation/userGuide.html)
- [JAWS Documentation](https://www.freedomscientific.com/training/jaws/)
- [VoiceOver User Guide](https://support.apple.com/guide/voiceover/welcome/mac)
- [Narrator User Guide](https://support.microsoft.com/en-us/windows/complete-guide-to-narrator-e4397a0d-ef4f-b386-d8ae-c172f109bdb1)
- [WebAIM Screen Reader Testing](https://webaim.org/articles/screenreader_testing/)

---

**Last Updated:** 2025-11-03
**Version:** 1.0.0
**Next Review:** After Phase 3 completion

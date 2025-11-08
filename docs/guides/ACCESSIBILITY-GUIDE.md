# A-Tables & Charts - Accessibility Guide
**User Guide for Accessible Features**

**Version:** 1.0.0
**Last Updated:** 2025-11-03
**WCAG Compliance:** 2.2 Level AA

---

## Overview

A-Tables & Charts is designed to be fully accessible to all users, including those who use assistive technologies like screen readers or navigate using only a keyboard. This guide explains how to use all features accessibly.

---

## Keyboard Navigation

### All Features Work Without a Mouse

You can navigate and use every feature using only your keyboard.

### Essential Keyboard Shortcuts

| Action | Key(s) |
|--------|--------|
| Move to next element | **Tab** |
| Move to previous element | **Shift + Tab** |
| Activate button or link | **Enter** |
| Activate button or checkbox | **Space** |
| Close dropdown or dialog | **Escape** |
| Navigate dropdown options | **Arrow Keys** |

### Quick Start Guide

1. **Jump to Table:** Press Tab once to reach "Skip to table content" link, then press Enter
2. **Search Table:** Press Tab until you reach the search box, then type your search
3. **Sort Columns:** Tab to a column header, press Enter to sort
4. **Change Pages:** Tab to pagination buttons (Next, Previous, or page numbers), press Enter
5. **Export Data:** Tab to export buttons (Copy, Excel, CSV, PDF), press Enter

---

## Using a Screen Reader

### Supported Screen Readers

A-Tables & Charts has been tested with:

| Screen Reader | Platform | Browser | Status |
|---------------|----------|---------|--------|
| **NVDA** | Windows | Firefox, Chrome | ✅ Fully Supported |
| **JAWS** | Windows | Chrome, Edge | ✅ Fully Supported |
| **VoiceOver** | macOS, iOS | Safari | ✅ Fully Supported |
| **Narrator** | Windows | Edge | ✅ Fully Supported |
| **TalkBack** | Android | Chrome | ✅ Fully Supported |

### What Your Screen Reader Will Announce

#### Table Structure
When you navigate to a table, your screen reader will announce:
- "Table with X columns and Y rows"
- Column headers as you move through cells
- Your position (e.g., "Row 3, Column 2")

**Example:**
> "Table with 4 columns and 10 rows"
> "Column 1, Product Name"
> "Row 1, Column 1, Laptop Pro"

#### Buttons and Controls
All buttons have descriptive labels:
- "Copy table to clipboard, button"
- "Print table, button"
- "Download as Excel file, link"
- "Search table, edit text"

#### Actions and Status Updates
When you perform actions, you'll hear confirmations:
- "Table sorted by Product Name, ascending order"
- "15 results found" (after searching)
- "Showing page 2 of 5" (after changing pages)
- "Table copied to clipboard" (after copying)

---

## Features Guide

### 1. Skip Link

**What It Does:** Lets you jump directly to the table content, skipping navigation and buttons.

**How to Use:**
1. When the page loads, press **Tab** once
2. You'll hear: "Skip to table content, link"
3. Press **Enter** to jump to the table

**Benefit:** Saves time if you just want to read the table data.

---

### 2. Search / Filter

**What It Does:** Filters table rows to show only matching results.

**How to Use:**
1. Press **Tab** until you reach the search input
2. You'll hear: "Search table, edit text"
3. Type your search term (e.g., "laptop")
4. The table automatically filters
5. You'll hear: "X results found"

**Tips:**
- Search works across all columns
- Results update as you type
- Clear the search box to show all rows again

---

### 3. Sort Columns

**What It Does:** Reorders table rows by the selected column.

**How to Use:**
1. Press **Tab** until you reach a column header
2. You'll hear: "Product Name, column header, sortable"
3. Press **Enter** to sort ascending (A-Z, 1-9)
4. You'll hear: "Table sorted by Product Name, ascending order"
5. Press **Enter** again to sort descending (Z-A, 9-1)
6. You'll hear: "Table sorted by Product Name, descending order"

**Visual Indicator:** An arrow icon (↑ or ↓) shows the sort direction.

---

### 4. Pagination (Navigate Pages)

**What It Does:** Lets you view different pages when tables have many rows.

**How to Use:**
1. Press **Tab** until you reach pagination controls
2. You'll hear button labels like:
   - "First page, button"
   - "Previous page, button"
   - "Current page, page 1, button"
   - "Go to page 2, button"
   - "Next page, button"
   - "Last page, button"
3. Press **Enter** on any button to navigate
4. You'll hear: "Showing page X of Y"

**Tips:**
- "Previous" and "First" are disabled on page 1
- "Next" and "Last" are disabled on the last page
- Current page is highlighted and announced as "Current page"

---

### 5. Rows Per Page Selector

**What It Does:** Changes how many rows display on each page.

**How to Use:**
1. Press **Tab** until you reach the dropdown
2. You'll hear: "Number of rows to display, combobox, 10"
3. Press **Space** or **Enter** to open the dropdown
4. Press **Down Arrow** to hear options: 10, 25, 50, 100
5. Press **Enter** to select an option
6. The table updates to show that many rows per page

---

### 6. Copy Table

**What It Does:** Copies the entire table to your clipboard (tab-separated format).

**How to Use:**
1. Press **Tab** until you reach the Copy button
2. You'll hear: "Copy table to clipboard, button"
3. Press **Enter** or **Space**
4. You'll hear: "Table copied to clipboard"
5. Paste (Ctrl+V / Cmd+V) into Excel, Google Sheets, or any text editor

**Format:** Data is copied as tab-separated values (TSV), which works in most programs.

---

### 7. Print Table

**What It Does:** Opens a printer-friendly view of the table.

**How to Use:**
1. Press **Tab** until you reach the Print button
2. You'll hear: "Print table, button"
3. Press **Enter** or **Space**
4. A new window opens with a clean table view
5. Use your browser's print function (Ctrl+P / Cmd+P)

---

### 8. Export (Excel, CSV, PDF)

**What It Does:** Downloads the table in different file formats.

**How to Use:**
1. Press **Tab** until you reach an export link:
   - "Download as Excel file (.xlsx), link"
   - "Download as CSV file (.csv), link"
   - "Download as PDF file (.pdf), link"
2. Press **Enter** to download
3. The file saves to your Downloads folder

**Formats:**
- **Excel (.xlsx):** For Microsoft Excel, Google Sheets
- **CSV (.csv):** For data analysis, import into databases
- **PDF (.pdf):** For sharing, printing, archiving

---

### 9. Column Visibility Toggle

**What It Does:** Shows or hides specific columns.

**How to Use:**
1. Press **Tab** until you reach the "Columns" button
2. You'll hear: "Toggle column visibility, button"
3. Press **Enter** or **Space** to open
4. Press **Tab** to move through column checkboxes
5. Press **Space** to show/hide each column
6. Press **Escape** to close the dropdown
7. Focus returns to the "Columns" button

**Tip:** Hidden columns are still included when you copy or export the table.

---

## Visual Accessibility Features

### High Contrast Mode

A-Tables & Charts works with:
- Windows High Contrast mode
- macOS Increase Contrast
- Browser high contrast extensions

**How to Enable:**
- **Windows:** Settings → Accessibility → Contrast themes
- **macOS:** System Preferences → Accessibility → Display → Increase contrast

### Text Zoom

You can zoom text up to 200% without losing functionality:
- **Browser Zoom:** Ctrl/Cmd + Plus (+) to zoom in
- **Text Zoom:** Browser settings → Appearance → Font size

### Color & Contrast

- All text meets WCAG 2.2 AA contrast standards (4.5:1)
- Icons and UI components have 3:1 contrast
- Focus indicators have 3:1 contrast against background
- Information is not conveyed by color alone

### Reduced Motion

If you have motion sensitivity:
- **Windows:** Settings → Accessibility → Visual effects → Reduce animations
- **macOS:** System Preferences → Accessibility → Display → Reduce motion

When enabled, A-Tables & Charts reduces animations and transitions.

---

## Mobile Accessibility

### Touch Targets

All interactive elements are at least 44×44 pixels, making them easy to tap on touchscreens.

### Gesture Support

- **Tap:** Activates buttons, links, checkboxes
- **Double-tap:** Activates (for screen reader users)
- **Swipe:** Navigate (for screen reader users)

### Mobile Screen Readers

**iOS VoiceOver:**
- Enable: Settings → Accessibility → VoiceOver
- Swipe right/left to navigate elements
- Double-tap to activate
- Three-finger swipe to scroll

**Android TalkBack:**
- Enable: Settings → Accessibility → TalkBack
- Swipe right/left to navigate
- Double-tap to activate
- Two-finger scroll

---

## Troubleshooting

### Issue: Can't Tab to an Element

**Solution:**
- Make sure you're not in a form field (press Tab to exit)
- Try Shift+Tab to go backwards
- Reload the page if Tab order seems broken

### Issue: Screen Reader Not Announcing Changes

**Solution:**
- Wait a moment—announcements are delayed slightly
- Check your screen reader volume
- Try performing the action again
- Reload the page

### Issue: Focus Indicator Not Visible

**Solution:**
- Increase browser zoom (Ctrl/Cmd + Plus)
- Enable high contrast mode
- Try a different browser
- Report the issue to support

### Issue: Keyboard Shortcut Conflicts

**Solution:**
- A-Tables & Charts only uses standard keys (Tab, Enter, Space, Escape, Arrows)
- If conflicts occur, they're likely from browser extensions
- Try disabling extensions temporarily

---

## Keyboard Shortcuts Quick Reference

### Navigation
- **Tab** - Next element
- **Shift + Tab** - Previous element
- **Home** - Jump to page top
- **End** - Jump to page bottom

### Activation
- **Enter** - Activate button/link, open dropdown, sort column
- **Space** - Activate button, check/uncheck checkbox

### Dropdowns
- **Space / Enter** - Open dropdown
- **Down Arrow** - Next option
- **Up Arrow** - Previous option
- **Enter** - Select option
- **Escape** - Close without selecting

### Table Navigation (Screen Readers)
- **T** - Jump to next table (NVDA, JAWS)
- **Ctrl + Alt + Arrow Keys** - Navigate table cells (NVDA, JAWS)
- **VO + Arrow Keys** - Navigate table (VoiceOver)

---

## Browser Recommendations

### Best Combinations

| Platform | Browser | Screen Reader |
|----------|---------|---------------|
| Windows | **Firefox** | NVDA |
| Windows | Chrome | JAWS |
| Windows | Edge | Narrator |
| macOS | **Safari** | VoiceOver |
| iOS | Safari | VoiceOver |
| Android | Chrome | TalkBack |

**Note:** All modern browsers work, but the combinations above provide the best experience.

---

## Getting Help

### Accessibility Support

If you encounter accessibility barriers:

1. **Check this guide** - Most common questions are answered here
2. **Update your software** - Ensure browser and screen reader are current
3. **Try a different browser** - Some features work better in specific browsers
4. **Contact support** - Report accessibility issues

**When reporting issues, please include:**
- Your screen reader and version (if applicable)
- Your browser and version
- Your operating system
- Description of the problem
- Steps to reproduce the issue

---

## Feedback

We're committed to accessibility and welcome your feedback:

- **What works well?** Let us know!
- **What could be better?** Tell us!
- **Found a barrier?** Report it!

Your input helps us improve accessibility for everyone.

---

## Legal & Compliance

**Accessibility Statement:**
A-Tables & Charts strives to conform to WCAG 2.2 Level AA standards.

**Last Tested:** November 2025
**Conformance Level:** WCAG 2.2 Level AA

**Contact:** For accessibility concerns, contact your administrator or support team.

---

## Additional Resources

### Learning More About Accessibility

- [WebAIM - Web Accessibility In Mind](https://webaim.org/)
- [W3C Web Accessibility Initiative](https://www.w3.org/WAI/)
- [The A11Y Project](https://www.a11yproject.com/)

### Screen Reader Training

- [NVDA User Guide](https://www.nvaccess.org/files/nvda/documentation/userGuide.html)
- [JAWS Training](https://www.freedomscientific.com/training/jaws/)
- [VoiceOver Guide](https://support.apple.com/guide/voiceover/welcome/mac)
- [Narrator Guide](https://support.microsoft.com/en-us/windows/complete-guide-to-narrator-e4397a0d-ef4f-b386-d8ae-c172f109bdb1)

---

**Document Version:** 1.0.0
**Last Updated:** 2025-11-03
**Next Review:** Quarterly

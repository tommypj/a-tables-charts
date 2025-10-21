# Testing the Frontend Shortcode

## Steps to Test:

### 1. Restart Local by Flywheel
- Stop the site
- Start the site again
- This clears any PHP opcode cache

### 2. Create a Test Page
1. Go to WordPress Admin → Pages → Add New
2. Title: "Test Table"
3. In the content area, add the shortcode:
   ```
   [atable id="1"]
   ```
   (Replace `1` with your actual table ID from the dashboard)

4. Publish the page

### 3. View the Page
- Click "View Page" 
- You should see your table rendered with:
  - Search box
  - Table data
  - Pagination (if more than 10 rows)

## Troubleshooting:

### If you see the raw shortcode `[atable id="1"]`:
1. Check the error log for PHP errors
2. Make sure the plugin is activated
3. Try deactivating and reactivating the plugin

### To get your Table ID:
1. Go to A-Tables & Charts → All Tables
2. Look at the table you want to display
3. The ID is in the URL when you click View or Edit

### Expected Output:
- Clean, responsive table
- Search functionality
- Pagination controls
- Mobile-friendly design

## Quick Copy Shortcode:
On the dashboard, click the "Shortcode" button next to any table to copy its shortcode automatically!

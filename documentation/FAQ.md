# Frequently Asked Questions (FAQ)

Common questions and quick answers about A-Tables & Charts.

---

## 📚 General Questions

### What is A-Tables & Charts?

A-Tables & Charts is a WordPress plugin that allows you to create responsive, sortable, and filterable tables from various data sources (CSV, Excel, MySQL, JSON, XML, Google Sheets) and visualize them with beautiful charts.

### What are the main features?

- ✅ Import from multiple data sources (CSV, Excel, MySQL, JSON, XML, Google Sheets)
- ✅ Responsive tables that work on all devices
- ✅ 8 chart types (Bar, Line, Pie, Doughnut, Column, Area, Scatter, Radar)
- ✅ Search, sort, and filter functionality
- ✅ Export to CSV, Excel, and PDF
- ✅ Pagination for large datasets
- ✅ Customizable styling
- ✅ Shortcode support
- ✅ No coding required

### Do I need coding knowledge?

No! A-Tables & Charts is designed to be user-friendly with a visual interface. However, advanced users can customize with CSS and PHP if desired.

### Will it work with my theme?

Yes! A-Tables & Charts is designed to work with any WordPress theme. It uses standard WordPress practices and has been tested with popular themes like Astra, OceanWP, GeneratePress, and many others.

### Is it mobile-responsive?

Yes! All tables and charts are fully responsive and will adapt to any screen size, including smartphones and tablets.

---

## 💰 Licensing & Updates

### What license do I need?

- **Regular License ($XX)**: For a single website
- **Extended License ($XXX)**: For use in a product sold to end users (SaaS, etc.)

### Do I get free updates?

Yes! All updates are free forever once you purchase the plugin.

### How long is support provided?

You get 6 months of support with your purchase. You can extend support for another 6 months at a discounted rate directly on CodeCanyon.

### Can I use it on multiple sites?

You need one Regular License per website. If you have multiple sites, you need to purchase multiple licenses.

---

## 🔧 Installation & Setup

### My plugin upload fails. What should I do?

**Common causes:**
1. **Wrong file downloaded**: Make sure you download "Installable WordPress file only" from CodeCanyon, not "All files & documentation"
2. **File size limit**: Your server's upload limit might be too small. Contact your hosting provider to increase `upload_max_filesize` in PHP settings
3. **Timeout**: Try installing via FTP instead (see manual installation guide)

### Where do I find the plugin after installation?

Look for "A-Tables & Charts" in your WordPress admin sidebar (left menu). If you don't see it, the plugin might not be activated. Go to Plugins and click "Activate" next to A-Tables & Charts.

### Can I migrate my data to another site?

Yes! You can export your tables/charts and import them on another WordPress site with the plugin installed. Or use a WordPress migration plugin like All-in-One WP Migration.

---

## 📊 Tables

### What file formats can I import?

- ✅ CSV (.csv)
- ✅ Excel (.xlsx, .xls)
- ✅ JSON (.json)
- ✅ XML (.xml)
- ✅ MySQL database
- ✅ Google Sheets (via URL)
- ✅ WooCommerce products (built-in)

### What's the maximum file size I can upload?

This depends on your server's PHP settings. Common limits are 2MB, 8MB, or 64MB. You can check in WordPress → Site Health → Info → Server. Contact your hosting provider to increase this limit if needed.

### How many rows can I import?

There's no hard limit in the plugin itself. However:
- **Small tables** (< 100 rows): No pagination needed
- **Medium tables** (100-1,000 rows): Use pagination
- **Large tables** (1,000-10,000 rows): Use pagination + server-side processing (Premium feature)
- **Very large tables** (> 10,000 rows): Consider connecting directly to MySQL database for best performance

### Can I edit data after importing?

Yes! You can:
- Edit individual cells inline
- Add/delete rows
- Add/delete columns
- Re-import to update data
- Connect to database for auto-updates

### My table doesn't show all data. Why?

Check these common issues:
1. **Pagination is enabled**: You're only seeing 10-25 rows per page. Click pagination buttons to see more
2. **Filter is active**: Clear any active filters
3. **Import error**: Check if all data was imported. Look for import log in the table settings
4. **Character encoding**: Your file might have special characters. Save your CSV as UTF-8 encoding

### Can I import tables from other plugins?

Yes! Common scenarios:
- **TablePress**: Export as CSV, then import here
- **wpDataTables**: Export as Excel, then import
- **Manual migration**: Copy data to Excel/CSV first

### How do I update data in my table?

**Method 1: Re-import**
- Go to table edit page
- Click "Re-import Data"
- Upload updated file
- Existing data will be replaced

**Method 2: Manual edit**
- Go to table edit page
- Click cells to edit inline
- Changes save automatically

**Method 3: Database connection** (for auto-updates)
- Connect table to MySQL database
- Data updates automatically when database changes

---

## 📈 Charts

### Which chart types are available?

We support 8 chart types:
1. **Bar Chart** - Horizontal bars, compare categories
2. **Column Chart** - Vertical bars, show trends
3. **Line Chart** - Connected points, show progression over time
4. **Area Chart** - Line chart with filled area below
5. **Pie Chart** - Circular slices, show proportions
6. **Doughnut Chart** - Pie chart with center hole
7. **Scatter Chart** - X/Y plot, show correlations
8. **Radar Chart** - Spider/web chart, compare multiple variables

### Can I create multiple charts from one table?

Yes! You can create unlimited charts from a single table. Each chart can:
- Use different data columns
- Have different chart types
- Have unique styling
- Show different data ranges

### My chart isn't displaying. What's wrong?

**Common fixes:**
1. **Check shortcode**: Make sure you're using `[atables_chart id=X]` not `[atables id=X]`
2. **JavaScript conflict**: Another plugin might be conflicting. Try disabling other plugins temporarily
3. **Browser cache**: Clear your browser cache and refresh
4. **Data issues**: Chart needs valid numeric data. Text data won't render in most chart types
5. **Theme conflict**: Try switching to a default WordPress theme (Twenty Twenty-Three) to test

### Can I customize chart colors?

Yes! You can:
- Choose from preset color schemes
- Pick custom colors for each data series
- Set gradient colors
- Customize background color
- Change grid line colors

### Charts look different on mobile. Is this normal?

Yes! Charts automatically resize for mobile devices. This is intentional for better mobile experience. You can adjust mobile-specific settings in chart configuration.

---

## 🔍 Search & Filtering

### How does search work?

The search box searches across all columns in your table. It's real-time (instant results as you type) and case-insensitive.

### Can I search specific columns only?

Yes! Enable "Column-specific filters" in Display Settings. This adds filter dropdowns below each column header.

### Can users filter by multiple criteria?

Yes! Enable "Advanced Filters" in Display Settings. Users can create complex filters with AND/OR logic.

### Does search work with special characters?

Yes! Search supports Unicode characters, accents, and special symbols (é, ñ, ü, etc.).

---

## 💾 Export Features

### What export formats are supported?

- ✅ CSV (.csv) - For Excel/Google Sheets
- ✅ Excel (.xlsx) - Native Excel format
- ✅ PDF (.pdf) - For printing/sharing
- ✅ Print - Direct printing from browser

### Does export include filtered data only?

Yes! When you apply filters and export, only the visible (filtered) rows will be exported. To export all data, clear all filters first.

### Can I automate exports?

Not directly from the interface, but developers can use our export hooks to create scheduled exports via WordPress cron jobs.

### Export file is empty. Why?

**Common causes:**
1. **All data is filtered out**: Clear filters and try again
2. **Server timeout**: Table might be too large. Try exporting filtered/paginated data
3. **File permissions**: Check that WordPress can write to `/wp-content/uploads/` directory
4. **Memory limit**: Very large tables need more memory. Contact hosting to increase PHP memory limit

---

## 🎨 Styling & Customization

### Can I change table colors?

Yes! You can customize:
- Header background color
- Row background colors (including striped rows)
- Border colors
- Text colors
- Hover effects
- Font family and size

### How do I add custom CSS?

**Method 1: Plugin settings**
- Go to A-Tables & Charts → Settings
- Scroll to "Custom CSS" section
- Add your CSS code
- Save settings

**Method 2: Theme customizer**
- Go to Appearance → Customize
- Additional CSS
- Add your styles

**Example CSS:**
```css
/* Change header color */
.atables-wrapper table thead {
    background-color: #0073aa !important;
    color: white !important;
}

/* Zebra striping */
.atables-wrapper table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}
```

### Can I change column widths?

Yes! In the table editor:
1. Click on column header
2. Set "Column Width" (in pixels or percentage)
3. Save table

### My custom styles don't apply. Why?

**Common issues:**
1. **Cache**: Clear plugin cache + browser cache
2. **Specificity**: Your CSS might not be specific enough. Add `!important` or increase specificity
3. **Loading order**: Custom CSS might load before plugin CSS. Use theme's Additional CSS section instead
4. **Syntax error**: Check CSS for typos

---

## ⚙️ Settings & Configuration

### Where are plugin settings?

Go to **A-Tables & Charts → Settings** in your WordPress admin menu.

### What settings are available?

- **General**: Default pagination, rows per page
- **Performance**: Enable caching, AJAX loading
- **Security**: File upload limits, allowed file types
- **Import**: Default data source, encoding
- **Display**: Default styling, responsive breakpoints
- **Export**: PDF settings, Excel format
- **Advanced**: Custom CSS, developer mode

### Changes don't save. What's wrong?

**Common fixes:**
1. **Nonce expired**: Reload the page and try again
2. **Plugin conflict**: Try disabling other plugins temporarily
3. **Permissions**: Make sure you have "Administrator" role
4. **Server timeout**: Contact hosting if saving takes too long

---

## 🚀 Performance

### My tables load slowly. How can I speed them up?

**Quick fixes:**
1. **Enable caching**: Settings → Performance → Enable Caching
2. **Enable pagination**: Don't display all 1000 rows at once!
3. **Disable unnecessary features**: If you don't need search, disable it
4. **Optimize images**: If table has images, compress them first
5. **Use CDN**: For chart libraries, enable CDN in settings

**Advanced fixes:**
1. **Server-side processing**: For tables > 1000 rows
2. **Database connection**: Instead of importing, connect directly to MySQL
3. **Lazy loading**: Enable in Display Settings
4. **Minification**: Enable asset minification in settings

### Does the plugin affect overall site speed?

The plugin only loads assets (CSS/JS) on pages where you use shortcodes. If you don't use tables on a page, no assets are loaded.

### How do I clear the cache?

Go to **A-Tables & Charts → Settings → Performance** and click "Clear Cache" button. Or use any WordPress caching plugin's clear cache feature.

---

## 🔒 Security

### Is my data secure?

Yes! We follow WordPress security best practices:
- ✅ All inputs are sanitized
- ✅ Database queries use prepared statements (SQL injection prevention)
- ✅ Nonce verification on all forms
- ✅ Capability checks (only admins can manage tables)
- ✅ File upload validation
- ✅ XSS prevention

### Can users see my database credentials?

No! Database credentials are stored encrypted in WordPress options table. They're never exposed in frontend code or accessible to non-admin users.

### Are file uploads safe?

Yes! We:
- Validate file types (only allow CSV, Excel, JSON, XML)
- Check file size limits
- Scan for malicious code
- Store in protected WordPress upload directory

---

## 🔌 Compatibility

### What WordPress versions are supported?

- **Minimum**: WordPress 5.8
- **Recommended**: WordPress 6.0+
- **Tested up to**: WordPress 6.4

### What PHP versions work?

- **Minimum**: PHP 7.4
- **Recommended**: PHP 8.0+
- **Tested up to**: PHP 8.2

### Does it work with page builders?

Yes! Tested with:
- ✅ Elementor
- ✅ Beaver Builder
- ✅ Divi
- ✅ WPBakery
- ✅ Oxygen
- ✅ Gutenberg (native support)

**Usage:** Use the Shortcode widget/element and paste your `[atables id=X]` shortcode.

### Does it work with WooCommerce?

Yes! You can:
- Display product tables
- Show product attributes
- Create custom product catalogs
- Export product data

### Does it work with multilingual plugins?

Partially. The plugin itself is translation-ready. WPML and Polylang should work, but you'll need separate tables for each language.

---

## 🆘 Troubleshooting

### I can't see my table on the frontend

**Check these:**
1. ✅ Is shortcode correct? `[atables id=1]` (check ID number)
2. ✅ Is table published? (Status = "Published" not "Draft")
3. ✅ Is page/post published?
4. ✅ Clear cache (browser + plugin + hosting)
5. ✅ Check for JavaScript errors (F12 → Console tab)

### Chart doesn't render

**Common fixes:**
1. ✅ Wrong shortcode: Use `[atables_chart id=X]` not `[atables id=X]`
2. ✅ Data type: Charts need numeric data, not text
3. ✅ JavaScript conflict: Try disabling other plugins
4. ✅ CDN blocked: Check if Google Charts CDN is accessible
5. ✅ Browser cache: Clear and reload

### Search doesn't work

**Check:**
1. ✅ Is search enabled in Display Settings?
2. ✅ JavaScript conflicts: Check browser console (F12)
3. ✅ Special characters: Try searching without accents first
4. ✅ Cache: Clear all caches

### Export doesn't work

**Fixes:**
1. ✅ Check file permissions: `/wp-content/uploads/` needs write access
2. ✅ Increase memory limit: Contact hosting
3. ✅ Try smaller dataset: Filter table first, then export
4. ✅ Browser popup blocker: Might be blocking download

---

## 💡 Tips & Best Practices

### Performance Tips

1. **Use pagination** for tables > 100 rows
2. **Enable caching** in settings
3. **Only enable features you need** (disable search if not needed)
4. **Optimize your data source** (clean CSV files load faster)
5. **Use database connection** instead of imports for large, frequently updated data

### Design Tips

1. **Choose appropriate chart type** for your data
2. **Use striped rows** for readability
3. **Enable responsive** for mobile users
4. **Keep column widths consistent**
5. **Use descriptive column names**

### Data Management Tips

1. **Clean your data before import** (remove empty rows, fix formatting)
2. **Use consistent date formats** (YYYY-MM-DD recommended)
3. **UTF-8 encoding** for international characters
4. **Back up your data** before re-importing
5. **Document your data sources** (add descriptions to tables)

---

## 📞 Still Need Help?

### Support Channels

1. **Documentation**: You're here! ✅
2. **Video Tutorials**: [YouTube Channel](https://youtube.com/channel)
3. **Support Forum**: [CodeCanyon Support](https://codecanyon.net/item/support)
4. **Email Support**: support@yourdomain.com
5. **Knowledge Base**: [https://your-site.com/kb](https://your-site.com/kb)

### Before Contacting Support

Please provide:
1. WordPress version
2. Plugin version
3. PHP version (WordPress → Site Health)
4. List of active plugins
5. Theme name
6. Description of issue
7. Screenshots (if applicable)
8. Console errors (F12 → Console tab)

### Response Times

- **Standard support**: Within 24 hours (business days)
- **Urgent issues**: Within 12 hours
- **Weekends**: Within 48 hours

---

**Didn't find your answer? Check the [Troubleshooting Guide](TROUBLESHOOTING.md) or [Contact Support](https://codecanyon.net/item/support)**

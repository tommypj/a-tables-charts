=== A-Tables and Charts for WordPress ===
Contributors: atablesteam
Tags: table, tables, charts, data visualization, excel, csv, import, export, responsive tables, data tables, mysql
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.4
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Create beautiful, responsive tables and interactive charts from multiple data sources. Import Excel, CSV, JSON, XML, MySQL, Google Sheets and more.

== Description ==

**A-Tables and Charts** is the most comprehensive WordPress plugin for creating and managing data tables and charts. Whether you need to display product catalogs, pricing tables, comparison tables, financial data, or statistical charts, A-Tables makes it effortless.

= 🚀 Key Features =

**Data Import & Management:**
* Import from Excel (XLSX, XLS)
* Import from CSV files
* Import from JSON data
* Import from XML files
* Connect to MySQL databases
* Import from Google Sheets
* Import from REST APIs
* Manual table creation
* Scheduled automatic data refresh

**Table Features:**
* Responsive design for all devices
* Advanced filtering and search
* Multi-column sorting
* Pagination with customizable options
* Conditional formatting rules
* Cell merging capabilities
* Data validation
* Formula support (calculations)
* Bulk operations
* Export to CSV, JSON, Excel, PDF

**Chart Features:**
* Multiple chart types (Bar, Line, Pie, Doughnut, Radar, Polar)
* Create charts from table data
* Interactive and responsive charts
* Chart.js powered visualizations
* Customizable colors and styling

**Developer Features:**
* WP-CLI commands for automation
* Comprehensive REST API
* Action and filter hooks
* Template system
* Gutenberg blocks
* Shortcode support
* Performance monitoring
* Cache system

**Accessibility:**
* WCAG 2.2 Level AA compliant
* Screen reader compatible
* Full keyboard navigation
* ARIA labels and landmarks
* High contrast support

= 💼 Perfect For =

* Product catalogs and price lists
* Comparison tables
* Statistical data display
* Financial reports
* Event schedules
* Team rosters
* Directory listings
* Research data
* Analytics dashboards
* Any tabular data presentation

= 🎯 Use Cases =

**E-commerce:**
Display product specifications, pricing tables, comparison charts, and inventory data.

**Business:**
Create financial reports, sales dashboards, performance metrics, and KPI tracking.

**Education:**
Show class schedules, grade tables, student rosters, and statistical analysis.

**Research:**
Present research data, survey results, statistical comparisons, and data visualizations.

= 📊 Chart Types Supported =

* **Bar Charts** - Vertical and horizontal bars
* **Line Charts** - Trends and time series
* **Pie Charts** - Proportional data
* **Doughnut Charts** - Alternative to pie charts
* **Radar Charts** - Multi-variable comparison
* **Polar Area Charts** - Circular data display

= 🔧 Technical Features =

**Performance:**
* Built-in caching system
* Optimized database queries
* Lazy loading for large datasets
* Performance monitoring dashboard
* Memory usage optimization

**Security:**
* Nonce verification on all AJAX requests
* Capability checks for all operations
* SQL injection protection
* XSS prevention
* CSRF protection
* Sanitized inputs and outputs

**Integration:**
* Gutenberg block editor
* Classic editor support
* Shortcode system
* Widget support
* WP-CLI integration
* Developer hooks

= 📝 Shortcode Usage =

Display a table:
`[atables id="123"]`

Display a chart:
`[atables_chart id="456"]`

Display a specific cell value:
`[atables_cell table_id="123" row="1" column="A"]`

= 🎨 Styling Options =

* Responsive layouts
* Customizable colors
* Multiple table themes
* Custom CSS support
* Mobile-optimized display
* Print-friendly styling

= 🌐 Multilingual Ready =

* Translation ready
* .pot file included
* RTL language support
* WPML compatible

= 🔄 Scheduled Data Refresh =

Automatically update your tables from external data sources:
* Schedule refreshes (15 minutes to daily)
* Multiple data sources per table
* Status tracking and notifications
* Manual refresh triggers
* Error handling and logging

= 💻 WP-CLI Commands =

Manage tables from command line:
* `wp atables table list` - List all tables
* `wp atables table create` - Create new tables
* `wp atables schedule run` - Trigger scheduled refreshes
* `wp atables cache clear` - Clear cache
* `wp atables export` - Export table data

= 📈 Performance Monitor =

Track plugin performance:
* Operation timing metrics
* Memory usage tracking
* Database query analysis
* Slow operation detection
* Optimization recommendations

= 🎓 Documentation & Support =

* Comprehensive documentation
* Code examples
* Video tutorials
* Developer API reference
* Regular updates

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins → Add New
3. Search for "A-Tables and Charts"
4. Click "Install Now"
5. Activate the plugin
6. Go to A-Tables in your admin menu to start creating tables

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins → Add New → Upload Plugin
4. Choose the downloaded ZIP file
5. Click "Install Now"
6. Activate the plugin
7. Go to A-Tables in your admin menu

= After Activation =

1. Navigate to **A-Tables** in your WordPress admin menu
2. Click **Add New Table** to create your first table
3. Choose your import method (Excel, CSV, manual, etc.)
4. Configure display settings
5. Copy the shortcode and paste it into any page or post

= Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* MySQL 5.6 or higher
* Modern web browser

== Frequently Asked Questions ==

= Can I import data from Excel? =

Yes! A-Tables supports importing from Excel files (XLSX and XLS formats). Simply click "Import from Excel" and upload your file.

= Does it work with large datasets? =

Yes! The plugin includes built-in pagination, lazy loading, and caching to handle large datasets efficiently. Performance monitoring helps you optimize large tables.

= Can I connect to external databases? =

Yes! You can connect to MySQL databases and execute queries to import data. The plugin includes 6-layer security validation for MySQL queries.

= Is it mobile responsive? =

Absolutely! All tables and charts are fully responsive and optimized for mobile devices, tablets, and desktops.

= Can I schedule automatic data updates? =

Yes! The scheduled refresh feature allows you to automatically update table data from MySQL, Google Sheets, CSV URLs, or REST APIs at intervals from 15 minutes to daily.

= Does it support charts? =

Yes! You can create beautiful interactive charts from your table data using Chart.js. Supports bar, line, pie, doughnut, radar, and polar area charts.

= Can I export table data? =

Yes! Export tables to CSV, JSON, Excel (XLSX), PDF, or XML formats with a single click.

= Is it accessible? =

Yes! The plugin is WCAG 2.2 Level AA compliant with full keyboard navigation, screen reader support, and proper ARIA labels.

= Can I use formulas like Excel? =

Yes! The plugin supports formulas for calculations, including SUM, AVERAGE, and custom expressions.

= Does it work with Gutenberg? =

Yes! A-Tables includes native Gutenberg blocks for tables and charts, plus shortcode support for the classic editor.

= Can I use it with WP-CLI? =

Yes! Comprehensive WP-CLI commands are available for automation, bulk operations, and server management.

= Is it developer-friendly? =

Absolutely! The plugin includes extensive hooks, filters, REST API, template system, and comprehensive documentation for developers.

= Can I customize the design? =

Yes! Multiple styling options, custom CSS support, and template overrides allow complete design customization.

= Does it support conditional formatting? =

Yes! Apply conditional formatting rules to highlight cells based on their values with custom colors and styles.

= Can I merge cells? =

Yes! The cell merging feature allows you to merge cells horizontally or vertically, just like in Excel.

== Screenshots ==

1. Dashboard - Overview of all tables and charts
2. Table Editor - Create and edit tables with intuitive interface
3. Import Options - Multiple import methods (Excel, CSV, JSON, XML, MySQL)
4. Display Settings - Customize table appearance and behavior
5. Chart Creation - Create interactive charts from table data
6. Conditional Formatting - Apply rules to highlight data
7. Scheduled Refresh - Automate data updates
8. Performance Monitor - Track plugin performance
9. Mobile Responsive - Tables look great on all devices
10. Gutenberg Block - Native block editor integration

== Changelog ==

= 1.0.4 - 2025-11-03 =
**Added:**
* WCAG 2.2 Level AA accessibility compliance (100% coverage)
* Scheduled data refresh system with 4 data sources
* WP-CLI commands for complete plugin management
* Performance monitoring dashboard with metrics
* Comprehensive test suite (54 tests, 100% pass rate)

**Improved:**
* Enhanced security with nonce verification
* Optimized database queries
* Better error handling and logging
* Performance optimizations

**Fixed:**
* Module dependency loading in cron system
* Various minor bug fixes

= 1.0.0 - 2025-10-31 =
**Initial Release:**
* Table import (Excel, CSV, JSON, XML)
* Manual table creation and editing
* MySQL database connection
* Google Sheets integration
* Chart creation (6 chart types)
* Conditional formatting
* Cell merging
* Data validation
* Formula support
* Advanced filtering and sorting
* Bulk operations
* Export functionality (CSV, JSON, Excel, PDF)
* Gutenberg blocks
* Shortcode support
* Template system
* Cache system
* Settings management

== Upgrade Notice ==

= 1.0.4 =
Major update with accessibility compliance, scheduled data refresh, WP-CLI commands, and performance monitoring. Recommended upgrade for all users.

= 1.0.0 =
Initial release of A-Tables and Charts.

== Additional Information ==

= Privacy =

This plugin does not collect or store any personal data from your visitors. All data remains on your WordPress installation.

= Support =

For support, documentation, and updates, visit [https://a-tables-charts.com](https://a-tables-charts.com)

= Credits =

* Chart.js - https://www.chartjs.org
* PHPSpreadsheet - https://github.com/PHPOffice/PhpSpreadsheet
* TCPDF - https://tcpdf.org

= Contributing =

Developers can contribute to the source code on our GitHub repository.

= System Requirements =

* WordPress 5.8+
* PHP 7.4+
* MySQL 5.6+
* 64MB+ PHP memory limit (128MB+ recommended)
* Modern browser with JavaScript enabled

= Translations =

* English (default)
* Translation ready with .pot file included
* Help us translate into your language!

== Developer Documentation ==

= Hooks and Filters =

The plugin provides numerous hooks for developers:

**Actions:**
* `atables_after_table_created` - After table creation
* `atables_after_table_updated` - After table update
* `atables_after_table_deleted` - After table deletion
* `atables_after_table_data_updated` - After data refresh
* `atables_after_scheduled_refresh_success` - After successful refresh
* `atables_before_table_render` - Before table rendering

**Filters:**
* `atables_table_data` - Filter table data before display
* `atables_chart_config` - Filter chart configuration
* `atables_export_data` - Filter export data
* `atables_import_data` - Filter import data

= Template Overrides =

Copy template files to your theme:
`your-theme/a-tables-charts/template-name.php`

= REST API Endpoints =

* `/wp-json/atables/v1/tables` - Get all tables
* `/wp-json/atables/v1/tables/{id}` - Get specific table
* `/wp-json/atables/v1/charts` - Get all charts

For complete developer documentation, visit our website.

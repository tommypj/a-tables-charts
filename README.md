# A-Tables & Charts - WordPress Tables & Charts Plugin

Create responsive tables and charts from various data sources including Excel, CSV, JSON, XML, MySQL, Google Sheets, WooCommerce, and more.

## ðŸš€ Features

- **Multiple Data Sources**: Import from Excel, CSV, JSON, XML, MySQL, Google Sheets, WooCommerce
- **Responsive Tables**: Mobile-friendly, sortable, filterable, paginated tables
- **Charts**: Create beautiful charts using Google Charts or Chart.js
- **Export**: Export tables to Excel, CSV, PDF, or print
- **Search & Filter**: Single search and multi-criteria filtering
- **Premium Features**: Server-side processing, advanced filters, front-end editing, calculated columns

## ðŸ“‹ Requirements

- **PHP**: 7.4 or higher
- **WordPress**: 5.8 or higher
- **MySQL**: 5.6 or higher

## ðŸ”§ Installation

### Development Setup

1. **Clone the repository**
   ```bash
   cd wp-content/plugins/
   git clone [repository-url] A-Tables & Charts
   cd A-Tables & Charts
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Build assets**
   ```bash
   npm run build
   ```

5. **Activate the plugin**
   - Go to WordPress Admin â†’ Plugins
   - Find "a-tables-charts" and click "Activate"

### Production Installation

1. Download the plugin ZIP file
2. Go to WordPress Admin â†’ Plugins â†’ Add New
3. Click "Upload Plugin" and select the ZIP file
4. Click "Install Now" and then "Activate"

## ðŸ—ï¸ Project Structure

```
A-Tables & Charts/
â”œâ”€â”€ assets/              # CSS, JS, images
â”œâ”€â”€ src/                 # PHP source code
â”‚   â”œâ”€â”€ modules/         # Feature modules
â”‚   â”‚   â”œâ”€â”€ core/        # Core plugin functionality
â”‚   â”‚   â”œâ”€â”€ dataSources/ # Data import & parsing
â”‚   â”‚   â”œâ”€â”€ tables/      # Table management
â”‚   â”‚   â”œâ”€â”€ charts/      # Chart rendering
â”‚   â”‚   â””â”€â”€ ...
â”‚   â”œâ”€â”€ shared/          # Shared utilities
â”‚   â””â”€â”€ database/        # Database migrations
â”œâ”€â”€ tests/               # Unit & integration tests
â”œâ”€â”€ docs/                # Documentation
â”œâ”€â”€ vendor/              # Composer dependencies
â”œâ”€â”€ node_modules/        # NPM dependencies
â””â”€â”€ A-Tables & Charts.php     # Main plugin file
```

## ðŸŽ¯ Quick Start

### Creating Your First Table

1. Go to **A-Tables & Charts â†’ Create Table**
2. Select a data source (e.g., upload Excel file)
3. Configure columns and display settings
4. Save the table
5. Copy the shortcode: `[wpdatatable id=1]`
6. Paste it in any post or page

### Creating a Chart

1. Go to **A-Tables & Charts â†’ All Tables**
2. Click "Create Chart" for any table
3. Select chart type (Line, Bar, Pie, etc.)
4. Configure data series and styling
5. Save the chart
6. Use the shortcode: `[wpdatachart id=1]`

## ðŸ› ï¸ Development

### Running Tests

```bash
# PHP Unit Tests
composer test

# Code Standards Check
composer cs

# Auto-fix Code Standards
composer cbf
```

### Build Commands

```bash
# Development build with watch
npm run dev

# Production build (minified)
npm run build

# Lint JavaScript
npm run lint
```

## ðŸ“š Documentation

- [User Guide](docs/user-guide/)
- [Developer Documentation](docs/api/)
- [Architecture Overview](docs/architecture/overview.md)
- [Database Schema](docs/architecture/database-schema.md)

## ðŸ”Œ Hooks & Filters

### Actions

```php
// Before table renders
do_action( 'A-Tables & Charts_before_render_table', $table_id );

// After table renders
do_action( 'A-Tables & Charts_after_render_table', $table_id );

// Before data import
do_action( 'A-Tables & Charts_before_import', $source_type, $config );
```

### Filters

```php
// Modify table data
apply_filters( 'A-Tables & Charts_filter_table_data', $data, $table_id );

// Modify export data
apply_filters( 'A-Tables & Charts_export_data', $data, $format, $table_id );

// Customize columns
apply_filters( 'A-Tables & Charts_table_columns', $columns, $table_id );
```

## ðŸ¤ Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## ðŸ“ Changelog

### Version 1.0.0 (2024-01-01)
- Initial release
- Core table and chart functionality
- Data import from multiple sources
- Export to Excel, CSV, PDF
- Responsive design
- Search, sort, filter, paginate

## ðŸ“„ License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## ðŸ†˜ Support

- **Documentation**: https://docs.A-Tables & Charts.com
- **Support Forum**: https://wordpress.org/support/plugin/A-Tables & Charts
- **Email**: support@A-Tables & Charts.com

## ðŸ‘¥ Authors

- A-Tables & Charts Team - [Website](https://A-Tables & Charts.com)

## ðŸ™ Acknowledgments

- Chart.js for chart rendering
- PHPSpreadsheet for Excel processing
- All contributors and users

---

Made with â¤ï¸ for the WordPress community

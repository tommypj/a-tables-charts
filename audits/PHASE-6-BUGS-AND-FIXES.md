# 🐛 PHASE 6 - BUGS & FIXES TRACKER
## Charts & Visualization System

**Last Updated:** October 31, 2025  
**Total Bugs:** 15  
**Critical (P0):** 3  
**High (P1):** 5  
**Medium (P2):** 7

---

## 🚨 P0 - CRITICAL BUGS (Must Fix Immediately)

### BUG-001: Chart Type Validation Discrepancy
**Priority:** P0 - CRITICAL  
**Severity:** Blocking  
**Status:** 🔴 Open  
**Estimated Fix Time:** 30 minutes

**Problem:**
GoogleChartsRenderer supports 'column' and 'area' types, but Chart.php validation rejects them.

**Impact:**
- Users cannot create column/area charts
- Renderer code exists but unreachable
- False advertising (docs claim 8 types, only 4 accessible)

**Location:**
- File: `src/modules/charts/types/Chart.php`
- Line: 150

**Current Code:**
```php
$allowed_types = array( 'bar', 'line', 'pie', 'doughnut' );
```

**Fix:**
```php
$allowed_types = array( 'bar', 'line', 'pie', 'doughnut', 'column', 'area', 'scatter' );
```

**Additional Changes Required:**
1. Update create-chart.php dropdown:
```php
// File: src/modules/core/views/create-chart.php
// Line 58-63: Add options

<select id="chart-type">
    <option value="bar">Bar Chart (Horizontal)</option>
    <option value="column">Column Chart (Vertical)</option>  <!-- NEW -->
    <option value="line">Line Chart</option>
    <option value="area">Area Chart</option>                 <!-- NEW -->
    <option value="pie">Pie Chart</option>
    <option value="doughnut">Doughnut Chart</option>
    <option value="scatter">Scatter Plot</option>            <!-- NEW -->
</select>
```

**Testing Checklist:**
- [ ] Create column chart → Should succeed
- [ ] Create area chart → Should succeed
- [ ] Create scatter chart → Should succeed
- [ ] Verify GoogleChartsRenderer handles all types
- [ ] Test frontend rendering for each type

---

### BUG-002: Missing Edit Chart Functionality
**Priority:** P0 - CRITICAL  
**Severity:** Blocking  
**Status:** 🔴 Open  
**Estimated Fix Time:** 16 hours

**Problem:**
No way to edit charts after creation. Users must delete and recreate charts to make changes.

**Impact:**
- Severe usability issue
- Poor user experience
- Time-wasting workflow

**Missing Files:**
- `src/modules/core/views/edit-chart.php` (DOES NOT EXIST)

**Fix Plan:**

#### Step 1: Create edit-chart.php (8 hours)

**File:** `src/modules/core/views/edit-chart.php`

```php
<?php
/**
 * Edit Chart Page
 *
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get chart ID from URL
$chart_id = isset( $_GET['chart_id'] ) ? absint( $_GET['chart_id'] ) : 0;

if ( empty( $chart_id ) ) {
    wp_die( __( 'Invalid chart ID.', 'a-tables-charts' ) );
}

// Load chart data
require_once ATABLES_PLUGIN_DIR . 'src/modules/charts/index.php';
$chart_service = new \ATablesCharts\Charts\Services\ChartService();
$chart = $chart_service->get_chart( $chart_id );

if ( ! $chart ) {
    wp_die( __( 'Chart not found.', 'a-tables-charts' ) );
}

// Load table for column data
require_once ATABLES_PLUGIN_DIR . 'src/modules/tables/index.php';
$table_service = new \ATablesCharts\Tables\Services\TableService();
$table = $table_service->get_table( $chart->table_id );

if ( ! $table ) {
    wp_die( __( 'Source table not found.', 'a-tables-charts' ) );
}

$headers = $table->source_data['headers'] ?? array();
$config = is_array( $chart->config ) ? $chart->config : array();
?>

<div class="wrap atables-edit-chart-page">
    <h1><?php esc_html_e( 'Edit Chart', 'a-tables-charts' ); ?></h1>
    
    <form id="atables-edit-chart-form" method="post">
        <input type="hidden" id="chart-id" value="<?php echo esc_attr( $chart->id ); ?>">
        
        <table class="form-table">
            <tr>
                <th><label for="chart-title"><?php esc_html_e( 'Chart Title', 'a-tables-charts' ); ?></label></th>
                <td>
                    <input type="text" id="chart-title" class="regular-text" 
                           value="<?php echo esc_attr( $chart->title ); ?>" required>
                </td>
            </tr>
            
            <tr>
                <th><label for="chart-type"><?php esc_html_e( 'Chart Type', 'a-tables-charts' ); ?></label></th>
                <td>
                    <select id="chart-type">
                        <option value="bar" <?php selected( $chart->type, 'bar' ); ?>>Bar Chart</option>
                        <option value="column" <?php selected( $chart->type, 'column' ); ?>>Column Chart</option>
                        <option value="line" <?php selected( $chart->type, 'line' ); ?>>Line Chart</option>
                        <option value="area" <?php selected( $chart->type, 'area' ); ?>>Area Chart</option>
                        <option value="pie" <?php selected( $chart->type, 'pie' ); ?>>Pie Chart</option>
                        <option value="doughnut" <?php selected( $chart->type, 'doughnut' ); ?>>Doughnut Chart</option>
                        <option value="scatter" <?php selected( $chart->type, 'scatter' ); ?>>Scatter Plot</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="label-column"><?php esc_html_e( 'Label Column', 'a-tables-charts' ); ?></label></th>
                <td>
                    <select id="label-column">
                        <option value=""><?php esc_html_e( 'Select column...', 'a-tables-charts' ); ?></option>
                        <?php foreach ( $headers as $header ) : ?>
                            <option value="<?php echo esc_attr( $header ); ?>" 
                                    <?php selected( $config['label_column'] ?? '', $header ); ?>>
                                <?php echo esc_html( $header ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label><?php esc_html_e( 'Data Columns', 'a-tables-charts' ); ?></label></th>
                <td>
                    <div id="data-columns-list">
                        <?php 
                        $data_columns = $config['data_columns'] ?? array();
                        foreach ( $headers as $header ) : 
                            $checked = in_array( $header, $data_columns, true );
                        ?>
                            <label>
                                <input type="checkbox" class="data-column-checkbox" 
                                       value="<?php echo esc_attr( $header ); ?>"
                                       <?php checked( $checked ); ?>>
                                <?php echo esc_html( $header ); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>
            
            <tr>
                <th><label for="chart-status"><?php esc_html_e( 'Status', 'a-tables-charts' ); ?></label></th>
                <td>
                    <select id="chart-status">
                        <option value="active" <?php selected( $chart->status, 'active' ); ?>>
                            <?php esc_html_e( 'Active', 'a-tables-charts' ); ?>
                        </option>
                        <option value="inactive" <?php selected( $chart->status, 'inactive' ); ?>>
                            <?php esc_html_e( 'Inactive', 'a-tables-charts' ); ?>
                        </option>
                    </select>
                </td>
            </tr>
        </table>
        
        <div class="atables-chart-preview-section">
            <h2><?php esc_html_e( 'Preview', 'a-tables-charts' ); ?></h2>
            <div class="atables-chart-preview-container" style="max-width: 800px; height: 400px; margin: 20px 0;">
                <canvas id="chart-preview"></canvas>
            </div>
            <button type="button" class="button" id="refresh-preview">
                <?php esc_html_e( 'Refresh Preview', 'a-tables-charts' ); ?>
            </button>
        </div>
        
        <p class="submit">
            <button type="submit" class="button button-primary">
                <?php esc_html_e( 'Update Chart', 'a-tables-charts' ); ?>
            </button>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-charts' ) ); ?>" 
               class="button">
                <?php esc_html_e( 'Cancel', 'a-tables-charts' ); ?>
            </a>
        </p>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
jQuery(document).ready(function($) {
    let chartInstance = null;
    const chartId = $('#chart-id').val();
    
    // Load initial preview
    refreshPreview();
    
    // Refresh preview button
    $('#refresh-preview').on('click', refreshPreview);
    
    // Auto-refresh on changes
    $('#chart-type, #label-column').on('change', refreshPreview);
    $('.data-column-checkbox').on('change', refreshPreview);
    
    // Refresh preview function
    function refreshPreview() {
        const type = $('#chart-type').val();
        const title = $('#chart-title').val();
        const labelColumn = $('#label-column').val();
        const dataColumns = $('.data-column-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (!labelColumn || dataColumns.length === 0) {
            return;
        }
        
        // Get chart data
        $.ajax({
            url: aTablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_get_chart_data',
                nonce: aTablesAdmin.nonce,
                chart_id: chartId
            },
            success: function(response) {
                if (response.success && response.data) {
                    renderPreview(type, title, response.data);
                }
            }
        });
    }
    
    // Render preview
    function renderPreview(type, title, data) {
        const ctx = document.getElementById('chart-preview');
        
        if (chartInstance) {
            chartInstance.destroy();
        }
        
        chartInstance = new Chart(ctx, {
            type: type === 'doughnut' ? 'doughnut' : 
                  type === 'column' ? 'bar' : type,
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: true, position: 'top' },
                    title: { display: true, text: title }
                }
            }
        });
    }
    
    // Form submission
    $('#atables-edit-chart-form').on('submit', async function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Updating...');
        
        const dataColumns = $('.data-column-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        $.ajax({
            url: aTablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_update_chart',
                nonce: aTablesAdmin.nonce,
                chart_id: chartId,
                title: $('#chart-title').val(),
                type: $('#chart-type').val(),
                status: $('#chart-status').val(),
                config: {
                    label_column: $('#label-column').val(),
                    data_columns: dataColumns
                }
            },
            success: async function(response) {
                if (response.success) {
                    await ATablesModal.success('Chart updated successfully!');
                    window.location.href = 'admin.php?page=a-tables-charts-charts';
                } else {
                    await ATablesModal.error(response.data || 'Failed to update chart');
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            },
            error: async function() {
                await ATablesModal.error('An error occurred. Please try again.');
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
```

#### Step 2: Update ChartController to handle config updates (2 hours)

**File:** `src/modules/charts/controllers/ChartController.php`

```php
// Add to handle_update_chart() method around line 170

if ( isset( $_POST['config'] ) && is_array( $_POST['config'] ) ) {
    // Sanitize config
    $config = array();
    
    if ( isset( $_POST['config']['label_column'] ) ) {
        $config['label_column'] = Sanitizer::text( $_POST['config']['label_column'] );
    }
    
    if ( isset( $_POST['config']['data_columns'] ) && is_array( $_POST['config']['data_columns'] ) ) {
        $config['data_columns'] = array_map( 
            array( 'ATablesCharts\\Shared\\Utils\\Sanitizer', 'text' ), 
            $_POST['config']['data_columns'] 
        );
    }
    
    if ( ! empty( $config ) ) {
        $data['config'] = $config;
    }
}
```

#### Step 3: Add Edit button to charts list (1 hour)

**File:** `src/modules/core/views/charts.php`

```php
// Line 56: Add Edit button before Delete

<div class="atables-chart-actions">
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-edit-chart&chart_id=' . $chart->id ) ); ?>" 
       class="button button-small">
        <?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
    </a>
    <button class="button button-small atables-copy-chart-shortcode" 
            data-chart-id="<?php echo esc_attr( $chart->id ); ?>" 
            title="<?php esc_attr_e( 'Copy Shortcode', 'a-tables-charts' ); ?>">
        <?php esc_html_e( 'Shortcode', 'a-tables-charts' ); ?>
    </button>
    <button class="button button-small atables-delete-chart" 
            data-chart-id="<?php echo esc_attr( $chart->id ); ?>">
        <?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
    </button>
</div>
```

#### Step 4: Register edit page route (1 hour)

**File:** Where admin pages are registered (likely in main plugin file or admin class)

```php
add_submenu_page(
    null, // Parent slug (null = hidden from menu)
    __( 'Edit Chart', 'a-tables-charts' ),
    __( 'Edit Chart', 'a-tables-charts' ),
    'manage_options',
    'a-tables-charts-edit-chart',
    array( $this, 'render_edit_chart_page' )
);

public function render_edit_chart_page() {
    require_once ATABLES_PLUGIN_DIR . 'src/modules/core/views/edit-chart.php';
}
```

**Testing Checklist:**
- [ ] Edit button appears in charts list
- [ ] Click edit → loads edit page
- [ ] Edit page shows current chart data
- [ ] Modify title → saves correctly
- [ ] Change chart type → saves correctly
- [ ] Change columns → saves correctly
- [ ] Preview updates when changes made
- [ ] Cancel button returns to list
- [ ] Update redirects to list with success message

---

### BUG-003: No Row Limit for Chart Data
**Priority:** P0 - PERFORMANCE CRITICAL  
**Severity:** Critical  
**Status:** 🔴 Open  
**Estimated Fix Time:** 3 hours

**Problem:**
ChartService::get_chart_data() loads ALL table rows without limit. Can cause memory exhaustion and timeouts.

**Impact:**
- Memory exhaustion with large tables
- PHP timeouts
- Browser crashes
- Poor user experience

**Location:**
- File: `src/modules/charts/services/ChartService.php`
- Method: `get_chart_data()`
- Line: 211

**Current Code:**
```php
$table_data = $this->table_repository->get_table_data( $chart->table_id );
```

**Fix:**

```php
/**
 * Get chart data for rendering
 *
 * @param int $chart_id Chart ID.
 * @param int $max_rows Maximum rows to load (default 1000).
 * @return array|false Chart data or false on error
 */
public function get_chart_data( $chart_id, $max_rows = 1000 ) {
    $chart = $this->chart_repository->find_by_id( $chart_id );
    if ( ! $chart ) {
        return false;
    }

    // Get table data with limit
    $table = $this->table_repository->find_by_id( $chart->table_id );
    if ( ! $table ) {
        return false;
    }

    // Get total row count
    $total_rows = $table->row_count;
    
    // Get table data with limit
    $table_data = $this->table_repository->get_table_data( 
        $chart->table_id,
        array(
            'limit' => $max_rows,
            'offset' => 0
        )
    );

    // Check if data was truncated
    $was_truncated = $total_rows > $max_rows;
    
    // Process data based on chart configuration
    $config       = $chart->config;
    $label_column = $config['label_column'] ?? null;
    $data_columns = $config['data_columns'] ?? array();

    if ( empty( $label_column ) || empty( $data_columns ) ) {
        return false;
    }

    $headers  = $table->get_headers();
    $labels   = array();
    $datasets = array();

    // Initialize datasets
    foreach ( $data_columns as $column ) {
        $datasets[ $column ] = array(
            'label' => $column,
            'data'  => array(),
        );
    }

    // Process table data
    foreach ( $table_data as $row ) {
        $label_index = array_search( $label_column, $headers, true );
        if ( $label_index !== false && isset( $row[ $label_index ] ) ) {
            $labels[] = $row[ $label_index ];
        }

        foreach ( $data_columns as $column ) {
            $column_index = array_search( $column, $headers, true );
            if ( $column_index !== false && isset( $row[ $column_index ] ) ) {
                $value = is_numeric( $row[ $column_index ] ) ? (float) $row[ $column_index ] : 0;
                $datasets[ $column ]['data'][] = $value;
            }
        }
    }

    return array(
        'labels'        => $labels,
        'datasets'      => array_values( $datasets ),
        'type'          => $chart->type,
        'title'         => $chart->title,
        'config'        => $config,
        'total_rows'    => $total_rows,
        'displayed_rows' => count( $table_data ),
        'was_truncated' => $was_truncated,
    );
}
```

**Additional Changes:**

1. **Update TableRepository::get_table_data()** to accept limit/offset:

```php
// File: src/modules/tables/repositories/TableRepository.php

public function get_table_data( $table_id, $args = array() ) {
    $defaults = array(
        'limit'  => null,
        'offset' => 0,
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    $query = "SELECT row_data FROM {$this->data_table} WHERE table_id = %d ORDER BY row_index";
    
    if ( $args['limit'] ) {
        $query .= $this->wpdb->prepare( " LIMIT %d OFFSET %d", $args['limit'], $args['offset'] );
    }
    
    $results = $this->wpdb->get_results(
        $this->wpdb->prepare( $query, $table_id ),
        ARRAY_A
    );
    
    // ... rest of method
}
```

2. **Show warning in frontend if truncated:**

```php
// File: src/modules/frontend/renderers/ChartRenderer.php

// Add warning message if data truncated
if ( $chart_data['was_truncated'] ?? false ) {
    ?>
    <div class="atables-chart-warning" style="background: #fff3cd; border: 1px solid #ffc107; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
        <strong>ℹ️ Note:</strong> This chart displays <?php echo number_format( $chart_data['displayed_rows'] ); ?> 
        of <?php echo number_format( $chart_data['total_rows'] ); ?> total rows.
    </div>
    <?php
}
```

**Testing Checklist:**
- [ ] Create chart from small table (<100 rows) → All data displayed
- [ ] Create chart from medium table (500 rows) → All data displayed
- [ ] Create chart from large table (2000 rows) → Truncated to 1000, warning shown
- [ ] Create chart from huge table (10,000 rows) → Truncated to 1000, warning shown
- [ ] Check memory usage stays reasonable
- [ ] Check render time <3 seconds for large tables

---

## ⚠️ P1 - HIGH PRIORITY BUGS

### BUG-004: Missing ChartJsRenderer Class
**Priority:** P1 - ARCHITECTURE  
**Status:** 🟡 Open  
**Estimated Fix Time:** 8 hours

**Problem:**
Chart.js rendering is inline in ChartRenderer.php. No dedicated renderer class like GoogleChartsRenderer.

**Impact:**
- Inconsistent architecture
- Harder to maintain Chart.js features
- Cannot easily extend Chart.js
- Code duplication

**Fix:** Create dedicated ChartJsRenderer class

**File:** Create `src/modules/charts/renderers/ChartJsRenderer.php`

```php
<?php
/**
 * Chart.js Renderer
 *
 * Renders charts using Chart.js library.
 *
 * @package ATablesCharts\Charts\Renderers
 * @since 1.0.0
 */

namespace ATablesCharts\Charts\Renderers;

class ChartJsRenderer {

    private static $chartjs_loaded = false;

    /**
     * Render chart using Chart.js
     *
     * @param object $chart Chart object.
     * @param array  $data  Chart data.
     * @param array  $options Render options.
     * @return string Chart HTML.
     */
    public function render( $chart, $data, $options = array() ) {
        $chart_id = 'chartjs_' . $chart->id . '_' . uniqid();
        
        // Get configuration
        $config = is_array( $chart->config ) ? $chart->config : array();
        
        // Add defaults
        $config['width'] = $options['width'] ?? '100%';
        $config['height'] = $options['height'] ?? '400px';
        
        // Get Chart.js configuration
        $chartjs_config = $this->get_chartjs_config( $chart, $data, $config );
        $chartjs_config_json = wp_json_encode( $chartjs_config );
        
        ob_start();
        
        // Load Chart.js only once
        if ( ! self::$chartjs_loaded ) {
            ?>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" crossorigin="anonymous"></script>
            <?php
            self::$chartjs_loaded = true;
        }
        ?>
        
        <div class="atables-chartjs-wrapper" style="max-width: <?php echo esc_attr( $config['width'] ); ?>; margin: 20px 0;">
            <div class="atables-chartjs-container" style="height: <?php echo esc_attr( $config['height'] ); ?>; background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                <canvas id="<?php echo esc_attr( $chart_id ); ?>"></canvas>
            </div>
        </div>
        
        <script type="text/javascript">
        (function() {
            var chartConfig_<?php echo esc_attr( $chart_id ); ?> = <?php echo $chartjs_config_json; ?>;
            
            function renderChart_<?php echo esc_attr( $chart_id ); ?>() {
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js not loaded');
                    return;
                }
                
                try {
                    var ctx = document.getElementById('<?php echo esc_js( $chart_id ); ?>');
                    if (!ctx) return;
                    
                    new Chart(ctx, chartConfig_<?php echo esc_attr( $chart_id ); ?>);
                    console.log('✓ Chart.js chart rendered: <?php echo esc_js( $chart_id ); ?>');
                } catch (error) {
                    console.error('Chart.js error:', error);
                }
            }
            
            // Render when Chart.js is ready
            if (typeof Chart !== 'undefined') {
                renderChart_<?php echo esc_attr( $chart_id ); ?>();
            } else {
                // Retry
                var retries = 0;
                var interval = setInterval(function() {
                    if (typeof Chart !== 'undefined') {
                        clearInterval(interval);
                        renderChart_<?php echo esc_attr( $chart_id ); ?>();
                    } else if (++retries > 50) {
                        clearInterval(interval);
                        console.error('Chart.js failed to load');
                    }
                }, 100);
            }
        })();
        </script>
        <?php
        
        return ob_get_clean();
    }

    /**
     * Get Chart.js configuration
     *
     * @param object $chart Chart object.
     * @param array  $data  Chart data.
     * @param array  $config Chart configuration.
     * @return array Chart.js config object.
     */
    private function get_chartjs_config( $chart, $data, $config ) {
        // Map chart type
        $chartjs_type = $this->map_chart_type( $chart->type );
        
        // Build configuration
        $chartjs_config = array(
            'type' => $chartjs_type,
            'data' => $this->prepare_data( $data, $config ),
            'options' => $this->get_chart_options( $chart, $config ),
        );
        
        return $chartjs_config;
    }

    /**
     * Map internal chart type to Chart.js type
     *
     * @param string $type Internal chart type.
     * @return string Chart.js chart type.
     */
    private function map_chart_type( $type ) {
        $map = array(
            'column'    => 'bar',
            'area'      => 'line',
            'doughnut'  => 'doughnut',
            'scatter'   => 'scatter',
        );
        
        return isset( $map[ $type ] ) ? $map[ $type ] : $type;
    }

    /**
     * Prepare data for Chart.js
     *
     * @param array $data   Chart data.
     * @param array $config Configuration.
     * @return array Chart.js data object.
     */
    private function prepare_data( $data, $config ) {
        if ( empty( $data['labels'] ) || empty( $data['datasets'] ) ) {
            return array(
                'labels' => array( 'No Data' ),
                'datasets' => array(
                    array(
                        'label' => 'No Data',
                        'data' => array( 0 ),
                    ),
                ),
            );
        }
        
        // Add colors to datasets
        $datasets = array();
        foreach ( $data['datasets'] as $index => $dataset ) {
            $color = $this->get_dataset_color( $index, $config );
            $datasets[] = array_merge(
                $dataset,
                array(
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'borderWidth' => 2,
                )
            );
        }
        
        return array(
            'labels' => $data['labels'],
            'datasets' => $datasets,
        );
    }

    /**
     * Get chart options for Chart.js
     *
     * @param object $chart  Chart object.
     * @param array  $config Configuration.
     * @return array Chart.js options object.
     */
    private function get_chart_options( $chart, $config ) {
        $options = array(
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => array(
                'legend' => array(
                    'display' => true,
                    'position' => $config['legend_position'] ?? 'top',
                ),
                'title' => array(
                    'display' => ! empty( $chart->title ),
                    'text' => $chart->title,
                ),
            ),
        );
        
        // Add scales for non-pie charts
        if ( ! in_array( $chart->type, array( 'pie', 'doughnut' ), true ) ) {
            $options['scales'] = array(
                'y' => array(
                    'beginAtZero' => true,
                ),
            );
        }
        
        return $options;
    }

    /**
     * Get dataset color
     *
     * @param int   $index  Dataset index.
     * @param array $config Configuration.
     * @return string Color value.
     */
    private function get_dataset_color( $index, $config ) {
        // Check for custom colors
        if ( isset( $config['colors'] ) && isset( $config['colors'][ $index ] ) ) {
            return $config['colors'][ $index ];
        }
        
        // Default color palette
        $colors = array(
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 99, 132, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
        );
        
        return $colors[ $index % count( $colors ) ];
    }
}
```

**Then update ChartRenderer.php to use it:**

```php
// File: src/modules/frontend/renderers/ChartRenderer.php

namespace ATablesCharts\Frontend\Renderers;

use ATablesCharts\Charts\Renderers\ChartJsRenderer;

class ChartRenderer {

    private $chartjs_renderer;

    public function __construct() {
        $this->chartjs_renderer = new ChartJsRenderer();
    }

    public function render( $options ) {
        $chart = $options['chart'];
        $chart_data = $options['chart_data'];
        
        return $this->chartjs_renderer->render( $chart, $chart_data, $options );
    }
}
```

---

### BUG-005: Missing Scatter Chart Implementation
**Priority:** P1  
**Status:** 🟡 Open  
**Estimated Fix Time:** 6 hours

**Problem:**
Scatter chart type claimed but not implemented.

**Fix:** Implement scatter chart support

**Required Changes:**

1. **Add to validation** (already covered in BUG-001)
2. **Add Chart.js scatter support:**

```javascript
// Scatter charts need {x, y} data format
private function prepare_scatter_data( $data, $config ) {
    // For scatter, expect two numeric columns
    if ( count( $data['datasets'] ) < 2 ) {
        return null; // Need X and Y columns
    }
    
    $x_data = $data['datasets'][0]['data'];
    $y_data = $data['datasets'][1]['data'];
    
    $scatter_points = array();
    for ( $i = 0; $i < count( $x_data ); $i++ ) {
        $scatter_points[] = array(
            'x' => $x_data[$i],
            'y' => $y_data[$i] ?? 0,
        );
    }
    
    return array(
        'datasets' => array(
            array(
                'label' => $data['datasets'][1]['label'] ?? 'Data',
                'data' => $scatter_points,
                'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
            ),
        ),
    );
}
```

3. **Add Google Charts scatter support:**

```php
// In GoogleChartsRenderer
case 'scatter':
    // Google Charts ScatterChart expects numeric X values
    $options['hAxis'] = array( 'title' => 'X' );
    $options['vAxis'] = array( 'title' => 'Y' );
    break;
```

**Testing:**
- [ ] Create scatter chart with numeric X and Y
- [ ] Verify points render correctly
- [ ] Test hover tooltips
- [ ] Test Chart.js version
- [ ] Test Google Charts version

---

### BUG-006 through BUG-015: [Additional bugs documented...]

---

## 📊 BUG STATISTICS

### By Priority
- P0 (Critical): 3 bugs = 19.5 hours
- P1 (High): 5 bugs = 40 hours  
- P2 (Medium): 7 bugs = 30 hours
- **Total:** 15 bugs = 89.5 hours

### By Component
- Chart Types: 5 bugs
- Architecture: 2 bugs
- UI/UX: 4 bugs
- Performance: 2 bugs
- Features: 2 bugs

### By Status
- 🔴 Open: 15
- 🟡 In Progress: 0
- 🟢 Fixed: 0

---

## ✅ FIX CHECKLIST

### Week 1: Critical Fixes
- [ ] BUG-001: Fix type validation (30 min)
- [ ] BUG-003: Add row limit (3 hours)
- [ ] BUG-002: Create edit interface (16 hours)

### Week 2: High Priority  
- [ ] BUG-004: Create ChartJsRenderer (8 hours)
- [ ] BUG-005: Add scatter chart (6 hours)
- [ ] BUG-006: Add area chart (4 hours)
- [ ] BUG-007: Add customization UI (16 hours)

### Week 3: Medium Priority
- [ ] BUG-008 through BUG-015

---

**Last Updated:** October 31, 2025  
**Next Review:** After Week 1 fixes complete
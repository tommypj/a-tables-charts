<?php
/**
 * Enhanced Table Editor with Tabs
 *
 * @package ATables
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$table_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

if ( ! $table_id ) {
    echo '<div class="wrap"><div class="notice notice-error"><p>' . esc_html__( 'Invalid table ID.', 'a-tables-charts' ) . '</p></div></div>';
    return;
}

$service = new \ATables\Features\Tables\TableService();
$table = $service->get_table( $table_id );

if ( ! $table ) {
    echo '<div class="wrap"><div class="notice notice-error"><p>' . esc_html__( 'Table not found.', 'a-tables-charts' ) . '</p></div></div>';
    return;
}

// Get display settings
global $wpdb;
$settings_table = $wpdb->prefix . 'atables_display_settings';
$display_settings = $wpdb->get_row(
    $wpdb->prepare( "SELECT * FROM {$settings_table} WHERE table_id = %d", $table_id ),
    ARRAY_A
);

// Default display settings
if ( ! $display_settings ) {
    $display_settings = array(
        'enable_search' => 1,
        'enable_sorting' => 1,
        'enable_pagination' => 1,
        'rows_per_page' => 10,
        'theme' => 'default',
        'custom_css' => '',
        'responsive_mode' => 'scroll',
    );
}

$is_pro = \ATables\Licensing\LicenseManager::is_pro_active();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html( $table['title'] ); ?></h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts' ) ); ?>" class="page-title-action">
        <?php esc_html_e( 'Back to Tables', 'a-tables-charts' ); ?>
    </a>
    <a href="<?php echo esc_url( get_permalink( get_option( 'page_on_front' ) ) . '?preview_table=' . $table_id ); ?>" class="page-title-action" target="_blank">
        <?php esc_html_e( 'Preview Table', 'a-tables-charts' ); ?>
    </a>

    <hr class="wp-header-end">

    <!-- Tab Navigation -->
    <nav class="nav-tab-wrapper atables-tab-wrapper">
        <a href="#tab-basic" class="nav-tab nav-tab-active" data-tab="basic">
            <span class="dashicons dashicons-admin-settings"></span>
            <?php esc_html_e( 'Basic Info', 'a-tables-charts' ); ?>
        </a>
        <a href="#tab-data" class="nav-tab" data-tab="data">
            <span class="dashicons dashicons-editor-table"></span>
            <?php esc_html_e( 'Table Data', 'a-tables-charts' ); ?>
        </a>
        <a href="#tab-display" class="nav-tab" data-tab="display">
            <span class="dashicons dashicons-admin-appearance"></span>
            <?php esc_html_e( 'Display Settings', 'a-tables-charts' ); ?>
        </a>

        <?php if ( $is_pro ) : ?>
        <a href="#tab-validation" class="nav-tab" data-tab="validation">
            <span class="dashicons dashicons-yes-alt"></span>
            <?php esc_html_e( 'Validation', 'a-tables-charts' ); ?>
            <span class="atables-pro-badge">PRO</span>
        </a>
        <a href="#tab-formatting" class="nav-tab" data-tab="formatting">
            <span class="dashicons dashicons-art"></span>
            <?php esc_html_e( 'Conditional Formatting', 'a-tables-charts' ); ?>
            <span class="atables-pro-badge">PRO</span>
        </a>
        <?php else : ?>
        <a href="#tab-validation" class="nav-tab nav-tab-locked" data-tab="validation">
            <span class="dashicons dashicons-lock"></span>
            <?php esc_html_e( 'Validation', 'a-tables-charts' ); ?>
            <span class="atables-pro-badge">PRO</span>
        </a>
        <a href="#tab-formatting" class="nav-tab nav-tab-locked" data-tab="formatting">
            <span class="dashicons dashicons-lock"></span>
            <?php esc_html_e( 'Conditional Formatting', 'a-tables-charts' ); ?>
            <span class="atables-pro-badge">PRO</span>
        </a>
        <?php endif; ?>
    </nav>

    <!-- Tab Content -->
    <div class="atables-tab-content">

        <!-- Basic Info Tab -->
        <div id="tab-basic" class="atables-tab-pane active">
            <div class="atables-card">
                <h2><?php esc_html_e( 'Table Information', 'a-tables-charts' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="table-title"><?php esc_html_e( 'Title', 'a-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="table-title" class="regular-text" value="<?php echo esc_attr( $table['title'] ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="table-description"><?php esc_html_e( 'Description', 'a-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <textarea id="table-description" class="large-text" rows="3"><?php echo esc_textarea( $table['description'] ); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Shortcode', 'a-tables-charts' ); ?></th>
                        <td>
                            <input type="text" class="regular-text code" value="[atables id=&quot;<?php echo esc_attr( $table_id ); ?>&quot;]" readonly onclick="this.select();">
                            <p class="description"><?php esc_html_e( 'Copy this shortcode and paste it into any page or post.', 'a-tables-charts' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Table Stats', 'a-tables-charts' ); ?></th>
                        <td>
                            <p class="description">
                                <?php
                                printf(
                                    esc_html__( '%1$d rows × %2$d columns | Created: %3$s', 'a-tables-charts' ),
                                    '<strong>' . esc_html( $table['row_count'] ) . '</strong>',
                                    '<strong>' . esc_html( $table['column_count'] ) . '</strong>',
                                    '<strong>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $table['created_at'] ) ) ) . '</strong>'
                                );
                                ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="button" class="button button-primary button-large" id="save-basic-info">
                        <?php esc_html_e( 'Save Changes', 'a-tables-charts' ); ?>
                    </button>
                </p>
            </div>
        </div>

        <!-- Table Data Tab -->
        <div id="tab-data" class="atables-tab-pane">
            <div class="atables-card">
                <div class="atables-data-header">
                    <h2><?php esc_html_e( 'Table Data', 'a-tables-charts' ); ?></h2>
                    <div class="atables-data-actions">
                        <button type="button" class="button" id="add-row-btn">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <?php esc_html_e( 'Add Row', 'a-tables-charts' ); ?>
                        </button>
                        <button type="button" class="button" id="add-column-btn">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <?php esc_html_e( 'Add Column', 'a-tables-charts' ); ?>
                        </button>
                        <button type="button" class="button button-primary" id="save-data-btn">
                            <span class="dashicons dashicons-saved"></span>
                            <?php esc_html_e( 'Save All Changes', 'a-tables-charts' ); ?>
                        </button>
                    </div>
                </div>

                <div class="atables-data-table-wrapper">
                    <table class="atables-editable-table" id="editable-table">
                        <thead>
                            <tr>
                                <th class="row-number">#</th>
                                <?php foreach ( $table['columns'] as $column ) : ?>
                                <th data-column-id="<?php echo esc_attr( $column['id'] ); ?>">
                                    <div class="column-header">
                                        <span class="column-name" contenteditable="true"><?php echo esc_html( $column['column_name'] ); ?></span>
                                        <button type="button" class="column-delete" title="<?php esc_attr_e( 'Delete Column', 'a-tables-charts' ); ?>">
                                            <span class="dashicons dashicons-no-alt"></span>
                                        </button>
                                    </div>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $table['rows'] as $index => $row ) : ?>
                            <tr data-row-id="<?php echo esc_attr( $row['id'] ); ?>">
                                <td class="row-number">
                                    <span><?php echo esc_html( $index + 1 ); ?></span>
                                    <button type="button" class="row-delete" title="<?php esc_attr_e( 'Delete Row', 'a-tables-charts' ); ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </td>
                                <?php foreach ( $table['columns'] as $column ) : ?>
                                <td contenteditable="true" data-column="<?php echo esc_attr( $column['column_name'] ); ?>">
                                    <?php echo esc_html( $row['row_data'][ $column['column_name'] ] ?? '' ); ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <p class="description" style="margin-top: 20px;">
                    <span class="dashicons dashicons-info"></span>
                    <?php esc_html_e( 'Click any cell to edit. Changes are saved when you click "Save All Changes".', 'a-tables-charts' ); ?>
                </p>
            </div>
        </div>

        <!-- Display Settings Tab -->
        <div id="tab-display" class="atables-tab-pane">
            <div class="atables-card">
                <h2><?php esc_html_e( 'Display Settings', 'a-tables-charts' ); ?></h2>
                <p class="description">
                    <?php esc_html_e( 'Configure how your table appears on the frontend.', 'a-tables-charts' ); ?>
                </p>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Table Features', 'a-tables-charts' ); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" id="enable-search" <?php checked( $display_settings['enable_search'], 1 ); ?>>
                                    <?php esc_html_e( 'Enable search box', 'a-tables-charts' ); ?>
                                </label>
                                <br>
                                <label>
                                    <input type="checkbox" id="enable-sorting" <?php checked( $display_settings['enable_sorting'], 1 ); ?>>
                                    <?php esc_html_e( 'Enable column sorting', 'a-tables-charts' ); ?>
                                </label>
                                <br>
                                <label>
                                    <input type="checkbox" id="enable-pagination" <?php checked( $display_settings['enable_pagination'], 1 ); ?>>
                                    <?php esc_html_e( 'Enable pagination', 'a-tables-charts' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rows-per-page"><?php esc_html_e( 'Rows Per Page', 'a-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="rows-per-page" class="small-text" min="1" max="100" value="<?php echo esc_attr( $display_settings['rows_per_page'] ); ?>">
                            <p class="description"><?php esc_html_e( 'Number of rows to display per page (if pagination is enabled).', 'a-tables-charts' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="table-theme"><?php esc_html_e( 'Table Theme', 'a-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <select id="table-theme" class="regular-text">
                                <option value="default" <?php selected( $display_settings['theme'], 'default' ); ?>><?php esc_html_e( 'Default', 'a-tables-charts' ); ?></option>
                                <option value="minimal" <?php selected( $display_settings['theme'], 'minimal' ); ?>><?php esc_html_e( 'Minimal', 'a-tables-charts' ); ?></option>
                                <option value="dark" <?php selected( $display_settings['theme'], 'dark' ); ?>><?php esc_html_e( 'Dark', 'a-tables-charts' ); ?></option>
                            </select>
                            <p class="description"><?php esc_html_e( 'Choose a visual theme for your table.', 'a-tables-charts' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="responsive-mode"><?php esc_html_e( 'Responsive Mode', 'a-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <select id="responsive-mode" class="regular-text">
                                <option value="scroll" <?php selected( $display_settings['responsive_mode'], 'scroll' ); ?>><?php esc_html_e( 'Horizontal Scroll', 'a-tables-charts' ); ?></option>
                                <option value="stack" <?php selected( $display_settings['responsive_mode'], 'stack' ); ?>><?php esc_html_e( 'Stack Columns', 'a-tables-charts' ); ?></option>
                            </select>
                            <p class="description"><?php esc_html_e( 'How the table adapts to small screens.', 'a-tables-charts' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="custom-css"><?php esc_html_e( 'Custom CSS', 'a-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <textarea id="custom-css" class="large-text code" rows="8"><?php echo esc_textarea( $display_settings['custom_css'] ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Add custom CSS styles for this table. Styles will be applied only to this table.', 'a-tables-charts' ); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="button" class="button button-primary button-large" id="save-display-settings">
                        <?php esc_html_e( 'Save Display Settings', 'a-tables-charts' ); ?>
                    </button>
                </p>
            </div>
        </div>

        <!-- Validation Tab (PRO) -->
        <div id="tab-validation" class="atables-tab-pane">
            <?php if ( $is_pro ) : ?>
            <div class="atables-card">
                <h2><?php esc_html_e( 'Validation Rules', 'a-tables-charts' ); ?></h2>
                <p><?php esc_html_e( 'Validation rules coming soon in PRO version.', 'a-tables-charts' ); ?></p>
            </div>
            <?php else : ?>
            <?php \ATables\Licensing\UpgradePrompts::show_prompt( 'validation' ); ?>
            <?php endif; ?>
        </div>

        <!-- Conditional Formatting Tab (PRO) -->
        <div id="tab-formatting" class="atables-tab-pane">
            <?php if ( $is_pro ) : ?>
            <div class="atables-card">
                <h2><?php esc_html_e( 'Conditional Formatting', 'a-tables-charts' ); ?></h2>
                <p><?php esc_html_e( 'Conditional formatting coming soon in PRO version.', 'a-tables-charts' ); ?></p>
            </div>
            <?php else : ?>
            <?php \ATables\Licensing\UpgradePrompts::show_prompt( 'conditional_formatting' ); ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    const tableId = <?php echo esc_js( $table_id ); ?>;

    // Tab switching
    $('.atables-tab-wrapper .nav-tab').on('click', function(e) {
        e.preventDefault();

        // Check if tab is locked
        if ($(this).hasClass('nav-tab-locked')) {
            alert('<?php esc_html_e( 'This feature is only available in the PRO version.', 'a-tables-charts' ); ?>');
            return;
        }

        const tab = $(this).data('tab');

        // Update active tab
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // Show corresponding content
        $('.atables-tab-pane').removeClass('active');
        $('#tab-' + tab).addClass('active');
    });

    // Save basic info
    $('#save-basic-info').on('click', function() {
        const button = $(this);
        const originalText = button.text();

        button.prop('disabled', true).text('<?php esc_html_e( 'Saving...', 'a-tables-charts' ); ?>');

        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_save_table',
                nonce: atablesAdmin.nonce,
                table_id: tableId,
                data: {
                    title: $('#table-title').val(),
                    description: $('#table-description').val()
                }
            },
            success: function(response) {
                if (response.success) {
                    // Update page title
                    $('.wp-heading-inline').text($('#table-title').val());
                    showNotice('success', response.data.message);
                } else {
                    showNotice('error', response.data.message);
                }
                button.prop('disabled', false).text(originalText);
            },
            error: function() {
                showNotice('error', '<?php esc_html_e( 'An error occurred. Please try again.', 'a-tables-charts' ); ?>');
                button.prop('disabled', false).text(originalText);
            }
        });
    });

    // Save display settings
    $('#save-display-settings').on('click', function() {
        const button = $(this);
        const originalText = button.text();

        button.prop('disabled', true).text('<?php esc_html_e( 'Saving...', 'a-tables-charts' ); ?>');

        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_save_display_settings',
                nonce: atablesAdmin.nonce,
                table_id: tableId,
                settings: {
                    enable_search: $('#enable-search').is(':checked') ? 1 : 0,
                    enable_sorting: $('#enable-sorting').is(':checked') ? 1 : 0,
                    enable_pagination: $('#enable-pagination').is(':checked') ? 1 : 0,
                    rows_per_page: $('#rows-per-page').val(),
                    theme: $('#table-theme').val(),
                    responsive_mode: $('#responsive-mode').val(),
                    custom_css: $('#custom-css').val()
                }
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                } else {
                    showNotice('error', response.data.message);
                }
                button.prop('disabled', false).text(originalText);
            },
            error: function() {
                showNotice('error', '<?php esc_html_e( 'An error occurred. Please try again.', 'a-tables-charts' ); ?>');
                button.prop('disabled', false).text(originalText);
            }
        });
    });

    // Save table data
    $('#save-data-btn').on('click', function() {
        const button = $(this);
        const originalText = button.text();

        button.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> <?php esc_html_e( 'Saving...', 'a-tables-charts' ); ?>');

        const tableData = extractTableData();

        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_save_table_data',
                nonce: atablesAdmin.nonce,
                table_id: tableId,
                columns: tableData.columns,
                rows: tableData.rows
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    // Reload page to reflect changes
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showNotice('error', response.data.message);
                    button.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                showNotice('error', '<?php esc_html_e( 'An error occurred. Please try again.', 'a-tables-charts' ); ?>');
                button.prop('disabled', false).html(originalText);
            }
        });
    });

    // Extract table data from editable table
    function extractTableData() {
        const columns = [];
        const rows = [];

        // Get columns
        $('#editable-table thead th').not('.row-number').each(function() {
            const columnName = $(this).find('.column-name').text().trim();
            const columnId = $(this).data('column-id');
            if (columnName) {
                columns.push({
                    id: columnId,
                    name: columnName
                });
            }
        });

        // Get rows
        $('#editable-table tbody tr').each(function() {
            const row = {};
            const rowId = $(this).data('row-id');

            $(this).find('td[contenteditable]').each(function(index) {
                const columnName = columns[index].name;
                row[columnName] = $(this).text().trim();
            });

            rows.push({
                id: rowId,
                data: row
            });
        });

        return { columns, rows };
    }

    // Add new row
    $('#add-row-btn').on('click', function() {
        const tbody = $('#editable-table tbody');
        const columnCount = $('#editable-table thead th').not('.row-number').length;
        const rowNumber = tbody.find('tr').length + 1;

        let newRow = '<tr data-row-id="new-' + Date.now() + '">';
        newRow += '<td class="row-number"><span>' + rowNumber + '</span><button type="button" class="row-delete"><span class="dashicons dashicons-trash"></span></button></td>';

        $('#editable-table thead th').not('.row-number').each(function() {
            const columnName = $(this).find('.column-name').text();
            newRow += '<td contenteditable="true" data-column="' + columnName + '"></td>';
        });

        newRow += '</tr>';
        tbody.append(newRow);

        showNotice('info', '<?php esc_html_e( 'New row added. Click "Save All Changes" to save.', 'a-tables-charts' ); ?>');
    });

    // Add new column
    $('#add-column-btn').on('click', function() {
        const columnName = prompt('<?php esc_html_e( 'Enter column name:', 'a-tables-charts' ); ?>');

        if (!columnName) return;

        // Add to header
        const newHeader = '<th data-column-id="new-' + Date.now() + '">' +
            '<div class="column-header">' +
            '<span class="column-name" contenteditable="true">' + columnName + '</span>' +
            '<button type="button" class="column-delete"><span class="dashicons dashicons-no-alt"></span></button>' +
            '</div></th>';
        $('#editable-table thead tr').append(newHeader);

        // Add to all rows
        $('#editable-table tbody tr').each(function() {
            $(this).append('<td contenteditable="true" data-column="' + columnName + '"></td>');
        });

        showNotice('info', '<?php esc_html_e( 'New column added. Click "Save All Changes" to save.', 'a-tables-charts' ); ?>');
    });

    // Delete row
    $(document).on('click', '.row-delete', function() {
        if (!confirm('<?php esc_html_e( 'Are you sure you want to delete this row?', 'a-tables-charts' ); ?>')) {
            return;
        }
        $(this).closest('tr').remove();
        updateRowNumbers();
        showNotice('info', '<?php esc_html_e( 'Row marked for deletion. Click "Save All Changes" to confirm.', 'a-tables-charts' ); ?>');
    });

    // Delete column
    $(document).on('click', '.column-delete', function() {
        if (!confirm('<?php esc_html_e( 'Are you sure you want to delete this column?', 'a-tables-charts' ); ?>')) {
            return;
        }
        const columnIndex = $(this).closest('th').index();
        $(this).closest('th').remove();
        $('#editable-table tbody tr').each(function() {
            $(this).find('td').eq(columnIndex).remove();
        });
        showNotice('info', '<?php esc_html_e( 'Column marked for deletion. Click "Save All Changes" to confirm.', 'a-tables-charts' ); ?>');
    });

    // Update row numbers
    function updateRowNumbers() {
        $('#editable-table tbody tr').each(function(index) {
            $(this).find('.row-number span').text(index + 1);
        });
    }

    // Show notice
    function showNotice(type, message) {
        const notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap > h1').after(notice);

        setTimeout(function() {
            notice.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
});
</script>

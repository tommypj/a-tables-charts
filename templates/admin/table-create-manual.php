<?php
/**
 * Manual Table Creation Template
 *
 * @package ATables
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e( 'Create Table Manually', 'a-tables-charts' ); ?></h1>

    <div class="atables-manual-create-container" style="max-width: 800px;">
        <div class="card" style="padding: 20px; margin-top: 20px;">
            <h2><?php esc_html_e( 'Table Setup', 'a-tables-charts' ); ?></h2>
            <p class="description">
                <?php esc_html_e( 'Create an empty table by defining its structure. You can add data after creation.', 'a-tables-charts' ); ?>
            </p>

            <form id="atables-manual-create-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="table-title"><?php esc_html_e( 'Table Title', 'a-tables-charts' ); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="text" id="table-title" name="title" class="regular-text" placeholder="<?php esc_attr_e( 'e.g., Product Catalog', 'a-tables-charts' ); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="table-description"><?php esc_html_e( 'Description', 'a-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <textarea id="table-description" name="description" class="large-text" rows="3" placeholder="<?php esc_attr_e( 'Optional description', 'a-tables-charts' ); ?>"></textarea>
                        </td>
                    </tr>
                </table>

                <h3><?php esc_html_e( 'Define Columns', 'a-tables-charts' ); ?></h3>
                <p class="description">
                    <?php esc_html_e( 'Add at least one column to your table. You can add more columns later.', 'a-tables-charts' ); ?>
                </p>

                <div id="columns-container">
                    <div class="column-row">
                        <input type="text" class="column-name regular-text" placeholder="<?php esc_attr_e( 'Column name (e.g., Product Name)', 'a-tables-charts' ); ?>" required>
                        <button type="button" class="button remove-column" disabled>
                            <span class="dashicons dashicons-no-alt"></span>
                            <?php esc_html_e( 'Remove', 'a-tables-charts' ); ?>
                        </button>
                    </div>
                </div>

                <p style="margin-top: 10px;">
                    <button type="button" class="button" id="add-column-btn">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php esc_html_e( 'Add Column', 'a-tables-charts' ); ?>
                    </button>
                </p>

                <h3><?php esc_html_e( 'Initial Rows', 'a-tables-charts' ); ?></h3>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="initial-rows"><?php esc_html_e( 'Number of Empty Rows', 'a-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="initial-rows" name="initial_rows" class="small-text" min="0" max="100" value="5">
                            <p class="description"><?php esc_html_e( 'Create empty rows to fill in later. Set to 0 to create table without rows.', 'a-tables-charts' ); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary button-large" id="create-button">
                        <span class="dashicons dashicons-saved"></span>
                        <?php esc_html_e( 'Create Table', 'a-tables-charts' ); ?>
                    </button>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-new' ) ); ?>" class="button button-large">
                        <?php esc_html_e( 'Cancel', 'a-tables-charts' ); ?>
                    </a>
                </p>

                <div id="create-progress" style="display: none; margin-top: 20px;">
                    <div class="notice notice-info inline">
                        <p>
                            <span class="dashicons dashicons-update dashicons-spin"></span>
                            <strong><?php esc_html_e( 'Creating table...', 'a-tables-charts' ); ?></strong>
                        </p>
                    </div>
                </div>

                <div id="create-result" style="display: none; margin-top: 20px;"></div>
            </form>
        </div>

        <!-- Quick Start Tips -->
        <div class="card" style="padding: 20px; margin-top: 20px; background: #f9f9f9;">
            <h3 style="margin-top: 0;">
                <span class="dashicons dashicons-lightbulb" style="color: #f0b849;"></span>
                <?php esc_html_e( 'Quick Tips', 'a-tables-charts' ); ?>
            </h3>
            <ul style="margin-left: 20px;">
                <li><?php esc_html_e( 'Start with the columns you need - you can always add more later', 'a-tables-charts' ); ?></li>
                <li><?php esc_html_e( 'Use descriptive column names that are easy to understand', 'a-tables-charts' ); ?></li>
                <li><?php esc_html_e( 'After creating the table, you can add/edit data in the table editor', 'a-tables-charts' ); ?></li>
                <li><?php esc_html_e( 'Set initial rows to 0 if you prefer to add data row by row later', 'a-tables-charts' ); ?></li>
            </ul>
        </div>
    </div>
</div>

<style>
.required {
    color: #d63638;
}

.column-row {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 10px;
}

.column-row .column-name {
    flex: 1;
}

.column-row .remove-column {
    flex-shrink: 0;
}

.column-row .remove-column .dashicons {
    margin-top: 4px;
}

#columns-container {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    background: #fff;
    margin-top: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    let columnCount = 1;

    // Add column
    $('#add-column-btn').on('click', function() {
        columnCount++;
        const newColumn = `
            <div class="column-row">
                <input type="text" class="column-name regular-text" placeholder="<?php esc_attr_e( 'Column name', 'a-tables-charts' ); ?>" required>
                <button type="button" class="button remove-column">
                    <span class="dashicons dashicons-no-alt"></span>
                    <?php esc_html_e( 'Remove', 'a-tables-charts' ); ?>
                </button>
            </div>
        `;
        $('#columns-container').append(newColumn);
        updateRemoveButtons();
    });

    // Remove column
    $(document).on('click', '.remove-column', function() {
        if (columnCount > 1) {
            $(this).closest('.column-row').remove();
            columnCount--;
            updateRemoveButtons();
        }
    });

    // Update remove button states
    function updateRemoveButtons() {
        $('.remove-column').prop('disabled', columnCount <= 1);
    }

    // Form submission
    $('#atables-manual-create-form').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const button = $('#create-button');

        // Collect column names
        const columns = [];
        $('.column-name').each(function() {
            const name = $(this).val().trim();
            if (name) {
                columns.push(name);
            }
        });

        // Validate
        if (columns.length === 0) {
            alert('<?php esc_html_e( 'Please add at least one column.', 'a-tables-charts' ); ?>');
            return;
        }

        // Check for duplicate column names
        const uniqueColumns = [...new Set(columns)];
        if (uniqueColumns.length !== columns.length) {
            alert('<?php esc_html_e( 'Column names must be unique.', 'a-tables-charts' ); ?>');
            return;
        }

        // Show progress
        $('#create-progress').show();
        $('#create-result').hide();
        button.prop('disabled', true);

        // Submit
        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_create_manual_table',
                nonce: atablesAdmin.nonce,
                title: $('#table-title').val(),
                description: $('#table-description').val(),
                columns: columns,
                initial_rows: parseInt($('#initial-rows').val()) || 0
            },
            success: function(response) {
                $('#create-progress').hide();

                if (response.success) {
                    $('#create-result').html(
                        '<div class="notice notice-success inline"><p><strong>' +
                        response.data.message +
                        '</strong></p></div>'
                    ).show();

                    // Redirect to edit page
                    setTimeout(function() {
                        window.location.href = '<?php echo admin_url( 'admin.php?page=a-tables-charts-edit&id=' ); ?>' + response.data.table_id;
                    }, 1500);
                } else {
                    $('#create-result').html(
                        '<div class="notice notice-error inline"><p><strong>' +
                        response.data.message +
                        '</strong></p></div>'
                    ).show();
                    button.prop('disabled', false);
                }
            },
            error: function() {
                $('#create-progress').hide();
                $('#create-result').html(
                    '<div class="notice notice-error inline"><p><strong><?php esc_html_e( 'An error occurred. Please try again.', 'a-tables-charts' ); ?></strong></p></div>'
                ).show();
                button.prop('disabled', false);
            }
        });
    });
});
</script>

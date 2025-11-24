<?php
/**
 * Table Edit Template
 *
 * @package ATables
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_new = empty( $table );
?>

<div class="wrap">
    <h1><?php echo $is_new ? esc_html__( 'Add New Table', 'a-tables-charts' ) : esc_html__( 'Edit Table', 'a-tables-charts' ); ?></h1>

    <form id="atables-form" style="max-width: 900px;">
        <input type="hidden" name="table_id" value="<?php echo $is_new ? '0' : esc_attr( $table['id'] ); ?>">

        <table class="form-table">
            <tr>
                <th><label for="title"><?php esc_html_e( 'Title', 'a-tables-charts' ); ?> *</label></th>
                <td>
                    <input type="text" id="title" name="title" class="regular-text" value="<?php echo $is_new ? '' : esc_attr( $table['title'] ); ?>" required>
                </td>
            </tr>
            <tr>
                <th><label for="description"><?php esc_html_e( 'Description', 'a-tables-charts' ); ?></label></th>
                <td>
                    <textarea id="description" name="description" class="large-text" rows="3"><?php echo $is_new ? '' : esc_textarea( $table['description'] ); ?></textarea>
                </td>
            </tr>
        </table>

        <h2><?php esc_html_e( 'Table Data', 'a-tables-charts' ); ?></h2>
        <p class="description"><?php esc_html_e( 'Enter your table data below. Click column headers to rename them.', 'a-tables-charts' ); ?></p>

        <div style="margin: 20px 0;">
            <button type="button" id="add-column" class="button"><?php esc_html_e( 'Add Column', 'a-tables-charts' ); ?></button>
            <button type="button" id="add-row" class="button"><?php esc_html_e( 'Add Row', 'a-tables-charts' ); ?></button>
        </div>

        <div id="table-container" style="overflow-x: auto; border: 1px solid #ddd; background: #fff;">
            <table id="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr id="header-row">
                        <?php
                        if ( $is_new ) {
                            // Default 3 columns for new tables
                            for ( $i = 1; $i <= 3; $i++ ) {
                                echo '<th contenteditable="true" style="border: 1px solid #ddd; padding: 10px; background: #f5f5f5;">Column ' . $i . '</th>';
                            }
                        } else {
                            foreach ( $table['columns'] as $column ) {
                                echo '<th contenteditable="true" style="border: 1px solid #ddd; padding: 10px; background: #f5f5f5;">' . esc_html( $column['column_name'] ) . '</th>';
                            }
                        }
                        ?>
                        <th style="width: 50px; border: 1px solid #ddd; padding: 10px; background: #f5f5f5;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ( $is_new || empty( $table['rows'] ) ) {
                        // Default 3 rows for new tables
                        for ( $i = 0; $i < 3; $i++ ) {
                            echo '<tr>';
                            for ( $j = 0; $j < 3; $j++ ) {
                                echo '<td contenteditable="true" style="border: 1px solid #ddd; padding: 10px;"></td>';
                            }
                            echo '<td style="border: 1px solid #ddd; padding: 10px; text-align: center;"><button type="button" class="delete-row button-link" style="color: #b32d2e;">×</button></td></tr>';
                        }
                    } else {
                        foreach ( $table['rows'] as $row ) {
                            echo '<tr>';
                            foreach ( $table['columns'] as $column ) {
                                $value = isset( $row['data'][ $column['column_name'] ] ) ? $row['data'][ $column['column_name'] ] : '';
                                echo '<td contenteditable="true" style="border: 1px solid #ddd; padding: 10px;">' . esc_html( $value ) . '</td>';
                            }
                            echo '<td style="border: 1px solid #ddd; padding: 10px; text-align: center;"><button type="button" class="delete-row button-link" style="color: #b32d2e;">×</button></td></tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Save Table', 'a-tables-charts' ); ?></button>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=atables' ) ); ?>" class="button button-large"><?php esc_html_e( 'Cancel', 'a-tables-charts' ); ?></a>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Add column
    $('#add-column').on('click', function() {
        $('#header-row').find('th:last').before('<th contenteditable="true" style="border: 1px solid #ddd; padding: 10px; background: #f5f5f5;">New Column</th>');
        $('#data-table tbody tr').each(function() {
            $(this).find('td:last').before('<td contenteditable="true" style="border: 1px solid #ddd; padding: 10px;"></td>');
        });
    });

    // Add row
    $('#add-row').on('click', function() {
        const columnCount = $('#header-row th').length - 1;
        let row = '<tr>';
        for (let i = 0; i < columnCount; i++) {
            row += '<td contenteditable="true" style="border: 1px solid #ddd; padding: 10px;"></td>';
        }
        row += '<td style="border: 1px solid #ddd; padding: 10px; text-align: center;"><button type="button" class="delete-row button-link" style="color: #b32d2e;">×</button></td></tr>';
        $('#data-table tbody').append(row);
    });

    // Delete row
    $(document).on('click', '.delete-row', function() {
        $(this).closest('tr').remove();
    });

    // Save form
    $('#atables-form').on('submit', function(e) {
        e.preventDefault();

        // Extract columns
        const columns = [];
        $('#header-row th[contenteditable]').each(function() {
            columns.push($(this).text().trim());
        });

        // Extract rows
        const rows = [];
        $('#data-table tbody tr').each(function() {
            const row = {};
            $(this).find('td[contenteditable]').each(function(index) {
                row[columns[index]] = $(this).text().trim();
            });
            rows.push(row);
        });

        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_save_table',
                nonce: atablesAdmin.nonce,
                table_id: $('[name="table_id"]').val(),
                title: $('#title').val(),
                description: $('#description').val(),
                columns: columns,
                rows: rows
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = '<?php echo admin_url( 'admin.php?page=atables' ); ?>';
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});
</script>

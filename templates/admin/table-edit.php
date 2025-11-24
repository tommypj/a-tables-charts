<?php
/**
 * Admin Edit Table Template
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
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Edit Table', 'a-tables-charts' ); ?></h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts' ) ); ?>" class="page-title-action">
        <?php esc_html_e( 'Back to Tables', 'a-tables-charts' ); ?>
    </a>

    <hr class="wp-header-end">

    <div class="atables-edit-container" style="max-width: 1200px;">
        <!-- Table Info -->
        <div class="card" style="padding: 20px; margin-top: 20px;">
            <h2><?php esc_html_e( 'Table Information', 'a-tables-charts' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Title', 'a-tables-charts' ); ?></th>
                    <td>
                        <input type="text" id="table-title" class="regular-text" value="<?php echo esc_attr( $table['title'] ); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Description', 'a-tables-charts' ); ?></th>
                    <td>
                        <textarea id="table-description" class="large-text" rows="3"><?php echo esc_textarea( $table['description'] ); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Shortcode', 'a-tables-charts' ); ?></th>
                    <td>
                        <input type="text" class="regular-text code" value="[atables id=&quot;<?php echo esc_attr( $table_id ); ?>&quot;]" readonly onclick="this.select();">
                        <p class="description"><?php esc_html_e( 'Copy this shortcode and paste it into any page or post to display the table.', 'a-tables-charts' ); ?></p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="button" class="button button-primary button-large" id="save-table-info">
                    <?php esc_html_e( 'Save Changes', 'a-tables-charts' ); ?>
                </button>
            </p>
        </div>

        <!-- Table Data Preview -->
        <div class="card" style="padding: 20px; margin-top: 20px;">
            <h2><?php esc_html_e( 'Table Data', 'a-tables-charts' ); ?></h2>
            <p class="description">
                <?php
                printf(
                    esc_html__( '%1$d rows × %2$d columns', 'a-tables-charts' ),
                    esc_html( $table['row_count'] ),
                    esc_html( $table['column_count'] )
                );
                ?>
            </p>

            <div style="overflow-x: auto; margin-top: 20px;">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <?php foreach ( $table['columns'] as $column ) : ?>
                            <th><?php echo esc_html( $column['column_name'] ); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rows_to_show = array_slice( $table['rows'], 0, 10 );
                        foreach ( $rows_to_show as $index => $row ) :
                        ?>
                        <tr>
                            <td><?php echo esc_html( $index + 1 ); ?></td>
                            <?php foreach ( $table['columns'] as $column ) : ?>
                            <td><?php echo esc_html( $row['row_data'][ $column['column_name'] ] ?? '' ); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ( $table['row_count'] > 10 ) : ?>
                <p class="description" style="margin-top: 10px;">
                    <?php
                    printf(
                        esc_html__( 'Showing first 10 rows of %d total rows.', 'a-tables-charts' ),
                        esc_html( $table['row_count'] )
                    );
                    ?>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pro Features -->
        <?php if ( ! \ATables\Licensing\LicenseManager::can_use_feature( 'validation' ) ) : ?>
        <div style="margin-top: 20px;">
            <?php \ATables\Licensing\UpgradePrompts::show_prompt( 'validation' ); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#save-table-info').on('click', function() {
        const button = $(this);
        const originalText = button.text();

        button.prop('disabled', true).text('<?php esc_html_e( 'Saving...', 'a-tables-charts' ); ?>');

        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_save_table',
                nonce: atablesAdmin.nonce,
                table_id: <?php echo esc_js( $table_id ); ?>,
                data: {
                    title: $('#table-title').val(),
                    description: $('#table-description').val()
                }
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert(response.data.message);
                }
                button.prop('disabled', false).text(originalText);
            },
            error: function() {
                alert('<?php esc_html_e( 'An error occurred. Please try again.', 'a-tables-charts' ); ?>');
                button.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>

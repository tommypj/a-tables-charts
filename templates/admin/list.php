<?php
/**
 * Tables List Template
 *
 * @package ATables
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Tables', 'a-tables-charts' ); ?></h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=atables-new' ) ); ?>" class="page-title-action">
        <?php esc_html_e( 'Add New', 'a-tables-charts' ); ?>
    </a>
    <hr class="wp-header-end">

    <?php if ( empty( $tables ) ) : ?>
        <div class="atables-empty">
            <p><?php esc_html_e( 'No tables found. Create your first table!', 'a-tables-charts' ); ?></p>
        </div>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Title', 'a-tables-charts' ); ?></th>
                    <th><?php esc_html_e( 'Rows', 'a-tables-charts' ); ?></th>
                    <th><?php esc_html_e( 'Columns', 'a-tables-charts' ); ?></th>
                    <th><?php esc_html_e( 'Shortcode', 'a-tables-charts' ); ?></th>
                    <th><?php esc_html_e( 'Created', 'a-tables-charts' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'a-tables-charts' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $tables as $table ) : ?>
                <tr>
                    <td>
                        <strong>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=atables-edit&id=' . $table['id'] ) ); ?>">
                                <?php echo esc_html( $table['title'] ); ?>
                            </a>
                        </strong>
                    </td>
                    <td><?php echo esc_html( $table['row_count'] ); ?></td>
                    <td><?php echo esc_html( $table['column_count'] ); ?></td>
                    <td><code>[atables id="<?php echo esc_attr( $table['id'] ); ?>"]</code></td>
                    <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $table['created_at'] ) ) ); ?></td>
                    <td>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=atables-edit&id=' . $table['id'] ) ); ?>">
                            <?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
                        </a>
                        |
                        <a href="#" class="atables-delete" data-id="<?php echo esc_attr( $table['id'] ); ?>" style="color: #b32d2e;">
                            <?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    $('.atables-delete').on('click', function(e) {
        e.preventDefault();

        if (!confirm('<?php esc_html_e( 'Are you sure you want to delete this table?', 'a-tables-charts' ); ?>')) {
            return;
        }

        const tableId = $(this).data('id');
        const row = $(this).closest('tr');

        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_delete_table',
                nonce: atablesAdmin.nonce,
                table_id: tableId
            },
            success: function(response) {
                if (response.success) {
                    row.fadeOut(function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});
</script>

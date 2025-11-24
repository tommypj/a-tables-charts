<?php
/**
 * Admin Tables List Template
 *
 * @package ATables
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get tables
$service = new \ATables\Features\Tables\TableService();
$per_page = 20;
$page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
$search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

$result = $service->get_tables( array(
    'per_page' => $per_page,
    'page'     => $page,
    'search'   => $search,
) );

$tables = $result['tables'];
$total = $result['total'];
$total_pages = ceil( $total / $per_page );
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php esc_html_e( 'Tables & Charts', 'a-tables-charts' ); ?>
    </h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-new' ) ); ?>" class="page-title-action">
        <?php esc_html_e( 'Add New', 'a-tables-charts' ); ?>
    </a>

    <?php if ( \ATables\Licensing\LicenseManager::is_pro_version() && ! \ATables\Licensing\LicenseManager::is_pro_active() ) : ?>
    <div class="notice notice-warning" style="margin-top: 20px;">
        <p>
            <strong><?php esc_html_e( 'Pro features are not active.', 'a-tables-charts' ); ?></strong>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-license' ) ); ?>">
                <?php esc_html_e( 'Activate your license', 'a-tables-charts' ); ?>
            </a>
        </p>
    </div>
    <?php endif; ?>

    <hr class="wp-header-end">

    <!-- Search Form -->
    <form method="get" style="margin: 20px 0;">
        <input type="hidden" name="page" value="a-tables-charts">
        <p class="search-box">
            <input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search tables...', 'a-tables-charts' ); ?>">
            <input type="submit" class="button" value="<?php esc_attr_e( 'Search Tables', 'a-tables-charts' ); ?>">
        </p>
    </form>

    <?php if ( empty( $tables ) ) : ?>
        <div class="atables-empty-state" style="text-align: center; padding: 60px 20px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
            <span class="dashicons dashicons-grid-view" style="font-size: 64px; color: #ccc;"></span>
            <h2><?php esc_html_e( 'No tables yet', 'a-tables-charts' ); ?></h2>
            <p><?php esc_html_e( 'Create your first table by uploading an Excel or CSV file.', 'a-tables-charts' ); ?></p>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-new' ) ); ?>" class="button button-primary button-large">
                <?php esc_html_e( 'Create Table', 'a-tables-charts' ); ?>
            </a>
        </div>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 50px;"><?php esc_html_e( 'ID', 'a-tables-charts' ); ?></th>
                    <th><?php esc_html_e( 'Title', 'a-tables-charts' ); ?></th>
                    <th><?php esc_html_e( 'Rows', 'a-tables-charts' ); ?></th>
                    <th><?php esc_html_e( 'Columns', 'a-tables-charts' ); ?></th>
                    <th><?php esc_html_e( 'Created', 'a-tables-charts' ); ?></th>
                    <th style="width: 200px;"><?php esc_html_e( 'Actions', 'a-tables-charts' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $tables as $table ) : ?>
                <tr>
                    <td><?php echo esc_html( $table['id'] ); ?></td>
                    <td>
                        <strong><?php echo esc_html( $table['title'] ); ?></strong>
                        <?php if ( ! empty( $table['description'] ) ) : ?>
                            <br><small class="description"><?php echo esc_html( $table['description'] ); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html( number_format( $table['row_count'] ) ); ?></td>
                    <td><?php echo esc_html( $table['column_count'] ); ?></td>
                    <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $table['created_at'] ) ) ); ?></td>
                    <td>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=a-tables-charts-edit&id=' . $table['id'] ) ); ?>" class="button button-small">
                            <?php esc_html_e( 'Edit', 'a-tables-charts' ); ?>
                        </a>
                        <button class="button button-small atables-delete-table" data-id="<?php echo esc_attr( $table['id'] ); ?>">
                            <?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ( $total_pages > 1 ) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                echo paginate_links( array(
                    'base'      => add_query_arg( 'paged', '%#%' ),
                    'format'    => '',
                    'prev_text' => __( '&laquo; Previous', 'a-tables-charts' ),
                    'next_text' => __( 'Next &raquo;', 'a-tables-charts' ),
                    'total'     => $total_pages,
                    'current'   => $page,
                ) );
                ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    $('.atables-delete-table').on('click', function() {
        if (!confirm('<?php esc_html_e( 'Are you sure you want to delete this table? This action cannot be undone.', 'a-tables-charts' ); ?>')) {
            return;
        }

        const button = $(this);
        const tableId = button.data('id');

        button.prop('disabled', true).text('<?php esc_html_e( 'Deleting...', 'a-tables-charts' ); ?>');

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
                    button.closest('tr').fadeOut(function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.data.message);
                    button.prop('disabled', false).text('<?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>');
                }
            },
            error: function() {
                alert('<?php esc_html_e( 'An error occurred. Please try again.', 'a-tables-charts' ); ?>');
                button.prop('disabled', false).text('<?php esc_html_e( 'Delete', 'a-tables-charts' ); ?>');
            }
        });
    });
});
</script>

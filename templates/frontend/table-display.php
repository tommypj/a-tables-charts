<?php
/**
 * Frontend Table Display Template
 *
 * @package ATables
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$table_class = 'atables-table atables-theme-default';
?>

<div class="atables-wrapper" data-table-id="<?php echo esc_attr( $table['id'] ); ?>" data-paginate="<?php echo esc_attr( $enable_pagination ? 'true' : 'false' ); ?>" data-per-page="<?php echo esc_attr( $rows_per_page ); ?>">

    <?php if ( $enable_search ) : ?>
    <div class="atables-search-box">
        <input type="search" class="atables-search-input" placeholder="<?php esc_attr_e( 'Search table...', 'a-tables-charts' ); ?>">
    </div>
    <?php endif; ?>

    <div class="atables-table-container">
        <table class="<?php echo esc_attr( $table_class ); ?>">
            <thead>
                <tr>
                    <?php foreach ( $table['columns'] as $column ) : ?>
                        <?php if ( $column['is_visible'] ) : ?>
                        <th class="<?php echo $enable_sorting ? 'sortable' : ''; ?>" data-column="<?php echo esc_attr( $column['column_name'] ); ?>">
                            <?php echo esc_html( $column['column_name'] ); ?>
                            <?php if ( $enable_sorting ) : ?>
                            <span class="sort-icon"></span>
                            <?php endif; ?>
                        </th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $table['rows'] as $row ) : ?>
                <tr>
                    <?php foreach ( $table['columns'] as $column ) : ?>
                        <?php if ( $column['is_visible'] ) : ?>
                        <td data-column="<?php echo esc_attr( $column['column_name'] ); ?>">
                            <?php echo esc_html( $row['row_data'][ $column['column_name'] ] ?? '' ); ?>
                        </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ( $enable_pagination ) : ?>
    <div class="atables-pagination">
        <div class="atables-pagination-info">
            <span class="showing-info"></span>
        </div>
        <div class="atables-pagination-controls">
            <button class="atables-prev-page" disabled>&laquo; <?php esc_html_e( 'Previous', 'a-tables-charts' ); ?></button>
            <span class="atables-page-numbers"></span>
            <button class="atables-next-page"><?php esc_html_e( 'Next', 'a-tables-charts' ); ?> &raquo;</button>
        </div>
    </div>
    <?php endif; ?>
</div>

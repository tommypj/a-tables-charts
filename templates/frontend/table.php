<?php
/**
 * Frontend Table Template
 *
 * @package ATables
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php
// Allow modules to add classes
$wrapper_classes = array( 'atables-wrapper' );
$wrapper_classes = apply_filters( 'atables_table_classes', $wrapper_classes, $table['id'] );
?>
<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-table-id="<?php echo esc_attr( $table['id'] ); ?>">
    <?php if ( ! empty( $table['title'] ) ) : ?>
        <h3 class="atables-title"><?php echo esc_html( $table['title'] ); ?></h3>
    <?php endif; ?>

    <?php if ( ! empty( $table['description'] ) ) : ?>
        <p class="atables-description"><?php echo esc_html( $table['description'] ); ?></p>
    <?php endif; ?>

    <table class="atables-table">
        <thead>
            <tr>
                <?php foreach ( $table['columns'] as $column ) : ?>
                    <th><?php echo esc_html( $column['column_name'] ); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $table['rows'] as $row ) : ?>
                <tr>
                    <?php foreach ( $table['columns'] as $column ) : ?>
                        <td>
                            <?php
                            $value = isset( $row['data'][ $column['column_name'] ] ) ? $row['data'][ $column['column_name'] ] : '';
                            echo esc_html( $value );
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

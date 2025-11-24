<?php
/**
 * Settings Template
 *
 * @package ATables
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e( 'Table Features', 'a-tables-charts' ); ?></h1>
    <p class="description"><?php esc_html_e( 'Enable or disable features independently. Each feature can be tested separately.', 'a-tables-charts' ); ?></p>

    <table class="wp-list-table widefat fixed striped" style="max-width: 800px;">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Feature', 'a-tables-charts' ); ?></th>
                <th><?php esc_html_e( 'Tier', 'a-tables-charts' ); ?></th>
                <th style="width: 100px;"><?php esc_html_e( 'Status', 'a-tables-charts' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $features as $key => $feature ) : ?>
            <tr>
                <td><strong><?php echo esc_html( $feature['title'] ); ?></strong></td>
                <td>
                    <?php if ( $feature['tier'] === 'pro' ) : ?>
                        <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">PRO</span>
                    <?php else : ?>
                        <span style="background: #46b450; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">FREE</span>
                    <?php endif; ?>
                </td>
                <td>
                    <label class="atables-toggle">
                        <input type="checkbox"
                               class="feature-toggle"
                               data-feature="<?php echo esc_attr( $key ); ?>"
                               <?php checked( $feature['enabled'] ); ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="save-notice" style="display: none; margin-top: 20px;"></div>
</div>

<style>
.atables-toggle {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.atables-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #2271b1;
}

input:checked + .toggle-slider:before {
    transform: translateX(26px);
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.feature-toggle').on('change', function() {
        const featureKey = $(this).data('feature');
        const enabled = $(this).is(':checked');
        const notice = $('#save-notice');

        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_toggle_feature',
                nonce: atablesAdmin.nonce,
                feature_key: featureKey,
                enabled: enabled ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    notice.html('<div class="notice notice-success inline"><p>' + response.data.message + '</p></div>').show();
                    setTimeout(function() {
                        notice.fadeOut();
                    }, 3000);
                } else {
                    notice.html('<div class="notice notice-error inline"><p>' + response.data.message + '</p></div>').show();
                }
            }
        });
    });
});
</script>

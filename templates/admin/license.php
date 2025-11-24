<?php
/**
 * License Management Page
 *
 * @package ATables
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$license_manager = \ATables\Licensing\LicenseManager::class;
$is_pro = $license_manager::is_pro_version();
$is_active = $license_manager::is_pro_active();

// Get license info
global $wpdb;
$license_table = $wpdb->prefix . 'atables_licenses';
$license = $wpdb->get_row( "SELECT * FROM {$license_table} LIMIT 1", ARRAY_A );

$license_key = $license ? $license['license_key'] : '';
$status = $license ? $license['status'] : '';
$activated_at = $license ? $license['activated_at'] : '';
$expires_at = $license ? $license['expires_at'] : '';
?>

<div class="wrap">
    <h1><?php esc_html_e( 'License Management', 'a-tables-charts' ); ?></h1>

    <?php if ( ! $is_pro ) : ?>
        <!-- Free Version - Upgrade Prompt -->
        <div style="max-width: 800px;">
            <div class="atables-license-free-banner">
                <div class="banner-content">
                    <div class="banner-icon">
                        <span class="dashicons dashicons-star-filled"></span>
                    </div>
                    <div class="banner-text">
                        <h2><?php esc_html_e( 'You\'re using the FREE version', 'a-tables-charts' ); ?></h2>
                        <p><?php esc_html_e( 'Upgrade to PRO to unlock powerful features like validation rules, conditional formatting, charts, advanced filtering, and more!', 'a-tables-charts' ); ?></p>
                    </div>
                </div>
                <div class="banner-actions">
                    <a href="https://your-site.com/pricing" target="_blank" class="button button-primary button-hero">
                        <?php esc_html_e( 'Upgrade to PRO', 'a-tables-charts' ); ?>
                    </a>
                    <a href="https://your-site.com/features" target="_blank" class="button button-hero">
                        <?php esc_html_e( 'Compare Features', 'a-tables-charts' ); ?>
                    </a>
                </div>
            </div>

            <!-- Pro Features List -->
            <div class="card" style="margin-top: 20px; padding: 20px;">
                <h2><?php esc_html_e( 'PRO Features', 'a-tables-charts' ); ?></h2>
                <div class="atables-features-grid">
                    <div class="feature-item">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <div>
                            <strong><?php esc_html_e( 'Data Validation', 'a-tables-charts' ); ?></strong>
                            <p><?php esc_html_e( 'Validate email, URL, numbers, required fields, and more', 'a-tables-charts' ); ?></p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="dashicons dashicons-art"></span>
                        <div>
                            <strong><?php esc_html_e( 'Conditional Formatting', 'a-tables-charts' ); ?></strong>
                            <p><?php esc_html_e( 'Highlight cells based on conditions with custom colors', 'a-tables-charts' ); ?></p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="dashicons dashicons-chart-bar"></span>
                        <div>
                            <strong><?php esc_html_e( 'Charts & Graphs', 'a-tables-charts' ); ?></strong>
                            <p><?php esc_html_e( 'Create beautiful charts from your table data', 'a-tables-charts' ); ?></p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="dashicons dashicons-download"></span>
                        <div>
                            <strong><?php esc_html_e( 'Export Tables', 'a-tables-charts' ); ?></strong>
                            <p><?php esc_html_e( 'Export to Excel, CSV, and PDF formats', 'a-tables-charts' ); ?></p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="dashicons dashicons-filter"></span>
                        <div>
                            <strong><?php esc_html_e( 'Advanced Filtering', 'a-tables-charts' ); ?></strong>
                            <p><?php esc_html_e( 'Multiple filter conditions and saved presets', 'a-tables-charts' ); ?></p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="dashicons dashicons-admin-generic"></span>
                        <div>
                            <strong><?php esc_html_e( 'Formulas & Calculations', 'a-tables-charts' ); ?></strong>
                            <p><?php esc_html_e( 'Create calculated columns with custom formulas', 'a-tables-charts' ); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activation Form for Free Users -->
            <div class="card" style="margin-top: 20px; padding: 20px;">
                <h2><?php esc_html_e( 'Have a License Key?', 'a-tables-charts' ); ?></h2>
                <p><?php esc_html_e( 'If you\'ve purchased a PRO license, activate it here:', 'a-tables-charts' ); ?></p>

                <form id="atables-activate-license-form">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="license-key"><?php esc_html_e( 'License Key', 'a-tables-charts' ); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="text" id="license-key" name="license_key" class="regular-text" placeholder="<?php esc_attr_e( 'Enter your license key', 'a-tables-charts' ); ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="purchase-code"><?php esc_html_e( 'Purchase Code', 'a-tables-charts' ); ?></label>
                            </th>
                            <td>
                                <input type="text" id="purchase-code" name="purchase_code" class="regular-text" placeholder="<?php esc_attr_e( 'Envato purchase code (optional)', 'a-tables-charts' ); ?>">
                                <p class="description"><?php esc_html_e( 'Only required for Envato/CodeCanyon purchases', 'a-tables-charts' ); ?></p>
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <button type="submit" class="button button-primary button-large" id="activate-button">
                            <?php esc_html_e( 'Activate License', 'a-tables-charts' ); ?>
                        </button>
                    </p>

                    <div id="activation-result" style="margin-top: 20px;"></div>
                </form>
            </div>
        </div>

    <?php else : ?>
        <!-- Pro Version - License Status -->
        <div style="max-width: 800px;">
            <?php if ( $is_active ) : ?>
                <!-- Active License -->
                <div class="atables-license-active-banner">
                    <div class="banner-icon">
                        <span class="dashicons dashicons-yes-alt"></span>
                    </div>
                    <div class="banner-content">
                        <h2><?php esc_html_e( 'PRO License Active', 'a-tables-charts' ); ?></h2>
                        <p><?php esc_html_e( 'Your license is active and all PRO features are available!', 'a-tables-charts' ); ?></p>
                    </div>
                </div>

                <!-- License Details -->
                <div class="card" style="margin-top: 20px; padding: 20px;">
                    <h2><?php esc_html_e( 'License Details', 'a-tables-charts' ); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e( 'License Key', 'a-tables-charts' ); ?></th>
                            <td>
                                <code style="background: #f0f0f0; padding: 5px 10px; border-radius: 3px;">
                                    <?php echo esc_html( substr( $license_key, 0, 8 ) . '••••••••' . substr( $license_key, -4 ) ); ?>
                                </code>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Status', 'a-tables-charts' ); ?></th>
                            <td>
                                <span class="atables-status-badge status-active">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                    <?php esc_html_e( 'Active', 'a-tables-charts' ); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Activated On', 'a-tables-charts' ); ?></th>
                            <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $activated_at ) ) ); ?></td>
                        </tr>
                        <?php if ( $expires_at ) : ?>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Expires On', 'a-tables-charts' ); ?></th>
                            <td>
                                <?php
                                $expires_timestamp = strtotime( $expires_at );
                                $days_left = ceil( ( $expires_timestamp - time() ) / DAY_IN_SECONDS );
                                echo esc_html( date_i18n( get_option( 'date_format' ), $expires_timestamp ) );

                                if ( $days_left > 0 ) {
                                    printf(
                                        ' <span class="description">(%s)</span>',
                                        sprintf(
                                            esc_html( _n( '%d day left', '%d days left', $days_left, 'a-tables-charts' ) ),
                                            $days_left
                                        )
                                    );
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>

                    <p class="submit">
                        <button type="button" class="button button-secondary" id="deactivate-license-btn">
                            <?php esc_html_e( 'Deactivate License', 'a-tables-charts' ); ?>
                        </button>
                        <button type="button" class="button" id="refresh-license-btn">
                            <span class="dashicons dashicons-update"></span>
                            <?php esc_html_e( 'Refresh License Status', 'a-tables-charts' ); ?>
                        </button>
                    </p>

                    <div id="license-action-result"></div>
                </div>

            <?php else : ?>
                <!-- Inactive/Expired License -->
                <div class="notice notice-warning" style="padding: 20px;">
                    <h2><?php esc_html_e( 'License Inactive', 'a-tables-charts' ); ?></h2>
                    <p><?php esc_html_e( 'Your license is not active. Please activate or renew your license to access PRO features.', 'a-tables-charts' ); ?></p>
                </div>

                <!-- Reactivation Form -->
                <div class="card" style="margin-top: 20px; padding: 20px;">
                    <h2><?php esc_html_e( 'Activate License', 'a-tables-charts' ); ?></h2>

                    <form id="atables-activate-license-form">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="license-key"><?php esc_html_e( 'License Key', 'a-tables-charts' ); ?> <span class="required">*</span></label>
                                </th>
                                <td>
                                    <input type="text" id="license-key" name="license_key" class="regular-text" value="<?php echo esc_attr( $license_key ); ?>" required>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="purchase-code"><?php esc_html_e( 'Purchase Code', 'a-tables-charts' ); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="purchase-code" name="purchase_code" class="regular-text">
                                    <p class="description"><?php esc_html_e( 'Only required for Envato/CodeCanyon purchases', 'a-tables-charts' ); ?></p>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="submit" class="button button-primary button-large" id="activate-button">
                                <?php esc_html_e( 'Activate License', 'a-tables-charts' ); ?>
                            </button>
                        </p>

                        <div id="activation-result" style="margin-top: 20px;"></div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.required {
    color: #d63638;
}

/* Free Version Banner */
.atables-license-free-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.atables-license-free-banner .banner-content {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 20px;
}

.atables-license-free-banner .banner-icon {
    flex-shrink: 0;
}

.atables-license-free-banner .banner-icon .dashicons {
    font-size: 48px;
    width: 48px;
    height: 48px;
    color: #ffd700;
}

.atables-license-free-banner .banner-text h2 {
    margin: 0 0 10px 0;
    color: #fff;
    font-size: 24px;
}

.atables-license-free-banner .banner-text p {
    margin: 0;
    font-size: 16px;
    opacity: 0.9;
}

.atables-license-free-banner .banner-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* Active License Banner */
.atables-license-active-banner {
    background: #d7ffd9;
    border: 2px solid #46b450;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.atables-license-active-banner .banner-icon {
    flex-shrink: 0;
}

.atables-license-active-banner .banner-icon .dashicons {
    font-size: 48px;
    width: 48px;
    height: 48px;
    color: #46b450;
}

.atables-license-active-banner h2 {
    margin: 0 0 5px 0;
    color: #135e96;
}

.atables-license-active-banner p {
    margin: 0;
    color: #135e96;
}

/* Features Grid */
.atables-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.feature-item {
    display: flex;
    gap: 12px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
    border-left: 3px solid #667eea;
}

.feature-item .dashicons {
    flex-shrink: 0;
    font-size: 24px;
    width: 24px;
    height: 24px;
    color: #667eea;
}

.feature-item strong {
    display: block;
    margin-bottom: 5px;
    color: #135e96;
}

.feature-item p {
    margin: 0;
    font-size: 13px;
    color: #666;
}

/* Status Badge */
.atables-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 4px;
    font-weight: 600;
}

.atables-status-badge.status-active {
    background: #d7ffd9;
    color: #046b15;
}

.atables-status-badge .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

/* Responsive */
@media screen and (max-width: 782px) {
    .atables-license-free-banner .banner-content {
        flex-direction: column;
    }

    .atables-features-grid {
        grid-template-columns: 1fr;
    }

    .atables-license-active-banner {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Activate license
    $('#atables-activate-license-form').on('submit', function(e) {
        e.preventDefault();

        const button = $('#activate-button');
        const originalText = button.text();
        const resultDiv = $('#activation-result');

        button.prop('disabled', true).text('<?php esc_html_e( 'Activating...', 'a-tables-charts' ); ?>');
        resultDiv.empty();

        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_activate_license',
                nonce: atablesAdmin.nonce,
                license_key: $('#license-key').val(),
                purchase_code: $('#purchase-code').val()
            },
            success: function(response) {
                if (response.success) {
                    resultDiv.html('<div class="notice notice-success inline"><p><strong>' + response.data.message + '</strong></p></div>');

                    // Reload page after 2 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    resultDiv.html('<div class="notice notice-error inline"><p><strong>' + response.data.message + '</strong></p></div>');
                    button.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                resultDiv.html('<div class="notice notice-error inline"><p><strong><?php esc_html_e( 'Connection error. Please try again.', 'a-tables-charts' ); ?></strong></p></div>');
                button.prop('disabled', false).text(originalText);
            }
        });
    });

    // Deactivate license
    $('#deactivate-license-btn').on('click', function() {
        if (!confirm('<?php esc_html_e( 'Are you sure you want to deactivate your license? PRO features will be disabled.', 'a-tables-charts' ); ?>')) {
            return;
        }

        const button = $(this);
        const originalText = button.text();
        const resultDiv = $('#license-action-result');

        button.prop('disabled', true).text('<?php esc_html_e( 'Deactivating...', 'a-tables-charts' ); ?>');
        resultDiv.empty();

        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_deactivate_license',
                nonce: atablesAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    resultDiv.html('<div class="notice notice-success inline"><p><strong>' + response.data.message + '</strong></p></div>');

                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    resultDiv.html('<div class="notice notice-error inline"><p><strong>' + response.data.message + '</strong></p></div>');
                    button.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                resultDiv.html('<div class="notice notice-error inline"><p><strong><?php esc_html_e( 'Connection error. Please try again.', 'a-tables-charts' ); ?></strong></p></div>');
                button.prop('disabled', false).text(originalText);
            }
        });
    });

    // Refresh license
    $('#refresh-license-btn').on('click', function() {
        const button = $(this);
        const originalHtml = button.html();
        const resultDiv = $('#license-action-result');

        button.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> <?php esc_html_e( 'Refreshing...', 'a-tables-charts' ); ?>');
        resultDiv.empty();

        $.ajax({
            url: atablesAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'atables_refresh_license',
                nonce: atablesAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    resultDiv.html('<div class="notice notice-success inline"><p><strong>' + response.data.message + '</strong></p></div>');

                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    resultDiv.html('<div class="notice notice-error inline"><p><strong>' + response.data.message + '</strong></p></div>');
                    button.prop('disabled', false).html(originalHtml);
                }
            },
            error: function() {
                resultDiv.html('<div class="notice notice-error inline"><p><strong><?php esc_html_e( 'Connection error. Please try again.', 'a-tables-charts' ); ?></strong></p></div>');
                button.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>

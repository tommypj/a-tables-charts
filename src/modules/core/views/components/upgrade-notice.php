<?php
/**
 * Upgrade Notice Component
 * 
 * Displays upgrade prompts throughout the admin interface.
 *
 * @package ATablesCharts
 * @subpackage Core\Views\Components
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use ATablesCharts\Shared\Utils\Features;

// Get feature name if provided
$feature_name = isset( $feature_name ) ? $feature_name : '';
$message = isset( $message ) ? $message : __( 'Upgrade to PRO to unlock advanced features and take your tables to the next level.', 'a-tables-charts' );
$cta_text = isset( $cta_text ) ? $cta_text : Features::get_upgrade_cta();
$source = isset( $source ) ? $source : 'general';
$style = isset( $style ) ? $style : 'full'; // full, compact, inline
?>

<?php if ( $style === 'full' ) : ?>
<div class="atables-upgrade-notice atables-upgrade-notice-full">
    <div class="atables-upgrade-icon">✨</div>
    <div class="atables-upgrade-content">
        <?php if ( ! empty( $feature_name ) ) : ?>
            <h3><?php echo esc_html( $feature_name ); ?> - <?php _e( 'PRO Feature', 'a-tables-charts' ); ?></h3>
        <?php else : ?>
            <h3><?php _e( 'Upgrade to PRO', 'a-tables-charts' ); ?></h3>
        <?php endif; ?>
        
        <p><?php echo esc_html( $message ); ?></p>
        
        <a href="<?php echo esc_url( Features::get_upgrade_url( $source ) ); ?>" 
           class="button button-primary atables-upgrade-button" 
           target="_blank">
            <?php echo esc_html( $cta_text ); ?>
        </a>
        
        <a href="<?php echo esc_url( Features::get_upgrade_url( $source ) ); ?>" 
           class="atables-learn-more" 
           target="_blank">
            <?php _e( 'Learn more about PRO features', 'a-tables-charts' ); ?> →
        </a>
    </div>
</div>

<?php elseif ( $style === 'compact' ) : ?>
<div class="atables-upgrade-notice atables-upgrade-notice-compact">
    <div class="atables-upgrade-content">
        <span class="atables-pro-badge">PRO</span>
        <span class="atables-upgrade-text"><?php echo esc_html( $message ); ?></span>
        <a href="<?php echo esc_url( Features::get_upgrade_url( $source ) ); ?>" 
           class="button button-small" 
           target="_blank">
            <?php echo esc_html( $cta_text ); ?>
        </a>
    </div>
</div>

<?php elseif ( $style === 'inline' ) : ?>
<span class="atables-upgrade-notice atables-upgrade-notice-inline">
    <span class="atables-pro-badge">PRO</span>
    <a href="<?php echo esc_url( Features::get_upgrade_url( $source ) ); ?>" 
       class="atables-upgrade-link" 
       target="_blank">
        <?php echo esc_html( $cta_text ); ?>
    </a>
</span>

<?php endif; ?>

<style>
/* Full Style Upgrade Notice */
.atables-upgrade-notice-full {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 20px;
    margin: 20px 0;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
}

.atables-upgrade-notice-full .atables-upgrade-icon {
    font-size: 48px;
    line-height: 1;
    flex-shrink: 0;
}

.atables-upgrade-notice-full .atables-upgrade-content {
    flex: 1;
}

.atables-upgrade-notice-full h3 {
    margin: 0 0 10px 0;
    color: white;
    font-size: 20px;
    font-weight: 600;
}

.atables-upgrade-notice-full p {
    margin: 0 0 15px 0;
    opacity: 0.95;
    font-size: 14px;
    line-height: 1.6;
}

.atables-upgrade-notice-full .atables-upgrade-button {
    background: white;
    color: #667eea;
    border: none;
    font-weight: 600;
    padding: 8px 24px;
    height: auto;
    line-height: 1.4;
    text-shadow: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-right: 15px;
}

.atables-upgrade-notice-full .atables-upgrade-button:hover {
    background: #f8f9ff;
    color: #667eea;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.atables-upgrade-notice-full .atables-learn-more {
    color: white;
    text-decoration: none;
    font-size: 13px;
    opacity: 0.9;
    display: inline-block;
}

.atables-upgrade-notice-full .atables-learn-more:hover {
    opacity: 1;
    text-decoration: underline;
}

/* Compact Style Upgrade Notice */
.atables-upgrade-notice-compact {
    background: #f8f9ff;
    border: 2px solid #667eea;
    border-radius: 6px;
    padding: 15px 20px;
    margin: 15px 0;
}

.atables-upgrade-notice-compact .atables-upgrade-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.atables-upgrade-notice-compact .atables-pro-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    flex-shrink: 0;
}

.atables-upgrade-notice-compact .atables-upgrade-text {
    flex: 1;
    font-size: 13px;
    color: #555;
}

.atables-upgrade-notice-compact .button {
    flex-shrink: 0;
}

/* Inline Style Upgrade Notice */
.atables-upgrade-notice-inline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-left: 10px;
}

.atables-upgrade-notice-inline .atables-pro-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.atables-upgrade-notice-inline .atables-upgrade-link {
    font-size: 12px;
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.atables-upgrade-notice-inline .atables-upgrade-link:hover {
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 782px) {
    .atables-upgrade-notice-full {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
    
    .atables-upgrade-notice-full .atables-upgrade-icon {
        font-size: 36px;
    }
    
    .atables-upgrade-notice-compact .atables-upgrade-content {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .atables-upgrade-notice-compact .button {
        width: 100%;
    }
}
</style>

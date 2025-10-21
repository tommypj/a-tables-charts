<?php
/**
 * Upgrade Page
 * 
 * Displays pricing plans and PRO features to encourage upgrades.
 *
 * @package ATablesCharts
 * @subpackage Core\Views
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use ATablesCharts\Shared\Utils\Features;

$pro_features = Features::get_pro_features();
$pricing_plans = Features::get_pricing_plans();
?>

<div class="wrap atables-upgrade-page">
    <!-- Header Section -->
    <div class="atables-upgrade-header">
        <h1>✨ <?php _e( 'Upgrade to A-Tables & Charts PRO', 'a-tables-charts' ); ?></h1>
        <p class="atables-subtitle">
            <?php _e( 'Unlock powerful features and take your tables to the next level', 'a-tables-charts' ); ?>
        </p>
    </div>
    
    <!-- Pricing Cards -->
    <div class="atables-pricing-section">
        <h2><?php _e( 'Choose Your Plan', 'a-tables-charts' ); ?></h2>
        <div class="atables-pricing-cards">
            <?php foreach ( $pricing_plans as $plan_key => $plan ) : ?>
                <div class="atables-pricing-card <?php echo $plan['recommended'] ? 'atables-pricing-featured' : ''; ?>">
                    <?php if ( $plan['recommended'] ) : ?>
                        <div class="atables-popular-badge">
                            <?php _e( 'Most Popular', 'a-tables-charts' ); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="atables-pricing-header">
                        <h3><?php echo esc_html( $plan['name'] ); ?></h3>
                        <div class="atables-pricing-price">
                            <span class="atables-price-amount"><?php echo esc_html( $plan['price'] ); ?></span>
                            <span class="atables-price-period"><?php echo esc_html( $plan['period'] ); ?></span>
                        </div>
                        <p class="atables-pricing-description">
                            <?php echo esc_html( $plan['description'] ); ?>
                        </p>
                    </div>
                    
                    <ul class="atables-pricing-features">
                        <?php foreach ( $plan['features'] as $feature ) : ?>
                            <li>✅ <?php echo esc_html( $feature ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <a href="<?php echo esc_url( $plan['url'] ); ?>" 
                       class="button button-primary button-hero atables-pricing-button" 
                       target="_blank">
                        <?php printf( __( 'Get %s', 'a-tables-charts' ), $plan['name'] ); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Features Grid -->
    <div class="atables-features-section">
        <h2><?php _e( 'What You Get with PRO', 'a-tables-charts' ); ?></h2>
        <p class="atables-subtitle">
            <?php _e( 'Everything in the free version, plus these powerful features:', 'a-tables-charts' ); ?>
        </p>
        
        <div class="atables-features-grid">
            <?php foreach ( $pro_features as $feature_key => $feature ) : ?>
                <div class="atables-feature-card">
                    <div class="atables-feature-icon"><?php echo $feature['icon']; ?></div>
                    <h4><?php echo esc_html( $feature['title'] ); ?></h4>
                    <p><?php echo esc_html( $feature['description'] ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- FAQ Section -->
    <div class="atables-faq-section">
        <h2><?php _e( 'Frequently Asked Questions', 'a-tables-charts' ); ?></h2>
        
        <div class="atables-faq-grid">
            <div class="atables-faq-item">
                <h4><?php _e( 'Can I upgrade later?', 'a-tables-charts' ); ?></h4>
                <p><?php _e( 'Yes! You can upgrade at any time. All your existing tables will work seamlessly with the PRO version.', 'a-tables-charts' ); ?></p>
            </div>
            
            <div class="atables-faq-item">
                <h4><?php _e( 'Do you offer refunds?', 'a-tables-charts' ); ?></h4>
                <p><?php _e( 'Yes, we offer a 30-day money-back guarantee. If you\'re not satisfied, we\'ll refund your purchase.', 'a-tables-charts' ); ?></p>
            </div>
            
            <div class="atables-faq-item">
                <h4><?php _e( 'Will my data be safe?', 'a-tables-charts' ); ?></h4>
                <p><?php _e( 'Absolutely! Your data stays in your WordPress database. We never access or store your data.', 'a-tables-charts' ); ?></p>
            </div>
            
            <div class="atables-faq-item">
                <h4><?php _e( 'What kind of support do you offer?', 'a-tables-charts' ); ?></h4>
                <p><?php _e( 'PRO users get priority email support with response times under 24 hours on business days.', 'a-tables-charts' ); ?></p>
            </div>
        </div>
    </div>
    
    <!-- CTA Section -->
    <div class="atables-cta-section">
        <h2><?php _e( 'Ready to Upgrade?', 'a-tables-charts' ); ?></h2>
        <p><?php _e( 'Join thousands of users who trust A-Tables & Charts PRO', 'a-tables-charts' ); ?></p>
        <a href="<?php echo esc_url( Features::get_upgrade_url( 'upgrade-page-bottom' ) ); ?>" 
           class="button button-primary button-hero" 
           target="_blank">
            <?php _e( 'View Pricing Plans', 'a-tables-charts' ); ?>
        </a>
    </div>
</div>

<style>
/* Upgrade Page Styles */
.atables-upgrade-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header Section */
.atables-upgrade-header {
    text-align: center;
    margin-bottom: 50px;
}

.atables-upgrade-header h1 {
    font-size: 36px;
    font-weight: 700;
    color: #1d1d1f;
    margin-bottom: 10px;
}

.atables-subtitle {
    font-size: 18px;
    color: #666;
    margin-bottom: 0;
}

/* Pricing Section */
.atables-pricing-section {
    margin-bottom: 60px;
}

.atables-pricing-section h2 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 40px;
    color: #1d1d1f;
}

.atables-pricing-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.atables-pricing-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
}

.atables-pricing-card:hover {
    border-color: #667eea;
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
}

.atables-pricing-featured {
    border-color: #667eea;
    border-width: 3px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

.atables-popular-badge {
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 20px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.atables-pricing-header h3 {
    font-size: 24px;
    margin: 10px 0 20px 0;
    color: #1d1d1f;
}

.atables-pricing-price {
    margin-bottom: 15px;
}

.atables-price-amount {
    font-size: 48px;
    font-weight: 700;
    color: #667eea;
    line-height: 1;
}

.atables-price-period {
    font-size: 16px;
    color: #666;
    margin-left: 5px;
}

.atables-pricing-description {
    font-size: 14px;
    color: #666;
    margin-bottom: 25px;
}

.atables-pricing-features {
    list-style: none;
    padding: 0;
    margin: 0 0 30px 0;
    text-align: left;
}

.atables-pricing-features li {
    padding: 10px 0;
    font-size: 14px;
    border-bottom: 1px solid #f3f4f6;
}

.atables-pricing-features li:last-child {
    border-bottom: none;
}

.atables-pricing-button {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    text-shadow: none;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.atables-pricing-button:hover {
    background: linear-gradient(135deg, #5568d3 0%, #653d8b 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

/* Features Section */
.atables-features-section {
    margin-bottom: 60px;
}

.atables-features-section h2 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 10px;
    color: #1d1d1f;
}

.atables-features-section .atables-subtitle {
    text-align: center;
    margin-bottom: 40px;
}

.atables-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.atables-feature-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 25px;
    text-align: center;
    transition: all 0.3s ease;
}

.atables-feature-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

.atables-feature-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.atables-feature-card h4 {
    font-size: 16px;
    margin: 0 0 10px 0;
    color: #1d1d1f;
}

.atables-feature-card p {
    font-size: 13px;
    color: #666;
    margin: 0;
    line-height: 1.6;
}

/* FAQ Section */
.atables-faq-section {
    margin-bottom: 60px;
}

.atables-faq-section h2 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 40px;
    color: #1d1d1f;
}

.atables-faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
}

.atables-faq-item {
    background: #f9fafb;
    border-radius: 8px;
    padding: 25px;
}

.atables-faq-item h4 {
    font-size: 16px;
    color: #1d1d1f;
    margin: 0 0 10px 0;
}

.atables-faq-item p {
    font-size: 14px;
    color: #666;
    margin: 0;
    line-height: 1.6;
}

/* CTA Section */
.atables-cta-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-align: center;
    padding: 60px 40px;
    border-radius: 12px;
    margin-bottom: 40px;
}

.atables-cta-section h2 {
    font-size: 32px;
    color: white;
    margin: 0 0 10px 0;
}

.atables-cta-section p {
    font-size: 18px;
    opacity: 0.95;
    margin: 0 0 30px 0;
}

.atables-cta-section .button {
    background: white;
    color: #667eea;
    border: none;
    text-shadow: none;
    padding: 12px 40px;
    height: auto;
    font-size: 16px;
}

.atables-cta-section .button:hover {
    background: #f8f9ff;
    color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

/* Responsive Design */
@media (max-width: 782px) {
    .atables-upgrade-header h1 {
        font-size: 28px;
    }
    
    .atables-subtitle {
        font-size: 16px;
    }
    
    .atables-pricing-cards {
        grid-template-columns: 1fr;
    }
    
    .atables-features-grid {
        grid-template-columns: 1fr;
    }
    
    .atables-faq-grid {
        grid-template-columns: 1fr;
    }
    
    .atables-cta-section {
        padding: 40px 20px;
    }
    
    .atables-cta-section h2 {
        font-size: 24px;
    }
}
</style>

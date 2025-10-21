/**
 * A-Tables & Charts - Upgrade Modal JavaScript
 *
 * Handles PRO feature clicks and displays upgrade modals.
 *
 * @package ATablesCharts
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Initialize upgrade prompts
     */
    function initUpgradePrompts() {
        // Handle clicks on PRO feature cards
        $(document).on('click', '.atables-pro-feature', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $card = $(this);
            const featureName = $card.find('h3').text() || $card.data('feature-name') || 'This Feature';
            const featureDescription = $card.find('p').text() || $card.data('feature-description') || '';
            
            // Build modal message
            let message = '<div class="atables-upgrade-modal-content">';
            message += '<p style="font-size: 16px; margin-bottom: 15px;">';
            message += '<strong>' + featureName + '</strong> is available in the PRO version.';
            message += '</p>';
            
            if (featureDescription) {
                message += '<p style="color: #666; margin-bottom: 20px;">';
                message += featureDescription;
                message += '</p>';
            }
            
            message += '<p style="font-size: 14px;">Upgrade now to unlock:</p>';
            message += '<ul style="text-align: left; margin: 15px 0; padding-left: 20px;">';
            message += '<li>📊 Excel & XML Import</li>';
            message += '<li>📈 Interactive Charts</li>';
            message += '<li>💾 Excel & PDF Export</li>';
            message += '<li>🎯 And much more!</li>';
            message += '</ul>';
            message += '</div>';
            
            // Show confirmation modal
            const upgrade = await ATablesModal.confirm({
                title: '✨ Upgrade to PRO',
                message: message,
                type: 'info',
                icon: '🚀',
                confirmText: 'View Pricing Plans',
                cancelText: 'Maybe Later',
                confirmClass: 'primary'
            });
            
            if (upgrade) {
                // Track which feature they clicked on
                const source = 'feature-' + featureName.toLowerCase().replace(/\s+/g, '-');
                const upgradeUrl = getUpgradeUrl(source);
                window.open(upgradeUrl, '_blank');
            }
        });
        
        // Handle PRO badge clicks
        $(document).on('click', '.atables-pro-badge', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            await showUpgradeModal('PRO Feature', 'This feature is only available in the PRO version.');
        });
        
        // Handle disabled buttons
        $(document).on('click', '.atables-btn-pro-disabled', async function(e) {
            e.preventDefault();
            
            const action = $(this).data('action') || 'use this feature';
            await showUpgradeModal('Upgrade Required', 'Upgrade to PRO to ' + action + '.');
        });
    }
    
    /**
     * Show generic upgrade modal
     */
    async function showUpgradeModal(title, message) {
        const upgrade = await ATablesModal.confirm({
            title: title,
            message: message + '<br><br>Would you like to see our pricing plans?',
            type: 'info',
            icon: '✨',
            confirmText: 'View Pricing',
            cancelText: 'Not Now',
            confirmClass: 'primary'
        });
        
        if (upgrade) {
            const upgradeUrl = getUpgradeUrl('modal');
            window.open(upgradeUrl, '_blank');
        }
    }
    
    /**
     * Get upgrade URL with source tracking
     */
    function getUpgradeUrl(source) {
        const baseUrl = 'https://a-tables-charts.com/pricing/';
        const url = new URL(baseUrl);
        
        if (source) {
            url.searchParams.set('ref', source);
        }
        
        // Add UTM parameters
        url.searchParams.set('utm_source', 'plugin');
        url.searchParams.set('utm_medium', 'upgrade-modal');
        url.searchParams.set('utm_campaign', 'lite-to-pro');
        
        return url.toString();
    }
    
    /**
     * Add visual effects to PRO features
     */
    function stylePROFeatures() {
        // Add hover effect message
        $('.atables-pro-feature').each(function() {
            const $card = $(this);
            
            // Add tooltip on hover
            $card.on('mouseenter', function() {
                if (!$card.find('.atables-pro-tooltip').length) {
                    const $tooltip = $('<div class="atables-pro-tooltip">Click to learn more</div>');
                    $card.append($tooltip);
                    
                    setTimeout(function() {
                        $tooltip.addClass('visible');
                    }, 10);
                }
            });
            
            $card.on('mouseleave', function() {
                const $tooltip = $card.find('.atables-pro-tooltip');
                $tooltip.removeClass('visible');
                setTimeout(function() {
                    $tooltip.remove();
                }, 300);
            });
        });
    }
    
    /**
     * Show upgrade banner on specific pages
     */
    function showUpgradeBanner() {
        // Check if banner was dismissed
        const bannerDismissed = localStorage.getItem('atables-upgrade-banner-dismissed');
        
        if (bannerDismissed) {
            const dismissedDate = new Date(bannerDismissed);
            const daysSinceDismissed = (new Date() - dismissedDate) / (1000 * 60 * 60 * 24);
            
            // Show again after 7 days
            if (daysSinceDismissed < 7) {
                return;
            }
        }
        
        // Create banner
        const $banner = $('<div class="atables-upgrade-banner">')
            .html(`
                <div class="atables-upgrade-banner-content">
                    <span class="atables-upgrade-banner-icon">✨</span>
                    <span class="atables-upgrade-banner-text">
                        <strong>Upgrade to PRO</strong> to unlock Excel import, charts, PDF export, and more!
                    </span>
                    <a href="${getUpgradeUrl('banner')}" class="atables-upgrade-banner-btn" target="_blank">
                        View Plans
                    </a>
                    <button class="atables-upgrade-banner-dismiss" aria-label="Dismiss">×</button>
                </div>
            `);
        
        // Add to page
        $('.wrap').first().prepend($banner);
        
        // Animate in
        setTimeout(function() {
            $banner.addClass('visible');
        }, 500);
        
        // Handle dismiss
        $banner.find('.atables-upgrade-banner-dismiss').on('click', function() {
            $banner.removeClass('visible');
            setTimeout(function() {
                $banner.remove();
            }, 300);
            
            // Save dismissal
            localStorage.setItem('atables-upgrade-banner-dismissed', new Date().toISOString());
        });
    }
    
    /**
     * Add PRO indicators to menu items
     */
    function addMenuIndicators() {
        // Add sparkle to upgrade menu item
        const $upgradeMenuItem = $('a[href*="atables-upgrade"]');
        if ($upgradeMenuItem.length) {
            $upgradeMenuItem.css({
                'color': '#f18500',
                'font-weight': '600'
            });
        }
    }
    
    /**
     * Track upgrade interest
     */
    function trackUpgradeInterest(action, feature) {
        // Send analytics event if available
        if (typeof gtag !== 'undefined') {
            gtag('event', 'upgrade_interest', {
                'event_category': 'upgrade',
                'event_label': feature,
                'event_action': action
            });
        }
        
        // Could also send to your own analytics endpoint
        if (typeof aTablesAdmin !== 'undefined' && aTablesAdmin.ajaxUrl) {
            $.ajax({
                url: aTablesAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'atables_track_upgrade_interest',
                    nonce: aTablesAdmin.nonce,
                    feature: feature,
                    action_type: action
                }
            });
        }
    }
    
    // Initialize when document is ready
    $(document).ready(function() {
        initUpgradePrompts();
        stylePROFeatures();
        addMenuIndicators();
        
        // Show banner on dashboard and table list pages
        if ($('.atables-dashboard').length || $('.atables-tables-list').length) {
            // Show banner after short delay
            setTimeout(showUpgradeBanner, 2000);
        }
    });
    
    // Add CSS for upgrade elements
    const upgradeStyles = `
        <style id="atables-upgrade-styles">
            /* PRO Feature Cards */
            .atables-pro-feature {
                position: relative;
                opacity: 0.75;
                cursor: pointer !important;
                transition: all 0.3s ease;
            }
            
            .atables-pro-feature:hover {
                opacity: 1;
                transform: translateY(-3px) !important;
                box-shadow: 0 8px 20px rgba(102, 126, 234, 0.25) !important;
            }
            
            .atables-pro-badge {
                position: absolute;
                top: 10px;
                right: 10px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                z-index: 10;
                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
                cursor: pointer;
            }
            
            .atables-pro-tooltip {
                position: absolute;
                bottom: -35px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 0, 0, 0.9);
                color: white;
                padding: 8px 15px;
                border-radius: 6px;
                font-size: 12px;
                white-space: nowrap;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
                z-index: 1000;
            }
            
            .atables-pro-tooltip.visible {
                opacity: 1;
            }
            
            .atables-pro-tooltip::before {
                content: '';
                position: absolute;
                top: -6px;
                left: 50%;
                transform: translateX(-50%);
                border-left: 6px solid transparent;
                border-right: 6px solid transparent;
                border-bottom: 6px solid rgba(0, 0, 0, 0.9);
            }
            
            /* Upgrade Banner */
            .atables-upgrade-banner {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
                opacity: 0;
                transform: translateY(-20px);
                transition: all 0.3s ease;
            }
            
            .atables-upgrade-banner.visible {
                opacity: 1;
                transform: translateY(0);
            }
            
            .atables-upgrade-banner-content {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            
            .atables-upgrade-banner-icon {
                font-size: 24px;
                flex-shrink: 0;
            }
            
            .atables-upgrade-banner-text {
                flex: 1;
                font-size: 14px;
                line-height: 1.5;
            }
            
            .atables-upgrade-banner-text strong {
                font-weight: 600;
            }
            
            .atables-upgrade-banner-btn {
                background: white;
                color: #667eea;
                padding: 8px 20px;
                border-radius: 6px;
                text-decoration: none;
                font-weight: 600;
                font-size: 13px;
                transition: all 0.3s ease;
                flex-shrink: 0;
            }
            
            .atables-upgrade-banner-btn:hover {
                background: #f8f9ff;
                color: #667eea;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }
            
            .atables-upgrade-banner-dismiss {
                background: transparent;
                border: none;
                color: white;
                font-size: 24px;
                line-height: 1;
                cursor: pointer;
                padding: 0;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: background 0.3s ease;
                flex-shrink: 0;
            }
            
            .atables-upgrade-banner-dismiss:hover {
                background: rgba(255, 255, 255, 0.2);
            }
            
            /* Upgrade Modal Content */
            .atables-upgrade-modal-content ul {
                font-size: 14px;
                color: #333;
            }
            
            .atables-upgrade-modal-content li {
                margin-bottom: 8px;
            }
            
            /* Disabled PRO Buttons */
            .atables-btn-pro-disabled {
                opacity: 0.6;
                cursor: pointer !important;
                position: relative;
            }
            
            .atables-btn-pro-disabled::after {
                content: '🔒';
                margin-left: 5px;
            }
            
            /* Responsive Design */
            @media (max-width: 782px) {
                .atables-upgrade-banner-content {
                    flex-wrap: wrap;
                    gap: 10px;
                }
                
                .atables-upgrade-banner-icon {
                    font-size: 20px;
                }
                
                .atables-upgrade-banner-text {
                    font-size: 13px;
                }
                
                .atables-upgrade-banner-btn {
                    width: 100%;
                    text-align: center;
                }
            }
        </style>
    `;
    
    // Inject styles
    if (!$('#atables-upgrade-styles').length) {
        $('head').append(upgradeStyles);
    }

})(jQuery);

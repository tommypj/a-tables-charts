<?php
/**
 * Upgrade Prompts
 *
 * @package ATables\Licensing
 * @since 2.0.0
 */

namespace ATables\Licensing;

/**
 * UpgradePrompts Class
 *
 * Displays upgrade prompts for pro features.
 */
class UpgradePrompts {

    /**
     * Feature descriptions
     *
     * @var array
     */
    private static $features = array(
        'validation' => array(
            'title'       => 'Data Validation',
            'description' => 'Set validation rules to ensure data quality and prevent errors.',
            'benefits'    => array(
                'Required field validation',
                'Email, URL, and format validation',
                'Min/max value ranges',
                'Custom error messages',
                'Unique value enforcement',
            ),
        ),
        'conditional_formatting' => array(
            'title'       => 'Conditional Formatting',
            'description' => 'Automatically highlight cells based on their values.',
            'benefits'    => array(
                'Color-code cells by value',
                'Multiple conditions per column',
                'Custom styles and colors',
                'Priority-based rules',
                'Visual data insights',
            ),
        ),
        'charts' => array(
            'title'       => 'Charts & Graphs',
            'description' => 'Create beautiful, interactive charts from your table data.',
            'benefits'    => array(
                'Bar, line, pie, and more',
                'Interactive tooltips',
                'Multiple chart types',
                'Real-time updates',
                'Responsive design',
            ),
        ),
        'formulas' => array(
            'title'       => 'Formulas & Calculations',
            'description' => 'Add calculated columns with Excel-like formulas.',
            'benefits'    => array(
                'SUM, AVG, MIN, MAX functions',
                'Custom formulas',
                'Cross-column calculations',
                'Auto-updating values',
                'Excel compatibility',
            ),
        ),
        'advanced_filtering' => array(
            'title'       => 'Advanced Filtering',
            'description' => 'Powerful filtering options for complex data queries.',
            'benefits'    => array(
                'Multiple filter conditions',
                'Date range filtering',
                'Number range filters',
                'Text search with operators',
                'Save filter presets',
            ),
        ),
        'export' => array(
            'title'       => 'Export & Import',
            'description' => 'Export your tables to Excel, CSV, or PDF formats.',
            'benefits'    => array(
                'Export to Excel (.xlsx)',
                'Export to CSV',
                'Export to PDF',
                'Batch export multiple tables',
                'Preserve formatting',
            ),
        ),
    );

    /**
     * Show upgrade prompt
     *
     * @param string $feature Feature name.
     */
    public static function show_prompt( $feature ) {
        $feature_data = self::get_feature_data( $feature );

        ?>
        <div class="atables-upgrade-prompt">
            <div class="atables-upgrade-prompt-icon">
                <span class="dashicons dashicons-lock"></span>
            </div>
            <div class="atables-upgrade-prompt-content">
                <h3><?php echo esc_html( $feature_data['title'] ); ?> - Pro Feature</h3>
                <p class="description"><?php echo esc_html( $feature_data['description'] ); ?></p>

                <ul class="atables-upgrade-benefits">
                    <?php foreach ( $feature_data['benefits'] as $benefit ) : ?>
                    <li>
                        <span class="dashicons dashicons-yes"></span>
                        <?php echo esc_html( $benefit ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <div class="atables-upgrade-actions">
                    <a href="<?php echo esc_url( self::get_upgrade_url( $feature ) ); ?>" class="button button-primary button-large" target="_blank">
                        <span class="dashicons dashicons-unlock"></span>
                        <?php esc_html_e( 'Upgrade to Pro', 'a-tables-charts' ); ?>
                    </a>
                    <a href="https://yoursite.com/features/" class="button button-secondary" target="_blank">
                        <?php esc_html_e( 'Learn More', 'a-tables-charts' ); ?>
                    </a>
                </div>
            </div>
        </div>

        <style>
        .atables-upgrade-prompt {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            color: #fff;
            margin: 20px 0;
        }

        .atables-upgrade-prompt-icon {
            flex-shrink: 0;
        }

        .atables-upgrade-prompt-icon .dashicons {
            font-size: 48px;
            width: 48px;
            height: 48px;
            opacity: 0.9;
        }

        .atables-upgrade-prompt-content {
            flex: 1;
        }

        .atables-upgrade-prompt h3 {
            margin: 0 0 10px 0;
            color: #fff;
            font-size: 24px;
        }

        .atables-upgrade-prompt .description {
            margin: 0 0 20px 0;
            font-size: 16px;
            opacity: 0.95;
            color: #fff;
        }

        .atables-upgrade-benefits {
            list-style: none;
            margin: 0 0 25px 0;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .atables-upgrade-benefits li {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .atables-upgrade-benefits .dashicons {
            color: #4ade80;
            flex-shrink: 0;
        }

        .atables-upgrade-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .atables-upgrade-actions .button-primary {
            background: #fff;
            border-color: #fff;
            color: #667eea;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .atables-upgrade-actions .button-primary:hover {
            background: #f0f0f0;
            border-color: #f0f0f0;
            color: #667eea;
        }

        .atables-upgrade-actions .button-secondary {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        .atables-upgrade-actions .button-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.4);
            color: #fff;
        }
        </style>
        <?php
    }

    /**
     * Get feature data
     *
     * @param string $feature Feature name.
     * @return array
     */
    private static function get_feature_data( $feature ) {
        if ( isset( self::$features[ $feature ] ) ) {
            return self::$features[ $feature ];
        }

        // Default feature data
        return array(
            'title'       => 'Pro Feature',
            'description' => 'This feature requires A-Tables & Charts Pro.',
            'benefits'    => array(
                'Access to all pro features',
                'Priority support',
                'Lifetime updates',
            ),
        );
    }

    /**
     * Get upgrade URL
     *
     * @param string $feature Feature name.
     * @return string
     */
    private static function get_upgrade_url( $feature ) {
        $base_url = 'https://yoursite.com/upgrade/';

        return add_query_arg(
            array(
                'feature' => $feature,
                'site'    => home_url(),
            ),
            $base_url
        );
    }

    /**
     * Show inline upgrade notice
     *
     * @param string $feature Feature name.
     */
    public static function show_inline_notice( $feature ) {
        $feature_data = self::get_feature_data( $feature );

        ?>
        <div class="atables-upgrade-notice">
            <span class="dashicons dashicons-lock"></span>
            <span><?php echo esc_html( $feature_data['title'] ); ?> is a Pro feature.</span>
            <a href="<?php echo esc_url( self::get_upgrade_url( $feature ) ); ?>" target="_blank">
                <?php esc_html_e( 'Upgrade Now', 'a-tables-charts' ); ?>
            </a>
        </div>
        <?php
    }
}

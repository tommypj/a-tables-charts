<?php
/**
 * Migration Runner
 * 
 * Handles running database migrations.
 * 
 * @package ATablesCharts\Core
 * @since 1.0.1
 */

namespace ATablesCharts\Core;

class MigrationRunner {
    
    /**
     * Run all pending migrations
     * 
     * @return array Results of all migrations
     */
    public static function run_all() {
        $results = array();
        
        // Load migration classes
        require_once ATABLES_PLUGIN_DIR . 'src/modules/core/migrations/AddDisplaySettingsColumn.php';
        require_once ATABLES_PLUGIN_DIR . 'src/modules/core/migrations/AddFilterPresetsTable.php';
        
        // Run migrations
        $migrations = array(
            'AddDisplaySettingsColumn' => '\ATablesCharts\Core\Migrations\AddDisplaySettingsColumn',
            'AddFilterPresetsTable' => '\ATablesCharts\Core\Migrations\AddFilterPresetsTable',
        );
        
        foreach ($migrations as $name => $class) {
            // Check if migration class has is_migrated method
            if (method_exists($class, 'is_migrated')) {
                if (!$class::is_migrated()) {
                    $result = $class::up();
                    $results[$name] = array(
                        'success' => $result !== false,
                        'message' => $result !== false ? 'Completed' : 'Failed'
                    );
                } else {
                    $results[$name] = array(
                        'success' => true,
                        'message' => 'Already run',
                        'skipped' => true
                    );
                }
            } elseif (method_exists($class, 'has_run')) {
                // Legacy method support
                if (!$class::has_run()) {
                    $result = $class::up();
                    $results[$name] = $result;
                } else {
                    $results[$name] = array(
                        'success' => true,
                        'message' => 'Already run',
                        'skipped' => true
                    );
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Check if any migrations need to run
     * 
     * @return bool
     */
    public static function has_pending() {
        require_once ATABLES_PLUGIN_DIR . 'src/modules/core/migrations/AddDisplaySettingsColumn.php';
        require_once ATABLES_PLUGIN_DIR . 'src/modules/core/migrations/AddFilterPresetsTable.php';
        
        $migrations = array(
            '\ATablesCharts\Core\Migrations\AddDisplaySettingsColumn',
            '\ATablesCharts\Core\Migrations\AddFilterPresetsTable',
        );
        
        foreach ($migrations as $class) {
            if (method_exists($class, 'is_migrated')) {
                if (!$class::is_migrated()) {
                    return true;
                }
            } elseif (method_exists($class, 'has_run')) {
                if (!$class::has_run()) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Display admin notice for pending migrations
     */
    public static function admin_notice() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (self::has_pending()) {
            ?>
            <div class="notice notice-warning">
                <p>
                    <strong>A-Tables & Charts:</strong> Database migration required for new features. 
                    <a href="<?php echo esc_url(admin_url('admin.php?page=a-tables-charts&action=run-migrations')); ?>" class="button button-primary">
                        Run Migration
                    </a>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * Handle migration request
     */
    public static function handle_migration_request() {
        if (!isset($_GET['action']) || $_GET['action'] !== 'run-migrations') {
            return;
        }
        
        if (!isset($_GET['page']) || $_GET['page'] !== 'a-tables-charts') {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('Access denied.');
        }
        
        // Run migrations
        $results = self::run_all();
        
        // Check if all successful
        $all_success = true;
        foreach ($results as $result) {
            if (!$result['success']) {
                $all_success = false;
                break;
            }
        }
        
        // Redirect with message
        $redirect_url = admin_url('admin.php?page=a-tables-charts');
        
        if ($all_success) {
            $redirect_url = add_query_arg('migrations-run', '1', $redirect_url);
        } else {
            $redirect_url = add_query_arg('migrations-failed', '1', $redirect_url);
        }
        
        wp_redirect($redirect_url);
        exit;
    }
    
    /**
     * Display migration messages
     */
    public static function display_messages() {
        if (isset($_GET['migrations-run'])) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>Success!</strong> Database migrations completed successfully.</p>
            </div>
            <?php
        }
        
        if (isset($_GET['migrations-failed'])) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><strong>Error:</strong> Some migrations failed. Please check the error log or contact support.</p>
            </div>
            <?php
        }
    }
}

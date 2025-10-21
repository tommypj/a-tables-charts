<?php
/**
 * Database Updater
 * 
 * Handles database schema updates for the plugin.
 * 
 * @package ATablesCharts\Core
 * @since 1.0.0
 */

namespace ATablesCharts\Core;

/**
 * DatabaseUpdater Class
 */
class DatabaseUpdater {
    
    /**
     * Check if database needs updates
     * 
     * @return bool
     */
    public static function needs_update() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'atables_charts';
        
        // Check if charts table exists
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;
        
        if (!$table_exists) {
            return false;
        }
        
        // Get existing columns
        $columns = $wpdb->get_results($wpdb->prepare("DESCRIBE %s", $table_name));
        $existing_columns = array_map(function($col) { return $col->Field; }, $columns);
        
        // Check for any missing required columns
        $required_columns = ['id', 'table_id', 'title', 'type', 'config', 'status', 'created_by', 'created_at', 'updated_at'];
        $missing_columns = array_diff($required_columns, $existing_columns);
        
        return !empty($missing_columns);
    }
    
    /**
     * Run database updates
     * 
     * @return array Result with success status and message
     */
    public static function update() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'atables_charts';
        $charset_collate = $wpdb->get_charset_collate();
        
        // Check if table exists
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;
        
        if (!$table_exists) {
            // Create complete table
            $sql = "CREATE TABLE $table_name (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                table_id bigint(20) UNSIGNED NOT NULL,
                title varchar(255) NOT NULL,
                type varchar(50) NOT NULL DEFAULT 'bar',
                config longtext NOT NULL,
                status varchar(20) NOT NULL DEFAULT 'active',
                created_by bigint(20) UNSIGNED NOT NULL,
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY table_id (table_id),
                KEY status (status),
                KEY created_by (created_by)
            ) $charset_collate;";
            
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
            
            update_option('atables_db_version', ATABLES_VERSION);
            
            return array(
                'success' => true,
                'message' => 'Charts table created successfully!'
            );
        }
        
        // Table exists - check for missing columns
        $columns = $wpdb->get_results($wpdb->prepare("DESCRIBE %s", $table_name));
        $existing_columns = array_map(function($col) { return $col->Field; }, $columns);
        
        $updates_made = array();
        $errors = array();
        
        // Define all required columns with their SQL
        $required_columns = array(
            'type' => "ALTER TABLE $table_name ADD COLUMN `type` varchar(50) NOT NULL DEFAULT 'bar' AFTER `title`",
            'config' => "ALTER TABLE $table_name ADD COLUMN `config` longtext NOT NULL AFTER `type`",
            'status' => "ALTER TABLE $table_name ADD COLUMN `status` varchar(20) NOT NULL DEFAULT 'active' AFTER `config`",
            'created_by' => "ALTER TABLE $table_name ADD COLUMN `created_by` bigint(20) UNSIGNED NOT NULL AFTER `status`",
            'created_at' => "ALTER TABLE $table_name ADD COLUMN `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`",
            'updated_at' => "ALTER TABLE $table_name ADD COLUMN `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`"
        );
        
        // Add missing columns
        foreach ($required_columns as $column_name => $sql) {
            if (!in_array($column_name, $existing_columns)) {
                $result = $wpdb->query($sql);
                
                if ($result !== false) {
                    $updates_made[] = $column_name;
                } else {
                    $errors[] = "Failed to add column '$column_name': " . $wpdb->last_error;
                }
            }
        }
        
        if (!empty($errors)) {
            return array(
                'success' => false,
                'message' => 'Failed to update database: ' . implode(', ', $errors)
            );
        }
        
        if (empty($updates_made)) {
            return array(
                'success' => true,
                'message' => 'Database is already up to date.'
            );
        }
        
        // Update version
        update_option('atables_db_version', ATABLES_VERSION);
        
        return array(
            'success' => true,
            'message' => 'Database updated successfully! Added columns: ' . implode(', ', $updates_made)
        );
    }
    
    /**
     * Display admin notice for database updates
     */
    public static function admin_notice() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (self::needs_update()) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong>A-Tables & Charts:</strong> Database update required. 
                    <a href="<?php echo esc_url(admin_url('admin.php?page=a-tables-charts&action=update-db')); ?>" class="button button-primary">
                        Update Database
                    </a>
                </p>
            </div>
            <?php
        }
    }
    
    /**
    * Handle database update request
    */
    public static function handle_update_request() {
    if (!isset($_GET['action']) || sanitize_key($_GET['action']) !== 'update-db') {
    return;
    }
    
    if (!isset($_GET['page']) || sanitize_key($_GET['page']) !== 'a-tables-charts') {
    return;
    }
        
        if (!current_user_can('manage_options')) {
            wp_die('Access denied.');
        }
        
        // Run update
        $result = self::update();
        
        // Redirect with message
        $redirect_url = admin_url('admin.php?page=a-tables-charts');
        
        if ($result['success']) {
            $redirect_url = add_query_arg('db-updated', '1', $redirect_url);
        } else {
            $redirect_url = add_query_arg('db-update-failed', '1', $redirect_url);
        }
        
        wp_redirect($redirect_url);
        exit;
    }
    
    /**
    * Display success/error messages
    */
    public static function display_messages() {
    if (isset($_GET['db-updated']) && sanitize_key($_GET['db-updated']) === '1') {
    ?>
    <div class="notice notice-success is-dismissible">
    <p><strong>Success!</strong> Database updated successfully. You can now create charts.</p>
    </div>
    <?php
    }
    
    if (isset($_GET['db-update-failed']) && sanitize_key($_GET['db-update-failed']) === '1') {
    ?>
    <div class="notice notice-error is-dismissible">
    <p><strong>Error:</strong> Failed to update database. Please try the manual fix script or contact support.</p>
    </div>
    <?php
    }
    }
}

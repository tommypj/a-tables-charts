<?php
/**
 * Security Audit Admin Page
 * 
 * Add this temporarily to your main plugin file to run the audit from WordPress admin
 */

// Add to your main plugin file hooks section:
add_action('admin_menu', 'a_tables_security_audit_menu');

function a_tables_security_audit_menu() {
    add_management_page(
        'Security Audit',
        'Security Audit',
        'manage_options',
        'a-tables-security-audit',
        'a_tables_security_audit_page'
    );
}

function a_tables_security_audit_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    echo '<div class="wrap">';
    echo '<h1>A-Tables & Charts Security Audit</h1>';
    
    if (isset($_POST['run_audit'])) {
        check_admin_referer('run_security_audit');
        
        echo '<div style="background: #000; color: #0f0; padding: 20px; font-family: monospace; white-space: pre-wrap; overflow: auto; max-height: 600px;">';
        
        ob_start();
        include dirname(plugin_dir_path(__FILE__)) . '/tools/security-audit.php';
        $output = ob_get_clean();
        
        echo esc_html($output);
        echo '</div>';
        
        $report_file = dirname(plugin_dir_path(__FILE__)) . '/security-audit-report.txt';
        if (file_exists($report_file)) {
            echo '<p><a href="' . esc_url(plugins_url('security-audit-report.txt', dirname(__FILE__))) . '" class="button">Download Full Report</a></p>';
        }
    } else {
        echo '<p>Click the button below to run a comprehensive security audit of your plugin.</p>';
        echo '<form method="post">';
        wp_nonce_field('run_security_audit');
        echo '<p><button type="submit" name="run_audit" class="button button-primary">Run Security Audit</button></p>';
        echo '</form>';
    }
    
    echo '</div>';
}

<?php
/**
 * Security Audit Script
 * 
 * Run from command line: php tools/security-audit.php
 * Or from WordPress root: php wp-content/plugins/a-tables-charts/tools/security-audit.php
 */

class SecurityAudit {
    private $issues = [];
    private $plugin_dir;
    private $stats = [
        'files_scanned' => 0,
        'sql_injection' => 0,
        'xss_vulnerabilities' => 0,
        'unsanitized_input' => 0,
        'missing_nonces' => 0
    ];
    
    public function __construct($plugin_dir) {
        $this->plugin_dir = rtrim($plugin_dir, '/\\');
    }
    
    public function run() {
        echo "🔍 A-Tables & Charts Security Audit\n";
        echo str_repeat('=', 70) . "\n\n";
        echo "Scanning directory: {$this->plugin_dir}\n\n";
        
        $this->check_sql_injection();
        $this->check_xss_vulnerabilities();
        $this->check_input_sanitization();
        $this->check_csrf_protection();
        
        $this->generate_report();
    }
    
    private function check_sql_injection() {
        echo "📊 Checking for SQL Injection vulnerabilities...\n";
        
        $files = $this->get_php_files();
        
        foreach ($files as $file) {
            $this->stats['files_scanned']++;
            $content = file_get_contents($file);
            $lines = explode("\n", $content);
            
            foreach ($lines as $num => $line) {
                // Check for wpdb queries without prepare
                if (preg_match('/\$wpdb->(query|get_results|get_row|get_col|get_var)\s*\(/', $line)) {
                    // Check if there's a prepare on the same line or nearby
                    $context = implode("\n", array_slice($lines, max(0, $num - 2), 5));
                    if (!preg_match('/->prepare\s*\(/', $context)) {
                        $this->add_issue('SQL Injection Risk', $file, $num + 1, trim($line));
                        $this->stats['sql_injection']++;
                    }
                }
            }
        }
        
        echo "  Found {$this->stats['sql_injection']} potential SQL injection issues\n\n";
    }
    
    private function check_xss_vulnerabilities() {
        echo "🔒 Checking for XSS vulnerabilities...\n";
        
        $files = $this->get_php_files();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $lines = explode("\n", $content);
            
            foreach ($lines as $num => $line) {
                // Check for unescaped echo
                if (preg_match('/\becho\s+\$[a-zA-Z_]/', $line)) {
                    if (!preg_match('/esc_(html|attr|url|js|textarea)/', $line)) {
                        $this->add_issue('Unescaped Output (XSS Risk)', $file, $num + 1, trim($line));
                        $this->stats['xss_vulnerabilities']++;
                    }
                }
                
                // Check for <?= shorthand
                if (preg_match('/<\?=\s*\$/', $line)) {
                    if (!preg_match('/esc_(html|attr|url)/', $line)) {
                        $this->add_issue('Unescaped Shorthand Echo (XSS Risk)', $file, $num + 1, trim($line));
                        $this->stats['xss_vulnerabilities']++;
                    }
                }
            }
        }
        
        echo "  Found {$this->stats['xss_vulnerabilities']} potential XSS issues\n\n";
    }
    
    private function check_input_sanitization() {
        echo "🧹 Checking for missing input sanitization...\n";
        
        $files = $this->get_php_files();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $lines = explode("\n", $content);
            
            foreach ($lines as $num => $line) {
                // Check for unsanitized $_POST
                if (preg_match('/\$_POST\s*\[/', $line)) {
                    $context = implode(' ', array_slice($lines, max(0, $num - 1), 3));
                    if (!preg_match('/sanitize_|absint|intval|floatval|wp_unslash/', $context)) {
                        $this->add_issue('Unsanitized POST Input', $file, $num + 1, trim($line));
                        $this->stats['unsanitized_input']++;
                    }
                }
                
                // Check for unsanitized $_GET
                if (preg_match('/\$_GET\s*\[/', $line)) {
                    $context = implode(' ', array_slice($lines, max(0, $num - 1), 3));
                    if (!preg_match('/sanitize_|absint|intval/', $context)) {
                        $this->add_issue('Unsanitized GET Input', $file, $num + 1, trim($line));
                        $this->stats['unsanitized_input']++;
                    }
                }
            }
        }
        
        echo "  Found {$this->stats['unsanitized_input']} unsanitized inputs\n\n";
    }
    
    private function check_csrf_protection() {
        echo "🛡️  Checking for CSRF protection...\n";
        
        $files = $this->get_php_files();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Check AJAX handlers
            if (preg_match_all('/add_action\s*\(\s*[\'"]wp_ajax_([^\'"]+)/', $content, $matches)) {
                foreach ($matches[1] as $action) {
                    // Look for nonce check in the file
                    if (!preg_match('/check_ajax_referer|wp_verify_nonce/', $content)) {
                        $this->add_issue('Missing AJAX Nonce Check', $file, 0, "Action: wp_ajax_{$action}");
                        $this->stats['missing_nonces']++;
                    }
                }
            }
        }
        
        echo "  Found {$this->stats['missing_nonces']} handlers without nonce checks\n\n";
    }
    
    private function get_php_files() {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->plugin_dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        $files = [];
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $path = $file->getPathname();
                // Skip vendor, node_modules, and tools
                if (strpos($path, 'vendor') === false &&
                    strpos($path, 'node_modules') === false &&
                    strpos($path, DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR) === false) {
                    $files[] = $path;
                }
            }
        }
        
        return $files;
    }
    
    private function add_issue($type, $file, $line, $code) {
        $this->issues[] = [
            'type' => $type,
            'file' => str_replace($this->plugin_dir . DIRECTORY_SEPARATOR, '', $file),
            'line' => $line,
            'code' => substr($code, 0, 100) // Limit code length
        ];
    }
    
    private function generate_report() {
        echo "\n" . str_repeat('=', 70) . "\n";
        echo "📋 AUDIT REPORT SUMMARY\n";
        echo str_repeat('=', 70) . "\n\n";
        
        echo "Files Scanned: {$this->stats['files_scanned']}\n\n";
        
        echo "Issues Found:\n";
        echo "  • SQL Injection Risks: {$this->stats['sql_injection']}\n";
        echo "  • XSS Vulnerabilities: {$this->stats['xss_vulnerabilities']}\n";
        echo "  • Unsanitized Inputs: {$this->stats['unsanitized_input']}\n";
        echo "  • Missing Nonces: {$this->stats['missing_nonces']}\n";
        $total = array_sum(array_slice($this->stats, 1));
        echo "  • TOTAL: {$total}\n\n";
        
        if (empty($this->issues)) {
            echo "✅ No security issues found!\n";
            return;
        }
        
        echo str_repeat('-', 70) . "\n\n";
        
        // Group by type
        $grouped = [];
        foreach ($this->issues as $issue) {
            $grouped[$issue['type']][] = $issue;
        }
        
        foreach ($grouped as $type => $issues) {
            echo "🔴 {$type} (" . count($issues) . " issues)\n";
            echo str_repeat('-', 70) . "\n";
            
            // Show first 10 of each type
            foreach (array_slice($issues, 0, 10) as $issue) {
                echo "  📁 File: {$issue['file']}\n";
                if ($issue['line'] > 0) {
                    echo "  📍 Line: {$issue['line']}\n";
                }
                echo "  💻 Code: {$issue['code']}\n";
                echo "\n";
            }
            
            if (count($issues) > 10) {
                echo "  ... and " . (count($issues) - 10) . " more\n";
            }
            echo "\n";
        }
        
        // Save detailed report
        $report_file = $this->plugin_dir . DIRECTORY_SEPARATOR . 'security-audit-report.txt';
        $report_content = "Security Audit Report\n";
        $report_content .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $report_content .= "Statistics:\n";
        foreach ($this->stats as $key => $value) {
            $report_content .= "  {$key}: {$value}\n";
        }
        $report_content .= "\nDetailed Issues:\n\n";
        $report_content .= print_r($this->issues, true);
        
        file_put_contents($report_file, $report_content);
        echo "📄 Detailed report saved to: security-audit-report.txt\n";
    }
}

// Determine plugin directory
if (php_sapi_name() === 'cli') {
    // Running from command line
    $plugin_dir = dirname(__DIR__);
} else {
    // Running from WordPress
    $plugin_dir = dirname(plugin_dir_path(__FILE__));
}

$audit = new SecurityAudit($plugin_dir);
$audit->run();

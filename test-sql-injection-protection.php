<?php
/**
 * SQL Injection Protection Test
 *
 * This test demonstrates that the MySQL Query feature blocks dangerous SQL commands
 * including DROP TABLE, DELETE, INSERT, UPDATE, and SQL injection attempts.
 *
 * Run this test: php test-sql-injection-protection.php
 */

// Load WordPress test environment
require_once __DIR__ . '/src/modules/database/MySQLQueryService.php';

use ATablesCharts\Database\MySQLQueryService;

// Mock WordPress functions for testing
if (!function_exists('__')) {
    function __($text) { return $text; }
}
if (!function_exists('esc_html')) {
    function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('get_current_user_id')) {
    function get_current_user_id() { return 1; }
}
if (!function_exists('apply_filters')) {
    function apply_filters($hook, $value) { return $value; }
}

// Test class
class SQLInjectionProtectionTest {

    private $service;
    private $passed = 0;
    private $failed = 0;

    public function __construct() {
        $this->service = new MySQLQueryService();
    }

    /**
     * Run all tests
     */
    public function run_all_tests() {
        echo "\n===========================================\n";
        echo "SQL INJECTION PROTECTION TEST\n";
        echo "===========================================\n\n";

        echo "Testing dangerous SQL commands...\n\n";

        // Test 1: DROP TABLE
        $this->test_blocked_query(
            "DROP TABLE wp_users",
            "DROP TABLE attack"
        );

        // Test 2: DELETE
        $this->test_blocked_query(
            "DELETE FROM wp_users WHERE id = 1",
            "DELETE attack"
        );

        // Test 3: INSERT
        $this->test_blocked_query(
            "INSERT INTO wp_users (user_login) VALUES ('hacker')",
            "INSERT attack"
        );

        // Test 4: UPDATE
        $this->test_blocked_query(
            "UPDATE wp_users SET user_pass = 'hacked' WHERE id = 1",
            "UPDATE attack"
        );

        // Test 5: TRUNCATE
        $this->test_blocked_query(
            "TRUNCATE TABLE wp_users",
            "TRUNCATE attack"
        );

        // Test 6: CREATE TABLE
        $this->test_blocked_query(
            "CREATE TABLE evil_table (id INT)",
            "CREATE TABLE attack"
        );

        // Test 7: ALTER TABLE
        $this->test_blocked_query(
            "ALTER TABLE wp_users ADD COLUMN evil VARCHAR(255)",
            "ALTER TABLE attack"
        );

        // Test 8: UNION-based SQL injection
        $this->test_blocked_query(
            "SELECT * FROM wp_posts UNION SELECT * FROM wp_users",
            "UNION SELECT injection"
        );

        // Test 9: Stacked queries
        $this->test_blocked_query(
            "SELECT * FROM wp_posts; DROP TABLE wp_users;",
            "Stacked queries attack"
        );

        // Test 10: LOAD_FILE attack
        $this->test_blocked_query(
            "SELECT LOAD_FILE('/etc/passwd')",
            "LOAD_FILE attack"
        );

        // Test 11: INTO OUTFILE attack
        $this->test_blocked_query(
            "SELECT * FROM wp_users INTO OUTFILE '/tmp/dump.txt'",
            "INTO OUTFILE attack"
        );

        // Test 12: BENCHMARK attack (DoS)
        $this->test_blocked_query(
            "SELECT BENCHMARK(1000000, MD5('test'))",
            "BENCHMARK DoS attack"
        );

        // Test 13: Comment-based bypass attempt
        $this->test_blocked_query(
            "SELECT * FROM wp_posts; /* comment */ DROP TABLE wp_users;",
            "Comment-based bypass"
        );

        // Test 14: Hex encoding bypass attempt
        $this->test_blocked_query(
            "SELECT * FROM wp_users WHERE user_login = 0x61646d696e",
            "Hex encoding bypass"
        );

        // Test 15: GRANT privilege escalation
        $this->test_blocked_query(
            "GRANT ALL PRIVILEGES ON *.* TO 'hacker'@'localhost'",
            "GRANT privilege escalation"
        );

        echo "\n-------------------------------------------\n";
        echo "Testing legitimate SELECT queries...\n\n";

        // Test 16: Valid SELECT query
        $this->test_allowed_query(
            "SELECT * FROM wp_posts WHERE post_status = 'publish'",
            "Valid SELECT query"
        );

        // Test 17: SELECT with JOIN
        $this->test_allowed_query(
            "SELECT p.*, pm.meta_value FROM wp_posts p LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id",
            "SELECT with JOIN"
        );

        // Test 18: SELECT with LIMIT
        $this->test_allowed_query(
            "SELECT * FROM wp_posts LIMIT 10",
            "SELECT with LIMIT"
        );

        // Print results
        echo "\n===========================================\n";
        echo "TEST RESULTS\n";
        echo "===========================================\n";
        echo "✓ PASSED: {$this->passed}\n";
        echo "✗ FAILED: {$this->failed}\n";

        if ($this->failed === 0) {
            echo "\n🎉 ALL TESTS PASSED! SQL Injection protection is working correctly.\n";
        } else {
            echo "\n⚠️  SOME TESTS FAILED! Security vulnerabilities detected.\n";
        }

        echo "===========================================\n\n";

        return $this->failed === 0;
    }

    /**
     * Test that a query is blocked
     */
    private function test_blocked_query($query, $test_name) {
        $result = $this->service->validate_query($query);

        if (!$result['valid']) {
            echo "✓ BLOCKED: {$test_name}\n";
            echo "  Query: " . substr($query, 0, 60) . (strlen($query) > 60 ? '...' : '') . "\n";
            echo "  Reason: {$result['errors'][0]}\n\n";
            $this->passed++;
        } else {
            echo "✗ ALLOWED: {$test_name} (SECURITY VULNERABILITY!)\n";
            echo "  Query: {$query}\n\n";
            $this->failed++;
        }
    }

    /**
     * Test that a query is allowed
     */
    private function test_allowed_query($query, $test_name) {
        $result = $this->service->validate_query($query);

        if ($result['valid']) {
            echo "✓ ALLOWED: {$test_name}\n";
            echo "  Query: " . substr($query, 0, 60) . (strlen($query) > 60 ? '...' : '') . "\n\n";
            $this->passed++;
        } else {
            echo "✗ BLOCKED: {$test_name} (False positive!)\n";
            echo "  Query: {$query}\n";
            echo "  Reason: {$result['errors'][0]}\n\n";
            $this->failed++;
        }
    }
}

// Run tests
$test = new SQLInjectionProtectionTest();
$success = $test->run_all_tests();

// Exit with appropriate code
exit($success ? 0 : 1);

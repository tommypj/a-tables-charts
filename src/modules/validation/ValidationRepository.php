<?php
/**
 * Validation Repository
 *
 * Handles database operations for validation rules using the new separate table.
 *
 * @package ATablesCharts\Validation\Repositories
 * @since 1.1.0
 */

namespace ATablesCharts\Validation\Repositories;

/**
 * ValidationRepository Class
 */
class ValidationRepository {

    /**
     * WordPress database object
     *
     * @var \wpdb
     */
    private $wpdb;

    /**
     * Table name
     *
     * @var string
     */
    private $table_name;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'atables_validation_rules';
    }

    /**
     * Get all validation rules for a table
     *
     * @param int $table_id Table ID.
     * @return array Validation rules grouped by column
     */
    public function get_rules( $table_id ) {
        $results = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE table_id = %d ORDER BY column_name, id",
                $table_id
            ),
            ARRAY_A
        );

        if ( empty( $results ) ) {
            return array();
        }

        // Group rules by column
        $grouped = array();
        foreach ( $results as $row ) {
            $column = $row['column_name'];
            if ( ! isset( $grouped[ $column ] ) ) {
                $grouped[ $column ] = array();
            }

            $rule_config = json_decode( $row['rule_config'], true );
            if ( ! is_array( $rule_config ) ) {
                $rule_config = array();
            }

            // Merge stored config with row data
            $rule = array_merge( $rule_config, array(
                'id' => $row['id'],
                'type' => $row['rule_type'],
                'message' => $row['error_message'],
            ) );

            $grouped[ $column ][] = $rule;
        }

        return $grouped;
    }

    /**
     * Save validation rules for a table
     *
     * Replaces all existing rules for the table.
     *
     * @param int   $table_id Table ID.
     * @param array $rules    Validation rules grouped by column.
     * @return bool True on success
     */
    public function save_rules( $table_id, $rules ) {
        // Delete existing rules for this table
        $this->wpdb->delete(
            $this->table_name,
            array( 'table_id' => $table_id ),
            array( '%d' )
        );

        if ( empty( $rules ) ) {
            return true;
        }

        // Insert new rules
        foreach ( $rules as $column => $column_rules ) {
            if ( ! is_array( $column_rules ) ) {
                continue;
            }

            foreach ( $column_rules as $rule ) {
                $rule_type = isset( $rule['type'] ) ? $rule['type'] : 'custom';
                $error_message = isset( $rule['message'] ) ? $rule['message'] : '';

                // Remove fields that are stored in separate columns
                $rule_config = $rule;
                unset( $rule_config['id'], $rule_config['type'], $rule_config['message'] );

                $result = $this->wpdb->insert(
                    $this->table_name,
                    array(
                        'table_id' => $table_id,
                        'column_name' => $column,
                        'rule_type' => $rule_type,
                        'rule_config' => wp_json_encode( $rule_config ),
                        'error_message' => $error_message,
                    ),
                    array( '%d', '%s', '%s', '%s', '%s' )
                );

                if ( false === $result ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Add a single validation rule
     *
     * @param int    $table_id    Table ID.
     * @param string $column      Column name.
     * @param array  $rule        Rule configuration.
     * @return int|false Rule ID on success, false on failure
     */
    public function add_rule( $table_id, $column, $rule ) {
        $rule_type = isset( $rule['type'] ) ? $rule['type'] : 'custom';
        $error_message = isset( $rule['message'] ) ? $rule['message'] : '';

        // Remove fields that are stored in separate columns
        $rule_config = $rule;
        unset( $rule_config['id'], $rule_config['type'], $rule_config['message'] );

        $result = $this->wpdb->insert(
            $this->table_name,
            array(
                'table_id' => $table_id,
                'column_name' => $column,
                'rule_type' => $rule_type,
                'rule_config' => wp_json_encode( $rule_config ),
                'error_message' => $error_message,
            ),
            array( '%d', '%s', '%s', '%s', '%s' )
        );

        if ( false === $result ) {
            return false;
        }

        return $this->wpdb->insert_id;
    }

    /**
     * Update a validation rule
     *
     * @param int   $rule_id Rule ID.
     * @param array $rule    Rule configuration.
     * @return bool True on success
     */
    public function update_rule( $rule_id, $rule ) {
        $rule_type = isset( $rule['type'] ) ? $rule['type'] : 'custom';
        $error_message = isset( $rule['message'] ) ? $rule['message'] : '';

        // Remove fields that are stored in separate columns
        $rule_config = $rule;
        unset( $rule_config['id'], $rule_config['type'], $rule_config['message'] );

        $result = $this->wpdb->update(
            $this->table_name,
            array(
                'rule_type' => $rule_type,
                'rule_config' => wp_json_encode( $rule_config ),
                'error_message' => $error_message,
            ),
            array( 'id' => $rule_id ),
            array( '%s', '%s', '%s' ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Delete a validation rule
     *
     * @param int $rule_id Rule ID.
     * @return bool True on success
     */
    public function delete_rule( $rule_id ) {
        $result = $this->wpdb->delete(
            $this->table_name,
            array( 'id' => $rule_id ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Delete all rules for a column
     *
     * @param int    $table_id Table ID.
     * @param string $column   Column name.
     * @return bool True on success
     */
    public function delete_column_rules( $table_id, $column ) {
        $result = $this->wpdb->delete(
            $this->table_name,
            array(
                'table_id' => $table_id,
                'column_name' => $column,
            ),
            array( '%d', '%s' )
        );

        return false !== $result;
    }

    /**
     * Delete all rules for a table
     *
     * @param int $table_id Table ID.
     * @return bool True on success
     */
    public function delete_table_rules( $table_id ) {
        $result = $this->wpdb->delete(
            $this->table_name,
            array( 'table_id' => $table_id ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Check if validation rules table exists
     *
     * @return bool
     */
    public function table_exists() {
        $result = $this->wpdb->get_var(
            $this->wpdb->prepare( "SHOW TABLES LIKE %s", $this->table_name )
        );

        return $result === $this->table_name;
    }

    /**
     * Get rules count for a table
     *
     * @param int $table_id Table ID.
     * @return int Number of rules
     */
    public function count_rules( $table_id ) {
        return (int) $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} WHERE table_id = %d",
                $table_id
            )
        );
    }
}

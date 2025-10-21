<?php
/**
 * Database Helper Functions
 * 
 * @package A_Tables_Charts
 * @since 1.0.5
 */

namespace A_Tables_Charts\Database;

if (!defined('ABSPATH')) {
    exit;
}

class DatabaseHelpers {
    
    /**
     * Prepare safe ORDER BY clause
     *
     * @param string $column Column name from user input
     * @param array $allowed_columns Whitelist of allowed columns
     * @param string $default Default column if not in whitelist
     * @return string Safe column name
     */
    public static function prepare_order_by($column, $allowed_columns, $default = 'id') {
        $column = sanitize_key($column);
        
        if (!in_array($column, $allowed_columns, true)) {
            $column = $default;
        }
        
        return $column;
    }
    
    /**
     * Prepare safe ORDER direction
     *
     * @param string $direction Direction from user input
     * @param string $default Default direction
     * @return string 'ASC' or 'DESC'
     */
    public static function prepare_order_direction($direction, $default = 'ASC') {
        $direction = strtoupper($direction);
        return in_array($direction, ['ASC', 'DESC'], true) ? $direction : $default;
    }
    
    /**
     * Prepare LIKE query
     *
     * @param string $search Search term
     * @param string $position Where to place wildcards
     * @return string Escaped search term with wildcards
     */
    public static function prepare_like($search, $position = 'both') {
        global $wpdb;
        
        $search = $wpdb->esc_like($search);
        
        switch ($position) {
            case 'start':
                return $search . '%';
            case 'end':
                return '%' . $search;
            case 'both':
            default:
                return '%' . $search . '%';
        }
    }
    
    /**
     * Build safe WHERE IN clause
     *
     * @param array $values Values for IN clause
     * @param string $format Format specifier (%d for int, %s for string)
     * @return string Prepared placeholders
     */
    public static function prepare_in_clause($values, $format = '%d') {
        global $wpdb;
        
        if (empty($values) || !is_array($values)) {
            return '';
        }
        
        $placeholders = array_fill(0, count($values), $format);
        $format_string = implode(', ', $placeholders);
        
        return $wpdb->prepare($format_string, $values);
    }
}

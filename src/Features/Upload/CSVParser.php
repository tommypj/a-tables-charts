<?php
/**
 * CSV Parser
 *
 * @package ATables\Features\Upload
 * @since 2.0.0
 */

namespace ATables\Features\Upload;

/**
 * CSVParser Class
 *
 * Parses CSV files into table data.
 */
class CSVParser {

    /**
     * Parse CSV file
     *
     * @param string $file_path Path to CSV file.
     * @return array Array with 'headers' and 'rows'.
     * @throws \Exception If file cannot be parsed.
     */
    public function parse( $file_path ) {
        if ( ! file_exists( $file_path ) ) {
            throw new \Exception( __( 'File not found.', 'a-tables-charts' ) );
        }

        $handle = fopen( $file_path, 'r' );

        if ( false === $handle ) {
            throw new \Exception( __( 'Could not open CSV file.', 'a-tables-charts' ) );
        }

        $data = array();

        while ( ( $row = fgetcsv( $handle ) ) !== false ) {
            $data[] = $row;
        }

        fclose( $handle );

        if ( empty( $data ) ) {
            throw new \Exception( __( 'CSV file is empty.', 'a-tables-charts' ) );
        }

        // First row is headers
        $headers = array_shift( $data );
        $headers = array_map( 'trim', $headers );

        // Remove empty rows
        $rows = array_filter( $data, function( $row ) {
            return ! empty( array_filter( $row ) );
        } );

        // Convert rows to associative arrays
        $formatted_rows = array();
        foreach ( $rows as $row ) {
            $formatted_row = array();
            foreach ( $headers as $index => $header ) {
                $formatted_row[ $header ] = isset( $row[ $index ] ) ? trim( $row[ $index ] ) : '';
            }
            $formatted_rows[] = $formatted_row;
        }

        return array(
            'headers' => $headers,
            'rows'    => $formatted_rows,
        );
    }
}

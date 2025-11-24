<?php
/**
 * Excel Parser
 *
 * @package ATables\Features\Upload
 * @since 2.0.0
 */

namespace ATables\Features\Upload;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * ExcelParser Class
 *
 * Parses Excel files (.xlsx, .xls) into table data.
 */
class ExcelParser {

    /**
     * Parse Excel file
     *
     * @param string $file_path Path to Excel file.
     * @return array Array with 'headers' and 'rows'.
     * @throws \Exception If file cannot be parsed.
     */
    public function parse( $file_path ) {
        if ( ! file_exists( $file_path ) ) {
            throw new \Exception( __( 'File not found.', 'a-tables-charts' ) );
        }

        try {
            $spreadsheet = IOFactory::load( $file_path );
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();

            if ( empty( $data ) ) {
                throw new \Exception( __( 'File is empty.', 'a-tables-charts' ) );
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
                    $formatted_row[ $header ] = isset( $row[ $index ] ) ? $row[ $index ] : '';
                }
                $formatted_rows[] = $formatted_row;
            }

            return array(
                'headers' => $headers,
                'rows'    => $formatted_rows,
            );

        } catch ( \Exception $e ) {
            throw new \Exception(
                sprintf(
                    __( 'Failed to parse Excel file: %s', 'a-tables-charts' ),
                    $e->getMessage()
                )
            );
        }
    }
}

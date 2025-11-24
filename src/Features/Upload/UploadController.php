<?php
/**
 * Upload Controller
 *
 * @package ATables\Features\Upload
 * @since 2.0.0
 */

namespace ATables\Features\Upload;

use ATables\Features\Tables\TableService;

/**
 * UploadController Class
 *
 * Handles file uploads and parsing.
 */
class UploadController {

    /**
     * Table service
     *
     * @var TableService
     */
    private $table_service;

    /**
     * Excel parser
     *
     * @var ExcelParser
     */
    private $excel_parser;

    /**
     * CSV parser
     *
     * @var CSVParser
     */
    private $csv_parser;

    /**
     * Constructor
     */
    public function __construct() {
        $this->table_service = new TableService();
        $this->excel_parser = new ExcelParser();
        $this->csv_parser = new CSVParser();
    }

    /**
     * Handle file upload via AJAX
     */
    public function upload_file() {
        check_ajax_referer( 'atables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'a-tables-charts' ) ) );
        }

        // Check if file was uploaded
        if ( ! isset( $_FILES['file'] ) ) {
            wp_send_json_error( array( 'message' => __( 'No file uploaded.', 'a-tables-charts' ) ) );
        }

        $file = $_FILES['file'];

        // Check for upload errors
        if ( $file['error'] !== UPLOAD_ERR_OK ) {
            wp_send_json_error( array( 'message' => __( 'File upload failed.', 'a-tables-charts' ) ) );
        }

        // Get file extension
        $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

        // Parse file based on extension
        try {
            if ( in_array( $ext, array( 'xlsx', 'xls' ), true ) ) {
                $data = $this->excel_parser->parse( $file['tmp_name'] );
            } elseif ( $ext === 'csv' ) {
                $data = $this->csv_parser->parse( $file['tmp_name'] );
            } else {
                wp_send_json_error( array(
                    'message' => __( 'Unsupported file type. Please upload Excel (.xlsx, .xls) or CSV file.', 'a-tables-charts' ),
                ) );
            }

            // Get table title from POST or use filename
            $title = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : pathinfo( $file['name'], PATHINFO_FILENAME );
            $description = isset( $_POST['description'] ) ? sanitize_textarea_field( $_POST['description'] ) : '';

            // Create table from data
            $table_id = $this->table_service->create_from_data(
                $title,
                $data['headers'],
                $data['rows'],
                $description
            );

            if ( ! $table_id ) {
                wp_send_json_error( array(
                    'message' => __( 'Failed to create table from file.', 'a-tables-charts' ),
                ) );
            }

            wp_send_json_success( array(
                'message'  => __( 'File uploaded and table created successfully!', 'a-tables-charts' ),
                'table_id' => $table_id,
            ) );

        } catch ( \Exception $e ) {
            wp_send_json_error( array(
                'message' => sprintf(
                    __( 'Error parsing file: %s', 'a-tables-charts' ),
                    $e->getMessage()
                ),
            ) );
        }
    }
}

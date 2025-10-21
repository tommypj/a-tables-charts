<?php
/**
 * Chart Controller
 *
 * Handles AJAX requests for chart operations.
 *
 * @package ATablesCharts\Charts\Controllers
 * @since 1.0.0
 */

namespace ATablesCharts\Charts\Controllers;

use ATablesCharts\Charts\Services\ChartService;
use ATablesCharts\Shared\Utils\Validator;
use ATablesCharts\Shared\Utils\Sanitizer;
use ATablesCharts\Shared\Utils\Helpers;

/**
 * ChartController Class
 *
 * Responsibilities:
 * - Handle AJAX chart operations
 * - Validate requests
 * - Return JSON responses
 */
class ChartController {

	/**
	 * Chart service
	 *
	 * @var ChartService
	 */
	private $service;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->service = new ChartService();
	}

	/**
	 * Register AJAX hooks
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_atables_create_chart', array( $this, 'handle_create_chart' ) );
		add_action( 'wp_ajax_atables_get_chart', array( $this, 'handle_get_chart' ) );
		add_action( 'wp_ajax_atables_get_charts', array( $this, 'handle_get_charts' ) );
		add_action( 'wp_ajax_atables_update_chart', array( $this, 'handle_update_chart' ) );
		add_action( 'wp_ajax_atables_delete_chart', array( $this, 'handle_delete_chart' ) );
		add_action( 'wp_ajax_atables_get_chart_data', array( $this, 'handle_get_chart_data' ) );
	}

	/**
	 * Handle create chart request
	 */
	public function handle_create_chart() {
		if ( ! $this->verify_nonce() || ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Permission denied.', 'a-tables-charts' ), 403 );
			return;
		}

		$table_id      = isset( $_POST['table_id'] ) ? (int) $_POST['table_id'] : 0;
		$title         = isset( $_POST['title'] ) ? Sanitizer::text( $_POST['title'] ) : '';
		$type          = isset( $_POST['type'] ) ? Sanitizer::text( $_POST['type'] ) : 'bar';
		$label_column  = isset( $_POST['label_column'] ) ? Sanitizer::text( $_POST['label_column'] ) : '';
		$data_columns  = isset( $_POST['data_columns'] ) ? $_POST['data_columns'] : array();

		if ( empty( $table_id ) || empty( $title ) ) {
			$this->send_error( __( 'Missing required fields.', 'a-tables-charts' ), 400 );
			return;
		}

		// Sanitize data columns.
		$data_columns = array_map( array( 'ATablesCharts\\Shared\\Utils\\Sanitizer', 'text' ), $data_columns );

		$chart_data = array(
			'table_id' => $table_id,
			'title'    => $title,
			'type'     => $type,
			'config'   => array(
				'label_column' => $label_column,
				'data_columns' => $data_columns,
			),
		);

		$result = $this->service->create_chart( $chart_data );

		if ( $result['success'] ) {
			$this->send_success( array( 'chart_id' => $result['chart_id'] ), $result['message'] );
		} else {
			$this->send_error( $result['message'], 400 );
		}
	}

	/**
	 * Handle get chart request
	 */
	public function handle_get_chart() {
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Permission denied.', 'a-tables-charts' ), 403 );
			return;
		}

		$chart_id = isset( $_POST['chart_id'] ) ? (int) $_POST['chart_id'] : 0;

		if ( empty( $chart_id ) ) {
			$this->send_error( __( 'Chart ID is required.', 'a-tables-charts' ), 400 );
			return;
		}

		$chart = $this->service->get_chart( $chart_id );

		if ( ! $chart ) {
			$this->send_error( __( 'Chart not found.', 'a-tables-charts' ), 404 );
			return;
		}

		$this->send_success( $chart->to_array() );
	}

	/**
	 * Handle get charts request
	 */
	public function handle_get_charts() {
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Permission denied.', 'a-tables-charts' ), 403 );
			return;
		}

		$page     = isset( $_POST['page'] ) ? (int) $_POST['page'] : 1;
		$per_page = isset( $_POST['per_page'] ) ? (int) $_POST['per_page'] : 20;
		$table_id = isset( $_POST['table_id'] ) ? (int) $_POST['table_id'] : null;

		$args = array(
			'page'     => $page,
			'per_page' => $per_page,
		);

		if ( $table_id ) {
			$args['table_id'] = $table_id;
		}

		$result = $this->service->get_all_charts( $args );

		$charts_array = array_map(
			function( $chart ) {
				return $chart->to_array();
			},
			$result['charts']
		);

		$this->send_success(
			array(
				'charts'   => $charts_array,
				'total'    => $result['total'],
				'page'     => $result['page'],
				'per_page' => $result['per_page'],
			)
		);
	}

	/**
	 * Handle update chart request
	 */
	public function handle_update_chart() {
		if ( ! $this->verify_nonce() || ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Permission denied.', 'a-tables-charts' ), 403 );
			return;
		}

		$chart_id = isset( $_POST['chart_id'] ) ? (int) $_POST['chart_id'] : 0;

		if ( empty( $chart_id ) ) {
			$this->send_error( __( 'Chart ID is required.', 'a-tables-charts' ), 400 );
			return;
		}

		$data = array();

		if ( isset( $_POST['title'] ) ) {
			$data['title'] = Sanitizer::text( $_POST['title'] );
		}

		if ( isset( $_POST['type'] ) ) {
			$data['type'] = Sanitizer::text( $_POST['type'] );
		}

		if ( isset( $_POST['status'] ) ) {
			$data['status'] = Sanitizer::text( $_POST['status'] );
		}

		$result = $this->service->update_chart( $chart_id, $data );

		if ( $result['success'] ) {
			$this->send_success( null, $result['message'] );
		} else {
			$this->send_error( $result['message'], 400 );
		}
	}

	/**
	 * Handle delete chart request
	 */
	public function handle_delete_chart() {
		if ( ! $this->verify_nonce() || ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Permission denied.', 'a-tables-charts' ), 403 );
			return;
		}

		$chart_id = isset( $_POST['chart_id'] ) ? (int) $_POST['chart_id'] : 0;

		if ( empty( $chart_id ) ) {
			$this->send_error( __( 'Chart ID is required.', 'a-tables-charts' ), 400 );
			return;
		}

		$result = $this->service->delete_chart( $chart_id );

		if ( $result['success'] ) {
			$this->send_success( null, $result['message'] );
		} else {
			$this->send_error( $result['message'], 400 );
		}
	}

	/**
	 * Handle get chart data request
	 */
	public function handle_get_chart_data() {
		if ( ! $this->verify_nonce() ) {
			$this->send_error( __( 'Permission denied.', 'a-tables-charts' ), 403 );
			return;
		}

		$chart_id = isset( $_POST['chart_id'] ) ? (int) $_POST['chart_id'] : 0;

		if ( empty( $chart_id ) ) {
			$this->send_error( __( 'Chart ID is required.', 'a-tables-charts' ), 400 );
			return;
		}

		$data = $this->service->get_chart_data( $chart_id );

		if ( ! $data ) {
			$this->send_error( __( 'Failed to generate chart data.', 'a-tables-charts' ), 500 );
			return;
		}

		$this->send_success( $data );
	}

	/**
	 * Verify nonce
	 *
	 * @return bool
	 */
	private function verify_nonce() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
		return Validator::nonce( $nonce, 'atables_admin_nonce' );
	}

	/**
	 * Send success response
	 *
	 * @param mixed  $data    Response data.
	 * @param string $message Success message.
	 */
	private function send_success( $data = null, $message = '' ) {
		Helpers::send_json( true, $data, $message, 200 );
	}

	/**
	 * Send error response
	 *
	 * @param string $message Error message.
	 * @param int    $code    HTTP status code.
	 */
	private function send_error( $message, $code = 400 ) {
		Helpers::send_json( false, null, $message, $code );
	}
}

<?php
/**
 * Handles reports CSV export batches.
 */

namespace Automattic\WooCommerce\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\API\Reports\ExportableInterface;

/**
 * Include dependencies.
 */
if ( ! class_exists( 'WC_CSV_Batch_Exporter', false ) ) {
	include_once WC_ABSPATH . 'includes/export/abstract-wc-csv-batch-exporter.php';
}

/**
 * ReportCSVExporter Class.
 */
class ReportCSVExporter extends \WC_CSV_Batch_Exporter {
	/**
	 * Type of report being exported.
	 *
	 * @var string
	 */
	protected $report_type;

	/**
	 * Parameters for the report query.
	 *
	 * @var array
	 */
	protected $report_args;

	/**
	 * REST controller for the report.
	 *
	 * @var WC_REST_Reports_Controller
	 */
	protected $controller;

	/**
	 * Constructor.
	 *
	 * @param string $type Report type. E.g. 'customers'.
	 * @param array  $args Report parameters.
	 */
	public function __construct( $type = false, $args = array() ) {
		parent::__construct();

		self::maybe_create_directory();

		if ( ! empty( $type ) ) {
			$this->set_report_type( $type );
			$this->set_column_names( $this->get_report_columns() );
		}

		if ( ! empty( $args ) ) {
			$this->set_report_args( $args );
		}
	}

	/**
	 * Create the directory for reports if it does not yet exist.
	 */
	public static function maybe_create_directory() {
		$reports_dir = self::get_reports_directory();

		$files = array(
			array(
				'base'    => $reports_dir,
				'file'    => '.htaccess',
				'content' => 'DirectoryIndex index.php index.html' . PHP_EOL . 'deny from all',
			),
			array(
				'base'    => $reports_dir,
				'file'    => 'index.html',
				'content' => '',
			),
		);

		foreach ( $files as $file ) {
			if ( ! file_exists( trailingslashit( $file['base'] ) ) ) {
				wp_mkdir_p( $file['base'] );
			}
			if ( ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'wb' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen
				if ( $file_handle ) {
					fwrite( $file_handle, $file['content'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
					fclose( $file_handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
				}
			}
		}
	}

	/**
	 * Get report uploads directory.
	 *
	 * @return string
	 */
	public static function get_reports_directory() {
		$upload_dir = wp_upload_dir();
		return trailingslashit( $upload_dir['basedir'] ) . 'woocommerce_uploads/reports/';
	}

	/**
	 * Get file path to export to.
	 *
	 * @return string
	 */
	protected function get_file_path() {
		return self::get_reports_directory() . $this->get_filename();
	}


	/**
	 * Setter for report type.
	 *
	 * @param string $type The report type. E.g. customers.
	 */
	public function set_report_type( $type ) {
		$this->report_type = $type;
		$this->export_type = "admin_{$type}_report";
		$this->filename    = "wc-{$type}-report-export";
		$this->controller  = $this->map_report_controller();
	}

	/**
	 * Setter for report args.
	 *
	 * @param array $args The report args.
	 */
	public function set_report_args( $args ) {
		// Use our own internal limit and include all extended info.
		$report_args = array_merge(
			$args,
			array(
				'per_page'      => $this->get_limit(),
				'extended_info' => true,
			)
		);

		// Should this happen externally?
		if ( isset( $report_args['page'] ) ) {
			$this->set_page( $report_args['page'] );
		}

		$this->report_args = $report_args;
	}

	/**
	 * Get a REST controller instance for the report type.
	 *
	 * @return bool|WC_REST_Reports_Controller Report controller instance or boolean false on error.
	 */
	protected function map_report_controller() {
		// @todo - Add filter to this list.
		$controller_map = array(
			'products'   => 'Automattic\WooCommerce\Admin\API\Reports\Products\Controller',
			'variations' => 'Automattic\WooCommerce\Admin\API\Reports\Variations\Controller',
			'orders'     => 'Automattic\WooCommerce\Admin\API\Reports\Orders\Controller',
			'categories' => 'Automattic\WooCommerce\Admin\API\Reports\Categories\Controller',
			'taxes'      => 'Automattic\WooCommerce\Admin\API\Reports\Taxes\Controller',
			'coupons'    => 'Automattic\WooCommerce\Admin\API\Reports\Coupons\Controller',
			'stock'      => 'Automattic\WooCommerce\Admin\API\Reports\Stock\Controller',
			'downloads'  => 'Automattic\WooCommerce\Admin\API\Reports\Downloads\Controller',
			'customers'  => 'Automattic\WooCommerce\Admin\API\Reports\Customers\Controller',
			'revenue'    => 'Automattic\WooCommerce\Admin\API\Reports\Revenue\Stats\Controller',
		);

		if ( isset( $controller_map[ $this->report_type ] ) ) {
			// Load the controllers if accessing outside the REST API.
			return new $controller_map[ $this->report_type ]();
		}

		// Should this do something else?
		return false;
	}

	/**
	 * Get the report columns from the controller.
	 *
	 * @return array Array of report column names.
	 */
	protected function get_report_columns() {
		// Default to the report's defined export columns.
		if ( $this->controller instanceof ExportableInterface ) {
			return $this->controller->get_export_columns();
		}

		// Fallback to generating columns from the report schema.
		$report_columns = array();
		$report_schema  = $this->controller->get_item_schema();

		if ( isset( $report_schema['properties'] ) ) {
			foreach ( $report_schema['properties'] as $column_name => $column_info ) {
				// Expand extended info columns into export.
				if ( 'extended_info' === $column_name ) {
					// Remove columns with questionable CSV values, like markup.
					$extended_info  = array_diff( array_keys( $column_info ), array( 'image' ) );
					$report_columns = array_merge( $report_columns, $extended_info );
				} else {
					$report_columns[] = $column_name;
				}
			}
		}

		return $report_columns;
	}

	/**
	 * Get total % complete.
	 *
	 * Forces an int from parent::get_percent_complete(), which can return a float.
	 *
	 * @return int Percent complete.
	 */
	public function get_percent_complete() {
		return intval( parent::get_percent_complete() );
	}

	/**
	 * Get total number of rows in export.
	 *
	 * @return int Number of rows to export.
	 */
	public function get_total_rows() {
		return $this->total_rows;
	}

	/**
	 * Prepare data for export.
	 */
	public function prepare_data_to_export() {
		$request  = new \WP_REST_Request( 'GET', "/wc-analytics/reports/{$this->report_type}" );
		$params   = $this->controller->get_collection_params();
		$defaults = array();

		foreach ( $params as $arg => $options ) {
			if ( isset( $options['default'] ) ) {
				$defaults[ $arg ] = $options['default'];
			}
		}

		$request->set_attributes( array( 'args' => $params ) );
		$request->set_default_params( $defaults );
		$request->set_query_params( $this->report_args );
		$request->sanitize_params();

		// Does the controller have an export-specific item retrieval method?
		// @todo - Potentially revisit. This is only for /revenue/stats/.
		if ( is_callable( array( $this->controller, 'get_export_items' ) ) ) {
			$response = $this->controller->get_export_items( $request );
		} else {
			$response = $this->controller->get_items( $request );
		}

		// Use WP_REST_Server::response_to_data() to embed links in data.
		add_filter( 'woocommerce_rest_check_permissions', '__return_true' );
		$rest_server = rest_get_server();
		$report_data = $rest_server->response_to_data( $response, true );
		remove_filter( 'woocommerce_rest_check_permissions', '__return_true' );

		$report_meta      = $response->get_headers();
		$this->total_rows = $report_meta['X-WP-Total'];
		$this->row_data   = array_map( array( $this, 'generate_row_data' ), $report_data );
	}

	/**
	 * Generate row data from a raw report item.
	 *
	 * @param object $item Report item data.
	 * @return array CSV row data.
	 */
	protected function get_raw_row_data( $item ) {
		$columns = $this->get_column_names();
		$row     = array();

		// Expand extended info.
		if ( isset( $item['extended_info'] ) ) {
			// Pull extended info property from report item object.
			$extended_info = (array) $item['extended_info'];
			unset( $item['extended_info'] );

			// Merge extended info columns into report item object.
			$item = array_merge( $item, $extended_info );
		}

		foreach ( $columns as $column_id => $column_name ) {
			$value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : null;

			if ( has_filter( "woocommerce_export_{$this->export_type}_column_{$column_name}" ) ) {
				// Filter for 3rd parties.
				$value = apply_filters( "woocommerce_export_{$this->export_type}_column_{$column_name}", '', $item );

			} elseif ( is_callable( array( $this, "get_column_value_{$column_name}" ) ) ) {
				// Handle special columns which don't map 1:1 to item data.
				$value = $this->{"get_column_value_{$column_name}"}( $item, $this->export_type );

			} elseif ( ! is_scalar( $value ) ) {
				// Ensure that the value is somewhat readable in CSV.
				$value = wp_json_encode( $value );
			}

			$row[ $column_id ] = $value;
		}

		return $row;
	}

	/**
	 * Get the export row for a given report item.
	 *
	 * @param object $item Report item data.
	 * @return array CSV row data.
	 */
	protected function generate_row_data( $item ) {
		// Default to the report's export method.
		if ( $this->controller instanceof ExportableInterface ) {
			$row = $this->controller->prepare_item_for_export( $item );
		} else {
			// Fallback to raw report data.
			$row = $this->get_raw_row_data( $item );
		}

		return apply_filters( "woocommerce_export_{$this->export_type}_row_data", $row, $item );
	}
}

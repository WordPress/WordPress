<?php
/**
 * REST API Reports Import Controller
 *
 * Handles requests to /reports/import
 */

namespace Automattic\WooCommerce\Admin\API\Reports\Import;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\ReportsSync;

/**
 * Reports Imports controller.
 *
 * @internal
 * @extends \Automattic\WooCommerce\Admin\API\Reports\Controller
 */
class Controller extends \Automattic\WooCommerce\Admin\API\Reports\Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'reports/import';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'import_items' ),
					'permission_callback' => array( $this, 'import_permissions_check' ),
					'args'                => $this->get_import_collection_params(),
				),
				'schema' => array( $this, 'get_import_public_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/cancel',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'cancel_import' ),
					'permission_callback' => array( $this, 'import_permissions_check' ),
				),
				'schema' => array( $this, 'get_import_public_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/delete',
			array(
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'delete_imported_items' ),
					'permission_callback' => array( $this, 'import_permissions_check' ),
				),
				'schema' => array( $this, 'get_import_public_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/status',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_import_status' ),
					'permission_callback' => array( $this, 'import_permissions_check' ),
				),
				'schema' => array( $this, 'get_import_public_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/totals',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_import_totals' ),
					'permission_callback' => array( $this, 'import_permissions_check' ),
					'args'                => $this->get_import_collection_params(),
				),
				'schema' => array( $this, 'get_import_public_schema' ),
			)
		);
	}

	/**
	 * Makes sure the current user has access to WRITE the settings APIs.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function import_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'edit' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you cannot edit this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Import data based on user request params.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function import_items( $request ) {
		$query_args = $this->prepare_objects_query( $request );
		$import     = ReportsSync::regenerate_report_data( $query_args['days'], $query_args['skip_existing'] );

		if ( is_wp_error( $import ) ) {
			$result = array(
				'status'  => 'error',
				'message' => $import->get_error_message(),
			);
		} else {
			$result = array(
				'status'  => 'success',
				'message' => $import,
			);
		}

		$response = $this->prepare_item_for_response( $result, $request );
		$data     = $this->prepare_response_for_collection( $response );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepare request object as query args.
	 *
	 * @param WP_REST_Request $request Request data.
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		$args                  = array();
		$args['skip_existing'] = $request['skip_existing'];
		$args['days']          = $request['days'];

		return $args;
	}

	/**
	 * Prepare the data object for response.
	 *
	 * @param object          $item Data object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data     = $this->add_additional_fields_to_object( $item, $request );
		$data     = $this->filter_response_by_context( $data, 'view' );
		$response = rest_ensure_response( $data );

		/**
		 * Filter the list returned from the API.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param array            $item     The original item.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_reports_import', $response, $item, $request );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_import_collection_params() {
		$params                  = array();
		$params['days']          = array(
			'description'       => __( 'Number of days to import.', 'woocommerce' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 0,
		);
		$params['skip_existing'] = array(
			'description'       => __( 'Skip importing existing order data.', 'woocommerce' ),
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);
		return $params;
	}

	/**
	 * Get the Report's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_import_public_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'report_import',
			'type'       => 'object',
			'properties' => array(
				'status'  => array(
					'description' => __( 'Regeneration status.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'message' => array(
					'description' => __( 'Regenerate data message.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Cancel all queued import actions.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function cancel_import( $request ) {
		ReportsSync::clear_queued_actions();

		$result = array(
			'status'  => 'success',
			'message' => __( 'All pending and in-progress import actions have been cancelled.', 'woocommerce' ),
		);

		$response = $this->prepare_item_for_response( $result, $request );
		$data     = $this->prepare_response_for_collection( $response );

		return rest_ensure_response( $data );
	}

	/**
	 * Delete all imported items.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_imported_items( $request ) {
		$delete = ReportsSync::delete_report_data();

		if ( is_wp_error( $delete ) ) {
			$result = array(
				'status'  => 'error',
				'message' => $delete->get_error_message(),
			);
		} else {
			$result = array(
				'status'  => 'success',
				'message' => $delete,
			);
		}

		$response = $this->prepare_item_for_response( $result, $request );
		$data     = $this->prepare_response_for_collection( $response );

		return rest_ensure_response( $data );
	}

	/**
	 * Get the status of the current import.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_import_status( $request ) {
		$result   = ReportsSync::get_import_stats();
		$response = $this->prepare_item_for_response( $result, $request );
		$data     = $this->prepare_response_for_collection( $response );

		return rest_ensure_response( $data );
	}

	/**
	 * Get the total orders and customers based on user supplied params.
	 *
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_import_totals( $request ) {
		$query_args = $this->prepare_objects_query( $request );
		$totals     = ReportsSync::get_import_totals( $query_args['days'], $query_args['skip_existing'] );

		$response = $this->prepare_item_for_response( $totals, $request );
		$data     = $this->prepare_response_for_collection( $response );

		return rest_ensure_response( $data );
	}
}

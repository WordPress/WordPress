<?php
/**
 * REST API Customers Controller
 *
 * Handles requests to /customers/*
 */

namespace Automattic\WooCommerce\Admin\API;

defined( 'ABSPATH' ) || exit;

/**
 * Customers controller.
 *
 * @internal
 * @extends \Automattic\WooCommerce\Admin\API\Reports\Customers\Controller
 */
class Customers extends \Automattic\WooCommerce\Admin\API\Reports\Customers\Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'customers';

	/**
	 * Register the routes for customers.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d-]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique ID for the resource.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Maps query arguments from the REST request.
	 *
	 * @param array $request Request array.
	 * @return array
	 */
	protected function prepare_reports_query( $request ) {
		$args              = parent::prepare_reports_query( $request );
		$args['customers'] = $request['include'];
		return $args;
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params            = parent::get_collection_params();
		$params['include'] = $params['customers'];
		unset( $params['customers'] );
		return $params;
	}
}

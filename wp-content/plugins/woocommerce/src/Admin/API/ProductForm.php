<?php
/**
 * REST API Product Form Controller
 *
 * Handles requests to retrieve product form data.
 */

namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Internal\Admin\ProductForm\FormFactory;

defined( 'ABSPATH' ) || exit;

/**
 * ProductForm Controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class ProductForm extends \WC_REST_Data_Controller {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-admin';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'product-form';

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_form_config' ),
					'permission_callback' => array( $this, 'get_product_form_permission_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/fields',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_fields' ),
					'permission_callback' => array( $this, 'get_product_form_permission_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to manage woocommerce.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_product_form_permission_check( $request ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to retrieve product form data.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get the form fields.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_fields( $request ) {
		$json = array_map(
			function( $field ) {
				return $field->get_json();
			},
			FormFactory::get_fields()
		);

		return rest_ensure_response( $json );
	}

	/**
	 * Get the form config.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_form_config( $request ) {
		$fields      = array_map(
			function( $field ) {
				return $field->get_json();
			},
			FormFactory::get_fields()
		);
		$subsections = array_map(
			function( $subsection ) {
				return $subsection->get_json();
			},
			FormFactory::get_subsections()
		);
		$sections    = array_map(
			function( $section ) {
				return $section->get_json();
			},
			FormFactory::get_sections()
		);
		$tabs        = array_map(
			function( $tab ) {
				return $tab->get_json();
			},
			FormFactory::get_tabs()
		);

		return rest_ensure_response(
			array(
				'fields'      => $fields,
				'subsections' => $subsections,
				'sections'    => $sections,
				'tabs'        => $tabs,
			)
		);
	}
}

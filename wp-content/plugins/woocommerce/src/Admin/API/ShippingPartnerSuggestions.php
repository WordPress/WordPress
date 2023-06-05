<?php
/**
 * Handles requests for shipping partner suggestions.
 */

namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Admin\Features\ShippingPartnerSuggestions\DefaultShippingPartners;
use Automattic\WooCommerce\Admin\Features\ShippingPartnerSuggestions\ShippingPartnerSuggestions as Suggestions;

defined( 'ABSPATH' ) || exit;

/**
 * ShippingPartnerSuggestions Controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class ShippingPartnerSuggestions extends \WC_REST_Data_Controller {
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
	protected $rest_base = 'shipping-partner-suggestions';

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
					'callback'            => array( $this, 'get_suggestions' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => array(
						'force_default_suggestions' => array(
							'type'        => 'boolean',
							'description' => __( 'Return the default shipping partner suggestions when woocommerce_show_marketplace_suggestions option is set to no', 'woocommerce' ),
						),
					),
				),
				'schema' => array( $this, 'get_suggestions_schema' ),
			)
		);

	}

	/**
	 * Check if a given request has access to manage plugins.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_permission_check( $request ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new \WP_Error( 'woocommerce_rest_cannot_update', __( 'Sorry, you cannot manage plugins.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}
		return true;
	}

	/**
	 * Check if suggestions should be shown in the settings screen.
	 *
	 * @return bool
	 */
	private function should_display() {
		if ( 'no' === get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) ) {
			return false;
		}

		/**
		 * The return value can be controlled via woocommerce_allow_shipping_partner_suggestions filter.
		 *
		 * @since 7.4.1
		 */
		return apply_filters( 'woocommerce_allow_shipping_partner_suggestions', true );
	}

	/**
	 * Return suggested shipping partners.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function get_suggestions( $request ) {
		$should_display = $this->should_display();
		$force_default  = $request->get_param( 'force_default_suggestions' );

		if ( $should_display ) {
			return Suggestions::get_suggestions();
		} elseif ( false === $should_display && true === $force_default ) {
			return rest_ensure_response( Suggestions::get_suggestions( DefaultShippingPartners::get_all() ) );
		}

		return rest_ensure_response( Suggestions::get_suggestions( DefaultShippingPartners::get_all() ) );
	}

	/**
	 * Get the schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public static function get_suggestions_schema() {
		$feature_def = array(
			'type'  => 'array',
			'items' => array(
				'type'       => 'object',
				'properties' => array(
					'icon'        => array(
						'type' => 'string',
					),
					'title'       => array(
						'type' => 'string',
					),
					'description' => array(
						'type' => 'string',
					),
				),
			),
		);
		$layout_def  = array(
			'type'       => 'object',
			'properties' => array(
				'image'    => array(
					'type'        => 'string',
					'description' => '',
				),
				'features' => $feature_def,
			),
		);

		$item_schema = array(
			'type'       => 'object',
			'required'   => array( 'name', 'is_visible', 'available_layouts' ),
			// require layout_row or layout_column. One of them must exist.
			'anyOf'      => array(
				array(
					'required' => 'layout_row',
				),
				array(
					'required' => 'layout_column',
				),
			),
			'properties' => array(
				'name'              => array(
					'description' => __( 'Plugin name.', 'woocommerce' ),
					'type'        => 'string',
					'required'    => true,
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'slug'              => array(
					'description' => __( 'Plugin slug used in https://wordpress.org/plugins/{slug}.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'layout_row'        => $layout_def,
				'layout_column'     => $layout_def,
				'description'       => array(
					'description' => __( 'Description', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'learn_more_link'   => array(
					'description' => __( 'Learn more link .', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'is_visible'        => array(
					'description' => __( 'Suggestion visibility.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'available_layouts' => array(
					'description' => __( 'Available layouts -- single, dual, or both', 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type' => 'string',
						'enum' => array( 'row', 'column' ),
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);

		$schema = array(
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title'   => 'shipping-partner-suggestions',
			'type'    => 'array',
			'items'   => array( $item_schema ),
		);

		return $schema;
	}
}

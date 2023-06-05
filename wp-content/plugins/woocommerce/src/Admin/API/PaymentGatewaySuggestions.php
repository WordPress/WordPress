<?php
/**
 * REST API Payment Gateway Suggestions Controller
 *
 * Handles requests to install and activate depedent plugins.
 */

namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions\DefaultPaymentGateways;
use Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions\Init as Suggestions;

defined( 'ABSPATH' ) || exit;

/**
 * PaymentGatewaySuggetsions Controller.
 *
 * @internal
 * @extends WC_REST_Data_Controller
 */
class PaymentGatewaySuggestions extends \WC_REST_Data_Controller {
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
	protected $rest_base = 'payment-gateway-suggestions';

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
							'description' => __( 'Return the default payment suggestions when woocommerce_show_marketplace_suggestions and woocommerce_setting_payments_recommendations_hidden options are set to no', 'woocommerce' ),
						),
					),
				),
				'schema' => array( $this, 'get_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/dismiss',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'dismiss_payment_gateway_suggestion' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
				),
				'schema' => array( $this, 'get_item_schema' ),
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
	 * Return suggested payment gateways.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function get_suggestions( $request ) {

		$should_display = Suggestions::should_display();
		$force_default  = $request->get_param( 'force_default_suggestions' );

		if ( $should_display ) {
			return Suggestions::get_suggestions();
		} elseif ( false === $should_display && true === $force_default ) {
			return rest_ensure_response( Suggestions::get_suggestions( DefaultPaymentGateways::get_all() ) );
		}

		return rest_ensure_response( array() );
	}

	/**
	 * Dismisses suggested payment gateways.
	 *
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function dismiss_payment_gateway_suggestion() {
		$success = Suggestions::dismiss();
		return rest_ensure_response( $success );
	}

	/**
	 * Get the schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'payment-gateway-suggestions',
			'type'       => 'array',
			'properties' => array(
				'content'                 => array(
					'description' => __( 'Suggestion description.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'id'                      => array(
					'description' => __( 'Suggestion ID.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'image'                   => array(
					'description' => __( 'Gateway image.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'is_visible'              => array(
					'description' => __( 'Suggestion visibility.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'plugins'                 => array(
					'description' => __( 'Array of plugin slugs.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'recommendation_priority' => array(
					'description' => __( 'Priority of recommendation.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'title'                   => array(
					'description' => __( 'Gateway title.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'transaction_processors'  => array(
					'description'         => __( 'Array of transaction processors and their images.', 'woocommerce' ),
					'type'                => 'object',
					'addtionalProperties' => array(
						'type'   => 'string',
						'format' => 'uri',
					),
					'context'             => array( 'view', 'edit' ),
					'readonly'            => true,
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}

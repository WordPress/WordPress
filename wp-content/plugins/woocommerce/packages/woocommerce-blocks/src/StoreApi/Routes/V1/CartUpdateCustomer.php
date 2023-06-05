<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Utilities\DraftOrderTrait;
use Automattic\WooCommerce\StoreApi\Utilities\ValidationUtils;

/**
 * CartUpdateCustomer class.
 *
 * Updates the customer billing and shipping address and returns an updated cart--things such as taxes may be recalculated.
 */
class CartUpdateCustomer extends AbstractCartRoute {
	use DraftOrderTrait;

	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-update-customer';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/cart/update-customer';
	}

	/**
	 * Get method arguments for this REST route.
	 *
	 * @return array An array of endpoints.
	 */
	public function get_args() {
		return [
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'get_response' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'billing_address'  => [
						'description'       => __( 'Billing address.', 'woocommerce' ),
						'type'              => 'object',
						'context'           => [ 'view', 'edit' ],
						'properties'        => $this->schema->billing_address_schema->get_properties(),
						'sanitize_callback' => null,
					],
					'shipping_address' => [
						'description'       => __( 'Shipping address.', 'woocommerce' ),
						'type'              => 'object',
						'context'           => [ 'view', 'edit' ],
						'properties'        => $this->schema->shipping_address_schema->get_properties(),
						'sanitize_callback' => null,
					],
				],
			],
			'schema'      => [ $this->schema, 'get_public_item_schema' ],
			'allow_batch' => [ 'v1' => true ],
		];
	}

	/**
	 * Validate address params now they are populated.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @param array            $billing Billing address.
	 * @param array            $shipping Shipping address.
	 * @return \WP_Error|true
	 */
	protected function validate_address_params( $request, $billing, $shipping ) {
		$posted_billing  = isset( $request['billing_address'] );
		$posted_shipping = isset( $request['shipping_address'] );
		$invalid_params  = array();
		$invalid_details = array();

		if ( $posted_billing ) {
			$billing_validation_check = $this->schema->billing_address_schema->validate_callback( $billing, $request, 'billing_address' );

			if ( false === $billing_validation_check ) {
				$invalid_params['billing_address'] = __( 'Invalid parameter.', 'woocommerce' );
			} elseif ( is_wp_error( $billing_validation_check ) ) {
				$invalid_params['billing_address']  = implode( ' ', $billing_validation_check->get_error_messages() );
				$invalid_details['billing_address'] = \rest_convert_error_to_response( $billing_validation_check )->get_data();
			}
		}

		if ( $posted_shipping ) {
			$shipping_validation_check = $this->schema->shipping_address_schema->validate_callback( $shipping, $request, 'shipping_address' );

			if ( false === $shipping_validation_check ) {
				$invalid_params['shipping_address'] = __( 'Invalid parameter.', 'woocommerce' );
			} elseif ( is_wp_error( $shipping_validation_check ) ) {
				$invalid_params['shipping_address']  = implode( ' ', $shipping_validation_check->get_error_messages() );
				$invalid_details['shipping_address'] = \rest_convert_error_to_response( $shipping_validation_check )->get_data();
			}
		}

		if ( $invalid_params ) {
			return new \WP_Error(
				'rest_invalid_param',
				/* translators: %s: List of invalid parameters. */
				sprintf( __( 'Invalid parameter(s): %s', 'woocommerce' ), implode( ', ', array_keys( $invalid_params ) ) ),
				[
					'status'  => 400,
					'params'  => $invalid_params,
					'details' => $invalid_details,
				]
			);
		}

		return true;
	}

	/**
	 * Handle the request and return a valid response for this endpoint.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_post_response( \WP_REST_Request $request ) {
		$cart     = $this->cart_controller->get_cart_instance();
		$customer = wc()->customer;

		// Get data from request object and merge with customer object, then sanitize.
		$billing  = $this->schema->billing_address_schema->sanitize_callback(
			wp_parse_args(
				$request['billing_address'] ?? [],
				$this->get_customer_billing_address( $customer )
			),
			$request,
			'billing_address'
		);
		$shipping = $this->schema->billing_address_schema->sanitize_callback(
			wp_parse_args(
				$request['shipping_address'] ?? [],
				$this->get_customer_shipping_address( $customer )
			),
			$request,
			'shipping_address'
		);

		// If the cart does not need shipping, shipping address is forced to match billing address unless defined.
		if ( ! $cart->needs_shipping() && ! isset( $request['shipping_address'] ) ) {
			$shipping = $billing;
		}

		// Run validation and sanitization now that the cart and customer data is loaded.
		$billing  = $this->schema->billing_address_schema->sanitize_callback( $billing, $request, 'billing_address' );
		$shipping = $this->schema->shipping_address_schema->sanitize_callback( $shipping, $request, 'shipping_address' );

		// Validate data now everything is clean..
		$validation_check = $this->validate_address_params( $request, $billing, $shipping );

		if ( is_wp_error( $validation_check ) ) {
			return rest_ensure_response( $validation_check );
		}

		$customer->set_props(
			array(
				'billing_first_name'  => $billing['first_name'] ?? null,
				'billing_last_name'   => $billing['last_name'] ?? null,
				'billing_company'     => $billing['company'] ?? null,
				'billing_address_1'   => $billing['address_1'] ?? null,
				'billing_address_2'   => $billing['address_2'] ?? null,
				'billing_city'        => $billing['city'] ?? null,
				'billing_state'       => $billing['state'] ?? null,
				'billing_postcode'    => $billing['postcode'] ?? null,
				'billing_country'     => $billing['country'] ?? null,
				'billing_phone'       => $billing['phone'] ?? null,
				'billing_email'       => $billing['email'] ?? null,
				'shipping_first_name' => $shipping['first_name'] ?? null,
				'shipping_last_name'  => $shipping['last_name'] ?? null,
				'shipping_company'    => $shipping['company'] ?? null,
				'shipping_address_1'  => $shipping['address_1'] ?? null,
				'shipping_address_2'  => $shipping['address_2'] ?? null,
				'shipping_city'       => $shipping['city'] ?? null,
				'shipping_state'      => $shipping['state'] ?? null,
				'shipping_postcode'   => $shipping['postcode'] ?? null,
				'shipping_country'    => $shipping['country'] ?? null,
				'shipping_phone'      => $shipping['phone'] ?? null,
			)
		);

		wc_do_deprecated_action(
			'woocommerce_blocks_cart_update_customer_from_request',
			array(
				$customer,
				$request,
			),
			'7.2.0',
			'woocommerce_store_api_cart_update_customer_from_request',
			'This action was deprecated in WooCommerce Blocks version 7.2.0. Please use woocommerce_store_api_cart_update_customer_from_request instead.'
		);

		/**
		 * Fires when the Checkout Block/Store API updates a customer from the API request data.
		 *
		 * @since 7.2.0
		 *
		 * @param \WC_Customer $customer Customer object.
		 * @param \WP_REST_Request $request Full details about the request.
		 */
		do_action( 'woocommerce_store_api_cart_update_customer_from_request', $customer, $request );

		$customer->save();

		$this->cart_controller->calculate_totals();

		return rest_ensure_response( $this->schema->get_item_response( $cart ) );
	}

	/**
	 * Get full customer billing address.
	 *
	 * @param \WC_Customer $customer Customer object.
	 * @return array
	 */
	protected function get_customer_billing_address( \WC_Customer $customer ) {
		$validation_util = new ValidationUtils();
		$billing_country = $customer->get_billing_country();
		$billing_state   = $customer->get_billing_state();

		/**
		 * There's a bug in WooCommerce core in which not having a state ("") would result in us validating against the store's state.
		 * This resets the state to an empty string if it doesn't match the country.
		 *
		 * @todo Removing this handling once we fix the issue with the state value always being the store one.
		 */
		if ( ! $validation_util->validate_state( $billing_state, $billing_country ) ) {
			$billing_state = '';
		}
		return [
			'first_name' => $customer->get_billing_first_name(),
			'last_name'  => $customer->get_billing_last_name(),
			'company'    => $customer->get_billing_company(),
			'address_1'  => $customer->get_billing_address_1(),
			'address_2'  => $customer->get_billing_address_2(),
			'city'       => $customer->get_billing_city(),
			'state'      => $billing_state,
			'postcode'   => $customer->get_billing_postcode(),
			'country'    => $billing_country,
			'phone'      => $customer->get_billing_phone(),
			'email'      => $customer->get_billing_email(),
		];
	}

	/**
	 * Get full customer shipping address.
	 *
	 * @param \WC_Customer $customer Customer object.
	 * @return array
	 */
	protected function get_customer_shipping_address( \WC_Customer $customer ) {
		return [
			'first_name' => $customer->get_shipping_first_name(),
			'last_name'  => $customer->get_shipping_last_name(),
			'company'    => $customer->get_shipping_company(),
			'address_1'  => $customer->get_shipping_address_1(),
			'address_2'  => $customer->get_shipping_address_2(),
			'city'       => $customer->get_shipping_city(),
			'state'      => $customer->get_shipping_state(),
			'postcode'   => $customer->get_shipping_postcode(),
			'country'    => $customer->get_shipping_country(),
			'phone'      => $customer->get_shipping_phone(),
		];
	}
}

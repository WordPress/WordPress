<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * CartSelectShippingRate class.
 */
class CartSelectShippingRate extends AbstractCartRoute {
	/**
	 * The route identifier.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'cart-select-shipping-rate';

	/**
	 * Get the path of this REST route.
	 *
	 * @return string
	 */
	public function get_path() {
		return '/cart/select-shipping-rate';
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
					'package_id' => array(
						'description' => __( 'The ID of the package being shipped. Leave blank to apply to all packages.', 'woocommerce' ),
						'type'        => [ 'integer', 'string', 'null' ],
						'required'    => false,
					),
					'rate_id'    => [
						'description' => __( 'The chosen rate ID for the package.', 'woocommerce' ),
						'type'        => 'string',
						'required'    => true,
					],
				],
			],
			'schema'      => [ $this->schema, 'get_public_item_schema' ],
			'allow_batch' => [ 'v1' => true ],
		];
	}

	/**
	 * Handle the request and return a valid response for this endpoint.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_route_post_response( \WP_REST_Request $request ) {
		if ( ! wc_shipping_enabled() ) {
			throw new RouteException( 'woocommerce_rest_shipping_disabled', __( 'Shipping is disabled.', 'woocommerce' ), 404 );
		}

		if ( ! isset( $request['rate_id'] ) ) {
			throw new RouteException( 'woocommerce_rest_cart_missing_rate_id', __( 'Invalid Rate ID.', 'woocommerce' ), 400 );
		}

		$cart       = $this->cart_controller->get_cart_instance();
		$package_id = isset( $request['package_id'] ) ? wc_clean( wp_unslash( $request['package_id'] ) ) : null;
		$rate_id    = wc_clean( wp_unslash( $request['rate_id'] ) );

		try {
			if ( ! is_null( $package_id ) ) {
				$this->cart_controller->select_shipping_rate( $package_id, $rate_id );
			} else {
				foreach ( $this->cart_controller->get_shipping_packages() as $package ) {
					$this->cart_controller->select_shipping_rate( $package['package_id'], $rate_id );
				}
			}
		} catch ( \WC_Rest_Exception $e ) {
			throw new RouteException( $e->getErrorCode(), $e->getMessage(), $e->getCode() );
		}

		/**
		 * Fires an action after a shipping method has been chosen for package(s) via the Store API.
		 *
		 * This allows extensions to perform addition actions after a shipping method has been chosen, but before the
		 * cart totals are recalculated.
		 *
		 * @since 9.0.0
		 *
		 * @param string|null $package_id The sanitized ID of the package being updated. Null if all packages are being updated.
		 * @param string $rate_id The sanitized chosen rate ID for the package.
		 * @param \WP_REST_Request $request Full details about the request.
		 */
		do_action( 'woocommerce_store_api_cart_select_shipping_rate', $package_id, $rate_id, $request );

		$cart->calculate_shipping();
		$cart->calculate_totals();

		return rest_ensure_response( $this->cart_schema->get_item_response( $cart ) );
	}
}

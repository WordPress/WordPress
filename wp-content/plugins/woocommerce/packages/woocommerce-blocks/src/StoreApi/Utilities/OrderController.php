<?php
namespace Automattic\WooCommerce\StoreApi\Utilities;

use \Exception;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * OrderController class.
 * Helper class which creates and syncs orders with the cart.
 */
class OrderController {

	/**
	 * Create order and set props based on global settings.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 *
	 * @return \WC_Order A new order object.
	 */
	public function create_order_from_cart() {
		if ( wc()->cart->is_empty() ) {
			throw new RouteException(
				'woocommerce_rest_cart_empty',
				__( 'Cannot create order from empty cart.', 'woocommerce' ),
				400
			);
		}

		add_filter( 'woocommerce_default_order_status', array( $this, 'default_order_status' ) );

		$order = new \WC_Order();
		$order->set_status( 'checkout-draft' );
		$order->set_created_via( 'store-api' );
		$this->update_order_from_cart( $order );

		remove_filter( 'woocommerce_default_order_status', array( $this, 'default_order_status' ) );

		return $order;
	}

	/**
	 * Update an order using data from the current cart.
	 *
	 * @param \WC_Order $order The order object to update.
	 */
	public function update_order_from_cart( \WC_Order $order ) {
		/**
		 * This filter ensures that local pickup locations are still used for order taxes by forcing the address used to
		 * calculate tax for an order to match the current address of the customer.
		 *
		 * -    The method `$customer->get_taxable_address()` runs the filter `woocommerce_customer_taxable_address`.
		 * -    While we have a session, our `ShippingController::filter_taxable_address` function uses this hook to set
		 *      the customer address to the pickup location address if local pickup is the chosen method.
		 *
		 * Without this code in place, `$customer->get_taxable_address()` is not used when order taxes are calculated,
		 * resulting in the wrong taxes being applied with local pickup.
		 *
		 * The alternative would be to instead use `woocommerce_order_get_tax_location` to return the pickup location
		 * address directly, however since we have the customer filter in place we don't need to duplicate effort.
		 *
		 * @see \WC_Abstract_Order::get_tax_location()
		 */
		add_filter(
			'woocommerce_order_get_tax_location',
			function( $location ) {

				if ( ! is_null( wc()->customer ) ) {

					$taxable_address = wc()->customer->get_taxable_address();

					$location = array(
						'country'  => $taxable_address[0],
						'state'    => $taxable_address[1],
						'postcode' => $taxable_address[2],
						'city'     => $taxable_address[3],
					);
				}

				return $location;
			}
		);

		// Ensure cart is current.
		wc()->cart->calculate_shipping();
		wc()->cart->calculate_totals();

		// Update the current order to match the current cart.
		$this->update_line_items_from_cart( $order );
		$this->update_addresses_from_cart( $order );
		$order->set_currency( get_woocommerce_currency() );
		$order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
		$order->set_customer_id( get_current_user_id() );
		$order->set_customer_ip_address( \WC_Geolocation::get_ip_address() );
		$order->set_customer_user_agent( wc_get_user_agent() );
		$order->update_meta_data( 'is_vat_exempt', wc()->cart->get_customer()->get_is_vat_exempt() ? 'yes' : 'no' );
		$order->calculate_totals();
	}

	/**
	 * Copies order data to customer object (not the session), so values persist for future checkouts.
	 *
	 * @param \WC_Order $order Order object.
	 */
	public function sync_customer_data_with_order( \WC_Order $order ) {
		if ( $order->get_customer_id() ) {
			$customer = new \WC_Customer( $order->get_customer_id() );
			$customer->set_props(
				[
					'billing_first_name'  => $order->get_billing_first_name(),
					'billing_last_name'   => $order->get_billing_last_name(),
					'billing_company'     => $order->get_billing_company(),
					'billing_address_1'   => $order->get_billing_address_1(),
					'billing_address_2'   => $order->get_billing_address_2(),
					'billing_city'        => $order->get_billing_city(),
					'billing_state'       => $order->get_billing_state(),
					'billing_postcode'    => $order->get_billing_postcode(),
					'billing_country'     => $order->get_billing_country(),
					'billing_email'       => $order->get_billing_email(),
					'billing_phone'       => $order->get_billing_phone(),
					'shipping_first_name' => $order->get_shipping_first_name(),
					'shipping_last_name'  => $order->get_shipping_last_name(),
					'shipping_company'    => $order->get_shipping_company(),
					'shipping_address_1'  => $order->get_shipping_address_1(),
					'shipping_address_2'  => $order->get_shipping_address_2(),
					'shipping_city'       => $order->get_shipping_city(),
					'shipping_state'      => $order->get_shipping_state(),
					'shipping_postcode'   => $order->get_shipping_postcode(),
					'shipping_country'    => $order->get_shipping_country(),
					'shipping_phone'      => $order->get_shipping_phone(),
				]
			);

			$customer->save();
		};
	}

	/**
	 * Final validation ran before payment is taken.
	 *
	 * By this point we have an order populated with customer data and items.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 * @param \WC_Order $order Order object.
	 */
	public function validate_order_before_payment( \WC_Order $order ) {
		$needs_shipping          = wc()->cart->needs_shipping();
		$chosen_shipping_methods = wc()->session->get( 'chosen_shipping_methods' );

		$this->validate_coupons( $order );
		$this->validate_email( $order );
		$this->validate_selected_shipping_methods( $needs_shipping, $chosen_shipping_methods );
		$this->validate_addresses( $order );
	}

	/**
	 * Convert a coupon code to a coupon object.
	 *
	 * @param string $coupon_code Coupon code.
	 * @return \WC_Coupon Coupon object.
	 */
	protected function get_coupon( $coupon_code ) {
		return new \WC_Coupon( $coupon_code );
	}

	/**
	 * Validate coupons applied to the order and remove those that are not valid.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 * @param \WC_Order $order Order object.
	 */
	protected function validate_coupons( \WC_Order $order ) {
		$coupon_codes  = $order->get_coupon_codes();
		$coupons       = array_filter( array_map( [ $this, 'get_coupon' ], $coupon_codes ) );
		$validators    = [ 'validate_coupon_email_restriction', 'validate_coupon_usage_limit' ];
		$coupon_errors = [];

		foreach ( $coupons as $coupon ) {
			try {
				array_walk(
					$validators,
					function( $validator, $index, $params ) {
						call_user_func_array( [ $this, $validator ], $params );
					},
					[ $coupon, $order ]
				);
			} catch ( Exception $error ) {
				$coupon_errors[ $coupon->get_code() ] = $error->getMessage();
			}
		}

		if ( $coupon_errors ) {
			// Remove all coupons that were not valid.
			foreach ( $coupon_errors as $coupon_code => $message ) {
				wc()->cart->remove_coupon( $coupon_code );
			}

			// Recalculate totals.
			wc()->cart->calculate_totals();

			// Re-sync order with cart.
			$this->update_order_from_cart( $order );

			// Return exception so customer can review before payment.
			throw new RouteException(
				'woocommerce_rest_cart_coupon_errors',
				sprintf(
					/* translators: %s Coupon codes. */
					__( 'Invalid coupons were removed from the cart: "%s"', 'woocommerce' ),
					implode( '", "', array_keys( $coupon_errors ) )
				),
				409,
				[
					'removed_coupons' => $coupon_errors,
				]
			);
		}
	}

	/**
	 * Validates the customer email. This is a required field.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 * @param \WC_Order $order Order object.
	 */
	protected function validate_email( \WC_Order $order ) {
		$email = $order->get_billing_email();

		if ( empty( $email ) ) {
			throw new RouteException(
				'woocommerce_rest_missing_email_address',
				__( 'A valid email address is required', 'woocommerce' ),
				400
			);
		}

		if ( ! is_email( $email ) ) {
			throw new RouteException(
				'woocommerce_rest_invalid_email_address',
				sprintf(
					/* translators: %s provided email. */
					__( 'The provided email address (%s) is not valid—please provide a valid email address', 'woocommerce' ),
					esc_html( $email )
				),
				400
			);
		}
	}

	/**
	 * Validates customer address data based on the locale to ensure required fields are set.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 * @param \WC_Order $order Order object.
	 */
	protected function validate_addresses( \WC_Order $order ) {
		$errors           = new \WP_Error();
		$needs_shipping   = wc()->cart->needs_shipping();
		$billing_address  = $order->get_address( 'billing' );
		$shipping_address = $order->get_address( 'shipping' );

		if ( $needs_shipping && ! $this->validate_allowed_country( $shipping_address['country'], (array) wc()->countries->get_shipping_countries() ) ) {
			throw new RouteException(
				'woocommerce_rest_invalid_address_country',
				sprintf(
					/* translators: %s country code. */
					__( 'Sorry, we do not ship orders to the provided country (%s)', 'woocommerce' ),
					$shipping_address['country']
				),
				400,
				[
					'allowed_countries' => array_keys( wc()->countries->get_shipping_countries() ),
				]
			);
		}

		if ( ! $this->validate_allowed_country( $billing_address['country'], (array) wc()->countries->get_allowed_countries() ) ) {
			throw new RouteException(
				'woocommerce_rest_invalid_address_country',
				sprintf(
					/* translators: %s country code. */
					__( 'Sorry, we do not allow orders from the provided country (%s)', 'woocommerce' ),
					$billing_address['country']
				),
				400,
				[
					'allowed_countries' => array_keys( wc()->countries->get_allowed_countries() ),
				]
			);
		}

		if ( $needs_shipping ) {
			$this->validate_address_fields( $shipping_address, 'shipping', $errors );
		}
		$this->validate_address_fields( $billing_address, 'billing', $errors );

		if ( ! $errors->has_errors() ) {
			return;
		}

		$errors_by_code = [];
		$error_codes    = $errors->get_error_codes();
		foreach ( $error_codes as $code ) {
			$errors_by_code[ $code ] = $errors->get_error_messages( $code );
		}

		// Surface errors from first code.
		foreach ( $errors_by_code as $code => $error_messages ) {
			throw new RouteException(
				'woocommerce_rest_invalid_address',
				sprintf(
					/* translators: %s Address type. */
					__( 'There was a problem with the provided %s:', 'woocommerce' ) . ' ' . implode( ', ', $error_messages ),
					'shipping' === $code ? __( 'shipping address', 'woocommerce' ) : __( 'billing address', 'woocommerce' )
				),
				400,
				[
					'errors' => $errors_by_code,
				]
			);
		}
	}

	/**
	 * Check all required address fields are set and return errors if not.
	 *
	 * @param string $country Country code.
	 * @param array  $allowed_countries List of valid country codes.
	 * @return boolean True if valid.
	 */
	protected function validate_allowed_country( $country, array $allowed_countries ) {
		return array_key_exists( $country, $allowed_countries );
	}

	/**
	 * Check all required address fields are set and return errors if not.
	 *
	 * @param array     $address Address array.
	 * @param string    $address_type billing or shipping address, used in error messages.
	 * @param \WP_Error $errors Error object.
	 */
	protected function validate_address_fields( $address, $address_type, \WP_Error $errors ) {
		$all_locales    = wc()->countries->get_country_locale();
		$current_locale = isset( $all_locales[ $address['country'] ] ) ? $all_locales[ $address['country'] ] : [];

		/**
		 * We are not using wc()->counties->get_default_address_fields() here because that is filtered. Instead, this array
		 * is based on assets/js/base/components/cart-checkout/address-form/default-address-fields.js
		 */
		$address_fields = [
			'first_name' => [
				'label'    => __( 'First name', 'woocommerce' ),
				'required' => true,
			],
			'last_name'  => [
				'label'    => __( 'Last name', 'woocommerce' ),
				'required' => true,
			],
			'company'    => [
				'label'    => __( 'Company', 'woocommerce' ),
				'required' => false,
			],
			'address_1'  => [
				'label'    => __( 'Address', 'woocommerce' ),
				'required' => true,
			],
			'address_2'  => [
				'label'    => __( 'Apartment, suite, etc.', 'woocommerce' ),
				'required' => false,
			],
			'country'    => [
				'label'    => __( 'Country/Region', 'woocommerce' ),
				'required' => true,
			],
			'city'       => [
				'label'    => __( 'City', 'woocommerce' ),
				'required' => true,
			],
			'state'      => [
				'label'    => __( 'State/County', 'woocommerce' ),
				'required' => true,
			],
			'postcode'   => [
				'label'    => __( 'Postal code', 'woocommerce' ),
				'required' => true,
			],
		];

		if ( $current_locale ) {
			foreach ( $current_locale as $key => $field ) {
				if ( isset( $address_fields[ $key ] ) ) {
					$address_fields[ $key ]['label']    = isset( $field['label'] ) ? $field['label'] : $address_fields[ $key ]['label'];
					$address_fields[ $key ]['required'] = isset( $field['required'] ) ? $field['required'] : $address_fields[ $key ]['required'];
				}
			}
		}

		foreach ( $address_fields as $address_field_key => $address_field ) {
			if ( empty( $address[ $address_field_key ] ) && $address_field['required'] ) {
				/* translators: %s Field label. */
				$errors->add( $address_type, sprintf( __( '%s is required', 'woocommerce' ), $address_field['label'] ), $address_field_key );
			}
		}
	}

	/**
	 * Check email restrictions of a coupon against the order.
	 *
	 * @throws Exception Exception if invalid data is detected.
	 * @param \WC_Coupon $coupon Coupon object applied to the cart.
	 * @param \WC_Order  $order Order object.
	 */
	protected function validate_coupon_email_restriction( \WC_Coupon $coupon, \WC_Order $order ) {
		$restrictions = $coupon->get_email_restrictions();

		if ( ! empty( $restrictions ) && $order->get_billing_email() && ! wc()->cart->is_coupon_emails_allowed( [ $order->get_billing_email() ], $restrictions ) ) {
			throw new Exception( $coupon->get_coupon_error( \WC_Coupon::E_WC_COUPON_NOT_YOURS_REMOVED ) );
		}
	}

	/**
	 * Check usage restrictions of a coupon against the order.
	 *
	 * @throws Exception Exception if invalid data is detected.
	 * @param \WC_Coupon $coupon Coupon object applied to the cart.
	 * @param \WC_Order  $order Order object.
	 */
	protected function validate_coupon_usage_limit( \WC_Coupon $coupon, \WC_Order $order ) {
		$coupon_usage_limit = $coupon->get_usage_limit_per_user();

		if ( $coupon_usage_limit > 0 ) {
			$data_store  = $coupon->get_data_store();
			$usage_count = $order->get_customer_id() ? $data_store->get_usage_by_user_id( $coupon, $order->get_customer_id() ) : $data_store->get_usage_by_email( $coupon, $order->get_billing_email() );

			if ( $usage_count >= $coupon_usage_limit ) {
				throw new Exception( $coupon->get_coupon_error( \WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED ) );
			}
		}
	}

	/**
	 * Check there is a shipping method if it requires shipping.
	 *
	 * @throws RouteException Exception if invalid data is detected.
	 * @param boolean $needs_shipping Current order needs shipping.
	 * @param array   $chosen_shipping_methods Array of shipping methods.
	 */
	public function validate_selected_shipping_methods( $needs_shipping, $chosen_shipping_methods = array() ) {
		if ( ! $needs_shipping || ! is_array( $chosen_shipping_methods ) ) {
			return;
		}

		foreach ( $chosen_shipping_methods as $chosen_shipping_method ) {
			if ( false === $chosen_shipping_method ) {
				throw new RouteException(
					'woocommerce_rest_invalid_shipping_option',
					__( 'Sorry, this order requires a shipping option.', 'woocommerce' ),
					400,
					[]
				);
			}
		}
	}

	/**
	 * Changes default order status to draft for orders created via this API.
	 *
	 * @return string
	 */
	public function default_order_status() {
		return 'checkout-draft';
	}

	/**
	 * Create order line items.
	 *
	 * @param \WC_Order $order The order object to update.
	 */
	protected function update_line_items_from_cart( \WC_Order $order ) {
		$cart_controller = new CartController();
		$cart            = $cart_controller->get_cart_instance();
		$cart_hashes     = $cart_controller->get_cart_hashes();

		if ( $order->get_cart_hash() !== $cart_hashes['line_items'] ) {
			$order->set_cart_hash( $cart_hashes['line_items'] );
			$order->remove_order_items( 'line_item' );
			wc()->checkout->create_order_line_items( $order, $cart );
		}

		if ( $order->get_meta_data( '_shipping_hash' ) !== $cart_hashes['shipping'] ) {
			$order->update_meta_data( '_shipping_hash', $cart_hashes['shipping'] );
			$order->remove_order_items( 'shipping' );
			wc()->checkout->create_order_shipping_lines( $order, wc()->session->get( 'chosen_shipping_methods' ), wc()->shipping()->get_packages() );
		}

		if ( $order->get_meta_data( '_coupons_hash' ) !== $cart_hashes['coupons'] ) {
			$order->remove_order_items( 'coupon' );
			$order->update_meta_data( '_coupons_hash', $cart_hashes['coupons'] );
			wc()->checkout->create_order_coupon_lines( $order, $cart );
		}

		if ( $order->get_meta_data( '_fees_hash' ) !== $cart_hashes['fees'] ) {
			$order->update_meta_data( '_fees_hash', $cart_hashes['fees'] );
			$order->remove_order_items( 'fee' );
			wc()->checkout->create_order_fee_lines( $order, $cart );
		}

		if ( $order->get_meta_data( '_taxes_hash' ) !== $cart_hashes['taxes'] ) {
			$order->update_meta_data( '_taxes_hash', $cart_hashes['taxes'] );
			$order->remove_order_items( 'tax' );
			wc()->checkout->create_order_tax_lines( $order, $cart );
		}
	}

	/**
	 * Update address data from cart and/or customer session data.
	 *
	 * @param \WC_Order $order The order object to update.
	 */
	protected function update_addresses_from_cart( \WC_Order $order ) {
		$order->set_props(
			[
				'billing_first_name'  => wc()->customer->get_billing_first_name(),
				'billing_last_name'   => wc()->customer->get_billing_last_name(),
				'billing_company'     => wc()->customer->get_billing_company(),
				'billing_address_1'   => wc()->customer->get_billing_address_1(),
				'billing_address_2'   => wc()->customer->get_billing_address_2(),
				'billing_city'        => wc()->customer->get_billing_city(),
				'billing_state'       => wc()->customer->get_billing_state(),
				'billing_postcode'    => wc()->customer->get_billing_postcode(),
				'billing_country'     => wc()->customer->get_billing_country(),
				'billing_email'       => wc()->customer->get_billing_email(),
				'billing_phone'       => wc()->customer->get_billing_phone(),
				'shipping_first_name' => wc()->customer->get_shipping_first_name(),
				'shipping_last_name'  => wc()->customer->get_shipping_last_name(),
				'shipping_company'    => wc()->customer->get_shipping_company(),
				'shipping_address_1'  => wc()->customer->get_shipping_address_1(),
				'shipping_address_2'  => wc()->customer->get_shipping_address_2(),
				'shipping_city'       => wc()->customer->get_shipping_city(),
				'shipping_state'      => wc()->customer->get_shipping_state(),
				'shipping_postcode'   => wc()->customer->get_shipping_postcode(),
				'shipping_country'    => wc()->customer->get_shipping_country(),
				'shipping_phone'      => wc()->customer->get_shipping_phone(),
			]
		);
	}
}

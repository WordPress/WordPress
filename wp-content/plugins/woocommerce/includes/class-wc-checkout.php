<?php
/**
 * Checkout functionality
 *
 * The WooCommerce checkout class handles the checkout process, collecting user data and processing the payment.
 *
 * @package WooCommerce\Classes
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Checkout class.
 */
class WC_Checkout {

	/**
	 * The single instance of the class.
	 *
	 * @var WC_Checkout|null
	 */
	protected static $instance = null;

	/**
	 * Checkout fields are stored here.
	 *
	 * @var array|null
	 */
	protected $fields = null;

	/**
	 * Holds posted data for backwards compatibility.
	 *
	 * @var array
	 */
	protected $legacy_posted_data = array();

	/**
	 * Caches customer object. @see get_value.
	 *
	 * @var WC_Customer
	 */
	private $logged_in_customer = null;

	/**
	 * Gets the main WC_Checkout Instance.
	 *
	 * @since 2.1
	 * @static
	 * @return WC_Checkout Main instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			// Hook in actions once.
			add_action( 'woocommerce_checkout_billing', array( self::$instance, 'checkout_form_billing' ) );
			add_action( 'woocommerce_checkout_shipping', array( self::$instance, 'checkout_form_shipping' ) );

			/**
			 * Runs once when the WC_Checkout class is first instantiated.
			 *
			 * @since 3.0.0 or earlier
			 */
			do_action( 'woocommerce_checkout_init', self::$instance );
		}
		return self::$instance;
	}

	/**
	 * See if variable is set. Used to support legacy public variables which are no longer defined.
	 *
	 * @param string $key Key.
	 * @return bool
	 */
	public function __isset( $key ) {
		return in_array(
			$key,
			array(
				'enable_signup',
				'enable_guest_checkout',
				'must_create_account',
				'checkout_fields',
				'posted',
				'shipping_method',
				'payment_method',
				'customer_id',
				'shipping_methods',
			),
			true
		);
	}

	/**
	 * Sets the legacy public variables for backwards compatibility.
	 *
	 * @param string $key   Key.
	 * @param mixed  $value Value.
	 */
	public function __set( $key, $value ) {
		switch ( $key ) {
			case 'enable_signup':
				$bool_value = wc_string_to_bool( $value );

				if ( $bool_value !== $this->is_registration_enabled() ) {
					remove_filter( 'woocommerce_checkout_registration_enabled', '__return_true', 0 );
					remove_filter( 'woocommerce_checkout_registration_enabled', '__return_false', 0 );
					add_filter( 'woocommerce_checkout_registration_enabled', $bool_value ? '__return_true' : '__return_false', 0 );
				}
				break;
			case 'enable_guest_checkout':
				$bool_value = wc_string_to_bool( $value );

				if ( $bool_value === $this->is_registration_required() ) {
					remove_filter( 'woocommerce_checkout_registration_required', '__return_true', 0 );
					remove_filter( 'woocommerce_checkout_registration_required', '__return_false', 0 );
					add_filter( 'woocommerce_checkout_registration_required', $bool_value ? '__return_false' : '__return_true', 0 );
				}
				break;
			case 'checkout_fields':
				$this->fields = $value;
				break;
			case 'shipping_methods':
				WC()->session->set( 'chosen_shipping_methods', $value );
				break;
			case 'posted':
				$this->legacy_posted_data = $value;
				break;
		}
	}

	/**
	 * Gets the legacy public variables for backwards compatibility.
	 *
	 * @param string $key Key.
	 * @return array|string
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'posted', 'shipping_method', 'payment_method' ), true ) && empty( $this->legacy_posted_data ) ) {
			$this->legacy_posted_data = $this->get_posted_data();
		}

		switch ( $key ) {
			case 'enable_signup':
				return $this->is_registration_enabled();
			case 'enable_guest_checkout':
				return ! $this->is_registration_required();
			case 'must_create_account':
				return $this->is_registration_required() && ! is_user_logged_in();
			case 'checkout_fields':
				return $this->get_checkout_fields();
			case 'posted':
				wc_doing_it_wrong( 'WC_Checkout->posted', 'Use $_POST directly.', '3.0.0' );
				return $this->legacy_posted_data;
			case 'shipping_method':
				return $this->legacy_posted_data['shipping_method'];
			case 'payment_method':
				return $this->legacy_posted_data['payment_method'];
			case 'customer_id':
				/**
				 * Provides an opportunity to modify the customer ID associated with the current checkout process.
				 *
				 * @since 3.0.0 or earlier
				 *
				 * @param int The current user's ID (this may be 0 if no user is logged in).
				 */
				return apply_filters( 'woocommerce_checkout_customer_id', get_current_user_id() );
			case 'shipping_methods':
				return (array) WC()->session->get( 'chosen_shipping_methods' );
		}
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woocommerce' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce' ), '2.1' );
	}

	/**
	 * Is registration required to checkout?
	 *
	 * @since  3.0.0
	 * @return boolean
	 */
	public function is_registration_required() {
		/**
		 * Controls if registration is required in order for checkout to be completed.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $checkout_registration_required If customers must be registered to checkout.
		 */
		return apply_filters( 'woocommerce_checkout_registration_required', 'yes' !== get_option( 'woocommerce_enable_guest_checkout' ) );
	}

	/**
	 * Is registration enabled on the checkout page?
	 *
	 * @since  3.0.0
	 * @return boolean
	 */
	public function is_registration_enabled() {
		/**
		 * Determines if customer registration is enabled during checkout.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $checkout_registration_enabled If checkout registration is enabled.
		 */
		return apply_filters( 'woocommerce_checkout_registration_enabled', 'yes' === get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) );
	}

	/**
	 * Get an array of checkout fields.
	 *
	 * @param  string $fieldset to get.
	 * @return array
	 */
	public function get_checkout_fields( $fieldset = '' ) {
		if ( ! is_null( $this->fields ) ) {
			return $fieldset ? $this->fields[ $fieldset ] : $this->fields;
		}

		// Fields are based on billing/shipping country. Grab those values but ensure they are valid for the store before using.
		$billing_country   = $this->get_value( 'billing_country' );
		$billing_country   = empty( $billing_country ) ? WC()->countries->get_base_country() : $billing_country;
		$allowed_countries = WC()->countries->get_allowed_countries();

		if ( ! array_key_exists( $billing_country, $allowed_countries ) ) {
			$billing_country = current( array_keys( $allowed_countries ) );
		}

		$shipping_country  = $this->get_value( 'shipping_country' );
		$shipping_country  = empty( $shipping_country ) ? WC()->countries->get_base_country() : $shipping_country;
		$allowed_countries = WC()->countries->get_shipping_countries();

		if ( ! array_key_exists( $shipping_country, $allowed_countries ) ) {
			$shipping_country = current( array_keys( $allowed_countries ) );
		}

		$this->fields = array(
			'billing'  => WC()->countries->get_address_fields(
				$billing_country,
				'billing_'
			),
			'shipping' => WC()->countries->get_address_fields(
				$shipping_country,
				'shipping_'
			),
			'account'  => array(),
			'order'    => array(
				'order_comments' => array(
					'type'        => 'textarea',
					'class'       => array( 'notes' ),
					'label'       => __( 'Order notes', 'woocommerce' ),
					'placeholder' => esc_attr__(
						'Notes about your order, e.g. special notes for delivery.',
						'woocommerce'
					),
				),
			),
		);

		if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) {
			$this->fields['account']['account_username'] = array(
				'type'         => 'text',
				'label'        => __( 'Account username', 'woocommerce' ),
				'required'     => true,
				'placeholder'  => esc_attr__( 'Username', 'woocommerce' ),
				'autocomplete' => 'username',
			);
		}

		if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
			$this->fields['account']['account_password'] = array(
				'type'         => 'password',
				'label'        => __( 'Create account password', 'woocommerce' ),
				'required'     => true,
				'placeholder'  => esc_attr__( 'Password', 'woocommerce' ),
				'autocomplete' => 'new-password',
			);
		}

		/**
		 * Sets the fields used during checkout.
		 *
		 * @since 3.0.0 or earlier
		 *
		 * @param array[] $checkout_fields
		 */
		$this->fields = apply_filters( 'woocommerce_checkout_fields', $this->fields );

		foreach ( $this->fields as $field_type => $fields ) {
			// Sort each of the checkout field sections based on priority.
			uasort( $this->fields[ $field_type ], 'wc_checkout_fields_uasort_comparison' );

			// Add accessibility labels to fields that have placeholders.
			foreach ( $fields as $single_field_type => $field ) {
				if ( empty( $field['label'] ) && ! empty( $field['placeholder'] ) ) {
					$this->fields[ $field_type ][ $single_field_type ]['label']       = $field['placeholder'];
					$this->fields[ $field_type ][ $single_field_type ]['label_class'] = array( 'screen-reader-text' );
				}
			}
		}

		return $fieldset ? $this->fields[ $fieldset ] : $this->fields;
	}

	/**
	 * When we process the checkout, lets ensure cart items are rechecked to prevent checkout.
	 */
	public function check_cart_items() {
		/**
		 * Provides an opportunity to check cart items before checkout. This generally occurs during checkout validation.
		 *
		 * @see WC_Checkout::validate_checkout()
		 * @since 3.0.0 or earlier
		 */
		do_action( 'woocommerce_check_cart_items' );
	}

	/**
	 * Output the billing form.
	 */
	public function checkout_form_billing() {
		wc_get_template( 'checkout/form-billing.php', array( 'checkout' => $this ) );
	}

	/**
	 * Output the shipping form.
	 */
	public function checkout_form_shipping() {
		wc_get_template( 'checkout/form-shipping.php', array( 'checkout' => $this ) );
	}

	/**
	 * Create an order. Error codes:
	 *      520 - Cannot insert order into the database.
	 *      521 - Cannot get order after creation.
	 *      522 - Cannot update order.
	 *      525 - Cannot create line item.
	 *      526 - Cannot create fee item.
	 *      527 - Cannot create shipping item.
	 *      528 - Cannot create tax item.
	 *      529 - Cannot create coupon item.
	 *
	 * @throws Exception When checkout validation fails.
	 * @param  array $data Posted data.
	 * @return int|WP_ERROR
	 */
	public function create_order( $data ) {
		/**
		 * Gives plugins an opportunity to create a new order themselves.
		 *
		 * @since 3.0.0 or earlier
		 *
		 * @param int|null    $order_id Can be set to an order ID to short-circuit the default order creation process.
		 * @param WC_Checkout $checkout Reference to the current WC_Checkout instance.
		 */
		$order_id = apply_filters( 'woocommerce_create_order', null, $this );
		if ( $order_id ) {
			return $order_id;
		}

		try {
			$order_id           = absint( WC()->session->get( 'order_awaiting_payment' ) );
			$cart_hash          = WC()->cart->get_cart_hash();
			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
			$order              = $order_id ? wc_get_order( $order_id ) : null;

			/**
			 * If there is an order pending payment, we can resume it here so
			 * long as it has not changed. If the order has changed, i.e.
			 * different items or cost, create a new order. We use a hash to
			 * detect changes which is based on cart items + order total.
			 */
			if ( $order && $order->has_cart_hash( $cart_hash ) && $order->has_status( array( 'pending', 'failed' ) ) ) {
				/**
				 * Indicates that we are resuming checkout for an existing order (which is pending payment, and which
				 * has not changed since it was added to the current shopping session).
				 *
				 * @since 3.0.0 or earlier
				 *
				 * @param int $order_id The ID of the order being resumed.
				 */
				do_action( 'woocommerce_resume_order', $order_id );

				// Remove all items - we will re-add them later.
				$order->remove_order_items();
			} else {
				$order = new WC_Order();
			}

			$fields_prefix = array(
				'shipping' => true,
				'billing'  => true,
			);

			$shipping_fields = array(
				'shipping_method' => true,
				'shipping_total'  => true,
				'shipping_tax'    => true,
			);
			foreach ( $data as $key => $value ) {
				if ( is_callable( array( $order, "set_{$key}" ) ) ) {
					$order->{"set_{$key}"}( $value );
					// Store custom fields prefixed with wither shipping_ or billing_. This is for backwards compatibility with 2.6.x.
				} elseif ( isset( $fields_prefix[ current( explode( '_', $key ) ) ] ) ) {
					if ( ! isset( $shipping_fields[ $key ] ) ) {
						$order->update_meta_data( '_' . $key, $value );
					}
				}
			}

			$order->hold_applied_coupons( $data['billing_email'] );
			$order->set_created_via( 'checkout' );
			$order->set_cart_hash( $cart_hash );
			/**
			 * This action is documented in woocommerce/includes/class-wc-checkout.php
			 *
			 * @since 3.0.0 or earlier
			 */
			$order->set_customer_id( apply_filters( 'woocommerce_checkout_customer_id', get_current_user_id() ) );
			$order->set_currency( get_woocommerce_currency() );
			$order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
			$order->set_customer_ip_address( WC_Geolocation::get_ip_address() );
			$order->set_customer_user_agent( wc_get_user_agent() );
			$order->set_customer_note( isset( $data['order_comments'] ) ? $data['order_comments'] : '' );
			$order->set_payment_method( isset( $available_gateways[ $data['payment_method'] ] ) ? $available_gateways[ $data['payment_method'] ] : $data['payment_method'] );
			$this->set_data_from_cart( $order );

			/**
			 * Action hook to adjust order before save.
			 *
			 * @since 3.0.0
			 */
			do_action( 'woocommerce_checkout_create_order', $order, $data );

			// Save the order.
			$order_id = $order->save();

			/**
			 * Action hook fired after an order is created used to add custom meta to the order.
			 *
			 * @since 3.0.0
			 */
			do_action( 'woocommerce_checkout_update_order_meta', $order_id, $data );

			/**
			 * Action hook fired after an order is created.
			 *
			 * @since 4.3.0
			 */
			do_action( 'woocommerce_checkout_order_created', $order );

			return $order_id;
		} catch ( Exception $e ) {
			if ( $order && $order instanceof WC_Order ) {
				$order->get_data_store()->release_held_coupons( $order );
				/**
				 * Action hook fired when an order is discarded due to Exception.
				 *
				 * @since 4.3.0
				 */
				do_action( 'woocommerce_checkout_order_exception', $order );
			}
			return new WP_Error( 'checkout-error', $e->getMessage() );
		}
	}

	/**
	 * Copy line items, tax, totals data from cart to order.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @throws Exception When unable to create order.
	 */
	public function set_data_from_cart( &$order ) {
		$order_vat_exempt = WC()->cart->get_customer()->get_is_vat_exempt() ? 'yes' : 'no';
		$order->add_meta_data( 'is_vat_exempt', $order_vat_exempt, true );
		$order->set_shipping_total( WC()->cart->get_shipping_total() );
		$order->set_discount_total( WC()->cart->get_discount_total() );
		$order->set_discount_tax( WC()->cart->get_discount_tax() );
		$order->set_cart_tax( WC()->cart->get_cart_contents_tax() + WC()->cart->get_fee_tax() );
		$order->set_shipping_tax( WC()->cart->get_shipping_tax() );
		$order->set_total( WC()->cart->get_total( 'edit' ) );
		$this->create_order_line_items( $order, WC()->cart );
		$this->create_order_fee_lines( $order, WC()->cart );
		$this->create_order_shipping_lines( $order, WC()->session->get( 'chosen_shipping_methods' ), WC()->shipping()->get_packages() );
		$this->create_order_tax_lines( $order, WC()->cart );
		$this->create_order_coupon_lines( $order, WC()->cart );
	}
	/**
	 * Add line items to the order.
	 *
	 * @param WC_Order $order Order instance.
	 * @param WC_Cart  $cart  Cart instance.
	 */
	public function create_order_line_items( &$order, $cart ) {
		foreach ( $cart->get_cart() as $cart_item_key => $values ) {
			/**
			 * Filter hook to get initial item object.
			 *
			 * @since 3.1.0
			 */
			$item                       = apply_filters( 'woocommerce_checkout_create_order_line_item_object', new WC_Order_Item_Product(), $cart_item_key, $values, $order );
			$product                    = $values['data'];
			$item->legacy_values        = $values; // @deprecated 4.4.0 For legacy actions.
			$item->legacy_cart_item_key = $cart_item_key; // @deprecated 4.4.0 For legacy actions.
			$item->set_props(
				array(
					'quantity'     => $values['quantity'],
					'variation'    => $values['variation'],
					'subtotal'     => $values['line_subtotal'],
					'total'        => $values['line_total'],
					'subtotal_tax' => $values['line_subtotal_tax'],
					'total_tax'    => $values['line_tax'],
					'taxes'        => $values['line_tax_data'],
				)
			);

			if ( $product ) {
				$item->set_props(
					array(
						'name'         => $product->get_name(),
						'tax_class'    => $product->get_tax_class(),
						'product_id'   => $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(),
						'variation_id' => $product->is_type( 'variation' ) ? $product->get_id() : 0,
					)
				);
			}

			$item->set_backorder_meta();

			/**
			 * Action hook to adjust item before save.
			 *
			 * @since 3.0.0
			 */
			do_action( 'woocommerce_checkout_create_order_line_item', $item, $cart_item_key, $values, $order );

			// Add item to order and save.
			$order->add_item( $item );
		}
	}

	/**
	 * Add fees to the order.
	 *
	 * @param WC_Order $order Order instance.
	 * @param WC_Cart  $cart  Cart instance.
	 */
	public function create_order_fee_lines( &$order, $cart ) {
		foreach ( $cart->get_fees() as $fee_key => $fee ) {
			$item                 = new WC_Order_Item_Fee();
			$item->legacy_fee     = $fee; // @deprecated 4.4.0 For legacy actions.
			$item->legacy_fee_key = $fee_key; // @deprecated 4.4.0 For legacy actions.
			$item->set_props(
				array(
					'name'      => $fee->name,
					'tax_class' => $fee->taxable ? $fee->tax_class : 0,
					'amount'    => $fee->amount,
					'total'     => $fee->total,
					'total_tax' => $fee->tax,
					'taxes'     => array(
						'total' => $fee->tax_data,
					),
				)
			);

			/**
			 * Action hook to adjust item before save.
			 *
			 * @since 3.0.0
			 */
			do_action( 'woocommerce_checkout_create_order_fee_item', $item, $fee_key, $fee, $order );

			// Add item to order and save.
			$order->add_item( $item );
		}
	}

	/**
	 * Add shipping lines to the order.
	 *
	 * @param WC_Order $order                   Order Instance.
	 * @param array    $chosen_shipping_methods Chosen shipping methods.
	 * @param array    $packages                Packages.
	 */
	public function create_order_shipping_lines( &$order, $chosen_shipping_methods, $packages ) {
		foreach ( $packages as $package_key => $package ) {
			if ( isset( $chosen_shipping_methods[ $package_key ], $package['rates'][ $chosen_shipping_methods[ $package_key ] ] ) ) {
				$shipping_rate            = $package['rates'][ $chosen_shipping_methods[ $package_key ] ];
				$item                     = new WC_Order_Item_Shipping();
				$item->legacy_package_key = $package_key; // @deprecated 4.4.0 For legacy actions.
				$item->set_props(
					array(
						'method_title' => $shipping_rate->label,
						'method_id'    => $shipping_rate->method_id,
						'instance_id'  => $shipping_rate->instance_id,
						'total'        => wc_format_decimal( $shipping_rate->cost ),
						'taxes'        => array(
							'total' => $shipping_rate->taxes,
						),
					)
				);

				foreach ( $shipping_rate->get_meta_data() as $key => $value ) {
					$item->add_meta_data( $key, $value, true );
				}

				/**
				 * Action hook to adjust item before save.
				 *
				 * @since 3.0.0
				 */
				do_action( 'woocommerce_checkout_create_order_shipping_item', $item, $package_key, $package, $order );

				// Add item to order and save.
				$order->add_item( $item );
			}
		}
	}

	/**
	 * Add tax lines to the order.
	 *
	 * @param WC_Order $order Order instance.
	 * @param WC_Cart  $cart  Cart instance.
	 */
	public function create_order_tax_lines( &$order, $cart ) {
		foreach ( array_keys( $cart->get_cart_contents_taxes() + $cart->get_shipping_taxes() + $cart->get_fee_taxes() ) as $tax_rate_id ) {
			/**
			 * Controls the zero rate tax ID.
			 *
			 * An order item tax will not be created for this ID (which by default is 'zero-rated').
			 *
			 * @since 3.0.0 or earlier
			 *
			 * @param string $tax_rate_id The ID of the zero rate tax.
			 */
			if ( $tax_rate_id && apply_filters( 'woocommerce_cart_remove_taxes_zero_rate_id', 'zero-rated' ) !== $tax_rate_id ) {
				$item = new WC_Order_Item_Tax();
				$item->set_props(
					array(
						'rate_id'            => $tax_rate_id,
						'tax_total'          => $cart->get_tax_amount( $tax_rate_id ),
						'shipping_tax_total' => $cart->get_shipping_tax_amount( $tax_rate_id ),
						'rate_code'          => WC_Tax::get_rate_code( $tax_rate_id ),
						'label'              => WC_Tax::get_rate_label( $tax_rate_id ),
						'compound'           => WC_Tax::is_compound( $tax_rate_id ),
						'rate_percent'       => WC_Tax::get_rate_percent_value( $tax_rate_id ),
					)
				);

				/**
				 * Action hook to adjust item before save.
				 *
				 * @since 3.0.0
				 */
				do_action( 'woocommerce_checkout_create_order_tax_item', $item, $tax_rate_id, $order );

				// Add item to order and save.
				$order->add_item( $item );
			}
		}
	}

	/**
	 * Add coupon lines to the order.
	 *
	 * @param WC_Order $order Order instance.
	 * @param WC_Cart  $cart  Cart instance.
	 */
	public function create_order_coupon_lines( &$order, $cart ) {
		foreach ( $cart->get_coupons() as $code => $coupon ) {
			$item = new WC_Order_Item_Coupon();
			$item->set_props(
				array(
					'code'         => $code,
					'discount'     => $cart->get_coupon_discount_amount( $code ),
					'discount_tax' => $cart->get_coupon_discount_tax_amount( $code ),
				)
			);

			// Avoid storing used_by - it's not needed and can get large.
			$coupon_data = $coupon->get_data();
			unset( $coupon_data['used_by'] );
			$item->add_meta_data( 'coupon_data', $coupon_data );

			/**
			 * Action hook to adjust item before save.
			 *
			 * @since 3.0.0
			 */
			do_action( 'woocommerce_checkout_create_order_coupon_item', $item, $code, $coupon, $order );

			// Add item to order and save.
			$order->add_item( $item );
		}
	}

	/**
	 * See if a fieldset should be skipped.
	 *
	 * @since 3.0.0
	 * @param string $fieldset_key Fieldset key.
	 * @param array  $data         Posted data.
	 * @return bool
	 */
	protected function maybe_skip_fieldset( $fieldset_key, $data ) {
		if ( 'shipping' === $fieldset_key && ( ! $data['ship_to_different_address'] || ! WC()->cart->needs_shipping_address() ) ) {
			return true;
		}

		if ( 'account' === $fieldset_key && ( is_user_logged_in() || ( ! $this->is_registration_required() && empty( $data['createaccount'] ) ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get posted data from the checkout form.
	 *
	 * @since  3.1.0
	 * @return array of data.
	 */
	public function get_posted_data() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$data = array(
			'terms'                              => (int) isset( $_POST['terms'] ),
			'terms-field'                        => (int) isset( $_POST['terms-field'] ),
			'createaccount'                      => (int) ( $this->is_registration_enabled() ? ! empty( $_POST['createaccount'] ) : false ),
			'payment_method'                     => isset( $_POST['payment_method'] ) ? wc_clean( wp_unslash( $_POST['payment_method'] ) ) : '',
			'shipping_method'                    => isset( $_POST['shipping_method'] ) ? wc_clean( wp_unslash( $_POST['shipping_method'] ) ) : '',
			'ship_to_different_address'          => ! empty( $_POST['ship_to_different_address'] ) && ! wc_ship_to_billing_address_only(),
			'woocommerce_checkout_update_totals' => isset( $_POST['woocommerce_checkout_update_totals'] ),
		);
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$skipped = array();
		$form_was_shown = isset( $_POST['woocommerce-process-checkout-nonce'] ); // phpcs:disable WordPress.Security.NonceVerification.Missing

		foreach ( $this->get_checkout_fields() as $fieldset_key => $fieldset ) {
			if ( $this->maybe_skip_fieldset( $fieldset_key, $data ) ) {
				$skipped[] = $fieldset_key;
				continue;
			}

			foreach ( $fieldset as $key => $field ) {
				$type = sanitize_title( isset( $field['type'] ) ? $field['type'] : 'text' );

				if ( isset( $_POST[ $key ] ) && '' !== $_POST[ $key ] ) { // phpcs:disable WordPress.Security.NonceVerification.Missing
					$value = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				} elseif ( isset( $field['default'] ) && 'checkbox' !== $type && ! $form_was_shown ) {
					$value = $field['default'];
				} else {
					$value = '';
				}

				if ( '' !== $value ) {
					switch ( $type ) {
						case 'checkbox':
							$value = 1;
							break;
						case 'multiselect':
							$value = implode( ', ', wc_clean( $value ) );
							break;
						case 'textarea':
							$value = wc_sanitize_textarea( $value );
							break;
						case 'password':
							break;
						default:
							$value = wc_clean( $value );
							break;
					}
				}

				$data[ $key ] = apply_filters( 'woocommerce_process_checkout_' . $type . '_field', apply_filters( 'woocommerce_process_checkout_field_' . $key, $value ) );
			}
		}

		if ( in_array( 'shipping', $skipped, true ) && ( WC()->cart->needs_shipping_address() || wc_ship_to_billing_address_only() ) ) {
			foreach ( $this->get_checkout_fields( 'shipping' ) as $key => $field ) {
				$data[ $key ] = isset( $data[ 'billing_' . substr( $key, 9 ) ] ) ? $data[ 'billing_' . substr( $key, 9 ) ] : '';
			}
		}

		// BW compatibility.
		$this->legacy_posted_data = $data;

		return apply_filters( 'woocommerce_checkout_posted_data', $data );
	}

	/**
	 * Validates the posted checkout data based on field properties.
	 *
	 * @since  3.0.0
	 * @param  array    $data   An array of posted data.
	 * @param  WP_Error $errors Validation error.
	 */
	protected function validate_posted_data( &$data, &$errors ) {
		foreach ( $this->get_checkout_fields() as $fieldset_key => $fieldset ) {
			$validate_fieldset = true;
			if ( $this->maybe_skip_fieldset( $fieldset_key, $data ) ) {
				$validate_fieldset = false;
			}

			foreach ( $fieldset as $key => $field ) {
				if ( ! isset( $data[ $key ] ) ) {
					continue;
				}
				$required    = ! empty( $field['required'] );
				$format      = array_filter( isset( $field['validate'] ) ? (array) $field['validate'] : array() );
				$field_label = isset( $field['label'] ) ? $field['label'] : '';

				if ( $validate_fieldset &&
					( isset( $field['type'] ) && 'country' === $field['type'] && '' !== $data[ $key ] ) &&
					! WC()->countries->country_exists( $data[ $key ] ) ) {
						/* translators: ISO 3166-1 alpha-2 country code */
						$errors->add( $key . '_validation', sprintf( __( "'%s' is not a valid country code.", 'woocommerce' ), $data[ $key ] ) );
				}

				switch ( $fieldset_key ) {
					case 'shipping':
						/* translators: %s: field name */
						$field_label = sprintf( _x( 'Shipping %s', 'checkout-validation', 'woocommerce' ), $field_label );
						break;
					case 'billing':
						/* translators: %s: field name */
						$field_label = sprintf( _x( 'Billing %s', 'checkout-validation', 'woocommerce' ), $field_label );
						break;
				}

				if ( in_array( 'postcode', $format, true ) ) {
					$country      = isset( $data[ $fieldset_key . '_country' ] ) ? $data[ $fieldset_key . '_country' ] : WC()->customer->{"get_{$fieldset_key}_country"}();
					$data[ $key ] = wc_format_postcode( $data[ $key ], $country );

					if ( $validate_fieldset && '' !== $data[ $key ] && ! WC_Validation::is_postcode( $data[ $key ], $country ) ) {
						switch ( $country ) {
							case 'IE':
								/* translators: %1$s: field name, %2$s finder.eircode.ie URL */
								$postcode_validation_notice = sprintf( __( '%1$s is not valid. You can look up the correct Eircode <a target="_blank" href="%2$s">here</a>.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>', 'https://finder.eircode.ie' );
								break;
							default:
								/* translators: %s: field name */
								$postcode_validation_notice = sprintf( __( '%s is not a valid postcode / ZIP.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' );
						}
						$errors->add( $key . '_validation', apply_filters( 'woocommerce_checkout_postcode_validation_notice', $postcode_validation_notice, $country, $data[ $key ] ), array( 'id' => $key ) );
					}
				}

				if ( in_array( 'phone', $format, true ) ) {
					if ( $validate_fieldset && '' !== $data[ $key ] && ! WC_Validation::is_phone( $data[ $key ] ) ) {
						/* translators: %s: phone number */
						$errors->add( $key . '_validation', sprintf( __( '%s is not a valid phone number.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' ), array( 'id' => $key ) );
					}
				}

				if ( in_array( 'email', $format, true ) && '' !== $data[ $key ] ) {
					$email_is_valid = is_email( $data[ $key ] );
					$data[ $key ]   = sanitize_email( $data[ $key ] );

					if ( $validate_fieldset && ! $email_is_valid ) {
						/* translators: %s: email address */
						$errors->add( $key . '_validation', sprintf( __( '%s is not a valid email address.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' ), array( 'id' => $key ) );
						continue;
					}
				}

				if ( '' !== $data[ $key ] && in_array( 'state', $format, true ) ) {
					$country      = isset( $data[ $fieldset_key . '_country' ] ) ? $data[ $fieldset_key . '_country' ] : WC()->customer->{"get_{$fieldset_key}_country"}();
					$valid_states = WC()->countries->get_states( $country );

					if ( ! empty( $valid_states ) && is_array( $valid_states ) && count( $valid_states ) > 0 ) {
						$valid_state_values = array_map( 'wc_strtoupper', array_flip( array_map( 'wc_strtoupper', $valid_states ) ) );
						$data[ $key ]       = wc_strtoupper( $data[ $key ] );

						if ( isset( $valid_state_values[ $data[ $key ] ] ) ) {
							// With this part we consider state value to be valid as well, convert it to the state key for the valid_states check below.
							$data[ $key ] = $valid_state_values[ $data[ $key ] ];
						}

						if ( $validate_fieldset && ! in_array( $data[ $key ], $valid_state_values, true ) ) {
							/* translators: 1: state field 2: valid states */
							$errors->add( $key . '_validation', sprintf( __( '%1$s is not valid. Please enter one of the following: %2$s', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>', implode( ', ', $valid_states ) ), array( 'id' => $key ) );
						}
					}
				}

				if ( $validate_fieldset && $required && '' === $data[ $key ] ) {
					/* translators: %s: field name */
					$errors->add( $key . '_required', apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' ), $field_label, $key ), array( 'id' => $key ) );
				}
			}
		}
	}

	/**
	 * Validates that the checkout has enough info to proceed.
	 *
	 * @since  3.0.0
	 * @param  array    $data   An array of posted data.
	 * @param  WP_Error $errors Validation errors.
	 */
	protected function validate_checkout( &$data, &$errors ) {
		$this->validate_posted_data( $data, $errors );
		$this->check_cart_items();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( empty( $data['woocommerce_checkout_update_totals'] ) && empty( $data['terms'] ) && ! empty( $data['terms-field'] ) ) {
			$errors->add( 'terms', __( 'Please read and accept the terms and conditions to proceed with your order.', 'woocommerce' ) );
		}

		if ( WC()->cart->needs_shipping() ) {
			$shipping_country = isset( $data['shipping_country'] ) ? $data['shipping_country'] : WC()->customer->get_shipping_country();

			if ( empty( $shipping_country ) ) {
				$errors->add( 'shipping', __( 'Please enter an address to continue.', 'woocommerce' ) );
			} elseif ( ! in_array( $shipping_country, array_keys( WC()->countries->get_shipping_countries() ), true ) ) {
				if ( WC()->countries->country_exists( $shipping_country ) ) {
					/* translators: %s: shipping location (prefix e.g. 'to' + ISO 3166-1 alpha-2 country code) */
					$errors->add( 'shipping', sprintf( __( 'Unfortunately <strong>we do not ship %s</strong>. Please enter an alternative shipping address.', 'woocommerce' ), WC()->countries->shipping_to_prefix() . ' ' . $shipping_country ) );
				}
			} else {
				$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

				foreach ( WC()->shipping()->get_packages() as $i => $package ) {
					if ( ! isset( $chosen_shipping_methods[ $i ], $package['rates'][ $chosen_shipping_methods[ $i ] ] ) ) {
						$errors->add( 'shipping', __( 'No shipping method has been selected. Please double check your address, or contact us if you need any help.', 'woocommerce' ) );
					}
				}
			}
		}

		if ( WC()->cart->needs_payment() ) {
			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

			if ( ! isset( $available_gateways[ $data['payment_method'] ] ) ) {
				$errors->add( 'payment', __( 'Invalid payment method.', 'woocommerce' ) );
			} else {
				$available_gateways[ $data['payment_method'] ]->validate_fields();
			}
		}

		do_action( 'woocommerce_after_checkout_validation', $data, $errors );
	}

	/**
	 * Set address field for customer.
	 *
	 * @since 3.0.7
	 * @param string $field String to update.
	 * @param string $key   Field key.
	 * @param array  $data  Array of data to get the value from.
	 */
	protected function set_customer_address_fields( $field, $key, $data ) {
		$billing_value  = null;
		$shipping_value = null;

		if ( isset( $data[ "billing_{$field}" ] ) && is_callable( array( WC()->customer, "set_billing_{$field}" ) ) ) {
			$billing_value  = $data[ "billing_{$field}" ];
			$shipping_value = $data[ "billing_{$field}" ];
		}

		if ( isset( $data[ "shipping_{$field}" ] ) && is_callable( array( WC()->customer, "set_shipping_{$field}" ) ) ) {
			$shipping_value = $data[ "shipping_{$field}" ];
		}

		if ( ! is_null( $billing_value ) && is_callable( array( WC()->customer, "set_billing_{$field}" ) ) ) {
			WC()->customer->{"set_billing_{$field}"}( $billing_value );
		}

		if ( ! is_null( $shipping_value ) && is_callable( array( WC()->customer, "set_shipping_{$field}" ) ) ) {
			WC()->customer->{"set_shipping_{$field}"}( $shipping_value );
		}
	}

	/**
	 * Update customer and session data from the posted checkout data.
	 *
	 * @since 3.0.0
	 * @param array $data Posted data.
	 */
	protected function update_session( $data ) {
		// Update both shipping and billing to the passed billing address first if set.
		$address_fields = array(
			'first_name',
			'last_name',
			'company',
			'email',
			'phone',
			'address_1',
			'address_2',
			'city',
			'postcode',
			'state',
			'country',
		);

		array_walk( $address_fields, array( $this, 'set_customer_address_fields' ), $data );
		WC()->customer->save();

		// Update customer shipping and payment method to posted method.
		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

		if ( is_array( $data['shipping_method'] ) ) {
			foreach ( $data['shipping_method'] as $i => $value ) {
				$chosen_shipping_methods[ $i ] = $value;
			}
		}

		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
		WC()->session->set( 'chosen_payment_method', $data['payment_method'] );

		// Update cart totals now we have customer address.
		WC()->cart->calculate_totals();
	}


	/**
	 * Process an order that does require payment.
	 *
	 * @since 3.0.0
	 * @param int    $order_id       Order ID.
	 * @param string $payment_method Payment method.
	 */
	protected function process_order_payment( $order_id, $payment_method ) {
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

		if ( ! isset( $available_gateways[ $payment_method ] ) ) {
			return;
		}

		// Store Order ID in session so it can be re-used after payment failure.
		WC()->session->set( 'order_awaiting_payment', $order_id );

		// Process Payment.
		$result = $available_gateways[ $payment_method ]->process_payment( $order_id );

		// Redirect to success/confirmation/payment page.
		if ( isset( $result['result'] ) && 'success' === $result['result'] ) {
			$result['order_id'] = $order_id;

			$result = apply_filters( 'woocommerce_payment_successful_result', $result, $order_id );

			if ( ! wp_doing_ajax() ) {
				// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
				wp_redirect( $result['redirect'] );
				exit;
			}

			wp_send_json( $result );
		}
	}

	/**
	 * Process an order that doesn't require payment.
	 *
	 * @since 3.0.0
	 * @param int $order_id Order ID.
	 */
	protected function process_order_without_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		$order->payment_complete();
		wc_empty_cart();

		if ( ! wp_doing_ajax() ) {
			wp_safe_redirect(
				apply_filters( 'woocommerce_checkout_no_payment_needed_redirect', $order->get_checkout_order_received_url(), $order )
			);
			exit;
		}

		wp_send_json(
			array(
				'result'   => 'success',
				'redirect' => apply_filters( 'woocommerce_checkout_no_payment_needed_redirect', $order->get_checkout_order_received_url(), $order ),
			)
		);
	}

	/**
	 * Create a new customer account if needed.
	 *
	 * @throws Exception When not able to create customer.
	 * @param array $data Posted data.
	 */
	protected function process_customer( $data ) {
		/**
		 * This action is documented in woocommerce/includes/class-wc-checkout.php
		 *
		 * @since 3.0.0 or earlier
		 */
		$customer_id = apply_filters( 'woocommerce_checkout_customer_id', get_current_user_id() );

		if ( ! is_user_logged_in() && ( $this->is_registration_required() || ! empty( $data['createaccount'] ) ) ) {
			$username    = ! empty( $data['account_username'] ) ? $data['account_username'] : '';
			$password    = ! empty( $data['account_password'] ) ? $data['account_password'] : '';
			$customer_id = wc_create_new_customer(
				$data['billing_email'],
				$username,
				$password,
				array(
					'first_name' => ! empty( $data['billing_first_name'] ) ? $data['billing_first_name'] : '',
					'last_name'  => ! empty( $data['billing_last_name'] ) ? $data['billing_last_name'] : '',
				)
			);

			if ( is_wp_error( $customer_id ) ) {
				throw new Exception( $customer_id->get_error_message() );
			}

			wc_set_customer_auth_cookie( $customer_id );

			// As we are now logged in, checkout will need to refresh to show logged in data.
			WC()->session->set( 'reload_checkout', true );

			// Also, recalculate cart totals to reveal any role-based discounts that were unavailable before registering.
			WC()->cart->calculate_totals();
		}

		// On multisite, ensure user exists on current site, if not add them before allowing login.
		if ( $customer_id && is_multisite() && is_user_logged_in() && ! is_user_member_of_blog() ) {
			add_user_to_blog( get_current_blog_id(), $customer_id, 'customer' );
		}

		// Add customer info from other fields.
		if ( $customer_id && apply_filters( 'woocommerce_checkout_update_customer_data', true, $this ) ) {
			$customer = new WC_Customer( $customer_id );

			if ( ! empty( $data['billing_first_name'] ) && '' === $customer->get_first_name() ) {
				$customer->set_first_name( $data['billing_first_name'] );
			}

			if ( ! empty( $data['billing_last_name'] ) && '' === $customer->get_last_name() ) {
				$customer->set_last_name( $data['billing_last_name'] );
			}

			// If the display name is an email, update to the user's full name.
			if ( is_email( $customer->get_display_name() ) ) {
				$customer->set_display_name( $customer->get_first_name() . ' ' . $customer->get_last_name() );
			}

			foreach ( $data as $key => $value ) {
				// Use setters where available.
				if ( is_callable( array( $customer, "set_{$key}" ) ) ) {
					$customer->{"set_{$key}"}( $value );

					// Store custom fields prefixed with wither shipping_ or billing_.
				} elseif ( 0 === stripos( $key, 'billing_' ) || 0 === stripos( $key, 'shipping_' ) ) {
					$customer->update_meta_data( $key, $value );
				}
			}

			/**
			 * Action hook to adjust customer before save.
			 *
			 * @since 3.0.0
			 */
			do_action( 'woocommerce_checkout_update_customer', $customer, $data );

			$customer->save();
		}

		do_action( 'woocommerce_checkout_update_user_meta', $customer_id, $data );
	}

	/**
	 * If checkout failed during an AJAX call, send failure response.
	 */
	protected function send_ajax_failure_response() {
		if ( wp_doing_ajax() ) {
			// Only print notices if not reloading the checkout, otherwise they're lost in the page reload.
			if ( ! isset( WC()->session->reload_checkout ) ) {
				$messages = wc_print_notices( true );
			}

			$response = array(
				'result'   => 'failure',
				'messages' => isset( $messages ) ? $messages : '',
				'refresh'  => isset( WC()->session->refresh_totals ),
				'reload'   => isset( WC()->session->reload_checkout ),
			);

			unset( WC()->session->refresh_totals, WC()->session->reload_checkout );

			wp_send_json( $response );
		}
	}

	/**
	 * Process the checkout after the confirm order button is pressed.
	 *
	 * @throws Exception When validation fails.
	 */
	public function process_checkout() {
		try {
			$nonce_value    = wc_get_var( $_REQUEST['woocommerce-process-checkout-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // phpcs:ignore
			$expiry_message = sprintf(
				/* translators: %s: shop cart url */
				__( 'Sorry, your session has expired. <a href="%s" class="wc-backward">Return to shop</a>', 'woocommerce' ),
				esc_url( wc_get_page_permalink( 'shop' ) )
			);

			if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, 'woocommerce-process_checkout' ) ) {
				// If the cart is empty, the nonce check failed because of session expiry.
				if ( WC()->cart->is_empty() ) {
					throw new Exception( $expiry_message );
				}

				WC()->session->set( 'refresh_totals', true );
				throw new Exception( __( 'We were unable to process your order, please try again.', 'woocommerce' ) );
			}

			wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );
			wc_set_time_limit( 0 );

			do_action( 'woocommerce_before_checkout_process' );

			if ( WC()->cart->is_empty() ) {
				throw new Exception( $expiry_message );
			}

			do_action( 'woocommerce_checkout_process' );

			$errors      = new WP_Error();
			$posted_data = $this->get_posted_data();

			// Update session for customer and totals.
			$this->update_session( $posted_data );

			// Validate posted data and cart items before proceeding.
			$this->validate_checkout( $posted_data, $errors );

			foreach ( $errors->errors as $code => $messages ) {
				$data = $errors->get_error_data( $code );
				foreach ( $messages as $message ) {
					wc_add_notice( $message, 'error', $data );
				}
			}

			if ( empty( $posted_data['woocommerce_checkout_update_totals'] ) && 0 === wc_notice_count( 'error' ) ) {
				$this->process_customer( $posted_data );
				$order_id = $this->create_order( $posted_data );
				$order    = wc_get_order( $order_id );

				if ( is_wp_error( $order_id ) ) {
					throw new Exception( $order_id->get_error_message() );
				}

				if ( ! $order ) {
					throw new Exception( __( 'Unable to create order.', 'woocommerce' ) );
				}

				do_action( 'woocommerce_checkout_order_processed', $order_id, $posted_data, $order );

				/**
				 * Note that woocommerce_cart_needs_payment is only used in
				 * WC_Checkout::process_checkout() to keep backwards compatibility.
				 * Use woocommerce_order_needs_payment instead.
				 *
				 * Note that at this point you can't rely on the Cart Object anymore,
				 * since it could be empty see:
				 * https://github.com/woocommerce/woocommerce/issues/24631
				 */
				if ( apply_filters( 'woocommerce_cart_needs_payment', $order->needs_payment(), WC()->cart ) ) {
					$this->process_order_payment( $order_id, $posted_data['payment_method'] );
				} else {
					$this->process_order_without_payment( $order_id );
				}
			}
		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
		}
		$this->send_ajax_failure_response();
	}

	/**
	 * Get a posted address field after sanitization and validation.
	 *
	 * @param string $key  Field key.
	 * @param string $type Type of address. Available options: 'billing' or 'shipping'.
	 * @return string
	 */
	public function get_posted_address_data( $key, $type = 'billing' ) {
		if ( 'billing' === $type || false === $this->legacy_posted_data['ship_to_different_address'] ) {
			$return = isset( $this->legacy_posted_data[ 'billing_' . $key ] ) ? $this->legacy_posted_data[ 'billing_' . $key ] : '';
		} else {
			$return = isset( $this->legacy_posted_data[ 'shipping_' . $key ] ) ? $this->legacy_posted_data[ 'shipping_' . $key ] : '';
		}
		return $return;
	}

	/**
	 * Gets the value either from POST, or from the customer object. Sets the default values in checkout fields.
	 *
	 * @param string $input Name of the input we want to grab data for. e.g. billing_country.
	 * @return string The default value.
	 */
	public function get_value( $input ) {
		// If the form was posted, get the posted value. This will only tend to happen when JavaScript is disabled client side.
		if ( ! empty( $_POST[ $input ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return wc_clean( wp_unslash( $_POST[ $input ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		// Allow 3rd parties to short circuit the logic and return their own default value.
		$value = apply_filters( 'woocommerce_checkout_get_value', null, $input );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		/**
		 * For logged in customers, pull data from their account rather than the session which may contain incomplete data.
		 * Another reason is that WC sets shipping address to the billing address on the checkout updates unless the
		 * "ship to another address" box is checked. @see issue #20975.
		 */
		$customer_object = false;

		if ( is_user_logged_in() ) {
			// Load customer object, but keep it cached to avoid reloading it multiple times.
			if ( is_null( $this->logged_in_customer ) ) {
				$this->logged_in_customer = new WC_Customer( get_current_user_id(), true );
			}
			$customer_object = $this->logged_in_customer;
		}

		if ( ! $customer_object ) {
			$customer_object = WC()->customer;
		}

		if ( is_callable( array( $customer_object, "get_$input" ) ) ) {
			$value = $customer_object->{"get_$input"}();
		} elseif ( $customer_object->meta_exists( $input ) ) {
			$value = $customer_object->get_meta( $input, true );
		}

		if ( '' === $value ) {
			$value = null;
		}

		return apply_filters( 'default_checkout_' . $input, $value, $input );
	}
}

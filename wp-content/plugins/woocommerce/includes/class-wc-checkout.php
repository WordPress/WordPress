<?php
/**
 * Checkout
 *
 * The WooCommerce checkout class handles the checkout process, collecting user data and processing the payment.
 *
 * @class 		WC_Cart
 * @version		2.1.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Checkout {

	/** @var array Array of posted form data. */
	public $posted;

	/** @var array Array of fields to display on the checkout. */
	public $checkout_fields;

	/** @var bool Whether or not the user must create an account to checkout. */
	public $must_create_account;

	/** @var bool Whether or not signups are allowed. */
	public $enable_signup;

	/** @var object The shipping method being used. */
	private $shipping_method;

	/** @var WC_Payment_Gateway The payment gateway being used. */
	private $payment_method;

	/** @var int ID of customer. */
	private $customer_id;

	/**
	 * @var WooCommerce The single instance of the class
	 * @since 2.1
	 */
	protected static $_instance = null;

	/**
	 * Main WooCommerce Instance
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @see WC()
	 * @return Main WooCommerce instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
	}

	/**
	 * Constructor for the checkout class. Hooks in methods and defines checkout fields.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct () {
		add_action( 'woocommerce_checkout_billing', array( $this,'checkout_form_billing' ) );
		add_action( 'woocommerce_checkout_shipping', array( $this,'checkout_form_shipping' ) );

		$this->enable_signup         = get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) == 'yes' ? true : false;
		$this->enable_guest_checkout = get_option( 'woocommerce_enable_guest_checkout' ) == 'yes' ? true : false;
		$this->must_create_account   = $this->enable_guest_checkout || is_user_logged_in() ? false : true;

		// Define all Checkout fields
		$this->checkout_fields['billing'] 	= WC()->countries->get_address_fields( $this->get_value('billing_country'), 'billing_' );
		$this->checkout_fields['shipping'] 	= WC()->countries->get_address_fields( $this->get_value('shipping_country'), 'shipping_' );

		if ( get_option( 'woocommerce_registration_generate_username' ) == 'no' ) {
			$this->checkout_fields['account']['account_username'] = array(
				'type' 			=> 'text',
				'label' 		=> __( 'Account username', 'woocommerce' ),
				'required'      => true,
				'placeholder' 	=> _x( 'Username', 'placeholder', 'woocommerce' )
			);
		}

		if ( get_option( 'woocommerce_registration_generate_password' ) == 'no' ) {
			$this->checkout_fields['account']['account_password'] = array(
				'type' 				=> 'password',
				'label' 			=> __( 'Account password', 'woocommerce' ),
				'required'          => true,
				'placeholder' 		=> _x( 'Password', 'placeholder', 'woocommerce' )
			);
		}

		$this->checkout_fields['order']	= array(
			'order_comments' => array(
				'type' => 'textarea',
				'class' => array('notes'),
				'label' => __( 'Order Notes', 'woocommerce' ),
				'placeholder' => _x('Notes about your order, e.g. special notes for delivery.', 'placeholder', 'woocommerce')
				)
			);

		$this->checkout_fields = apply_filters( 'woocommerce_checkout_fields', $this->checkout_fields );

		do_action( 'woocommerce_checkout_init', $this );
	}


	/**
	 * Checkout process
	 */
	public function check_cart_items() {
		// When we process the checkout, lets ensure cart items are rechecked to prevent checkout
		do_action('woocommerce_check_cart_items');
	}


	/**
	 * Output the billing information form
	 *
	 * @access public
	 * @return void
	 */
	public function checkout_form_billing() {
		wc_get_template( 'checkout/form-billing.php', array( 'checkout' => $this ) );
	}


	/**
	 * Output the shipping information form
	 *
	 * @access public
	 * @return void
	 */
	public function checkout_form_shipping() {
		wc_get_template( 'checkout/form-shipping.php', array( 'checkout' => $this ) );
	}


	/**
	 * create_order function.
	 * @access public
	 * @throws Exception
	 * @return int
	 */
	public function create_order() {
		global $wpdb;

		// Give plugins the opportunity to create an order themselves
		$order_id = apply_filters( 'woocommerce_create_order', null, $this );

		if ( is_numeric( $order_id ) )
			return $order_id;

		// Create Order (send cart variable so we can record items and reduce inventory). Only create if this is a new order, not if the payment was rejected.
		$order_data = apply_filters( 'woocommerce_new_order_data', array(
			'post_type' 	=> 'shop_order',
			'post_title' 	=> sprintf( __( 'Order &ndash; %s', 'woocommerce' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'woocommerce' ) ) ),
			'post_status' 	=> 'publish',
			'ping_status'	=> 'closed',
			'post_excerpt' 	=> isset( $this->posted['order_comments'] ) ? $this->posted['order_comments'] : '',
			'post_author' 	=> 1,
			'post_password'	=> uniqid( 'order_' )	// Protects the post just in case
		) );

		// Insert or update the post data
		$create_new_order = true;

		if ( WC()->session->order_awaiting_payment > 0 ) {

			$order_id = absint( WC()->session->order_awaiting_payment );

			/* Check order is unpaid by getting its status */
			$terms        = wp_get_object_terms( $order_id, 'shop_order_status', array( 'fields' => 'slugs' ) );
			$order_status = isset( $terms[0] ) ? $terms[0] : 'pending';

			// Resume the unpaid order if its pending
			if ( get_post( $order_id ) && ( $order_status == 'pending' || $order_status == 'failed' ) ) {

				// Update the existing order as we are resuming it
				$create_new_order = false;
				$order_data['ID'] = $order_id;
				wp_update_post( $order_data );

				// Clear the old line items - we'll add these again in case they changed
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d )", $order_id ) );

				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d", $order_id ) );

				// Trigger an action for the resumed order
				do_action( 'woocommerce_resume_order', $order_id );
			}
		}

		if ( $create_new_order ) {
			$order_id = wp_insert_post( $order_data, true );

			if ( is_wp_error( $order_id ) )
				throw new Exception( 'Error: Unable to create order. Please try again.' );
			else
				do_action( 'woocommerce_new_order', $order_id );
		}

		// Store user data
		if ( $this->checkout_fields['billing'] )
			foreach ( $this->checkout_fields['billing'] as $key => $field ) {
				update_post_meta( $order_id, '_' . $key, $this->posted[ $key ] );

				if ( $this->customer_id && apply_filters( 'woocommerce_checkout_update_customer_data', true, $this ) )
					update_user_meta( $this->customer_id, $key, $this->posted[ $key ] );
			}

		if ( $this->checkout_fields['shipping'] && WC()->cart->needs_shipping() ) {
			foreach ( $this->checkout_fields['shipping'] as $key => $field ) {
				$postvalue = false;

				if ( $this->posted['ship_to_different_address'] == false ) {
					if ( isset( $this->posted[ str_replace( 'shipping_', 'billing_', $key ) ] ) ) {
						$postvalue = $this->posted[ str_replace( 'shipping_', 'billing_', $key ) ];
						update_post_meta( $order_id, '_' . $key, $postvalue );
					}
				} else {
					$postvalue = $this->posted[ $key ];
					update_post_meta( $order_id, '_' . $key, $postvalue );
				}

				// User
				if ( $postvalue && $this->customer_id && apply_filters( 'woocommerce_checkout_update_customer_data', true, $this ) )
					update_user_meta( $this->customer_id, $key, $postvalue );
			}
		}

		// Save any other user meta
		if ( $this->customer_id )
			do_action( 'woocommerce_checkout_update_user_meta', $this->customer_id, $this->posted );

		// Store the line items to the new/resumed order
		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

			$_product = $values['data'];

           	// Add line item
           	$item_id = wc_add_order_item( $order_id, array(
		 		'order_item_name' 		=> $_product->get_title(),
		 		'order_item_type' 		=> 'line_item'
		 	) );

		 	// Add line item meta
		 	if ( $item_id ) {
			 	wc_add_order_item_meta( $item_id, '_qty', apply_filters( 'woocommerce_stock_amount', $values['quantity'] ) );
			 	wc_add_order_item_meta( $item_id, '_tax_class', $_product->get_tax_class() );
			 	wc_add_order_item_meta( $item_id, '_product_id', $values['product_id'] );
			 	wc_add_order_item_meta( $item_id, '_variation_id', $values['variation_id'] );
			 	wc_add_order_item_meta( $item_id, '_line_subtotal', wc_format_decimal( $values['line_subtotal'] ) );
			 	wc_add_order_item_meta( $item_id, '_line_total', wc_format_decimal( $values['line_total'] ) );
			 	wc_add_order_item_meta( $item_id, '_line_tax', wc_format_decimal( $values['line_tax'] ) );
			 	wc_add_order_item_meta( $item_id, '_line_subtotal_tax', wc_format_decimal( $values['line_subtotal_tax'] ) );

			 	// Store variation data in meta so admin can view it
				if ( $values['variation'] && is_array( $values['variation'] ) ) {
					foreach ( $values['variation'] as $key => $value ) {
						$key = str_replace( 'attribute_', '', $key );
						wc_add_order_item_meta( $item_id, $key, $value );
					}
				}

			 	// Add line item meta for backorder status
			 	if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $values['quantity'] ) ) {
			 		wc_add_order_item_meta( $item_id, apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce' ), $cart_item_key, $order_id ), $values['quantity'] - max( 0, $_product->get_total_stock() ) );
			 	}

			 	// Allow plugins to add order item meta
			 	do_action( 'woocommerce_add_order_item_meta', $item_id, $values, $cart_item_key );
		 	}
		}

		// Store fees
		foreach ( WC()->cart->get_fees() as $fee_key => $fee ) {
			$item_id = wc_add_order_item( $order_id, array(
		 		'order_item_name' 		=> $fee->name,
		 		'order_item_type' 		=> 'fee'
		 	) );

		 	if ( $fee->taxable )
		 		wc_add_order_item_meta( $item_id, '_tax_class', $fee->tax_class );
		 	else
		 		wc_add_order_item_meta( $item_id, '_tax_class', '0' );

		 	wc_add_order_item_meta( $item_id, '_line_total', wc_format_decimal( $fee->amount ) );
			wc_add_order_item_meta( $item_id, '_line_tax', wc_format_decimal( $fee->tax ) );
			
			// Allow plugins to add order item meta to fees
			do_action( 'woocommerce_add_order_fee_meta', $order_id, $item_id, $fee, $fee_key );
		}

		// Store shipping for all packages
		$packages = WC()->shipping->get_packages();

		foreach ( $packages as $i => $package ) {
			if ( isset( $package['rates'][ $this->shipping_methods[ $i ] ] ) ) {

				$method = $package['rates'][ $this->shipping_methods[ $i ] ];

				$item_id = wc_add_order_item( $order_id, array(
			 		'order_item_name' 		=> $method->label,
			 		'order_item_type' 		=> 'shipping'
			 	) );

				if ( $item_id ) {
			 		wc_add_order_item_meta( $item_id, 'method_id', $method->id );
		 			wc_add_order_item_meta( $item_id, 'cost', wc_format_decimal( $method->cost ) );
					do_action( 'woocommerce_add_shipping_order_item', $order_id, $item_id, $i );
		 		}
			}
		}

		// Store tax rows
		foreach ( array_keys( WC()->cart->taxes + WC()->cart->shipping_taxes ) as $key ) {
			$code = WC()->cart->tax->get_rate_code( $key );
			
			if ( $code ) {
				$item_id = wc_add_order_item( $order_id, array(
			 		'order_item_name' 		=> $code,
			 		'order_item_type' 		=> 'tax'
			 	) );

			 	// Add line item meta
			 	if ( $item_id ) {
			 		wc_add_order_item_meta( $item_id, 'rate_id', $key );
			 		wc_add_order_item_meta( $item_id, 'label', WC()->cart->tax->get_rate_label( $key ) );
				 	wc_add_order_item_meta( $item_id, 'compound', absint( WC()->cart->tax->is_compound( $key ) ? 1 : 0 ) );
				 	wc_add_order_item_meta( $item_id, 'tax_amount', wc_format_decimal( isset( WC()->cart->taxes[ $key ] ) ? WC()->cart->taxes[ $key ] : 0 ) );
				 	wc_add_order_item_meta( $item_id, 'shipping_tax_amount', wc_format_decimal( isset( WC()->cart->shipping_taxes[ $key ] ) ? WC()->cart->shipping_taxes[ $key ] : 0 ) );
				}
			}
		}

		// Store coupons
		if ( $applied_coupons = WC()->cart->get_coupons() ) {
			foreach ( $applied_coupons as $code => $coupon ) {

				$item_id = wc_add_order_item( $order_id, array(
			 		'order_item_name' 		=> $code,
			 		'order_item_type' 		=> 'coupon'
			 	) );

			 	// Add line item meta
			 	if ( $item_id ) {
			 		wc_add_order_item_meta( $item_id, 'discount_amount', isset( WC()->cart->coupon_discount_amounts[ $code ] ) ? WC()->cart->coupon_discount_amounts[ $code ] : 0 );
				}
			}
		}

		if ( $this->payment_method ) {
			update_post_meta( $order_id, '_payment_method', 		$this->payment_method->id );
			update_post_meta( $order_id, '_payment_method_title', 	$this->payment_method->get_title() );
		}
		if ( empty( $this->posted['billing_email'] ) && is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			update_post_meta( $order_id, '_billing_email', $current_user->user_email );
		}
		update_post_meta( $order_id, '_order_shipping', 		wc_format_decimal( WC()->cart->shipping_total ) );
		update_post_meta( $order_id, '_order_discount', 		wc_format_decimal( WC()->cart->get_order_discount_total() ) );
		update_post_meta( $order_id, '_cart_discount', 			wc_format_decimal( WC()->cart->get_cart_discount_total() ) );
		update_post_meta( $order_id, '_order_tax', 				wc_format_decimal( WC()->cart->tax_total ) );
		update_post_meta( $order_id, '_order_shipping_tax', 	wc_format_decimal( WC()->cart->shipping_tax_total ) );
		update_post_meta( $order_id, '_order_total', 			wc_format_decimal( WC()->cart->total, get_option( 'woocommerce_price_num_decimals' ) ) );

		update_post_meta( $order_id, '_order_key', 				'wc_' . apply_filters('woocommerce_generate_order_key', uniqid('order_') ) );
		update_post_meta( $order_id, '_customer_user', 			absint( $this->customer_id ) );
		update_post_meta( $order_id, '_order_currency', 		get_woocommerce_currency() );
		update_post_meta( $order_id, '_prices_include_tax', 	get_option( 'woocommerce_prices_include_tax' ) );
		update_post_meta( $order_id, '_customer_ip_address',	isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'] );
		update_post_meta( $order_id, '_customer_user_agent', 	isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '' );

		// Let plugins add meta
		do_action( 'woocommerce_checkout_update_order_meta', $order_id, $this->posted );

		// Order status
		wp_set_object_terms( $order_id, 'pending', 'shop_order_status' );

		return $order_id;
	}

	/**
	 * Process the checkout after the confirm order button is pressed
	 *
	 * @access public
	 * @return void
	 */
	public function process_checkout() {
		global $wpdb, $current_user;

		wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-process_checkout' );

		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) )
			define( 'WOOCOMMERCE_CHECKOUT', true );

		// Prevent timeout
		@set_time_limit(0);

		do_action( 'woocommerce_before_checkout_process' );

		if ( sizeof( WC()->cart->get_cart() ) == 0 )
			wc_add_notice( sprintf( __( 'Sorry, your session has expired. <a href="%s" class="wc-backward">Return to homepage</a>', 'woocommerce' ), home_url() ), 'error' );

		do_action( 'woocommerce_checkout_process' );

		// Checkout fields (not defined in checkout_fields)
		$this->posted['terms']                     = isset( $_POST['terms'] ) ? 1 : 0;
		$this->posted['createaccount']             = isset( $_POST['createaccount'] ) ? 1 : 0;
		$this->posted['payment_method']            = isset( $_POST['payment_method'] ) ? stripslashes( $_POST['payment_method'] ) : '';
		$this->posted['shipping_method']           = isset( $_POST['shipping_method'] ) ? $_POST['shipping_method'] : '';
		$this->posted['ship_to_different_address'] = isset( $_POST['ship_to_different_address'] ) ? true : false;

		if ( isset( $_POST['shiptobilling'] ) ) {
			_deprecated_argument( 'WC_Checkout::process_checkout()', '2.1', 'The "shiptobilling" field is deprecated. THe template files are out of date' );

			$this->posted['ship_to_different_address'] = $_POST['shiptobilling'] ? false : true;
		}

		// Ship to billing only option
		if ( WC()->cart->ship_to_billing_address_only() )
			$this->posted['ship_to_different_address']  = false;

		// Update customer shipping and payment method to posted method
		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

		if ( isset( $this->posted['shipping_method'] ) && is_array( $this->posted['shipping_method'] ) ) {
			foreach ( $this->posted['shipping_method'] as $i => $value ) {
				$chosen_shipping_methods[ $i ] = wc_clean( $value );
			}
		}

		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
		WC()->session->set( 'chosen_payment_method', $this->posted['payment_method'] );

		// Note if we skip shipping
		$skipped_shipping = false;

		// Get posted checkout_fields and do validation
		foreach ( $this->checkout_fields as $fieldset_key => $fieldset ) {

			// Skip shipping if not needed
			if ( $fieldset_key == 'shipping' && ( $this->posted['ship_to_different_address'] == false || ! WC()->cart->needs_shipping() ) ) {
				$skipped_shipping = true;
				continue;
			}

			// Ship account if not needed
			if ( $fieldset_key == 'account' && ( is_user_logged_in() || ( $this->must_create_account == false && empty( $this->posted['createaccount'] ) ) ) ) {
				continue;
			}

			foreach ( $fieldset as $key => $field ) {

				if ( ! isset( $field['type'] ) )
					$field['type'] = 'text';

				// Get Value
				switch ( $field['type'] ) {
					case "checkbox" :
						$this->posted[ $key ] = isset( $_POST[ $key ] ) ? 1 : 0;
					break;
					case "multiselect" :
						$this->posted[ $key ] = isset( $_POST[ $key ] ) ? implode( ', ', array_map( 'wc_clean', $_POST[ $key ] ) ) : '';
					break;
					case "textarea" :
						$this->posted[ $key ] = isset( $_POST[ $key ] ) ? wp_strip_all_tags( wp_check_invalid_utf8( stripslashes( $_POST[ $key ] ) ) ) : '';
					break;
					default :
						$this->posted[ $key ] = isset( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : '';
					break;
				}

				// Hooks to allow modification of value
				$this->posted[ $key ] = apply_filters( 'woocommerce_process_checkout_' . sanitize_title( $field['type'] ) . '_field', $this->posted[ $key ] );
				$this->posted[ $key ] = apply_filters( 'woocommerce_process_checkout_field_' . $key, $this->posted[ $key ] );

				// Validation: Required fields
				if ( isset( $field['required'] ) && $field['required'] && empty( $this->posted[ $key ] ) )
					wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is a required field.', 'woocommerce' ), 'error' );

				if ( ! empty( $this->posted[ $key ] ) ) {

					// Validation rules
					if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
						foreach ( $field['validate'] as $rule ) {
							switch ( $rule ) {
								case 'postcode' :
									$this->posted[ $key ] = strtoupper( str_replace( ' ', '', $this->posted[ $key ] ) );

									if ( ! WC_Validation::is_postcode( $this->posted[ $key ], $_POST[ $fieldset_key . '_country' ] ) ) :
										wc_add_notice( __( 'Please enter a valid postcode/ZIP.', 'woocommerce' ), 'error' );
									else :
										$this->posted[ $key ] = wc_format_postcode( $this->posted[ $key ], $_POST[ $fieldset_key . '_country' ] );
									endif;
								break;
								case 'phone' :
									$this->posted[ $key ] = wc_format_phone_number( $this->posted[ $key ] );

									if ( ! WC_Validation::is_phone( $this->posted[ $key ] ) )
										wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid phone number.', 'woocommerce' ), 'error' );
								break;
								case 'email' :
									$this->posted[ $key ] = strtolower( $this->posted[ $key ] );

									if ( ! is_email( $this->posted[ $key ] ) )
										wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', 'woocommerce' ), 'error' );
								break;
								case 'state' :
									// Get valid states
									$valid_states = WC()->countries->get_states( $_POST[ $fieldset_key . '_country' ] );
									if ( $valid_states )
										$valid_state_values = array_flip( array_map( 'strtolower', $valid_states ) );

									// Convert value to key if set
									if ( isset( $valid_state_values[ strtolower( $this->posted[ $key ] ) ] ) )
										 $this->posted[ $key ] = $valid_state_values[ strtolower( $this->posted[ $key ] ) ];

									// Only validate if the country has specific state options
									if ( $valid_states && sizeof( $valid_states ) > 0 )
										if ( ! in_array( $this->posted[ $key ], array_keys( $valid_states ) ) )
											wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not valid. Please enter one of the following:', 'woocommerce' ) . ' ' . implode( ', ', $valid_states ), 'error' );
								break;
							}
						}
					}
				}
			}
		}

		// Update customer location to posted location so we can correctly check available shipping methods
		if ( isset( $this->posted['billing_country'] ) )
			WC()->customer->set_country( $this->posted['billing_country'] );
		if ( isset( $this->posted['billing_state'] ) )
			WC()->customer->set_state( $this->posted['billing_state'] );
		if ( isset( $this->posted['billing_postcode'] ) )
			WC()->customer->set_postcode( $this->posted['billing_postcode'] );

		// Shipping Information
		if ( ! $skipped_shipping ) {

			// Update customer location to posted location so we can correctly check available shipping methods
			if ( isset( $this->posted['shipping_country'] ) )
				WC()->customer->set_shipping_country( $this->posted['shipping_country'] );
			if ( isset( $this->posted['shipping_state'] ) )
				WC()->customer->set_shipping_state( $this->posted['shipping_state'] );
			if ( isset( $this->posted['shipping_postcode'] ) )
				WC()->customer->set_shipping_postcode( $this->posted['shipping_postcode'] );

		} else {

			// Update customer location to posted location so we can correctly check available shipping methods
			if ( isset( $this->posted['billing_country'] ) )
				WC()->customer->set_shipping_country( $this->posted['billing_country'] );
			if ( isset( $this->posted['billing_state'] ) )
				WC()->customer->set_shipping_state( $this->posted['billing_state'] );
			if ( isset( $this->posted['billing_postcode'] ) )
				WC()->customer->set_shipping_postcode( $this->posted['billing_postcode'] );

		}

		// Update cart totals now we have customer address
		WC()->cart->calculate_totals();

		// Terms
		if ( ! isset( $_POST['woocommerce_checkout_update_totals'] ) && empty( $this->posted['terms'] ) && wc_get_page_id( 'terms' ) > 0 )
			wc_add_notice( __( 'You must accept our Terms &amp; Conditions.', 'woocommerce' ), 'error' );

		if ( WC()->cart->needs_shipping() ) {

			if ( ! in_array( WC()->customer->get_shipping_country(), array_keys( WC()->countries->get_shipping_countries() ) ) )
				wc_add_notice( sprintf( __( 'Unfortunately <strong>we do not ship to %s</strong>. Please enter an alternative shipping address.', 'woocommerce' ), WC()->countries->shipping_to_prefix() . ' ' . WC()->customer->get_shipping_country() ), 'error' );

			// Validate Shipping Methods
			$packages               = WC()->shipping->get_packages();
			$this->shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

			foreach ( $packages as $i => $package ) {
				if ( ! isset( $package['rates'][ $this->shipping_methods[ $i ] ] ) ) {
					wc_add_notice( __( 'Invalid shipping method.', 'woocommerce' ), 'error' );
					$this->shipping_methods[ $i ] = '';
				}
			}
		}

		if ( WC()->cart->needs_payment() ) {

			// Payment Method
			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

			if ( ! isset( $available_gateways[ $this->posted['payment_method'] ] ) ) {
				$this->payment_method = '';
				wc_add_notice( __( 'Invalid payment method.', 'woocommerce' ), 'error' );
			} else {
				$this->payment_method = $available_gateways[ $this->posted['payment_method'] ];
				$this->payment_method->validate_fields();
			}
		}

		// Action after validation
		do_action( 'woocommerce_after_checkout_validation', $this->posted );

		if ( ! isset( $_POST['woocommerce_checkout_update_totals'] ) && wc_notice_count( 'error' ) == 0 ) {

			try {

				// Customer accounts
				$this->customer_id = apply_filters( 'woocommerce_checkout_customer_id', get_current_user_id() );

				if ( ! is_user_logged_in() && ( $this->must_create_account || ! empty( $this->posted['createaccount'] ) ) ) {

					$username     = ! empty( $this->posted['account_username'] ) ? $this->posted['account_username'] : '';
					$password     = ! empty( $this->posted['account_password'] ) ? $this->posted['account_password'] : '';
					$new_customer = wc_create_new_customer( $this->posted['billing_email'], $username, $password );

                	if ( is_wp_error( $new_customer ) )
                		throw new Exception( $new_customer->get_error_message() );

                	$this->customer_id = $new_customer;

                	wc_set_customer_auth_cookie( $this->customer_id );

                	// As we are now logged in, checkout will need to refresh to show logged in data
                	WC()->session->set( 'reload_checkout', true );

                	// Add customer info from other billing fields
                	if ( $this->posted['billing_first_name'] && apply_filters( 'woocommerce_checkout_update_customer_data', true, $this ) ) {
                		$userdata = array( 
							'ID'           => $this->customer_id, 
							'first_name'   => $this->posted['billing_first_name'] ? $this->posted['billing_first_name'] : '', 
							'last_name'    => $this->posted['billing_last_name'] ? $this->posted['billing_last_name'] : '',
							'display_name' => $this->posted['billing_first_name'] ? $this->posted['billing_first_name'] : ''
                		);
                		wp_update_user( apply_filters( 'woocommerce_checkout_customer_userdata', $userdata, $this ) );
                	}
				}

				// Do a final stock check at this point
				$this->check_cart_items();

				// Abort if errors are present
				if ( wc_notice_count( 'error' ) > 0 )
					throw new Exception();

				$order_id = $this->create_order();

				do_action( 'woocommerce_checkout_order_processed', $order_id, $this->posted );

				// Process payment
				if ( WC()->cart->needs_payment() ) {

					// Store Order ID in session so it can be re-used after payment failure
					WC()->session->order_awaiting_payment = $order_id;

					// Process Payment
					$result = $available_gateways[ $this->posted['payment_method'] ]->process_payment( $order_id );

					// Redirect to success/confirmation/payment page
					if ( $result['result'] == 'success' ) {

						$result = apply_filters( 'woocommerce_payment_successful_result', $result, $order_id );

						if ( is_ajax() ) {
							echo '<!--WC_START-->' . json_encode( $result ) . '<!--WC_END-->';
							exit;
						} else {
							wp_redirect( $result['redirect'] );
							exit;
						}

					}

				} else {

					if ( empty( $order ) )
						$order = new WC_Order( $order_id );

					// No payment was required for order
					$order->payment_complete();

					// Empty the Cart
					WC()->cart->empty_cart();

					// Get redirect
					$return_url = $order->get_checkout_order_received_url();

					// Redirect to success/confirmation/payment page
					if ( is_ajax() ) {
						echo '<!--WC_START-->' . json_encode(
							array(
								'result' 	=> 'success',
								'redirect'  => apply_filters( 'woocommerce_checkout_no_payment_needed_redirect', $return_url, $order )
							)
						) . '<!--WC_END-->';
						exit;
					} else {
						wp_safe_redirect(
							apply_filters( 'woocommerce_checkout_no_payment_needed_redirect', $return_url, $order )
						);
						exit;
					}

				}

			} catch ( Exception $e ) {

				if ( ! empty( $e ) )
					wc_add_notice( $e->getMessage(), 'error' );

			}

		} // endif

		// If we reached this point then there were errors
		if ( is_ajax() ) {

			ob_start();
			wc_print_notices();
			$messages = ob_get_clean();

			echo '<!--WC_START-->' . json_encode(
				array(
					'result'	=> 'failure',
					'messages' 	=> $messages,
					'refresh' 	=> isset( WC()->session->refresh_totals ) ? 'true' : 'false',
					'reload'    => isset( WC()->session->reload_checkout ) ? 'true' : 'false'
				)
			) . '<!--WC_END-->';

			unset( WC()->session->refresh_totals, WC()->session->reload_checkout );
			exit;
		}
	}


	/**
	 * Gets the value either from the posted data, or from the users meta data
	 *
	 * @access public
	 * @param string $input
	 * @return string|null
	 */
	public function get_value( $input ) {
		if ( ! empty( $_POST[ $input ] ) ) {

			return wc_clean( $_POST[ $input ] );

		} else {

			$value = apply_filters( 'woocommerce_checkout_get_value', null, $input );

			if ( $value !== null )
				return $value;

			if ( is_user_logged_in() ) {

				$current_user = wp_get_current_user();

				if ( $meta = get_user_meta( $current_user->ID, $input, true ) )
					return $meta;

				if ( $input == "billing_email" )
					return $current_user->user_email;
			}

			switch ( $input ) {
				case "billing_country" :
					return apply_filters( 'default_checkout_country', WC()->customer->get_country() ? WC()->customer->get_country() : WC()->countries->get_base_country(), 'billing' );
				case "billing_state" :
					return apply_filters( 'default_checkout_state', WC()->customer->has_calculated_shipping() ? WC()->customer->get_state() : '', 'billing' );
				case "billing_postcode" :
					return apply_filters( 'default_checkout_postcode', WC()->customer->get_postcode() ? WC()->customer->get_postcode() : '', 'billing' );
				case "shipping_country" :
					return apply_filters( 'default_checkout_country', WC()->customer->get_shipping_country() ? WC()->customer->get_shipping_country() : WC()->countries->get_base_country(), 'shipping' );
				case "shipping_state" :
					return apply_filters( 'default_checkout_state', WC()->customer->has_calculated_shipping() ? WC()->customer->get_shipping_state() : '', 'shipping' );
				case "shipping_postcode" :
					return apply_filters( 'default_checkout_postcode', WC()->customer->get_shipping_postcode() ? WC()->customer->get_shipping_postcode() : '', 'shipping' );
				default :
					return apply_filters( 'default_checkout_' . $input, null, $input );
			}
		}
	}
}

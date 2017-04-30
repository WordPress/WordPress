<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handle frontend forms
 *
 * @class 		WC_Form_Handler
 * @version		2.1.0
 * @package		WooCommerce/Classes/
 * @category	Class
 * @author 		WooThemes
 */
class WC_Form_Handler {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'save_address' ) );
		add_action( 'template_redirect', array( $this, 'save_account_details' ) );

		add_action( 'init', array( $this, 'checkout_action' ), 20 );
		add_action( 'init', array( $this, 'process_login' ) );
		add_action( 'init', array( $this, 'process_registration' ) );
		add_action( 'init', array( $this, 'process_reset_password' ) );

		add_action( 'init', array( $this, 'cancel_order' ) );
		add_action( 'init', array( $this, 'order_again' ) );

		add_action( 'init', array( $this, 'update_cart_action' ) );
		add_action( 'init', array( $this, 'add_to_cart_action' ) );

		add_action( 'wp', array( $this, 'pay_action' ), 20 );
		add_action( 'wp', array( $this, 'add_payment_method_action' ), 20 );
	}

	/**
	 * Save and and update a billing or shipping address if the
	 * form was submitted through the user account page.
	 */
	public function save_address() {
		global $woocommerce, $wp;

		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return;
		}

		if ( empty( $_POST[ 'action' ] ) || ( 'edit_address' !== $_POST[ 'action' ] ) || empty( $_POST['_wpnonce'] ) ) {
			return;
		}

		wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-edit_address' );

		$user_id = get_current_user_id();

		if ( $user_id <= 0 ) {
			return;
		}

		$load_address = isset( $wp->query_vars['edit-address'] ) ? sanitize_key( $wp->query_vars['edit-address'] ) : 'billing';

		$address = WC()->countries->get_address_fields( esc_attr( $_POST[ $load_address . '_country' ] ), $load_address . '_' );

		foreach ( $address as $key => $field ) {

			if ( ! isset( $field['type'] ) ) {
				$field['type'] = 'text';
			}

			// Get Value
			switch ( $field['type'] ) {
				case "checkbox" :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? 1 : 0;
				break;
				default :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : '';
				break;
			}

			// Hook to allow modification of value
			$_POST[ $key ] = apply_filters( 'woocommerce_process_myaccount_field_' . $key, $_POST[ $key ] );

			// Validation: Required fields
			if ( ! empty( $field['required'] ) && empty( $_POST[ $key ] ) ) {
				wc_add_notice( $field['label'] . ' ' . __( 'is a required field.', 'woocommerce' ), 'error' );
			}

			// Validation rules
			if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
				foreach ( $field['validate'] as $rule ) {
					switch ( $rule ) {
						case 'postcode' :
							$_POST[ $key ] = strtoupper( str_replace( ' ', '', $_POST[ $key ] ) );

							if ( ! WC_Validation::is_postcode( $_POST[ $key ], $_POST[ $load_address . '_country' ] ) ) {
								wc_add_notice( __( 'Please enter a valid postcode/ZIP.', 'woocommerce' ), 'error' );
							} else {
								$_POST[ $key ] = wc_format_postcode( $_POST[ $key ], $_POST[ $load_address . '_country' ] );
							}
						break;
						case 'phone' :
							$_POST[ $key ] = wc_format_phone_number( $_POST[ $key ] );

							if ( ! WC_Validation::is_phone( $_POST[ $key ] ) ) {
								wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid phone number.', 'woocommerce' ), 'error' );
							}
						break;
						case 'email' :
							$_POST[ $key ] = strtolower( $_POST[ $key ] );

							if ( ! is_email( $_POST[ $key ] ) ) {
								wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', 'woocommerce' ), 'error' );
							}
						break;
					}
				}
			}
		}

		if ( wc_notice_count( 'error' ) == 0 ) {

			foreach ( $address as $key => $field ) {
				update_user_meta( $user_id, $key, $_POST[ $key ] );
			}

			wc_add_notice( __( 'Address changed successfully.', 'woocommerce' ) );

			do_action( 'woocommerce_customer_save_address', $user_id, $load_address );

			wp_safe_redirect( get_permalink( wc_get_page_id('myaccount') ) );
			exit;
		}
	}

	/**
	 * Save the password/account details and redirect back to the my account page.
	 */
	public function save_account_details() {

		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return;
		}

		if ( empty( $_POST[ 'action' ] ) || ( 'save_account_details' !== $_POST[ 'action' ] ) || empty( $_POST['_wpnonce'] ) ) {
			return;
		}

		wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-save_account_details' );

		$update       = true;
		$errors       = new WP_Error();
		$user         = new stdClass();

		$user->ID     = (int) get_current_user_id();
		$current_user = get_user_by( 'id', $user->ID );

		if ( $user->ID <= 0 ) {
			return;
		}

		$account_first_name = ! empty( $_POST[ 'account_first_name' ] ) ? wc_clean( $_POST[ 'account_first_name' ] ) : '';
		$account_last_name  = ! empty( $_POST[ 'account_last_name' ] ) ? wc_clean( $_POST[ 'account_last_name' ] ) : '';
		$account_email      = ! empty( $_POST[ 'account_email' ] ) ? sanitize_email( $_POST[ 'account_email' ] ) : '';
		$pass1              = ! empty( $_POST[ 'password_1' ] ) ? $_POST[ 'password_1' ] : '';
		$pass2              = ! empty( $_POST[ 'password_2' ] ) ? $_POST[ 'password_2' ] : '';

		$user->first_name   = $account_first_name;
		$user->last_name    = $account_last_name;
		$user->user_email   = $account_email;
		$user->display_name = $user->first_name;

		if ( $pass1 ) {
			$user->user_pass = $pass1;
		}

		if ( empty( $account_first_name ) || empty( $account_last_name ) ) {
			wc_add_notice( __( 'Please enter your name.', 'woocommerce' ), 'error' );
		}

		if ( empty( $account_email ) || ! is_email( $account_email ) ) {
			wc_add_notice( __( 'Please provide a valid email address.', 'woocommerce' ), 'error' );
		} elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
			wc_add_notice( __( 'This email address is already registered.', 'woocommerce' ), 'error' );
		}

		if ( ! empty( $pass1 ) && empty( $pass2 ) ) {
			wc_add_notice( __( 'Please re-enter your password.', 'woocommerce' ), 'error' );
		} elseif ( ! empty( $pass1 ) && $pass1 !== $pass2 ) {
			wc_add_notice( __( 'Passwords do not match.', 'woocommerce' ), 'error' );
		}

		// Allow plugins to return their own errors.
		do_action_ref_array( 'user_profile_update_errors', array ( &$errors, $update, &$user ) );

		if ( $errors->get_error_messages() ) {
			foreach ( $errors->get_error_messages() as $error ) {
				wc_add_notice( $error, 'error' );
			}
		}

		if ( wc_notice_count( 'error' ) == 0 ) {

			wp_update_user( $user ) ;

			wc_add_notice( __( 'Account details changed successfully.', 'woocommerce' ) );

			do_action( 'woocommerce_save_account_details', $user->ID );

			wp_safe_redirect( get_permalink( wc_get_page_id( 'myaccount' ) ) );
			exit;
		}
	}

	/**
	 * Process the checkout form.
	 */
	public function checkout_action() {
		if ( isset( $_POST['woocommerce_checkout_place_order'] ) || isset( $_POST['woocommerce_checkout_update_totals'] ) ) {

			if ( sizeof( WC()->cart->get_cart() ) == 0 ) {
				wp_redirect( get_permalink( wc_get_page_id( 'cart' ) ) );
				exit;
			}

			if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
				define( 'WOOCOMMERCE_CHECKOUT', true );
			}

			$woocommerce_checkout = WC()->checkout();
			$woocommerce_checkout->process_checkout();
		}
	}

	/**
	 * Process the pay form.
	 */
	public function pay_action() {
		global $wp;

		if ( isset( $_POST['woocommerce_pay'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-pay' ) ) {

			ob_start();

			// Pay for existing order
			$order_key 	= $_GET['key'];
			$order_id 	= absint( $wp->query_vars['order-pay'] );
			$order 		= new WC_Order( $order_id );

			if ( $order->id == $order_id && $order->order_key == $order_key && in_array( $order->status, array( 'pending', 'failed' ) ) ) {

				// Set customer location to order location
				if ( $order->billing_country ) {
					WC()->customer->set_country( $order->billing_country );
				}
				if ( $order->billing_state ) {
					WC()->customer->set_state( $order->billing_state );
				}
				if ( $order->billing_postcode ) {
					WC()->customer->set_postcode( $order->billing_postcode );
				}
				if ( $order->billing_city ) {
					WC()->customer->set_city( $order->billing_city );
				}

				// Update payment method
				if ( $order->needs_payment() ) {
					$payment_method = wc_clean( $_POST['payment_method'] );

					$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

					// Update meta
					update_post_meta( $order_id, '_payment_method', $payment_method );

					if ( isset( $available_gateways[ $payment_method ] ) ) {
						$payment_method_title = $available_gateways[ $payment_method ]->get_title();
					}

					update_post_meta( $order_id, '_payment_method_title', $payment_method_title);

					// Validate
					$available_gateways[ $payment_method ]->validate_fields();

					// Process
					if ( wc_notice_count( 'error' ) == 0 ) {

						$result = $available_gateways[ $payment_method ]->process_payment( $order_id );

						// Redirect to success/confirmation/payment page
						if ( 'success' == $result['result'] ) {
							wp_redirect( $result['redirect'] );
							exit;
						}

					}

				} else {
					// No payment was required for order
					$order->payment_complete();
					wp_safe_redirect( $order->get_checkout_order_received_url() );
					exit;
				}

			}

		}
	}

	/**
	 * Process the add payment method form.
	 */
	public function add_payment_method_action() {
		global $wp;

		if ( isset( $_POST['woocommerce_add_payment_method'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-add-payment-method' ) ) {

			ob_start();

			$payment_method = wc_clean( $_POST['payment_method'] );

			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

			// Validate
			$available_gateways[ $payment_method ]->validate_fields();

			// Process
			if ( wc_error_count() == 0 ) {
				$result = $available_gateways[ $payment_method ]->add_payment_method();

				// Redirect to success/confirmation/payment page
				if ( $result['result'] == 'success' ) {
					wc_add_message( __( 'Payment method added.', 'woocommerce' ) );
					wp_redirect( $result['redirect'] );
					exit();
				}

			}

		}

	}

	/**
	 * Remove from cart/update.
	 */
	public function update_cart_action() {

		// Add Discount
		if ( ! empty( $_POST['apply_coupon'] ) && ! empty( $_POST['coupon_code'] ) ) {
			WC()->cart->add_discount( sanitize_text_field( $_POST['coupon_code'] ) );
		}

		// Remove Coupon Codes
		elseif ( isset( $_GET['remove_coupon'] ) ) {

			WC()->cart->remove_coupon( wc_clean( $_GET['remove_coupon'] ) );

		}

		// Remove from cart
		elseif ( ! empty( $_GET['remove_item'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'woocommerce-cart' ) ) {

			WC()->cart->set_quantity( $_GET['remove_item'], 0 );

			wc_add_notice( __( 'Cart updated.', 'woocommerce' ) );

			$referer = wp_get_referer() ? wp_get_referer() : WC()->cart->get_cart_url();
			wp_safe_redirect( $referer );
			exit;

		}

		// Update Cart - checks apply_coupon too because they are in the same form
		if ( ( ! empty( $_POST['apply_coupon'] ) || ! empty( $_POST['update_cart'] ) || ! empty( $_POST['proceed'] ) ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-cart' ) ) {

			$cart_updated = false;
			$cart_totals  = isset( $_POST['cart'] ) ? $_POST['cart'] : '';

			if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

					$_product = $values['data'];

					// Skip product if no updated quantity was posted
					if ( ! isset( $cart_totals[ $cart_item_key ]['qty'] ) ) {
						continue;
					}

					// Sanitize
					$quantity = apply_filters( 'woocommerce_stock_amount_cart_item', apply_filters( 'woocommerce_stock_amount', preg_replace( "/[^0-9\.]/", '', $cart_totals[ $cart_item_key ]['qty'] ) ), $cart_item_key );

					if ( '' === $quantity || $quantity == $values['quantity'] )
						continue;

					// Update cart validation
					$passed_validation 	= apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $values, $quantity );

					// is_sold_individually
					if ( $_product->is_sold_individually() && $quantity > 1 ) {
						wc_add_notice( sprintf( __( 'You can only have 1 %s in your cart.', 'woocommerce' ), $_product->get_title() ), 'error' );
						$passed_validation = false;
					}

					if ( $passed_validation ) {
						WC()->cart->set_quantity( $cart_item_key, $quantity, false );
					}

					$cart_updated = true;
				}
			}

			// Trigger action - let 3rd parties update the cart if they need to and update the $cart_updated variable
			$cart_updated = apply_filters( 'woocommerce_update_cart_action_cart_updated', $cart_updated );

			if ( $cart_updated ) {
				// Recalc our totals
				WC()->cart->calculate_totals();
			}

			if ( ! empty( $_POST['proceed'] ) ) {
				wp_safe_redirect( WC()->cart->get_checkout_url() );
				exit;
			} elseif ( $cart_updated ) {
				wc_add_notice( __( 'Cart updated.', 'woocommerce' ) );

				$referer = ( wp_get_referer() ) ? wp_get_referer() : WC()->cart->get_cart_url();
				$referer = remove_query_arg( 'remove_coupon', $referer );
				wp_safe_redirect( $referer );
				exit;
			}
		}
	}

	/**
	 * Place a previous order again.
	 */
	public function order_again() {

		// Nothing to do
		if ( ! isset( $_GET['order_again'] ) || ! is_user_logged_in() || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'woocommerce-order_again' ) ) {
			return;
		}

		// Clear current cart
		WC()->cart->empty_cart();

		// Load the previous order - Stop if the order does not exist
		$order = new WC_Order( absint( $_GET['order_again'] ) );

		if ( empty( $order->id ) ) {
			return;
		}

		if ( 'completed' != $order->status ) {
			return;
		}

		// Make sure the user is allowed to order again. By default it check if the
		// previous order belonged to the current user.
		if ( ! current_user_can( 'order_again', $order->id ) ) {
			return;
		}

		// Copy products from the order to the cart
		foreach ( $order->get_items() as $item ) {
			// Load all product info including variation data
			$product_id   = (int) apply_filters( 'woocommerce_add_to_cart_product_id', $item['product_id'] );
			$quantity     = (int) $item['qty'];
			$variation_id = (int) $item['variation_id'];
			$variations   = array();
			$cart_item_data = apply_filters( 'woocommerce_order_again_cart_item_data', array(), $item, $order );

			foreach ( $item['item_meta'] as $meta_name => $meta_value ) {
				if ( taxonomy_is_product_attribute( $meta_name ) ) {
					$variations[ $meta_name ] = $meta_value[0];
				} elseif ( meta_is_product_attribute( $meta_name, $meta_value, $product_id ) ) {
					$variations[ $meta_name ] = $meta_value[0];
				}
			}

			// Add to cart validation
			if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations, $cart_item_data ) ) {
				continue;
			}

			WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations, $cart_item_data );
		}

		do_action( 'woocommerce_ordered_again', $order->id );

		// Redirect to cart
		wc_add_notice( __( 'The cart has been filled with the items from your previous order.', 'woocommerce' ) );
		wp_safe_redirect( WC()->cart->get_cart_url() );
		exit;
	}

	/**
	 * Cancel a pending order.
	 */
	public function cancel_order() {
		if ( isset( $_GET['cancel_order'] ) && isset( $_GET['order'] ) && isset( $_GET['order_id'] ) ) {

			$order_key        = $_GET['order'];
			$order_id         = absint( $_GET['order_id'] );
			$order            = new WC_Order( $order_id );
			$user_can_cancel  = current_user_can( 'cancel_order', $order_id );
			$order_can_cancel = in_array( $order->status, apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ) ) );
			$redirect         = $_GET['redirect'];

			if ( $order->status == 'cancelled' ) {
				// Already cancelled - take no action
			} elseif ( $user_can_cancel && $order_can_cancel && $order->id == $order_id && $order->order_key == $order_key && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'woocommerce-cancel_order' ) ) {

				// Cancel the order + restore stock
				$order->cancel_order( __('Order cancelled by customer.', 'woocommerce' ) );

				// Message
				wc_add_notice( apply_filters( 'woocommerce_order_cancelled_notice', __( 'Your order was cancelled.', 'woocommerce' ) ), apply_filters( 'woocommerce_order_cancelled_notice_type', 'notice' ) );

				do_action( 'woocommerce_cancelled_order', $order->id );

			} elseif ( $user_can_cancel && ! $order_can_cancel ) {
				wc_add_notice( __( 'Your order can no longer be cancelled. Please contact us if you need assistance.', 'woocommerce' ), 'error' );
			} else {
				wc_add_notice( __( 'Invalid order.', 'woocommerce' ), 'error' );
			}

			if ( $redirect ) {
				wp_safe_redirect( $redirect );
				exit;
			}
		}
	}

	/**
	 * Add to cart action
	 *
	 * Checks for a valid request, does validation (via hooks) and then redirects if valid.
	 *
	 * @param bool $url (default: false)
	 */
	public function add_to_cart_action( $url = false ) {
		if ( empty( $_REQUEST['add-to-cart'] ) || ! is_numeric( $_REQUEST['add-to-cart'] ) ) {
			return;
		}

		$product_id          = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['add-to-cart'] ) );
		$was_added_to_cart   = false;
		$added_to_cart       = array();
		$adding_to_cart      = get_product( $product_id );
		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->product_type, $adding_to_cart );

		// Variable product handling
		if ( 'variable' === $add_to_cart_handler ) {

			$variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( $_REQUEST['variation_id'] );
			$quantity           = empty( $_REQUEST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_REQUEST['quantity'] );
			$all_variations_set = true;
			$variations         = array();

			// Only allow integer variation ID - if its not set, redirect to the product page
			if ( empty( $variation_id ) ) {
				wc_add_notice( __( 'Please choose product options&hellip;', 'woocommerce' ), 'error' );
				return;
			}

			$attributes = $adding_to_cart->get_attributes();
			$variation  = get_product( $variation_id );

			// Verify all attributes
			foreach ( $attributes as $attribute ) {
				if ( ! $attribute['is_variation'] ) {
					continue;
				}

				$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

				if ( isset( $_REQUEST[ $taxonomy ] ) ) {

					// Get value from post data
					// Don't use wc_clean as it destroys sanitized characters
					$value = sanitize_title( trim( stripslashes( $_REQUEST[ $taxonomy ] ) ) );

					// Get valid value from variation
					$valid_value = $variation->variation_data[ $taxonomy ];

					// Allow if valid
					if ( $valid_value == '' || $valid_value == $value ) {
						if ( $attribute['is_taxonomy'] ) {
							$variations[ $taxonomy ] = $value;
						}
						else {
							// For custom attributes, get the name from the slug
							$options = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
							foreach ( $options as $option ) {
								if ( sanitize_title( $option ) == $value ) {
									$value = $option;
									break;
								}
							}
							 $variations[ $taxonomy ] = $value;
						}
						continue;
					}

				}

				$all_variations_set = false;
			}

			if ( $all_variations_set ) {
				// Add to cart validation
				$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

				if ( $passed_validation ) {
					if ( WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
						wc_add_to_cart_message( $product_id );
						$was_added_to_cart = true;
						$added_to_cart[] = $product_id;
					}
				}
			} else {
				wc_add_notice( __( 'Please choose product options&hellip;', 'woocommerce' ), 'error' );
				return;
			}

		// Grouped Products
		} elseif ( 'grouped' === $add_to_cart_handler ) {

			if ( ! empty( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) ) {

				$quantity_set = false;

				foreach ( $_REQUEST['quantity'] as $item => $quantity ) {
					if ( $quantity <= 0 ) {
						continue;
					}

					$quantity_set = true;

					// Add to cart validation
					$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $item, $quantity );

					if ( $passed_validation ) {
						if ( WC()->cart->add_to_cart( $item, $quantity ) ) {
							$was_added_to_cart = true;
							$added_to_cart[] = $item;
						}
					}
				}

				if ( $was_added_to_cart ) {
					wc_add_to_cart_message( $added_to_cart );
				}

				if ( ! $was_added_to_cart && ! $quantity_set ) {
					wc_add_notice( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'woocommerce' ), 'error' );
					return;
				}

			} elseif ( $product_id ) {

				/* Link on product archives */
				wc_add_notice( __( 'Please choose a product to add to your cart&hellip;', 'woocommerce' ), 'error' );
				return;

			}

		// Simple Products
		} else {

			$quantity 			= empty( $_REQUEST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_REQUEST['quantity'] );

			// Add to cart validation
			$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

			if ( $passed_validation ) {
				// Add the product to the cart
				if ( WC()->cart->add_to_cart( $product_id, $quantity ) ) {
					wc_add_to_cart_message( $product_id );
					$was_added_to_cart = true;
					$added_to_cart[] = $product_id;
				}
			}

		}

		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && wc_notice_count( 'error' ) == 0 ) {

			$url = apply_filters( 'add_to_cart_redirect', $url );

			// If has custom URL redirect there
			if ( $url ) {
				wp_safe_redirect( $url );
				exit;
			}

			// Redirect to cart option
			elseif ( get_option('woocommerce_cart_redirect_after_add') == 'yes' ) {
				wp_safe_redirect( WC()->cart->get_cart_url() );
				exit;
			}

		}

	}

	/**
	 * Process the login form.
	 */
	public function process_login() {
		if ( ! empty( $_POST['login'] ) && ! empty( $_POST['_wpnonce'] ) ) {

			wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-login' );

			try {
				$creds  = array();

				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . $validation_error->get_error_message() );
				}

				if ( empty( $_POST['username'] ) ) {
					throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'Username is required.', 'woocommerce' ) );
				}

				if ( empty( $_POST['password'] ) ) {
					throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'Password is required.', 'woocommerce' ) );
				}

				if ( is_email( $_POST['username'] ) && apply_filters( 'woocommerce_get_username_from_email', true ) ) {
					$user = get_user_by( 'email', $_POST['username'] );

					if ( isset( $user->user_login ) ) {
						$creds['user_login'] 	= $user->user_login;
					} else {
						throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . __( 'A user could not be found with this email address.', 'woocommerce' ) );
					}

				} else {
					$creds['user_login'] 	= $_POST['username'];
				}

				$creds['user_password'] = $_POST['password'];
				$creds['remember']      = isset( $_POST['rememberme'] );
				$secure_cookie          = is_ssl() ? true : false;
				$user                   = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), $secure_cookie );

				if ( is_wp_error( $user ) ) {
					throw new Exception( $user->get_error_message() );
				} else {

					if ( ! empty( $_POST['redirect'] ) ) {
						$redirect = esc_url( $_POST['redirect'] );
					} elseif ( wp_get_referer() ) {
						$redirect = esc_url( wp_get_referer() );
					} else {
						$redirect = esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) );
					}

					// Feedback
					wc_add_notice( sprintf( __( 'You are now logged in as <strong>%s</strong>', 'woocommerce' ), $user->display_name ) );

					wp_redirect( apply_filters( 'woocommerce_login_redirect', $redirect, $user ) );
					exit;
				}

			} catch (Exception $e) {

				wc_add_notice( apply_filters('login_errors', $e->getMessage() ), 'error' );

			}
		}
	}

	/**
	 * Handle reset password form
	 */
	public function process_reset_password() {
		if ( ! isset( $_POST['wc_reset_password'] ) ) {
			return;
		}

		// process lost password form
		if ( isset( $_POST['user_login'] ) && isset( $_POST['_wpnonce'] ) ) {
			wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-lost_password' );

			WC_Shortcode_My_Account::retrieve_password();
		}

		// process reset password form
		if ( isset( $_POST['password_1'] ) && isset( $_POST['password_2'] ) && isset( $_POST['reset_key'] ) && isset( $_POST['reset_login'] ) && isset( $_POST['_wpnonce'] ) ) {

			// verify reset key again
			$user = WC_Shortcode_My_Account::check_password_reset_key( $_POST['reset_key'], $_POST['reset_login'] );

			if ( is_object( $user ) ) {

				// save these values into the form again in case of errors
				$args['key']   = wc_clean( $_POST['reset_key'] );
				$args['login'] = wc_clean( $_POST['reset_login'] );

				wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce-reset_password' );

				if ( empty( $_POST['password_1'] ) || empty( $_POST['password_2'] ) ) {
					wc_add_notice( __( 'Please enter your password.', 'woocommerce' ), 'error' );
					$args['form'] = 'reset_password';
				}

				if ( $_POST[ 'password_1' ] !== $_POST[ 'password_2' ] ) {
					wc_add_notice( __( 'Passwords do not match.', 'woocommerce' ), 'error' );
					$args['form'] = 'reset_password';
				}

				$errors = new WP_Error();
				do_action( 'validate_password_reset', $errors, $user );
				if ( $errors->get_error_messages() ) {
					foreach ( $errors->get_error_messages() as $error ) {
						wc_add_notice( $error, 'error');
					}
				}

				if ( 0 == wc_notice_count( 'error' ) ) {

					WC_Shortcode_My_Account::reset_password( $user, $_POST['password_1'] );

					do_action( 'woocommerce_customer_reset_password', $user );

					wp_redirect( add_query_arg( 'reset', 'true', remove_query_arg( array( 'key', 'login' ) ) ) );
					exit;
				}
			}

		}
	}

	/**
	 * Process the registration form.
	 */
	public function process_registration() {
		if ( ! empty( $_POST['register'] ) ) {

			wp_verify_nonce( $_POST['register'], 'woocommerce-register' );

			if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) {
				$_username = $_POST['username'];
			} else {
				$_username = '';
			}

			if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
				$_password = $_POST['password'];
			} else {
				$_password = '';
			}

			try {

				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $_username, $_password, $_POST['email'] );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( '<strong>' . __( 'Error', 'woocommerce' ) . ':</strong> ' . $validation_error->get_error_message() );
				}

			} catch ( Exception $e ) {

				wc_add_notice( $e->getMessage(), 'error' );
				return;

			}

			$username   = ! empty( $_username ) ? wc_clean( $_username ) : '';
			$email      = ! empty( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
			$password   = $_password;

			// Anti-spam trap
			if ( ! empty( $_POST['email_2'] ) ) {
				wc_add_notice( '<strong>' . __( 'ERROR', 'woocommerce' ) . '</strong>: ' . __( 'Anti-spam field was filled in.', 'woocommerce' ), 'error' );
				return;
			}

			$new_customer = wc_create_new_customer( $email, $username, $password );

			if ( is_wp_error( $new_customer ) ) {
				wc_add_notice( $new_customer->get_error_message(), 'error' );
				return;
			}

			wc_set_customer_auth_cookie( $new_customer );

			// Redirect
			if ( wp_get_referer() ) {
				$redirect = esc_url( wp_get_referer() );
			} else {
				$redirect = esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) );
			}

			wp_redirect( apply_filters( 'woocommerce_registration_redirect', $redirect ) );
			exit;
		}
	}
}

new WC_Form_Handler();

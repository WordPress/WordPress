<?php
/**
 * Handle frontend forms.
 *
 * @package WooCommerce\Classes\
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Form_Handler class.
 */
class WC_Form_Handler {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'redirect_reset_password_link' ) );
		add_action( 'template_redirect', array( __CLASS__, 'save_address' ) );
		add_action( 'template_redirect', array( __CLASS__, 'save_account_details' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'checkout_action' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_login' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_registration' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_lost_password' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_reset_password' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'cancel_order' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'update_cart_action' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'add_to_cart_action' ), 20 );

		// May need $wp global to access query vars.
		add_action( 'wp', array( __CLASS__, 'pay_action' ), 20 );
		add_action( 'wp', array( __CLASS__, 'add_payment_method_action' ), 20 );
		add_action( 'wp', array( __CLASS__, 'delete_payment_method_action' ), 20 );
		add_action( 'wp', array( __CLASS__, 'set_default_payment_method_action' ), 20 );
	}

	/**
	 * Remove key and user ID (or user login, as a fallback) from query string, set cookie, and redirect to account page to show the form.
	 */
	public static function redirect_reset_password_link() {
		if ( is_account_page() && isset( $_GET['key'] ) && ( isset( $_GET['id'] ) || isset( $_GET['login'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			// If available, get $user_id from query string parameter for fallback purposes.
			if ( isset( $_GET['login'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$user    = get_user_by( 'login', sanitize_user( wp_unslash( $_GET['login'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$user_id = $user ? $user->ID : 0;
			} else {
				$user_id = absint( $_GET['id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			// If the reset token is not for the current user, ignore the reset request (don't redirect).
			$logged_in_user_id = get_current_user_id();
			if ( $logged_in_user_id && $logged_in_user_id !== $user_id ) {
				wc_add_notice( __( 'This password reset key is for a different user account. Please log out and try again.', 'woocommerce' ), 'error' );
				return;
			}

			$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
			$value   = sprintf( '%d:%s', $user_id, wp_unslash( $_GET['key'] ) ); // phpcs:ignore
			WC_Shortcode_My_Account::set_reset_password_cookie( $value );
			wp_safe_redirect(
				add_query_arg(
					array(
						'show-reset-form' => 'true',
						'action'          => $action,
					),
					wc_lostpassword_url()
				)
			);
			exit;
		}
	}

	/**
	 * Save and and update a billing or shipping address if the
	 * form was submitted through the user account page.
	 */
	public static function save_address() {
		global $wp;

		$nonce_value = wc_get_var( $_REQUEST['woocommerce-edit-address-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-edit_address' ) ) {
			return;
		}

		if ( empty( $_POST['action'] ) || 'edit_address' !== $_POST['action'] ) {
			return;
		}

		wc_nocache_headers();

		$user_id = get_current_user_id();

		if ( $user_id <= 0 ) {
			return;
		}

		$customer = new WC_Customer( $user_id );

		if ( ! $customer ) {
			return;
		}

		$load_address = isset( $wp->query_vars['edit-address'] ) ? wc_edit_address_i18n( sanitize_title( $wp->query_vars['edit-address'] ), true ) : 'billing';

		if ( ! isset( $_POST[ $load_address . '_country' ] ) ) {
			return;
		}

		$address = WC()->countries->get_address_fields( wc_clean( wp_unslash( $_POST[ $load_address . '_country' ] ) ), $load_address . '_' );

		foreach ( $address as $key => $field ) {
			if ( ! isset( $field['type'] ) ) {
				$field['type'] = 'text';
			}

			// Get Value.
			if ( 'checkbox' === $field['type'] ) {
				$value = (int) isset( $_POST[ $key ] );
			} else {
				$value = isset( $_POST[ $key ] ) ? wc_clean( wp_unslash( $_POST[ $key ] ) ) : '';
			}

			// Hook to allow modification of value.
			$value = apply_filters( 'woocommerce_process_myaccount_field_' . $key, $value );

			// Validation: Required fields.
			if ( ! empty( $field['required'] ) && empty( $value ) ) {
				/* translators: %s: Field name. */
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), $field['label'] ), 'error', array( 'id' => $key ) );
			}

			if ( ! empty( $value ) ) {
				// Validation and formatting rules.
				if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
					foreach ( $field['validate'] as $rule ) {
						switch ( $rule ) {
							case 'postcode':
								$country = wc_clean( wp_unslash( $_POST[ $load_address . '_country' ] ) );
								$value   = wc_format_postcode( $value, $country );

								if ( '' !== $value && ! WC_Validation::is_postcode( $value, $country ) ) {
									switch ( $country ) {
										case 'IE':
											$postcode_validation_notice = __( 'Please enter a valid Eircode.', 'woocommerce' );
											break;
										default:
											$postcode_validation_notice = __( 'Please enter a valid postcode / ZIP.', 'woocommerce' );
									}
									wc_add_notice( $postcode_validation_notice, 'error' );
								}
								break;
							case 'phone':
								if ( '' !== $value && ! WC_Validation::is_phone( $value ) ) {
									/* translators: %s: Phone number. */
									wc_add_notice( sprintf( __( '%s is not a valid phone number.', 'woocommerce' ), '<strong>' . $field['label'] . '</strong>' ), 'error' );
								}
								break;
							case 'email':
								$value = strtolower( $value );

								if ( ! is_email( $value ) ) {
									/* translators: %s: Email address. */
									wc_add_notice( sprintf( __( '%s is not a valid email address.', 'woocommerce' ), '<strong>' . $field['label'] . '</strong>' ), 'error' );
								}
								break;
						}
					}
				}
			}

			try {
				// Set prop in customer object.
				if ( is_callable( array( $customer, "set_$key" ) ) ) {
					$customer->{"set_$key"}( $value );
				} else {
					$customer->update_meta_data( $key, $value );
				}
			} catch ( WC_Data_Exception $e ) {
				// Set notices. Ignore invalid billing email, since is already validated.
				if ( 'customer_invalid_billing_email' !== $e->getErrorCode() ) {
					wc_add_notice( $e->getMessage(), 'error' );
				}
			}
		}

		/**
		 * Hook: woocommerce_after_save_address_validation.
		 *
		 * Allow developers to add custom validation logic and throw an error to prevent save.
		 *
		 * @param int         $user_id User ID being saved.
		 * @param string      $load_address Type of address e.g. billing or shipping.
		 * @param array       $address The address fields.
		 * @param WC_Customer $customer The customer object being saved. @since 3.6.0
		 */
		do_action( 'woocommerce_after_save_address_validation', $user_id, $load_address, $address, $customer );

		if ( 0 < wc_notice_count( 'error' ) ) {
			return;
		}

		$customer->save();

		wc_add_notice( __( 'Address changed successfully.', 'woocommerce' ) );

		do_action( 'woocommerce_customer_save_address', $user_id, $load_address );

		wp_safe_redirect( wc_get_endpoint_url( 'edit-address', '', wc_get_page_permalink( 'myaccount' ) ) );
		exit;
	}

	/**
	 * Save the password/account details and redirect back to the my account page.
	 */
	public static function save_account_details() {
		$nonce_value = wc_get_var( $_REQUEST['save-account-details-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		if ( ! wp_verify_nonce( $nonce_value, 'save_account_details' ) ) {
			return;
		}

		if ( empty( $_POST['action'] ) || 'save_account_details' !== $_POST['action'] ) {
			return;
		}

		wc_nocache_headers();

		$user_id = get_current_user_id();

		if ( $user_id <= 0 ) {
			return;
		}

		$account_first_name   = ! empty( $_POST['account_first_name'] ) ? wc_clean( wp_unslash( $_POST['account_first_name'] ) ) : '';
		$account_last_name    = ! empty( $_POST['account_last_name'] ) ? wc_clean( wp_unslash( $_POST['account_last_name'] ) ) : '';
		$account_display_name = ! empty( $_POST['account_display_name'] ) ? wc_clean( wp_unslash( $_POST['account_display_name'] ) ) : '';
		$account_email        = ! empty( $_POST['account_email'] ) ? wc_clean( wp_unslash( $_POST['account_email'] ) ) : '';
		$pass_cur             = ! empty( $_POST['password_current'] ) ? $_POST['password_current'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$pass1                = ! empty( $_POST['password_1'] ) ? $_POST['password_1'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$pass2                = ! empty( $_POST['password_2'] ) ? $_POST['password_2'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$save_pass            = true;

		// Current user data.
		$current_user       = get_user_by( 'id', $user_id );
		$current_first_name = $current_user->first_name;
		$current_last_name  = $current_user->last_name;
		$current_email      = $current_user->user_email;

		// New user data.
		$user               = new stdClass();
		$user->ID           = $user_id;
		$user->first_name   = $account_first_name;
		$user->last_name    = $account_last_name;
		$user->display_name = $account_display_name;

		// Prevent display name to be changed to email.
		if ( is_email( $account_display_name ) ) {
			wc_add_notice( __( 'Display name cannot be changed to email address due to privacy concern.', 'woocommerce' ), 'error' );
		}

		// Handle required fields.
		$required_fields = apply_filters(
			'woocommerce_save_account_details_required_fields',
			array(
				'account_first_name'   => __( 'First name', 'woocommerce' ),
				'account_last_name'    => __( 'Last name', 'woocommerce' ),
				'account_display_name' => __( 'Display name', 'woocommerce' ),
				'account_email'        => __( 'Email address', 'woocommerce' ),
			)
		);

		foreach ( $required_fields as $field_key => $field_name ) {
			if ( empty( $_POST[ $field_key ] ) ) {
				/* translators: %s: Field name. */
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>' . esc_html( $field_name ) . '</strong>' ), 'error', array( 'id' => $field_key ) );
			}
		}

		if ( $account_email ) {
			$account_email = sanitize_email( $account_email );
			if ( ! is_email( $account_email ) ) {
				wc_add_notice( __( 'Please provide a valid email address.', 'woocommerce' ), 'error' );
			} elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
				wc_add_notice( __( 'This email address is already registered.', 'woocommerce' ), 'error' );
			}
			$user->user_email = $account_email;
		}

		if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
			wc_add_notice( __( 'Please fill out all password fields.', 'woocommerce' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
			wc_add_notice( __( 'Please enter your current password.', 'woocommerce' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
			wc_add_notice( __( 'Please re-enter your password.', 'woocommerce' ), 'error' );
			$save_pass = false;
		} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
			wc_add_notice( __( 'New passwords do not match.', 'woocommerce' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
			wc_add_notice( __( 'Your current password is incorrect.', 'woocommerce' ), 'error' );
			$save_pass = false;
		}

		if ( $pass1 && $save_pass ) {
			$user->user_pass = $pass1;
		}

		// Allow plugins to return their own errors.
		$errors = new WP_Error();
		do_action_ref_array( 'woocommerce_save_account_details_errors', array( &$errors, &$user ) );

		if ( $errors->get_error_messages() ) {
			foreach ( $errors->get_error_messages() as $error ) {
				wc_add_notice( $error, 'error' );
			}
		}

		if ( wc_notice_count( 'error' ) === 0 ) {
			wp_update_user( $user );

			// Update customer object to keep data in sync.
			$customer = new WC_Customer( $user->ID );

			if ( $customer ) {
				// Keep billing data in sync if data changed.
				if ( is_email( $user->user_email ) && $current_email !== $user->user_email ) {
					$customer->set_billing_email( $user->user_email );
				}

				if ( $current_first_name !== $user->first_name ) {
					$customer->set_billing_first_name( $user->first_name );
				}

				if ( $current_last_name !== $user->last_name ) {
					$customer->set_billing_last_name( $user->last_name );
				}

				$customer->save();
			}

			wc_add_notice( __( 'Account details changed successfully.', 'woocommerce' ) );

			do_action( 'woocommerce_save_account_details', $user->ID );

			wp_safe_redirect( wc_get_endpoint_url( 'edit-account', '', wc_get_page_permalink( 'myaccount' ) ) );
			exit;
		}
	}

	/**
	 * Process the checkout form.
	 */
	public static function checkout_action() {
		if ( isset( $_POST['woocommerce_checkout_place_order'] ) || isset( $_POST['woocommerce_checkout_update_totals'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wc_nocache_headers();

			if ( WC()->cart->is_empty() ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}

			wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

			WC()->checkout()->process_checkout();
		}
	}

	/**
	 * Process the pay form.
	 *
	 * @throws Exception On payment error.
	 */
	public static function pay_action() {
		global $wp;

		if ( isset( $_POST['woocommerce_pay'], $_GET['key'] ) ) {
			wc_nocache_headers();

			$nonce_value = wc_get_var( $_REQUEST['woocommerce-pay-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

			if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-pay' ) ) {
				return;
			}

			ob_start();

			// Pay for existing order.
			$order_key = wp_unslash( $_GET['key'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$order_id  = absint( $wp->query_vars['order-pay'] );
			$order     = wc_get_order( $order_id );

			if ( $order_id === $order->get_id() && hash_equals( $order->get_order_key(), $order_key ) && $order->needs_payment() ) {

				do_action( 'woocommerce_before_pay_action', $order );

				WC()->customer->set_props(
					array(
						'billing_country'  => $order->get_billing_country() ? $order->get_billing_country() : null,
						'billing_state'    => $order->get_billing_state() ? $order->get_billing_state() : null,
						'billing_postcode' => $order->get_billing_postcode() ? $order->get_billing_postcode() : null,
						'billing_city'     => $order->get_billing_city() ? $order->get_billing_city() : null,
					)
				);
				WC()->customer->save();

				if ( ! empty( $_POST['terms-field'] ) && empty( $_POST['terms'] ) ) {
					wc_add_notice( __( 'Please read and accept the terms and conditions to proceed with your order.', 'woocommerce' ), 'error' );
					return;
				}

				// Update payment method.
				if ( $order->needs_payment() ) {
					try {
						$payment_method_id = isset( $_POST['payment_method'] ) ? wc_clean( wp_unslash( $_POST['payment_method'] ) ) : false;

						if ( ! $payment_method_id ) {
							throw new Exception( __( 'Invalid payment method.', 'woocommerce' ) );
						}

						$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
						$payment_method     = isset( $available_gateways[ $payment_method_id ] ) ? $available_gateways[ $payment_method_id ] : false;

						if ( ! $payment_method ) {
							throw new Exception( __( 'Invalid payment method.', 'woocommerce' ) );
						}

						$order->set_payment_method( $payment_method );
						$order->save();

						$payment_method->validate_fields();

						if ( 0 === wc_notice_count( 'error' ) ) {

							$result = $payment_method->process_payment( $order_id );

							// Redirect to success/confirmation/payment page.
							if ( isset( $result['result'] ) && 'success' === $result['result'] ) {
								$result['order_id'] = $order_id;

								$result = apply_filters( 'woocommerce_payment_successful_result', $result, $order_id );

								wp_redirect( $result['redirect'] ); //phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
								exit;
							}
						}
					} catch ( Exception $e ) {
						wc_add_notice( $e->getMessage(), 'error' );
					}
				} else {
					// No payment was required for order.
					$order->payment_complete();
					wp_safe_redirect( $order->get_checkout_order_received_url() );
					exit;
				}

				do_action( 'woocommerce_after_pay_action', $order );

			}
		}
	}

	/**
	 * Process the add payment method form.
	 */
	public static function add_payment_method_action() {
		if ( isset( $_POST['woocommerce_add_payment_method'], $_POST['payment_method'] ) ) {
			wc_nocache_headers();

			$nonce_value = wc_get_var( $_REQUEST['woocommerce-add-payment-method-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

			if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-add-payment-method' ) ) {
				return;
			}

			if ( ! apply_filters( 'woocommerce_add_payment_method_form_is_valid', true ) ) {
				return;
			}

			// Test rate limit.
			$current_user_id = get_current_user_id();
			$rate_limit_id   = 'add_payment_method_' . $current_user_id;
			$delay           = (int) apply_filters( 'woocommerce_payment_gateway_add_payment_method_delay', 20 );

			if ( WC_Rate_Limiter::retried_too_soon( $rate_limit_id ) ) {
				wc_add_notice(
					sprintf(
						/* translators: %d number of seconds */
						_n(
							'You cannot add a new payment method so soon after the previous one. Please wait for %d second.',
							'You cannot add a new payment method so soon after the previous one. Please wait for %d seconds.',
							$delay,
							'woocommerce'
						),
						$delay
					),
					'error'
				);
				return;
			}

			WC_Rate_Limiter::set_rate_limit( $rate_limit_id, $delay );

			ob_start();

			$payment_method_id  = wc_clean( wp_unslash( $_POST['payment_method'] ) );
			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

			if ( isset( $available_gateways[ $payment_method_id ] ) ) {
				$gateway = $available_gateways[ $payment_method_id ];

				if ( ! $gateway->supports( 'add_payment_method' ) && ! $gateway->supports( 'tokenization' ) ) {
					wc_add_notice( __( 'Invalid payment gateway.', 'woocommerce' ), 'error' );
					return;
				}

				$gateway->validate_fields();

				if ( wc_notice_count( 'error' ) > 0 ) {
					return;
				}

				$result = $gateway->add_payment_method();

				if ( 'success' === $result['result'] ) {
					wc_add_notice( __( 'Payment method successfully added.', 'woocommerce' ) );
				}

				if ( 'failure' === $result['result'] ) {
					wc_add_notice( __( 'Unable to add payment method to your account.', 'woocommerce' ), 'error' );
				}

				if ( ! empty( $result['redirect'] ) ) {
					wp_redirect( $result['redirect'] ); //phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
					exit();
				}
			}
		}
	}

	/**
	 * Process the delete payment method form.
	 */
	public static function delete_payment_method_action() {
		global $wp;

		if ( isset( $wp->query_vars['delete-payment-method'] ) ) {
			wc_nocache_headers();

			$token_id = absint( $wp->query_vars['delete-payment-method'] );
			$token    = WC_Payment_Tokens::get( $token_id );

			if ( is_null( $token ) || get_current_user_id() !== $token->get_user_id() || ! isset( $_REQUEST['_wpnonce'] ) || false === wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), 'delete-payment-method-' . $token_id ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wc_add_notice( __( 'Invalid payment method.', 'woocommerce' ), 'error' );
			} else {
				WC_Payment_Tokens::delete( $token_id );
				wc_add_notice( __( 'Payment method deleted.', 'woocommerce' ) );
			}

			wp_safe_redirect( wc_get_account_endpoint_url( 'payment-methods' ) );
			exit();
		}

	}

	/**
	 * Process the delete payment method form.
	 */
	public static function set_default_payment_method_action() {
		global $wp;

		if ( isset( $wp->query_vars['set-default-payment-method'] ) ) {
			wc_nocache_headers();

			$token_id = absint( $wp->query_vars['set-default-payment-method'] );
			$token    = WC_Payment_Tokens::get( $token_id );

			if ( is_null( $token ) || get_current_user_id() !== $token->get_user_id() || ! isset( $_REQUEST['_wpnonce'] ) || false === wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), 'set-default-payment-method-' . $token_id ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wc_add_notice( __( 'Invalid payment method.', 'woocommerce' ), 'error' );
			} else {
				WC_Payment_Tokens::set_users_default( $token->get_user_id(), intval( $token_id ) );
				wc_add_notice( __( 'This payment method was successfully set as your default.', 'woocommerce' ) );
			}

			wp_safe_redirect( wc_get_account_endpoint_url( 'payment-methods' ) );
			exit();
		}

	}

	/**
	 * Remove from cart/update.
	 */
	public static function update_cart_action() {
		if ( ! ( isset( $_REQUEST['apply_coupon'] ) || isset( $_REQUEST['remove_coupon'] ) || isset( $_REQUEST['remove_item'] ) || isset( $_REQUEST['undo_item'] ) || isset( $_REQUEST['update_cart'] ) || isset( $_REQUEST['proceed'] ) ) ) {
			return;
		}

		wc_nocache_headers();

		$nonce_value = wc_get_var( $_REQUEST['woocommerce-cart-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		if ( ! empty( $_POST['apply_coupon'] ) && ! empty( $_POST['coupon_code'] ) ) {
			WC()->cart->add_discount( wc_format_coupon_code( wp_unslash( $_POST['coupon_code'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		} elseif ( isset( $_GET['remove_coupon'] ) ) {
			WC()->cart->remove_coupon( wc_format_coupon_code( urldecode( wp_unslash( $_GET['remove_coupon'] ) ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		} elseif ( ! empty( $_GET['remove_item'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) {
			$cart_item_key = sanitize_text_field( wp_unslash( $_GET['remove_item'] ) );
			$cart_item     = WC()->cart->get_cart_item( $cart_item_key );

			if ( $cart_item ) {
				WC()->cart->remove_cart_item( $cart_item_key );

				$product = wc_get_product( $cart_item['product_id'] );

				/* translators: %s: Item name. */
				$item_removed_title = apply_filters( 'woocommerce_cart_item_removed_title', $product ? sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), $product->get_name() ) : __( 'Item', 'woocommerce' ), $cart_item );

				// Don't show undo link if removed item is out of stock.
				if ( $product && $product->is_in_stock() && $product->has_enough_stock( $cart_item['quantity'] ) ) {
					/* Translators: %s Product title. */
					$removed_notice  = sprintf( __( '%s removed.', 'woocommerce' ), $item_removed_title );
					$removed_notice .= ' <a href="' . esc_url( wc_get_cart_undo_url( $cart_item_key ) ) . '" class="restore-item">' . __( 'Undo?', 'woocommerce' ) . '</a>';
				} else {
					/* Translators: %s Product title. */
					$removed_notice = sprintf( __( '%s removed.', 'woocommerce' ), $item_removed_title );
				}

				wc_add_notice( $removed_notice, apply_filters( 'woocommerce_cart_item_removed_notice_type', 'success' ) );
			}

			$referer = wp_get_referer() ? remove_query_arg( array( 'remove_item', 'add-to-cart', 'added-to-cart', 'order_again', '_wpnonce' ), add_query_arg( 'removed_item', '1', wp_get_referer() ) ) : wc_get_cart_url();
			wp_safe_redirect( $referer );
			exit;

		} elseif ( ! empty( $_GET['undo_item'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) {

			// Undo Cart Item.
			$cart_item_key = sanitize_text_field( wp_unslash( $_GET['undo_item'] ) );

			WC()->cart->restore_cart_item( $cart_item_key );

			$referer = wp_get_referer() ? remove_query_arg( array( 'undo_item', '_wpnonce' ), wp_get_referer() ) : wc_get_cart_url();
			wp_safe_redirect( $referer );
			exit;

		}

		// Update Cart - checks apply_coupon too because they are in the same form.
		if ( ( ! empty( $_POST['apply_coupon'] ) || ! empty( $_POST['update_cart'] ) || ! empty( $_POST['proceed'] ) ) && wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) {

			$cart_updated = false;
			$cart_totals  = isset( $_POST['cart'] ) ? wp_unslash( $_POST['cart'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( ! WC()->cart->is_empty() && is_array( $cart_totals ) ) {
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

					$_product = $values['data'];

					// Skip product if no updated quantity was posted.
					if ( ! isset( $cart_totals[ $cart_item_key ] ) || ! isset( $cart_totals[ $cart_item_key ]['qty'] ) ) {
						continue;
					}

					// Sanitize.
					$quantity = apply_filters( 'woocommerce_stock_amount_cart_item', wc_stock_amount( preg_replace( '/[^0-9\.]/', '', $cart_totals[ $cart_item_key ]['qty'] ) ), $cart_item_key );

					if ( '' === $quantity || $quantity === $values['quantity'] ) {
						continue;
					}

					// Update cart validation.
					$passed_validation = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $values, $quantity );

					// is_sold_individually.
					if ( $_product->is_sold_individually() && $quantity > 1 ) {
						/* Translators: %s Product title. */
						wc_add_notice( sprintf( __( 'You can only have 1 %s in your cart.', 'woocommerce' ), $_product->get_name() ), 'error' );
						$passed_validation = false;
					}

					if ( $passed_validation ) {
						WC()->cart->set_quantity( $cart_item_key, $quantity, false );
						$cart_updated = true;
					}
				}
			}

			// Trigger action - let 3rd parties update the cart if they need to and update the $cart_updated variable.
			$cart_updated = apply_filters( 'woocommerce_update_cart_action_cart_updated', $cart_updated );

			if ( $cart_updated ) {
				WC()->cart->calculate_totals();
			}

			if ( ! empty( $_POST['proceed'] ) ) {
				wp_safe_redirect( wc_get_checkout_url() );
				exit;
			} elseif ( $cart_updated ) {
				wc_add_notice( __( 'Cart updated.', 'woocommerce' ), apply_filters( 'woocommerce_cart_updated_notice_type', 'success' ) );
				$referer = remove_query_arg( array( 'remove_coupon', 'add-to-cart' ), ( wp_get_referer() ? wp_get_referer() : wc_get_cart_url() ) );
				wp_safe_redirect( $referer );
				exit;
			}
		}
	}

	/**
	 * Place a previous order again.
	 *
	 * @deprecated 3.5.0 Logic moved to cart session handling.
	 */
	public static function order_again() {
		wc_deprecated_function( 'WC_Form_Handler::order_again', '3.5', 'This method should not be called manually.' );
	}

	/**
	 * Cancel a pending order.
	 */
	public static function cancel_order() {
		if (
			isset( $_GET['cancel_order'] ) &&
			isset( $_GET['order'] ) &&
			isset( $_GET['order_id'] ) &&
			( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'woocommerce-cancel_order' ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		) {
			wc_nocache_headers();

			$order_key        = wp_unslash( $_GET['order'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$order_id         = absint( $_GET['order_id'] );
			$order            = wc_get_order( $order_id );
			$user_can_cancel  = current_user_can( 'cancel_order', $order_id );
			$order_can_cancel = $order->has_status( apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) );
			$redirect         = isset( $_GET['redirect'] ) ? wp_unslash( $_GET['redirect'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( $user_can_cancel && $order_can_cancel && $order->get_id() === $order_id && hash_equals( $order->get_order_key(), $order_key ) ) {

				// Cancel the order + restore stock.
				WC()->session->set( 'order_awaiting_payment', false );
				$order->update_status( 'cancelled', __( 'Order cancelled by customer.', 'woocommerce' ) );

				wc_add_notice( apply_filters( 'woocommerce_order_cancelled_notice', __( 'Your order was cancelled.', 'woocommerce' ) ), apply_filters( 'woocommerce_order_cancelled_notice_type', 'notice' ) );

				do_action( 'woocommerce_cancelled_order', $order->get_id() );

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
	 * Add to cart action.
	 *
	 * Checks for a valid request, does validation (via hooks) and then redirects if valid.
	 *
	 * @param bool $url (default: false) URL to redirect to.
	 */
	public static function add_to_cart_action( $url = false ) {
		if ( ! isset( $_REQUEST['add-to-cart'] ) || ! is_numeric( wp_unslash( $_REQUEST['add-to-cart'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		wc_nocache_headers();

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( wp_unslash( $_REQUEST['add-to-cart'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$was_added_to_cart = false;
		$adding_to_cart    = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			return;
		}

		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->get_type(), $adding_to_cart );

		if ( 'variable' === $add_to_cart_handler || 'variation' === $add_to_cart_handler ) {
			$was_added_to_cart = self::add_to_cart_handler_variable( $product_id );
		} elseif ( 'grouped' === $add_to_cart_handler ) {
			$was_added_to_cart = self::add_to_cart_handler_grouped( $product_id );
		} elseif ( has_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler ) ) {
			do_action( 'woocommerce_add_to_cart_handler_' . $add_to_cart_handler, $url ); // Custom handler.
		} else {
			$was_added_to_cart = self::add_to_cart_handler_simple( $product_id );
		}

		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && 0 === wc_notice_count( 'error' ) ) {
			$url = apply_filters( 'woocommerce_add_to_cart_redirect', $url, $adding_to_cart );

			if ( $url ) {
				wp_safe_redirect( $url );
				exit;
			} elseif ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		}
	}

	/**
	 * Handle adding simple products to the cart.
	 *
	 * @since 2.4.6 Split from add_to_cart_action.
	 * @param int $product_id Product ID to add to the cart.
	 * @return bool success or not
	 */
	private static function add_to_cart_handler_simple( $product_id ) {
		$quantity          = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_REQUEST['quantity'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
			return true;
		}
		return false;
	}

	/**
	 * Handle adding grouped products to the cart.
	 *
	 * @since 2.4.6 Split from add_to_cart_action.
	 * @param int $product_id Product ID to add to the cart.
	 * @return bool success or not
	 */
	private static function add_to_cart_handler_grouped( $product_id ) {
		$was_added_to_cart = false;
		$added_to_cart     = array();
		$items             = isset( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) ? wp_unslash( $_REQUEST['quantity'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! empty( $items ) ) {
			$quantity_set = false;

			foreach ( $items as $item => $quantity ) {
				$quantity = wc_stock_amount( $quantity );
				if ( $quantity <= 0 ) {
					continue;
				}
				$quantity_set = true;

				// Add to cart validation.
				$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $item, $quantity );

				// Suppress total recalculation until finished.
				remove_action( 'woocommerce_add_to_cart', array( WC()->cart, 'calculate_totals' ), 20, 0 );

				if ( $passed_validation && false !== WC()->cart->add_to_cart( $item, $quantity ) ) {
					$was_added_to_cart      = true;
					$added_to_cart[ $item ] = $quantity;
				}

				add_action( 'woocommerce_add_to_cart', array( WC()->cart, 'calculate_totals' ), 20, 0 );
			}

			if ( ! $was_added_to_cart && ! $quantity_set ) {
				wc_add_notice( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'woocommerce' ), 'error' );
			} elseif ( $was_added_to_cart ) {
				wc_add_to_cart_message( $added_to_cart );
				WC()->cart->calculate_totals();
				return true;
			}
		} elseif ( $product_id ) {
			/* Link on product archives */
			wc_add_notice( __( 'Please choose a product to add to your cart&hellip;', 'woocommerce' ), 'error' );
		}
		return false;
	}

	/**
	 * Handle adding variable products to the cart.
	 *
	 * @since 2.4.6 Split from add_to_cart_action.
	 * @throws Exception If add to cart fails.
	 * @param int $product_id Product ID to add to the cart.
	 * @return bool success or not
	 */
	private static function add_to_cart_handler_variable( $product_id ) {
		$variation_id = empty( $_REQUEST['variation_id'] ) ? '' : absint( wp_unslash( $_REQUEST['variation_id'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$quantity     = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_REQUEST['quantity'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$variations   = array();

		$product      = wc_get_product( $product_id );

		foreach ( $_REQUEST as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'attribute_' !== substr( $key, 0, 10 ) ) {
				continue;
			}

			$variations[ sanitize_title( wp_unslash( $key ) ) ] = wp_unslash( $value );
		}

		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

		if ( ! $passed_validation ) {
			return false;
		}

		// Prevent parent variable product from being added to cart.
		if ( empty( $variation_id ) && $product && $product->is_type( 'variable' ) ) {
			/* translators: 1: product link, 2: product name */
			wc_add_notice( sprintf( __( 'Please choose product options by visiting <a href="%1$s" title="%2$s">%2$s</a>.', 'woocommerce' ), esc_url( get_permalink( $product_id ) ), esc_html( $product->get_name() ) ), 'error' );

			return false;
		}

		if ( false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
			return true;
		}

		return false;
	}

	/**
	 * Process the login form.
	 *
	 * @throws Exception On login error.
	 */
	public static function process_login() {
		// The global form-login.php template used `_wpnonce` in template versions < 3.3.0.
		$nonce_value = wc_get_var( $_REQUEST['woocommerce-login-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		if ( isset( $_POST['login'], $_POST['username'], $_POST['password'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-login' ) ) {

			try {
				$creds = array(
					'user_login'    => trim( wp_unslash( $_POST['username'] ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					'user_password' => $_POST['password'], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
					'remember'      => isset( $_POST['rememberme'] ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				);

				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $creds['user_login'], $creds['user_password'] );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . $validation_error->get_error_message() );
				}

				if ( empty( $creds['user_login'] ) ) {
					throw new Exception( '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . __( 'Username is required.', 'woocommerce' ) );
				}

				// On multisite, ensure user exists on current site, if not add them before allowing login.
				if ( is_multisite() ) {
					$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

					if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
						add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
					}
				}

				// Perform the login.
				$user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );

				if ( is_wp_error( $user ) ) {
					throw new Exception( $user->get_error_message() );
				} else {

					if ( ! empty( $_POST['redirect'] ) ) {
						$redirect = wp_unslash( $_POST['redirect'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					} elseif ( wc_get_raw_referer() ) {
						$redirect = wc_get_raw_referer();
					} else {
						$redirect = wc_get_page_permalink( 'myaccount' );
					}

					wp_redirect( wp_validate_redirect( apply_filters( 'woocommerce_login_redirect', remove_query_arg( 'wc_error', $redirect ), $user ), wc_get_page_permalink( 'myaccount' ) ) ); // phpcs:ignore
					exit;
				}
			} catch ( Exception $e ) {
				wc_add_notice( apply_filters( 'login_errors', $e->getMessage() ), 'error' );
				do_action( 'woocommerce_login_failed' );
			}
		}
	}

	/**
	 * Handle lost password form.
	 */
	public static function process_lost_password() {
		if ( isset( $_POST['wc_reset_password'], $_POST['user_login'] ) ) {
			$nonce_value = wc_get_var( $_REQUEST['woocommerce-lost-password-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

			if ( ! wp_verify_nonce( $nonce_value, 'lost_password' ) ) {
				return;
			}

			$success = WC_Shortcode_My_Account::retrieve_password();

			// If successful, redirect to my account with query arg set.
			if ( $success ) {
				wp_safe_redirect( add_query_arg( 'reset-link-sent', 'true', wc_get_account_endpoint_url( 'lost-password' ) ) );
				exit;
			}
		}
	}

	/**
	 * Handle reset password form.
	 */
	public static function process_reset_password() {
		$nonce_value = wc_get_var( $_REQUEST['woocommerce-reset-password-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		if ( ! wp_verify_nonce( $nonce_value, 'reset_password' ) ) {
			return;
		}

		$posted_fields = array( 'wc_reset_password', 'password_1', 'password_2', 'reset_key', 'reset_login' );

		foreach ( $posted_fields as $field ) {
			if ( ! isset( $_POST[ $field ] ) ) {
				return;
			}

			if ( in_array( $field, array( 'password_1', 'password_2' ), true ) ) {
				// Don't unslash password fields
				// @see https://github.com/woocommerce/woocommerce/issues/23922.
				$posted_fields[ $field ] = $_POST[ $field ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			} else {
				$posted_fields[ $field ] = wp_unslash( $_POST[ $field ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			}
		}

		$user = WC_Shortcode_My_Account::check_password_reset_key( $posted_fields['reset_key'], $posted_fields['reset_login'] );

		if ( $user instanceof WP_User ) {
			if ( empty( $posted_fields['password_1'] ) ) {
				wc_add_notice( __( 'Please enter your password.', 'woocommerce' ), 'error' );
			}

			if ( $posted_fields['password_1'] !== $posted_fields['password_2'] ) {
				wc_add_notice( __( 'Passwords do not match.', 'woocommerce' ), 'error' );
			}

			$errors = new WP_Error();

			do_action( 'validate_password_reset', $errors, $user );

			wc_add_wp_error_notices( $errors );

			if ( 0 === wc_notice_count( 'error' ) ) {
				WC_Shortcode_My_Account::reset_password( $user, $posted_fields['password_1'] );

				do_action( 'woocommerce_customer_reset_password', $user );

				wp_safe_redirect( add_query_arg( 'password-reset', 'true', wc_get_page_permalink( 'myaccount' ) ) );
				exit;
			}
		}
	}

	/**
	 * Process the registration form.
	 *
	 * @throws Exception On registration error.
	 */
	public static function process_registration() {
		$nonce_value = isset( $_POST['_wpnonce'] ) ? wp_unslash( $_POST['_wpnonce'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$nonce_value = isset( $_POST['woocommerce-register-nonce'] ) ? wp_unslash( $_POST['woocommerce-register-nonce'] ) : $nonce_value; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( isset( $_POST['register'], $_POST['email'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {
			$username = 'no' === get_option( 'woocommerce_registration_generate_username' ) && isset( $_POST['username'] ) ? wp_unslash( $_POST['username'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$password = 'no' === get_option( 'woocommerce_registration_generate_password' ) && isset( $_POST['password'] ) ? $_POST['password'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$email    = wp_unslash( $_POST['email'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			try {
				$validation_error  = new WP_Error();
				$validation_error  = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $username, $password, $email );
				$validation_errors = $validation_error->get_error_messages();

				if ( 1 === count( $validation_errors ) ) {
					throw new Exception( $validation_error->get_error_message() );
				} elseif ( $validation_errors ) {
					foreach ( $validation_errors as $message ) {
						wc_add_notice( '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . $message, 'error' );
					}
					throw new Exception();
				}

				$new_customer = wc_create_new_customer( sanitize_email( $email ), wc_clean( $username ), $password );

				if ( is_wp_error( $new_customer ) ) {
					throw new Exception( $new_customer->get_error_message() );
				}

				if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) ) {
					wc_add_notice( __( 'Your account was created successfully and a password has been sent to your email address.', 'woocommerce' ) );
				} else {
					wc_add_notice( __( 'Your account was created successfully. Your login details have been sent to your email address.', 'woocommerce' ) );
				}

				// Only redirect after a forced login - otherwise output a success notice.
				if ( apply_filters( 'woocommerce_registration_auth_new_customer', true, $new_customer ) ) {
					wc_set_customer_auth_cookie( $new_customer );

					if ( ! empty( $_POST['redirect'] ) ) {
						$redirect = wp_sanitize_redirect( wp_unslash( $_POST['redirect'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					} elseif ( wc_get_raw_referer() ) {
						$redirect = wc_get_raw_referer();
					} else {
						$redirect = wc_get_page_permalink( 'myaccount' );
					}

					wp_redirect( wp_validate_redirect( apply_filters( 'woocommerce_registration_redirect', $redirect ), wc_get_page_permalink( 'myaccount' ) ) ); //phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
					exit;
				}
			} catch ( Exception $e ) {
				if ( $e->getMessage() ) {
					wc_add_notice( '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . $e->getMessage(), 'error' );
				}
			}
		}
	}
}

WC_Form_Handler::init();

<?php
/**
 * Personal data erasers.
 *
 * @since 3.4.0
 * @package WooCommerce\Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Privacy_Erasers Class.
 */
class WC_Privacy_Erasers {
	/**
	 * Finds and erases customer data by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public static function customer_data_eraser( $email_address, $page ) {
		$response = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);

		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		if ( ! $user instanceof WP_User ) {
			return $response;
		}

		$customer = new WC_Customer( $user->ID );

		if ( ! $customer ) {
			return $response;
		}

		$props_to_erase = apply_filters(
			'woocommerce_privacy_erase_customer_personal_data_props',
			array(
				'billing_first_name'  => __( 'Billing First Name', 'woocommerce' ),
				'billing_last_name'   => __( 'Billing Last Name', 'woocommerce' ),
				'billing_company'     => __( 'Billing Company', 'woocommerce' ),
				'billing_address_1'   => __( 'Billing Address 1', 'woocommerce' ),
				'billing_address_2'   => __( 'Billing Address 2', 'woocommerce' ),
				'billing_city'        => __( 'Billing City', 'woocommerce' ),
				'billing_postcode'    => __( 'Billing Postal/Zip Code', 'woocommerce' ),
				'billing_state'       => __( 'Billing State', 'woocommerce' ),
				'billing_country'     => __( 'Billing Country / Region', 'woocommerce' ),
				'billing_phone'       => __( 'Billing Phone Number', 'woocommerce' ),
				'billing_email'       => __( 'Email Address', 'woocommerce' ),
				'shipping_first_name' => __( 'Shipping First Name', 'woocommerce' ),
				'shipping_last_name'  => __( 'Shipping Last Name', 'woocommerce' ),
				'shipping_company'    => __( 'Shipping Company', 'woocommerce' ),
				'shipping_address_1'  => __( 'Shipping Address 1', 'woocommerce' ),
				'shipping_address_2'  => __( 'Shipping Address 2', 'woocommerce' ),
				'shipping_city'       => __( 'Shipping City', 'woocommerce' ),
				'shipping_postcode'   => __( 'Shipping Postal/Zip Code', 'woocommerce' ),
				'shipping_state'      => __( 'Shipping State', 'woocommerce' ),
				'shipping_country'    => __( 'Shipping Country / Region', 'woocommerce' ),
				'shipping_phone'      => __( 'Shipping Phone Number', 'woocommerce' ),
			),
			$customer
		);

		foreach ( $props_to_erase as $prop => $label ) {
			$erased = false;

			if ( is_callable( array( $customer, 'get_' . $prop ) ) && is_callable( array( $customer, 'set_' . $prop ) ) ) {
				$value = $customer->{"get_$prop"}( 'edit' );

				if ( $value ) {
					$customer->{"set_$prop"}( '' );
					$erased = true;
				}
			}

			$erased = apply_filters( 'woocommerce_privacy_erase_customer_personal_data_prop', $erased, $prop, $customer );

			if ( $erased ) {
				/* Translators: %s Prop name. */
				$response['messages'][]    = sprintf( __( 'Removed customer "%s"', 'woocommerce' ), $label );
				$response['items_removed'] = true;
			}
		}

		$customer->save();

		/**
		 * Allow extensions to remove data for this customer and adjust the response.
		 *
		 * @since 3.4.0
		 * @param array    $response Array response data. Must include messages, num_items_removed, num_items_retained, done.
		 * @param WC_Order $order A customer object.
		 */
		return apply_filters( 'woocommerce_privacy_erase_personal_data_customer', $response, $customer );
	}

	/**
	 * Finds and erases data which could be used to identify a person from WooCommerce data associated with an email address.
	 *
	 * Orders are erased in blocks of 10 to avoid timeouts.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public static function order_data_eraser( $email_address, $page ) {
		$page            = (int) $page;
		$user            = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
		$erasure_enabled = wc_string_to_bool( get_option( 'woocommerce_erasure_request_removes_order_data', 'no' ) );
		$response        = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);

		$order_query = array(
			'limit'    => 10,
			'page'     => $page,
			'customer' => array( $email_address ),
		);

		if ( $user instanceof WP_User ) {
			$order_query['customer'][] = (int) $user->ID;
		}

		$orders = wc_get_orders( $order_query );

		if ( 0 < count( $orders ) ) {
			foreach ( $orders as $order ) {
				if ( apply_filters( 'woocommerce_privacy_erase_order_personal_data', $erasure_enabled, $order ) ) {
					self::remove_order_personal_data( $order );

					/* Translators: %s Order number. */
					$response['messages'][]    = sprintf( __( 'Removed personal data from order %s.', 'woocommerce' ), $order->get_order_number() );
					$response['items_removed'] = true;
				} else {
					/* Translators: %s Order number. */
					$response['messages'][]     = sprintf( __( 'Personal data within order %s has been retained.', 'woocommerce' ), $order->get_order_number() );
					$response['items_retained'] = true;
				}
			}
			$response['done'] = 10 > count( $orders );
		} else {
			$response['done'] = true;
		}

		return $response;
	}

	/**
	 * Finds and removes customer download logs by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public static function download_data_eraser( $email_address, $page ) {
		$page            = (int) $page;
		$user            = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
		$erasure_enabled = wc_string_to_bool( get_option( 'woocommerce_erasure_request_removes_download_data', 'no' ) );
		$response        = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);

		$downloads_query = array(
			'limit'  => -1,
			'page'   => $page,
			'return' => 'ids',
		);

		if ( $user instanceof WP_User ) {
			$downloads_query['user_id'] = (int) $user->ID;
		} else {
			$downloads_query['user_email'] = $email_address;
		}

		$customer_download_data_store = WC_Data_Store::load( 'customer-download' );

		// Revoke download permissions.
		if ( apply_filters( 'woocommerce_privacy_erase_download_personal_data', $erasure_enabled, $email_address ) ) {
			if ( $user instanceof WP_User ) {
				$result = $customer_download_data_store->delete_by_user_id( (int) $user->ID );
			} else {
				$result = $customer_download_data_store->delete_by_user_email( $email_address );
			}
			if ( $result ) {
				$response['messages'][]    = __( 'Removed access to downloadable files.', 'woocommerce' );
				$response['items_removed'] = true;
			}
		} else {
			$response['messages'][]     = __( 'Customer download permissions have been retained.', 'woocommerce' );
			$response['items_retained'] = true;
		}

		return $response;
	}

	/**
	 * Remove personal data specific to WooCommerce from an order object.
	 *
	 * Note; this will hinder order processing for obvious reasons!
	 *
	 * @param WC_Order $order Order object.
	 */
	public static function remove_order_personal_data( $order ) {
		$anonymized_data = array();

		/**
		 * Allow extensions to remove their own personal data for this order first, so order data is still available.
		 *
		 * @since 3.4.0
		 * @param WC_Order $order A customer object.
		 */
		do_action( 'woocommerce_privacy_before_remove_order_personal_data', $order );

		/**
		 * Expose props and data types we'll be anonymizing.
		 *
		 * @since 3.4.0
		 * @param array    $props Keys are the prop names, values are the data type we'll be passing to wp_privacy_anonymize_data().
		 * @param WC_Order $order A customer object.
		 */
		$props_to_remove = apply_filters(
			'woocommerce_privacy_remove_order_personal_data_props',
			array(
				'customer_ip_address' => 'ip',
				'customer_user_agent' => 'text',
				'billing_first_name'  => 'text',
				'billing_last_name'   => 'text',
				'billing_company'     => 'text',
				'billing_address_1'   => 'text',
				'billing_address_2'   => 'text',
				'billing_city'        => 'text',
				'billing_postcode'    => 'text',
				'billing_state'       => 'address_state',
				'billing_country'     => 'address_country',
				'billing_phone'       => 'phone',
				'billing_email'       => 'email',
				'shipping_first_name' => 'text',
				'shipping_last_name'  => 'text',
				'shipping_company'    => 'text',
				'shipping_address_1'  => 'text',
				'shipping_address_2'  => 'text',
				'shipping_city'       => 'text',
				'shipping_postcode'   => 'text',
				'shipping_state'      => 'address_state',
				'shipping_country'    => 'address_country',
				'shipping_phone'      => 'phone',
				'customer_id'         => 'numeric_id',
				'transaction_id'      => 'numeric_id',
			),
			$order
		);

		if ( ! empty( $props_to_remove ) && is_array( $props_to_remove ) ) {
			foreach ( $props_to_remove as $prop => $data_type ) {
				// Get the current value in edit context.
				$value = $order->{"get_$prop"}( 'edit' );

				// If the value is empty, it does not need to be anonymized.
				if ( empty( $value ) || empty( $data_type ) ) {
					continue;
				}

				$anon_value = function_exists( 'wp_privacy_anonymize_data' ) ? wp_privacy_anonymize_data( $data_type, $value ) : '';

				/**
				 * Expose a way to control the anonymized value of a prop via 3rd party code.
				 *
				 * @since 3.4.0
				 * @param string   $anon_value Value of this prop after anonymization.
				 * @param string   $prop Name of the prop being removed.
				 * @param string   $value Current value of the data.
				 * @param string   $data_type Type of data.
				 * @param WC_Order $order An order object.
				 */
				$anonymized_data[ $prop ] = apply_filters( 'woocommerce_privacy_remove_order_personal_data_prop_value', $anon_value, $prop, $value, $data_type, $order );
			}
		}

		// Set all new props and persist the new data to the database.
		$order->set_props( $anonymized_data );

		// Remove meta data.
		$meta_to_remove = apply_filters(
			'woocommerce_privacy_remove_order_personal_data_meta',
			array(
				'Payer first name'     => 'text',
				'Payer last name'      => 'text',
				'Payer PayPal address' => 'email',
				'Transaction ID'       => 'numeric_id',
			)
		);

		if ( ! empty( $meta_to_remove ) && is_array( $meta_to_remove ) ) {
			foreach ( $meta_to_remove as $meta_key => $data_type ) {
				$value = $order->get_meta( $meta_key );

				// If the value is empty, it does not need to be anonymized.
				if ( empty( $value ) || empty( $data_type ) ) {
					continue;
				}

				$anon_value = function_exists( 'wp_privacy_anonymize_data' ) ? wp_privacy_anonymize_data( $data_type, $value ) : '';

				/**
				 * Expose a way to control the anonymized value of a value via 3rd party code.
				 *
				 * @since 3.4.0
				 * @param string   $anon_value Value of this data after anonymization.
				 * @param string   $prop meta_key key being removed.
				 * @param string   $value Current value of the data.
				 * @param string   $data_type Type of data.
				 * @param WC_Order $order An order object.
				 */
				$anon_value = apply_filters( 'woocommerce_privacy_remove_order_personal_data_meta_value', $anon_value, $meta_key, $value, $data_type, $order );

				if ( $anon_value ) {
					$order->update_meta_data( $meta_key, $anon_value );
				} else {
					$order->delete_meta_data( $meta_key );
				}
			}
		}

		$order->update_meta_data( '_anonymized', 'yes' );
		$order->save();

		// Delete order notes which can contain PII.
		$notes = wc_get_order_notes(
			array(
				'order_id' => $order->get_id(),
			)
		);

		foreach ( $notes as $note ) {
			wc_delete_order_note( $note->id );
		}

		// Add note that this event occurred.
		$order->add_order_note( __( 'Personal data removed.', 'woocommerce' ) );

		/**
		 * Allow extensions to remove their own personal data for this order.
		 *
		 * @since 3.4.0
		 * @param WC_Order $order A customer object.
		 */
		do_action( 'woocommerce_privacy_remove_order_personal_data', $order );
	}

	/**
	 * Finds and erases customer tokens by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public static function customer_tokens_eraser( $email_address, $page ) {
		$response = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);

		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		if ( ! $user instanceof WP_User ) {
			return $response;
		}

		$tokens = WC_Payment_Tokens::get_tokens(
			array(
				'user_id' => $user->ID,
			)
		);

		if ( empty( $tokens ) ) {
			return $response;
		}

		foreach ( $tokens as $token ) {
			WC_Payment_Tokens::delete( $token->get_id() );

			/* Translators: %s Prop name. */
			$response['messages'][]    = sprintf( __( 'Removed payment token "%d"', 'woocommerce' ), $token->get_id() );
			$response['items_removed'] = true;
		}

		/**
		 * Allow extensions to remove data for tokens and adjust the response.
		 *
		 * @since 3.4.0
		 * @param array $response Array response data. Must include messages, num_items_removed, num_items_retained, done.
		 * @param array $tokens   Array of tokens.
		 */
		return apply_filters( 'woocommerce_privacy_erase_personal_data_tokens', $response, $tokens );
	}
}

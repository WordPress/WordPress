<?php
/**
 * WooCommerce Account Functions
 *
 * Functions for account specific things.
 *
 * @package WooCommerce\Functions
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the url to the lost password endpoint url.
 *
 * @param  string $default_url Default lost password URL.
 * @return string
 */
function wc_lostpassword_url( $default_url = '' ) {
	// Avoid loading too early.
	if ( ! did_action( 'init' ) ) {
		return $default_url;
	}

	// Don't redirect to the woocommerce endpoint on global network admin lost passwords.
	if ( is_multisite() && isset( $_GET['redirect_to'] ) && false !== strpos( wp_unslash( $_GET['redirect_to'] ), network_admin_url() ) ) { // WPCS: input var ok, sanitization ok, CSRF ok.
		return $default_url;
	}

	$wc_account_page_url    = wc_get_page_permalink( 'myaccount' );
	$wc_account_page_exists = wc_get_page_id( 'myaccount' ) > 0;
	$lost_password_endpoint = get_option( 'woocommerce_myaccount_lost_password_endpoint' );

	if ( $wc_account_page_exists && ! empty( $lost_password_endpoint ) ) {
		return wc_get_endpoint_url( $lost_password_endpoint, '', $wc_account_page_url );
	} else {
		return $default_url;
	}
}

add_filter( 'lostpassword_url', 'wc_lostpassword_url', 10, 1 );

/**
 * Get the link to the edit account details page.
 *
 * @return string
 */
function wc_customer_edit_account_url() {
	$edit_account_url = wc_get_endpoint_url( 'edit-account', '', wc_get_page_permalink( 'myaccount' ) );

	return apply_filters( 'woocommerce_customer_edit_account_url', $edit_account_url );
}

/**
 * Get the edit address slug translation.
 *
 * @param  string $id   Address ID.
 * @param  bool   $flip Flip the array to make it possible to retrieve the values ​​from both sides.
 *
 * @return string       Address slug i18n.
 */
function wc_edit_address_i18n( $id, $flip = false ) {
	$slugs = apply_filters(
		'woocommerce_edit_address_slugs',
		array(
			'billing'  => sanitize_title( _x( 'billing', 'edit-address-slug', 'woocommerce' ) ),
			'shipping' => sanitize_title( _x( 'shipping', 'edit-address-slug', 'woocommerce' ) ),
		)
	);

	if ( $flip ) {
		$slugs = array_flip( $slugs );
	}

	if ( ! isset( $slugs[ $id ] ) ) {
		return $id;
	}

	return $slugs[ $id ];
}

/**
 * Get My Account menu items.
 *
 * @since 2.6.0
 * @return array
 */
function wc_get_account_menu_items() {
	$endpoints = array(
		'orders'          => get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' ),
		'downloads'       => get_option( 'woocommerce_myaccount_downloads_endpoint', 'downloads' ),
		'edit-address'    => get_option( 'woocommerce_myaccount_edit_address_endpoint', 'edit-address' ),
		'payment-methods' => get_option( 'woocommerce_myaccount_payment_methods_endpoint', 'payment-methods' ),
		'edit-account'    => get_option( 'woocommerce_myaccount_edit_account_endpoint', 'edit-account' ),
		'customer-logout' => get_option( 'woocommerce_logout_endpoint', 'customer-logout' ),
	);

	$items = array(
		'dashboard'       => __( 'Dashboard', 'woocommerce' ),
		'orders'          => __( 'Orders', 'woocommerce' ),
		'downloads'       => __( 'Downloads', 'woocommerce' ),
		'edit-address'    => _n( 'Address', 'Addresses', ( 1 + (int) wc_shipping_enabled() ), 'woocommerce' ),
		'payment-methods' => __( 'Payment methods', 'woocommerce' ),
		'edit-account'    => __( 'Account details', 'woocommerce' ),
		'customer-logout' => __( 'Logout', 'woocommerce' ),
	);

	// Remove missing endpoints.
	foreach ( $endpoints as $endpoint_id => $endpoint ) {
		if ( empty( $endpoint ) ) {
			unset( $items[ $endpoint_id ] );
		}
	}

	// Check if payment gateways support add new payment methods.
	if ( isset( $items['payment-methods'] ) ) {
		$support_payment_methods = false;
		foreach ( WC()->payment_gateways->get_available_payment_gateways() as $gateway ) {
			if ( $gateway->supports( 'add_payment_method' ) || $gateway->supports( 'tokenization' ) ) {
				$support_payment_methods = true;
				break;
			}
		}

		if ( ! $support_payment_methods ) {
			unset( $items['payment-methods'] );
		}
	}

	return apply_filters( 'woocommerce_account_menu_items', $items, $endpoints );
}

/**
 * Get account menu item classes.
 *
 * @since 2.6.0
 * @param string $endpoint Endpoint.
 * @return string
 */
function wc_get_account_menu_item_classes( $endpoint ) {
	global $wp;

	$classes = array(
		'woocommerce-MyAccount-navigation-link',
		'woocommerce-MyAccount-navigation-link--' . $endpoint,
	);

	// Set current item class.
	$current = isset( $wp->query_vars[ $endpoint ] );
	if ( 'dashboard' === $endpoint && ( isset( $wp->query_vars['page'] ) || empty( $wp->query_vars ) ) ) {
		$current = true; // Dashboard is not an endpoint, so needs a custom check.
	} elseif ( 'orders' === $endpoint && isset( $wp->query_vars['view-order'] ) ) {
		$current = true; // When looking at individual order, highlight Orders list item (to signify where in the menu the user currently is).
	} elseif ( 'payment-methods' === $endpoint && isset( $wp->query_vars['add-payment-method'] ) ) {
		$current = true;
	}

	if ( $current ) {
		$classes[] = 'is-active';
	}

	$classes = apply_filters( 'woocommerce_account_menu_item_classes', $classes, $endpoint );

	return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
}

/**
 * Get account endpoint URL.
 *
 * @since 2.6.0
 * @param string $endpoint Endpoint.
 * @return string
 */
function wc_get_account_endpoint_url( $endpoint ) {
	if ( 'dashboard' === $endpoint ) {
		return wc_get_page_permalink( 'myaccount' );
	}

	if ( 'customer-logout' === $endpoint ) {
		return wc_logout_url();
	}

	return wc_get_endpoint_url( $endpoint, '', wc_get_page_permalink( 'myaccount' ) );
}

/**
 * Get My Account > Orders columns.
 *
 * @since 2.6.0
 * @return array
 */
function wc_get_account_orders_columns() {
	/**
	 * Filters the array of My Account > Orders columns.
	 *
	 * @since 2.6.0
	 * @param array $columns Array of column labels keyed by column IDs.
	 */
	return apply_filters(
		'woocommerce_account_orders_columns',
		array(
			'order-number'  => __( 'Order', 'woocommerce' ),
			'order-date'    => __( 'Date', 'woocommerce' ),
			'order-status'  => __( 'Status', 'woocommerce' ),
			'order-total'   => __( 'Total', 'woocommerce' ),
			'order-actions' => __( 'Actions', 'woocommerce' ),
		)
	);
}

/**
 * Get My Account > Downloads columns.
 *
 * @since 2.6.0
 * @return array
 */
function wc_get_account_downloads_columns() {
	$columns = apply_filters(
		'woocommerce_account_downloads_columns',
		array(
			'download-product'   => __( 'Product', 'woocommerce' ),
			'download-remaining' => __( 'Downloads remaining', 'woocommerce' ),
			'download-expires'   => __( 'Expires', 'woocommerce' ),
			'download-file'      => __( 'Download', 'woocommerce' ),
			'download-actions'   => '&nbsp;',
		)
	);

	if ( ! has_filter( 'woocommerce_account_download_actions' ) ) {
		unset( $columns['download-actions'] );
	}

	return $columns;
}

/**
 * Get My Account > Payment methods columns.
 *
 * @since 2.6.0
 * @return array
 */
function wc_get_account_payment_methods_columns() {
	return apply_filters(
		'woocommerce_account_payment_methods_columns',
		array(
			'method'  => __( 'Method', 'woocommerce' ),
			'expires' => __( 'Expires', 'woocommerce' ),
			'actions' => '&nbsp;',
		)
	);
}

/**
 * Get My Account > Payment methods types
 *
 * @since 2.6.0
 * @return array
 */
function wc_get_account_payment_methods_types() {
	return apply_filters(
		'woocommerce_payment_methods_types',
		array(
			'cc'     => __( 'Credit card', 'woocommerce' ),
			'echeck' => __( 'eCheck', 'woocommerce' ),
		)
	);
}

/**
 * Get account orders actions.
 *
 * @since  3.2.0
 * @param  int|WC_Order $order Order instance or ID.
 * @return array
 */
function wc_get_account_orders_actions( $order ) {
	if ( ! is_object( $order ) ) {
		$order_id = absint( $order );
		$order    = wc_get_order( $order_id );
	}

	$actions = array(
		'pay'    => array(
			'url'  => $order->get_checkout_payment_url(),
			'name' => __( 'Pay', 'woocommerce' ),
		),
		'view'   => array(
			'url'  => $order->get_view_order_url(),
			'name' => __( 'View', 'woocommerce' ),
		),
		'cancel' => array(
			'url'  => $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ),
			'name' => __( 'Cancel', 'woocommerce' ),
		),
	);

	if ( ! $order->needs_payment() ) {
		unset( $actions['pay'] );
	}

	if ( ! in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ), true ) ) {
		unset( $actions['cancel'] );
	}

	return apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order );
}

/**
 * Get account formatted address.
 *
 * @since  3.2.0
 * @param  string $address_type Address type.
 *                              Accepts: 'billing' or 'shipping'.
 *                              Default to 'billing'.
 * @param  int    $customer_id  Customer ID.
 *                              Default to 0.
 * @return string
 */
function wc_get_account_formatted_address( $address_type = 'billing', $customer_id = 0 ) {
	$getter  = "get_{$address_type}";
	$address = array();

	if ( 0 === $customer_id ) {
		$customer_id = get_current_user_id();
	}

	$customer = new WC_Customer( $customer_id );

	if ( is_callable( array( $customer, $getter ) ) ) {
		$address = $customer->$getter();
		unset( $address['email'], $address['tel'] );
	}

	return WC()->countries->get_formatted_address( apply_filters( 'woocommerce_my_account_my_address_formatted_address', $address, $customer->get_id(), $address_type ) );
}

/**
 * Returns an array of a user's saved payments list for output on the account tab.
 *
 * @since  2.6
 * @param  array $list         List of payment methods passed from wc_get_customer_saved_methods_list().
 * @param  int   $customer_id  The customer to fetch payment methods for.
 * @return array               Filtered list of customers payment methods.
 */
function wc_get_account_saved_payment_methods_list( $list, $customer_id ) {
	$payment_tokens = WC_Payment_Tokens::get_customer_tokens( $customer_id );
	foreach ( $payment_tokens as $payment_token ) {
		$delete_url      = wc_get_endpoint_url( 'delete-payment-method', $payment_token->get_id() );
		$delete_url      = wp_nonce_url( $delete_url, 'delete-payment-method-' . $payment_token->get_id() );
		$set_default_url = wc_get_endpoint_url( 'set-default-payment-method', $payment_token->get_id() );
		$set_default_url = wp_nonce_url( $set_default_url, 'set-default-payment-method-' . $payment_token->get_id() );

		$type            = strtolower( $payment_token->get_type() );
		$list[ $type ][] = array(
			'method'     => array(
				'gateway' => $payment_token->get_gateway_id(),
			),
			'expires'    => esc_html__( 'N/A', 'woocommerce' ),
			'is_default' => $payment_token->is_default(),
			'actions'    => array(
				'delete' => array(
					'url'  => $delete_url,
					'name' => esc_html__( 'Delete', 'woocommerce' ),
				),
			),
		);
		$key             = key( array_slice( $list[ $type ], -1, 1, true ) );

		if ( ! $payment_token->is_default() ) {
			$list[ $type ][ $key ]['actions']['default'] = array(
				'url'  => $set_default_url,
				'name' => esc_html__( 'Make default', 'woocommerce' ),
			);
		}

		$list[ $type ][ $key ] = apply_filters( 'woocommerce_payment_methods_list_item', $list[ $type ][ $key ], $payment_token );
	}
	return $list;
}

add_filter( 'woocommerce_saved_payment_methods_list', 'wc_get_account_saved_payment_methods_list', 10, 2 );

/**
 * Controls the output for credit cards on the my account page.
 *
 * @since 2.6
 * @param  array            $item         Individual list item from woocommerce_saved_payment_methods_list.
 * @param  WC_Payment_Token $payment_token The payment token associated with this method entry.
 * @return array                           Filtered item.
 */
function wc_get_account_saved_payment_methods_list_item_cc( $item, $payment_token ) {
	if ( 'cc' !== strtolower( $payment_token->get_type() ) ) {
		return $item;
	}

	$card_type               = $payment_token->get_card_type();
	$item['method']['last4'] = $payment_token->get_last4();
	$item['method']['brand'] = ( ! empty( $card_type ) ? ucfirst( $card_type ) : esc_html__( 'Credit card', 'woocommerce' ) );
	$item['expires']         = $payment_token->get_expiry_month() . '/' . substr( $payment_token->get_expiry_year(), -2 );

	return $item;
}

add_filter( 'woocommerce_payment_methods_list_item', 'wc_get_account_saved_payment_methods_list_item_cc', 10, 2 );

/**
 * Controls the output for eChecks on the my account page.
 *
 * @since 2.6
 * @param  array            $item         Individual list item from woocommerce_saved_payment_methods_list.
 * @param  WC_Payment_Token $payment_token The payment token associated with this method entry.
 * @return array                           Filtered item.
 */
function wc_get_account_saved_payment_methods_list_item_echeck( $item, $payment_token ) {
	if ( 'echeck' !== strtolower( $payment_token->get_type() ) ) {
		return $item;
	}

	$item['method']['last4'] = $payment_token->get_last4();
	$item['method']['brand'] = esc_html__( 'eCheck', 'woocommerce' );

	return $item;
}

add_filter( 'woocommerce_payment_methods_list_item', 'wc_get_account_saved_payment_methods_list_item_echeck', 10, 2 );

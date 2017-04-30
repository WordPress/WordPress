<?php
/**
 * WooCommerce Customer Functions
 *
 * Functions for customers.
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	WooCommerce/Functions
 * @version 	2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from seeing the admin bar
 *
 * Note: get_option( 'woocommerce_lock_down_admin', true ) is a deprecated option here for backwards compat. Defaults to true.
 *
 * @access public
 * @param bool $show_admin_bar
 * @return bool
 */
function wc_disable_admin_bar( $show_admin_bar ) {
	if ( apply_filters( 'woocommerce_disable_admin_bar', get_option( 'woocommerce_lock_down_admin', 'yes' ) === 'yes' ) && ! ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_woocommerce' ) ) ) {
		$show_admin_bar = false;
	}

	return $show_admin_bar;
}
add_filter( 'show_admin_bar', 'wc_disable_admin_bar', 10, 1 );


/**
 * Create a new customer
 *
 * @param  string $email
 * @param  string $username
 * @param  string $password
 * @return int|WP_Error on failure, Int (user ID) on success
 */
function wc_create_new_customer( $email, $username = '', $password = '' ) {

	// Check the e-mail address
	if ( empty( $email ) || ! is_email( $email ) ) {
		return new WP_Error( 'registration-error', __( 'Please provide a valid email address.', 'woocommerce' ) );
	}

	if ( email_exists( $email ) ) {
		return new WP_Error( 'registration-error', __( 'An account is already registered with your email address. Please login.', 'woocommerce' ) );
	}

	// Handle username creation
	if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) || ! empty( $username ) ) {

		$username = sanitize_user( $username );

		if ( empty( $username ) || ! validate_username( $username ) ) {
			return new WP_Error( 'registration-error', __( 'Please enter a valid account username.', 'woocommerce' ) );
		}

		if ( username_exists( $username ) )
			return new WP_Error( 'registration-error', __( 'An account is already registered with that username. Please choose another.', 'woocommerce' ) );
	} else {

		$username = sanitize_user( current( explode( '@', $email ) ) );

		// Ensure username is unique
		$append     = 1;
		$o_username = $username;

		while ( username_exists( $username ) ) {
			$username = $o_username . $append;
			$append ++;
		}
	}

	// Handle password creation
	if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && empty( $password ) ) {
		$password = wp_generate_password();
		$password_generated = true;

	} elseif ( empty( $password ) ) {
		return new WP_Error( 'registration-error', __( 'Please enter an account password.', 'woocommerce' ) );

	} else {
		$password_generated = false;
	}

	// WP Validation
	$validation_errors = new WP_Error();

	do_action( 'woocommerce_register_post', $username, $email, $validation_errors );

	$validation_errors = apply_filters( 'woocommerce_registration_errors', $validation_errors, $username, $email );

	if ( $validation_errors->get_error_code() )
		return $validation_errors;

	$new_customer_data = apply_filters( 'woocommerce_new_customer_data', array(
		'user_login' => $username,
		'user_pass'  => $password,
		'user_email' => $email,
		'role'       => 'customer'
	) );

	$customer_id = wp_insert_user( $new_customer_data );

	if ( is_wp_error( $customer_id ) ) {
		return new WP_Error( 'registration-error', '<strong>' . __( 'ERROR', 'woocommerce' ) . '</strong>: ' . __( 'Couldn&#8217;t register you&hellip; please contact us if you continue to have problems.', 'woocommerce' ) );
	}

	do_action( 'woocommerce_created_customer', $customer_id, $new_customer_data, $password_generated );

	return $customer_id;
}

/**
 * Login a customer (set auth cookie and set global user object)
 *
 * @param  int $customer_id
 * @return void
 */
function wc_set_customer_auth_cookie( $customer_id ) {
	global $current_user;

	$current_user = get_user_by( 'id', $customer_id );

	wp_set_auth_cookie( $customer_id, true );
}

/**
 * Get past orders (by email) and update them
 *
 * @param  int $customer_id
 * @return int
 */
function wc_update_new_customer_past_orders( $customer_id ) {

	$customer = get_user_by( 'id', absint( $customer_id ) );

	$customer_orders = get_posts( array(
		'numberposts' => -1,
		'post_type'   => 'shop_order',
		'post_status' => 'publish',
		'fields'      => 'ids',
		'meta_query' => array(
			array(
				'key'     => '_customer_user',
				'value'   => array( 0, '' ),
				'compare' => 'IN'
			),
			array(
				'key'     => '_billing_email',
				'value'   => $customer->user_email,
			)
		),
	) );

	$linked = 0;
	$complete = 0;

	if ( $customer_orders )
		foreach ( $customer_orders as $order_id ) {
			update_post_meta( $order_id, '_customer_user', $customer->ID );

			$order_status = wp_get_post_terms( $order_id, 'shop_order_status' );

			if ( $order_status ) {
				$order_status = current( $order_status );
				$order_status = sanitize_title( $order_status->slug );
			}

			if ( $order_status == 'completed' )
				$complete ++;

			$linked ++;
		}

	if ( $complete ) {
		update_user_meta( $customer_id, 'paying_customer', 1 );
		update_user_meta( $customer_id, '_order_count', '' );
		update_user_meta( $customer_id, '_money_spent', '' );
	}

	return $linked;
}

/**
 * Order Status completed - This is a paying customer
 *
 * @access public
 * @param int $order_id
 * @return void
 */
function wc_paying_customer( $order_id ) {

	$order = new WC_Order( $order_id );

	if ( $order->user_id > 0 ) {
		update_user_meta( $order->user_id, 'paying_customer', 1 );

		$old_spent = absint( get_user_meta( $order->user_id, '_money_spent', true ) );
		update_user_meta( $order->user_id, '_money_spent', $old_spent + $order->order_total );

		$old_count = absint( get_user_meta( $order->user_id, '_order_count', true ) );
		update_user_meta( $order->user_id, '_order_count', $old_count + 1 );
	}
}
add_action( 'woocommerce_order_status_completed', 'wc_paying_customer' );


/**
 * Checks if a user (by email) has bought an item
 *
 * @access public
 * @param string $customer_email
 * @param int $user_id
 * @param int $product_id
 * @return bool
 */
function wc_customer_bought_product( $customer_email, $user_id, $product_id ) {
	global $wpdb;

	$emails = array();

	if ( $user_id ) {
		$user     = get_user_by( 'id', $user_id );
		$emails[] = $user->user_email;
	}

	if ( is_email( $customer_email ) ) {
		$emails[] = $customer_email;
	}

	if ( sizeof( $emails ) == 0 ) {
		return false;
	}

	$completed  = get_term_by( 'slug', 'completed', 'shop_order_status' );
	$processing = get_term_by( 'slug', 'processing', 'shop_order_status' );

	return $wpdb->get_var(
		$wpdb->prepare( "
			SELECT COUNT( DISTINCT order_items.order_item_id )
			FROM {$wpdb->prefix}woocommerce_order_items as order_items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON order_items.order_item_id = itemmeta.order_item_id
			LEFT JOIN {$wpdb->postmeta} AS postmeta ON order_items.order_id = postmeta.post_id
			LEFT JOIN {$wpdb->term_relationships} AS rel ON order_items.order_id = rel.object_ID
			WHERE
				rel.term_taxonomy_id IN ( %d, %d ) AND
				itemmeta.meta_value  = %s AND
				itemmeta.meta_key    IN ( '_variation_id', '_product_id' ) AND
				postmeta.meta_key    IN ( '_billing_email', '_customer_user' ) AND
				(
					postmeta.meta_value  IN ( '" . implode( "','", array_unique( $emails ) ) . "' ) OR
					(
						postmeta.meta_value = %s AND
						postmeta.meta_value > 0
					)
				)
			", $completed->term_taxonomy_id, $processing->term_taxonomy_id, $product_id, $user_id
		)
	);
}

/**
 * Checks if a user has a certain capability
 *
 * @access public
 * @param array $allcaps
 * @param array $caps
 * @param array $args
 * @return bool
 */
function wc_customer_has_capability( $allcaps, $caps, $args ) {
	if ( isset( $caps[0] ) ) {
		switch ( $caps[0] ) {

			case 'view_order' :
				$user_id = $args[1];
				$order = new WC_Order( $args[2] );

				if ( $user_id == $order->user_id ) {
					$allcaps['view_order'] = true;
				}

				break;

			case 'pay_for_order' :
				$user_id = $args[1];
				$order_id = isset( $args[2] ) ? $args[2] : null;

				// When no order ID, we assume it's a new order
				// and thus, customer can pay for it
				if ( ! $order_id ) {
					$allcaps['pay_for_order'] = true;

					break;
				}

				$order = new WC_Order( $order_id );
				if ( $user_id == $order->user_id || empty( $order->user_id ) ) {
					$allcaps['pay_for_order'] = true;
				}

				break;

			case 'order_again' :
				$user_id = $args[1];
				$order = new WC_Order( $args[2] );

				if ( $user_id == $order->user_id ) {
					$allcaps['order_again'] = true;
				}

				break;

			case 'cancel_order' :
				$user_id = $args[1];
				$order = new WC_Order( $args[2] );

				if ( $user_id == $order->user_id ) {
					$allcaps['cancel_order'] = true;
				}

				break;

			case 'download_file' :
				$user_id = $args[1];
				$download = $args[2];

				if ( $user_id == $download->user_id ) {
					$allcaps['download_file'] = true;
				}

				break;
		}
	}

	return $allcaps;
}

add_filter( 'user_has_cap', 'wc_customer_has_capability', 10, 3 );

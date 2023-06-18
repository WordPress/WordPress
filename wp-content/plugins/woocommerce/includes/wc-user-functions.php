<?php
/**
 * WooCommerce Customer Functions
 *
 * Functions for customers.
 *
 * @package WooCommerce\Functions
 * @version 2.2.0
 */

use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from seeing the admin bar.
 *
 * Note: get_option( 'woocommerce_lock_down_admin', true ) is a deprecated option here for backwards compatibility. Defaults to true.
 *
 * @param bool $show_admin_bar If should display admin bar.
 * @return bool
 */
function wc_disable_admin_bar( $show_admin_bar ) {
	/**
	 * Controls whether the WooCommerce admin bar should be disabled.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $enabled
	 */
	if ( apply_filters( 'woocommerce_disable_admin_bar', true ) && ! ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_woocommerce' ) ) ) {
		$show_admin_bar = false;
	}

	return $show_admin_bar;
}
add_filter( 'show_admin_bar', 'wc_disable_admin_bar', 10, 1 ); // phpcs:ignore WordPress.VIP.AdminBarRemoval.RemovalDetected

if ( ! function_exists( 'wc_create_new_customer' ) ) {

	/**
	 * Create a new customer.
	 *
	 * @param  string $email    Customer email.
	 * @param  string $username Customer username.
	 * @param  string $password Customer password.
	 * @param  array  $args     List of arguments to pass to `wp_insert_user()`.
	 * @return int|WP_Error Returns WP_Error on failure, Int (user ID) on success.
	 */
	function wc_create_new_customer( $email, $username = '', $password = '', $args = array() ) {
		if ( empty( $email ) || ! is_email( $email ) ) {
			return new WP_Error( 'registration-error-invalid-email', __( 'Please provide a valid email address.', 'woocommerce' ) );
		}

		if ( email_exists( $email ) ) {
			return new WP_Error( 'registration-error-email-exists', apply_filters( 'woocommerce_registration_error_email_exists', __( 'An account is already registered with your email address. <a href="#" class="showlogin">Please log in.</a>', 'woocommerce' ), $email ) );
		}

		if ( 'yes' === get_option( 'woocommerce_registration_generate_username', 'yes' ) && empty( $username ) ) {
			$username = wc_create_new_customer_username( $email, $args );
		}

		$username = sanitize_user( $username );

		if ( empty( $username ) || ! validate_username( $username ) ) {
			return new WP_Error( 'registration-error-invalid-username', __( 'Please enter a valid account username.', 'woocommerce' ) );
		}

		if ( username_exists( $username ) ) {
			return new WP_Error( 'registration-error-username-exists', __( 'An account is already registered with that username. Please choose another.', 'woocommerce' ) );
		}

		// Handle password creation.
		$password_generated = false;
		if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && empty( $password ) ) {
			$password           = wp_generate_password();
			$password_generated = true;
		}

		if ( empty( $password ) ) {
			return new WP_Error( 'registration-error-missing-password', __( 'Please enter an account password.', 'woocommerce' ) );
		}

		// Use WP_Error to handle registration errors.
		$errors = new WP_Error();

		do_action( 'woocommerce_register_post', $username, $email, $errors );

		$errors = apply_filters( 'woocommerce_registration_errors', $errors, $username, $email );

		if ( $errors->get_error_code() ) {
			return $errors;
		}

		$new_customer_data = apply_filters(
			'woocommerce_new_customer_data',
			array_merge(
				$args,
				array(
					'user_login' => $username,
					'user_pass'  => $password,
					'user_email' => $email,
					'role'       => 'customer',
				)
			)
		);

		$customer_id = wp_insert_user( $new_customer_data );

		if ( is_wp_error( $customer_id ) ) {
			return $customer_id;
		}

		do_action( 'woocommerce_created_customer', $customer_id, $new_customer_data, $password_generated );

		return $customer_id;
	}
}

/**
 * Create a unique username for a new customer.
 *
 * @since 3.6.0
 * @param string $email New customer email address.
 * @param array  $new_user_args Array of new user args, maybe including first and last names.
 * @param string $suffix Append string to username to make it unique.
 * @return string Generated username.
 */
function wc_create_new_customer_username( $email, $new_user_args = array(), $suffix = '' ) {
	$username_parts = array();

	if ( isset( $new_user_args['first_name'] ) ) {
		$username_parts[] = sanitize_user( $new_user_args['first_name'], true );
	}

	if ( isset( $new_user_args['last_name'] ) ) {
		$username_parts[] = sanitize_user( $new_user_args['last_name'], true );
	}

	// Remove empty parts.
	$username_parts = array_filter( $username_parts );

	// If there are no parts, e.g. name had unicode chars, or was not provided, fallback to email.
	if ( empty( $username_parts ) ) {
		$email_parts    = explode( '@', $email );
		$email_username = $email_parts[0];

		// Exclude common prefixes.
		if ( in_array(
			$email_username,
			array(
				'sales',
				'hello',
				'mail',
				'contact',
				'info',
			),
			true
		) ) {
			// Get the domain part.
			$email_username = $email_parts[1];
		}

		$username_parts[] = sanitize_user( $email_username, true );
	}

	$username = wc_strtolower( implode( '.', $username_parts ) );

	if ( $suffix ) {
		$username .= $suffix;
	}

	/**
	 * WordPress 4.4 - filters the list of blocked usernames.
	 *
	 * @since 3.7.0
	 * @param array $usernames Array of blocked usernames.
	 */
	$illegal_logins = (array) apply_filters( 'illegal_user_logins', array() );

	// Stop illegal logins and generate a new random username.
	if ( in_array( strtolower( $username ), array_map( 'strtolower', $illegal_logins ), true ) ) {
		$new_args = array();

		/**
		 * Filter generated customer username.
		 *
		 * @since 3.7.0
		 * @param string $username      Generated username.
		 * @param string $email         New customer email address.
		 * @param array  $new_user_args Array of new user args, maybe including first and last names.
		 * @param string $suffix        Append string to username to make it unique.
		 */
		$new_args['first_name'] = apply_filters(
			'woocommerce_generated_customer_username',
			'woo_user_' . zeroise( wp_rand( 0, 9999 ), 4 ),
			$email,
			$new_user_args,
			$suffix
		);

		return wc_create_new_customer_username( $email, $new_args, $suffix );
	}

	if ( username_exists( $username ) ) {
		// Generate something unique to append to the username in case of a conflict with another user.
		$suffix = '-' . zeroise( wp_rand( 0, 9999 ), 4 );
		return wc_create_new_customer_username( $email, $new_user_args, $suffix );
	}

	/**
	 * Filter new customer username.
	 *
	 * @since 3.7.0
	 * @param string $username      Customer username.
	 * @param string $email         New customer email address.
	 * @param array  $new_user_args Array of new user args, maybe including first and last names.
	 * @param string $suffix        Append string to username to make it unique.
	 */
	return apply_filters( 'woocommerce_new_customer_username', $username, $email, $new_user_args, $suffix );
}

/**
 * Login a customer (set auth cookie and set global user object).
 *
 * @param int $customer_id Customer ID.
 */
function wc_set_customer_auth_cookie( $customer_id ) {
	wp_set_current_user( $customer_id );
	wp_set_auth_cookie( $customer_id, true );

	// Update session.
	WC()->session->init_session_cookie();
}

/**
 * Get past orders (by email) and update them.
 *
 * @param  int $customer_id Customer ID.
 * @return int
 */
function wc_update_new_customer_past_orders( $customer_id ) {
	$linked          = 0;
	$complete        = 0;
	$customer        = get_user_by( 'id', absint( $customer_id ) );
	$customer_orders = wc_get_orders(
		array(
			'limit'    => -1,
			'customer' => array( array( 0, $customer->user_email ) ),
			'return'   => 'ids',
		)
	);

	if ( ! empty( $customer_orders ) ) {
		foreach ( $customer_orders as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				continue;
			}

			$order->set_customer_id( $customer->ID );
			$order->save();

			if ( $order->has_downloadable_item() ) {
				$data_store = WC_Data_Store::load( 'customer-download' );
				$data_store->delete_by_order_id( $order->get_id() );
				wc_downloadable_product_permissions( $order->get_id(), true );
			}

			do_action( 'woocommerce_update_new_customer_past_order', $order_id, $customer );

			if ( $order->get_status() === 'wc-completed' ) {
				$complete++;
			}

			$linked++;
		}
	}

	if ( $complete ) {
		update_user_meta( $customer_id, 'paying_customer', 1 );
		update_user_meta( $customer_id, '_order_count', '' );
		update_user_meta( $customer_id, '_money_spent', '' );
		delete_user_meta( $customer_id, '_last_order' );
	}

	return $linked;
}

/**
 * Order payment completed - This is a paying customer.
 *
 * @param int $order_id Order ID.
 */
function wc_paying_customer( $order_id ) {
	$order       = wc_get_order( $order_id );
	$customer_id = $order->get_customer_id();

	if ( $customer_id > 0 && 'shop_order_refund' !== $order->get_type() ) {
		$customer = new WC_Customer( $customer_id );

		if ( ! $customer->get_is_paying_customer() ) {
			$customer->set_is_paying_customer( true );
			$customer->save();
		}
	}
}
add_action( 'woocommerce_payment_complete', 'wc_paying_customer' );
add_action( 'woocommerce_order_status_completed', 'wc_paying_customer' );

/**
 * Checks if a user (by email or ID or both) has bought an item.
 *
 * @param string $customer_email Customer email to check.
 * @param int    $user_id User ID to check.
 * @param int    $product_id Product ID to check.
 * @return bool
 */
function wc_customer_bought_product( $customer_email, $user_id, $product_id ) {
	global $wpdb;

	$result = apply_filters( 'woocommerce_pre_customer_bought_product', null, $customer_email, $user_id, $product_id );

	if ( null !== $result ) {
		return $result;
	}

	$transient_name    = 'wc_customer_bought_product_' . md5( $customer_email . $user_id );
	$transient_version = WC_Cache_Helper::get_transient_version( 'orders' );
	$transient_value   = get_transient( $transient_name );

	if ( isset( $transient_value['value'], $transient_value['version'] ) && $transient_value['version'] === $transient_version ) {
		$result = $transient_value['value'];
	} else {
		$customer_data = array( $user_id );

		if ( $user_id ) {
			$user = get_user_by( 'id', $user_id );

			if ( isset( $user->user_email ) ) {
				$customer_data[] = $user->user_email;
			}
		}

		if ( is_email( $customer_email ) ) {
			$customer_data[] = $customer_email;
		}

		$customer_data = array_map( 'esc_sql', array_filter( array_unique( $customer_data ) ) );
		$statuses      = array_map( 'esc_sql', wc_get_is_paid_statuses() );

		if ( count( $customer_data ) === 0 ) {
			return false;
		}

		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$statuses = array_map(
				function ( $status ) {
					return "wc-$status";
				},
				$statuses
			);
			$order_table = OrdersTableDataStore::get_orders_table_name();
			$sql = "
SELECT im.meta_value FROM $order_table AS o
INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON o.id = i.order_id
INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
WHERE o.status IN ('" . implode( "','", $statuses ) . "')
AND im.meta_key IN ('_product_id', '_variation_id' )
AND im.meta_value != 0
AND ( o.customer_id IN ('" . implode( "','", $customer_data ) . "') OR o.billing_email IN ('" . implode( "','", $customer_data ) . "') )

";
			$result = $wpdb->get_col( $sql );
		} else {
			$result = $wpdb->get_col(
				"
SELECT im.meta_value FROM {$wpdb->posts} AS p
INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $statuses ) . "' )
AND pm.meta_key IN ( '_billing_email', '_customer_user' )
AND im.meta_key IN ( '_product_id', '_variation_id' )
AND im.meta_value != 0
AND pm.meta_value IN ( '" . implode( "','", $customer_data ) . "' )
		"
			); // WPCS: unprepared SQL ok.
		}
		$result = array_map( 'absint', $result );

		$transient_value = array(
			'version' => $transient_version,
			'value'   => $result,
		);

		set_transient( $transient_name, $transient_value, DAY_IN_SECONDS * 30 );
	}
	return in_array( absint( $product_id ), $result, true );
}

/**
 * Checks if the current user has a role.
 *
 * @param string $role The role.
 * @return bool
 */
function wc_current_user_has_role( $role ) {
	return wc_user_has_role( wp_get_current_user(), $role );
}

/**
 * Checks if a user has a role.
 *
 * @param int|\WP_User $user The user.
 * @param string       $role The role.
 * @return bool
 */
function wc_user_has_role( $user, $role ) {
	if ( ! is_object( $user ) ) {
		$user = get_userdata( $user );
	}

	if ( ! $user || ! $user->exists() ) {
		return false;
	}

	return in_array( $role, $user->roles, true );
}

/**
 * Checks if a user has a certain capability.
 *
 * @param array $allcaps All capabilities.
 * @param array $caps    Capabilities.
 * @param array $args    Arguments.
 *
 * @return array The filtered array of all capabilities.
 */
function wc_customer_has_capability( $allcaps, $caps, $args ) {
	if ( isset( $caps[0] ) ) {
		switch ( $caps[0] ) {
			case 'view_order':
				$user_id = intval( $args[1] );
				$order   = wc_get_order( $args[2] );

				if ( $order && $user_id === $order->get_user_id() ) {
					$allcaps['view_order'] = true;
				}
				break;
			case 'pay_for_order':
				$user_id  = intval( $args[1] );
				$order_id = isset( $args[2] ) ? $args[2] : null;

				// When no order ID, we assume it's a new order
				// and thus, customer can pay for it.
				if ( ! $order_id ) {
					$allcaps['pay_for_order'] = true;
					break;
				}

				$order = wc_get_order( $order_id );

				if ( $order && ( $user_id === $order->get_user_id() || ! $order->get_user_id() ) ) {
					$allcaps['pay_for_order'] = true;
				}
				break;
			case 'order_again':
				$user_id = intval( $args[1] );
				$order   = wc_get_order( $args[2] );

				if ( $order && $user_id === $order->get_user_id() ) {
					$allcaps['order_again'] = true;
				}
				break;
			case 'cancel_order':
				$user_id = intval( $args[1] );
				$order   = wc_get_order( $args[2] );

				if ( $order && $user_id === $order->get_user_id() ) {
					$allcaps['cancel_order'] = true;
				}
				break;
			case 'download_file':
				$user_id  = intval( $args[1] );
				$download = $args[2];

				if ( $download && $user_id === $download->get_user_id() ) {
					$allcaps['download_file'] = true;
				}
				break;
		}
	}
	return $allcaps;
}
add_filter( 'user_has_cap', 'wc_customer_has_capability', 10, 3 );

/**
 * Safe way of allowing shop managers restricted capabilities that will remove
 * access to the capabilities if WooCommerce is deactivated.
 *
 * @since 3.5.4
 * @param bool[]   $allcaps Array of key/value pairs where keys represent a capability name and boolean values
 *                          represent whether the user has that capability.
 * @param string[] $caps    Required primitive capabilities for the requested capability.
 * @param array    $args Arguments that accompany the requested capability check.
 * @param WP_User  $user    The user object.
 * @return bool[]
 */
function wc_shop_manager_has_capability( $allcaps, $caps, $args, $user ) {

	if ( wc_user_has_role( $user, 'shop_manager' ) ) {
		// @see wc_modify_map_meta_cap, which limits editing to customers.
		$allcaps['edit_users'] = true;
	}

	return $allcaps;
}
add_filter( 'user_has_cap', 'wc_shop_manager_has_capability', 10, 4 );

/**
 * Modify the list of editable roles to prevent non-admin adding admin users.
 *
 * @param  array $roles Roles.
 * @return array
 */
function wc_modify_editable_roles( $roles ) {
	if ( is_multisite() && is_super_admin() ) {
		return $roles;
	}
	if ( ! wc_current_user_has_role( 'administrator' ) ) {
		unset( $roles['administrator'] );

		if ( wc_current_user_has_role( 'shop_manager' ) ) {
			$shop_manager_editable_roles = apply_filters( 'woocommerce_shop_manager_editable_roles', array( 'customer' ) );
			return array_intersect_key( $roles, array_flip( $shop_manager_editable_roles ) );
		}
	}

	return $roles;
}
add_filter( 'editable_roles', 'wc_modify_editable_roles' );

/**
 * Modify capabilities to prevent non-admin users editing admin users.
 *
 * $args[0] will be the user being edited in this case.
 *
 * @param  array  $caps    Array of caps.
 * @param  string $cap     Name of the cap we are checking.
 * @param  int    $user_id ID of the user being checked against.
 * @param  array  $args    Arguments.
 * @return array
 */
function wc_modify_map_meta_cap( $caps, $cap, $user_id, $args ) {
	if ( is_multisite() && is_super_admin() ) {
		return $caps;
	}
	switch ( $cap ) {
		case 'edit_user':
		case 'remove_user':
		case 'promote_user':
		case 'delete_user':
			if ( ! isset( $args[0] ) || $args[0] === $user_id ) {
				break;
			} else {
				if ( ! wc_current_user_has_role( 'administrator' ) ) {
					if ( wc_user_has_role( $args[0], 'administrator' ) ) {
						$caps[] = 'do_not_allow';
					} elseif ( wc_current_user_has_role( 'shop_manager' ) ) {
						// Shop managers can only edit customer info.
						$userdata                    = get_userdata( $args[0] );
						$shop_manager_editable_roles = apply_filters( 'woocommerce_shop_manager_editable_roles', array( 'customer' ) );
						if ( property_exists( $userdata, 'roles' ) && ! empty( $userdata->roles ) && ! array_intersect( $userdata->roles, $shop_manager_editable_roles ) ) {
							$caps[] = 'do_not_allow';
						}
					}
				}
			}
			break;
	}
	return $caps;
}
add_filter( 'map_meta_cap', 'wc_modify_map_meta_cap', 10, 4 );

/**
 * Get customer download permissions from the database.
 *
 * @param int $customer_id Customer/User ID.
 * @return array
 */
function wc_get_customer_download_permissions( $customer_id ) {
	$data_store = WC_Data_Store::load( 'customer-download' );
	return apply_filters( 'woocommerce_permission_list', $data_store->get_downloads_for_customer( $customer_id ), $customer_id );
}

/**
 * Get customer available downloads.
 *
 * @param int $customer_id Customer/User ID.
 * @return array
 */
function wc_get_customer_available_downloads( $customer_id ) {
	$downloads   = array();
	$_product    = null;
	$order       = null;
	$file_number = 0;

	// Get results from valid orders only.
	$results = wc_get_customer_download_permissions( $customer_id );

	if ( $results ) {
		foreach ( $results as $result ) {
			$order_id = intval( $result->order_id );

			if ( ! $order || $order->get_id() !== $order_id ) {
				// New order.
				$order    = wc_get_order( $order_id );
				$_product = null;
			}

			// Make sure the order exists for this download.
			if ( ! $order ) {
				continue;
			}

			// Check if downloads are permitted.
			if ( ! $order->is_download_permitted() ) {
				continue;
			}

			$product_id = intval( $result->product_id );

			if ( ! $_product || $_product->get_id() !== $product_id ) {
				// New product.
				$file_number = 0;
				$_product    = wc_get_product( $product_id );
			}

			// Check product exists and has the file.
			if ( ! $_product || ! $_product->exists() || ! $_product->has_file( $result->download_id ) ) {
				continue;
			}

			$download_file = $_product->get_file( $result->download_id );

			// If the downloadable file has been disabled (it may be located in an untrusted location) then do not return it.
			if ( ! $download_file->get_enabled() ) {
				continue;
			}

			// Download name will be 'Product Name' for products with a single downloadable file, and 'Product Name - File X' for products with multiple files.
			$download_name = apply_filters(
				'woocommerce_downloadable_product_name',
				$download_file['name'],
				$_product,
				$result->download_id,
				$file_number
			);

			$downloads[] = array(
				'download_url'        => add_query_arg(
					array(
						'download_file' => $product_id,
						'order'         => $result->order_key,
						'email'         => rawurlencode( $result->user_email ),
						'key'           => $result->download_id,
					),
					home_url( '/' )
				),
				'download_id'         => $result->download_id,
				'product_id'          => $_product->get_id(),
				'product_name'        => $_product->get_name(),
				'product_url'         => $_product->is_visible() ? $_product->get_permalink() : '', // Since 3.3.0.
				'download_name'       => $download_name,
				'order_id'            => $order->get_id(),
				'order_key'           => $order->get_order_key(),
				'downloads_remaining' => $result->downloads_remaining,
				'access_expires'      => $result->access_expires,
				'file'                => array(
					'name' => $download_file->get_name(),
					'file' => $download_file->get_file(),
				),
			);

			$file_number++;
		}
	}

	return apply_filters( 'woocommerce_customer_available_downloads', $downloads, $customer_id );
}

/**
 * Get total spent by customer.
 *
 * @param  int $user_id User ID.
 * @return string
 */
function wc_get_customer_total_spent( $user_id ) {
	$customer = new WC_Customer( $user_id );
	return $customer->get_total_spent();
}

/**
 * Get total orders by customer.
 *
 * @param  int $user_id User ID.
 * @return int
 */
function wc_get_customer_order_count( $user_id ) {
	$customer = new WC_Customer( $user_id );
	return $customer->get_order_count();
}

/**
 * Reset _customer_user on orders when a user is deleted.
 *
 * @param int $user_id User ID.
 */
function wc_reset_order_customer_id_on_deleted_user( $user_id ) {
	global $wpdb;

	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
		$order_table_ds = wc_get_container()->get( OrdersTableDataStore::class );
		$order_table    = $order_table_ds::get_orders_table_name();
		$wpdb->update(
			$order_table,
			array(
				'customer_id'      => 0,
				'date_updated_gmt' => current_time( 'mysql', true ),
			),
			array(
				'customer_id' => $user_id,
			),
			array(
				'%d',
				'%s',
			),
			array(
				'%d',
			)
		);
	}

	if ( ! OrderUtil::custom_orders_table_usage_is_enabled() || OrderUtil::is_custom_order_tables_in_sync() ) {
		$wpdb->update(
			$wpdb->postmeta,
			array(
				'meta_value' => 0, //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			),
			array(
				'meta_key'   => '_customer_user', //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value' => $user_id, //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
	}
}

add_action( 'deleted_user', 'wc_reset_order_customer_id_on_deleted_user' );

/**
 * Get review verification status.
 *
 * @param  int $comment_id Comment ID.
 * @return bool
 */
function wc_review_is_from_verified_owner( $comment_id ) {
	$verified = get_comment_meta( $comment_id, 'verified', true );
	return '' === $verified ? WC_Comments::add_comment_purchase_verification( $comment_id ) : (bool) $verified;
}

/**
 * Disable author archives for customers.
 *
 * @since 2.5.0
 */
function wc_disable_author_archives_for_customers() {
	global $author;

	if ( is_author() ) {
		$user = get_user_by( 'id', $author );

		if ( user_can( $user, 'customer' ) && ! user_can( $user, 'edit_posts' ) ) {
			wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
			exit;
		}
	}
}

add_action( 'template_redirect', 'wc_disable_author_archives_for_customers' );

/**
 * Hooks into the `profile_update` hook to set the user last updated timestamp.
 *
 * @since 2.6.0
 * @param int   $user_id The user that was updated.
 * @param array $old     The profile fields pre-change.
 */
function wc_update_profile_last_update_time( $user_id, $old ) {
	wc_set_user_last_update_time( $user_id );
}

add_action( 'profile_update', 'wc_update_profile_last_update_time', 10, 2 );

/**
 * Hooks into the update user meta function to set the user last updated timestamp.
 *
 * @since 2.6.0
 * @param int    $meta_id     ID of the meta object that was changed.
 * @param int    $user_id     The user that was updated.
 * @param string $meta_key    Name of the meta key that was changed.
 * @param mixed  $_meta_value Value of the meta that was changed.
 */
function wc_meta_update_last_update_time( $meta_id, $user_id, $meta_key, $_meta_value ) {
	$keys_to_track = apply_filters( 'woocommerce_user_last_update_fields', array( 'first_name', 'last_name' ) );

	$update_time = in_array( $meta_key, $keys_to_track, true ) ? true : false;
	$update_time = 'billing_' === substr( $meta_key, 0, 8 ) ? true : $update_time;
	$update_time = 'shipping_' === substr( $meta_key, 0, 9 ) ? true : $update_time;

	if ( $update_time ) {
		wc_set_user_last_update_time( $user_id );
	}
}

add_action( 'update_user_meta', 'wc_meta_update_last_update_time', 10, 4 );

/**
 * Sets a user's "last update" time to the current timestamp.
 *
 * @since 2.6.0
 * @param int $user_id The user to set a timestamp for.
 */
function wc_set_user_last_update_time( $user_id ) {
	update_user_meta( $user_id, 'last_update', gmdate( 'U' ) );
}

/**
 * Get customer saved payment methods list.
 *
 * @since 2.6.0
 * @param int $customer_id Customer ID.
 * @return array
 */
function wc_get_customer_saved_methods_list( $customer_id ) {
	return apply_filters( 'woocommerce_saved_payment_methods_list', array(), $customer_id );
}

/**
 * Get info about customer's last order.
 *
 * @since 2.6.0
 * @param int $customer_id Customer ID.
 * @return WC_Order|bool Order object if successful or false.
 */
function wc_get_customer_last_order( $customer_id ) {
	$customer = new WC_Customer( $customer_id );

	return $customer->get_last_order();
}

/**
 * Add support for searching by display_name.
 *
 * @since 3.2.0
 * @param array $search_columns Column names.
 * @return array
 */
function wc_user_search_columns( $search_columns ) {
	$search_columns[] = 'display_name';
	return $search_columns;
}
add_filter( 'user_search_columns', 'wc_user_search_columns' );

/**
 * When a user is deleted in WordPress, delete corresponding WooCommerce data.
 *
 * @param int $user_id User ID being deleted.
 */
function wc_delete_user_data( $user_id ) {
	global $wpdb;

	// Clean up sessions.
	$wpdb->delete(
		$wpdb->prefix . 'woocommerce_sessions',
		array(
			'session_key' => $user_id,
		)
	);

	// Revoke API keys.
	$wpdb->delete(
		$wpdb->prefix . 'woocommerce_api_keys',
		array(
			'user_id' => $user_id,
		)
	);

	// Clean up payment tokens.
	$payment_tokens = WC_Payment_Tokens::get_customer_tokens( $user_id );

	foreach ( $payment_tokens as $payment_token ) {
		$payment_token->delete();
	}
}
add_action( 'delete_user', 'wc_delete_user_data' );

/**
 * Store user agents. Used for tracker.
 *
 * @since 3.0.0
 * @param string     $user_login User login.
 * @param int|object $user       User.
 */
function wc_maybe_store_user_agent( $user_login, $user ) {
	if ( 'yes' === get_option( 'woocommerce_allow_tracking', 'no' ) && user_can( $user, 'manage_woocommerce' ) ) {
		$admin_user_agents   = array_filter( (array) get_option( 'woocommerce_tracker_ua', array() ) );
		$admin_user_agents[] = wc_get_user_agent();
		update_option( 'woocommerce_tracker_ua', array_unique( $admin_user_agents ), false );
	}
}
add_action( 'wp_login', 'wc_maybe_store_user_agent', 10, 2 );

/**
 * Update logic triggered on login.
 *
 * @since 3.4.0
 * @param string $user_login User login.
 * @param object $user       User.
 */
function wc_user_logged_in( $user_login, $user ) {
	wc_update_user_last_active( $user->ID );
	update_user_meta( $user->ID, '_woocommerce_load_saved_cart_after_login', 1 );
}
add_action( 'wp_login', 'wc_user_logged_in', 10, 2 );

/**
 * Update when the user was last active.
 *
 * @since 3.4.0
 */
function wc_current_user_is_active() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	wc_update_user_last_active( get_current_user_id() );
}
add_action( 'wp', 'wc_current_user_is_active', 10 );

/**
 * Set the user last active timestamp to now.
 *
 * @since 3.4.0
 * @param int $user_id User ID to mark active.
 */
function wc_update_user_last_active( $user_id ) {
	if ( ! $user_id ) {
		return;
	}
	update_user_meta( $user_id, 'wc_last_active', (string) strtotime( gmdate( 'Y-m-d', time() ) ) );
}

/**
 * Translate WC roles using the woocommerce textdomain.
 *
 * @since 3.7.0
 * @param string $translation  Translated text.
 * @param string $text         Text to translate.
 * @param string $context      Context information for the translators.
 * @param string $domain       Text domain. Unique identifier for retrieving translated strings.
 * @return string
 */
function wc_translate_user_roles( $translation, $text, $context, $domain ) {
	// translate_user_role() only accepts a second parameter starting in WP 5.2.
	if ( version_compare( get_bloginfo( 'version' ), '5.2', '<' ) ) {
		return $translation;
	}

	if ( 'User role' === $context && 'default' === $domain && in_array( $text, array( 'Shop manager', 'Customer' ), true ) ) {
		return translate_user_role( $text, 'woocommerce' );
	}

	return $translation;
}
add_filter( 'gettext_with_context', 'wc_translate_user_roles', 10, 4 );

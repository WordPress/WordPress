<?php
/**
 * WC_Order_Data_Store_CPT class file.
 *
 * @package WooCommerce\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Order Data Store: Stored in CPT.
 *
 * @version  3.0.0
 */
class WC_Order_Data_Store_CPT extends Abstract_WC_Order_Data_Store_CPT implements WC_Object_Data_Store_Interface, WC_Order_Data_Store_Interface {

	/**
	 * Data stored in meta keys, but not considered "meta" for an order.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $internal_meta_keys = array(
		'_customer_user',
		'_order_key',
		'_order_currency',
		'_billing_first_name',
		'_billing_last_name',
		'_billing_company',
		'_billing_address_1',
		'_billing_address_2',
		'_billing_city',
		'_billing_state',
		'_billing_postcode',
		'_billing_country',
		'_billing_email',
		'_billing_phone',
		'_shipping_first_name',
		'_shipping_last_name',
		'_shipping_company',
		'_shipping_address_1',
		'_shipping_address_2',
		'_shipping_city',
		'_shipping_state',
		'_shipping_postcode',
		'_shipping_country',
		'_shipping_phone',
		'_completed_date',
		'_paid_date',
		'_edit_lock',
		'_edit_last',
		'_cart_discount',
		'_cart_discount_tax',
		'_order_shipping',
		'_order_shipping_tax',
		'_order_tax',
		'_order_total',
		'_payment_method',
		'_payment_method_title',
		'_transaction_id',
		'_customer_ip_address',
		'_customer_user_agent',
		'_created_via',
		'_order_version',
		'_prices_include_tax',
		'_date_completed',
		'_date_paid',
		'_payment_tokens',
		'_billing_address_index',
		'_shipping_address_index',
		'_recorded_sales',
		'_recorded_coupon_usage_counts',
		'_download_permissions_granted',
		'_order_stock_reduced',
		'_new_order_email_sent',
	);

	/**
	 * Custom setters for props. Add key here if it has corresponding set_ and get_ method present.
	 *
	 * @var string[]
	 */
	protected $internal_data_store_key_getters = array(
		'_download_permissions_granted' => 'download_permissions_granted',
		'_recorded_sales'               => 'recorded_sales',
		'_recorded_coupon_usage_counts' => 'recorded_coupon_usage_counts',
		'_order_stock_reduced'          => 'order_stock_reduced',
		'_new_order_email_sent'         => 'new_order_email_sent',
	);

	/**
	 * Method to create a new order in the database.
	 *
	 * @param WC_Order $order Order object.
	 */
	public function create( &$order ) {
		if ( '' === $order->get_order_key() ) {
			$order->set_order_key( wc_generate_order_key() );
		}
		parent::create( $order );
		do_action( 'woocommerce_new_order', $order->get_id(), $order );
	}

	/**
	 * Read order data. Can be overridden by child classes to load other props.
	 *
	 * @param WC_Order $order Order object.
	 * @param object   $post_object Post object.
	 * @since 3.0.0
	 */
	protected function read_order_data( &$order, $post_object ) {
		parent::read_order_data( $order, $post_object );
		$id             = $order->get_id();
		$date_completed = get_post_meta( $id, '_date_completed', true );
		$date_paid      = get_post_meta( $id, '_date_paid', true );

		if ( ! $date_completed ) {
			$date_completed = get_post_meta( $id, '_completed_date', true );
		}

		if ( ! $date_paid ) {
			$date_paid = get_post_meta( $id, '_paid_date', true );
		}

		$order->set_props(
			array(
				'order_key'                    => get_post_meta( $id, '_order_key', true ),
				'customer_id'                  => get_post_meta( $id, '_customer_user', true ),
				'billing_first_name'           => get_post_meta( $id, '_billing_first_name', true ),
				'billing_last_name'            => get_post_meta( $id, '_billing_last_name', true ),
				'billing_company'              => get_post_meta( $id, '_billing_company', true ),
				'billing_address_1'            => get_post_meta( $id, '_billing_address_1', true ),
				'billing_address_2'            => get_post_meta( $id, '_billing_address_2', true ),
				'billing_city'                 => get_post_meta( $id, '_billing_city', true ),
				'billing_state'                => get_post_meta( $id, '_billing_state', true ),
				'billing_postcode'             => get_post_meta( $id, '_billing_postcode', true ),
				'billing_country'              => get_post_meta( $id, '_billing_country', true ),
				'billing_email'                => get_post_meta( $id, '_billing_email', true ),
				'billing_phone'                => get_post_meta( $id, '_billing_phone', true ),
				'shipping_first_name'          => get_post_meta( $id, '_shipping_first_name', true ),
				'shipping_last_name'           => get_post_meta( $id, '_shipping_last_name', true ),
				'shipping_company'             => get_post_meta( $id, '_shipping_company', true ),
				'shipping_address_1'           => get_post_meta( $id, '_shipping_address_1', true ),
				'shipping_address_2'           => get_post_meta( $id, '_shipping_address_2', true ),
				'shipping_city'                => get_post_meta( $id, '_shipping_city', true ),
				'shipping_state'               => get_post_meta( $id, '_shipping_state', true ),
				'shipping_postcode'            => get_post_meta( $id, '_shipping_postcode', true ),
				'shipping_country'             => get_post_meta( $id, '_shipping_country', true ),
				'shipping_phone'               => get_post_meta( $id, '_shipping_phone', true ),
				'payment_method'               => get_post_meta( $id, '_payment_method', true ),
				'payment_method_title'         => get_post_meta( $id, '_payment_method_title', true ),
				'transaction_id'               => get_post_meta( $id, '_transaction_id', true ),
				'customer_ip_address'          => get_post_meta( $id, '_customer_ip_address', true ),
				'customer_user_agent'          => get_post_meta( $id, '_customer_user_agent', true ),
				'created_via'                  => get_post_meta( $id, '_created_via', true ),
				'date_completed'               => $date_completed,
				'date_paid'                    => $date_paid,
				'cart_hash'                    => get_post_meta( $id, '_cart_hash', true ),
				'customer_note'                => $post_object->post_excerpt,

				// Operational data props.
				'order_stock_reduced'          => get_post_meta( $id, '_order_stock_reduced', true ),
				'download_permissions_granted' => get_post_meta( $id, '_download_permissions_granted', true ),
				'new_order_email_sent'         => get_post_meta( $id, '_new_order_email_sent', true ),
				'recorded_sales'               => wc_string_to_bool( get_post_meta( $id, '_recorded_sales', true ) ),
				'recorded_coupon_usage_counts' => get_post_meta( $id, '_recorded_coupon_usage_counts', true ),
			)
		);
	}

	/**
	 * Method to update an order in the database.
	 *
	 * @param WC_Order $order Order object.
	 */
	public function update( &$order ) {
		// Before updating, ensure date paid is set if missing.
		if ( ! $order->get_date_paid( 'edit' ) && version_compare( $order->get_version( 'edit' ), '3.0', '<' ) && $order->has_status( apply_filters( 'woocommerce_payment_complete_order_status', $order->needs_processing() ? 'processing' : 'completed', $order->get_id(), $order ) ) ) {
			$order->set_date_paid( $order->get_date_created( 'edit' ) );
		}

		// Also grab the current status so we can compare.
		$previous_status = get_post_status( $order->get_id() );

		// Update the order.
		parent::update( $order );

		// Fire a hook depending on the status - this should be considered a creation if it was previously draft status.
		$new_status = $order->get_status( 'edit' );

		if ( $new_status !== $previous_status && in_array( $previous_status, array( 'new', 'auto-draft', 'draft' ), true ) ) {
			do_action( 'woocommerce_new_order', $order->get_id(), $order );
		} else {
			do_action( 'woocommerce_update_order', $order->get_id(), $order );
		}
	}

	/**
	 * Helper method that updates all the post meta for an order based on it's settings in the WC_Order class.
	 *
	 * @param WC_Order $order Order object.
	 * @since 3.0.0
	 */
	protected function update_post_meta( &$order ) {
		$updated_props     = array();
		$id                = $order->get_id();
		$meta_key_to_props = array(
			'_order_key'                    => 'order_key',
			'_customer_user'                => 'customer_id',
			'_payment_method'               => 'payment_method',
			'_payment_method_title'         => 'payment_method_title',
			'_transaction_id'               => 'transaction_id',
			'_customer_ip_address'          => 'customer_ip_address',
			'_customer_user_agent'          => 'customer_user_agent',
			'_created_via'                  => 'created_via',
			'_date_completed'               => 'date_completed',
			'_date_paid'                    => 'date_paid',
			'_cart_hash'                    => 'cart_hash',
			'_download_permissions_granted' => 'download_permissions_granted',
			'_recorded_sales'               => 'recorded_sales',
			'_recorded_coupon_usage_counts' => 'recorded_coupon_usage_counts',
			'_new_order_email_sent'         => 'new_order_email_sent',
			'_order_stock_reduced'          => 'order_stock_reduced',
		);

		$props_to_update = $this->get_props_to_update( $order, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $order->{"get_$prop"}( 'edit' );
			$value = is_string( $value ) ? wp_slash( $value ) : $value;
			switch ( $prop ) {
				case 'date_paid':
				case 'date_completed':
					$value = ! is_null( $value ) ? $value->getTimestamp() : '';
					break;
				case 'download_permissions_granted':
				case 'recorded_sales':
				case 'recorded_coupon_usage_counts':
				case 'order_stock_reduced':
					if ( is_null( $value ) || '' === $value ) {
						break;
					}
					$value = is_bool( $value ) ? wc_bool_to_string( $value ) : $value;
					break;
				case 'new_order_email_sent':
					if ( is_null( $value ) || '' === $value ) {
						break;
					}
					$value = is_bool( $value ) ? wc_bool_to_string( $value ) : $value;
					$value = 'yes' === $value ? 'true' : 'false'; // For backward compatibility, we store as true/false in DB.
					break;
			}

			// We want to persist internal data store keys as 'yes' or 'no' if they are boolean to maintain compatibility.
			if ( is_bool( $value ) && in_array( $prop, array_values( $this->internal_data_store_key_getters ), true ) ) {
				$value = wc_bool_to_string( $value );
			}

			$updated = $this->update_or_delete_post_meta( $order, $meta_key, $value );

			if ( $updated ) {
				$updated_props[] = $prop;
			}
		}

		$address_props = array(
			'billing'  => array(
				'_billing_first_name' => 'billing_first_name',
				'_billing_last_name'  => 'billing_last_name',
				'_billing_company'    => 'billing_company',
				'_billing_address_1'  => 'billing_address_1',
				'_billing_address_2'  => 'billing_address_2',
				'_billing_city'       => 'billing_city',
				'_billing_state'      => 'billing_state',
				'_billing_postcode'   => 'billing_postcode',
				'_billing_country'    => 'billing_country',
				'_billing_email'      => 'billing_email',
				'_billing_phone'      => 'billing_phone',
			),
			'shipping' => array(
				'_shipping_first_name' => 'shipping_first_name',
				'_shipping_last_name'  => 'shipping_last_name',
				'_shipping_company'    => 'shipping_company',
				'_shipping_address_1'  => 'shipping_address_1',
				'_shipping_address_2'  => 'shipping_address_2',
				'_shipping_city'       => 'shipping_city',
				'_shipping_state'      => 'shipping_state',
				'_shipping_postcode'   => 'shipping_postcode',
				'_shipping_country'    => 'shipping_country',
				'_shipping_phone'      => 'shipping_phone',
			),
		);

		foreach ( $address_props as $props_key => $props ) {
			$props_to_update = $this->get_props_to_update( $order, $props );
			foreach ( $props_to_update as $meta_key => $prop ) {
				$value   = $order->{"get_$prop"}( 'edit' );
				$value   = is_string( $value ) ? wp_slash( $value ) : $value;
				$updated = $this->update_or_delete_post_meta( $order, $meta_key, $value );

				if ( $updated ) {
					$updated_props[] = $prop;
					$updated_props[] = $props_key;
				}
			}
		}

		parent::update_post_meta( $order );

		// If address changed, store concatenated version to make searches faster.
		if ( in_array( 'billing', $updated_props, true ) || ! metadata_exists( 'post', $id, '_billing_address_index' ) ) {
			update_post_meta( $id, '_billing_address_index', implode( ' ', $order->get_address( 'billing' ) ) );
		}
		if ( in_array( 'shipping', $updated_props, true ) || ! metadata_exists( 'post', $id, '_shipping_address_index' ) ) {
			update_post_meta( $id, '_shipping_address_index', implode( ' ', $order->get_address( 'shipping' ) ) );
		}

		// Legacy date handling. @todo remove in 4.0.
		if ( in_array( 'date_paid', $updated_props, true ) ) {
			$value = $order->get_date_paid( 'edit' );
			// In 2.6.x date_paid was stored as _paid_date in local mysql format.
			update_post_meta( $id, '_paid_date', ! is_null( $value ) ? $value->date( 'Y-m-d H:i:s' ) : '' );
		}

		if ( in_array( 'date_completed', $updated_props, true ) ) {
			$value = $order->get_date_completed( 'edit' );
			// In 2.6.x date_completed was stored as _completed_date in local mysql format.
			update_post_meta( $id, '_completed_date', ! is_null( $value ) ? $value->date( 'Y-m-d H:i:s' ) : '' );
		}

		// If customer changed, update any downloadable permissions.
		if ( in_array( 'customer_id', $updated_props, true ) || in_array( 'billing_email', $updated_props, true ) ) {
			$data_store = WC_Data_Store::load( 'customer-download' );
			$data_store->update_user_by_order_id( $id, $order->get_customer_id(), $order->get_billing_email() );
		}

		// Mark user account as active.
		if ( in_array( 'customer_id', $updated_props, true ) ) {
			wc_update_user_last_active( $order->get_customer_id() );
		}

		do_action( 'woocommerce_order_object_updated_props', $order, $updated_props );
	}

	/**
	 * Excerpt for post.
	 *
	 * @param  WC_Order $order Order object.
	 * @return string
	 */
	protected function get_post_excerpt( $order ) {
		return $order->get_customer_note();
	}

	/**
	 * Get order key.
	 *
	 * @since 4.3.0
	 * @param WC_order $order Order object.
	 * @return string
	 */
	protected function get_order_key( $order ) {
		if ( '' !== $order->get_order_key() ) {
			return $order->get_order_key();
		}

		return parent::get_order_key( $order );
	}

	/**
	 * Get amount already refunded.
	 *
	 * @param  WC_Order $order Order object.
	 * @return float
	 */
	public function get_total_refunded( $order ) {
		global $wpdb;

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM( postmeta.meta_value )
				FROM $wpdb->postmeta AS postmeta
				INNER JOIN $wpdb->posts AS posts ON ( posts.post_type = 'shop_order_refund' AND posts.post_parent = %d )
				WHERE postmeta.meta_key = '_refund_amount'
				AND postmeta.post_id = posts.ID",
				$order->get_id()
			)
		);

		return floatval( $total );
	}

	/**
	 * Get the total tax refunded.
	 *
	 * @param  WC_Order $order Order object.
	 * @return float
	 */
	public function get_total_tax_refunded( $order ) {
		global $wpdb;

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM( order_itemmeta.meta_value )
				FROM {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
				INNER JOIN $wpdb->posts AS posts ON ( posts.post_type = 'shop_order_refund' AND posts.post_parent = %d )
				INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON ( order_items.order_id = posts.ID AND order_items.order_item_type = 'tax' )
				WHERE order_itemmeta.order_item_id = order_items.order_item_id
				AND order_itemmeta.meta_key IN ('tax_amount', 'shipping_tax_amount')",
				$order->get_id()
			)
		) ?? 0;

		return abs( $total );
	}

	/**
	 * Get the total shipping refunded.
	 *
	 * @param  WC_Order $order Order object.
	 * @return float
	 */
	public function get_total_shipping_refunded( $order ) {
		global $wpdb;

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM( order_itemmeta.meta_value )
				FROM {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
				INNER JOIN $wpdb->posts AS posts ON ( posts.post_type = 'shop_order_refund' AND posts.post_parent = %d )
				INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON ( order_items.order_id = posts.ID AND order_items.order_item_type = 'shipping' )
				WHERE order_itemmeta.order_item_id = order_items.order_item_id
				AND order_itemmeta.meta_key IN ('cost')",
				$order->get_id()
			)
		) ?? 0;

		return abs( $total );
	}

	/**
	 * Finds an Order ID based on an order key.
	 *
	 * @param string $order_key An order key has generated by.
	 * @return int The ID of an order, or 0 if the order could not be found
	 */
	public function get_order_id_by_order_key( $order_key ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_order_key' AND meta_value = %s", $order_key ) );
	}

	/**
	 * Return count of orders with a specific status.
	 *
	 * @param  string $status Order status. Function wc_get_order_statuses() returns a list of valid statuses.
	 * @return int
	 */
	public function get_order_count( $status ) {
		global $wpdb;
		return absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( * ) FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND post_status = %s", $status ) ) );
	}

	/**
	 * Get all orders matching the passed in args.
	 *
	 * @deprecated 3.1.0 - Use wc_get_orders instead.
	 * @see    wc_get_orders()
	 *
	 * @param  array $args List of args passed to wc_get_orders().
	 *
	 * @return array|object
	 */
	public function get_orders( $args = array() ) {
		wc_deprecated_function( 'WC_Order_Data_Store_CPT::get_orders', '3.1.0', 'Use wc_get_orders instead.' );
		return wc_get_orders( $args );
	}

	/**
	 * Generate meta query for wc_get_orders.
	 *
	 * @param  array  $values List of customers ids or emails.
	 * @param  string $relation 'or' or 'and' relation used to build the WP meta_query.
	 * @return array
	 */
	private function get_orders_generate_customer_meta_query( $values, $relation = 'or' ) {
		$meta_query = array(
			'relation'        => strtoupper( $relation ),
			'customer_emails' => array(
				'key'     => '_billing_email',
				'value'   => array(),
				'compare' => 'IN',
			),
			'customer_ids'    => array(
				'key'     => '_customer_user',
				'value'   => array(),
				'compare' => 'IN',
			),
		);
		foreach ( $values as $value ) {
			if ( is_array( $value ) ) {
				$query_part = $this->get_orders_generate_customer_meta_query( $value, 'and' );
				if ( is_wp_error( $query_part ) ) {
					return $query_part;
				}
				$meta_query[] = $query_part;
			} elseif ( is_email( $value ) ) {
				$meta_query['customer_emails']['value'][] = sanitize_email( $value );
			} elseif ( is_numeric( $value ) ) {
				$meta_query['customer_ids']['value'][] = strval( absint( $value ) );
			} else {
				return new WP_Error( 'woocommerce_query_invalid', __( 'Invalid customer query.', 'woocommerce' ), $values );
			}
		}

		if ( empty( $meta_query['customer_emails']['value'] ) ) {
			unset( $meta_query['customer_emails'] );
			unset( $meta_query['relation'] );
		}

		if ( empty( $meta_query['customer_ids']['value'] ) ) {
			unset( $meta_query['customer_ids'] );
			unset( $meta_query['relation'] );
		}

		return $meta_query;
	}

	/**
	 * Get unpaid orders after a certain date,
	 *
	 * @param  int $date Timestamp.
	 * @return array
	 */
	public function get_unpaid_orders( $date ) {
		global $wpdb;

		$unpaid_orders = $wpdb->get_col(
			$wpdb->prepare(
			// @codingStandardsIgnoreStart
				"SELECT posts.ID
				FROM {$wpdb->posts} AS posts
				WHERE   posts.post_type   IN ('" . implode( "','", wc_get_order_types() ) . "')
				AND     posts.post_status = 'wc-pending'
				AND     posts.post_modified < %s",
				// @codingStandardsIgnoreEnd
				gmdate( 'Y-m-d H:i:s', absint( $date ) )
			)
		);

		return $unpaid_orders;
	}

	/**
	 * Search order data for a term and return ids.
	 *
	 * @param  string $term Searched term.
	 * @return array of ids
	 */
	public function search_orders( $term ) {
		global $wpdb;

		/**
		 * Searches on meta data can be slow - this lets you choose what fields to search.
		 * 3.0.0 added _billing_address and _shipping_address meta which contains all address data to make this faster.
		 * This however won't work on older orders unless updated, so search a few others (expand this using the filter if needed).
		 *
		 * @var array
		 */
		$search_fields = array_map(
			'wc_clean',
			apply_filters(
				'woocommerce_shop_order_search_fields',
				array(
					'_billing_address_index',
					'_shipping_address_index',
					'_billing_last_name',
					'_billing_email',
					'_billing_phone',
				)
			)
		);
		$order_ids     = array();

		if ( is_numeric( $term ) ) {
			$order_ids[] = absint( $term );
		}

		if ( ! empty( $search_fields ) ) {
			$order_ids = array_unique(
				array_merge(
					$order_ids,
					$wpdb->get_col(
						$wpdb->prepare(
							"SELECT DISTINCT p1.post_id FROM {$wpdb->postmeta} p1 WHERE p1.meta_value LIKE %s AND p1.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $search_fields ) ) . "')", // @codingStandardsIgnoreLine
							'%' . $wpdb->esc_like( wc_clean( $term ) ) . '%'
						)
					),
					$wpdb->get_col(
						$wpdb->prepare(
							"SELECT order_id
							FROM {$wpdb->prefix}woocommerce_order_items as order_items
							WHERE order_item_name LIKE %s",
							'%' . $wpdb->esc_like( wc_clean( $term ) ) . '%'
						)
					),
					$wpdb->get_col(
						$wpdb->prepare(
							"SELECT DISTINCT os.order_id FROM {$wpdb->prefix}wc_order_stats os
							INNER JOIN {$wpdb->prefix}wc_customer_lookup cl ON os.customer_id = cl.customer_id
							INNER JOIN {$wpdb->usermeta} um ON cl.user_id = um.user_id
							WHERE (um.meta_key = 'billing_phone' OR um.meta_key = 'shipping_phone')
							AND um.meta_value = %s",
							wc_clean( $term )
						)
					)
				)
			);
		}

		return apply_filters( 'woocommerce_shop_order_search_results', $order_ids, $term, $search_fields );
	}

	/**
	 * Gets information about whether permissions were generated yet.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @return bool
	 */
	public function get_download_permissions_granted( $order ) {
		$order_id = WC_Order_Factory::get_order_id( $order );
		return wc_string_to_bool( get_post_meta( $order_id, '_download_permissions_granted', true ) );
	}

	/**
	 * Stores information about whether permissions were generated yet.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @param bool         $set True or false.
	 */
	public function set_download_permissions_granted( $order, $set ) {
		if ( $order instanceof WC_Order ) {
			$order->set_download_permissions_granted( $set );
		}
		$order_id = WC_Order_Factory::get_order_id( $order );
		update_post_meta( $order_id, '_download_permissions_granted', wc_bool_to_string( $set ) );
	}

	/**
	 * Gets information about whether sales were recorded.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @return bool
	 */
	public function get_recorded_sales( $order ) {
		$order_id = WC_Order_Factory::get_order_id( $order );
		return wc_string_to_bool( get_post_meta( $order_id, '_recorded_sales', true ) );
	}

	/**
	 * Stores information about whether sales were recorded.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @param bool         $set True or false.
	 */
	public function set_recorded_sales( $order, $set ) {
		if ( $order instanceof WC_Order ) {
			$order->set_recorded_sales( $set );
		}
		$order_id = WC_Order_Factory::get_order_id( $order );
		update_post_meta( $order_id, '_recorded_sales', wc_bool_to_string( $set ) );
	}

	/**
	 * Gets information about whether coupon counts were updated.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @return bool
	 */
	public function get_recorded_coupon_usage_counts( $order ) {
		$order_id = WC_Order_Factory::get_order_id( $order );
		return wc_string_to_bool( get_post_meta( $order_id, '_recorded_coupon_usage_counts', true ) );
	}

	/**
	 * Stores information about whether coupon counts were updated.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @param bool         $set True or false.
	 */
	public function set_recorded_coupon_usage_counts( $order, $set ) {
		if ( $order instanceof WC_Order ) {
			$order->set_recorded_coupon_usage_counts( $set );
		}
		$order_id = WC_Order_Factory::get_order_id( $order );
		update_post_meta( $order_id, '_recorded_coupon_usage_counts', wc_bool_to_string( $set ) );
	}

	/**
	 * Whether email have been sent for this order.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 *
	 * @return bool               Whether email is sent.
	 */
	public function get_email_sent( $order ) {
		$order_id = WC_Order_Factory::get_order_id( $order );
		return wc_string_to_bool( get_post_meta( $order_id, '_new_order_email_sent', true ) );
	}

	/**
	 * Whether email have been sent for this order.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 *
	 * @return bool               Whether email is sent.
	 */
	public function get_new_order_email_sent( $order ) {
		return $this->get_email_sent( $order );
	}

	/**
	 * Stores information about whether email was sent.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @param bool         $set True or false.
	 */
	public function set_email_sent( $order, $set ) {
		if ( $order instanceof WC_Order ) {
			$order->set_new_order_email_sent( $set );
		}
		$order_id = WC_Order_Factory::get_order_id( $order );
		$value    = wc_bool_to_string( $set );
		$value    = 'yes' === $value ? 'true' : 'false'; // For backward compat, we store this as true|false string.
		update_post_meta( $order_id, '_new_order_email_sent', $value );
	}

	/**
	 * Stores information about whether email was sent.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @param bool         $set True or false.
	 */
	public function set_new_order_email_sent( $order, $set ) {
		$this->set_email_sent( $order, $set );
	}

	/**
	 * Return array of coupon_code => meta_key for coupon which have usage limit and have tentative keys.
	 * Pass $coupon_id if key for only one of the coupon is needed.
	 *
	 * @param WC_Order $order     Order object.
	 * @param int      $coupon_id If passed, will return held key for that coupon.
	 *
	 * @return array|string Key value pair for coupon code and meta key name. If $coupon_id is passed, returns meta_key for only that coupon.
	 */
	public function get_coupon_held_keys( $order, $coupon_id = null ) {
		$held_keys = $order->get_meta( '_coupon_held_keys' );
		if ( $coupon_id ) {
			return isset( $held_keys[ $coupon_id ] ) ? $held_keys[ $coupon_id ] : null;
		}
		return $held_keys;
	}

	/**
	 * Return array of coupon_code => meta_key for coupon which have usage limit per customer and have tentative keys.
	 *
	 * @param WC_Order $order Order object.
	 * @param int      $coupon_id If passed, will return held key for that coupon.
	 *
	 * @return mixed
	 */
	public function get_coupon_held_keys_for_users( $order, $coupon_id = null ) {
		$held_keys_for_user = $order->get_meta( '_coupon_held_keys_for_users' );
		if ( $coupon_id ) {
			return isset( $held_keys_for_user[ $coupon_id ] ) ? $held_keys_for_user[ $coupon_id ] : null;
		}
		return $held_keys_for_user;
	}

	/**
	 * Add/Update list of meta keys that are currently being used by this order to hold a coupon.
	 * This is used to figure out what all meta entries we should delete when order is cancelled/completed.
	 *
	 * @param WC_Order $order              Order object.
	 * @param array    $held_keys          Array of coupon_code => meta_key.
	 * @param array    $held_keys_for_user Array of coupon_code => meta_key for held coupon for user.
	 *
	 * @return mixed
	 */
	public function set_coupon_held_keys( $order, $held_keys, $held_keys_for_user ) {
		if ( is_array( $held_keys ) && 0 < count( $held_keys ) ) {
			$order->update_meta_data( '_coupon_held_keys', $held_keys );
		}
		if ( is_array( $held_keys_for_user ) && 0 < count( $held_keys_for_user ) ) {
			$order->update_meta_data( '_coupon_held_keys_for_users', $held_keys_for_user );
		}
	}

	/**
	 * Release all coupons held by this order.
	 *
	 * @param WC_Order $order Current order object.
	 * @param bool     $save  Whether to delete keys from DB right away. Could be useful to pass `false` if you are building a bulk request.
	 */
	public function release_held_coupons( $order, $save = true ) {
		$coupon_held_keys = $this->get_coupon_held_keys( $order );
		if ( is_array( $coupon_held_keys ) ) {
			foreach ( $coupon_held_keys as $coupon_id => $meta_key ) {
				delete_post_meta( $coupon_id, $meta_key );
			}
		}
		$order->delete_meta_data( '_coupon_held_keys' );

		$coupon_held_keys_for_users = $this->get_coupon_held_keys_for_users( $order );
		if ( is_array( $coupon_held_keys_for_users ) ) {
			foreach ( $coupon_held_keys_for_users as $coupon_id => $meta_key ) {
				delete_post_meta( $coupon_id, $meta_key );
			}
		}
		$order->delete_meta_data( '_coupon_held_keys_for_users' );

		if ( $save ) {
			$order->save_meta_data();
		}

	}

	/**
	 * Gets information about whether stock was reduced.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @return bool
	 */
	public function get_stock_reduced( $order ) {
		$order_id = WC_Order_Factory::get_order_id( $order );
		return wc_string_to_bool( get_post_meta( $order_id, '_order_stock_reduced', true ) );
	}

	/**
	 * Stores information about whether stock was reduced.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @return bool
	 */
	public function get_order_stock_reduced( $order ) {
		return $this->get_stock_reduced( $order );
	}

	/**
	 * Stores information about whether stock was reduced.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @param bool         $set True or false.
	 */
	public function set_stock_reduced( $order, $set ) {
		if ( $order instanceof WC_Order ) {
			$order->set_order_stock_reduced( $set );
		}
		$order_id = WC_Order_Factory::get_order_id( $order );
		update_post_meta( $order_id, '_order_stock_reduced', wc_bool_to_string( $set ) );
	}

	/**
	 * Gets information about whether stock was reduced.
	 *
	 * @param WC_Order|int $order Order ID or order object.
	 * @param bool         $set True or false.
	 */
	public function set_order_stock_reduced( $order, $set ) {
		$this->set_stock_reduced( $order, $set );
	}

	/**
	 * Get the order type based on Order ID.
	 *
	 * @since 3.0.0
	 * @param int|WP_Post $order Order | Order id.
	 *
	 * @return string
	 */
	public function get_order_type( $order ) {
		return get_post_type( $order );
	}

	/**
	 * Get valid WP_Query args from a WC_Order_Query's query variables.
	 *
	 * @since 3.1.0
	 * @param array $query_vars query vars from a WC_Order_Query.
	 * @return array
	 */
	protected function get_wp_query_args( $query_vars ) {

		// Map query vars to ones that get_wp_query_args or WP_Query recognize.
		$key_mapping = array(
			'customer_id'    => 'customer_user',
			'status'         => 'post_status',
			'currency'       => 'order_currency',
			'version'        => 'order_version',
			'discount_total' => 'cart_discount',
			'discount_tax'   => 'cart_discount_tax',
			'shipping_total' => 'order_shipping',
			'shipping_tax'   => 'order_shipping_tax',
			'cart_tax'       => 'order_tax',
			'total'          => 'order_total',
			'page'           => 'paged',
		);

		foreach ( $key_mapping as $query_key => $db_key ) {
			if ( isset( $query_vars[ $query_key ] ) ) {
				$query_vars[ $db_key ] = $query_vars[ $query_key ];
				unset( $query_vars[ $query_key ] );
			}
		}

		// Add the 'wc-' prefix to status if needed.
		if ( ! empty( $query_vars['post_status'] ) ) {
			if ( is_array( $query_vars['post_status'] ) ) {
				foreach ( $query_vars['post_status'] as &$status ) {
					$status = wc_is_order_status( 'wc-' . $status ) ? 'wc-' . $status : $status;
				}
			} else {
				$query_vars['post_status'] = wc_is_order_status( 'wc-' . $query_vars['post_status'] ) ? 'wc-' . $query_vars['post_status'] : $query_vars['post_status'];
			}
		}

		$wp_query_args = parent::get_wp_query_args( $query_vars );

		if ( ! isset( $wp_query_args['date_query'] ) ) {
			$wp_query_args['date_query'] = array();
		}
		if ( ! isset( $wp_query_args['meta_query'] ) ) {
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			$wp_query_args['meta_query'] = array();
		}

		$date_queries = array(
			'date_created'   => 'post_date',
			'date_modified'  => 'post_modified',
			'date_completed' => '_date_completed',
			'date_paid'      => '_date_paid',
		);
		foreach ( $date_queries as $query_var_key => $db_key ) {
			if ( isset( $query_vars[ $query_var_key ] ) && '' !== $query_vars[ $query_var_key ] ) {

				// Remove any existing meta queries for the same keys to prevent conflicts.
				$existing_queries = wp_list_pluck( $wp_query_args['meta_query'], 'key', true );
				$meta_query_index = array_search( $db_key, $existing_queries, true );
				if ( false !== $meta_query_index ) {
					unset( $wp_query_args['meta_query'][ $meta_query_index ] );
				}

				$wp_query_args = $this->parse_date_for_wp_query( $query_vars[ $query_var_key ], $db_key, $wp_query_args );
			}
		}

		if ( isset( $query_vars['customer'] ) && '' !== $query_vars['customer'] && array() !== $query_vars['customer'] ) {
			$values         = is_array( $query_vars['customer'] ) ? $query_vars['customer'] : array( $query_vars['customer'] );
			$customer_query = $this->get_orders_generate_customer_meta_query( $values );
			if ( is_wp_error( $customer_query ) ) {
				$wp_query_args['errors'][] = $customer_query;
			} else {
				$wp_query_args['meta_query'][] = $customer_query;
			}
		}

		if ( isset( $query_vars['anonymized'] ) ) {
			if ( $query_vars['anonymized'] ) {
				$wp_query_args['meta_query'][] = array(
					'key'   => '_anonymized',
					'value' => 'yes',
				);
			} else {
				$wp_query_args['meta_query'][] = array(
					'key'     => '_anonymized',
					'compare' => 'NOT EXISTS',
				);
			}
		}

		if ( ! isset( $query_vars['paginate'] ) || ! $query_vars['paginate'] ) {
			$wp_query_args['no_found_rows'] = true;
		}

		return apply_filters( 'woocommerce_order_data_store_cpt_get_orders_query', $wp_query_args, $query_vars, $this );
	}

	/**
	 * Query for Orders matching specific criteria.
	 *
	 * @since 3.1.0
	 *
	 * @param array $query_vars query vars from a WC_Order_Query.
	 *
	 * @return array|object
	 */
	public function query( $query_vars ) {
		$args = $this->get_wp_query_args( $query_vars );

		if ( ! empty( $args['errors'] ) ) {
			$query = (object) array(
				'posts'         => array(),
				'found_posts'   => 0,
				'max_num_pages' => 0,
			);
		} else {
			$query = new WP_Query( $args );
		}

		if ( isset( $query_vars['return'] ) && 'ids' === $query_vars['return'] ) {
			$orders = $query->posts;
		} else {
			update_post_caches( $query->posts ); // We already fetching posts, might as well hydrate some caches.
			$order_ids = wp_list_pluck( $query->posts, 'ID' );
			$orders    = $this->compile_orders( $order_ids, $query_vars, $query );
		}

		if ( isset( $query_vars['paginate'] ) && $query_vars['paginate'] ) {
			return (object) array(
				'orders'        => $orders,
				'total'         => $query->found_posts,
				'max_num_pages' => $query->max_num_pages,
			);
		}

		return $orders;
	}

	/**
	 * Compile order response and set caches as needed for order ids.
	 *
	 * @param array    $order_ids  List of order IDS to compile.
	 * @param array    $query_vars Original query arguments.
	 * @param WP_Query $query      Query object.
	 *
	 * @return array Orders.
	 */
	private function compile_orders( $order_ids, $query_vars, $query ) {
		if ( empty( $order_ids ) ) {
			return array();
		}
		$orders = array();

		$this->prime_caches_for_orders( $order_ids, $query_vars );

		foreach ( $query->posts as $post ) {
			$order = wc_get_order( $post );

			// If the order returns false, don't add it to the list.
			if ( false === $order ) {
				continue;
			}

			$orders[] = $order;
		}

		return $orders;
	}

	/**
	 * Helper method to prime caches for orders. Call this if you are going to be fetching orders in a loop.
	 *
	 * @param array $order_ids List of order IDS to prime caches for.
	 * @param array $query_vars Original query arguments.
	 */
	public function prime_caches_for_orders( $order_ids, $query_vars ) {
		// Lets do some cache hydrations so that we don't have to fetch data from DB for every order.
		$this->prime_raw_meta_cache_for_orders( $order_ids, $query_vars );
		$this->prime_refund_caches_for_order( $order_ids, $query_vars );
		$this->prime_order_item_caches_for_orders( $order_ids, $query_vars );
	}

	/**
	 * Prime refund cache for orders.
	 *
	 * @param array $order_ids  Order Ids to prime cache for.
	 * @param array $query_vars Query vars for the query.
	 */
	private function prime_refund_caches_for_order( $order_ids, $query_vars ) {
		if ( ! isset( $query_vars['type'] ) || ! ( 'shop_order' === $query_vars['type'] ) ) {
			return;
		}
		if ( isset( $query_vars['fields'] ) && 'all' !== $query_vars['fields'] ) {
			if ( is_array( $query_vars['fields'] ) && ! in_array( 'refunds', $query_vars['fields'], true ) ) {
				return;
			}
		}
		$cache_keys_mapping = array();
		foreach ( $order_ids as $order_id ) {
			$cache_keys_mapping[ $order_id ] = WC_Cache_Helper::get_cache_prefix( 'orders' ) . 'refunds' . $order_id;
		}
		$non_cached_ids = array();
		$cache_values   = wc_cache_get_multiple( array_values( $cache_keys_mapping ), 'orders' );
		foreach ( $order_ids as $order_id ) {
			if ( false === $cache_values[ $cache_keys_mapping[ $order_id ] ] ) {
				$non_cached_ids[] = $order_id;
			}
		}
		if ( empty( $non_cached_ids ) ) {
			return;
		}

		$refunds       = wc_get_orders(
			array(
				'type'            => 'shop_order_refund',
				'post_parent__in' => $non_cached_ids,
				'limit'           => - 1,
			)
		);
		$order_refunds = array_reduce(
			$refunds,
			function ( $order_refunds_array, WC_Order_Refund $refund ) {
				if ( ! isset( $order_refunds_array[ $refund->get_parent_id() ] ) ) {
					$order_refunds_array[ $refund->get_parent_id() ] = array();
				}
				$order_refunds_array[ $refund->get_parent_id() ][] = $refund;
				return $order_refunds_array;
			},
			array()
		);
		foreach ( $non_cached_ids as $order_id ) {
			$refunds = array();
			if ( isset( $order_refunds[ $order_id ] ) ) {
				$refunds = $order_refunds[ $order_id ];
			}
			wp_cache_set( $cache_keys_mapping[ $order_id ], $refunds, 'orders' );
		}
	}

	/**
	 * Prime cache for raw meta data for orders in bulk. Difference between this and WP built-in metadata is that this method also fetches `meta_id` field which we use and cache it.
	 *
	 * @param array $order_ids  Order Ids to prime cache for.
	 * @param array $query_vars Query vars for the query.
	 */
	private function prime_raw_meta_cache_for_orders( $order_ids, $query_vars ) {
		global $wpdb;

		if ( isset( $query_vars['fields'] ) && 'all' !== $query_vars['fields'] ) {
			if ( is_array( $query_vars['fields'] ) && ! in_array( 'meta_data', $query_vars['fields'], true ) ) {
				return;
			}
		}

		$cache_keys_mapping = array();
		foreach ( $order_ids as $order_id ) {
			$cache_keys_mapping[ $order_id ] = WC_Order::generate_meta_cache_key( $order_id, 'orders' );
		}
		$cache_values   = wc_cache_get_multiple( array_values( $cache_keys_mapping ), 'orders' );
		$non_cached_ids = array();
		foreach ( $order_ids as $order_id ) {
			if ( false === $cache_values[ $cache_keys_mapping[ $order_id ] ] ) {
				$non_cached_ids[] = $order_id;
			}
		}
		if ( empty( $non_cached_ids ) ) {
			return;
		}
		$order_ids           = esc_sql( $non_cached_ids );
		$order_ids_in        = "'" . implode( "', '", $order_ids ) . "'";
		$raw_meta_data_array = $wpdb->get_results(
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT post_id as object_id, meta_id, meta_key, meta_value
				FROM {$wpdb->postmeta}
				WHERE post_id IN ( $order_ids_in )
				ORDER BY post_id"
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		);
		$raw_meta_data_collection = array_reduce(
			$raw_meta_data_array,
			function ( $collection, $raw_meta_data ) {
				if ( ! isset( $collection[ $raw_meta_data->object_id ] ) ) {
					$collection[ $raw_meta_data->object_id ] = array();
				}
				$collection[ $raw_meta_data->object_id ][] = $raw_meta_data;
				return $collection;
			},
			array()
		);
		WC_Order::prime_raw_meta_data_cache( $raw_meta_data_collection, 'orders' );
	}
}

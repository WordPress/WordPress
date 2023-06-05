<?php
/**
 * Abstract_WC_Order_Data_Store_CPT class file.
 *
 * @package WooCommerce\Classes
 */

use Automattic\Jetpack\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Order Data Store: Stored in CPT.
 *
 * @version  3.0.0
 */
abstract class Abstract_WC_Order_Data_Store_CPT extends WC_Data_Store_WP implements WC_Abstract_Order_Data_Store_Interface, WC_Object_Data_Store_Interface {

	/**
	 * Internal meta type used to store order data.
	 *
	 * @var string
	 */
	protected $meta_type = 'post';

	/**
	 * Data stored in meta keys, but not considered "meta" for an order.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $internal_meta_keys = array(
		'_order_currency',
		'_cart_discount',
		'_cart_discount_tax',
		'_order_shipping',
		'_order_shipping_tax',
		'_order_tax',
		'_order_total',
		'_order_version',
		'_prices_include_tax',
		'_payment_tokens',
	);

	/**
	 * Custom setters for props. Add key here if it has corresponding set_ and get_ method present.
	 *
	 * @var string[]
	 */
	protected $internal_data_store_key_getters = array();

	/**
	 * Return internal key getters name.
	 *
	 * @return string[]
	 */
	public function get_internal_data_store_key_getters() {
		return $this->internal_data_store_key_getters;
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Method to create a new order in the database.
	 *
	 * @param WC_Order $order Order object.
	 */
	public function create( &$order ) {
		$order->set_version( Constants::get_constant( 'WC_VERSION' ) );
		$order->set_currency( $order->get_currency() ? $order->get_currency() : get_woocommerce_currency() );
		if ( ! $order->get_date_created( 'edit' ) ) {
			$order->set_date_created( time() );
		}

		$id = wp_insert_post(
			apply_filters(
				'woocommerce_new_order_data',
				array(
					'post_date'     => gmdate( 'Y-m-d H:i:s', $order->get_date_created( 'edit' )->getOffsetTimestamp() ),
					'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $order->get_date_created( 'edit' )->getTimestamp() ),
					'post_type'     => $order->get_type( 'edit' ),
					'post_status'   => $this->get_post_status( $order ),
					'ping_status'   => 'closed',
					'post_author'   => 1,
					'post_title'    => $this->get_post_title(),
					'post_password' => $this->get_order_key( $order ),
					'post_parent'   => $order->get_parent_id( 'edit' ),
					'post_excerpt'  => $this->get_post_excerpt( $order ),
				)
			),
			true
		);

		if ( $id && ! is_wp_error( $id ) ) {
			$order->set_id( $id );
			$this->update_post_meta( $order );
			$order->save_meta_data();
			$order->apply_changes();
			$this->clear_caches( $order );
		}
	}

	/**
	 * Method to read an order from the database.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @throws Exception If passed order is invalid.
	 */
	public function read( &$order ) {
		$order->set_defaults();
		$post_object = get_post( $order->get_id() );
		if ( ! $order->get_id() || ! $post_object || ! in_array( $post_object->post_type, wc_get_order_types(), true ) ) {
			throw new Exception( __( 'Invalid order.', 'woocommerce' ) );
		}

		$order->set_props(
			array(
				'parent_id'     => $post_object->post_parent,
				'date_created'  => $this->string_to_timestamp( $post_object->post_date_gmt ),
				'date_modified' => $this->string_to_timestamp( $post_object->post_modified_gmt ),
				'status'        => $post_object->post_status,
			)
		);

		$this->read_order_data( $order, $post_object );
		$order->read_meta_data();
		$order->set_object_read( true );

		/**
		 * In older versions, discounts may have been stored differently.
		 * Update them now so if the object is saved, the correct values are
		 * stored.
		 */
		if ( version_compare( $order->get_version( 'edit' ), '2.3.7', '<' ) && $order->get_prices_include_tax( 'edit' ) ) {
			$order->set_discount_total( (float) get_post_meta( $order->get_id(), '_cart_discount', true ) - (float) get_post_meta( $order->get_id(), '_cart_discount_tax', true ) );
		}
	}

	/**
	 * Method to update an order in the database.
	 *
	 * @param WC_Order $order Order object.
	 */
	public function update( &$order ) {
		$order->save_meta_data();
		$order->set_version( Constants::get_constant( 'WC_VERSION' ) );

		if ( null === $order->get_date_created( 'edit' ) ) {
			$order->set_date_created( time() );
		}

		$changes = $order->get_changes();

		// Only update the post when the post data changes.
		if ( array_intersect( array( 'date_created', 'date_modified', 'status', 'parent_id', 'post_excerpt' ), array_keys( $changes ) ) ) {
			$post_data = array(
				'post_date'         => gmdate( 'Y-m-d H:i:s', $order->get_date_created( 'edit' )->getOffsetTimestamp() ),
				'post_date_gmt'     => gmdate( 'Y-m-d H:i:s', $order->get_date_created( 'edit' )->getTimestamp() ),
				'post_status'       => $this->get_post_status( $order ),
				'post_parent'       => $order->get_parent_id(),
				'post_excerpt'      => $this->get_post_excerpt( $order ),
				'post_modified'     => isset( $changes['date_modified'] ) ? gmdate( 'Y-m-d H:i:s', $order->get_date_modified( 'edit' )->getOffsetTimestamp() ) : current_time( 'mysql' ),
				'post_modified_gmt' => isset( $changes['date_modified'] ) ? gmdate( 'Y-m-d H:i:s', $order->get_date_modified( 'edit' )->getTimestamp() ) : current_time( 'mysql', 1 ),
			);

			/**
			 * When updating this object, to prevent infinite loops, use $wpdb
			 * to update data, since wp_update_post spawns more calls to the
			 * save_post action.
			 *
			 * This ensures hooks are fired by either WP itself (admin screen save),
			 * or an update purely from CRUD.
			 */
			if ( doing_action( 'save_post' ) ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $order->get_id() ) );
				clean_post_cache( $order->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $order->get_id() ), $post_data ) );
			}
			$order->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.
		}
		$this->update_post_meta( $order );
		$order->apply_changes();
		$this->clear_caches( $order );
	}

	/**
	 * Method to delete an order from the database.
	 *
	 * @param WC_Order $order Order object.
	 * @param array    $args Array of args to pass to the delete method.
	 *
	 * @return void
	 */
	public function delete( &$order, $args = array() ) {
		$id   = $order->get_id();
		$args = wp_parse_args(
			$args,
			array(
				'force_delete' => false,
			)
		);

		if ( ! $id ) {
			return;
		}

		if ( $args['force_delete'] ) {
			wp_delete_post( $id );
			$order->set_id( 0 );
			do_action( 'woocommerce_delete_order', $id );
		} else {
			wp_trash_post( $id );
			$order->set_status( 'trash' );
			do_action( 'woocommerce_trash_order', $id );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Additional Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the status to save to the post object.
	 *
	 * Plugins extending the order classes can override this to change the stored status/add prefixes etc.
	 *
	 * @since 3.6.0
	 * @param  WC_order $order Order object.
	 * @return string
	 */
	protected function get_post_status( $order ) {
		$order_status = $order->get_status( 'edit' );

		if ( ! $order_status ) {
			$order_status = apply_filters( 'woocommerce_default_order_status', 'pending' );
		}

		$post_status    = $order_status;
		$valid_statuses = get_post_stati();

		// Add a wc- prefix to the status, but exclude some core statuses which should not be prefixed.
		// @todo In the future this should only happen based on `wc_is_order_status`, but in order to
		// preserve back-compatibility this happens to all statuses except a select few. A doing_it_wrong
		// Notice will be needed here, followed by future removal.
		if ( ! in_array( $post_status, array( 'auto-draft', 'draft', 'trash' ), true ) && in_array( 'wc-' . $post_status, $valid_statuses, true ) ) {
			$post_status = 'wc-' . $post_status;
		}

		return $post_status;
	}

	/**
	 * Excerpt for post.
	 *
	 * @param  WC_order $order Order object.
	 * @return string
	 */
	protected function get_post_excerpt( $order ) {
		return '';
	}

	/**
	 * Get a title for the new post type.
	 *
	 * @return string
	 */
	protected function get_post_title() {
		// @codingStandardsIgnoreStart
		/* translators: %s: Order date */
		return sprintf( __( 'Order &ndash; %s', 'woocommerce' ), (new DateTime('now'))->format( _x( 'M d, Y @ h:i A', 'Order date parsed by DateTime::format', 'woocommerce' ) ) );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Get order key.
	 *
	 * @since 4.3.0
	 * @param WC_order $order Order object.
	 * @return string
	 */
	protected function get_order_key( $order ) {
		return wc_generate_order_key();
	}

	/**
	 * Read order data. Can be overridden by child classes to load other props.
	 *
	 * @param WC_Order $order Order object.
	 * @param object   $post_object Post object.
	 * @since 3.0.0
	 */
	protected function read_order_data( &$order, $post_object ) {
		$id = $order->get_id();

		$order->set_props(
			array(
				'currency'           => get_post_meta( $id, '_order_currency', true ),
				'discount_total'     => get_post_meta( $id, '_cart_discount', true ),
				'discount_tax'       => get_post_meta( $id, '_cart_discount_tax', true ),
				'shipping_total'     => get_post_meta( $id, '_order_shipping', true ),
				'shipping_tax'       => get_post_meta( $id, '_order_shipping_tax', true ),
				'cart_tax'           => get_post_meta( $id, '_order_tax', true ),
				'total'              => get_post_meta( $id, '_order_total', true ),
				'version'            => get_post_meta( $id, '_order_version', true ),
				'prices_include_tax' => metadata_exists( 'post', $id, '_prices_include_tax' ) ? 'yes' === get_post_meta( $id, '_prices_include_tax', true ) : 'yes' === get_option( 'woocommerce_prices_include_tax' ),
			)
		);

		// Gets extra data associated with the order if needed.
		foreach ( $order->get_extra_data_keys() as $key ) {
			$function = 'set_' . $key;
			if ( is_callable( array( $order, $function ) ) ) {
				$order->{$function}( get_post_meta( $order->get_id(), '_' . $key, true ) );
			}
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
		$meta_key_to_props = array(
			'_order_currency'     => 'currency',
			'_cart_discount'      => 'discount_total',
			'_cart_discount_tax'  => 'discount_tax',
			'_order_shipping'     => 'shipping_total',
			'_order_shipping_tax' => 'shipping_tax',
			'_order_tax'          => 'cart_tax',
			'_order_total'        => 'total',
			'_order_version'      => 'version',
			'_prices_include_tax' => 'prices_include_tax',
		);

		$props_to_update = $this->get_props_to_update( $order, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $order->{"get_$prop"}( 'edit' );
			$value = is_string( $value ) ? wp_slash( $value ) : $value;

			if ( 'prices_include_tax' === $prop ) {
				$value = $value ? 'yes' : 'no';
			}

			$updated = $this->update_or_delete_post_meta( $order, $meta_key, $value );

			if ( $updated ) {
				$updated_props[] = $prop;
			}
		}

		do_action( 'woocommerce_order_object_updated_props', $order, $updated_props );
	}

	/**
	 * Clear any caches.
	 *
	 * @param WC_Order $order Order object.
	 * @since 3.0.0
	 */
	protected function clear_caches( &$order ) {
		clean_post_cache( $order->get_id() );
		wc_delete_shop_order_transients( $order );
		wp_cache_delete( 'order-items-' . $order->get_id(), 'orders' );
	}

	/**
	 * Read order items of a specific type from the database for this order.
	 *
	 * @param  WC_Order $order Order object.
	 * @param  string   $type Order item type.
	 * @return array
	 */
	public function read_items( $order, $type ) {
		global $wpdb;

		// Get from cache if available.
		$items = 0 < $order->get_id() ? wp_cache_get( 'order-items-' . $order->get_id(), 'orders' ) : false;

		if ( false === $items ) {
			$items = $wpdb->get_results(
				$wpdb->prepare( "SELECT order_item_type, order_item_id, order_id, order_item_name FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d ORDER BY order_item_id;", $order->get_id() )
			);
			foreach ( $items as $item ) {
				wp_cache_set( 'item-' . $item->order_item_id, $item, 'order-items' );
			}
			if ( 0 < $order->get_id() ) {
				wp_cache_set( 'order-items-' . $order->get_id(), $items, 'orders' );
			}
		}

		$items = wp_list_filter( $items, array( 'order_item_type' => $type ) );

		if ( ! empty( $items ) ) {
			$items = array_map( array( 'WC_Order_Factory', 'get_order_item' ), array_combine( wp_list_pluck( $items, 'order_item_id' ), $items ) );
		} else {
			$items = array();
		}

		return $items;
	}

	/**
	 * Return the order type of a given item which belongs to WC_Order.
	 *
	 * @since  3.2.0
	 * @param  WC_Order $order Order Object.
	 * @param  int      $order_item_id Order item id.
	 * @return string Order Item type
	 */
	public function get_order_item_type( $order, $order_item_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT order_item_type FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d and order_item_id = %d;", $order->get_id(), $order_item_id ) );
	}

	/**
	 * Prime following caches:
	 *  1. item-$order_item_id   For individual items.
	 *  2. order-items-$order-id For fetching items associated with an order.
	 *  3. order-item meta.
	 *
	 * @param array $order_ids  Order Ids to prime cache for.
	 * @param array $query_vars Query vars for the query.
	 */
	protected function prime_order_item_caches_for_orders( $order_ids, $query_vars ) {
		global $wpdb;
		if ( isset( $query_vars['fields'] ) && 'all' !== $query_vars['fields'] ) {
			$line_items = array(
				'line_items',
				'shipping_lines',
				'fee_lines',
				'coupon_lines',
			);

			if ( is_array( $query_vars['fields'] ) && 0 === count( array_intersect( $line_items, $query_vars['fields'] ) ) ) {
				return;
			}
		}
		$cache_keys     = array_map(
			function ( $order_id ) {
				return 'order-items-' . $order_id;
			},
			$order_ids
		);
		$cache_values   = wc_cache_get_multiple( $cache_keys, 'orders' );
		$non_cached_ids = array();
		foreach ( $order_ids as $order_id ) {
			if ( false === $cache_values[ 'order-items-' . $order_id ] ) {
				$non_cached_ids[] = $order_id;
			}
		}
		if ( empty( $non_cached_ids ) ) {
			return;
		}

		$non_cached_ids        = esc_sql( $non_cached_ids );
		$non_cached_ids_string = implode( ',', $non_cached_ids );
		$order_items           = $wpdb->get_results(
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			"SELECT order_item_type, order_item_id, order_id, order_item_name FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id in ( $non_cached_ids_string ) ORDER BY order_item_id;"
		);
		if ( empty( $order_items ) ) {
			return;
		}

		$order_items_for_all_orders = array_reduce(
			$order_items,
			function ( $order_items_collection, $order_item ) {
				if ( ! isset( $order_items_collection[ $order_item->order_id ] ) ) {
					$order_items_collection[ $order_item->order_id ] = array();
				}
				$order_items_collection[ $order_item->order_id ][] = $order_item;
				return $order_items_collection;
			}
		);
		foreach ( $order_items_for_all_orders as $order_id => $items ) {
			wp_cache_set( 'order-items-' . $order_id, $items, 'orders' );
		}
		foreach ( $order_items as $item ) {
			wp_cache_set( 'item-' . $item->order_item_id, $item, 'order-items' );
		}
		$order_item_ids = wp_list_pluck( $order_items, 'order_item_id' );
		update_meta_cache( 'order_item', $order_item_ids );
	}

	/**
	 * Remove all line items (products, coupons, shipping, taxes) from the order.
	 *
	 * @param WC_Order $order Order object.
	 * @param string   $type Order item type. Default null.
	 */
	public function delete_items( $order, $type = null ) {
		global $wpdb;
		if ( ! empty( $type ) ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM itemmeta USING {$wpdb->prefix}woocommerce_order_itemmeta itemmeta INNER JOIN {$wpdb->prefix}woocommerce_order_items items WHERE itemmeta.order_item_id = items.order_item_id AND items.order_id = %d AND items.order_item_type = %s", $order->get_id(), $type ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d AND order_item_type = %s", $order->get_id(), $type ) );
		} else {
			$wpdb->query( $wpdb->prepare( "DELETE FROM itemmeta USING {$wpdb->prefix}woocommerce_order_itemmeta itemmeta INNER JOIN {$wpdb->prefix}woocommerce_order_items items WHERE itemmeta.order_item_id = items.order_item_id and items.order_id = %d", $order->get_id() ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d", $order->get_id() ) );
		}
		$this->clear_caches( $order );
	}

	/**
	 * Get token ids for an order.
	 *
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	public function get_payment_token_ids( $order ) {
		$token_ids = array_filter( (array) get_post_meta( $order->get_id(), '_payment_tokens', true ) );
		return $token_ids;
	}

	/**
	 * Update token ids for an order.
	 *
	 * @param WC_Order $order Order object.
	 * @param array    $token_ids Payment token ids.
	 */
	public function update_payment_token_ids( $order, $token_ids ) {
		update_post_meta( $order->get_id(), '_payment_tokens', $token_ids );
	}

	/**
	 * Get the order's title.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return string Order title.
	 */
	public function get_title( WC_Order $order ) {
		return get_the_title( $order->get_id() );
	}

	/**
	 * Given an initialized order object, update the post/postmeta records.
	 *
	 * @param WC_Abstract_Order $order Order object.
	 *
	 * @return bool Whether the order was updated.
	 */
	public function update_order_from_object( $order ) {
		if ( ! $order->get_id() ) {
			return false;
		}
		$this->update_order_meta_from_object( $order );

		// Add hook to update post_modified date so that it's the same as order. Without this hook, WP will set the modified date to current date, and we will think that posts and orders are out of sync again.
		add_filter( 'wp_insert_post_data', array( $this, 'update_post_modified_data' ), 10, 2 );
		$post_data = array(
			'ID'                 => $order->get_id(),
			'post_date'          => gmdate( 'Y-m-d H:i:s', $order->get_date_created( 'edit' )->getOffsetTimestamp() ),
			'post_date_gmt'      => gmdate( 'Y-m-d H:i:s', $order->get_date_created( 'edit' )->getTimestamp() ),
			'post_status'        => $this->get_post_status( $order ),
			'post_parent'        => $order->get_parent_id(),
			'edit_date'          => true,
			'post_excerpt'       => method_exists( $order, 'get_customer_note' ) ? $order->get_customer_note() : '',
			'post_type'          => $order->get_type(),
			'order_modified'     => ! is_null( $order->get_date_modified() ) ? gmdate( 'Y-m-d H:i:s', $order->get_date_modified( 'edit' )->getOffsetTimestamp() ) : '',
			'order_modified_gmt' => ! is_null( $order->get_date_modified() ) ? gmdate( 'Y-m-d H:i:s', $order->get_date_modified( 'edit' )->getTimestamp() ) : '',
		);
		$updated   = wp_update_post( $post_data );
		remove_filter( 'wp_insert_post_data', array( $this, 'update_post_modified_data' ) );
		return $updated;
	}

	/**
	 * Change the modified date of the post to match the order's modified date if passed.
	 *
	 * @hooked wp_insert_post_data See function update_order_from_object.
	 *
	 * @param array $data An array of slashed, sanitized, and processed post data.
	 * @param array $postarr An array of sanitized (and slashed) but otherwise unmodified post data.
	 *
	 * @return array Data with updated modified date.
	 */
	public function update_post_modified_data( $data, $postarr ) {
		if ( ! isset( $postarr['order_modified'] ) || ! isset( $postarr['order_modified_gmt'] ) ) {
			return $data;
		}

		$data['post_modified']     = $postarr['order_modified'];
		$data['post_modified_gmt'] = $postarr['order_modified_gmt'];
		return $data;
	}

	/**
	 * Helper method to update order metadata from intialized order object.
	 *
	 * @param WC_Abstract_Order $order Order object.
	 */
	private function update_order_meta_from_object( $order ) {
		if ( is_null( $order->get_meta() ) ) {
			return;
		}

		$existing_meta_data = get_post_meta( $order->get_id() );

		foreach ( $order->get_meta_data() as $meta_data ) {
			if ( isset( $existing_meta_data[ $meta_data->key ] ) ) {
				if ( $existing_meta_data[ $meta_data->key ] === $meta_data->value ) {
					unset( $existing_meta_data[ $meta_data->key ] );
					continue;
				}

				unset( $existing_meta_data[ $meta_data->key ] );
				delete_post_meta( $order->get_id(), $meta_data->key );
			}
			add_post_meta( $order->get_id(), $meta_data->key, $meta_data->value, false );
		}

		// Find remaining meta that was deleted from the order but still present in the associated post.
		// Post meta corresponding to order props is excluded (as it shouldn't be deleted).
		$keys_to_delete = array_diff(
			array_keys( $existing_meta_data ),
			$this->internal_meta_keys,
			array_keys( $this->get_internal_data_store_key_getters() )
		);

		foreach ( $keys_to_delete as $meta_key ) {
			delete_post_meta( $order->get_id(), $meta_key );
		}

		$this->update_post_meta( $order );
	}
}

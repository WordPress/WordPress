<?php
/**
 * Class WC_Coupon_Data_Store_CPT file.
 *
 * @package WooCommerce\DataStores
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Coupon Data Store: Custom Post Type.
 *
 * @version  3.0.0
 */
class WC_Coupon_Data_Store_CPT extends WC_Data_Store_WP implements WC_Coupon_Data_Store_Interface, WC_Object_Data_Store_Interface {

	/**
	 * Internal meta type used to store coupon data.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	protected $meta_type = 'post';

	/**
	 * Data stored in meta keys, but not considered "meta" for a coupon.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $internal_meta_keys = array(
		'discount_type',
		'coupon_amount',
		'expiry_date',
		'date_expires',
		'usage_count',
		'individual_use',
		'product_ids',
		'exclude_product_ids',
		'usage_limit',
		'usage_limit_per_user',
		'limit_usage_to_x_items',
		'free_shipping',
		'product_categories',
		'exclude_product_categories',
		'exclude_sale_items',
		'minimum_amount',
		'maximum_amount',
		'customer_email',
		'_used_by',
		'_edit_lock',
		'_edit_last',
	);

	/**
	 * The updated coupon properties
	 *
	 * @since 4.1.0
	 * @var array
	 */
	protected $updated_props = array();

	/**
	 * Method to create a new coupon in the database.
	 *
	 * @since 3.0.0
	 * @param WC_Coupon $coupon Coupon object.
	 */
	public function create( &$coupon ) {
		if ( ! $coupon->get_date_created( 'edit' ) ) {
			$coupon->set_date_created( time() );
		}

		$coupon_id = wp_insert_post(
			apply_filters(
				'woocommerce_new_coupon_data',
				array(
					'post_type'     => 'shop_coupon',
					'post_status'   => 'publish',
					'post_author'   => get_current_user_id(),
					'post_title'    => $coupon->get_code( 'edit' ),
					'post_content'  => '',
					'post_excerpt'  => $coupon->get_description( 'edit' ),
					'post_date'     => gmdate( 'Y-m-d H:i:s', $coupon->get_date_created()->getOffsetTimestamp() ),
					'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $coupon->get_date_created()->getTimestamp() ),
				)
			),
			true
		);

		if ( $coupon_id ) {
			$coupon->set_id( $coupon_id );
			$this->update_post_meta( $coupon );
			$coupon->save_meta_data();
			$coupon->apply_changes();
			delete_transient( 'rest_api_coupons_type_count' );
			do_action( 'woocommerce_new_coupon', $coupon_id, $coupon );
		}
	}

	/**
	 * Method to read a coupon.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 *
	 * @throws Exception If invalid coupon.
	 */
	public function read( &$coupon ) {
		$coupon->set_defaults();

		$post_object = get_post( $coupon->get_id() );

		if ( ! $coupon->get_id() || ! $post_object || 'shop_coupon' !== $post_object->post_type ) {
			throw new Exception( __( 'Invalid coupon.', 'woocommerce' ) );
		}

		$coupon_id = $coupon->get_id();
		$coupon->set_props(
			array(
				'code'                        => $post_object->post_title,
				'description'                 => $post_object->post_excerpt,
				'status'                      => $post_object->post_status,
				'date_created'                => $this->string_to_timestamp( $post_object->post_date_gmt ),
				'date_modified'               => $this->string_to_timestamp( $post_object->post_modified_gmt ),
				'date_expires'                => metadata_exists( 'post', $coupon_id, 'date_expires' ) ? get_post_meta( $coupon_id, 'date_expires', true ) : get_post_meta( $coupon_id, 'expiry_date', true ), // @todo: Migrate expiry_date meta to date_expires in upgrade routine.
				'discount_type'               => get_post_meta( $coupon_id, 'discount_type', true ),
				'amount'                      => get_post_meta( $coupon_id, 'coupon_amount', true ),
				'usage_count'                 => get_post_meta( $coupon_id, 'usage_count', true ),
				'individual_use'              => 'yes' === get_post_meta( $coupon_id, 'individual_use', true ),
				'product_ids'                 => array_filter( (array) explode( ',', get_post_meta( $coupon_id, 'product_ids', true ) ) ),
				'excluded_product_ids'        => array_filter( (array) explode( ',', get_post_meta( $coupon_id, 'exclude_product_ids', true ) ) ),
				'usage_limit'                 => get_post_meta( $coupon_id, 'usage_limit', true ),
				'usage_limit_per_user'        => get_post_meta( $coupon_id, 'usage_limit_per_user', true ),
				'limit_usage_to_x_items'      => 0 < get_post_meta( $coupon_id, 'limit_usage_to_x_items', true ) ? get_post_meta( $coupon_id, 'limit_usage_to_x_items', true ) : null,
				'free_shipping'               => 'yes' === get_post_meta( $coupon_id, 'free_shipping', true ),
				'product_categories'          => array_filter( (array) get_post_meta( $coupon_id, 'product_categories', true ) ),
				'excluded_product_categories' => array_filter( (array) get_post_meta( $coupon_id, 'exclude_product_categories', true ) ),
				'exclude_sale_items'          => 'yes' === get_post_meta( $coupon_id, 'exclude_sale_items', true ),
				'minimum_amount'              => get_post_meta( $coupon_id, 'minimum_amount', true ),
				'maximum_amount'              => get_post_meta( $coupon_id, 'maximum_amount', true ),
				'email_restrictions'          => array_filter( (array) get_post_meta( $coupon_id, 'customer_email', true ) ),
				'used_by'                     => array_filter( (array) get_post_meta( $coupon_id, '_used_by' ) ),
			)
		);
		$coupon->read_meta_data();
		$coupon->set_object_read( true );
		do_action( 'woocommerce_coupon_loaded', $coupon );
	}

	/**
	 * Updates a coupon in the database.
	 *
	 * @since 3.0.0
	 * @param WC_Coupon $coupon Coupon object.
	 */
	public function update( &$coupon ) {
		$coupon->save_meta_data();
		$changes = $coupon->get_changes();

		if ( array_intersect( array( 'code', 'description', 'date_created', 'date_modified' ), array_keys( $changes ) ) ) {
			$post_data = array(
				'post_title'        => $coupon->get_code( 'edit' ),
				'post_excerpt'      => $coupon->get_description( 'edit' ),
				'post_date'         => gmdate( 'Y-m-d H:i:s', $coupon->get_date_created( 'edit' )->getOffsetTimestamp() ),
				'post_date_gmt'     => gmdate( 'Y-m-d H:i:s', $coupon->get_date_created( 'edit' )->getTimestamp() ),
				'post_modified'     => isset( $changes['date_modified'] ) ? gmdate( 'Y-m-d H:i:s', $coupon->get_date_modified( 'edit' )->getOffsetTimestamp() ) : current_time( 'mysql' ),
				'post_modified_gmt' => isset( $changes['date_modified'] ) ? gmdate( 'Y-m-d H:i:s', $coupon->get_date_modified( 'edit' )->getTimestamp() ) : current_time( 'mysql', 1 ),
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
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $coupon->get_id() ) );
				clean_post_cache( $coupon->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $coupon->get_id() ), $post_data ) );
			}
			$coupon->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.
		}
		$this->update_post_meta( $coupon );
		$coupon->apply_changes();
		delete_transient( 'rest_api_coupons_type_count' );
		do_action( 'woocommerce_update_coupon', $coupon->get_id(), $coupon );
	}

	/**
	 * Deletes a coupon from the database.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 * @param array     $args Array of args to pass to the delete method.
	 */
	public function delete( &$coupon, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'force_delete' => false,
			)
		);

		$id = $coupon->get_id();

		if ( ! $id ) {
			return;
		}

		if ( $args['force_delete'] ) {
			wp_delete_post( $id );

			wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'coupons' ) . 'coupon_id_from_code_' . $coupon->get_code(), 'coupons' );

			$coupon->set_id( 0 );
			do_action( 'woocommerce_delete_coupon', $id );
		} else {
			wp_trash_post( $id );
			do_action( 'woocommerce_trash_coupon', $id );
		}
	}

	/**
	 * Helper method that updates all the post meta for a coupon based on it's settings in the WC_Coupon class.
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 * @since 3.0.0
	 */
	private function update_post_meta( &$coupon ) {
		$meta_key_to_props = array(
			'discount_type'              => 'discount_type',
			'coupon_amount'              => 'amount',
			'individual_use'             => 'individual_use',
			'product_ids'                => 'product_ids',
			'exclude_product_ids'        => 'excluded_product_ids',
			'usage_limit'                => 'usage_limit',
			'usage_limit_per_user'       => 'usage_limit_per_user',
			'limit_usage_to_x_items'     => 'limit_usage_to_x_items',
			'usage_count'                => 'usage_count',
			'date_expires'               => 'date_expires',
			'free_shipping'              => 'free_shipping',
			'product_categories'         => 'product_categories',
			'exclude_product_categories' => 'excluded_product_categories',
			'exclude_sale_items'         => 'exclude_sale_items',
			'minimum_amount'             => 'minimum_amount',
			'maximum_amount'             => 'maximum_amount',
			'customer_email'             => 'email_restrictions',
		);

		$props_to_update = $this->get_props_to_update( $coupon, $meta_key_to_props );
		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $coupon->{"get_$prop"}( 'edit' );
			$value = is_string( $value ) ? wp_slash( $value ) : $value;
			switch ( $prop ) {
				case 'individual_use':
				case 'free_shipping':
				case 'exclude_sale_items':
					$value = wc_bool_to_string( $value );
					break;
				case 'product_ids':
				case 'excluded_product_ids':
					$value = implode( ',', array_filter( array_map( 'intval', $value ) ) );
					break;
				case 'product_categories':
				case 'excluded_product_categories':
					$value = array_filter( array_map( 'intval', $value ) );
					break;
				case 'email_restrictions':
					$value = array_filter( array_map( 'sanitize_email', $value ) );
					break;
				case 'date_expires':
					$value = $value ? $value->getTimestamp() : null;
					break;
			}

			$updated = $this->update_or_delete_post_meta( $coupon, $meta_key, $value );

			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}

		do_action( 'woocommerce_coupon_object_updated_props', $coupon, $this->updated_props );
	}

	/**
	 * Increase usage count for current coupon.
	 *
	 * @since 3.0.0
	 * @param WC_Coupon $coupon           Coupon object.
	 * @param string    $used_by          Either user ID or billing email.
	 * @param WC_Order  $order (Optional) If passed, clears the hold record associated with order.

	 * @return int New usage count.
	 */
	public function increase_usage_count( &$coupon, $used_by = '', $order = null ) {
		$coupon_held_key_for_user = '';
		if ( $order instanceof WC_Order ) {
			$coupon_held_key_for_user = $order->get_data_store()->get_coupon_held_keys_for_users( $order, $coupon->get_id() );
		}

		$new_count = $this->update_usage_count_meta( $coupon, 'increase' );

		if ( $used_by ) {
			$this->add_coupon_used_by( $coupon, $used_by, $coupon_held_key_for_user );
			$coupon->set_used_by( (array) get_post_meta( $coupon->get_id(), '_used_by' ) );
		}

		do_action( 'woocommerce_increase_coupon_usage_count', $coupon, $new_count, $used_by );

		return $new_count;
	}

	/**
	 * Helper function to add a `_used_by` record to track coupons used by the user.
	 *
	 * @param WC_Coupon $coupon           Coupon object.
	 * @param string    $used_by          Either user ID or billing email.
	 * @param string    $coupon_held_key (Optional) Update meta key to `_used_by` instead of adding a new record.
	 */
	private function add_coupon_used_by( $coupon, $used_by, $coupon_held_key ) {
		global $wpdb;
		if ( $coupon_held_key && '' !== $coupon_held_key ) {
			// Looks like we added a tentative record for this coupon getting used.
			// Lets change the tentative record to a permanent one.
			$result = $wpdb->query(
				$wpdb->prepare(
					"
					UPDATE $wpdb->postmeta SET meta_key = %s, meta_value = %s WHERE meta_key = %s LIMIT 1",
					'_used_by',
					$used_by,
					$coupon_held_key
				)
			);
			if ( ! $result ) {
				// If no rows were updated, then insert a `_used_by` row manually to maintain consistency.
				add_post_meta( $coupon->get_id(), '_used_by', strtolower( $used_by ) );
			}
		} else {
			add_post_meta( $coupon->get_id(), '_used_by', strtolower( $used_by ) );
		}
	}

	/**
	 * Decrease usage count for current coupon.
	 *
	 * @since 3.0.0
	 * @param WC_Coupon $coupon Coupon object.
	 * @param string    $used_by Either user ID or billing email.
	 * @return int New usage count.
	 */
	public function decrease_usage_count( &$coupon, $used_by = '' ) {
		global $wpdb;
		$new_count = $this->update_usage_count_meta( $coupon, 'decrease' );
		if ( $used_by ) {
			/**
			 * We're doing this the long way because `delete_post_meta( $id, $key, $value )` deletes.
			 * all instances where the key and value match, and we only want to delete one.
			 */
			$meta_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT meta_id FROM $wpdb->postmeta WHERE meta_key = '_used_by' AND meta_value = %s AND post_id = %d LIMIT 1;",
					$used_by,
					$coupon->get_id()
				)
			);
			if ( $meta_id ) {
				delete_metadata_by_mid( 'post', $meta_id );
				$coupon->set_used_by( (array) get_post_meta( $coupon->get_id(), '_used_by' ) );
			}
		}

		do_action( 'woocommerce_decrease_coupon_usage_count', $coupon, $new_count, $used_by );

		return $new_count;
	}

	/**
	 * Increase or decrease the usage count for a coupon by 1.
	 *
	 * @since 3.0.0
	 * @param WC_Coupon $coupon Coupon object.
	 * @param string    $operation 'increase' or 'decrease'.
	 * @return int New usage count
	 */
	private function update_usage_count_meta( &$coupon, $operation = 'increase' ) {
		global $wpdb;
		$id       = $coupon->get_id();
		$operator = ( 'increase' === $operation ) ? '+' : '-';

		add_post_meta( $id, 'usage_count', $coupon->get_usage_count( 'edit' ), true );
		$wpdb->query(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"UPDATE $wpdb->postmeta SET meta_value = meta_value {$operator} 1 WHERE meta_key = 'usage_count' AND post_id = %d;",
				$id
			)
		);

		// Get the latest value direct from the DB, instead of possibly the WP meta cache.
		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'usage_count' AND post_id = %d;", $id ) );
	}

	/**
	 * Returns tentative usage count for coupon.
	 *
	 * @param int $coupon_id Coupon ID.
	 *
	 * @return int Tentative usage count.
	 */
	public function get_tentative_usage_count( $coupon_id ) {
		global $wpdb;
		return $wpdb->get_var(
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$this->get_tentative_usage_query( $coupon_id )
		);
	}

	/**
	 * Get the number of uses for a coupon by user ID.
	 *
	 * @since 3.0.0
	 * @param WC_Coupon $coupon Coupon object.
	 * @param int       $user_id User ID.
	 * @return int
	 */
	public function get_usage_by_user_id( &$coupon, $user_id ) {
		global $wpdb;
		$usage_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT( meta_id ) FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_used_by' AND meta_value = %d;",
				$coupon->get_id(),
				$user_id
			)
		);
		$tentative_usage_count = $this->get_tentative_usages_for_user( $coupon->get_id(), array( $user_id ) );
		return $tentative_usage_count + $usage_count;
	}

	/**
	 * Get the number of uses for a coupon by email address
	 *
	 * @since 3.6.4
	 * @param WC_Coupon $coupon Coupon object.
	 * @param string    $email Email address.
	 * @return int
	 */
	public function get_usage_by_email( &$coupon, $email ) {
		global $wpdb;
		$usage_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT( meta_id ) FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_used_by' AND meta_value = %s;",
				$coupon->get_id(),
				$email
			)
		);
		$tentative_usage_count = $this->get_tentative_usages_for_user( $coupon->get_id(), array( $email ) );
		return $tentative_usage_count + $usage_count;
	}

	/**
	 * Get tentative coupon usages for user.
	 *
	 * @param int   $coupon_id    Coupon ID.
	 * @param array $user_aliases Array of user aliases to check tentative usages for.
	 *
	 * @return string|null
	 */
	public function get_tentative_usages_for_user( $coupon_id, $user_aliases ) {
		global $wpdb;
		return $wpdb->get_var(
			$this->get_tentative_usage_query_for_user( $coupon_id, $user_aliases )
		); // WPCS: unprepared SQL ok.

	}

	/**
	 * Get held time for resources before cancelling the order. Use 60 minutes as sane default.
	 * Note that the filter `woocommerce_coupon_hold_minutes` only support minutes because it's getting used elsewhere as well, however this function returns in seconds.
	 *
	 * @return int
	 */
	private function get_tentative_held_time() {
		return apply_filters( 'woocommerce_coupon_hold_minutes', ( (int) get_option( 'woocommerce_hold_stock_minutes', 60 ) ) ) * 60;
	}

	/**
	 * Check and records coupon usage tentatively for short period of time so that counts validation is correct. Returns early if there is no limit defined for the coupon.
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 *
	 * @return bool|int|string|null Returns meta key if coupon was held, null if returned early.
	 */
	public function check_and_hold_coupon( $coupon ) {
		global $wpdb;

		$usage_limit = $coupon->get_usage_limit();
		$held_time   = $this->get_tentative_held_time();

		if ( 0 >= $usage_limit || 0 >= $held_time ) {
			return null;
		}

		if ( ! apply_filters( 'woocommerce_hold_stock_for_checkout', true ) ) {
			return null;
		}

		// Make sure we have usage_count meta key for this coupon because its required for `$query_for_usages`.
		// We are not directly modifying `$query_for_usages` to allow for `usage_count` not present only keep that query simple.
		if ( ! metadata_exists( 'post', $coupon->get_id(), 'usage_count' ) ) {
			$coupon->set_usage_count( $coupon->get_usage_count() ); // Use `get_usage_count` here to write default value, which may changed by a filter.
			$coupon->save();
		}

		$query_for_usages = $wpdb->prepare(
			"
			SELECT meta_value from $wpdb->postmeta
			WHERE {$wpdb->postmeta}.meta_key = 'usage_count'
			AND {$wpdb->postmeta}.post_id = %d
			LIMIT 1
			FOR UPDATE
			",
			$coupon->get_id()
		);

		$query_for_tentative_usages = $this->get_tentative_usage_query( $coupon->get_id() );
		$db_timestamp               = $wpdb->get_var( 'SELECT UNIX_TIMESTAMP() FROM DUAL' );

		$coupon_usage_key = '_coupon_held_' . ( (int) $db_timestamp + $held_time ) . '_' . wp_generate_password( 6, false );

		$insert_statement = $wpdb->prepare(
			"
			INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value )
			SELECT %d, %s, %s FROM DUAL
			WHERE ( $query_for_usages ) + ( $query_for_tentative_usages ) < %d
			",
			$coupon->get_id(),
			$coupon_usage_key,
			'',
			$usage_limit
		); // WPCS: unprepared SQL ok.

		/**
		 * In some cases, specifically when there is a combined index on post_id,meta_key, the insert statement above could end up in a deadlock.
		 * We will try to insert 3 times before giving up to recover from deadlock.
		 */
		for ( $count = 0; $count < 3; $count++ ) {
			$result = $wpdb->query( $insert_statement ); // WPCS: unprepared SQL ok.
			if ( false !== $result ) {
				// Clear meta cache.
				wp_cache_delete( WC_Coupon::generate_meta_cache_key( $coupon->get_id(), 'coupons' ), 'coupons' );
				break;
			}
		}

		return $result > 0 ? $coupon_usage_key : $result;
	}

	/**
	 * Generate query to calculate tentative usages for the coupon.
	 *
	 * @param int $coupon_id Coupon ID to get tentative usage query for.
	 *
	 * @return string Query for tentative usages.
	 */
	private function get_tentative_usage_query( $coupon_id ) {
		global $wpdb;
		return $wpdb->prepare(
			"
			SELECT COUNT(meta_id) FROM $wpdb->postmeta
			WHERE {$wpdb->postmeta}.meta_key like %s
			AND {$wpdb->postmeta}.meta_key > %s
			AND {$wpdb->postmeta}.post_id = %d
			FOR UPDATE
			",
			array(
				'_coupon_held_%',
				'_coupon_held_' . time(),
				$coupon_id,
			)
		);  // WPCS: unprepared SQL ok.
	}

	/**
	 * Check and records coupon usage tentatively for passed user aliases for short period of time so that counts validation is correct. Returns early if there is no limit per user for the coupon.
	 *
	 * @param WC_Coupon $coupon       Coupon object.
	 * @param array     $user_aliases Emails or Ids to check for user.
	 * @param string    $user_alias   Email/ID to use as `used_by` value.
	 *
	 * @return null|false|int
	 */
	public function check_and_hold_coupon_for_user( $coupon, $user_aliases, $user_alias ) {
		global $wpdb;
		$limit_per_user = $coupon->get_usage_limit_per_user();
		$held_time      = $this->get_tentative_held_time();

		if ( 0 >= $limit_per_user || 0 >= $held_time ) {
			// This coupon do not have any restriction for usage per customer. No need to check further, lets bail.
			return null;
		}

		if ( ! apply_filters( 'woocommerce_hold_stock_for_checkout', true ) ) {
			return null;
		}

		$format = implode( "','", array_fill( 0, count( $user_aliases ), '%s' ) );

		$query_for_usages = $wpdb->prepare(
			"
				SELECT COUNT(*) FROM $wpdb->postmeta
				WHERE {$wpdb->postmeta}.meta_key = '_used_by'
				AND {$wpdb->postmeta}.meta_value IN ('$format')
				AND {$wpdb->postmeta}.post_id = %d
				FOR UPDATE
				",
			array_merge(
				$user_aliases,
				array( $coupon->get_id() )
			)
		); // WPCS: unprepared SQL ok.

		$query_for_tentative_usages = $this->get_tentative_usage_query_for_user( $coupon->get_id(), $user_aliases );
		$db_timestamp               = $wpdb->get_var( 'SELECT UNIX_TIMESTAMP() FROM DUAL' );

		$coupon_used_by_meta_key    = '_maybe_used_by_' . ( (int) $db_timestamp + $held_time ) . '_' . wp_generate_password( 6, false );
		$insert_statement           = $wpdb->prepare(
			"
			INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value )
			SELECT %d, %s, %s FROM DUAL
			WHERE ( $query_for_usages ) + ( $query_for_tentative_usages ) < %d
			",
			$coupon->get_id(),
			$coupon_used_by_meta_key,
			$user_alias,
			$limit_per_user
		); // WPCS: unprepared SQL ok.

		// This query can potentially be deadlocked if a combined index on post_id and meta_key is present and there is
		// high concurrency, in which case DB will abort the query which has done less work to resolve deadlock.
		// We will try up to 3 times before giving up.
		for ( $count = 0; $count < 3; $count++ ) {
			$result = $wpdb->query( $insert_statement ); // WPCS: unprepared SQL ok.
			if ( false !== $result ) {
				// Clear meta cache.
				wp_cache_delete( WC_Coupon::generate_meta_cache_key( $coupon->get_id(), 'coupons' ), 'coupons' );
				break;
			}
		}

		return $result > 0 ? $coupon_used_by_meta_key : $result;
	}

	/**
	 * Generate query to calculate tentative usages for the coupon by the user.
	 *
	 * @param int   $coupon_id    Coupon ID.
	 * @param array $user_aliases List of user aliases to check for usages.
	 *
	 * @return string Tentative usages query.
	 */
	private function get_tentative_usage_query_for_user( $coupon_id, $user_aliases ) {
		global $wpdb;

		$format = implode( "','", array_fill( 0, count( $user_aliases ), '%s' ) );

		// Note that if you are debugging, `_maybe_used_by_%` will be converted to `_maybe_used_by_{...very long str...}` to very long string. This is expected, and is automatically corrected while running the insert query.
		return $wpdb->prepare(
			"
				SELECT COUNT( meta_id ) FROM $wpdb->postmeta
				WHERE {$wpdb->postmeta}.meta_key like %s
				AND {$wpdb->postmeta}.meta_key > %s
				AND {$wpdb->postmeta}.post_id = %d
				AND {$wpdb->postmeta}.meta_value IN ('$format')
				FOR UPDATE
				",
			array_merge(
				array(
					'_maybe_used_by_%',
					'_maybe_used_by_' . time(),
					$coupon_id,
				),
				$user_aliases
			)
		); // WPCS: unprepared SQL ok.
	}

	/**
	 * Return a coupon code for a specific ID.
	 *
	 * @since 3.0.0
	 * @param int $id Coupon ID.
	 * @return string Coupon Code
	 */
	public function get_code_by_id( $id ) {
		global $wpdb;
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_title
				FROM $wpdb->posts
				WHERE ID = %d
				AND post_type = 'shop_coupon'
				AND post_status = 'publish'",
				$id
			)
		);
	}

	/**
	 * Return an array of IDs for for a specific coupon code.
	 * Can return multiple to check for existence.
	 *
	 * @since 3.0.0
	 * @param string $code Coupon code.
	 * @return array Array of IDs.
	 */
	public function get_ids_by_code( $code ) {
		global $wpdb;
		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_date DESC",
				wc_sanitize_coupon_code( $code )
			)
		);
	}
}

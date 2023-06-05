<?php
/**
 * Post Data
 *
 * Standardises certain post data on save.
 *
 * @package WooCommerce\Classes\Data
 * @version 2.2.0
 */

use Automattic\WooCommerce\Internal\DataStores\Orders\DataSynchronizer;
use Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore as ProductAttributesLookupDataStore;
use Automattic\WooCommerce\Proxies\LegacyProxy;
use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Post data class.
 */
class WC_Post_Data {

	/**
	 * Editing term.
	 *
	 * @var object
	 */
	private static $editing_term = null;

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_filter( 'post_type_link', array( __CLASS__, 'variation_post_link' ), 10, 2 );
		add_action( 'shutdown', array( __CLASS__, 'do_deferred_product_sync' ), 10 );
		add_action( 'set_object_terms', array( __CLASS__, 'force_default_term' ), 10, 5 );
		add_action( 'set_object_terms', array( __CLASS__, 'delete_product_query_transients' ) );
		add_action( 'deleted_term_relationships', array( __CLASS__, 'delete_product_query_transients' ) );
		add_action( 'woocommerce_product_set_stock_status', array( __CLASS__, 'delete_product_query_transients' ) );
		add_action( 'woocommerce_product_set_visibility', array( __CLASS__, 'delete_product_query_transients' ) );
		add_action( 'woocommerce_product_type_changed', array( __CLASS__, 'product_type_changed' ), 10, 3 );

		add_action( 'edit_term', array( __CLASS__, 'edit_term' ), 10, 3 );
		add_action( 'edited_term', array( __CLASS__, 'edited_term' ), 10, 3 );
		add_filter( 'update_order_item_metadata', array( __CLASS__, 'update_order_item_metadata' ), 10, 5 );
		add_filter( 'update_post_metadata', array( __CLASS__, 'update_post_metadata' ), 10, 5 );
		add_filter( 'wp_insert_post_data', array( __CLASS__, 'wp_insert_post_data' ) );
		add_filter( 'oembed_response_data', array( __CLASS__, 'filter_oembed_response_data' ), 10, 2 );
		add_filter( 'wp_untrash_post_status', array( __CLASS__, 'wp_untrash_post_status' ), 10, 3 );

		// Status transitions.
		add_action( 'transition_post_status', array( __CLASS__, 'transition_post_status' ), 10, 3 );
		add_action( 'delete_post', array( __CLASS__, 'delete_post' ) );
		add_action( 'wp_trash_post', array( __CLASS__, 'trash_post' ) );
		add_action( 'untrashed_post', array( __CLASS__, 'untrash_post' ) );
		add_action( 'before_delete_post', array( __CLASS__, 'before_delete_order' ) );
		add_action( 'woocommerce_before_delete_order', array( __CLASS__, 'before_delete_order' ) );

		// Meta cache flushing.
		add_action( 'updated_post_meta', array( __CLASS__, 'flush_object_meta_cache' ), 10, 4 );
		add_action( 'updated_order_item_meta', array( __CLASS__, 'flush_object_meta_cache' ), 10, 4 );
	}

	/**
	 * Link to parent products when getting permalink for variation.
	 *
	 * @param string  $permalink Permalink.
	 * @param WP_Post $post      Post data.
	 *
	 * @return string
	 */
	public static function variation_post_link( $permalink, $post ) {
		if ( isset( $post->ID, $post->post_type ) && 'product_variation' === $post->post_type ) {
			$variation = wc_get_product( $post->ID );

			if ( $variation && $variation->get_parent_id() ) {
				return $variation->get_permalink();
			}
		}
		return $permalink;
	}

	/**
	 * Sync products queued to sync.
	 */
	public static function do_deferred_product_sync() {
		global $wc_deferred_product_sync;

		if ( ! empty( $wc_deferred_product_sync ) ) {
			$wc_deferred_product_sync = wp_parse_id_list( $wc_deferred_product_sync );
			array_walk( $wc_deferred_product_sync, array( __CLASS__, 'deferred_product_sync' ) );
		}
	}

	/**
	 * Sync a product.
	 *
	 * @param int $product_id Product ID.
	 */
	public static function deferred_product_sync( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( is_callable( array( $product, 'sync' ) ) ) {
			$product->sync( $product );
		}
	}

	/**
	 * When a post status changes.
	 *
	 * @param string  $new_status New status.
	 * @param string  $old_status Old status.
	 * @param WP_Post $post       Post data.
	 */
	public static function transition_post_status( $new_status, $old_status, $post ) {
		if ( ( 'publish' === $new_status || 'publish' === $old_status ) && in_array( $post->post_type, array( 'product', 'product_variation' ), true ) ) {
			self::delete_product_query_transients();
		}
	}

	/**
	 * Delete product view transients when needed e.g. when post status changes, or visibility/stock status is modified.
	 */
	public static function delete_product_query_transients() {
		WC_Cache_Helper::get_transient_version( 'product_query', true );
	}

	/**
	 * Handle type changes.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Product $product Product data.
	 * @param string     $from    Origin type.
	 * @param string     $to      New type.
	 */
	public static function product_type_changed( $product, $from, $to ) {
		/**
		 * Filter to prevent variations from being deleted while switching from a variable product type to a variable product type.
		 *
		 * @since 5.0.0
		 *
		 * @param bool       A boolean value of true will delete the variations.
		 * @param WC_Product $product Product data.
		 * @return string    $from    Origin type.
		 * @param string     $to      New type.
		 */
		if ( apply_filters( 'woocommerce_delete_variations_on_product_type_change', 'variable' === $from && 'variable' !== $to, $product, $from, $to ) ) {
			// If the product is no longer variable, we should ensure all variations are removed.
			$data_store = WC_Data_Store::load( 'product-variable' );
			$data_store->delete_variations( $product->get_id(), true );
		}
	}

	/**
	 * When editing a term, check for product attributes.
	 *
	 * @param  int    $term_id  Term ID.
	 * @param  int    $tt_id    Term taxonomy ID.
	 * @param  string $taxonomy Taxonomy slug.
	 */
	public static function edit_term( $term_id, $tt_id, $taxonomy ) {
		if ( strpos( $taxonomy, 'pa_' ) === 0 ) {
			self::$editing_term = get_term_by( 'id', $term_id, $taxonomy );
		} else {
			self::$editing_term = null;
		}
	}

	/**
	 * When a term is edited, check for product attributes and update variations.
	 *
	 * @param  int    $term_id  Term ID.
	 * @param  int    $tt_id    Term taxonomy ID.
	 * @param  string $taxonomy Taxonomy slug.
	 */
	public static function edited_term( $term_id, $tt_id, $taxonomy ) {
		if ( ! is_null( self::$editing_term ) && strpos( $taxonomy, 'pa_' ) === 0 ) {
			$edited_term = get_term_by( 'id', $term_id, $taxonomy );

			if ( $edited_term->slug !== self::$editing_term->slug ) {
				global $wpdb;

				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE meta_key = %s AND meta_value = %s;", $edited_term->slug, 'attribute_' . sanitize_title( $taxonomy ), self::$editing_term->slug ) );

				$wpdb->query(
					$wpdb->prepare(
						"UPDATE {$wpdb->postmeta} SET meta_value = REPLACE( meta_value, %s, %s ) WHERE meta_key = '_default_attributes'",
						serialize( self::$editing_term->taxonomy ) . serialize( self::$editing_term->slug ),
						serialize( $edited_term->taxonomy ) . serialize( $edited_term->slug )
					)
				);
			}
		} else {
			self::$editing_term = null;
		}
	}

	/**
	 * Ensure floats are correctly converted to strings based on PHP locale.
	 *
	 * @param  null   $check      Whether to allow updating metadata for the given type.
	 * @param  int    $object_id  Object ID.
	 * @param  string $meta_key   Meta key.
	 * @param  mixed  $meta_value Meta value. Must be serializable if non-scalar.
	 * @param  mixed  $prev_value If specified, only update existing metadata entries with the specified value. Otherwise, update all entries.
	 * @return null|bool
	 */
	public static function update_order_item_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
		if ( ! empty( $meta_value ) && is_float( $meta_value ) ) {

			// Convert float to string.
			$meta_value = wc_float_to_string( $meta_value );

			// Update meta value with new string.
			update_metadata( 'order_item', $object_id, $meta_key, $meta_value, $prev_value );

			return true;
		}
		return $check;
	}

	/**
	 * Ensure floats are correctly converted to strings based on PHP locale.
	 *
	 * @param  null   $check      Whether to allow updating metadata for the given type.
	 * @param  int    $object_id  Object ID.
	 * @param  string $meta_key   Meta key.
	 * @param  mixed  $meta_value Meta value. Must be serializable if non-scalar.
	 * @param  mixed  $prev_value If specified, only update existing metadata entries with the specified value. Otherwise, update all entries.
	 * @return null|bool
	 */
	public static function update_post_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
		// Delete product cache if someone uses meta directly.
		if ( in_array( get_post_type( $object_id ), array( 'product', 'product_variation' ), true ) ) {
			wp_cache_delete( 'product-' . $object_id, 'products' );
		}

		if ( ! empty( $meta_value ) && is_float( $meta_value ) && ! registered_meta_key_exists( 'post', $meta_key ) && in_array( get_post_type( $object_id ), array_merge( wc_get_order_types(), array( 'shop_coupon', 'product', 'product_variation' ) ), true ) ) {

			// Convert float to string.
			$meta_value = wc_float_to_string( $meta_value );

			// Update meta value with new string.
			update_metadata( 'post', $object_id, $meta_key, $meta_value, $prev_value );

			return true;
		}
		return $check;
	}

	/**
	 * Forces the order posts to have a title in a certain format (containing the date).
	 * Forces certain product data based on the product's type, e.g. grouped products cannot have a parent.
	 *
	 * @param array $data An array of slashed post data.
	 * @return array
	 */
	public static function wp_insert_post_data( $data ) {
		if ( 'shop_order' === $data['post_type'] && isset( $data['post_date'] ) ) {
			$order_title = 'Order';
			if ( $data['post_date'] ) {
				$order_title .= ' &ndash; ' . date_i18n( 'F j, Y @ h:i A', strtotime( $data['post_date'] ) );
			}
			$data['post_title'] = $order_title;
		} elseif ( 'product' === $data['post_type'] && isset( $_POST['product-type'] ) ) { // WPCS: input var ok, CSRF ok.
			$product_type = wc_clean( wp_unslash( $_POST['product-type'] ) ); // WPCS: input var ok, CSRF ok.
			switch ( $product_type ) {
				case 'grouped':
				case 'variable':
					$data['post_parent'] = 0;
					break;
			}
		} elseif ( 'product' === $data['post_type'] && 'auto-draft' === $data['post_status'] ) {
			$data['post_title'] = 'AUTO-DRAFT';
		} elseif ( 'shop_coupon' === $data['post_type'] ) {
			// Coupons should never allow unfiltered HTML.
			$data['post_title'] = wp_filter_kses( $data['post_title'] );
		}

		return $data;
	}

	/**
	 * Change embed data for certain post types.
	 *
	 * @since 3.2.0
	 * @param array   $data The response data.
	 * @param WP_Post $post The post object.
	 * @return array
	 */
	public static function filter_oembed_response_data( $data, $post ) {
		if ( in_array( $post->post_type, array( 'shop_order', 'shop_coupon' ), true ) ) {
			return array();
		}
		return $data;
	}

	/**
	 * Removes variations etc belonging to a deleted post, and clears transients.
	 *
	 * @param mixed $id ID of post being deleted.
	 */
	public static function delete_post( $id ) {
		$container = wc_get_container();
		if ( ! $container->get( LegacyProxy::class )->call_function( 'current_user_can', 'delete_posts' ) || ! $id ) {
			return;
		}

		$post_type = self::get_post_type( $id );
		switch ( $post_type ) {
			case 'product':
				$data_store = WC_Data_Store::load( 'product-variable' );
				$data_store->delete_variations( $id, true );
				$data_store->delete_from_lookup_table( $id, 'wc_product_meta_lookup' );
				$container->get( ProductAttributesLookupDataStore::class )->on_product_deleted( $id );

				$parent_id = wp_get_post_parent_id( $id );
				if ( $parent_id ) {
					wc_delete_product_transients( $parent_id );
				}

				break;
			case 'product_variation':
				$data_store = WC_Data_Store::load( 'product' );
				$data_store->delete_from_lookup_table( $id, 'wc_product_meta_lookup' );
				wc_delete_product_transients( wp_get_post_parent_id( $id ) );
				$container->get( ProductAttributesLookupDataStore::class )->on_product_deleted( $id );

				break;
			case 'shop_order':
			case DataSynchronizer::PLACEHOLDER_ORDER_POST_TYPE:
				global $wpdb;

				$refunds = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'shop_order_refund' AND post_parent = %d", $id ) );

				if ( ! is_null( $refunds ) ) {
					foreach ( $refunds as $refund ) {
						wp_delete_post( $refund->ID, true );
					}
				}
				break;
		}
	}

	/**
	 * Trash post.
	 *
	 * @param mixed $id Post ID.
	 */
	public static function trash_post( $id ) {
		if ( ! $id ) {
			return;
		}

		$post_type = self::get_post_type( $id );

		// If this is an order, trash any refunds too.
		if ( in_array( $post_type, wc_get_order_types( 'order-count' ), true ) ) {
			global $wpdb;

			$refunds = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'shop_order_refund' AND post_parent = %d", $id ) );

			foreach ( $refunds as $refund ) {
				$wpdb->update( $wpdb->posts, array( 'post_status' => 'trash' ), array( 'ID' => $refund->ID ) );
			}

			wc_delete_shop_order_transients( $id );

			// If this is a product, trash children variations.
		} elseif ( 'product' === $post_type ) {
			$data_store = WC_Data_Store::load( 'product-variable' );
			$data_store->delete_variations( $id, false );
			wc_get_container()->get( ProductAttributesLookupDataStore::class )->on_product_deleted( $id );
		} elseif ( 'product_variation' === $post_type ) {
			wc_get_container()->get( ProductAttributesLookupDataStore::class )->on_product_deleted( $id );
		}
	}

	/**
	 * Untrash post.
	 *
	 * @param mixed $id Post ID.
	 */
	public static function untrash_post( $id ) {
		if ( ! $id ) {
			return;
		}

		$post_type = self::get_post_type( $id );

		if ( in_array( $post_type, wc_get_order_types( 'order-count' ), true ) ) {
			global $wpdb;

			$refunds = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'shop_order_refund' AND post_parent = %d", $id ) );

			foreach ( $refunds as $refund ) {
				$wpdb->update( $wpdb->posts, array( 'post_status' => 'wc-completed' ), array( 'ID' => $refund->ID ) );
			}

			wc_delete_shop_order_transients( $id );

		} elseif ( 'product' === $post_type ) {
			$data_store = WC_Data_Store::load( 'product-variable' );
			$data_store->untrash_variations( $id );

			wc_product_force_unique_sku( $id );

			wc_get_container()->get( ProductAttributesLookupDataStore::class )->on_product_changed( $id );
		} elseif ( 'product_variation' === $post_type ) {
			wc_get_container()->get( ProductAttributesLookupDataStore::class )->on_product_changed( $id );
		}
	}

	/**
	 * Get the post type for a given post.
	 *
	 * @param int $id The post id.
	 * @return string The post type.
	 */
	private static function get_post_type( $id ) {
		return wc_get_container()->get( LegacyProxy::class )->call_function( 'get_post_type', $id );
	}

	/**
	 * Before deleting an order, do some cleanup.
	 *
	 * @since 3.2.0
	 * @param int $order_id Order ID.
	 */
	public static function before_delete_order( $order_id ) {
		if ( OrderUtil::is_order( $order_id, wc_get_order_types() ) ) {
			// Clean up user.
			$order = wc_get_order( $order_id );

			// Check for `get_customer_id`, since this may be e.g. a refund order (which doesn't implement it).
			$customer_id = is_callable( array( $order, 'get_customer_id' ) ) ? $order->get_customer_id() : 0;

			if ( $customer_id > 0 && 'shop_order' === $order->get_type() ) {
				$customer    = new WC_Customer( $customer_id );
				$order_count = $customer->get_order_count();
				$order_count --;

				if ( 0 === $order_count ) {
					$customer->set_is_paying_customer( false );
					$customer->save();
				}

				// Delete order count and last order meta.
				delete_user_meta( $customer_id, '_order_count' );
				delete_user_meta( $customer_id, '_last_order' );
			}

			// Clean up items.
			self::delete_order_items( $order_id );
			self::delete_order_downloadable_permissions( $order_id );
		}
	}

	/**
	 * Remove item meta on permanent deletion.
	 *
	 * @param int $postid Post ID.
	 */
	public static function delete_order_items( $postid ) {
		global $wpdb;

		if ( OrderUtil::is_order( $postid, wc_get_order_types() ) ) {
			do_action( 'woocommerce_delete_order_items', $postid );

			$wpdb->query(
				"
				DELETE {$wpdb->prefix}woocommerce_order_items, {$wpdb->prefix}woocommerce_order_itemmeta
				FROM {$wpdb->prefix}woocommerce_order_items
				JOIN {$wpdb->prefix}woocommerce_order_itemmeta ON {$wpdb->prefix}woocommerce_order_items.order_item_id = {$wpdb->prefix}woocommerce_order_itemmeta.order_item_id
				WHERE {$wpdb->prefix}woocommerce_order_items.order_id = '{$postid}';
				"
			); // WPCS: unprepared SQL ok.

			do_action( 'woocommerce_deleted_order_items', $postid );
		}
	}

	/**
	 * Remove downloadable permissions on permanent order deletion.
	 *
	 * @param int $postid Post ID.
	 */
	public static function delete_order_downloadable_permissions( $postid ) {
		if ( OrderUtil::is_order( $postid, wc_get_order_types() ) ) {
			do_action( 'woocommerce_delete_order_downloadable_permissions', $postid );

			$data_store = WC_Data_Store::load( 'customer-download' );
			$data_store->delete_by_order_id( $postid );

			do_action( 'woocommerce_deleted_order_downloadable_permissions', $postid );
		}
	}

	/**
	 * Flush meta cache for CRUD objects on direct update.
	 *
	 * @param  int    $meta_id    Meta ID.
	 * @param  int    $object_id  Object ID.
	 * @param  string $meta_key   Meta key.
	 * @param  mixed  $meta_value Meta value.
	 */
	public static function flush_object_meta_cache( $meta_id, $object_id, $meta_key, $meta_value ) {
		WC_Cache_Helper::invalidate_cache_group( 'object_' . $object_id );
	}

	/**
	 * Ensure default category gets set.
	 *
	 * @since 3.3.0
	 * @param int    $object_id Product ID.
	 * @param array  $terms     Terms array.
	 * @param array  $tt_ids    Term ids array.
	 * @param string $taxonomy  Taxonomy name.
	 * @param bool   $append    Are we appending or setting terms.
	 */
	public static function force_default_term( $object_id, $terms, $tt_ids, $taxonomy, $append ) {
		if ( ! $append && 'product_cat' === $taxonomy && empty( $tt_ids ) && 'product' === get_post_type( $object_id ) ) {
			$default_term = absint( get_option( 'default_product_cat', 0 ) );
			$tt_ids       = array_map( 'absint', $tt_ids );

			if ( $default_term && ! in_array( $default_term, $tt_ids, true ) ) {
				wp_set_post_terms( $object_id, array( $default_term ), 'product_cat', true );
			}
		}
	}

	/**
	 * Ensure statuses are correctly reassigned when restoring orders and products.
	 *
	 * @param string $new_status      The new status of the post being restored.
	 * @param int    $post_id         The ID of the post being restored.
	 * @param string $previous_status The status of the post at the point where it was trashed.
	 * @return string
	 */
	public static function wp_untrash_post_status( $new_status, $post_id, $previous_status ) {
		$post_types = array( 'shop_order', 'shop_coupon', 'product', 'product_variation' );

		if ( in_array( get_post_type( $post_id ), $post_types, true ) ) {
			$new_status = $previous_status;
		}

		return $new_status;
	}

	/**
	 * When setting stock level, ensure the stock status is kept in sync.
	 *
	 * @param  int    $meta_id    Meta ID.
	 * @param  int    $object_id  Object ID.
	 * @param  string $meta_key   Meta key.
	 * @param  mixed  $meta_value Meta value.
	 * @deprecated    3.3
	 */
	public static function sync_product_stock_status( $meta_id, $object_id, $meta_key, $meta_value ) {}

	/**
	 * Update changed downloads.
	 *
	 * @deprecated  3.3.0 No action is necessary on changes to download paths since download_id is no longer based on file hash.
	 * @param int   $product_id   Product ID.
	 * @param int   $variation_id Variation ID. Optional product variation identifier.
	 * @param array $downloads    Newly set files.
	 */
	public static function process_product_file_download_paths( $product_id, $variation_id, $downloads ) {
		wc_deprecated_function( __FUNCTION__, '3.3' );
	}

	/**
	 * Delete transients when terms are set.
	 *
	 * @deprecated   3.6
	 * @param int    $object_id  Object ID.
	 * @param mixed  $terms      An array of object terms.
	 * @param array  $tt_ids     An array of term taxonomy IDs.
	 * @param string $taxonomy   Taxonomy slug.
	 * @param mixed  $append     Whether to append new terms to the old terms.
	 * @param array  $old_tt_ids Old array of term taxonomy IDs.
	 */
	public static function set_object_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		if ( in_array( get_post_type( $object_id ), array( 'product', 'product_variation' ), true ) ) {
			self::delete_product_query_transients();
		}
	}
}

WC_Post_Data::init();

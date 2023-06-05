<?php
/**
 * Class Abstract_WC_Order_Item_Type_Data_Store file.
 *
 * @package WooCommerce\DataStores
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Order Item Data Store
 *
 * @version  3.0.0
 */
abstract class Abstract_WC_Order_Item_Type_Data_Store extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {

	/**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 *
	 * @var string
	 */
	protected $meta_type = 'order_item';

	/**
	 * This only needs set if you are using a custom metadata type (for example payment tokens.
	 * This should be the name of the field your table uses for associating meta with objects.
	 * For example, in payment_tokenmeta, this would be payment_token_id.
	 *
	 * @var string
	 */
	protected $object_id_field_for_meta = 'order_item_id';

	/**
	 * Create a new order item in the database.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item $item Order item object.
	 */
	public function create( &$item ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'woocommerce_order_items',
			array(
				'order_item_name' => $item->get_name(),
				'order_item_type' => $item->get_type(),
				'order_id'        => $item->get_order_id(),
			)
		);
		$item->set_id( $wpdb->insert_id );
		$this->save_item_data( $item );
		$item->save_meta_data();
		$item->apply_changes();
		$this->clear_cache( $item );

		do_action( 'woocommerce_new_order_item', $item->get_id(), $item, $item->get_order_id() );
	}

	/**
	 * Update a order item in the database.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item $item Order item object.
	 */
	public function update( &$item ) {
		global $wpdb;

		$changes = $item->get_changes();

		if ( array_intersect( array( 'name', 'order_id' ), array_keys( $changes ) ) ) {
			$wpdb->update(
				$wpdb->prefix . 'woocommerce_order_items',
				array(
					'order_item_name' => $item->get_name(),
					'order_item_type' => $item->get_type(),
					'order_id'        => $item->get_order_id(),
				),
				array( 'order_item_id' => $item->get_id() )
			);
		}

		$this->save_item_data( $item );
		$item->save_meta_data();
		$item->apply_changes();
		$this->clear_cache( $item );

		do_action( 'woocommerce_update_order_item', $item->get_id(), $item, $item->get_order_id() );
	}

	/**
	 * Remove an order item from the database.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item $item Order item object.
	 * @param array         $args Array of args to pass to the delete method.
	 */
	public function delete( &$item, $args = array() ) {
		if ( $item->get_id() ) {
			global $wpdb;
			do_action( 'woocommerce_before_delete_order_item', $item->get_id() );
			$wpdb->delete( $wpdb->prefix . 'woocommerce_order_items', array( 'order_item_id' => $item->get_id() ) );
			$wpdb->delete( $wpdb->prefix . 'woocommerce_order_itemmeta', array( 'order_item_id' => $item->get_id() ) );
			do_action( 'woocommerce_delete_order_item', $item->get_id() );
			$this->clear_cache( $item );
		}
	}

	/**
	 * Read a order item from the database.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order_Item $item Order item object.
	 *
	 * @throws Exception If invalid order item.
	 */
	public function read( &$item ) {
		global $wpdb;

		$item->set_defaults();

		// Get from cache if available.
		$data = wp_cache_get( 'item-' . $item->get_id(), 'order-items' );

		if ( false === $data ) {
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT order_id, order_item_name FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d LIMIT 1;", $item->get_id() ) );
			wp_cache_set( 'item-' . $item->get_id(), $data, 'order-items' );
		}

		if ( ! $data ) {
			throw new Exception( __( 'Invalid order item.', 'woocommerce' ) );
		}

		$item->set_props(
			array(
				'order_id' => $data->order_id,
				'name'     => $data->order_item_name,
			)
		);
		$item->read_meta_data();
	}

	/**
	 * Saves an item's data to the database / item meta.
	 * Ran after both create and update, so $item->get_id() will be set.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item $item Order item object.
	 */
	public function save_item_data( &$item ) {}

	/**
	 * Clear meta cache.
	 *
	 * @param WC_Order_Item $item Order item object.
	 */
	public function clear_cache( &$item ) {
		wp_cache_delete( 'item-' . $item->get_id(), 'order-items' );
		wp_cache_delete( 'order-items-' . $item->get_order_id(), 'orders' );
		wp_cache_delete( $item->get_id(), $this->meta_type . '_meta' );
	}
}

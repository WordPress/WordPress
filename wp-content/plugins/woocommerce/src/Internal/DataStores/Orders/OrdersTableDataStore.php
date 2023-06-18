<?php
/**
 * OrdersTableDataStore class file.
 */

namespace Automattic\WooCommerce\Internal\DataStores\Orders;

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Caches\OrderCache;
use Automattic\WooCommerce\Internal\Utilities\DatabaseUtil;
use Automattic\WooCommerce\Proxies\LegacyProxy;
use Automattic\WooCommerce\Utilities\ArrayUtil;
use Exception;
use WC_Abstract_Order;
use WC_Data;
use WC_Order;

defined( 'ABSPATH' ) || exit;

/**
 * This class is the standard data store to be used when the custom orders table is in use.
 */
class OrdersTableDataStore extends \Abstract_WC_Order_Data_Store_CPT implements \WC_Object_Data_Store_Interface, \WC_Order_Data_Store_Interface {

	/**
	 * Order IDs for which we are checking read on sync in the current request.
	 *
	 * @var array.
	 */
	private static $reading_order_ids = array();

	/**
	 * Data stored in meta keys, but not considered "meta" for an order.
	 *
	 * @since 7.0.0
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
	 * Handles custom metadata in the wc_orders_meta table.
	 *
	 * @var OrdersTableDataStoreMeta
	 */
	protected $data_store_meta;

	/**
	 * The database util object to use.
	 *
	 * @var DatabaseUtil
	 */
	protected $database_util;

	/**
	 * The posts data store object to use.
	 *
	 * @var \WC_Order_Data_Store_CPT
	 */
	private $cpt_data_store;

	/**
	 * Logger object to be used to log events.
	 *
	 * @var \WC_Logger
	 */
	private $error_logger;

	/**
	 * The instance of the LegacyProxy object to use.
	 *
	 * @var LegacyProxy
	 */
	private $legacy_proxy;

	/**
	 * Initialize the object.
	 *
	 * @internal
	 * @param OrdersTableDataStoreMeta $data_store_meta Metadata instance.
	 * @param DatabaseUtil             $database_util   The database util instance to use.
	 * @param LegacyProxy              $legacy_proxy    The legacy proxy instance to use.
	 *
	 * @return void
	 */
	final public function init( OrdersTableDataStoreMeta $data_store_meta, DatabaseUtil $database_util, LegacyProxy $legacy_proxy ) {
		$this->data_store_meta    = $data_store_meta;
		$this->database_util      = $database_util;
		$this->legacy_proxy       = $legacy_proxy;
		$this->error_logger       = $legacy_proxy->call_function( 'wc_get_logger' );
		$this->internal_meta_keys = $this->get_internal_meta_keys();
	}

	/**
	 * Get the custom orders table name.
	 *
	 * @return string The custom orders table name.
	 */
	public static function get_orders_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'wc_orders';
	}

	/**
	 * Get the order addresses table name.
	 *
	 * @return string The order addresses table name.
	 */
	public static function get_addresses_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'wc_order_addresses';
	}

	/**
	 * Get the orders operational data table name.
	 *
	 * @return string The orders operational data table name.
	 */
	public static function get_operational_data_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'wc_order_operational_data';
	}

	/**
	 * Get the orders meta data table name.
	 *
	 * @return string Name of order meta data table.
	 */
	public static function get_meta_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'wc_orders_meta';
	}

	/**
	 * Get the names of all the tables involved in the custom orders table feature.
	 *
	 * See also : get_all_table_names_with_id.
	 *
	 * @return string[]
	 */
	public function get_all_table_names() {
		return array(
			$this->get_orders_table_name(),
			$this->get_addresses_table_name(),
			$this->get_operational_data_table_name(),
			$this->get_meta_table_name(),
		);
	}

	/**
	 * Similar to get_all_table_names, but also returns the table name along with the items table.
	 *
	 * @return array Names of the tables.
	 */
	public static function get_all_table_names_with_id() {
		global $wpdb;
		return array(
			'orders'           => self::get_orders_table_name(),
			'addresses'        => self::get_addresses_table_name(),
			'operational_data' => self::get_operational_data_table_name(),
			'meta'             => self::get_meta_table_name(),
			'items'            => $wpdb->prefix . 'woocommerce_order_items',
		);
	}

	/**
	 * Table column to WC_Order mapping for wc_orders table.
	 *
	 * @var \string[][]
	 */
	protected $order_column_mapping = array(
		'id'                   => array(
			'type' => 'int',
			'name' => 'id',
		),
		'status'               => array(
			'type' => 'string',
			'name' => 'status',
		),
		'type'                 => array(
			'type' => 'string',
			'name' => 'type',
		),
		'currency'             => array(
			'type' => 'string',
			'name' => 'currency',
		),
		'tax_amount'           => array(
			'type' => 'decimal',
			'name' => 'cart_tax',
		),
		'total_amount'         => array(
			'type' => 'decimal',
			'name' => 'total',
		),
		'customer_id'          => array(
			'type' => 'int',
			'name' => 'customer_id',
		),
		'billing_email'        => array(
			'type' => 'string',
			'name' => 'billing_email',
		),
		'date_created_gmt'     => array(
			'type' => 'date',
			'name' => 'date_created',
		),
		'date_updated_gmt'     => array(
			'type' => 'date',
			'name' => 'date_modified',
		),
		'parent_order_id'      => array(
			'type' => 'int',
			'name' => 'parent_id',
		),
		'payment_method'       => array(
			'type' => 'string',
			'name' => 'payment_method',
		),
		'payment_method_title' => array(
			'type' => 'string',
			'name' => 'payment_method_title',
		),
		'ip_address'           => array(
			'type' => 'string',
			'name' => 'customer_ip_address',
		),
		'transaction_id'       => array(
			'type' => 'string',
			'name' => 'transaction_id',
		),
		'user_agent'           => array(
			'type' => 'string',
			'name' => 'customer_user_agent',
		),
		'customer_note'        => array(
			'type' => 'string',
			'name' => 'customer_note',
		),
	);

	/**
	 * Table column to WC_Order mapping for billing addresses in wc_address table.
	 *
	 * @var \string[][]
	 */
	protected $billing_address_column_mapping = array(
		'id'           => array( 'type' => 'int' ),
		'order_id'     => array( 'type' => 'int' ),
		'address_type' => array( 'type' => 'string' ),
		'first_name'   => array(
			'type' => 'string',
			'name' => 'billing_first_name',
		),
		'last_name'    => array(
			'type' => 'string',
			'name' => 'billing_last_name',
		),
		'company'      => array(
			'type' => 'string',
			'name' => 'billing_company',
		),
		'address_1'    => array(
			'type' => 'string',
			'name' => 'billing_address_1',
		),
		'address_2'    => array(
			'type' => 'string',
			'name' => 'billing_address_2',
		),
		'city'         => array(
			'type' => 'string',
			'name' => 'billing_city',
		),
		'state'        => array(
			'type' => 'string',
			'name' => 'billing_state',
		),
		'postcode'     => array(
			'type' => 'string',
			'name' => 'billing_postcode',
		),
		'country'      => array(
			'type' => 'string',
			'name' => 'billing_country',
		),
		'email'        => array(
			'type' => 'string',
			'name' => 'billing_email',
		),
		'phone'        => array(
			'type' => 'string',
			'name' => 'billing_phone',
		),
	);

	/**
	 * Table column to WC_Order mapping for shipping addresses in wc_address table.
	 *
	 * @var \string[][]
	 */
	protected $shipping_address_column_mapping = array(
		'id'           => array( 'type' => 'int' ),
		'order_id'     => array( 'type' => 'int' ),
		'address_type' => array( 'type' => 'string' ),
		'first_name'   => array(
			'type' => 'string',
			'name' => 'shipping_first_name',
		),
		'last_name'    => array(
			'type' => 'string',
			'name' => 'shipping_last_name',
		),
		'company'      => array(
			'type' => 'string',
			'name' => 'shipping_company',
		),
		'address_1'    => array(
			'type' => 'string',
			'name' => 'shipping_address_1',
		),
		'address_2'    => array(
			'type' => 'string',
			'name' => 'shipping_address_2',
		),
		'city'         => array(
			'type' => 'string',
			'name' => 'shipping_city',
		),
		'state'        => array(
			'type' => 'string',
			'name' => 'shipping_state',
		),
		'postcode'     => array(
			'type' => 'string',
			'name' => 'shipping_postcode',
		),
		'country'      => array(
			'type' => 'string',
			'name' => 'shipping_country',
		),
		'email'        => array( 'type' => 'string' ),
		'phone'        => array(
			'type' => 'string',
			'name' => 'shipping_phone',
		),
	);

	/**
	 * Table column to WC_Order mapping for wc_operational_data table.
	 *
	 * @var \string[][]
	 */
	protected $operational_data_column_mapping = array(
		'id'                          => array( 'type' => 'int' ),
		'order_id'                    => array( 'type' => 'int' ),
		'created_via'                 => array(
			'type' => 'string',
			'name' => 'created_via',
		),
		'woocommerce_version'         => array(
			'type' => 'string',
			'name' => 'version',
		),
		'prices_include_tax'          => array(
			'type' => 'bool',
			'name' => 'prices_include_tax',
		),
		'coupon_usages_are_counted'   => array(
			'type' => 'bool',
			'name' => 'recorded_coupon_usage_counts',
		),
		'download_permission_granted' => array(
			'type' => 'bool',
			'name' => 'download_permissions_granted',
		),
		'cart_hash'                   => array(
			'type' => 'string',
			'name' => 'cart_hash',
		),
		'new_order_email_sent'        => array(
			'type' => 'bool',
			'name' => 'new_order_email_sent',
		),
		'order_key'                   => array(
			'type' => 'string',
			'name' => 'order_key',
		),
		'order_stock_reduced'         => array(
			'type' => 'bool',
			'name' => 'order_stock_reduced',
		),
		'date_paid_gmt'               => array(
			'type' => 'date',
			'name' => 'date_paid',
		),
		'date_completed_gmt'          => array(
			'type' => 'date',
			'name' => 'date_completed',
		),
		'shipping_tax_amount'         => array(
			'type' => 'decimal',
			'name' => 'shipping_tax',
		),
		'shipping_total_amount'       => array(
			'type' => 'decimal',
			'name' => 'shipping_total',
		),
		'discount_tax_amount'         => array(
			'type' => 'decimal',
			'name' => 'discount_tax',
		),
		'discount_total_amount'       => array(
			'type' => 'decimal',
			'name' => 'discount_total',
		),
		'recorded_sales'              => array(
			'type' => 'bool',
			'name' => 'recorded_sales',
		),
	);

	/**
	 * Cache variable to store combined mapping.
	 *
	 * @var array[][][]
	 */
	private $all_order_column_mapping;

	/**
	 * Return combined mappings for all order tables.
	 *
	 * @return array|\array[][][] Return combined mapping.
	 */
	public function get_all_order_column_mappings() {
		if ( ! isset( $this->all_order_column_mapping ) ) {
			$this->all_order_column_mapping = array(
				'orders'           => $this->order_column_mapping,
				'billing_address'  => $this->billing_address_column_mapping,
				'shipping_address' => $this->shipping_address_column_mapping,
				'operational_data' => $this->operational_data_column_mapping,
			);
		}

		return $this->all_order_column_mapping;
	}

	/**
	 * Helper function to get alias for op table, this is used in select query.
	 *
	 * @return string Alias.
	 */
	private function get_op_table_alias() : string {
		return 'order_operational_data';
	}

	/**
	 * Helper function to get alias for address table, this is used in select query.
	 *
	 * @param string $type Address type.
	 *
	 * @return string Alias.
	 */
	private function get_address_table_alias( string $type ) : string {
		return "address_$type";
	}

	/**
	 * Helper method to get a CPT data store instance to use.
	 *
	 * @return \WC_Order_Data_Store_CPT Data store instance.
	 */
	public function get_cpt_data_store_instance() {
		if ( ! isset( $this->cpt_data_store ) ) {
			$this->cpt_data_store = $this->get_post_data_store_for_backfill();
		}
		return $this->cpt_data_store;
	}


	/**
	 * Returns data store object to use backfilling.
	 *
	 * @return \Abstract_WC_Order_Data_Store_CPT
	 */
	protected function get_post_data_store_for_backfill() {
		return new \WC_Order_Data_Store_CPT();
	}

	/**
	 * Backfills order details in to WP_Post DB. Uses WC_Order_Data_store_CPT.
	 *
	 * @param \WC_Abstract_Order $order Order object to backfill.
	 */
	public function backfill_post_record( $order ) {
		$cpt_data_store = $this->get_post_data_store_for_backfill();
		if ( is_null( $cpt_data_store ) || ! method_exists( $cpt_data_store, 'update_order_from_object' ) ) {
			return;
		}

		$cpt_data_store->update_order_from_object( $order );
		foreach ( $cpt_data_store->get_internal_data_store_key_getters() as $key => $getter_name ) {
			if (
				is_callable( array( $cpt_data_store, "set_$getter_name" ) ) &&
				is_callable( array( $this, "get_$getter_name" ) )
			) {
				call_user_func_array(
					array(
						$cpt_data_store,
						"set_$getter_name",
					),
					array(
						$order,
						$this->{"get_$getter_name"}( $order ),
					)
				);
			}
		}
	}

	/**
	 * Get information about whether permissions are granted yet.
	 *
	 * @param \WC_Order $order Order object.
	 *
	 * @return bool Whether permissions are granted.
	 */
	public function get_download_permissions_granted( $order ) {
		$order_id = is_int( $order ) ? $order : $order->get_id();
		$order    = wc_get_order( $order_id );
		return $order->get_download_permissions_granted();
	}

	/**
	 * Stores information about whether permissions were generated yet.
	 *
	 * @param \WC_Order $order Order ID or order object.
	 * @param bool      $set True or false.
	 */
	public function set_download_permissions_granted( $order, $set ) {
		if ( is_int( $order ) ) {
			$order = wc_get_order( $order );
		}
		$order->set_download_permissions_granted( $set );
		$order->save();
	}

	/**
	 * Gets information about whether sales were recorded.
	 *
	 * @param \WC_Order $order Order object.
	 *
	 * @return bool Whether sales are recorded.
	 */
	public function get_recorded_sales( $order ) {
		$order_id = is_int( $order ) ? $order : $order->get_id();
		$order    = wc_get_order( $order_id );
		return $order->get_recorded_sales();
	}

	/**
	 * Stores information about whether sales were recorded.
	 *
	 * @param \WC_Order $order Order object.
	 * @param bool      $set True or false.
	 */
	public function set_recorded_sales( $order, $set ) {
		if ( is_int( $order ) ) {
			$order = wc_get_order( $order );
		}
		$order->set_recorded_sales( $set );
		$order->save();
	}

	/**
	 * Gets information about whether coupon counts were updated.
	 *
	 * @param \WC_Order $order Order object.
	 *
	 * @return bool Whether coupon counts were updated.
	 */
	public function get_recorded_coupon_usage_counts( $order ) {
		$order_id = is_int( $order ) ? $order : $order->get_id();
		$order    = wc_get_order( $order_id );
		return $order->get_recorded_coupon_usage_counts();
	}

	/**
	 * Stores information about whether coupon counts were updated.
	 *
	 * @param \WC_Order $order Order object.
	 * @param bool      $set True or false.
	 */
	public function set_recorded_coupon_usage_counts( $order, $set ) {
		if ( is_int( $order ) ) {
			$order = wc_get_order( $order );
		}
		$order->set_recorded_coupon_usage_counts( $set );
		$order->save();
	}

	/**
	 * Whether email have been sent for this order.
	 *
	 * @param \WC_Order|int $order Order object.
	 *
	 * @return bool Whether email is sent.
	 */
	public function get_email_sent( $order ) {
		$order_id = is_int( $order ) ? $order : $order->get_id();
		$order    = wc_get_order( $order_id );
		return $order->get_new_order_email_sent();
	}

	/**
	 * Stores information about whether email was sent.
	 *
	 * @param \WC_Order $order Order object.
	 * @param bool      $set True or false.
	 */
	public function set_email_sent( $order, $set ) {
		if ( is_int( $order ) ) {
			$order = wc_get_order( $order );
		}
		$order->set_new_order_email_sent( $set );
		$order->save();
	}

	/**
	 * Helper setter for email_sent.
	 *
	 * @param \WC_Order $order Order object.
	 *
	 * @return bool Whether email was sent.
	 */
	public function get_new_order_email_sent( $order ) {
		return $this->get_email_sent( $order );
	}

	/**
	 * Helper setter for new order email sent.
	 *
	 * @param \WC_Order $order Order object.
	 * @param bool      $set True or false.
	 */
	public function set_new_order_email_sent( $order, $set ) {
		if ( is_int( $order ) ) {
			$order = wc_get_order( $order );
		}
		$order->set_new_order_email_sent( $set );
		$order->save();
	}

	/**
	 * Gets information about whether stock was reduced.
	 *
	 * @param \WC_Order $order Order object.
	 *
	 * @return bool Whether stock was reduced.
	 */
	public function get_stock_reduced( $order ) {
		$order_id = is_int( $order ) ? $order : $order->get_id();
		$order    = wc_get_order( $order_id );
		return $order->get_order_stock_reduced();
	}

	/**
	 * Stores information about whether stock was reduced.
	 *
	 * @param \WC_Order $order Order ID or order object.
	 * @param bool      $set True or false.
	 */
	public function set_stock_reduced( $order, $set ) {
		if ( is_int( $order ) ) {
			$order = wc_get_order( $order );
		}
		$order->set_order_stock_reduced( $set );
		$order->save();
	}

	/**
	 * Helper getter for `order_stock_reduced`.
	 *
	 * @param \WC_Order $order Order object.
	 * @return bool Whether stock was reduced.
	 */
	public function get_order_stock_reduced( $order ) {
		return $this->get_stock_reduced( $order );
	}

	/**
	 * Helper setter for `order_stock_reduced`.
	 *
	 * @param \WC_Order $order Order ID or order object.
	 * @param bool      $set Whether stock was reduced.
	 */
	public function set_order_stock_reduced( $order, $set ) {
		$this->set_stock_reduced( $order, $set );
	}

	/**
	 * Get amount already refunded.
	 *
	 * @param \WC_Order $order Order object.
	 *
	 * @return float Refunded amount.
	 */
	public function get_total_refunded( $order ) {
		global $wpdb;
		$order_table = self::get_orders_table_name();
		$total       = $wpdb->get_var(
			$wpdb->prepare(
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $order_table is hardcoded.
				"
SELECT SUM( total_amount ) FROM $order_table
WHERE
    type = %s AND
    parent_order_id = %d
;
",
				// phpcs:enable
				'shop_order_refund',
				$order->get_id()
			)
		);
		return -1 * ( isset( $total ) ? $total : 0 );
	}

	/**
	 * Get the total tax refunded.
	 *
	 * @param  WC_Order $order Order object.
	 * @return float
	 */
	public function get_total_tax_refunded( $order ) {
		global $wpdb;

		$order_table = self::get_orders_table_name();

		$total = $wpdb->get_var(
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $order_table is hardcoded.
			$wpdb->prepare(
				"SELECT SUM( order_itemmeta.meta_value )
				FROM {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
				INNER JOIN $order_table AS orders ON ( orders.type = 'shop_order_refund' AND orders.parent_order_id = %d )
				INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON ( order_items.order_id = orders.id AND order_items.order_item_type = 'tax' )
				WHERE order_itemmeta.order_item_id = order_items.order_item_id
				AND order_itemmeta.meta_key IN ('tax_amount', 'shipping_tax_amount')",
				$order->get_id()
			)
		) ?? 0;
		// phpcs:enable

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

		$order_table = self::get_orders_table_name();

		$total = $wpdb->get_var(
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $order_table is hardcoded.
			$wpdb->prepare(
				"SELECT SUM( order_itemmeta.meta_value )
				FROM {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
				INNER JOIN $order_table AS orders ON ( orders.type = 'shop_order_refund' AND orders.parent_order_id = %d )
				INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON ( order_items.order_id = orders.id AND order_items.order_item_type = 'shipping' )
				WHERE order_itemmeta.order_item_id = order_items.order_item_id
				AND order_itemmeta.meta_key IN ('cost')",
				$order->get_id()
			)
		) ?? 0;
		// phpcs:enable

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

		$orders_table = self::get_orders_table_name();
		$op_table     = self::get_operational_data_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT {$orders_table}.id FROM {$orders_table}
				INNER JOIN {$op_table} ON {$op_table}.order_id = {$orders_table}.id
				WHERE {$op_table}.order_key = %s AND {$op_table}.order_key != ''",
				$order_key
			)
		);
		// phpcs:enable
	}

	/**
	 * Return count of orders with a specific status.
	 *
	 * @param  string $status Order status. Function wc_get_order_statuses() returns a list of valid statuses.
	 * @return int
	 */
	public function get_order_count( $status ) {
		global $wpdb;

		$orders_table = self::get_orders_table_name();

		return absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$orders_table} WHERE type = %s AND status = %s", 'shop_order', $status ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Get all orders matching the passed in args.
	 *
	 * @deprecated 3.1.0 - Use {@see wc_get_orders} instead.
	 * @param  array $args List of args passed to wc_get_orders().
	 * @return array|object
	 */
	public function get_orders( $args = array() ) {
		wc_deprecated_function( __METHOD__, '3.1.0', 'Use wc_get_orders instead.' );
		return wc_get_orders( $args );
	}

	/**
	 * Get unpaid orders last updated before the specified date.
	 *
	 * @param  int $date Timestamp.
	 * @return array
	 */
	public function get_unpaid_orders( $date ) {
		global $wpdb;

		$orders_table    = self::get_orders_table_name();
		$order_types_sql = "('" . implode( "','", wc_get_order_types() ) . "')";

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT id FROM {$orders_table} WHERE
				{$orders_table}.type IN {$order_types_sql}
				AND {$orders_table}.status = %s
				AND {$orders_table}.date_updated_gmt < %s",
				'wc-pending',
				gmdate( 'Y-m-d H:i:s', absint( $date ) )
			)
		);
		// phpcs:enable
	}

	/**
	 * Search order data for a term and return matching order IDs.
	 *
	 * @param string $term Search term.
	 *
	 * @return int[] Array of order IDs.
	 */
	public function search_orders( $term ) {
		$order_ids = wc_get_orders(
			array(
				's'      => $term,
				'return' => 'ids',
			)
		);

		/**
		 * Provides an opportunity to modify the list of order IDs obtained during an order search.
		 *
		 * This hook is used for Custom Order Table queries. For Custom Post Type order searches, the corresponding hook
		 * is `woocommerce_shop_order_search_results`.
		 *
		 * @since 7.0.0
		 *
		 * @param int[]  $order_ids Search results as an array of order IDs.
		 * @param string $term      The search term.
		 */
		return array_map( 'intval', (array) apply_filters( 'woocommerce_cot_shop_order_search_results', $order_ids, $term ) );
	}

	/**
	 * Fetch order type for orders in bulk.
	 *
	 * @param array $order_ids Order IDs.
	 *
	 * @return array array( $order_id1 => $type1, ... ) Array for all orders.
	 */
	public function get_orders_type( $order_ids ) {
		global $wpdb;

		if ( empty( $order_ids ) ) {
			return array();
		}

		$orders_table          = self::get_orders_table_name();
		$order_ids_placeholder = implode( ', ', array_fill( 0, count( $order_ids ), '%d' ) );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, type FROM {$orders_table} WHERE id IN ( $order_ids_placeholder )",
				$order_ids
			)
		);
		// phpcs:enable
		$order_types = array();
		foreach ( $results as $row ) {
			$order_types[ $row->id ] = $row->type;
		}
		return $order_types;
	}

	/**
	 * Get order type from DB.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return string Order type.
	 */
	public function get_order_type( $order_id ) {
		$type = $this->get_orders_type( array( $order_id ) );
		return $type[ $order_id ] ?? '';
	}

	/**
	 * Method to read an order from custom tables.
	 *
	 * @param \WC_Order $order Order object.
	 *
	 * @throws \Exception If passed order is invalid.
	 */
	public function read( &$order ) {
		$orders_array = array( $order->get_id() => $order );
		$this->read_multiple( $orders_array );
	}

	/**
	 * Reads multiple orders from custom tables in one pass.
	 *
	 * @since 6.9.0
	 * @param array[\WC_Order] $orders Order objects.
	 * @throws \Exception If passed an invalid order.
	 */
	public function read_multiple( &$orders ) {
		$order_ids = array_keys( $orders );
		$data      = $this->get_order_data_for_ids( $order_ids );

		if ( count( $data ) !== count( $order_ids ) ) {
			throw new \Exception( __( 'Invalid order IDs in call to read_multiple()', 'woocommerce' ) );
		}

		$data_synchronizer = wc_get_container()->get( DataSynchronizer::class );
		if ( ! $data_synchronizer instanceof DataSynchronizer ) {
			return;
		}

		$data_sync_enabled = $data_synchronizer->data_sync_is_enabled() && 0 === $data_synchronizer->get_current_orders_pending_sync_count_cached();
		$load_posts_for    = array_diff( $order_ids, self::$reading_order_ids );
		$post_orders       = $data_sync_enabled ? $this->get_post_orders_for_ids( array_intersect_key( $orders, array_flip( $load_posts_for ) ) ) : array();

		foreach ( $data as $order_data ) {
			$order_id = absint( $order_data->id );
			$order    = $orders[ $order_id ];

			$this->init_order_record( $order, $order_id, $order_data );

			if ( $data_sync_enabled && $this->should_sync_order( $order ) && isset( $post_orders[ $order_id ] ) ) {
				self::$reading_order_ids[] = $order_id;
				$this->maybe_sync_order( $order, $post_orders[ $order->get_id() ] );
			}
		}
	}

	/**
	 * Helper method to check whether to sync the order.
	 *
	 * @param \WC_Abstract_Order $order Order object.
	 *
	 * @return bool Whether the order should be synced.
	 */
	private function should_sync_order( \WC_Abstract_Order $order ) : bool {
		$draft_order    = in_array( $order->get_status(), array( 'draft', 'auto-draft' ), true );
		$already_synced = in_array( $order->get_id(), self::$reading_order_ids, true );
		return ! $draft_order && ! $already_synced;
	}

	/**
	 * Helper method to initialize order object from DB data.
	 *
	 * @param \WC_Abstract_Order $order Order object.
	 * @param int                $order_id Order ID.
	 * @param \stdClass          $order_data Order data fetched from DB.
	 *
	 * @return void
	 */
	protected function init_order_record( \WC_Abstract_Order &$order, int $order_id, \stdClass $order_data ) {
		$order->set_defaults();
		$order->set_id( $order_id );
		$filtered_meta_data = $this->filter_raw_meta_data( $order, $order_data->meta_data );
		$order->init_meta_data( $filtered_meta_data );
		$this->set_order_props_from_data( $order, $order_data );
		$order->set_object_read( true );
	}

	/**
	 * For post based data stores, this was used to filter internal meta data. For custom tables, technically there is no internal meta data,
	 * (i.e. we store all core data as properties for the order, and not in meta data). So this method is a no-op.
	 *
	 * Except that some meta such as billing_address_index and shipping_address_index are infact stored in meta data, so we need to filter those out.
	 *
	 * However, declaring $internal_meta_keys is still required so that our backfill and other comparison checks works as expected.
	 *
	 * @param \WC_Data $object Object to filter meta data for.
	 * @param array    $raw_meta_data Raw meta data.
	 *
	 * @return array Filtered meta data.
	 */
	public function filter_raw_meta_data( &$object, $raw_meta_data ) {
		$filtered_meta_data = parent::filter_raw_meta_data( $object, $raw_meta_data );
		$allowed_keys       = array(
			'_billing_address_index',
			'_shipping_address_index',
		);
		$allowed_meta       = array_filter(
			$raw_meta_data,
			function( $meta ) use ( $allowed_keys ) {
				return in_array( $meta->meta_key, $allowed_keys, true );
			}
		);

		return array_merge( $allowed_meta, $filtered_meta_data );
	}

	/**
	 * Sync order to/from posts tables if we are able to detect difference between order and posts but the sync is enabled.
	 *
	 * @param \WC_Abstract_Order $order Order object.
	 * @param \WC_Abstract_Order $post_order Order object initialized from post.
	 *
	 * @return void
	 * @throws \Exception If passed an invalid order.
	 */
	private function maybe_sync_order( \WC_Abstract_Order &$order, \WC_Abstract_Order $post_order ) {
		if ( ! $this->is_post_different_from_order( $order, $post_order ) ) {
			return;
		}

		// Modified dates can be empty when the order is created but never updated again. Fallback to created date in those cases.
		$order_modified_date      = $order->get_date_modified() ?? $order->get_date_created();
		$order_modified_date      = is_null( $order_modified_date ) ? 0 : $order_modified_date->getTimestamp();
		$post_order_modified_date = $post_order->get_date_modified() ?? $post_order->get_date_created();
		$post_order_modified_date = is_null( $post_order_modified_date ) ? 0 : $post_order_modified_date->getTimestamp();

		/**
		 * We are here because there was difference in posts and order data, although the sync is enabled.
		 * When order modified date is more recent than post modified date, it can only mean that COT definitely has more updated version of the order.
		 *
		 * In a case where post meta was updated (without updating post_modified date), post_modified would be equal to order_modified date.
		 *
		 * So we write back to the order table when order modified date is more recent than post modified date. Otherwise, we write to the post table.
		 */
		if ( $post_order_modified_date >= $order_modified_date ) {
			$this->migrate_post_record( $order, $post_order );
		}
	}

	/**
	 * Get the post type order representation.
	 *
	 * @param \WP_Post $post Post object.
	 *
	 * @return \WC_Order Order object.
	 */
	private function get_cpt_order( $post ) {
		$cpt_order = new \WC_Order();
		$cpt_order->set_id( $post->ID );
		$cpt_data_store = $this->get_cpt_data_store_instance();
		$cpt_data_store->read( $cpt_order );
		return $cpt_order;
	}

	/**
	 * Helper function to get posts data for an order in bullk. We use to this to compute posts object in bulk so that we can compare it with COT data.
	 *
	 * @param array $orders    List of orders mapped by $order_id.
	 *
	 * @return array List of posts.
	 */
	private function get_post_orders_for_ids( array $orders ): array {
		$order_ids = array_keys( $orders );
		// We have to bust meta cache, otherwise we will just get the meta cached by OrderTableDataStore.
		foreach ( $order_ids as $order_id ) {
			wp_cache_delete( WC_Order::generate_meta_cache_key( $order_id, 'orders' ), 'orders' );
		}

		$cpt_stores       = array();
		$cpt_store_orders = array();
		foreach ( $orders as $order_id => $order ) {
			$table_data_store     = $order->get_data_store();
			$cpt_data_store       = $table_data_store->get_cpt_data_store_instance();
			$cpt_store_class_name = get_class( $cpt_data_store );
			if ( ! isset( $cpt_stores[ $cpt_store_class_name ] ) ) {
				$cpt_stores[ $cpt_store_class_name ]       = $cpt_data_store;
				$cpt_store_orders[ $cpt_store_class_name ] = array();
			}
			$cpt_store_orders[ $cpt_store_class_name ][ $order_id ] = $order;
		}

		$cpt_orders = array();
		foreach ( $cpt_stores as $cpt_store_name => $cpt_store ) {
			// Prime caches if we can.
			if ( method_exists( $cpt_store, 'prime_caches_for_orders' ) ) {
				$cpt_store->prime_caches_for_orders( array_keys( $cpt_store_orders[ $cpt_store_name ] ), array() );
			}

			foreach ( $cpt_store_orders[ $cpt_store_name ] as $order_id => $order ) {
				$cpt_order_class_name = wc_get_order_type( $order->get_type() )['class_name'];
				$cpt_order            = new $cpt_order_class_name();

				try {
					$cpt_order->set_id( $order_id );
					$cpt_store->read( $cpt_order );
					$cpt_orders[ $order_id ] = $cpt_order;
				} catch ( Exception $e ) {
					// If the post record has been deleted (for instance, by direct query) then an exception may be thrown.
					$this->error_logger->warning(
						sprintf(
							/* translators: %1$d order ID. */
							__( 'Unable to load the post record for order %1$d', 'woocommerce' ),
							$order_id
						),
						array(
							'exception_code' => $e->getCode(),
							'exception_msg'  => $e->getMessage(),
							'origin'         => __METHOD__,
						)
					);
				}
			}
		}
		return $cpt_orders;
	}

	/**
	 * Computes whether post has been updated after last order. Tries to do it as efficiently as possible.
	 *
	 * @param \WC_Abstract_Order $order Order object.
	 * @param \WC_Abstract_Order $post_order Order object read from posts table.
	 *
	 * @return bool True if post is different than order.
	 */
	private function is_post_different_from_order( $order, $post_order ): bool {
		if ( ArrayUtil::deep_compare_array_diff( $order->get_base_data(), $post_order->get_base_data(), false ) ) {
			return true;
		}

		$meta_diff = $this->get_diff_meta_data_between_orders( $order, $post_order );
		if ( ! empty( $meta_diff ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Migrate meta data from post to order.
	 *
	 * @param \WC_Abstract_Order $order Order object.
	 * @param \WC_Abstract_Order $post_order Order object read from posts table.
	 *
	 * @return array List of meta data that was migrated.
	 */
	private function migrate_meta_data_from_post_order( \WC_Abstract_Order &$order, \WC_Abstract_Order $post_order ) {
		$diff = $this->get_diff_meta_data_between_orders( $order, $post_order, true );
		$order->save_meta_data();
		return $diff;
	}

	/**
	 * Helper function to compute diff between metadata of post and cot data for an order.
	 *
	 * Also provides an option to sync the metadata as well, since we are already computing the diff.
	 *
	 * @param \WC_Abstract_Order $order1 Order object read from posts.
	 * @param \WC_Abstract_Order $order2 Order object read from COT.
	 * @param bool               $sync   Whether to also sync the meta data.
	 *
	 * @return array Difference between post and COT meta data.
	 */
	private function get_diff_meta_data_between_orders( \WC_Abstract_Order &$order1, \WC_Abstract_Order $order2, $sync = false ): array {
		$order1_meta        = ArrayUtil::select( $order1->get_meta_data(), 'get_data', ArrayUtil::SELECT_BY_OBJECT_METHOD );
		$order2_meta        = ArrayUtil::select( $order2->get_meta_data(), 'get_data', ArrayUtil::SELECT_BY_OBJECT_METHOD );
		$order1_meta_by_key = ArrayUtil::select_as_assoc( $order1_meta, 'key', ArrayUtil::SELECT_BY_ARRAY_KEY );
		$order2_meta_by_key = ArrayUtil::select_as_assoc( $order2_meta, 'key', ArrayUtil::SELECT_BY_ARRAY_KEY );

		$diff = array();
		foreach ( $order1_meta_by_key as $key => $value ) {
			if ( in_array( $key, $this->internal_meta_keys, true ) ) {
				// These should have already been verified in the base data comparison.
				continue;
			}
			$order1_values = ArrayUtil::select( $value, 'value', ArrayUtil::SELECT_BY_ARRAY_KEY );
			if ( ! array_key_exists( $key, $order2_meta_by_key ) ) {
				$sync && $order1->delete_meta_data( $key );
				$diff[ $key ] = $order1_values;
				unset( $order2_meta_by_key[ $key ] );
				continue;
			}

			$order2_values = ArrayUtil::select( $order2_meta_by_key[ $key ], 'value', ArrayUtil::SELECT_BY_ARRAY_KEY );
			$new_diff      = ArrayUtil::deep_assoc_array_diff( $order1_values, $order2_values );
			if ( ! empty( $new_diff ) && $sync ) {
				if ( count( $order2_values ) > 1 ) {
					$sync && $order1->delete_meta_data( $key );
					foreach ( $order2_values as $post_order_value ) {
						$sync && $order1->add_meta_data( $key, $post_order_value, false );
					}
				} else {
					$sync && $order1->update_meta_data( $key, $order2_values[0] );
				}
				$diff[ $key ] = $new_diff;
				unset( $order2_meta_by_key[ $key ] );
			}
		}

		foreach ( $order2_meta_by_key as $key => $value ) {
			if ( array_key_exists( $key, $order1_meta_by_key ) || in_array( $key, $this->internal_meta_keys, true ) ) {
				continue;
			}
			$order2_values = ArrayUtil::select( $value, 'value', ArrayUtil::SELECT_BY_ARRAY_KEY );
			foreach ( $order2_values as $meta_value ) {
				$sync && $order1->add_meta_data( $key, $meta_value );
			}
			$diff[ $key ] = $order2_values;
		}
		return $diff;
	}

	/**
	 * Log difference between post and COT data for an order.
	 *
	 * @param array $diff Difference between post and COT data.
	 *
	 * @return void
	 */
	private function log_diff( array $diff ): void {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- This is a log function.
		$this->error_logger->notice( 'Diff found: ' . print_r( $diff, true ) );
	}

	/**
	 * Migrate post record from a given order object.
	 *
	 * @param \WC_Abstract_Order $order Order object.
	 * @param \WC_Abstract_Order $post_order Order object read from posts.
	 *
	 * @return void
	 */
	private function migrate_post_record( \WC_Abstract_Order &$order, \WC_Abstract_Order $post_order ): void {
		$this->migrate_meta_data_from_post_order( $order, $post_order );
		$post_order_base_data = $post_order->get_base_data();
		foreach ( $post_order_base_data as $key => $value ) {
			$this->set_order_prop( $order, $key, $value );
		}
		$this->persist_updates( $order, false );
	}

	/**
	 * Sets order properties based on a row from the database.
	 *
	 * @param \WC_Abstract_Order $order      The order object.
	 * @param object             $order_data A row of order data from the database.
	 */
	private function set_order_props_from_data( &$order, $order_data ) {
		foreach ( $this->get_all_order_column_mappings() as $table_name => $column_mapping ) {
			foreach ( $column_mapping as $column_name => $prop_details ) {
				if ( ! isset( $prop_details['name'] ) ) {
					continue;
				}
				$prop_value = $order_data->{$prop_details['name']};
				if ( is_null( $prop_value ) ) {
					continue;
				}

				if ( 'date' === $prop_details['type'] ) {
					$prop_value = $this->string_to_timestamp( $prop_value );
				}

				$this->set_order_prop( $order, $prop_details['name'], $prop_value );
			}
		}
	}

	/**
	 * Set order prop if a setter exists in either the order object or in the data store.
	 *
	 * @param \WC_Abstract_Order $order Order object.
	 * @param string             $prop_name Property name.
	 * @param mixed              $prop_value Property value.
	 *
	 * @return bool True if the property was set, false otherwise.
	 */
	private function set_order_prop( \WC_Abstract_Order $order, string $prop_name, $prop_value ) {
		$prop_setter_function_name = "set_{$prop_name}";
		if ( is_callable( array( $order, $prop_setter_function_name ) ) ) {
			return $order->{$prop_setter_function_name}( $prop_value );
		} elseif ( is_callable( array( $this, $prop_setter_function_name ) ) ) {
			return $this->{$prop_setter_function_name}( $order, $prop_value, false );
		}
		return false;
	}

	/**
	 * Return order data for a single order ID.
	 *
	 * @param int $id Order ID.
	 *
	 * @return object|\WP_Error DB order object or WP_Error.
	 */
	private function get_order_data_for_id( $id ) {
		$results = $this->get_order_data_for_ids( array( $id ) );

		return is_array( $results ) && count( $results ) > 0 ? $results[ $id ] : $results;
	}

	/**
	 * Return order data for multiple IDs.
	 *
	 * @param array $ids List of order IDs.
	 *
	 * @return \stdClass[]|object|null DB Order objects or error.
	 */
	protected function get_order_data_for_ids( $ids ) {
		if ( ! $ids ) {
			return array();
		}

		global $wpdb;
		if ( empty( $ids ) ) {
			return array();
		}
		$order_table_query = $this->get_order_table_select_statement();
		$id_placeholder    = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
		$order_meta_table  = self::get_meta_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $order_table_query is autogenerated and should already be prepared.
		$table_data = $wpdb->get_results(
			$wpdb->prepare(
				"$order_table_query WHERE wc_order.id in ( $id_placeholder )",
				$ids
			)
		);
		// phpcs:enable

		$meta_data_query = $this->get_order_meta_select_statement();
		$order_data      = array();
		$meta_data       = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- $meta_data_query and $order_meta_table is autogenerated and should already be prepared. $id_placeholder is already prepared.
				"$meta_data_query WHERE $order_meta_table.order_id in ( $id_placeholder )",
				$ids
			)
		);
		foreach ( $table_data as $table_datum ) {
			$order_data[ $table_datum->id ]            = $table_datum;
			$order_data[ $table_datum->id ]->meta_data = array();
		}

		foreach ( $meta_data as $meta_datum ) {
			// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Not a meta query.
			$order_data[ $meta_datum->order_id ]->meta_data[] = (object) array(
				'meta_id'    => $meta_datum->id,
				'meta_key'   => $meta_datum->meta_key,
				'meta_value' => $meta_datum->meta_value,
			);
			// phpcs:enable
		}
		return $order_data;
	}

	/**
	 * Helper method to generate combined select statement.
	 *
	 * @return string Select SQL statement to fetch order.
	 */
	private function get_order_table_select_statement() {
		$order_table                  = $this::get_orders_table_name();
		$order_table_alias            = 'wc_order';
		$select_clause                = $this->generate_select_clause_for_props( $order_table_alias, $this->order_column_mapping );
		$billing_address_table_alias  = $this->get_address_table_alias( 'billing' );
		$shipping_address_table_alias = $this->get_address_table_alias( 'shipping' );
		$op_data_table_alias          = $this->get_op_table_alias();
		$billing_address_clauses      = $this->join_billing_address_table_to_order_query( $order_table_alias, $billing_address_table_alias );
		$shipping_address_clauses     = $this->join_shipping_address_table_to_order_query( $order_table_alias, $shipping_address_table_alias );
		$operational_data_clauses     = $this->join_operational_data_table_to_order_query( $order_table_alias, $op_data_table_alias );

		return "
SELECT $select_clause, {$billing_address_clauses['select']}, {$shipping_address_clauses['select']}, {$operational_data_clauses['select']}
FROM $order_table $order_table_alias
LEFT JOIN {$billing_address_clauses['join']}
LEFT JOIN {$shipping_address_clauses['join']}
LEFT JOIN {$operational_data_clauses['join']}
";
	}

	/**
	 * Helper function to generate select statement for fetching metadata in bulk.
	 *
	 * @return string Select SQL statement to fetch order metadata.
	 */
	private function get_order_meta_select_statement() {
		$order_meta_table = self::get_meta_table_name();
		return "
SELECT $order_meta_table.id, $order_meta_table.order_id, $order_meta_table.meta_key, $order_meta_table.meta_value
FROM $order_meta_table
		";
	}

	/**
	 * Helper method to generate join query for billing addresses in wc_address table.
	 *
	 * @param string $order_table_alias Alias for order table to use in join.
	 * @param string $address_table_alias Alias for address table to use in join.
	 *
	 * @return array Select and join statements for billing address table.
	 */
	private function join_billing_address_table_to_order_query( $order_table_alias, $address_table_alias ) {
		return $this->join_address_table_order_query( 'billing', $order_table_alias, $address_table_alias );
	}

	/**
	 * Helper method to generate join query for shipping addresses in wc_address table.
	 *
	 * @param string $order_table_alias Alias for order table to use in join.
	 * @param string $address_table_alias Alias for address table to use in join.
	 *
	 * @return array Select and join statements for shipping address table.
	 */
	private function join_shipping_address_table_to_order_query( $order_table_alias, $address_table_alias ) {
		return $this->join_address_table_order_query( 'shipping', $order_table_alias, $address_table_alias );
	}

	/**
	 * Helper method to generate join and select query for address table.
	 *
	 * @param string $address_type Type of address. Typically will be `billing` or `shipping`.
	 * @param string $order_table_alias Alias of order table to use.
	 * @param string $address_table_alias Alias for address table to use.
	 *
	 * @return array Select and join statements for address table.
	 */
	private function join_address_table_order_query( $address_type, $order_table_alias, $address_table_alias ) {
		global $wpdb;
		$address_table    = $this::get_addresses_table_name();
		$column_props_map = 'billing' === $address_type ? $this->billing_address_column_mapping : $this->shipping_address_column_mapping;
		$clauses          = $this->generate_select_and_join_clauses( $order_table_alias, $address_table, $address_table_alias, $column_props_map );
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $clauses['join'] and $address_table_alias are hardcoded.
		$clauses['join'] = $wpdb->prepare(
			"{$clauses['join']} AND $address_table_alias.address_type = %s",
			$address_type
		);

		// phpcs:enable
		return array(
			'select' => $clauses['select'],
			'join'   => $clauses['join'],
		);
	}

	/**
	 * Helper method to join order operational data table.
	 *
	 * @param string $order_table_alias Alias to use for order table.
	 * @param string $operational_table_alias Alias to use for operational data table.
	 *
	 * @return array Select and join queries for operational data table.
	 */
	private function join_operational_data_table_to_order_query( $order_table_alias, $operational_table_alias ) {
		$operational_data_table = $this::get_operational_data_table_name();

		return $this->generate_select_and_join_clauses(
			$order_table_alias,
			$operational_data_table,
			$operational_table_alias,
			$this->operational_data_column_mapping
		);
	}

	/**
	 * Helper method to generate join and select clauses.
	 *
	 * @param string  $order_table_alias Alias for order table.
	 * @param string  $table Table to join.
	 * @param string  $table_alias Alias for table to join.
	 * @param array[] $column_props_map Column to prop map for table to join.
	 *
	 * @return array Select and join queries.
	 */
	private function generate_select_and_join_clauses( $order_table_alias, $table, $table_alias, $column_props_map ) {
		// Add aliases to column names so they will be unique when fetching.
		$select_clause = $this->generate_select_clause_for_props( $table_alias, $column_props_map );
		$join_clause   = "$table $table_alias ON $table_alias.order_id = $order_table_alias.id";

		return array(
			'select' => $select_clause,
			'join'   => $join_clause,
		);
	}

	/**
	 * Helper method to generate select clause for props.
	 *
	 * @param string  $table_alias Alias for table.
	 * @param array[] $props Props to column mapping for table.
	 *
	 * @return string Select clause.
	 */
	private function generate_select_clause_for_props( $table_alias, $props ) {
		$select_clauses = array();
		foreach ( $props as $column_name => $prop_details ) {
			$select_clauses[] = isset( $prop_details['name'] ) ? "$table_alias.$column_name as {$prop_details['name']}" : "$table_alias.$column_name as {$table_alias}_$column_name";
		}

		return implode( ', ', $select_clauses );
	}

	/**
	 * Persists order changes to the database.
	 *
	 * @param \WC_Abstract_Order $order            The order.
	 * @param bool               $force_all_fields Force saving all fields to DB and just changed.
	 *
	 * @throws \Exception If order data is not valid.
	 *
	 * @since 6.8.0
	 */
	protected function persist_order_to_db( &$order, bool $force_all_fields = false ) {
		$context   = ( 0 === absint( $order->get_id() ) ) ? 'create' : 'update';
		$data_sync = wc_get_container()->get( DataSynchronizer::class );

		if ( 'create' === $context ) {
			$post_id = wp_insert_post(
				array(
					'post_type'   => $data_sync->data_sync_is_enabled() ? $order->get_type() : $data_sync::PLACEHOLDER_ORDER_POST_TYPE,
					'post_status' => 'draft',
				)
			);

			if ( ! $post_id ) {
				throw new \Exception( __( 'Could not create order in posts table.', 'woocommerce' ) );
			}

			$order->set_id( $post_id );
		}

		$only_changes = ! $force_all_fields && 'update' === $context;
		// Figure out what needs to be updated in the database.
		$db_updates = $this->get_db_rows_for_order( $order, $context, $only_changes );

		// Persist changes.
		foreach ( $db_updates as $update ) {
			// Make sure 'data' and 'format' entries match before passing to $wpdb.
			ksort( $update['data'] );
			ksort( $update['format'] );

			$result = $this->database_util->insert_on_duplicate_key_update(
				$update['table'],
				$update['data'],
				array_values( $update['format'] )
			);

			if ( false === $result ) {
				// translators: %s is a table name.
				throw new \Exception( sprintf( __( 'Could not persist order to database table "%s".', 'woocommerce' ), $update['table'] ) );
			}
		}

		$changes = $order->get_changes();
		$this->update_address_index_meta( $order, $changes );
	}

	/**
	 * Generates an array of rows with all the details required to insert or update an order in the database.
	 *
	 * @param \WC_Abstract_Order $order The order.
	 * @param string             $context The context: 'create' or 'update'.
	 * @param boolean            $only_changes Whether to consider only changes in the order for generating the rows.
	 *
	 * @return array
	 * @throws \Exception When invalid data is found for the given context.
	 *
	 * @since 6.8.0
	 */
	protected function get_db_rows_for_order( \WC_Abstract_Order $order, string $context = 'create', bool $only_changes = false ): array {
		$result = array();

		$row = $this->get_db_row_from_order( $order, $this->order_column_mapping, $only_changes );
		if ( 'create' === $context && ! $row ) {
			throw new \Exception( 'No data for new record.' ); // This shouldn't occur.
		}

		if ( $row ) {
			$result[] = array(
				'table'  => self::get_orders_table_name(),
				'data'   => array_merge( $row['data'], array( 'id' => $order->get_id() ) ),
				'format' => array_merge( $row['format'], array( 'id' => '%d' ) ),
			);
		}

		// wc_order_operational_data.
		$row = $this->get_db_row_from_order( $order, $this->operational_data_column_mapping, $only_changes );
		if ( $row ) {
			$result[] = array(
				'table'  => self::get_operational_data_table_name(),
				'data'   => array_merge( $row['data'], array( 'order_id' => $order->get_id() ) ),
				'format' => array_merge( $row['format'], array( 'order_id' => '%d' ) ),
			);
		}

		// wc_order_addresses.
		foreach ( array( 'billing', 'shipping' ) as $address_type ) {
			$row = $this->get_db_row_from_order( $order, $this->{$address_type . '_address_column_mapping'}, $only_changes );

			if ( $row ) {
				$result[] = array(
					'table'  => self::get_addresses_table_name(),
					'data'   => array_merge(
						$row['data'],
						array(
							'order_id'     => $order->get_id(),
							'address_type' => $address_type,
						)
					),
					'format' => array_merge(
						$row['format'],
						array(
							'order_id'     => '%d',
							'address_type' => '%s',
						)
					),
				);
			}
		}

		/**
		 * Allow third parties to include rows that need to be inserted/updated in custom tables when persisting an order.
		 *
		 * @since 6.8.0
		 *
		 * @param array      Array of rows to be inserted/updated when persisting an order. Each entry should be an array with
		 *                   keys 'table', 'data' (the row), 'format' (row format), 'where' and 'where_format'.
		 * @param \WC_Order  The order object.
		 * @param string     The context of the operation: 'create' or 'update'.
		 */
		$ext_rows = apply_filters( 'woocommerce_orders_table_datastore_extra_db_rows_for_order', array(), $order, $context );

		return array_merge( $result, $ext_rows );
	}

	/**
	 * Produces an array with keys 'row' and 'format' that can be passed to `$wpdb->update()` as the `$data` and
	 * `$format` parameters. Values are taken from the order changes array and properly formatted for inclusion in the
	 * database.
	 *
	 * @param \WC_Abstract_Order $order          Order.
	 * @param array              $column_mapping Table column mapping.
	 * @param bool               $only_changes   Whether to consider only changes in the order object or all fields.
	 * @return array
	 *
	 * @since 6.8.0
	 */
	protected function get_db_row_from_order( $order, $column_mapping, $only_changes = false ) {
		$changes = $only_changes ? $order->get_changes() : array_merge( $order->get_data(), $order->get_changes() );

		$changes['type'] = $order->get_type();

		// Make sure 'status' is correct.
		if ( array_key_exists( 'status', $column_mapping ) ) {
			$changes['status'] = $this->get_post_status( $order );
		}

		$row        = array();
		$row_format = array();

		foreach ( $column_mapping as $column => $details ) {
			if ( ! isset( $details['name'] ) || ! array_key_exists( $details['name'], $changes ) ) {
				continue;
			}

			$row[ $column ]        = $this->database_util->format_object_value_for_db( $changes[ $details['name'] ], $details['type'] );
			$row_format[ $column ] = $this->database_util->get_wpdb_format_for_type( $details['type'] );
		}

		if ( ! $row ) {
			return false;
		}

		return array(
			'data'   => $row,
			'format' => $row_format,
		);
	}

	/**
	 * Method to delete an order from the database.
	 *
	 * @param \WC_Abstract_Order $order Order object.
	 * @param array              $args Array of args to pass to the delete method.
	 *
	 * @return void
	 */
	public function delete( &$order, $args = array() ) {
		$order_id = $order->get_id();

		if ( ! $order_id ) {
			return;
		}

		if ( ! empty( $args['force_delete'] ) ) {

			/**
			 * Fires immediately before an order is deleted from the database.
			 *
			 * @since 7.1.0
			 *
			 * @param int      $order_id ID of the order about to be deleted.
			 * @param WC_Order $order    Instance of the order that is about to be deleted.
			 */
			do_action( 'woocommerce_before_delete_order', $order_id, $order );

			$this->upshift_or_delete_child_orders( $order );
			$this->delete_order_data_from_custom_order_tables( $order_id );
			$this->delete_items( $order );

			$order->set_id( 0 );

			/** We can delete the post data if:
			 * 1. The HPOS table is authoritative and synchronization is enabled.
			 * 2. The post record is of type `shop_order_placehold`, since this is created by the HPOS in the first place.
			 *
			 * In other words, we do not delete the post record when HPOS table is authoritative and synchronization is disabled but post record is a full record and not just a placeholder, because it implies that the order was created before HPOS was enabled.
			 */
			$data_synchronizer = wc_get_container()->get( DataSynchronizer::class );
			if ( $data_synchronizer->data_sync_is_enabled() || get_post_type( $order_id ) === 'shop_order_placehold' ) {
				// Delete the associated post, which in turn deletes order items, etc. through {@see WC_Post_Data}.
				// Once we stop creating posts for orders, we should do the cleanup here instead.
				wp_delete_post( $order_id );
			}

			do_action( 'woocommerce_delete_order', $order_id ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		} else {
			/**
			 * Fires immediately before an order is trashed.
			 *
			 * @since 7.1.0
			 *
			 * @param int      $order_id ID of the order about to be deleted.
			 * @param WC_Order $order    Instance of the order that is about to be deleted.
			 */
			do_action( 'woocommerce_before_trash_order', $order_id, $order );

			$this->trash_order( $order );

			do_action( 'woocommerce_trash_order', $order_id ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		}
	}

	/**
	 * Set the parent id of child orders to the parent order's parent if the post type
	 * for the order is hierarchical, just delete the child orders otherwise.
	 *
	 * @param \WC_Abstract_Order $order Order object.
	 *
	 * @return void
	 */
	private function upshift_or_delete_child_orders( $order ) {
		global $wpdb;

		$order_table     = self::get_orders_table_name();
		$order_parent_id = $order->get_parent_id();

		if ( $this->legacy_proxy->call_function( 'is_post_type_hierarchical', $order->get_type() ) ) {
			$wpdb->update(
				$order_table,
				array( 'parent_order_id' => $order_parent_id ),
				array( 'parent_order_id' => $order->get_id() ),
				array( '%d' ),
				array( '%d' )
			);
		} else {
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$child_order_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT id FROM $order_table WHERE parent_order_id=%d",
					$order->get_id()
				)
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

			foreach ( $child_order_ids as $child_order_id ) {
				$child_order = wc_get_order( $child_order_id );
				if ( $child_order ) {
					$child_order->delete( true );
				}
			}
		}
	}

	/**
	 * Trashes an order.
	 *
	 * @param  WC_Order $order The order object.
	 *
	 * @return void
	 */
	public function trash_order( $order ) {
		global $wpdb;

		if ( 'trash' === $order->get_status( 'edit' ) ) {
			return;
		}

		$trash_metadata = array(
			'_wp_trash_meta_status' => 'wc-' . $order->get_status( 'edit' ),
			'_wp_trash_meta_time'   => time(),
		);

		foreach ( $trash_metadata as $meta_key => $meta_value ) {
			$this->add_meta(
				$order,
				(object) array(
					'key'   => $meta_key,
					'value' => $meta_value,
				)
			);
		}

		$wpdb->update(
			self::get_orders_table_name(),
			array(
				'status'           => 'trash',
				'date_updated_gmt' => current_time( 'Y-m-d H:i:s', true ),
			),
			array( 'id' => $order->get_id() ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		$order->set_status( 'trash' );

		$data_synchronizer = wc_get_container()->get( DataSynchronizer::class );
		if ( $data_synchronizer->data_sync_is_enabled() ) {
			wp_trash_post( $order->get_id() );
		}
	}

	/**
	 * Attempts to restore the specified order back to its original status (after having been trashed).
	 *
	 * @param WC_Order $order The order to be untrashed.
	 *
	 * @return bool If the operation was successful.
	 */
	public function untrash_order( WC_Order $order ): bool {
		$id     = $order->get_id();
		$status = $order->get_status();

		if ( 'trash' !== $status ) {
			wc_get_logger()->warning(
				sprintf(
					/* translators: 1: order ID, 2: order status */
					__( 'Order %1$d cannot be restored from the trash: it has already been restored to status "%2$s".', 'woocommerce' ),
					$id,
					$status
				)
			);
			return false;
		}

		$previous_status           = $order->get_meta( '_wp_trash_meta_status' );
		$valid_statuses            = wc_get_order_statuses();
		$previous_state_is_invalid = ! array_key_exists( $previous_status, $valid_statuses );
		$pending_is_valid_status   = array_key_exists( 'wc-pending', $valid_statuses );

		if ( $previous_state_is_invalid && $pending_is_valid_status ) {
			// If the previous status is no longer valid, let's try to restore it to "pending" instead.
			wc_get_logger()->warning(
				sprintf(
					/* translators: 1: order ID, 2: order status */
					__( 'The previous status of order %1$d ("%2$s") is invalid. It has been restored to "pending" status instead.', 'woocommerce' ),
					$id,
					$previous_status
				)
			);

			$previous_status = 'pending';
		} elseif ( $previous_state_is_invalid ) {
			// If we cannot restore to pending, we should probably stand back and let the merchant intervene some other way.
			wc_get_logger()->warning(
				sprintf(
					/* translators: 1: order ID, 2: order status */
					__( 'The previous status of order %1$d ("%2$s") is invalid. It could not be restored.', 'woocommerce' ),
					$id,
					$previous_status
				)
			);

			return false;
		}

		/**
		 * Fires before an order is restored from the trash.
		 *
		 * @since 7.2.0
		 *
		 * @param int    $order_id        Order ID.
		 * @param string $previous_status The status of the order before it was trashed.
		 */
		do_action( 'woocommerce_untrash_order', $order->get_id(), $previous_status );

		$order->set_status( $previous_status );
		$order->save();

		// Was the status successfully restored? Let's clean up the meta and indicate success...
		if ( 'wc-' . $order->get_status() === $previous_status ) {
			$order->delete_meta_data( '_wp_trash_meta_status' );
			$order->delete_meta_data( '_wp_trash_meta_time' );
			$order->delete_meta_data( '_wp_trash_meta_comments_status' );
			$order->save_meta_data();

			return true;
		}

		// ...Or log a warning and bail.
		wc_get_logger()->warning(
			sprintf(
				/* translators: 1: order ID, 2: order status */
				__( 'Something went wrong when trying to restore order %d from the trash. It could not be restored.', 'woocommerce' ),
				$id
			)
		);

		return false;
	}


	/**
	 * Deletes order data from custom order tables.
	 *
	 * @param int $order_id The order ID.
	 * @return void
	 */
	public function delete_order_data_from_custom_order_tables( $order_id ) {
		global $wpdb;
		$order_cache = wc_get_container()->get( OrderCache::class );

		// Delete COT-specific data.
		foreach ( $this->get_all_table_names() as $table ) {
			$wpdb->delete(
				$table,
				( self::get_orders_table_name() === $table )
					? array( 'id' => $order_id )
					: array( 'order_id' => $order_id ),
				array( '%d' )
			);
			$order_cache->remove( $order_id );
		}
	}

	/**
	 * Method to create an order in the database.
	 *
	 * @param \WC_Order $order Order object.
	 */
	public function create( &$order ) {
		if ( '' === $order->get_order_key() ) {
			$order->set_order_key( wc_generate_order_key() );
		}

		$this->persist_save( $order );

		/**
		 * Fires when a new order is created.
		 *
		 * @since 2.7.0
		 *
		 * @param int       Order ID.
		 * @param \WC_Order Order object.
		 */
		do_action( 'woocommerce_new_order', $order->get_id(), $order );
	}

	/**
	 * Helper method responsible for persisting new data to order table.
	 *
	 * This should not contain and specific meta or actions, so that it can be used other order types safely.
	 *
	 * @param \WC_Order $order Order object.
	 * @param bool      $force_all_fields Force update all fields, instead of calculating and updating only changed fields.
	 * @param bool      $backfill Whether to backfill data to post datastore.
	 *
	 * @return void
	 *
	 * @throws \Exception When unable to save data.
	 */
	protected function persist_save( &$order, bool $force_all_fields = false, $backfill = true ) {
		$order->set_version( Constants::get_constant( 'WC_VERSION' ) );
		$order->set_currency( $order->get_currency() ? $order->get_currency() : get_woocommerce_currency() );

		if ( ! $order->get_date_created( 'edit' ) ) {
			$order->set_date_created( time() );
		}

		$this->update_order_meta( $order );

		$this->persist_order_to_db( $order, $force_all_fields );

		$order->save_meta_data();
		$order->apply_changes();

		if ( $backfill ) {
			$this->maybe_backfill_post_record( $order );
		}
		$this->clear_caches( $order );
	}

	/**
	 * Method to update an order in the database.
	 *
	 * @param \WC_Order $order Order object.
	 */
	public function update( &$order ) {
		// Before updating, ensure date paid is set if missing.
		if (
			! $order->get_date_paid( 'edit' )
			&& version_compare( $order->get_version( 'edit' ), '3.0', '<' )
			&& $order->has_status( apply_filters( 'woocommerce_payment_complete_order_status', $order->needs_processing() ? 'processing' : 'completed', $order->get_id(), $order ) ) // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		) {
			$order->set_date_paid( $order->get_date_created( 'edit' ) );
		}

		if ( null === $order->get_date_created( 'edit' ) ) {
			$order->set_date_created( time() );
		}

		$order->set_version( Constants::get_constant( 'WC_VERSION' ) );

		// Fetch changes.
		$changes = $order->get_changes();
		$this->persist_updates( $order );

		// Update download permissions if necessary.
		if ( array_key_exists( 'billing_email', $changes ) || array_key_exists( 'customer_id', $changes ) ) {
			$data_store = \WC_Data_Store::load( 'customer-download' );
			$data_store->update_user_by_order_id( $order->get_id(), $order->get_customer_id(), $order->get_billing_email() );
		}

		// Mark user account as active.
		if ( array_key_exists( 'customer_id', $changes ) ) {
			wc_update_user_last_active( $order->get_customer_id() );
		}

		$order->apply_changes();
		$this->clear_caches( $order );

		do_action( 'woocommerce_update_order', $order->get_id(), $order ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
	}

	/**
	 * Proxy to udpating order meta. Here for backward compatibility reasons.
	 *
	 * @param \WC_Order $order Order object.
	 *
	 * @return void
	 */
	protected function update_post_meta( &$order ) {
		$this->update_order_meta( $order );
	}

	/**
	 * Helper method that is responsible for persisting order updates to the database.
	 *
	 * This is expected to be reused by other order types, and should not contain any specific metadata updates or actions.
	 *
	 * @param \WC_Order $order Order object.
	 * @param bool      $backfill Whether to backfill data to post tables.
	 *
	 * @return array $changes Array of changes.
	 *
	 * @throws \Exception When unable to persist order.
	 */
	protected function persist_updates( &$order, $backfill = true ) {
		// Fetch changes.
		$changes = $order->get_changes();

		if ( ! isset( $changes['date_modified'] ) ) {
			$order->set_date_modified( time() );
		}

		if ( $backfill ) {
			$this->maybe_backfill_post_record( $order );
		}

		$this->persist_order_to_db( $order );
		$order->save_meta_data();

		return $changes;
	}

	/**
	 * Helper function to decide whether to backfill post record.
	 *
	 * @param \WC_Abstract_Order $order Order object.
	 *
	 * @return void
	 */
	private function maybe_backfill_post_record( $order ) {
		$data_sync = wc_get_container()->get( DataSynchronizer::class );
		if ( $data_sync->data_sync_is_enabled() ) {
			$this->backfill_post_record( $order );
		}
	}

	/**
	 * Helper method that updates post meta based on an order object.
	 * Mostly used for backwards compatibility purposes in this datastore.
	 *
	 * @param \WC_Order $order Order object.
	 *
	 * @since 7.0.0
	 */
	public function update_order_meta( &$order ) {
		$changes = $order->get_changes();
		$this->update_address_index_meta( $order, $changes );
	}

	/**
	 * Helper function to update billing and shipping address metadata.
	 *
	 * @param \WC_Abstract_Order $order Order Object.
	 * @param array              $changes Array of changes.
	 *
	 * @return void
	 */
	private function update_address_index_meta( $order, $changes ) {
		// If address changed, store concatenated version to make searches faster.
		foreach ( array( 'billing', 'shipping' ) as $address_type ) {
			if ( isset( $changes[ $address_type ] ) ) {
				$order->update_meta_data( "_{$address_type}_address_index", implode( ' ', $order->get_address( $address_type ) ) );
			}
		}
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
				$coupon = new \WC_Coupon( $coupon_id );
				$coupon->delete_meta_data( $meta_key );
				$coupon->save_meta_data();
			}
		}
		$order->delete_meta_data( '_coupon_held_keys' );

		$coupon_held_keys_for_users = $this->get_coupon_held_keys_for_users( $order );
		if ( is_array( $coupon_held_keys_for_users ) ) {
			foreach ( $coupon_held_keys_for_users as $coupon_id => $meta_key ) {
				$coupon = new \WC_Coupon( $coupon_id );
				$coupon->delete_meta_data( $meta_key );
				$coupon->save_meta_data();
			}
		}
		$order->delete_meta_data( '_coupon_held_keys_for_users' );

		if ( $save ) {
			$order->save_meta_data();
		}

	}

	/**
	 * Performs actual query to get orders. Uses `OrdersTableQuery` to build and generate the query.
	 *
	 * @param array $query_vars Query variables.
	 *
	 * @return array|object List of orders and count of orders.
	 */
	public function query( $query_vars ) {
		if ( ! isset( $query_vars['paginate'] ) || ! $query_vars['paginate'] ) {
			$query_vars['no_found_rows'] = true;
		}

		if ( isset( $query_vars['anonymized'] ) ) {
			$query_vars['meta_query'] = $query_vars['meta_query'] ?? array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query

			if ( $query_vars['anonymized'] ) {
				$query_vars['meta_query'][] = array(
					'key'   => '_anonymized',
					'value' => 'yes',
				);
			} else {
				$query_vars['meta_query'][] = array(
					'key'     => '_anonymized',
					'compare' => 'NOT EXISTS',
				);
			}
		}

		try {
			$query = new OrdersTableQuery( $query_vars );
		} catch ( \Exception $e ) {
			$query = (object) array(
				'orders'        => array(),
				'found_orders'  => 0,
				'max_num_pages' => 0,
			);
		}

		if ( isset( $query_vars['return'] ) && 'ids' === $query_vars['return'] ) {
			$orders = $query->orders;
		} else {
			$orders = WC()->order_factory->get_orders( $query->orders );
		}

		if ( isset( $query_vars['paginate'] ) && $query_vars['paginate'] ) {
			return (object) array(
				'orders'        => $orders,
				'total'         => $query->found_orders,
				'max_num_pages' => $query->max_num_pages,
			);
		}

		return $orders;
	}

	//phpcs:enable Squiz.Commenting, Generic.Commenting

	/**
	 * Get the SQL needed to create all the tables needed for the custom orders table feature.
	 *
	 * @return string
	 */
	public function get_database_schema() {
		global $wpdb;

		$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		$orders_table_name           = $this->get_orders_table_name();
		$addresses_table_name        = $this->get_addresses_table_name();
		$operational_data_table_name = $this->get_operational_data_table_name();
		$meta_table                  = $this->get_meta_table_name();

		$sql = "
CREATE TABLE $orders_table_name (
	id bigint(20) unsigned,
	status varchar(20) null,
	currency varchar(10) null,
	type varchar(20) null,
	tax_amount decimal(26,8) null,
	total_amount decimal(26,8) null,
	customer_id bigint(20) unsigned null,
	billing_email varchar(320) null,
	date_created_gmt datetime null,
	date_updated_gmt datetime null,
	parent_order_id bigint(20) unsigned null,
	payment_method varchar(100) null,
	payment_method_title text null,
	transaction_id varchar(100) null,
	ip_address varchar(100) null,
	user_agent text null,
	customer_note text null,
	PRIMARY KEY (id),
	KEY status (status),
	KEY date_created (date_created_gmt),
	KEY customer_id_billing_email (customer_id, billing_email),
	KEY billing_email (billing_email),
	KEY type_status (type, status),
	KEY parent_order_id (parent_order_id),
	KEY date_updated (date_updated_gmt)
) $collate;
CREATE TABLE $addresses_table_name (
	id bigint(20) unsigned auto_increment primary key,
	order_id bigint(20) unsigned NOT NULL,
	address_type varchar(20) null,
	first_name text null,
	last_name text null,
	company text null,
	address_1 text null,
	address_2 text null,
	city text null,
	state text null,
	postcode text null,
	country text null,
	email varchar(320) null,
	phone varchar(100) null,
	KEY order_id (order_id),
	UNIQUE KEY address_type_order_id (address_type, order_id),
	KEY email (email),
	KEY phone (phone)
) $collate;
CREATE TABLE $operational_data_table_name (
	id bigint(20) unsigned auto_increment primary key,
	order_id bigint(20) unsigned NULL,
	created_via varchar(100) NULL,
	woocommerce_version varchar(20) NULL,
	prices_include_tax tinyint(1) NULL,
	coupon_usages_are_counted tinyint(1) NULL,
	download_permission_granted tinyint(1) NULL,
	cart_hash varchar(100) NULL,
	new_order_email_sent tinyint(1) NULL,
	order_key varchar(100) NULL,
	order_stock_reduced tinyint(1) NULL,
	date_paid_gmt datetime NULL,
	date_completed_gmt datetime NULL,
	shipping_tax_amount decimal(26, 8) NULL,
	shipping_total_amount decimal(26, 8) NULL,
	discount_tax_amount decimal(26, 8) NULL,
	discount_total_amount decimal(26, 8) NULL,
	recorded_sales tinyint(1) NULL,
	UNIQUE KEY order_id (order_id),
	KEY order_key (order_key)
) $collate;
CREATE TABLE $meta_table (
	id bigint(20) unsigned auto_increment primary key,
	order_id bigint(20) unsigned null,
	meta_key varchar(255),
	meta_value text null,
	KEY meta_key_value (meta_key, meta_value(100)),
	KEY order_id_meta_key_meta_value (order_id, meta_key, meta_value(100))
) $collate;
";

		return $sql;
	}

	/**
	 * Returns an array of meta for an object.
	 *
	 * @param  WC_Data $object WC_Data object.
	 * @return array
	 */
	public function read_meta( &$object ) {
		$raw_meta_data = $this->data_store_meta->read_meta( $object );
		return $this->filter_raw_meta_data( $object, $raw_meta_data );
	}

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @param  WC_Data  $object WC_Data object.
	 * @param  stdClass $meta (containing at least ->id).
	 *
	 * @return bool
	 */
	public function delete_meta( &$object, $meta ) {
		$delete_meta = $this->data_store_meta->delete_meta( $object, $meta );

		if ( $object instanceof WC_Abstract_Order ) {
			$this->maybe_backfill_post_record( $object );
		}

		return $delete_meta;
	}

	/**
	 * Add new piece of meta.
	 *
	 * @param  WC_Data  $object WC_Data object.
	 * @param  stdClass $meta (containing ->key and ->value).
	 *
	 * @return int|bool  meta ID or false on failure
	 */
	public function add_meta( &$object, $meta ) {
		$add_meta = $this->data_store_meta->add_meta( $object, $meta );

		if ( $object instanceof WC_Abstract_Order ) {
			$this->maybe_backfill_post_record( $object );
		}

		return $add_meta;
	}

	/**
	 * Update meta.
	 *
	 * @param  WC_Data  $object WC_Data object.
	 * @param  stdClass $meta (containing ->id, ->key and ->value).
	 *
	 * @return bool
	 */
	public function update_meta( &$object, $meta ) {
		$update_meta = $this->data_store_meta->update_meta( $object, $meta );

		if ( $object instanceof WC_Abstract_Order ) {
			$this->maybe_backfill_post_record( $object );
		}

		return $update_meta;
	}
}

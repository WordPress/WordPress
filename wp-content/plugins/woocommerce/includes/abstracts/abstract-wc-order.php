<?php
/**
 * Abstract Order
 *
 * Handles generic order data and database interaction which is extended by both
 * WC_Order (regular orders) and WC_Order_Refund (refunds are negative orders).
 *
 * @class       WC_Abstract_Order
 * @version     3.0.0
 * @package     WooCommerce\Classes
 */

use Automattic\WooCommerce\Caches\OrderCache;
use Automattic\WooCommerce\Proxies\LegacyProxy;
use Automattic\WooCommerce\Utilities\ArrayUtil;
use Automattic\WooCommerce\Utilities\NumberUtil;
use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) || exit;

require_once WC_ABSPATH . 'includes/legacy/abstract-wc-legacy-order.php';

/**
 * WC_Abstract_Order class.
 */
abstract class WC_Abstract_Order extends WC_Abstract_Legacy_Order {
	use WC_Item_Totals;

	/**
	 * Order Data array. This is the core order data exposed in APIs since 3.0.0.
	 *
	 * Notes: cart_tax = cart_tax is the new name for the legacy 'order_tax'
	 * which is the tax for items only, not shipping.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $data = array(
		'parent_id'          => 0,
		'status'             => '',
		'currency'           => '',
		'version'            => '',
		'prices_include_tax' => false,
		'date_created'       => null,
		'date_modified'      => null,
		'discount_total'     => 0,
		'discount_tax'       => 0,
		'shipping_total'     => 0,
		'shipping_tax'       => 0,
		'cart_tax'           => 0,
		'total'              => 0,
		'total_tax'          => 0,
	);

	/**
	 * List of properties that were earlier managed by data store. However, since DataStore is a not a stored entity in itself, they used to store data in metadata of the data object.
	 * With custom tables, some of these are moved from metadata to their own columns, but existing code will still try to add them to metadata. This array is used to keep track of such properties.
	 *
	 * Only reason to add a property here is that you are moving properties from DataStore instance to data object. If you are adding a new property, consider adding it to to $data array instead.
	 *
	 * @var array
	 */
	protected $legacy_datastore_props = array(
		'_recorded_coupon_usage_counts',
	);

	/**
	 * Order items will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $items = array();

	/**
	 * Order items that need deleting are stored here.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $items_to_delete = array();

	/**
	 * Stores meta in cache for future reads.
	 *
	 * A group must be set to to enable caching.
	 *
	 * @var string
	 */
	protected $cache_group = 'orders';

	/**
	 * Which data store to load.
	 *
	 * @var string
	 */
	protected $data_store_name = 'order';

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'order';

	/**
	 * Get the order if ID is passed, otherwise the order is new and empty.
	 * This class should NOT be instantiated, but the wc_get_order function or new WC_Order_Factory
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param  int|object|WC_Order $order Order to read.
	 */
	public function __construct( $order = 0 ) {
		parent::__construct( $order );

		if ( is_numeric( $order ) && $order > 0 ) {
			$this->set_id( $order );
		} elseif ( $order instanceof self ) {
			$this->set_id( $order->get_id() );
		} elseif ( ! empty( $order->ID ) ) {
			$this->set_id( $order->ID );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = WC_Data_Store::load( $this->data_store_name );

		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}

	/**
	 * This method overwrites the base class's clone method to make it a no-op. In base class WC_Data, we are unsetting the meta_id to clone.
	 * It seems like this was done to avoid conflicting the metadata when duplicating products. However, doing that does not seems necessary for orders.
	 * In-fact, when we do that for orders, we lose the capability to clone orders with custom meta data by caching plugins. This is because, when we clone an order object for caching, it will clone the metadata without the ID. Unfortunately, when this cached object with nulled meta ID is retrieved, WC_Data will consider it as a new meta and will insert it as a new meta-data causing duplicates.
	 *
	 * Eventually, we should move away from overwriting the __clone method in base class itself, since it's easily possible to still duplicate the product without having to hook into the __clone method.
	 *
	 * @since 7.6.0
	 */
	public function __clone() {}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'shop_order';
	}

	/**
	 * Get all class data in array format.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_data() {
		return array_merge(
			array(
				'id' => $this->get_id(),
			),
			$this->data,
			array(
				'meta_data'      => $this->get_meta_data(),
				'line_items'     => $this->get_items( 'line_item' ),
				'tax_lines'      => $this->get_items( 'tax' ),
				'shipping_lines' => $this->get_items( 'shipping' ),
				'fee_lines'      => $this->get_items( 'fee' ),
				'coupon_lines'   => $this->get_items( 'coupon' ),
			)
		);
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete orders from the database.
	| Written in abstract fashion so that the way orders are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/

	/**
	 * Save data to the database.
	 *
	 * @since 3.0.0
	 * @return int order ID
	 */
	public function save() {
		if ( ! $this->data_store ) {
			return $this->get_id();
		}

		try {
			/**
			 * Trigger action before saving to the DB. Allows you to adjust object props before save.
			 *
			 * @param WC_Data          $this The object being saved.
			 * @param WC_Data_Store_WP $data_store THe data store persisting the data.
			 */
			do_action( 'woocommerce_before_' . $this->object_type . '_object_save', $this, $this->data_store );

			if ( $this->get_id() ) {
				$this->data_store->update( $this );
			} else {
				$this->data_store->create( $this );
			}

			$this->save_items();

			if ( OrderUtil::orders_cache_usage_is_enabled() ) {
				$order_cache = wc_get_container()->get( OrderCache::class );
				$order_cache->remove( $this->get_id() );
			}

			/**
			 * Trigger action after saving to the DB.
			 *
			 * @param WC_Data          $this The object being saved.
			 * @param WC_Data_Store_WP $data_store THe data store persisting the data.
			 */
			do_action( 'woocommerce_after_' . $this->object_type . '_object_save', $this, $this->data_store );

		} catch ( Exception $e ) {
			$message_id = $this->get_id() ? $this->get_id() : __( '(no ID)', 'woocommerce' );
			$this->handle_exception(
				$e,
				wp_kses_post(
					sprintf(
						/* translators: 1: Order ID or "(no ID)" if not known. */
						__( 'Error saving order ID %1$s.', 'woocommerce' ),
						$message_id
					)
				)
			);
		}

		return $this->get_id();
	}

	/**
	 * Log an error about this order is exception is encountered.
	 *
	 * @param Exception $e Exception object.
	 * @param string    $message Message regarding exception thrown.
	 * @since 3.7.0
	 */
	protected function handle_exception( $e, $message = 'Error' ) {
		wc_get_logger()->error(
			$message,
			array(
				'order' => $this,
				'error' => $e,
			)
		);
	}

	/**
	 * Save all order items which are part of this order.
	 */
	protected function save_items() {
		$items_changed = false;

		foreach ( $this->items_to_delete as $item ) {
			$item->delete();
			$items_changed = true;
		}
		$this->items_to_delete = array();

		// Add/save items.
		foreach ( $this->items as $item_group => $items ) {
			if ( is_array( $items ) ) {
				$items = array_filter( $items );
				foreach ( $items as $item_key => $item ) {
					$item->set_order_id( $this->get_id() );

					$item_id = $item->save();

					// If ID changed (new item saved to DB)...
					if ( $item_id !== $item_key ) {
						$this->items[ $item_group ][ $item_id ] = $item;

						unset( $this->items[ $item_group ][ $item_key ] );

						$items_changed = true;
					}
				}
			}
		}

		if ( $items_changed ) {
			delete_transient( 'wc_order_' . $this->get_id() . '_needs_processing' );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get parent order ID.
	 *
	 * @since 3.0.0
	 * @param  string $context View or edit context.
	 * @return integer
	 */
	public function get_parent_id( $context = 'view' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Gets order currency.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_currency( $context = 'view' ) {
		return $this->get_prop( 'currency', $context );
	}

	/**
	 * Get order_version.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_version( $context = 'view' ) {
		return $this->get_prop( 'version', $context );
	}

	/**
	 * Get prices_include_tax.
	 *
	 * @param  string $context View or edit context.
	 * @return bool
	 */
	public function get_prices_include_tax( $context = 'view' ) {
		return $this->get_prop( 'prices_include_tax', $context );
	}

	/**
	 * Get date_created.
	 *
	 * @param  string $context View or edit context.
	 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_created( $context = 'view' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Get date_modified.
	 *
	 * @param  string $context View or edit context.
	 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_modified( $context = 'view' ) {
		return $this->get_prop( 'date_modified', $context );
	}

	/**
	 * Get date_modified.
	 *
	 * @param  string $context View or edit context.
	 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_paid( $context = 'view' ) {
		return $this->get_prop( 'date_paid', $context );
	}

	/**
	 * Get date_modified.
	 *
	 * @param  string $context View or edit context.
	 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_completed( $context = 'view' ) {
		return $this->get_prop( 'date_completed', $context );
	}


	/**
	 * Return the order statuses without wc- internal prefix.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_status( $context = 'view' ) {
		$status = $this->get_prop( 'status', $context );

		if ( empty( $status ) && 'view' === $context ) {
			// In view context, return the default status if no status has been set.
			$status = apply_filters( 'woocommerce_default_order_status', 'pending' );
		}
		return $status;
	}

	/**
	 * Get discount_total.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_discount_total( $context = 'view' ) {
		return $this->get_prop( 'discount_total', $context );
	}

	/**
	 * Get discount_tax.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_discount_tax( $context = 'view' ) {
		return $this->get_prop( 'discount_tax', $context );
	}

	/**
	 * Get shipping_total.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_shipping_total( $context = 'view' ) {
		return $this->get_prop( 'shipping_total', $context );
	}

	/**
	 * Get shipping_tax.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_shipping_tax( $context = 'view' ) {
		return $this->get_prop( 'shipping_tax', $context );
	}

	/**
	 * Gets cart tax amount.
	 *
	 * @param  string $context View or edit context.
	 * @return float
	 */
	public function get_cart_tax( $context = 'view' ) {
		return $this->get_prop( 'cart_tax', $context );
	}

	/**
	 * Gets order grand total including taxes, shipping cost, fees, and coupon discounts. Used in gateways.
	 *
	 * @param  string $context View or edit context.
	 * @return float
	 */
	public function get_total( $context = 'view' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Get total tax amount. Alias for get_order_tax().
	 *
	 * @param  string $context View or edit context.
	 * @return float
	 */
	public function get_total_tax( $context = 'view' ) {
		return $this->get_prop( 'total_tax', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Non-CRUD Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Gets the total discount amount.
	 *
	 * @param  bool $ex_tax Show discount excl any tax.
	 * @return float
	 */
	public function get_total_discount( $ex_tax = true ) {
		if ( $ex_tax ) {
			$total_discount = $this->get_discount_total();
		} else {
			$total_discount = $this->get_discount_total() + $this->get_discount_tax();
		}
		return apply_filters( 'woocommerce_order_get_total_discount', NumberUtil::round( $total_discount, WC_ROUNDING_PRECISION ), $this );
	}

	/**
	 * Gets order subtotal. Order subtotal is the price of all items excluding taxes, fees, shipping cost, and coupon discounts.
	 * If sale price is set on an item, the subtotal will include this sale discount. E.g. a product with a regular
	 * price of $100 bought at a 50% discount will represent $50 of the subtotal for the order.
	 *
	 * @return float
	 */
	public function get_subtotal() {
		$subtotal = NumberUtil::round( $this->get_cart_subtotal_for_order(), wc_get_price_decimals() );
		return apply_filters( 'woocommerce_order_get_subtotal', (float) $subtotal, $this );
	}

	/**
	 * Get taxes, merged by code, formatted ready for output.
	 *
	 * @return array
	 */
	public function get_tax_totals() {
		$tax_totals = array();

		foreach ( $this->get_items( 'tax' ) as $key => $tax ) {
			$code = $tax->get_rate_code();

			if ( ! isset( $tax_totals[ $code ] ) ) {
				$tax_totals[ $code ]         = new stdClass();
				$tax_totals[ $code ]->amount = 0;
			}

			$tax_totals[ $code ]->id               = $key;
			$tax_totals[ $code ]->rate_id          = $tax->get_rate_id();
			$tax_totals[ $code ]->is_compound      = $tax->is_compound();
			$tax_totals[ $code ]->label            = $tax->get_label();
			$tax_totals[ $code ]->amount          += (float) $tax->get_tax_total() + (float) $tax->get_shipping_tax_total();
			$tax_totals[ $code ]->formatted_amount = wc_price( $tax_totals[ $code ]->amount, array( 'currency' => $this->get_currency() ) );
		}

		if ( apply_filters( 'woocommerce_order_hide_zero_taxes', true ) ) {
			$amounts    = array_filter( wp_list_pluck( $tax_totals, 'amount' ) );
			$tax_totals = array_intersect_key( $tax_totals, $amounts );
		}

		return apply_filters( 'woocommerce_order_get_tax_totals', $tax_totals, $this );
	}

	/**
	 * Get all valid statuses for this order
	 *
	 * @since 3.0.0
	 * @return array Internal status keys e.g. 'wc-processing'
	 */
	protected function get_valid_statuses() {
		return array_keys( wc_get_order_statuses() );
	}

	/**
	 * Get user ID. Used by orders, not other order types like refunds.
	 *
	 * @param  string $context View or edit context.
	 * @return int
	 */
	public function get_user_id( $context = 'view' ) {
		return 0;
	}

	/**
	 * Get user. Used by orders, not other order types like refunds.
	 *
	 * @return WP_User|false
	 */
	public function get_user() {
		return false;
	}

	/**
	 * Gets information about whether coupon counts were updated.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return bool True if coupon counts were updated, false otherwise.
	 */
	public function get_recorded_coupon_usage_counts( $context = 'view' ) {
		return wc_string_to_bool( $this->get_prop( 'recorded_coupon_usage_counts', $context ) );
	}

	/**
	 * Get basic order data in array format.
	 *
	 * @return array
	 */
	public function get_base_data() {
		return array_merge(
			array( 'id' => $this->get_id() ),
			$this->data
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting order data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object. However, for backwards compatibility pre 3.0.0 some of these
	| setters may handle both.
	*/

	/**
	 * Set parent order ID.
	 *
	 * @since 3.0.0
	 * @param int $value Value to set.
	 * @throws WC_Data_Exception Exception thrown if parent ID does not exist or is invalid.
	 */
	public function set_parent_id( $value ) {
		if ( $value && ( $value === $this->get_id() || ! wc_get_order( $value ) ) ) {
			$this->error( 'order_invalid_parent_id', __( 'Invalid parent ID', 'woocommerce' ) );
		}
		$this->set_prop( 'parent_id', absint( $value ) );
	}

	/**
	 * Set order status.
	 *
	 * @since 3.0.0
	 * @param string $new_status Status to change the order to. No internal wc- prefix is required.
	 * @return array details of change
	 */
	public function set_status( $new_status ) {
		$old_status = $this->get_status();
		$new_status = 'wc-' === substr( $new_status, 0, 3 ) ? substr( $new_status, 3 ) : $new_status;

		$status_exceptions = array( 'auto-draft', 'trash' );

		// If setting the status, ensure it's set to a valid status.
		if ( true === $this->object_read ) {
			// Only allow valid new status.
			if ( ! in_array( 'wc-' . $new_status, $this->get_valid_statuses(), true ) && ! in_array( $new_status, $status_exceptions, true ) ) {
				$new_status = 'pending';
			}

			// If the old status is set but unknown (e.g. draft) assume its pending for action usage.
			if ( $old_status && ( 'auto-draft' === $old_status || ( ! in_array( 'wc-' . $old_status, $this->get_valid_statuses(), true ) && ! in_array( $old_status, $status_exceptions, true ) ) ) ) {
				$old_status = 'pending';
			}
		}

		$this->set_prop( 'status', $new_status );

		return array(
			'from' => $old_status,
			'to'   => $new_status,
		);
	}

	/**
	 * Set order_version.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_version( $value ) {
		$this->set_prop( 'version', $value );
	}

	/**
	 * Set order_currency.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_currency( $value ) {
		if ( $value && ! in_array( $value, array_keys( get_woocommerce_currencies() ), true ) ) {
			$this->error( 'order_invalid_currency', __( 'Invalid currency code', 'woocommerce' ) );
		}
		$this->set_prop( 'currency', $value ? $value : get_woocommerce_currency() );
	}

	/**
	 * Set prices_include_tax.
	 *
	 * @param bool $value Value to set.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_prices_include_tax( $value ) {
		$this->set_prop( 'prices_include_tax', (bool) $value );
	}

	/**
	 * Set date_created.
	 *
	 * @param  string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if there is no date.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_date_created( $date = null ) {
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set date_modified.
	 *
	 * @param  string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if there is no date.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_date_modified( $date = null ) {
		$this->set_date_prop( 'date_modified', $date );
	}

	/**
	 * Set discount_total.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_discount_total( $value ) {
		$this->set_prop( 'discount_total', wc_format_decimal( $value, false, true ) );
	}

	/**
	 * Set discount_tax.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_discount_tax( $value ) {
		$this->set_prop( 'discount_tax', wc_format_decimal( $value, false, true ) );
	}

	/**
	 * Set shipping_total.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_shipping_total( $value ) {
		$this->set_prop( 'shipping_total', wc_format_decimal( $value, false, true ) );
	}

	/**
	 * Set shipping_tax.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_shipping_tax( $value ) {
		$this->set_prop( 'shipping_tax', wc_format_decimal( $value, false, true ) );
		$this->set_total_tax( (float) $this->get_cart_tax() + (float) $this->get_shipping_tax() );
	}

	/**
	 * Set cart tax.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_cart_tax( $value ) {
		$this->set_prop( 'cart_tax', wc_format_decimal( $value, false, true ) );
		$this->set_total_tax( (float) $this->get_cart_tax() + (float) $this->get_shipping_tax() );
	}

	/**
	 * Sets order tax (sum of cart and shipping tax). Used internally only.
	 *
	 * @param string $value Value to set.
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	protected function set_total_tax( $value ) {
		// We round here because this is a total entry, as opposed to line items in other setters.
		$this->set_prop( 'total_tax', wc_format_decimal( NumberUtil::round( $value, wc_get_price_decimals() ) ) );
	}

	/**
	 * Set total.
	 *
	 * @param string $value Value to set.
	 * @param string $deprecated Function used to set different totals based on this.
	 *
	 * @return bool|void
	 * @throws WC_Data_Exception Exception may be thrown if value is invalid.
	 */
	public function set_total( $value, $deprecated = '' ) {
		if ( $deprecated ) {
			wc_deprecated_argument( 'total_type', '3.0', 'Use dedicated total setter methods instead.' );
			return $this->legacy_set_total( $value, $deprecated );
		}
		$this->set_prop( 'total', wc_format_decimal( $value, wc_get_price_decimals() ) );
	}

	/**
	 * Stores information about whether the coupon usage were counted.
	 *
	 * @param bool|string $value True if counted, false if not.
	 *
	 * @return void
	 */
	public function set_recorded_coupon_usage_counts( $value ) {
		$this->set_prop( 'recorded_coupon_usage_counts', wc_string_to_bool( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Order Item Handling
	|--------------------------------------------------------------------------
	|
	| Order items are used for products, taxes, shipping, and fees within
	| each order.
	*/

	/**
	 * Remove all line items (products, coupons, shipping, taxes) from the order.
	 *
	 * @param string $type Order item type. Default null.
	 */
	public function remove_order_items( $type = null ) {

		/**
		 * Trigger action before removing all order line items. Allows you to track order items.
		 *
		 * @param  WC_Order  $this  The current order object.
		 * @param  string $type Order item type. Default null.
		 *
		 * @since 7.8.0
		 */
		do_action( 'woocommerce_remove_order_items', $this, $type );
		if ( ! empty( $type ) ) {
			$this->data_store->delete_items( $this, $type );

			$group = $this->type_to_group( $type );

			if ( $group ) {
				unset( $this->items[ $group ] );
			}
		} else {
			$this->data_store->delete_items( $this );
			$this->items = array();
		}
		/**
		 * Trigger action after removing all order line items.
		 *
		 * @param  WC_Order  $this  The current order object.
		 * @param  string $type Order item type. Default null.
		 *
		 * @since 7.8.0
		 */
		do_action( 'woocommerce_removed_order_items', $this, $type );
	}

	/**
	 * Convert a type to a types group.
	 *
	 * @param string $type type to lookup.
	 * @return string
	 */
	protected function type_to_group( $type ) {
		$type_to_group = apply_filters(
			'woocommerce_order_type_to_group',
			array(
				'line_item' => 'line_items',
				'tax'       => 'tax_lines',
				'shipping'  => 'shipping_lines',
				'fee'       => 'fee_lines',
				'coupon'    => 'coupon_lines',
			)
		);
		return isset( $type_to_group[ $type ] ) ? $type_to_group[ $type ] : '';
	}

	/**
	 * Return an array of items/products within this order.
	 *
	 * @param string|array $types Types of line items to get (array or string).
	 * @return WC_Order_Item[]
	 */
	public function get_items( $types = 'line_item' ) {
		$items = array();
		$types = array_filter( (array) $types );

		foreach ( $types as $type ) {
			$group = $this->type_to_group( $type );

			if ( $group ) {
				if ( ! isset( $this->items[ $group ] ) ) {
					$this->items[ $group ] = array_filter( $this->data_store->read_items( $this, $type ) );
				}
				// Don't use array_merge here because keys are numeric.
				$items = $items + $this->items[ $group ];
			}
		}

		return apply_filters( 'woocommerce_order_get_items', $items, $this, $types );
	}

	/**
	 * Return array of values for calculations.
	 *
	 * @param string $field Field name to return.
	 *
	 * @return array Array of values.
	 */
	protected function get_values_for_total( $field ) {
		$items = array_map(
			function ( $item ) use ( $field ) {
				return wc_add_number_precision( $item[ $field ], false );
			},
			array_values( $this->get_items() )
		);
		return $items;
	}

	/**
	 * Return an array of coupons within this order.
	 *
	 * @since  3.7.0
	 * @return WC_Order_Item_Coupon[]
	 */
	public function get_coupons() {
		return $this->get_items( 'coupon' );
	}

	/**
	 * Return an array of fees within this order.
	 *
	 * @return WC_Order_item_Fee[]
	 */
	public function get_fees() {
		return $this->get_items( 'fee' );
	}

	/**
	 * Return an array of taxes within this order.
	 *
	 * @return WC_Order_Item_Tax[]
	 */
	public function get_taxes() {
		return $this->get_items( 'tax' );
	}

	/**
	 * Return an array of shipping costs within this order.
	 *
	 * @return WC_Order_Item_Shipping[]
	 */
	public function get_shipping_methods() {
		return $this->get_items( 'shipping' );
	}

	/**
	 * Gets formatted shipping method title.
	 *
	 * @return string
	 */
	public function get_shipping_method() {
		$names = array();
		foreach ( $this->get_shipping_methods() as $shipping_method ) {
			$names[] = $shipping_method->get_name();
		}
		return apply_filters( 'woocommerce_order_shipping_method', implode( ', ', $names ), $this );
	}

	/**
	 * Get used coupon codes only.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_coupon_codes() {
		$coupon_codes = array();
		$coupons      = $this->get_items( 'coupon' );

		if ( $coupons ) {
			foreach ( $coupons as $coupon ) {
				$coupon_codes[] = $coupon->get_code();
			}
		}
		return $coupon_codes;
	}

	/**
	 * Gets the count of order items of a certain type.
	 *
	 * @param string $item_type Item type to lookup.
	 * @return int|string
	 */
	public function get_item_count( $item_type = '' ) {
		$items = $this->get_items( empty( $item_type ) ? 'line_item' : $item_type );
		$count = 0;

		foreach ( $items as $item ) {
			$count += $item->get_quantity();
		}

		return apply_filters( 'woocommerce_get_item_count', $count, $item_type, $this );
	}

	/**
	 * Get an order item object, based on its type.
	 *
	 * @since  3.0.0
	 * @param  int  $item_id ID of item to get.
	 * @param  bool $load_from_db Prior to 3.2 this item was loaded direct from WC_Order_Factory, not this object. This param is here for backwards compatibility with that. If false, uses the local items variable instead.
	 * @return WC_Order_Item|false
	 */
	public function get_item( $item_id, $load_from_db = true ) {
		if ( $load_from_db ) {
			return WC_Order_Factory::get_order_item( $item_id );
		}

		// Search for item id.
		if ( $this->items ) {
			foreach ( $this->items as $group => $items ) {
				if ( isset( $items[ $item_id ] ) ) {
					return $items[ $item_id ];
				}
			}
		}

		// Load all items of type and cache.
		$type = $this->data_store->get_order_item_type( $this, $item_id );

		if ( ! $type ) {
			return false;
		}

		$items = $this->get_items( $type );

		return ! empty( $items[ $item_id ] ) ? $items[ $item_id ] : false;
	}

	/**
	 * Get key for where a certain item type is stored in _items.
	 *
	 * @since  3.0.0
	 * @param  string $item object Order item (product, shipping, fee, coupon, tax).
	 * @return string
	 */
	protected function get_items_key( $item ) {
		if ( is_a( $item, 'WC_Order_Item_Product' ) ) {
			return 'line_items';
		} elseif ( is_a( $item, 'WC_Order_Item_Fee' ) ) {
			return 'fee_lines';
		} elseif ( is_a( $item, 'WC_Order_Item_Shipping' ) ) {
			return 'shipping_lines';
		} elseif ( is_a( $item, 'WC_Order_Item_Tax' ) ) {
			return 'tax_lines';
		} elseif ( is_a( $item, 'WC_Order_Item_Coupon' ) ) {
			return 'coupon_lines';
		}
		return apply_filters( 'woocommerce_get_items_key', '', $item );
	}

	/**
	 * Remove item from the order.
	 *
	 * @param int $item_id Item ID to delete.
	 * @return false|void
	 */
	public function remove_item( $item_id ) {
		$item      = $this->get_item( $item_id, false );
		$items_key = $item ? $this->get_items_key( $item ) : false;

		if ( ! $items_key ) {
			return false;
		}

		// Unset and remove later.
		$this->items_to_delete[] = $item;
		unset( $this->items[ $items_key ][ $item->get_id() ] );
	}

	/**
	 * Adds an order item to this order. The order item will not persist until save.
	 *
	 * @since 3.0.0
	 * @param WC_Order_Item $item Order item object (product, shipping, fee, coupon, tax).
	 * @return false|void
	 */
	public function add_item( $item ) {
		$items_key = $this->get_items_key( $item );

		if ( ! $items_key ) {
			return false;
		}

		// Make sure existing items are loaded so we can append this new one.
		if ( ! isset( $this->items[ $items_key ] ) ) {
			$this->items[ $items_key ] = $this->get_items( $item->get_type() );
		}

		// Set parent.
		$item->set_order_id( $this->get_id() );

		// Append new row with generated temporary ID.
		$item_id = $item->get_id();

		if ( $item_id ) {
			$this->items[ $items_key ][ $item_id ] = $item;
		} else {
			$this->items[ $items_key ][ 'new:' . $items_key . count( $this->items[ $items_key ] ) ] = $item;
		}
	}

	/**
	 * Check and records coupon usage tentatively so that counts validation is correct. Display an error if coupon usage limit has been reached.
	 *
	 * If you are using this method, make sure to `release_held_coupons` in case an Exception is thrown.
	 *
	 * @throws Exception When not able to apply coupon.
	 *
	 * @param string $billing_email Billing email of order.
	 */
	public function hold_applied_coupons( $billing_email ) {
		$held_keys          = array();
		$held_keys_for_user = array();
		$error              = null;

		try {
			foreach ( WC()->cart->get_applied_coupons() as $code ) {
				$coupon = new WC_Coupon( $code );
				if ( ! $coupon->get_data_store() ) {
					continue;
				}

				// Hold coupon for when global coupon usage limit is present.
				if ( 0 < $coupon->get_usage_limit() ) {
					$held_key = $this->hold_coupon( $coupon );
					if ( $held_key ) {
						$held_keys[ $coupon->get_id() ] = $held_key;
					}
				}

				// Hold coupon for when usage limit per customer is enabled.
				if ( 0 < $coupon->get_usage_limit_per_user() ) {

					if ( ! isset( $user_ids_and_emails ) ) {
						$user_alias          = get_current_user_id() ? wp_get_current_user()->ID : sanitize_email( $billing_email );
						$user_ids_and_emails = $this->get_billing_and_current_user_aliases( $billing_email );
					}

					$held_key_for_user = $this->hold_coupon_for_users( $coupon, $user_ids_and_emails, $user_alias );

					if ( $held_key_for_user ) {
						$held_keys_for_user[ $coupon->get_id() ] = $held_key_for_user;
					}
				}
			}
		} catch ( Exception $e ) {
			$error = $e;
		} finally {
			// Even in case of error, we will save keys for whatever coupons that were held so our data remains accurate.
			// We save them in bulk instead of one by one for performance reasons.
			if ( 0 < count( $held_keys_for_user ) || 0 < count( $held_keys ) ) {
				$this->get_data_store()->set_coupon_held_keys( $this, $held_keys, $held_keys_for_user );
			}
			if ( $error instanceof Exception ) {
				throw $error;
			}
		}
	}


	/**
	 * Hold coupon if a global usage limit is defined.
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 *
	 * @return string    Meta key which indicates held coupon.
	 * @throws Exception When can't be held.
	 */
	private function hold_coupon( $coupon ) {
		$result = $coupon->get_data_store()->check_and_hold_coupon( $coupon );
		if ( false === $result ) {
			// translators: Actual coupon code.
			throw new Exception( sprintf( __( 'An unexpected error happened while applying the Coupon %s.', 'woocommerce' ), esc_html( $coupon->get_code() ) ) );
		} elseif ( 0 === $result ) {
			// translators: Actual coupon code.
			throw new Exception( sprintf( __( 'Coupon %s was used in another transaction during this checkout, and coupon usage limit is reached. Please remove the coupon and try again.', 'woocommerce' ), esc_html( $coupon->get_code() ) ) );
		}
		return $result;
	}

	/**
	 * Hold coupon if usage limit per customer is defined.
	 *
	 * @param WC_Coupon $coupon              Coupon object.
	 * @param array     $user_ids_and_emails Array of user Id and emails to check for usage limit.
	 * @param string    $user_alias          User ID or email to use to record current usage.
	 *
	 * @return string    Meta key which indicates held coupon.
	 * @throws Exception When coupon can't be held.
	 */
	private function hold_coupon_for_users( $coupon, $user_ids_and_emails, $user_alias ) {
		$result = $coupon->get_data_store()->check_and_hold_coupon_for_user( $coupon, $user_ids_and_emails, $user_alias );
		if ( false === $result ) {
			// translators: Actual coupon code.
			throw new Exception( sprintf( __( 'An unexpected error happened while applying the Coupon %s.', 'woocommerce' ), esc_html( $coupon->get_code() ) ) );
		} elseif ( 0 === $result ) {
			// translators: Actual coupon code.
			throw new Exception( sprintf( __( 'You have used this coupon %s in another transaction during this checkout, and coupon usage limit is reached. Please remove the coupon and try again.', 'woocommerce' ), esc_html( $coupon->get_code() ) ) );
		}
		return $result;
	}

	/**
	 * Helper method to get all aliases for current user and provide billing email.
	 *
	 * @param string $billing_email Billing email provided in form.
	 *
	 * @return array     Array of all aliases.
	 * @throws Exception When validation fails.
	 */
	private function get_billing_and_current_user_aliases( $billing_email ) {
		$emails = array( $billing_email );
		if ( get_current_user_id() ) {
			$emails[] = wp_get_current_user()->user_email;
		}
		$emails              = array_unique(
			array_map( 'strtolower', array_map( 'sanitize_email', $emails ) )
		);
		$customer_data_store = WC_Data_Store::load( 'customer' );
		$user_ids            = $customer_data_store->get_user_ids_for_billing_email( $emails );
		return array_merge( $user_ids, $emails );
	}

	/**
	 * Apply a coupon to the order and recalculate totals.
	 *
	 * @since 3.2.0
	 * @param string|WC_Coupon $raw_coupon Coupon code or object.
	 * @return true|WP_Error True if applied, error if not.
	 */
	public function apply_coupon( $raw_coupon ) {
		if ( is_a( $raw_coupon, 'WC_Coupon' ) ) {
			$coupon = $raw_coupon;
		} elseif ( is_string( $raw_coupon ) ) {
			$code   = wc_format_coupon_code( $raw_coupon );
			$coupon = new WC_Coupon( $code );

			if ( $coupon->get_code() !== $code ) {
				return new WP_Error( 'invalid_coupon', __( 'Invalid coupon code', 'woocommerce' ) );
			}
		} else {
			return new WP_Error( 'invalid_coupon', __( 'Invalid coupon', 'woocommerce' ) );
		}

		// Check to make sure coupon is not already applied.
		$applied_coupons = $this->get_items( 'coupon' );
		foreach ( $applied_coupons as $applied_coupon ) {
			if ( $applied_coupon->get_code() === $coupon->get_code() ) {
				return new WP_Error( 'invalid_coupon', __( 'Coupon code already applied!', 'woocommerce' ) );
			}
		}

		$discounts = new WC_Discounts( $this );
		$applied   = $discounts->apply_coupon( $coupon );

		if ( is_wp_error( $applied ) ) {
			return $applied;
		}

		$data_store = $coupon->get_data_store();

		// Check specific for guest checkouts here as well since WC_Cart handles that separately in check_customer_coupons.
		if ( $data_store && 0 === $this->get_customer_id() ) {
			$usage_count = $data_store->get_usage_by_email( $coupon, $this->get_billing_email() );
			if ( 0 < $coupon->get_usage_limit_per_user() && $usage_count >= $coupon->get_usage_limit_per_user() ) {
				return new WP_Error(
					'invalid_coupon',
					$coupon->get_coupon_error( 106 ),
					array(
						'status' => 400,
					)
				);
			}
		}

		/**
		 * Action to signal that a coupon has been applied to an order.
		 *
		 * @param  WC_Coupon $coupon The applied coupon object.
		 * @param  WC_Order  $order  The current order object.
		 *
		 * @since 7.3
		 */
		do_action( 'woocommerce_order_applied_coupon', $coupon, $this );

		$this->set_coupon_discount_amounts( $discounts );
		$this->save();

		// Recalculate totals and taxes.
		$this->recalculate_coupons();

		// Record usage so counts and validation is correct.
		$used_by = $this->get_user_id();

		if ( ! $used_by ) {
			$used_by = $this->get_billing_email();
		}

		$order_data_store = $this->get_data_store();
		if ( $order_data_store->get_recorded_coupon_usage_counts( $this ) ) {
			$coupon->increase_usage_count( $used_by );
		}

		wc_update_coupon_usage_counts( $this->get_id() );

		return true;
	}

	/**
	 * Remove a coupon from the order and recalculate totals.
	 *
	 * Coupons affect line item totals, but there is no relationship between
	 * coupon and line total, so to remove a coupon we need to work from the
	 * line subtotal (price before discount) and re-apply all coupons in this
	 * order.
	 *
	 * Manual discounts are not affected; those are separate and do not affect
	 * stored line totals.
	 *
	 * @since 3.2.0
	 * @since 7.6.0 Returns a boolean indicating success.
	 *
	 * @param  string $code Coupon code.
	 * @return bool TRUE if coupon was removed, FALSE otherwise.
	 */
	public function remove_coupon( $code ) {
		$coupons = $this->get_items( 'coupon' );

		// Remove the coupon line.
		foreach ( $coupons as $item_id => $coupon ) {
			if ( $coupon->get_code() === $code ) {
				$this->remove_item( $item_id );
				$coupon_object = new WC_Coupon( $code );
				$coupon_object->decrease_usage_count( $this->get_user_id() );
				$this->recalculate_coupons();

				return true;
			}
		}

		return false;
	}

	/**
	 * Apply all coupons in this order again to all line items.
	 * This method is public since WooCommerce 3.8.0.
	 *
	 * @since 3.2.0
	 */
	public function recalculate_coupons() {
		// Reset line item totals.
		foreach ( $this->get_items() as $item ) {
			$item->set_total( $item->get_subtotal() );
			$item->set_total_tax( $item->get_subtotal_tax() );
		}

		$discounts = new WC_Discounts( $this );

		foreach ( $this->get_items( 'coupon' ) as $coupon_item ) {
			$coupon_code = $coupon_item->get_code();
			$coupon_id   = wc_get_coupon_id_by_code( $coupon_code );

			// If we have a coupon ID (loaded via wc_get_coupon_id_by_code) we can simply load the new coupon object using the ID.
			if ( $coupon_id ) {
				$coupon_object = new WC_Coupon( $coupon_id );

			} else {

				// If we do not have a coupon ID (was it virtual? has it been deleted?) we must create a temporary coupon using what data we have stored during checkout.
				$coupon_object = new WC_Coupon();
				$coupon_object->set_props( (array) $coupon_item->get_meta( 'coupon_data', true ) );
				$coupon_object->set_code( $coupon_code );
				$coupon_object->set_virtual( true );

				// If there is no coupon amount (maybe dynamic?), set it to the given **discount** amount so the coupon's same value is applied.
				if ( ! $coupon_object->get_amount() ) {

					// If the order originally had prices including tax, remove the discount + discount tax.
					if ( $this->get_prices_include_tax() ) {
						$coupon_object->set_amount( $coupon_item->get_discount() + $coupon_item->get_discount_tax() );
					} else {
						$coupon_object->set_amount( $coupon_item->get_discount() );
					}
					$coupon_object->set_discount_type( 'fixed_cart' );
				}
			}

			/**
			 * Allow developers to filter this coupon before it gets re-applied to the order.
			 *
			 * @since 3.2.0
			 */
			$coupon_object = apply_filters( 'woocommerce_order_recalculate_coupons_coupon_object', $coupon_object, $coupon_code, $coupon_item, $this );

			if ( $coupon_object ) {
				$discounts->apply_coupon( $coupon_object, false );
			}
		}

		$this->set_coupon_discount_amounts( $discounts );
		$this->set_item_discount_amounts( $discounts );

		// Recalculate totals and taxes.
		$this->calculate_totals( true );
	}

	/**
	 * After applying coupons via the WC_Discounts class, update line items.
	 *
	 * @since 3.2.0
	 * @param WC_Discounts $discounts Discounts class.
	 */
	protected function set_item_discount_amounts( $discounts ) {
		$item_discounts = $discounts->get_discounts_by_item();
		$tax_location   = $this->get_tax_location();
		$tax_location   = array( $tax_location['country'], $tax_location['state'], $tax_location['postcode'], $tax_location['city'] );

		if ( $item_discounts ) {
			foreach ( $item_discounts as $item_id => $amount ) {
				$item = $this->get_item( $item_id, false );

				// If the prices include tax, discounts should be taken off the tax inclusive prices like in the cart.
				if ( $this->get_prices_include_tax() && wc_tax_enabled() && 'taxable' === $item->get_tax_status() ) {
					$taxes = WC_Tax::calc_tax( $amount, $this->get_tax_rates( $item->get_tax_class(), $tax_location ), true );

					// Use unrounded taxes so totals will be re-calculated accurately, like in cart.
					$amount = $amount - array_sum( $taxes );
				}

				$item->set_total( max( 0, $item->get_total() - $amount ) );
			}
		}
	}

	/**
	 * After applying coupons via the WC_Discounts class, update or create coupon items.
	 *
	 * @since 3.2.0
	 * @param WC_Discounts $discounts Discounts class.
	 */
	protected function set_coupon_discount_amounts( $discounts ) {
		$coupons           = $this->get_items( 'coupon' );
		$coupon_code_to_id = wc_list_pluck( $coupons, 'get_id', 'get_code' );
		$all_discounts     = $discounts->get_discounts();
		$coupon_discounts  = $discounts->get_discounts_by_coupon();
		$tax_location      = $this->get_tax_location();
		$tax_location      = array(
			$tax_location['country'],
			$tax_location['state'],
			$tax_location['postcode'],
			$tax_location['city'],
		);

		if ( $coupon_discounts ) {
			foreach ( $coupon_discounts as $coupon_code => $amount ) {
				$item_id = isset( $coupon_code_to_id[ $coupon_code ] ) ? $coupon_code_to_id[ $coupon_code ] : 0;

				if ( ! $item_id ) {
					$coupon_item = new WC_Order_Item_Coupon();
					$coupon_item->set_code( $coupon_code );

					// Add coupon data.
					$coupon_id = wc_get_coupon_id_by_code( $coupon_code );
					$coupon    = new WC_Coupon( $coupon_id );

					// Avoid storing used_by - it's not needed and can get large.
					$coupon_data = $coupon->get_data();
					unset( $coupon_data['used_by'] );

					$coupon_item->add_meta_data( 'coupon_data', $coupon_data );
				} else {
					$coupon_item = $this->get_item( $item_id, false );
				}

				$discount_tax = 0;

				// Work out how much tax has been removed as a result of the discount from this coupon.
				foreach ( $all_discounts[ $coupon_code ] as $item_id => $item_discount_amount ) {
					$item = $this->get_item( $item_id, false );

					if ( 'taxable' !== $item->get_tax_status() || ! wc_tax_enabled() ) {
						continue;
					}

					$taxes = array_sum( WC_Tax::calc_tax( $item_discount_amount, $this->get_tax_rates( $item->get_tax_class(), $tax_location ), $this->get_prices_include_tax() ) );
					if ( 'yes' !== get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
						$taxes = wc_round_tax_total( $taxes );
					}

					$discount_tax += $taxes;

					if ( $this->get_prices_include_tax() ) {
						$amount = $amount - $taxes;
					}
				}

				$coupon_item->set_discount( $amount );
				$coupon_item->set_discount_tax( $discount_tax );

				$this->add_item( $coupon_item );
			}
		}
	}

	/**
	 * Add a product line item to the order. This is the only line item type with
	 * its own method because it saves looking up order amounts (costs are added up for you).
	 *
	 * @param  WC_Product $product Product object.
	 * @param  int        $qty Quantity to add.
	 * @param  array      $args Args for the added product.
	 * @return int
	 */
	public function add_product( $product, $qty = 1, $args = array() ) {
		if ( $product ) {
			$order = ArrayUtil::get_value_or_default( $args, 'order' );
			$total = wc_get_price_excluding_tax(
				$product,
				array(
					'qty'   => $qty,
					'order' => $order,
				)
			);

			$default_args = array(
				'name'         => $product->get_name(),
				'tax_class'    => $product->get_tax_class(),
				'product_id'   => $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(),
				'variation_id' => $product->is_type( 'variation' ) ? $product->get_id() : 0,
				'variation'    => $product->is_type( 'variation' ) ? $product->get_attributes() : array(),
				'subtotal'     => $total,
				'total'        => $total,
				'quantity'     => $qty,
			);
		} else {
			$default_args = array(
				'quantity' => $qty,
			);
		}

		$args = wp_parse_args( $args, $default_args );

		// BW compatibility with old args.
		if ( isset( $args['totals'] ) ) {
			foreach ( $args['totals'] as $key => $value ) {
				if ( 'tax' === $key ) {
					$args['total_tax'] = $value;
				} elseif ( 'tax_data' === $key ) {
					$args['taxes'] = $value;
				} else {
					$args[ $key ] = $value;
				}
			}
		}

		$item = wc_get_container()->get( LegacyProxy::class )->get_instance_of( WC_Order_Item_Product::class );
		$item->set_props( $args );
		$item->set_backorder_meta();
		$item->set_order_id( $this->get_id() );
		$item->save();
		$this->add_item( $item );
		wc_do_deprecated_action( 'woocommerce_order_add_product', array( $this->get_id(), $item->get_id(), $product, $qty, $args ), '3.0', 'woocommerce_new_order_item action instead' );
		delete_transient( 'wc_order_' . $this->get_id() . '_needs_processing' );
		return $item->get_id();
	}

	/*
	|--------------------------------------------------------------------------
	| Payment Token Handling
	|--------------------------------------------------------------------------
	|
	| Payment tokens are hashes used to take payments by certain gateways.
	|
	*/

	/**
	 * Add a payment token to an order
	 *
	 * @since 2.6
	 * @param WC_Payment_Token $token Payment token object.
	 * @return boolean|int The new token ID or false if it failed.
	 */
	public function add_payment_token( $token ) {
		if ( empty( $token ) || ! ( $token instanceof WC_Payment_Token ) ) {
			return false;
		}

		$token_ids   = $this->data_store->get_payment_token_ids( $this );
		$token_ids[] = $token->get_id();
		$this->data_store->update_payment_token_ids( $this, $token_ids );

		do_action( 'woocommerce_payment_token_added_to_order', $this->get_id(), $token->get_id(), $token, $token_ids );
		return $token->get_id();
	}

	/**
	 * Returns a list of all payment tokens associated with the current order
	 *
	 * @since 2.6
	 * @return array An array of payment token objects
	 */
	public function get_payment_tokens() {
		return $this->data_store->get_payment_token_ids( $this );
	}

	/*
	|--------------------------------------------------------------------------
	| Calculations.
	|--------------------------------------------------------------------------
	|
	| These methods calculate order totals and taxes based on the current data.
	|
	*/

	/**
	 * Calculate shipping total.
	 *
	 * @since 2.2
	 * @return float
	 */
	public function calculate_shipping() {
		$shipping_total = 0;

		foreach ( $this->get_shipping_methods() as $shipping ) {
			$shipping_total += $shipping->get_total();
		}

		$this->set_shipping_total( $shipping_total );
		$this->save();

		return $this->get_shipping_total();
	}

	/**
	 * Get all tax classes for items in the order.
	 *
	 * @since 2.6.3
	 * @return array
	 */
	public function get_items_tax_classes() {
		$found_tax_classes = array();

		foreach ( $this->get_items() as $item ) {
			if ( is_callable( array( $item, 'get_tax_status' ) ) && in_array( $item->get_tax_status(), array( 'taxable', 'shipping' ), true ) ) {
				$found_tax_classes[] = $item->get_tax_class();
			}
		}

		return array_unique( $found_tax_classes );
	}

	/**
	 * Get tax location for this order.
	 *
	 * @since 3.2.0
	 * @param array $args array Override the location.
	 * @return array
	 */
	protected function get_tax_location( $args = array() ) {
		$tax_based_on = get_option( 'woocommerce_tax_based_on' );

		if ( 'shipping' === $tax_based_on && ! $this->get_shipping_country() ) {
			$tax_based_on = 'billing';
		}

		$args = wp_parse_args(
			$args,
			array(
				'country'  => 'billing' === $tax_based_on ? $this->get_billing_country() : $this->get_shipping_country(),
				'state'    => 'billing' === $tax_based_on ? $this->get_billing_state() : $this->get_shipping_state(),
				'postcode' => 'billing' === $tax_based_on ? $this->get_billing_postcode() : $this->get_shipping_postcode(),
				'city'     => 'billing' === $tax_based_on ? $this->get_billing_city() : $this->get_shipping_city(),
			)
		);

		/**
		 * Filters whether apply base tax for local pickup shipping method or not.
		 *
		 * @since 6.8.0
		 * @param boolean apply_base_tax Whether apply base tax for local pickup. Default true.
		 */
		$apply_base_tax = true === apply_filters( 'woocommerce_apply_base_tax_for_local_pickup', true );

		/**
		 * Filters local pickup shipping methods.
		 *
		 * @since 6.8.0
		 * @param string[] $local_pickup_methods Local pickup shipping method IDs.
		 */
		$local_pickup_methods = apply_filters( 'woocommerce_local_pickup_methods', array( 'legacy_local_pickup', 'local_pickup' ) );

		$shipping_method_ids = ArrayUtil::select( $this->get_shipping_methods(), 'get_method_id', ArrayUtil::SELECT_BY_OBJECT_METHOD );

		// Set shop base address as a tax location if order has local pickup shipping method.
		if ( $apply_base_tax && count( array_intersect( $shipping_method_ids, $local_pickup_methods ) ) > 0 ) {
			$tax_based_on = 'base';
		}

		// Default to base.
		if ( 'base' === $tax_based_on || empty( $args['country'] ) ) {
			$args['country']  = WC()->countries->get_base_country();
			$args['state']    = WC()->countries->get_base_state();
			$args['postcode'] = WC()->countries->get_base_postcode();
			$args['city']     = WC()->countries->get_base_city();
		}

		return apply_filters( 'woocommerce_order_get_tax_location', $args, $this );
	}

	/**
	 * Public wrapper for exposing get_tax_location() method, enabling 3rd parties to get the tax location for an order.
	 *
	 * @since 7.6.0
	 * @param array $args array Override the location.
	 * @return array
	 */
	public function get_taxable_location( $args = array() ) {
		return $this->get_tax_location( $args );
	}

	/**
	 * Get tax rates for an order. Use order's shipping or billing address, defaults to base location.
	 *
	 * @param string $tax_class     Tax class to get rates for.
	 * @param array  $location_args Location to compute rates for. Should be in form: array( country, state, postcode, city).
	 * @param object $customer      Only used to maintain backward compatibility for filter `woocommerce-matched_rates`.
	 *
	 * @return mixed|void Tax rates.
	 */
	protected function get_tax_rates( $tax_class, $location_args = array(), $customer = null ) {
		$tax_location = $this->get_tax_location( $location_args );
		$tax_location = array( $tax_location['country'], $tax_location['state'], $tax_location['postcode'], $tax_location['city'] );
		return WC_Tax::get_rates_from_location( $tax_class, $tax_location, $customer );
	}

	/**
	 * Calculate taxes for all line items and shipping, and store the totals and tax rows.
	 *
	 * If by default the taxes are based on the shipping address and the current order doesn't
	 * have any, it would use the billing address rather than using the Shopping base location.
	 *
	 * Will use the base country unless customer addresses are set.
	 *
	 * @param array $args Added in 3.0.0 to pass things like location.
	 */
	public function calculate_taxes( $args = array() ) {
		do_action( 'woocommerce_order_before_calculate_taxes', $args, $this );

		$calculate_tax_for  = $this->get_tax_location( $args );
		$shipping_tax_class = get_option( 'woocommerce_shipping_tax_class' );

		if ( 'inherit' === $shipping_tax_class ) {
			$found_classes      = array_intersect( array_merge( array( '' ), WC_Tax::get_tax_class_slugs() ), $this->get_items_tax_classes() );
			$shipping_tax_class = count( $found_classes ) ? current( $found_classes ) : false;
		}

		$is_vat_exempt = apply_filters( 'woocommerce_order_is_vat_exempt', 'yes' === $this->get_meta( 'is_vat_exempt' ), $this );

		// Trigger tax recalculation for all items.
		foreach ( $this->get_items( array( 'line_item', 'fee' ) ) as $item_id => $item ) {
			if ( ! $is_vat_exempt ) {
				$item->calculate_taxes( $calculate_tax_for );
			} else {
				$item->set_taxes( false );
			}
		}

		foreach ( $this->get_shipping_methods() as $item_id => $item ) {
			if ( false !== $shipping_tax_class && ! $is_vat_exempt ) {
				$item->calculate_taxes( array_merge( $calculate_tax_for, array( 'tax_class' => $shipping_tax_class ) ) );
			} else {
				$item->set_taxes( false );
			}
		}

		$this->update_taxes();
	}

	/**
	 * Calculate fees for all line items.
	 *
	 * @return float Fee total.
	 */
	public function get_total_fees() {
		return array_reduce(
			$this->get_fees(),
			function( $carry, $item ) {
				return $carry + $item->get_total();
			}
		);
	}

	/**
	 * Update tax lines for the order based on the line item taxes themselves.
	 */
	public function update_taxes() {
		$cart_taxes     = array();
		$shipping_taxes = array();
		$existing_taxes = $this->get_taxes();
		$saved_rate_ids = array();

		foreach ( $this->get_items( array( 'line_item', 'fee' ) ) as $item_id => $item ) {
			$taxes = $item->get_taxes();
			foreach ( $taxes['total'] as $tax_rate_id => $tax ) {
				$tax_amount = (float) $this->round_line_tax( $tax, false );

				$cart_taxes[ $tax_rate_id ] = isset( $cart_taxes[ $tax_rate_id ] ) ? (float) $cart_taxes[ $tax_rate_id ] + $tax_amount : $tax_amount;
			}
		}

		foreach ( $this->get_shipping_methods() as $item_id => $item ) {
			$taxes = $item->get_taxes();
			foreach ( $taxes['total'] as $tax_rate_id => $tax ) {
				$tax_amount = (float) $tax;

				if ( 'yes' !== get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
					$tax_amount = wc_round_tax_total( $tax_amount );
				}

				$shipping_taxes[ $tax_rate_id ] = isset( $shipping_taxes[ $tax_rate_id ] ) ? $shipping_taxes[ $tax_rate_id ] + $tax_amount : $tax_amount;
			}
		}

		foreach ( $existing_taxes as $tax ) {
			// Remove taxes which no longer exist for cart/shipping.
			if ( ( ! array_key_exists( $tax->get_rate_id(), $cart_taxes ) && ! array_key_exists( $tax->get_rate_id(), $shipping_taxes ) ) || in_array( $tax->get_rate_id(), $saved_rate_ids, true ) ) {
				$this->remove_item( $tax->get_id() );
				continue;
			}
			$saved_rate_ids[] = $tax->get_rate_id();
			$tax->set_rate( $tax->get_rate_id() );
			$tax->set_tax_total( isset( $cart_taxes[ $tax->get_rate_id() ] ) ? $cart_taxes[ $tax->get_rate_id() ] : 0 );
			$tax->set_label( WC_Tax::get_rate_label( $tax->get_rate_id() ) );
			$tax->set_shipping_tax_total( ! empty( $shipping_taxes[ $tax->get_rate_id() ] ) ? $shipping_taxes[ $tax->get_rate_id() ] : 0 );
			$tax->save();
		}

		$new_rate_ids = wp_parse_id_list( array_diff( array_keys( $cart_taxes + $shipping_taxes ), $saved_rate_ids ) );

		// New taxes.
		foreach ( $new_rate_ids as $tax_rate_id ) {
			$item = new WC_Order_Item_Tax();
			$item->set_rate( $tax_rate_id );
			$item->set_tax_total( isset( $cart_taxes[ $tax_rate_id ] ) ? $cart_taxes[ $tax_rate_id ] : 0 );
			$item->set_shipping_tax_total( ! empty( $shipping_taxes[ $tax_rate_id ] ) ? $shipping_taxes[ $tax_rate_id ] : 0 );
			$this->add_item( $item );
		}

		$this->set_shipping_tax( array_sum( $shipping_taxes ) );
		$this->set_cart_tax( array_sum( $cart_taxes ) );
		$this->save();
	}

	/**
	 * Helper function.
	 * If you add all items in this order in cart again, this would be the cart subtotal (assuming all other settings are same).
	 *
	 * @return float Cart subtotal.
	 */
	protected function get_cart_subtotal_for_order() {
		return wc_remove_number_precision(
			$this->get_rounded_items_total(
				$this->get_values_for_total( 'subtotal' )
			)
		);
	}

	/**
	 * Helper function.
	 * If you add all items in this order in cart again, this would be the cart total (assuming all other settings are same).
	 *
	 * @return float Cart total.
	 */
	protected function get_cart_total_for_order() {
		return wc_remove_number_precision(
			$this->get_rounded_items_total(
				$this->get_values_for_total( 'total' )
			)
		);
	}

	/**
	 * Calculate totals by looking at the contents of the order. Stores the totals and returns the orders final total.
	 *
	 * @since 2.2
	 * @param  bool $and_taxes Calc taxes if true.
	 * @return float calculated grand total.
	 */
	public function calculate_totals( $and_taxes = true ) {
		do_action( 'woocommerce_order_before_calculate_totals', $and_taxes, $this );

		$fees_total        = 0;
		$shipping_total    = 0;
		$cart_subtotal_tax = 0;
		$cart_total_tax    = 0;

		$cart_subtotal = $this->get_cart_subtotal_for_order();
		$cart_total    = $this->get_cart_total_for_order();

		// Sum shipping costs.
		foreach ( $this->get_shipping_methods() as $shipping ) {
			$shipping_total += NumberUtil::round( $shipping->get_total(), wc_get_price_decimals() );
		}

		$this->set_shipping_total( $shipping_total );

		// Sum fee costs.
		foreach ( $this->get_fees() as $item ) {
			$fee_total = $item->get_total();

			if ( 0 > $fee_total ) {
				$max_discount = NumberUtil::round( $cart_total + $fees_total + $shipping_total, wc_get_price_decimals() ) * -1;

				if ( $fee_total < $max_discount && 0 > $max_discount ) {
					$item->set_total( $max_discount );
				}
			}
			$fees_total += $item->get_total();
		}

		// Calculate taxes for items, shipping, discounts. Note; this also triggers save().
		if ( $and_taxes ) {
			$this->calculate_taxes();
		}

		// Sum taxes again so we can work out how much tax was discounted. This uses original values, not those possibly rounded to 2dp.
		foreach ( $this->get_items() as $item ) {
			$taxes = $item->get_taxes();

			foreach ( $taxes['total'] as $tax_rate_id => $tax ) {
				$cart_total_tax += (float) $tax;
			}

			foreach ( $taxes['subtotal'] as $tax_rate_id => $tax ) {
				$cart_subtotal_tax += (float) $tax;
			}
		}

		$this->set_discount_total( NumberUtil::round( $cart_subtotal - $cart_total, wc_get_price_decimals() ) );
		$this->set_discount_tax( wc_round_tax_total( $cart_subtotal_tax - $cart_total_tax ) );
		$this->set_total( NumberUtil::round( $cart_total + $fees_total + $this->get_shipping_total() + $this->get_cart_tax() + $this->get_shipping_tax(), wc_get_price_decimals() ) );

		do_action( 'woocommerce_order_after_calculate_totals', $and_taxes, $this );

		$this->save();

		return $this->get_total();
	}

	/**
	 * Get item subtotal - this is the cost before discount.
	 *
	 * @param object $item Item to get total from.
	 * @param bool   $inc_tax (default: false).
	 * @param bool   $round (default: true).
	 * @return float
	 */
	public function get_item_subtotal( $item, $inc_tax = false, $round = true ) {
		$subtotal = 0;

		if ( is_callable( array( $item, 'get_subtotal' ) ) && $item->get_quantity() ) {
			if ( $inc_tax ) {
				$subtotal = ( $item->get_subtotal() + $item->get_subtotal_tax() ) / $item->get_quantity();
			} else {
				$subtotal = floatval( $item->get_subtotal() ) / $item->get_quantity();
			}

			$subtotal = $round ? number_format( (float) $subtotal, wc_get_price_decimals(), '.', '' ) : $subtotal;
		}

		return apply_filters( 'woocommerce_order_amount_item_subtotal', $subtotal, $this, $item, $inc_tax, $round );
	}

	/**
	 * Get line subtotal - this is the cost before discount.
	 *
	 * @param object $item Item to get total from.
	 * @param bool   $inc_tax (default: false).
	 * @param bool   $round (default: true).
	 * @return float
	 */
	public function get_line_subtotal( $item, $inc_tax = false, $round = true ) {
		$subtotal = 0;

		if ( is_callable( array( $item, 'get_subtotal' ) ) ) {
			if ( $inc_tax ) {
				$subtotal = $item->get_subtotal() + $item->get_subtotal_tax();
			} else {
				$subtotal = $item->get_subtotal();
			}

			$subtotal = $round ? NumberUtil::round( $subtotal, wc_get_price_decimals() ) : $subtotal;
		}

		return apply_filters( 'woocommerce_order_amount_line_subtotal', $subtotal, $this, $item, $inc_tax, $round );
	}

	/**
	 * Calculate item cost - useful for gateways.
	 *
	 * @param object $item Item to get total from.
	 * @param bool   $inc_tax (default: false).
	 * @param bool   $round (default: true).
	 * @return float
	 */
	public function get_item_total( $item, $inc_tax = false, $round = true ) {
		$total = 0;

		if ( is_callable( array( $item, 'get_total' ) ) && $item->get_quantity() ) {
			if ( $inc_tax ) {
				$total = ( $item->get_total() + $item->get_total_tax() ) / $item->get_quantity();
			} else {
				$total = floatval( $item->get_total() ) / $item->get_quantity();
			}

			$total = $round ? NumberUtil::round( $total, wc_get_price_decimals() ) : $total;
		}

		return apply_filters( 'woocommerce_order_amount_item_total', $total, $this, $item, $inc_tax, $round );
	}

	/**
	 * Calculate line total - useful for gateways.
	 *
	 * @param object $item Item to get total from.
	 * @param bool   $inc_tax (default: false).
	 * @param bool   $round (default: true).
	 * @return float
	 */
	public function get_line_total( $item, $inc_tax = false, $round = true ) {
		$total = 0;

		if ( is_callable( array( $item, 'get_total' ) ) ) {
			// Check if we need to add line tax to the line total.
			$total = $inc_tax ? $item->get_total() + $item->get_total_tax() : $item->get_total();

			// Check if we need to round.
			$total = $round ? NumberUtil::round( $total, wc_get_price_decimals() ) : $total;
		}

		return apply_filters( 'woocommerce_order_amount_line_total', $total, $this, $item, $inc_tax, $round );
	}

	/**
	 * Get item tax - useful for gateways.
	 *
	 * @param mixed $item Item to get total from.
	 * @param bool  $round (default: true).
	 * @return float
	 */
	public function get_item_tax( $item, $round = true ) {
		$tax = 0;

		if ( is_callable( array( $item, 'get_total_tax' ) ) && $item->get_quantity() ) {
			$tax = $item->get_total_tax() / $item->get_quantity();
			$tax = $round ? wc_round_tax_total( $tax ) : $tax;
		}

		return apply_filters( 'woocommerce_order_amount_item_tax', $tax, $item, $round, $this );
	}

	/**
	 * Get line tax - useful for gateways.
	 *
	 * @param mixed $item Item to get total from.
	 * @return float
	 */
	public function get_line_tax( $item ) {
		return apply_filters( 'woocommerce_order_amount_line_tax', is_callable( array( $item, 'get_total_tax' ) ) ? wc_round_tax_total( $item->get_total_tax() ) : 0, $item, $this );
	}

	/**
	 * Gets line subtotal - formatted for display.
	 *
	 * @param object $item Item to get total from.
	 * @param string $tax_display Incl or excl tax display mode.
	 * @return string
	 */
	public function get_formatted_line_subtotal( $item, $tax_display = '' ) {
		$tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );

		if ( 'excl' === $tax_display ) {
			$ex_tax_label = $this->get_prices_include_tax() ? 1 : 0;

			$subtotal = wc_price(
				$this->get_line_subtotal( $item ),
				array(
					'ex_tax_label' => $ex_tax_label,
					'currency'     => $this->get_currency(),
				)
			);
		} else {
			$subtotal = wc_price( $this->get_line_subtotal( $item, true ), array( 'currency' => $this->get_currency() ) );
		}

		return apply_filters( 'woocommerce_order_formatted_line_subtotal', $subtotal, $item, $this );
	}

	/**
	 * Gets order total - formatted for display.
	 *
	 * @return string
	 */
	public function get_formatted_order_total() {
		$formatted_total = wc_price( $this->get_total(), array( 'currency' => $this->get_currency() ) );
		return apply_filters( 'woocommerce_get_formatted_order_total', $formatted_total, $this );
	}

	/**
	 * Gets subtotal - subtotal is shown before discounts, but with localised taxes.
	 *
	 * @param bool   $compound (default: false).
	 * @param string $tax_display (default: the tax_display_cart value).
	 * @return string
	 */
	public function get_subtotal_to_display( $compound = false, $tax_display = '' ) {
		$tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );
		$subtotal    = $this->get_cart_subtotal_for_order();

		if ( ! $compound ) {

			if ( 'incl' === $tax_display ) {
				$subtotal_taxes = 0;
				foreach ( $this->get_items() as $item ) {
					$subtotal_taxes += self::round_line_tax( $item->get_subtotal_tax(), false );
				}
				$subtotal += wc_round_tax_total( $subtotal_taxes );
			}

			$subtotal = wc_price( $subtotal, array( 'currency' => $this->get_currency() ) );

			if ( 'excl' === $tax_display && $this->get_prices_include_tax() && wc_tax_enabled() ) {
				$subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
			}
		} else {
			if ( 'incl' === $tax_display ) {
				return '';
			}

			// Add Shipping Costs.
			$subtotal += $this->get_shipping_total();

			// Remove non-compound taxes.
			foreach ( $this->get_taxes() as $tax ) {
				if ( $tax->is_compound() ) {
					continue;
				}
				$subtotal = $subtotal + $tax->get_tax_total() + $tax->get_shipping_tax_total();
			}

			// Remove discounts.
			$subtotal = $subtotal - $this->get_total_discount();
			$subtotal = wc_price( $subtotal, array( 'currency' => $this->get_currency() ) );
		}

		return apply_filters( 'woocommerce_order_subtotal_to_display', $subtotal, $compound, $this );
	}

	/**
	 * Gets shipping (formatted).
	 *
	 * @param string $tax_display Excl or incl tax display mode.
	 * @return string
	 */
	public function get_shipping_to_display( $tax_display = '' ) {
		$tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );

		if ( 0 < abs( (float) $this->get_shipping_total() ) ) {

			if ( 'excl' === $tax_display ) {

				// Show shipping excluding tax.
				$shipping = wc_price( $this->get_shipping_total(), array( 'currency' => $this->get_currency() ) );

				if ( (float) $this->get_shipping_tax() > 0 && $this->get_prices_include_tax() ) {
					$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_tax_label', '&nbsp;<small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>', $this, $tax_display );
				}
			} else {

				// Show shipping including tax.
				$shipping = wc_price( $this->get_shipping_total() + $this->get_shipping_tax(), array( 'currency' => $this->get_currency() ) );

				if ( (float) $this->get_shipping_tax() > 0 && ! $this->get_prices_include_tax() ) {
					$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_tax_label', '&nbsp;<small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>', $this, $tax_display );
				}
			}

			/* translators: %s: method */
			$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_shipped_via', '&nbsp;<small class="shipped_via">' . sprintf( __( 'via %s', 'woocommerce' ), $this->get_shipping_method() ) . '</small>', $this );

		} elseif ( $this->get_shipping_method() ) {
			$shipping = $this->get_shipping_method();
		} else {
			$shipping = __( 'Free!', 'woocommerce' );
		}

		return apply_filters( 'woocommerce_order_shipping_to_display', $shipping, $this, $tax_display );
	}

	/**
	 * Get the discount amount (formatted).
	 *
	 * @since  2.3.0
	 * @param string $tax_display Excl or incl tax display mode.
	 * @return string
	 */
	public function get_discount_to_display( $tax_display = '' ) {
		$tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );

		/**
		 * Filter the discount amount to display.
		 *
		 * @since 2.7.0.
		 */
		return apply_filters( 'woocommerce_order_discount_to_display', wc_price( $this->get_total_discount( 'excl' === $tax_display ), array( 'currency' => $this->get_currency() ) ), $this );
	}

	/**
	 * Add total row for subtotal.
	 *
	 * @param array  $total_rows Reference to total rows array.
	 * @param string $tax_display Excl or incl tax display mode.
	 */
	protected function add_order_item_totals_subtotal_row( &$total_rows, $tax_display ) {
		$subtotal = $this->get_subtotal_to_display( false, $tax_display );

		if ( $subtotal ) {
			$total_rows['cart_subtotal'] = array(
				'label' => __( 'Subtotal:', 'woocommerce' ),
				'value' => $subtotal,
			);
		}
	}

	/**
	 * Add total row for discounts.
	 *
	 * @param array  $total_rows Reference to total rows array.
	 * @param string $tax_display Excl or incl tax display mode.
	 */
	protected function add_order_item_totals_discount_row( &$total_rows, $tax_display ) {
		if ( $this->get_total_discount() > 0 ) {
			$total_rows['discount'] = array(
				'label' => __( 'Discount:', 'woocommerce' ),
				'value' => '-' . $this->get_discount_to_display( $tax_display ),
			);
		}
	}

	/**
	 * Add total row for shipping.
	 *
	 * @param array  $total_rows Reference to total rows array.
	 * @param string $tax_display Excl or incl tax display mode.
	 */
	protected function add_order_item_totals_shipping_row( &$total_rows, $tax_display ) {
		if ( $this->get_shipping_method() ) {
			$total_rows['shipping'] = array(
				'label' => __( 'Shipping:', 'woocommerce' ),
				'value' => $this->get_shipping_to_display( $tax_display ),
			);
		}
	}

	/**
	 * Add total row for fees.
	 *
	 * @param array  $total_rows Reference to total rows array.
	 * @param string $tax_display Excl or incl tax display mode.
	 */
	protected function add_order_item_totals_fee_rows( &$total_rows, $tax_display ) {
		$fees = $this->get_fees();

		if ( $fees ) {
			foreach ( $fees as $id => $fee ) {
				if ( apply_filters( 'woocommerce_get_order_item_totals_excl_free_fees', empty( $fee['line_total'] ) && empty( $fee['line_tax'] ), $id ) ) {
					continue;
				}
				$total_rows[ 'fee_' . $fee->get_id() ] = array(
					'label' => $fee->get_name() . ':',
					'value' => wc_price( 'excl' === $tax_display ? $fee->get_total() : $fee->get_total() + $fee->get_total_tax(), array( 'currency' => $this->get_currency() ) ),
				);
			}
		}
	}

	/**
	 * Add total row for taxes.
	 *
	 * @param array  $total_rows Reference to total rows array.
	 * @param string $tax_display Excl or incl tax display mode.
	 */
	protected function add_order_item_totals_tax_rows( &$total_rows, $tax_display ) {
		// Tax for tax exclusive prices.
		if ( 'excl' === $tax_display && wc_tax_enabled() ) {
			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( $this->get_tax_totals() as $code => $tax ) {
					$total_rows[ sanitize_title( $code ) ] = array(
						'label' => $tax->label . ':',
						'value' => $tax->formatted_amount,
					);
				}
			} else {
				$total_rows['tax'] = array(
					'label' => WC()->countries->tax_or_vat() . ':',
					'value' => wc_price( $this->get_total_tax(), array( 'currency' => $this->get_currency() ) ),
				);
			}
		}
	}

	/**
	 * Add total row for grand total.
	 *
	 * @param array  $total_rows Reference to total rows array.
	 * @param string $tax_display Excl or incl tax display mode.
	 */
	protected function add_order_item_totals_total_row( &$total_rows, $tax_display ) {
		$total_rows['order_total'] = array(
			'label' => __( 'Total:', 'woocommerce' ),
			'value' => $this->get_formatted_order_total( $tax_display ),
		);
	}

	/**
	 * Get totals for display on pages and in emails.
	 *
	 * @param mixed $tax_display Excl or incl tax display mode.
	 * @return array
	 */
	public function get_order_item_totals( $tax_display = '' ) {
		$tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );
		$total_rows  = array();

		$this->add_order_item_totals_subtotal_row( $total_rows, $tax_display );
		$this->add_order_item_totals_discount_row( $total_rows, $tax_display );
		$this->add_order_item_totals_shipping_row( $total_rows, $tax_display );
		$this->add_order_item_totals_fee_rows( $total_rows, $tax_display );
		$this->add_order_item_totals_tax_rows( $total_rows, $tax_display );
		$this->add_order_item_totals_total_row( $total_rows, $tax_display );

		return apply_filters( 'woocommerce_get_order_item_totals', $total_rows, $this, $tax_display );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	|
	| Checks if a condition is true or false.
	|
	*/

	/**
	 * Checks the order status against a passed in status.
	 *
	 * @param array|string $status Status to check.
	 * @return bool
	 */
	public function has_status( $status ) {
		return apply_filters( 'woocommerce_order_has_status', ( is_array( $status ) && in_array( $this->get_status(), $status, true ) ) || $this->get_status() === $status, $this, $status );
	}

	/**
	 * Check whether this order has a specific shipping method or not.
	 *
	 * @param string $method_id Method ID to check.
	 * @return bool
	 */
	public function has_shipping_method( $method_id ) {
		foreach ( $this->get_shipping_methods() as $shipping_method ) {
			if ( strpos( $shipping_method->get_method_id(), $method_id ) === 0 ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns true if the order contains a free product.
	 *
	 * @since 2.5.0
	 * @return bool
	 */
	public function has_free_item() {
		foreach ( $this->get_items() as $item ) {
			if ( ! $item->get_total() ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get order title.
	 *
	 * @return string Order title.
	 */
	public function get_title() : string {
		if ( method_exists( $this->data_store, 'get_title' ) ) {
			return $this->data_store->get_title( $this );
		} else {
			return __( 'Order', 'woocommerce' );
		}
	}
}

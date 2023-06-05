<?php
/**
 * Discount calculation
 *
 * @package WooCommerce\Classes
 * @since   3.2.0
 */

use Automattic\WooCommerce\Utilities\NumberUtil;
use Automattic\WooCommerce\Utilities\StringUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Discounts class.
 */
class WC_Discounts {

	/**
	 * Reference to cart or order object.
	 *
	 * @since 3.2.0
	 * @var WC_Cart|WC_Order
	 */
	protected $object;

	/**
	 * An array of items to discount.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * An array of discounts which have been applied to items.
	 *
	 * @var array[] Code => Item Key => Value
	 */
	protected $discounts = array();

	/**
	 * WC_Discounts Constructor.
	 *
	 * @param WC_Cart|WC_Order $object Cart or order object.
	 */
	public function __construct( $object = null ) {
		if ( is_a( $object, 'WC_Cart' ) ) {
			$this->set_items_from_cart( $object );
		} elseif ( is_a( $object, 'WC_Order' ) ) {
			$this->set_items_from_order( $object );
		}
	}

	/**
	 * Set items directly. Used by WC_Cart_Totals.
	 *
	 * @since 3.2.3
	 * @param array $items Items to set.
	 */
	public function set_items( $items ) {
		$this->items     = $items;
		$this->discounts = array();
		uasort( $this->items, array( $this, 'sort_by_price' ) );
	}

	/**
	 * Normalise cart items which will be discounted.
	 *
	 * @since 3.2.0
	 * @param WC_Cart $cart Cart object.
	 */
	public function set_items_from_cart( $cart ) {
		$this->items     = array();
		$this->discounts = array();

		if ( ! is_a( $cart, 'WC_Cart' ) ) {
			return;
		}

		$this->object = $cart;

		foreach ( $cart->get_cart() as $key => $cart_item ) {
			$item                = new stdClass();
			$item->key           = $key;
			$item->object        = $cart_item;
			$item->product       = $cart_item['data'];
			$item->quantity      = $cart_item['quantity'];
			$item->price         = wc_add_number_precision_deep( (float) $item->product->get_price() * (float) $item->quantity );
			$this->items[ $key ] = $item;
		}

		uasort( $this->items, array( $this, 'sort_by_price' ) );
	}

	/**
	 * Normalise order items which will be discounted.
	 *
	 * @since 3.2.0
	 * @param WC_Order $order Order object.
	 */
	public function set_items_from_order( $order ) {
		$this->items     = array();
		$this->discounts = array();

		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		$this->object = $order;

		foreach ( $order->get_items() as $order_item ) {
			$item           = new stdClass();
			$item->key      = $order_item->get_id();
			$item->object   = $order_item;
			$item->product  = $order_item->get_product();
			$item->quantity = $order_item->get_quantity();
			$item->price    = wc_add_number_precision_deep( $order_item->get_subtotal() );

			if ( $order->get_prices_include_tax() ) {
				$item->price += wc_add_number_precision_deep( $order_item->get_subtotal_tax() );
			}

			$this->items[ $order_item->get_id() ] = $item;
		}

		uasort( $this->items, array( $this, 'sort_by_price' ) );
	}

	/**
	 * Get the object concerned.
	 *
	 * @since  3.3.2
	 * @return object
	 */
	public function get_object() {
		return $this->object;
	}

	/**
	 * Get items.
	 *
	 * @since  3.2.0
	 * @return object[]
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Get items to validate.
	 *
	 * @since  3.3.2
	 * @return object[]
	 */
	public function get_items_to_validate() {
		return apply_filters( 'woocommerce_coupon_get_items_to_validate', $this->get_items(), $this );
	}

	/**
	 * Get discount by key with or without precision.
	 *
	 * @since  3.2.0
	 * @param  string $key name of discount row to return.
	 * @param  bool   $in_cents Should the totals be returned in cents, or without precision.
	 * @return float
	 */
	public function get_discount( $key, $in_cents = false ) {
		$item_discount_totals = $this->get_discounts_by_item( $in_cents );
		return isset( $item_discount_totals[ $key ] ) ? $item_discount_totals[ $key ] : 0;
	}

	/**
	 * Get all discount totals.
	 *
	 * @since  3.2.0
	 * @param  bool $in_cents Should the totals be returned in cents, or without precision.
	 * @return array
	 */
	public function get_discounts( $in_cents = false ) {
		$discounts = $this->discounts;
		return $in_cents ? $discounts : wc_remove_number_precision_deep( $discounts );
	}

	/**
	 * Get all discount totals per item.
	 *
	 * @since  3.2.0
	 * @param  bool $in_cents Should the totals be returned in cents, or without precision.
	 * @return array
	 */
	public function get_discounts_by_item( $in_cents = false ) {
		$discounts            = $this->discounts;
		$item_discount_totals = (array) array_shift( $discounts );

		foreach ( $discounts as $item_discounts ) {
			foreach ( $item_discounts as $item_key => $item_discount ) {
				$item_discount_totals[ $item_key ] += $item_discount;
			}
		}

		return $in_cents ? $item_discount_totals : wc_remove_number_precision_deep( $item_discount_totals );
	}

	/**
	 * Get all discount totals per coupon.
	 *
	 * @since  3.2.0
	 * @param  bool $in_cents Should the totals be returned in cents, or without precision.
	 * @return array
	 */
	public function get_discounts_by_coupon( $in_cents = false ) {
		$coupon_discount_totals = array_map( 'array_sum', $this->discounts );

		return $in_cents ? $coupon_discount_totals : wc_remove_number_precision_deep( $coupon_discount_totals );
	}

	/**
	 * Get discounted price of an item without precision.
	 *
	 * @since  3.2.0
	 * @param  object $item Get data for this item.
	 * @return float
	 */
	public function get_discounted_price( $item ) {
		return wc_remove_number_precision_deep( $this->get_discounted_price_in_cents( $item ) );
	}

	/**
	 * Get discounted price of an item to precision (in cents).
	 *
	 * @since  3.2.0
	 * @param  object $item Get data for this item.
	 * @return int
	 */
	public function get_discounted_price_in_cents( $item ) {
		return absint( NumberUtil::round( $item->price - $this->get_discount( $item->key, true ) ) );
	}

	/**
	 * Apply a discount to all items using a coupon.
	 *
	 * @since  3.2.0
	 * @param  WC_Coupon $coupon Coupon object being applied to the items.
	 * @param  bool      $validate Set to false to skip coupon validation.
	 * @throws Exception Error message when coupon isn't valid.
	 * @return bool|WP_Error True if applied or WP_Error instance in failure.
	 */
	public function apply_coupon( $coupon, $validate = true ) {
		if ( ! is_a( $coupon, 'WC_Coupon' ) ) {
			return new WP_Error( 'invalid_coupon', __( 'Invalid coupon', 'woocommerce' ) );
		}

		$is_coupon_valid = $validate ? $this->is_coupon_valid( $coupon ) : true;

		if ( is_wp_error( $is_coupon_valid ) ) {
			return $is_coupon_valid;
		}

		$coupon_code = $coupon->get_code();
		if ( StringUtil::is_null_or_whitespace( $this->discounts[ $coupon_code ] ?? null ) ) {
			$this->discounts[ $coupon_code ] = array_fill_keys( array_keys( $this->items ), 0 );
		}

		$items_to_apply = $this->get_items_to_apply_coupon( $coupon );

		// Core discounts are handled here as of 3.2.
		switch ( $coupon->get_discount_type() ) {
			case 'percent':
				$this->apply_coupon_percent( $coupon, $items_to_apply );
				break;
			case 'fixed_product':
				$this->apply_coupon_fixed_product( $coupon, $items_to_apply );
				break;
			case 'fixed_cart':
				$this->apply_coupon_fixed_cart( $coupon, $items_to_apply );
				break;
			default:
				$this->apply_coupon_custom( $coupon, $items_to_apply );
				break;
		}

		return true;
	}

	/**
	 * Sort by price.
	 *
	 * @since  3.2.0
	 * @param  array $a First element.
	 * @param  array $b Second element.
	 * @return int
	 */
	protected function sort_by_price( $a, $b ) {
		$price_1 = $a->price * $a->quantity;
		$price_2 = $b->price * $b->quantity;
		if ( $price_1 === $price_2 ) {
			return 0;
		}
		return ( $price_1 < $price_2 ) ? 1 : -1;
	}

	/**
	 * Filter out all products which have been fully discounted to 0.
	 * Used as array_filter callback.
	 *
	 * @since  3.2.0
	 * @param  object $item Get data for this item.
	 * @return bool
	 */
	protected function filter_products_with_price( $item ) {
		return $this->get_discounted_price_in_cents( $item ) > 0;
	}

	/**
	 * Get items which the coupon should be applied to.
	 *
	 * @since  3.2.0
	 * @param  object $coupon Coupon object.
	 * @return array
	 */
	protected function get_items_to_apply_coupon( $coupon ) {
		$items_to_apply = array();

		foreach ( $this->get_items_to_validate() as $item ) {
			$item_to_apply = clone $item; // Clone the item so changes to this item do not affect the originals.

			if ( 0 === $this->get_discounted_price_in_cents( $item_to_apply ) || 0 >= $item_to_apply->quantity ) {
				continue;
			}

			if ( ! $coupon->is_valid_for_product( $item_to_apply->product, $item_to_apply->object ) && ! $coupon->is_valid_for_cart() ) {
				continue;
			}

			$items_to_apply[] = $item_to_apply;
		}
		return $items_to_apply;
	}

	/**
	 * Apply percent discount to items and return an array of discounts granted.
	 *
	 * @since  3.2.0
	 * @param  WC_Coupon $coupon Coupon object. Passed through filters.
	 * @param  array     $items_to_apply Array of items to apply the coupon to.
	 * @return int Total discounted.
	 */
	protected function apply_coupon_percent( $coupon, $items_to_apply ) {
		$total_discount        = 0;
		$cart_total            = 0;
		$limit_usage_qty       = 0;
		$applied_count         = 0;
		$adjust_final_discount = true;

		if ( null !== $coupon->get_limit_usage_to_x_items() ) {
			$limit_usage_qty = $coupon->get_limit_usage_to_x_items();
		}

		$coupon_amount = $coupon->get_amount();

		foreach ( $items_to_apply as $item ) {
			// Find out how much price is available to discount for the item.
			$discounted_price = $this->get_discounted_price_in_cents( $item );

			// Get the price we actually want to discount, based on settings.
			$price_to_discount = ( 'yes' === get_option( 'woocommerce_calc_discounts_sequentially', 'no' ) ) ? $discounted_price : NumberUtil::round( $item->price );

			// See how many and what price to apply to.
			$apply_quantity    = $limit_usage_qty && ( $limit_usage_qty - $applied_count ) < $item->quantity ? $limit_usage_qty - $applied_count : $item->quantity;
			$apply_quantity    = max( 0, apply_filters( 'woocommerce_coupon_get_apply_quantity', $apply_quantity, $item, $coupon, $this ) );
			$price_to_discount = ( $price_to_discount / $item->quantity ) * $apply_quantity;

			// Run coupon calculations.
			$discount = floor( $price_to_discount * ( $coupon_amount / 100 ) );

			if ( is_a( $this->object, 'WC_Cart' ) && has_filter( 'woocommerce_coupon_get_discount_amount' ) ) {
				// Send through the legacy filter, but not as cents.
				$filtered_discount = wc_add_number_precision( apply_filters( 'woocommerce_coupon_get_discount_amount', wc_remove_number_precision( $discount ), wc_remove_number_precision( $price_to_discount ), $item->object, false, $coupon ) );

				if ( $filtered_discount !== $discount ) {
					$discount              = $filtered_discount;
					$adjust_final_discount = false;
				}
			}

			$discount       = wc_round_discount( min( $discounted_price, $discount ), 0 );
			$cart_total     = $cart_total + $price_to_discount;
			$total_discount = $total_discount + $discount;
			$applied_count  = $applied_count + $apply_quantity;

			// Store code and discount amount per item.
			$this->discounts[ $coupon->get_code() ][ $item->key ] += $discount;
		}

		// Work out how much discount would have been given to the cart as a whole and compare to what was discounted on all line items.
		$cart_total_discount = wc_round_discount( $cart_total * ( $coupon_amount / 100 ), 0 );

		if ( $total_discount < $cart_total_discount && $adjust_final_discount ) {
			$total_discount += $this->apply_coupon_remainder( $coupon, $items_to_apply, $cart_total_discount - $total_discount );
		}

		return $total_discount;
	}

	/**
	 * Apply fixed product discount to items.
	 *
	 * @since  3.2.0
	 * @param  WC_Coupon $coupon Coupon object. Passed through filters.
	 * @param  array     $items_to_apply Array of items to apply the coupon to.
	 * @param  int       $amount Fixed discount amount to apply in cents. Leave blank to pull from coupon.
	 * @return int Total discounted.
	 */
	protected function apply_coupon_fixed_product( $coupon, $items_to_apply, $amount = null ) {
		$total_discount  = 0;
		$amount          = $amount ? $amount : wc_add_number_precision( $coupon->get_amount() );
		$limit_usage_qty = 0;
		$applied_count   = 0;

		if ( null !== $coupon->get_limit_usage_to_x_items() ) {
			$limit_usage_qty = $coupon->get_limit_usage_to_x_items();
		}

		foreach ( $items_to_apply as $item ) {
			// Find out how much price is available to discount for the item.
			$discounted_price = $this->get_discounted_price_in_cents( $item );

			// Get the price we actually want to discount, based on settings.
			$price_to_discount = ( 'yes' === get_option( 'woocommerce_calc_discounts_sequentially', 'no' ) ) ? $discounted_price : $item->price;

			// Run coupon calculations.
			if ( $limit_usage_qty ) {
				$apply_quantity = $limit_usage_qty - $applied_count < $item->quantity ? $limit_usage_qty - $applied_count : $item->quantity;
				$apply_quantity = max( 0, apply_filters( 'woocommerce_coupon_get_apply_quantity', $apply_quantity, $item, $coupon, $this ) );
				$discount       = min( $amount, $item->price / $item->quantity ) * $apply_quantity;
			} else {
				$apply_quantity = apply_filters( 'woocommerce_coupon_get_apply_quantity', $item->quantity, $item, $coupon, $this );
				$discount       = $amount * $apply_quantity;
			}

			if ( is_a( $this->object, 'WC_Cart' ) && has_filter( 'woocommerce_coupon_get_discount_amount' ) ) {
				// Send through the legacy filter, but not as cents.
				$discount = wc_add_number_precision( apply_filters( 'woocommerce_coupon_get_discount_amount', wc_remove_number_precision( $discount ), wc_remove_number_precision( $price_to_discount ), $item->object, false, $coupon ) );
			}

			$discount       = min( $discounted_price, $discount );
			$total_discount = $total_discount + $discount;
			$applied_count  = $applied_count + $apply_quantity;

			// Store code and discount amount per item.
			$this->discounts[ $coupon->get_code() ][ $item->key ] += $discount;
		}
		return $total_discount;
	}

	/**
	 * Apply fixed cart discount to items.
	 *
	 * @since  3.2.0
	 * @param  WC_Coupon $coupon Coupon object. Passed through filters.
	 * @param  array     $items_to_apply Array of items to apply the coupon to.
	 * @param  int       $amount Fixed discount amount to apply in cents. Leave blank to pull from coupon.
	 * @return int Total discounted.
	 */
	protected function apply_coupon_fixed_cart( $coupon, $items_to_apply, $amount = null ) {
		$total_discount = 0;
		$amount         = $amount ? $amount : wc_add_number_precision( $coupon->get_amount() );
		$items_to_apply = array_filter( $items_to_apply, array( $this, 'filter_products_with_price' ) );
		$item_count     = array_sum( wp_list_pluck( $items_to_apply, 'quantity' ) );

		if ( ! $item_count ) {
			return $total_discount;
		}

		if ( ! $amount ) {
			// If there is no amount we still send it through so filters are fired.
			$total_discount = $this->apply_coupon_fixed_product( $coupon, $items_to_apply, 0 );
		} else {
			$per_item_discount = absint( $amount / $item_count ); // round it down to the nearest cent.

			if ( $per_item_discount > 0 ) {
				$total_discount = $this->apply_coupon_fixed_product( $coupon, $items_to_apply, $per_item_discount );

				/**
				 * If there is still discount remaining, repeat the process.
				 */
				if ( $total_discount > 0 && $total_discount < $amount ) {
					$total_discount += $this->apply_coupon_fixed_cart( $coupon, $items_to_apply, $amount - $total_discount );
				}
			} elseif ( $amount > 0 ) {
				$total_discount += $this->apply_coupon_remainder( $coupon, $items_to_apply, $amount );
			}
		}
		return $total_discount;
	}

	/**
	 * Apply custom coupon discount to items.
	 *
	 * @since  3.3
	 * @param  WC_Coupon $coupon Coupon object. Passed through filters.
	 * @param  array     $items_to_apply Array of items to apply the coupon to.
	 * @return int Total discounted.
	 */
	protected function apply_coupon_custom( $coupon, $items_to_apply ) {
		$limit_usage_qty = 0;
		$applied_count   = 0;

		if ( null !== $coupon->get_limit_usage_to_x_items() ) {
			$limit_usage_qty = $coupon->get_limit_usage_to_x_items();
		}

		// Apply the coupon to each item.
		foreach ( $items_to_apply as $item ) {
			// Find out how much price is available to discount for the item.
			$discounted_price = $this->get_discounted_price_in_cents( $item );

			// Get the price we actually want to discount, based on settings.
			$price_to_discount = wc_remove_number_precision( ( 'yes' === get_option( 'woocommerce_calc_discounts_sequentially', 'no' ) ) ? $discounted_price : $item->price );

			// See how many and what price to apply to.
			$apply_quantity = $limit_usage_qty && ( $limit_usage_qty - $applied_count ) < $item->quantity ? $limit_usage_qty - $applied_count : $item->quantity;
			$apply_quantity = max( 0, apply_filters( 'woocommerce_coupon_get_apply_quantity', $apply_quantity, $item, $coupon, $this ) );

			// Run coupon calculations.
			$discount      = wc_add_number_precision( $coupon->get_discount_amount( $price_to_discount / $item->quantity, $item->object, true ) ) * $apply_quantity;
			$discount      = wc_round_discount( min( $discounted_price, $discount ), 0 );
			$applied_count = $applied_count + $apply_quantity;

			// Store code and discount amount per item.
			$this->discounts[ $coupon->get_code() ][ $item->key ] += $discount;
		}

		// Allow post-processing for custom coupon types (e.g. calculating discrepancy, etc).
		$this->discounts[ $coupon->get_code() ] = apply_filters( 'woocommerce_coupon_custom_discounts_array', $this->discounts[ $coupon->get_code() ], $coupon );

		return array_sum( $this->discounts[ $coupon->get_code() ] );
	}

	/**
	 * Deal with remaining fractional discounts by splitting it over items
	 * until the amount is expired, discounting 1 cent at a time.
	 *
	 * @since 3.2.0
	 * @param  WC_Coupon $coupon Coupon object if applicable. Passed through filters.
	 * @param  array     $items_to_apply Array of items to apply the coupon to.
	 * @param  int       $amount Fixed discount amount to apply.
	 * @return int Total discounted.
	 */
	protected function apply_coupon_remainder( $coupon, $items_to_apply, $amount ) {
		$total_discount = 0;

		foreach ( $items_to_apply as $item ) {
			for ( $i = 0; $i < $item->quantity; $i ++ ) {
				// Find out how much price is available to discount for the item.
				$price_to_discount = $this->get_discounted_price_in_cents( $item );

				// Run coupon calculations.
				$discount = min( $price_to_discount, 1 );

				// Store totals.
				$total_discount += $discount;

				// Store code and discount amount per item.
				$this->discounts[ $coupon->get_code() ][ $item->key ] += $discount;

				if ( $total_discount >= $amount ) {
					break 2;
				}
			}
			if ( $total_discount >= $amount ) {
				break;
			}
		}
		return $total_discount;
	}

	/**
	 * Ensure coupon exists or throw exception.
	 *
	 * A coupon is also considered to no longer exist if it has been placed in the trash, even if the trash has not yet
	 * been emptied.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_exists( $coupon ) {
		if ( ( ! $coupon->get_id() && ! $coupon->get_virtual() ) || 'trash' === $coupon->get_status() ) {
			/* translators: %s: coupon code */
			throw new Exception( sprintf( __( 'Coupon "%s" does not exist!', 'woocommerce' ), esc_html( $coupon->get_code() ) ), 105 );
		}

		return true;
	}

	/**
	 * Ensure coupon usage limit is valid or throw exception.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_usage_limit( $coupon ) {
		if ( ! $coupon->get_usage_limit() ) {
			return true;
		}
		$usage_count           = $coupon->get_usage_count();
		$data_store            = $coupon->get_data_store();
		$tentative_usage_count = is_callable( array( $data_store, 'get_tentative_usage_count' ) ) ? $data_store->get_tentative_usage_count( $coupon->get_id() ) : 0;
		if ( $usage_count + $tentative_usage_count < $coupon->get_usage_limit() ) {
			// All good.
			return true;
		}
		// Coupon usage limit is reached. Let's show as informative error message as we can.
		if ( 0 === $tentative_usage_count ) {
			// No held coupon, usage limit is indeed reached.
			$error_code = WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED;
		} elseif ( is_user_logged_in() ) {
			$recent_pending_orders = wc_get_orders(
				array(
					'limit'       => 1,
					'post_status' => array( 'wc-failed', 'wc-pending' ),
					'customer'    => get_current_user_id(),
					'return'      => 'ids',
				)
			);
			if ( count( $recent_pending_orders ) > 0 ) {
				// User logged in and have a pending order, maybe they are trying to use the coupon.
				$error_code = WC_Coupon::E_WC_COUPON_USAGE_LIMIT_COUPON_STUCK;
			} else {
				$error_code = WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED;
			}
		} else {
			// Maybe this user was trying to use the coupon but got stuck. We can't know for sure (performantly). Show a slightly better error message.
			$error_code = WC_Coupon::E_WC_COUPON_USAGE_LIMIT_COUPON_STUCK_GUEST;
		}
		throw new Exception( $coupon->get_coupon_error( $error_code ), $error_code );
	}

	/**
	 * Ensure coupon user usage limit is valid or throw exception.
	 *
	 * Per user usage limit - check here if user is logged in (against user IDs).
	 * Checked again for emails later on in WC_Cart::check_customer_coupons().
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon  Coupon data.
	 * @param  int       $user_id User ID.
	 * @return bool
	 */
	protected function validate_coupon_user_usage_limit( $coupon, $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			if ( $this->object instanceof WC_Order ) {
				$user_id = $this->object->get_customer_id();
			} else {
				$user_id = get_current_user_id();
			}
		}

		if ( $coupon && $user_id && apply_filters( 'woocommerce_coupon_validate_user_usage_limit', $coupon->get_usage_limit_per_user() > 0, $user_id, $coupon, $this ) && $coupon->get_id() && $coupon->get_data_store() ) {
			$data_store  = $coupon->get_data_store();
			$usage_count = $data_store->get_usage_by_user_id( $coupon, $user_id );
			if ( $usage_count >= $coupon->get_usage_limit_per_user() ) {
				if ( $data_store->get_tentative_usages_for_user( $coupon->get_id(), array( $user_id ) ) > 0 ) {
					$error_message = $coupon->get_coupon_error( WC_Coupon::E_WC_COUPON_USAGE_LIMIT_COUPON_STUCK );
					$error_code    = WC_Coupon::E_WC_COUPON_USAGE_LIMIT_COUPON_STUCK;
				} else {
					$error_message = $coupon->get_coupon_error( WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED );
					$error_code    = WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED;
				}
				throw new Exception( $error_message, $error_code );
			}
		}

		return true;
	}

	/**
	 * Ensure coupon date is valid or throw exception.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_expiry_date( $coupon ) {
		if ( $coupon->get_date_expires() && apply_filters( 'woocommerce_coupon_validate_expiry_date', time() > $coupon->get_date_expires()->getTimestamp(), $coupon, $this ) ) {
			throw new Exception( __( 'This coupon has expired.', 'woocommerce' ), 107 );
		}

		return true;
	}

	/**
	 * Ensure coupon amount is valid or throw exception.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon   Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_minimum_amount( $coupon ) {
		$subtotal = wc_remove_number_precision( $this->get_object_subtotal() );

		if ( $coupon->get_minimum_amount() > 0 && apply_filters( 'woocommerce_coupon_validate_minimum_amount', $coupon->get_minimum_amount() > $subtotal, $coupon, $subtotal ) ) {
			/* translators: %s: coupon minimum amount */
			throw new Exception( sprintf( __( 'The minimum spend for this coupon is %s.', 'woocommerce' ), wc_price( $coupon->get_minimum_amount() ) ), 108 );
		}

		return true;
	}

	/**
	 * Ensure coupon amount is valid or throw exception.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon   Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_maximum_amount( $coupon ) {
		$subtotal = wc_remove_number_precision( $this->get_object_subtotal() );

		if ( $coupon->get_maximum_amount() > 0 && apply_filters( 'woocommerce_coupon_validate_maximum_amount', $coupon->get_maximum_amount() < $subtotal, $coupon ) ) {
			/* translators: %s: coupon maximum amount */
			throw new Exception( sprintf( __( 'The maximum spend for this coupon is %s.', 'woocommerce' ), wc_price( $coupon->get_maximum_amount() ) ), 112 );
		}

		return true;
	}

	/**
	 * Ensure coupon is valid for products in the list is valid or throw exception.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_product_ids( $coupon ) {
		if ( count( $coupon->get_product_ids() ) > 0 ) {
			$valid = false;

			foreach ( $this->get_items_to_validate() as $item ) {
				if ( $item->product && ( in_array( $item->product->get_id(), $coupon->get_product_ids(), true ) || in_array( $item->product->get_parent_id(), $coupon->get_product_ids(), true ) ) ) {
					$valid = true;
					break;
				}
			}

			if ( ! $valid ) {
				throw new Exception( __( 'Sorry, this coupon is not applicable to selected products.', 'woocommerce' ), 109 );
			}
		}

		return true;
	}

	/**
	 * Ensure coupon is valid for product categories in the list is valid or throw exception.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_product_categories( $coupon ) {
		if ( count( $coupon->get_product_categories() ) > 0 ) {
			$valid = false;

			foreach ( $this->get_items_to_validate() as $item ) {
				if ( $coupon->get_exclude_sale_items() && $item->product && $item->product->is_on_sale() ) {
					continue;
				}

				$product_cats = wc_get_product_cat_ids( $item->product->get_id() );

				if ( $item->product->get_parent_id() ) {
					$product_cats = array_merge( $product_cats, wc_get_product_cat_ids( $item->product->get_parent_id() ) );
				}

				// If we find an item with a cat in our allowed cat list, the coupon is valid.
				if ( count( array_intersect( $product_cats, $coupon->get_product_categories() ) ) > 0 ) {
					$valid = true;
					break;
				}
			}

			if ( ! $valid ) {
				throw new Exception( __( 'Sorry, this coupon is not applicable to selected products.', 'woocommerce' ), 109 );
			}
		}

		return true;
	}

	/**
	 * Ensure coupon is valid for sale items in the list is valid or throw exception.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_sale_items( $coupon ) {
		if ( $coupon->get_exclude_sale_items() ) {
			$valid = true;

			foreach ( $this->get_items_to_validate() as $item ) {
				if ( $item->product && $item->product->is_on_sale() ) {
					$valid = false;
					break;
				}
			}

			if ( ! $valid ) {
				throw new Exception( __( 'Sorry, this coupon is not valid for sale items.', 'woocommerce' ), 110 );
			}
		}

		return true;
	}

	/**
	 * All exclusion rules must pass at the same time for a product coupon to be valid.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_excluded_items( $coupon ) {
		$items = $this->get_items_to_validate();
		if ( ! empty( $items ) && $coupon->is_type( wc_get_product_coupon_types() ) ) {
			$valid = false;

			foreach ( $items as $item ) {
				if ( $item->product && $coupon->is_valid_for_product( $item->product, $item->object ) ) {
					$valid = true;
					break;
				}
			}

			if ( ! $valid ) {
				throw new Exception( __( 'Sorry, this coupon is not applicable to selected products.', 'woocommerce' ), 109 );
			}
		}

		return true;
	}

	/**
	 * Cart discounts cannot be added if non-eligible product is found.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_eligible_items( $coupon ) {
		if ( ! $coupon->is_type( wc_get_product_coupon_types() ) ) {
			$this->validate_coupon_sale_items( $coupon );
			$this->validate_coupon_excluded_product_ids( $coupon );
			$this->validate_coupon_excluded_product_categories( $coupon );
		}

		return true;
	}

	/**
	 * Exclude products.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_excluded_product_ids( $coupon ) {
		// Exclude Products.
		if ( count( $coupon->get_excluded_product_ids() ) > 0 ) {
			$products = array();

			foreach ( $this->get_items_to_validate() as $item ) {
				if ( $item->product && in_array( $item->product->get_id(), $coupon->get_excluded_product_ids(), true ) || in_array( $item->product->get_parent_id(), $coupon->get_excluded_product_ids(), true ) ) {
					$products[] = $item->product->get_name();
				}
			}

			if ( ! empty( $products ) ) {
				/* translators: %s: products list */
				throw new Exception( sprintf( __( 'Sorry, this coupon is not applicable to the products: %s.', 'woocommerce' ), implode( ', ', $products ) ), 113 );
			}
		}

		return true;
	}

	/**
	 * Exclude categories from product list.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool
	 */
	protected function validate_coupon_excluded_product_categories( $coupon ) {
		if ( count( $coupon->get_excluded_product_categories() ) > 0 ) {
			$categories = array();

			foreach ( $this->get_items_to_validate() as $item ) {
				if ( ! $item->product ) {
					continue;
				}

				$product_cats = wc_get_product_cat_ids( $item->product->get_id() );

				if ( $item->product->get_parent_id() ) {
					$product_cats = array_merge( $product_cats, wc_get_product_cat_ids( $item->product->get_parent_id() ) );
				}

				$cat_id_list = array_intersect( $product_cats, $coupon->get_excluded_product_categories() );
				if ( count( $cat_id_list ) > 0 ) {
					foreach ( $cat_id_list as $cat_id ) {
						$cat          = get_term( $cat_id, 'product_cat' );
						$categories[] = $cat->name;
					}
				}
			}

			if ( ! empty( $categories ) ) {
				/* translators: %s: categories list */
				throw new Exception( sprintf( __( 'Sorry, this coupon is not applicable to the categories: %s.', 'woocommerce' ), implode( ', ', array_unique( $categories ) ) ), 114 );
			}
		}

		return true;
	}

	/**
	 * Get the object subtotal
	 *
	 * @return int
	 */
	protected function get_object_subtotal() {
		if ( is_a( $this->object, 'WC_Cart' ) ) {
			return wc_add_number_precision( $this->object->get_displayed_subtotal() );
		} elseif ( is_a( $this->object, 'WC_Order' ) ) {
			$subtotal = wc_add_number_precision( $this->object->get_subtotal() );

			if ( $this->object->get_prices_include_tax() ) {
				// Add tax to tax-exclusive subtotal.
				$subtotal = $subtotal + wc_add_number_precision( NumberUtil::round( $this->object->get_total_tax(), wc_get_price_decimals() ) );
			}

			return $subtotal;
		} else {
			return array_sum( wp_list_pluck( $this->items, 'price' ) );
		}
	}

	/**
	 * Check if a coupon is valid.
	 *
	 * Error Codes:
	 * - 100: Invalid filtered.
	 * - 101: Invalid removed.
	 * - 102: Not yours removed.
	 * - 103: Already applied.
	 * - 104: Individual use only.
	 * - 105: Not exists.
	 * - 106: Usage limit reached.
	 * - 107: Expired.
	 * - 108: Minimum spend limit not met.
	 * - 109: Not applicable.
	 * - 110: Not valid for sale items.
	 * - 111: Missing coupon code.
	 * - 112: Maximum spend limit met.
	 * - 113: Excluded products.
	 * - 114: Excluded categories.
	 *
	 * @since  3.2.0
	 * @throws Exception Error message.
	 * @param  WC_Coupon $coupon Coupon data.
	 * @return bool|WP_Error
	 */
	public function is_coupon_valid( $coupon ) {
		try {
			$this->validate_coupon_exists( $coupon );
			$this->validate_coupon_usage_limit( $coupon );
			$this->validate_coupon_user_usage_limit( $coupon );
			$this->validate_coupon_expiry_date( $coupon );
			$this->validate_coupon_minimum_amount( $coupon );
			$this->validate_coupon_maximum_amount( $coupon );
			$this->validate_coupon_product_ids( $coupon );
			$this->validate_coupon_product_categories( $coupon );
			$this->validate_coupon_excluded_items( $coupon );
			$this->validate_coupon_eligible_items( $coupon );

			if ( ! apply_filters( 'woocommerce_coupon_is_valid', true, $coupon, $this ) ) {
				throw new Exception( __( 'Coupon is not valid.', 'woocommerce' ), 100 );
			}
		} catch ( Exception $e ) {
			/**
			 * Filter the coupon error message.
			 *
			 * @param string    $error_message Error message.
			 * @param int       $error_code    Error code.
			 * @param WC_Coupon $coupon        Coupon data.
			 */
			$message = apply_filters( 'woocommerce_coupon_error', is_numeric( $e->getMessage() ) ? $coupon->get_coupon_error( $e->getMessage() ) : $e->getMessage(), $e->getCode(), $coupon );

			return new WP_Error(
				'invalid_coupon',
				$message,
				array(
					'status' => 400,
				)
			);
		}
		return true;
	}
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy Abstract Order
 *
 * Legacy and deprecated functions are here to keep the WC_Abstract_Order clean.
 * This class will be removed in future versions.
 *
 * @version	 3.0.0
 * @package	 WooCommerce\Abstracts
 * @category	Abstract Class
 * @author	  WooThemes
 */
abstract class WC_Abstract_Legacy_Order extends WC_Data {

	/**
	 * Add coupon code to the order.
	 * @param string|array $code
	 * @param int $discount tax amount.
	 * @param int $discount_tax amount.
	 * @return int order item ID
	 * @throws WC_Data_Exception
	 */
	public function add_coupon( $code = array(), $discount = 0, $discount_tax = 0 ) {
		wc_deprecated_function( 'WC_Order::add_coupon', '3.0', 'a new WC_Order_Item_Coupon object and add to order with WC_Order::add_item()' );

		$item = new WC_Order_Item_Coupon();
		$item->set_props( array(
			'code'         => $code,
			'discount'     => $discount,
			'discount_tax' => $discount_tax,
			'order_id'     => $this->get_id(),
		) );
		$item->save();
		$this->add_item( $item );
		wc_do_deprecated_action( 'woocommerce_order_add_coupon', array( $this->get_id(), $item->get_id(), $code, $discount, $discount_tax ), '3.0', 'woocommerce_new_order_item action instead.' );
		return $item->get_id();
	}

	/**
	 * Add a tax row to the order.
	 * @param int $tax_rate_id
	 * @param int $tax_amount amount of tax.
	 * @param int $shipping_tax_amount shipping amount.
	 * @return int order item ID
	 * @throws WC_Data_Exception
	 */
	public function add_tax( $tax_rate_id, $tax_amount = 0, $shipping_tax_amount = 0 ) {
		wc_deprecated_function( 'WC_Order::add_tax', '3.0', 'a new WC_Order_Item_Tax object and add to order with WC_Order::add_item()' );

		$item = new WC_Order_Item_Tax();
		$item->set_props( array(
			'rate_id'            => $tax_rate_id,
			'tax_total'          => $tax_amount,
			'shipping_tax_total' => $shipping_tax_amount,
		) );
		$item->set_rate( $tax_rate_id );
		$item->set_order_id( $this->get_id() );
		$item->save();
		$this->add_item( $item );
		wc_do_deprecated_action( 'woocommerce_order_add_tax', array( $this->get_id(), $item->get_id(), $tax_rate_id, $tax_amount, $shipping_tax_amount ), '3.0', 'woocommerce_new_order_item action instead.' );
		return $item->get_id();
	}

	/**
	 * Add a shipping row to the order.
	 * @param WC_Shipping_Rate shipping_rate
	 * @return int order item ID
	 * @throws WC_Data_Exception
	 */
	public function add_shipping( $shipping_rate ) {
		wc_deprecated_function( 'WC_Order::add_shipping', '3.0', 'a new WC_Order_Item_Shipping object and add to order with WC_Order::add_item()' );

		$item = new WC_Order_Item_Shipping();
		$item->set_props( array(
			'method_title' => $shipping_rate->label,
			'method_id'    => $shipping_rate->id,
			'total'        => wc_format_decimal( $shipping_rate->cost ),
			'taxes'        => $shipping_rate->taxes,
			'order_id'     => $this->get_id(),
		) );
		foreach ( $shipping_rate->get_meta_data() as $key => $value ) {
			$item->add_meta_data( $key, $value, true );
		}
		$item->save();
		$this->add_item( $item );
		wc_do_deprecated_action( 'woocommerce_order_add_shipping', array( $this->get_id(), $item->get_id(), $shipping_rate ), '3.0', 'woocommerce_new_order_item action instead.' );
		return $item->get_id();
	}

	/**
	 * Add a fee to the order.
	 * Order must be saved prior to adding items.
	 *
	 * Fee is an amount of money charged for a particular piece of work
	 * or for a particular right or service, and not supposed to be negative.
	 *
	 * @throws WC_Data_Exception
	 * @param  object $fee Fee data.
	 * @return int         Updated order item ID.
	 */
	public function add_fee( $fee ) {
		wc_deprecated_function( 'WC_Order::add_fee', '3.0', 'a new WC_Order_Item_Fee object and add to order with WC_Order::add_item()' );

		$item = new WC_Order_Item_Fee();
		$item->set_props( array(
			'name'      => $fee->name,
			'tax_class' => $fee->taxable ? $fee->tax_class : 0,
			'total'     => $fee->amount,
			'total_tax' => $fee->tax,
			'taxes'     => array(
				'total' => $fee->tax_data,
			),
			'order_id'  => $this->get_id(),
		) );
		$item->save();
		$this->add_item( $item );
		wc_do_deprecated_action( 'woocommerce_order_add_fee', array( $this->get_id(), $item->get_id(), $fee ), '3.0', 'woocommerce_new_order_item action instead.' );
		return $item->get_id();
	}

	/**
	 * Update a line item for the order.
	 *
	 * Note this does not update order totals.
	 *
	 * @param object|int $item order item ID or item object.
	 * @param WC_Product $product
	 * @param array $args data to update.
	 * @return int updated order item ID
	 * @throws WC_Data_Exception
	 */
	public function update_product( $item, $product, $args ) {
		wc_deprecated_function( 'WC_Order::update_product', '3.0', 'an interaction with the WC_Order_Item_Product class' );
		if ( is_numeric( $item ) ) {
			$item = $this->get_item( $item );
		}
		if ( ! is_object( $item ) || ! $item->is_type( 'line_item' ) ) {
			return false;
		}
		if ( ! $this->get_id() ) {
			$this->save(); // Order must exist
		}

		// BW compatibility with old args
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

		// Handle qty if set.
		if ( isset( $args['qty'] ) ) {
			if ( $product->backorders_require_notification() && $product->is_on_backorder( $args['qty'] ) ) {
				$item->add_meta_data( apply_filters( 'woocommerce_backordered_item_meta_name', __( 'Backordered', 'woocommerce' ), $item ), $args['qty'] - max( 0, $product->get_stock_quantity() ), true );
			}
			$args['subtotal'] = $args['subtotal'] ? $args['subtotal'] : wc_get_price_excluding_tax( $product, array( 'qty' => $args['qty'] ) );
			$args['total']	= $args['total'] ? $args['total'] : wc_get_price_excluding_tax( $product, array( 'qty' => $args['qty'] ) );
		}

		$item->set_order_id( $this->get_id() );
		$item->set_props( $args );
		$item->save();
		do_action( 'woocommerce_order_edit_product', $this->get_id(), $item->get_id(), $args, $product );

		return $item->get_id();
	}

	/**
	 * Update coupon for order. Note this does not update order totals.
	 * @param object|int $item
	 * @param array $args
	 * @return int updated order item ID
	 * @throws WC_Data_Exception
	 */
	public function update_coupon( $item, $args ) {
		wc_deprecated_function( 'WC_Order::update_coupon', '3.0', 'an interaction with the WC_Order_Item_Coupon class' );
		if ( is_numeric( $item ) ) {
			$item = $this->get_item( $item );
		}
		if ( ! is_object( $item ) || ! $item->is_type( 'coupon' ) ) {
			return false;
		}
		if ( ! $this->get_id() ) {
			$this->save(); // Order must exist
		}

		// BW compatibility for old args
		if ( isset( $args['discount_amount'] ) ) {
			$args['discount'] = $args['discount_amount'];
		}
		if ( isset( $args['discount_amount_tax'] ) ) {
			$args['discount_tax'] = $args['discount_amount_tax'];
		}

		$item->set_order_id( $this->get_id() );
		$item->set_props( $args );
		$item->save();

		do_action( 'woocommerce_order_update_coupon', $this->get_id(), $item->get_id(), $args );

		return $item->get_id();
	}

	/**
	 * Update shipping method for order.
	 *
	 * Note this does not update the order total.
	 *
	 * @param object|int $item
	 * @param array $args
	 * @return int updated order item ID
	 * @throws WC_Data_Exception
	 */
	public function update_shipping( $item, $args ) {
		wc_deprecated_function( 'WC_Order::update_shipping', '3.0', 'an interaction with the WC_Order_Item_Shipping class' );
		if ( is_numeric( $item ) ) {
			$item = $this->get_item( $item );
		}
		if ( ! is_object( $item ) || ! $item->is_type( 'shipping' ) ) {
			return false;
		}
		if ( ! $this->get_id() ) {
			$this->save(); // Order must exist
		}

		// BW compatibility for old args
		if ( isset( $args['cost'] ) ) {
			$args['total'] = $args['cost'];
		}

		$item->set_order_id( $this->get_id() );
		$item->set_props( $args );
		$item->save();
		$this->calculate_shipping();

		do_action( 'woocommerce_order_update_shipping', $this->get_id(), $item->get_id(), $args );

		return $item->get_id();
	}

	/**
	 * Update fee for order.
	 *
	 * Note this does not update order totals.
	 *
	 * @param object|int $item
	 * @param array $args
	 * @return int updated order item ID
	 * @throws WC_Data_Exception
	 */
	public function update_fee( $item, $args ) {
		wc_deprecated_function( 'WC_Order::update_fee', '3.0', 'an interaction with the WC_Order_Item_Fee class' );
		if ( is_numeric( $item ) ) {
			$item = $this->get_item( $item );
		}
		if ( ! is_object( $item ) || ! $item->is_type( 'fee' ) ) {
			return false;
		}
		if ( ! $this->get_id() ) {
			$this->save(); // Order must exist
		}

		$item->set_order_id( $this->get_id() );
		$item->set_props( $args );
		$item->save();

		do_action( 'woocommerce_order_update_fee', $this->get_id(), $item->get_id(), $args );

		return $item->get_id();
	}

	/**
	 * Update tax line on order.
	 * Note this does not update order totals.
	 *
	 * @since 3.0
	 * @param object|int $item
	 * @param array $args
	 * @return int updated order item ID
	 * @throws WC_Data_Exception
	 */
	public function update_tax( $item, $args ) {
		wc_deprecated_function( 'WC_Order::update_tax', '3.0', 'an interaction with the WC_Order_Item_Tax class' );
		if ( is_numeric( $item ) ) {
			$item = $this->get_item( $item );
		}
		if ( ! is_object( $item ) || ! $item->is_type( 'tax' ) ) {
			return false;
		}
		if ( ! $this->get_id() ) {
			$this->save(); // Order must exist
		}

		$item->set_order_id( $this->get_id() );
		$item->set_props( $args );
		$item->save();

		do_action( 'woocommerce_order_update_tax', $this->get_id(), $item->get_id(), $args );

		return $item->get_id();
	}

	/**
	 * Get a product (either product or variation).
	 * @deprecated 4.4.0
	 * @param object $item
	 * @return WC_Product|bool
	 */
	public function get_product_from_item( $item ) {
		wc_deprecated_function( 'WC_Abstract_Legacy_Order::get_product_from_item', '4.4.0', '$item->get_product()' );
		if ( is_callable( array( $item, 'get_product' ) ) ) {
			$product = $item->get_product();
		} else {
			$product = false;
		}
		return apply_filters( 'woocommerce_get_product_from_item', $product, $item, $this );
	}

	/**
	 * Set the customer address.
	 * @param array $address Address data.
	 * @param string $type billing or shipping.
	 */
	public function set_address( $address, $type = 'billing' ) {
		foreach ( $address as $key => $value ) {
			update_post_meta( $this->get_id(), "_{$type}_" . $key, $value );
			if ( is_callable( array( $this, "set_{$type}_{$key}" ) ) ) {
				$this->{"set_{$type}_{$key}"}( $value );
			}
		}
	}

	/**
	 * Set an order total.
	 * @param float $amount
	 * @param string $total_type
	 * @return bool
	 */
	public function legacy_set_total( $amount, $total_type = 'total' ) {
		if ( ! in_array( $total_type, array( 'shipping', 'tax', 'shipping_tax', 'total', 'cart_discount', 'cart_discount_tax' ) ) ) {
			return false;
		}

		switch ( $total_type ) {
			case 'total' :
				$amount = wc_format_decimal( $amount, wc_get_price_decimals() );
				$this->set_total( $amount );
				update_post_meta( $this->get_id(), '_order_total', $amount );
				break;
			case 'cart_discount' :
				$amount = wc_format_decimal( $amount );
				$this->set_discount_total( $amount );
				update_post_meta( $this->get_id(), '_cart_discount', $amount );
				break;
			case 'cart_discount_tax' :
				$amount = wc_format_decimal( $amount );
				$this->set_discount_tax( $amount );
				update_post_meta( $this->get_id(), '_cart_discount_tax', $amount );
				break;
			case 'shipping' :
				$amount = wc_format_decimal( $amount );
				$this->set_shipping_total( $amount );
				update_post_meta( $this->get_id(), '_order_shipping', $amount );
				break;
			case 'shipping_tax' :
				$amount = wc_format_decimal( $amount );
				$this->set_shipping_tax( $amount );
				update_post_meta( $this->get_id(), '_order_shipping_tax', $amount );
				break;
			case 'tax' :
				$amount = wc_format_decimal( $amount );
				$this->set_cart_tax( $amount );
				update_post_meta( $this->get_id(), '_order_tax', $amount );
				break;
		}

		return true;
	}

	/**
	 * Magic __isset method for backwards compatibility. Handles legacy properties which could be accessed directly in the past.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function __isset( $key ) {
		$legacy_props = array( 'completed_date', 'id', 'order_type', 'post', 'status', 'post_status', 'customer_note', 'customer_message', 'user_id', 'customer_user', 'prices_include_tax', 'tax_display_cart', 'display_totals_ex_tax', 'display_cart_ex_tax', 'order_date', 'modified_date', 'cart_discount', 'cart_discount_tax', 'order_shipping', 'order_shipping_tax', 'order_total', 'order_tax', 'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_postcode', 'billing_country', 'billing_phone', 'billing_email', 'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country', 'customer_ip_address', 'customer_user_agent', 'payment_method_title', 'payment_method', 'order_currency' );
		return $this->get_id() ? ( in_array( $key, $legacy_props ) || metadata_exists( 'post', $this->get_id(), '_' . $key ) ) : false;
	}

	/**
	 * Magic __get method for backwards compatibility.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key ) {
		wc_doing_it_wrong( $key, 'Order properties should not be accessed directly.', '3.0' );

		if ( 'completed_date' === $key ) {
			return $this->get_date_completed() ? gmdate( 'Y-m-d H:i:s', $this->get_date_completed()->getOffsetTimestamp() ) : '';
		} elseif ( 'paid_date' === $key ) {
			return $this->get_date_paid() ? gmdate( 'Y-m-d H:i:s', $this->get_date_paid()->getOffsetTimestamp() ) : '';
		} elseif ( 'modified_date' === $key ) {
			return $this->get_date_modified() ? gmdate( 'Y-m-d H:i:s', $this->get_date_modified()->getOffsetTimestamp() ) : '';
		} elseif ( 'order_date' === $key ) {
			return $this->get_date_created() ? gmdate( 'Y-m-d H:i:s', $this->get_date_created()->getOffsetTimestamp() ) : '';
		} elseif ( 'id' === $key ) {
			return $this->get_id();
		} elseif ( 'post' === $key ) {
			return get_post( $this->get_id() );
		} elseif ( 'status' === $key ) {
			return $this->get_status();
		} elseif ( 'post_status' === $key ) {
			return get_post_status( $this->get_id() );
		} elseif ( 'customer_message' === $key || 'customer_note' === $key ) {
			return $this->get_customer_note();
		} elseif ( in_array( $key, array( 'user_id', 'customer_user' ) ) ) {
			return $this->get_customer_id();
		} elseif ( 'tax_display_cart' === $key ) {
			return get_option( 'woocommerce_tax_display_cart' );
		} elseif ( 'display_totals_ex_tax' === $key ) {
			return 'excl' === get_option( 'woocommerce_tax_display_cart' );
		} elseif ( 'display_cart_ex_tax' === $key ) {
			return 'excl' === get_option( 'woocommerce_tax_display_cart' );
		} elseif ( 'cart_discount' === $key ) {
			return $this->get_total_discount();
		} elseif ( 'cart_discount_tax' === $key ) {
			return $this->get_discount_tax();
		} elseif ( 'order_tax' === $key ) {
			return $this->get_cart_tax();
		} elseif ( 'order_shipping_tax' === $key ) {
			return $this->get_shipping_tax();
		} elseif ( 'order_shipping' === $key ) {
			return $this->get_shipping_total();
		} elseif ( 'order_total' === $key ) {
			return $this->get_total();
		} elseif ( 'order_type' === $key ) {
			return $this->get_type();
		} elseif ( 'order_currency' === $key ) {
			return $this->get_currency();
		} elseif ( 'order_version' === $key ) {
			return $this->get_version();
	 	} elseif ( is_callable( array( $this, "get_{$key}" ) ) ) {
			return $this->{"get_{$key}"}();
		} else {
			return get_post_meta( $this->get_id(), '_' . $key, true );
		}
	}

	/**
	 * has_meta function for order items. This is different to the WC_Data
	 * version and should be removed in future versions.
	 *
	 * @deprecated 3.0
	 *
	 * @param int $order_item_id
	 *
	 * @return array of meta data.
	 */
	public function has_meta( $order_item_id ) {
		global $wpdb;

		wc_deprecated_function( 'WC_Order::has_meta( $order_item_id )', '3.0', 'WC_Order_item::get_meta_data' );

		return $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value, meta_id, order_item_id
			FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d
			ORDER BY meta_id", absint( $order_item_id ) ), ARRAY_A );
	}

	/**
	 * Display meta data belonging to an item.
	 * @param  array $item
	 */
	public function display_item_meta( $item ) {
		wc_deprecated_function( 'WC_Order::display_item_meta', '3.0', 'wc_display_item_meta' );
		$product   = $item->get_product();
		$item_meta = new WC_Order_Item_Meta( $item, $product );
		$item_meta->display();
	}

	/**
	 * Display download links for an order item.
	 * @param  array $item
	 */
	public function display_item_downloads( $item ) {
		wc_deprecated_function( 'WC_Order::display_item_downloads', '3.0', 'wc_display_item_downloads' );
		$product   = $item->get_product();

		if ( $product && $product->exists() && $product->is_downloadable() && $this->is_download_permitted() ) {
			$download_files = $this->get_item_downloads( $item );
			$i			  = 0;
			$links		  = array();

			foreach ( $download_files as $download_id => $file ) {
				$i++;
				/* translators: 1: current item count */
				$prefix  = count( $download_files ) > 1 ? sprintf( __( 'Download %d', 'woocommerce' ), $i ) : __( 'Download', 'woocommerce' );
				$links[] = '<small class="download-url">' . esc_html( $prefix ) . ': <a href="' . esc_url( $file['download_url'] ) . '" target="_blank">' . esc_html( $file['name'] ) . '</a></small>' . "\n";
			}

			echo '<br/>' . implode( '<br/>', $links );
		}
	}

	/**
	 * Get the Download URL.
	 *
	 * @param  int $product_id
	 * @param  int $download_id
	 * @return string
	 */
	public function get_download_url( $product_id, $download_id ) {
		wc_deprecated_function( 'WC_Order::get_download_url', '3.0', 'WC_Order_Item_Product::get_item_download_url' );
		return add_query_arg( array(
			'download_file' => $product_id,
			'order'         => $this->get_order_key(),
			'email'         => urlencode( $this->get_billing_email() ),
			'key'           => $download_id,
		), trailingslashit( home_url() ) );
	}

	/**
	 * Get the downloadable files for an item in this order.
	 *
	 * @param  array $item
	 * @return array
	 */
	public function get_item_downloads( $item ) {
		wc_deprecated_function( 'WC_Order::get_item_downloads', '3.0', 'WC_Order_Item_Product::get_item_downloads' );

		if ( ! $item instanceof WC_Order_Item ) {
			if ( ! empty( $item['variation_id'] ) ) {
				$product_id = $item['variation_id'];
			} elseif ( ! empty( $item['product_id'] ) ) {
				$product_id = $item['product_id'];
			} else {
				return array();
			}

			// Create a 'virtual' order item to allow retrieving item downloads when
			// an array of product_id is passed instead of actual order item.
			$item = new WC_Order_Item_Product();
			$item->set_product( wc_get_product( $product_id ) );
			$item->set_order_id( $this->get_id() );
		}

		return $item->get_item_downloads();
	}

	/**
	 * Gets shipping total. Alias of WC_Order::get_shipping_total().
	 * @deprecated 3.0.0 since this is an alias only.
	 * @return float
	 */
	public function get_total_shipping() {
		return $this->get_shipping_total();
	}

	/**
	 * Get order item meta.
	 * @deprecated 3.0.0
	 * @param mixed $order_item_id
	 * @param string $key (default: '')
	 * @param bool $single (default: false)
	 * @return array|string
	 */
	public function get_item_meta( $order_item_id, $key = '', $single = false ) {
		wc_deprecated_function( 'WC_Order::get_item_meta', '3.0', 'wc_get_order_item_meta' );
		return get_metadata( 'order_item', $order_item_id, $key, $single );
	}

	/**
	 * Get all item meta data in array format in the order it was saved. Does not group meta by key like get_item_meta().
	 *
	 * @param mixed $order_item_id
	 * @return array of objects
	 */
	public function get_item_meta_array( $order_item_id ) {
		wc_deprecated_function( 'WC_Order::get_item_meta_array', '3.0', 'WC_Order_Item::get_meta_data() (note the format has changed)' );
		$item            = $this->get_item( $order_item_id );
		$meta_data       = $item->get_meta_data();
		$item_meta_array = array();

		foreach ( $meta_data as $meta ) {
			$item_meta_array[ $meta->id ] = $meta;
		}

		return $item_meta_array;
	}

	/**
	 * Get coupon codes only.
	 *
	 * @deprecated 3.7.0 - Replaced with better named method to reflect the actual data being returned.
	 * @return array
	 */
	public function get_used_coupons() {
		wc_deprecated_function( 'get_used_coupons', '3.7', 'WC_Abstract_Order::get_coupon_codes' );
		return $this->get_coupon_codes();
	}

	/**
	 * Expand item meta into the $item array.
	 * @deprecated 3.0.0 Item meta no longer expanded due to new order item
	 *		classes. This function now does nothing to avoid data breakage.
	 * @param array $item before expansion.
	 * @return array
	 */
	public function expand_item_meta( $item ) {
		wc_deprecated_function( 'WC_Order::expand_item_meta', '3.0' );
		return $item;
	}

	/**
	 * Load the order object. Called from the constructor.
	 * @deprecated 3.0.0 Logic moved to constructor
	 * @param int|object|WC_Order $order Order to init.
	 */
	protected function init( $order ) {
		wc_deprecated_function( 'WC_Order::init', '3.0', 'Logic moved to constructor' );
		if ( is_numeric( $order ) ) {
			$this->set_id( $order );
		} elseif ( $order instanceof WC_Order ) {
			$this->set_id( absint( $order->get_id() ) );
		} elseif ( isset( $order->ID ) ) {
			$this->set_id( absint( $order->ID ) );
		}
		$this->set_object_read( false );
		$this->data_store->read( $this );
	}

	/**
	 * Gets an order from the database.
	 * @deprecated 3.0
	 * @param int $id (default: 0).
	 * @return bool
	 */
	public function get_order( $id = 0 ) {
		wc_deprecated_function( 'WC_Order::get_order', '3.0' );

		if ( ! $id ) {
			return false;
		}

		$result = get_post( $id );

		if ( $result ) {
			$this->populate( $result );
			return true;
		}

		return false;
	}

	/**
	 * Populates an order from the loaded post data.
	 * @deprecated 3.0
	 * @param mixed $result
	 */
	public function populate( $result ) {
		wc_deprecated_function( 'WC_Order::populate', '3.0' );
		$this->set_id( $result->ID );
		$this->set_object_read( false );
		$this->data_store->read( $this );
	}

	/**
	 * Cancel the order and restore the cart (before payment).
	 * @deprecated 3.0.0 Moved to event handler.
	 * @param string $note (default: '') Optional note to add.
	 */
	public function cancel_order( $note = '' ) {
		wc_deprecated_function( 'WC_Order::cancel_order', '3.0', 'WC_Order::update_status' );
		WC()->session->set( 'order_awaiting_payment', false );
		$this->update_status( 'cancelled', $note );
	}

	/**
	 * Record sales.
	 * @deprecated 3.0.0
	 */
	public function record_product_sales() {
		wc_deprecated_function( 'WC_Order::record_product_sales', '3.0', 'wc_update_total_sales_counts' );
		wc_update_total_sales_counts( $this->get_id() );
	}

	/**
	 * Increase applied coupon counts.
	 * @deprecated 3.0.0
	 */
	public function increase_coupon_usage_counts() {
		wc_deprecated_function( 'WC_Order::increase_coupon_usage_counts', '3.0', 'wc_update_coupon_usage_counts' );
		wc_update_coupon_usage_counts( $this->get_id() );
	}

	/**
	 * Decrease applied coupon counts.
	 * @deprecated 3.0.0
	 */
	public function decrease_coupon_usage_counts() {
		wc_deprecated_function( 'WC_Order::decrease_coupon_usage_counts', '3.0', 'wc_update_coupon_usage_counts' );
		wc_update_coupon_usage_counts( $this->get_id() );
	}

	/**
	 * Reduce stock levels for all line items in the order.
	 * @deprecated 3.0.0
	 */
	public function reduce_order_stock() {
		wc_deprecated_function( 'WC_Order::reduce_order_stock', '3.0', 'wc_reduce_stock_levels' );
		wc_reduce_stock_levels( $this->get_id() );
	}

	/**
	 * Send the stock notifications.
	 * @deprecated 3.0.0 No longer needs to be called directly.
	 *
	 * @param $product
	 * @param $new_stock
	 * @param $qty_ordered
	 */
	public function send_stock_notifications( $product, $new_stock, $qty_ordered ) {
		wc_deprecated_function( 'WC_Order::send_stock_notifications', '3.0' );
	}

	/**
	 * Output items for display in html emails.
	 * @deprecated 3.0.0 Moved to template functions.
	 * @param array $args Items args.
	 * @return string
	 */
	public function email_order_items_table( $args = array() ) {
		wc_deprecated_function( 'WC_Order::email_order_items_table', '3.0', 'wc_get_email_order_items' );
		return wc_get_email_order_items( $this, $args );
	}

	/**
	 * Get currency.
	 * @deprecated 3.0.0
	 */
	public function get_order_currency() {
		wc_deprecated_function( 'WC_Order::get_order_currency', '3.0', 'WC_Order::get_currency' );
		return apply_filters( 'woocommerce_get_order_currency', $this->get_currency(), $this );
	}
}

<?php
/**
 * Order
 *
 * The WooCommerce order class handles order data.
 *
 * @class 		WC_Order
 * @version		2.1.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Order {

	/** @public int Order (post) ID */
	public $id;

	/**
	 * Get the order if ID is passed, otherwise the order is new and empty.
	 *
	 * @access public
	 * @param string $id (default: '')
	 * @return void
	 */
	public function __construct( $id = '' ) {
		$this->prices_include_tax = get_option('woocommerce_prices_include_tax') == 'yes' ? true : false;
		$this->tax_display_cart   = get_option( 'woocommerce_tax_display_cart' );

		$this->display_totals_ex_tax = $this->tax_display_cart == 'excl' ? true : false;
		$this->display_cart_ex_tax   = $this->tax_display_cart == 'excl' ? true : false;

		if ( $id > 0 ) {
			$this->get_order( $id );
		}
	}


	/**
	 * Gets an order from the database.
	 *
	 * @access public
	 * @param int $id (default: 0)
	 * @return bool
	 */
	public function get_order( $id = 0 ) {
		if ( ! $id ) {
			return false;
		}
		if ( $result = get_post( $id ) ) {
			$this->populate( $result );
			return true;
		}
		return false;
	}


	/**
	 * Populates an order from the loaded post data.
	 *
	 * @access public
	 * @param mixed $result
	 * @return void
	 */
	public function populate( $result ) {
		// Standard post data
		$this->id                  = $result->ID;
		$this->order_date          = $result->post_date;
		$this->modified_date       = $result->post_modified;
		$this->customer_message    = $result->post_excerpt;
		$this->customer_note       = $result->post_excerpt;
		$this->post_status         = $result->post_status;

		// Billing email cam default to user if set
		if ( empty( $this->billing_email ) && ! empty( $this->customer_user ) ) {
			$user                = get_user_by( 'id', $this->customer_user );
			$this->billing_email = $user->user_email;
		}

		// Get status
		$terms        = wp_get_object_terms( $this->id, 'shop_order_status', array( 'fields' => 'slugs' ) );
		$this->status = isset( $terms[0] ) ? $terms[0] : apply_filters( 'woocommerce_default_order_status', 'pending' );
	}

	/**
	 * __isset function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset( $key ) {
		if ( ! $this->id ) {
			return false;
		}
		return metadata_exists( 'post', $this->id, '_' . $key );
	}

	/**
	 * __get function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		// Get values or default if not set
		if ( 'completed_date' == $key ) {
			$value = ( $value = get_post_meta( $this->id, '_completed_date', true ) ) ? $value : $this->modified_date;
		} elseif ( 'user_id' == $key ) {
			$value = ( $value = get_post_meta( $this->id, '_customer_user', true ) ) ? absint( $value ) : '';
		} else {
			$value = get_post_meta( $this->id, '_' . $key, true );
		}

		return $value;
	}

	/**
	 * Check if an order key is valid.
	 *
	 * @access public
	 * @param mixed $key
	 * @return bool
	 */
	public function key_is_valid( $key ) {
		if ( $key == $this->order_key ) {
			return true;
		}

		return false;
	}

	/**
	 * get_order_number function.
	 *
	 * Gets the order number for display (by default, order ID)
	 *
	 * @access public
	 * @return string
	 */
	public function get_order_number() {
		return apply_filters( 'woocommerce_order_number', _x( '#', 'hash before order number', 'woocommerce' ) . $this->id, $this );
	}

	/**
	 * Get a formatted billing address for the order.
	 *
	 * @access public
	 * @return string
	 */
	public function get_formatted_billing_address() {
		if ( ! $this->formatted_billing_address ) {

			// Formatted Addresses
			$address = apply_filters( 'woocommerce_order_formatted_billing_address', array(
				'first_name'	=> $this->billing_first_name,
				'last_name'		=> $this->billing_last_name,
				'company'		=> $this->billing_company,
				'address_1'		=> $this->billing_address_1,
				'address_2'		=> $this->billing_address_2,
				'city'			=> $this->billing_city,
				'state'			=> $this->billing_state,
				'postcode'		=> $this->billing_postcode,
				'country'		=> $this->billing_country
			), $this );

			$this->formatted_billing_address = WC()->countries->get_formatted_address( $address );
		}
		return $this->formatted_billing_address;
	}

	/**
	 * Get the billing address in an array.
	 *
	 * @access public
	 * @return string
	 */
	public function get_billing_address() {
		if ( ! $this->billing_address ) {
			// Formatted Addresses
			$address = array(
				'address_1'		=> $this->billing_address_1,
				'address_2'		=> $this->billing_address_2,
				'city'			=> $this->billing_city,
				'state'			=> $this->billing_state,
				'postcode'		=> $this->billing_postcode,
				'country'		=> $this->billing_country
			);
			$joined_address = array();
			foreach ( $address as $part ) {
				if ( ! empty( $part ) ) {
					$joined_address[] = $part;
				}
			}
			$this->billing_address = implode( ', ', $joined_address );
		}
		return $this->billing_address;
	}

	/**
	 * Get a formatted shipping address for the order.
	 *
	 * @access public
	 * @return string
	 */
	public function get_formatted_shipping_address() {
		if ( ! $this->formatted_shipping_address ) {
			if ( $this->shipping_address_1 ) {

				// Formatted Addresses
				$address = apply_filters( 'woocommerce_order_formatted_shipping_address', array(
					'first_name' 	=> $this->shipping_first_name,
					'last_name'		=> $this->shipping_last_name,
					'company'		=> $this->shipping_company,
					'address_1'		=> $this->shipping_address_1,
					'address_2'		=> $this->shipping_address_2,
					'city'			=> $this->shipping_city,
					'state'			=> $this->shipping_state,
					'postcode'		=> $this->shipping_postcode,
					'country'		=> $this->shipping_country
				), $this );

				$this->formatted_shipping_address = WC()->countries->get_formatted_address( $address );
			}
		}
		return $this->formatted_shipping_address;
	}


	/**
	 * Get the shipping address in an array.
	 *
	 * @access public
	 * @return array
	 */
	public function get_shipping_address() {
		if ( ! $this->shipping_address ) {
			if ( $this->shipping_address_1 ) {
				// Formatted Addresses
				$address = array(
					'address_1'		=> $this->shipping_address_1,
					'address_2'		=> $this->shipping_address_2,
					'city'			=> $this->shipping_city,
					'state'			=> $this->shipping_state,
					'postcode'		=> $this->shipping_postcode,
					'country'		=> $this->shipping_country
				);
				$joined_address = array();
				foreach ( $address as $part ) {
					if ( ! empty( $part ) ) {
						$joined_address[] = $part;
					}
				}
				$this->shipping_address = implode( ', ', $joined_address );
			}
		}
		return $this->shipping_address;
	}

	/**
	 * Return an array of items/products within this order.
	 *
	 * @access public
	 * @param string|array $type Types of line items to get (array or string)
	 * @return array
	 */
	public function get_items( $type = '' ) {
		global $wpdb;

		if ( empty( $type ) ) {
			$type = array( 'line_item' );
		}

		if ( ! is_array( $type ) ) {
			$type = array( $type );
		}

		$type = array_map( 'esc_attr', $type );

		$line_items = $wpdb->get_results( $wpdb->prepare( "
			SELECT 		order_item_id, order_item_name, order_item_type
			FROM 		{$wpdb->prefix}woocommerce_order_items
			WHERE 		order_id = %d
			AND 		order_item_type IN ( '" . implode( "','", $type ) . "' )
			ORDER BY 	order_item_id
		", $this->id ) );

		$items = array();

		// Reserved meta keys
		$reserved_item_meta_keys = array(
			'name',
			'type',
			'item_meta',
			'qty',
			'tax_class',
			'product_id',
			'variation_id',
			'line_subtotal',
			'line_total',
			'line_tax',
			'line_subtotal_tax'
		);

		// Loop items
		foreach ( $line_items as $item ) {
			// Place line item into array to return
			$items[ $item->order_item_id ]['name']      = $item->order_item_name;
			$items[ $item->order_item_id ]['type']      = $item->order_item_type;
			$items[ $item->order_item_id ]['item_meta'] = $this->get_item_meta( $item->order_item_id );

			// Expand meta data into the array
			foreach ( $items[ $item->order_item_id ]['item_meta'] as $name => $value ) {
				if ( in_array( $name, $reserved_item_meta_keys ) ) {
					continue;
				}
				if ( '_' === substr( $name, 0, 1 ) ) {
					$items[ $item->order_item_id ][ substr( $name, 1 ) ] = $value[0];
				} elseif ( ! in_array( $name, $reserved_item_meta_keys ) ) {
					$items[ $item->order_item_id ][ $name ] = $value[0];
				}
			}
		}

		return apply_filters( 'woocommerce_order_get_items', $items, $this );
	}

	/**
	 * Gets order total - formatted for display.
	 *
	 * @access public
	 * @return string
	 */
	public function get_item_count( $type = '' ) {
		global $wpdb;

		if ( empty( $type ) ) {
			$type = array( 'line_item' );
		}

		if ( ! is_array( $type ) ) {
			$type = array( $type );
		}

		$items = $this->get_items( $type );

		$count = 0;

		foreach ( $items as $item ) {
			if ( ! empty( $item['qty'] ) ) {
				$count += $item['qty'];
			} else {
				$count ++;
			}
		}

		return apply_filters( 'woocommerce_get_item_count', $count, $type, $this );
	}

	/**
	 * Return an array of fees within this order.
	 *
	 * @access public
	 * @return array
	 */
	public function get_fees() {
		return $this->get_items( 'fee' );
	}

	/**
	 * Return an array of taxes within this order.
	 *
	 * @access public
	 * @return array
	 */
	public function get_taxes() {
		return $this->get_items( 'tax' );
	}

	/**
	 * Return an array of shipping costs within this order.
	 *
	 * @return array
	 */
	public function get_shipping_methods() {
		return $this->get_items( 'shipping' );
	}

	/**
	 * Check whether this order has a specific shipping method or not
	 * @param string $method_id
	 * @return bool
	 */
	public function has_shipping_method( $method_id ) {
		$shipping_methods = $this->get_shipping_methods();
		$has_method = false;

		if ( ! $shipping_methods ) {
			return false;
		}

		foreach ( $shipping_methods as $shipping_method ) {
			if ( $shipping_method['method_id'] == $method_id ) {
				$has_method = true;
			}
		}

		return $has_method;
	}

	/**
	 * Get taxes, merged by code, formatted ready for output.
	 *
	 * @access public
	 * @return array
	 */
	public function get_tax_totals() {
		$taxes      = $this->get_items( 'tax' );
		$tax_totals = array();

		foreach ( $taxes as $key => $tax ) {

			$code = $tax[ 'name' ];

			if ( ! isset( $tax_totals[ $code ] ) ) {
				$tax_totals[ $code ] = new stdClass();
				$tax_totals[ $code ]->amount = 0;
			}

			$tax_totals[ $code ]->is_compound       = $tax[ 'compound' ];
			$tax_totals[ $code ]->label             = isset( $tax[ 'label' ] ) ? $tax[ 'label' ] : $tax[ 'name' ];
			$tax_totals[ $code ]->amount           += $tax[ 'tax_amount' ] + $tax[ 'shipping_tax_amount' ];
			$tax_totals[ $code ]->formatted_amount  = wc_price( wc_round_tax_total( $tax_totals[ $code ]->amount ), array('currency' => $this->get_order_currency()) );
		}

		return apply_filters( 'woocommerce_order_tax_totals', $tax_totals, $this );
	}

	/**
	 * has_meta function for order items.
	 * @access public
	 * @param string $order_item_id
	 * @return array of meta data
	 */
	public function has_meta( $order_item_id ) {
		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value, meta_id, order_item_id
			FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d
			ORDER BY meta_id", absint( $order_item_id ) ), ARRAY_A );
	}

	/**
	 * Get order item meta.
	 *
	 * @access public
	 * @param mixed $order_item_id
	 * @param string $key (default: '')
	 * @param bool $single (default: false)
	 * @return array|string
	 */
	public function get_item_meta( $order_item_id, $key = '', $single = false ) {
		return get_metadata( 'order_item', $order_item_id, $key, $single );
	}

	/** Total Getters *******************************************************/

	/**
	 * Gets the total (product) discount amount - these are applied before tax.
	 *
	 * @access public
	 * @return float
	 */
	public function get_cart_discount() {
		return apply_filters( 'woocommerce_order_amount_cart_discount', (double) $this->cart_discount, $this );
	}

	/**
	 * Gets the total (product) discount amount - these are applied before tax.
	 *
	 * @access public
	 * @return float
	 */
	public function get_order_discount() {
		return apply_filters( 'woocommerce_order_amount_order_discount', (double) $this->order_discount, $this );
	}

	/**
	 * Gets the total discount amount - both kinds
	 *
	 * @access public
	 * @return float
	 */
	public function get_total_discount() {
		return apply_filters( 'woocommerce_order_amount_total_discount', $this->get_cart_discount() + $this->get_order_discount(), $this );
	}

	/**
	 * Gets shipping tax amount.
	 *
	 * @access public
	 * @return float
	 */
	public function get_cart_tax() {
		return apply_filters( 'woocommerce_order_amount_cart_tax', (double) $this->order_tax, $this );
	}

	/**
	 * Gets shipping tax amount.
	 *
	 * @access public
	 * @return float
	 */
	public function get_shipping_tax() {
		return apply_filters( 'woocommerce_order_amount_shipping_tax', (double) $this->order_shipping_tax, $this );
	}

	/**
	 * Gets shipping and product tax.
	 *
	 * @access public
	 * @return float
	 */
	public function get_total_tax() {
		return apply_filters( 'woocommerce_order_amount_total_tax', wc_round_tax_total( $this->get_cart_tax() + $this->get_shipping_tax() ), $this );
	}

	/**
	 * Gets shipping total.
	 *
	 * @access public
	 * @return float
	 */
	public function get_total_shipping() {
		return apply_filters( 'woocommerce_order_amount_total_shipping', (double) $this->order_shipping, $this );
	}

	/**
	 * Gets order total.
	 *
	 * @access public
	 * @return float
	 */
	public function get_total() {
		return apply_filters( 'woocommerce_order_amount_total', (double) $this->order_total, $this );
	}

	/**
	 * Get item subtotal - this is the cost before discount.
	 *
	 * @access public
	 * @param mixed $item
	 * @param bool $inc_tax (default: false)
	 * @param bool $round (default: true)
	 * @return float
	 */
	public function get_item_subtotal( $item, $inc_tax = false, $round = true ) {
		if ( $inc_tax ) {
			$price = ( $item['line_subtotal'] + $item['line_subtotal_tax'] ) / $item['qty'];
		} else {
			$price = ( $item['line_subtotal'] / $item['qty'] );
		}

		$price = $round ? round( $price, 2 ) : $price;

		return apply_filters( 'woocommerce_order_amount_item_subtotal', $price, $this );
	}

	/**
	 * Get line subtotal - this is the cost before discount.
	 *
	 * @access public
	 * @param mixed $item
	 * @param bool $inc_tax (default: false)
	 * @param bool $round (default: true)
	 * @return float
	 */
	public function get_line_subtotal( $item, $inc_tax = false, $round = true ) {
		if ( $inc_tax ) {
			$price = $item['line_subtotal'] + $item['line_subtotal_tax'];
		} else {
			$price = $item['line_subtotal'];
		}

		$price = $round ? round( $price, 2 ) : $price;

		return apply_filters( 'woocommerce_order_amount_line_subtotal', $price, $this );
	}

	/**
	 * Calculate item cost - useful for gateways.
	 *
	 * @access public
	 * @param mixed $item
	 * @param bool $inc_tax (default: false)
	 * @param bool $round (default: true)
	 * @return float
	 */
	public function get_item_total( $item, $inc_tax = false, $round = true ) {
		if ( $inc_tax ) {
			$price = ( $item['line_total'] + $item['line_tax'] ) / $item['qty'];
		} else {
			$price = $item['line_total'] / $item['qty'];
		}

		$price = $round ? round( $price, 2 ) : $price;

		return apply_filters( 'woocommerce_order_amount_item_total', $price, $this );
	}

	/**
	 * Calculate line total - useful for gateways.
	 *
	 * @access public
	 * @param mixed $item
	 * @param bool $inc_tax (default: false)
	 * @return float
	 */
	public function get_line_total( $item, $inc_tax = false ) {
		$line_total = $inc_tax ? round( $item['line_total'] + $item['line_tax'], 2 ) : round( $item['line_total'], 2 );
		return apply_filters( 'woocommerce_order_amount_line_total', $line_total, $this );
	}

	/**
	 * Calculate item tax - useful for gateways.
	 *
	 * @access public
	 * @param mixed $item
	 * @param bool $round (default: true)
	 * @return float
	 */
	public function get_item_tax( $item, $round = true ) {
		$price = $item['line_tax'] / $item['qty'];
		$price = $round ? wc_round_tax_total( $price ) : $price;
		return apply_filters( 'woocommerce_order_amount_item_tax', $price, $item, $round, $this );
	}

	/**
	 * Calculate line tax - useful for gateways.
	 *
	 * @access public
	 * @param mixed $item
	 * @return float
	 */
	public function get_line_tax( $item ) {
		return apply_filters( 'woocommerce_order_amount_line_tax', wc_round_tax_total( $item['line_tax'] ), $item, $this );
	}

	/**
	 * Gets shipping total.
	 *
	 * @deprecated As of 2.1, use of get_total_shipping() is preferred
	 * @access public
	 * @return float
	 */
	public function get_shipping() {
		_deprecated_function( 'get_shipping', '2.1', 'get_total_shipping' );
		return $this->get_total_shipping();
	}

	/**
	 * get_order_total function. Alias for get_total()
	 *
	 * @deprecated As of 2.1, use of get_total() is preferred
	 * @access public
	 * @return float
	 */
	public function get_order_total() {
		_deprecated_function( 'get_order_total', '2.1', 'get_total' );
		return $this->get_total();
	}

	/** End Total Getters *******************************************************/

	/**
	 * Gets formatted shipping method title.
	 *
	 * @return string
	 */
	public function get_shipping_method() {
		$labels = array();

		// Backwards compat < 2.1 - get shipping title stored in meta
		if ( $this->shipping_method_title ) {
			$labels[] = $this->shipping_method_title;
		} else {
			// 2.1+ get line items for shipping
			$shipping_methods = $this->get_shipping_methods();

			foreach ( $shipping_methods as $shipping ) {
				$labels[] = $shipping['name'];
			}
		}

		return apply_filters( 'woocommerce_order_shipping_method', implode( ', ', $labels ), $this );
	}

	/**
	 * Gets line subtotal - formatted for display.
	 *
	 * @access public
	 * @param array  $item
	 * @param string $tax_display
	 * @return string
	 */
	public function get_formatted_line_subtotal( $item, $tax_display = '' ) {
		if ( ! $tax_display ) {
			$tax_display = $this->tax_display_cart;
		}

		if ( ! isset( $item['line_subtotal'] ) || ! isset( $item['line_subtotal_tax'] ) ) {
			return '';
		}

		if ( 'excl' == $tax_display ) {
			$ex_tax_label = $this->prices_include_tax ? 1 : 0;

			$subtotal = wc_price( $this->get_line_subtotal( $item ), array( 'ex_tax_label' => $ex_tax_label, 'currency' => $this->get_order_currency() ) );
		} else {
			$subtotal = wc_price( $this->get_line_subtotal( $item, true ), array('currency' => $this->get_order_currency()) );
		}

		return apply_filters( 'woocommerce_order_formatted_line_subtotal', $subtotal, $item, $this );
	}

	/**
	 * Gets order currency
	 *
	 * @access public
	 * @return string
	 */
	public function get_order_currency() {

		$currency = $this->order_currency;

		return apply_filters( 'woocommerce_get_order_currency', $currency, $this );
	}

	/**
	 * Gets order total - formatted for display.
	 *
	 * @access public
	 * @return string
	 */
	public function get_formatted_order_total() {

		$formatted_total = wc_price( $this->order_total , array('currency' => $this->get_order_currency()));

		return apply_filters( 'woocommerce_get_formatted_order_total', $formatted_total, $this );
	}


	/**
	 * Gets subtotal - subtotal is shown before discounts, but with localised taxes.
	 *
	 * @access public
	 * @param bool $compound (default: false)
	 * @param string $tax_display (default: the tax_display_cart value)
	 * @return string
	 */
	public function get_subtotal_to_display( $compound = false, $tax_display = '' ) {
		if ( ! $tax_display ) {
			$tax_display = $this->tax_display_cart;
		}

		$subtotal = 0;

		if ( ! $compound ) {
			foreach ( $this->get_items() as $item ) {

				if ( ! isset( $item['line_subtotal'] ) || ! isset( $item['line_subtotal_tax'] ) ) {
					return '';
				}

				$subtotal += $item['line_subtotal'];

				if ( 'incl' == $tax_display ) {
					$subtotal += $item['line_subtotal_tax'];
				}
			}

			$subtotal = wc_price( $subtotal, array('currency' => $this->get_order_currency()) );

			if ( $tax_display == 'excl' && $this->prices_include_tax ) {
				$subtotal .= ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
			}

		} else {

			if ( 'incl' == $tax_display ) {
				return '';
			}

			foreach ( $this->get_items() as $item ) {

				$subtotal += $item['line_subtotal'];

			}

			// Add Shipping Costs
			$subtotal += $this->get_total_shipping();

			// Remove non-compound taxes
			foreach ( $this->get_taxes() as $tax ) {

				if ( ! empty( $tax['compound'] ) ) {
					continue;
				}

				$subtotal = $subtotal + $tax['tax_amount'] + $tax['shipping_tax_amount'];

			}

			// Remove discounts
			$subtotal = $subtotal - $this->get_cart_discount();

			$subtotal = wc_price( $subtotal, array('currency' => $this->get_order_currency()) );
		}

		return apply_filters( 'woocommerce_order_subtotal_to_display', $subtotal, $compound, $this );
	}


	/**
	 * Gets shipping (formatted).
	 *
	 * @access public
	 * @return string
	 */
	public function get_shipping_to_display( $tax_display = '' ) {
		if ( ! $tax_display ) {
			$tax_display = $this->tax_display_cart;
		}

		if ( $this->order_shipping > 0 ) {

			$tax_text = '';

			if ( $tax_display == 'excl' ) {

				// Show shipping excluding tax
				$shipping = wc_price( $this->order_shipping, array('currency' => $this->get_order_currency()) );

				if ( $this->order_shipping_tax > 0 && $this->prices_include_tax ) {
					$tax_text = WC()->countries->ex_tax_or_vat() . ' ';
				}

			} else {

				// Show shipping including tax
				$shipping = wc_price( $this->order_shipping + $this->order_shipping_tax, array('currency' => $this->get_order_currency()) );

				if ( $this->order_shipping_tax > 0 && ! $this->prices_include_tax ) {
					$tax_text = WC()->countries->inc_tax_or_vat() . ' ';
				}

			}

			$shipping .= sprintf( __( '&nbsp;<small>%svia %s</small>', 'woocommerce' ), $tax_text, $this->get_shipping_method() );

		} elseif ( $this->get_shipping_method() ) {
			$shipping = $this->get_shipping_method();
		} else {
			$shipping = __( 'Free!', 'woocommerce' );
		}

		return apply_filters( 'woocommerce_order_shipping_to_display', $shipping, $this );
	}


	/**
	 * Get cart discount (formatted).
	 *
	 * @access public
	 * @return string.
	 */
	public function get_cart_discount_to_display() {
		return apply_filters( 'woocommerce_order_cart_discount_to_display', wc_price( $this->get_cart_discount(), array( 'currency' => $this->get_order_currency() ) ), $this );
	}


	/**
	 * Get cart discount (formatted).
	 *
	 * @access public
	 * @return string
	 */
	public function get_order_discount_to_display() {
		return apply_filters( 'woocommerce_order_discount_to_display', wc_price( $this->get_order_discount(), array( 'currency' => $this->get_order_currency() ) ), $this );
	}


	/**
	 * Get a product (either product or variation).
	 *
	 * @access public
	 * @param mixed $item
	 * @return WC_Product
	 */
	public function get_product_from_item( $item ) {
		$_product = get_product( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );

		return apply_filters( 'woocommerce_get_product_from_item', $_product, $item, $this );
	}


	/**
	 * Get totals for display on pages and in emails.
	 *
	 * @access public
	 * @return array
	 */
	public function get_order_item_totals( $tax_display = '' ) {
		if ( ! $tax_display ) {
			$tax_display = $this->tax_display_cart;
		}

		$total_rows = array();

		if ( $subtotal = $this->get_subtotal_to_display() ) {
			$total_rows['cart_subtotal'] = array(
				'label' => __( 'Cart Subtotal:', 'woocommerce' ),
				'value'	=> $subtotal
			);
		}

		if ( $this->get_cart_discount() > 0 ) {
			$total_rows['cart_discount'] = array(
				'label' => __( 'Cart Discount:', 'woocommerce' ),
				'value'	=> '-' . $this->get_cart_discount_to_display()
			);
		}

		if ( $this->get_shipping_method() ) {
			$total_rows['shipping'] = array(
				'label' => __( 'Shipping:', 'woocommerce' ),
				'value'	=> $this->get_shipping_to_display()
			);
		}

		if ( $fees = $this->get_fees() )
			foreach( $fees as $id => $fee ) {
				if ( apply_filters( 'woocommerce_get_order_item_totals_excl_free_fees', $fee['line_total'] + $fee['line_tax'] == 0, $id ) ) {
					continue;
				}

				if ( 'excl' == $tax_display ) {

					$total_rows[ 'fee_' . $id ] = array(
						'label' => $fee['name'],
						'value'	=> wc_price( $fee['line_total'], array('currency' => $this->get_order_currency()) )
					);

				} else {

					$total_rows[ 'fee_' . $id ] = array(
						'label' => $fee['name'],
						'value'	=> wc_price( $fee['line_total'] + $fee['line_tax'], array('currency' => $this->get_order_currency()) )
					);

				}
			}

		// Tax for tax exclusive prices
		if ( 'excl' == $tax_display ) {
			if ( get_option( 'woocommerce_tax_total_display' ) == 'itemized' ) {
				foreach ( $this->get_tax_totals() as $code => $tax ) {
					$total_rows[ sanitize_title( $code ) ] = array(
						'label' => $tax->label . ':',
						'value'	=> $tax->formatted_amount
					);
				}
			} else {
				$total_rows['tax'] = array(
					'label' => WC()->countries->tax_or_vat() . ':',
					'value'	=> wc_price( $this->get_total_tax(), array('currency' => $this->get_order_currency()) )
				);
			}
		}

		if ( $this->get_order_discount() > 0 ) {
			$total_rows['order_discount'] = array(
				'label' => __( 'Order Discount:', 'woocommerce' ),
				'value'	=> '-' . $this->get_order_discount_to_display()
			);
		}

		$total_rows['order_total'] = array(
			'label' => __( 'Order Total:', 'woocommerce' ),
			'value'	=> $this->get_formatted_order_total()
		);

		// Tax for inclusive prices
		if ( 'yes' == get_option( 'woocommerce_calc_taxes' ) && 'incl' == $tax_display ) {

			$tax_string_array = array();

			if ( 'itemized' == get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( $this->get_tax_totals() as $code => $tax ) {
					$tax_string_array[] = sprintf( '%s %s', $tax->formatted_amount, $tax->label );
				}
			} else {
				$tax_string_array[] = sprintf( '%s %s', wc_price( $this->get_total_tax(), array('currency' => $this->get_order_currency()) ), WC()->countries->tax_or_vat() );
			}

			if ( ! empty( $tax_string_array ) ) {
				$total_rows['order_total']['value'] .= ' ' . sprintf( __( '(Includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) );
			}
		}

		return apply_filters( 'woocommerce_get_order_item_totals', $total_rows, $this );
	}


	/**
	 * Output items for display in html emails.
	 *
	 * @access public
	 * @param bool $show_download_links (default: false)
	 * @param bool $show_sku (default: false)
	 * @param bool $show_purchase_note (default: false)
	 * @param bool $show_image (default: false)
	 * @param array $image_size (default: array( 32, 32 )
	 * @param bool plain text
	 * @return string
	 */
	public function email_order_items_table( $show_download_links = false, $show_sku = false, $show_purchase_note = false, $show_image = false, $image_size = array( 32, 32 ), $plain_text = false ) {

		ob_start();

		$template = $plain_text ? 'emails/plain/email-order-items.php' : 'emails/email-order-items.php';

		wc_get_template( $template, array(
			'order'					=> $this,
			'items' 				=> $this->get_items(),
			'show_download_links'	=> $show_download_links,
			'show_sku'				=> $show_sku,
			'show_purchase_note'	=> $show_purchase_note,
			'show_image' 			=> $show_image,
			'image_size'			=> $image_size
		) );

		$return = apply_filters( 'woocommerce_email_order_items_table', ob_get_clean(), $this );

		return $return;
	}

	/**
	 * Checks if product download is permitted
	 *
	 * @access public
	 * @return bool
	 */
	public function is_download_permitted() {
		return apply_filters( 'woocommerce_order_is_download_permitted', $this->status == 'completed' || ( get_option( 'woocommerce_downloads_grant_access_after_payment' ) == 'yes' && $this->status == 'processing' ), $this );
	}

	/**
	 * Returns true if the order contains a downloadable product.
	 *
	 * @access public
	 * @return bool
	 */
	public function has_downloadable_item() {
		$has_downloadable_item = false;

		foreach ( $this->get_items() as $item ) {

			$_product = $this->get_product_from_item( $item );

			if ( $_product && $_product->exists() && $_product->is_downloadable() && $_product->has_file() ) {
				$has_downloadable_item = true;
			}

		}

		return $has_downloadable_item;
	}


	/**
	 * Generates a URL so that a customer can pay for their (unpaid - pending) order. Pass 'true' for the checkout version which doesn't offer gateway choices.
	 *
	 * @access public
	 * @param  boolean $on_checkout
	 * @return string
	 */
	public function get_checkout_payment_url( $on_checkout = false ) {

		$pay_url = wc_get_endpoint_url( 'order-pay', $this->id, get_permalink( wc_get_page_id( 'checkout' ) ) );

		if ( 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) || is_ssl() ) {
			$pay_url = str_replace( 'http:', 'https:', $pay_url );
		}

		if ( $on_checkout ) {
			$pay_url = add_query_arg( 'key', $this->order_key, $pay_url );
		} else {
			$pay_url = add_query_arg( array( 'pay_for_order' => 'true', 'key' => $this->order_key ), $pay_url );
		}

		return apply_filters( 'woocommerce_get_checkout_payment_url', $pay_url, $this );
	}


	/**
	 * Generates a URL for the thanks page (order received)
	 *
	 * @access public
	 * @return string
	 */
	public function get_checkout_order_received_url() {

		$order_received_url = wc_get_endpoint_url( 'order-received', $this->id, get_permalink( wc_get_page_id( 'checkout' ) ) );

		if ( 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) || is_ssl() ) {
			$order_received_url = str_replace( 'http:', 'https:', $order_received_url );
		}

		$order_received_url = add_query_arg( 'key', $this->order_key, $order_received_url );

		return apply_filters( 'woocommerce_get_checkout_order_received_url', $order_received_url, $this );
	}


	/**
	 * Generates a URL so that a customer can cancel their (unpaid - pending) order.
	 *
	 * @access public
	 * @return string
	 */
	public function get_cancel_order_url( $redirect = '' ) {
		$cancel_endpoint = get_permalink( wc_get_page_id( 'cart' ) );
		if ( ! $cancel_endpoint ) {
			$cancel_endpoint = home_url();
		}

		if ( false === strpos( $cancel_endpoint, '?' ) ) {
			$cancel_endpoint = trailingslashit( $cancel_endpoint );
		}

		return apply_filters('woocommerce_get_cancel_order_url', wp_nonce_url( add_query_arg( array( 'cancel_order' => 'true', 'order' => $this->order_key, 'order_id' => $this->id, 'redirect' => $redirect ), $cancel_endpoint ), 'woocommerce-cancel_order' ) );
	}

	/**
	 * Generates a URL to view an order from the my account page
	 *
	 * @return string
	 */
	public function get_view_order_url() {
		$view_order_url = wc_get_endpoint_url( 'view-order', $this->id, get_permalink( wc_get_page_id( 'myaccount' ) ) );

		return apply_filters( 'woocommerce_get_view_order_url', $view_order_url, $this );
	}

	/**
	 * Gets any downloadable product file urls.
	 *
	 * @deprecated as of 2.1 get_item_downloads is preferred as downloads are more than just file urls
	 * @param int $product_id product identifier
	 * @param int $variation_id variation identifier, or null
	 * @param array $item the item
	 * @return array available downloadable file urls
	 */
	public function get_downloadable_file_urls( $product_id, $variation_id, $item ) {
		global $wpdb;

		_deprecated_function( 'get_downloadable_file_urls', '2.1', 'get_item_downloads' );

		$download_file = $variation_id > 0 ? $variation_id : $product_id;
		$_product = get_product( $download_file );

		$user_email = $this->billing_email;

		$results = $wpdb->get_results( $wpdb->prepare("
			SELECT download_id
			FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
			WHERE user_email = %s
			AND order_key = %s
			AND product_id = %s
		", $user_email, $this->order_key, $download_file ) );

		$file_urls = array();
		foreach ( $results as $result ) {
			if ( $_product->has_file( $result->download_id ) ) {
				$file_urls[ $_product->get_file_download_path( $result->download_id ) ] = $this->get_download_url( $download_file, $result->download_id );
			}
		}

		return apply_filters( 'woocommerce_get_downloadable_file_urls', $file_urls, $product_id, $variation_id, $item );
	}

	/**
	 * Get the downloadable files for an item in this order
	 * @param  array $item
	 * @return array
	 */
	public function get_item_downloads( $item ) {
		global $wpdb;

		$product_id   = $item['variation_id'] > 0 ? $item['variation_id'] : $item['product_id'];
		$product      = get_product( $product_id );
		$download_ids = $wpdb->get_col( $wpdb->prepare("
			SELECT download_id
			FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
			WHERE user_email = %s
			AND order_key = %s
			AND product_id = %s
			ORDER BY permission_id
		", $this->billing_email, $this->order_key, $product_id ) );

		$files = array();

		foreach ( $download_ids as $download_id ) {
			if ( $product->has_file( $download_id ) ) {
				$files[ $download_id ]                 = $product->get_file( $download_id );
				$files[ $download_id ]['download_url'] = $this->get_download_url( $product_id, $download_id );
			}
		}

		return apply_filters( 'woocommerce_get_item_downloads', $files, $item, $this );
	}

	/**
	 * Get the Download URL
	 * @param  int $product_id
	 * @param  int $download_id
	 * @return string
	 */
	public function get_download_url( $product_id, $download_id ) {
		return add_query_arg( array(
			'download_file' => $product_id,
			'order'         => $this->order_key,
			'email'         => urlencode( $this->billing_email ),
			'key'           => $download_id
		), trailingslashit( home_url() ) );
	}

	/**
	 * Adds a note (comment) to the order
	 *
	 * @access public
	 * @param string $note Note to add
	 * @param int $is_customer_note (default: 0) Is this a note for the customer?
	 * @return id Comment ID
	 */
	public function add_order_note( $note, $is_customer_note = 0 ) {

		$is_customer_note = intval( $is_customer_note );

		if ( is_user_logged_in() && current_user_can( 'edit_shop_order', $this->id ) ) {
			$user                 = get_user_by( 'id', get_current_user_id() );
			$comment_author       = $user->display_name;
			$comment_author_email = $user->user_email;
		} else {
			$comment_author       = __( 'WooCommerce', 'woocommerce' );
			$comment_author_email = strtolower( __( 'WooCommerce', 'woocommerce' ) ) . '@';
			$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', $_SERVER['HTTP_HOST'] ) : 'noreply.com';
			$comment_author_email = sanitize_email( $comment_author_email );
		}

		$comment_post_ID 		= $this->id;
		$comment_author_url 	= '';
		$comment_content 		= $note;
		$comment_agent			= 'WooCommerce';
		$comment_type			= 'order_note';
		$comment_parent			= 0;
		$comment_approved 		= 1;
		$commentdata 			= apply_filters( 'woocommerce_new_order_note_data', compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_agent', 'comment_type', 'comment_parent', 'comment_approved' ), array( 'order_id' => $this->id, 'is_customer_note' => $is_customer_note ) );

		$comment_id = wp_insert_comment( $commentdata );

		add_comment_meta( $comment_id, 'is_customer_note', $is_customer_note );

		if ( $is_customer_note ) {
			do_action( 'woocommerce_new_customer_note', array( 'order_id' => $this->id, 'customer_note' => $note ) );
		}

		return $comment_id;
	}


	/**
	 * Updates status of order
	 *
	 * @access public
	 * @param string $new_status_slug Status to change the order to
	 * @param string $note (default: '') Optional note to add
	 * @return void
	 */
	public function update_status( $new_status_slug, $note = '' ) {

		if ( $note ) {
			$note .= ' ';
		}

		$old_status = get_term_by( 'slug', sanitize_title( $this->status ), 'shop_order_status' );
		$new_status = get_term_by( 'slug', sanitize_title( $new_status_slug ), 'shop_order_status' );

		if ( $new_status ) {

			wp_set_object_terms( $this->id, array( $new_status->slug ), 'shop_order_status', false );

			if ( $this->id && $this->status != $new_status->slug ) {

				// Status was changed
				do_action( 'woocommerce_order_status_' . $new_status->slug, $this->id );
				do_action( 'woocommerce_order_status_' . $this->status . '_to_' . $new_status->slug, $this->id );
				do_action( 'woocommerce_order_status_changed', $this->id, $this->status, $new_status->slug );

				if ( $old_status ) {
					$this->add_order_note( $note . sprintf( __( 'Order status changed from %s to %s.', 'woocommerce' ), __( $old_status->name, 'woocommerce' ), __( $new_status->name, 'woocommerce' ) ) );
				}

				// Record the completed date of the order
				if ( 'completed' == $new_status->slug ) {
					update_post_meta( $this->id, '_completed_date', current_time('mysql') );
				}

				if ( 'processing' == $new_status->slug || 'completed' == $new_status->slug || 'on-hold' == $new_status->slug ) {

					// Record the sales
					$this->record_product_sales();

					// Increase coupon usage counts
					$this->increase_coupon_usage_counts();
				}

				// If the order is cancelled, restore used coupons
				if ( 'cancelled' == $new_status->slug ) {
					$this->decrease_coupon_usage_counts();
				}

				// Update last modified
				wp_update_post( array( 'ID' => $this->id ) );

				$this->status = $new_status->slug;
			}

		}

		wc_delete_shop_order_transients( $this->id );
	}


	/**
	 * Cancel the order and restore the cart (before payment)
	 *
	 * @access public
	 * @param string $note (default: '') Optional note to add
	 * @return void
	 */
	public function cancel_order( $note = '' ) {
		unset( WC()->session->order_awaiting_payment );

		$this->update_status( 'cancelled', $note );

	}

	/**
	 * When a payment is complete this function is called
	 *
	 * Most of the time this should mark an order as 'processing' so that admin can process/post the items
	 * If the cart contains only downloadable items then the order is 'complete' since the admin needs to take no action
	 * Stock levels are reduced at this point
	 * Sales are also recorded for products
	 * Finally, record the date of payment
	 *
	 * @access public
	 * @return void
	 */
	public function payment_complete() {

		do_action( 'woocommerce_pre_payment_complete', $this->id );

		if ( ! empty( WC()->session->order_awaiting_payment ) ) {
			unset( WC()->session->order_awaiting_payment );
		}

		if ( $this->id && ( 'on-hold' == $this->status || 'pending' == $this->status || 'failed' == $this->status ) ) {

			$order_needs_processing = true;

			if ( sizeof( $this->get_items() ) > 0 ) {

				foreach( $this->get_items() as $item ) {

					if ( $item['product_id'] > 0 ) {

						$_product = $this->get_product_from_item( $item );

						if ( false !== $_product && ( $_product->is_downloadable() && $_product->is_virtual() ) || ! apply_filters( 'woocommerce_order_item_needs_processing', true, $_product, $this->id ) ) {
							$order_needs_processing = false;
							continue;
						}

					}
					$order_needs_processing = true;
					break;
				}
			}

			$new_order_status = $order_needs_processing ? 'processing' : 'completed';

			$new_order_status = apply_filters( 'woocommerce_payment_complete_order_status', $new_order_status, $this->id );

			$this->update_status( $new_order_status );

			add_post_meta( $this->id, '_paid_date', current_time('mysql'), true );

			$this_order = array(
				'ID' => $this->id,
				'post_date' => current_time( 'mysql', 0 ),
				'post_date_gmt' => current_time( 'mysql', 1 )
			);
			wp_update_post( $this_order );

			if ( apply_filters( 'woocommerce_payment_complete_reduce_order_stock', true, $this->id ) ) {
				$this->reduce_order_stock(); // Payment is complete so reduce stock levels
			}

			do_action( 'woocommerce_payment_complete', $this->id );

		} else {

			do_action( 'woocommerce_payment_complete_order_status_' . $this->status, $this->id );

		}
	}


	/**
	 * Record sales
	 *
	 * @access public
	 * @return void
	 */
	public function record_product_sales() {

		if ( 'yes' == get_post_meta( $this->id, '_recorded_sales', true ) ) {
			return;
		}

		if ( sizeof( $this->get_items() ) > 0 ) {
			foreach ( $this->get_items() as $item ) {
				if ( $item['product_id'] > 0 ) {
					$sales = (int) get_post_meta( $item['product_id'], 'total_sales', true );
					$sales += (int) $item['qty'];
					if ( $sales ) {
						update_post_meta( $item['product_id'], 'total_sales', $sales );
					}
				}
			}
		}

		update_post_meta( $this->id, '_recorded_sales', 'yes' );
	}


	/**
	 * Get coupon codes only.
	 *
	 * @access public
	 * @return array
	 */
	public function get_used_coupons() {

		$codes   = array();
		$coupons = $this->get_items( 'coupon' );

		foreach ( $coupons as $item_id => $item ) {
			$codes[] = trim( $item['name'] );
		}

		return $codes;
	}


	/**
	 * Increase applied coupon counts
	 *
	 * @access public
	 * @return void
	 */
	public function increase_coupon_usage_counts() {

		if ( 'yes' == get_post_meta( $this->id, '_recorded_coupon_usage_counts', true ) ) {
			return;
		}

		if ( sizeof( $this->get_used_coupons() ) > 0 ) {
			foreach ( $this->get_used_coupons() as $code ) {
				if ( ! $code ) {
					continue;
				}

				$coupon = new WC_Coupon( $code );

				$used_by = $this->user_id;
				if ( ! $used_by ) {
					$used_by = $this->billing_email;
				}

				$coupon->inc_usage_count( $used_by );
			}
		}

		update_post_meta( $this->id, '_recorded_coupon_usage_counts', 'yes' );
	}


	/**
	 * Decrease applied coupon counts
	 *
	 * @access public
	 * @return void
	 */
	public function decrease_coupon_usage_counts() {

		if ( 'yes' != get_post_meta( $this->id, '_recorded_coupon_usage_counts', true ) ) {
			return;
		}

		if ( sizeof( $this->get_used_coupons() ) > 0 ) {
			foreach ( $this->get_used_coupons() as $code ) {
				if ( ! $code ) {
					continue;
				}

				$coupon = new WC_Coupon( $code );

				$used_by = $this->user_id;
				if ( ! $used_by ) {
					$used_by = $this->billing_email;
				}

				$coupon->dcr_usage_count( $used_by );
			}
		}

		delete_post_meta( $this->id, '_recorded_coupon_usage_counts' );
	}


	/**
	 * Reduce stock levels
	 *
	 * @access public
	 * @return void
	 */
	public function reduce_order_stock() {

		if ( 'yes' == get_option('woocommerce_manage_stock') && sizeof( $this->get_items() ) > 0 ) {

			// Reduce stock levels and do any other actions with products in the cart
			foreach ( $this->get_items() as $item ) {

				if ( $item['product_id'] > 0 ) {
					$_product = $this->get_product_from_item( $item );

					if ( $_product && $_product->exists() && $_product->managing_stock() ) {

						$old_stock = $_product->stock;

						$qty = apply_filters( 'woocommerce_order_item_quantity', $item['qty'], $this, $item );

						$new_quantity = $_product->reduce_stock( $qty );

						$this->add_order_note( sprintf( __( 'Item #%s stock reduced from %s to %s.', 'woocommerce' ), $item['product_id'], $old_stock, $new_quantity) );

						$this->send_stock_notifications( $_product, $new_quantity, $item['qty'] );

					}

				}

			}

			do_action( 'woocommerce_reduce_order_stock', $this );

			$this->add_order_note( __( 'Order item stock reduced successfully.', 'woocommerce' ) );
		}

	}


	/**
	 * send_stock_notifications function.
	 *
	 * @access public
	 * @param object $product
	 * @param int $new_stock
	 * @param int $qty_ordered
	 * @return void
	 */
	public function send_stock_notifications( $product, $new_stock, $qty_ordered ) {

		// Backorders
		if ( $new_stock < 0 ) {
			do_action( 'woocommerce_product_on_backorder', array( 'product' => $product, 'order_id' => $this->id, 'quantity' => $qty_ordered ) );
		}

		// stock status notifications
		$notification_sent = false;

		if ( 'yes' == get_option( 'woocommerce_notify_no_stock' ) && get_option('woocommerce_notify_no_stock_amount') >= $new_stock ) {
			do_action( 'woocommerce_no_stock', $product );
			$notification_sent = true;
		}
		if ( ! $notification_sent && 'yes' == get_option( 'woocommerce_notify_low_stock' ) && get_option('woocommerce_notify_low_stock_amount') >= $new_stock ) {
			do_action( 'woocommerce_low_stock', $product );
			$notification_sent = true;
		}

	}


	/**
	 * List order notes (public) for the customer
	 *
	 * @access public
	 * @return array
	 */
	public function get_customer_order_notes() {

		$notes = array();

		$args = array(
			'post_id' => $this->id,
			'approve' => 'approve',
			'type' => ''
		);

		remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );

		$comments = get_comments( $args );

		foreach ( $comments as $comment ) {
			$is_customer_note = get_comment_meta( $comment->comment_ID, 'is_customer_note', true );
			$comment->comment_content = make_clickable( $comment->comment_content );
			if ( $is_customer_note ) {
				$notes[] = $comment;
			}
		}

		add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );

		return (array) $notes;

	}

	/**
	 * Checks if an order needs payment, based on status and order total
	 *
	 * @access public
	 * @return bool
	 */
	public function needs_payment() {
		$valid_order_statuses = apply_filters( 'woocommerce_valid_order_statuses_for_payment', array( 'pending', 'failed' ), $this );

		if ( in_array( $this->status, $valid_order_statuses ) && $this->get_total() > 0 ) {
			$needs_payment = true;
		} else {
			$needs_payment = false;
		}

		return apply_filters( 'woocommerce_order_needs_payment', $needs_payment, $this, $valid_order_statuses );
	}
}

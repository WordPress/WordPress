<?php
/**
 * WooCommerce coupons
 *
 * The WooCommerce coupons class gets coupon data from storage and checks coupon validity
 *
 * @class 		WC_Coupon
 * @package		WooCommerce/Classes
 * @category	Class
 * @author		WooThemes
 */
class WC_Coupon {

	// Coupon message codes
	const E_WC_COUPON_INVALID_FILTERED               = 100;
	const E_WC_COUPON_INVALID_REMOVED                = 101;
	const E_WC_COUPON_NOT_YOURS_REMOVED              = 102;
	const E_WC_COUPON_ALREADY_APPLIED                = 103;
	const E_WC_COUPON_ALREADY_APPLIED_INDIV_USE_ONLY = 104;
	const E_WC_COUPON_NOT_EXIST                      = 105;
	const E_WC_COUPON_USAGE_LIMIT_REACHED            = 106;
	const E_WC_COUPON_EXPIRED                        = 107;
	const E_WC_COUPON_MIN_SPEND_LIMIT_NOT_MET        = 108;
	const E_WC_COUPON_NOT_APPLICABLE                 = 109;
	const E_WC_COUPON_NOT_VALID_SALE_ITEMS           = 110;
	const E_WC_COUPON_PLEASE_ENTER                   = 111;
	const WC_COUPON_SUCCESS                          = 200;
	const WC_COUPON_REMOVED                          = 201;

	/** @public string Coupon code. */
	public $code;

	/** @public int Coupon ID. */
	public $id;

	/** @public string Type of discount. */
	public $type;

	/** @public string Type of discount (alias). */
	public $discount_type;

	/** @public string Coupon amount. */
	public $amount;

	/** @public string "Yes" if for individual use. */
	public $individual_use;

	/** @public array Array of product IDs. */
	public $product_ids;

	/** @public int Coupon usage limit. */
	public $usage_limit;

	/** @public int Coupon usage limit per user. */
	public $usage_limit_per_user;

	/** @public int Coupon usage limit per item. */
	public $limit_usage_to_x_items;

	/** @public int Coupon usage count. */
	public $usage_count;

	/** @public string Expiry date. */
	public $expiry_date;

	/** @public string "yes" if applied before tax. */
	public $apply_before_tax;

	/** @public string "yes" if coupon grants free shipping. */
	public $free_shipping;

	/** @public array Array of category ids. */
	public $product_categories;

	/** @public array Array of category ids. */
	public $exclude_product_categories;

	/** @public string "yes" if coupon does NOT apply to items on sale. */
	public $exclude_sale_items;

	/** @public string Minimum cart amount. */
	public $minimum_amount;

	/** @public string Coupon owner's email. */
	public $customer_email;

	/** @public array Post meta. */
	public $coupon_custom_fields;

	/** @public string How much the coupon is worth. */
	public $coupon_amount;

	/** @public string Error message. */
	public $error_message;

	/**
	 * Coupon constructor. Loads coupon data.
	 *
	 * @access public
	 * @param mixed $code code of the coupon to load
	 */
	public function __construct( $code ) {
		global $wpdb;

		$this->code  = apply_filters( 'woocommerce_coupon_code', $code );

		// Coupon data lets developers create coupons through code
		$coupon_data = apply_filters( 'woocommerce_get_shop_coupon_data', false, $code );

        if ( $coupon_data ) {

			$this->id                         = absint( $coupon_data['id'] );
			$this->type                       = esc_html( $coupon_data['type'] );
			$this->amount                     = esc_html( $coupon_data['amount'] );
			$this->individual_use             = esc_html( $coupon_data['individual_use'] );
			$this->product_ids                = is_array( $coupon_data['product_ids'] ) ? $coupon_data['product_ids'] : array();
			$this->exclude_product_ids        = is_array( $coupon_data['exclude_product_ids'] ) ? $coupon_data['exclude_product_ids'] : array();
			$this->usage_limit                = absint( $coupon_data['usage_limit'] );
			$this->usage_limit_per_user       = isset( $coupon_data['usage_limit_per_user'] ) ? absint( $coupon_data['usage_limit_per_user'] ) : 0;
			$this->limit_usage_to_x_items     = isset( $coupon_data['limit_usage_to_x_items'] ) ? absint( $coupon_data['limit_usage_to_x_items'] ) : '';
			$this->usage_count                = absint( $coupon_data['usage_count'] );
			$this->expiry_date                = esc_html( $coupon_data['expiry_date'] );
			$this->apply_before_tax           = esc_html( $coupon_data['apply_before_tax'] );
			$this->free_shipping              = esc_html( $coupon_data['free_shipping'] );
			$this->product_categories         = is_array( $coupon_data['product_categories'] ) ? $coupon_data['product_categories'] : array();
			$this->exclude_product_categories = is_array( $coupon_data['exclude_product_categories'] ) ? $coupon_data['exclude_product_categories'] : array();
			$this->exclude_sale_items         = esc_html( $coupon_data['exclude_sale_items'] );
			$this->minimum_amount             = esc_html( $coupon_data['minimum_amount'] );
			$this->customer_email             = esc_html( $coupon_data['customer_email'] );

        } else {

            $coupon_id 	= $wpdb->get_var( $wpdb->prepare( apply_filters( 'woocommerce_coupon_code_query', "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish'" ), $this->code ) );

            if ( ! $coupon_id )
            	return;

			$coupon             = get_post( $coupon_id );
			$this->post_title   = apply_filters( 'woocommerce_coupon_code', $coupon->post_title );

            if ( empty( $coupon ) || $this->code !== $this->post_title )
            	return;

            $this->id                   = $coupon->ID;
            $this->coupon_custom_fields = get_post_meta( $this->id );

            $load_data = array(
				'discount_type'              => 'fixed_cart',
				'coupon_amount'              => 0,
				'individual_use'             => 'no',
				'product_ids'                => '',
				'exclude_product_ids'        => '',
				'usage_limit'                => '',
				'usage_limit_per_user'       => '',
				'limit_usage_to_x_items'     => '',
				'usage_count'                => '',
				'expiry_date'                => '',
				'apply_before_tax'           => 'yes',
				'free_shipping'              => 'no',
				'product_categories'         => array(),
				'exclude_product_categories' => array(),
				'exclude_sale_items'         => 'no',
				'minimum_amount'             => '',
				'customer_email'             => array()
            );

            foreach ( $load_data as $key => $default )
            	$this->$key = isset( $this->coupon_custom_fields[ $key ][0] ) && $this->coupon_custom_fields[ $key ][0] !== '' ? $this->coupon_custom_fields[ $key ][0] : $default;

            // Alias
            $this->type                    = $this->discount_type;
            $this->amount                  = $this->coupon_amount;

            // Formatting
            $this->product_ids                = array_filter( array_map( 'trim', explode( ',', $this->product_ids ) ) );
            $this->exclude_product_ids        = array_filter( array_map( 'trim', explode( ',', $this->exclude_product_ids ) ) );
 			$this->expiry_date                = $this->expiry_date ? strtotime( $this->expiry_date ) : '';
            $this->product_categories         = array_filter( array_map( 'trim', (array) maybe_unserialize( $this->product_categories ) ) );
       		$this->exclude_product_categories = array_filter( array_map( 'trim', (array) maybe_unserialize( $this->exclude_product_categories ) ) );
			$this->customer_email             = array_filter( array_map( 'trim', array_map( 'strtolower', (array) maybe_unserialize( $this->customer_email ) ) ) );
        }

        do_action( 'woocommerce_coupon_loaded', $this );
	}


	/**
	 * Check if coupon needs applying before tax.
	 *
	 * @access public
	 * @return bool
	 */
	public function apply_before_tax() {
		return $this->apply_before_tax == 'yes' ? true : false;
	}


	/**
	 * Check if a coupon enables free shipping.
	 *
	 * @access public
	 * @return bool
	 */
	public function enable_free_shipping() {
		return $this->free_shipping == 'yes' ? true : false;
	}


	/**
	 * Check if a coupon excludes sale items.
	 *
	 * @access public
	 * @return bool
	 */
	public function exclude_sale_items() {
		return $this->exclude_sale_items == 'yes' ? true : false;
	}



	/**
	 * Increase usage count fo current coupon.
	 *
	 * @access public
	 * @param  string $used_by Either user ID or billing email
	 * @return void
	 */
	public function inc_usage_count( $used_by = '' ) {
		$this->usage_count++;
		update_post_meta( $this->id, 'usage_count', $this->usage_count );

		if ( $used_by ) {
			add_post_meta( $this->id, '_used_by', strtolower( $used_by ) );
		}
	}


	/**
	 * Decrease usage count fo current coupon.
	 *
	 * @access public
	 * @param  string $used_by Either user ID or billing email
	 * @return void
	 */
	public function dcr_usage_count( $used_by = '' ) {
		global $wpdb;

		$this->usage_count--;
		update_post_meta( $this->id, 'usage_count', $this->usage_count );

		// Delete 1 used by meta
		$meta_id = $wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE meta_key = '_used_by' AND meta_value = %s AND post_id = %d LIMIT 1;", $used_by, $this->id ) );
		if ( $meta_id ) {
			delete_metadata_by_mid( 'post', $meta_id );
		}
	}

	/**
	 * Returns the error_message string
	 *
	 * @access public
	 * @return string
	 */
	public function get_error_message() {
		return $this->error_message;
	}

	/**
	 * is_valid function.
	 *
	 * Check if a coupon is valid. Return a reason code if invalid. Reason codes:
	 *
	 * @access public
	 * @return bool|WP_Error validity or a WP_Error if not valid
	 */
	public function is_valid() {

		$error_code = null;
		$valid      = true;
		$error      = false;

		if ( $this->id ) {

			// Usage Limit
			if ( $this->usage_limit > 0 ) {
				if ( $this->usage_count >= $this->usage_limit ) {
					$valid = false;
					$error_code = self::E_WC_COUPON_USAGE_LIMIT_REACHED;
				}
			}

			// Per user usage limit - check here if user is logged in (against user IDs)
			// Checked again for emails later on in WC_Cart::check_customer_coupons()
			if ( $this->usage_limit_per_user > 0 && is_user_logged_in() ) {
				$used_by     = (array) get_post_meta( $this->id, '_used_by' );
				$usage_count = sizeof( array_keys( $used_by, get_current_user_id() ) );

				if ( $usage_count >= $this->usage_limit_per_user ) {
					$valid = false;
					$error_code = self::E_WC_COUPON_USAGE_LIMIT_REACHED;
				}
			}

			// Expired
			if ( $this->expiry_date ) {
				if ( current_time( 'timestamp' ) > $this->expiry_date ) {
					$valid = false;
					$error_code = self::E_WC_COUPON_EXPIRED;
				}
			}

			// Minimum spend
			if ( $this->minimum_amount > 0 ) {
				if ( $this->minimum_amount > WC()->cart->subtotal ) {
					$valid = false;
					$error_code = self::E_WC_COUPON_MIN_SPEND_LIMIT_NOT_MET;
				}
			}

			// Product ids - If a product included is found in the cart then its valid
			if ( sizeof( $this->product_ids ) > 0 ) {
				$valid_for_cart = false;
				if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
					foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

						if ( in_array( $cart_item['product_id'], $this->product_ids ) || in_array( $cart_item['variation_id'], $this->product_ids ) || in_array( $cart_item['data']->get_parent(), $this->product_ids ) )
							$valid_for_cart = true;
					}
				}
				if ( ! $valid_for_cart ) {
					$valid = false;
					$error_code = self::E_WC_COUPON_NOT_APPLICABLE;
				}
			}

			// Category ids - If a product included is found in the cart then its valid
			if ( sizeof( $this->product_categories ) > 0 ) {
				$valid_for_cart = false;
				if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
					foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

						$product_cats = wp_get_post_terms($cart_item['product_id'], 'product_cat', array("fields" => "ids"));

						if ( sizeof( array_intersect( $product_cats, $this->product_categories ) ) > 0 )
							$valid_for_cart = true;
					}
				}
				if ( ! $valid_for_cart ) {
					$valid = false;
					$error_code = self::E_WC_COUPON_NOT_APPLICABLE;
				}
			}

			// Cart discounts cannot be added if non-eligble product is found in cart
			if ( $this->type != 'fixed_product' && $this->type != 'percent_product' ) {

				// Exclude Products
				if ( sizeof( $this->exclude_product_ids ) > 0 ) {
					$valid_for_cart = true;
					if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
						foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							if ( in_array( $cart_item['product_id'], $this->exclude_product_ids ) || in_array( $cart_item['variation_id'], $this->exclude_product_ids ) || in_array( $cart_item['data']->get_parent(), $this->exclude_product_ids ) ) {
								$valid_for_cart = false;
							}
						}
					}
					if ( ! $valid_for_cart ) {
						$valid = false;
						$error_code = self::E_WC_COUPON_NOT_APPLICABLE;
					}
				}

				// Exclude Sale Items
				if ( $this->exclude_sale_items == 'yes' ) {
					$valid_for_cart = true;
					$product_ids_on_sale = wc_get_product_ids_on_sale();
					if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
						foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							if ( in_array( $cart_item['product_id'], $product_ids_on_sale, true ) || in_array( $cart_item['variation_id'], $product_ids_on_sale, true ) || in_array( $cart_item['data']->get_parent(), $product_ids_on_sale, true ) ) {
								$valid_for_cart = false;
							}
						}
					}
					if ( ! $valid_for_cart ) {
						$valid = false;
						$error_code = self::E_WC_COUPON_NOT_VALID_SALE_ITEMS;
					}
				}

				// Exclude Categories
				if ( sizeof( $this->exclude_product_categories ) > 0 ) {
					$valid_for_cart = true;
					if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
						foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

							$product_cats = wp_get_post_terms( $cart_item['product_id'], 'product_cat', array( "fields" => "ids" ) );

							if ( sizeof( array_intersect( $product_cats, $this->exclude_product_categories ) ) > 0 )
								$valid_for_cart = false;
						}
					}
					if ( ! $valid_for_cart ) {
						$valid = false;
						$error_code = self::E_WC_COUPON_NOT_APPLICABLE;
					}
				}
			}

			$valid = apply_filters( 'woocommerce_coupon_is_valid', $valid, $this );

			if ( $valid ) {
				return true;
			} else {
				if ( is_null( $error_code ) )
					$error_code = self::E_WC_COUPON_INVALID_FILTERED;
			}

		} else {
			$error_code = self::E_WC_COUPON_NOT_EXIST;
		}

		if ( $error_code )
			$this->error_message = $this->get_coupon_error( $error_code );

		return false;
	}

	/**
	 * Check if a coupon is valid
	 *
	 * @return bool
	 */
	public function is_valid_for_cart() {
		if ( $this->type != 'fixed_cart' && $this->type != 'percent' )
			return false;
		else
			return true;
	}

	/**
	 * Check if a coupon is valid for a product
	 * 
	 * @param  WC_Product  $product
	 * @return boolean
	 */
	public function is_valid_for_product( $product ) {
		if ( $this->type != 'fixed_product' && $this->type != 'percent_product' )
			return false;

		$valid        = false;
		$product_cats = wp_get_post_terms( $product->id, 'product_cat', array( "fields" => "ids" ) );

		// Specific products get the discount
		if ( sizeof( $this->product_ids ) > 0 ) {

			if ( in_array( $product->id, $this->product_ids ) || ( isset( $product->variation_id ) && in_array( $product->variation_id, $this->product_ids ) ) || in_array( $product->get_parent(), $this->product_ids ) )
				$valid = true;

		// Category discounts
		} elseif ( sizeof( $this->product_categories ) > 0 ) {

			if ( sizeof( array_intersect( $product_cats, $this->product_categories ) ) > 0 )
				$valid = true;

		} else {
			// No product ids - all items discounted
			$valid = true;
		}

		// Specific product ID's excluded from the discount
		if ( sizeof( $this->exclude_product_ids ) > 0 )
			if ( in_array( $product->id, $this->exclude_product_ids ) || ( isset( $product->variation_id ) && in_array( $product->variation_id, $this->exclude_product_ids ) ) || in_array( $product->get_parent(), $this->exclude_product_ids ) )
				$valid = false;

		// Specific categories excluded from the discount
		if ( sizeof( $this->exclude_product_categories ) > 0 )
			if ( sizeof( array_intersect( $product_cats, $this->exclude_product_categories ) ) > 0 )
				$valid = false;

		// Sale Items excluded from discount
		if ( $this->exclude_sale_items == 'yes' ) {
			$product_ids_on_sale = wc_get_product_ids_on_sale();

			if ( in_array( $product->id, $product_ids_on_sale, true ) || ( isset( $product->variation_id ) && in_array( $product->variation_id, $product_ids_on_sale, true ) ) || in_array( $product->get_parent(), $product_ids_on_sale, true ) )
				$valid = false;
		}

		return apply_filters( 'woocommerce_coupon_is_valid_for_product', $valid, $product, $this );
	}

	/**
	 * Get discount amount for a cart item
	 * 
	 * @param  float $discounting_amount Amount the coupon is being applied to
	 * @param  array|null $cart_item Cart item being discounted if applicable
	 * @param  boolean $single True if discounting a single qty item, false if its the line
	 * @return float Amount this coupon has discounted
	 */
	public function get_discount_amount( $discounting_amount, $cart_item = null, $single = false ) {
		$discount = 0;

		if ( $this->type == 'fixed_product') {

			$discount = $discounting_amount < $this->amount ? $discounting_amount : $this->amount;

			// If dealing with a line and not a single item, we need to multiple fixed discount by cart item qty.
			if ( ! $single && ! is_null( $cart_item ) ) {
				// Discount for the line.
				$discount = $discount * $cart_item['quantity'];
			}

		} elseif ( $this->type == 'percent_product' || $this->type == 'percent' ) {

			$discount = round( ( $discounting_amount / 100 ) * $this->amount, WC()->cart->dp );

		} elseif ( $this->type == 'fixed_cart' ) {
			if ( ! is_null( $cart_item ) ) {
				/**
				 * This is the most complex discount - we need to divide the discount between rows based on their price in
				 * proportion to the subtotal. This is so rows with different tax rates get a fair discount, and so rows
				 * with no price (free) don't get discounted.
				 *
				 * Get item discount by dividing item cost by subtotal to get a %
				 */
				$discount_percent = 0;

				if ( WC()->cart->subtotal_ex_tax ) {
					$discount_percent = ( $cart_item['data']->get_price_excluding_tax() * $cart_item['quantity'] ) / WC()->cart->subtotal_ex_tax;
				}
					
				$discount = min( ( $this->amount * $discount_percent ) / $cart_item['quantity'], $discounting_amount );
			} else {
				$discount = min( $this->amount, $discounting_amount );
			}
		}

		// Handle the limit_usage_to_x_items option
		if ( in_array( $this->type, array( 'percent_product', 'fixed_product' ) ) && ! is_null( $cart_item ) ) {
			$qty = empty( $this->limit_usage_to_x_items ) ? $cart_item['quantity'] : min( $this->limit_usage_to_x_items, $cart_item['quantity'] );

			if ( $single ) {
				$discount = ( $discount * $qty ) / $cart_item['quantity'];
			} else {
				$discount = ( $discount / $cart_item['quantity'] ) * $qty;
			}
		}

		return apply_filters( 'woocommerce_coupon_get_discount_amount', $discount, $discounting_amount, $cart_item, $single, $this );
	}

	/**
	 * Converts one of the WC_Coupon message/error codes to a message string and
	 * displays the message/error.
	 *
	 * @access public
	 * @param int $msg_code Message/error code.
	 * @return void
	 */
	public function add_coupon_message( $msg_code ) {

		if ( $msg_code < 200 )
			wc_add_notice( $this->get_coupon_error( $msg_code ), 'error' );
		else
			wc_add_notice( $this->get_coupon_message( $msg_code ) );
	}

	/**
	 * Map one of the WC_Coupon message codes to a message string
	 *
	 * @access public
	 * @param mixed $msg_code
	 * @return string| Message/error string
	 */
	public function get_coupon_message( $msg_code ) {

		switch ( $msg_code ) {
			case self::WC_COUPON_SUCCESS :
				$msg = __( 'Coupon code applied successfully.', 'woocommerce' );
			break;
			case self::WC_COUPON_REMOVED :
				$msg = __( 'Coupon code removed successfully.', 'woocommerce' );
			break;
			default:
				$msg = '';
			break;
		}

		return apply_filters( 'woocommerce_coupon_message', $msg, $msg_code, $this );
	}

	/**
	 * Map one of the WC_Coupon error codes to a message string
	 *
	 * @access public
	 * @param int $err_code Message/error code.
	 * @return string| Message/error string
	 */
	public function get_coupon_error( $err_code ) {

		switch ( $err_code ) {
			case self::E_WC_COUPON_INVALID_FILTERED:
				$err = __( 'Coupon is not valid.', 'woocommerce' );
			break;
			case self::E_WC_COUPON_NOT_EXIST:
				$err = __( 'Coupon does not exist!', 'woocommerce' );
			break;
			case self::E_WC_COUPON_INVALID_REMOVED:
				$err = sprintf( __( 'Sorry, it seems the coupon "%s" is invalid - it has now been removed from your order.', 'woocommerce' ), $this->code );
			break;
			case self::E_WC_COUPON_NOT_YOURS_REMOVED:
				$err = sprintf( __( 'Sorry, it seems the coupon "%s" is not yours - it has now been removed from your order.', 'woocommerce' ), $this->code );
			break;
			case self::E_WC_COUPON_ALREADY_APPLIED:
				$err = __( 'Coupon code already applied!', 'woocommerce' );
			break;
			case self::E_WC_COUPON_ALREADY_APPLIED_INDIV_USE_ONLY:
				$err = sprintf( __( 'Sorry, coupon "%s" has already been applied and cannot be used in conjunction with other coupons.', 'woocommerce' ), $this->code );
			break;
			case self::E_WC_COUPON_USAGE_LIMIT_REACHED:
				$err = __( 'Coupon usage limit has been reached.', 'woocommerce' );
			break;
			case self::E_WC_COUPON_EXPIRED:
				$err = __( 'This coupon has expired.', 'woocommerce' );
			break;
			case self::E_WC_COUPON_MIN_SPEND_LIMIT_NOT_MET:
				$err = sprintf( __( 'The minimum spend for this coupon is %s.', 'woocommerce' ), wc_price( $this->minimum_amount ) );
			break;
			case self::E_WC_COUPON_NOT_APPLICABLE:
				$err = __( 'Sorry, this coupon is not applicable to your cart contents.', 'woocommerce' );
			break;
			case self::E_WC_COUPON_NOT_VALID_SALE_ITEMS:
				$err = __( 'Sorry, this coupon is not valid for sale items.', 'woocommerce' );
			break;
			default:
				$err = '';
			break;
		}

		return apply_filters( 'woocommerce_coupon_error', $err, $err_code, $this );
	}

	/**
	 * Map one of the WC_Coupon error codes to an error string
	 * No coupon instance will be available where a coupon does not exist,
	 * so this static method exists.
	 *
	 * @access public
	 * @param int $err_code Error code
	 * @return string| Error string
	 */
	public static function get_generic_coupon_error( $err_code ) {

		switch ( $err_code ) {
			case self::E_WC_COUPON_NOT_EXIST:
				$err = __( 'Coupon does not exist!', 'woocommerce' );
			break;
			case self::E_WC_COUPON_PLEASE_ENTER:
				$err = __( 'Please enter a coupon code.', 'woocommerce' );
			break;
			default:
				$err = '';
			break;
		}

		// When using this static method, there is no $this to pass to filter
		return apply_filters( 'woocommerce_coupon_error', $err, $err_code, null );
	}

}

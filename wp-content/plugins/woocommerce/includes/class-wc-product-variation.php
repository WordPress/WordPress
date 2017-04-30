<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Product Variation Class
 *
 * The WooCommerce product variation class handles product variation data.
 *
 * @class 		WC_Product_Variation
 * @version		2.1.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Product_Variation extends WC_Product {

	/** @public int ID of the variable product. */
	public $variation_id;

	/** @public object Parent Variable product object. */
	public $parent;

	/** @public array Stores variation data (attributes) for the current variation. */
	public $variation_data = array();

	/** @public bool True if the variation has a length. */
	public $variation_has_length = false;

	/** @public bool True if the variation has a width. */
	public $variation_has_width = false;

	/** @public bool True if the variation has a height. */
	public $variation_has_height = false;

	/** @public bool True if the variation has a weight. */
	public $variation_has_weight = false;

	/** @public bool True if the variation has stock and is managing stock. */
	public $variation_has_stock = false;

	/** @public bool True if the variation has a sku. */
	public $variation_has_sku = false;

	/** @public string Stores the shipping class of the variation. */
	public $variation_shipping_class = false;

	/** @public int Stores the shipping class ID of the variation. */
	public $variation_shipping_class_id = false;

	/** @public bool True if the variation has a tax class. */
	public $variation_has_tax_class = false;

	/** @public bool True if the variation has file paths. */
	public $variation_has_downloadable_files = false;

	/**
	 * Loads all product data from custom fields
	 *
	 * @access public
	 * @param int $variation_id ID of the variation to load
	 * @param array $args Array of the arguments containing parent product data
	 * @return void
	 */
	public function __construct( $variation, $args = array() ) {

		$this->product_type = 'variation';

		if ( is_object( $variation ) ) {
			$this->variation_id = absint( $variation->ID );
		} else {
			$this->variation_id = absint( $variation );
		}

		/* Get main product data from parent (args) */
		$this->id   = ! empty( $args['parent_id'] ) ? intval( $args['parent_id'] ) : wp_get_post_parent_id( $this->variation_id );

		// The post doesn't have a parent id, therefore its invalid.
		if ( empty( $this->id ) )
			return;

		// Get post data
		$this->parent = ! empty( $args['parent'] ) ? $args['parent'] : get_product( $this->id );
		$this->post   = ! empty( $this->parent->post ) ? $this->parent->post : array();
		$this->product_custom_fields = get_post_meta( $this->variation_id );

		// Get the variation attributes from meta
		foreach ( $this->product_custom_fields as $name => $value ) {
			if ( ! strstr( $name, 'attribute_' ) )
				continue;

			$this->variation_data[ $name ] = sanitize_title( $value[0] );
		}

		// Now get variation meta to override the parent variable product
		if ( ! empty( $this->product_custom_fields['_sku'][0] ) ) {
			$this->variation_has_sku = true;
			$this->sku               = $this->product_custom_fields['_sku'][0];
		}

		if ( ! empty( $this->product_custom_fields['_downloadable_files'][0] ) ) {
			$this->variation_has_downloadable_files = true;
			$this->downloadable_files               = $this->product_custom_fields['_downloadable_files'][0];
		}

		if ( isset( $this->product_custom_fields['_stock'][0] ) && '' !== $this->product_custom_fields['_stock'][0] && 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$this->variation_has_stock = true;
			$this->manage_stock        = 'yes';
			$this->stock               = $this->product_custom_fields['_stock'][0];
		}

		if ( isset( $this->product_custom_fields['_weight'][0] ) && $this->product_custom_fields['_weight'][0] !== '' ) {
			$this->variation_has_weight = true;
			$this->weight               = $this->product_custom_fields['_weight'][0];
		}

		if ( isset( $this->product_custom_fields['_length'][0] ) && $this->product_custom_fields['_length'][0] !== '' ) {
			$this->variation_has_length = true;
			$this->length               = $this->product_custom_fields['_length'][0];
		}

		if ( isset( $this->product_custom_fields['_width'][0] ) && $this->product_custom_fields['_width'][0] !== '' ) {
			$this->variation_has_width = true;
			$this->width               = $this->product_custom_fields['_width'][0];
		}

		if ( isset( $this->product_custom_fields['_height'][0] ) && $this->product_custom_fields['_height'][0] !== '' ) {
			$this->variation_has_height = true;
			$this->height               = $this->product_custom_fields['_height'][0];
		}

		if ( isset( $this->product_custom_fields['_downloadable'][0] ) && $this->product_custom_fields['_downloadable'][0] == 'yes' ) {
			$this->downloadable = 'yes';
		} else {
			$this->downloadable = 'no';
		}

		if ( isset( $this->product_custom_fields['_virtual'][0] ) && $this->product_custom_fields['_virtual'][0] == 'yes' ) {
			$this->virtual = 'yes';
		} else {
			$this->virtual = 'no';
		}

		if ( isset( $this->product_custom_fields['_tax_class'][0] ) ) {
			$this->variation_has_tax_class = true;
			$this->tax_class               = $this->product_custom_fields['_tax_class'][0];
		}

		if ( isset( $this->product_custom_fields['_sale_price_dates_from'][0] ) )
			$this->sale_price_dates_from = $this->product_custom_fields['_sale_price_dates_from'][0];

		if ( isset( $this->product_custom_fields['_sale_price_dates_to'][0] ) )
			$this->sale_price_dates_to = $this->product_custom_fields['_sale_price_dates_to'][0];

		// Prices
		$this->price         = isset( $this->product_custom_fields['_price'][0] ) ? $this->product_custom_fields['_price'][0] : '';
		$this->regular_price = isset( $this->product_custom_fields['_regular_price'][0] ) ? $this->product_custom_fields['_regular_price'][0] : '';
		$this->sale_price    = isset( $this->product_custom_fields['_sale_price'][0] ) ? $this->product_custom_fields['_sale_price'][0] : '';

		// Backwards compat for prices
		if ( $this->price !== '' && $this->regular_price == '' ) {
			update_post_meta( $this->variation_id, '_regular_price', $this->price );
			$this->regular_price = $this->price;

			if ( $this->sale_price !== '' && $this->sale_price < $this->regular_price ) {
				update_post_meta( $this->variation_id, '_price', $this->sale_price );
				$this->price = $this->sale_price;
			}
		}

		$this->total_stock = $this->stock;
	}

	/**
	 * Returns whether or not the product post exists.
	 *
	 * @access public
	 * @return bool
	 */
	public function exists() {
		return empty( $this->id ) ? false : true;
	}

	/**
	 * Wrapper for get_permalink. Adds this variations attributes to the URL.
	 * @return string
	 */
	public function get_permalink() {
		return add_query_arg( $this->variation_data, get_permalink( $this->id ) );
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_url() {
		$url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( array_merge( array( 'variation_id' => $this->variation_id, 'add-to-cart' => $this->id ), $this->variation_data ) ) ) : get_permalink( $this->id );

		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}

	/**
	 * Get the add to cart button text
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_text() {
		$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add to cart', 'woocommerce' ) : __( 'Read More', 'woocommerce' );

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}

	/**
	 * Checks if this particular variation is visible (variations with no price, or out of stock, can be hidden)
	 *
	 * @return bool
	 */
	public function variation_is_visible() {
		$visible = true;

		// Published == enabled checkbox
		if ( get_post_status( $this->variation_id ) != 'publish' )
			$visible = false;

		// Out of stock visibility
		elseif ( get_option('woocommerce_hide_out_of_stock_items') == 'yes' && ! $this->is_in_stock() )
			$visible = false;

		// Price not set
		elseif ( $this->get_price() === "" )
			$visible = false;

		return apply_filters( 'woocommerce_variation_is_visible', $visible, $this->variation_id, $this->id );
	}

	/**
	 * Returns false if the product cannot be bought.
	 *
	 * @access public
	 * @return bool
	 */
	public function is_purchasable() {

		// Published == enabled checkbox
		if ( get_post_status( $this->variation_id ) != 'publish' )
			$purchasable = false;

		else
			$purchasable = parent::is_purchasable();

		return $purchasable;
	}

	/**
	 * Returns whether or not the variations parent is visible.
	 *
	 * @access public
	 * @return bool
	 */
	public function parent_is_visible() {
		return $this->is_visible();
	}

	/**
     * Get variation ID
     *
     * @return int
     */
    public function get_variation_id() {
        return absint( $this->variation_id );
    }

    /**
     * Get variation attribute values
     *
     * @return array of attributes and their values for this variation
     */
    public function get_variation_attributes() {
        return $this->variation_data;
    }

	/**
     * Get variation price HTML. Prices are not inherited from parents.
     *
     * @return string containing the formatted price
     */
	public function get_price_html( $price = '' ) {

		$tax_display_mode      = get_option( 'woocommerce_tax_display_shop' );
		$display_price         = $tax_display_mode == 'incl' ? $this->get_price_including_tax() : $this->get_price_excluding_tax();
		$display_regular_price = $tax_display_mode == 'incl' ? $this->get_price_including_tax( 1, $this->get_regular_price() ) : $this->get_price_excluding_tax( 1, $this->get_regular_price() );
		$display_sale_price    = $tax_display_mode == 'incl' ? $this->get_price_including_tax( 1, $this->get_sale_price() ) : $this->get_price_excluding_tax( 1, $this->get_sale_price() );

		if ( $this->get_price() !== '' ) {
			if ( $this->is_on_sale() ) {

				$price = '<del>' . wc_price( $display_regular_price ) . '</del> <ins>' . wc_price( $display_sale_price ) . '</ins>' . $this->get_price_suffix();

				$price = apply_filters( 'woocommerce_variation_sale_price_html', $price, $this );

			} elseif ( $this->get_price() > 0 ) {

				$price = wc_price( $display_price ) . $this->get_price_suffix();

				$price = apply_filters( 'woocommerce_variation_price_html', $price, $this );

			} else {

				$price = __( 'Free!', 'woocommerce' );

				$price = apply_filters( 'woocommerce_variation_free_price_html', $price, $this );

			}
		} else {
			$price = apply_filters( 'woocommerce_variation_empty_price_html', '', $this );
		}

		return apply_filters( 'woocommerce_get_variation_price_html', $price, $this );
	}

    /**
     * Gets the main product image ID.
     * @return int
     */
    public function get_image_id() {
    	if ( $this->variation_id && has_post_thumbnail( $this->variation_id ) ) {
			$image_id = get_post_thumbnail_id( $this->variation_id );
		} elseif ( has_post_thumbnail( $this->id ) ) {
			$image_id = get_post_thumbnail_id( $this->id );
		} elseif ( ( $parent_id = wp_get_post_parent_id( $this->id ) ) && has_post_thumbnail( $parent_id ) ) {
			$image_id = get_post_thumbnail_id( $parent_id );
		} else {
			$image_id = 0;
		}
		return $image_id;
    }

    /**
     * Gets the main product image.
     *
     * @access public
     * @param string $size (default: 'shop_thumbnail')
     * @return string
     */
    public function get_image( $size = 'shop_thumbnail', $attr = array() ) {
    	if ( $this->variation_id && has_post_thumbnail( $this->variation_id ) ) {
			$image = get_the_post_thumbnail( $this->variation_id, $size, $attr );
		} elseif ( has_post_thumbnail( $this->id ) ) {
			$image = get_the_post_thumbnail( $this->id, $size, $attr );
		} elseif ( ( $parent_id = wp_get_post_parent_id( $this->id ) ) && has_post_thumbnail( $parent_id ) ) {
			$image = get_the_post_thumbnail( $parent_id, $size , $attr);
		} else {
			$image = wc_placeholder_img( $size );
		}

		return $image;
    }

	/**
	 * Set stock level of the product variation.
	 * @param int  $amount
	 * @param bool $force_variation_stock If true, the variation's stock will be updated and not the parents.
	 * @return int
	 * @todo Need to return 0 if is_null? Or something. Should not be just return.
	 */
	function set_stock( $amount = null, $force_variation_stock = false ) {
		if ( is_null( $amount ) )
			return;

		if ( $amount === '' && $force_variation_stock ) {

			// If amount is an empty string, stock management is being turned off at variation level
			$this->variation_has_stock = false;
			$this->stock               = '';
			unset( $this->manage_stock );

			// Update meta
			update_post_meta( $this->variation_id, '_stock', '' );

			// Refresh parent prices
			WC_Product_Variable::sync( $this->id );

		} elseif ( $this->variation_has_stock || $force_variation_stock ) {

			// Update stock amount
			$this->stock               = intval( $amount );
			$this->variation_has_stock = true;
			$this->manage_stock        = 'yes';

			// Update meta
			update_post_meta( $this->variation_id, '_stock', $this->stock );

			// Clear total stock transient
			delete_transient( 'wc_product_total_stock_' . $this->id );

			// Check parents out of stock attribute
			if ( ! $this->is_in_stock() ) {

				// Check parent
				$parent_product = get_product( $this->id );

				// Only continue if the parent has backorders off and all children are stock managed and out of stock
				if ( ! $parent_product->backorders_allowed() && $parent_product->get_total_stock() <= get_option( 'woocommerce_notify_no_stock_amount' ) ) {

					$all_managed = true;

					if ( sizeof( $parent_product->get_children() ) > 0 ) {
						foreach ( $parent_product->get_children() as $child_id ) {
							$stock = get_post_meta( $child_id, '_stock', true );
							if ( $stock == '' ) {
								$all_managed = false;
								break;
							}
						}
					}

					if ( $all_managed ) {
						$this->set_stock_status( 'outofstock' );
					}
				}

			} elseif ( $this->is_in_stock() ) {
				$this->set_stock_status( 'instock' );
			}

			// Refresh parent prices
			WC_Product_Variable::sync( $this->id );

			// Trigger action
			do_action( 'woocommerce_product_set_stock', $this );

			return $this->get_stock_quantity();

		} else {

			return parent::set_stock( $amount );

		}
	}

	/**
	 * Reduce stock level of the product.
	 *
	 * @param int $by (default: 1) Amount to reduce by
	 * @return int stock level
	 */
	public function reduce_stock( $by = 1 ) {
		if ( $this->variation_has_stock ) {
			return $this->set_stock( $this->stock - $by );
		} else {
			return parent::reduce_stock( $by );
		}
	}

	/**
	 * Increase stock level of the product.
	 *
	 * @param int $by (default: 1) Amount to increase by
	 * @return int stock level
	 */
	public function increase_stock( $by = 1 ) {
		if ( $this->variation_has_stock ) {
			return $this->set_stock( $this->stock + $by );
		} else {
			return parent::increase_stock( $by );
		}
	}

	/**
	 * Get the shipping class, and if not set, get the shipping class of the parent.
	 *
	 * @access public
	 * @return string
	 */
	public function get_shipping_class() {
		if ( ! $this->variation_shipping_class ) {
			$classes = get_the_terms( $this->variation_id, 'product_shipping_class' );

			if ( $classes && ! is_wp_error( $classes ) ) {
				$this->variation_shipping_class = esc_attr( current( $classes )->slug );
			} else {
				$this->variation_shipping_class = parent::get_shipping_class();
			}
		}

		return $this->variation_shipping_class;
	}

	/**
	 * Returns the product shipping class ID.
	 *
	 * @access public
	 * @return int
	 */
	public function get_shipping_class_id() {
		if ( ! $this->variation_shipping_class_id ) {

			$classes = get_the_terms( $this->variation_id, 'product_shipping_class' );

			if ( $classes && ! is_wp_error( $classes ) )
				$this->variation_shipping_class_id = current( $classes )->term_id;
			else
				$this->variation_shipping_class_id = parent::get_shipping_class_id();

		}
		return absint( $this->variation_shipping_class_id );
	}

	/**
	 * Get product name with extra details such as SKU, price and attributes. Used within admin.
	 *
	 * @access public
	 * @param mixed $product
	 * @return string Formatted product name, including attributes and price
	 */
	public function get_formatted_name() {

		if ( $this->get_sku() )
			$identifier = $this->get_sku();
		else
			$identifier = '#' . $this->variation_id;

		$attributes = $this->get_variation_attributes();
		$extra_data = ' &ndash; ' . implode( ', ', $attributes ) . ' &ndash; ' . wc_price( $this->get_price() );

		return sprintf( __( '%s &ndash; %s%s', 'woocommerce' ), $identifier, $this->get_title(), $extra_data );
	}
}

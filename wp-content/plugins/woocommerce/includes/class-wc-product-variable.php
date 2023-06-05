<?php
/**
 * Variable Product
 *
 * The WooCommerce product class handles individual product data.
 *
 * @version 3.0.0
 * @package WooCommerce\Classes\Products
 */

defined( 'ABSPATH' ) || exit;

/**
 * Variable product class.
 */
class WC_Product_Variable extends WC_Product {

	/**
	 * Array of children variation IDs. Determined by children.
	 *
	 * @var array
	 */
	protected $children = null;

	/**
	 * Array of visible children variation IDs. Determined by children.
	 *
	 * @var array
	 */
	protected $visible_children = null;

	/**
	 * Array of variation attributes IDs. Determined by children.
	 *
	 * @var array
	 */
	protected $variation_attributes = null;

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'variable';
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the add to cart button text.
	 *
	 * @return string
	 */
	public function add_to_cart_text() {
		return apply_filters( 'woocommerce_product_add_to_cart_text', $this->is_purchasable() ? __( 'Select options', 'woocommerce' ) : __( 'Read more', 'woocommerce' ), $this );
	}

	/**
	 * Get the add to cart button text description - used in aria tags.
	 *
	 * @since 3.3.0
	 * @return string
	 */
	public function add_to_cart_description() {
		/* translators: %s: Product title */
		return apply_filters( 'woocommerce_product_add_to_cart_description', sprintf( __( 'Select options for &ldquo;%s&rdquo;', 'woocommerce' ), $this->get_name() ), $this );
	}

	/**
	 * Get an array of all sale and regular prices from all variations. This is used for example when displaying the price range at variable product level or seeing if the variable product is on sale.
	 *
	 * @param  bool $for_display If true, prices will be adapted for display based on the `woocommerce_tax_display_shop` setting (including or excluding taxes).
	 * @return array Array of RAW prices, regular prices, and sale prices with keys set to variation ID.
	 */
	public function get_variation_prices( $for_display = false ) {
		$prices = $this->data_store->read_price_data( $this, $for_display );

		foreach ( $prices as $price_key => $variation_prices ) {
			$prices[ $price_key ] = $this->sort_variation_prices( $variation_prices );
		}

		return $prices;
	}

	/**
	 * Get the min or max variation regular price.
	 *
	 * @param  string  $min_or_max Min or max price.
	 * @param  boolean $for_display If true, prices will be adapted for display based on the `woocommerce_tax_display_shop` setting (including or excluding taxes).
	 * @return string
	 */
	public function get_variation_regular_price( $min_or_max = 'min', $for_display = false ) {
		$prices = $this->get_variation_prices( $for_display );
		$price  = 'min' === $min_or_max ? current( $prices['regular_price'] ) : end( $prices['regular_price'] );

		return apply_filters( 'woocommerce_get_variation_regular_price', $price, $this, $min_or_max, $for_display );
	}

	/**
	 * Get the min or max variation sale price.
	 *
	 * @param  string  $min_or_max Min or max price.
	 * @param  boolean $for_display If true, prices will be adapted for display based on the `woocommerce_tax_display_shop` setting (including or excluding taxes).
	 * @return string
	 */
	public function get_variation_sale_price( $min_or_max = 'min', $for_display = false ) {
		$prices = $this->get_variation_prices( $for_display );
		$price  = 'min' === $min_or_max ? current( $prices['sale_price'] ) : end( $prices['sale_price'] );

		return apply_filters( 'woocommerce_get_variation_sale_price', $price, $this, $min_or_max, $for_display );
	}

	/**
	 * Get the min or max variation (active) price.
	 *
	 * @param  string  $min_or_max Min or max price.
	 * @param  boolean $for_display If true, prices will be adapted for display based on the `woocommerce_tax_display_shop` setting (including or excluding taxes).
	 * @return string
	 */
	public function get_variation_price( $min_or_max = 'min', $for_display = false ) {
		$prices = $this->get_variation_prices( $for_display );
		$price  = 'min' === $min_or_max ? current( $prices['price'] ) : end( $prices['price'] );

		return apply_filters( 'woocommerce_get_variation_price', $price, $this, $min_or_max, $for_display );
	}

	/**
	 * Returns the price in html format.
	 *
	 * Note: Variable prices do not show suffixes like other product types. This
	 * is due to some things like tax classes being set at variation level which
	 * could differ from the parent price. The only way to show accurate prices
	 * would be to load the variation and get it's price, which adds extra
	 * overhead and still has edge cases where the values would be inaccurate.
	 *
	 * Additionally, ranges of prices no longer show 'striked out' sale prices
	 * due to the strings being very long and unclear/confusing. A single range
	 * is shown instead.
	 *
	 * @param string $price Price (default: '').
	 * @return string
	 */
	public function get_price_html( $price = '' ) {
		$prices = $this->get_variation_prices( true );

		if ( empty( $prices['price'] ) ) {
			$price = apply_filters( 'woocommerce_variable_empty_price_html', '', $this );
		} else {
			$min_price     = current( $prices['price'] );
			$max_price     = end( $prices['price'] );
			$min_reg_price = current( $prices['regular_price'] );
			$max_reg_price = end( $prices['regular_price'] );

			if ( $min_price !== $max_price ) {
				$price = wc_format_price_range( $min_price, $max_price );
			} elseif ( $this->is_on_sale() && $min_reg_price === $max_reg_price ) {
				$price = wc_format_sale_price( wc_price( $max_reg_price ), wc_price( $min_price ) );
			} else {
				$price = wc_price( $min_price );
			}

			$price = apply_filters( 'woocommerce_variable_price_html', $price . $this->get_price_suffix(), $this );
		}

		return apply_filters( 'woocommerce_get_price_html', $price, $this );
	}

	/**
	 * Get the suffix to display after prices > 0.
	 *
	 * This is skipped if the suffix
	 * has dynamic values such as {price_excluding_tax} for variable products.
	 *
	 * @see get_price_html for an explanation as to why.
	 * @param  string  $price Price to calculate, left blank to just use get_price().
	 * @param  integer $qty   Quantity passed on to get_price_including_tax() or get_price_excluding_tax().
	 * @return string
	 */
	public function get_price_suffix( $price = '', $qty = 1 ) {
		$suffix = get_option( 'woocommerce_price_display_suffix' );

		if ( strstr( $suffix, '{' ) ) {
			return apply_filters( 'woocommerce_get_price_suffix', '', $this, $price, $qty );
		} else {
			return parent::get_price_suffix( $price, $qty );
		}
	}

	/**
	 * Return a products child ids.
	 *
	 * This is lazy loaded as it's not used often and does require several queries.
	 *
	 * @param bool|string $visible_only Visible only.
	 * @return array Children ids
	 */
	public function get_children( $visible_only = '' ) {
		if ( is_bool( $visible_only ) ) {
			wc_deprecated_argument( 'visible_only', '3.0', 'WC_Product_Variable::get_visible_children' );

			return $visible_only ? $this->get_visible_children() : $this->get_children();
		}

		if ( null === $this->children ) {
			$children = $this->data_store->read_children( $this );
			$this->set_children( $children['all'] );
			$this->set_visible_children( $children['visible'] );
		}

		return apply_filters( 'woocommerce_get_children', $this->children, $this, false );
	}

	/**
	 * Return a products child ids - visible only.
	 *
	 * This is lazy loaded as it's not used often and does require several queries.
	 *
	 * @since 3.0.0
	 * @return array Children ids
	 */
	public function get_visible_children() {
		if ( null === $this->visible_children ) {
			$children = $this->data_store->read_children( $this );
			$this->set_children( $children['all'] );
			$this->set_visible_children( $children['visible'] );
		}
		return apply_filters( 'woocommerce_get_children', $this->visible_children, $this, true );
	}

	/**
	 * Return an array of attributes used for variations, as well as their possible values.
	 *
	 * This is lazy loaded as it's not used often and does require several queries.
	 *
	 * @return array Attributes and their available values
	 */
	public function get_variation_attributes() {
		if ( null === $this->variation_attributes ) {
			$this->variation_attributes = $this->data_store->read_variation_attributes( $this );
		}
		return $this->variation_attributes;
	}

	/**
	 * If set, get the default attributes for a variable product.
	 *
	 * @param string $attribute_name Attribute name.
	 * @return string
	 */
	public function get_variation_default_attribute( $attribute_name ) {
		$defaults       = $this->get_default_attributes();
		$attribute_name = sanitize_title( $attribute_name );

		return isset( $defaults[ $attribute_name ] ) ? $defaults[ $attribute_name ] : '';
	}

	/**
	 * Variable products themselves cannot be downloadable.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return bool
	 */
	public function get_downloadable( $context = 'view' ) {
		return false;
	}

	/**
	 * Variable products themselves cannot be virtual.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return bool
	 */
	public function get_virtual( $context = 'view' ) {
		return false;
	}

	/**
	 * Get an array of available variations for the current product.
	 *
	 * @param string $return Optional. The format to return the results in. Can be 'array' to return an array of variation data or 'objects' for the product objects. Default 'array'.
	 *
	 * @return array[]|WC_Product_Variation[]
	 */
	public function get_available_variations( $return = 'array' ) {
		$variation_ids        = $this->get_children();
		$available_variations = array();

		if ( is_callable( '_prime_post_caches' ) ) {
			_prime_post_caches( $variation_ids );
		}

		foreach ( $variation_ids as $variation_id ) {

			$variation = wc_get_product( $variation_id );

			// Hide out of stock variations if 'Hide out of stock items from the catalog' is checked.
			if ( ! $variation || ! $variation->exists() || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $variation->is_in_stock() ) ) {
				continue;
			}

			// Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price).
			if ( apply_filters( 'woocommerce_hide_invisible_variations', true, $this->get_id(), $variation ) && ! $variation->variation_is_visible() ) {
				continue;
			}

			if ( 'array' === $return ) {
				$available_variations[] = $this->get_available_variation( $variation );
			} else {
				$available_variations[] = $variation;
			}
		}

		if ( 'array' === $return ) {
			$available_variations = array_values( array_filter( $available_variations ) );
		}

		return $available_variations;
	}

	/**
	 * Check if a given variation is currently available.
	 *
	 * @param WC_Product_Variation $variation Variation to check.
	 *
	 * @return bool True if the variation is available, false otherwise.
	 */
	private function variation_is_available( WC_Product_Variation $variation ) {
		// Hide out of stock variations if 'Hide out of stock items from the catalog' is checked.
		if ( ! $variation || ! $variation->exists() || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $variation->is_in_stock() ) ) {
			return false;
		}

		// Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price).
		if ( apply_filters( 'woocommerce_hide_invisible_variations', true, $this->get_id(), $variation ) && ! $variation->variation_is_visible() ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns an array of data for a variation. Used in the add to cart form.
	 *
	 * @since  2.4.0
	 * @param  WC_Product $variation Variation product object or ID.
	 * @return array|bool
	 */
	public function get_available_variation( $variation ) {
		if ( is_numeric( $variation ) ) {
			$variation = wc_get_product( $variation );
		}
		if ( ! $variation instanceof WC_Product_Variation ) {
			return false;
		}
		// See if prices should be shown for each variation after selection.
		$show_variation_price = apply_filters( 'woocommerce_show_variation_price', $variation->get_price() === '' || $this->get_variation_sale_price( 'min' ) !== $this->get_variation_sale_price( 'max' ) || $this->get_variation_regular_price( 'min' ) !== $this->get_variation_regular_price( 'max' ), $this, $variation );

		return apply_filters(
			'woocommerce_available_variation',
			array(
				'attributes'            => $variation->get_variation_attributes(),
				'availability_html'     => wc_get_stock_html( $variation ),
				'backorders_allowed'    => $variation->backorders_allowed(),
				'dimensions'            => $variation->get_dimensions( false ),
				'dimensions_html'       => wc_format_dimensions( $variation->get_dimensions( false ) ),
				'display_price'         => wc_get_price_to_display( $variation ),
				'display_regular_price' => wc_get_price_to_display( $variation, array( 'price' => $variation->get_regular_price() ) ),
				'image'                 => wc_get_product_attachment_props( $variation->get_image_id() ),
				'image_id'              => $variation->get_image_id(),
				'is_downloadable'       => $variation->is_downloadable(),
				'is_in_stock'           => $variation->is_in_stock(),
				'is_purchasable'        => $variation->is_purchasable(),
				'is_sold_individually'  => $variation->is_sold_individually() ? 'yes' : 'no',
				'is_virtual'            => $variation->is_virtual(),
				'max_qty'               => 0 < $variation->get_max_purchase_quantity() ? $variation->get_max_purchase_quantity() : '',
				'min_qty'               => $variation->get_min_purchase_quantity(),
				'price_html'            => $show_variation_price ? '<span class="price">' . $variation->get_price_html() . '</span>' : '',
				'sku'                   => $variation->get_sku(),
				'variation_description' => wc_format_content( $variation->get_description() ),
				'variation_id'          => $variation->get_id(),
				'variation_is_active'   => $variation->variation_is_active(),
				'variation_is_visible'  => $variation->variation_is_visible(),
				'weight'                => $variation->get_weight(),
				'weight_html'           => wc_format_weight( $variation->get_weight() ),
			),
			$this,
			$variation
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Sets an array of variation attributes.
	 *
	 * @since 3.0.0
	 * @param array $variation_attributes Attributes list.
	 */
	public function set_variation_attributes( $variation_attributes ) {
		$this->variation_attributes = $variation_attributes;
	}

	/**
	 * Sets an array of children for the product.
	 *
	 * @since 3.0.0
	 * @param array $children Children products.
	 */
	public function set_children( $children ) {
		$this->children = array_filter( wp_parse_id_list( (array) $children ) );
	}

	/**
	 * Sets an array of visible children only.
	 *
	 * @since 3.0.0
	 * @param array $visible_children List of visible children products.
	 */
	public function set_visible_children( $visible_children ) {
		$this->visible_children = array_filter( wp_parse_id_list( (array) $visible_children ) );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Ensure properties are set correctly before save.
	 *
	 * @since 3.0.0
	 */
	public function validate_props() {
		parent::validate_props();

		if ( ! $this->get_manage_stock() ) {
			$this->data_store->sync_stock_status( $this );
		}
	}

	/**
	 * Do any extra processing needed before the actual product save
	 * (but after triggering the 'woocommerce_before_..._object_save' action)
	 *
	 * @return mixed A state value that will be passed to after_data_store_save_or_update.
	 */
	protected function before_data_store_save_or_update() {
		// Get names before save.
		$previous_name = $this->data['name'];
		$new_name      = $this->get_name( 'edit' );

		return array(
			'previous_name' => $previous_name,
			'new_name'      => $new_name,
		);
	}

	/**
	 * Do any extra processing needed after the actual product save
	 * (but before triggering the 'woocommerce_after_..._object_save' action)
	 *
	 * @param mixed $state The state object that was returned by before_data_store_save_or_update.
	 */
	protected function after_data_store_save_or_update( $state ) {
		$this->data_store->sync_variation_names( $this, $state['previous_name'], $state['new_name'] );
		$this->data_store->sync_managed_variation_stock_status( $this );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	*/

	/**
	 * Returns whether or not the product is on sale.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit. What the value is for. Valid values are view and edit.
	 * @return bool
	 */
	public function is_on_sale( $context = 'view' ) {
		$prices  = $this->get_variation_prices();
		$on_sale = $prices['regular_price'] !== $prices['sale_price'] && $prices['sale_price'] === $prices['price'];

		return 'view' === $context ? apply_filters( 'woocommerce_product_is_on_sale', $on_sale, $this ) : $on_sale;
	}

	/**
	 * Is a child in stock?
	 *
	 * @return boolean
	 */
	public function child_is_in_stock() {
		return $this->data_store->child_is_in_stock( $this );
	}

	/**
	 * Is a child on backorder?
	 *
	 * @since 3.3.0
	 * @return boolean
	 */
	public function child_is_on_backorder() {
		return $this->data_store->child_has_stock_status( $this, 'onbackorder' );
	}

	/**
	 * Does a child have a weight set?
	 *
	 * @return boolean
	 */
	public function child_has_weight() {
		$transient_name = 'wc_child_has_weight_' . $this->get_id();
		$has_weight     = get_transient( $transient_name );

		if ( false === $has_weight ) {
			$has_weight = $this->data_store->child_has_weight( $this );
			set_transient( $transient_name, (int) $has_weight, DAY_IN_SECONDS * 30 );
		}

		return (bool) $has_weight;
	}

	/**
	 * Does a child have dimensions set?
	 *
	 * @return boolean
	 */
	public function child_has_dimensions() {
		$transient_name = 'wc_child_has_dimensions_' . $this->get_id();
		$has_dimension  = get_transient( $transient_name );

		if ( false === $has_dimension ) {
			$has_dimension = $this->data_store->child_has_dimensions( $this );
			set_transient( $transient_name, (int) $has_dimension, DAY_IN_SECONDS * 30 );
		}

		return (bool) $has_dimension;
	}

	/**
	 * Returns whether or not the product has dimensions set.
	 *
	 * @return bool
	 */
	public function has_dimensions() {
		return parent::has_dimensions() || $this->child_has_dimensions();
	}

	/**
	 * Returns whether or not the product has weight set.
	 *
	 * @return bool
	 */
	public function has_weight() {
		return parent::has_weight() || $this->child_has_weight();
	}

	/**
	 * Returns whether or not the product has additional options that need
	 * selecting before adding to cart.
	 *
	 * @since  3.0.0
	 * @return boolean
	 */
	public function has_options() {
		return apply_filters( 'woocommerce_product_has_options', true, $this );
	}


	/*
	|--------------------------------------------------------------------------
	| Sync with child variations.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Sync a variable product with it's children. These sync functions sync
	 * upwards (from child to parent) when the variation is saved.
	 *
	 * @param WC_Product|int $product Product object or ID for which you wish to sync.
	 * @param bool           $save If true, the product object will be saved to the DB before returning it.
	 * @return WC_Product Synced product object.
	 */
	public static function sync( $product, $save = true ) {
		if ( ! is_a( $product, 'WC_Product' ) ) {
			$product = wc_get_product( $product );
		}
		if ( is_a( $product, 'WC_Product_Variable' ) ) {
			$data_store = WC_Data_Store::load( 'product-' . $product->get_type() );
			$data_store->sync_price( $product );
			$data_store->sync_stock_status( $product );
			self::sync_attributes( $product ); // Legacy update of attributes.

			do_action( 'woocommerce_variable_product_sync_data', $product );

			if ( $save ) {
				$product->save();
			}

			wc_do_deprecated_action(
				'woocommerce_variable_product_sync',
				array(
					$product->get_id(),
					$product->get_visible_children(),
				),
				'3.0',
				'woocommerce_variable_product_sync_data, woocommerce_new_product or woocommerce_update_product'
			);
		}

		return $product;
	}

	/**
	 * Sync parent stock status with the status of all children and save.
	 *
	 * @param WC_Product|int $product Product object or ID for which you wish to sync.
	 * @param bool           $save If true, the product object will be saved to the DB before returning it.
	 * @return WC_Product Synced product object.
	 */
	public static function sync_stock_status( $product, $save = true ) {
		if ( ! is_a( $product, 'WC_Product' ) ) {
			$product = wc_get_product( $product );
		}
		if ( is_a( $product, 'WC_Product_Variable' ) ) {
			$data_store = WC_Data_Store::load( 'product-' . $product->get_type() );
			$data_store->sync_stock_status( $product );

			if ( $save ) {
				$product->save();
			}
		}

		return $product;
	}

	/**
	 * Sort an associative array of $variation_id => $price pairs in order of min and max prices.
	 *
	 * @param array $prices associative array of $variation_id => $price pairs.
	 * @return array
	 */
	protected function sort_variation_prices( $prices ) {
		asort( $prices );

		return $prices;
	}
}

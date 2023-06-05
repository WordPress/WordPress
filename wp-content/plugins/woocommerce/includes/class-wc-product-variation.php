<?php
/**
 * Product Variation
 *
 * The WooCommerce product variation class handles product variation data.
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product variation class.
 */
class WC_Product_Variation extends WC_Product_Simple {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'product_variation';

	/**
	 * Parent data.
	 *
	 * @var array
	 */
	protected $parent_data = array(
		'title'             => '',
		'sku'               => '',
		'manage_stock'      => '',
		'backorders'        => '',
		'stock_quantity'    => '',
		'weight'            => '',
		'length'            => '',
		'width'             => '',
		'height'            => '',
		'tax_class'         => '',
		'shipping_class_id' => '',
		'image_id'          => '',
		'purchase_note'     => '',
	);

	/**
	 * Override the default constructor to set custom defaults.
	 *
	 * @param int|WC_Product|object $product Product to init.
	 */
	public function __construct( $product = 0 ) {
		$this->data['tax_class']         = 'parent';
		$this->data['attribute_summary'] = '';
		parent::__construct( $product );
	}

	/**
	 * Prefix for action and filter hooks on data.
	 *
	 * @since  3.0.0
	 * @return string
	 */
	protected function get_hook_prefix() {
		return 'woocommerce_product_variation_get_';
	}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'variation';
	}

	/**
	 * If the stock level comes from another product ID.
	 *
	 * @since  3.0.0
	 * @return int
	 */
	public function get_stock_managed_by_id() {
		return 'parent' === $this->get_manage_stock() ? $this->get_parent_id() : $this->get_id();
	}

	/**
	 * Get the product's title. For variations this is the parent product name.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'woocommerce_product_title', $this->parent_data['title'], $this );
	}

	/**
	 * Get product name with SKU or ID. Used within admin.
	 *
	 * @return string Formatted product name
	 */
	public function get_formatted_name() {
		if ( $this->get_sku() ) {
			$identifier = $this->get_sku();
		} else {
			$identifier = '#' . $this->get_id();
		}

		$formatted_variation_list = wc_get_formatted_variation( $this, true, true, true );

		return sprintf( '%2$s (%1$s)', $identifier, $this->get_name() ) . '<span class="description">' . $formatted_variation_list . '</span>';
	}

	/**
	 * Get variation attribute values. Keys are prefixed with attribute_, as stored, unless $with_prefix is false.
	 *
	 * @param bool $with_prefix Whether keys should be prepended with attribute_ or not, default is true.
	 * @return array of attributes and their values for this variation.
	 */
	public function get_variation_attributes( $with_prefix = true ) {
		$attributes           = $this->get_attributes();
		$variation_attributes = array();
		$prefix               = $with_prefix ? 'attribute_' : '';

		foreach ( $attributes as $key => $value ) {
			$variation_attributes[ $prefix . $key ] = $value;
		}
		return $variation_attributes;
	}

	/**
	 * Returns a single product attribute as a string.
	 *
	 * @param  string $attribute to get.
	 * @return string
	 */
	public function get_attribute( $attribute ) {
		$attributes = $this->get_attributes();
		$attribute  = sanitize_title( $attribute );

		if ( isset( $attributes[ $attribute ] ) ) {
			$value = $attributes[ $attribute ];
			$term  = taxonomy_exists( $attribute ) ? get_term_by( 'slug', $value, $attribute ) : false;
			return ! is_wp_error( $term ) && $term ? $term->name : $value;
		}

		$att_str = 'pa_' . $attribute;
		if ( isset( $attributes[ $att_str ] ) ) {
			$value = $attributes[ $att_str ];
			$term  = taxonomy_exists( $att_str ) ? get_term_by( 'slug', $value, $att_str ) : false;
			return ! is_wp_error( $term ) && $term ? $term->name : $value;
		}

		return '';
	}

	/**
	 * Wrapper for get_permalink. Adds this variations attributes to the URL.
	 *
	 * @param  array|null $item_object item array If a cart or order item is passed, we can get a link containing the exact attributes selected for the variation, rather than the default attributes.
	 * @return string
	 */
	public function get_permalink( $item_object = null ) {
		$url = get_permalink( $this->get_parent_id() );

		if ( ! empty( $item_object['variation'] ) ) {
			$data = $item_object['variation'];
		} elseif ( ! empty( $item_object['item_meta_array'] ) ) {
			$data_keys   = array_map( 'wc_variation_attribute_name', wp_list_pluck( $item_object['item_meta_array'], 'key' ) );
			$data_values = wp_list_pluck( $item_object['item_meta_array'], 'value' );
			$data        = array_intersect_key( array_combine( $data_keys, $data_values ), $this->get_variation_attributes() );
		} else {
			$data = $this->get_variation_attributes();
		}

		$data = array_filter( $data, 'wc_array_filter_default_attributes' );

		if ( empty( $data ) ) {
			return $url;
		}

		// Filter and encode keys and values so this is not broken by add_query_arg.
		$data = array_map( 'urlencode', $data );
		$keys = array_map( 'urlencode', array_keys( $data ) );

		return add_query_arg( array_combine( $keys, $data ), $url );
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @return string
	 */
	public function add_to_cart_url() {
		$url = $this->is_purchasable() ? remove_query_arg(
			'added-to-cart',
			add_query_arg(
				array(
					'variation_id' => $this->get_id(),
					'add-to-cart'  => $this->get_parent_id(),
				),
				$this->get_permalink()
			)
		) : $this->get_permalink();
		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}

	/**
	 * Get SKU (Stock-keeping unit) - product unique ID.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_sku( $context = 'view' ) {
		$value = $this->get_prop( 'sku', $context );

		// Inherit value from parent.
		if ( 'view' === $context && empty( $value ) ) {
			$value = apply_filters( $this->get_hook_prefix() . 'sku', $this->parent_data['sku'], $this );
		}
		return $value;
	}

	/**
	 * Returns the product's weight.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_weight( $context = 'view' ) {
		$value = $this->get_prop( 'weight', $context );

		// Inherit value from parent.
		if ( 'view' === $context && empty( $value ) ) {
			$value = apply_filters( $this->get_hook_prefix() . 'weight', $this->parent_data['weight'], $this );
		}
		return $value;
	}

	/**
	 * Returns the product length.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_length( $context = 'view' ) {
		$value = $this->get_prop( 'length', $context );

		// Inherit value from parent.
		if ( 'view' === $context && empty( $value ) ) {
			$value = apply_filters( $this->get_hook_prefix() . 'length', $this->parent_data['length'], $this );
		}
		return $value;
	}

	/**
	 * Returns the product width.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_width( $context = 'view' ) {
		$value = $this->get_prop( 'width', $context );

		// Inherit value from parent.
		if ( 'view' === $context && empty( $value ) ) {
			$value = apply_filters( $this->get_hook_prefix() . 'width', $this->parent_data['width'], $this );
		}
		return $value;
	}

	/**
	 * Returns the product height.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_height( $context = 'view' ) {
		$value = $this->get_prop( 'height', $context );

		// Inherit value from parent.
		if ( 'view' === $context && empty( $value ) ) {
			$value = apply_filters( $this->get_hook_prefix() . 'height', $this->parent_data['height'], $this );
		}
		return $value;
	}

	/**
	 * Returns the tax class.
	 *
	 * Does not use get_prop so it can handle 'parent' inheritance correctly.
	 *
	 * @param  string $context view, edit, or unfiltered.
	 * @return string
	 */
	public function get_tax_class( $context = 'view' ) {
		$value = null;

		if ( array_key_exists( 'tax_class', $this->data ) ) {
			$value = array_key_exists( 'tax_class', $this->changes ) ? $this->changes['tax_class'] : $this->data['tax_class'];

			if ( 'edit' !== $context && 'parent' === $value ) {
				$value = $this->parent_data['tax_class'];
			}

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . 'tax_class', $value, $this );
			}
		}
		return $value;
	}

	/**
	 * Return if product manage stock.
	 *
	 * @since 3.0.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return boolean|string true, false, or parent.
	 */
	public function get_manage_stock( $context = 'view' ) {
		$value = $this->get_prop( 'manage_stock', $context );

		// Inherit value from parent.
		if ( 'view' === $context && false === $value && true === wc_string_to_bool( $this->parent_data['manage_stock'] ) ) {
			$value = 'parent';
		}
		return $value;
	}

	/**
	 * Returns number of items available for sale.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return int|null
	 */
	public function get_stock_quantity( $context = 'view' ) {
		$value = $this->get_prop( 'stock_quantity', $context );

		// Inherit value from parent.
		if ( 'view' === $context && 'parent' === $this->get_manage_stock() ) {
			$value = apply_filters( $this->get_hook_prefix() . 'stock_quantity', $this->parent_data['stock_quantity'], $this );
		}
		return $value;
	}

	/**
	 * Get backorders.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @since 3.0.0
	 * @return string yes no or notify
	 */
	public function get_backorders( $context = 'view' ) {
		$value = $this->get_prop( 'backorders', $context );

		// Inherit value from parent.
		if ( 'view' === $context && 'parent' === $this->get_manage_stock() ) {
			$value = apply_filters( $this->get_hook_prefix() . 'backorders', $this->parent_data['backorders'], $this );
		}
		return $value;
	}

	/**
	 * Get main image ID.
	 *
	 * @since 3.0.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_image_id( $context = 'view' ) {
		$image_id = $this->get_prop( 'image_id', $context );

		if ( 'view' === $context && ! $image_id ) {
			$image_id = apply_filters( $this->get_hook_prefix() . 'image_id', $this->parent_data['image_id'], $this );
		}

		return $image_id;
	}

	/**
	 * Get purchase note.
	 *
	 * @since 3.0.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_purchase_note( $context = 'view' ) {
		$value = $this->get_prop( 'purchase_note', $context );

		// Inherit value from parent.
		if ( 'view' === $context && empty( $value ) ) {
			$value = apply_filters( $this->get_hook_prefix() . 'purchase_note', $this->parent_data['purchase_note'], $this );
		}
		return $value;
	}

	/**
	 * Get shipping class ID.
	 *
	 * @since 3.0.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return int
	 */
	public function get_shipping_class_id( $context = 'view' ) {
		$shipping_class_id = $this->get_prop( 'shipping_class_id', $context );

		if ( 'view' === $context && ! $shipping_class_id ) {
			$shipping_class_id = apply_filters( $this->get_hook_prefix() . 'shipping_class_id', $this->parent_data['shipping_class_id'], $this );
		}

		return $shipping_class_id;
	}

	/**
	 * Get catalog visibility.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_catalog_visibility( $context = 'view' ) {
		return apply_filters( $this->get_hook_prefix() . 'catalog_visibility', $this->parent_data['catalog_visibility'], $this );
	}

	/**
	 * Get attribute summary.
	 *
	 * By default, attribute summary contains comma-delimited 'attribute_name: attribute_value' pairs for all attributes.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 3.6.0
	 * @return string
	 */
	public function get_attribute_summary( $context = 'view' ) {
		return $this->get_prop( 'attribute_summary', $context );
	}


	/**
	 * Set attribute summary.
	 *
	 * By default, attribute summary contains comma-delimited 'attribute_name: attribute_value' pairs for all attributes.
	 *
	 * @since 3.6.0
	 * @param string $attribute_summary Summary of attribute names and values assigned to the variation.
	 */
	public function set_attribute_summary( $attribute_summary ) {
		$this->set_prop( 'attribute_summary', $attribute_summary );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set the parent data array for this variation.
	 *
	 * @since 3.0.0
	 * @param array $parent_data parent data array for this variation.
	 */
	public function set_parent_data( $parent_data ) {
		$parent_data = wp_parse_args(
			$parent_data,
			array(
				'title'              => '',
				'status'             => '',
				'sku'                => '',
				'manage_stock'       => 'no',
				'backorders'         => 'no',
				'stock_quantity'     => '',
				'weight'             => '',
				'length'             => '',
				'width'              => '',
				'height'             => '',
				'tax_class'          => '',
				'shipping_class_id'  => 0,
				'image_id'           => 0,
				'purchase_note'      => '',
				'catalog_visibility' => 'visible',
			)
		);

		// Normalize tax class.
		$parent_data['tax_class'] = sanitize_title( $parent_data['tax_class'] );
		$parent_data['tax_class'] = 'standard' === $parent_data['tax_class'] ? '' : $parent_data['tax_class'];
		$valid_classes            = $this->get_valid_tax_classes();

		if ( ! in_array( $parent_data['tax_class'], $valid_classes, true ) ) {
			$parent_data['tax_class'] = '';
		}

		$this->parent_data = $parent_data;
	}

	/**
	 * Get the parent data array for this variation.
	 *
	 * @since  3.0.0
	 * @return array
	 */
	public function get_parent_data() {
		return $this->parent_data;
	}

	/**
	 * Set attributes. Unlike the parent product which uses terms, variations are assigned
	 * specific attributes using name value pairs.
	 *
	 * @param array $raw_attributes array of raw attributes.
	 */
	public function set_attributes( $raw_attributes ) {
		$raw_attributes = (array) $raw_attributes;
		$attributes     = array();

		foreach ( $raw_attributes as $key => $value ) {
			// Remove attribute prefix which meta gets stored with.
			if ( 0 === strpos( $key, 'attribute_' ) ) {
				$key = substr( $key, 10 );
			}
			$attributes[ $key ] = $value;
		}
		$this->set_prop( 'attributes', $attributes );
	}

	/**
	 * Returns whether or not the product has any visible attributes.
	 *
	 * Variations are mapped to specific attributes unlike products, and the return
	 * value of ->get_attributes differs. Therefore this returns false.
	 *
	 * @return boolean
	 */
	public function has_attributes() {
		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	*/

	/**
	 * Returns false if the product cannot be bought.
	 * Override abstract method so that: i) Disabled variations are not be purchasable by admins. ii) Enabled variations are not purchasable if the parent product is not purchasable.
	 *
	 * @return bool
	 */
	public function is_purchasable() {
		return apply_filters( 'woocommerce_variation_is_purchasable', $this->variation_is_visible() && parent::is_purchasable() && ( 'publish' === $this->parent_data['status'] || current_user_can( 'edit_post', $this->get_parent_id() ) ), $this );
	}

	/**
	 * Controls whether this particular variation will appear greyed-out (inactive) or not (active).
	 * Used by extensions to make incompatible variations appear greyed-out, etc.
	 * Other possible uses: prevent out-of-stock variations from being selected.
	 *
	 * @return bool
	 */
	public function variation_is_active() {
		return apply_filters( 'woocommerce_variation_is_active', true, $this );
	}

	/**
	 * Checks if this particular variation is visible. Invisible variations are enabled and can be selected, but no price / stock info is displayed.
	 * Instead, a suitable 'unavailable' message is displayed.
	 * Invisible by default: Disabled variations and variations with an empty price.
	 *
	 * @return bool
	 */
	public function variation_is_visible() {
		return apply_filters( 'woocommerce_variation_is_visible', 'publish' === get_post_status( $this->get_id() ) && '' !== $this->get_price(), $this->get_id(), $this->get_parent_id(), $this );
	}

	/**
	 * Return valid tax classes. Adds 'parent' to the default list of valid tax classes.
	 *
	 * @return array valid tax classes
	 */
	protected function get_valid_tax_classes() {
		$valid_classes   = WC_Tax::get_tax_class_slugs();
		$valid_classes[] = 'parent';

		return $valid_classes;
	}
}

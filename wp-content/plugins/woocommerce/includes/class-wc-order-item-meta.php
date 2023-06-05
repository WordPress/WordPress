<?php
/**
 * Order Item Meta
 *
 * A Simple class for managing order item meta so plugins add it in the correct format.
 *
 * @package     WooCommerce\Classes
 * @deprecated  3.0.0 wc_display_item_meta function is used instead.
 * @version     2.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Order item meta class.
 */
class WC_Order_Item_Meta {

	/**
	 * For handling backwards compatibility.
	 *
	 * @var bool
	 */
	private $legacy = false;

	/**
	 * Order item
	 *
	 * @var array|null
	 */
	private $item = null;

	/**
	 * Post meta data
	 *
	 * @var array|null
	 */
	public $meta = null;

	/**
	 * Product object.
	 *
	 * @var WC_Product|null
	 */
	public $product = null;

	/**
	 * Constructor.
	 *
	 * @param array       $item defaults to array().
	 * @param \WC_Product $product defaults to null.
	 */
	public function __construct( $item = array(), $product = null ) {
		wc_deprecated_function( 'WC_Order_Item_Meta::__construct', '3.1', 'WC_Order_Item_Product' );

		// Backwards (pre 2.4) compatibility.
		if ( ! isset( $item['item_meta'] ) ) {
			$this->legacy = true;
			$this->meta   = array_filter( (array) $item );
			return;
		}
		$this->item    = $item;
		$this->meta    = array_filter( (array) $item['item_meta'] );
		$this->product = $product;
	}

	/**
	 * Display meta in a formatted list.
	 *
	 * @param bool   $flat       Flat (default: false).
	 * @param bool   $return     Return (default: false).
	 * @param string $hideprefix Hide prefix (default: _).
	 * @param  string $delimiter Delimiter used to separate items when $flat is true.
	 * @return string|void
	 */
	public function display( $flat = false, $return = false, $hideprefix = '_', $delimiter = ", \n" ) {
		$output         = '';
		$formatted_meta = $this->get_formatted( $hideprefix );

		if ( ! empty( $formatted_meta ) ) {
			$meta_list = array();

			foreach ( $formatted_meta as $meta ) {
				if ( $flat ) {
					$meta_list[] = wp_kses_post( $meta['label'] . ': ' . $meta['value'] );
				} else {
					$meta_list[] = '
						<dt class="variation-' . sanitize_html_class( sanitize_text_field( $meta['key'] ) ) . '">' . wp_kses_post( $meta['label'] ) . ':</dt>
						<dd class="variation-' . sanitize_html_class( sanitize_text_field( $meta['key'] ) ) . '">' . wp_kses_post( wpautop( make_clickable( $meta['value'] ) ) ) . '</dd>
					';
				}
			}

			if ( ! empty( $meta_list ) ) {
				if ( $flat ) {
					$output .= implode( $delimiter, $meta_list );
				} else {
					$output .= '<dl class="variation">' . implode( '', $meta_list ) . '</dl>';
				}
			}
		}

		$output = apply_filters( 'woocommerce_order_items_meta_display', $output, $this, $flat );

		if ( $return ) {
			return $output;
		} else {
			echo $output; // WPCS: XSS ok.
		}
	}

	/**
	 * Return an array of formatted item meta in format e.g.
	 *
	 * Returns: array(
	 *   'pa_size' => array(
	 *     'label' => 'Size',
	 *     'value' => 'Medium',
	 *   )
	 * )
	 *
	 * @since 2.4
	 * @param string $hideprefix exclude meta when key is prefixed with this, defaults to '_'.
	 * @return array
	 */
	public function get_formatted( $hideprefix = '_' ) {
		if ( $this->legacy ) {
			return $this->get_formatted_legacy( $hideprefix );
		}

		$formatted_meta = array();

		if ( ! empty( $this->item['item_meta_array'] ) ) {
			foreach ( $this->item['item_meta_array'] as $meta_id => $meta ) {
				if ( '' === $meta->value || is_serialized( $meta->value ) || ( ! empty( $hideprefix ) && substr( $meta->key, 0, 1 ) === $hideprefix ) ) {
					continue;
				}

				$attribute_key = urldecode( str_replace( 'attribute_', '', $meta->key ) );
				$meta_value    = $meta->value;

				// If this is a term slug, get the term's nice name.
				if ( taxonomy_exists( $attribute_key ) ) {
					$term = get_term_by( 'slug', $meta_value, $attribute_key );

					if ( ! is_wp_error( $term ) && is_object( $term ) && $term->name ) {
						$meta_value = $term->name;
					}
				}

				$formatted_meta[ $meta_id ] = array(
					'key'   => $meta->key,
					'label' => wc_attribute_label( $attribute_key, $this->product ),
					'value' => apply_filters( 'woocommerce_order_item_display_meta_value', $meta_value, $meta, $this->item ),
				);
			}
		}

		return apply_filters( 'woocommerce_order_items_meta_get_formatted', $formatted_meta, $this );
	}

	/**
	 * Return an array of formatted item meta in format e.g.
	 * Handles @deprecated args.
	 *
	 * @param string $hideprefix Hide prefix.
	 *
	 * @return array
	 */
	public function get_formatted_legacy( $hideprefix = '_' ) {
		if ( ! wp_doing_ajax() ) {
			wc_deprecated_argument( 'WC_Order_Item_Meta::get_formatted', '2.4', 'Item Meta Data is being called with legacy arguments' );
		}

		$formatted_meta = array();

		foreach ( $this->meta as $meta_key => $meta_values ) {
			if ( empty( $meta_values ) || ( ! empty( $hideprefix ) && substr( $meta_key, 0, 1 ) === $hideprefix ) ) {
				continue;
			}
			foreach ( (array) $meta_values as $meta_value ) {
				// Skip serialised meta.
				if ( is_serialized( $meta_value ) ) {
					continue;
				}

				$attribute_key = urldecode( str_replace( 'attribute_', '', $meta_key ) );

				// If this is a term slug, get the term's nice name.
				if ( taxonomy_exists( $attribute_key ) ) {
					$term = get_term_by( 'slug', $meta_value, $attribute_key );
					if ( ! is_wp_error( $term ) && is_object( $term ) && $term->name ) {
						$meta_value = $term->name;
					}
				}

				// Unique key required.
				$formatted_meta_key = $meta_key;
				$loop               = 0;
				while ( isset( $formatted_meta[ $formatted_meta_key ] ) ) {
					$loop ++;
					$formatted_meta_key = $meta_key . '-' . $loop;
				}

				$formatted_meta[ $formatted_meta_key ] = array(
					'key'   => $meta_key,
					'label' => wc_attribute_label( $attribute_key, $this->product ),
					'value' => apply_filters( 'woocommerce_order_item_display_meta_value', $meta_value, $this->meta, $this->item ),
				);
			}
		}

		return $formatted_meta;
	}
}

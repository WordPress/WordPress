<?php
/**
 * Product Data
 *
 * Displays the product data box, tabbed, with several panels covering price, stock etc.
 *
 * @package  WooCommerce\Admin\Meta Boxes
 * @version  3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Meta_Box_Product_Data Class.
 */
class WC_Meta_Box_Product_Data {

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function output( $post ) {
		global $thepostid, $product_object;

		$thepostid      = $post->ID;
		$product_object = $thepostid ? wc_get_product( $thepostid ) : new WC_Product();

		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );

		include __DIR__ . '/views/html-product-data-panel.php';
	}

	/**
	 * Show tab content/settings.
	 */
	private static function output_tabs() {
		global $post, $thepostid, $product_object;

		include __DIR__ . '/views/html-product-data-general.php';
		include __DIR__ . '/views/html-product-data-inventory.php';
		include __DIR__ . '/views/html-product-data-shipping.php';
		include __DIR__ . '/views/html-product-data-linked-products.php';
		include __DIR__ . '/views/html-product-data-attributes.php';
		include __DIR__ . '/views/html-product-data-advanced.php';
	}

	/**
	 * Return array of product type options.
	 *
	 * @return array
	 */
	private static function get_product_type_options() {
		/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
		return apply_filters(
			'product_type_options',
			wc_get_default_product_type_options(),
		);
		/* phpcs: enable */
	}

	/**
	 * Return array of tabs to show.
	 *
	 * @return array
	 */
	private static function get_product_data_tabs() {
		/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
		$tabs = apply_filters(
			'woocommerce_product_data_tabs',
			array(
				'general'        => array(
					'label'    => __( 'General', 'woocommerce' ),
					'target'   => 'general_product_data',
					'class'    => array( 'hide_if_grouped' ),
					'priority' => 10,
				),
				'inventory'      => array(
					'label'    => __( 'Inventory', 'woocommerce' ),
					'target'   => 'inventory_product_data',
					'class'    => array( 'show_if_simple', 'show_if_variable', 'show_if_grouped', 'show_if_external' ),
					'priority' => 20,
				),
				'shipping'       => array(
					'label'    => __( 'Shipping', 'woocommerce' ),
					'target'   => 'shipping_product_data',
					'class'    => array( 'hide_if_virtual', 'hide_if_grouped', 'hide_if_external' ),
					'priority' => 30,
				),
				'linked_product' => array(
					'label'    => __( 'Linked Products', 'woocommerce' ),
					'target'   => 'linked_product_data',
					'class'    => array(),
					'priority' => 40,
				),
				'attribute'      => array(
					'label'    => __( 'Attributes', 'woocommerce' ),
					'target'   => 'product_attributes',
					'class'    => array(),
					'priority' => 50,
				),
				'variations'     => array(
					'label'    => __( 'Variations', 'woocommerce' ),
					'target'   => 'variable_product_options',
					'class'    => array( 'show_if_variable' ),
					'priority' => 60,
				),
				'advanced'       => array(
					'label'    => __( 'Advanced', 'woocommerce' ),
					'target'   => 'advanced_product_data',
					'class'    => array(),
					'priority' => 70,
				),
			)
		);
		/* phpcs: enable */

		// Sort tabs based on priority.
		uasort( $tabs, array( __CLASS__, 'product_data_tabs_sort' ) );

		return $tabs;
	}

	/**
	 * Callback to sort product data tabs on priority.
	 *
	 * @since 3.1.0
	 * @param int $a First item.
	 * @param int $b Second item.
	 *
	 * @return bool
	 */
	private static function product_data_tabs_sort( $a, $b ) {
		if ( ! isset( $a['priority'], $b['priority'] ) ) {
			return -1;
		}

		if ( $a['priority'] === $b['priority'] ) {
			return 0;
		}

		return $a['priority'] < $b['priority'] ? -1 : 1;
	}

	/**
	 * Filter callback for finding variation attributes.
	 *
	 * @param  WC_Product_Attribute $attribute Product attribute.
	 * @return bool
	 */
	private static function filter_variation_attributes( $attribute ) {
		return true === $attribute->get_variation();
	}

	/**
	 * Filter callback for finding non-variation attributes.
	 *
	 * @param  WC_Product_Attribute $attribute Product attribute.
	 * @return bool
	 */
	private static function filter_non_variation_attributes( $attribute ) {
		return false === $attribute->get_variation();
	}

	/**
	 * Show options for the variable product type.
	 */
	public static function output_variations() {
		global $post, $wpdb, $product_object;

		/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
		$variation_attributes   = array_filter( $product_object->get_attributes(), array( __CLASS__, 'filter_variation_attributes' ) );
		$default_attributes     = $product_object->get_default_attributes();
		$variations_count       = absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_count', $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation' AND post_status IN ('publish', 'private')", $post->ID ) ), $post->ID ) );
		$variations_per_page    = absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) );
		$variations_total_pages = ceil( $variations_count / $variations_per_page );
		$modal_title            = get_bloginfo( 'name' ) . __( ' says', 'woocommerce' );
		/* phpcs: enable */

		include __DIR__ . '/views/html-product-data-variations.php';
	}

	/**
	 * Prepare downloads for save.
	 *
	 * @param array $file_names File names.
	 * @param array $file_urls File urls.
	 * @param array $file_hashes File hashes.
	 *
	 * @return array
	 */
	private static function prepare_downloads( $file_names, $file_urls, $file_hashes ) {
		$downloads = array();

		if ( ! empty( $file_urls ) ) {
			$file_url_size = count( $file_urls );

			for ( $i = 0; $i < $file_url_size; $i ++ ) {
				if ( ! empty( $file_urls[ $i ] ) ) {
					$downloads[] = array(
						'name'        => wc_clean( $file_names[ $i ] ),
						'file'        => wp_unslash( trim( $file_urls[ $i ] ) ),
						'download_id' => wc_clean( $file_hashes[ $i ] ),
					);
				}
			}
		}
		return $downloads;
	}

	/**
	 * Prepare children for save.
	 *
	 * @return array
	 */
	private static function prepare_children() {
		return isset( $_POST['grouped_products'] ) ? array_filter( array_map( 'intval', (array) $_POST['grouped_products'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Prepare attributes for save.
	 *
	 * @param array $data Attribute data.
	 *
	 * @return array
	 */
	public static function prepare_attributes( $data = false ) {
		$attributes = array();

		if ( ! $data ) {
			$data = stripslashes_deep( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( isset( $data['attribute_names'], $data['attribute_values'] ) ) {
			$attribute_names         = $data['attribute_names'];
			$attribute_values        = $data['attribute_values'];
			$attribute_visibility    = isset( $data['attribute_visibility'] ) ? $data['attribute_visibility'] : array();
			$attribute_variation     = isset( $data['attribute_variation'] ) ? $data['attribute_variation'] : array();
			$attribute_position      = $data['attribute_position'];
			$attribute_names_max_key = max( array_keys( $attribute_names ) );

			for ( $i = 0; $i <= $attribute_names_max_key; $i++ ) {
				if ( empty( $attribute_names[ $i ] ) || ! isset( $attribute_values[ $i ] ) ) {
					continue;
				}
				$attribute_id   = 0;
				$attribute_name = wc_clean( esc_html( $attribute_names[ $i ] ) );

				if ( 'pa_' === substr( $attribute_name, 0, 3 ) ) {
					$attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_name );
				}

				$options = isset( $attribute_values[ $i ] ) ? $attribute_values[ $i ] : '';

				if ( is_array( $options ) ) {
					// Term ids sent as array.
					$options = wp_parse_id_list( $options );
				} else {
					// Terms or text sent in textarea.
					$options = 0 < $attribute_id ? wc_sanitize_textarea( esc_html( wc_sanitize_term_text_based( $options ) ) ) : wc_sanitize_textarea( esc_html( $options ) );
					$options = wc_get_text_attributes( $options );
				}

				if ( empty( $options ) ) {
					continue;
				}

				$attribute = new WC_Product_Attribute();
				$attribute->set_id( $attribute_id );
				$attribute->set_name( $attribute_name );
				$attribute->set_options( $options );
				$attribute->set_position( $attribute_position[ $i ] );
				$attribute->set_visible( isset( $attribute_visibility[ $i ] ) );
				$attribute->set_variation( isset( $attribute_variation[ $i ] ) );
				/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
				$attributes[] = apply_filters( 'woocommerce_admin_meta_boxes_prepare_attribute', $attribute, $data, $i );
				/* phpcs: enable */
			}
		}
		return $attributes;
	}

	/**
	 * Prepare attributes for a specific variation or defaults.
	 *
	 * @param  array  $all_attributes List of attribute keys.
	 * @param  string $key_prefix Attribute key prefix.
	 * @param  int    $index Attribute array index.
	 * @return array
	 */
	private static function prepare_set_attributes( $all_attributes, $key_prefix = 'attribute_', $index = null ) {
		$attributes = array();

		if ( $all_attributes ) {
			foreach ( $all_attributes as $attribute ) {
				if ( $attribute->get_variation() ) {
					$attribute_key = sanitize_title( $attribute->get_name() );

					if ( ! is_null( $index ) ) {
						$value = isset( $_POST[ $key_prefix . $attribute_key ][ $index ] ) ? wp_unslash( $_POST[ $key_prefix . $attribute_key ][ $index ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					} else {
						$value = isset( $_POST[ $key_prefix . $attribute_key ] ) ? wp_unslash( $_POST[ $key_prefix . $attribute_key ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					}

					if ( $attribute->is_taxonomy() ) {
						// Don't use wc_clean as it destroys sanitized characters.
						$value = sanitize_title( $value );
					} else {
						$value = html_entity_decode( wc_clean( $value ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // WPCS: sanitization ok.
					}

					$attributes[ $attribute_key ] = $value;
				}
			}
		}

		return $attributes;
	}

	/**
	 * Save meta box data.
	 *
	 * @param int     $post_id WP post id.
	 * @param WP_Post $post Post object.
	 */
	public static function save( $post_id, $post ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// Process product type first so we have the correct class to run setters.
		$product_type = empty( $_POST['product-type'] ) ? WC_Product_Factory::get_product_type( $post_id ) : sanitize_title( wp_unslash( $_POST['product-type'] ) );
		$classname    = WC_Product_Factory::get_product_classname( $post_id, $product_type ? $product_type : 'simple' );
		$product      = new $classname( $post_id );
		$attributes   = self::prepare_attributes();
		$stock        = null;

		// Handle stock changes.
		if ( isset( $_POST['_stock'] ) ) {
			if ( isset( $_POST['_original_stock'] ) && wc_stock_amount( $product->get_stock_quantity( 'edit' ) ) !== wc_stock_amount( wp_unslash( $_POST['_original_stock'] ) ) ) {
				/* translators: 1: product ID 2: quantity in stock */
				WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The stock has not been updated because the value has changed since editing. Product %1$d has %2$d units in stock.', 'woocommerce' ), $product->get_id(), $product->get_stock_quantity( 'edit' ) ) );
			} else {
				$stock = wc_stock_amount( wp_unslash( $_POST['_stock'] ) );
			}
		}

		// Handle dates.
		$date_on_sale_from = '';
		$date_on_sale_to   = '';

		// Force date from to beginning of day.
		if ( isset( $_POST['_sale_price_dates_from'] ) ) {
			$date_on_sale_from = wc_clean( wp_unslash( $_POST['_sale_price_dates_from'] ) );

			if ( ! empty( $date_on_sale_from ) ) {
				$date_on_sale_from = date( 'Y-m-d 00:00:00', strtotime( $date_on_sale_from ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			}
		}

		// Force date to to the end of the day.
		if ( isset( $_POST['_sale_price_dates_to'] ) ) {
			$date_on_sale_to = wc_clean( wp_unslash( $_POST['_sale_price_dates_to'] ) );

			if ( ! empty( $date_on_sale_to ) ) {
				$date_on_sale_to = date( 'Y-m-d 23:59:59', strtotime( $date_on_sale_to ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
			}
		}

		$errors = $product->set_props(
			array(
				'sku'                => isset( $_POST['_sku'] ) ? wc_clean( wp_unslash( $_POST['_sku'] ) ) : null,
				'purchase_note'      => isset( $_POST['_purchase_note'] ) ? wp_kses_post( wp_unslash( $_POST['_purchase_note'] ) ) : '',
				'downloadable'       => isset( $_POST['_downloadable'] ),
				'virtual'            => isset( $_POST['_virtual'] ),
				'featured'           => isset( $_POST['_featured'] ),
				'catalog_visibility' => isset( $_POST['_visibility'] ) ? wc_clean( wp_unslash( $_POST['_visibility'] ) ) : null,
				'tax_status'         => isset( $_POST['_tax_status'] ) ? wc_clean( wp_unslash( $_POST['_tax_status'] ) ) : null,
				'tax_class'          => isset( $_POST['_tax_class'] ) ? sanitize_title( wp_unslash( $_POST['_tax_class'] ) ) : null,
				'weight'             => isset( $_POST['_weight'] ) ? wc_clean( wp_unslash( $_POST['_weight'] ) ) : null,
				'length'             => isset( $_POST['_length'] ) ? wc_clean( wp_unslash( $_POST['_length'] ) ) : null,
				'width'              => isset( $_POST['_width'] ) ? wc_clean( wp_unslash( $_POST['_width'] ) ) : null,
				'height'             => isset( $_POST['_height'] ) ? wc_clean( wp_unslash( $_POST['_height'] ) ) : null,
				'shipping_class_id'  => isset( $_POST['product_shipping_class'] ) ? absint( wp_unslash( $_POST['product_shipping_class'] ) ) : null,
				'sold_individually'  => ! empty( $_POST['_sold_individually'] ),
				'upsell_ids'         => isset( $_POST['upsell_ids'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['upsell_ids'] ) ) : array(),
				'cross_sell_ids'     => isset( $_POST['crosssell_ids'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['crosssell_ids'] ) ) : array(),
				'regular_price'      => isset( $_POST['_regular_price'] ) ? wc_clean( wp_unslash( $_POST['_regular_price'] ) ) : null,
				'sale_price'         => isset( $_POST['_sale_price'] ) ? wc_clean( wp_unslash( $_POST['_sale_price'] ) ) : null,
				'date_on_sale_from'  => $date_on_sale_from,
				'date_on_sale_to'    => $date_on_sale_to,
				'manage_stock'       => ! empty( $_POST['_manage_stock'] ),
				'backorders'         => isset( $_POST['_backorders'] ) ? wc_clean( wp_unslash( $_POST['_backorders'] ) ) : null,
				'stock_status'       => isset( $_POST['_stock_status'] ) ? wc_clean( wp_unslash( $_POST['_stock_status'] ) ) : null,
				'stock_quantity'     => $stock,
				'low_stock_amount'   => isset( $_POST['_low_stock_amount'] ) && '' !== $_POST['_low_stock_amount'] ? wc_stock_amount( wp_unslash( $_POST['_low_stock_amount'] ) ) : '',
				'download_limit'     => isset( $_POST['_download_limit'] ) && '' !== $_POST['_download_limit'] ? absint( wp_unslash( $_POST['_download_limit'] ) ) : '',
				'download_expiry'    => isset( $_POST['_download_expiry'] ) && '' !== $_POST['_download_expiry'] ? absint( wp_unslash( $_POST['_download_expiry'] ) ) : '',
				// Those are sanitized inside prepare_downloads.
				'downloads'          => self::prepare_downloads(
					isset( $_POST['_wc_file_names'] ) ? wp_unslash( $_POST['_wc_file_names'] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					isset( $_POST['_wc_file_urls'] ) ? wp_unslash( $_POST['_wc_file_urls'] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					isset( $_POST['_wc_file_hashes'] ) ? wp_unslash( $_POST['_wc_file_hashes'] ) : array() // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				),
				'product_url'        => isset( $_POST['_product_url'] ) ? esc_url_raw( wp_unslash( $_POST['_product_url'] ) ) : '',
				'button_text'        => isset( $_POST['_button_text'] ) ? wc_clean( wp_unslash( $_POST['_button_text'] ) ) : '',
				'children'           => 'grouped' === $product_type ? self::prepare_children() : null,
				'reviews_allowed'    => ! empty( $_POST['comment_status'] ) && 'open' === $_POST['comment_status'],
				'attributes'         => $attributes,
				'default_attributes' => self::prepare_set_attributes( $attributes, 'default_attribute_' ),
			)
		);

		if ( is_wp_error( $errors ) ) {
			WC_Admin_Meta_Boxes::add_error( $errors->get_error_message() );
		}

		/**
		 * Set props before save.
		 *
		 * @since 3.0.0
		 */
		do_action( 'woocommerce_admin_process_product_object', $product );

		$product->save();

		if ( $product->is_type( 'variable' ) ) {
			$original_post_title = isset( $_POST['original_post_title'] ) ? wc_clean( wp_unslash( $_POST['original_post_title'] ) ) : '';
			$post_title          = isset( $_POST['post_title'] ) ? wc_clean( wp_unslash( $_POST['post_title'] ) ) : '';

			$product->get_data_store()->sync_variation_names( $product, $original_post_title, $post_title );
		}
		/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
		do_action( 'woocommerce_process_product_meta_' . $product_type, $post_id );
		/* phpcs:enable WordPress.Security.NonceVerification.Missing and WooCommerce.Commenting.CommentHooks.MissingHookComment */
	}

	/**
	 * Save variation meta box data.
	 *
	 * @param int     $post_id WP post id.
	 * @param WP_Post $post Post object.
	 */
	public static function save_variations( $post_id, $post ) {
		global $wpdb;

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['variable_post_id'] ) ) {
			$parent = wc_get_product( $post_id );
			$parent->set_default_attributes( self::prepare_set_attributes( $parent->get_attributes(), 'default_attribute_' ) );
			$parent->save();

			$max_loop   = max( array_keys( wp_unslash( $_POST['variable_post_id'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$data_store = $parent->get_data_store();
			$data_store->sort_all_product_variations( $parent->get_id() );
			$new_variation_menu_order_id    = ! empty( $_POST['new_variation_menu_order_id'] ) ? wc_clean( wp_unslash( $_POST['new_variation_menu_order_id'] ) ) : false;
			$new_variation_menu_order_value = ! empty( $_POST['new_variation_menu_order_value'] ) ? wc_clean( wp_unslash( $_POST['new_variation_menu_order_value'] ) ) : false;

			// Only perform this operation if setting menu order via the prompt.
			if ( $new_variation_menu_order_id && $new_variation_menu_order_value ) {
				/*
				 * We need to gather all the variations with menu order that is
				 * equal or greater than the menu order that is newly set and
				 * increment them by one so that we can correctly insert the updated
				 * variation menu order.
				 */
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE {$wpdb->posts} SET menu_order = menu_order + 1 WHERE post_type = 'product_variation' AND post_parent = %d AND post_status = 'publish' AND menu_order >= %d AND ID != %d",
						$post_id,
						$new_variation_menu_order_value,
						$new_variation_menu_order_id
					)
				);
			}

			for ( $i = 0; $i <= $max_loop; $i++ ) {

				if ( ! isset( $_POST['variable_post_id'][ $i ] ) ) {
					continue;
				}
				$variation_id = absint( $_POST['variable_post_id'][ $i ] );
				$variation    = wc_get_product_object( 'variation', $variation_id );
				$stock        = null;

				// Handle stock changes.
				if ( isset( $_POST['variable_stock'], $_POST['variable_stock'][ $i ] ) ) {
					if ( isset( $_POST['variable_original_stock'], $_POST['variable_original_stock'][ $i ] ) && wc_stock_amount( $variation->get_stock_quantity( 'edit' ) ) !== wc_stock_amount( wp_unslash( $_POST['variable_original_stock'][ $i ] ) ) ) {
						/* translators: 1: product ID 2: quantity in stock */
						WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The stock has not been updated because the value has changed since editing. Product %1$d has %2$d units in stock.', 'woocommerce' ), $variation->get_id(), $variation->get_stock_quantity( 'edit' ) ) );
					} else {
						$stock = wc_stock_amount( wp_unslash( $_POST['variable_stock'][ $i ] ) );
					}
				}

				// Handle dates.
				$date_on_sale_from = '';
				$date_on_sale_to   = '';

				// Force date from to beginning of day.
				if ( isset( $_POST['variable_sale_price_dates_from'][ $i ] ) ) {
					$date_on_sale_from = wc_clean( wp_unslash( $_POST['variable_sale_price_dates_from'][ $i ] ) );

					if ( ! empty( $date_on_sale_from ) ) {
						$date_on_sale_from = date( 'Y-m-d 00:00:00', strtotime( $date_on_sale_from ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					}
				}

				// Force date to to the end of the day.
				if ( isset( $_POST['variable_sale_price_dates_to'][ $i ] ) ) {
					$date_on_sale_to = wc_clean( wp_unslash( $_POST['variable_sale_price_dates_to'][ $i ] ) );

					if ( ! empty( $date_on_sale_to ) ) {
						$date_on_sale_to = date( 'Y-m-d 23:59:59', strtotime( $date_on_sale_to ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					}
				}

				$errors = $variation->set_props(
					array(
						'status'            => isset( $_POST['variable_enabled'][ $i ] ) ? 'publish' : 'private',
						'menu_order'        => isset( $_POST['variation_menu_order'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variation_menu_order'][ $i ] ) ) : null,
						'regular_price'     => isset( $_POST['variable_regular_price'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_regular_price'][ $i ] ) ) : null,
						'sale_price'        => isset( $_POST['variable_sale_price'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_sale_price'][ $i ] ) ) : null,
						'virtual'           => isset( $_POST['variable_is_virtual'][ $i ] ),
						'downloadable'      => isset( $_POST['variable_is_downloadable'][ $i ] ),
						'date_on_sale_from' => $date_on_sale_from,
						'date_on_sale_to'   => $date_on_sale_to,
						'description'       => isset( $_POST['variable_description'][ $i ] ) ? wp_kses_post( wp_unslash( $_POST['variable_description'][ $i ] ) ) : null,
						'download_limit'    => isset( $_POST['variable_download_limit'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_download_limit'][ $i ] ) ) : null,
						'download_expiry'   => isset( $_POST['variable_download_expiry'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_download_expiry'][ $i ] ) ) : null,
						// Those are sanitized inside prepare_downloads.
						'downloads'         => self::prepare_downloads(
							isset( $_POST['_wc_variation_file_names'][ $variation_id ] ) ? wp_unslash( $_POST['_wc_variation_file_names'][ $variation_id ] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
							isset( $_POST['_wc_variation_file_urls'][ $variation_id ] ) ? wp_unslash( $_POST['_wc_variation_file_urls'][ $variation_id ] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
							isset( $_POST['_wc_variation_file_hashes'][ $variation_id ] ) ? wp_unslash( $_POST['_wc_variation_file_hashes'][ $variation_id ] ) : array() // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						),
						'manage_stock'      => isset( $_POST['variable_manage_stock'][ $i ] ),
						'stock_quantity'    => $stock,
						'low_stock_amount'  => isset( $_POST['variable_low_stock_amount'][ $i ] ) && '' !== $_POST['variable_low_stock_amount'][ $i ] ? wc_stock_amount( wp_unslash( $_POST['variable_low_stock_amount'][ $i ] ) ) : '',
						'backorders'        => isset( $_POST['variable_backorders'], $_POST['variable_backorders'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_backorders'][ $i ] ) ) : null,
						'stock_status'      => isset( $_POST['variable_stock_status'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_stock_status'][ $i ] ) ) : null,
						'image_id'          => isset( $_POST['upload_image_id'][ $i ] ) ? wc_clean( wp_unslash( $_POST['upload_image_id'][ $i ] ) ) : null,
						'attributes'        => self::prepare_set_attributes( $parent->get_attributes(), 'attribute_', $i ),
						'sku'               => isset( $_POST['variable_sku'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_sku'][ $i ] ) ) : '',
						'weight'            => isset( $_POST['variable_weight'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_weight'][ $i ] ) ) : '',
						'length'            => isset( $_POST['variable_length'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_length'][ $i ] ) ) : '',
						'width'             => isset( $_POST['variable_width'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_width'][ $i ] ) ) : '',
						'height'            => isset( $_POST['variable_height'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_height'][ $i ] ) ) : '',
						'shipping_class_id' => isset( $_POST['variable_shipping_class'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_shipping_class'][ $i ] ) ) : null,
						'tax_class'         => isset( $_POST['variable_tax_class'][ $i ] ) ? wc_clean( wp_unslash( $_POST['variable_tax_class'][ $i ] ) ) : null,
					)
				);

				if ( is_wp_error( $errors ) ) {
					WC_Admin_Meta_Boxes::add_error( $errors->get_error_message() );
				}

				/**
				 * Set variation props before save.
				 *
				 * @param object $variation WC_Product_Variation object.
				 * @param int $i
				 * @since 3.8.0
				 */
				do_action( 'woocommerce_admin_process_variation_object', $variation, $i );

				$variation->save();
				/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
				do_action( 'woocommerce_save_product_variation', $variation_id, $i );
				/* phpcs: enable */
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}

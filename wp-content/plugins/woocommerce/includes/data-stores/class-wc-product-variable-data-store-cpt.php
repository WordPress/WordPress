<?php
/**
 * File for WC Variable Product Data Store class.
 *
 * @package WooCommerce\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Variable Product Data Store: Stored in CPT.
 *
 * @version 3.0.0
 */
class WC_Product_Variable_Data_Store_CPT extends WC_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface, WC_Product_Variable_Data_Store_Interface {

	/**
	 * Cached & hashed prices array for child variations.
	 *
	 * @var array
	 */
	protected $prices_array = array();

	/**
	 * Read attributes from post meta.
	 *
	 * @param WC_Product $product Product object.
	 */
	protected function read_attributes( &$product ) {
		$meta_attributes = get_post_meta( $product->get_id(), '_product_attributes', true );

		if ( ! empty( $meta_attributes ) && is_array( $meta_attributes ) ) {
			$attributes   = array();
			$force_update = false;
			foreach ( $meta_attributes as $meta_attribute_key => $meta_attribute_value ) {
				$meta_value = array_merge(
					array(
						'name'         => '',
						'value'        => '',
						'position'     => 0,
						'is_visible'   => 0,
						'is_variation' => 0,
						'is_taxonomy'  => 0,
					),
					(array) $meta_attribute_value
				);

				// Maintain data integrity. 4.9 changed sanitization functions - update the values here so variations function correctly.
				if ( $meta_value['is_variation'] && strstr( $meta_value['name'], '/' ) && sanitize_title( $meta_value['name'] ) !== $meta_attribute_key ) {
					global $wpdb;

					$old_slug      = 'attribute_' . $meta_attribute_key;
					$new_slug      = 'attribute_' . sanitize_title( $meta_value['name'] );
					$old_meta_rows = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s;", $old_slug ) ); // WPCS: db call ok, cache ok.

					if ( $old_meta_rows ) {
						foreach ( $old_meta_rows as $old_meta_row ) {
							update_post_meta( $old_meta_row->post_id, $new_slug, $old_meta_row->meta_value );
						}
					}

					$force_update = true;
				}

				// Check if is a taxonomy attribute.
				if ( ! empty( $meta_value['is_taxonomy'] ) ) {
					if ( ! taxonomy_exists( $meta_value['name'] ) ) {
						continue;
					}
					$id      = wc_attribute_taxonomy_id_by_name( $meta_value['name'] );
					$options = wc_get_object_terms( $product->get_id(), $meta_value['name'], 'term_id' );
				} else {
					$id      = 0;
					$options = wc_get_text_attributes( $meta_value['value'] );
				}

				$attribute = new WC_Product_Attribute();
				$attribute->set_id( $id );
				$attribute->set_name( $meta_value['name'] );
				$attribute->set_options( $options );
				$attribute->set_position( $meta_value['position'] );
				$attribute->set_visible( $meta_value['is_visible'] );
				$attribute->set_variation( $meta_value['is_variation'] );
				$attributes[] = $attribute;
			}
			$product->set_attributes( $attributes );

			if ( $force_update ) {
				$this->update_attributes( $product, true );
			}
		}
	}

	/**
	 * Read product data.
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @since 3.0.0
	 */
	protected function read_product_data( &$product ) {
		parent::read_product_data( $product );

		// Make sure data which does not apply to variables is unset.
		$product->set_regular_price( '' );
		$product->set_sale_price( '' );
	}

	/**
	 * Loads variation child IDs.
	 *
	 * @param WC_Product $product Product object.
	 * @param bool       $force_read True to bypass the transient.
	 *
	 * @return array
	 */
	public function read_children( &$product, $force_read = false ) {
		$children_transient_name = 'wc_product_children_' . $product->get_id();
		$children                = get_transient( $children_transient_name );
		if ( false === $children ) {
			$children = array();
		}

		if ( empty( $children ) || ! is_array( $children ) || ! isset( $children['all'] ) || ! isset( $children['visible'] ) || $force_read ) {
			$all_args = array(
				'post_parent' => $product->get_id(),
				'post_type'   => 'product_variation',
				'orderby'     => array(
					'menu_order' => 'ASC',
					'ID'         => 'ASC',
				),
				'fields'      => 'ids',
				'post_status' => array( 'publish', 'private' ),
				'numberposts' => -1, // phpcs:ignore WordPress.VIP.PostsPerPage.posts_per_page_numberposts
			);

			$visible_only_args                = $all_args;
			$visible_only_args['post_status'] = 'publish';

			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$visible_only_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'outofstock',
					'operator' => 'NOT IN',
				);
			}
			$children['all']     = get_posts( apply_filters( 'woocommerce_variable_children_args', $all_args, $product, false ) );
			$children['visible'] = get_posts( apply_filters( 'woocommerce_variable_children_args', $visible_only_args, $product, true ) );

			set_transient( $children_transient_name, $children, DAY_IN_SECONDS * 30 );
		}

		$children['all']     = wp_parse_id_list( (array) $children['all'] );
		$children['visible'] = wp_parse_id_list( (array) $children['visible'] );

		return $children;
	}

	/**
	 * Loads an array of attributes used for variations, as well as their possible values.
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @return array
	 */
	public function read_variation_attributes( &$product ) {
		global $wpdb;

		$variation_attributes = array();
		$attributes           = $product->get_attributes();
		$child_ids            = $product->get_children();
		$cache_key            = WC_Cache_Helper::get_cache_prefix( 'product_' . $product->get_id() ) . 'product_variation_attributes_' . $product->get_id();
		$cache_group          = 'products';
		$cached_data          = wp_cache_get( $cache_key, $cache_group );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $attribute ) {
				if ( empty( $attribute['is_variation'] ) ) {
					continue;
				}

				// Get possible values for this attribute, for only visible variations.
				if ( ! empty( $child_ids ) ) {
					$format     = array_fill( 0, count( $child_ids ), '%d' );
					$query_in   = '(' . implode( ',', $format ) . ')';
					$query_args = array( 'attribute_name' => wc_variation_attribute_name( $attribute['name'] ) ) + $child_ids;
					$values     = array_unique(
						$wpdb->get_col(
							$wpdb->prepare(
								"SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN {$query_in}", // @codingStandardsIgnoreLine.
								$query_args
							)
						)
					);
				} else {
					$values = array();
				}

				// Empty value indicates that all options for given attribute are available.
				if ( in_array( null, $values, true ) || in_array( '', $values, true ) || empty( $values ) ) {
					$values = $attribute['is_taxonomy'] ? wc_get_object_terms( $product->get_id(), $attribute['name'], 'slug' ) : wc_get_text_attributes( $attribute['value'] );
					// Get custom attributes (non taxonomy) as defined.
				} elseif ( ! $attribute['is_taxonomy'] ) {
					$text_attributes          = wc_get_text_attributes( $attribute['value'] );
					$assigned_text_attributes = $values;
					$values                   = array();

					// Pre 2.4 handling where 'slugs' were saved instead of the full text attribute.
					if ( version_compare( get_post_meta( $product->get_id(), '_product_version', true ), '2.4.0', '<' ) ) {
						$assigned_text_attributes = array_map( 'sanitize_title', $assigned_text_attributes );
						foreach ( $text_attributes as $text_attribute ) {
							if ( in_array( sanitize_title( $text_attribute ), $assigned_text_attributes, true ) ) {
								$values[] = $text_attribute;
							}
						}
					} else {
						foreach ( $text_attributes as $text_attribute ) {
							if ( in_array( $text_attribute, $assigned_text_attributes, true ) ) {
								$values[] = $text_attribute;
							}
						}
					}
				}
				$variation_attributes[ $attribute['name'] ] = array_unique( $values );
			}
		}

		wp_cache_set( $cache_key, $variation_attributes, $cache_group );

		return $variation_attributes;
	}

	/**
	 * Get an array of all sale and regular prices from all variations. This is used for example when displaying the price range at variable product level or seeing if the variable product is on sale.
	 *
	 * Can be filtered by plugins which modify costs, but otherwise will include the raw meta costs unlike get_price() which runs costs through the woocommerce_get_price filter.
	 * This is to ensure modified prices are not cached, unless intended.
	 *
	 * @param WC_Product $product Product object.
	 * @param bool       $for_display If true, prices will be adapted for display based on the `woocommerce_tax_display_shop` setting (including or excluding taxes).
	 *
	 * @return array of prices
	 * @since  3.0.0
	 */
	public function read_price_data( &$product, $for_display = false ) {

		/**
		 * Transient name for storing prices for this product (note: Max transient length is 45)
		 *
		 * @since 2.5.0 a single transient is used per product for all prices, rather than many transients per product.
		 */
		$transient_name    = 'wc_var_prices_' . $product->get_id();
		$transient_version = WC_Cache_Helper::get_transient_version( 'product' );
		$price_hash        = $this->get_price_hash( $product, $for_display );

		// Check if prices array is stale.
		if ( ! isset( $this->prices_array['version'] ) || $this->prices_array['version'] !== $transient_version ) {
			$this->prices_array = array(
				'version' => $transient_version,
			);
		}

		/**
		 * $this->prices_array is an array of values which may have been modified from what is stored in transients - this may not match $transient_cached_prices_array.
		 * If the value has already been generated, we don't need to grab the values again so just return them. They are already filtered.
		 */
		if ( empty( $this->prices_array[ $price_hash ] ) ) {
			$transient_cached_prices_array = array_filter( (array) json_decode( strval( get_transient( $transient_name ) ), true ) );

			// If the product version has changed since the transient was last saved, reset the transient cache.
			if ( ! isset( $transient_cached_prices_array['version'] ) || $transient_version !== $transient_cached_prices_array['version'] ) {
				$transient_cached_prices_array = array(
					'version' => $transient_version,
				);
			}

			// If the prices are not stored for this hash, generate them and add to the transient.
			if ( empty( $transient_cached_prices_array[ $price_hash ] ) ) {
				$prices_array = array(
					'price'         => array(),
					'regular_price' => array(),
					'sale_price'    => array(),
				);

				$variation_ids = $product->get_visible_children();

				if ( is_callable( '_prime_post_caches' ) ) {
					_prime_post_caches( $variation_ids );
				}

				foreach ( $variation_ids as $variation_id ) {
					$variation = wc_get_product( $variation_id );

					if ( $variation ) {
						$price         = apply_filters( 'woocommerce_variation_prices_price', $variation->get_price( 'edit' ), $variation, $product );
						$regular_price = apply_filters( 'woocommerce_variation_prices_regular_price', $variation->get_regular_price( 'edit' ), $variation, $product );
						$sale_price    = apply_filters( 'woocommerce_variation_prices_sale_price', $variation->get_sale_price( 'edit' ), $variation, $product );

						// Skip empty prices.
						if ( '' === $price ) {
							continue;
						}

						// If sale price does not equal price, the product is not yet on sale.
						if ( $sale_price === $regular_price || $sale_price !== $price ) {
							$sale_price = $regular_price;
						}

						// If we are getting prices for display, we need to account for taxes.
						if ( $for_display ) {
							if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
								$price         = '' === $price ? '' : wc_get_price_including_tax(
									$variation,
									array(
										'qty'   => 1,
										'price' => $price,
									)
								);
								$regular_price = '' === $regular_price ? '' : wc_get_price_including_tax(
									$variation,
									array(
										'qty'   => 1,
										'price' => $regular_price,
									)
								);
								$sale_price    = '' === $sale_price ? '' : wc_get_price_including_tax(
									$variation,
									array(
										'qty'   => 1,
										'price' => $sale_price,
									)
								);
							} else {
								$price         = '' === $price ? '' : wc_get_price_excluding_tax(
									$variation,
									array(
										'qty'   => 1,
										'price' => $price,
									)
								);
								$regular_price = '' === $regular_price ? '' : wc_get_price_excluding_tax(
									$variation,
									array(
										'qty'   => 1,
										'price' => $regular_price,
									)
								);
								$sale_price    = '' === $sale_price ? '' : wc_get_price_excluding_tax(
									$variation,
									array(
										'qty'   => 1,
										'price' => $sale_price,
									)
								);
							}
						}

						$prices_array['price'][ $variation_id ]         = wc_format_decimal( $price, wc_get_price_decimals() );
						$prices_array['regular_price'][ $variation_id ] = wc_format_decimal( $regular_price, wc_get_price_decimals() );
						$prices_array['sale_price'][ $variation_id ]    = wc_format_decimal( $sale_price, wc_get_price_decimals() );

						$prices_array = apply_filters( 'woocommerce_variation_prices_array', $prices_array, $variation, $for_display );
					}
				}

				// Add all pricing data to the transient array.
				foreach ( $prices_array as $key => $values ) {
					$transient_cached_prices_array[ $price_hash ][ $key ] = $values;
				}

				set_transient( $transient_name, wp_json_encode( $transient_cached_prices_array ), DAY_IN_SECONDS * 30 );
			}

			/**
			 * Give plugins one last chance to filter the variation prices array which has been generated and store locally to the class.
			 * This value may differ from the transient cache. It is filtered once before storing locally.
			 */
			$this->prices_array[ $price_hash ] = apply_filters( 'woocommerce_variation_prices', $transient_cached_prices_array[ $price_hash ], $product, $for_display );
		}
		return $this->prices_array[ $price_hash ];
	}

	/**
	 * Create unique cache key based on the tax location (affects displayed/cached prices), product version and active price filters.
	 * DEVELOPERS should filter this hash if offering conditional pricing to keep it unique.
	 *
	 * @param WC_Product $product Product object.
	 * @param bool       $for_display If taxes should be calculated or not.
	 *
	 * @since  3.0.0
	 * @return string
	 */
	protected function get_price_hash( &$product, $for_display = false ) {
		global $wp_filter;

		$price_hash = array( false );

		if ( $for_display && wc_tax_enabled() ) {
			$price_hash = array(
				get_option( 'woocommerce_tax_display_shop', 'excl' ),
				WC_Tax::get_rates(),
				empty( WC()->customer ) ? false : WC()->customer->is_vat_exempt(),
			);
		}

		$filter_names = array( 'woocommerce_variation_prices_price', 'woocommerce_variation_prices_regular_price', 'woocommerce_variation_prices_sale_price' );

		foreach ( $filter_names as $filter_name ) {
			if ( ! empty( $wp_filter[ $filter_name ] ) ) {
				$price_hash[ $filter_name ] = array();

				foreach ( $wp_filter[ $filter_name ] as $priority => $callbacks ) {
					$price_hash[ $filter_name ][] = array_values( wp_list_pluck( $callbacks, 'function' ) );
				}
			}
		}

		return md5( wp_json_encode( apply_filters( 'woocommerce_get_variation_prices_hash', $price_hash, $product, $for_display ) ) );
	}

	/**
	 * Does a child have a weight set?
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @since  3.0.0
	 * @return boolean
	 */
	public function child_has_weight( $product ) {
		global $wpdb;
		$children = $product->get_visible_children();
		if ( ! $children ) {
			return false;
		}

		$format   = array_fill( 0, count( $children ), '%d' );
		$query_in = '(' . implode( ',', $format ) . ')';

		return null !== $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_weight' AND meta_value > 0 AND post_id IN {$query_in}", $children ) ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Does a child have dimensions set?
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @since  3.0.0
	 * @return boolean
	 */
	public function child_has_dimensions( $product ) {
		global $wpdb;
		$children = $product->get_visible_children();
		if ( ! $children ) {
			return false;
		}

		$format   = array_fill( 0, count( $children ), '%d' );
		$query_in = '(' . implode( ',', $format ) . ')';

		return null !== $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key IN ( '_length', '_width', '_height' ) AND meta_value > 0 AND post_id IN {$query_in}", $children ) ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Is a child in stock?
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @since  3.0.0
	 * @return boolean
	 */
	public function child_is_in_stock( $product ) {
		return $this->child_has_stock_status( $product, 'instock' );
	}

	/**
	 * Does a child have a stock status?
	 *
	 * @param WC_Product $product Product object.
	 * @param string     $status 'instock', 'outofstock', or 'onbackorder'.
	 *
	 * @since  3.3.0
	 * @return boolean
	 */
	public function child_has_stock_status( $product, $status ) {
		global $wpdb;

		$children = $product->get_children();

		if ( $children ) {
			$format     = array_fill( 0, count( $children ), '%d' );
			$query_in   = '(' . implode( ',', $format ) . ')';
			$query_args = array( 'stock_status' => $status ) + $children;
			// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
			if ( get_option( 'woocommerce_product_lookup_table_is_generating' ) ) {
				$query = "SELECT COUNT( post_id ) FROM {$wpdb->postmeta} WHERE meta_key = '_stock_status' AND meta_value = %s AND post_id IN {$query_in}";
			} else {
				$query = "SELECT COUNT( product_id ) FROM {$wpdb->wc_product_meta_lookup} WHERE stock_status = %s AND product_id IN {$query_in}";
			}
			$children_with_status = $wpdb->get_var(
				$wpdb->prepare(
					$query,
					$query_args
				)
			);
			// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
		} else {
			$children_with_status = 0;
		}

		return (bool) $children_with_status;
	}

	/**
	 * Syncs all variation names if the parent name is changed.
	 *
	 * @param WC_Product $product Product object.
	 * @param string     $previous_name Variation previous name.
	 * @param string     $new_name Variation new name.
	 *
	 * @since 3.0.0
	 */
	public function sync_variation_names( &$product, $previous_name = '', $new_name = '' ) {
		if ( $new_name !== $previous_name ) {
			global $wpdb;

			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->posts}
					SET post_title = REPLACE( post_title, %s, %s )
					WHERE post_type = 'product_variation'
					AND post_parent = %d",
					$previous_name ? $previous_name : 'AUTO-DRAFT',
					$new_name,
					$product->get_id()
				)
			);
		}
	}

	/**
	 * Stock managed at the parent level - update children being managed by this product.
	 * This sync function syncs downwards (from parent to child) when the variable product is saved.
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @since 3.0.0
	 */
	public function sync_managed_variation_stock_status( &$product ) {
		global $wpdb;

		if ( $product->get_manage_stock() ) {
			$children = $product->get_children();
			$changed  = false;

			if ( $children ) {
				$status           = $product->get_stock_status();
				$format           = array_fill( 0, count( $children ), '%d' );
				$query_in         = '(' . implode( ',', $format ) . ')';
				$managed_children = array_unique( $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_manage_stock' AND meta_value != 'yes' AND post_id IN {$query_in}", $children ) ) ); // @codingStandardsIgnoreLine.
				foreach ( $managed_children as $managed_child ) {
					if ( update_post_meta( $managed_child, '_stock_status', $status ) ) {
						$this->update_lookup_table( $managed_child, 'wc_product_meta_lookup' );
						$changed = true;
					}
				}
			}

			if ( $changed ) {
				$children = $this->read_children( $product, true );
				$product->set_children( $children['all'] );
				$product->set_visible_children( $children['visible'] );
			}
		}
	}

	/**
	 * Sync variable product prices with children.
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @since 3.0.0
	 */
	public function sync_price( &$product ) {
		global $wpdb;

		$children = $product->get_visible_children();
		if ( $children ) {
			$format   = array_fill( 0, count( $children ), '%d' );
			$query_in = '(' . implode( ',', $format ) . ')';
			$prices   = array_unique( $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_price' AND post_id IN {$query_in}", $children ) ) ); // @codingStandardsIgnoreLine.
		} else {
			$prices = array();
		}

		delete_post_meta( $product->get_id(), '_price' );
		delete_post_meta( $product->get_id(), '_sale_price' );
		delete_post_meta( $product->get_id(), '_regular_price' );

		if ( $prices ) {
			sort( $prices, SORT_NUMERIC );
			// To allow sorting and filtering by multiple values, we have no choice but to store child prices in this manner.
			foreach ( $prices as $price ) {
				if ( is_null( $price ) || '' === $price ) {
					continue;
				}
				add_post_meta( $product->get_id(), '_price', $price, false );
			}
		}

		$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );

		/**
		 * Fire an action for this direct update so it can be detected by other code.
		 *
		 * @since 3.6
		 * @param int $product_id Product ID that was updated directly.
		 */
		do_action( 'woocommerce_updated_product_price', $product->get_id() );
	}

	/**
	 * Sync variable product stock status with children.
	 * Change does not persist unless saved by caller.
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @since 3.0.0
	 */
	public function sync_stock_status( &$product ) {
		if ( $product->child_is_in_stock() ) {
			$product->set_stock_status( 'instock' );
		} elseif ( $product->child_is_on_backorder() ) {
			$product->set_stock_status( 'onbackorder' );
		} else {
			$product->set_stock_status( 'outofstock' );
		}
	}

	/**
	 * Delete variations of a product.
	 *
	 * @param int  $product_id Product ID.
	 * @param bool $force_delete False to trash.
	 *
	 * @since 3.0.0
	 */
	public function delete_variations( $product_id, $force_delete = false ) {
		if ( ! is_numeric( $product_id ) || 0 >= $product_id ) {
			return;
		}

		$variation_ids = wp_parse_id_list(
			get_posts(
				array(
					'post_parent' => $product_id,
					'post_type'   => 'product_variation',
					'fields'      => 'ids',
					'post_status' => array( 'any', 'trash', 'auto-draft' ),
					'numberposts' => -1, // phpcs:ignore WordPress.VIP.PostsPerPage.posts_per_page_numberposts
				)
			)
		);

		if ( ! empty( $variation_ids ) ) {
			foreach ( $variation_ids as $variation_id ) {
				if ( $force_delete ) {
					do_action( 'woocommerce_before_delete_product_variation', $variation_id );
					wp_delete_post( $variation_id, true );
					do_action( 'woocommerce_delete_product_variation', $variation_id );
				} else {
					wp_trash_post( $variation_id );
					do_action( 'woocommerce_trash_product_variation', $variation_id );
				}
			}
		}

		delete_transient( 'wc_product_children_' . $product_id );
	}

	/**
	 * Untrash variations.
	 *
	 * @param int $product_id Product ID.
	 */
	public function untrash_variations( $product_id ) {
		$variation_ids = wp_parse_id_list(
			get_posts(
				array(
					'post_parent' => $product_id,
					'post_type'   => 'product_variation',
					'fields'      => 'ids',
					'post_status' => 'trash',
					'numberposts' => -1, // phpcs:ignore WordPress.VIP.PostsPerPage.posts_per_page_numberposts
				)
			)
		);

		if ( ! empty( $variation_ids ) ) {
			foreach ( $variation_ids as $variation_id ) {
				wp_untrash_post( $variation_id );
			}
		}

		delete_transient( 'wc_product_children_' . $product_id );
	}
}

<?php
/**
 * REST API Legacy Products controller
 *
 * Handles requests to the /products endpoint.
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce\RestApi
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Legacy Products controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_CRUD_Controller
 */
class WC_REST_Legacy_Products_Controller extends WC_REST_CRUD_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v2';

	/**
	 * Query args.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param array           $args    Request args.
	 * @param WP_REST_Request $request Request data.
	 * @return array
	 */
	public function query_args( $args, $request ) {
		// Set post_status.
		$args['post_status'] = $request['status'];

		// Taxonomy query to filter products by type, category,
		// tag, shipping class, and attribute.
		$tax_query = array();

		// Map between taxonomy name and arg's key.
		$taxonomies = array(
			'product_cat'            => 'category',
			'product_tag'            => 'tag',
			'product_shipping_class' => 'shipping_class',
		);

		// Set tax_query for each passed arg.
		foreach ( $taxonomies as $taxonomy => $key ) {
			if ( ! empty( $request[ $key ] ) ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $request[ $key ],
				);
			}
		}

		// Filter product type by slug.
		if ( ! empty( $request['type'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => $request['type'],
			);
		}

		// Filter by attribute and term.
		if ( ! empty( $request['attribute'] ) && ! empty( $request['attribute_term'] ) ) {
			if ( in_array( $request['attribute'], wc_get_attribute_taxonomy_names(), true ) ) {
				$tax_query[] = array(
					'taxonomy' => $request['attribute'],
					'field'    => 'term_id',
					'terms'    => $request['attribute_term'],
				);
			}
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		// Filter featured.
		if ( is_bool( $request['featured'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
				'operator' => true === $request['featured'] ? 'IN' : 'NOT IN',
			);
		}

		// Filter by sku.
		if ( ! empty( $request['sku'] ) ) {
			$skus = explode( ',', $request['sku'] );
			// Include the current string as a SKU too.
			if ( 1 < count( $skus ) ) {
				$skus[] = $request['sku'];
			}

			$args['meta_query'] = $this->add_meta_query( $args, array(
				'key'     => '_sku',
				'value'   => $skus,
				'compare' => 'IN',
			) );
		}

		// Filter by tax class.
		if ( ! empty( $request['tax_class'] ) ) {
			$args['meta_query'] = $this->add_meta_query( $args, array(
				'key'   => '_tax_class',
				'value' => 'standard' !== $request['tax_class'] ? $request['tax_class'] : '',
			) );
		}

		// Price filter.
		if ( ! empty( $request['min_price'] ) || ! empty( $request['max_price'] ) ) {
			$args['meta_query'] = $this->add_meta_query( $args, wc_get_min_max_price_meta_query( $request ) );
		}

		// Filter product in stock or out of stock.
		if ( is_bool( $request['in_stock'] ) ) {
			$args['meta_query'] = $this->add_meta_query( $args, array(
				'key'   => '_stock_status',
				'value' => true === $request['in_stock'] ? 'instock' : 'outofstock',
			) );
		}

		// Filter by on sale products.
		if ( is_bool( $request['on_sale'] ) ) {
			$on_sale_key           = $request['on_sale'] ? 'post__in' : 'post__not_in';
			$args[ $on_sale_key ] += wc_get_product_ids_on_sale();
		}

		// Force the post_type argument, since it's not a user input variable.
		if ( ! empty( $request['sku'] ) ) {
			$args['post_type'] = array( 'product', 'product_variation' );
		} else {
			$args['post_type'] = $this->post_type;
		}

		return $args;
	}

	/**
	 * Prepare a single product output for response.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param WP_Post         $post    Post object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_item_for_response( $post, $request ) {
		$product = wc_get_product( $post );
		$data    = $this->get_product_data( $product );

		// Add variations to variable products.
		if ( $product->is_type( 'variable' ) && $product->has_child() ) {
			$data['variations'] = $product->get_children();
		}

		// Add grouped products data.
		if ( $product->is_type( 'grouped' ) && $product->has_child() ) {
			$data['grouped_products'] = $product->get_children();
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $product, $request ) );

		/**
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
		 * prepared for the response.
		 *
		 * @param WP_REST_Response   $response   The response object.
		 * @param WP_Post            $post       Post object.
		 * @param WP_REST_Request    $request    Request object.
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}", $response, $post, $request );
	}

	/**
	 * Get product menu order.
	 *
	 * @deprecated 3.0.0
	 * @param WC_Product $product Product instance.
	 * @return int
	 */
	protected function get_product_menu_order( $product ) {
		return $product->get_menu_order();
	}

	/**
	 * Save product meta.
	 *
	 * @deprecated 3.0.0
	 * @param WC_Product $product
	 * @param WP_REST_Request $request
	 * @return bool
	 * @throws WC_REST_Exception
	 */
	protected function save_product_meta( $product, $request ) {
		$product = $this->set_product_meta( $product, $request );
		$product->save();

		return true;
	}

	/**
	 * Set product meta.
	 *
	 * @deprecated 3.0.0
	 *
	 * @throws WC_REST_Exception REST API exceptions.
	 * @param WC_Product      $product Product instance.
	 * @param WP_REST_Request $request Request data.
	 * @return WC_Product
	 */
	protected function set_product_meta( $product, $request ) {
		// Virtual.
		if ( isset( $request['virtual'] ) ) {
			$product->set_virtual( $request['virtual'] );
		}

		// Tax status.
		if ( isset( $request['tax_status'] ) ) {
			$product->set_tax_status( $request['tax_status'] );
		}

		// Tax Class.
		if ( isset( $request['tax_class'] ) ) {
			$product->set_tax_class( $request['tax_class'] );
		}

		// Catalog Visibility.
		if ( isset( $request['catalog_visibility'] ) ) {
			$product->set_catalog_visibility( $request['catalog_visibility'] );
		}

		// Purchase Note.
		if ( isset( $request['purchase_note'] ) ) {
			$product->set_purchase_note( wc_clean( $request['purchase_note'] ) );
		}

		// Featured Product.
		if ( isset( $request['featured'] ) ) {
			$product->set_featured( $request['featured'] );
		}

		// Shipping data.
		$product = $this->save_product_shipping_data( $product, $request );

		// SKU.
		if ( isset( $request['sku'] ) ) {
			$product->set_sku( wc_clean( $request['sku'] ) );
		}

		// Attributes.
		if ( isset( $request['attributes'] ) ) {
			$attributes = array();

			foreach ( $request['attributes'] as $attribute ) {
				$attribute_id   = 0;
				$attribute_name = '';

				// Check ID for global attributes or name for product attributes.
				if ( ! empty( $attribute['id'] ) ) {
					$attribute_id   = absint( $attribute['id'] );
					$attribute_name = wc_attribute_taxonomy_name_by_id( $attribute_id );
				} elseif ( ! empty( $attribute['name'] ) ) {
					$attribute_name = wc_clean( $attribute['name'] );
				}

				if ( ! $attribute_id && ! $attribute_name ) {
					continue;
				}

				if ( $attribute_id ) {

					if ( isset( $attribute['options'] ) ) {
						$options = $attribute['options'];

						if ( ! is_array( $attribute['options'] ) ) {
							// Text based attributes - Posted values are term names.
							$options = explode( WC_DELIMITER, $options );
						}

						$values = array_map( 'wc_sanitize_term_text_based', $options );
						$values = array_filter( $values, 'strlen' );
					} else {
						$values = array();
					}

					if ( ! empty( $values ) ) {
						// Add attribute to array, but don't set values.
						$attribute_object = new WC_Product_Attribute();
						$attribute_object->set_id( $attribute_id );
						$attribute_object->set_name( $attribute_name );
						$attribute_object->set_options( $values );
						$attribute_object->set_position( isset( $attribute['position'] ) ? (string) absint( $attribute['position'] ) : '0' );
						$attribute_object->set_visible( ( isset( $attribute['visible'] ) && $attribute['visible'] ) ? 1 : 0 );
						$attribute_object->set_variation( ( isset( $attribute['variation'] ) && $attribute['variation'] ) ? 1 : 0 );
						$attributes[] = $attribute_object;
					}
				} elseif ( isset( $attribute['options'] ) ) {
					// Custom attribute - Add attribute to array and set the values.
					if ( is_array( $attribute['options'] ) ) {
						$values = $attribute['options'];
					} else {
						$values = explode( WC_DELIMITER, $attribute['options'] );
					}
					$attribute_object = new WC_Product_Attribute();
					$attribute_object->set_name( $attribute_name );
					$attribute_object->set_options( $values );
					$attribute_object->set_position( isset( $attribute['position'] ) ? (string) absint( $attribute['position'] ) : '0' );
					$attribute_object->set_visible( ( isset( $attribute['visible'] ) && $attribute['visible'] ) ? 1 : 0 );
					$attribute_object->set_variation( ( isset( $attribute['variation'] ) && $attribute['variation'] ) ? 1 : 0 );
					$attributes[] = $attribute_object;
				}
			}
			$product->set_attributes( $attributes );
		}

		// Sales and prices.
		if ( in_array( $product->get_type(), array( 'variable', 'grouped' ), true ) ) {
			$product->set_regular_price( '' );
			$product->set_sale_price( '' );
			$product->set_date_on_sale_to( '' );
			$product->set_date_on_sale_from( '' );
			$product->set_price( '' );
		} else {
			// Regular Price.
			if ( isset( $request['regular_price'] ) ) {
				$product->set_regular_price( $request['regular_price'] );
			}

			// Sale Price.
			if ( isset( $request['sale_price'] ) ) {
				$product->set_sale_price( $request['sale_price'] );
			}

			if ( isset( $request['date_on_sale_from'] ) ) {
				$product->set_date_on_sale_from( $request['date_on_sale_from'] );
			}

			if ( isset( $request['date_on_sale_to'] ) ) {
				$product->set_date_on_sale_to( $request['date_on_sale_to'] );
			}
		}

		// Product parent ID for groups.
		if ( isset( $request['parent_id'] ) ) {
			$product->set_parent_id( $request['parent_id'] );
		}

		// Sold individually.
		if ( isset( $request['sold_individually'] ) ) {
			$product->set_sold_individually( $request['sold_individually'] );
		}

		// Stock status.
		if ( isset( $request['in_stock'] ) ) {
			$stock_status = true === $request['in_stock'] ? 'instock' : 'outofstock';
		} else {
			$stock_status = $product->get_stock_status();
		}

		// Stock data.
		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			// Manage stock.
			if ( isset( $request['manage_stock'] ) ) {
				$product->set_manage_stock( $request['manage_stock'] );
			}

			// Backorders.
			if ( isset( $request['backorders'] ) ) {
				$product->set_backorders( $request['backorders'] );
			}

			if ( $product->is_type( 'grouped' ) ) {
				$product->set_manage_stock( 'no' );
				$product->set_backorders( 'no' );
				$product->set_stock_quantity( '' );
				$product->set_stock_status( $stock_status );
			} elseif ( $product->is_type( 'external' ) ) {
				$product->set_manage_stock( 'no' );
				$product->set_backorders( 'no' );
				$product->set_stock_quantity( '' );
				$product->set_stock_status( 'instock' );
			} elseif ( $product->get_manage_stock() ) {
				// Stock status is always determined by children so sync later.
				if ( ! $product->is_type( 'variable' ) ) {
					$product->set_stock_status( $stock_status );
				}

				// Stock quantity.
				if ( isset( $request['stock_quantity'] ) ) {
					$product->set_stock_quantity( wc_stock_amount( $request['stock_quantity'] ) );
				} elseif ( isset( $request['inventory_delta'] ) ) {
					$stock_quantity  = wc_stock_amount( $product->get_stock_quantity() );
					$stock_quantity += wc_stock_amount( $request['inventory_delta'] );
					$product->set_stock_quantity( wc_stock_amount( $stock_quantity ) );
				}
			} else {
				// Don't manage stock.
				$product->set_manage_stock( 'no' );
				$product->set_stock_quantity( '' );
				$product->set_stock_status( $stock_status );
			}
		} elseif ( ! $product->is_type( 'variable' ) ) {
			$product->set_stock_status( $stock_status );
		}

		// Upsells.
		if ( isset( $request['upsell_ids'] ) ) {
			$upsells = array();
			$ids     = $request['upsell_ids'];

			if ( ! empty( $ids ) ) {
				foreach ( $ids as $id ) {
					if ( $id && $id > 0 ) {
						$upsells[] = $id;
					}
				}
			}

			$product->set_upsell_ids( $upsells );
		}

		// Cross sells.
		if ( isset( $request['cross_sell_ids'] ) ) {
			$crosssells = array();
			$ids        = $request['cross_sell_ids'];

			if ( ! empty( $ids ) ) {
				foreach ( $ids as $id ) {
					if ( $id && $id > 0 ) {
						$crosssells[] = $id;
					}
				}
			}

			$product->set_cross_sell_ids( $crosssells );
		}

		// Product categories.
		if ( isset( $request['categories'] ) && is_array( $request['categories'] ) ) {
			$product = $this->save_taxonomy_terms( $product, $request['categories'] );
		}

		// Product tags.
		if ( isset( $request['tags'] ) && is_array( $request['tags'] ) ) {
			$product = $this->save_taxonomy_terms( $product, $request['tags'], 'tag' );
		}

		// Downloadable.
		if ( isset( $request['downloadable'] ) ) {
			$product->set_downloadable( $request['downloadable'] );
		}

		// Downloadable options.
		if ( $product->get_downloadable() ) {

			// Downloadable files.
			if ( isset( $request['downloads'] ) && is_array( $request['downloads'] ) ) {
				$product = $this->save_downloadable_files( $product, $request['downloads'] );
			}

			// Download limit.
			if ( isset( $request['download_limit'] ) ) {
				$product->set_download_limit( $request['download_limit'] );
			}

			// Download expiry.
			if ( isset( $request['download_expiry'] ) ) {
				$product->set_download_expiry( $request['download_expiry'] );
			}
		}

		// Product url and button text for external products.
		if ( $product->is_type( 'external' ) ) {
			if ( isset( $request['external_url'] ) ) {
				$product->set_product_url( $request['external_url'] );
			}

			if ( isset( $request['button_text'] ) ) {
				$product->set_button_text( $request['button_text'] );
			}
		}

		// Save default attributes for variable products.
		if ( $product->is_type( 'variable' ) ) {
			$product = $this->save_default_attributes( $product, $request );
		}

		return $product;
	}

	/**
	 * Save variations.
	 *
	 * @deprecated 3.0.0
	 *
	 * @throws WC_REST_Exception REST API exceptions.
	 * @param WC_Product      $product          Product instance.
	 * @param WP_REST_Request $request          Request data.
	 * @return bool
	 */
	protected function save_variations_data( $product, $request ) {
		foreach ( $request['variations'] as $menu_order => $data ) {
			$variation = new WC_Product_Variation( isset( $data['id'] ) ? absint( $data['id'] ) : 0 );

			// Create initial name and status.
			if ( ! $variation->get_slug() ) {
				/* translators: 1: variation id 2: product name */
				$variation->set_name( sprintf( __( 'Variation #%1$s of %2$s', 'woocommerce' ), $variation->get_id(), $product->get_name() ) );
				$variation->set_status( isset( $data['visible'] ) && false === $data['visible'] ? 'private' : 'publish' );
			}

			// Parent ID.
			$variation->set_parent_id( $product->get_id() );

			// Menu order.
			$variation->set_menu_order( $menu_order );

			// Status.
			if ( isset( $data['visible'] ) ) {
				$variation->set_status( false === $data['visible'] ? 'private' : 'publish' );
			}

			// SKU.
			if ( isset( $data['sku'] ) ) {
				$variation->set_sku( wc_clean( $data['sku'] ) );
			}

			// Thumbnail.
			if ( isset( $data['image'] ) && is_array( $data['image'] ) ) {
				$image = $data['image'];
				$image = current( $image );
				if ( is_array( $image ) ) {
					$image['position'] = 0;
				}

				$variation = $this->set_product_images( $variation, array( $image ) );
			}

			// Virtual variation.
			if ( isset( $data['virtual'] ) ) {
				$variation->set_virtual( $data['virtual'] );
			}

			// Downloadable variation.
			if ( isset( $data['downloadable'] ) ) {
				$variation->set_downloadable( $data['downloadable'] );
			}

			// Downloads.
			if ( $variation->get_downloadable() ) {
				// Downloadable files.
				if ( isset( $data['downloads'] ) && is_array( $data['downloads'] ) ) {
					$variation = $this->save_downloadable_files( $variation, $data['downloads'] );
				}

				// Download limit.
				if ( isset( $data['download_limit'] ) ) {
					$variation->set_download_limit( $data['download_limit'] );
				}

				// Download expiry.
				if ( isset( $data['download_expiry'] ) ) {
					$variation->set_download_expiry( $data['download_expiry'] );
				}
			}

			// Shipping data.
			$variation = $this->save_product_shipping_data( $variation, $data );

			// Stock handling.
			if ( isset( $data['manage_stock'] ) ) {
				$variation->set_manage_stock( $data['manage_stock'] );
			}

			if ( isset( $data['in_stock'] ) ) {
				$variation->set_stock_status( true === $data['in_stock'] ? 'instock' : 'outofstock' );
			}

			if ( isset( $data['backorders'] ) ) {
				$variation->set_backorders( $data['backorders'] );
			}

			if ( $variation->get_manage_stock() ) {
				if ( isset( $data['stock_quantity'] ) ) {
					$variation->set_stock_quantity( $data['stock_quantity'] );
				} elseif ( isset( $data['inventory_delta'] ) ) {
					$stock_quantity  = wc_stock_amount( $variation->get_stock_quantity() );
					$stock_quantity += wc_stock_amount( $data['inventory_delta'] );
					$variation->set_stock_quantity( $stock_quantity );
				}
			} else {
				$variation->set_backorders( 'no' );
				$variation->set_stock_quantity( '' );
			}

			// Regular Price.
			if ( isset( $data['regular_price'] ) ) {
				$variation->set_regular_price( $data['regular_price'] );
			}

			// Sale Price.
			if ( isset( $data['sale_price'] ) ) {
				$variation->set_sale_price( $data['sale_price'] );
			}

			if ( isset( $data['date_on_sale_from'] ) ) {
				$variation->set_date_on_sale_from( $data['date_on_sale_from'] );
			}

			if ( isset( $data['date_on_sale_to'] ) ) {
				$variation->set_date_on_sale_to( $data['date_on_sale_to'] );
			}

			// Tax class.
			if ( isset( $data['tax_class'] ) ) {
				$variation->set_tax_class( $data['tax_class'] );
			}

			// Description.
			if ( isset( $data['description'] ) ) {
				$variation->set_description( wp_kses_post( $data['description'] ) );
			}

			// Update taxonomies.
			if ( isset( $data['attributes'] ) ) {
				$attributes = array();
				$parent_attributes = $product->get_attributes();

				foreach ( $data['attributes'] as $attribute ) {
					$attribute_id   = 0;
					$attribute_name = '';

					// Check ID for global attributes or name for product attributes.
					if ( ! empty( $attribute['id'] ) ) {
						$attribute_id   = absint( $attribute['id'] );
						$attribute_name = wc_attribute_taxonomy_name_by_id( $attribute_id );
					} elseif ( ! empty( $attribute['name'] ) ) {
						$attribute_name = sanitize_title( $attribute['name'] );
					}

					if ( ! $attribute_id && ! $attribute_name ) {
						continue;
					}

					if ( ! isset( $parent_attributes[ $attribute_name ] ) || ! $parent_attributes[ $attribute_name ]->get_variation() ) {
						continue;
					}

					$attribute_key   = sanitize_title( $parent_attributes[ $attribute_name ]->get_name() );
					$attribute_value = isset( $attribute['option'] ) ? wc_clean( stripslashes( $attribute['option'] ) ) : '';

					if ( $parent_attributes[ $attribute_name ]->is_taxonomy() ) {
						// If dealing with a taxonomy, we need to get the slug from the name posted to the API.
						$term = get_term_by( 'name', $attribute_value, $attribute_name );

						if ( $term && ! is_wp_error( $term ) ) {
							$attribute_value = $term->slug;
						} else {
							$attribute_value = sanitize_title( $attribute_value );
						}
					}

					$attributes[ $attribute_key ] = $attribute_value;
				}

				$variation->set_attributes( $attributes );
			}

			$variation->save();

			do_action( 'woocommerce_rest_save_product_variation', $variation->get_id(), $menu_order, $data );
		}

		return true;
	}

	/**
	 * Add post meta fields.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param WP_Post         $post    Post data.
	 * @param WP_REST_Request $request Request data.
	 * @return bool|WP_Error
	 */
	protected function add_post_meta_fields( $post, $request ) {
		return $this->update_post_meta_fields( $post, $request );
	}

	/**
	 * Update post meta fields.
	 *
	 * @param WP_Post         $post    Post data.
	 * @param WP_REST_Request $request Request data.
	 * @return bool|WP_Error
	 */
	protected function update_post_meta_fields( $post, $request ) {
		$product = wc_get_product( $post );

		// Check for featured/gallery images, upload it and set it.
		if ( isset( $request['images'] ) ) {
			$product = $this->set_product_images( $product, $request['images'] );
		}

		// Save product meta fields.
		$product = $this->set_product_meta( $product, $request );

		// Save the product data.
		$product->save();

		// Save variations.
		if ( $product->is_type( 'variable' ) ) {
			if ( isset( $request['variations'] ) && is_array( $request['variations'] ) ) {
				$this->save_variations_data( $product, $request );
			}
		}

		// Clear caches here so in sync with any new variations/children.
		wc_delete_product_transients( $product->get_id() );
		wp_cache_delete( 'product-' . $product->get_id(), 'products' );

		return true;
	}

	/**
	 * Delete post.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param int|WP_Post $id Post ID or WP_Post instance.
	 */
	protected function delete_post( $id ) {
		if ( ! empty( $id->ID ) ) {
			$id = $id->ID;
		} elseif ( ! is_numeric( $id ) || 0 >= $id ) {
			return;
		}

		// Delete product attachments.
		$attachments = get_posts( array(
			'post_parent' => $id,
			'post_status' => 'any',
			'post_type'   => 'attachment',
		) );

		foreach ( (array) $attachments as $attachment ) {
			wp_delete_attachment( $attachment->ID, true );
		}

		// Delete product.
		$product = wc_get_product( $id );
		$product->delete( true );
	}

	/**
	 * Get post types.
	 *
	 * @deprecated 3.0.0
	 *
	 * @return array
	 */
	protected function get_post_types() {
		return array( 'product', 'product_variation' );
	}

	/**
	 * Save product images.
	 *
	 * @deprecated 3.0.0
	 *
	 * @param int $product_id
	 * @param array $images
	 * @throws WC_REST_Exception
	 */
	protected function save_product_images( $product_id, $images ) {
		$product = wc_get_product( $product_id );

		return set_product_images( $product, $images );
	}
}

<?php
/**
 * REST API variations controller
 *
 * Handles requests to the /products/<product_id>/variations endpoints.
 *
 * @package WooCommerce\RestApi
 * @since   3.0.0
 */

use Automattic\WooCommerce\Utilities\I18nUtil;

defined( 'ABSPATH' ) || exit;

/**
 * REST API variations controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Products_V2_Controller
 */
class WC_REST_Product_Variations_V2_Controller extends WC_REST_Products_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v2';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'products/(?P<product_id>[\d]+)/variations';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'product_variation';

	/**
	 * Register the routes for products.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace, '/' . $this->rest_base, array(
				'args'   => array(
					'product_id' => array(
						'description' => __( 'Unique identifier for the variable product.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
				'args'   => array(
					'product_id' => array(
						'description' => __( 'Unique identifier for the variable product.', 'woocommerce' ),
						'type'        => 'integer',
					),
					'id'         => array(
						'description' => __( 'Unique identifier for the variation.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param(
							array(
								'default' => 'view',
							)
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'default'     => false,
							'type'        => 'boolean',
							'description' => __( 'Whether to bypass trash and force deletion.', 'woocommerce' ),
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/batch', array(
				'args'   => array(
					'product_id' => array(
						'description' => __( 'Unique identifier for the variable product.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'batch_items' ),
					'permission_callback' => array( $this, 'batch_items_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_public_batch_schema' ),
			)
		);
	}

	/**
	 * Get object.
	 *
	 * @since  3.0.0
	 * @param  int $id Object ID.
	 * @return WC_Data
	 */
	protected function get_object( $id ) {
		return wc_get_product( $id );
	}

	/**
	 * Check if a given request has access to update an item.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function update_item_permissions_check( $request ) {
		$object = $this->get_object( (int) $request['id'] );

		if ( $object && 0 !== $object->get_id() && ! wc_rest_check_post_permissions( $this->post_type, 'edit', $object->get_id() ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you are not allowed to edit this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		// Check if variation belongs to the correct parent product.
		if ( $object && 0 !== $object->get_parent_id() && absint( $request['product_id'] ) !== $object->get_parent_id() ) {
			return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Parent product does not match current variation.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Prepare a single variation output for response.
	 *
	 * @since  3.0.0
	 * @param  WC_Data         $object  Object data.
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_object_for_response( $object, $request ) {
		$data = array(
			'id'                    => $object->get_id(),
			'date_created'          => wc_rest_prepare_date_response( $object->get_date_created(), false ),
			'date_created_gmt'      => wc_rest_prepare_date_response( $object->get_date_created() ),
			'date_modified'         => wc_rest_prepare_date_response( $object->get_date_modified(), false ),
			'date_modified_gmt'     => wc_rest_prepare_date_response( $object->get_date_modified() ),
			'description'           => wc_format_content( $object->get_description() ),
			'permalink'             => $object->get_permalink(),
			'sku'                   => $object->get_sku(),
			'price'                 => $object->get_price(),
			'regular_price'         => $object->get_regular_price(),
			'sale_price'            => $object->get_sale_price(),
			'date_on_sale_from'     => wc_rest_prepare_date_response( $object->get_date_on_sale_from(), false ),
			'date_on_sale_from_gmt' => wc_rest_prepare_date_response( $object->get_date_on_sale_from() ),
			'date_on_sale_to'       => wc_rest_prepare_date_response( $object->get_date_on_sale_to(), false ),
			'date_on_sale_to_gmt'   => wc_rest_prepare_date_response( $object->get_date_on_sale_to() ),
			'on_sale'               => $object->is_on_sale(),
			'visible'               => $object->is_visible(),
			'purchasable'           => $object->is_purchasable(),
			'virtual'               => $object->is_virtual(),
			'downloadable'          => $object->is_downloadable(),
			'downloads'             => $this->get_downloads( $object ),
			'download_limit'        => '' !== $object->get_download_limit() ? (int) $object->get_download_limit() : -1,
			'download_expiry'       => '' !== $object->get_download_expiry() ? (int) $object->get_download_expiry() : -1,
			'tax_status'            => $object->get_tax_status(),
			'tax_class'             => $object->get_tax_class(),
			'manage_stock'          => $object->managing_stock(),
			'stock_quantity'        => $object->get_stock_quantity(),
			'in_stock'              => $object->is_in_stock(),
			'backorders'            => $object->get_backorders(),
			'backorders_allowed'    => $object->backorders_allowed(),
			'backordered'           => $object->is_on_backorder(),
			'weight'                => $object->get_weight(),
			'dimensions'            => array(
				'length' => $object->get_length(),
				'width'  => $object->get_width(),
				'height' => $object->get_height(),
			),
			'shipping_class'        => $object->get_shipping_class(),
			'shipping_class_id'     => $object->get_shipping_class_id(),
			'image'                 => current( $this->get_images( $object ) ),
			'attributes'            => $this->get_attributes( $object ),
			'menu_order'            => $object->get_menu_order(),
			'meta_data'             => $object->get_meta_data(),
		);

		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $object, $request ) );

		/**
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type,
		 * refers to object type being prepared for the response.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WC_Data          $object   Object data.
		 * @param WP_REST_Request  $request  Request object.
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}_object", $response, $object, $request );
	}

	/**
	 * Prepare objects query.
	 *
	 * @since  3.0.0
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		$args = parent::prepare_objects_query( $request );

		$args['post_parent'] = $request['product_id'];

		return $args;
	}

	/**
	 * Prepare a single variation for create or update.
	 *
	 * @param  WP_REST_Request $request Request object.
	 * @param  bool            $creating If is creating a new object.
	 * @return WP_Error|WC_Data
	 */
	protected function prepare_object_for_database( $request, $creating = false ) {
		if ( isset( $request['id'] ) ) {
			$variation = wc_get_product( absint( $request['id'] ) );
		} else {
			$variation = new WC_Product_Variation();
		}

		// Update parent ID just once.
		if ( 0 === $variation->get_parent_id() ) {
			$variation->set_parent_id( absint( $request['product_id'] ) );
		}

		// Status.
		if ( isset( $request['visible'] ) ) {
			$variation->set_status( false === $request['visible'] ? 'private' : 'publish' );
		}

		// SKU.
		if ( isset( $request['sku'] ) ) {
			$variation->set_sku( wc_clean( $request['sku'] ) );
		}

		// Thumbnail.
		if ( isset( $request['image'] ) ) {
			if ( is_array( $request['image'] ) && ! empty( $request['image'] ) ) {
				$image = $request['image'];
				if ( is_array( $image ) ) {
					$image['position'] = 0;
				}

				$variation = $this->set_product_images( $variation, array( $image ) );
			} else {
				$variation->set_image_id( '' );
			}
		}

		// Virtual variation.
		if ( isset( $request['virtual'] ) ) {
			$variation->set_virtual( $request['virtual'] );
		}

		// Downloadable variation.
		if ( isset( $request['downloadable'] ) ) {
			$variation->set_downloadable( $request['downloadable'] );
		}

		// Downloads.
		if ( $variation->get_downloadable() ) {
			// Downloadable files.
			if ( isset( $request['downloads'] ) && is_array( $request['downloads'] ) ) {
				$variation = $this->save_downloadable_files( $variation, $request['downloads'] );
			}

			// Download limit.
			if ( isset( $request['download_limit'] ) ) {
				$variation->set_download_limit( $request['download_limit'] );
			}

			// Download expiry.
			if ( isset( $request['download_expiry'] ) ) {
				$variation->set_download_expiry( $request['download_expiry'] );
			}
		}

		// Shipping data.
		$variation = $this->save_product_shipping_data( $variation, $request );

		// Stock handling.
		if ( isset( $request['manage_stock'] ) ) {
			if ( 'parent' === $request['manage_stock'] ) {
				$variation->set_manage_stock( false ); // This just indicates the variation does not manage stock, but the parent does.
			} else {
				$variation->set_manage_stock( wc_string_to_bool( $request['manage_stock'] ) );
			}
		}

		if ( isset( $request['in_stock'] ) ) {
			$variation->set_stock_status( true === $request['in_stock'] ? 'instock' : 'outofstock' );
		}

		if ( isset( $request['backorders'] ) ) {
			$variation->set_backorders( $request['backorders'] );
		}

		if ( $variation->get_manage_stock() ) {
			if ( isset( $request['stock_quantity'] ) ) {
				$variation->set_stock_quantity( $request['stock_quantity'] );
			} elseif ( isset( $request['inventory_delta'] ) ) {
				$stock_quantity  = wc_stock_amount( $variation->get_stock_quantity() );
				$stock_quantity += wc_stock_amount( $request['inventory_delta'] );
				$variation->set_stock_quantity( $stock_quantity );
			}
		} else {
			$variation->set_backorders( 'no' );
			$variation->set_stock_quantity( '' );
		}

		// Regular Price.
		if ( isset( $request['regular_price'] ) ) {
			$variation->set_regular_price( $request['regular_price'] );
		}

		// Sale Price.
		if ( isset( $request['sale_price'] ) ) {
			$variation->set_sale_price( $request['sale_price'] );
		}

		if ( isset( $request['date_on_sale_from'] ) ) {
			$variation->set_date_on_sale_from( $request['date_on_sale_from'] );
		}

		if ( isset( $request['date_on_sale_from_gmt'] ) ) {
			$variation->set_date_on_sale_from( $request['date_on_sale_from_gmt'] ? strtotime( $request['date_on_sale_from_gmt'] ) : null );
		}

		if ( isset( $request['date_on_sale_to'] ) ) {
			$variation->set_date_on_sale_to( $request['date_on_sale_to'] );
		}

		if ( isset( $request['date_on_sale_to_gmt'] ) ) {
			$variation->set_date_on_sale_to( $request['date_on_sale_to_gmt'] ? strtotime( $request['date_on_sale_to_gmt'] ) : null );
		}

		// Tax class.
		if ( isset( $request['tax_class'] ) ) {
			$variation->set_tax_class( $request['tax_class'] );
		}

		// Description.
		if ( isset( $request['description'] ) ) {
			$variation->set_description( wp_kses_post( $request['description'] ) );
		}

		// Update taxonomies.
		if ( isset( $request['attributes'] ) ) {
			$attributes        = array();
			$parent            = wc_get_product( $variation->get_parent_id() );
			$parent_attributes = $parent->get_attributes();

			foreach ( $request['attributes'] as $attribute ) {
				$attribute_id   = 0;
				$attribute_name = '';

				// Check ID for global attributes or name for product attributes.
				if ( ! empty( $attribute['id'] ) ) {
					$attribute_id       = absint( $attribute['id'] );
					$raw_attribute_name = wc_attribute_taxonomy_name_by_id( $attribute_id );
				} elseif ( ! empty( $attribute['name'] ) ) {
					$raw_attribute_name = sanitize_title( $attribute['name'] );
				}

				if ( ! $attribute_id && ! $raw_attribute_name ) {
					continue;
				}

				$attribute_name = sanitize_title( $raw_attribute_name );

				if ( ! isset( $parent_attributes[ $attribute_name ] ) || ! $parent_attributes[ $attribute_name ]->get_variation() ) {
					continue;
				}

				$attribute_key   = sanitize_title( $parent_attributes[ $attribute_name ]->get_name() );
				$attribute_value = isset( $attribute['option'] ) ? wc_clean( stripslashes( $attribute['option'] ) ) : '';

				if ( $parent_attributes[ $attribute_name ]->is_taxonomy() ) {
					// If dealing with a taxonomy, we need to get the slug from the name posted to the API.
					$term = get_term_by( 'name', $attribute_value, $raw_attribute_name ); // @codingStandardsIgnoreLine

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

		// Menu order.
		if ( $request['menu_order'] ) {
			$variation->set_menu_order( $request['menu_order'] );
		}

		// Meta data.
		if ( is_array( $request['meta_data'] ) ) {
			foreach ( $request['meta_data'] as $meta ) {
				$variation->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
			}
		}

		/**
		 * Filters an object before it is inserted via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`,
		 * refers to the object type slug.
		 *
		 * @param WC_Data         $variation Object object.
		 * @param WP_REST_Request $request   Request object.
		 * @param bool            $creating  If is creating a new object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}_object", $variation, $request, $creating );
	}

	/**
	 * Clear caches here so in sync with any new variations.
	 *
	 * @param WC_Data $object Object data.
	 */
	public function clear_transients( $object ) {
		wc_delete_product_transients( $object->get_parent_id() );
		wp_cache_delete( 'product-' . $object->get_parent_id(), 'products' );
	}

	/**
	 * Delete a variation.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {
		$force  = (bool) $request['force'];
		$object = $this->get_object( (int) $request['id'] );
		$result = false;

		if ( ! $object || 0 === $object->get_id() ) {
			return new WP_Error(
				"woocommerce_rest_{$this->post_type}_invalid_id", __( 'Invalid ID.', 'woocommerce' ), array(
					'status' => 404,
				)
			);
		}

		$supports_trash = EMPTY_TRASH_DAYS > 0 && is_callable( array( $object, 'get_status' ) );

		/**
		 * Filter whether an object is trashable.
		 *
		 * Return false to disable trash support for the object.
		 *
		 * @param boolean $supports_trash Whether the object type support trashing.
		 * @param WC_Data $object         The object being considered for trashing support.
		 */
		$supports_trash = apply_filters( "woocommerce_rest_{$this->post_type}_object_trashable", $supports_trash, $object );

		if ( ! wc_rest_check_post_permissions( $this->post_type, 'delete', $object->get_id() ) ) {
			return new WP_Error(
				/* translators: %s: post type */
				"woocommerce_rest_user_cannot_delete_{$this->post_type}", sprintf( __( 'Sorry, you are not allowed to delete %s.', 'woocommerce' ), $this->post_type ), array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_object_for_response( $object, $request );

		// If we're forcing, then delete permanently.
		if ( $force ) {
			$object->delete( true );
			$result = 0 === $object->get_id();
		} else {
			// If we don't support trashing for this type, error out.
			if ( ! $supports_trash ) {
				return new WP_Error(
					/* translators: %s: post type */
					'woocommerce_rest_trash_not_supported', sprintf( __( 'The %s does not support trashing.', 'woocommerce' ), $this->post_type ), array(
						'status' => 501,
					)
				);
			}

			// Otherwise, only trash if we haven't already.
			if ( is_callable( array( $object, 'get_status' ) ) ) {
				if ( 'trash' === $object->get_status() ) {
					return new WP_Error(
						/* translators: %s: post type */
						'woocommerce_rest_already_trashed', sprintf( __( 'The %s has already been deleted.', 'woocommerce' ), $this->post_type ), array(
							'status' => 410,
						)
					);
				}

				$object->delete();
				$result = 'trash' === $object->get_status();
			}
		}

		if ( ! $result ) {
			return new WP_Error(
				/* translators: %s: post type */
				'woocommerce_rest_cannot_delete', sprintf( __( 'The %s cannot be deleted.', 'woocommerce' ), $this->post_type ), array(
					'status' => 500,
				)
			);
		}

		// Delete parent product transients.
		if ( 0 !== $object->get_parent_id() ) {
			wc_delete_product_transients( $object->get_parent_id() );
		}

		/**
		 * Fires after a single object is deleted or trashed via the REST API.
		 *
		 * @param WC_Data          $object   The deleted or trashed object.
		 * @param WP_REST_Response $response The response data.
		 * @param WP_REST_Request  $request  The request sent to the API.
		 */
		do_action( "woocommerce_rest_delete_{$this->post_type}_object", $object, $response, $request );

		return $response;
	}

	/**
	 * Bulk create, update and delete items.
	 *
	 * @since  3.0.0
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array Of WP_Error or WP_REST_Response.
	 */
	public function batch_items( $request ) {
		$items       = array_filter( $request->get_params() );
		$params      = $request->get_url_params();
		$query       = $request->get_query_params();
		$product_id  = $params['product_id'];
		$body_params = array();

		foreach ( array( 'update', 'create', 'delete' ) as $batch_type ) {
			if ( ! empty( $items[ $batch_type ] ) ) {
				$injected_items = array();
				foreach ( $items[ $batch_type ] as $item ) {
					$injected_items[] = is_array( $item ) ? array_merge(
						array(
							'product_id' => $product_id,
						), $item
					) : $item;
				}
				$body_params[ $batch_type ] = $injected_items;
			}
		}

		$request = new WP_REST_Request( $request->get_method() );
		$request->set_body_params( $body_params );
		$request->set_query_params( $query );

		return parent::batch_items( $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param WC_Data         $object  Object data.
	 * @param WP_REST_Request $request Request object.
	 * @return array                   Links for the given post.
	 */
	protected function prepare_links( $object, $request ) {
		$product_id = (int) $request['product_id'];
		$base       = str_replace( '(?P<product_id>[\d]+)', $product_id, $this->rest_base );
		$links      = array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $base, $object->get_id() ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $base ) ),
			),
			'up'         => array(
				'href' => rest_url( sprintf( '/%s/products/%d', $this->namespace, $product_id ) ),
			),
		);
		return $links;
	}

	/**
	 * Get the Variation's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$weight_unit_label    = I18nUtil::get_weight_unit_label( get_option( 'woocommerce_weight_unit', 'kg' ) );
		$dimension_unit_label = I18nUtil::get_dimensions_unit_label( get_option( 'woocommerce_dimension_unit', 'cm' ) );
		$schema               = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id'                    => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created'          => array(
					'description' => __( "The date the variation was created, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_modified'         => array(
					'description' => __( "The date the variation was last modified, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'description'           => array(
					'description' => __( 'Variation description.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'permalink'             => array(
					'description' => __( 'Variation URL.', 'woocommerce' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'sku'                   => array(
					'description' => __( 'Unique identifier.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'price'                 => array(
					'description' => __( 'Current variation price.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'regular_price'         => array(
					'description' => __( 'Variation regular price.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'sale_price'            => array(
					'description' => __( 'Variation sale price.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'date_on_sale_from'     => array(
					'description' => __( "Start date of sale price, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_on_sale_from_gmt' => array(
					'description' => __( 'Start date of sale price, as GMT.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_on_sale_to'       => array(
					'description' => __( "End date of sale price, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_on_sale_to_gmt'   => array(
					'description' => __( 'End date of sale price, as GMT.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'on_sale'               => array(
					'description' => __( 'Shows if the variation is on sale.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'visible'               => array(
					'description' => __( "Define if the variation is visible on the product's page.", 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => true,
					'context'     => array( 'view', 'edit' ),
				),
				'purchasable'           => array(
					'description' => __( 'Shows if the variation can be bought.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'virtual'               => array(
					'description' => __( 'If the variation is virtual.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'downloadable'          => array(
					'description' => __( 'If the variation is downloadable.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'downloads'             => array(
					'description' => __( 'List of downloadable files.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'   => array(
								'description' => __( 'File ID.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'name' => array(
								'description' => __( 'File name.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'file' => array(
								'description' => __( 'File URL.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
				'download_limit'        => array(
					'description' => __( 'Number of times downloadable files can be downloaded after purchase.', 'woocommerce' ),
					'type'        => 'integer',
					'default'     => -1,
					'context'     => array( 'view', 'edit' ),
				),
				'download_expiry'       => array(
					'description' => __( 'Number of days until access to downloadable files expires.', 'woocommerce' ),
					'type'        => 'integer',
					'default'     => -1,
					'context'     => array( 'view', 'edit' ),
				),
				'tax_status'            => array(
					'description' => __( 'Tax status.', 'woocommerce' ),
					'type'        => 'string',
					'default'     => 'taxable',
					'enum'        => array( 'taxable', 'shipping', 'none' ),
					'context'     => array( 'view', 'edit' ),
				),
				'tax_class'             => array(
					'description' => __( 'Tax class.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'manage_stock'          => array(
					'description' => __( 'Stock management at variation level.', 'woocommerce' ),
					'type'        => 'mixed',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'stock_quantity'        => array(
					'description' => __( 'Stock quantity.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'in_stock'              => array(
					'description' => __( 'Controls whether or not the variation is listed as "in stock" or "out of stock" on the frontend.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => true,
					'context'     => array( 'view', 'edit' ),
				),
				'backorders'            => array(
					'description' => __( 'If managing stock, this controls if backorders are allowed.', 'woocommerce' ),
					'type'        => 'string',
					'default'     => 'no',
					'enum'        => array( 'no', 'notify', 'yes' ),
					'context'     => array( 'view', 'edit' ),
				),
				'backorders_allowed'    => array(
					'description' => __( 'Shows if backorders are allowed.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'backordered'           => array(
					'description' => __( 'Shows if the variation is on backordered.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'weight'                => array(
					/* translators: %s: weight unit */
					'description' => sprintf( __( 'Variation weight (%s).', 'woocommerce' ), $weight_unit_label ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'dimensions'            => array(
					'description' => __( 'Variation dimensions.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'properties'  => array(
						'length' => array(
							/* translators: %s: dimension unit */
							'description' => sprintf( __( 'Variation length (%s).', 'woocommerce' ), $dimension_unit_label ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'width'  => array(
							/* translators: %s: dimension unit */
							'description' => sprintf( __( 'Variation width (%s).', 'woocommerce' ), $dimension_unit_label ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'height' => array(
							/* translators: %s: dimension unit */
							'description' => sprintf( __( 'Variation height (%s).', 'woocommerce' ), $dimension_unit_label ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
				'shipping_class'        => array(
					'description' => __( 'Shipping class slug.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'shipping_class_id'     => array(
					'description' => __( 'Shipping class ID.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'image'                 => array(
					'description' => __( 'Variation image data.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'properties'  => array(
						'id'                => array(
							'description' => __( 'Image ID.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
						),
						'date_created'      => array(
							'description' => __( "The date the image was created, in the site's timezone.", 'woocommerce' ),
							'type'        => 'date-time',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'date_created_gmt'  => array(
							'description' => __( 'The date the image was created, as GMT.', 'woocommerce' ),
							'type'        => 'date-time',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'date_modified'     => array(
							'description' => __( "The date the image was last modified, in the site's timezone.", 'woocommerce' ),
							'type'        => 'date-time',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'date_modified_gmt' => array(
							'description' => __( 'The date the image was last modified, as GMT.', 'woocommerce' ),
							'type'        => 'date-time',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true,
						),
						'src'               => array(
							'description' => __( 'Image URL.', 'woocommerce' ),
							'type'        => 'string',
							'format'      => 'uri',
							'context'     => array( 'view', 'edit' ),
						),
						'name'              => array(
							'description' => __( 'Image name.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'alt'               => array(
							'description' => __( 'Image alternative text.', 'woocommerce' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'position'          => array(
							'description' => __( 'Image position. 0 means that the image is featured.', 'woocommerce' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
				'attributes'            => array(
					'description' => __( 'List of attributes.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'     => array(
								'description' => __( 'Attribute ID.', 'woocommerce' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
							'name'   => array(
								'description' => __( 'Attribute name.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'option' => array(
								'description' => __( 'Selected attribute term name.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
				'menu_order'            => array(
					'description' => __( 'Menu order, used to custom sort products.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'meta_data'             => array(
					'description' => __( 'Meta data.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'    => array(
								'description' => __( 'Meta ID.', 'woocommerce' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'key'   => array(
								'description' => __( 'Meta key.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'value' => array(
								'description' => __( 'Meta value.', 'woocommerce' ),
								'type'        => 'mixed',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}

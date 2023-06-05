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

use Automattic\Jetpack\Constants;

/**
 * REST API variations controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Product_Variations_V2_Controller
 */
class WC_REST_Product_Variations_Controller extends WC_REST_Product_Variations_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Register the routes for products.
	 */
	public function register_routes() {
		parent::register_routes();

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/generate',
			array(
				'args'   => array(
					'product_id' => array(
						'description' => __( 'Unique identifier for the variable product.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'generate' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Prepare a single variation output for response.
	 *
	 * @param  WC_Data         $object  Object data.
	 * @param  WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function prepare_object_for_response( $object, $request ) {
		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = array(
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
			'status'                => $object->get_status(),
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
			'stock_status'          => $object->get_stock_status(),
			'backorders'            => $object->get_backorders(),
			'backorders_allowed'    => $object->backorders_allowed(),
			'backordered'           => $object->is_on_backorder(),
			'low_stock_amount'      => '' === $object->get_low_stock_amount() ? null : $object->get_low_stock_amount(),
			'weight'                => $object->get_weight(),
			'dimensions'            => array(
				'length' => $object->get_length(),
				'width'  => $object->get_width(),
				'height' => $object->get_height(),
			),
			'shipping_class'        => $object->get_shipping_class(),
			'shipping_class_id'     => $object->get_shipping_class_id(),
			'image'                 => $this->get_image( $object, $context ),
			'attributes'            => $this->get_attributes( $object ),
			'menu_order'            => $object->get_menu_order(),
			'meta_data'             => $object->get_meta_data(),
		);

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

		$variation->set_parent_id( absint( $request['product_id'] ) );

		// Status.
		if ( isset( $request['status'] ) ) {
			$variation->set_status( get_post_status_object( $request['status'] ) ? $request['status'] : 'draft' );
		}

		// SKU.
		if ( isset( $request['sku'] ) ) {
			$variation->set_sku( wc_clean( $request['sku'] ) );
		}

		// Thumbnail.
		if ( isset( $request['image'] ) ) {
			if ( is_array( $request['image'] ) ) {
				$variation = $this->set_variation_image( $variation, $request['image'] );
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
			$variation->set_manage_stock( $request['manage_stock'] );
		}

		if ( isset( $request['stock_status'] ) ) {
			$variation->set_stock_status( $request['stock_status'] );
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
			// isset() returns false for value null, thus we need to check whether the value has been sent by the request.
			if ( array_key_exists( 'low_stock_amount', $request->get_params() ) ) {
				if ( null === $request['low_stock_amount'] ) {
					$variation->set_low_stock_amount( '' );
				} else {
					$variation->set_low_stock_amount( wc_stock_amount( $request['low_stock_amount'] ) );
				}
			}
		} else {
			$variation->set_backorders( 'no' );
			$variation->set_stock_quantity( '' );
			$variation->set_low_stock_amount( '' );
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
			$attributes = array();
			$parent     = wc_get_product( $variation->get_parent_id() );

			if ( ! $parent ) {
				return new WP_Error(
					// Translators: %d parent ID.
					"woocommerce_rest_{$this->post_type}_invalid_parent",
					__( 'Cannot set attributes due to invalid parent product.', 'woocommerce' ),
					array( 'status' => 404 )
				);
			}

			$parent_attributes = $parent->get_attributes();

			foreach ( $request['attributes'] as $attribute ) {
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
	 * Get the image for a product variation.
	 *
	 * @param WC_Product_Variation $variation Variation data.
	 * @param string               $context   Context of the request: 'view' or 'edit'.
	 * @return array
	 */
	protected function get_image( $variation, $context = 'view' ) {
		if ( ! $variation->get_image_id( $context ) ) {
			return;
		}

		$attachment_id   = $variation->get_image_id();
		$attachment_post = get_post( $attachment_id );
		if ( is_null( $attachment_post ) ) {
			return;
		}

		$attachment = wp_get_attachment_image_src( $attachment_id, 'full' );
		if ( ! is_array( $attachment ) ) {
			return;
		}

		if ( ! isset( $image ) ) {
			return array(
				'id'                => (int) $attachment_id,
				'date_created'      => wc_rest_prepare_date_response( $attachment_post->post_date, false ),
				'date_created_gmt'  => wc_rest_prepare_date_response( strtotime( $attachment_post->post_date_gmt ) ),
				'date_modified'     => wc_rest_prepare_date_response( $attachment_post->post_modified, false ),
				'date_modified_gmt' => wc_rest_prepare_date_response( strtotime( $attachment_post->post_modified_gmt ) ),
				'src'               => current( $attachment ),
				'name'              => get_the_title( $attachment_id ),
				'alt'               => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
			);
		}
	}

	/**
	 * Set variation image.
	 *
	 * @throws WC_REST_Exception REST API exceptions.
	 * @param  WC_Product_Variation $variation Variation instance.
	 * @param  array                $image    Image data.
	 * @return WC_Product_Variation
	 */
	protected function set_variation_image( $variation, $image ) {
		$attachment_id = isset( $image['id'] ) ? absint( $image['id'] ) : 0;

		if ( 0 === $attachment_id ) {
			if ( isset( $image['src'] ) ) {
				$upload = wc_rest_upload_image_from_url( esc_url_raw( $image['src'] ) );

				if ( is_wp_error( $upload ) ) {
					if ( ! apply_filters( 'woocommerce_rest_suppress_image_upload_error', false, $upload, $variation->get_id(), array( $image ) ) ) {
						throw new WC_REST_Exception( 'woocommerce_variation_image_upload_error', $upload->get_error_message(), 400 );
					}
				}

				$attachment_id = wc_rest_set_uploaded_image_as_attachment( $upload, $variation->get_id() );
			} else {
				$variation->set_image_id( '' );
				return $variation;
			}
		}

		if ( ! wp_attachment_is_image( $attachment_id ) ) {
			/* translators: %s: attachment ID */
			throw new WC_REST_Exception( 'woocommerce_variation_invalid_image_id', sprintf( __( '#%s is an invalid image ID.', 'woocommerce' ), $attachment_id ), 400 );
		}

		$variation->set_image_id( $attachment_id );

		// Set the image alt if present.
		if ( ! empty( $image['alt'] ) ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', wc_clean( $image['alt'] ) );
		}

		// Set the image name if present.
		if ( ! empty( $image['name'] ) ) {
			wp_update_post(
				array(
					'ID'         => $attachment_id,
					'post_title' => $image['name'],
				)
			);
		}

		return $variation;
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
					'description' => __( "End date of sale price, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'on_sale'               => array(
					'description' => __( 'Shows if the variation is on sale.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'status'                => array(
					'description' => __( 'Variation status.', 'woocommerce' ),
					'type'        => 'string',
					'default'     => 'publish',
					'enum'        => array_keys( get_post_statuses() ),
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
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'stock_quantity'        => array(
					'description' => __( 'Stock quantity.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'stock_status'          => array(
					'description' => __( 'Controls the stock status of the product.', 'woocommerce' ),
					'type'        => 'string',
					'default'     => 'instock',
					'enum'        => array_keys( wc_get_product_stock_status_options() ),
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
				'low_stock_amount'       => array(
					'description' => __( 'Low Stock amount for the variation.', 'woocommerce' ),
					'type'        => array( 'integer', 'null' ),
					'context'     => array( 'view', 'edit' ),
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

	/**
	 * Prepare objects query.
	 *
	 * @since  3.0.0
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		$args = WC_REST_CRUD_Controller::prepare_objects_query( $request );

		// Set post_status.
		$args['post_status'] = $request['status'];

		// Filter by local attributes.
		if ( ! empty( $request['local_attributes'] ) && is_array( $request['local_attributes'] ) ) {
			foreach ( $request['local_attributes'] as $attribute ) {
				if ( ! isset( $attribute['attribute'] ) || ! isset( $attribute['term'] ) ) {
					continue;
				}
				$args['meta_query'] = $this->add_meta_query( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					$args,
					array(
						'key'   => 'attribute_' . $attribute['attribute'],
						'value' => $attribute['term'],
					)
				);
			}
		}

		// Filter by sku.
		if ( ! empty( $request['sku'] ) ) {
			$skus = explode( ',', $request['sku'] );
			// Include the current string as a SKU too.
			if ( 1 < count( $skus ) ) {
				$skus[] = $request['sku'];
			}

			$args['meta_query'] = $this->add_meta_query( // WPCS: slow query ok.
				$args,
				array(
					'key'     => '_sku',
					'value'   => $skus,
					'compare' => 'IN',
				)
			);
		}

		// Filter by tax class.
		if ( ! empty( $request['tax_class'] ) ) {
			$args['meta_query'] = $this->add_meta_query( // WPCS: slow query ok.
				$args,
				array(
					'key'   => '_tax_class',
					'value' => 'standard' !== $request['tax_class'] ? $request['tax_class'] : '',
				)
			);
		}

		// Price filter.
		if ( ! empty( $request['min_price'] ) || ! empty( $request['max_price'] ) ) {
			$args['meta_query'] = $this->add_meta_query( $args, wc_get_min_max_price_meta_query( $request ) );  // WPCS: slow query ok.
		}

		// Filter product based on stock_status.
		if ( ! empty( $request['stock_status'] ) ) {
			$args['meta_query'] = $this->add_meta_query( // WPCS: slow query ok.
				$args,
				array(
					'key'   => '_stock_status',
					'value' => $request['stock_status'],
				)
			);
		}

		// Filter by on sale products.
		if ( is_bool( $request['on_sale'] ) ) {
			$on_sale_key = $request['on_sale'] ? 'post__in' : 'post__not_in';
			$on_sale_ids = wc_get_product_ids_on_sale();

			// Use 0 when there's no on sale products to avoid return all products.
			$on_sale_ids = empty( $on_sale_ids ) ? array( 0 ) : $on_sale_ids;

			$args[ $on_sale_key ] += $on_sale_ids;
		}

		// Force the post_type argument, since it's not a user input variable.
		if ( ! empty( $request['sku'] ) ) {
			$args['post_type'] = array( 'product', 'product_variation' );
		} else {
			$args['post_type'] = $this->post_type;
		}

		$args['post_parent'] = $request['product_id'];

		return $args;
	}

	/**
	 * Get the query params for collections of attachments.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		unset(
			$params['in_stock'],
			$params['type'],
			$params['featured'],
			$params['category'],
			$params['tag'],
			$params['shipping_class'],
			$params['attribute'],
			$params['attribute_term']
		);

		$params['stock_status'] = array(
			'description'       => __( 'Limit result set to products with specified stock status.', 'woocommerce' ),
			'type'              => 'string',
			'enum'              => array_keys( wc_get_product_stock_status_options() ),
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}

	/**
	 * Generate all variations for a given product.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function generate( $request ) {
		$product_id = (int) $request['product_id'];

		if ( 'product' !== get_post_type( $product_id ) ) {
			return new WP_Error( 'woocommerce_rest_product_invalid_id', __( 'Invalid product ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		wc_maybe_define_constant( 'WC_MAX_LINKED_VARIATIONS', 50 );
		wc_set_time_limit( 0 );

		$response          = array();
		$product           = wc_get_product( $product_id );
		$data_store        = $product->get_data_store();
		$response['count'] = $data_store->create_all_product_variations( $product, Constants::get_constant( 'WC_MAX_LINKED_VARIATIONS' ) );

		$data_store->sort_all_product_variations( $product->get_id() );

		return rest_ensure_response( $response );
	}
}

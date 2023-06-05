<?php
/**
 * REST API Products controller
 *
 * Handles requests to the /products endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   2.6.0
 */

use Automattic\WooCommerce\Utilities\I18nUtil;

defined( 'ABSPATH' ) || exit;

/**
 * REST API Products controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_CRUD_Controller
 */
class WC_REST_Products_V2_Controller extends WC_REST_CRUD_Controller {

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
	protected $rest_base = 'products';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'product';

	/**
	 * If object is hierarchical.
	 *
	 * @var bool
	 */
	protected $hierarchical = true;

	/**
	 * Initialize product actions.
	 */
	public function __construct() {
		add_action( "woocommerce_rest_insert_{$this->post_type}_object", array( $this, 'clear_transients' ) );
	}

	/**
	 * Register the routes for products.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
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
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
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
							'description' => __( 'Whether to bypass trash and force deletion.', 'woocommerce' ),
							'type'        => 'boolean',
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/batch',
			array(
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
	 * @param int $id Object ID.
	 *
	 * @since  3.0.0
	 * @return WC_Data
	 */
	protected function get_object( $id ) {
		return wc_get_product( $id );
	}

	/**
	 * Prepare a single product output for response.
	 *
	 * @param WC_Data         $object  Object data.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  3.0.0
	 * @return WP_REST_Response
	 */
	public function prepare_object_for_response( $object, $request ) {
		$context       = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$this->request = $request;
		$data          = $this->get_product_data( $object, $context, $request );

		// Add variations to variable products.
		if ( $object->is_type( 'variable' ) && $object->has_child() ) {
			$data['variations'] = $object->get_children();
		}

		// Add grouped products data.
		if ( $object->is_type( 'grouped' ) && $object->has_child() ) {
			$data['grouped_products'] = $object->get_children();
		}

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
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @since  3.0.0
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		$args = parent::prepare_objects_query( $request );

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
			$args['tax_query'] = $tax_query; // WPCS: slow query ok.
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

		// Filter product in stock or out of stock.
		if ( is_bool( $request['in_stock'] ) ) {
			$args['meta_query'] = $this->add_meta_query( // WPCS: slow query ok.
				$args,
				array(
					'key'   => '_stock_status',
					'value' => true === $request['in_stock'] ? 'instock' : 'outofstock',
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

		return $args;
	}

	/**
	 * Get the downloads for a product or product variation.
	 *
	 * @param WC_Product|WC_Product_Variation $product Product instance.
	 *
	 * @return array
	 */
	protected function get_downloads( $product ) {
		$downloads = array();

		if ( $product->is_downloadable() ) {
			foreach ( $product->get_downloads() as $file_id => $file ) {
				$downloads[] = array(
					'id'   => $file_id, // MD5 hash.
					'name' => $file['name'],
					'file' => $file['file'],
				);
			}
		}

		return $downloads;
	}

	/**
	 * Get taxonomy terms.
	 *
	 * @param WC_Product $product  Product instance.
	 * @param string     $taxonomy Taxonomy slug.
	 *
	 * @return array
	 */
	protected function get_taxonomy_terms( $product, $taxonomy = 'cat' ) {
		$terms = array();

		foreach ( wc_get_object_terms( $product->get_id(), 'product_' . $taxonomy ) as $term ) {
			$terms[] = array(
				'id'   => $term->term_id,
				'name' => $term->name,
				'slug' => $term->slug,
			);
		}

		return $terms;
	}

	/**
	 * Get the images for a product or product variation.
	 *
	 * @param WC_Product|WC_Product_Variation $product Product instance.
	 *
	 * @return array
	 */
	protected function get_images( $product ) {
		$images         = array();
		$attachment_ids = array();

		// Add featured image.
		if ( $product->get_image_id() ) {
			$attachment_ids[] = $product->get_image_id();
		}

		// Add gallery images.
		$attachment_ids = array_merge( $attachment_ids, $product->get_gallery_image_ids() );

		// Build image data.
		foreach ( $attachment_ids as $position => $attachment_id ) {
			$attachment_post = get_post( $attachment_id );
			if ( is_null( $attachment_post ) ) {
				continue;
			}

			$attachment = wp_get_attachment_image_src( $attachment_id, 'full' );
			if ( ! is_array( $attachment ) ) {
				continue;
			}

			$images[] = array(
				'id'                => (int) $attachment_id,
				'date_created'      => wc_rest_prepare_date_response( $attachment_post->post_date, false ),
				'date_created_gmt'  => wc_rest_prepare_date_response( strtotime( $attachment_post->post_date_gmt ) ),
				'date_modified'     => wc_rest_prepare_date_response( $attachment_post->post_modified, false ),
				'date_modified_gmt' => wc_rest_prepare_date_response( strtotime( $attachment_post->post_modified_gmt ) ),
				'src'               => current( $attachment ),
				'name'              => get_the_title( $attachment_id ),
				'alt'               => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
				'position'          => (int) $position,
			);
		}

		// Set a placeholder image if the product has no images set.
		if ( empty( $images ) ) {
			$images[] = array(
				'id'                => 0,
				'date_created'      => wc_rest_prepare_date_response( current_time( 'mysql' ), false ), // Default to now.
				'date_created_gmt'  => wc_rest_prepare_date_response( time() ), // Default to now.
				'date_modified'     => wc_rest_prepare_date_response( current_time( 'mysql' ), false ),
				'date_modified_gmt' => wc_rest_prepare_date_response( time() ),
				'src'               => wc_placeholder_img_src(),
				'name'              => __( 'Placeholder', 'woocommerce' ),
				'alt'               => __( 'Placeholder', 'woocommerce' ),
				'position'          => 0,
			);
		}

		return $images;
	}

	/**
	 * Get attribute taxonomy label.
	 *
	 * @param string $name Taxonomy name.
	 *
	 * @deprecated 3.0.0
	 * @return     string
	 */
	protected function get_attribute_taxonomy_label( $name ) {
		$tax    = get_taxonomy( $name );
		$labels = get_taxonomy_labels( $tax );

		return $labels->singular_name;
	}

	/**
	 * Get product attribute taxonomy name.
	 *
	 * @param string     $slug    Taxonomy name.
	 * @param WC_Product $product Product data.
	 *
	 * @since  3.0.0
	 * @return string
	 */
	protected function get_attribute_taxonomy_name( $slug, $product ) {
		// Format slug so it matches attributes of the product.
		$slug       = wc_attribute_taxonomy_slug( $slug );
		$attributes = $product->get_attributes();
		$attribute  = false;

		// pa_ attributes.
		if ( isset( $attributes[ wc_attribute_taxonomy_name( $slug ) ] ) ) {
			$attribute = $attributes[ wc_attribute_taxonomy_name( $slug ) ];
		} elseif ( isset( $attributes[ $slug ] ) ) {
			$attribute = $attributes[ $slug ];
		}

		if ( ! $attribute ) {
			return $slug;
		}

		// Taxonomy attribute name.
		if ( $attribute->is_taxonomy() ) {
			$taxonomy = $attribute->get_taxonomy_object();
			return $taxonomy->attribute_label;
		}

		// Custom product attribute name.
		return $attribute->get_name();
	}

	/**
	 * Get default attributes.
	 *
	 * @param WC_Product $product Product instance.
	 *
	 * @return array
	 */
	protected function get_default_attributes( $product ) {
		$default = array();

		if ( $product->is_type( 'variable' ) ) {
			foreach ( array_filter( (array) $product->get_default_attributes(), 'strlen' ) as $key => $value ) {
				if ( 0 === strpos( $key, 'pa_' ) ) {
					$default[] = array(
						'id'     => wc_attribute_taxonomy_id_by_name( $key ),
						'name'   => $this->get_attribute_taxonomy_name( $key, $product ),
						'option' => $value,
					);
				} else {
					$default[] = array(
						'id'     => 0,
						'name'   => $this->get_attribute_taxonomy_name( $key, $product ),
						'option' => $value,
					);
				}
			}
		}

		return $default;
	}

	/**
	 * Get attribute options.
	 *
	 * @param int   $product_id Product ID.
	 * @param array $attribute  Attribute data.
	 *
	 * @return array
	 */
	protected function get_attribute_options( $product_id, $attribute ) {
		if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {
			return wc_get_product_terms(
				$product_id,
				$attribute['name'],
				array(
					'fields' => 'names',
				)
			);
		} elseif ( isset( $attribute['value'] ) ) {
			return array_map( 'trim', explode( '|', $attribute['value'] ) );
		}

		return array();
	}

	/**
	 * Get the attributes for a product or product variation.
	 *
	 * @param WC_Product|WC_Product_Variation $product Product instance.
	 *
	 * @return array
	 */
	protected function get_attributes( $product ) {
		$attributes = array();

		if ( $product->is_type( 'variation' ) ) {
			$_product = wc_get_product( $product->get_parent_id() );
			foreach ( $product->get_variation_attributes() as $attribute_name => $attribute ) {
				$name = str_replace( 'attribute_', '', $attribute_name );

				if ( empty( $attribute ) && '0' !== $attribute ) {
					continue;
				}

				// Taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`.
				if ( 0 === strpos( $attribute_name, 'attribute_pa_' ) ) {
					$option_term  = get_term_by( 'slug', $attribute, $name );
					$attributes[] = array(
						'id'     => wc_attribute_taxonomy_id_by_name( $name ),
						'name'   => $this->get_attribute_taxonomy_name( $name, $_product ),
						'option' => $option_term && ! is_wp_error( $option_term ) ? $option_term->name : $attribute,
					);
				} else {
					$attributes[] = array(
						'id'     => 0,
						'name'   => $this->get_attribute_taxonomy_name( $name, $_product ),
						'option' => $attribute,
					);
				}
			}
		} else {
			foreach ( $product->get_attributes() as $attribute ) {
				$attributes[] = array(
					'id'        => $attribute['is_taxonomy'] ? wc_attribute_taxonomy_id_by_name( $attribute['name'] ) : 0,
					'name'      => $this->get_attribute_taxonomy_name( $attribute['name'], $product ),
					'position'  => (int) $attribute['position'],
					'visible'   => (bool) $attribute['is_visible'],
					'variation' => (bool) $attribute['is_variation'],
					'options'   => $this->get_attribute_options( $product->get_id(), $attribute ),
				);
			}
		}

		return $attributes;
	}

	/**
	 * Fetch price HTML.
	 *
	 * @param WC_Product $product Product object.
	 * @param string     $context Context of request, can be `view` or `edit`.
	 *
	 * @return string
	 */
	protected function api_get_price_html( $product, $context ) {
		return $product->get_price_html();
	}

	/**
	 * Fetch related IDs.
	 *
	 * @param WC_Product $product Product object.
	 * @param string     $context Context of request, can be `view` or `edit`.
	 *
	 * @return array
	 */
	protected function api_get_related_ids( $product, $context ) {
		return array_map( 'absint', array_values( wc_get_related_products( $product->get_id() ) ) );
	}

	/**
	 * Fetch meta data.
	 *
	 * @param WC_Product $product Product object.
	 * @param string     $context Context of request, can be `view` or `edit`.
	 *
	 * @return array
	 */
	protected function api_get_meta_data( $product, $context ) {
		$meta_data = $product->get_meta_data();

		if ( ! isset( $this->request ) || ! $this->request instanceof WP_REST_Request ) {
			return $meta_data;
		}

		return $this->get_meta_data_for_response( $this->request, $meta_data );
	}

	/**
	 * Get product data.
	 *
	 * @param WC_Product $product Product instance.
	 * @param string     $context Request context. Options: 'view' and 'edit'.
	 *
	 * @return array
	 */
	protected function get_product_data( $product, $context = 'view' ) {
		/*
		 * @param WP_REST_Request $request Current request object. For backward compatibility, we pass this argument silently.
		 *
		 *  TODO: Refactor to fix this behavior when DI gets included to make it obvious and clean.
		*/
		$request = func_num_args() >= 3 ? func_get_arg( 2 ) : new WP_REST_Request( '', '', array( 'context' => $context ) );
		$fields  = $this->get_fields_for_response( $request );

		$base_data = array();
		foreach ( $fields as $field ) {
			switch ( $field ) {
				case 'id':
					$base_data['id'] = $product->get_id();
					break;
				case 'name':
					$base_data['name'] = $product->get_name( $context );
					break;
				case 'slug':
					$base_data['slug'] = $product->get_slug( $context );
					break;
				case 'permalink':
					$base_data['permalink'] = $product->get_permalink();
					break;
				case 'date_created':
					$base_data['date_created'] = wc_rest_prepare_date_response( $product->get_date_created( $context ), false );
					break;
				case 'date_created_gmt':
					$base_data['date_created_gmt'] = wc_rest_prepare_date_response( $product->get_date_created( $context ) );
					break;
				case 'date_modified':
					$base_data['date_modified'] = wc_rest_prepare_date_response( $product->get_date_modified( $context ), false );
					break;
				case 'date_modified_gmt':
					$base_data['date_modified_gmt'] = wc_rest_prepare_date_response( $product->get_date_modified( $context ) );
					break;
				case 'type':
					$base_data['type'] = $product->get_type();
					break;
				case 'status':
					$base_data['status'] = $product->get_status( $context );
					break;
				case 'featured':
					$base_data['featured'] = $product->is_featured();
					break;
				case 'catalog_visibility':
					$base_data['catalog_visibility'] = $product->get_catalog_visibility( $context );
					break;
				case 'description':
					$base_data['description'] = 'view' === $context ? wpautop( do_shortcode( $product->get_description() ) ) : $product->get_description( $context );
					break;
				case 'short_description':
					$base_data['short_description'] = 'view' === $context ? apply_filters( 'woocommerce_short_description', $product->get_short_description() ) : $product->get_short_description( $context );
					break;
				case 'sku':
					$base_data['sku'] = $product->get_sku( $context );
					break;
				case 'price':
					$base_data['price'] = $product->get_price( $context );
					break;
				case 'regular_price':
					$base_data['regular_price'] = $product->get_regular_price( $context );
					break;
				case 'sale_price':
					$base_data['sale_price'] = $product->get_sale_price( $context ) ? $product->get_sale_price( $context ) : '';
					break;
				case 'date_on_sale_from':
					$base_data['date_on_sale_from'] = wc_rest_prepare_date_response( $product->get_date_on_sale_from( $context ), false );
					break;
				case 'date_on_sale_from_gmt':
					$base_data['date_on_sale_from_gmt'] = wc_rest_prepare_date_response( $product->get_date_on_sale_from( $context ) );
					break;
				case 'date_on_sale_to':
					$base_data['date_on_sale_to'] = wc_rest_prepare_date_response( $product->get_date_on_sale_to( $context ), false );
					break;
				case 'date_on_sale_to_gmt':
					$base_data['date_on_sale_to_gmt'] = wc_rest_prepare_date_response( $product->get_date_on_sale_to( $context ) );
					break;
				case 'on_sale':
					$base_data['on_sale'] = $product->is_on_sale( $context );
					break;
				case 'purchasable':
					$base_data['purchasable'] = $product->is_purchasable();
					break;
				case 'total_sales':
					$base_data['total_sales'] = $product->get_total_sales( $context );
					break;
				case 'virtual':
					$base_data['virtual'] = $product->is_virtual();
					break;
				case 'downloadable':
					$base_data['downloadable'] = $product->is_downloadable();
					break;
				case 'downloads':
					$base_data['downloads'] = $this->get_downloads( $product );
					break;
				case 'download_limit':
					$base_data['download_limit'] = $product->get_download_limit( $context );
					break;
				case 'download_expiry':
					$base_data['download_expiry'] = $product->get_download_expiry( $context );
					break;
				case 'external_url':
					$base_data['external_url'] = $product->is_type( 'external' ) ? $product->get_product_url( $context ) : '';
					break;
				case 'button_text':
					$base_data['button_text'] = $product->is_type( 'external' ) ? $product->get_button_text( $context ) : '';
					break;
				case 'tax_status':
					$base_data['tax_status'] = $product->get_tax_status( $context );
					break;
				case 'tax_class':
					$base_data['tax_class'] = $product->get_tax_class( $context );
					break;
				case 'manage_stock':
					$base_data['manage_stock'] = $product->managing_stock();
					break;
				case 'stock_quantity':
					$base_data['stock_quantity'] = $product->get_stock_quantity( $context );
					break;
				case 'in_stock':
					$base_data['in_stock'] = $product->is_in_stock();
					break;
				case 'backorders':
					$base_data['backorders'] = $product->get_backorders( $context );
					break;
				case 'backorders_allowed':
					$base_data['backorders_allowed'] = $product->backorders_allowed();
					break;
				case 'backordered':
					$base_data['backordered'] = $product->is_on_backorder();
					break;
				case 'low_stock_amount':
					$base_data['low_stock_amount'] = '' === $product->get_low_stock_amount() ? null : $product->get_low_stock_amount();
					break;
				case 'sold_individually':
					$base_data['sold_individually'] = $product->is_sold_individually();
					break;
				case 'weight':
					$base_data['weight'] = $product->get_weight( $context );
					break;
				case 'dimensions':
					$base_data['dimensions'] = array(
						'length' => $product->get_length( $context ),
						'width'  => $product->get_width( $context ),
						'height' => $product->get_height( $context ),
					);
					break;
				case 'shipping_required':
					$base_data['shipping_required'] = $product->needs_shipping();
					break;
				case 'shipping_taxable':
					$base_data['shipping_taxable'] = $product->is_shipping_taxable();
					break;
				case 'shipping_class':
					$base_data['shipping_class'] = $product->get_shipping_class();
					break;
				case 'shipping_class_id':
					$base_data['shipping_class_id'] = $product->get_shipping_class_id( $context );
					break;
				case 'reviews_allowed':
					$base_data['reviews_allowed'] = $product->get_reviews_allowed( $context );
					break;
				case 'average_rating':
					$base_data['average_rating'] = 'view' === $context ? wc_format_decimal( $product->get_average_rating(), 2 ) : $product->get_average_rating( $context );
					break;
				case 'rating_count':
					$base_data['rating_count'] = $product->get_rating_count();
					break;
				case 'upsell_ids':
					$base_data['upsell_ids'] = array_map( 'absint', $product->get_upsell_ids( $context ) );
					break;
				case 'cross_sell_ids':
					$base_data['cross_sell_ids'] = array_map( 'absint', $product->get_cross_sell_ids( $context ) );
					break;
				case 'parent_id':
					$base_data['parent_id'] = $product->get_parent_id( $context );
					break;
				case 'purchase_note':
					$base_data['purchase_note'] = 'view' === $context ? wpautop( do_shortcode( wp_kses_post( $product->get_purchase_note() ) ) ) : $product->get_purchase_note( $context );
					break;
				case 'categories':
					$base_data['categories'] = $this->get_taxonomy_terms( $product );
					break;
				case 'tags':
					$base_data['tags'] = $this->get_taxonomy_terms( $product, 'tag' );
					break;
				case 'images':
					$base_data['images'] = $this->get_images( $product );
					break;
				case 'attributes':
					$base_data['attributes'] = $this->get_attributes( $product );
					break;
				case 'default_attributes':
					$base_data['default_attributes'] = $this->get_default_attributes( $product );
					break;
				case 'variations':
					$base_data['variations'] = array();
					break;
				case 'grouped_products':
					$base_data['grouped_products'] = array();
					break;
				case 'menu_order':
					$base_data['menu_order'] = $product->get_menu_order( $context );
					break;
			}
		}

		$data = array_merge(
			$base_data,
			$this->fetch_fields_using_getters( $product, $context, $fields )
		);

		return $data;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param WC_Data         $object  Object data.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return array Links for the given post.
	 */
	protected function prepare_links( $object, $request ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $object->get_id() ) ),  // @codingStandardsIgnoreLine.
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),  // @codingStandardsIgnoreLine.
			),
		);

		if ( $object->get_parent_id() ) {
			$links['up'] = array(
				'href' => rest_url( sprintf( '/%s/products/%d', $this->namespace, $object->get_parent_id() ) ),  // @codingStandardsIgnoreLine.
			);
		}

		return $links;
	}

	/**
	 * Prepare a single product for create or update.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @param bool            $creating If is creating a new object.
	 *
	 * @return WP_Error|WC_Data
	 */
	protected function prepare_object_for_database( $request, $creating = false ) {
		$id = isset( $request['id'] ) ? absint( $request['id'] ) : 0;

		// Type is the most important part here because we need to be using the correct class and methods.
		if ( isset( $request['type'] ) ) {
			$classname = WC_Product_Factory::get_classname_from_product_type( $request['type'] );

			if ( ! class_exists( $classname ) ) {
				$classname = 'WC_Product_Simple';
			}

			$product = new $classname( $id );
		} elseif ( isset( $request['id'] ) ) {
			$product = wc_get_product( $id );
		} else {
			$product = new WC_Product_Simple();
		}

		if ( 'variation' === $product->get_type() ) {
			return new WP_Error(
				"woocommerce_rest_invalid_{$this->post_type}_id",
				__( 'To manipulate product variations you should use the /products/&lt;product_id&gt;/variations/&lt;id&gt; endpoint.', 'woocommerce' ),
				array(
					'status' => 404,
				)
			);
		}

		// Post title.
		if ( isset( $request['name'] ) ) {
			$product->set_name( wp_filter_post_kses( $request['name'] ) );
		}

		// Post content.
		if ( isset( $request['description'] ) ) {
			$product->set_description( wp_filter_post_kses( $request['description'] ) );
		}

		// Post excerpt.
		if ( isset( $request['short_description'] ) ) {
			$product->set_short_description( wp_filter_post_kses( $request['short_description'] ) );
		}

		// Post status.
		if ( isset( $request['status'] ) ) {
			$product->set_status( get_post_status_object( $request['status'] ) ? $request['status'] : 'draft' );
		}

		// Post slug.
		if ( isset( $request['slug'] ) ) {
			$product->set_slug( $request['slug'] );
		}

		// Menu order.
		if ( isset( $request['menu_order'] ) ) {
			$product->set_menu_order( $request['menu_order'] );
		}

		// Comment status.
		if ( isset( $request['reviews_allowed'] ) ) {
			$product->set_reviews_allowed( $request['reviews_allowed'] );
		}

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
			$product->set_purchase_note( wp_kses_post( wp_unslash( $request['purchase_note'] ) ) );
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

			if ( isset( $request['date_on_sale_from_gmt'] ) ) {
				$product->set_date_on_sale_from( $request['date_on_sale_from_gmt'] ? strtotime( $request['date_on_sale_from_gmt'] ) : null );
			}

			if ( isset( $request['date_on_sale_to'] ) ) {
				$product->set_date_on_sale_to( $request['date_on_sale_to'] );
			}

			if ( isset( $request['date_on_sale_to_gmt'] ) ) {
				$product->set_date_on_sale_to( $request['date_on_sale_to_gmt'] ? strtotime( $request['date_on_sale_to_gmt'] ) : null );
			}
		}

		// Product parent ID.
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

		// Set children for a grouped product.
		if ( $product->is_type( 'grouped' ) && isset( $request['grouped_products'] ) ) {
			$product->set_children( $request['grouped_products'] );
		}

		// Check for featured/gallery images, upload it and set it.
		if ( isset( $request['images'] ) ) {
			$product = $this->set_product_images( $product, $request['images'] );
		}

		// Allow set meta_data.
		if ( is_array( $request['meta_data'] ) ) {
			foreach ( $request['meta_data'] as $meta ) {
				$product->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
			}
		}

		/**
		 * Filters an object before it is inserted via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`,
		 * refers to the object type slug.
		 *
		 * @param WC_Data         $product  Object object.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating If is creating a new object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}_object", $product, $request, $creating );
	}

	/**
	 * Set product images.
	 *
	 * @param WC_Product $product Product instance.
	 * @param array      $images  Images data.
	 *
	 * @throws WC_REST_Exception REST API exceptions.
	 * @return WC_Product
	 */
	protected function set_product_images( $product, $images ) {
		$images = is_array( $images ) ? array_filter( $images ) : array();

		if ( ! empty( $images ) ) {
			$gallery_positions = array();

			foreach ( $images as $index => $image ) {
				$attachment_id = isset( $image['id'] ) ? absint( $image['id'] ) : 0;

				if ( 0 === $attachment_id && isset( $image['src'] ) ) {
					$upload = wc_rest_upload_image_from_url( esc_url_raw( $image['src'] ) );

					if ( is_wp_error( $upload ) ) {
						if ( ! apply_filters( 'woocommerce_rest_suppress_image_upload_error', false, $upload, $product->get_id(), $images ) ) {
							throw new WC_REST_Exception( 'woocommerce_product_image_upload_error', $upload->get_error_message(), 400 );
						} else {
							continue;
						}
					}

					$attachment_id = wc_rest_set_uploaded_image_as_attachment( $upload, $product->get_id() );
				}

				if ( ! wp_attachment_is_image( $attachment_id ) ) {
					/* translators: %s: attachment id */
					throw new WC_REST_Exception( 'woocommerce_product_invalid_image_id', sprintf( __( '#%s is an invalid image ID.', 'woocommerce' ), $attachment_id ), 400 );
				}

				$gallery_positions[ $attachment_id ] = absint( isset( $image['position'] ) ? $image['position'] : $index );

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

				// Set the image source if present, for future reference.
				if ( ! empty( $image['src'] ) ) {
					update_post_meta( $attachment_id, '_wc_attachment_source', esc_url_raw( $image['src'] ) );
				}
			}

			// Sort images and get IDs in correct order.
			asort( $gallery_positions );

			// Get gallery in correct order.
			$gallery = array_keys( $gallery_positions );

			// Featured image is in position 0.
			$image_id = array_shift( $gallery );

			// Set images.
			$product->set_image_id( $image_id );
			$product->set_gallery_image_ids( $gallery );
		} else {
			$product->set_image_id( '' );
			$product->set_gallery_image_ids( array() );
		}

		return $product;
	}

	/**
	 * Save product shipping data.
	 *
	 * @param WC_Product $product Product instance.
	 * @param array      $data    Shipping data.
	 *
	 * @return WC_Product
	 */
	protected function save_product_shipping_data( $product, $data ) {
		// Virtual.
		if ( isset( $data['virtual'] ) && true === $data['virtual'] ) {
			$product->set_weight( '' );
			$product->set_height( '' );
			$product->set_length( '' );
			$product->set_width( '' );
		} else {
			if ( isset( $data['weight'] ) ) {
				$product->set_weight( $data['weight'] );
			}

			// Height.
			if ( isset( $data['dimensions']['height'] ) ) {
				$product->set_height( $data['dimensions']['height'] );
			}

			// Width.
			if ( isset( $data['dimensions']['width'] ) ) {
				$product->set_width( $data['dimensions']['width'] );
			}

			// Length.
			if ( isset( $data['dimensions']['length'] ) ) {
				$product->set_length( $data['dimensions']['length'] );
			}
		}

		// Shipping class.
		if ( isset( $data['shipping_class'] ) ) {
			$data_store        = $product->get_data_store();
			$shipping_class_id = $data_store->get_shipping_class_id_by_slug( wc_clean( $data['shipping_class'] ) );
			$product->set_shipping_class_id( $shipping_class_id );
		}

		return $product;
	}

	/**
	 * Save downloadable files.
	 *
	 * @param WC_Product $product    Product instance.
	 * @param array      $downloads  Downloads data.
	 * @param int        $deprecated Deprecated since 3.0.
	 *
	 * @return WC_Product
	 */
	protected function save_downloadable_files( $product, $downloads, $deprecated = 0 ) {
		if ( $deprecated ) {
			wc_deprecated_argument( 'variation_id', '3.0', 'save_downloadable_files() not requires a variation_id anymore.' );
		}

		$files = array();
		foreach ( $downloads as $key => $file ) {
			if ( empty( $file['file'] ) ) {
				continue;
			}

			$download = new WC_Product_Download();
			$download->set_id( ! empty( $file['id'] ) ? $file['id'] : wp_generate_uuid4() );
			$download->set_name( $file['name'] ? $file['name'] : wc_get_filename_from_url( $file['file'] ) );
			$download->set_file( apply_filters( 'woocommerce_file_download_path', $file['file'], $product, $key ) );
			$files[] = $download;
		}
		$product->set_downloads( $files );

		return $product;
	}

	/**
	 * Save taxonomy terms.
	 *
	 * @param WC_Product $product  Product instance.
	 * @param array      $terms    Terms data.
	 * @param string     $taxonomy Taxonomy name.
	 *
	 * @return WC_Product
	 */
	protected function save_taxonomy_terms( $product, $terms, $taxonomy = 'cat' ) {
		$term_ids = wp_list_pluck( $terms, 'id' );

		if ( 'cat' === $taxonomy ) {
			$product->set_category_ids( $term_ids );
		} elseif ( 'tag' === $taxonomy ) {
			$product->set_tag_ids( $term_ids );
		}

		return $product;
	}

	/**
	 * Save default attributes.
	 *
	 * @param WC_Product      $product Product instance.
	 * @param WP_REST_Request $request Request data.
	 *
	 * @since  3.0.0
	 * @return WC_Product
	 */
	protected function save_default_attributes( $product, $request ) {
		if ( isset( $request['default_attributes'] ) && is_array( $request['default_attributes'] ) ) {

			$attributes         = $product->get_attributes();
			$default_attributes = array();

			foreach ( $request['default_attributes'] as $attribute ) {
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

				if ( isset( $attributes[ $attribute_name ] ) ) {
					$_attribute = $attributes[ $attribute_name ];

					if ( $_attribute['is_variation'] ) {
						$value = isset( $attribute['option'] ) ? wc_clean( stripslashes( $attribute['option'] ) ) : '';

						if ( ! empty( $_attribute['is_taxonomy'] ) ) {
							// If dealing with a taxonomy, we need to get the slug from the name posted to the API.
							$term = get_term_by( 'name', $value, $attribute_name );

							if ( $term && ! is_wp_error( $term ) ) {
								$value = $term->slug;
							} else {
								$value = sanitize_title( $value );
							}
						}

						if ( $value ) {
							$default_attributes[ $attribute_name ] = $value;
						}
					}
				}
			}

			$product->set_default_attributes( $default_attributes );
		}

		return $product;
	}

	/**
	 * Clear caches here so in sync with any new variations/children.
	 *
	 * @param WC_Data $object Object data.
	 */
	public function clear_transients( $object ) {
		wc_delete_product_transients( $object->get_id() );
		wp_cache_delete( 'product-' . $object->get_id(), 'products' );
	}

	/**
	 * Delete a single item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_item( $request ) {
		$id     = (int) $request['id'];
		$force  = (bool) $request['force'];
		$object = $this->get_object( (int) $request['id'] );
		$result = false;

		if ( ! $object || 0 === $object->get_id() ) {
			return new WP_Error(
				"woocommerce_rest_{$this->post_type}_invalid_id",
				__( 'Invalid ID.', 'woocommerce' ),
				array(
					'status' => 404,
				)
			);
		}

		if ( 'variation' === $object->get_type() ) {
			return new WP_Error(
				"woocommerce_rest_invalid_{$this->post_type}_id",
				__( 'To manipulate product variations you should use the /products/&lt;product_id&gt;/variations/&lt;id&gt; endpoint.', 'woocommerce' ),
				array(
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
				"woocommerce_rest_user_cannot_delete_{$this->post_type}",
				/* translators: %s: post type */
				sprintf( __( 'Sorry, you are not allowed to delete %s.', 'woocommerce' ), $this->post_type ),
				array(
					'status' => rest_authorization_required_code(),
				)
			);
		}

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_object_for_response( $object, $request );

		// If we're forcing, then delete permanently.
		if ( $force ) {
			if ( $object->is_type( 'variable' ) ) {
				foreach ( $object->get_children() as $child_id ) {
					$child = wc_get_product( $child_id );
					if ( ! empty( $child ) ) {
						$child->delete( true );
					}
				}
			} else {
				// For other product types, if the product has children, remove the relationship.
				foreach ( $object->get_children() as $child_id ) {
					$child = wc_get_product( $child_id );
					if ( ! empty( $child ) ) {
						$child->set_parent_id( 0 );
						$child->save();
					}
				}
			}

			$object->delete( true );
			$result = 0 === $object->get_id();
		} else {
			// If we don't support trashing for this type, error out.
			if ( ! $supports_trash ) {
				return new WP_Error(
					'woocommerce_rest_trash_not_supported',
					/* translators: %s: post type */
					sprintf( __( 'The %s does not support trashing.', 'woocommerce' ), $this->post_type ),
					array(
						'status' => 501,
					)
				);
			}

			// Otherwise, only trash if we haven't already.
			if ( is_callable( array( $object, 'get_status' ) ) ) {
				if ( 'trash' === $object->get_status() ) {
					return new WP_Error(
						'woocommerce_rest_already_trashed',
						/* translators: %s: post type */
						sprintf( __( 'The %s has already been deleted.', 'woocommerce' ), $this->post_type ),
						array(
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
				'woocommerce_rest_cannot_delete',
				/* translators: %s: post type */
				sprintf( __( 'The %s cannot be deleted.', 'woocommerce' ), $this->post_type ),
				array(
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
	 * Get the Product's schema, conforming to JSON Schema.
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
				'name'                  => array(
					'description' => __( 'Product name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'slug'                  => array(
					'description' => __( 'Product slug.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'permalink'             => array(
					'description' => __( 'Product URL.', 'woocommerce' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created'          => array(
					'description' => __( "The date the product was created, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_created_gmt'      => array(
					'description' => __( 'The date the product was created, as GMT.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_modified'         => array(
					'description' => __( "The date the product was last modified, in the site's timezone.", 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_modified_gmt'     => array(
					'description' => __( 'The date the product was last modified, as GMT.', 'woocommerce' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'type'                  => array(
					'description' => __( 'Product type.', 'woocommerce' ),
					'type'        => 'string',
					'default'     => 'simple',
					'enum'        => array_keys( wc_get_product_types() ),
					'context'     => array( 'view', 'edit' ),
				),
				'status'                => array(
					'description' => __( 'Product status (post status).', 'woocommerce' ),
					'type'        => 'string',
					'default'     => 'publish',
					'enum'        => array_merge( array_keys( get_post_statuses() ), array( 'future' ) ),
					'context'     => array( 'view', 'edit' ),
				),
				'featured'              => array(
					'description' => __( 'Featured product.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'catalog_visibility'    => array(
					'description' => __( 'Catalog visibility.', 'woocommerce' ),
					'type'        => 'string',
					'default'     => 'visible',
					'enum'        => array( 'visible', 'catalog', 'search', 'hidden' ),
					'context'     => array( 'view', 'edit' ),
				),
				'description'           => array(
					'description' => __( 'Product description.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'short_description'     => array(
					'description' => __( 'Product short description.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'sku'                   => array(
					'description' => __( 'Unique identifier.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'price'                 => array(
					'description' => __( 'Current product price.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'regular_price'         => array(
					'description' => __( 'Product regular price.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'sale_price'            => array(
					'description' => __( 'Product sale price.', 'woocommerce' ),
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
				'price_html'            => array(
					'description' => __( 'Price formatted in HTML.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'on_sale'               => array(
					'description' => __( 'Shows if the product is on sale.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'purchasable'           => array(
					'description' => __( 'Shows if the product can be bought.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'total_sales'           => array(
					'description' => __( 'Amount of sales.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'virtual'               => array(
					'description' => __( 'If the product is virtual.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'downloadable'          => array(
					'description' => __( 'If the product is downloadable.', 'woocommerce' ),
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
				'external_url'          => array(
					'description' => __( 'Product external URL. Only for external products.', 'woocommerce' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'edit' ),
				),
				'button_text'           => array(
					'description' => __( 'Product external button text. Only for external products.', 'woocommerce' ),
					'type'        => 'string',
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
					'description' => __( 'Stock management at product level.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'stock_quantity'        => array(
					'description' => __( 'Stock quantity.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'in_stock'              => array(
					'description' => __( 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.', 'woocommerce' ),
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
					'description' => __( 'Shows if the product is on backordered.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'sold_individually'     => array(
					'description' => __( 'Allow one item to be bought in a single order.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'weight'                => array(
					/* translators: %s: weight unit */
					'description' => sprintf( __( 'Product weight (%s).', 'woocommerce' ), $weight_unit_label ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'dimensions'            => array(
					'description' => __( 'Product dimensions.', 'woocommerce' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'properties'  => array(
						'length' => array(
							/* translators: %s: dimension unit */
							'description' => sprintf( __( 'Product length (%s).', 'woocommerce' ), $dimension_unit_label ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'width'  => array(
							/* translators: %s: dimension unit */
							'description' => sprintf( __( 'Product width (%s).', 'woocommerce' ), $dimension_unit_label ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
						'height' => array(
							/* translators: %s: dimension unit */
							'description' => sprintf( __( 'Product height (%s).', 'woocommerce' ), $dimension_unit_label ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
						),
					),
				),
				'shipping_required'     => array(
					'description' => __( 'Shows if the product need to be shipped.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'shipping_taxable'      => array(
					'description' => __( 'Shows whether or not the product shipping is taxable.', 'woocommerce' ),
					'type'        => 'boolean',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'shipping_class'        => array(
					'description' => __( 'Shipping class slug.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'shipping_class_id'     => array(
					'description' => __( 'Shipping class ID.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'reviews_allowed'       => array(
					'description' => __( 'Allow reviews.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => true,
					'context'     => array( 'view', 'edit' ),
				),
				'average_rating'        => array(
					'description' => __( 'Reviews average rating.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'rating_count'          => array(
					'description' => __( 'Amount of reviews that the product have.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'related_ids'           => array(
					'description' => __( 'List of related products IDs.', 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type' => 'integer',
					),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'upsell_ids'            => array(
					'description' => __( 'List of up-sell products IDs.', 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type' => 'integer',
					),
					'context'     => array( 'view', 'edit' ),
				),
				'cross_sell_ids'        => array(
					'description' => __( 'List of cross-sell products IDs.', 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type' => 'integer',
					),
					'context'     => array( 'view', 'edit' ),
				),
				'parent_id'             => array(
					'description' => __( 'Product parent ID.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'purchase_note'         => array(
					'description' => __( 'Optional note to send the customer after purchase.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'categories'            => array(
					'description' => __( 'List of categories.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'   => array(
								'description' => __( 'Category ID.', 'woocommerce' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
							'name' => array(
								'description' => __( 'Category name.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'slug' => array(
								'description' => __( 'Category slug.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
						),
					),
				),
				'tags'                  => array(
					'description' => __( 'List of tags.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'   => array(
								'description' => __( 'Tag ID.', 'woocommerce' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
							'name' => array(
								'description' => __( 'Tag name.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'slug' => array(
								'description' => __( 'Tag slug.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
						),
					),
				),
				'images'                => array(
					'description' => __( 'List of images.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
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
				),
				'attributes'            => array(
					'description' => __( 'List of attributes.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'        => array(
								'description' => __( 'Attribute ID.', 'woocommerce' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
							'name'      => array(
								'description' => __( 'Attribute name.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'position'  => array(
								'description' => __( 'Attribute position.', 'woocommerce' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
							'visible'   => array(
								'description' => __( "Define if the attribute is visible on the \"Additional information\" tab in the product's page.", 'woocommerce' ),
								'type'        => 'boolean',
								'default'     => false,
								'context'     => array( 'view', 'edit' ),
							),
							'variation' => array(
								'description' => __( 'Define if the attribute can be used as variation.', 'woocommerce' ),
								'type'        => 'boolean',
								'default'     => false,
								'context'     => array( 'view', 'edit' ),
							),
							'options'   => array(
								'description' => __( 'List of available term names of the attribute.', 'woocommerce' ),
								'type'        => 'array',
								'context'     => array( 'view', 'edit' ),
								'items'       => array(
									'type' => 'string',
								),
							),
						),
					),
				),
				'default_attributes'    => array(
					'description' => __( 'Defaults variation attributes.', 'woocommerce' ),
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
				'variations'            => array(
					'description' => __( 'List of variations IDs.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type' => 'integer',
					),
					'readonly'    => true,
				),
				'grouped_products'      => array(
					'description' => __( 'List of grouped products ID.', 'woocommerce' ),
					'type'        => 'array',
					'items'       => array(
						'type' => 'integer',
					),
					'context'     => array( 'view', 'edit' ),
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
	 * Get the query params for collections of attachments.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['orderby']['enum'] = array_merge( $params['orderby']['enum'], array( 'menu_order' ) );

		$params['slug']           = array(
			'description'       => __( 'Limit result set to products with a specific slug.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['status']         = array(
			'default'           => 'any',
			'description'       => __( 'Limit result set to products assigned a specific status.', 'woocommerce' ),
			'type'              => 'string',
			'enum'              => array_merge( array( 'any', 'future', 'trash' ), array_keys( get_post_statuses() ) ),
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['type']           = array(
			'description'       => __( 'Limit result set to products assigned a specific type.', 'woocommerce' ),
			'type'              => 'string',
			'enum'              => array_keys( wc_get_product_types() ),
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['sku']            = array(
			'description'       => __( 'Limit result set to products with specific SKU(s). Use commas to separate.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['featured']       = array(
			'description'       => __( 'Limit result set to featured products.', 'woocommerce' ),
			'type'              => 'boolean',
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['category']       = array(
			'description'       => __( 'Limit result set to products assigned a specific category ID.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['tag']            = array(
			'description'       => __( 'Limit result set to products assigned a specific tag ID.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['shipping_class'] = array(
			'description'       => __( 'Limit result set to products assigned a specific shipping class ID.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['attribute']      = array(
			'description'       => __( 'Limit result set to products with a specific attribute. Use the taxonomy name/attribute slug.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['attribute_term'] = array(
			'description'       => __( 'Limit result set to products with a specific attribute term ID (required an assigned attribute).', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'wp_parse_id_list',
			'validate_callback' => 'rest_validate_request_arg',
		);

		if ( wc_tax_enabled() ) {
			$params['tax_class'] = array(
				'description'       => __( 'Limit result set to products with a specific tax class.', 'woocommerce' ),
				'type'              => 'string',
				'enum'              => array_merge( array( 'standard' ), WC_Tax::get_tax_class_slugs() ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			);
		}

		$params['in_stock']     = array(
			'description'       => __( 'Limit result set to products in stock or out of stock.', 'woocommerce' ),
			'type'              => 'boolean',
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['on_sale']      = array(
			'description'       => __( 'Limit result set to products on sale.', 'woocommerce' ),
			'type'              => 'boolean',
			'sanitize_callback' => 'wc_string_to_bool',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['min_price']    = array(
			'description'       => __( 'Limit result set to products based on a minimum price.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['max_price']    = array(
			'description'       => __( 'Limit result set to products based on a maximum price.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['include_meta'] = array(
			'default'           => array(),
			'description'       => __( 'Limit meta_data to specific keys.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'string',
			),
			'sanitize_callback' => 'wp_parse_list',
		);
		$params['exclude_meta'] = array(
			'default'           => array(),
			'description'       => __( 'Ensure meta_data excludes specific keys.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'string',
			),
			'sanitize_callback' => 'wp_parse_list',
		);

		return $params;
	}
}

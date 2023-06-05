<?php
/**
 * WooCommerce API Products Class
 *
 * Handles requests to the /products endpoint
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce\RestApi
 * @since       2.1
 * @version     3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_API_Products extends WC_API_Resource {

	/** @var string $base the route base */
	protected $base = '/products';

	/**
	 * Register the routes for this class
	 *
	 * GET/POST /products
	 * GET /products/count
	 * GET/PUT/DELETE /products/<id>
	 * GET /products/<id>/reviews
	 *
	 * @since 2.1
	 * @param array $routes
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET/POST /products
		$routes[ $this->base ] = array(
			array( array( $this, 'get_products' ), WC_API_Server::READABLE ),
			array( array( $this, 'create_product' ), WC_API_SERVER::CREATABLE | WC_API_Server::ACCEPT_DATA ),
		);

		# GET /products/count
		$routes[ $this->base . '/count' ] = array(
			array( array( $this, 'get_products_count' ), WC_API_Server::READABLE ),
		);

		# GET/PUT/DELETE /products/<id>
		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_product' ), WC_API_Server::READABLE ),
			array( array( $this, 'edit_product' ), WC_API_Server::EDITABLE | WC_API_Server::ACCEPT_DATA ),
			array( array( $this, 'delete_product' ), WC_API_Server::DELETABLE ),
		);

		# GET /products/<id>/reviews
		$routes[ $this->base . '/(?P<id>\d+)/reviews' ] = array(
			array( array( $this, 'get_product_reviews' ), WC_API_Server::READABLE ),
		);

		# GET /products/<id>/orders
		$routes[ $this->base . '/(?P<id>\d+)/orders' ] = array(
			array( array( $this, 'get_product_orders' ), WC_API_Server::READABLE ),
		);

		# GET /products/categories
		$routes[ $this->base . '/categories' ] = array(
			array( array( $this, 'get_product_categories' ), WC_API_Server::READABLE ),
		);

		# GET /products/categories/<id>
		$routes[ $this->base . '/categories/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_product_category' ), WC_API_Server::READABLE ),
		);

		# GET/POST /products/attributes
		$routes[ $this->base . '/attributes' ] = array(
			array( array( $this, 'get_product_attributes' ), WC_API_Server::READABLE ),
			array( array( $this, 'create_product_attribute' ), WC_API_SERVER::CREATABLE | WC_API_Server::ACCEPT_DATA ),
		);

		# GET/PUT/DELETE /attributes/<id>
		$routes[ $this->base . '/attributes/(?P<id>\d+)' ] = array(
			array( array( $this, 'get_product_attribute' ), WC_API_Server::READABLE ),
			array( array( $this, 'edit_product_attribute' ), WC_API_Server::EDITABLE | WC_API_Server::ACCEPT_DATA ),
			array( array( $this, 'delete_product_attribute' ), WC_API_Server::DELETABLE ),
		);

		# GET /products/sku/<product sku>
		$routes[ $this->base . '/sku/(?P<sku>\w[\w\s\-]*)' ] = array(
			array( array( $this, 'get_product_by_sku' ), WC_API_Server::READABLE ),
		);

		# POST|PUT /products/bulk
		$routes[ $this->base . '/bulk' ] = array(
			array( array( $this, 'bulk' ), WC_API_Server::EDITABLE | WC_API_Server::ACCEPT_DATA ),
		);

		return $routes;
	}

	/**
	 * Get all products
	 *
	 * @since 2.1
	 * @param string $fields
	 * @param string $type
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_products( $fields = null, $type = null, $filter = array(), $page = 1 ) {

		if ( ! empty( $type ) ) {
			$filter['type'] = $type;
		}

		$filter['page'] = $page;

		$query = $this->query_products( $filter );

		$products = array();

		foreach ( $query->posts as $product_id ) {

			if ( ! $this->is_readable( $product_id ) ) {
				continue;
			}

			$products[] = current( $this->get_product( $product_id, $fields ) );
		}

		$this->server->add_pagination_headers( $query );

		return array( 'products' => $products );
	}

	/**
	 * Get the product for the given ID
	 *
	 * @since 2.1
	 * @param int $id the product ID
	 * @param string $fields
	 * @return array|WP_Error
	 */
	public function get_product( $id, $fields = null ) {

		$id = $this->validate_request( $id, 'product', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$product = wc_get_product( $id );

		// add data that applies to every product type
		$product_data = $this->get_product_data( $product );

		// add variations to variable products
		if ( $product->is_type( 'variable' ) && $product->has_child() ) {
			$product_data['variations'] = $this->get_variation_data( $product );
		}

		// add the parent product data to an individual variation
		if ( $product->is_type( 'variation' ) && $product->get_parent_id() ) {
			$_product = wc_get_product( $product->get_parent_id() );
			$product_data['parent'] = $this->get_product_data( $_product );
		}

		return array( 'product' => apply_filters( 'woocommerce_api_product_response', $product_data, $product, $fields, $this->server ) );
	}

	/**
	 * Get the total number of products
	 *
	 * @since 2.1
	 *
	 * @param string $type
	 * @param array $filter
	 *
	 * @return array|WP_Error
	 */
	public function get_products_count( $type = null, $filter = array() ) {
		try {
			if ( ! current_user_can( 'read_private_products' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_products_count', __( 'You do not have permission to read the products count', 'woocommerce' ), 401 );
			}

			if ( ! empty( $type ) ) {
				$filter['type'] = $type;
			}

			$query = $this->query_products( $filter );

			return array( 'count' => (int) $query->found_posts );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Create a new product
	 *
	 * @since 2.2
	 *
	 * @param array $data posted data
	 *
	 * @return array|WP_Error
	 */
	public function create_product( $data ) {
		$id = 0;

		try {
			if ( ! isset( $data['product'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_product_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'product' ), 400 );
			}

			$data = $data['product'];

			// Check permissions
			if ( ! current_user_can( 'publish_products' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_create_product', __( 'You do not have permission to create products', 'woocommerce' ), 401 );
			}

			$data = apply_filters( 'woocommerce_api_create_product_data', $data, $this );

			// Check if product title is specified
			if ( ! isset( $data['title'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_product_title', sprintf( __( 'Missing parameter %s', 'woocommerce' ), 'title' ), 400 );
			}

			// Check product type
			if ( ! isset( $data['type'] ) ) {
				$data['type'] = 'simple';
			}

			// Set visible visibility when not sent
			if ( ! isset( $data['catalog_visibility'] ) ) {
				$data['catalog_visibility'] = 'visible';
			}

			// Validate the product type
			if ( ! in_array( wc_clean( $data['type'] ), array_keys( wc_get_product_types() ) ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_product_type', sprintf( __( 'Invalid product type - the product type must be any of these: %s', 'woocommerce' ), implode( ', ', array_keys( wc_get_product_types() ) ) ), 400 );
			}

			// Enable description html tags.
			$post_content = isset( $data['description'] ) ? wc_clean( $data['description'] ) : '';
			if ( $post_content && isset( $data['enable_html_description'] ) && true === $data['enable_html_description'] ) {

				$post_content = wp_filter_post_kses( $data['description'] );
			}

			// Enable short description html tags.
			$post_excerpt = isset( $data['short_description'] ) ? wc_clean( $data['short_description'] ) : '';
			if ( $post_excerpt && isset( $data['enable_html_short_description'] ) && true === $data['enable_html_short_description'] ) {
				$post_excerpt = wp_filter_post_kses( $data['short_description'] );
			}

			$classname = WC_Product_Factory::get_classname_from_product_type( $data['type'] );
			if ( ! class_exists( $classname ) ) {
				$classname = 'WC_Product_Simple';
			}
			$product = new $classname();

			$product->set_name( wc_clean( $data['title'] ) );
			$product->set_status( isset( $data['status'] ) ? wc_clean( $data['status'] ) : 'publish' );
			$product->set_short_description( isset( $data['short_description'] ) ? $post_excerpt : '' );
			$product->set_description( isset( $data['description'] ) ? $post_content : '' );

			// Attempts to create the new product.
			$product->save();
			$id = $product->get_id();

			// Checks for an error in the product creation
			if ( 0 >= $id ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_create_product', $id->get_error_message(), 400 );
			}

			// Check for featured/gallery images, upload it and set it
			if ( isset( $data['images'] ) ) {
				$product = $this->save_product_images( $product, $data['images'] );
			}

			// Save product meta fields
			$product = $this->save_product_meta( $product, $data );
			$product->save();

			// Save variations
			if ( isset( $data['type'] ) && 'variable' == $data['type'] && isset( $data['variations'] ) && is_array( $data['variations'] ) ) {
				$this->save_variations( $product, $data );
			}

			do_action( 'woocommerce_api_create_product', $id, $data );

			// Clear cache/transients
			wc_delete_product_transients( $id );

			$this->server->send_status( 201 );

			return $this->get_product( $id );
		} catch ( WC_Data_Exception $e ) {
			$this->clear_product( $id );
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		} catch ( WC_API_Exception $e ) {
			$this->clear_product( $id );
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Edit a product
	 *
	 * @since 2.2
	 *
	 * @param int $id the product ID
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function edit_product( $id, $data ) {
		try {
			if ( ! isset( $data['product'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_product_data', sprintf( __( 'No %1$s data specified to edit %1$s', 'woocommerce' ), 'product' ), 400 );
			}

			$data = $data['product'];

			$id = $this->validate_request( $id, 'product', 'edit' );

			if ( is_wp_error( $id ) ) {
				return $id;
			}

			$product = wc_get_product( $id );

			$data = apply_filters( 'woocommerce_api_edit_product_data', $data, $this );

			// Product title.
			if ( isset( $data['title'] ) ) {
				$product->set_name( wc_clean( $data['title'] ) );
			}

			// Product name (slug).
			if ( isset( $data['name'] ) ) {
				$product->set_slug( wc_clean( $data['name'] ) );
			}

			// Product status.
			if ( isset( $data['status'] ) ) {
				$product->set_status( wc_clean( $data['status'] ) );
			}

			// Product short description.
			if ( isset( $data['short_description'] ) ) {
				// Enable short description html tags.
				$post_excerpt = ( isset( $data['enable_html_short_description'] ) && true === $data['enable_html_short_description'] ) ? wp_filter_post_kses( $data['short_description'] ) : wc_clean( $data['short_description'] );
				$product->set_short_description( $post_excerpt );
			}

			// Product description.
			if ( isset( $data['description'] ) ) {
				// Enable description html tags.
				$post_content = ( isset( $data['enable_html_description'] ) && true === $data['enable_html_description'] ) ? wp_filter_post_kses( $data['description'] ) : wc_clean( $data['description'] );
				$product->set_description( $post_content );
			}

			// Validate the product type.
			if ( isset( $data['type'] ) && ! in_array( wc_clean( $data['type'] ), array_keys( wc_get_product_types() ) ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_product_type', sprintf( __( 'Invalid product type - the product type must be any of these: %s', 'woocommerce' ), implode( ', ', array_keys( wc_get_product_types() ) ) ), 400 );
			}

			// Check for featured/gallery images, upload it and set it.
			if ( isset( $data['images'] ) ) {
				$product = $this->save_product_images( $product, $data['images'] );
			}

			// Save product meta fields.
			$product = $this->save_product_meta( $product, $data );

			// Save variations.
			if ( $product->is_type( 'variable' ) ) {
				if ( isset( $data['variations'] ) && is_array( $data['variations'] ) ) {
					$this->save_variations( $product, $data );
				} else {
					// Just sync variations.
					$product = WC_Product_Variable::sync( $product, false );
				}
			}

			$product->save();

			do_action( 'woocommerce_api_edit_product', $id, $data );

			// Clear cache/transients.
			wc_delete_product_transients( $id );

			return $this->get_product( $id );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Delete a product.
	 *
	 * @since 2.2
	 *
	 * @param int $id the product ID.
	 * @param bool $force true to permanently delete order, false to move to trash.
	 *
	 * @return array|WP_Error
	 */
	public function delete_product( $id, $force = false ) {

		$id = $this->validate_request( $id, 'product', 'delete' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$product = wc_get_product( $id );

		do_action( 'woocommerce_api_delete_product', $id, $this );

		// If we're forcing, then delete permanently.
		if ( $force ) {
			if ( $product->is_type( 'variable' ) ) {
				foreach ( $product->get_children() as $child_id ) {
					$child = wc_get_product( $child_id );
					if ( ! empty( $child ) ) {
						$child->delete( true );
					}
				}
			} else {
				// For other product types, if the product has children, remove the relationship.
				foreach ( $product->get_children() as $child_id ) {
					$child = wc_get_product( $child_id );
					if ( ! empty( $child ) ) {
						$child->set_parent_id( 0 );
						$child->save();
					}
				}
			}

			$product->delete( true );
			$result = ! ( $product->get_id() > 0 );
		} else {
			$product->delete();
			$result = 'trash' === $product->get_status();
		}

		if ( ! $result ) {
			return new WP_Error( 'woocommerce_api_cannot_delete_product', sprintf( __( 'This %s cannot be deleted', 'woocommerce' ), 'product' ), array( 'status' => 500 ) );
		}

		// Delete parent product transients.
		if ( $parent_id = wp_get_post_parent_id( $id ) ) {
			wc_delete_product_transients( $parent_id );
		}

		if ( $force ) {
			return array( 'message' => sprintf( __( 'Permanently deleted %s', 'woocommerce' ), 'product' ) );
		} else {
			$this->server->send_status( '202' );

			return array( 'message' => sprintf( __( 'Deleted %s', 'woocommerce' ), 'product' ) );
		}
	}

	/**
	 * Get the reviews for a product
	 *
	 * @since 2.1
	 * @param int $id the product ID to get reviews for
	 * @param string $fields fields to include in response
	 * @return array|WP_Error
	 */
	public function get_product_reviews( $id, $fields = null ) {

		$id = $this->validate_request( $id, 'product', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$comments = get_approved_comments( $id );
		$reviews  = array();

		foreach ( $comments as $comment ) {

			$reviews[] = array(
				'id'             => intval( $comment->comment_ID ),
				'created_at'     => $this->server->format_datetime( $comment->comment_date_gmt ),
				'review'         => $comment->comment_content,
				'rating'         => get_comment_meta( $comment->comment_ID, 'rating', true ),
				'reviewer_name'  => $comment->comment_author,
				'reviewer_email' => $comment->comment_author_email,
				'verified'       => wc_review_is_from_verified_owner( $comment->comment_ID ),
			);
		}

		return array( 'product_reviews' => apply_filters( 'woocommerce_api_product_reviews_response', $reviews, $id, $fields, $comments, $this->server ) );
	}

	/**
	 * Get the orders for a product
	 *
	 * @since 2.4.0
	 * @param int $id the product ID to get orders for
	 * @param string fields  fields to retrieve
	 * @param array $filter filters to include in response
	 * @param string $status the order status to retrieve
	 * @param $page  $page   page to retrieve
	 * @return array|WP_Error
	 */
	public function get_product_orders( $id, $fields = null, $filter = array(), $status = null, $page = 1 ) {
		global $wpdb;

		$id = $this->validate_request( $id, 'product', 'read' );

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		$order_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT order_id
			FROM {$wpdb->prefix}woocommerce_order_items
			WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = '_product_id' AND meta_value = %d )
			AND order_item_type = 'line_item'
		 ", $id ) );

		if ( empty( $order_ids ) ) {
			return array( 'orders' => array() );
		}

		$filter = array_merge( $filter, array(
			'in' => implode( ',', $order_ids ),
		) );

		$orders = WC()->api->WC_API_Orders->get_orders( $fields, $filter, $status, $page );

		return array( 'orders' => apply_filters( 'woocommerce_api_product_orders_response', $orders['orders'], $id, $filter, $fields, $this->server ) );
	}

	/**
	 * Get a listing of product categories
	 *
	 * @since 2.2
	 *
	 * @param string|null $fields fields to limit response to
	 *
	 * @return array|WP_Error
	 */
	public function get_product_categories( $fields = null ) {
		try {
			// Permissions check
			if ( ! current_user_can( 'manage_product_terms' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_product_categories', __( 'You do not have permission to read product categories', 'woocommerce' ), 401 );
			}

			$product_categories = array();

			$terms = get_terms( 'product_cat', array( 'hide_empty' => false, 'fields' => 'ids' ) );

			foreach ( $terms as $term_id ) {
				$product_categories[] = current( $this->get_product_category( $term_id, $fields ) );
			}

			return array( 'product_categories' => apply_filters( 'woocommerce_api_product_categories_response', $product_categories, $terms, $fields, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get the product category for the given ID
	 *
	 * @since 2.2
	 *
	 * @param string $id product category term ID
	 * @param string|null $fields fields to limit response to
	 *
	 * @return array|WP_Error
	 */
	public function get_product_category( $id, $fields = null ) {
		try {
			$id = absint( $id );

			// Validate ID
			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_product_category_id', __( 'Invalid product category ID', 'woocommerce' ), 400 );
			}

			// Permissions check
			if ( ! current_user_can( 'manage_product_terms' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_product_categories', __( 'You do not have permission to read product categories', 'woocommerce' ), 401 );
			}

			$term = get_term( $id, 'product_cat' );

			if ( is_wp_error( $term ) || is_null( $term ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_product_category_id', __( 'A product category with the provided ID could not be found', 'woocommerce' ), 404 );
			}

			$term_id = intval( $term->term_id );

			// Get category display type
			$display_type = get_term_meta( $term_id, 'display_type', true );

			// Get category image
			$image = '';
			if ( $image_id = get_term_meta( $term_id, 'thumbnail_id', true ) ) {
				$image = wp_get_attachment_url( $image_id );
			}

			$product_category = array(
				'id'          => $term_id,
				'name'        => $term->name,
				'slug'        => $term->slug,
				'parent'      => $term->parent,
				'description' => $term->description,
				'display'     => $display_type ? $display_type : 'default',
				'image'       => $image ? esc_url( $image ) : '',
				'count'       => intval( $term->count ),
			);

			return array( 'product_category' => apply_filters( 'woocommerce_api_product_category_response', $product_category, $id, $fields, $term, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Helper method to get product post objects
	 *
	 * @since 2.1
	 * @param array $args request arguments for filtering query
	 * @return WP_Query
	 */
	private function query_products( $args ) {

		// Set base query arguments
		$query_args = array(
			'fields'      => 'ids',
			'post_type'   => 'product',
			'post_status' => 'publish',
			'meta_query'  => array(),
		);

		if ( ! empty( $args['type'] ) ) {

			$types = explode( ',', $args['type'] );

			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => $types,
				),
			);

			unset( $args['type'] );
		}

		// Filter products by category
		if ( ! empty( $args['category'] ) ) {
			$query_args['product_cat'] = $args['category'];
		}

		// Filter by specific sku
		if ( ! empty( $args['sku'] ) ) {
			if ( ! is_array( $query_args['meta_query'] ) ) {
				$query_args['meta_query'] = array();
			}

			$query_args['meta_query'][] = array(
				'key'     => '_sku',
				'value'   => $args['sku'],
				'compare' => '=',
			);

			$query_args['post_type'] = array( 'product', 'product_variation' );
		}

		$query_args = $this->merge_query_args( $query_args, $args );

		return new WP_Query( $query_args );
	}

	/**
	 * Get standard product data that applies to every product type
	 *
	 * @since 2.1
	 * @param WC_Product|int $product
	 * @return array
	 */
	private function get_product_data( $product ) {
		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return array();
		}

		$prices_precision = wc_get_price_decimals();
		return array(
			'title'              => $product->get_name(),
			'id'                 => $product->get_id(),
			'created_at'         => $this->server->format_datetime( $product->get_date_created(), false, true ),
			'updated_at'         => $this->server->format_datetime( $product->get_date_modified(), false, true ),
			'type'               => $product->get_type(),
			'status'             => $product->get_status(),
			'downloadable'       => $product->is_downloadable(),
			'virtual'            => $product->is_virtual(),
			'permalink'          => $product->get_permalink(),
			'sku'                => $product->get_sku(),
			'price'              => wc_format_decimal( $product->get_price(), $prices_precision ),
			'regular_price'      => wc_format_decimal( $product->get_regular_price(), $prices_precision ),
			'sale_price'         => $product->get_sale_price() ? wc_format_decimal( $product->get_sale_price(), $prices_precision ) : null,
			'price_html'         => $product->get_price_html(),
			'taxable'            => $product->is_taxable(),
			'tax_status'         => $product->get_tax_status(),
			'tax_class'          => $product->get_tax_class(),
			'managing_stock'     => $product->managing_stock(),
			'stock_quantity'     => $product->get_stock_quantity(),
			'in_stock'           => $product->is_in_stock(),
			'backorders_allowed' => $product->backorders_allowed(),
			'backordered'        => $product->is_on_backorder(),
			'sold_individually'  => $product->is_sold_individually(),
			'purchaseable'       => $product->is_purchasable(),
			'featured'           => $product->is_featured(),
			'visible'            => $product->is_visible(),
			'catalog_visibility' => $product->get_catalog_visibility(),
			'on_sale'            => $product->is_on_sale(),
			'product_url'        => $product->is_type( 'external' ) ? $product->get_product_url() : '',
			'button_text'        => $product->is_type( 'external' ) ? $product->get_button_text() : '',
			'weight'             => $product->get_weight() ? wc_format_decimal( $product->get_weight(), 2 ) : null,
			'dimensions'         => array(
				'length' => $product->get_length(),
				'width'  => $product->get_width(),
				'height' => $product->get_height(),
				'unit'   => get_option( 'woocommerce_dimension_unit' ),
			),
			'shipping_required'  => $product->needs_shipping(),
			'shipping_taxable'   => $product->is_shipping_taxable(),
			'shipping_class'     => $product->get_shipping_class(),
			'shipping_class_id'  => ( 0 !== $product->get_shipping_class_id() ) ? $product->get_shipping_class_id() : null,
			'description'        => wpautop( do_shortcode( $product->get_description() ) ),
			'short_description'  => apply_filters( 'woocommerce_short_description', $product->get_short_description() ),
			'reviews_allowed'    => $product->get_reviews_allowed(),
			'average_rating'     => wc_format_decimal( $product->get_average_rating(), 2 ),
			'rating_count'       => $product->get_rating_count(),
			'related_ids'        => array_map( 'absint', array_values( wc_get_related_products( $product->get_id() ) ) ),
			'upsell_ids'         => array_map( 'absint', $product->get_upsell_ids() ),
			'cross_sell_ids'     => array_map( 'absint', $product->get_cross_sell_ids() ),
			'parent_id'          => $product->get_parent_id(),
			'categories'         => wc_get_object_terms( $product->get_id(), 'product_cat', 'name' ),
			'tags'               => wc_get_object_terms( $product->get_id(), 'product_tag', 'name' ),
			'images'             => $this->get_images( $product ),
			'featured_src'       => wp_get_attachment_url( get_post_thumbnail_id( $product->get_id() ) ),
			'attributes'         => $this->get_attributes( $product ),
			'downloads'          => $this->get_downloads( $product ),
			'download_limit'     => $product->get_download_limit(),
			'download_expiry'    => $product->get_download_expiry(),
			'download_type'      => 'standard',
			'purchase_note'      => wpautop( do_shortcode( wp_kses_post( $product->get_purchase_note() ) ) ),
			'total_sales'        => $product->get_total_sales(),
			'variations'         => array(),
			'parent'             => array(),
		);
	}

	/**
	 * Get an individual variation's data
	 *
	 * @since 2.1
	 * @param WC_Product $product
	 * @return array
	 */
	private function get_variation_data( $product ) {
		$prices_precision = wc_get_price_decimals();
		$variations       = array();

		foreach ( $product->get_children() as $child_id ) {

			$variation = wc_get_product( $child_id );

			if ( ! $variation || ! $variation->exists() ) {
				continue;
			}

			$variations[] = array(
				'id'                => $variation->get_id(),
				'created_at'        => $this->server->format_datetime( $variation->get_date_created(), false, true ),
				'updated_at'        => $this->server->format_datetime( $variation->get_date_modified(), false, true ),
				'downloadable'      => $variation->is_downloadable(),
				'virtual'           => $variation->is_virtual(),
				'permalink'         => $variation->get_permalink(),
				'sku'               => $variation->get_sku(),
				'price'             => wc_format_decimal( $variation->get_price(), $prices_precision ),
				'regular_price'     => wc_format_decimal( $variation->get_regular_price(), $prices_precision ),
				'sale_price'        => $variation->get_sale_price() ? wc_format_decimal( $variation->get_sale_price(), $prices_precision ) : null,
				'taxable'           => $variation->is_taxable(),
				'tax_status'        => $variation->get_tax_status(),
				'tax_class'         => $variation->get_tax_class(),
				'managing_stock'    => $variation->managing_stock(),
				'stock_quantity'    => (int) $variation->get_stock_quantity(),
				'in_stock'          => $variation->is_in_stock(),
				'backordered'       => $variation->is_on_backorder(),
				'purchaseable'      => $variation->is_purchasable(),
				'visible'           => $variation->variation_is_visible(),
				'on_sale'           => $variation->is_on_sale(),
				'weight'            => $variation->get_weight() ? wc_format_decimal( $variation->get_weight(), 2 ) : null,
				'dimensions'        => array(
					'length' => $variation->get_length(),
					'width'  => $variation->get_width(),
					'height' => $variation->get_height(),
					'unit'   => get_option( 'woocommerce_dimension_unit' ),
				),
				'shipping_class'    => $variation->get_shipping_class(),
				'shipping_class_id' => ( 0 !== $variation->get_shipping_class_id() ) ? $variation->get_shipping_class_id() : null,
				'image'             => $this->get_images( $variation ),
				'attributes'        => $this->get_attributes( $variation ),
				'downloads'         => $this->get_downloads( $variation ),
				'download_limit'    => (int) $product->get_download_limit(),
				'download_expiry'   => (int) $product->get_download_expiry(),
			);
		}

		return $variations;
	}

	/**
	 * Save default attributes.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product
	 * @param array $request
	 * @return WC_Product
	 */
	protected function save_default_attributes( $product, $request ) {
		// Update default attributes options setting.
		if ( isset( $request['default_attribute'] ) ) {
			$request['default_attributes'] = $request['default_attribute'];
		}

		if ( isset( $request['default_attributes'] ) && is_array( $request['default_attributes'] ) ) {
			$attributes         = $product->get_attributes();
			$default_attributes = array();

			foreach ( $request['default_attributes'] as $default_attr_key => $default_attr ) {
				if ( ! isset( $default_attr['name'] ) ) {
					continue;
				}

				$taxonomy = sanitize_title( $default_attr['name'] );

				if ( isset( $default_attr['slug'] ) ) {
					$taxonomy = $this->get_attribute_taxonomy_by_slug( $default_attr['slug'] );
				}

				if ( isset( $attributes[ $taxonomy ] ) ) {
					$_attribute = $attributes[ $taxonomy ];

					if ( $_attribute['is_variation'] ) {
						$value = '';

						if ( isset( $default_attr['option'] ) ) {
							if ( $_attribute['is_taxonomy'] ) {
								// Don't use wc_clean as it destroys sanitized characters.
								$value = sanitize_title( trim( stripslashes( $default_attr['option'] ) ) );
							} else {
								$value = wc_clean( trim( stripslashes( $default_attr['option'] ) ) );
							}
						}

						if ( $value ) {
							$default_attributes[ $taxonomy ] = $value;
						}
					}
				}
			}

			$product->set_default_attributes( $default_attributes );
		}

		return $product;
	}

	/**
	 * Save product meta
	 *
	 * @since  2.2
	 * @param  WC_Product $product
	 * @param  array $data
	 * @return WC_Product
	 * @throws WC_API_Exception
	 */
	protected function save_product_meta( $product, $data ) {
		global $wpdb;

		// Virtual
		if ( isset( $data['virtual'] ) ) {
			$product->set_virtual( $data['virtual'] );
		}

		// Tax status
		if ( isset( $data['tax_status'] ) ) {
			$product->set_tax_status( wc_clean( $data['tax_status'] ) );
		}

		// Tax Class
		if ( isset( $data['tax_class'] ) ) {
			$product->set_tax_class( wc_clean( $data['tax_class'] ) );
		}

		// Catalog Visibility
		if ( isset( $data['catalog_visibility'] ) ) {
			$product->set_catalog_visibility( wc_clean( $data['catalog_visibility'] ) );
		}

		// Purchase Note
		if ( isset( $data['purchase_note'] ) ) {
			$product->set_purchase_note( wc_clean( $data['purchase_note'] ) );
		}

		// Featured Product
		if ( isset( $data['featured'] ) ) {
			$product->set_featured( $data['featured'] );
		}

		// Shipping data
		$product = $this->save_product_shipping_data( $product, $data );

		// SKU
		if ( isset( $data['sku'] ) ) {
			$sku     = $product->get_sku();
			$new_sku = wc_clean( $data['sku'] );

			if ( '' == $new_sku ) {
				$product->set_sku( '' );
			} elseif ( $new_sku !== $sku ) {
				if ( ! empty( $new_sku ) ) {
					$unique_sku = wc_product_has_unique_sku( $product->get_id(), $new_sku );
					if ( ! $unique_sku ) {
						throw new WC_API_Exception( 'woocommerce_api_product_sku_already_exists', __( 'The SKU already exists on another product.', 'woocommerce' ), 400 );
					} else {
						$product->set_sku( $new_sku );
					}
				} else {
					$product->set_sku( '' );
				}
			}
		}

		// Attributes
		if ( isset( $data['attributes'] ) ) {
			$attributes = array();

			foreach ( $data['attributes'] as $attribute ) {
				$is_taxonomy = 0;
				$taxonomy    = 0;

				if ( ! isset( $attribute['name'] ) ) {
					continue;
				}

				$attribute_slug = sanitize_title( $attribute['name'] );

				if ( isset( $attribute['slug'] ) ) {
					$taxonomy       = $this->get_attribute_taxonomy_by_slug( $attribute['slug'] );
					$attribute_slug = sanitize_title( $attribute['slug'] );
				}

				if ( $taxonomy ) {
					$is_taxonomy = 1;
				}

				if ( $is_taxonomy ) {

					$attribute_id = wc_attribute_taxonomy_id_by_name( $attribute['name'] );

					if ( isset( $attribute['options'] ) ) {
						$options = $attribute['options'];

						if ( ! is_array( $attribute['options'] ) ) {
							// Text based attributes - Posted values are term names
							$options = explode( WC_DELIMITER, $options );
						}

						$values = array_map( 'wc_sanitize_term_text_based', $options );
						$values = array_filter( $values, 'strlen' );
					} else {
						$values = array();
					}

					// Update post terms
					if ( taxonomy_exists( $taxonomy ) ) {
						wp_set_object_terms( $product->get_id(), $values, $taxonomy );
					}

					if ( ! empty( $values ) ) {
						// Add attribute to array, but don't set values.
						$attribute_object = new WC_Product_Attribute();
						$attribute_object->set_id( $attribute_id );
						$attribute_object->set_name( $taxonomy );
						$attribute_object->set_options( $values );
						$attribute_object->set_position( isset( $attribute['position'] ) ? absint( $attribute['position'] ) : 0 );
						$attribute_object->set_visible( ( isset( $attribute['visible'] ) && $attribute['visible'] ) ? 1 : 0 );
						$attribute_object->set_variation( ( isset( $attribute['variation'] ) && $attribute['variation'] ) ? 1 : 0 );
						$attributes[] = $attribute_object;
					}
				} elseif ( isset( $attribute['options'] ) ) {
					// Array based
					if ( is_array( $attribute['options'] ) ) {
						$values = $attribute['options'];

					// Text based, separate by pipe
					} else {
						$values = array_map( 'wc_clean', explode( WC_DELIMITER, $attribute['options'] ) );
					}

					// Custom attribute - Add attribute to array and set the values.
					$attribute_object = new WC_Product_Attribute();
					$attribute_object->set_name( $attribute['name'] );
					$attribute_object->set_options( $values );
					$attribute_object->set_position( isset( $attribute['position'] ) ? absint( $attribute['position'] ) : 0 );
					$attribute_object->set_visible( ( isset( $attribute['visible'] ) && $attribute['visible'] ) ? 1 : 0 );
					$attribute_object->set_variation( ( isset( $attribute['variation'] ) && $attribute['variation'] ) ? 1 : 0 );
					$attributes[] = $attribute_object;
				}
			}

			uasort( $attributes, 'wc_product_attribute_uasort_comparison' );

			$product->set_attributes( $attributes );
		}

		// Sales and prices.
		if ( in_array( $product->get_type(), array( 'variable', 'grouped' ) ) ) {

			// Variable and grouped products have no prices.
			$product->set_regular_price( '' );
			$product->set_sale_price( '' );
			$product->set_date_on_sale_to( '' );
			$product->set_date_on_sale_from( '' );
			$product->set_price( '' );

		} else {

			// Regular Price.
			if ( isset( $data['regular_price'] ) ) {
				$regular_price = ( '' === $data['regular_price'] ) ? '' : $data['regular_price'];
				$product->set_regular_price( $regular_price );
			}

			// Sale Price.
			if ( isset( $data['sale_price'] ) ) {
				$sale_price = ( '' === $data['sale_price'] ) ? '' : $data['sale_price'];
				$product->set_sale_price( $sale_price );
			}

			if ( isset( $data['sale_price_dates_from'] ) ) {
				$date_from = $data['sale_price_dates_from'];
			} else {
				$date_from = $product->get_date_on_sale_from() ? date( 'Y-m-d', $product->get_date_on_sale_from()->getTimestamp() ) : '';
			}

			if ( isset( $data['sale_price_dates_to'] ) ) {
				$date_to = $data['sale_price_dates_to'];
			} else {
				$date_to = $product->get_date_on_sale_to() ? date( 'Y-m-d', $product->get_date_on_sale_to()->getTimestamp() ) : '';
			}

			if ( $date_to && ! $date_from ) {
				$date_from = strtotime( 'NOW', current_time( 'timestamp', true ) );
			}

			$product->set_date_on_sale_to( $date_to );
			$product->set_date_on_sale_from( $date_from );

			if ( $product->is_on_sale( 'edit' ) ) {
				$product->set_price( $product->get_sale_price( 'edit' ) );
			} else {
				$product->set_price( $product->get_regular_price( 'edit' ) );
			}
		}

		// Product parent ID for groups
		if ( isset( $data['parent_id'] ) ) {
			$product->set_parent_id( absint( $data['parent_id'] ) );
		}

		// Sold Individually
		if ( isset( $data['sold_individually'] ) ) {
			$product->set_sold_individually( true === $data['sold_individually'] ? 'yes' : '' );
		}

		// Stock status
		if ( isset( $data['in_stock'] ) ) {
			$stock_status = ( true === $data['in_stock'] ) ? 'instock' : 'outofstock';
		} else {
			$stock_status = $product->get_stock_status();

			if ( '' === $stock_status ) {
				$stock_status = 'instock';
			}
		}

		// Stock Data
		if ( 'yes' == get_option( 'woocommerce_manage_stock' ) ) {
			// Manage stock
			if ( isset( $data['managing_stock'] ) ) {
				$managing_stock = ( true === $data['managing_stock'] ) ? 'yes' : 'no';
				$product->set_manage_stock( $managing_stock );
			} else {
				$managing_stock = $product->get_manage_stock() ? 'yes' : 'no';
			}

			// Backorders
			if ( isset( $data['backorders'] ) ) {
				if ( 'notify' == $data['backorders'] ) {
					$backorders = 'notify';
				} else {
					$backorders = ( true === $data['backorders'] ) ? 'yes' : 'no';
				}

				$product->set_backorders( $backorders );
			} else {
				$backorders = $product->get_backorders();
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
			} elseif ( 'yes' == $managing_stock ) {
				$product->set_backorders( $backorders );

				// Stock status is always determined by children so sync later.
				if ( ! $product->is_type( 'variable' ) ) {
					$product->set_stock_status( $stock_status );
				}

				// Stock quantity
				if ( isset( $data['stock_quantity'] ) ) {
					$product->set_stock_quantity( wc_stock_amount( $data['stock_quantity'] ) );
				}
			} else {
				// Don't manage stock.
				$product->set_manage_stock( 'no' );
				$product->set_backorders( $backorders );
				$product->set_stock_quantity( '' );
				$product->set_stock_status( $stock_status );
			}
		} elseif ( ! $product->is_type( 'variable' ) ) {
			$product->set_stock_status( $stock_status );
		}

		// Upsells
		if ( isset( $data['upsell_ids'] ) ) {
			$upsells = array();
			$ids     = $data['upsell_ids'];

			if ( ! empty( $ids ) ) {
				foreach ( $ids as $id ) {
					if ( $id && $id > 0 ) {
						$upsells[] = $id;
					}
				}

				$product->set_upsell_ids( $upsells );
			} else {
				$product->set_upsell_ids( array() );
			}
		}

		// Cross sells
		if ( isset( $data['cross_sell_ids'] ) ) {
			$crosssells = array();
			$ids        = $data['cross_sell_ids'];

			if ( ! empty( $ids ) ) {
				foreach ( $ids as $id ) {
					if ( $id && $id > 0 ) {
						$crosssells[] = $id;
					}
				}

				$product->set_cross_sell_ids( $crosssells );
			} else {
				$product->set_cross_sell_ids( array() );
			}
		}

		// Product categories
		if ( isset( $data['categories'] ) && is_array( $data['categories'] ) ) {
			$product->set_category_ids( $data['categories'] );
		}

		// Product tags
		if ( isset( $data['tags'] ) && is_array( $data['tags'] ) ) {
			$product->set_tag_ids( $data['tags'] );
		}

		// Downloadable
		if ( isset( $data['downloadable'] ) ) {
			$is_downloadable = ( true === $data['downloadable'] ) ? 'yes' : 'no';
			$product->set_downloadable( $is_downloadable );
		} else {
			$is_downloadable = $product->get_downloadable() ? 'yes' : 'no';
		}

		// Downloadable options
		if ( 'yes' == $is_downloadable ) {

			// Downloadable files
			if ( isset( $data['downloads'] ) && is_array( $data['downloads'] ) ) {
				$product = $this->save_downloadable_files( $product, $data['downloads'] );
			}

			// Download limit
			if ( isset( $data['download_limit'] ) ) {
				$product->set_download_limit( $data['download_limit'] );
			}

			// Download expiry
			if ( isset( $data['download_expiry'] ) ) {
				$product->set_download_expiry( $data['download_expiry'] );
			}
		}

		// Product url
		if ( $product->is_type( 'external' ) ) {
			if ( isset( $data['product_url'] ) ) {
				$product->set_product_url( $data['product_url'] );
			}

			if ( isset( $data['button_text'] ) ) {
				$product->set_button_text( $data['button_text'] );
			}
		}

		// Reviews allowed
		if ( isset( $data['reviews_allowed'] ) ) {
			$product->set_reviews_allowed( $data['reviews_allowed'] );
		}

		// Save default attributes for variable products.
		if ( $product->is_type( 'variable' ) ) {
			$product = $this->save_default_attributes( $product, $data );
		}

		// Do action for product type
		do_action( 'woocommerce_api_process_product_meta_' . $product->get_type(), $product->get_id(), $data );

		return $product;
	}

	/**
	 * Save variations
	 *
	 * @since  2.2
	 * @param  WC_Product $product
	 * @param  array $request
	 *
	 * @return true
	 *
	 * @throws WC_API_Exception
	 */
	protected function save_variations( $product, $request ) {
		global $wpdb;

		$id         = $product->get_id();
		$attributes = $product->get_attributes();

		foreach ( $request['variations'] as $menu_order => $data ) {
			$variation_id = isset( $data['id'] ) ? absint( $data['id'] ) : 0;
			$variation    = new WC_Product_Variation( $variation_id );

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
				$image = current( $data['image'] );
				if ( is_array( $image ) ) {
					$image['position'] = 0;
				}

				$variation = $this->save_product_images( $variation, array( $image ) );
			}

			// Virtual variation.
			if ( isset( $data['virtual'] ) ) {
				$variation->set_virtual( $data['virtual'] );
			}

			// Downloadable variation.
			if ( isset( $data['downloadable'] ) ) {
				$is_downloadable = $data['downloadable'];
				$variation->set_downloadable( $is_downloadable );
			} else {
				$is_downloadable = $variation->get_downloadable();
			}

			// Downloads.
			if ( $is_downloadable ) {
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
			$manage_stock = (bool) $variation->get_manage_stock();
			if ( isset( $data['managing_stock'] ) ) {
				$manage_stock = $data['managing_stock'];
			}
			$variation->set_manage_stock( $manage_stock );

			$stock_status = $variation->get_stock_status();
			if ( isset( $data['in_stock'] ) ) {
				$stock_status = true === $data['in_stock'] ? 'instock' : 'outofstock';
			}
			$variation->set_stock_status( $stock_status );

			$backorders = $variation->get_backorders();
			if ( isset( $data['backorders'] ) ) {
				$backorders = $data['backorders'];
			}
			$variation->set_backorders( $backorders );

			if ( $manage_stock ) {
				if ( isset( $data['stock_quantity'] ) ) {
					$variation->set_stock_quantity( $data['stock_quantity'] );
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

			if ( isset( $data['sale_price_dates_from'] ) ) {
				$variation->set_date_on_sale_from( $data['sale_price_dates_from'] );
			}

			if ( isset( $data['sale_price_dates_to'] ) ) {
				$variation->set_date_on_sale_to( $data['sale_price_dates_to'] );
			}

			// Tax class.
			if ( isset( $data['tax_class'] ) ) {
				$variation->set_tax_class( $data['tax_class'] );
			}

			// Update taxonomies.
			if ( isset( $data['attributes'] ) ) {
				$_attributes = array();

				foreach ( $data['attributes'] as $attribute_key => $attribute ) {
					if ( ! isset( $attribute['name'] ) ) {
						continue;
					}

					$taxonomy   = 0;
					$_attribute = array();

					if ( isset( $attribute['slug'] ) ) {
						$taxonomy = $this->get_attribute_taxonomy_by_slug( $attribute['slug'] );
					}

					if ( ! $taxonomy ) {
						$taxonomy = sanitize_title( $attribute['name'] );
					}

					if ( isset( $attributes[ $taxonomy ] ) ) {
						$_attribute = $attributes[ $taxonomy ];
					}

					if ( isset( $_attribute['is_variation'] ) && $_attribute['is_variation'] ) {
						$_attribute_key = sanitize_title( $_attribute['name'] );

						if ( isset( $_attribute['is_taxonomy'] ) && $_attribute['is_taxonomy'] ) {
							// Don't use wc_clean as it destroys sanitized characters
							$_attribute_value = isset( $attribute['option'] ) ? sanitize_title( stripslashes( $attribute['option'] ) ) : '';
						} else {
							$_attribute_value = isset( $attribute['option'] ) ? wc_clean( stripslashes( $attribute['option'] ) ) : '';
						}

						$_attributes[ $_attribute_key ] = $_attribute_value;
					}
				}

				$variation->set_attributes( $_attributes );
			}

			$variation->save();

			do_action( 'woocommerce_api_save_product_variation', $variation_id, $menu_order, $variation );
		}

		return true;
	}

	/**
	 * Save product shipping data
	 *
	 * @since 2.2
	 * @param WC_Product $product
	 * @param array $data
	 * @return WC_Product
	 */
	private function save_product_shipping_data( $product, $data ) {
		if ( isset( $data['weight'] ) ) {
			$product->set_weight( '' === $data['weight'] ? '' : wc_format_decimal( $data['weight'] ) );
		}

		// Product dimensions
		if ( isset( $data['dimensions'] ) ) {
			// Height
			if ( isset( $data['dimensions']['height'] ) ) {
				$product->set_height( '' === $data['dimensions']['height'] ? '' : wc_format_decimal( $data['dimensions']['height'] ) );
			}

			// Width
			if ( isset( $data['dimensions']['width'] ) ) {
				$product->set_width( '' === $data['dimensions']['width'] ? '' : wc_format_decimal( $data['dimensions']['width'] ) );
			}

			// Length
			if ( isset( $data['dimensions']['length'] ) ) {
				$product->set_length( '' === $data['dimensions']['length'] ? '' : wc_format_decimal( $data['dimensions']['length'] ) );
			}
		}

		// Virtual
		if ( isset( $data['virtual'] ) ) {
			$virtual = ( true === $data['virtual'] ) ? 'yes' : 'no';

			if ( 'yes' == $virtual ) {
				$product->set_weight( '' );
				$product->set_height( '' );
				$product->set_length( '' );
				$product->set_width( '' );
			}
		}

		// Shipping class
		if ( isset( $data['shipping_class'] ) ) {
			$data_store        = $product->get_data_store();
			$shipping_class_id = $data_store->get_shipping_class_id_by_slug( wc_clean( $data['shipping_class'] ) );
			$product->set_shipping_class_id( $shipping_class_id );
		}

		return $product;
	}

	/**
	 * Save downloadable files
	 *
	 * @since 2.2
	 * @param WC_Product $product
	 * @param array $downloads
	 * @param int $deprecated Deprecated since 3.0.
	 * @return WC_Product
	 */
	private function save_downloadable_files( $product, $downloads, $deprecated = 0 ) {
		if ( $deprecated ) {
			wc_deprecated_argument( 'variation_id', '3.0', 'save_downloadable_files() does not require a variation_id anymore.' );
		}

		$files = array();
		foreach ( $downloads as $key => $file ) {
			if ( isset( $file['url'] ) ) {
				$file['file'] = $file['url'];
			}

			if ( empty( $file['file'] ) ) {
				continue;
			}

			$download = new WC_Product_Download();
			$download->set_id( ! empty( $file['id'] ) ? $file['id'] : wp_generate_uuid4() );
			$download->set_name( $file['name'] ? $file['name'] : wc_get_filename_from_url( $file['file'] ) );
			$download->set_file( apply_filters( 'woocommerce_file_download_path', $file['file'], $product, $key ) );
			$files[]  = $download;
		}
		$product->set_downloads( $files );

		return $product;
	}

	/**
	 * Get attribute taxonomy by slug.
	 *
	 * @since 2.2
	 * @param string $slug
	 * @return string|null
	 */
	private function get_attribute_taxonomy_by_slug( $slug ) {
		$taxonomy = null;
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		foreach ( $attribute_taxonomies as $key => $tax ) {
			if ( $slug == $tax->attribute_name ) {
				$taxonomy = 'pa_' . $tax->attribute_name;

				break;
			}
		}

		return $taxonomy;
	}

	/**
	 * Get the images for a product or product variation
	 *
	 * @since 2.1
	 * @param WC_Product|WC_Product_Variation $product
	 * @return array
	 */
	private function get_images( $product ) {
		$images        = $attachment_ids = array();
		$product_image = $product->get_image_id();

		// Add featured image.
		if ( ! empty( $product_image ) ) {
			$attachment_ids[] = $product_image;
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
				'id'         => (int) $attachment_id,
				'created_at' => $this->server->format_datetime( $attachment_post->post_date_gmt ),
				'updated_at' => $this->server->format_datetime( $attachment_post->post_modified_gmt ),
				'src'        => current( $attachment ),
				'title'      => get_the_title( $attachment_id ),
				'alt'        => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
				'position'   => (int) $position,
			);
		}

		// Set a placeholder image if the product has no images set.
		if ( empty( $images ) ) {

			$images[] = array(
				'id'         => 0,
				'created_at' => $this->server->format_datetime( time() ), // Default to now.
				'updated_at' => $this->server->format_datetime( time() ),
				'src'        => wc_placeholder_img_src(),
				'title'      => __( 'Placeholder', 'woocommerce' ),
				'alt'        => __( 'Placeholder', 'woocommerce' ),
				'position'   => 0,
			);
		}

		return $images;
	}

	/**
	 * Save product images
	 *
	 * @since  2.2
	 *
	 * @param WC_Product $product
	 * @param array      $images
	 *
	 * @return WC_Product
	 * @throws WC_API_Exception
	 */
	protected function save_product_images( $product, $images ) {
		if ( is_array( $images ) ) {
			$gallery = array();

			foreach ( $images as $image ) {
				if ( isset( $image['position'] ) && 0 == $image['position'] ) {
					$attachment_id = isset( $image['id'] ) ? absint( $image['id'] ) : 0;

					if ( 0 === $attachment_id && isset( $image['src'] ) ) {
						$upload = $this->upload_product_image( esc_url_raw( $image['src'] ) );

						if ( is_wp_error( $upload ) ) {
							throw new WC_API_Exception( 'woocommerce_api_cannot_upload_product_image', $upload->get_error_message(), 400 );
						}

						$attachment_id = $this->set_product_image_as_attachment( $upload, $product->get_id() );
					}

					$product->set_image_id( $attachment_id );
				} else {
					$attachment_id = isset( $image['id'] ) ? absint( $image['id'] ) : 0;

					if ( 0 === $attachment_id && isset( $image['src'] ) ) {
						$upload = $this->upload_product_image( esc_url_raw( $image['src'] ) );

						if ( is_wp_error( $upload ) ) {
							throw new WC_API_Exception( 'woocommerce_api_cannot_upload_product_image', $upload->get_error_message(), 400 );
						}

						$gallery[] = $this->set_product_image_as_attachment( $upload, $product->get_id() );
					} else {
						$gallery[] = $attachment_id;
					}
				}
			}

			if ( ! empty( $gallery ) ) {
				$product->set_gallery_image_ids( $gallery );
			}
		} else {
			$product->set_image_id( '' );
			$product->set_gallery_image_ids( array() );
		}

		return $product;
	}

	/**
	 * Upload image from URL
	 *
	 * @since  2.2
	 *
	 * @param  string $image_url
	 *
	 * @return array
	 *
	 * @throws WC_API_Exception
	 */
	public function upload_product_image( $image_url ) {
		$upload = wc_rest_upload_image_from_url( $image_url );
		if ( is_wp_error( $upload ) ) {
			throw new WC_API_Exception( 'woocommerce_api_product_image_upload_error', $upload->get_error_message(), 400 );
		}

		return $upload;
	}

	/**
	 * Sets product image as attachment and returns the attachment ID.
	 *
	 * @since 2.2
	 * @param array $upload
	 * @param int $id
	 * @return int
	 */
	protected function set_product_image_as_attachment( $upload, $id ) {
		$info    = wp_check_filetype( $upload['file'] );
		$title   = '';
		$content = '';

		if ( $image_meta = @wp_read_image_metadata( $upload['file'] ) ) {
			if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
				$title = wc_clean( $image_meta['title'] );
			}
			if ( trim( $image_meta['caption'] ) ) {
				$content = wc_clean( $image_meta['caption'] );
			}
		}

		$attachment = array(
			'post_mime_type' => $info['type'],
			'guid'           => $upload['url'],
			'post_parent'    => $id,
			'post_title'     => $title,
			'post_content'   => $content,
		);

		$attachment_id = wp_insert_attachment( $attachment, $upload['file'], $id );
		if ( ! is_wp_error( $attachment_id ) ) {
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
		}

		return $attachment_id;
	}

	/**
	 * Get attribute options.
	 *
	 * @param int $product_id
	 * @param array $attribute
	 * @return array
	 */
	protected function get_attribute_options( $product_id, $attribute ) {
		if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {
			return wc_get_product_terms( $product_id, $attribute['name'], array( 'fields' => 'names' ) );
		} elseif ( isset( $attribute['value'] ) ) {
			return array_map( 'trim', explode( '|', $attribute['value'] ) );
		}

		return array();
	}

	/**
	 * Get the attributes for a product or product variation
	 *
	 * @since 2.1
	 * @param WC_Product|WC_Product_Variation $product
	 * @return array
	 */
	private function get_attributes( $product ) {

		$attributes = array();

		if ( $product->is_type( 'variation' ) ) {

			// variation attributes
			foreach ( $product->get_variation_attributes() as $attribute_name => $attribute ) {

				// taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`
				$attributes[] = array(
					'name'   => wc_attribute_label( str_replace( 'attribute_', '', $attribute_name ) ),
					'slug'   => str_replace( 'attribute_', '', wc_attribute_taxonomy_slug( $attribute_name ) ),
					'option' => $attribute,
				);
			}
		} else {

			foreach ( $product->get_attributes() as $attribute ) {
				$attributes[] = array(
					'name'      => wc_attribute_label( $attribute['name'] ),
					'slug'      => wc_attribute_taxonomy_slug( $attribute['name'] ),
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
	 * Get the downloads for a product or product variation
	 *
	 * @since 2.1
	 * @param WC_Product|WC_Product_Variation $product
	 * @return array
	 */
	private function get_downloads( $product ) {

		$downloads = array();

		if ( $product->is_downloadable() ) {

			foreach ( $product->get_downloads() as $file_id => $file ) {

				$downloads[] = array(
					'id'   => $file_id, // do not cast as int as this is a hash
					'name' => $file['name'],
					'file' => $file['file'],
				);
			}
		}

		return $downloads;
	}

	/**
	 * Get a listing of product attributes
	 *
	 * @since 2.4.0
	 *
	 * @param string|null $fields fields to limit response to
	 *
	 * @return array|WP_Error
	 */
	public function get_product_attributes( $fields = null ) {
		try {
			// Permissions check
			if ( ! current_user_can( 'manage_product_terms' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_product_attributes', __( 'You do not have permission to read product attributes', 'woocommerce' ), 401 );
			}

			$product_attributes   = array();
			$attribute_taxonomies = wc_get_attribute_taxonomies();

			foreach ( $attribute_taxonomies as $attribute ) {
				$product_attributes[] = array(
					'id'           => intval( $attribute->attribute_id ),
					'name'         => $attribute->attribute_label,
					'slug'         => wc_attribute_taxonomy_name( $attribute->attribute_name ),
					'type'         => $attribute->attribute_type,
					'order_by'     => $attribute->attribute_orderby,
					'has_archives' => (bool) $attribute->attribute_public,
				);
			}

			return array( 'product_attributes' => apply_filters( 'woocommerce_api_product_attributes_response', $product_attributes, $attribute_taxonomies, $fields, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get the product attribute for the given ID
	 *
	 * @since 2.4.0
	 *
	 * @param string $id product attribute term ID
	 * @param string|null $fields fields to limit response to
	 *
	 * @return array|WP_Error
	 */
	public function get_product_attribute( $id, $fields = null ) {
		global $wpdb;

		try {
			$id = absint( $id );

			// Validate ID
			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_product_attribute_id', __( 'Invalid product attribute ID', 'woocommerce' ), 400 );
			}

			// Permissions check
			if ( ! current_user_can( 'manage_product_terms' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_read_product_categories', __( 'You do not have permission to read product attributes', 'woocommerce' ), 401 );
			}

			$attribute = $wpdb->get_row( $wpdb->prepare( "
				SELECT *
				FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
				WHERE attribute_id = %d
			 ", $id ) );

			if ( is_wp_error( $attribute ) || is_null( $attribute ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_product_attribute_id', __( 'A product attribute with the provided ID could not be found', 'woocommerce' ), 404 );
			}

			$product_attribute = array(
				'id'           => intval( $attribute->attribute_id ),
				'name'         => $attribute->attribute_label,
				'slug'         => wc_attribute_taxonomy_name( $attribute->attribute_name ),
				'type'         => $attribute->attribute_type,
				'order_by'     => $attribute->attribute_orderby,
				'has_archives' => (bool) $attribute->attribute_public,
			);

			return array( 'product_attribute' => apply_filters( 'woocommerce_api_product_attribute_response', $product_attribute, $id, $fields, $attribute, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Validate attribute data.
	 *
	 * @since  2.4.0
	 * @param  string $name
	 * @param  string $slug
	 * @param  string $type
	 * @param  string $order_by
	 * @param  bool   $new_data
	 * @return bool
	 * @throws WC_API_Exception
	 */
	protected function validate_attribute_data( $name, $slug, $type, $order_by, $new_data = true ) {
		if ( empty( $name ) ) {
			throw new WC_API_Exception( 'woocommerce_api_missing_product_attribute_name', sprintf( __( 'Missing parameter %s', 'woocommerce' ), 'name' ), 400 );
		}

		if ( strlen( $slug ) > 28 ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_attribute_slug_too_long', sprintf( __( 'Slug "%s" is too long (28 characters max). Shorten it, please.', 'woocommerce' ), $slug ), 400 );
		} elseif ( wc_check_if_attribute_name_is_reserved( $slug ) ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_attribute_slug_reserved_name', sprintf( __( 'Slug "%s" is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), $slug ), 400 );
		} elseif ( $new_data && taxonomy_exists( wc_attribute_taxonomy_name( $slug ) ) ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_attribute_slug_already_exists', sprintf( __( 'Slug "%s" is already in use. Change it, please.', 'woocommerce' ), $slug ), 400 );
		}

		// Validate the attribute type
		if ( ! in_array( wc_clean( $type ), array_keys( wc_get_attribute_types() ) ) ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_attribute_type', sprintf( __( 'Invalid product attribute type - the product attribute type must be any of these: %s', 'woocommerce' ), implode( ', ', array_keys( wc_get_attribute_types() ) ) ), 400 );
		}

		// Validate the attribute order by
		if ( ! in_array( wc_clean( $order_by ), array( 'menu_order', 'name', 'name_num', 'id' ) ) ) {
			throw new WC_API_Exception( 'woocommerce_api_invalid_product_attribute_order_by', sprintf( __( 'Invalid product attribute order_by type - the product attribute order_by type must be any of these: %s', 'woocommerce' ), implode( ', ', array( 'menu_order', 'name', 'name_num', 'id' ) ) ), 400 );
		}

		return true;
	}

	/**
	 * Create a new product attribute
	 *
	 * @since 2.4.0
	 *
	 * @param array $data posted data
	 *
	 * @return array|WP_Error
	 */
	public function create_product_attribute( $data ) {
		global $wpdb;

		try {
			if ( ! isset( $data['product_attribute'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_product_attribute_data', sprintf( __( 'No %1$s data specified to create %1$s', 'woocommerce' ), 'product_attribute' ), 400 );
			}

			$data = $data['product_attribute'];

			// Check permissions
			if ( ! current_user_can( 'manage_product_terms' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_create_product_attribute', __( 'You do not have permission to create product attributes', 'woocommerce' ), 401 );
			}

			$data = apply_filters( 'woocommerce_api_create_product_attribute_data', $data, $this );

			if ( ! isset( $data['name'] ) ) {
				$data['name'] = '';
			}

			// Set the attribute slug
			if ( ! isset( $data['slug'] ) ) {
				$data['slug'] = wc_sanitize_taxonomy_name( stripslashes( $data['name'] ) );
			} else {
				$data['slug'] = preg_replace( '/^pa\_/', '', wc_sanitize_taxonomy_name( stripslashes( $data['slug'] ) ) );
			}

			// Set attribute type when not sent
			if ( ! isset( $data['type'] ) ) {
				$data['type'] = 'select';
			}

			// Set order by when not sent
			if ( ! isset( $data['order_by'] ) ) {
				$data['order_by'] = 'menu_order';
			}

			// Validate the attribute data
			$this->validate_attribute_data( $data['name'], $data['slug'], $data['type'], $data['order_by'], true );

			$insert = $wpdb->insert(
				$wpdb->prefix . 'woocommerce_attribute_taxonomies',
				array(
					'attribute_label'   => $data['name'],
					'attribute_name'    => $data['slug'],
					'attribute_type'    => $data['type'],
					'attribute_orderby' => $data['order_by'],
					'attribute_public'  => isset( $data['has_archives'] ) && true === $data['has_archives'] ? 1 : 0,
				),
				array( '%s', '%s', '%s', '%s', '%d' )
			);

			// Checks for an error in the product creation
			if ( is_wp_error( $insert ) ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_create_product_attribute', $insert->get_error_message(), 400 );
			}

			$id = $wpdb->insert_id;

			do_action( 'woocommerce_api_create_product_attribute', $id, $data );

			// Clear transients
			delete_transient( 'wc_attribute_taxonomies' );
			WC_Cache_Helper::invalidate_cache_group( 'woocommerce-attributes' );

			$this->server->send_status( 201 );

			return $this->get_product_attribute( $id );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Edit a product attribute
	 *
	 * @since 2.4.0
	 *
	 * @param int $id the attribute ID
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function edit_product_attribute( $id, $data ) {
		global $wpdb;

		try {
			if ( ! isset( $data['product_attribute'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_product_attribute_data', sprintf( __( 'No %1$s data specified to edit %1$s', 'woocommerce' ), 'product_attribute' ), 400 );
			}

			$id   = absint( $id );
			$data = $data['product_attribute'];

			// Check permissions
			if ( ! current_user_can( 'manage_product_terms' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_edit_product_attribute', __( 'You do not have permission to edit product attributes', 'woocommerce' ), 401 );
			}

			$data      = apply_filters( 'woocommerce_api_edit_product_attribute_data', $data, $this );
			$attribute = $this->get_product_attribute( $id );

			if ( is_wp_error( $attribute ) ) {
				return $attribute;
			}

			$attribute_name     = isset( $data['name'] ) ? $data['name'] : $attribute['product_attribute']['name'];
			$attribute_type     = isset( $data['type'] ) ? $data['type'] : $attribute['product_attribute']['type'];
			$attribute_order_by = isset( $data['order_by'] ) ? $data['order_by'] : $attribute['product_attribute']['order_by'];

			if ( isset( $data['slug'] ) ) {
				$attribute_slug = wc_sanitize_taxonomy_name( stripslashes( $data['slug'] ) );
			} else {
				$attribute_slug = $attribute['product_attribute']['slug'];
			}
			$attribute_slug = preg_replace( '/^pa\_/', '', $attribute_slug );

			if ( isset( $data['has_archives'] ) ) {
				$attribute_public = true === $data['has_archives'] ? 1 : 0;
			} else {
				$attribute_public = $attribute['product_attribute']['has_archives'];
			}

			// Validate the attribute data
			$this->validate_attribute_data( $attribute_name, $attribute_slug, $attribute_type, $attribute_order_by, false );

			$update = $wpdb->update(
				$wpdb->prefix . 'woocommerce_attribute_taxonomies',
				array(
					'attribute_label'   => $attribute_name,
					'attribute_name'    => $attribute_slug,
					'attribute_type'    => $attribute_type,
					'attribute_orderby' => $attribute_order_by,
					'attribute_public'  => $attribute_public,
				),
				array( 'attribute_id' => $id ),
				array( '%s', '%s', '%s', '%s', '%d' ),
				array( '%d' )
			);

			// Checks for an error in the product creation
			if ( false === $update ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_edit_product_attribute', __( 'Could not edit the attribute', 'woocommerce' ), 400 );
			}

			do_action( 'woocommerce_api_edit_product_attribute', $id, $data );

			// Clear transients
			delete_transient( 'wc_attribute_taxonomies' );
			WC_Cache_Helper::invalidate_cache_group( 'woocommerce-attributes' );

			return $this->get_product_attribute( $id );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Delete a product attribute
	 *
	 * @since  2.4.0
	 *
	 * @param  int $id the product attribute ID
	 *
	 * @return array|WP_Error
	 */
	public function delete_product_attribute( $id ) {
		global $wpdb;

		try {
			// Check permissions
			if ( ! current_user_can( 'manage_product_terms' ) ) {
				throw new WC_API_Exception( 'woocommerce_api_user_cannot_delete_product_attribute', __( 'You do not have permission to delete product attributes', 'woocommerce' ), 401 );
			}

			$id = absint( $id );

			$attribute_name = $wpdb->get_var( $wpdb->prepare( "
				SELECT attribute_name
				FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
				WHERE attribute_id = %d
			 ", $id ) );

			if ( is_null( $attribute_name ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_product_attribute_id', __( 'A product attribute with the provided ID could not be found', 'woocommerce' ), 404 );
			}

			$deleted = $wpdb->delete(
				$wpdb->prefix . 'woocommerce_attribute_taxonomies',
				array( 'attribute_id' => $id ),
				array( '%d' )
			);

			if ( false === $deleted ) {
				throw new WC_API_Exception( 'woocommerce_api_cannot_delete_product_attribute', __( 'Could not delete the attribute', 'woocommerce' ), 401 );
			}

			$taxonomy = wc_attribute_taxonomy_name( $attribute_name );

			if ( taxonomy_exists( $taxonomy ) ) {
				$terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
				foreach ( $terms as $term ) {
					wp_delete_term( $term->term_id, $taxonomy );
				}
			}

			do_action( 'woocommerce_attribute_deleted', $id, $attribute_name, $taxonomy );
			do_action( 'woocommerce_api_delete_product_attribute', $id, $this );

			// Clear transients
			delete_transient( 'wc_attribute_taxonomies' );
			WC_Cache_Helper::invalidate_cache_group( 'woocommerce-attributes' );

			return array( 'message' => sprintf( __( 'Deleted %s', 'woocommerce' ), 'product_attribute' ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get product by SKU
	 *
	 * @deprecated 2.4.0
	 *
	 * @since  2.3.0
	 *
	 * @param  int    $sku the product SKU
	 * @param  string $fields
	 *
	 * @return array|WP_Error
	 */
	public function get_product_by_sku( $sku, $fields = null ) {
		try {
			$id = wc_get_product_id_by_sku( $sku );

			if ( empty( $id ) ) {
				throw new WC_API_Exception( 'woocommerce_api_invalid_product_sku', __( 'Invalid product SKU', 'woocommerce' ), 404 );
			}

			return $this->get_product( $id, $fields );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Clear product
	 *
	 * @param int $product_id
	 */
	protected function clear_product( $product_id ) {
		if ( ! is_numeric( $product_id ) || 0 >= $product_id ) {
			return;
		}

		// Delete product attachments
		$attachments = get_children( array(
			'post_parent' => $product_id,
			'post_status' => 'any',
			'post_type'   => 'attachment',
		) );

		foreach ( (array) $attachments as $attachment ) {
			wp_delete_attachment( $attachment->ID, true );
		}

		// Delete product
		$product = wc_get_product( $product_id );
		$product->delete();
	}

	/**
	 * Bulk update or insert products
	 * Accepts an array with products in the formats supported by
	 * WC_API_Products->create_product() and WC_API_Products->edit_product()
	 *
	 * @since 2.4.0
	 *
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function bulk( $data ) {

		try {
			if ( ! isset( $data['products'] ) ) {
				throw new WC_API_Exception( 'woocommerce_api_missing_products_data', sprintf( __( 'No %1$s data specified to create/edit %1$s', 'woocommerce' ), 'products' ), 400 );
			}

			$data  = $data['products'];
			$limit = apply_filters( 'woocommerce_api_bulk_limit', 100, 'products' );

			// Limit bulk operation
			if ( count( $data ) > $limit ) {
				throw new WC_API_Exception( 'woocommerce_api_products_request_entity_too_large', sprintf( __( 'Unable to accept more than %s items for this request.', 'woocommerce' ), $limit ), 413 );
			}

			$products = array();

			foreach ( $data as $_product ) {
				$product_id  = 0;
				$product_sku = '';

				// Try to get the product ID
				if ( isset( $_product['id'] ) ) {
					$product_id = intval( $_product['id'] );
				}

				if ( ! $product_id && isset( $_product['sku'] ) ) {
					$product_sku = wc_clean( $_product['sku'] );
					$product_id  = wc_get_product_id_by_sku( $product_sku );
				}

				if ( $product_id ) {

					// Product exists / edit product
					$edit = $this->edit_product( $product_id, array( 'product' => $_product ) );

					if ( is_wp_error( $edit ) ) {
						$products[] = array(
							'id'    => $product_id,
							'sku'   => $product_sku,
							'error' => array( 'code' => $edit->get_error_code(), 'message' => $edit->get_error_message() ),
						);
					} else {
						$products[] = $edit['product'];
					}
				} else {

					// Product don't exists / create product
					$new = $this->create_product( array( 'product' => $_product ) );

					if ( is_wp_error( $new ) ) {
						$products[] = array(
							'id'    => $product_id,
							'sku'   => $product_sku,
							'error' => array( 'code' => $new->get_error_code(), 'message' => $new->get_error_message() ),
						);
					} else {
						$products[] = $new['product'];
					}
				}
			}

			return array( 'products' => apply_filters( 'woocommerce_api_products_bulk_response', $products, $this ) );
		} catch ( WC_API_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}
}

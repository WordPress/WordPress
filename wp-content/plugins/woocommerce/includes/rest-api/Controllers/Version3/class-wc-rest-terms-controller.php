<?php
/**
 * Abstract Rest Terms Controller
 *
 * @package WooCommerce\RestApi
 * @version  3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Internal\AssignDefaultCategory;

/**
 * Terms controller class.
 */
abstract class WC_REST_Terms_Controller extends WC_REST_Controller {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '';

	/**
	 * Taxonomy.
	 *
	 * @var string
	 */
	protected $taxonomy = '';

	/**
	 * Cached taxonomies by attribute id.
	 *
	 * @var array
	 */
	protected $taxonomies_by_id = array();

	/**
	 * Register the routes for terms.
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
					'args'                => array_merge(
						$this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
						array(
							'name' => array(
								'type'        => 'string',
								'description' => __( 'Name for the resource.', 'woocommerce' ),
								'required'    => true,
							),
						)
					),
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
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
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
							'description' => __( 'Required to be true, as resource does not support trashing.', 'woocommerce' ),
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
	 * Check if a given request has access to read the terms.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		$permissions = $this->check_permissions( $request, 'read' );
		if ( is_wp_error( $permissions ) ) {
			return $permissions;
		}

		if ( ! $permissions ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to create a term.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_item_permissions_check( $request ) {
		$permissions = $this->check_permissions( $request, 'create' );
		if ( is_wp_error( $permissions ) ) {
			return $permissions;
		}

		if ( ! $permissions ) {
			return new WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to read a term.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		$permissions = $this->check_permissions( $request, 'read' );
		if ( is_wp_error( $permissions ) ) {
			return $permissions;
		}

		if ( ! $permissions ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to update a term.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function update_item_permissions_check( $request ) {
		$permissions = $this->check_permissions( $request, 'edit' );
		if ( is_wp_error( $permissions ) ) {
			return $permissions;
		}

		if ( ! $permissions ) {
			return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you are not allowed to edit this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to delete a term.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function delete_item_permissions_check( $request ) {
		$permissions = $this->check_permissions( $request, 'delete' );
		if ( is_wp_error( $permissions ) ) {
			return $permissions;
		}

		if ( ! $permissions ) {
			return new WP_Error( 'woocommerce_rest_cannot_delete', __( 'Sorry, you are not allowed to delete this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access batch create, update and delete items.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return boolean|WP_Error
	 */
	public function batch_items_permissions_check( $request ) {
		$permissions = $this->check_permissions( $request, 'batch' );
		if ( is_wp_error( $permissions ) ) {
			return $permissions;
		}

		if ( ! $permissions ) {
			return new WP_Error( 'woocommerce_rest_cannot_batch', __( 'Sorry, you are not allowed to batch manipulate this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check permissions.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @param string          $context Request context.
	 * @return bool|WP_Error
	 */
	protected function check_permissions( $request, $context = 'read' ) {
		// Get taxonomy.
		$taxonomy = $this->get_taxonomy( $request );
		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			return new WP_Error( 'woocommerce_rest_taxonomy_invalid', __( 'Taxonomy does not exist.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		// Check permissions for a single term.
		$id = intval( $request['id'] );
		if ( $id ) {
			$term = get_term( $id, $taxonomy );

			if ( is_wp_error( $term ) || ! $term || $term->taxonomy !== $taxonomy ) {
				return new WP_Error( 'woocommerce_rest_term_invalid', __( 'Resource does not exist.', 'woocommerce' ), array( 'status' => 404 ) );
			}

			return wc_rest_check_product_term_permissions( $taxonomy, $context, $term->term_id );
		}

		return wc_rest_check_product_term_permissions( $taxonomy, $context );
	}

	/**
	 * Get terms associated with a taxonomy.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$taxonomy      = $this->get_taxonomy( $request );
		$prepared_args = array(
			'exclude'    => $request['exclude'],
			'include'    => $request['include'],
			'order'      => $request['order'],
			'orderby'    => $request['orderby'],
			'product'    => $request['product'],
			'hide_empty' => $request['hide_empty'],
			'number'     => $request['per_page'],
			'search'     => $request['search'],
			'slug'       => $request['slug'],
		);

		if ( ! empty( $request['offset'] ) ) {
			$prepared_args['offset'] = $request['offset'];
		} else {
			$prepared_args['offset'] = ( $request['page'] - 1 ) * $prepared_args['number'];
		}

		$taxonomy_obj = get_taxonomy( $taxonomy );

		if ( $taxonomy_obj->hierarchical && isset( $request['parent'] ) ) {
			if ( 0 === $request['parent'] ) {
				// Only query top-level terms.
				$prepared_args['parent'] = 0;
			} else {
				if ( $request['parent'] ) {
					$prepared_args['parent'] = $request['parent'];
				}
			}
		}

		/**
		 * Filter the query arguments, before passing them to `get_terms()`.
		 *
		 * Enables adding extra arguments or setting defaults for a terms
		 * collection request.
		 *
		 * @see https://developer.wordpress.org/reference/functions/get_terms/
		 *
		 * @param array           $prepared_args Array of arguments to be
		 *                                       passed to get_terms.
		 * @param WP_REST_Request $request       The current request.
		 */
		$prepared_args = apply_filters( "woocommerce_rest_{$taxonomy}_query", $prepared_args, $request );

		if ( ! empty( $prepared_args['product'] ) ) {
			$query_result = $this->get_terms_for_product( $prepared_args, $request );
			$total_terms  = $this->total_terms;
		} else {
			$query_result = get_terms( $taxonomy, $prepared_args );

			$count_args = $prepared_args;
			unset( $count_args['number'] );
			unset( $count_args['offset'] );
			$total_terms = wp_count_terms( $taxonomy, $count_args );

			// Ensure we don't return results when offset is out of bounds.
			// See https://core.trac.wordpress.org/ticket/35935.
			if ( $prepared_args['offset'] && $prepared_args['offset'] >= $total_terms ) {
				$query_result = array();
			}

			// wp_count_terms can return a falsy value when the term has no children.
			if ( ! $total_terms ) {
				$total_terms = 0;
			}
		}
		$response = array();
		foreach ( $query_result as $term ) {
			$data       = $this->prepare_item_for_response( $term, $request );
			$response[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $response );

		// Store pagination values for headers then unset for count query.
		$per_page = (int) $prepared_args['number'];
		$page     = ceil( ( ( (int) $prepared_args['offset'] ) / $per_page ) + 1 );

		$response->header( 'X-WP-Total', (int) $total_terms );
		$max_pages = ceil( $total_terms / $per_page );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$base = str_replace( '(?P<attribute_id>[\d]+)', $request['attribute_id'] ?? '', $this->rest_base );
		$base = add_query_arg( $request->get_query_params(), rest_url( '/' . $this->namespace . '/' . $base ) );
		if ( $page > 1 ) {
			$prev_page = $page - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Create a single term for a taxonomy.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function create_item( $request ) {
		$taxonomy = $this->get_taxonomy( $request );
		$name     = $request['name'];
		$args     = array();
		$schema   = $this->get_item_schema();

		if ( ! empty( $schema['properties']['description'] ) && isset( $request['description'] ) ) {
			$args['description'] = $request['description'];
		}
		if ( isset( $request['slug'] ) ) {
			$args['slug'] = $request['slug'];
		}
		if ( isset( $request['parent'] ) ) {
			if ( ! is_taxonomy_hierarchical( $taxonomy ) ) {
				return new WP_Error( 'woocommerce_rest_taxonomy_not_hierarchical', __( 'Can not set resource parent, taxonomy is not hierarchical.', 'woocommerce' ), array( 'status' => 400 ) );
			}
			$args['parent'] = $request['parent'];
		}

		$term = wp_insert_term( $name, $taxonomy, $args );
		if ( is_wp_error( $term ) ) {
			$error_data = array( 'status' => 400 );

			// If we're going to inform the client that the term exists,
			// give them the identifier they can actually use.
			$term_id = $term->get_error_data( 'term_exists' );
			if ( $term_id ) {
				$error_data['resource_id'] = $term_id;
			}

			return new WP_Error( $term->get_error_code(), $term->get_error_message(), $error_data );
		}

		$term = get_term( $term['term_id'], $taxonomy );

		$this->update_additional_fields_for_object( $term, $request );

		// Add term data.
		$meta_fields = $this->update_term_meta_fields( $term, $request );
		if ( is_wp_error( $meta_fields ) ) {
			wp_delete_term( $term->term_id, $taxonomy );

			return $meta_fields;
		}

		/**
		 * Fires after a single term is created or updated via the REST API.
		 *
		 * @param WP_Term         $term      Inserted Term object.
		 * @param WP_REST_Request $request   Request object.
		 * @param boolean         $creating  True when creating term, false when updating.
		 */
		do_action( "woocommerce_rest_insert_{$taxonomy}", $term, $request, true );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $term, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );

		$base = '/' . $this->namespace . '/' . $this->rest_base;
		if ( ! empty( $request['attribute_id'] ) ) {
			$base = str_replace( '(?P<attribute_id>[\d]+)', (int) $request['attribute_id'], $base );
		}

		$response->header( 'Location', rest_url( $base . '/' . $term->term_id ) );

		return $response;
	}

	/**
	 * Get a single term from a taxonomy.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function get_item( $request ) {
		$taxonomy = $this->get_taxonomy( $request );
		$term     = get_term( (int) $request['id'], $taxonomy );

		if ( is_wp_error( $term ) ) {
			return $term;
		}

		$response = $this->prepare_item_for_response( $term, $request );

		return rest_ensure_response( $response );
	}

	/**
	 * Update a single term from a taxonomy.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function update_item( $request ) {
		$taxonomy      = $this->get_taxonomy( $request );
		$term          = get_term( (int) $request['id'], $taxonomy );
		$schema        = $this->get_item_schema();
		$prepared_args = array();

		if ( isset( $request['name'] ) ) {
			$prepared_args['name'] = $request['name'];
		}
		if ( ! empty( $schema['properties']['description'] ) && isset( $request['description'] ) ) {
			$prepared_args['description'] = $request['description'];
		}
		if ( isset( $request['slug'] ) ) {
			$prepared_args['slug'] = $request['slug'];
		}
		if ( isset( $request['parent'] ) ) {
			if ( ! is_taxonomy_hierarchical( $taxonomy ) ) {
				return new WP_Error( 'woocommerce_rest_taxonomy_not_hierarchical', __( 'Can not set resource parent, taxonomy is not hierarchical.', 'woocommerce' ), array( 'status' => 400 ) );
			}
			$prepared_args['parent'] = $request['parent'];
		}

		// Only update the term if we haz something to update.
		if ( ! empty( $prepared_args ) ) {
			$update = wp_update_term( $term->term_id, $term->taxonomy, $prepared_args );
			if ( is_wp_error( $update ) ) {
				return $update;
			}
		}

		$term = get_term( (int) $request['id'], $taxonomy );

		$this->update_additional_fields_for_object( $term, $request );

		// Update term data.
		$meta_fields = $this->update_term_meta_fields( $term, $request );
		if ( is_wp_error( $meta_fields ) ) {
			return $meta_fields;
		}

		/**
		 * Fires after a single term is created or updated via the REST API.
		 *
		 * @param WP_Term         $term      Inserted Term object.
		 * @param WP_REST_Request $request   Request object.
		 * @param boolean         $creating  True when creating term, false when updating.
		 */
		do_action( "woocommerce_rest_insert_{$taxonomy}", $term, $request, false );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $term, $request );
		return rest_ensure_response( $response );
	}

	/**
	 * Delete a single term from a taxonomy.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_item( $request ) {
		$taxonomy = $this->get_taxonomy( $request );
		$force    = isset( $request['force'] ) ? (bool) $request['force'] : false;

		// We don't support trashing for this type, error out.
		if ( ! $force ) {
			return new WP_Error( 'woocommerce_rest_trash_not_supported', __( 'Resource does not support trashing.', 'woocommerce' ), array( 'status' => 501 ) );
		}

		$term = get_term( (int) $request['id'], $taxonomy );
		// Get default category id.
		$default_category_id = absint( get_option( 'default_product_cat', 0 ) );

		// Prevent deleting the default product category.
		if ( $default_category_id === (int) $request['id'] ) {
			return new WP_Error( 'woocommerce_rest_cannot_delete', __( 'Default product category cannot be deleted.', 'woocommerce' ), array( 'status' => 500 ) );
		}

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $term, $request );

		$retval = wp_delete_term( $term->term_id, $term->taxonomy );
		if ( ! $retval ) {
			return new WP_Error( 'woocommerce_rest_cannot_delete', __( 'The resource cannot be deleted.', 'woocommerce' ), array( 'status' => 500 ) );
		}

		// Schedule action to assign default category.
		wc_get_container()->get( AssignDefaultCategory::class )->schedule_action();

		/**
		 * Fires after a single term is deleted via the REST API.
		 *
		 * @param WP_Term          $term     The deleted term.
		 * @param WP_REST_Response $response The response data.
		 * @param WP_REST_Request  $request  The request sent to the API.
		 */
		do_action( "woocommerce_rest_delete_{$taxonomy}", $term, $response, $request );

		return $response;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param object          $term   Term object.
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array Links for the given term.
	 */
	protected function prepare_links( $term, $request ) {
		$base = '/' . $this->namespace . '/' . $this->rest_base;

		if ( ! empty( $request['attribute_id'] ) ) {
			$base = str_replace( '(?P<attribute_id>[\d]+)', (int) $request['attribute_id'], $base );
		}

		$links = array(
			'self'       => array(
				'href' => rest_url( trailingslashit( $base ) . $term->term_id ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
		);

		if ( $term->parent ) {
			$parent_term = get_term( (int) $term->parent, $term->taxonomy );
			if ( $parent_term ) {
				$links['up'] = array(
					'href' => rest_url( trailingslashit( $base ) . $parent_term->term_id ),
				);
			}
		}

		return $links;
	}

	/**
	 * Update term meta fields.
	 *
	 * @param WP_Term         $term    Term object.
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	protected function update_term_meta_fields( $term, $request ) {
		return true;
	}

	/**
	 * Get the terms attached to a product.
	 *
	 * This is an alternative to `get_terms()` that uses `get_the_terms()`
	 * instead, which hits the object cache. There are a few things not
	 * supported, notably `include`, `exclude`. In `self::get_items()` these
	 * are instead treated as a full query.
	 *
	 * @param array           $prepared_args Arguments for `get_terms()`.
	 * @param WP_REST_Request $request       Full details about the request.
	 * @return array List of term objects. (Total count in `$this->total_terms`).
	 */
	protected function get_terms_for_product( $prepared_args, $request ) {
		$taxonomy = $this->get_taxonomy( $request );

		$query_result = get_the_terms( $prepared_args['product'], $taxonomy );
		if ( empty( $query_result ) ) {
			$this->total_terms = 0;
			return array();
		}

		// get_items() verifies that we don't have `include` set, and default.
		// ordering is by `name`.
		if ( ! in_array( $prepared_args['orderby'], array( 'name', 'none', 'include' ), true ) ) {
			switch ( $prepared_args['orderby'] ) {
				case 'id':
					$this->sort_column = 'term_id';
					break;
				case 'slug':
				case 'term_group':
				case 'description':
				case 'count':
					$this->sort_column = $prepared_args['orderby'];
					break;
			}
			usort( $query_result, array( $this, 'compare_terms' ) );
		}
		if ( strtolower( $prepared_args['order'] ) !== 'asc' ) {
			$query_result = array_reverse( $query_result );
		}

		// Pagination.
		$this->total_terms = count( $query_result );
		$query_result      = array_slice( $query_result, $prepared_args['offset'], $prepared_args['number'] );

		return $query_result;
	}

	/**
	 * Comparison function for sorting terms by a column.
	 *
	 * Uses `$this->sort_column` to determine field to sort by.
	 *
	 * @param stdClass $left Term object.
	 * @param stdClass $right Term object.
	 * @return int <0 if left is higher "priority" than right, 0 if equal, >0 if right is higher "priority" than left.
	 */
	protected function compare_terms( $left, $right ) {
		$col       = $this->sort_column;
		$left_val  = $left->$col;
		$right_val = $right->$col;

		if ( is_int( $left_val ) && is_int( $right_val ) ) {
			return $left_val - $right_val;
		}

		return strcmp( $left_val, $right_val );
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['context']['default'] = 'view';

		$params['exclude']    = array(
			'description'       => __( 'Ensure result set excludes specific IDs.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['include']    = array(
			'description'       => __( 'Limit result set to specific ids.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['offset']     = array(
			'description'       => __( 'Offset the result set by a specific number of items. Applies to hierarchical taxonomies only.', 'woocommerce' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order']      = array(
			'description'       => __( 'Order sort attribute ascending or descending.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'default'           => 'asc',
			'enum'              => array(
				'asc',
				'desc',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orderby']    = array(
			'description'       => __( 'Sort collection by resource attribute.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'default'           => 'name',
			'enum'              => array(
				'id',
				'include',
				'name',
				'slug',
				'term_group',
				'description',
				'count',
			),
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['hide_empty'] = array(
			'description'       => __( 'Whether to hide resources not assigned to any products.', 'woocommerce' ),
			'type'              => 'boolean',
			'default'           => false,
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['parent']     = array(
			'description'       => __( 'Limit result set to resources assigned to a specific parent. Applies to hierarchical taxonomies only.', 'woocommerce' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['product']    = array(
			'description'       => __( 'Limit result set to resources assigned to a specific product.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => null,
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['slug']       = array(
			'description'       => __( 'Limit result set to resources with a specific slug.', 'woocommerce' ),
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}

	/**
	 * Get taxonomy.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return int|WP_Error
	 */
	protected function get_taxonomy( $request ) {
		$attribute_id = $request['attribute_id'];

		if ( empty( $attribute_id ) ) {
			return $this->taxonomy;
		}

		if ( isset( $this->taxonomies_by_id[ $attribute_id ] ) ) {
			return $this->taxonomies_by_id[ $attribute_id ];
		}

		$taxonomy = WC()->call_function( 'wc_attribute_taxonomy_name_by_id', (int) $request['attribute_id'] );
		if ( ! empty( $taxonomy ) ) {
			$this->taxonomy                          = $taxonomy;
			$this->taxonomies_by_id[ $attribute_id ] = $taxonomy;
		}

		return $taxonomy;
	}
}

<?php
/**
 * Abstract Rest Posts Controller Class
 *
 * @class WC_REST_Posts_Controller
 * @package WooCommerce\RestApi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_REST_Posts_Controller
 *
 * @package WooCommerce\RestApi
 * @version  2.6.0
 */
abstract class WC_REST_Posts_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Controls visibility on frontend.
	 *
	 * @var string
	 */
	protected $public = false;

	/**
	 * Check if a given request has access to read items.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_post_permissions( $this->post_type, 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to create an item.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! wc_rest_check_post_permissions( $this->post_type, 'create' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to read an item.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		$post = get_post( (int) $request['id'] );

		if ( $post && ! wc_rest_check_post_permissions( $this->post_type, 'read', $post->ID ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to update an item.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function update_item_permissions_check( $request ) {
		$post = get_post( (int) $request['id'] );

		if ( $post && ! wc_rest_check_post_permissions( $this->post_type, 'edit', $post->ID ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you are not allowed to edit this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to delete an item.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error
	 */
	public function delete_item_permissions_check( $request ) {
		$post = get_post( (int) $request['id'] );

		if ( $post && ! wc_rest_check_post_permissions( $this->post_type, 'delete', $post->ID ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_delete', __( 'Sorry, you are not allowed to delete this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access batch create, update and delete items.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return boolean|WP_Error
	 */
	public function batch_items_permissions_check( $request ) {
		if ( ! wc_rest_check_post_permissions( $this->post_type, 'batch' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_batch', __( 'Sorry, you are not allowed to batch manipulate this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get a single item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$id   = (int) $request['id'];
		$post = get_post( $id );

		if ( ! empty( $post->post_type ) && 'product_variation' === $post->post_type && 'product' === $this->post_type ) {
			return new WP_Error( "woocommerce_rest_invalid_{$this->post_type}_id", __( 'To manipulate product variations you should use the /products/&lt;product_id&gt;/variations/&lt;id&gt; endpoint.', 'woocommerce' ), array( 'status' => 404 ) );
		} elseif ( empty( $id ) || empty( $post->ID ) || $post->post_type !== $this->post_type ) {
			return new WP_Error( "woocommerce_rest_invalid_{$this->post_type}_id", __( 'Invalid ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$data = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $data );

		if ( $this->public ) {
			$response->link_header( 'alternate', get_permalink( $id ), array( 'type' => 'text/html' ) );
		}

		return $response;
	}

	/**
	 * Create a single item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			/* translators: %s: post type */
			return new WP_Error( "woocommerce_rest_{$this->post_type}_exists", sprintf( __( 'Cannot create existing %s.', 'woocommerce' ), $this->post_type ), array( 'status' => 400 ) );
		}

		$post = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		$post->post_type = $this->post_type;
		$post_id         = wp_insert_post( $post, true );

		if ( is_wp_error( $post_id ) ) {

			if ( in_array( $post_id->get_error_code(), array( 'db_insert_error' ) ) ) {
				$post_id->add_data( array( 'status' => 500 ) );
			} else {
				$post_id->add_data( array( 'status' => 400 ) );
			}
			return $post_id;
		}
		$post->ID = $post_id;
		$post     = get_post( $post_id );

		$this->update_additional_fields_for_object( $post, $request );

		// Add meta fields.
		$meta_fields = $this->add_post_meta_fields( $post, $request );
		if ( is_wp_error( $meta_fields ) ) {
			// Remove post.
			$this->delete_post( $post );

			return $meta_fields;
		}

		/**
		 * Fires after a single item is created or updated via the REST API.
		 *
		 * @param WP_Post         $post      Post object.
		 * @param WP_REST_Request $request   Request object.
		 * @param boolean         $creating  True when creating item, false when updating.
		 */
		do_action( "woocommerce_rest_insert_{$this->post_type}", $post, $request, true );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $post, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $post_id ) ) );

		return $response;
	}

	/**
	 * Add post meta fields.
	 *
	 * @param WP_Post         $post Post Object.
	 * @param WP_REST_Request $request WP_REST_Request Object.
	 * @return bool|WP_Error
	 */
	protected function add_post_meta_fields( $post, $request ) {
		return true;
	}

	/**
	 * Delete post.
	 *
	 * @param WP_Post $post Post object.
	 */
	protected function delete_post( $post ) {
		wp_delete_post( $post->ID, true );
	}

	/**
	 * Update a single post.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_item( $request ) {
		$id   = (int) $request['id'];
		$post = get_post( $id );

		if ( ! empty( $post->post_type ) && 'product_variation' === $post->post_type && 'product' === $this->post_type ) {
			return new WP_Error( "woocommerce_rest_invalid_{$this->post_type}_id", __( 'To manipulate product variations you should use the /products/&lt;product_id&gt;/variations/&lt;id&gt; endpoint.', 'woocommerce' ), array( 'status' => 404 ) );
		} elseif ( empty( $id ) || empty( $post->ID ) || $post->post_type !== $this->post_type ) {
			return new WP_Error( "woocommerce_rest_{$this->post_type}_invalid_id", __( 'ID is invalid.', 'woocommerce' ), array( 'status' => 400 ) );
		}

		$post = $this->prepare_item_for_database( $request );
		if ( is_wp_error( $post ) ) {
			return $post;
		}
		// Convert the post object to an array, otherwise wp_update_post will expect non-escaped input.
		$post_id = wp_update_post( (array) $post, true );
		if ( is_wp_error( $post_id ) ) {
			if ( in_array( $post_id->get_error_code(), array( 'db_update_error' ) ) ) {
				$post_id->add_data( array( 'status' => 500 ) );
			} else {
				$post_id->add_data( array( 'status' => 400 ) );
			}
			return $post_id;
		}

		$post = get_post( $post_id );
		$this->update_additional_fields_for_object( $post, $request );

		// Update meta fields.
		$meta_fields = $this->update_post_meta_fields( $post, $request );
		if ( is_wp_error( $meta_fields ) ) {
			return $meta_fields;
		}

		/**
		 * Fires after a single item is created or updated via the REST API.
		 *
		 * @param WP_Post         $post      Post object.
		 * @param WP_REST_Request $request   Request object.
		 * @param boolean         $creating  True when creating item, false when updating.
		 */
		do_action( "woocommerce_rest_insert_{$this->post_type}", $post, $request, false );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $post, $request );
		return rest_ensure_response( $response );
	}

	/**
	 * Get a collection of posts.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$args                         = array();
		$args['offset']               = $request['offset'];
		$args['order']                = $request['order'];
		$args['orderby']              = $request['orderby'];
		$args['paged']                = $request['page'];
		$args['post__in']             = $request['include'];
		$args['post__not_in']         = $request['exclude'];
		$args['posts_per_page']       = $request['per_page'];
		$args['name']                 = $request['slug'];
		$args['post_parent__in']      = $request['parent'];
		$args['post_parent__not_in']  = $request['parent_exclude'];
		$args['s']                    = $request['search'];

		$args['date_query'] = array();
		// Set before into date query. Date query must be specified as an array of an array.
		if ( isset( $request['before'] ) ) {
			$args['date_query'][0]['before'] = $request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $request['after'] ) ) {
			$args['date_query'][0]['after'] = $request['after'];
		}

		if ( 'wc/v1' === $this->namespace ) {
			if ( is_array( $request['filter'] ) ) {
				$args = array_merge( $args, $request['filter'] );
				unset( $args['filter'] );
			}
		}

		// Force the post_type argument, since it's not a user input variable.
		$args['post_type'] = $this->post_type;

		/**
		 * Filter the query arguments for a request.
		 *
		 * Enables adding extra arguments or setting defaults for a post
		 * collection request.
		 *
		 * @param array           $args    Key value array of query var to query value.
		 * @param WP_REST_Request $request The request used.
		 */
		$args = apply_filters( "woocommerce_rest_{$this->post_type}_query", $args, $request );
		$query_args = $this->prepare_items_query( $args, $request );

		$posts_query = new WP_Query();
		$query_result = $posts_query->query( $query_args );

		$posts = array();
		foreach ( $query_result as $post ) {
			if ( ! wc_rest_check_post_permissions( $this->post_type, 'read', $post->ID ) ) {
				continue;
			}

			$data = $this->prepare_item_for_response( $post, $request );
			$posts[] = $this->prepare_response_for_collection( $data );
		}

		$page = (int) $query_args['paged'];
		$total_posts = $posts_query->found_posts;

		if ( $total_posts < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $query_args['paged'] );
			$count_query = new WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		$max_pages = ceil( $total_posts / (int) $query_args['posts_per_page'] );

		$response = rest_ensure_response( $posts );
		$response->header( 'X-WP-Total', (int) $total_posts );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$request_params = $request->get_query_params();
		if ( ! empty( $request_params['filter'] ) ) {
			// Normalize the pagination params.
			unset( $request_params['filter']['posts_per_page'] );
			unset( $request_params['filter']['paged'] );
		}
		$base = add_query_arg( $request_params, rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

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
	 * Delete a single item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_item( $request ) {
		$id    = (int) $request['id'];
		$force = (bool) $request['force'];
		$post  = get_post( $id );

		if ( empty( $id ) || empty( $post->ID ) || $post->post_type !== $this->post_type ) {
			return new WP_Error( "woocommerce_rest_{$this->post_type}_invalid_id", __( 'ID is invalid.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$supports_trash = EMPTY_TRASH_DAYS > 0;

		/**
		 * Filter whether an item is trashable.
		 *
		 * Return false to disable trash support for the item.
		 *
		 * @param boolean $supports_trash Whether the item type support trashing.
		 * @param WP_Post $post           The Post object being considered for trashing support.
		 */
		$supports_trash = apply_filters( "woocommerce_rest_{$this->post_type}_trashable", $supports_trash, $post );

		if ( ! wc_rest_check_post_permissions( $this->post_type, 'delete', $post->ID ) ) {
			/* translators: %s: post type */
			return new WP_Error( "woocommerce_rest_user_cannot_delete_{$this->post_type}", sprintf( __( 'Sorry, you are not allowed to delete %s.', 'woocommerce' ), $this->post_type ), array( 'status' => rest_authorization_required_code() ) );
		}

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $post, $request );

		// If we're forcing, then delete permanently.
		if ( $force ) {
			$result = wp_delete_post( $id, true );
		} else {
			// If we don't support trashing for this type, error out.
			if ( ! $supports_trash ) {
				/* translators: %s: post type */
				return new WP_Error( 'woocommerce_rest_trash_not_supported', sprintf( __( 'The %s does not support trashing.', 'woocommerce' ), $this->post_type ), array( 'status' => 501 ) );
			}

			// Otherwise, only trash if we haven't already.
			if ( 'trash' === $post->post_status ) {
				/* translators: %s: post type */
				return new WP_Error( 'woocommerce_rest_already_trashed', sprintf( __( 'The %s has already been deleted.', 'woocommerce' ), $this->post_type ), array( 'status' => 410 ) );
			}

			// (Note that internally this falls through to `wp_delete_post` if
			// the trash is disabled.)
			$result = wp_trash_post( $id );
		}

		if ( ! $result ) {
			/* translators: %s: post type */
			return new WP_Error( 'woocommerce_rest_cannot_delete', sprintf( __( 'The %s cannot be deleted.', 'woocommerce' ), $this->post_type ), array( 'status' => 500 ) );
		}

		/**
		 * Fires after a single item is deleted or trashed via the REST API.
		 *
		 * @param object           $post     The deleted or trashed item.
		 * @param WP_REST_Response $response The response data.
		 * @param WP_REST_Request  $request  The request sent to the API.
		 */
		do_action( "woocommerce_rest_delete_{$this->post_type}", $post, $response, $request );

		return $response;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param WP_Post         $post Post object.
	 * @param WP_REST_Request $request Request object.
	 * @return array Links for the given post.
	 */
	protected function prepare_links( $post, $request ) {
		$links = array(
			'self' => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $post->ID ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		return $links;
	}

	/**
	 * Determine the allowed query_vars for a get_items() response and
	 * prepare for WP_Query.
	 *
	 * @param array           $prepared_args Prepared arguments.
	 * @param WP_REST_Request $request Request object.
	 * @return array          $query_args
	 */
	protected function prepare_items_query( $prepared_args = array(), $request = null ) {

		$valid_vars = array_flip( $this->get_allowed_query_vars() );
		$query_args = array();
		foreach ( $valid_vars as $var => $index ) {
			if ( isset( $prepared_args[ $var ] ) ) {
				/**
				 * Filter the query_vars used in `get_items` for the constructed query.
				 *
				 * The dynamic portion of the hook name, $var, refers to the query_var key.
				 *
				 * @param mixed $prepared_args[ $var ] The query_var value.
				 */
				$query_args[ $var ] = apply_filters( "woocommerce_rest_query_var-{$var}", $prepared_args[ $var ] );
			}
		}

		$query_args['ignore_sticky_posts'] = true;

		if ( 'include' === $query_args['orderby'] ) {
			$query_args['orderby'] = 'post__in';
		} elseif ( 'id' === $query_args['orderby'] ) {
			$query_args['orderby'] = 'ID'; // ID must be capitalized.
		} elseif ( 'slug' === $query_args['orderby'] ) {
			$query_args['orderby'] = 'name';
		}

		return $query_args;
	}

	/**
	 * Get all the WP Query vars that are allowed for the API request.
	 *
	 * @return array
	 */
	protected function get_allowed_query_vars() {
		global $wp;

		/**
		 * Filter the publicly allowed query vars.
		 *
		 * Allows adjusting of the default query vars that are made public.
		 *
		 * @param array  Array of allowed WP_Query query vars.
		 */
		$valid_vars = apply_filters( 'query_vars', $wp->public_query_vars );

		$post_type_obj = get_post_type_object( $this->post_type );
		if ( current_user_can( $post_type_obj->cap->edit_posts ) ) {
			/**
			 * Filter the allowed 'private' query vars for authorized users.
			 *
			 * If the user has the `edit_posts` capability, we also allow use of
			 * private query parameters, which are only undesirable on the
			 * frontend, but are safe for use in query strings.
			 *
			 * To disable anyway, use
			 * `add_filter( 'woocommerce_rest_private_query_vars', '__return_empty_array' );`
			 *
			 * @param array $private_query_vars Array of allowed query vars for authorized users.
			 * }
			 */
			$private = apply_filters( 'woocommerce_rest_private_query_vars', $wp->private_query_vars );
			$valid_vars = array_merge( $valid_vars, $private );
		}
		// Define our own in addition to WP's normal vars.
		$rest_valid = array(
			'date_query',
			'ignore_sticky_posts',
			'offset',
			'post__in',
			'post__not_in',
			'post_parent',
			'post_parent__in',
			'post_parent__not_in',
			'posts_per_page',
			'meta_query',
			'tax_query',
			'meta_key',
			'meta_value',
			'meta_compare',
			'meta_value_num',
		);
		$valid_vars = array_merge( $valid_vars, $rest_valid );

		/**
		 * Filter allowed query vars for the REST API.
		 *
		 * This filter allows you to add or remove query vars from the final allowed
		 * list for all requests, including unauthenticated ones. To alter the
		 * vars for editors only.
		 *
		 * @param array {
		 *    Array of allowed WP_Query query vars.
		 *
		 *    @param string $allowed_query_var The query var to allow.
		 * }
		 */
		$valid_vars = apply_filters( 'woocommerce_rest_query_vars', $valid_vars );

		return $valid_vars;
	}

	/**
	 * Get the query params for collections of attachments.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['context']['default'] = 'view';

		$params['after'] = array(
			'description'        => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'woocommerce' ),
			'type'               => 'string',
			'format'             => 'date-time',
			'validate_callback'  => 'rest_validate_request_arg',
		);
		$params['before'] = array(
			'description'        => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'woocommerce' ),
			'type'               => 'string',
			'format'             => 'date-time',
			'validate_callback'  => 'rest_validate_request_arg',
		);
		$params['exclude'] = array(
			'description'       => __( 'Ensure result set excludes specific IDs.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type'          => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['include'] = array(
			'description'       => __( 'Limit result set to specific ids.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type'          => 'integer',
			),
			'default'           => array(),
			'sanitize_callback' => 'wp_parse_id_list',
		);
		$params['offset'] = array(
			'description'        => __( 'Offset the result set by a specific number of items.', 'woocommerce' ),
			'type'               => 'integer',
			'sanitize_callback'  => 'absint',
			'validate_callback'  => 'rest_validate_request_arg',
		);
		$params['order'] = array(
			'description'        => __( 'Order sort attribute ascending or descending.', 'woocommerce' ),
			'type'               => 'string',
			'default'            => 'desc',
			'enum'               => array( 'asc', 'desc' ),
			'validate_callback'  => 'rest_validate_request_arg',
		);
		$params['orderby'] = array(
			'description'        => __( 'Sort collection by object attribute.', 'woocommerce' ),
			'type'               => 'string',
			'default'            => 'date',
			'enum'               => array(
				'date',
				'id',
				'include',
				'title',
				'slug',
				'modified',
			),
			'validate_callback'  => 'rest_validate_request_arg',
		);

		$post_type_obj = get_post_type_object( $this->post_type );

		if ( isset( $post_type_obj->hierarchical ) && $post_type_obj->hierarchical ) {
			$params['parent'] = array(
				'description'       => __( 'Limit result set to those of particular parent IDs.', 'woocommerce' ),
				'type'              => 'array',
				'items'             => array(
					'type'          => 'integer',
				),
				'sanitize_callback' => 'wp_parse_id_list',
				'default'           => array(),
			);
			$params['parent_exclude'] = array(
				'description'       => __( 'Limit result set to all items except those of a particular parent ID.', 'woocommerce' ),
				'type'              => 'array',
				'items'             => array(
					'type'          => 'integer',
				),
				'sanitize_callback' => 'wp_parse_id_list',
				'default'           => array(),
			);
		}

		if ( 'wc/v1' === $this->namespace ) {
			$params['filter'] = array(
				'type'        => 'object',
				'description' => __( 'Use WP Query arguments to modify the response; private query vars require appropriate authorization.', 'woocommerce' ),
			);
		}

		return $params;
	}

	/**
	 * Update post meta fields.
	 *
	 * @param WP_Post         $post Post object.
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	protected function update_post_meta_fields( $post, $request ) {
		return true;
	}
}

<?php
/**
 * REST API Taxes controller
 *
 * Handles requests to the /taxes endpoint.
 *
 * @package WooCommerce\RestApi
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Taxes controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Controller
 */
class WC_REST_Taxes_V1_Controller extends WC_REST_Controller {

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
	protected $rest_base = 'taxes';

	/**
	 * Register the routes for taxes.
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
	 * Check whether a given request has permission to read taxes.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access create taxes.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|WP_Error
	 */
	public function create_item_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'create' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_create', __( 'Sorry, you are not allowed to create resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access to read a tax.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'read' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access update a tax.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|WP_Error
	 */
	public function update_item_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'edit' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you are not allowed to edit this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access delete a tax.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|WP_Error
	 */
	public function delete_item_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'delete' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_delete', __( 'Sorry, you are not allowed to delete this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Check if a given request has access batch create, update and delete items.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return bool|WP_Error
	 */
	public function batch_items_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'batch' ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_batch', __( 'Sorry, you are not allowed to batch manipulate this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Get all taxes.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		global $wpdb;

		$prepared_args           = array();
		$prepared_args['order']  = $request['order'];
		$prepared_args['number'] = $request['per_page'];
		if ( ! empty( $request['offset'] ) ) {
			$prepared_args['offset'] = $request['offset'];
		} else {
			$prepared_args['offset'] = ( $request['page'] - 1 ) * $prepared_args['number'];
		}
		$orderby_possibles        = array(
			'id'       => 'tax_rate_id',
			'order'    => 'tax_rate_order',
			'priority' => 'tax_rate_priority',
		);
		$prepared_args['orderby'] = $orderby_possibles[ $request['orderby'] ];
		$prepared_args['class']   = $request['class'];

		/**
		 * Filter arguments, before passing to $wpdb->get_results(), when querying taxes via the REST API.
		 *
		 * @param array           $prepared_args Array of arguments for $wpdb->get_results().
		 * @param WP_REST_Request $request       The current request.
		 */
		$prepared_args = apply_filters( 'woocommerce_rest_tax_query', $prepared_args, $request );

		$orderby = sanitize_key( $prepared_args['orderby'] ) . ' ' . sanitize_key( $prepared_args['order'] );
		$query   = "
			SELECT *
			FROM {$wpdb->prefix}woocommerce_tax_rates
			%s
			ORDER BY {$orderby}
			LIMIT %%d, %%d
		";

		$wpdb_prepare_args = array(
			$prepared_args['offset'],
			$prepared_args['number'],
		);

		// Filter by tax class.
		if ( empty( $prepared_args['class'] ) ) {
			$query = sprintf( $query, '' );
		} else {
			$class = 'standard' !== $prepared_args['class'] ? sanitize_title( $prepared_args['class'] ) : '';
			array_unshift( $wpdb_prepare_args, $class );
			$query = sprintf( $query, 'WHERE tax_rate_class = %s' );
		}

		// Query taxes.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results(
			$wpdb->prepare(
				$query,
				$wpdb_prepare_args
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared

		$taxes = array();
		foreach ( $results as $tax ) {
			$data    = $this->prepare_item_for_response( $tax, $request );
			$taxes[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $taxes );

		$per_page = (int) $prepared_args['number'];
		$page     = ceil( ( ( (int) $prepared_args['offset'] ) / $per_page ) + 1 );

		// Unset LIMIT args.
		array_splice( $wpdb_prepare_args, -2 );

		// Count query.
		$query = str_replace(
			array(
				'SELECT *',
				'LIMIT %d, %d',
			),
			array(
				'SELECT COUNT(*)',
				'',
			),
			$query
		);

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$total_taxes = (int) $wpdb->get_var( empty( $wpdb_prepare_args ) ? $query : $wpdb->prepare( $query, $wpdb_prepare_args ) );
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared

		// Calculate totals.
		$response->header( 'X-WP-Total', $total_taxes );
		$max_pages = ceil( $total_taxes / $per_page );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );
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
	 * Take tax data from the request and return the updated or newly created rate.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @param stdClass|null   $current Existing tax object.
	 * @return object
	 */
	protected function create_or_update_tax( $request, $current = null ) {
		$id     = absint( isset( $request['id'] ) ? $request['id'] : 0 );
		$data   = array();
		$fields = array(
			'tax_rate_country',
			'tax_rate_state',
			'tax_rate',
			'tax_rate_name',
			'tax_rate_priority',
			'tax_rate_compound',
			'tax_rate_shipping',
			'tax_rate_order',
			'tax_rate_class',
		);

		foreach ( $fields as $field ) {
			// Keys via API differ from the stored names returned by _get_tax_rate.
			$key = 'tax_rate' === $field ? 'rate' : str_replace( 'tax_rate_', '', $field );

			// Remove data that was not posted.
			if ( ! isset( $request[ $key ] ) ) {
				continue;
			}

			// Test new data against current data.
			if ( $current && $current->$field === $request[ $key ] ) {
				continue;
			}

			// Add to data array.
			switch ( $key ) {
				case 'tax_rate_priority':
				case 'tax_rate_compound':
				case 'tax_rate_shipping':
				case 'tax_rate_order':
					$data[ $field ] = absint( $request[ $key ] );
					break;
				case 'tax_rate_class':
					$data[ $field ] = 'standard' !== $request['tax_rate_class'] ? $request['tax_rate_class'] : '';
					break;
				default:
					$data[ $field ] = wc_clean( $request[ $key ] );
					break;
			}
		}

		if ( ! $id ) {
			$id = WC_Tax::_insert_tax_rate( $data );
		} elseif ( $data ) {
			WC_Tax::_update_tax_rate( $id, $data );
		}

		// Add locales.
		if ( ! empty( $request['postcode'] ) ) {
			WC_Tax::_update_tax_rate_postcodes( $id, wc_clean( $request['postcode'] ) );
		}
		if ( ! empty( $request['city'] ) ) {
			WC_Tax::_update_tax_rate_cities( $id, wc_clean( $request['city'] ) );
		}

		return WC_Tax::_get_tax_rate( $id, OBJECT );
	}

	/**
	 * Create a single tax.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['id'] ) ) {
			return new WP_Error( 'woocommerce_rest_tax_exists', __( 'Cannot create existing resource.', 'woocommerce' ), array( 'status' => 400 ) );
		}

		$tax = $this->create_or_update_tax( $request );

		$this->update_additional_fields_for_object( $tax, $request );

		/**
		 * Fires after a tax is created or updated via the REST API.
		 *
		 * @param stdClass        $tax       Data used to create the tax.
		 * @param WP_REST_Request $request   Request object.
		 * @param boolean         $creating  True when creating tax, false when updating tax.
		 */
		do_action( 'woocommerce_rest_insert_tax', $tax, $request, true );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $tax, $request );
		$response = rest_ensure_response( $response );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $tax->tax_rate_id ) ) );

		return $response;
	}

	/**
	 * Get a single tax.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$id      = (int) $request['id'];
		$tax_obj = WC_Tax::_get_tax_rate( $id, OBJECT );

		if ( empty( $id ) || empty( $tax_obj ) ) {
			return new WP_Error( 'woocommerce_rest_invalid_id', __( 'Invalid resource ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$tax      = $this->prepare_item_for_response( $tax_obj, $request );
		$response = rest_ensure_response( $tax );

		return $response;
	}

	/**
	 * Update a single tax.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_item( $request ) {
		$id      = (int) $request['id'];
		$tax_obj = WC_Tax::_get_tax_rate( $id, OBJECT );

		if ( empty( $id ) || empty( $tax_obj ) ) {
			return new WP_Error( 'woocommerce_rest_invalid_id', __( 'Invalid resource ID.', 'woocommerce' ), array( 'status' => 404 ) );
		}

		$tax = $this->create_or_update_tax( $request, $tax_obj );

		$this->update_additional_fields_for_object( $tax, $request );

		/**
		 * Fires after a tax is created or updated via the REST API.
		 *
		 * @param stdClass        $tax       Data used to create the tax.
		 * @param WP_REST_Request $request   Request object.
		 * @param boolean         $creating  True when creating tax, false when updating tax.
		 */
		do_action( 'woocommerce_rest_insert_tax', $tax, $request, false );

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $tax, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Delete a single tax.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {
		global $wpdb;

		$id    = (int) $request['id'];
		$force = isset( $request['force'] ) ? (bool) $request['force'] : false;

		// We don't support trashing for this type, error out.
		if ( ! $force ) {
			return new WP_Error( 'woocommerce_rest_trash_not_supported', __( 'Taxes do not support trashing.', 'woocommerce' ), array( 'status' => 501 ) );
		}

		$tax = WC_Tax::_get_tax_rate( $id, OBJECT );

		if ( empty( $id ) || empty( $tax ) ) {
			return new WP_Error( 'woocommerce_rest_invalid_id', __( 'Invalid resource ID.', 'woocommerce' ), array( 'status' => 400 ) );
		}

		$request->set_param( 'context', 'edit' );
		$response = $this->prepare_item_for_response( $tax, $request );

		WC_Tax::_delete_tax_rate( $id );

		if ( 0 === $wpdb->rows_affected ) {
			return new WP_Error( 'woocommerce_rest_cannot_delete', __( 'The resource cannot be deleted.', 'woocommerce' ), array( 'status' => 500 ) );
		}

		/**
		 * Fires after a tax is deleted via the REST API.
		 *
		 * @param stdClass         $tax      The tax data.
		 * @param WP_REST_Response $response The response returned from the API.
		 * @param WP_REST_Request  $request  The request sent to the API.
		 */
		do_action( 'woocommerce_rest_delete_tax', $tax, $response, $request );

		return $response;
	}

	/**
	 * Prepare a single tax output for response.
	 *
	 * @param stdClass        $tax     Tax object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $tax, $request ) {
		$id   = (int) $tax->tax_rate_id;
		$data = array(
			'id'       => $id,
			'country'  => $tax->tax_rate_country,
			'state'    => $tax->tax_rate_state,
			'postcode' => '',
			'city'     => '',
			'rate'     => $tax->tax_rate,
			'name'     => $tax->tax_rate_name,
			'priority' => (int) $tax->tax_rate_priority,
			'compound' => (bool) $tax->tax_rate_compound,
			'shipping' => (bool) $tax->tax_rate_shipping,
			'order'    => (int) $tax->tax_rate_order,
			'class'    => $tax->tax_rate_class ? $tax->tax_rate_class : 'standard',
		);

		$data = $this->add_tax_rate_locales( $data, $tax );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $tax ) );

		/**
		 * Filter tax object returned from the REST API.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param stdClass         $tax      Tax object used to create response.
		 * @param WP_REST_Request  $request  Request object.
		 */
		return apply_filters( 'woocommerce_rest_prepare_tax', $response, $tax, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param stdClass $tax Tax object.
	 * @return array Links for the given tax.
	 */
	protected function prepare_links( $tax ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $tax->tax_rate_id ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		return $links;
	}

	/**
	 * Add tax rate locales to the response array.
	 *
	 * @param array    $data Response data.
	 * @param stdClass $tax  Tax object.
	 *
	 * @return array
	 */
	protected function add_tax_rate_locales( $data, $tax ) {
		global $wpdb;

		// Get locales from a tax rate.
		$locales = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT location_code, location_type
				FROM {$wpdb->prefix}woocommerce_tax_rate_locations
				WHERE tax_rate_id = %d
				",
				$tax->tax_rate_id
			)
		);

		if ( ! is_wp_error( $tax ) && ! is_null( $tax ) ) {
			foreach ( $locales as $locale ) {
				$data[ $locale->location_type ] = $locale->location_code;
			}
		}

		return $data;
	}

	/**
	 * Get the Taxes schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'tax',
			'type'       => 'object',
			'properties' => array(
				'id'       => array(
					'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'country'  => array(
					'description' => __( 'Country ISO 3166 code.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'state'    => array(
					'description' => __( 'State code.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'postcode' => array(
					'description' => __( 'Postcode / ZIP.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'city'     => array(
					'description' => __( 'City name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'rate'     => array(
					'description' => __( 'Tax rate.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'name'     => array(
					'description' => __( 'Tax rate name.', 'woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'priority' => array(
					'description' => __( 'Tax priority.', 'woocommerce' ),
					'type'        => 'integer',
					'default'     => 1,
					'context'     => array( 'view', 'edit' ),
				),
				'compound' => array(
					'description' => __( 'Whether or not this is a compound rate.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'context'     => array( 'view', 'edit' ),
				),
				'shipping' => array(
					'description' => __( 'Whether or not this tax rate also gets applied to shipping.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => true,
					'context'     => array( 'view', 'edit' ),
				),
				'order'    => array(
					'description' => __( 'Indicates the order that will appear in queries.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'class'    => array(
					'description' => __( 'Tax class.', 'woocommerce' ),
					'type'        => 'string',
					'default'     => 'standard',
					'enum'        => array_merge( array( 'standard' ), WC_Tax::get_tax_class_slugs() ),
					'context'     => array( 'view', 'edit' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params                       = array();
		$params['context']            = $this->get_context_param();
		$params['context']['default'] = 'view';

		$params['page']     = array(
			'description'       => __( 'Current page of the collection.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
			'minimum'           => 1,
		);
		$params['per_page'] = array(
			'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce' ),
			'type'              => 'integer',
			'default'           => 10,
			'minimum'           => 1,
			'maximum'           => 100,
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['offset']   = array(
			'description'       => __( 'Offset the result set by a specific number of items.', 'woocommerce' ),
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['order']    = array(
			'default'           => 'asc',
			'description'       => __( 'Order sort attribute ascending or descending.', 'woocommerce' ),
			'enum'              => array( 'asc', 'desc' ),
			'sanitize_callback' => 'sanitize_key',
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['orderby']  = array(
			'default'           => 'order',
			'description'       => __( 'Sort collection by object attribute.', 'woocommerce' ),
			'enum'              => array(
				'id',
				'order',
				'priority',
			),
			'sanitize_callback' => 'sanitize_key',
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['class']    = array(
			'description'       => __( 'Sort by tax class.', 'woocommerce' ),
			'enum'              => array_merge( array( 'standard' ), WC_Tax::get_tax_class_slugs() ),
			'sanitize_callback' => 'sanitize_title',
			'type'              => 'string',
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}
}

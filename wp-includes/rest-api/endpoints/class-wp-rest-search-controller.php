<?php
/**
 * REST API: WP_REST_Search_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.0.0
 */

/**
 * Core class to search through all WordPress content via the REST API.
 *
 * @since 5.0.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Search_Controller extends WP_REST_Controller {

	/**
	 * ID property name.
	 */
	const PROP_ID = 'id';

	/**
	 * Title property name.
	 */
	const PROP_TITLE = 'title';

	/**
	 * URL property name.
	 */
	const PROP_URL = 'url';

	/**
	 * Type property name.
	 */
	const PROP_TYPE = 'type';

	/**
	 * Subtype property name.
	 */
	const PROP_SUBTYPE = 'subtype';

	/**
	 * Identifier for the 'any' type.
	 */
	const TYPE_ANY = 'any';

	/**
	 * Search handlers used by the controller.
	 *
	 * @since 5.0.0
	 * @var array
	 */
	protected $search_handlers = array();

	/**
	 * Constructor.
	 *
	 * @since 5.0.0
	 *
	 * @param array $search_handlers List of search handlers to use in the controller. Each search
	 *                               handler instance must extend the `WP_REST_Search_Handler` class.
	 */
	public function __construct( array $search_handlers ) {
		$this->namespace = 'wp/v2';
		$this->rest_base = 'search';

		foreach ( $search_handlers as $search_handler ) {
			if ( ! $search_handler instanceof WP_REST_Search_Handler ) {
				_doing_it_wrong(
					__METHOD__,
					/* translators: %s: PHP class name. */
					sprintf( __( 'REST search handlers must extend the %s class.' ), 'WP_REST_Search_Handler' ),
					'5.0.0'
				);
				continue;
			}

			$this->search_handlers[ $search_handler->get_type() ] = $search_handler;
		}
	}

	/**
	 * Registers the routes for the search controller.
	 *
	 * @since 5.0.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permission_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Checks if a given request has access to search content.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has search access, WP_Error object otherwise.
	 */
	public function get_items_permission_check( $request ) {
		return true;
	}

	/**
	 * Retrieves a collection of search results.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$handler = $this->get_search_handler( $request );
		if ( is_wp_error( $handler ) ) {
			return $handler;
		}

		$result = $handler->search_items( $request );

		if ( ! isset( $result[ WP_REST_Search_Handler::RESULT_IDS ] ) || ! is_array( $result[ WP_REST_Search_Handler::RESULT_IDS ] ) || ! isset( $result[ WP_REST_Search_Handler::RESULT_TOTAL ] ) ) {
			return new WP_Error(
				'rest_search_handler_error',
				__( 'Internal search handler error.' ),
				array( 'status' => 500 )
			);
		}

		$ids = $result[ WP_REST_Search_Handler::RESULT_IDS ];

		$results = array();

		foreach ( $ids as $id ) {
			$data      = $this->prepare_item_for_response( $id, $request );
			$results[] = $this->prepare_response_for_collection( $data );
		}

		$total     = (int) $result[ WP_REST_Search_Handler::RESULT_TOTAL ];
		$page      = (int) $request['page'];
		$per_page  = (int) $request['per_page'];
		$max_pages = ceil( $total / $per_page );

		if ( $page > $max_pages && $total > 0 ) {
			return new WP_Error(
				'rest_search_invalid_page_number',
				__( 'The page number requested is larger than the number of pages available.' ),
				array( 'status' => 400 )
			);
		}

		$response = rest_ensure_response( $results );
		$response->header( 'X-WP-Total', $total );
		$response->header( 'X-WP-TotalPages', $max_pages );

		$request_params = $request->get_query_params();
		$base           = add_query_arg( urlencode_deep( $request_params ), rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );

		if ( $page > 1 ) {
			$prev_link = add_query_arg( 'page', $page - 1, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $page < $max_pages ) {
			$next_link = add_query_arg( 'page', $page + 1, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	/**
	 * Prepares a single search result for response.
	 *
	 * @since 5.0.0
	 * @since 5.6.0 The `$id` parameter can accept a string.
	 *
	 * @param int|string      $id      ID of the item to prepare.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $id, $request ) {
		$handler = $this->get_search_handler( $request );
		if ( is_wp_error( $handler ) ) {
			return new WP_REST_Response();
		}

		$fields = $this->get_fields_for_response( $request );

		$data = $handler->prepare_item( $id, $fields );
		$data = $this->add_additional_fields_to_object( $data, $request );

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		$links               = $handler->prepare_item_links( $id );
		$links['collection'] = array(
			'href' => rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ),
		);
		$response->add_links( $links );

		return $response;
	}

	/**
	 * Retrieves the item schema, conforming to JSON Schema.
	 *
	 * @since 5.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$types    = array();
		$subtypes = array();

		foreach ( $this->search_handlers as $search_handler ) {
			$types[]  = $search_handler->get_type();
			$subtypes = array_merge( $subtypes, $search_handler->get_subtypes() );
		}

		$types    = array_unique( $types );
		$subtypes = array_unique( $subtypes );

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'search-result',
			'type'       => 'object',
			'properties' => array(
				self::PROP_ID      => array(
					'description' => __( 'Unique identifier for the object.' ),
					'type'        => array( 'integer', 'string' ),
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
				),
				self::PROP_TITLE   => array(
					'description' => __( 'The title for the object.' ),
					'type'        => 'string',
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
				),
				self::PROP_URL     => array(
					'description' => __( 'URL to the object.' ),
					'type'        => 'string',
					'format'      => 'uri',
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
				),
				self::PROP_TYPE    => array(
					'description' => __( 'Object type.' ),
					'type'        => 'string',
					'enum'        => $types,
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
				),
				self::PROP_SUBTYPE => array(
					'description' => __( 'Object subtype.' ),
					'type'        => 'string',
					'enum'        => $subtypes,
					'context'     => array( 'view', 'embed' ),
					'readonly'    => true,
				),
			),
		);

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

	/**
	 * Retrieves the query params for the search results collection.
	 *
	 * @since 5.0.0
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		$types    = array();
		$subtypes = array();

		foreach ( $this->search_handlers as $search_handler ) {
			$types[]  = $search_handler->get_type();
			$subtypes = array_merge( $subtypes, $search_handler->get_subtypes() );
		}

		$types    = array_unique( $types );
		$subtypes = array_unique( $subtypes );

		$query_params = parent::get_collection_params();

		$query_params['context']['default'] = 'view';

		$query_params[ self::PROP_TYPE ] = array(
			'default'     => $types[0],
			'description' => __( 'Limit results to items of an object type.' ),
			'type'        => 'string',
			'enum'        => $types,
		);

		$query_params[ self::PROP_SUBTYPE ] = array(
			'default'           => self::TYPE_ANY,
			'description'       => __( 'Limit results to items of one or more object subtypes.' ),
			'type'              => 'array',
			'items'             => array(
				'enum' => array_merge( $subtypes, array( self::TYPE_ANY ) ),
				'type' => 'string',
			),
			'sanitize_callback' => array( $this, 'sanitize_subtypes' ),
		);

		return $query_params;
	}

	/**
	 * Sanitizes the list of subtypes, to ensure only subtypes of the passed type are included.
	 *
	 * @since 5.0.0
	 *
	 * @param string|array    $subtypes  One or more subtypes.
	 * @param WP_REST_Request $request   Full details about the request.
	 * @param string          $parameter Parameter name.
	 * @return array|WP_Error List of valid subtypes, or WP_Error object on failure.
	 */
	public function sanitize_subtypes( $subtypes, $request, $parameter ) {
		$subtypes = wp_parse_slug_list( $subtypes );

		$subtypes = rest_parse_request_arg( $subtypes, $request, $parameter );
		if ( is_wp_error( $subtypes ) ) {
			return $subtypes;
		}

		// 'any' overrides any other subtype.
		if ( in_array( self::TYPE_ANY, $subtypes, true ) ) {
			return array( self::TYPE_ANY );
		}

		$handler = $this->get_search_handler( $request );
		if ( is_wp_error( $handler ) ) {
			return $handler;
		}

		return array_intersect( $subtypes, $handler->get_subtypes() );
	}

	/**
	 * Gets the search handler to handle the current request.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Search_Handler|WP_Error Search handler for the request type, or WP_Error object on failure.
	 */
	protected function get_search_handler( $request ) {
		$type = $request->get_param( self::PROP_TYPE );

		if ( ! $type || ! isset( $this->search_handlers[ $type ] ) ) {
			return new WP_Error(
				'rest_search_invalid_type',
				__( 'Invalid type parameter.' ),
				array( 'status' => 400 )
			);
		}

		return $this->search_handlers[ $type ];
	}
}

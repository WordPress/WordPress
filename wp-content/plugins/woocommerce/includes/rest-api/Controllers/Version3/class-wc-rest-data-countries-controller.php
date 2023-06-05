<?php
/**
 * REST API Data countries controller.
 *
 * Handles requests to the /data/countries endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   3.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Data countries controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Controller
 */
class WC_REST_Data_Countries_Controller extends WC_REST_Data_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'data/countries';

	/**
	 * Register routes.
	 *
	 * @since 3.5.0
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
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<location>[\w-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => array(
						'location' => array(
							'description' => __( 'ISO3166 alpha-2 country code.', 'woocommerce' ),
							'type'        => 'string',
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Get a list of countries and states.
	 *
	 * @param  string          $country_code Country code.
	 * @param  WP_REST_Request $request      Request data.
	 * @return array|mixed Response data, ready for insertion into collection data.
	 */
	public function get_country( $country_code, $request ) {
		$countries = WC()->countries->get_countries();
		$states    = WC()->countries->get_states();
		$data      = array();

		if ( ! array_key_exists( $country_code, $countries ) ) {
			return false;
		}

		$country = array(
			'code' => $country_code,
			'name' => $countries[ $country_code ],
		);

		$local_states = array();
		if ( isset( $states[ $country_code ] ) ) {
			foreach ( $states[ $country_code ] as $state_code => $state_name ) {
				$local_states[] = array(
					'code' => $state_code,
					'name' => $state_name,
				);
			}
		}
		$country['states'] = $local_states;
		return $country;
	}

	/**
	 * Return the list of states for all countries.
	 *
	 * @since  3.5.0
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$countries = WC()->countries->get_countries();
		$data      = array();

		foreach ( array_keys( $countries ) as $country_code ) {
			$country  = $this->get_country( $country_code, $request );
			$response = $this->prepare_item_for_response( $country, $request );
			$data[]   = $this->prepare_response_for_collection( $response );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Return the list of states for a given country.
	 *
	 * @since  3.5.0
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$data = $this->get_country( strtoupper( $request['location'] ), $request );
		if ( empty( $data ) ) {
			return new WP_Error( 'woocommerce_rest_data_invalid_location', __( 'There are no locations matching these parameters.', 'woocommerce' ), array( 'status' => 404 ) );
		}
		return $this->prepare_item_for_response( $data, $request );
	}

	/**
	 * Prepare the data object for response.
	 *
	 * @since  3.5.0
	 * @param object          $item Data object.
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data     = $this->add_additional_fields_to_object( $item, $request );
		$data     = $this->filter_response_by_context( $data, 'view' );
		$response = rest_ensure_response( $data );

		$response->add_links( $this->prepare_links( $item ) );

		/**
		 * Filter the states list for a country returned from the API.
		 *
		 * Allows modification of the location data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param array            $data     The original country's states list.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_data_country', $response, $item, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param object $item Data object.
	 * @return array Links for the given country.
	 */
	protected function prepare_links( $item ) {
		$country_code = strtolower( $item['code'] );
		$links        = array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%s', $this->namespace, $this->rest_base, $country_code ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		return $links;
	}


	/**
	 * Get the location schema, conforming to JSON Schema.
	 *
	 * @since  3.5.0
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'data_countries',
			'type'       => 'object',
			'properties' => array(
				'code'   => array(
					'type'        => 'string',
					'description' => __( 'ISO3166 alpha-2 country code.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'name'   => array(
					'type'        => 'string',
					'description' => __( 'Full name of country.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'states' => array(
					'type'        => 'array',
					'description' => __( 'List of states in this country.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type'       => 'object',
						'context'    => array( 'view' ),
						'readonly'   => true,
						'properties' => array(
							'code' => array(
								'type'        => 'string',
								'description' => __( 'State code.', 'woocommerce' ),
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'name' => array(
								'type'        => 'string',
								'description' => __( 'Full name of state.', 'woocommerce' ),
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}
}

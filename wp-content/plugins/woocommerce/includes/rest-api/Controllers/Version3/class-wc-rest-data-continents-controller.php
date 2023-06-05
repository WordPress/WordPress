<?php
/**
 * REST API Data continents controller.
 *
 * Handles requests to the /data/continents endpoint.
 *
 * @package WooCommerce\RestApi
 * @since   3.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Data continents controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Controller
 */
class WC_REST_Data_Continents_Controller extends WC_REST_Data_Controller {

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
	protected $rest_base = 'data/continents';

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
						'continent' => array(
							'description' => __( '2 character continent code.', 'woocommerce' ),
							'type'        => 'string',
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Return the list of countries and states for a given continent.
	 *
	 * @since  3.5.0
	 * @param  string          $continent_code Continent code.
	 * @param  WP_REST_Request $request        Request data.
	 * @return array|mixed Response data, ready for insertion into collection data.
	 */
	public function get_continent( $continent_code, $request ) {
		$continents  = WC()->countries->get_continents();
		$countries   = WC()->countries->get_countries();
		$states      = WC()->countries->get_states();
		$locale_info = include WC()->plugin_path() . '/i18n/locale-info.php';
		$data        = array();

		if ( ! array_key_exists( $continent_code, $continents ) ) {
			return false;
		}

		$continent_list = $continents[ $continent_code ];

		$continent = array(
			'code' => $continent_code,
			'name' => $continent_list['name'],
		);

		$local_countries = array();
		foreach ( $continent_list['countries'] as $country_code ) {
			if ( isset( $countries[ $country_code ] ) ) {
				$country = array(
					'code' => $country_code,
					'name' => $countries[ $country_code ],
				);

				// If we have detailed locale information include that in the response.
				if ( array_key_exists( $country_code, $locale_info ) ) {
					// Defensive programming against unexpected changes in locale-info.php.
					$country_data = wp_parse_args(
						$locale_info[ $country_code ],
						array(
							'currency_code'  => 'USD',
							'currency_pos'   => 'left',
							'decimal_sep'    => '.',
							'dimension_unit' => 'in',
							'num_decimals'   => 2,
							'thousand_sep'   => ',',
							'weight_unit'    => 'lbs',
						)
					);

					$country = array_merge( $country, $country_data );
				}

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

				// Allow only desired keys (e.g. filter out tax rates).
				$allowed = array(
					'code',
					'currency_code',
					'currency_pos',
					'decimal_sep',
					'dimension_unit',
					'name',
					'num_decimals',
					'states',
					'thousand_sep',
					'weight_unit',
				);
				$country = array_intersect_key( $country, array_flip( $allowed ) );

				$local_countries[] = $country;
			}
		}

		$continent['countries'] = $local_countries;
		return $continent;
	}

	/**
	 * Return the list of states for all continents.
	 *
	 * @since  3.5.0
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$continents = WC()->countries->get_continents();
		$data       = array();

		foreach ( array_keys( $continents ) as $continent_code ) {
			$continent = $this->get_continent( $continent_code, $request );
			$response  = $this->prepare_item_for_response( $continent, $request );
			$data[]    = $this->prepare_response_for_collection( $response );
		}

		return rest_ensure_response( $data );
	}

	/**
	 * Return the list of locations for a given continent.
	 *
	 * @since  3.5.0
	 * @param  WP_REST_Request $request Request data.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$data = $this->get_continent( strtoupper( $request['location'] ), $request );
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
		 * Filter the location list returned from the API.
		 *
		 * Allows modification of the location data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param array            $item     The original list of continent(s), countries, and states.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'woocommerce_rest_prepare_data_continent', $response, $item, $request );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param object $item Data object.
	 * @return array Links for the given continent.
	 */
	protected function prepare_links( $item ) {
		$continent_code = strtolower( $item['code'] );
		$links          = array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%s', $this->namespace, $this->rest_base, $continent_code ) ),
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
			'title'      => 'data_continents',
			'type'       => 'object',
			'properties' => array(
				'code'      => array(
					'type'        => 'string',
					'description' => __( '2 character continent code.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'name'      => array(
					'type'        => 'string',
					'description' => __( 'Full name of continent.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
				),
				'countries' => array(
					'type'        => 'array',
					'description' => __( 'List of countries on this continent.', 'woocommerce' ),
					'context'     => array( 'view' ),
					'readonly'    => true,
					'items'       => array(
						'type'       => 'object',
						'context'    => array( 'view' ),
						'readonly'   => true,
						'properties' => array(
							'code'           => array(
								'type'        => 'string',
								'description' => __( 'ISO3166 alpha-2 country code.', 'woocommerce' ),
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'currency_code'  => array(
								'type'        => 'string',
								'description' => __( 'Default ISO4127 alpha-3 currency code for the country.', 'woocommerce' ),
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'currency_pos'   => array(
								'type'        => 'string',
								'description' => __( 'Currency symbol position for this country.', 'woocommerce' ),
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'decimal_sep'    => array(
								'type'        => 'string',
								'description' => __( 'Decimal separator for displayed prices for this country.', 'woocommerce' ),
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'dimension_unit' => array(
								'type'        => 'string',
								'description' => __( 'The unit lengths are defined in for this country.', 'woocommerce' ),
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'name'           => array(
								'type'        => 'string',
								'description' => __( 'Full name of country.', 'woocommerce' ),
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'num_decimals'   => array(
								'type'        => 'integer',
								'description' => __( 'Number of decimal points shown in displayed prices for this country.', 'woocommerce' ),
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'states'         => array(
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
							'thousand_sep'   => array(
								'type'        => 'string',
								'description' => __( 'Thousands separator for displayed prices in this country.', 'woocommerce' ),
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'weight_unit'    => array(
								'type'        => 'string',
								'description' => __( 'The unit weights are defined in for this country.', 'woocommerce' ),
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

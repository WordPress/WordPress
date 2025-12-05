<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Endpoints
 */

/**
 * Represents an implementation of the WPSEO_Endpoint interface to register one or multiple endpoints.
 */
class WPSEO_Endpoint_File_Size implements WPSEO_Endpoint {

	/**
	 * The namespace of the REST route.
	 *
	 * @var string
	 */
	public const REST_NAMESPACE = 'yoast/v1';

	/**
	 * The route of the endpoint to retrieve the file size.
	 *
	 * @var string
	 */
	public const ENDPOINT_SINGULAR = 'file_size';

	/**
	 * The name of the capability needed to retrieve data using the endpoints.
	 *
	 * @var string
	 */
	public const CAPABILITY_RETRIEVE = 'manage_options';

	/**
	 * The service provider.
	 *
	 * @var WPSEO_File_Size_Service
	 */
	private $service;

	/**
	 * Sets the service provider.
	 *
	 * @param WPSEO_File_Size_Service $service The service provider.
	 */
	public function __construct( WPSEO_File_Size_Service $service ) {
		$this->service = $service;
	}

	/**
	 * Registers the routes for the endpoints.
	 *
	 * @return void
	 */
	public function register() {
		$route_args = [
			'methods'             => 'GET',
			'args'                => [
				'url' => [
					'required'    => true,
					'type'        => 'string',
					'description' => 'The url to retrieve',
				],
			],
			'callback'            => [
				$this->service,
				'get',
			],
			'permission_callback' => [
				$this,
				'can_retrieve_data',
			],
		];
		register_rest_route( self::REST_NAMESPACE, self::ENDPOINT_SINGULAR, $route_args );
	}

	/**
	 * Determines whether or not data can be retrieved for the registered endpoints.
	 *
	 * @return bool Whether or not data can be retrieved.
	 */
	public function can_retrieve_data() {
		return current_user_can( self::CAPABILITY_RETRIEVE );
	}
}

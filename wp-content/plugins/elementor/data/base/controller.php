<?php
namespace Elementor\Data\Base;

use Elementor\Data\Manager;
use Elementor\Plugin;
use WP_REST_Controller;
use WP_REST_Server;

abstract class Controller extends WP_REST_Controller {
	/**
	 * Loaded endpoint(s).
	 *
	 * @var \Elementor\Data\Base\Endpoint[]
	 */
	public $endpoints = [];

	/**
	 * Loaded processor(s).
	 *
	 * @var \Elementor\Data\Base\Processor[][]
	 */
	public $processors = [];

	/**
	 * Controller constructor.
	 *
	 * Register endpoints on 'rest_api_init'.
	 */
	public function __construct() {
		// TODO: Controllers and endpoints can have common interface.

		// TODO: Uncomment when native 3rd plugins uses V2.
		// $this->deprecated();

		$this->namespace = Manager::ROOT_NAMESPACE . '/v' . Manager::VERSION;
		$this->rest_base = Manager::REST_BASE . $this->get_name();

		add_action( 'rest_api_init', function () {
			$this->register(); // Because 'register' is protected.
		} );

		/**
		 * Since all actions were removed for custom internal REST server.
		 * Re-add the actions.
		 */
		add_action( 'elementor_rest_api_before_init', function () {
			add_action( 'rest_api_init', function() {
				$this->register();
			} );
		} );
	}

	/**
	 * Get controller name.
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get controller namespace.
	 *
	 * @return string
	 */
	public function get_namespace() {
		return $this->namespace;
	}

	/**
	 * Get controller reset base.
	 *
	 * @return string
	 */
	public function get_rest_base() {
		return $this->rest_base;
	}

	/**
	 * Get controller route.
	 *
	 * @return string
	 */
	public function get_controller_route() {
		return $this->get_namespace() . '/' . $this->get_rest_base();
	}

	/**
	 * Retrieves the index for a controller.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_controller_index() {
		$server = rest_get_server();
		$routes = $server->get_routes();

		$endpoints = array_intersect_key( $server->get_routes(), $routes );

		$controller_route = $this->get_controller_route();

		array_walk( $endpoints, function ( &$item, $endpoint ) use ( &$endpoints, $controller_route ) {
			if ( ! strstr( $endpoint, $controller_route ) ) {
				unset( $endpoints[ $endpoint ] );
			}
		} );

		$data = [
			'namespace' => $this->get_namespace(),
			'controller' => $controller_route,
			'routes' => $server->get_data_for_routes( $endpoints ),
		];

		$response = rest_ensure_response( $data );

		// Link to the root index.
		$response->add_link( 'up', rest_url( '/' ) );

		return $response;
	}

	/**
	 * Get processors.
	 *
	 * @param string $command
	 *
	 * @return \Elementor\Data\Base\Processor[]
	 */
	public function get_processors( $command ) {
		$result = [];

		if ( isset( $this->processors[ $command ] ) ) {
			$result = $this->processors[ $command ];
		}

		return $result;
	}

	public function get_items( $request ) {
		return $this->get_controller_index();
	}

	/**
	 * Creates multiple items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function create_items( $request ) {
		return new \WP_Error( 'invalid-method', sprintf( "Method '%s' not implemented. Must be overridden in subclass.", __METHOD__ ), [ 'status' => 405 ] );
	}

	/**
	 * Updates multiple items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function update_items( $request ) {
		return new \WP_Error( 'invalid-method', sprintf( "Method '%s' not implemented. Must be overridden in subclass.", __METHOD__ ), [ 'status' => 405 ] );
	}

	/**
	 * Delete multiple items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function delete_items( $request ) {
		return new \WP_Error( 'invalid-method', sprintf( "Method '%s' not implemented. Must be overridden in subclass.", __METHOD__ ), [ 'status' => 405 ] );
	}

	/**
	 * Register endpoints.
	 */
	abstract public function register_endpoints();

	/**
	 * Register processors.
	 */
	public function register_processors() {
	}

	/**
	 * Register internal endpoints.
	 */
	protected function register_internal_endpoints() {
		register_rest_route( $this->get_namespace(), '/' . $this->get_rest_base(), [
			[
				'methods' => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_items' ],
				'args' => [],
				'permission_callback' => function ( $request ) {
					return $this->get_permission_callback( $request );
				},
			],
		] );
	}

	/**
	 * Register endpoint.
	 *
	 * @param string $endpoint_class
	 *
	 * @return \Elementor\Data\Base\Endpoint
	 */
	protected function register_endpoint( $endpoint_class ) {
		$endpoint_instance = new $endpoint_class( $this );

		// TODO: Validate instance like in register_sub_endpoint().

		$endpoint_route = $this->get_name() . '/' . $endpoint_instance->get_name();

		$this->endpoints[ $endpoint_route ] = $endpoint_instance;

		$command = $endpoint_route;
		$format = $endpoint_instance::get_format();

		if ( $command ) {
			$format = $command . '/' . $format;
		} else {
			$format = $format . $command;
		}

		// `$e.data.registerFormat()`.
		Manager::instance()->register_endpoint_format( $command, $format );

		return $endpoint_instance;
	}

	/**
	 * Register a processor.
	 *
	 * That will be later attached to the endpoint class.
	 *
	 * @param string $processor_class
	 *
	 * @return \Elementor\Data\Base\Processor $processor_instance
	 */
	protected function register_processor( $processor_class ) {
		$processor_instance = new $processor_class( $this );

		// TODO: Validate processor instance.

		$command = $processor_instance->get_command();

		if ( ! isset( $this->processors[ $command ] ) ) {
			$this->processors[ $command ] = [];
		}

		$this->processors[ $command ] [] = $processor_instance;

		return $processor_instance;
	}

	/**
	 * Register.
	 *
	 * Endpoints & processors.
	 */
	protected function register() {
		$this->register_internal_endpoints();
		$this->register_endpoints();

		// Aka hooks.
		$this->register_processors();
	}

	/**
	 * Retrieves a recursive collection of all endpoint(s), items.
	 *
	 * Get items recursive, will run overall endpoints of the current controller.
	 * For each endpoint it will run `$endpoint->getItems( $request ) // the $request passed in get_items_recursive`.
	 * Will skip $skip_endpoints endpoint(s).
	 *
	 * Example, scenario:
	 * Controller 'test-controller'.
	 * Controller endpoints: 'endpoint1', 'endpoint2'.
	 * Endpoint2 get_items method: `get_items() { return 'test' }`.
	 * Call `Controller.get_items_recursive( ['endpoint1'] )`, result: [ 'endpoint2' => 'test' ];
	 *
	 * @param array $skip_endpoints
	 *
	 * @return array
	 */
	public function get_items_recursive( $skip_endpoints = [] ) {
		$response = [];

		foreach ( $this->endpoints as $endpoint ) {
			// Skip self.
			if ( in_array( $endpoint, $skip_endpoints, true ) ) {
				continue;
			}

			$response[ $endpoint->get_name() ] = $endpoint->get_items( null );
		}

		return $response;
	}

	/**
	 * Get permission callback.
	 *
	 * Default controller permission callback.
	 * By default endpoint will inherit the permission callback from the controller.
	 * By default permission is `current_user_can( 'manage_options' );`.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function get_permission_callback( $request ) {
		// The function is public since endpoint need to access it.
		switch ( $request->get_method() ) {
			case 'GET':
			case 'POST':
			case 'UPDATE':
			case 'PUT':
			case 'DELETE':
			case 'PATCH':
				return current_user_can( 'manage_options' );
		}

		return false;
	}

	private static $notify_deprecated = true;

	private function deprecated() {
		add_action( 'elementor/init', function () {
			if ( ! self::$notify_deprecated ) {
				return;
			}

			Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function(
				'Elementor\Data\Manager',
				'3.5.0',
				'Elementor\Data\V2\Manager'
			);

			self::$notify_deprecated = false;
		} );
	}
}

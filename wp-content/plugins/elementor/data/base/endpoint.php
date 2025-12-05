<?php

namespace Elementor\Data\Base;

use Elementor\Data\Manager;
use WP_REST_Server;

abstract class Endpoint {

	const AVAILABLE_METHODS = [
		WP_REST_Server::READABLE,
		WP_REST_Server::CREATABLE,
		WP_REST_Server::EDITABLE,
		WP_REST_Server::DELETABLE,
		WP_REST_Server::ALLMETHODS,
	];

	/**
	 * Controller of current endpoint.
	 *
	 * @var \Elementor\Data\Base\Controller
	 */
	protected $controller;

	/**
	 * Loaded sub endpoint(s).
	 *
	 * @var \Elementor\Data\Base\SubEndpoint[]
	 */
	private $sub_endpoints = [];

	/**
	 * Get format suffix.
	 *
	 * Examples:
	 * '{one_parameter_name}'.
	 * '{one_parameter_name}/{two_parameter_name}/'.
	 * '{one_parameter_name}/whatever/anything/{two_parameter_name}/' and so on for each endpoint or sub-endpoint.
	 *
	 * @return string current location will later be added automatically.
	 */
	public static function get_format() {
		return '';
	}

	/**
	 * Endpoint constructor.
	 *
	 * @param \Elementor\Data\Base\Controller $controller
	 *
	 * @throws \Exception If invalid controller.
	 */
	public function __construct( $controller ) {
		if ( ! ( $controller instanceof Controller ) ) {
			throw new \Exception( 'Invalid controller.' );
		}

		$this->controller = $controller;
		$this->register();
	}

	/**
	 * Get endpoint name.
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get base route.
	 *
	 * Removing 'index' from endpoint.
	 *
	 * @return string
	 */
	public function get_base_route() {
		$endpoint_name = $this->get_name();

		// TODO: Allow this only for internal routes.
		// TODO: Make difference between internal and external endpoints.
		if ( 'index' === $endpoint_name ) {
			$endpoint_name = '';
		}

		return '/' . $this->controller->get_rest_base() . '/' . $endpoint_name;
	}

	/**
	 * Register the endpoint.
	 *
	 * By default: register get items route.
	 *
	 * @throws \Exception If invalid endpoint registered.
	 */
	protected function register() {
		$this->register_items_route();
	}

	/**
	 * Register sub endpoint.
	 *
	 * @param string $route
	 * @param string $endpoint_class
	 *
	 * @return \Elementor\Data\Base\SubEndpoint
	 * @throws \Exception If invalid sub endpoint registered.
	 */
	protected function register_sub_endpoint( $route, $endpoint_class ) {
		$endpoint_instance = new $endpoint_class( $route, $this );

		if ( ! ( $endpoint_instance instanceof SubEndpoint ) ) {
			throw new \Exception( 'Invalid endpoint instance.' );
		}

		$endpoint_route = $route . '/' . $endpoint_instance->get_name();

		$this->sub_endpoints[ $endpoint_route ] = $endpoint_instance;

		$component_name = $endpoint_instance->controller->get_rest_base();
		$parent_instance = $endpoint_instance->get_parent();
		$parent_name = $endpoint_instance->get_name();
		$parent_format_suffix = $parent_instance::get_format();
		$current_format_suffix = $endpoint_instance::get_format();

		$command = $component_name . '/' . $parent_name;
		$format = $component_name . '/' . $parent_format_suffix . '/' . $parent_name . '/' . $current_format_suffix;

		Manager::instance()->register_endpoint_format( $command, $format );

		return $endpoint_instance;
	}

	/**
	 * Base callback.
	 *
	 * All reset requests from the client should pass this function.
	 *
	 * @param string           $methods
	 * @param \WP_REST_Request $request
	 * @param bool             $is_multi
	 *
	 * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 * @throws \Exception If invalid method.
	 */
	public function base_callback( $methods, $request, $is_multi = false ) {
		// TODO: Find better solution.
		$json_params = $request->get_json_params();

		if ( $json_params ) {
			$request->set_body_params( $json_params );
		}

		// TODO: Handle permission callback.
		switch ( $methods ) {
			case WP_REST_Server::READABLE:
				$result = $is_multi ? $this->get_items( $request ) : $this->get_item( $request->get_param( 'id' ), $request );
				break;

			case WP_REST_Server::CREATABLE:
				$result = $is_multi ? $this->create_items( $request ) : $this->create_item( $request->get_param( 'id' ), $request );
				break;

			case WP_REST_Server::EDITABLE:
				$result = $is_multi ? $this->update_items( $request ) : $this->update_item( $request->get_param( 'id' ), $request );
				break;

			case WP_REST_Server::DELETABLE:
				$result = $is_multi ? $this->delete_items( $request ) : $this->delete_item( $request->get_param( 'id' ), $request );
				break;

			default:
				throw new \Exception( 'Invalid method.' );
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Retrieves a collection of items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		return $this->controller->get_items( $request );
	}

	/**
	 * Retrieves one item from the collection.
	 *
	 * @param string           $id
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $id, $request ) {
		return $this->controller->get_item( $request );
	}

	/**
	 * Get permission callback.
	 *
	 * By default get permission callback from the controller.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return boolean
	 */
	public function get_permission_callback( $request ) {
		return $this->controller->get_permission_callback( $request );
	}

	/**
	 * Creates one item.
	 *
	 * @param string           $id id of request item.
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function create_item( $id, $request ) {
		return $this->controller->create_item( $request );
	}

	/**
	 * Creates multiple items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function create_items( $request ) {
		return $this->controller->create_items( $request );
	}

	/**
	 * Updates one item.
	 *
	 * @param string           $id id of request item.
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function update_item( $id, $request ) {
		return $this->controller->update_item( $request );
	}

	/**
	 * Updates multiple items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function update_items( $request ) {
		return $this->controller->update_items( $request );
	}

	/**
	 * Delete one item.
	 *
	 * @param string           $id id of request item.
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $id, $request ) {
		return $this->controller->delete_item( $request );
	}

	/**
	 * Delete multiple items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function delete_items( $request ) {
		return $this->controller->delete_items( $request );
	}

	/**
	 * Register item route.
	 *
	 * @param string $methods
	 * @param array  $args
	 * @param string $route
	 *
	 * @throws \Exception If invalid method.
	 */
	public function register_item_route( $methods = WP_REST_Server::READABLE, $args = [], $route = '/' ) {
		$args = array_merge( [
			'id' => [
				'description' => 'Unique identifier for the object.',
				'type' => 'string',
			],
		], $args );

		if ( isset( $args['id'] ) && $args['id'] ) {
			$route .= '(?P<id>[\w]+)/';
		}

		$this->register_route( $route, $methods, function ( $request ) use ( $methods ) {
			return $this->base_callback( $methods, $request );
		}, $args );
	}

	/**
	 * Register items route.
	 *
	 * @param string $methods
	 *
	 * @throws \Exception If invalid method.
	 */
	public function register_items_route( $methods = WP_REST_Server::READABLE ) {
		$this->register_route( '', $methods, function ( $request ) use ( $methods ) {
			return $this->base_callback( $methods, $request, true );
		} );
	}

	/**
	 * Register route.
	 *
	 * @param string $route
	 * @param string $methods
	 * @param null   $callback
	 * @param array  $args
	 *
	 * @return bool
	 * @throws \Exception If invalid method.
	 */
	public function register_route( $route = '', $methods = WP_REST_Server::READABLE, $callback = null, $args = [] ) {
		if ( ! in_array( $methods, self::AVAILABLE_METHODS, true ) ) {
			throw new \Exception( 'Invalid method.' );
		}

		$route = $this->get_base_route() . $route;

		return register_rest_route( $this->controller->get_namespace(), $route, [
			[
				'args' => $args,
				'methods' => $methods,
				'callback' => $callback,
				'permission_callback' => function ( $request ) {
					return $this->get_permission_callback( $request );
				},
			],
		] );
	}
}

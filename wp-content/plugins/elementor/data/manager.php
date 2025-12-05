<?php
namespace Elementor\Data;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Data\Base\Processor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Manager extends BaseModule {

	const ROOT_NAMESPACE = 'elementor';

	const REST_BASE = '';

	const VERSION = '1';

	/**
	 * @var \WP_REST_Server
	 */
	private $server;

	/**
	 * @var boolean
	 */
	private $is_internal = false;

	/**
	 * @var array
	 */
	private $cache = [];

	/**
	 * Loaded controllers.
	 *
	 * @var \Elementor\Data\Base\Controller[]
	 */
	public $controllers = [];

	/**
	 * Loaded command(s) format.
	 *
	 * @var string[]
	 */
	public $command_formats = [];

	/**
	 * Fix issue with 'Potentially polymorphic call. The code may be inoperable depending on the actual class instance passed as the argument.'.
	 *
	 * @return \Elementor\Core\Base\Module|\Elementor\Data\Manager
	 */
	public static function instance() {
		return ( parent::instance() );
	}

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_rest_error_handler' ] );
	}

	public function get_name() {
		return 'data-manager';
	}

	/**
	 * @return \Elementor\Data\Base\Controller[]
	 */
	public function get_controllers() {
		return $this->controllers;
	}

	private function get_cache( $key ) {
		return self::get_items( $this->cache, $key );
	}

	private function set_cache( $key, $value ) {
		$this->cache[ $key ] = $value;
	}

	/**
	 * Register controller.
	 *
	 * @param string $controller_class_name
	 *
	 * @return \Elementor\Data\Base\Controller
	 */
	public function register_controller( $controller_class_name ) {
		$controller_instance = new $controller_class_name();

		return $this->register_controller_instance( $controller_instance );
	}

	/**
	 * Register controller instance.
	 *
	 * @param \Elementor\Data\Base\Controller $controller_instance
	 *
	 * @return \Elementor\Data\Base\Controller
	 */
	public function register_controller_instance( $controller_instance ) {
		// TODO: Validate instance.

		$this->controllers[ $controller_instance->get_name() ] = $controller_instance;

		return $controller_instance;
	}

	/**
	 * Register endpoint format.
	 *
	 * @param string $command
	 * @param string $format
	 */
	public function register_endpoint_format( $command, $format ) {
		$this->command_formats[ $command ] = rtrim( $format, '/' );
	}

	public function register_rest_error_handler() {
		if ( ! $this->is_internal() ) {
			$logger_manager = \Elementor\Core\Logger\Manager::instance();
			$logger_manager->register_error_handler();
		}
	}

	/**
	 * Find controller instance.
	 *
	 * By given command name.
	 *
	 * @param string $command
	 *
	 * @return false|\Elementor\Data\Base\Controller
	 */
	public function find_controller_instance( $command ) {
		$command_parts = explode( '/', $command );
		$assumed_command_parts = [];

		foreach ( $command_parts as $command_part ) {
			$assumed_command_parts [] = $command_part;

			foreach ( $this->controllers as $controller_name => $controller ) {
				$assumed_command = implode( '/', $assumed_command_parts );

				if ( $assumed_command === $controller_name ) {
					return $controller;
				}
			}
		}

		return false;
	}

	/**
	 * Command extract args.
	 *
	 * @param string $command
	 * @param array  $args
	 *
	 * @return \stdClass
	 */
	public function command_extract_args( $command, $args = [] ) {
		$result = new \stdClass();
		$result->command = $command;
		$result->args = $args;

		if ( false !== strpos( $command, '?' ) ) {
			$command_parts = explode( '?', $command );
			$pure_command = $command_parts[0];
			$query_string = $command_parts[1];

			parse_str( $query_string, $temp );

			$result->command = rtrim( $pure_command, '/' );
			$result->args = array_merge( $args, $temp );
		}

		return $result;
	}

	/**
	 * Command to endpoint.
	 *
	 * Format is required otherwise $command will returned.
	 *
	 * @param string $command
	 * @param string $format
	 * @param array  $args
	 *
	 * @return string endpoint
	 */
	public function command_to_endpoint( $command, $format, $args ) {
		$endpoint = $command;

		if ( $format ) {
			$formatted = $format;

			array_walk( $args, function ( $val, $key ) use ( &$formatted ) {
				$formatted = str_replace( '{' . $key . '}', $val, $formatted );
			} );

			// Remove remaining format if not requested via `$args`.
			if ( strstr( $formatted, '/{' ) ) {
				/**
				 * Example:
				 * $command = 'example/documents';
				 * $format = 'example/documents/{document_id}/elements/{element_id}';
				 * $formatted = 'example/documents/1618/elements/{element_id}';
				 * Result:
				 * $formatted = 'example/documents/1618/elements';
				 */
				$formatted = substr( $formatted, 0, strpos( $formatted, '/{' ) );
			}

			$endpoint = $formatted;
		}

		return $endpoint;
	}

	/**
	 * Run server.
	 *
	 * Init WordPress reset api.
	 *
	 * @return \WP_REST_Server
	 */
	public function run_server() {
		/**
		 * If run_server() called means, that rest api is simulated from the backend.
		 */
		$this->is_internal = true;

		if ( ! $this->server ) {
			// Remove all 'rest_api_init' actions.
			remove_all_actions( 'rest_api_init' );

			// Call custom reset api loader.
			do_action( 'elementor_rest_api_before_init' );

			$this->server = rest_get_server(); // Init API.
		}

		return $this->server;
	}

	/**
	 * Kill server.
	 *
	 * Free server and controllers.
	 */
	public function kill_server() {
		global $wp_rest_server;

		$this->controllers = [];
		$this->command_formats = [];
		$this->server = false;
		$this->is_internal = false;
		$this->cache = [];
		$wp_rest_server = false;
	}

	/**
	 * Run processor.
	 *
	 * @param Processor $processor
	 * @param array     $data
	 *
	 * @return mixed
	 */
	public function run_processor( $processor, $data ) {
		if ( call_user_func_array( [ $processor, 'get_conditions' ], $data ) ) {
			return call_user_func_array( [ $processor, 'apply' ], $data );
		}

		return null;
	}

	/**
	 * Run processors.
	 *
	 * Filter them by class.
	 *
	 * @param Processor[] $processors
	 * @param string      $filter_by_class
	 * @param array       $data
	 *
	 * @return false|array
	 */
	public function run_processors( $processors, $filter_by_class, $data ) {
		foreach ( $processors as $processor ) {
			if ( $processor instanceof $filter_by_class ) {
				if ( Processor\Before::class === $filter_by_class ) {
					$this->run_processor( $processor, $data );
				} elseif ( Processor\After::class === $filter_by_class ) {
					$result = $this->run_processor( $processor, $data );
					if ( $result ) {
						$data[1] = $result;
					}
				} else {
					// TODO: error
					break;
				}
			}
		}

		return isset( $data[1] ) ? $data[1] : false;
	}

	/**
	 * Run request.
	 *
	 * Simulate rest API from within the backend.
	 * Use args as query.
	 *
	 * @param string $endpoint
	 * @param array  $args
	 * @param string $method
	 *
	 * @return \WP_REST_Response
	 */
	private function run_request( $endpoint, $args, $method ) {
		$this->run_server();

		$endpoint = '/' . self::ROOT_NAMESPACE . '/v' . self::VERSION . '/' . $endpoint;

		// Run reset api.
		$request = new \WP_REST_Request( $method, $endpoint );

		if ( 'GET' === $method ) {
			$request->set_query_params( $args );
		} else {
			$request->set_body_params( $args );
		}

		return rest_do_request( $request );
	}

	/**
	 * Run endpoint.
	 *
	 * Wrapper for `$this->run_request` return `$response->getData()` instead of `$response`.
	 *
	 * @param string $endpoint
	 * @param array  $args
	 * @param string $method
	 *
	 * @return array
	 */
	public function run_endpoint( $endpoint, $args = [], $method = 'GET' ) {
		$response = $this->run_request( $endpoint, $args, $method );

		return $response->get_data();
	}

	/**
	 * Run ( simulated reset api ).
	 *
	 * Do:
	 * Init reset server.
	 * Run before processors.
	 * Run command as reset api endpoint from internal.
	 * Run after processors.
	 *
	 * @param string $command
	 * @param array  $args
	 * @param string $method
	 *
	 * @return array|false processed result
	 */
	public function run( $command, $args = [], $method = 'GET' ) {
		$key = crc32( $command . '-' . wp_json_encode( $args ) . '-' . $method );
		$cache = $this->get_cache( $key );

		if ( $cache ) {
			return $cache;
		}

		$this->run_server();

		$controller_instance = $this->find_controller_instance( $command );

		if ( ! $controller_instance ) {
			$this->set_cache( $key, [] );
			return [];
		}

		$extracted_command = $this->command_extract_args( $command, $args );
		$command = $extracted_command->command;
		$args = $extracted_command->args;

		$format = isset( $this->command_formats[ $command ] ) ? $this->command_formats[ $command ] : false;

		$command_processors = $controller_instance->get_processors( $command );

		$endpoint = $this->command_to_endpoint( $command, $format, $args );

		$this->run_processors( $command_processors, Processor\Before::class, [ $args ] );

		$response = $this->run_request( $endpoint, $args, $method );
		$result = $response->get_data();

		if ( $response->is_error() ) {
			$this->set_cache( $key, [] );
			return [];
		}

		$result = $this->run_processors( $command_processors, Processor\After::class, [ $args, $result ] );

		$this->set_cache( $key, $result );

		return $result;
	}

	public function is_internal() {
		return $this->is_internal;
	}
}

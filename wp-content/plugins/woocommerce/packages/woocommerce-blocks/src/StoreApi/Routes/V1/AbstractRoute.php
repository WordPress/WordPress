<?php
namespace Automattic\WooCommerce\StoreApi\Routes\V1;

use Automattic\WooCommerce\StoreApi\SchemaController;
use Automattic\WooCommerce\StoreApi\Routes\RouteInterface;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\Exceptions\InvalidCartException;
use Automattic\WooCommerce\StoreApi\Schemas\v1\AbstractSchema;
use WP_Error;

/**
 * AbstractRoute class.
 */
abstract class AbstractRoute implements RouteInterface {
	/**
	 * Schema class instance.
	 *
	 * @var AbstractSchema
	 */
	protected $schema;

	/**
	 * Route namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/store/v1';

	/**
	 * Schema Controller instance.
	 *
	 * @var SchemaController
	 */
	protected $schema_controller;

	/**
	 * The routes schema.
	 *
	 * @var string
	 */
	const SCHEMA_TYPE = '';

	/**
	 * The routes schema version.
	 *
	 * @var integer
	 */
	const SCHEMA_VERSION = 1;

	/**
	 * Constructor.
	 *
	 * @param SchemaController $schema_controller Schema Controller instance.
	 * @param AbstractSchema   $schema Schema class for this route.
	 */
	public function __construct( SchemaController $schema_controller, AbstractSchema $schema ) {
		$this->schema_controller = $schema_controller;
		$this->schema            = $schema;
	}

	/**
	 * Get the namespace for this route.
	 *
	 * @return string
	 */
	public function get_namespace() {
		return $this->namespace;
	}

	/**
	 * Set the namespace for this route.
	 *
	 * @param string $namespace Given namespace.
	 */
	public function set_namespace( $namespace ) {
		$this->namespace = $namespace;
	}

	/**
	 * Get item schema properties.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		return $this->schema->get_item_schema();
	}

	/**
	 * Get the route response based on the type of request.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_response( \WP_REST_Request $request ) {
		$response = null;

		try {
			$response = $this->get_response_by_request_method( $request );
		} catch ( RouteException $error ) {
			$response = $this->get_route_error_response( $error->getErrorCode(), $error->getMessage(), $error->getCode(), $error->getAdditionalData() );
		} catch ( InvalidCartException $error ) {
			$response = $this->get_route_error_response_from_object( $error->getError(), $error->getCode(), $error->getAdditionalData() );
		} catch ( \Exception $error ) {
			$response = $this->get_route_error_response( 'woocommerce_rest_unknown_server_error', $error->getMessage(), 500 );
		}

		return is_wp_error( $response ) ? $this->error_to_response( $response ) : $response;
	}

	/**
	 * Get the route response based on the type of request.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	protected function get_response_by_request_method( \WP_REST_Request $request ) {
		switch ( $request->get_method() ) {
			case 'POST':
				return $this->get_route_post_response( $request );
			case 'PUT':
			case 'PATCH':
				return $this->get_route_update_response( $request );
			case 'DELETE':
				return $this->get_route_delete_response( $request );
		}
		return $this->get_route_response( $request );
	}

	/**
	 * Converts an error to a response object. Based on \WP_REST_Server.
	 *
	 * @param \WP_Error $error WP_Error instance.
	 * @return \WP_REST_Response List of associative arrays with code and message keys.
	 */
	protected function error_to_response( $error ) {
		$error_data = $error->get_error_data();
		$status     = isset( $error_data, $error_data['status'] ) ? $error_data['status'] : 500;
		$errors     = [];

		foreach ( (array) $error->errors as $code => $messages ) {
			foreach ( (array) $messages as $message ) {
				$errors[] = array(
					'code'    => $code,
					'message' => $message,
					'data'    => $error->get_error_data( $code ),
				);
			}
		}

		$data = array_shift( $errors );

		if ( count( $errors ) ) {
			$data['additional_errors'] = $errors;
		}

		return new \WP_REST_Response( $data, $status );
	}

	/**
	 * Get route response for GET requests.
	 *
	 * When implemented, should return a \WP_REST_Response.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 */
	protected function get_route_response( \WP_REST_Request $request ) {
		throw new RouteException( 'woocommerce_rest_invalid_endpoint', __( 'Method not implemented', 'woocommerce' ), 404 );
	}

	/**
	 * Get route response for POST requests.
	 *
	 * When implemented, should return a \WP_REST_Response.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 */
	protected function get_route_post_response( \WP_REST_Request $request ) {
		throw new RouteException( 'woocommerce_rest_invalid_endpoint', __( 'Method not implemented', 'woocommerce' ), 404 );
	}

	/**
	 * Get route response for PUT requests.
	 *
	 * When implemented, should return a \WP_REST_Response.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 */
	protected function get_route_update_response( \WP_REST_Request $request ) {
		throw new RouteException( 'woocommerce_rest_invalid_endpoint', __( 'Method not implemented', 'woocommerce' ), 404 );
	}

	/**
	 * Get route response for DELETE requests.
	 *
	 * When implemented, should return a \WP_REST_Response.
	 *
	 * @throws RouteException On error.
	 * @param \WP_REST_Request $request Request object.
	 */
	protected function get_route_delete_response( \WP_REST_Request $request ) {
		throw new RouteException( 'woocommerce_rest_invalid_endpoint', __( 'Method not implemented', 'woocommerce' ), 404 );
	}

	/**
	 * Get route response when something went wrong.
	 *
	 * @param string $error_code String based error code.
	 * @param string $error_message User facing error message.
	 * @param int    $http_status_code HTTP status. Defaults to 500.
	 * @param array  $additional_data  Extra data (key value pairs) to expose in the error response.
	 * @return \WP_Error WP Error object.
	 */
	protected function get_route_error_response( $error_code, $error_message, $http_status_code = 500, $additional_data = [] ) {
		return new \WP_Error( $error_code, $error_message, array_merge( $additional_data, [ 'status' => $http_status_code ] ) );
	}

	/**
	 * Get route response when something went wrong and the supplied error is a WP_Error. This currently only happens
	 * when an item in the cart is out of stock, partially out of stock, can only be bought individually, or when the
	 * item is not purchasable.
	 *
	 * @param WP_Error $error_object The WP_Error object containing the error.
	 * @param int      $http_status_code HTTP status. Defaults to 500.
	 * @param array    $additional_data  Extra data (key value pairs) to expose in the error response.
	 * @return WP_Error WP Error object.
	 */
	protected function get_route_error_response_from_object( $error_object, $http_status_code = 500, $additional_data = [] ) {
		$error_object->add_data( array_merge( $additional_data, [ 'status' => $http_status_code ] ) );
		return $error_object;
	}

	/**
	 * Prepare a single item for response.
	 *
	 * @param mixed            $item Item to format to schema.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response $response Response data.
	 */
	public function prepare_item_for_response( $item, \WP_REST_Request $request ) {
		$response = rest_ensure_response( $this->schema->get_item_response( $item ) );
		$response->add_links( $this->prepare_links( $item, $request ) );

		return $response;
	}

	/**
	 * Retrieves the context param.
	 *
	 * Ensures consistent descriptions between endpoints, and populates enum from schema.
	 *
	 * @param array $args Optional. Additional arguments for context parameter. Default empty array.
	 * @return array Context parameter details.
	 */
	protected function get_context_param( $args = array() ) {
		$param_details = array(
			'description'       => __( 'Scope under which the request is made; determines fields present in response.', 'woocommerce' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$schema = $this->get_item_schema();

		if ( empty( $schema['properties'] ) ) {
			return array_merge( $param_details, $args );
		}

		$contexts = array();

		foreach ( $schema['properties'] as $attributes ) {
			if ( ! empty( $attributes['context'] ) ) {
				$contexts = array_merge( $contexts, $attributes['context'] );
			}
		}

		if ( ! empty( $contexts ) ) {
			$param_details['enum'] = array_unique( $contexts );
			rsort( $param_details['enum'] );
		}

		return array_merge( $param_details, $args );
	}

	/**
	 * Prepares a response for insertion into a collection.
	 *
	 * @param \WP_REST_Response $response Response object.
	 * @return array|mixed Response data, ready for insertion into collection data.
	 */
	protected function prepare_response_for_collection( \WP_REST_Response $response ) {
		$data   = (array) $response->get_data();
		$server = rest_get_server();
		$links  = $server::get_compact_response_links( $response );

		if ( ! empty( $links ) ) {
			$data['_links'] = $links;
		}

		return $data;
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param mixed            $item Item to prepare.
	 * @param \WP_REST_Request $request Request object.
	 * @return array
	 */
	protected function prepare_links( $item, $request ) {
		return [];
	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param(),
		);
	}
}

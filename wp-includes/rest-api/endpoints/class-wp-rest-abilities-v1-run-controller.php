<?php
/**
 * REST API run controller for Abilities API.
 *
 * @package WordPress
 * @subpackage Abilities_API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Core controller used to execute abilities via the REST API.
 *
 * @since 6.9.0
 *
 * @see WP_REST_Controller
 */
class WP_REST_Abilities_V1_Run_Controller extends WP_REST_Controller {

	/**
	 * REST API namespace.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $namespace = 'wp-abilities/v1';

	/**
	 * REST API base route.
	 *
	 * @since 6.9.0
	 * @var string
	 */
	protected $rest_base = 'abilities';

	/**
	 * Registers the routes for ability execution.
	 *
	 * @since 6.9.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<name>[a-zA-Z0-9\-\/]+?)/run',
			array(
				'args'   => array(
					'name' => array(
						'description' => __( 'Unique identifier for the ability.' ),
						'type'        => 'string',
						'pattern'     => '^[a-zA-Z0-9\-\/]+$',
					),
				),

				// TODO: We register ALLMETHODS because at route registration time, we don't know which abilities
				// exist or their annotations (`destructive`, `idempotent`, `readonly`). This is due to WordPress
				// load order - routes are registered early, before plugins have registered their abilities.
				// This approach works but could be improved with lazy route registration or a different
				// architecture that allows type-specific routes after abilities are registered.
				// This was the same issue that we ended up seeing with the Feature API.
				array(
					'methods'             => WP_REST_Server::ALLMETHODS,
					'callback'            => array( $this, 'execute_ability' ),
					'permission_callback' => array( $this, 'check_ability_permissions' ),
					'args'                => $this->get_run_args(),
				),
				'schema' => array( $this, 'get_run_schema' ),
			)
		);
	}

	/**
	 * Executes an ability.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function execute_ability( $request ) {
		$ability = wp_get_ability( $request['name'] );
		if ( ! $ability ) {
			return new WP_Error(
				'rest_ability_not_found',
				__( 'Ability not found.' ),
				array( 'status' => 404 )
			);
		}

		$input  = $this->get_input_from_request( $request );
		$result = $ability->execute( $input );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Validates if the HTTP method matches the expected method for the ability based on its annotations.
	 *
	 * @since 6.9.0
	 *
	 * @param string                     $request_method The HTTP method of the request.
	 * @param array<string, (null|bool)> $annotations    The ability annotations.
	 * @return true|WP_Error True on success, or WP_Error object on failure.
	 */
	public function validate_request_method( string $request_method, array $annotations ) {
		$expected_method = 'POST';
		if ( ! empty( $annotations['readonly'] ) ) {
			$expected_method = 'GET';
		} elseif ( ! empty( $annotations['destructive'] ) && ! empty( $annotations['idempotent'] ) ) {
			$expected_method = 'DELETE';
		}

		if ( $expected_method === $request_method ) {
			return true;
		}

		$error_message = __( 'Abilities that perform updates require POST method.' );
		if ( 'GET' === $expected_method ) {
			$error_message = __( 'Read-only abilities require GET method.' );
		} elseif ( 'DELETE' === $expected_method ) {
			$error_message = __( 'Abilities that perform destructive actions require DELETE method.' );
		}
		return new WP_Error(
			'rest_ability_invalid_method',
			$error_message,
			array( 'status' => 405 )
		);
	}

	/**
	 * Checks if a given request has permission to execute a specific ability.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has execution permission, WP_Error object otherwise.
	 */
	public function check_ability_permissions( $request ) {
		$ability = wp_get_ability( $request['name'] );
		if ( ! $ability || ! $ability->get_meta_item( 'show_in_rest' ) ) {
			return new WP_Error(
				'rest_ability_not_found',
				__( 'Ability not found.' ),
				array( 'status' => 404 )
			);
		}

		$is_valid = $this->validate_request_method(
			$request->get_method(),
			$ability->get_meta_item( 'annotations' )
		);
		if ( is_wp_error( $is_valid ) ) {
			return $is_valid;
		}

		$input = $this->get_input_from_request( $request );
		$input = $ability->normalize_input( $input );
		if ( is_wp_error( $input ) ) {
			return $this->ensure_error_status( $input, 400 );
		}

		$is_valid = $ability->validate_input( $input );
		if ( is_wp_error( $is_valid ) ) {
			return $this->ensure_error_status( $is_valid, 400 );
		}

		$result = $ability->check_permissions( $input );
		if ( is_wp_error( $result ) ) {
			$result->add_data( array( 'status' => rest_authorization_required_code() ) );
			return $result;
		}
		if ( ! $result ) {
			return new WP_Error(
				'rest_ability_cannot_execute',
				__( 'Sorry, you are not allowed to execute this ability.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Ensures a WP_Error object carries an HTTP status, adding a default when none is set.
	 *
	 * @since 7.1.0
	 *
	 * @param WP_Error $error  Error object to update.
	 * @param int      $status HTTP status code to add if not already present.
	 * @return WP_Error The error object, with a default status when needed.
	 */
	private function ensure_error_status( WP_Error $error, int $status ): WP_Error {
		$error_data = $error->get_error_data();
		if ( ! is_array( $error_data ) || ! isset( $error_data['status'] ) ) {
			$error->add_data( array( 'status' => $status ) );
		}

		return $error;
	}

	/**
	 * Extracts input parameters from the request.
	 *
	 * @since 6.9.0
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return mixed|null The input parameters.
	 */
	private function get_input_from_request( $request ) {
		if ( in_array( $request->get_method(), array( 'GET', 'DELETE' ), true ) ) {
			// For GET and DELETE requests, look for 'input' query parameter.
			$query_params = $request->get_query_params();
			return $query_params['input'] ?? null;
		}

		// For POST requests, look for 'input' in JSON body.
		$json_params = $request->get_json_params();
		return $json_params['input'] ?? null;
	}

	/**
	 * Sanitizes the run input by coercing it to the ability's input schema.
	 *
	 * Registered as the `input` argument `sanitize_callback` so that both
	 * `check_ability_permissions()` and `execute_ability()` receive natively typed input
	 * regardless of transport.
	 *
	 * @since 7.1.0
	 *
	 * @see WP_REST_Abilities_V1_Run_Controller::coerce_input_to_schema()
	 *
	 * @param mixed           $input   Raw input extracted from the request.
	 * @param WP_REST_Request $request The request object.
	 * @return mixed Coerced input, or the raw input when it cannot be safely coerced.
	 */
	public function sanitize_input_for_ability( $input, $request ) {
		$ability = wp_get_ability( $request['name'] );
		if ( ! $ability instanceof WP_Ability ) {
			return $input;
		}

		return $this->coerce_input_to_schema( $input, $ability );
	}

	/**
	 * Coerces raw request input to the types declared in the ability input schema.
	 *
	 * GET and DELETE deliver every scalar as a string ("10", "true") and a list as a single
	 * comma-separated string, so without coercion an ability receives raw strings where its
	 * schema declares integers, booleans, or arrays.
	 *
	 * Coercion never changes what validation accepts. Input is coerced only when
	 * {@see WP_Ability::validate_input()} already accepts it, and any error surfaced while
	 * sanitizing falls back to the raw input, so `validate_input()` stays the single authority
	 * on what is rejected.
	 *
	 * @since 7.1.0
	 *
	 * @param mixed      $input   Raw input extracted from the request.
	 * @param WP_Ability $ability The ability being executed.
	 * @return mixed Coerced input, or the raw input when it cannot be safely coerced.
	 */
	private function coerce_input_to_schema( $input, WP_Ability $ability ) {
		if ( null === $input ) {
			return $input;
		}

		$schema = $ability->get_input_schema();
		if ( empty( $schema ) ) {
			return $input;
		}

		/*
		 * Only coerce input that already validates. Sanitizing invalid input can silently
		 * change which values are accepted -- `additionalProperties: false` strips unknown
		 * keys, and a non-numeric string casts to 0 -- so leaving invalid input untouched
		 * lets validate_input() reject it exactly as it does without coercion.
		 *
		 * validate_input() is asked rather than rest_validate_value_from_schema() so that the
		 * `wp_ability_validate_input` filter decides what counts as valid here as well. A filter
		 * that overrides a schema failure accepts the input, so the input is coerced; a filter
		 * that rejects otherwise valid input leaves it untouched for validate_input() to report.
		 */
		if ( is_wp_error( $ability->validate_input( $input ) ) ) {
			return $input;
		}

		$sanitized = rest_sanitize_value_from_schema( $input, $schema, 'input' );

		/*
		 * Sanitizing can still surface an error the lenient validation above did not, such as
		 * items that are unique as strings but collide once cast to integers (`uniqueItems`).
		 * The error may be returned at the top level or nested inside the returned array, so
		 * scan recursively and fall back to the raw input on any error.
		 */
		if ( $this->input_contains_error( $sanitized ) ) {
			return $input;
		}

		return $sanitized;
	}

	/**
	 * Determines whether a sanitized value is, or contains, a WP_Error.
	 *
	 * @since 7.1.0
	 *
	 * @param mixed $value The value to inspect.
	 * @return bool True if the value is, or contains, a WP_Error.
	 */
	private function input_contains_error( $value ): bool {
		if ( is_wp_error( $value ) ) {
			return true;
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $item ) {
				if ( $this->input_contains_error( $item ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Retrieves the arguments for ability execution endpoint.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> Arguments for the run endpoint.
	 */
	public function get_run_args(): array {
		return array(
			'input' => array(
				'description'       => __( 'Input parameters for the ability execution.' ),
				'type'              => array( 'integer', 'number', 'boolean', 'string', 'array', 'object', 'null' ),
				'default'           => null,
				'sanitize_callback' => array( $this, 'sanitize_input_for_ability' ),
			),
		);
	}

	/**
	 * Retrieves the schema for ability execution endpoint.
	 *
	 * @since 6.9.0
	 *
	 * @return array<string, mixed> Schema for the run endpoint.
	 */
	public function get_run_schema(): array {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'ability-execution',
			'type'       => 'object',
			'properties' => array(
				'result' => array(
					'description' => __( 'The result of the ability execution.' ),
					'type'        => array( 'integer', 'number', 'boolean', 'string', 'array', 'object', 'null' ),
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			),
		);
	}
}

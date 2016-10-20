<?php

/**
 * Manage a WordPress site's settings.
 */

class WP_REST_Settings_Controller extends WP_REST_Controller {

	protected $rest_base = 'settings';
	protected $namespace = 'wp/v2';

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'args'                => array(),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Check if a given request has access to read and manage settings.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return boolean
	 */
	public function get_item_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get the settings.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|array
	 */
	public function get_item( $request ) {
		$options  = $this->get_registered_options();
		$response = array();

		foreach ( $options as $name => $args ) {
			/**
			 * Filters the value of a setting recognized by the REST API.
			 *
			 * Allow hijacking the setting value and overriding the built-in behavior by returning a
			 * non-null value.  The returned value will be presented as the setting value instead.
			 *
			 * @since 4.7.0
			 *
			 * @param mixed  $result  Value to use for the requested setting. Can be a scalar
			 *                        matching the registered schema for the setting, or null to
			 *                        follow the default `get_option` behavior.
			 * @param string $name    Setting name (as shown in REST API responses).
			 * @param array  $args    Arguments passed to `register_setting()` for this setting.
			 */
			$response[ $name ] = apply_filters( 'rest_pre_get_setting', null, $name, $args );

			if ( is_null( $response[ $name ] ) ) {
				// Default to a null value as "null" in the response means "not set".
				$response[ $name ] = get_option( $args['option_name'], $args['schema']['default'] );
			}

			// Because get_option() is lossy, we have to
			// cast values to the type they are registered with.
			$response[ $name ] = $this->prepare_value( $response[ $name ], $args['schema'] );
		}

		return $response;
	}

	/**
	 * Prepare a value for output based off a schema array.
	 *
	 * @param  mixed $value
	 * @param  array $schema
	 * @return mixed
	 */
	protected function prepare_value( $value, $schema ) {
		switch ( $schema['type'] ) {
			case 'string':
				return (string) $value;
			case 'number':
				return (float) $value;
			case 'boolean':
				return (bool) $value;
			default:
				return null;
		}
	}

	/**
	 * Update settings for the settings object.
	 *
	 * @param  WP_REST_Request $request Full detail about the request.
	 * @return WP_Error|array
	 */
	public function update_item( $request ) {
		$options = $this->get_registered_options();
		$params = $request->get_params();

		foreach ( $options as $name => $args ) {
			if ( ! array_key_exists( $name, $params ) ) {
				continue;
			}

			/**
			 * Filters whether to preempt a setting value update.
			 *
			 * Allow hijacking the setting update logic and overriding the built-in behavior by
			 * returning true.
			 *
			 * @since 4.7.0
			 *
			 * @param boolean $result Whether to override the default behavior for updating the
			 *                        value of a setting.
			 * @param string  $name   Setting name (as shown in REST API responses).
			 * @param mixed   $value  Updated setting value.
			 * @param array   $args   Arguments passed to `register_setting()` for this setting.
			 */
			$updated = apply_filters( 'rest_pre_update_setting', false, $name, $request[ $name ], $args );
			if ( $updated ) {
				continue;
			}

			// A null value means reset the option, which is essentially deleting it
			// from the database and then relying on the default value.
			if ( is_null( $request[ $name ] ) ) {
				delete_option( $args['option_name'] );
			} else {
				update_option( $args['option_name'], $request[ $name ] );
			}
		}

		return $this->get_item( $request );
	}

	/**
	 * Get all the registered options for the Settings API
	 *
	 * @return array
	 */
	protected function get_registered_options() {
		$rest_options = array();

		foreach ( get_registered_settings() as $name => $args ) {
			if ( empty( $args['show_in_rest'] ) ) {
				continue;
			}

			$rest_args = array();
			if ( is_array( $args['show_in_rest'] ) ) {
				$rest_args = $args['show_in_rest'];
			}

			$defaults = array(
				'name'   => ! empty( $rest_args['name'] ) ? $rest_args['name'] : $name,
				'schema' => array(),
			);
			$rest_args = array_merge( $defaults, $rest_args );

			$default_schema = array(
				'type'        => empty( $args['type'] ) ? null : $args['type'],
				'description' => empty( $args['description'] ) ? '' : $args['description'],
				'default'     => isset( $args['default'] ) ? $args['default'] : null,
			);

			$rest_args['schema'] = array_merge( $default_schema, $rest_args['schema'] );
			$rest_args['option_name'] = $name;

			// Skip over settings that don't have a defined type in the schema.
			if ( empty( $rest_args['schema']['type'] ) ) {
				continue;
			}

			// Whitelist the supported types for settings, as we don't want invalid types
			// to be updated with arbitrary values that we can't do decent sanitizing for.
			if ( ! in_array( $rest_args['schema']['type'], array( 'number', 'string', 'boolean' ), true ) ) {
				continue;
			}

			$rest_options[ $rest_args['name'] ] = $rest_args;
		}

		return $rest_options;
	}

	/**
	 * Get the site setting schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$options = $this->get_registered_options();

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'settings',
			'type'       => 'object',
			'properties' => array(),
		);

		foreach ( $options as $option_name => $option ) {
			$schema['properties'][ $option_name ] = $option['schema'];
		}

		return $this->add_additional_fields_schema( $schema );
	}
}

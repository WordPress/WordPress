<?php
/**
 * REST API: WP_REST_Meta_Fields class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.7.0
 */

/**
 * Core class to manage meta values for an object via the REST API.
 *
 * @since 4.7.0
 */
abstract class WP_REST_Meta_Fields {

	/**
	 * Retrieves the object meta type.
	 *
	 * @since 4.7.0
	 *
	 * @return string One of 'post', 'comment', 'term', 'user', or anything
	 *                else supported by `_get_meta_table()`.
	 */
	abstract protected function get_meta_type();

	/**
	 * Retrieves the object meta subtype.
	 *
	 * @since 4.9.8
	 *
	 * @return string Subtype for the meta type, or empty string if no specific subtype.
	 */
	protected function get_meta_subtype() {
		return '';
	}

	/**
	 * Retrieves the object type for register_rest_field().
	 *
	 * @since 4.7.0
	 *
	 * @return string The REST field type, such as post type name, taxonomy name, 'comment', or `user`.
	 */
	abstract protected function get_rest_field_type();

	/**
	 * Registers the meta field.
	 *
	 * @since 4.7.0
	 *
	 * @see register_rest_field()
	 */
	public function register_field() {
		register_rest_field(
			$this->get_rest_field_type(),
			'meta',
			array(
				'get_callback'    => array( $this, 'get_value' ),
				'update_callback' => array( $this, 'update_value' ),
				'schema'          => $this->get_field_schema(),
			)
		);
	}

	/**
	 * Retrieves the meta field value.
	 *
	 * @since 4.7.0
	 *
	 * @param int             $object_id Object ID to fetch meta for.
	 * @param WP_REST_Request $request   Full details about the request.
	 * @return object|WP_Error Object containing the meta values by name, otherwise WP_Error object.
	 */
	public function get_value( $object_id, $request ) {
		$fields   = $this->get_registered_fields();
		$response = array();

		foreach ( $fields as $meta_key => $args ) {
			$name       = $args['name'];
			$all_values = get_metadata( $this->get_meta_type(), $object_id, $meta_key, false );

			if ( $args['single'] ) {
				if ( empty( $all_values ) ) {
					$value = $args['schema']['default'];
				} else {
					$value = $all_values[0];
				}

				$value = $this->prepare_value_for_response( $value, $request, $args );
			} else {
				$value = array();

				foreach ( $all_values as $row ) {
					$value[] = $this->prepare_value_for_response( $row, $request, $args );
				}
			}

			$response[ $name ] = $value;
		}

		return $response;
	}

	/**
	 * Prepares a meta value for a response.
	 *
	 * This is required because some native types cannot be stored correctly
	 * in the database, such as booleans. We need to cast back to the relevant
	 * type before passing back to JSON.
	 *
	 * @since 4.7.0
	 *
	 * @param mixed           $value   Meta value to prepare.
	 * @param WP_REST_Request $request Current request object.
	 * @param array           $args    Options for the field.
	 * @return mixed Prepared value.
	 */
	protected function prepare_value_for_response( $value, $request, $args ) {
		if ( ! empty( $args['prepare_callback'] ) ) {
			$value = call_user_func( $args['prepare_callback'], $value, $request, $args );
		}

		return $value;
	}

	/**
	 * Updates meta values.
	 *
	 * @since 4.7.0
	 *
	 * @param array $meta      Array of meta parsed from the request.
	 * @param int   $object_id Object ID to fetch meta for.
	 * @return null|WP_Error Null on success, WP_Error object on failure.
	 */
	public function update_value( $meta, $object_id ) {
		$fields = $this->get_registered_fields();

		foreach ( $fields as $meta_key => $args ) {
			$name = $args['name'];
			if ( ! array_key_exists( $name, $meta ) ) {
				continue;
			}

			/*
			 * A null value means reset the field, which is essentially deleting it
			 * from the database and then relying on the default value.
			 */
			if ( is_null( $meta[ $name ] ) ) {
				$args = $this->get_registered_fields()[ $meta_key ];

				if ( $args['single'] ) {
					$current = get_metadata( $this->get_meta_type(), $object_id, $meta_key, true );

					if ( is_wp_error( rest_validate_value_from_schema( $current, $args['schema'] ) ) ) {
						return new WP_Error(
							'rest_invalid_stored_value',
							/* translators: %s: Custom field key. */
							sprintf( __( 'The %s property has an invalid stored value, and cannot be updated to null.' ), $name ),
							array( 'status' => 500 )
						);
					}
				}

				$result = $this->delete_meta_value( $object_id, $meta_key, $name );
				if ( is_wp_error( $result ) ) {
					return $result;
				}
				continue;
			}

			$value = $meta[ $name ];

			if ( ! $args['single'] && is_array( $value ) && count( array_filter( $value, 'is_null' ) ) ) {
				return new WP_Error(
					'rest_invalid_stored_value',
					/* translators: %s: Custom field key. */
					sprintf( __( 'The %s property has an invalid stored value, and cannot be updated to null.' ), $name ),
					array( 'status' => 500 )
				);
			}

			$is_valid = rest_validate_value_from_schema( $value, $args['schema'], 'meta.' . $name );
			if ( is_wp_error( $is_valid ) ) {
				$is_valid->add_data( array( 'status' => 400 ) );
				return $is_valid;
			}

			$value = rest_sanitize_value_from_schema( $value, $args['schema'] );

			if ( $args['single'] ) {
				$result = $this->update_meta_value( $object_id, $meta_key, $name, $value );
			} else {
				$result = $this->update_multi_meta_value( $object_id, $meta_key, $name, $value );
			}

			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return null;
	}

	/**
	 * Deletes a meta value for an object.
	 *
	 * @since 4.7.0
	 *
	 * @param int    $object_id Object ID the field belongs to.
	 * @param string $meta_key  Key for the field.
	 * @param string $name      Name for the field that is exposed in the REST API.
	 * @return bool|WP_Error True if meta field is deleted, WP_Error otherwise.
	 */
	protected function delete_meta_value( $object_id, $meta_key, $name ) {
		$meta_type = $this->get_meta_type();

		if ( ! current_user_can( "delete_{$meta_type}_meta", $object_id, $meta_key ) ) {
			return new WP_Error(
				'rest_cannot_delete',
				/* translators: %s: Custom field key. */
				sprintf( __( 'Sorry, you are not allowed to edit the %s custom field.' ), $name ),
				array(
					'key'    => $name,
					'status' => rest_authorization_required_code(),
				)
			);
		}

		if ( ! delete_metadata( $meta_type, $object_id, wp_slash( $meta_key ) ) ) {
			return new WP_Error(
				'rest_meta_database_error',
				__( 'Could not delete meta value from database.' ),
				array(
					'key'    => $name,
					'status' => WP_Http::INTERNAL_SERVER_ERROR,
				)
			);
		}

		return true;
	}

	/**
	 * Updates multiple meta values for an object.
	 *
	 * Alters the list of values in the database to match the list of provided values.
	 *
	 * @since 4.7.0
	 *
	 * @param int    $object_id Object ID to update.
	 * @param string $meta_key  Key for the custom field.
	 * @param string $name      Name for the field that is exposed in the REST API.
	 * @param array  $values    List of values to update to.
	 * @return bool|WP_Error True if meta fields are updated, WP_Error otherwise.
	 */
	protected function update_multi_meta_value( $object_id, $meta_key, $name, $values ) {
		$meta_type = $this->get_meta_type();

		if ( ! current_user_can( "edit_{$meta_type}_meta", $object_id, $meta_key ) ) {
			return new WP_Error(
				'rest_cannot_update',
				/* translators: %s: Custom field key. */
				sprintf( __( 'Sorry, you are not allowed to edit the %s custom field.' ), $name ),
				array(
					'key'    => $name,
					'status' => rest_authorization_required_code(),
				)
			);
		}

		$current = get_metadata( $meta_type, $object_id, $meta_key, false );

		$to_remove = $current;
		$to_add    = $values;

		foreach ( $to_add as $add_key => $value ) {
			$remove_keys = array_keys( $to_remove, $value, true );

			if ( empty( $remove_keys ) ) {
				continue;
			}

			if ( count( $remove_keys ) > 1 ) {
				// To remove, we need to remove first, then add, so don't touch.
				continue;
			}

			$remove_key = $remove_keys[0];

			unset( $to_remove[ $remove_key ] );
			unset( $to_add[ $add_key ] );
		}

		/*
		 * `delete_metadata` removes _all_ instances of the value, so only call once. Otherwise,
		 * `delete_metadata` will return false for subsequent calls of the same value.
		 * Use serialization to produce a predictable string that can be used by array_unique.
		 */
		$to_remove = array_map( 'maybe_unserialize', array_unique( array_map( 'maybe_serialize', $to_remove ) ) );

		foreach ( $to_remove as $value ) {
			if ( ! delete_metadata( $meta_type, $object_id, wp_slash( $meta_key ), wp_slash( $value ) ) ) {
				return new WP_Error(
					'rest_meta_database_error',
					/* translators: %s: Custom field key. */
					sprintf( __( 'Could not update the meta value of %s in database.' ), $meta_key ),
					array(
						'key'    => $name,
						'status' => WP_Http::INTERNAL_SERVER_ERROR,
					)
				);
			}
		}

		foreach ( $to_add as $value ) {
			if ( ! add_metadata( $meta_type, $object_id, wp_slash( $meta_key ), wp_slash( $value ) ) ) {
				return new WP_Error(
					'rest_meta_database_error',
					/* translators: %s: Custom field key. */
					sprintf( __( 'Could not update the meta value of %s in database.' ), $meta_key ),
					array(
						'key'    => $name,
						'status' => WP_Http::INTERNAL_SERVER_ERROR,
					)
				);
			}
		}

		return true;
	}

	/**
	 * Updates a meta value for an object.
	 *
	 * @since 4.7.0
	 *
	 * @param int    $object_id Object ID to update.
	 * @param string $meta_key  Key for the custom field.
	 * @param string $name      Name for the field that is exposed in the REST API.
	 * @param mixed  $value     Updated value.
	 * @return bool|WP_Error True if the meta field was updated, WP_Error otherwise.
	 */
	protected function update_meta_value( $object_id, $meta_key, $name, $value ) {
		$meta_type = $this->get_meta_type();

		if ( ! current_user_can( "edit_{$meta_type}_meta", $object_id, $meta_key ) ) {
			return new WP_Error(
				'rest_cannot_update',
				/* translators: %s: Custom field key. */
				sprintf( __( 'Sorry, you are not allowed to edit the %s custom field.' ), $name ),
				array(
					'key'    => $name,
					'status' => rest_authorization_required_code(),
				)
			);
		}

		// Do the exact same check for a duplicate value as in update_metadata() to avoid update_metadata() returning false.
		$old_value = get_metadata( $meta_type, $object_id, $meta_key );
		$subtype   = get_object_subtype( $meta_type, $object_id );
		$args      = $this->get_registered_fields()[ $meta_key ];

		if ( 1 === count( $old_value ) ) {
			$sanitized = sanitize_meta( $meta_key, $value, $meta_type, $subtype );

			if ( in_array( $args['type'], array( 'string', 'number', 'integer', 'boolean' ), true ) ) {
				// The return value of get_metadata will always be a string for scalar types.
				$sanitized = (string) $sanitized;
			}

			if ( $sanitized === $old_value[0] ) {
				return true;
			}
		}

		if ( ! update_metadata( $meta_type, $object_id, wp_slash( $meta_key ), wp_slash_strings_only( $value ) ) ) {
			return new WP_Error(
				'rest_meta_database_error',
				/* translators: %s: Custom field key. */
				sprintf( __( 'Could not update the meta value of %s in database.' ), $meta_key ),
				array(
					'key'    => $name,
					'status' => WP_Http::INTERNAL_SERVER_ERROR,
				)
			);
		}

		return true;
	}

	/**
	 * Retrieves all the registered meta fields.
	 *
	 * @since 4.7.0
	 *
	 * @return array Registered fields.
	 */
	protected function get_registered_fields() {
		$registered = array();

		$meta_type    = $this->get_meta_type();
		$meta_subtype = $this->get_meta_subtype();

		$meta_keys = get_registered_meta_keys( $meta_type );
		if ( ! empty( $meta_subtype ) ) {
			$meta_keys = array_merge( $meta_keys, get_registered_meta_keys( $meta_type, $meta_subtype ) );
		}

		foreach ( $meta_keys as $name => $args ) {
			if ( empty( $args['show_in_rest'] ) ) {
				continue;
			}

			$rest_args = array();

			if ( is_array( $args['show_in_rest'] ) ) {
				$rest_args = $args['show_in_rest'];
			}

			$default_args = array(
				'name'             => $name,
				'single'           => $args['single'],
				'type'             => ! empty( $args['type'] ) ? $args['type'] : null,
				'schema'           => array(),
				'prepare_callback' => array( $this, 'prepare_value' ),
			);

			$default_schema = array(
				'type'        => $default_args['type'],
				'description' => empty( $args['description'] ) ? '' : $args['description'],
				'default'     => isset( $args['default'] ) ? $args['default'] : null,
			);

			$rest_args           = array_merge( $default_args, $rest_args );
			$rest_args['schema'] = array_merge( $default_schema, $rest_args['schema'] );

			$type = ! empty( $rest_args['type'] ) ? $rest_args['type'] : null;
			$type = ! empty( $rest_args['schema']['type'] ) ? $rest_args['schema']['type'] : $type;

			if ( null === $rest_args['schema']['default'] ) {
				$rest_args['schema']['default'] = static::get_empty_value_for_type( $type );
			}

			$rest_args['schema'] = $this->default_additional_properties_to_false( $rest_args['schema'] );

			if ( ! in_array( $type, array( 'string', 'boolean', 'integer', 'number', 'array', 'object' ), true ) ) {
				continue;
			}

			if ( empty( $rest_args['single'] ) ) {
				$rest_args['schema'] = array(
					'type'  => 'array',
					'items' => $rest_args['schema'],
				);
			}

			$registered[ $name ] = $rest_args;
		}

		return $registered;
	}

	/**
	 * Retrieves the object's meta schema, conforming to JSON Schema.
	 *
	 * @since 4.7.0
	 *
	 * @return array Field schema data.
	 */
	public function get_field_schema() {
		$fields = $this->get_registered_fields();

		$schema = array(
			'description' => __( 'Meta fields.' ),
			'type'        => 'object',
			'context'     => array( 'view', 'edit' ),
			'properties'  => array(),
			'arg_options' => array(
				'sanitize_callback' => null,
				'validate_callback' => array( $this, 'check_meta_is_array' ),
			),
		);

		foreach ( $fields as $args ) {
			$schema['properties'][ $args['name'] ] = $args['schema'];
		}

		return $schema;
	}

	/**
	 * Prepares a meta value for output.
	 *
	 * Default preparation for meta fields. Override by passing the
	 * `prepare_callback` in your `show_in_rest` options.
	 *
	 * @since 4.7.0
	 *
	 * @param mixed           $value   Meta value from the database.
	 * @param WP_REST_Request $request Request object.
	 * @param array           $args    REST-specific options for the meta key.
	 * @return mixed Value prepared for output. If a non-JsonSerializable object, null.
	 */
	public static function prepare_value( $value, $request, $args ) {
		if ( $args['single'] ) {
			$schema = $args['schema'];
		} else {
			$schema = $args['schema']['items'];
		}

		if ( '' === $value && in_array( $schema['type'], array( 'boolean', 'integer', 'number' ), true ) ) {
			$value = static::get_empty_value_for_type( $schema['type'] );
		}

		if ( is_wp_error( rest_validate_value_from_schema( $value, $schema ) ) ) {
			return null;
		}

		return rest_sanitize_value_from_schema( $value, $schema );
	}

	/**
	 * Check the 'meta' value of a request is an associative array.
	 *
	 * @since 4.7.0
	 *
	 * @param mixed           $value   The meta value submitted in the request.
	 * @param WP_REST_Request $request Full details about the request.
	 * @param string          $param   The parameter name.
	 * @return array|false The meta array, if valid, false otherwise.
	 */
	public function check_meta_is_array( $value, $request, $param ) {
		if ( ! is_array( $value ) ) {
			return false;
		}

		return $value;
	}

	/**
	 * Recursively add additionalProperties = false to all objects in a schema if no additionalProperties setting
	 * is specified.
	 *
	 * This is needed to restrict properties of objects in meta values to only
	 * registered items, as the REST API will allow additional properties by
	 * default.
	 *
	 * @since 5.3.0
	 *
	 * @param array $schema The schema array.
	 * @return array
	 */
	protected function default_additional_properties_to_false( $schema ) {
		switch ( $schema['type'] ) {
			case 'object':
				foreach ( $schema['properties'] as $key => $child_schema ) {
					$schema['properties'][ $key ] = $this->default_additional_properties_to_false( $child_schema );
				}

				if ( ! isset( $schema['additionalProperties'] ) ) {
					$schema['additionalProperties'] = false;
				}
				break;
			case 'array':
				$schema['items'] = $this->default_additional_properties_to_false( $schema['items'] );
				break;
		}

		return $schema;
	}

	/**
	 * Gets the empty value for a schema type.
	 *
	 * @since 5.3.0
	 *
	 * @param string $type The schema type.
	 * @return mixed
	 */
	protected static function get_empty_value_for_type( $type ) {
		switch ( $type ) {
			case 'string':
				return '';
			case 'boolean':
				return false;
			case 'integer':
				return 0;
			case 'number':
				return 0.0;
			case 'array':
			case 'object':
				return array();
			default:
				return null;
		}
	}
}

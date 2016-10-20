<?php

/**
 * Manage meta values for an object.
 */
abstract class WP_REST_Meta_Fields {

	/**
	 * Get the object type for meta.
	 *
	 * @return string One of 'post', 'comment', 'term', 'user', or anything
	 *                else supported by `_get_meta_table()`.
	 */
	abstract protected function get_meta_type();

	/**
	 * Get the object type for `register_rest_field`.
	 *
	 * @return string Custom post type, 'taxonomy', 'comment', or `user`.
	 */
	abstract protected function get_rest_field_type();

	/**
	 * Register the meta field.
	 */
	public function register_field() {
		register_rest_field( $this->get_rest_field_type(), 'meta', array(
			'get_callback' => array( $this, 'get_value' ),
			'update_callback' => array( $this, 'update_value' ),
			'schema' => $this->get_field_schema(),
		));
	}

	/**
	 * Get the `meta` field value.
	 *
	 * @param int             $object_id Object ID to fetch meta for.
	 * @param WP_REST_Request $request   Full details about the request.
	 * @return WP_Error|object
	 */
	public function get_value( $object_id, $request ) {
		$fields   = $this->get_registered_fields();
		$response = array();

		foreach ( $fields as $name => $args ) {
			$all_values = get_metadata( $this->get_meta_type(), $object_id, $name, false );
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

		return (object) $response;
	}

	/**
	 * Prepare value for response.
	 *
	 * This is required because some native types cannot be stored correctly in
	 * the database, such as booleans. We need to cast back to the relevant
	 * type before passing back to JSON.
	 *
	 * @param mixed           $value   Value to prepare.
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
	 * Update meta values.
	 *
	 * @param WP_REST_Request $request    Full details about the request.
	 * @param int             $object_id  Object ID to fetch meta for.
	 * @return WP_Error|null Error if one occurs, null on success.
	 */
	public function update_value( $request, $object_id ) {
		$fields = $this->get_registered_fields();

		foreach ( $fields as $name => $args ) {
			if ( ! array_key_exists( $name, $request ) ) {
				continue;
			}

			// A null value means reset the field, which is essentially deleting it
			// from the database and then relying on the default value.
			if ( is_null( $request[ $name ] ) ) {
				$result = $this->delete_meta_value( $object_id, $name );
			} elseif ( $args['single'] ) {
				$result = $this->update_meta_value( $object_id, $name, $request[ $name ] );
			} else {
				$result = $this->update_multi_meta_value( $object_id, $name, $request[ $name ] );
			}

			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return null;
	}

	/**
	 * Delete meta value for an object.
	 *
	 * @param int    $object_id Object ID the field belongs to.
	 * @param string $name      Key for the field.
	 * @return bool|WP_Error True if meta field is deleted, error otherwise.
	 */
	protected function delete_meta_value( $object_id, $name ) {
		if ( ! current_user_can( 'delete_post_meta', $object_id, $name ) ) {
			return new WP_Error(
				'rest_cannot_delete',
				sprintf( __( 'You do not have permission to edit the %s custom field.' ), $name ),
				array( 'key' => $name, 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! delete_metadata( $this->get_meta_type(), $object_id, wp_slash( $name ) ) ) {
			return new WP_Error(
				'rest_meta_database_error',
				__( 'Could not delete meta value from database.' ),
				array( 'key' => $name, 'status' => WP_Http::INTERNAL_SERVER_ERROR )
			);
		}

		return true;
	}

	/**
	 * Update multiple meta values for an object.
	 *
	 * Alters the list of values in the database to match the list of provided values.
	 *
	 * @param int    $object_id Object ID to update.
	 * @param string $name      Key for the custom field.
	 * @param array  $values    List of values to update to.
	 * @return bool|WP_Error True if meta fields are updated, error otherwise.
	 */
	protected function update_multi_meta_value( $object_id, $name, $values ) {
		if ( ! current_user_can( 'edit_post_meta', $object_id, $name ) ) {
			return new WP_Error(
				'rest_cannot_update',
				sprintf( __( 'You do not have permission to edit the %s custom field.' ), $name ),
				array( 'key' => $name, 'status' => rest_authorization_required_code() )
			);
		}

		$current = get_metadata( $this->get_meta_type(), $object_id, $name, false );

		$to_remove = $current;
		$to_add = $values;
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

		// `delete_metadata` removes _all_ instances of the value, so only call
		// once.
		$to_remove = array_unique( $to_remove );
		foreach ( $to_remove as $value ) {
			if ( ! delete_metadata( $this->get_meta_type(), $object_id, wp_slash( $name ), wp_slash( $value ) ) ) {
				return new WP_Error(
					'rest_meta_database_error',
					__( 'Could not update meta value in database.' ),
					array( 'key' => $name, 'status' => WP_Http::INTERNAL_SERVER_ERROR )
				);
			}
		}
		foreach ( $to_add as $value ) {
			if ( ! add_metadata( $this->get_meta_type(), $object_id, wp_slash( $name ), wp_slash( $value ) ) ) {
				return new WP_Error(
					'rest_meta_database_error',
					__( 'Could not update meta value in database.' ),
					array( 'key' => $name, 'status' => WP_Http::INTERNAL_SERVER_ERROR )
				);
			}
		}

		return true;
	}

	/**
	 * Update meta value for an object.
	 *
	 * @param int    $object_id Object ID to update.
	 * @param string $name      Key for the custom field.
	 * @param mixed  $value     Updated value.
	 * @return bool|WP_Error True if meta field is updated, error otherwise.
	 */
	protected function update_meta_value( $object_id, $name, $value ) {
		if ( ! current_user_can( 'edit_post_meta', $object_id, $name ) ) {
			return new WP_Error(
				'rest_cannot_update',
				sprintf( __( 'You do not have permission to edit the %s custom field.' ), $name ),
				array( 'key' => $name, 'status' => rest_authorization_required_code() )
			);
		}

		$meta_type  = $this->get_meta_type();
		$meta_key   = wp_slash( $name );
		$meta_value = wp_slash( $value );

		// Do the exact same check for a duplicate value as in update_metadata() to avoid update_metadata() returning false.
		$old_value = get_metadata( $meta_type, $object_id, $meta_key );
		if ( 1 === count( $old_value ) ) {
			if ( $old_value[0] === $meta_value ) {
				return true;
			}
		}

		if ( ! update_metadata( $meta_type, $object_id, $meta_key, $meta_value ) ) {
			return new WP_Error(
				'rest_meta_database_error',
				__( 'Could not update meta value in database.' ),
				array( 'key' => $name, 'status' => WP_Http::INTERNAL_SERVER_ERROR )
			);
		}

		return true;
	}

	/**
	 * Get all the registered meta fields.
	 *
	 * @return array
	 */
	protected function get_registered_fields() {
		$registered = array();

		foreach ( get_registered_meta_keys( $this->get_meta_type() ) as $name => $args ) {
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
				'schema'           => array(),
				'prepare_callback' => array( $this, 'prepare_value' ),
			);
			$default_schema = array(
				'type'        => null,
				'description' => empty( $args['description'] ) ? '' : $args['description'],
				'default'     => isset( $args['default'] ) ? $args['default'] : null,
			);
			$rest_args = array_merge( $default_args, $rest_args );
			$rest_args['schema'] = array_merge( $default_schema, $rest_args['schema'] );

			if ( empty( $rest_args['schema']['type'] ) ) {
				// Skip over meta fields that don't have a defined type.
				if ( empty( $args['type'] ) ) {
					continue;
				}

				if ( $rest_args['single'] ) {
					$rest_args['schema']['type'] = $args['type'];
				} else {
					$rest_args['schema']['type'] = 'array';
					$rest_args['schema']['items'] = array(
						'type' => $args['type'],
					);
				}
			}

			$registered[ $rest_args['name'] ] = $rest_args;
		} // End foreach().

		return $registered;
	}

	/**
	 * Get the object's `meta` schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_field_schema() {
		$fields = $this->get_registered_fields();

		$schema = array(
			'description' => __( 'Meta fields.' ),
			'type'        => 'object',
			'context'     => array( 'view', 'edit' ),
			'properties'  => array(),
		);

		foreach ( $fields as $key => $args ) {
			$schema['properties'][ $key ] = $args['schema'];
		}

		return $schema;
	}

	/**
	 * Prepare a meta value for output.
	 *
	 * Default preparation for meta fields. Override by passing the
	 * `prepare_callback` in your `show_in_rest` options.
	 *
	 * @param mixed           $value   Meta value from the database.
	 * @param WP_REST_Request $request Request object.
	 * @param array           $args    REST-specific options for the meta key.
	 * @return mixed Value prepared for output.
	 */
	public static function prepare_value( $value, $request, $args ) {
		$type = $args['schema']['type'];

		// For multi-value fields, check the item type instead.
		if ( 'array' === $type && ! empty( $args['schema']['items']['type'] ) ) {
			$type = $args['schema']['items']['type'];
		}

		switch ( $type ) {
			case 'string':
				$value = (string) $value;
				break;
			case 'number':
				$value = (float) $value;
				break;
			case 'boolean':
				$value = (bool) $value;
				break;
		}

		// Don't allow objects to be output.
		if ( is_object( $value ) && ! ( $value instanceof JsonSerializable ) ) {
			return null;
		}

		return $value;
	}
}

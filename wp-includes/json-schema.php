<?php
/**
 * JSON Schema API: shared functions for working with JSON Schema.
 *
 * @package WordPress
 * @subpackage JSON_Schema
 * @since 7.1.0
 */

/**
 * Gets the JSON Schema keywords allowed for a given schema profile.
 *
 * Use this when preparing a schema that will be consumed outside of
 * WordPress's server-side validation, such as by REST clients, frontend code,
 * or AI providers.
 *
 * The 'rest-api' profile returns the subset of JSON Schema draft-04 keywords
 * that the REST API has historically exposed. The 'draft-04' profile preserves
 * the larger draft-04 vocabulary used by clients that can consume standalone
 * schemas.
 *
 * Allowing a keyword to be exposed does not make WordPress validate or
 * sanitize values against it.
 *
 * @since 7.1.0
 *
 * @param string $schema_profile Optional. Name of the schema profile to get keywords for.
 *                               Accepts 'rest-api' or 'draft-04'. Any other value falls
 *                               back to the 'rest-api' profile. Default 'rest-api'.
 * @return string[] Allowed JSON Schema keywords.
 */
function wp_get_json_schema_allowed_keywords( string $schema_profile = 'rest-api' ): array {
	$rest_keywords = rest_get_allowed_schema_keywords();

	$keywords_by_profile = array(
		'rest-api' => $rest_keywords,
		'draft-04' => array_merge(
			array(
				'$schema',
				'id',
				'$ref',
			),
			$rest_keywords,
			array(
				'required',
				'allOf',
				'not',
				'definitions',
				'dependencies',
				'additionalItems',
			)
		),
	);

	$allowed_keywords = $keywords_by_profile[ $schema_profile ] ?? $rest_keywords;

	/**
	 * Filters the JSON Schema keywords allowed for a given schema profile.
	 *
	 * Use this to decide which keywords may be exposed to clients for a profile.
	 * It does not make WordPress validate or sanitize values against the keyword.
	 *
	 * @since 7.1.0
	 *
	 * @param string[] $allowed_keywords Allowed JSON Schema keywords.
	 * @param string   $schema_profile   The schema profile the keywords are for.
	 */
	return apply_filters( 'wp_json_schema_allowed_keywords', $allowed_keywords, $schema_profile );
}

/**
 * Prepares a JSON Schema for clients.
 *
 * Converts WordPress-specific schema conventions and removes unsupported
 * keywords so clients can interpret the schema using the selected profile.
 *
 * Applies the same preparation to nested schemas. Existing default values
 * are normalized according to their schemas so empty objects encode as `{}`
 * rather than `[]`, including within nested default values.
 *
 * @since 7.1.0
 *
 * @param array<string, mixed> $schema         The schema array.
 * @param string               $schema_profile Optional. Name of the schema profile
 *                                             whose keywords should be preserved.
 *                                             Default 'draft-04'.
 * @return array<string, mixed> The prepared schema.
 */
function wp_prepare_json_schema_for_client( array $schema, string $schema_profile = 'draft-04' ): array {
	$allowed_keywords = array_fill_keys( wp_get_json_schema_allowed_keywords( $schema_profile ), true );

	return _wp_prepare_json_schema_for_client_with_allowed_keywords( $schema, $allowed_keywords );
}

/**
 * Prepares a JSON Schema for clients using a given keyword lookup.
 *
 * Normalizes schema conventions and default values throughout nested schemas
 * while retaining only the allowed keywords.
 *
 * @since 7.1.0
 * @access private
 *
 * @param array<string, mixed> $schema           The schema array.
 * @param array<string, true>  $allowed_keywords Lookup map of allowed JSON Schema keywords.
 * @return array<string, mixed> The prepared schema.
 */
function _wp_prepare_json_schema_for_client_with_allowed_keywords( array $schema, array $allowed_keywords ): array {
	if ( array_key_exists( 'default', $schema ) ) {
		$schema['default'] = wp_prepare_json_value_for_client( $schema['default'], $schema );
	}

	$schema = array_intersect_key( $schema, $allowed_keywords );

	/*
	 * Collect draft-03 per-property `required: true` flags into a draft-04
	 * `required` array of property names on the parent object schema.
	 *
	 * This mirrors rest_validate_object_value_from_schema(), where a draft-04
	 * `required` array takes precedence: when one is present, per-property
	 * booleans are ignored during validation. They are therefore left out of
	 * the array here as well (but still stripped from the output) so the
	 * published schema describes exactly what gets enforced.
	 */
	if ( isset( $schema['properties'] ) && is_array( $schema['properties'] ) ) {
		$has_required_array = isset( $schema['required'] ) && is_array( $schema['required'] );
		$required           = array();
		foreach ( $schema['properties'] as $property => &$property_schema ) {
			if ( is_array( $property_schema ) && ! wp_is_numeric_array( $property_schema ) && isset( $property_schema['required'] ) && is_bool( $property_schema['required'] ) ) {
				if ( ! $has_required_array && true === $property_schema['required'] ) {
					$required[] = (string) $property;
				}
				unset( $property_schema['required'] );
			}
		}
		unset( $property_schema );

		/*
		 * Property keys are unique, so the collected list needs no deduplication.
		 * When a draft-04 array is already present, leave it untouched.
		 */
		if ( ! $has_required_array && count( $required ) > 0 ) {
			$schema['required'] = $required;
		}
	}

	/*
	 * A boolean `required` outside of an object's property list has no draft-04
	 * equivalent, so drop it rather than emit an invalid keyword.
	 */
	if ( isset( $schema['required'] ) && is_bool( $schema['required'] ) ) {
		unset( $schema['required'] );
	}

	/*
	 * Sub-schema maps: keys are user-defined, values are sub-schemas.
	 * Note: 'dependencies' values can also be property-dependency arrays
	 * (numeric arrays of strings) which are skipped via wp_is_numeric_array().
	 */
	foreach ( array( 'properties', 'patternProperties', 'definitions', 'dependencies' ) as $keyword ) {
		if ( isset( $schema[ $keyword ] ) && is_array( $schema[ $keyword ] ) ) {
			foreach ( $schema[ $keyword ] as $key => $child_schema ) {
				if ( is_array( $child_schema ) && ! wp_is_numeric_array( $child_schema ) ) {
					$schema[ $keyword ][ $key ] = _wp_prepare_json_schema_for_client_with_allowed_keywords( $child_schema, $allowed_keywords );
				}
			}
		}
	}

	// Single sub-schema keywords.
	foreach ( array( 'not', 'additionalProperties', 'additionalItems' ) as $keyword ) {
		if ( isset( $schema[ $keyword ] ) && is_array( $schema[ $keyword ] ) && ! wp_is_numeric_array( $schema[ $keyword ] ) ) {
			$schema[ $keyword ] = _wp_prepare_json_schema_for_client_with_allowed_keywords( $schema[ $keyword ], $allowed_keywords );
		}
	}

	// Items: single schema or tuple array of schemas.
	if ( isset( $schema['items'] ) && is_array( $schema['items'] ) ) {
		if ( ! wp_is_numeric_array( $schema['items'] ) ) {
			$schema['items'] = _wp_prepare_json_schema_for_client_with_allowed_keywords( $schema['items'], $allowed_keywords );
		} else {
			foreach ( $schema['items'] as $index => $item_schema ) {
				if ( is_array( $item_schema ) && ! wp_is_numeric_array( $item_schema ) ) {
					$schema['items'][ $index ] = _wp_prepare_json_schema_for_client_with_allowed_keywords( $item_schema, $allowed_keywords );
				}
			}
		}
	}

	// Array-of-schemas keywords.
	foreach ( array( 'anyOf', 'oneOf', 'allOf' ) as $keyword ) {
		if ( isset( $schema[ $keyword ] ) && is_array( $schema[ $keyword ] ) ) {
			foreach ( $schema[ $keyword ] as $index => $sub_schema ) {
				if ( is_array( $sub_schema ) && ! wp_is_numeric_array( $sub_schema ) ) {
					$schema[ $keyword ][ $index ] = _wp_prepare_json_schema_for_client_with_allowed_keywords( $sub_schema, $allowed_keywords );
				}
			}
		}
	}

	return $schema;
}

/**
 * Prepares a value for JSON encoding according to its schema.
 *
 * Preserves the distinction between JSON objects and arrays by converting
 * empty PHP arrays to objects where the schema identifies them as objects.
 * Nested properties and items are prepared according to their schemas.
 * Empty arrays remain arrays when a matching schema allows them.
 *
 * Prepares the representation of existing values without inserting defaults
 * or performing full JSON Schema validation.
 *
 * @since 7.2.0
 *
 * @param mixed                $value  The value to prepare.
 * @param array<string, mixed> $schema The schema describing the value.
 * @return mixed The value prepared for JSON encoding.
 */
function wp_prepare_json_value_for_client( $value, array $schema ) {
	if ( $value instanceof JsonSerializable ) {
		$value = $value->jsonSerialize();
	}

	if ( ! is_array( $value ) && ! $value instanceof stdClass ) {
		return $value;
	}

	foreach ( array( 'anyOf', 'oneOf' ) as $keyword ) {
		if ( ! isset( $schema[ $keyword ] ) ) {
			continue;
		}

		$matching_schema = null;

		/*
		 * Prefer an array schema for PHP lists. An empty PHP array also matches an
		 * object schema, but should remain [] when an array schema accepts it.
		 */
		if ( is_array( $value ) && array_is_list( $value ) ) {
			foreach ( $schema[ $keyword ] as $candidate_schema ) {
				if ( ! isset( $candidate_schema['type'] ) && isset( $schema['type'] ) ) {
					$candidate_schema['type'] = $schema['type'];
				}

				if ( _wp_json_schema_matches_type( $value, $candidate_schema, 'array' ) ) {
					$matching_schema = $candidate_schema;
					break;
				}
			}
		}

		// Like rest_filter_response_by_context(), use the first matching oneOf branch.
		if ( null === $matching_schema ) {
			$matching_schema = 'anyOf' === $keyword
				? rest_find_any_matching_schema( $value, $schema, '' )
				: rest_find_one_matching_schema( $value, $schema, '', true );
		}

		if ( is_wp_error( $matching_schema ) ) {
			continue;
		}

		if ( ! isset( $schema['type'] ) && isset( $matching_schema['type'] ) ) {
			$schema['type'] = $matching_schema['type'];
		}

		$value = wp_prepare_json_value_for_client( $value, $matching_schema );
	}

	$is_array_value = is_array( $value );
	$types          = isset( $schema['type'] ) ? (array) $schema['type'] : array();
	$allows_object  = in_array( 'object', $types, true );
	$allows_array   = in_array( 'array', $types, true );

	if ( $is_array_value && empty( $value ) && $allows_object && ! $allows_array ) {
		return (object) $value;
	}

	$is_object_value = $value instanceof stdClass || ( $is_array_value && $allows_object && ( ! $allows_array || ! array_is_list( $value ) ) );
	if ( $allows_object && $is_object_value ) {
		if ( $value instanceof stdClass ) {
			$value = clone $value;
		}

		foreach ( $value as $property => $property_value ) {
			$property_schema = null;
			if ( isset( $schema['properties'][ $property ] ) && is_array( $schema['properties'][ $property ] ) ) {
				$property_schema = $schema['properties'][ $property ];
			} else {
				$property_schema = rest_find_matching_pattern_property_schema( $property, $schema );
				if ( null === $property_schema && isset( $schema['additionalProperties'] ) && is_array( $schema['additionalProperties'] ) ) {
					$property_schema = $schema['additionalProperties'];
				}
			}

			if ( null === $property_schema ) {
				continue;
			}

			$property_value = wp_prepare_json_value_for_client( $property_value, $property_schema );
			if ( is_array( $value ) ) {
				$value[ $property ] = $property_value;
			} else {
				$value->$property = $property_value;
			}
		}
	} elseif ( $allows_array && $is_array_value && isset( $schema['items'] ) && is_array( $schema['items'] ) ) {
		foreach ( $value as $index => $item ) {
			$value[ $index ] = wp_prepare_json_value_for_client( $item, $schema['items'] );
		}
	}

	return $value;
}

/**
 * Determines whether a value matches a JSON Schema through a given type.
 *
 * Combining schemas must have a matching branch that allows the requested type.
 * Finding the type in one branch and validating the value against another branch
 * is not sufficient.
 *
 * @since 7.2.0
 * @access private
 *
 * @param mixed                $value  The value to validate.
 * @param array<string, mixed> $schema The schema to inspect.
 * @param string               $type   The type to match.
 * @return bool Whether the value matches the schema through the requested type.
 */
function _wp_json_schema_matches_type( $value, array $schema, string $type ): bool {
	if ( isset( $schema['type'] ) && ! in_array( $type, (array) $schema['type'], true ) ) {
		return false;
	}

	foreach ( array( 'anyOf', 'oneOf' ) as $keyword ) {
		if ( ! isset( $schema[ $keyword ] ) ) {
			continue;
		}

		foreach ( $schema[ $keyword ] as $sub_schema ) {
			if ( ! is_array( $sub_schema ) ) {
				continue;
			}

			// Match rest_find_any_matching_schema(), which applies the parent type to untyped branches.
			if ( ! isset( $sub_schema['type'] ) && isset( $schema['type'] ) ) {
				$sub_schema['type'] = $schema['type'];
			}

			if ( _wp_json_schema_matches_type( $value, $sub_schema, $type ) ) {
				continue 2;
			}
		}

		return false;
	}

	if ( ! isset( $schema['type'] ) && ! isset( $schema['anyOf'] ) && ! isset( $schema['oneOf'] ) ) {
		return false;
	}

	// Explicit and inferred types must both validate through the requested type only.
	$schema['type'] = $type;

	return ! is_wp_error( rest_validate_value_from_schema( $value, $schema ) );
}

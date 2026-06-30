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
 * Use this before exposing a schema outside of WordPress's server-side
 * validation, for example in REST responses, Ability metadata, or AI provider
 * requests. The prepared schema uses forms that JSON Schema draft-04 clients
 * can understand.
 *
 * WordPress-internal schema conveniences are converted or removed only where
 * needed to keep the exposed schema valid for the selected profile.
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
 * @since 7.1.0
 * @access private
 *
 * @param array<string, mixed> $schema           The schema array.
 * @param array<string, true>  $allowed_keywords Lookup map of allowed JSON Schema keywords.
 * @return array<string, mixed> The prepared schema.
 */
function _wp_prepare_json_schema_for_client_with_allowed_keywords( array $schema, array $allowed_keywords ): array {
	if ( isset( $schema['type'] ) && 'object' === $schema['type'] && isset( $schema['default'] ) ) {
		$default = $schema['default'];
		if ( is_array( $default ) && empty( $default ) ) {
			$schema['default'] = (object) $default;
		}
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

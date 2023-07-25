<?php
/**
 * Navigation Fallback
 *
 * Functions required for managing Navigation fallbacks behavior.
 *
 * @package WordPress
 * @since 6.3.0
 */

/**
 * Expose additional fields in the embeddable links of the
 * Navigation Fallback REST endpoint.
 *
 * The endpoint may embed the full Navigation Menu object into the
 * response as the `self` link. By default, the Posts Controller
 * will only expose a limited subset of fields but the editor requires
 * additional fields to be available in order to utilize the menu.
 *
 * @since 6.3.0
 *
 * @param array $schema the schema for the `wp_navigation` post.
 * @return array the modified schema.
 */
function wp_add_fields_to_navigation_fallback_embedded_links( $schema ) {
	// Expose top level fields.
	$schema['properties']['status']['context']  = array_merge( $schema['properties']['status']['context'], array( 'embed' ) );
	$schema['properties']['content']['context'] = array_merge( $schema['properties']['content']['context'], array( 'embed' ) );

	/*
	 * Exposes sub properties of content field.
	 * These sub properties aren't exposed by the posts controller by default,
	 * for requests where context is `embed`.
	 *
	 * @see WP_REST_Posts_Controller::get_item_schema()
	 */
	$schema['properties']['content']['properties']['raw']['context']           = array_merge( $schema['properties']['content']['properties']['raw']['context'], array( 'embed' ) );
	$schema['properties']['content']['properties']['rendered']['context']      = array_merge( $schema['properties']['content']['properties']['rendered']['context'], array( 'embed' ) );
	$schema['properties']['content']['properties']['block_version']['context'] = array_merge( $schema['properties']['content']['properties']['block_version']['context'], array( 'embed' ) );

	/*
	 * Exposes sub properties of title field.
	 * These sub properties aren't exposed by the posts controller by default,
	 * for requests where context is `embed`.
	 *
	 * @see WP_REST_Posts_Controller::get_item_schema()
	 */
	$schema['properties']['title']['properties']['raw']['context'] = array_merge( $schema['properties']['title']['properties']['raw']['context'], array( 'embed' ) );

	return $schema;
}

add_filter(
	'rest_wp_navigation_item_schema',
	'wp_add_fields_to_navigation_fallback_embedded_links'
);

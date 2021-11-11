<?php
/**
 * Block navigation areas functions.
 *
 * @package WordPress
 */

/**
 * Registers the navigation areas supported by the current theme. The expected
 * shape of the argument is:
 * array(
 *     'primary'   => 'Primary',
 *     'secondary' => 'Secondary',
 *     'tertiary'  => 'Tertiary',
 * )
 *
 * @since 5.9.0
 *
 * @param array $new_areas Supported navigation areas.
 */
function register_navigation_areas( $new_areas ) {
	global $navigation_areas;
	$navigation_areas = $new_areas;
}

/**
 * Register the default navigation areas.
 *
 * @since 5.9.0
 * @access private
 */
function _register_default_navigation_areas() {
	register_navigation_areas(
		array(
			'primary'   => _x( 'Primary', 'navigation area' ),
			'secondary' => _x( 'Secondary', 'navigation area' ),
			'tertiary'  => _x( 'Tertiary', 'navigation area' ),
		)
	);
}

/**
 * Returns the available navigation areas.
 *
 * @since 5.9.0
 *
 * @return array Registered navigation areas.
 */
function get_navigation_areas() {
	global $navigation_areas;
	return $navigation_areas;
}

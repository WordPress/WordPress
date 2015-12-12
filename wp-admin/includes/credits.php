<?php
/**
 * WordPress Credits Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 */

/**
 * Retrieve the contributor credits.
 *
 * @global string $wp_version The current WordPress version.
 *
 * @since 3.2.0
 *
 * @return array|false A list of all of the contributors, or false on error.
 */
function wp_credits() {
	global $wp_version;
	$locale = get_locale();

	$results = get_site_transient( 'wordpress_credits_' . $locale );

	if ( ! is_array( $results )
		|| false !== strpos( $wp_version, '-' )
		|| ( isset( $results['data']['version'] ) && strpos( $wp_version, $results['data']['version'] ) !== 0 )
	) {
		$response = wp_remote_get( "http://api.wordpress.org/core/credits/1.1/?version=$wp_version&locale=$locale" );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
			return false;

		$results = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $results ) )
			return false;

		set_site_transient( 'wordpress_credits_' . $locale, $results, DAY_IN_SECONDS );
	}

	return $results;
}

/**
 * Retrieve the link to a contributor's WordPress.org profile page.
 *
 * @access private
 * @since 3.2.0
 *
 * @param string &$display_name The contributor's display name, passed by reference.
 * @param string $username      The contributor's username.
 * @param string $profiles      URL to the contributor's WordPress.org profile page.
 */
function _wp_credits_add_profile_link( &$display_name, $username, $profiles ) {
	$display_name = '<a href="' . esc_url( sprintf( $profiles, $username ) ) . '">' . esc_html( $display_name ) . '</a>';
}

/**
 * Retrieve the link to an external library used in WordPress.
 *
 * @access private
 * @since 3.2.0
 *
 * @param string &$data External library data, passed by reference.
 */
function _wp_credits_build_object_link( &$data ) {
	$data = '<a href="' . esc_url( $data[1] ) . '">' . esc_html( $data[0] ) . '</a>';
}

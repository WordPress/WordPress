<?php
/**
 * BackPress styles procedural API.
 *
 * @package BackPress
 * @since r79
 */

/**
 * Display styles that are in the queue or part of $handles.
 *
 * @since r79
 * @uses do_action() Calls 'wp_print_styles' hook.
 * @global object $wp_styles The WP_Styles object for printing styles.
 *
 * @param array $handles (optional) Styles to be printed.  (void) prints queue, (string) prints that style, (array of strings) prints those styles.
 * @return bool True on success, false on failure.
 */
function wp_print_styles( $handles = false ) {
	do_action( 'wp_print_styles' );
	if ( '' === $handles ) // for wp_head
		$handles = false;

	global $wp_styles;
	if ( !is_a($wp_styles, 'WP_Styles') ) {
		if ( !$handles )
			return array(); // No need to instantiate if nothing's there.
		else
			$wp_styles = new WP_Styles();
	}

	return $wp_styles->do_items( $handles );
}

/**
 * Register CSS style file.
 *
 * @since r79
 * @see WP_Styles::add() For parameter and additional information.
 */
function wp_register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
	global $wp_styles;
	if ( !is_a($wp_styles, 'WP_Styles') )
		$wp_styles = new WP_Styles();

	$wp_styles->add( $handle, $src, $deps, $ver, $media );
}

/**
 * Remove a registered CSS file.
 *
 * @since r79
 * @see WP_Styles::remove() For parameter and additional information.
 */
function wp_deregister_style( $handle ) {
	global $wp_styles;
	if ( !is_a($wp_styles, 'WP_Styles') )
		$wp_styles = new WP_Styles();

	$wp_styles->remove( $handle );
}

/**
 * Enqueue a CSS style file.
 *
 * @since r79
 * @see WP_Styles::add(), WP_Styles::enqueue()
 */
function wp_enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = false ) {
	global $wp_styles;
	if ( !is_a($wp_styles, 'WP_Styles') )
		$wp_styles = new WP_Styles();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$wp_styles->add( $_handle[0], $src, $deps, $ver, $media );
	}
	$wp_styles->enqueue( $handle );
}

/**
 * Check whether style has been added to WordPress Styles.
 *
 * The values for list defaults to 'queue', which is the same as enqueue for
 * styles.
 *
 * @since WP unknown; BP unknown
 *
 * @param string $handle Handle used to add style.
 * @param string $list Optional, defaults to 'queue'. Others values are 'registered', 'queue', 'done', 'to_do'
 * @return bool
 */
function wp_style_is( $handle, $list = 'queue' ) {
	global $wp_styles;
	if ( !is_a($wp_styles, 'WP_Styles') )
		$wp_styles = new WP_Styles();

	$query = $wp_styles->query( $handle, $list );

	if ( is_object( $query ) )
		return true;

	return $query;
}

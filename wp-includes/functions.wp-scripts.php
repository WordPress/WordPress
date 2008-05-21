<?php

/**
 * Prints script tags in document head
 *
 * Called by admin-header.php and by wp_head hook. Since it is called by wp_head on every page load,
 * the function does not instantiate the WP_Scripts object unless script names are explicitly passed.
 * Does make use of already instantiated $wp_scripts if present.
 * Use provided wp_print_scripts hook to register/enqueue new scripts.
 *
 * @see WP_Scripts::print_scripts()
 */
function wp_print_scripts( $handles = false ) {
	do_action( 'wp_print_scripts' );
	if ( '' === $handles ) // for wp_head
		$handles = false;

	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') ) {
		if ( !$handles )
			return array(); // No need to instantiate if nothing's there.
		else
			$wp_scripts = new WP_Scripts();
	}

	return $wp_scripts->do_items( $handles );
}

function wp_register_script( $handle, $src, $deps = array(), $ver = false ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		$wp_scripts = new WP_Scripts();

	$wp_scripts->add( $handle, $src, $deps, $ver );
}

/**
 * Localizes a script
 *
 * Localizes only if script has already been added
 *
 * @see WP_Script::localize()
 */
function wp_localize_script( $handle, $object_name, $l10n ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		return false;

	return $wp_scripts->localize( $handle, $object_name, $l10n );
}

function wp_deregister_script( $handle ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		$wp_scripts = new WP_Scripts();

	$wp_scripts->remove( $handle );
}

/**
 * Equeues script
 *
 * Registers the script if src provided (does NOT overwrite) and enqueues.
 *
 * @see WP_Script::add(), WP_Script::enqueue()
*/
function wp_enqueue_script( $handle, $src = false, $deps = array(), $ver = false ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		$wp_scripts = new WP_Scripts();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$wp_scripts->add( $_handle[0], $src, $deps, $ver );
	}
	$wp_scripts->enqueue( $handle );
}

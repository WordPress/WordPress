<?php
/**
 * BackPress script procedural API.
 *
 * @package BackPress
 * @since r16
 */

/**
 * Prints script tags in document head.
 *
 * Called by admin-header.php and by wp_head hook. Since it is called by wp_head
 * on every page load, the function does not instantiate the WP_Scripts object
 * unless script names are explicitly passed. Does make use of already
 * instantiated $wp_scripts if present. Use provided wp_print_scripts hook to
 * register/enqueue new scripts.
 *
 * @since r16
 * @see WP_Dependencies::print_scripts()
 */
function wp_print_scripts( $handles = false ) {
	do_action( 'wp_print_scripts' );
	if ( '' === $handles ) // for wp_head
		$handles = false;

	global $wp_scripts;
	if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>init</code>' ), '3.3' );

		if ( !$handles )
			return array(); // No need to instantiate if nothing is there.
		else
			$wp_scripts = new WP_Scripts();
	}

	return $wp_scripts->do_items( $handles );
}

/**
 * Register new Javascript file.
 *
 * @since r16
 * @param string $handle Script name
 * @param string $src Script url
 * @param array $deps (optional) Array of script names on which this script depends
 * @param string|bool $ver (optional) Script version (used for cache busting), set to NULL to disable
 * @param bool $in_footer (optional) Whether to enqueue the script before </head> or before </body>
 * @return null
 */
function wp_register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
	global $wp_scripts;
	if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>init</code>' ), '3.3' );
		$wp_scripts = new WP_Scripts();
	}

	$wp_scripts->add( $handle, $src, $deps, $ver );
	if ( $in_footer )
		$wp_scripts->add_data( $handle, 'group', 1 );
}

/**
 * Wrapper for $wp_scripts->localize().
 *
 * Used to localizes a script.
 * Works only if the script has already been added.
 * Accepts an associative array $l10n and creates JS object:
 * "$object_name" = {
 *   key: value,
 *   key: value,
 *   ...
 * }
 * See http://core.trac.wordpress.org/ticket/11520 for more information.
 *
 * @since r16
 *
 * @param string $handle The script handle that was registered or used in script-loader
 * @param string $object_name Name for the created JS object. This is passed directly so it should be qualified JS variable /[a-zA-Z0-9_]+/
 * @param array $l10n Associative PHP array containing the translated strings. HTML entities will be converted and the array will be JSON encoded.
 * @return bool Whether the localization was added successfully.
 */
function wp_localize_script( $handle, $object_name, $l10n ) {
	global $wp_scripts;
	if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>init</code>' ), '3.3' );

		return false;
	}

	return $wp_scripts->localize( $handle, $object_name, $l10n );
}

/**
 * Adds extra Javascript.
 *
 * Works only if the script referenced by $handle has already been added.
 * Accepts string $script that will be printed before the main script tag.
 *
 * @since 3.3
 * @see WP_Scripts::add_script_data()
 */
function wp_add_script_before( $handle, $script ) {
	global $wp_scripts;
	if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>init</code>' ), '3.3' );
		return false;
	}

	return $wp_scripts->add_script_data( $handle, $script );
}

/**
 * Remove a registered script.
 *
 * @since r16
 * @see WP_Scripts::remove() For parameter information.
 */
function wp_deregister_script( $handle ) {
	global $wp_scripts;
	if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>init</code>' ), '3.3' );
		$wp_scripts = new WP_Scripts();
	}

	$wp_scripts->remove( $handle );
}

/**
 * Enqueues script.
 *
 * Registers the script if src provided (does NOT overwrite) and enqueues.
 *
 * @since r16
 * @see wp_register_script() For parameter information.
 */
function wp_enqueue_script( $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ) {
	global $wp_scripts;
	if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>init</code>' ), '3.3' );
		$wp_scripts = new WP_Scripts();
	}

	if ( $src ) {
		$_handle = explode('?', $handle);
		$wp_scripts->add( $_handle[0], $src, $deps, $ver );
		if ( $in_footer )
			$wp_scripts->add_data( $_handle[0], 'group', 1 );
	}
	$wp_scripts->enqueue( $handle );
}

/**
 * Remove an enqueued script.
 *
 * @since WP 3.1
 * @see WP_Scripts::dequeue() For parameter information.
 */
function wp_dequeue_script( $handle ) {
	global $wp_scripts;
	if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>init</code>' ), '3.3' );
		$wp_scripts = new WP_Scripts();
	}

	$wp_scripts->dequeue( $handle );
}

/**
 * Check whether script has been added to WordPress Scripts.
 *
 * The values for list defaults to 'queue', which is the same as enqueue for
 * scripts.
 *
 * @since WP unknown; BP unknown
 *
 * @param string $handle Handle used to add script.
 * @param string $list Optional, defaults to 'queue'. Others values are 'registered', 'queue', 'done', 'to_do'
 * @return bool
 */
function wp_script_is( $handle, $list = 'queue' ) {
	global $wp_scripts;
	if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>init</code>' ), '3.3' );
		$wp_scripts = new WP_Scripts();
	}

	$query = $wp_scripts->query( $handle, $list );

	if ( is_object( $query ) )
		return true;

	return $query;
}

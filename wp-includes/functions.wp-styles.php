<?php
/**
 * BackPress Styles Procedural API
 *
 * @since 2.6.0
 *
 * @package WordPress
 * @subpackage BackPress
 */

/**
 * Initialize $wp_styles if it has not been set.
 *
 * @global WP_Styles $wp_styles
 *
 * @since 4.2.0
 *
 * @return WP_Styles WP_Styles instance.
 */
function wp_styles() {
	global $wp_styles;
	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		$wp_styles = new WP_Styles();
	}
	return $wp_styles;
}

/**
 * Display styles that are in the $handles queue.
 *
 * Passing an empty array to $handles prints the queue,
 * passing an array with one string prints that style,
 * and passing an array of strings prints those styles.
 *
 * @global WP_Styles $wp_styles The WP_Styles object for printing styles.
 *
 * @since 2.6.0
 *
 * @param string|bool|array $handles Styles to be printed. Default 'false'.
 * @return array On success, a processed array of WP_Dependencies items; otherwise, an empty array.
 */
function wp_print_styles( $handles = false ) {
	if ( '' === $handles ) { // for wp_head
		$handles = false;
	}
	/**
	 * Fires before styles in the $handles queue are printed.
	 *
	 * @since 2.6.0
	 */
	if ( ! $handles ) {
		do_action( 'wp_print_styles' );
	}

	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	global $wp_styles;
	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		if ( ! $handles ) {
			return array(); // No need to instantiate if nothing is there.
		}
	}

	return wp_styles()->do_items( $handles );
}

/**
 * Add extra CSS styles to a registered stylesheet.
 *
 * Styles will only be added if the stylesheet in already in the queue.
 * Accepts a string $data containing the CSS. If two or more CSS code blocks
 * are added to the same stylesheet $handle, they will be printed in the order
 * they were added, i.e. the latter added styles can redeclare the previous.
 *
 * @see WP_Styles::add_inline_style()
 *
 * @since 3.3.0
 *
 * @param string $handle Name of the stylesheet to add the extra styles to. Must be lowercase.
 * @param string $data   String containing the CSS styles to be added.
 * @return bool True on success, false on failure.
 */
function wp_add_inline_style( $handle, $data ) {
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	if ( false !== stripos( $data, '</style>' ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Do not pass style tags to wp_add_inline_style().' ), '3.7' );
		$data = trim( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $data ) );
	}

	return wp_styles()->add_inline_style( $handle, $data );
}

/**
 * Register a CSS stylesheet.
 *
 * @see WP_Dependencies::add()
 * @link http://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 * @since 2.6.0
 *
 * @param string      $handle Name of the stylesheet.
 * @param string|bool $src    Path to the stylesheet from the WordPress root directory. Example: '/css/mystyle.css'.
 * @param array       $deps   An array of registered style handles this stylesheet depends on. Default empty array.
 * @param string|bool $ver    String specifying the stylesheet version number. Used to ensure that the correct version
 *                            is sent to the client regardless of caching. Default 'false'. Accepts 'false', 'null', or 'string'.
 * @param string      $media  Optional. The media for which this stylesheet has been defined.
 *                            Default 'all'. Accepts 'all', 'aural', 'braille', 'handheld', 'projection', 'print',
 *                            'screen', 'tty', or 'tv'.
 */
function wp_register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	wp_styles()->add( $handle, $src, $deps, $ver, $media );
}

/**
 * Remove a registered stylesheet.
 *
 * @see WP_Dependencies::remove()
 *
 * @since 2.1.0
 *
 * @param string $handle Name of the stylesheet to be removed.
 */
function wp_deregister_style( $handle ) {
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	wp_styles()->remove( $handle );
}

/**
 * Enqueue a CSS stylesheet.
 *
 * Registers the style if source provided (does NOT overwrite) and enqueues.
 *
 * @see WP_Dependencies::add(), WP_Dependencies::enqueue()
 * @link http://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 * @since 2.6.0
 *
 * @param string      $handle Name of the stylesheet.
 * @param string|bool $src    Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
 * @param array       $deps   An array of registered style handles this stylesheet depends on. Default empty array.
 * @param string|bool $ver    String specifying the stylesheet version number, if it has one. This parameter is used
 *                            to ensure that the correct version is sent to the client regardless of caching, and so
 *                            should be included if a version number is available and makes sense for the stylesheet.
 * @param string      $media  Optional. The media for which this stylesheet has been defined.
 *                            Default 'all'. Accepts 'all', 'aural', 'braille', 'handheld', 'projection', 'print',
 *                            'screen', 'tty', or 'tv'.
 */
function wp_enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {
	global $wp_styles;
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	$wp_styles = wp_styles();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$wp_styles->add( $_handle[0], $src, $deps, $ver, $media );
	}
	$wp_styles->enqueue( $handle );
}

/**
 * Remove a previously enqueued CSS stylesheet.
 *
 * @see WP_Dependencies::dequeue()
 *
 * @since 3.1.0
 *
 * @param string $handle Name of the stylesheet to be removed.
 */
function wp_dequeue_style( $handle ) {
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	wp_styles()->dequeue( $handle );
}

/**
 * Check whether a CSS stylesheet has been added to the queue.
 *
 * @global WP_Styles $wp_styles The WP_Styles object for printing styles.
 *
 * @since 2.8.0
 *
 * @param string $handle Name of the stylesheet.
 * @param string $list   Optional. Status of the stylesheet to check. Default 'enqueued'.
 *                       Accepts 'enqueued', 'registered', 'queue', 'to_do', and 'done'.
 * @return bool Whether style is queued.
 */
function wp_style_is( $handle, $list = 'enqueued' ) {
	_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );

	return (bool) wp_styles()->query( $handle, $list );
}

/**
 * Add metadata to a CSS stylesheet.
 *
 * Works only if the stylesheet has already been added.
 *
 * Possible values for $key and $value:
 * 'conditional' string      Comments for IE 6, lte IE 7 etc.
 * 'rtl'         bool|string To declare an RTL stylesheet.
 * 'suffix'      string      Optional suffix, used in combination with RTL.
 * 'alt'         bool        For rel="alternate stylesheet".
 * 'title'       string      For preferred/alternate stylesheets.
 *
 * @see WP_Dependency::add_data()
 *
 * @since 3.6.0
 *
 * @param string $handle Name of the stylesheet.
 * @param string $key    Name of data point for which we're storing a value.
 *                       Accepts 'conditional', 'rtl' and 'suffix', 'alt' and 'title'.
 * @param mixed  $value  String containing the CSS data to be added.
 * @return bool True on success, false on failure.
 */
function wp_style_add_data( $handle, $key, $value ) {
	return wp_styles()->add_data( $handle, $key, $value );
}

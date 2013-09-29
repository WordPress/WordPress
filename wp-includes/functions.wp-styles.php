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
 * @param array|bool $handles Styles to be printed. An empty array prints the queue,
 *  an array with one string prints that style, and an array of strings prints those styles.
 * @return bool True on success, false on failure.
 */
function wp_print_styles( $handles = false ) {
	if ( '' === $handles ) // for wp_head
		$handles = false;

	if ( ! $handles )
		do_action( 'wp_print_styles' );

	global $wp_styles;
	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );

		if ( !$handles )
			return array(); // No need to instantiate if nothing is there.
		else
			$wp_styles = new WP_Styles();
	}

	return $wp_styles->do_items( $handles );
}

/**
 * Adds extra CSS.
 *
 * Works only if the stylesheet has already been added.
 * Accepts a string $data containing the CSS. If two or more CSS code blocks are
 * added to the same stylesheet $handle, they will be printed in the order
 * they were added, i.e. the latter added styles can redeclare the previous.
 *
 * @since 3.3.0
 * @see WP_Scripts::add_inline_style()
 */
function wp_add_inline_style( $handle, $data ) {
	global $wp_styles;
	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$wp_styles = new WP_Styles();
	}

	return $wp_styles->add_inline_style( $handle, $data );
}

/**
 * Register CSS style file.
 *
 * @since r79
 * @see WP_Styles::add() For additional information.
 * @global object $wp_styles The WP_Styles object for printing styles.
 * @link http://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 * @param string $handle Name of the stylesheet.
 * @param string|bool $src Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
 * @param array $deps Array of handles of any stylesheet that this stylesheet depends on.
 *  (Stylesheets that must be loaded before this stylesheet.) Pass an empty array if there are no dependencies.
 * @param string|bool $ver String specifying the stylesheet version number. Set to null to disable.
 *  Used to ensure that the correct version is sent to the client regardless of caching.
 * @param string $media The media for which this stylesheet has been defined.
 */
function wp_register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
	global $wp_styles;
	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$wp_styles = new WP_Styles();
	}

	$wp_styles->add( $handle, $src, $deps, $ver, $media );
}

/**
 * Remove a registered CSS file.
 *
 * @since r79
 * @see WP_Styles::remove() For additional information.
 * @global object $wp_styles The WP_Styles object for printing styles.
 *
 * @param string $handle Name of the stylesheet.
 */
function wp_deregister_style( $handle ) {
	global $wp_styles;
	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$wp_styles = new WP_Styles();
	}

	$wp_styles->remove( $handle );
}

/**
 * Enqueue a CSS style file.
 *
 * Registers the style if src provided (does NOT overwrite) and enqueues.
 *
 * @since r79
 * @see WP_Styles::add(), WP_Styles::enqueue()
 * @global object $wp_styles The WP_Styles object for printing styles.
 * @link http://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 * @param string $handle Name of the stylesheet.
 * @param string|bool $src Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
 * @param array $deps Array of handles (names) of any stylesheet that this stylesheet depends on.
 *  (Stylesheets that must be loaded before this stylesheet.) Pass an empty array if there are no dependencies.
 * @param string|bool $ver String specifying the stylesheet version number, if it has one. This parameter
 *  is used to ensure that the correct version is sent to the client regardless of caching, and so should be included
 *  if a version number is available and makes sense for the stylesheet.
 * @param string $media The media for which this stylesheet has been defined.
 */
function wp_enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {
	global $wp_styles;
	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$wp_styles = new WP_Styles();
	}

	if ( $src ) {
		$_handle = explode('?', $handle);
		$wp_styles->add( $_handle[0], $src, $deps, $ver, $media );
	}
	$wp_styles->enqueue( $handle );
}

/**
 * Remove an enqueued style.
 *
 * @since WP 3.1
 * @see WP_Styles::dequeue() For parameter information.
 */
function wp_dequeue_style( $handle ) {
	global $wp_styles;
	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$wp_styles = new WP_Styles();
	}

	$wp_styles->dequeue( $handle );
}

/**
 * Check whether style has been added to WordPress Styles.
 *
 * By default, checks if the style has been enqueued. You can also
 * pass 'registered' to $list, to see if the style is registered,
 * and you can check processing statuses with 'to_do' and 'done'.
 *
 * @since WP unknown; BP unknown
 * @global object $wp_styles The WP_Styles object for printing styles.
 *
 * @param string $handle Name of the stylesheet.
 * @param string $list Optional. Defaults to 'enqueued'. Values are
 * 	'registered', 'enqueued' (or 'queue'), 'to_do', and 'done'.
 * @return bool Whether style is in the list.
 */
function wp_style_is( $handle, $list = 'enqueued' ) {
	global $wp_styles;
	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
		if ( ! did_action( 'init' ) )
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Scripts and styles should not be registered or enqueued until the %1$s, %2$s, or %3$s hooks.' ),
				'<code>wp_enqueue_scripts</code>', '<code>admin_enqueue_scripts</code>', '<code>login_enqueue_scripts</code>' ), '3.3' );
		$wp_styles = new WP_Styles();
	}

	return (bool) $wp_styles->query( $handle, $list );
}

/**
 * Add metadata to CSS style files.
 *
 * Works only if the stylesheet has already been added.
 * Possible values for $key and $value:
 *
 * conditional string      comments for IE 6, lte IE 7 etc.
 * rtl         bool|string to declare an RTL stylesheet
 * suffix      string      optional suffix, used in combination with RTL
 * alt         bool        for rel="alternate stylesheet"
 * title       string      for preferred/alternate stylesheets
 *
 * @since 3.6.0
 * @see WP_Dependencies::add_data()
 *
 * @param string $handle Script name.
 * @param string $key Name of data point for which we're storing a value.
 *  Values are 'conditional', 'rtl', and 'suffix', and 'alt', 'title'.
 * @param mixed $data
 * @return bool True on success, false on failure.
 */
function wp_style_add_data( $handle, $key, $value ) {
	global $wp_styles;
	return $wp_styles->add_data( $handle, $key, $value );
}

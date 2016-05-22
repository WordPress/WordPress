<?php
/**
 * WordPress Administration Screen API.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Get the column headers for a screen
 *
 * @since 2.7.0
 *
 * @staticvar array $column_headers
 *
 * @param string|WP_Screen $screen The screen you want the headers for
 * @return array Containing the headers in the format id => UI String
 */
function get_column_headers( $screen ) {
	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	static $column_headers = array();

	if ( ! isset( $column_headers[ $screen->id ] ) ) {

		/**
		 * Filters the column headers for a list table on a specific screen.
		 *
		 * The dynamic portion of the hook name, `$screen->id`, refers to the
		 * ID of a specific screen. For example, the screen ID for the Posts
		 * list table is edit-post, so the filter for that screen would be
		 * manage_edit-post_columns.
		 *
		 * @since 3.0.0
		 *
		 * @param array $columns An array of column headers. Default empty.
		 */
		$column_headers[ $screen->id ] = apply_filters( "manage_{$screen->id}_columns", array() );
	}

	return $column_headers[ $screen->id ];
}

/**
 * Get a list of hidden columns.
 *
 * @since 2.7.0
 *
 * @param string|WP_Screen $screen The screen you want the hidden columns for
 * @return array
 */
function get_hidden_columns( $screen ) {
	if ( is_string( $screen ) ) {
		$screen = convert_to_screen( $screen );
	}

	$hidden = get_user_option( 'manage' . $screen->id . 'columnshidden' );

	$use_defaults = ! is_array( $hidden );

	if ( $use_defaults ) {
		$hidden = array();

		/**
		 * Filters the default list of hidden columns.
		 *
		 * @since 4.4.0
		 *
		 * @param array     $hidden An array of columns hidden by default.
		 * @param WP_Screen $screen WP_Screen object of the current screen.
		 */
		$hidden = apply_filters( 'default_hidden_columns', $hidden, $screen );
	}

	/**
	 * Filters the list of hidden columns.
	 *
	 * @since 4.4.0
	 * @since 4.4.1 Added the `use_defaults` parameter.
	 *
	 * @param array     $hidden An array of hidden columns.
	 * @param WP_Screen $screen WP_Screen object of the current screen.
	 * @param bool      $use_defaults Whether to show the default columns.
	 */
	return apply_filters( 'hidden_columns', $hidden, $screen, $use_defaults );
}

/**
 * Prints the meta box preferences for screen meta.
 *
 * @since 2.7.0
 *
 * @global array $wp_meta_boxes
 *
 * @param WP_Screen $screen
 */
function meta_box_prefs( $screen ) {
	global $wp_meta_boxes;

	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	if ( empty($wp_meta_boxes[$screen->id]) )
		return;

	$hidden = get_hidden_meta_boxes($screen);

	foreach ( array_keys( $wp_meta_boxes[ $screen->id ] ) as $context ) {
		foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
			if ( ! isset( $wp_meta_boxes[ $screen->id ][ $context ][ $priority ] ) ) {
				continue;
			}
			foreach ( $wp_meta_boxes[ $screen->id ][ $context ][ $priority ] as $box ) {
				if ( false == $box || ! $box['title'] )
					continue;
				// Submit box cannot be hidden
				if ( 'submitdiv' == $box['id'] || 'linksubmitdiv' == $box['id'] )
					continue;
				$box_id = $box['id'];
				echo '<label for="' . $box_id . '-hide">';
				echo '<input class="hide-postbox-tog" name="' . $box_id . '-hide" type="checkbox" id="' . $box_id . '-hide" value="' . $box_id . '"' . (! in_array($box_id, $hidden) ? ' checked="checked"' : '') . ' />';
				echo "{$box['title']}</label>\n";
			}
		}
	}
}

/**
 * Get Hidden Meta Boxes
 *
 * @since 2.7.0
 *
 * @param string|WP_Screen $screen Screen identifier
 * @return array Hidden Meta Boxes
 */
function get_hidden_meta_boxes( $screen ) {
	if ( is_string( $screen ) )
		$screen = convert_to_screen( $screen );

	$hidden = get_user_option( "metaboxhidden_{$screen->id}" );

	$use_defaults = ! is_array( $hidden );

	// Hide slug boxes by default
	if ( $use_defaults ) {
		$hidden = array();
		if ( 'post' == $screen->base ) {
			if ( 'post' == $screen->post_type || 'page' == $screen->post_type || 'attachment' == $screen->post_type )
				$hidden = array('slugdiv', 'trackbacksdiv', 'postcustom', 'postexcerpt', 'commentstatusdiv', 'commentsdiv', 'authordiv', 'revisionsdiv');
			else
				$hidden = array( 'slugdiv' );
		}

		/**
		 * Filters the default list of hidden meta boxes.
		 *
		 * @since 3.1.0
		 *
		 * @param array     $hidden An array of meta boxes hidden by default.
		 * @param WP_Screen $screen WP_Screen object of the current screen.
		 */
		$hidden = apply_filters( 'default_hidden_meta_boxes', $hidden, $screen );
	}

	/**
	 * Filters the list of hidden meta boxes.
	 *
	 * @since 3.3.0
	 *
	 * @param array     $hidden       An array of hidden meta boxes.
	 * @param WP_Screen $screen       WP_Screen object of the current screen.
	 * @param bool      $use_defaults Whether to show the default meta boxes.
	 *                                Default true.
	 */
	return apply_filters( 'hidden_meta_boxes', $hidden, $screen, $use_defaults );
}

/**
 * Register and configure an admin screen option
 *
 * @since 3.1.0
 *
 * @param string $option An option name.
 * @param mixed $args Option-dependent arguments.
 */
function add_screen_option( $option, $args = array() ) {
	$current_screen = get_current_screen();

	if ( ! $current_screen )
		return;

	$current_screen->add_option( $option, $args );
}

/**
 * Get the current screen object
 *
 * @since 3.1.0
 *
 * @global WP_Screen $current_screen
 *
 * @return WP_Screen Current screen object
 */
function get_current_screen() {
	global $current_screen;

	if ( ! isset( $current_screen ) )
		return null;

	return $current_screen;
}

/**
 * Set the current screen object
 *
 * @since 3.0.0
 *
 * @param mixed $hook_name Optional. The hook name (also known as the hook suffix) used to determine the screen,
 *	                       or an existing screen object.
 */
function set_current_screen( $hook_name = '' ) {
	WP_Screen::get( $hook_name )->set_current_screen();
}

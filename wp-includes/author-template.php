<?php
/**
 * Author Template functions for use in themes.
 *
 * These functions must be used within the WordPress Loop.
 *
 * @link https://codex.wordpress.org/Author_Templates
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Retrieve the author of the current post.
 *
 * @since 1.5.0
 *
 * @global WP_User $authordata The current author's data.
 *
 * @param string $deprecated Deprecated.
 * @return string|null The author's display name.
 */
function get_the_author( $deprecated = '' ) {
	global $authordata;

	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.1.0' );
	}

	/**
	 * Filters the display name of the current post's author.
	 *
	 * @since 2.9.0
	 *
	 * @param string|null $display_name The author's display name.
	 */
	return apply_filters( 'the_author', is_object( $authordata ) ? $authordata->display_name : null );
}

/**
 * Display the name of the author of the current post.
 *
 * The behavior of this function is based off of old functionality predating
 * get_the_author(). This function is not deprecated, but is designed to echo
 * the value from get_the_author() and as an result of any old theme that might
 * still use the old behavior will also pass the value from get_the_author().
 *
 * The normal, expected behavior of this function is to echo the author and not
 * return it. However, backward compatibility has to be maintained.
 *
 * @since 0.71
 *
 * @see get_the_author()
 * @link https://developer.wordpress.org/reference/functions/the_author/
 *
 * @param string $deprecated      Deprecated.
 * @param bool   $deprecated_echo Deprecated. Use get_the_author(). Echo the string or return it.
 * @return string|null The author's display name, from get_the_author().
 */
function the_author( $deprecated = '', $deprecated_echo = true ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.1.0' );
	}

	if ( true !== $deprecated_echo ) {
		_deprecated_argument(
			__FUNCTION__,
			'1.5.0',
			sprintf(
				/* translators: %s: get_the_author() */
				__( 'Use %s instead if you do not want the value echoed.' ),
				'<code>get_the_author()</code>'
			)
		);
	}

	if ( $deprecated_echo ) {
		echo get_the_author();
	}

	return get_the_author();
}

/**
 * Retrieve the author who last edited the current post.
 *
 * @since 2.8.0
 *
 * @return string|void The author's display name.
 */
function get_the_modified_author() {
	$last_id = get_post_meta( get_post()->ID, '_edit_last', true );

	if ( $last_id ) {
		$last_user = get_userdata( $last_id );

		/**
		 * Filters the display name of the author who last edited the current post.
		 *
		 * @since 2.8.0
		 *
		 * @param string $display_name The author's display name.
		 */
		return apply_filters( 'the_modified_author', $last_user->display_name );
	}
}

/**
 * Display the name of the author who last edited the current post,
 * if the author's ID is available.
 *
 * @since 2.8.0
 *
 * @see get_the_author()
 */
function the_modified_author() {
	echo get_the_modified_author();
}

/**
 * Retrieves the requested data of the author of the current post.
 *
 * Valid values for the `$field` parameter include:
 *
 * - admin_color
 * - aim
 * - comment_shortcuts
 * - description
 * - display_name
 * - first_name
 * - ID
 * - jabber
 * - last_name
 * - nickname
 * - plugins_last_view
 * - plugins_per_page
 * - rich_editing
 * - syntax_highlighting
 * - user_activation_key
 * - user_description
 * - user_email
 * - user_firstname
 * - user_lastname
 * - user_level
 * - user_login
 * - user_nicename
 * - user_pass
 * - user_registered
 * - user_status
 * - user_url
 * - yim
 *
 * @since 2.8.0
 *
 * @global WP_User $authordata The current author's data.
 *
 * @param string    $field   Optional. The user field to retrieve. Default empty.
 * @param int|false $user_id Optional. User ID.
 * @return string The author's field from the current author's DB object, otherwise an empty string.
 */
function get_the_author_meta( $field = '', $user_id = false ) {
	$original_user_id = $user_id;

	if ( ! $user_id ) {
		global $authordata;
		$user_id = isset( $authordata->ID ) ? $authordata->ID : 0;
	} else {
		$authordata = get_userdata( $user_id );
	}

	if ( in_array( $field, array( 'login', 'pass', 'nicename', 'email', 'url', 'registered', 'activation_key', 'status' ), true ) ) {
		$field = 'user_' . $field;
	}

	$value = isset( $authordata->$field ) ? $authordata->$field : '';

	/**
	 * Filters the value of the requested user metadata.
	 *
	 * The filter name is dynamic and depends on the $field parameter of the function.
	 *
	 * @since 2.8.0
	 * @since 4.3.0 The `$original_user_id` parameter was added.
	 *
	 * @param string    $value            The value of the metadata.
	 * @param int       $user_id          The user ID for the value.
	 * @param int|false $original_user_id The original user ID, as passed to the function.
	 */
	return apply_filters( "get_the_author_{$field}", $value, $user_id, $original_user_id );
}

/**
 * Outputs the field from the user's DB object. Defaults to current post's author.
 *
 * @since 2.8.0
 *
 * @param string    $field   Selects the field of the users record. See get_the_author_meta()
 *                           for the list of possible fields.
 * @param int|false $user_id Optional. User ID.
 *
 * @see get_the_author_meta()
 */
function the_author_meta( $field = '', $user_id = false ) {
	$author_meta = get_the_author_meta( $field, $user_id );

	/**
	 * The value of the requested user metadata.
	 *
	 * The filter name is dynamic and depends on the $field parameter of the function.
	 *
	 * @since 2.8.0
	 *
	 * @param string    $author_meta The value of the metadata.
	 * @param int|false $user_id     The user ID.
	 */
	echo apply_filters( "the_author_{$field}", $author_meta, $user_id );
}

/**
 * Retrieve either author's link or author's name.
 *
 * If the author has a home page set, return an HTML link, otherwise just return the
 * author's name.
 *
 * @since 3.0.0
 *
 * @return string|null An HTML link if the author's url exist in user meta,
 *                     else the result of get_the_author().
 */
function get_the_author_link() {
	if ( get_the_author_meta( 'url' ) ) {
		return sprintf(
			'<a href="%1$s" title="%2$s" rel="author external">%3$s</a>',
			esc_url( get_the_author_meta( 'url' ) ),
			/* translators: %s: Author's display name. */
			esc_attr( sprintf( __( 'Visit %s&#8217;s website' ), get_the_author() ) ),
			get_the_author()
		);
	} else {
		return get_the_author();
	}
}

/**
 * Display either author's link or author's name.
 *
 * If the author has a home page set, echo an HTML link, otherwise just echo the
 * author's name.
 *
 * @link https://developer.wordpress.org/reference/functions/the_author_link/
 *
 * @since 2.1.0
 */
function the_author_link() {
	echo get_the_author_link();
}

/**
 * Retrieve the number of posts by the author of the current post.
 *
 * @since 1.5.0
 *
 * @return int The number of posts by the author.
 */
function get_the_author_posts() {
	$post = get_post();
	if ( ! $post ) {
		return 0;
	}
	return count_user_posts( $post->post_author, $post->post_type );
}

/**
 * Display the number of posts by the author of the current post.
 *
 * @link https://developer.wordpress.org/reference/functions/the_author_posts/
 * @since 0.71
 */
function the_author_posts() {
	echo get_the_author_posts();
}

/**
 * Retrieves an HTML link to the author page of the current post's author.
 *
 * Returns an HTML-formatted link using get_author_posts_url().
 *
 * @since 4.4.0
 *
 * @global WP_User $authordata The current author's data.
 *
 * @return string An HTML link to the author page, or an empty string if $authordata isn't defined.
 */
function get_the_author_posts_link() {
	global $authordata;
	if ( ! is_object( $authordata ) ) {
		return '';
	}

	$link = sprintf(
		'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
		esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
		/* translators: %s: Author's display name. */
		esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ),
		get_the_author()
	);

	/**
	 * Filters the link to the author page of the author of the current post.
	 *
	 * @since 2.9.0
	 *
	 * @param string $link HTML link.
	 */
	return apply_filters( 'the_author_posts_link', $link );
}

/**
 * Displays an HTML link to the author page of the current post's author.
 *
 * @since 1.2.0
 * @since 4.4.0 Converted into a wrapper for get_the_author_posts_link()
 *
 * @param string $deprecated Unused.
 */
function the_author_posts_link( $deprecated = '' ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.1.0' );
	}
	echo get_the_author_posts_link();
}

/**
 * Retrieve the URL to the author page for the user with the ID provided.
 *
 * @since 2.1.0
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param int    $author_id       Author ID.
 * @param string $author_nicename Optional. The author's nicename (slug). Default empty.
 * @return string The URL to the author's page.
 */
function get_author_posts_url( $author_id, $author_nicename = '' ) {
	global $wp_rewrite;
	$auth_ID = (int) $author_id;
	$link    = $wp_rewrite->get_author_permastruct();

	if ( empty( $link ) ) {
		$file = home_url( '/' );
		$link = $file . '?author=' . $auth_ID;
	} else {
		if ( '' === $author_nicename ) {
			$user = get_userdata( $author_id );
			if ( ! empty( $user->user_nicename ) ) {
				$author_nicename = $user->user_nicename;
			}
		}
		$link = str_replace( '%author%', $author_nicename, $link );
		$link = home_url( user_trailingslashit( $link ) );
	}

	/**
	 * Filters the URL to the author's page.
	 *
	 * @since 2.1.0
	 *
	 * @param string $link            The URL to the author's page.
	 * @param int    $author_id       The author's ID.
	 * @param string $author_nicename The author's nice name.
	 */
	$link = apply_filters( 'author_link', $link, $author_id, $author_nicename );

	return $link;
}

/**
 * List all the authors of the site, with several options available.
 *
 * @link https://developer.wordpress.org/reference/functions/wp_list_authors/
 *
 * @since 1.2.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string|array $args {
 *     Optional. Array or string of default arguments.
 *
 *     @type string       $orderby       How to sort the authors. Accepts 'nicename', 'email', 'url', 'registered',
 *                                       'user_nicename', 'user_email', 'user_url', 'user_registered', 'name',
 *                                       'display_name', 'post_count', 'ID', 'meta_value', 'user_login'. Default 'name'.
 *     @type string       $order         Sorting direction for $orderby. Accepts 'ASC', 'DESC'. Default 'ASC'.
 *     @type int          $number        Maximum authors to return or display. Default empty (all authors).
 *     @type bool         $optioncount   Show the count in parenthesis next to the author's name. Default false.
 *     @type bool         $exclude_admin Whether to exclude the 'admin' account, if it exists. Default true.
 *     @type bool         $show_fullname Whether to show the author's full name. Default false.
 *     @type bool         $hide_empty    Whether to hide any authors with no posts. Default true.
 *     @type string       $feed          If not empty, show a link to the author's feed and use this text as the alt
 *                                       parameter of the link. Default empty.
 *     @type string       $feed_image    If not empty, show a link to the author's feed and use this image URL as
 *                                       clickable anchor. Default empty.
 *     @type string       $feed_type     The feed type to link to. Possible values include 'rss2', 'atom'.
 *                                       Default is the value of get_default_feed().
 *     @type bool         $echo          Whether to output the result or instead return it. Default true.
 *     @type string       $style         If 'list', each author is wrapped in an `<li>` element, otherwise the authors
 *                                       will be separated by commas.
 *     @type bool         $html          Whether to list the items in HTML form or plaintext. Default true.
 *     @type array|string $exclude       Array or comma/space-separated list of author IDs to exclude. Default empty.
 *     @type array|string $include       Array or comma/space-separated list of author IDs to include. Default empty.
 * }
 * @return void|string Void if 'echo' argument is true, list of authors if 'echo' is false.
 */
function wp_list_authors( $args = '' ) {
	global $wpdb;

	$defaults = array(
		'orderby'       => 'name',
		'order'         => 'ASC',
		'number'        => '',
		'optioncount'   => false,
		'exclude_admin' => true,
		'show_fullname' => false,
		'hide_empty'    => true,
		'feed'          => '',
		'feed_image'    => '',
		'feed_type'     => '',
		'echo'          => true,
		'style'         => 'list',
		'html'          => true,
		'exclude'       => '',
		'include'       => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$return = '';

	$query_args           = wp_array_slice_assoc( $args, array( 'orderby', 'order', 'number', 'exclude', 'include' ) );
	$query_args['fields'] = 'ids';
	$authors              = get_users( $query_args );

	$author_count = array();
	foreach ( (array) $wpdb->get_results( "SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE " . get_private_posts_cap_sql( 'post' ) . ' GROUP BY post_author' ) as $row ) {
		$author_count[ $row->post_author ] = $row->count;
	}
	foreach ( $authors as $author_id ) {
		$posts = isset( $author_count[ $author_id ] ) ? $author_count[ $author_id ] : 0;

		if ( ! $posts && $args['hide_empty'] ) {
			continue;
		}

		$author = get_userdata( $author_id );

		if ( $args['exclude_admin'] && 'admin' === $author->display_name ) {
			continue;
		}

		if ( $args['show_fullname'] && $author->first_name && $author->last_name ) {
			$name = "$author->first_name $author->last_name";
		} else {
			$name = $author->display_name;
		}

		if ( ! $args['html'] ) {
			$return .= $name . ', ';

			continue; // No need to go further to process HTML.
		}

		if ( 'list' === $args['style'] ) {
			$return .= '<li>';
		}

		$link = sprintf(
			'<a href="%1$s" title="%2$s">%3$s</a>',
			get_author_posts_url( $author->ID, $author->user_nicename ),
			/* translators: %s: Author's display name. */
			esc_attr( sprintf( __( 'Posts by %s' ), $author->display_name ) ),
			$name
		);

		if ( ! empty( $args['feed_image'] ) || ! empty( $args['feed'] ) ) {
			$link .= ' ';
			if ( empty( $args['feed_image'] ) ) {
				$link .= '(';
			}

			$link .= '<a href="' . get_author_feed_link( $author->ID, $args['feed_type'] ) . '"';

			$alt = '';
			if ( ! empty( $args['feed'] ) ) {
				$alt  = ' alt="' . esc_attr( $args['feed'] ) . '"';
				$name = $args['feed'];
			}

			$link .= '>';

			if ( ! empty( $args['feed_image'] ) ) {
				$link .= '<img src="' . esc_url( $args['feed_image'] ) . '" style="border: none;"' . $alt . ' />';
			} else {
				$link .= $name;
			}

			$link .= '</a>';

			if ( empty( $args['feed_image'] ) ) {
				$link .= ')';
			}
		}

		if ( $args['optioncount'] ) {
			$link .= ' (' . $posts . ')';
		}

		$return .= $link;
		$return .= ( 'list' === $args['style'] ) ? '</li>' : ', ';
	}

	$return = rtrim( $return, ', ' );

	if ( $args['echo'] ) {
		echo $return;
	} else {
		return $return;
	}
}

/**
 * Determines whether this site has more than one author.
 *
 * Checks to see if more than one author has published posts.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 3.2.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return bool Whether or not we have more than one author
 */
function is_multi_author() {
	global $wpdb;

	$is_multi_author = get_transient( 'is_multi_author' );
	if ( false === $is_multi_author ) {
		$rows            = (array) $wpdb->get_col( "SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' LIMIT 2" );
		$is_multi_author = 1 < count( $rows ) ? 1 : 0;
		set_transient( 'is_multi_author', $is_multi_author );
	}

	/**
	 * Filters whether the site has more than one author with published posts.
	 *
	 * @since 3.2.0
	 *
	 * @param bool $is_multi_author Whether $is_multi_author should evaluate as true.
	 */
	return apply_filters( 'is_multi_author', (bool) $is_multi_author );
}

/**
 * Helper function to clear the cache for number of authors.
 *
 * @since 3.2.0
 * @access private
 */
function __clear_multi_author_cache() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionDoubleUnderscore,PHPCompatibility.FunctionNameRestrictions.ReservedFunctionNames.FunctionDoubleUnderscore
	delete_transient( 'is_multi_author' );
}

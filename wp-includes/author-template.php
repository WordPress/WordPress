<?php
/**
 * Author Template functions for use in themes.
 *
 * These functions must be used within the WordPress Loop.
 *
 * @link http://codex.wordpress.org/Author_Templates
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Retrieve the author of the current post.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @uses apply_filters() Calls 'the_author' hook on the author display name.
 *
 * @param string $deprecated Deprecated.
 * @return string The author's display name.
 */
function get_the_author($deprecated = '') {
	global $authordata;

	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '0.0' );

	return apply_filters('the_author', is_object($authordata) ? $authordata->display_name : null);
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
 * return it. However, backwards compatiability has to be maintained.
 *
 * @since 0.71
 * @see get_the_author()
 * @link http://codex.wordpress.org/Template_Tags/the_author
 *
 * @param string $deprecated Deprecated.
 * @param string $deprecated_echo Deprecated. Use get_the_author(). Echo the string or return it.
 * @return string The author's display name, from get_the_author().
 */
function the_author( $deprecated = '', $deprecated_echo = true ) {
	if ( !empty( $deprecated ) || $deprecated_echo !== true )
		_deprecated_argument( __FUNCTION__, '1.5', $deprecated_echo !== true ? __('Use get_the_author() instead if you do not want the value echoed.') : null );
	if ( $deprecated_echo )
		echo get_the_author();
	return get_the_author();
}

/**
 * Retrieve the author who last edited the current post.
 *
 * @since 2.8
 * @uses $post The current post's DB object.
 * @uses get_post_meta() Retrieves the ID of the author who last edited the current post.
 * @uses get_userdata() Retrieves the author's DB object.
 * @uses apply_filters() Calls 'the_modified_author' hook on the author display name.
 * @return string The author's display name.
 */
function get_the_modified_author() {
	global $post;
	if ( $last_id = get_post_meta($post->ID, '_edit_last', true) ) {
		$last_user = get_userdata($last_id);
		return apply_filters('the_modified_author', $last_user->display_name);
	}
}

/**
 * Display the name of the author who last edited the current post.
 *
 * @since 2.8
 * @see get_the_author()
 * @return string The author's display name, from get_the_modified_author().
 */
function the_modified_author() {
	echo get_the_modified_author();
}

/**
 * Retrieve the requested data of the author of the current post.
 * @link http://codex.wordpress.org/Template_Tags/the_author_meta
 * @since 2.8.0
 * @uses $authordata The current author's DB object (if $user_id not specified).
 * @param string $field selects the field of the users record.
 * @param int $user_id Optional. User ID.
 * @return string The author's field from the current author's DB object.
 */
function get_the_author_meta($field = '', $user_id = false) {
	if ( ! $user_id )
		global $authordata;
	else
		$authordata = get_userdata( $user_id );

	$field = strtolower($field);
	$user_field = "user_$field";

	if ( 'id' == $field )
		$value = isset($authordata->ID) ? (int)$authordata->ID : 0;
	elseif ( isset($authordata->$user_field) )
		$value = $authordata->$user_field;
	else
		$value = isset($authordata->$field) ? $authordata->$field : '';

	return apply_filters('get_the_author_' . $field, $value, $user_id);
}

/**
 * Retrieve the requested data of the author of the current post.
 * @link http://codex.wordpress.org/Template_Tags/the_author_meta
 * @since 2.8.0
 * @param string $field selects the field of the users record.
 * @param int $user_id Optional. User ID.
 * @echo string The author's field from the current author's DB object.
 */
function the_author_meta($field = '', $user_id = false) {
	echo apply_filters('the_author_' . $field, get_the_author_meta($field, $user_id), $user_id);
}

/**
 * Display either author's link or author's name.
 *
 * If the author has a home page set, echo an HTML link, otherwise just echo the
 * author's name.
 *
 * @link http://codex.wordpress.org/Template_Tags/the_author_link
 * @since 2.1
 * @uses get_the_author_meta()
 * @uses the_author()
 */
function the_author_link() {
	if ( get_the_author_meta('url') ) {
		echo '<a href="' . get_the_author_meta('url') . '" title="' . esc_attr( sprintf(__("Visit %s&#8217;s website"), get_the_author()) ) . '" rel="external">' . get_the_author() . '</a>';
	} else {
		the_author();
	}
}

/**
 * Retrieve the number of posts by the author of the current post.
 *
 * @since 1.5
 * @uses $post The current post in the Loop's DB object.
 * @uses get_usernumposts()
 * @return int The number of posts by the author.
 */
function get_the_author_posts() {
	global $post;
	return get_usernumposts($post->post_author);
}

/**
 * Display the number of posts by the author of the current post.
 *
 * @link http://codex.wordpress.org/Template_Tags/the_author_posts
 * @since 0.71
 * @uses get_the_author_posts() Echos returned value from function.
 */
function the_author_posts() {
	echo get_the_author_posts();
}

/**
 * Display an HTML link to the author page of the author of the current post.
 *
 * Does just echo get_author_posts_url() function, like the others do. The
 * reason for this, is that another function is used to help in printing the
 * link to the author's posts.
 *
 * @link http://codex.wordpress.org/Template_Tags/the_author_posts_link
 * @since 1.2.0
 * @uses $authordata The current author's DB object.
 * @uses get_author_posts_url()
 * @uses get_the_author()
 * @param string $deprecated Deprecated.
 */
function the_author_posts_link($deprecated = '') {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '0.0' );

	global $authordata;
	$link = sprintf(
		'<a href="%1$s" title="%2$s">%3$s</a>',
		get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
		esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ),
		get_the_author()
	);
	echo apply_filters( 'the_author_posts_link', $link );
}

/**
 * Retrieve the URL to the author page of the author of the current post.
 *
 * @since 2.1.0
 * @uses $wp_rewrite WP_Rewrite
 * @return string The URL to the author's page.
 */
function get_author_posts_url($author_id, $author_nicename = '') {
	global $wp_rewrite;
	$auth_ID = (int) $author_id;
	$link = $wp_rewrite->get_author_permastruct();

	if ( empty($link) ) {
		$file = get_option('home') . '/';
		$link = $file . '?author=' . $auth_ID;
	} else {
		if ( '' == $author_nicename ) {
			$user = get_userdata($author_id);
			if ( !empty($user->user_nicename) )
				$author_nicename = $user->user_nicename;
		}
		$link = str_replace('%author%', $author_nicename, $link);
		$link = get_option('home') . trailingslashit($link);
	}

	$link = apply_filters('author_link', $link, $author_id, $author_nicename);

	return $link;
}

/**
 * List all the authors of the blog, with several options available.
 *
 * <ul>
 * <li>optioncount (boolean) (false): Show the count in parenthesis next to the
 * author's name.</li>
 * <li>exclude_admin (boolean) (true): Exclude the 'admin' user that is
 * installed bydefault.</li>
 * <li>show_fullname (boolean) (false): Show their full names.</li>
 * <li>hide_empty (boolean) (true): Don't show authors without any posts.</li>
 * <li>feed (string) (''): If isn't empty, show links to author's feeds.</li>
 * <li>feed_image (string) (''): If isn't empty, use this image to link to
 * feeds.</li>
 * <li>echo (boolean) (true): Set to false to return the output, instead of
 * echoing.</li>
 * <li>style (string) ('list'): Whether to display list of authors in list form
 * or as a string.</li>
 * <li>html (bool) (true): Whether to list the items in html for or plaintext.
 * </li>
 * </ul>
 *
 * @link http://codex.wordpress.org/Template_Tags/wp_list_authors
 * @since 1.2.0
 * @param array $args The argument array.
 * @return null|string The output, if echo is set to false.
 */
function wp_list_authors($args = '') {
	global $wpdb;

	$defaults = array(
		'optioncount' => false, 'exclude_admin' => true,
		'show_fullname' => false, 'hide_empty' => true,
		'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true,
		'style' => 'list', 'html' => true
	);

	$r = wp_parse_args( $args, $defaults );
	extract($r, EXTR_SKIP);
	$return = '';

	/** @todo Move select to get_authors(). */
	$authors = $wpdb->get_results("SELECT ID, user_nicename from $wpdb->users " . ($exclude_admin ? "WHERE user_login <> 'admin' " : '') . "ORDER BY display_name");

	$author_count = array();
	foreach ((array) $wpdb->get_results("SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE post_type = 'post' AND " . get_private_posts_cap_sql( 'post' ) . " GROUP BY post_author") as $row) {
		$author_count[$row->post_author] = $row->count;
	}

	foreach ( (array) $authors as $author ) {

		$link = '';

		$author = get_userdata( $author->ID );
		$posts = (isset($author_count[$author->ID])) ? $author_count[$author->ID] : 0;
		$name = $author->display_name;

		if ( $show_fullname && ($author->first_name != '' && $author->last_name != '') )
			$name = "$author->first_name $author->last_name";

		if( !$html ) {
			if ( $posts == 0 ) {
				if ( ! $hide_empty )
					$return .= $name . ', ';
			} else
				$return .= $name . ', ';

			// No need to go further to process HTML.
			continue;
		}

		if ( !($posts == 0 && $hide_empty) && 'list' == $style )
			$return .= '<li>';
		if ( $posts == 0 ) {
			if ( ! $hide_empty )
				$link = $name;
		} else {
			$link = '<a href="' . get_author_posts_url($author->ID, $author->user_nicename) . '" title="' . esc_attr( sprintf(__("Posts by %s"), $author->display_name) ) . '">' . $name . '</a>';

			if ( (! empty($feed_image)) || (! empty($feed)) ) {
				$link .= ' ';
				if (empty($feed_image))
					$link .= '(';
				$link .= '<a href="' . get_author_feed_link($author->ID) . '"';

				if ( !empty($feed) ) {
					$title = ' title="' . esc_attr($feed) . '"';
					$alt = ' alt="' . esc_attr($feed) . '"';
					$name = $feed;
					$link .= $title;
				}

				$link .= '>';

				if ( !empty($feed_image) )
					$link .= "<img src=\"" . esc_url($feed_image) . "\" style=\"border: none;\"$alt$title" . ' />';
				else
					$link .= $name;

				$link .= '</a>';

				if ( empty($feed_image) )
					$link .= ')';
			}

			if ( $optioncount )
				$link .= ' ('. $posts . ')';

		}

		if ( !($posts == 0 && $hide_empty) && 'list' == $style )
			$return .= $link . '</li>';
		else if ( ! $hide_empty )
			$return .= $link . ', ';
	}

	$return = trim($return, ', ');

	if ( ! $echo )
		return $return;
	echo $return;
}

?>

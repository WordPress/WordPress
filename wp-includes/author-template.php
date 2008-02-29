<?php
/**
 * Author Template functions for use in themes.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * get_the_author() - Get the author of the current post in the Loop.
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
	return apply_filters('the_author', $authordata->display_name);
}

/**
 * the_author() - Echo the name of the author of the current post in the Loop.
 *
 * The behavior of this function is based off of old functionality predating get_the_author().
 * This function is not deprecated, but is designed to echo the value from get_the_author()
 * and as an result of any old theme that might still use the old behavior will also
 * pass the value from get_the_author().
 *
 * The normal, expected behavior of this function is to echo the author and not return it.
 * However, backwards compatiability has to be maintained.
 *
 * @since 0.71
 * @see get_the_author()
 *
 * @param string $deprecated Deprecated.
 * @param string $deprecated_echo Echo the string or return it. Deprecated, use get_the_author().
 * @return string The author's display name, from get_the_author().
 */
function the_author($deprecated = '', $deprecated_echo = true) {
	if ( $deprecated_echo )
		echo get_the_author();
	return get_the_author();
}

/**
 * get_the_author_description() - Get the description of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The author's description.
 */
function get_the_author_description() {
	global $authordata;
	return $authordata->description;
}

/**
 * the_author_description() - Echo the description of the author of the current post in the Loop.
 *
 * @since 1.0.0
 * @see get_the_author_description()
 */
function the_author_description() {
	echo get_the_author_description();
}

/**
 * get_the_author_login() - Get the login name of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The author's login name (username).
 */
function get_the_author_login() {
	global $authordata;
	return $authordata->user_login;
}

/**
 * the_author_login() - Echo the login name of the author of the current post in the Loop.
 *
 * @since 0.71
 * @see get_the_author_login()
 */
function the_author_login() {
	echo get_the_author_login();
}

/**
 * get_the_author_firstname() - Get the first name of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The author's first name.
 */
function get_the_author_firstname() {
	global $authordata;
	return $authordata->first_name;
}

/**
 * the_author_firstname() - Echo the first name of the author of the current post in the Loop.
 *
 * @since 0.71
 * @uses get_the_author_firstname()
 */
function the_author_firstname() {
	echo get_the_author_firstname();
}

/**
 * get_the_author_lastname() - Get the last name of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The author's last name.
 */
function get_the_author_lastname() {
	global $authordata;
	return $authordata->last_name;
}

/**
 * the_author_lastname() - Echo the last name of the author of the current post in the Loop.
 *
 * @since 0.71
 * @uses get_the_author_lastname()
 */
function the_author_lastname() {
	echo get_the_author_lastname();
}

/**
 * get_the_author_nickname() - Get the nickname of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The author's nickname.
 */
function get_the_author_nickname() {
	global $authordata;
	return $authordata->nickname;
}

/**
 * the_author_nickname() - Echo the nickname of the author of the current post in the Loop.
 *
 * @since 0.71
 * @uses get_the_author_nickname()
 */
function the_author_nickname() {
	echo get_the_author_nickname();
}

/**
 * get_the_author_ID() - Get the ID of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return int The author's ID.
 */
function get_the_author_ID() {
	global $authordata;
	return (int) $authordata->ID;
}

/**
 * the_author_ID() - Echo the ID of the author of the current post in the Loop.
 *
 * @since 0.71
 * @uses get_the_author_ID()
 */
function the_author_ID() {
	echo get_the_author_id();
}

/**
 * get_the_author_email() - Get the email of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The author's username.
 */
function get_the_author_email() {
	global $authordata;
	return $authordata->user_email;
}

/**
 * the_author_email() - Echo the email of the author of the current post in the Loop.
 *
 * @since 0.71
 * @uses get_the_author_email()
 */
function the_author_email() {
	echo apply_filters('the_author_email', get_the_author_email() );
}

/**
 * get_the_author_url() - Get the URL to the home page of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The URL to the author's page.
 */
function get_the_author_url() {
	global $authordata;

	if ( 'http://' == $authordata->user_url )
		return '';

	return $authordata->user_url;
}

/**
 * the_author_url() - Echo the URL to the home page of the author of the current post in the Loop.
 *
 * @since 0.71
 * @uses get_the_author_url()
 */
function the_author_url() {
	echo get_the_author_url();
}

/**
 * the_author_link() - If the author has a home page set, echo an HTML link, otherwise just echo the author's name.
 *
 * @since 2.1
 * @uses get_the_author_url()
 * @uses the_author()
 */
function the_author_link() {
	if (get_the_author_url()) {
		echo '<a href="' . get_the_author_url() . '" title="' . sprintf(__("Visit %s's website"), get_the_author()) . '" rel="external">' . get_the_author() . '</a>';
	} else {
		the_author();
	}
}

/**
 * get_the_author_icq() - Get the ICQ number of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The author's ICQ number.
 */
function get_the_author_icq() {
	global $authordata;
	return $authordata->icq;
}

/**
 * the_author_icq() - Echo the ICQ number of the author of the current post in the Loop.
 *
 * @since 0.71
 * @see get_the_author_icq()
 */
function the_author_icq() {
	echo get_the_author_icq();
}

/**
 * get_the_author_aim() - Get the AIM name of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The author's AIM name.
 */
function get_the_author_aim() {
	global $authordata;
	return str_replace(' ', '+', $authordata->aim);
}

/**
 * the_author_aim() - Echo the AIM name of the author of the current post in the Loop.
 *
 * @since 0.71
 * @see get_the_author_aim()
 */
function the_author_aim() {
	echo get_the_author_aim();
}

/**
 * get_the_author_yim() - Get the Yahoo! IM name of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The author's Yahoo! IM name.
 */
function get_the_author_yim() {
	global $authordata;
	return $authordata->yim;
}

/**
 * the_author_yim() - Echo the Yahoo! IM name of the author of the current post in the Loop.
 *
 * @since 0.71
 * @see get_the_author_yim()
 */
function the_author_yim() {
	echo get_the_author_yim();
}

/**
 * get_the_author_msn() - Get the MSN address of the author of the current post in the Loop.
 *
 * @since 1.5
 * @uses $authordata The current author's DB object.
 * @return string The author's MSN address.
 */
function get_the_author_msn() {
	global $authordata;
	return $authordata->msn;
}

/**
 * the_author_msn() - Echo the MSN address of the author of the current post in the Loop.
 *
 * @since 0.71
 * @see get_the_author_msn()
 */
function the_author_msn() {
	echo get_the_author_msn();
}

/**
 * get_the_author_posts() - Get the number of posts by the author of the current post in the Loop.
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
 * the_author_posts() - Echo the number of posts by the author of the current post in the Loop.
 *
 * @since 0.71
 * @uses get_the_author_posts() Echos returned value from function.
 */
function the_author_posts() {
	echo get_the_author_posts();
}

/**
 * the_author_posts_link() - Echo an HTML link to the author page of the author of the current post in the Loop.
 *
 * Does just echo get_author_posts_url() function, like the others do. The reason for this,
 * is that another function is used to help in printing the link to the author's posts.
 *
 * @since 1.2
 * @uses $authordata The current author's DB object.
 * @uses get_author_posts_url()
 * @uses get_the_author()
 * @param string $deprecated Deprecated.
 */
function the_author_posts_link($deprecated = '') {
	global $authordata;
	printf(
		'<a href="%1$s" title="%2$s">%3$s</a>',
		get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
		sprintf( __( 'Posts by %s' ), attribute_escape( get_the_author() ) ),
		get_the_author()
	);
}

/**
 * get_author_posts_url() - Get the URL to the author page of the author of the current post in the Loop.
 *
 * @since 2.1
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
 * get_author_name() - Get the specified author's preferred display name.
 *
 * @since 1.0.0
 * @param int $auth_id The ID of the author.
 * @return string The author's display name.
 */
function get_author_name( $auth_id ) {
	$authordata = get_userdata( $auth_id );
	return $authordata->display_name;
}

/**
 * wp_list_authors() - List all the authors of the blog, with several options available.
 *
 * optioncount (boolean) (false): Show the count in parenthesis next to the author's name.
 * exclude_admin (boolean) (true): Exclude the 'admin' user that is installed by default.
 * show_fullname (boolean) (false): Show their full names.
 * hide_empty (boolean) (true): Don't show authors without any posts.
 * feed (string) (''): If isn't empty, show links to author's feeds.
 * feed_image (string) (''): If isn't empty, use this image to link to feeds.
 * echo (boolean) (true): Set to false to return the output, instead of echoing.
 *
 * @since 1.2
 * @param array $args The argument array.
 * @return null|string The output, if echo is set to false.
 */
function wp_list_authors($args = '') {
	global $wpdb;

	$defaults = array(
		'optioncount' => false, 'exclude_admin' => true,
		'show_fullname' => false, 'hide_empty' => true,
		'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true
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
		$author = get_userdata( $author->ID );
		$posts = (isset($author_count[$author->ID])) ? $author_count[$author->ID] : 0;
		$name = $author->display_name;

		if ( $show_fullname && ($author->first_name != '' && $author->last_name != '') )
			$name = "$author->first_name $author->last_name";

		if ( !($posts == 0 && $hide_empty) )
			$return .= '<li>';
		if ( $posts == 0 ) {
			if ( !$hide_empty )
				$link = $name;
		} else {
			$link = '<a href="' . get_author_posts_url($author->ID, $author->user_nicename) . '" title="' . sprintf(__("Posts by %s"), attribute_escape($author->display_name)) . '">' . $name . '</a>';

			if ( (! empty($feed_image)) || (! empty($feed)) ) {
				$link .= ' ';
				if (empty($feed_image))
					$link .= '(';
				$link .= '<a href="' . get_author_rss_link(0, $author->ID, $author->user_nicename) . '"';

				if ( !empty($feed) ) {
					$title = ' title="' . $feed . '"';
					$alt = ' alt="' . $feed . '"';
					$name = $feed;
					$link .= $title;
				}

				$link .= '>';

				if ( !empty($feed_image) )
					$link .= "<img src=\"$feed_image\" style=\"border: none;\"$alt$title" . ' />';
				else
					$link .= $name;

				$link .= '</a>';

				if ( empty($feed_image) )
					$link .= ')';
			}

			if ( $optioncount )
				$link .= ' ('. $posts . ')';

		}

		if ( !($posts == 0 && $hide_empty) )
			$return .= $link . '</li>';
	}
	if ( !$echo )
		return $return;
	echo $return;
}

?>
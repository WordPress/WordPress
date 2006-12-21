<?php

function get_the_author($deprecated = '') {
	global $authordata;
	return apply_filters('the_author', $authordata->display_name);
}

// Using echo = false is deprecated.  Use get_the_author instead.
function the_author($deprecated = '', $deprecated_echo = true) {
	if ( $deprecated_echo )
		echo get_the_author();
	return get_the_author();
}

function get_the_author_description() {
	global $authordata;
	return $authordata->description;
}
function the_author_description() {
	echo get_the_author_description();
}

function get_the_author_login() {
	global $authordata;
	return $authordata->user_login;
}

function the_author_login() {
	echo get_the_author_login();
}

function get_the_author_firstname() {
	global $authordata;
	return $authordata->first_name;
}
function the_author_firstname() {
	echo get_the_author_firstname();
}

function get_the_author_lastname() {
	global $authordata;
	return $authordata->last_name;
}

function the_author_lastname() {
	echo get_the_author_lastname();
}

function get_the_author_nickname() {
	global $authordata;
	return $authordata->nickname;
}

function the_author_nickname() {
	echo get_the_author_nickname();
}

function get_the_author_ID() {
	global $authordata;
	return $authordata->ID;
}
function the_author_ID() {
	echo get_the_author_id();
}

function get_the_author_email() {
	global $authordata;
	return $authordata->user_email;
}

function the_author_email() {
	echo apply_filters('the_author_email', get_the_author_email() );
}

function get_the_author_url() {
	global $authordata;
	return $authordata->user_url;
}

function the_author_url() {
	echo get_the_author_url();
}

function the_author_link() {
	if (get_the_author_url()) {
		echo '<a href="' . get_the_author_url() . '" title="' . sprintf(__("Visit %s's website"), get_the_author()) . '" rel="external">' . get_the_author() . '</a>';
	} else {
		the_author();
	}
}

function get_the_author_icq() {
	global $authordata;
	return $authordata->icq;
}

function the_author_icq() {
	echo get_the_author_icq();
}

function get_the_author_aim() {
	global $authordata;
	return str_replace(' ', '+', $authordata->aim);
}

function the_author_aim() {
	echo get_the_author_aim();
}

function get_the_author_yim() {
	global $authordata;
	return $authordata->yim;
}

function the_author_yim() {
	echo get_the_author_yim();
}

function get_the_author_msn() {
	global $authordata;
	return $authordata->msn;
}

function the_author_msn() {
	echo get_the_author_msn();
}

function get_the_author_posts() {
	global $post;
	$posts = get_usernumposts($post->post_author);
	return $posts;
}

function the_author_posts() {
	echo get_the_author_posts();
}

/* the_author_posts_link() requires no get_, use get_author_posts_url() */
function the_author_posts_link($deprecated = '') {
	global $authordata;

	echo '<a href="' . get_author_posts_url($authordata->ID, $authordata->user_nicename) . '" title="' . sprintf(__("Posts by %s"), attribute_escape(get_the_author())) . '">' . get_the_author() . '</a>';
}

function get_author_posts_url($author_id, $author_nicename = '') {
	global $wpdb, $wp_rewrite, $post, $cache_userdata;
	$auth_ID = $author_id;
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

// Get author's preferred display name
function get_author_name( $auth_id ) {
	$authordata = get_userdata( $auth_id );

	return $authordata->display_name;
}

function wp_list_authors($args = '') {
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);

	$defaults = array('optioncount' => false, 'exclude_admin' => true, 'show_fullname' => false, 'hide_empty' => true,
		'feed' => '', 'feed_image' => '');
	$r = array_merge($defaults, $r);
	extract($r);

	global $wpdb;
	// TODO:  Move select to get_authors().
	$query = "SELECT ID, user_nicename from $wpdb->users " . ($exclude_admin ? "WHERE user_login <> 'admin' " : '') . "ORDER BY display_name";
	$authors = $wpdb->get_results($query);

	foreach ( (array) $authors as $author ) {
		$author = get_userdata( $author->ID );
		$posts = get_usernumposts($author->ID);
		$name = $author->nickname;

		if ( $show_fullname && ($author->first_name != '' && $author->last_name != '') )
			$name = "$author->first_name $author->last_name";

		if ( !($posts == 0 && $hide_empty) )
			echo "<li>";
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
					$link .= "<img src=\"$feed_image\" border=\"0\"$alt$title" . ' />';
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
			echo "$link</li>";
	}
}

?>
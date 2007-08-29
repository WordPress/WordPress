<?php
// Based on "Permalink Redirect" from Scott Yang and "Enforce www. Preference" by Mark Jaquith

function redirect_canonical() {
	global $wp_rewrite, $posts;

	if ( is_feed() || is_trackback() || is_search() || is_comments_popup() || is_admin() )
		return;

	// build the URL in the address bar
	$requested_url  = ( isset($_SERVER['HTTPS'] ) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
	$requested_url .= $_SERVER['HTTP_HOST'];
	$requested_url .= $_SERVER['REQUEST_URI'];

	$original = @parse_url($requested_url);
	if ( false === $original )
		return;

	$redirect = $original;
	$redirect_url = false;

	// These tests give us a WP-generated permalink
	if ( is_404() ) {
		$redirect_url = redirect_guess_404_permalink();
	} elseif ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) {
		// rewriting of old ?p=X, ?m=2004, ?m=200401, ?m=20040101
		if ( is_single() && get_query_var('p') ) {
			if ( $redirect_url = get_permalink(get_query_var('p')) )
				$redirect['query'] = remove_query_arg('p', $redirect['query']);
		} elseif ( is_page() && get_query_var('page_id') ) {
			if ( $redirect_url = get_permalink(get_query_var('page_id')) )
				$redirect['query'] = remove_query_arg('page_id', $redirect['query']);
		} elseif ( get_query_var('m') && ( is_year() || is_month() || is_day() ) ) {
			$m = get_query_var('m');
			switch ( strlen($m) ) {
				case 4: // Yearly
					$redirect_url = get_year_link($m);
					break;
				case 6: // Monthly
					$redirect_url = get_month_link( substr($m, 0, 4), substr($m, 4, 2) );
					break;
				case 8: // Daily
					$redirect_url = get_day_link(substr($m, 0, 4), substr($m, 4, 2), substr($m, 6, 2));
					break;
			}
			if ( $redirect_url )
				$redirect['query'] = remove_query_arg('m', $redirect['query']);
		// now moving on to non ?m=X year/month/day links
		} elseif ( is_day() && get_query_var('year') && get_query_var('monthnum') && get_query_var('day') ) { 
			if ( $redirect_url = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day')) )
				$redirect['query'] = remove_query_arg(array('year', 'monthnum', 'day'), $redirect['query']);
		} elseif ( is_month() && get_query_var('year') && get_query_var('monthnum') ) {
			if ( $redirect_url = get_month_link(get_query_var('year'), get_query_var('monthnum')) )
				$redirect['query'] = remove_query_arg(array('year', 'monthnum'), $redirect['query']);
		} elseif ( is_year() && get_query_var('year') ) {
			if ( $redirect_url = get_year_link(get_query_var('year')) )
				$redirect['query'] = remove_query_arg('year', $redirect['query']);
		} elseif ( is_category() && get_query_var('cat') ) {
			if ( $redirect_url = get_category_link(get_query_var('cat')) )
				$redirect['query'] = remove_query_arg('cat', $redirect['query']);
		} elseif ( is_author() && get_query_var('author') ) {
			$author = get_userdata(get_query_var('author'));
			if ( false !== $author && $redirect_url = get_author_link(false, $author->ID, $author->user_nicename) )
				$redirect['query'] = remove_query_arg('author', $redirect['author']);
		}

	// paging
		if ( $paged = get_query_var('paged') ) {
			if ( $paged > 1 ) {
				if ( !$redirect_url )
					$redirect_url = $requested_url;
				$paged_redirect = @parse_url($redirect_url);
				$paged_redirect['path'] = preg_replace('|/index.php/?$|', '/', $paged_redirect['path']);
				$paged_redirect['path'] = trailingslashit($paged_redirect['path']);
				if ( $wp_rewrite->using_index_permalinks() && strpos($paged_redirect['path'], '/index.php/') === false )
					$paged_redirect['path'] .= 'index.php/';
				$paged_redirect['path'] .= user_trailingslashit("page/$paged");
				$redirect_url = $paged_redirect['scheme'] . '://' . $paged_redirect['host'] . $paged_redirect['path'];
			}
			$redirect['query'] = remove_query_arg('paged', $redirect['query']);
		}
	}

	// tack on any additional query vars
	if ( $redirect_url && $redirect['query'] ) {
		if ( strpos($redirect_url, '?') !== false )
			$redirect_url .= '&';
		else
			$redirect_url .= '?';
		$redirect_url .= $redirect['query'];
	}

	if ( !$redirect_url ) { // we're only going down this road if we don't have a WP-generated link from above

		// www.example.com vs example.com
		$user_home = parse_url(get_option('home'));
		$redirect['host'] = $user_home['host'];

		// trailing /index.php or /index.php/
		$redirect['path'] = preg_replace('|/index.php/?$|', '/', $redirect['path']);

		// need to strip off the query string
		if ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() && !is_404() && (!is_home() || ( is_home() && is_paged() ) ) )
			$redirect['path'] = user_trailingslashit($redirect['path']);

		if ( array($original['host'], $original['path'], $original['query']) !== array($redirect['host'], $redirect['path'], $redirect['query']) ) {
			$redirect_url = $redirect['scheme'] . '://' . $redirect['host'] . $redirect['path'];
			if ( $redirect['query'] )
				$redirect_url .= '?' . $redirect['query'];
		}
	}

	if ( $redirect_url && $redirect_url != $requested_url ) {
		// var_dump($redirect_url); die();
		$redirect_url = apply_filters('redirect_canonical', $redirect_url, $requested_url);
		wp_redirect($redirect_url, 301);
		exit();
	}
}

function redirect_guess_404_permalink() {
	global $wp_query, $wpdb;
	if ( !get_query_var('name') )
		return false;

	$where = "post_name LIKE '" . $wpdb->escape(get_query_var('name')) . "%'";

	// if any of year, monthnum, or day are set, use them to refine the query
	if ( get_query_var('year') )
		$where .= " AND YEAR(post_date) = '" . $wpdb->escape(get_query_var('year')) . "'";
	if ( get_query_var('monthnum') )
		$where .= " AND MONTH(post_date) = '" . $wpdb->escape(get_query_var('monthnum')) . "'";
	if ( get_query_var('day') )
		$where .= " AND DAYOFMONTH(post_date) = '" . $wpdb->escape(get_query_var('day')) . "'";

	$post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $where AND post_status = 'publish'");
	if ( !$post_id )
		return false;
	return get_permalink($post_id);
}

add_action('template_redirect', 'redirect_canonical');

?>
<?php
/**
 * Canonical API to handle WordPress Redirecting
 *
 * Based on "Permalink Redirect" from Scott Yang and "Enforce www. Preference"
 * by Mark Jaquith
 *
 * @author Scott Yang
 * @author Mark Jaquith
 * @package WordPress
 * @since 2.3
 */

/**
 * Redirects incoming links to the proper URL based on the site url
 *
 * Search engines consider www.somedomain.com and somedomain.com to be two
 * different URLs when they both go to the same location. This SEO enhancement
 * prevents penality for duplicate content by redirecting all incoming links to
 * one or the other.
 *
 * Prevents redirection for feeds, trackbacks, searches, comment popup, and
 * admin URLs. Does not redirect on IIS, page/post previews, and on form data.
 *
 * Will also attempt to find the correct link when a user enters a URL that does
 * not exist based on exact WordPress query. Will instead try to parse the URL
 * or query in an attempt to figure the correct page to go to.
 *
 * @since 2.3
 * @uses $wp_rewrite
 * @uses $is_IIS
 *
 * @param string $requested_url Optional. The URL that was requested, used to
 *		figure if redirect is needed.
 * @param bool $do_redirect Optional. Redirect to the new URL.
 * @return null|false|string Null, if redirect not needed. False, if redirect
 *		not needed or the string of the URL
 */
function redirect_canonical($requested_url=null, $do_redirect=true) {
	global $wp_rewrite, $is_IIS;

	if ( is_feed() || is_trackback() || is_search() || is_comments_popup() || is_admin() || $is_IIS || ( isset($_POST) && count($_POST) ) || is_preview() || is_robots() )
		return;

	if ( !$requested_url ) {
		// build the URL in the address bar
		$requested_url  = ( isset($_SERVER['HTTPS'] ) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://';
		$requested_url .= $_SERVER['HTTP_HOST'];
		$requested_url .= $_SERVER['REQUEST_URI'];
	}

	$original = @parse_url($requested_url);
	if ( false === $original )
		return;

	// Some PHP setups turn requests for / into /index.php in REQUEST_URI
	$original['path'] = preg_replace('|/index\.php$|', '/', $original['path']);

	$redirect = $original;
	$redirect_url = false;

	// These tests give us a WP-generated permalink
	if ( is_404() ) {
		$redirect_url = redirect_guess_404_permalink();
	} elseif ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) {
		// rewriting of old ?p=X, ?m=2004, ?m=200401, ?m=20040101
		if ( is_single() && isset($_GET['p']) ) {
			if ( $redirect_url = get_permalink(get_query_var('p')) )
				$redirect['query'] = remove_query_arg('p', $redirect['query']);
		} elseif ( is_page() && isset($_GET['page_id']) ) {
			if ( $redirect_url = get_permalink(get_query_var('page_id')) )
				$redirect['query'] = remove_query_arg('page_id', $redirect['query']);
		} elseif ( isset($_GET['m']) && ( is_year() || is_month() || is_day() ) ) {
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
		} elseif ( is_day() && get_query_var('year') && get_query_var('monthnum') && isset($_GET['day']) ) {
			if ( $redirect_url = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day')) )
				$redirect['query'] = remove_query_arg(array('year', 'monthnum', 'day'), $redirect['query']);
		} elseif ( is_month() && get_query_var('year') && isset($_GET['monthnum']) ) {
			if ( $redirect_url = get_month_link(get_query_var('year'), get_query_var('monthnum')) )
				$redirect['query'] = remove_query_arg(array('year', 'monthnum'), $redirect['query']);
		} elseif ( is_year() && isset($_GET['year']) ) {
			if ( $redirect_url = get_year_link(get_query_var('year')) )
				$redirect['query'] = remove_query_arg('year', $redirect['query']);
		} elseif ( is_category() && isset($_GET['cat']) ) {
			if ( $redirect_url = get_category_link(get_query_var('cat')) )
				$redirect['query'] = remove_query_arg('cat', $redirect['query']);
		} elseif ( is_author() && isset($_GET['author']) ) {
			$author = get_userdata(get_query_var('author'));
			if ( false !== $author && $redirect_url = get_author_link(false, $author->ID, $author->user_nicename) )
				$redirect['query'] = remove_query_arg('author', $redirect['author']);
		}

	// paging
		if ( $paged = get_query_var('paged') ) {
			if ( $paged > 0 ) {
				if ( !$redirect_url )
					$redirect_url = $requested_url;
				$paged_redirect = @parse_url($redirect_url);
				$paged_redirect['path'] = preg_replace('|/page/[0-9]+?(/+)?$|', '/', $paged_redirect['path']); // strip off any existing paging
				$paged_redirect['path'] = preg_replace('|/index.php/?$|', '/', $paged_redirect['path']); // strip off trailing /index.php/
				if ( $paged > 1 && !is_single() ) {
					$paged_redirect['path'] = trailingslashit($paged_redirect['path']);
					if ( $wp_rewrite->using_index_permalinks() && strpos($paged_redirect['path'], '/index.php/') === false )
						$paged_redirect['path'] .= 'index.php/';
					$paged_redirect['path'] .= user_trailingslashit("page/$paged", 'paged');
				} elseif ( !is_home() && !is_single() ){
					$paged_redirect['path'] = user_trailingslashit($paged_redirect['path'], 'paged');
				}
				$redirect_url = $paged_redirect['scheme'] . '://' . $paged_redirect['host'] . $paged_redirect['path'];
				$redirect['path'] = $paged_redirect['path'];
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

	if ( $redirect_url )
		$redirect = @parse_url($redirect_url);

	// www.example.com vs example.com
	$user_home = @parse_url(get_option('home'));
	if ( isset($user_home['host']) )
		$redirect['host'] = $user_home['host'];

	// Handle ports
	if ( isset($user_home['port']) )
		$redirect['port'] = $user_home['port'];
	else
		unset($redirect['port']);

	// trailing /index.php/
	$redirect['path'] = preg_replace('|/index.php/$|', '/', $redirect['path']);

	// strip /index.php/ when we're not using PATHINFO permalinks
	if ( !$wp_rewrite->using_index_permalinks() )
		$redirect['path'] = str_replace('/index.php/', '/', $redirect['path']);

	// trailing slashes
	if ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() && !is_404() && (!is_home() || ( is_home() && (get_query_var('paged') > 1) ) ) ) {
		$user_ts_type = '';
		if ( get_query_var('paged') > 0 ) {
			$user_ts_type = 'paged';
		} else {
			foreach ( array('single', 'category', 'page', 'day', 'month', 'year') as $type ) {
				$func = 'is_' . $type;
				if ( call_user_func($func) ) {
					$user_ts_type = $type;
					break;
				}
			}
		}
		$redirect['path'] = user_trailingslashit($redirect['path'], $user_ts_type);
	} elseif ( is_home() ) {
		$redirect['path'] = trailingslashit($redirect['path']);
	}

	// Always trailing slash the 'home' URL
	if ( $redirect['path'] == $user_home['path'] )
		$redirect['path'] = trailingslashit($redirect['path']);

	// Ignore differences in host capitalization, as this can lead to infinite redirects
	if ( strtolower($original['host']) == strtolower($redirect['host']) )
		$redirect['host'] = $original['host'];

	if ( array($original['host'], $original['port'], $original['path'], $original['query']) !== array($redirect['host'], $redirect['port'], $redirect['path'], $redirect['query']) ) {
		$redirect_url = $redirect['scheme'] . '://' . $redirect['host'];
		if ( isset($redirect['port']) )
		 	$redirect_url .= ':' . $redirect['port'];
		$redirect_url .= $redirect['path'];
		if ( $redirect['query'] )
			$redirect_url .= '?' . $redirect['query'];
	}

	if ( !$redirect_url || $redirect_url == $requested_url )
	 	return false;

	// Note that you can use the "redirect_canonical" filter to cancel a canonical redirect for whatever reason by returning FALSE
	$redirect_url = apply_filters('redirect_canonical', $redirect_url, $requested_url);

	if ( !$redirect_url || $redirect_url == $requested_url ) // yes, again -- in case the filter aborted the request
	 	return false;

	if ( $do_redirect ) {
		// protect against chained redirects
		if ( !redirect_canonical($redirect_url, false) ) {
			wp_redirect($redirect_url, 301);
			exit();
		} else {
			return false;
		}
	} else {
		return $redirect_url;
	}
}

/**
 * Attempts to guess correct post based on query vars
 *
 * @since 2.3
 * @uses $wpdb
 *
 * @return bool|string Returns False, if it can't find post, returns correct
 *		location on success.
 */
function redirect_guess_404_permalink() {
	global $wpdb;
	if ( !get_query_var('name') )
		return false;

	$where = $wpdb->prepare("post_name LIKE %s", get_query_var('name') . '%');

	// if any of year, monthnum, or day are set, use them to refine the query
	if ( get_query_var('year') )
		$where .= $wpdb->prepare(" AND YEAR(post_date) = %d", get_query_var('year'));
	if ( get_query_var('monthnum') )
		$where .= $wpdb->prepare(" AND MONTH(post_date) = %d", get_query_var('monthnum'));
	if ( get_query_var('day') )
		$where .= $wpdb->prepare(" AND DAYOFMONTH(post_date) = %d", get_query_var('day'));

	$post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE $where AND post_status = 'publish'");
	if ( !$post_id )
		return false;
	return get_permalink($post_id);
}

add_action('template_redirect', 'redirect_canonical');

?>

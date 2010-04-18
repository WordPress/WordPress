<?php
/**
 * Canonical API to handle WordPress Redirecting
 *
 * Based on "Permalink Redirect" from Scott Yang and "Enforce www. Preference"
 * by Mark Jaquith
 *
 * @package WordPress
 * @since 2.3.0
 */

/**
 * Redirects incoming links to the proper URL based on the site url.
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
 * @since 2.3.0
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
	global $wp_rewrite, $is_IIS, $wp_query, $wpdb;

	if ( is_trackback() || is_search() || is_comments_popup() || is_admin() || $is_IIS || ( isset($_POST) && count($_POST) ) || is_preview() || is_robots() )
		return;

	if ( !$requested_url ) {
		// build the URL in the address bar
		$requested_url  = is_ssl() ? 'https://' : 'http://';
		$requested_url .= $_SERVER['HTTP_HOST'];
		$requested_url .= $_SERVER['REQUEST_URI'];
	}

	$original = @parse_url($requested_url);
	if ( false === $original )
		return;

	// Some PHP setups turn requests for / into /index.php in REQUEST_URI
	// See: http://trac.wordpress.org/ticket/5017
	// See: http://trac.wordpress.org/ticket/7173
	// Disabled, for now:
	// $original['path'] = preg_replace('|/index\.php$|', '/', $original['path']);

	$redirect = $original;
	$redirect_url = false;

	// Notice fixing
	if ( !isset($redirect['path']) )
		$redirect['path'] = '';
	if ( !isset($redirect['query']) )
		$redirect['query'] = '';

	if ( is_singular() && 1 > $wp_query->post_count && ($id = get_query_var('p')) ) {

		$vars = $wpdb->get_results( $wpdb->prepare("SELECT post_type, post_parent FROM $wpdb->posts WHERE ID = %d", $id) );

		if ( isset($vars[0]) && $vars = $vars[0] ) {
			if ( 'revision' == $vars->post_type && $vars->post_parent > 0 )
				$id = $vars->post_parent;

			if ( $redirect_url = get_permalink($id) )
				$redirect['query'] = remove_query_arg(array('p', 'page_id', 'attachment_id'), $redirect['query']);
		}
	}

	// These tests give us a WP-generated permalink
	if ( is_404() ) {
		$redirect_url = redirect_guess_404_permalink();
	} elseif ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) {
		// rewriting of old ?p=X, ?m=2004, ?m=200401, ?m=20040101
		if ( is_attachment() && !empty($_GET['attachment_id']) && ! $redirect_url ) {
			if ( $redirect_url = get_attachment_link(get_query_var('attachment_id')) )
				$redirect['query'] = remove_query_arg('attachment_id', $redirect['query']);
		} elseif ( is_single() && !empty($_GET['p']) && ! $redirect_url ) {
			if ( $redirect_url = get_permalink(get_query_var('p')) )
				$redirect['query'] = remove_query_arg('p', $redirect['query']);
			if ( get_query_var( 'page' ) ) {
				$redirect_url = trailingslashit( $redirect_url ) . user_trailingslashit( get_query_var( 'page' ), 'single_paged' );
				$redirect['query'] = remove_query_arg( 'page', $redirect['query'] );
			}
		} elseif ( is_single() && !empty($_GET['name'])  && ! $redirect_url ) {
			if ( $redirect_url = get_permalink( $wp_query->get_queried_object_id() ) )
				$redirect['query'] = remove_query_arg('name', $redirect['query']);
		} elseif ( is_page() && !empty($_GET['page_id']) && ! $redirect_url ) {
			if ( $redirect_url = get_permalink(get_query_var('page_id')) )
				$redirect['query'] = remove_query_arg('page_id', $redirect['query']);
		} elseif ( is_page() && !is_feed() && isset($wp_query->queried_object) && 'page' == get_option('show_on_front') && $wp_query->queried_object->ID == get_option('page_on_front')  && ! $redirect_url ) {
			$redirect_url = home_url('/');
		} elseif ( is_home() && !empty($_GET['page_id']) && 'page' == get_option('show_on_front') && get_query_var('page_id') == get_option('page_for_posts')  && ! $redirect_url ) {
			if ( $redirect_url = get_permalink(get_option('page_for_posts')) )
				$redirect['query'] = remove_query_arg('page_id', $redirect['query']);
		} elseif ( !empty($_GET['m']) && ( is_year() || is_month() || is_day() ) ) {
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
		} elseif ( is_day() && get_query_var('year') && get_query_var('monthnum') && !empty($_GET['day']) ) {
			if ( $redirect_url = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day')) )
				$redirect['query'] = remove_query_arg(array('year', 'monthnum', 'day'), $redirect['query']);
		} elseif ( is_month() && get_query_var('year') && !empty($_GET['monthnum']) ) {
			if ( $redirect_url = get_month_link(get_query_var('year'), get_query_var('monthnum')) )
				$redirect['query'] = remove_query_arg(array('year', 'monthnum'), $redirect['query']);
		} elseif ( is_year() && !empty($_GET['year']) ) {
			if ( $redirect_url = get_year_link(get_query_var('year')) )
				$redirect['query'] = remove_query_arg('year', $redirect['query']);
		} elseif ( is_author() && !empty($_GET['author']) && preg_match( '|^[0-9]+$|', $_GET['author'] ) ) {
			$author = get_userdata(get_query_var('author'));
			if ( false !== $author && $redirect_url = get_author_posts_url($author->ID, $author->user_nicename) )
				$redirect['query'] = remove_query_arg('author', $redirect['query']);
		} elseif ( is_category() || is_tag() || is_tax() ) { // Terms (Tags/categories)

			$term_count = 0;
			foreach ( array('category__in', 'category__not_in', 'category__and', 'post__in', 'post__not_in',
			'tag__in', 'tag__not_in', 'tag__and', 'tag_slug__in', 'tag_slug__and') as $key )
				$term_count += count($wp_query->query_vars[$key]);

			$obj = $wp_query->get_queried_object();

			if ( $term_count <= 1 && !empty($obj->term_id) && ( $tax_url = get_term_link((int)$obj->term_id, $obj->taxonomy) ) && !is_wp_error($tax_url) ) {

				if ( is_category() ) {
					$redirect['query'] = remove_query_arg( array( 'category_name', 'category', 'cat'), $redirect['query']);
				} elseif ( is_tag() ) {
					$redirect['query'] = remove_query_arg( array( 'tag', 'tag_id'), $redirect['query']);
				} elseif ( is_tax() ) { // Custom taxonomies will have a custom query var, remove those too:
					$tax = get_taxonomy( $obj->taxonomy );
					if ( false !== $tax->query_var)
						$redirect['query'] = remove_query_arg($tax->query_var, $redirect['query']);
					else
						$redirect['query'] = remove_query_arg( array( 'term', 'taxonomy'), $redirect['query']);
				}

				$tax_url = parse_url($tax_url);
				if ( ! empty($tax_url['query']) ) { // Custom taxonomies may only be accessable via ?taxonomy=..&term=..
					parse_str($tax_url['query'], $query_vars);
					$redirect['query'] = add_query_arg($query_vars, $redirect['query']);
				} else { // Taxonomy is accessable via a "pretty-URL"
					$redirect['path'] = $tax_url['path'];
				}

			}
		} elseif ( is_single() && strpos($wp_rewrite->permalink_structure, '%category%') !== false ) {
			$category = get_term_by('slug', get_query_var('category_name'), 'category');
			$post_terms = wp_get_object_terms($wp_query->get_queried_object_id(), 'category');
			if ( (!$category || is_wp_error($category)) || ( !is_wp_error($post_terms) && !empty($post_terms) && !in_array($category, $post_terms) ) )
				$redirect_url = get_permalink($wp_query->get_queried_object_id());
		}

		// paging and feeds
		if ( get_query_var('paged') || is_feed() || get_query_var('cpage') ) {
			if ( !$redirect_url )
				$redirect_url = $requested_url;
			$paged_redirect = @parse_url($redirect_url);
			while ( preg_match( '#/page/?[0-9]+?(/+)?$#', $paged_redirect['path'] ) || preg_match( '#/(comments/?)?(feed|rss|rdf|atom|rss2)(/+)?$#', $paged_redirect['path'] ) || preg_match( '#/comment-page-[0-9]+(/+)?$#', $paged_redirect['path'] ) ) {
				// Strip off paging and feed
				$paged_redirect['path'] = preg_replace('#/page/?[0-9]+?(/+)?$#', '/', $paged_redirect['path']); // strip off any existing paging
				$paged_redirect['path'] = preg_replace('#/(comments/?)?(feed|rss2?|rdf|atom)(/+|$)#', '/', $paged_redirect['path']); // strip off feed endings
				$paged_redirect['path'] = preg_replace('#/comment-page-[0-9]+?(/+)?$#', '/', $paged_redirect['path']); // strip off any existing comment paging
			}

			$addl_path = '';
			if ( is_feed() ) {
				$addl_path = !empty( $addl_path ) ? trailingslashit($addl_path) : '';
				if ( get_query_var( 'withcomments' ) )
					$addl_path .= 'comments/';
				$addl_path .= user_trailingslashit( 'feed/' . ( ( 'rss2' ==  get_query_var('feed') || 'feed' == get_query_var('feed') ) ? '' : get_query_var('feed') ), 'feed' );
				$redirect['query'] = remove_query_arg( 'feed', $redirect['query'] );
			}

			if ( get_query_var('paged') > 0 ) {
				$paged = get_query_var('paged');
				$redirect['query'] = remove_query_arg( 'paged', $redirect['query'] );
				if ( !is_feed() ) {
					if ( $paged > 1 && !is_single() ) {
						$addl_path = ( !empty( $addl_path ) ? trailingslashit($addl_path) : '' ) . user_trailingslashit("page/$paged", 'paged');
					} elseif ( !is_single() ) {
						$addl_path = !empty( $addl_path ) ? trailingslashit($addl_path) : '';
					}
				} elseif ( $paged > 1 ) {
					$redirect['query'] = add_query_arg( 'paged', $paged, $redirect['query'] );
				}
			}

			if ( get_option('page_comments') && ( ( 'newest' == get_option('default_comments_page') && get_query_var('cpage') > 0 ) || ( 'newest' != get_option('default_comments_page') && get_query_var('cpage') > 1 ) ) ) {
				$addl_path = ( !empty( $addl_path ) ? trailingslashit($addl_path) : '' ) . user_trailingslashit( 'comment-page-' . get_query_var('cpage'), 'commentpaged' );
				$redirect['query'] = remove_query_arg( 'cpage', $redirect['query'] );
			}

			$paged_redirect['path'] = user_trailingslashit( preg_replace('|/index.php/?$|', '/', $paged_redirect['path']) ); // strip off trailing /index.php/
			if ( !empty( $addl_path ) && $wp_rewrite->using_index_permalinks() && strpos($paged_redirect['path'], '/index.php/') === false )
				$paged_redirect['path'] = trailingslashit($paged_redirect['path']) . 'index.php/';
			if ( !empty( $addl_path ) )
				$paged_redirect['path'] = trailingslashit($paged_redirect['path']) . $addl_path;
			$redirect_url = $paged_redirect['scheme'] . '://' . $paged_redirect['host'] . $paged_redirect['path'];
			$redirect['path'] = $paged_redirect['path'];
		}
	}

	// tack on any additional query vars
	$redirect['query'] = preg_replace( '#^\??&*?#', '', $redirect['query'] );
	if ( $redirect_url && !empty($redirect['query']) ) {
		if ( strpos($redirect_url, '?') !== false )
			$redirect_url .= '&';
		else
			$redirect_url .= '?';
		$redirect_url .= $redirect['query'];
	}

	if ( $redirect_url )
		$redirect = @parse_url($redirect_url);

	// www.example.com vs example.com
	$user_home = @parse_url(home_url());
	if ( !empty($user_home['host']) )
		$redirect['host'] = $user_home['host'];
	if ( empty($user_home['path']) )
		$user_home['path'] = '/';

	// Handle ports
	if ( !empty($user_home['port']) )
		$redirect['port'] = $user_home['port'];
	else
		unset($redirect['port']);

	// trailing /index.php
	$redirect['path'] = preg_replace('|/index.php/*?$|', '/', $redirect['path']);

	// Remove trailing spaces from the path
	$redirect['path'] = preg_replace( '#(%20| )+$#', '', $redirect['path'] );

	if ( !empty( $redirect['query'] ) ) {
		// Remove trailing spaces from certain terminating query string args
		$redirect['query'] = preg_replace( '#((p|page_id|cat|tag)=[^&]*?)(%20| )+$#', '$1', $redirect['query'] );

		// Clean up empty query strings
		$redirect['query'] = trim(preg_replace( '#(^|&)(p|page_id|cat|tag)=?(&|$)#', '&', $redirect['query']), '&');

		// Remove redundant leading ampersands
		$redirect['query'] = preg_replace( '#^\??&*?#', '', $redirect['query'] );
	}

	// strip /index.php/ when we're not using PATHINFO permalinks
	if ( !$wp_rewrite->using_index_permalinks() )
		$redirect['path'] = str_replace('/index.php/', '/', $redirect['path']);

	// trailing slashes
	if ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() && !is_404() && (!is_front_page() || ( is_front_page() && (get_query_var('paged') > 1) ) ) ) {
		$user_ts_type = '';
		if ( get_query_var('paged') > 0 ) {
			$user_ts_type = 'paged';
		} else {
			foreach ( array('single', 'category', 'page', 'day', 'month', 'year', 'home') as $type ) {
				$func = 'is_' . $type;
				if ( call_user_func($func) ) {
					$user_ts_type = $type;
					break;
				}
			}
		}
		$redirect['path'] = user_trailingslashit($redirect['path'], $user_ts_type);
	} elseif ( is_front_page() ) {
		$redirect['path'] = trailingslashit($redirect['path']);
	}

	// Strip multiple slashes out of the URL
	if ( strpos($redirect['path'], '//') > -1 )
		$redirect['path'] = preg_replace('|/+|', '/', $redirect['path']);

	// Always trailing slash the Front Page URL
	if ( trailingslashit( $redirect['path'] ) == trailingslashit( $user_home['path'] ) )
		$redirect['path'] = trailingslashit($redirect['path']);

	// Ignore differences in host capitalization, as this can lead to infinite redirects
	// Only redirect no-www <=> yes-www
	if ( strtolower($original['host']) == strtolower($redirect['host']) ||
		( strtolower($original['host']) != 'www.' . strtolower($redirect['host']) && 'www.' . strtolower($original['host']) != strtolower($redirect['host']) ) )
		$redirect['host'] = $original['host'];

	$compare_original = array($original['host'], $original['path']);

	if ( !empty( $original['port'] ) )
		$compare_original[] = $original['port'];

	if ( !empty( $original['query'] ) )
		$compare_original[] = $original['query'];

	$compare_redirect = array($redirect['host'], $redirect['path']);

	if ( !empty( $redirect['port'] ) )
		$compare_redirect[] = $redirect['port'];

	if ( !empty( $redirect['query'] ) )
		$compare_redirect[] = $redirect['query'];

	if ( $compare_original !== $compare_redirect ) {
		$redirect_url = $redirect['scheme'] . '://' . $redirect['host'];
		if ( !empty($redirect['port']) )
			$redirect_url .= ':' . $redirect['port'];
		$redirect_url .= $redirect['path'];
		if ( !empty($redirect['query']) )
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
			// Debug
			// die("1: $redirect_url<br />2: " . redirect_canonical( $redirect_url, false ) );
			return false;
		}
	} else {
		return $redirect_url;
	}
}

/**
 * Attempts to guess correct post based on query vars.
 *
 * @since 2.3.0
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

	// if any of post_type, year, monthnum, or day are set, use them to refine the query
	if ( get_query_var('post_type') )
		$where .= $wpdb->prepare(" AND post_type = %s", get_query_var('post_type'));
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

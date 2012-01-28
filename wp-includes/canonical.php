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
 * prevents penalty for duplicate content by redirecting all incoming links to
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
function redirect_canonical( $requested_url = null, $do_redirect = true ) {
	global $wp_rewrite, $is_IIS, $wp_query, $wpdb;

	if ( is_trackback() || is_search() || is_comments_popup() || is_admin() || !empty($_POST) || is_preview() || is_robots() || $is_IIS )
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
				$redirect['query'] = remove_query_arg(array('p', 'page_id', 'attachment_id', 'post_type'), $redirect['query']);
		}
	}

	// These tests give us a WP-generated permalink
	if ( is_404() ) {

		// Redirect ?page_id, ?p=, ?attachment_id= to their respective url's
		$id = max( get_query_var('p'), get_query_var('page_id'), get_query_var('attachment_id') );
		if ( $id && $redirect_post = get_post($id) ) {
			$post_type_obj = get_post_type_object($redirect_post->post_type);
			if ( $post_type_obj->public ) {
				$redirect_url = get_permalink($redirect_post);
				$redirect['query'] = remove_query_arg(array('p', 'page_id', 'attachment_id', 'post_type'), $redirect['query']);
			}
		}

		if ( ! $redirect_url )
			$redirect_url = redirect_guess_404_permalink( $requested_url );

	} elseif ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) {
		// rewriting of old ?p=X, ?m=2004, ?m=200401, ?m=20040101
		if ( is_attachment() && !empty($_GET['attachment_id']) && ! $redirect_url ) {
			if ( $redirect_url = get_attachment_link(get_query_var('attachment_id')) )
				$redirect['query'] = remove_query_arg('attachment_id', $redirect['query']);
		} elseif ( is_single() && !empty($_GET['p']) && ! $redirect_url ) {
			if ( $redirect_url = get_permalink(get_query_var('p')) )
				$redirect['query'] = remove_query_arg(array('p', 'post_type'), $redirect['query']);
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
			if ( ( false !== $author ) && $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_author = %d AND $wpdb->posts.post_status = 'publish' LIMIT 1", $author->ID ) ) ) {
				if ( $redirect_url = get_author_posts_url($author->ID, $author->user_nicename) )
					$redirect['query'] = remove_query_arg('author', $redirect['query']);
			}
		} elseif ( is_category() || is_tag() || is_tax() ) { // Terms (Tags/categories)

			$term_count = 0;
			foreach ( $wp_query->tax_query->queries as $tax_query )
				$term_count += count( $tax_query['terms'] );

			$obj = $wp_query->get_queried_object();
			if ( $term_count <= 1 && !empty($obj->term_id) && ( $tax_url = get_term_link((int)$obj->term_id, $obj->taxonomy) ) && !is_wp_error($tax_url) ) {
				if ( !empty($redirect['query']) ) {
					// Strip taxonomy query vars off the url.
					$qv_remove = array( 'term', 'taxonomy');
					if ( is_category() ) {
						$qv_remove[] = 'category_name';
						$qv_remove[] = 'cat';
					} elseif ( is_tag() ) {
						$qv_remove[] = 'tag';
						$qv_remove[] = 'tag_id';
					} else { // Custom taxonomies will have a custom query var, remove those too:
						$tax_obj = get_taxonomy( $obj->taxonomy );
						if ( false !== $tax_obj->query_var )
							$qv_remove[] = $tax_obj->query_var;
					}

					$rewrite_vars = array_diff( array_keys($wp_query->query), array_keys($_GET) );

					if ( !array_diff($rewrite_vars, array_keys($_GET))  ) { // Check to see if all the Query vars are coming from the rewrite, none are set via $_GET
						$redirect['query'] = remove_query_arg($qv_remove, $redirect['query']); //Remove all of the per-tax qv's

						// Create the destination url for this taxonomy
						$tax_url = parse_url($tax_url);
						if ( ! empty($tax_url['query']) ) { // Taxonomy accessible via ?taxonomy=..&term=.. or any custom qv..
							parse_str($tax_url['query'], $query_vars);
							$redirect['query'] = add_query_arg($query_vars, $redirect['query']);
						} else { // Taxonomy is accessible via a "pretty-URL"
							$redirect['path'] = $tax_url['path'];
						}

					} else { // Some query vars are set via $_GET. Unset those from $_GET that exist via the rewrite
						foreach ( $qv_remove as $_qv ) {
							if ( isset($rewrite_vars[$_qv]) )
								$redirect['query'] = remove_query_arg($_qv, $redirect['query']);
						}
					}
				}

			}
		} elseif ( is_single() && strpos($wp_rewrite->permalink_structure, '%category%') !== false ) {
			$category = get_category_by_path(get_query_var('category_name'));
			$post_terms = wp_get_object_terms($wp_query->get_queried_object_id(), 'category', array('fields' => 'tt_ids'));
			if ( (!$category || is_wp_error($category)) || ( !is_wp_error($post_terms) && !empty($post_terms) && !in_array($category->term_taxonomy_id, $post_terms) ) )
				$redirect_url = get_permalink($wp_query->get_queried_object_id());
		}

		// Post Paging
		if ( is_singular() && get_query_var('page') && $redirect_url ) {
			$redirect_url = trailingslashit( $redirect_url ) . user_trailingslashit( get_query_var( 'page' ), 'single_paged' );
			$redirect['query'] = remove_query_arg( 'page', $redirect['query'] );
		}

		// paging and feeds
		if ( get_query_var('paged') || is_feed() || get_query_var('cpage') ) {
			while ( preg_match( "#/$wp_rewrite->pagination_base/?[0-9]+?(/+)?$#", $redirect['path'] ) || preg_match( '#/(comments/?)?(feed|rss|rdf|atom|rss2)(/+)?$#', $redirect['path'] ) || preg_match( '#/comment-page-[0-9]+(/+)?$#', $redirect['path'] ) ) {
				// Strip off paging and feed
				$redirect['path'] = preg_replace("#/$wp_rewrite->pagination_base/?[0-9]+?(/+)?$#", '/', $redirect['path']); // strip off any existing paging
				$redirect['path'] = preg_replace('#/(comments/?)?(feed|rss2?|rdf|atom)(/+|$)#', '/', $redirect['path']); // strip off feed endings
				$redirect['path'] = preg_replace('#/comment-page-[0-9]+?(/+)?$#', '/', $redirect['path']); // strip off any existing comment paging
			}

			$addl_path = '';
			if ( is_feed() && in_array( get_query_var('feed'), $wp_rewrite->feeds ) ) {
				$addl_path = !empty( $addl_path ) ? trailingslashit($addl_path) : '';
				if ( get_query_var( 'withcomments' ) )
					$addl_path .= 'comments/';
				if ( ( 'rss' == get_default_feed() && 'feed' == get_query_var('feed') ) || 'rss' == get_query_var('feed') )
					$addl_path .= user_trailingslashit( 'feed/' . ( ( get_default_feed() == 'rss2' ) ? '' : 'rss2' ), 'feed' );
				else
					$addl_path .= user_trailingslashit( 'feed/' . ( ( get_default_feed() ==  get_query_var('feed') || 'feed' == get_query_var('feed') ) ? '' : get_query_var('feed') ), 'feed' );
				$redirect['query'] = remove_query_arg( 'feed', $redirect['query'] );
			} elseif ( is_feed() && 'old' == get_query_var('feed') ) {
				$old_feed_files = array(
					'wp-atom.php'         => 'atom',
					'wp-commentsrss2.php' => 'comments_rss2',
					'wp-feed.php'         => get_default_feed(),
					'wp-rdf.php'          => 'rdf',
					'wp-rss.php'          => 'rss2',
					'wp-rss2.php'         => 'rss2',
				);
				if ( isset( $old_feed_files[ basename( $redirect['path'] ) ] ) ) {
					$redirect_url = get_feed_link( $old_feed_files[ basename( $redirect['path'] ) ] );
					wp_redirect( $redirect_url, 301 );
					die();
				}
			}

			if ( get_query_var('paged') > 0 ) {
				$paged = get_query_var('paged');
				$redirect['query'] = remove_query_arg( 'paged', $redirect['query'] );
				if ( !is_feed() ) {
					if ( $paged > 1 && !is_single() ) {
						$addl_path = ( !empty( $addl_path ) ? trailingslashit($addl_path) : '' ) . user_trailingslashit("$wp_rewrite->pagination_base/$paged", 'paged');
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

			$redirect['path'] = user_trailingslashit( preg_replace('|/index.php/?$|', '/', $redirect['path']) ); // strip off trailing /index.php/
			if ( !empty( $addl_path ) && $wp_rewrite->using_index_permalinks() && strpos($redirect['path'], '/index.php/') === false )
				$redirect['path'] = trailingslashit($redirect['path']) . 'index.php/';
			if ( !empty( $addl_path ) )
				$redirect['path'] = trailingslashit($redirect['path']) . $addl_path;
			$redirect_url = $redirect['scheme'] . '://' . $redirect['host'] . $redirect['path'];
		}
	}

	// tack on any additional query vars
	$redirect['query'] = preg_replace( '#^\??&*?#', '', $redirect['query'] );
	if ( $redirect_url && !empty($redirect['query']) ) {
		parse_str( $redirect['query'], $_parsed_query );
		$redirect = @parse_url($redirect_url);

		if ( ! empty( $_parsed_query['name'] ) && ! empty( $redirect['query'] ) ) {
			parse_str( $redirect['query'], $_parsed_redirect_query );

			if ( empty( $_parsed_redirect_query['name'] ) )
				unset( $_parsed_query['name'] );
		}

		$_parsed_query = array_map( 'rawurlencode', $_parsed_query );
		$redirect_url = add_query_arg( $_parsed_query, $redirect_url );
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

		// Redirect obsolete feeds
		$redirect['query'] = preg_replace( '#(^|&)feed=rss(&|$)#', '$1feed=rss2$3', $redirect['query'] );

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

	// Hex encoded octets are case-insensitive.
	if ( false !== strpos($requested_url, '%') ) {
		if ( !function_exists('lowercase_octets') ) {
			function lowercase_octets($matches) {
				return strtolower( $matches[0] );
			}
		}
		$requested_url = preg_replace_callback('|%[a-fA-F0-9][a-fA-F0-9]|', 'lowercase_octets', $requested_url);
	}

	// Note that you can use the "redirect_canonical" filter to cancel a canonical redirect for whatever reason by returning false
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
 * Attempts to guess the correct URL from the current URL (that produced a 404) or
 * the current query variables.
 *
 * @since 2.3.0
 * @uses $wpdb
 *
 * @param string $current_url Optional. The URL that has 404'd.
 * @return bool|string The correct URL if one is found. False on failure.
 */
function redirect_guess_404_permalink( $current_url = '' ) {
	global $wpdb, $wp_rewrite;

	if ( ! empty( $current_url ) )
		$parsed_url = @parse_url( $current_url );

	// Attempt to redirect bare category slugs if the permalink structure starts
	// with the %category% tag.
	if ( isset( $parsed_url['path'] )
		&& preg_match( '#^[^%]+%category%#', $wp_rewrite->permalink_structure )
		&& $cat = get_category_by_path( $parsed_url['path'] )
	) {
		if ( ! is_wp_error( $cat ) )
			return get_term_link( $cat );
	}

	if ( get_query_var('name') ) {
		$where = $wpdb->prepare("post_name LIKE %s", like_escape( get_query_var('name') ) . '%');

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
		if ( ! $post_id )
			return false;
		return get_permalink( $post_id );
	}

	return false;
}

add_action('template_redirect', 'redirect_canonical');

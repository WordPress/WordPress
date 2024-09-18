<?php
/**
 * WordPress Rewrite API
 *
 * @package WordPress
 * @subpackage Rewrite
 */

/**
 * Endpoint mask that matches nothing.
 *
 * @since 2.1.0
 */
define( 'EP_NONE', 0 );

/**
 * Endpoint mask that matches post permalinks.
 *
 * @since 2.1.0
 */
define( 'EP_PERMALINK', 1 );

/**
 * Endpoint mask that matches attachment permalinks.
 *
 * @since 2.1.0
 */
define( 'EP_ATTACHMENT', 2 );

/**
 * Endpoint mask that matches any date archives.
 *
 * @since 2.1.0
 */
define( 'EP_DATE', 4 );

/**
 * Endpoint mask that matches yearly archives.
 *
 * @since 2.1.0
 */
define( 'EP_YEAR', 8 );

/**
 * Endpoint mask that matches monthly archives.
 *
 * @since 2.1.0
 */
define( 'EP_MONTH', 16 );

/**
 * Endpoint mask that matches daily archives.
 *
 * @since 2.1.0
 */
define( 'EP_DAY', 32 );

/**
 * Endpoint mask that matches the site root.
 *
 * @since 2.1.0
 */
define( 'EP_ROOT', 64 );

/**
 * Endpoint mask that matches comment feeds.
 *
 * @since 2.1.0
 */
define( 'EP_COMMENTS', 128 );

/**
 * Endpoint mask that matches searches.
 *
 * Note that this only matches a search at a "pretty" URL such as
 * `/search/my-search-term`, not `?s=my-search-term`.
 *
 * @since 2.1.0
 */
define( 'EP_SEARCH', 256 );

/**
 * Endpoint mask that matches category archives.
 *
 * @since 2.1.0
 */
define( 'EP_CATEGORIES', 512 );

/**
 * Endpoint mask that matches tag archives.
 *
 * @since 2.3.0
 */
define( 'EP_TAGS', 1024 );

/**
 * Endpoint mask that matches author archives.
 *
 * @since 2.1.0
 */
define( 'EP_AUTHORS', 2048 );

/**
 * Endpoint mask that matches pages.
 *
 * @since 2.1.0
 */
define( 'EP_PAGES', 4096 );

/**
 * Endpoint mask that matches all archive views.
 *
 * @since 3.7.0
 */
define( 'EP_ALL_ARCHIVES', EP_DATE | EP_YEAR | EP_MONTH | EP_DAY | EP_CATEGORIES | EP_TAGS | EP_AUTHORS );

/**
 * Endpoint mask that matches everything.
 *
 * @since 2.1.0
 */
define( 'EP_ALL', EP_PERMALINK | EP_ATTACHMENT | EP_ROOT | EP_COMMENTS | EP_SEARCH | EP_PAGES | EP_ALL_ARCHIVES );

/**
 * Adds a rewrite rule that transforms a URL structure to a set of query vars.
 *
 * Any value in the $after parameter that isn't 'bottom' will result in the rule
 * being placed at the top of the rewrite rules.
 *
 * @since 2.1.0
 * @since 4.4.0 Array support was added to the `$query` parameter.
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param string       $regex Regular expression to match request against.
 * @param string|array $query The corresponding query vars for this rewrite rule.
 * @param string       $after Optional. Priority of the new rule. Accepts 'top'
 *                            or 'bottom'. Default 'bottom'.
 */
function add_rewrite_rule( $regex, $query, $after = 'bottom' ) {
	global $wp_rewrite;

	$wp_rewrite->add_rule( $regex, $query, $after );
}

/**
 * Adds a new rewrite tag (like %postname%).
 *
 * The `$query` parameter is optional. If it is omitted you must ensure that you call
 * this on, or before, the {@see 'init'} hook. This is because `$query` defaults to
 * `$tag=`, and for this to work a new query var has to be added.
 *
 * @since 2.1.0
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 * @global WP         $wp         Current WordPress environment instance.
 *
 * @param string $tag   Name of the new rewrite tag.
 * @param string $regex Regular expression to substitute the tag for in rewrite rules.
 * @param string $query Optional. String to append to the rewritten query. Must end in '='. Default empty.
 */
function add_rewrite_tag( $tag, $regex, $query = '' ) {
	// Validate the tag's name.
	if ( strlen( $tag ) < 3 || '%' !== $tag[0] || '%' !== $tag[ strlen( $tag ) - 1 ] ) {
		return;
	}

	global $wp_rewrite, $wp;

	if ( empty( $query ) ) {
		$qv = trim( $tag, '%' );
		$wp->add_query_var( $qv );
		$query = $qv . '=';
	}

	$wp_rewrite->add_rewrite_tag( $tag, $regex, $query );
}

/**
 * Removes an existing rewrite tag (like %postname%).
 *
 * @since 4.5.0
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param string $tag Name of the rewrite tag.
 */
function remove_rewrite_tag( $tag ) {
	global $wp_rewrite;
	$wp_rewrite->remove_rewrite_tag( $tag );
}

/**
 * Adds a permalink structure.
 *
 * @since 3.0.0
 *
 * @see WP_Rewrite::add_permastruct()
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param string $name   Name for permalink structure.
 * @param string $struct Permalink structure.
 * @param array  $args   Optional. Arguments for building the rules from the permalink structure,
 *                       see WP_Rewrite::add_permastruct() for full details. Default empty array.
 */
function add_permastruct( $name, $struct, $args = array() ) {
	global $wp_rewrite;

	// Back-compat for the old parameters: $with_front and $ep_mask.
	if ( ! is_array( $args ) ) {
		$args = array( 'with_front' => $args );
	}

	if ( func_num_args() === 4 ) {
		$args['ep_mask'] = func_get_arg( 3 );
	}

	$wp_rewrite->add_permastruct( $name, $struct, $args );
}

/**
 * Removes a permalink structure.
 *
 * Can only be used to remove permastructs that were added using add_permastruct().
 * Built-in permastructs cannot be removed.
 *
 * @since 4.5.0
 *
 * @see WP_Rewrite::remove_permastruct()
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param string $name Name for permalink structure.
 */
function remove_permastruct( $name ) {
	global $wp_rewrite;

	$wp_rewrite->remove_permastruct( $name );
}

/**
 * Adds a new feed type like /atom1/.
 *
 * @since 2.1.0
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param string   $feedname Feed name. Should not start with '_'.
 * @param callable $callback Callback to run on feed display.
 * @return string Feed action name.
 */
function add_feed( $feedname, $callback ) {
	global $wp_rewrite;

	if ( ! in_array( $feedname, $wp_rewrite->feeds, true ) ) {
		$wp_rewrite->feeds[] = $feedname;
	}

	$hook = 'do_feed_' . $feedname;

	// Remove default function hook.
	remove_action( $hook, $hook );

	add_action( $hook, $callback, 10, 2 );

	return $hook;
}

/**
 * Removes rewrite rules and then recreate rewrite rules.
 *
 * @since 3.0.0
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param bool $hard Whether to update .htaccess (hard flush) or just update
 *                   rewrite_rules option (soft flush). Default is true (hard).
 */
function flush_rewrite_rules( $hard = true ) {
	global $wp_rewrite;

	if ( is_callable( array( $wp_rewrite, 'flush_rules' ) ) ) {
		$wp_rewrite->flush_rules( $hard );
	}
}

/**
 * Adds an endpoint, like /trackback/.
 *
 * Adding an endpoint creates extra rewrite rules for each of the matching
 * places specified by the provided bitmask. For example:
 *
 *     add_rewrite_endpoint( 'json', EP_PERMALINK | EP_PAGES );
 *
 * will add a new rewrite rule ending with "json(/(.*))?/?$" for every permastruct
 * that describes a permalink (post) or page. This is rewritten to "json=$match"
 * where $match is the part of the URL matched by the endpoint regex (e.g. "foo" in
 * "[permalink]/json/foo/").
 *
 * A new query var with the same name as the endpoint will also be created.
 *
 * When specifying $places ensure that you are using the EP_* constants (or a
 * combination of them using the bitwise OR operator) as their values are not
 * guaranteed to remain static (especially `EP_ALL`).
 *
 * Be sure to flush the rewrite rules - see flush_rewrite_rules() - when your plugin gets
 * activated and deactivated.
 *
 * @since 2.1.0
 * @since 4.3.0 Added support for skipping query var registration by passing `false` to `$query_var`.
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 *
 * @param string      $name      Name of the endpoint.
 * @param int         $places    Endpoint mask describing the places the endpoint should be added.
 *                               Accepts a mask of:
 *                               - `EP_ALL`
 *                               - `EP_NONE`
 *                               - `EP_ALL_ARCHIVES`
 *                               - `EP_ATTACHMENT`
 *                               - `EP_AUTHORS`
 *                               - `EP_CATEGORIES`
 *                               - `EP_COMMENTS`
 *                               - `EP_DATE`
 *                               - `EP_DAY`
 *                               - `EP_MONTH`
 *                               - `EP_PAGES`
 *                               - `EP_PERMALINK`
 *                               - `EP_ROOT`
 *                               - `EP_SEARCH`
 *                               - `EP_TAGS`
 *                               - `EP_YEAR`
 * @param string|bool $query_var Name of the corresponding query variable. Pass `false` to skip registering a query_var
 *                               for this endpoint. Defaults to the value of `$name`.
 */
function add_rewrite_endpoint( $name, $places, $query_var = true ) {
	global $wp_rewrite;
	$wp_rewrite->add_endpoint( $name, $places, $query_var );
}

/**
 * Filters the URL base for taxonomies.
 *
 * To remove any manually prepended /index.php/.
 *
 * @access private
 * @since 2.6.0
 *
 * @param string $base The taxonomy base that we're going to filter
 * @return string
 */
function _wp_filter_taxonomy_base( $base ) {
	if ( ! empty( $base ) ) {
		$base = preg_replace( '|^/index\.php/|', '', $base );
		$base = trim( $base, '/' );
	}
	return $base;
}


/**
 * Resolves numeric slugs that collide with date permalinks.
 *
 * Permalinks of posts with numeric slugs can sometimes look to WP_Query::parse_query()
 * like a date archive, as when your permalink structure is `/%year%/%postname%/` and
 * a post with post_name '05' has the URL `/2015/05/`.
 *
 * This function detects conflicts of this type and resolves them in favor of the
 * post permalink.
 *
 * Note that, since 4.3.0, wp_unique_post_slug() prevents the creation of post slugs
 * that would result in a date archive conflict. The resolution performed in this
 * function is primarily for legacy content, as well as cases when the admin has changed
 * the site's permalink structure in a way that introduces URL conflicts.
 *
 * @since 4.3.0
 *
 * @param array $query_vars Optional. Query variables for setting up the loop, as determined in
 *                          WP::parse_request(). Default empty array.
 * @return array Returns the original array of query vars, with date/post conflicts resolved.
 */
function wp_resolve_numeric_slug_conflicts( $query_vars = array() ) {
	if ( ! isset( $query_vars['year'] ) && ! isset( $query_vars['monthnum'] ) && ! isset( $query_vars['day'] ) ) {
		return $query_vars;
	}

	// Identify the 'postname' position in the permastruct array.
	$permastructs   = array_values( array_filter( explode( '/', get_option( 'permalink_structure' ) ) ) );
	$postname_index = array_search( '%postname%', $permastructs, true );

	if ( false === $postname_index ) {
		return $query_vars;
	}

	/*
	 * A numeric slug could be confused with a year, month, or day, depending on position. To account for
	 * the possibility of post pagination (eg 2015/2 for the second page of a post called '2015'), our
	 * `is_*` checks are generous: check for year-slug clashes when `is_year` *or* `is_month`, and check
	 * for month-slug clashes when `is_month` *or* `is_day`.
	 */
	$compare = '';
	if ( 0 === $postname_index && ( isset( $query_vars['year'] ) || isset( $query_vars['monthnum'] ) ) ) {
		$compare = 'year';
	} elseif ( $postname_index && '%year%' === $permastructs[ $postname_index - 1 ] && ( isset( $query_vars['monthnum'] ) || isset( $query_vars['day'] ) ) ) {
		$compare = 'monthnum';
	} elseif ( $postname_index && '%monthnum%' === $permastructs[ $postname_index - 1 ] && isset( $query_vars['day'] ) ) {
		$compare = 'day';
	}

	if ( ! $compare ) {
		return $query_vars;
	}

	// This is the potentially clashing slug.
	$value = '';
	if ( $compare && array_key_exists( $compare, $query_vars ) ) {
		$value = $query_vars[ $compare ];
	}

	$post = get_page_by_path( $value, OBJECT, 'post' );
	if ( ! ( $post instanceof WP_Post ) ) {
		return $query_vars;
	}

	// If the date of the post doesn't match the date specified in the URL, resolve to the date archive.
	if ( preg_match( '/^([0-9]{4})\-([0-9]{2})/', $post->post_date, $matches ) && isset( $query_vars['year'] ) && ( 'monthnum' === $compare || 'day' === $compare ) ) {
		// $matches[1] is the year the post was published.
		if ( (int) $query_vars['year'] !== (int) $matches[1] ) {
			return $query_vars;
		}

		// $matches[2] is the month the post was published.
		if ( 'day' === $compare && isset( $query_vars['monthnum'] ) && (int) $query_vars['monthnum'] !== (int) $matches[2] ) {
			return $query_vars;
		}
	}

	/*
	 * If the located post contains nextpage pagination, then the URL chunk following postname may be
	 * intended as the page number. Verify that it's a valid page before resolving to it.
	 */
	$maybe_page = '';
	if ( 'year' === $compare && isset( $query_vars['monthnum'] ) ) {
		$maybe_page = $query_vars['monthnum'];
	} elseif ( 'monthnum' === $compare && isset( $query_vars['day'] ) ) {
		$maybe_page = $query_vars['day'];
	}
	// Bug found in #11694 - 'page' was returning '/4'.
	$maybe_page = (int) trim( $maybe_page, '/' );

	$post_page_count = substr_count( $post->post_content, '<!--nextpage-->' ) + 1;

	// If the post doesn't have multiple pages, but a 'page' candidate is found, resolve to the date archive.
	if ( 1 === $post_page_count && $maybe_page ) {
		return $query_vars;
	}

	// If the post has multiple pages and the 'page' number isn't valid, resolve to the date archive.
	if ( $post_page_count > 1 && $maybe_page > $post_page_count ) {
		return $query_vars;
	}

	// If we've gotten to this point, we have a slug/date clash. First, adjust for nextpage.
	if ( '' !== $maybe_page ) {
		$query_vars['page'] = (int) $maybe_page;
	}

	// Next, unset autodetected date-related query vars.
	unset( $query_vars['year'] );
	unset( $query_vars['monthnum'] );
	unset( $query_vars['day'] );

	// Then, set the identified post.
	$query_vars['name'] = $post->post_name;

	// Finally, return the modified query vars.
	return $query_vars;
}

/**
 * Examines a URL and try to determine the post ID it represents.
 *
 * Checks are supposedly from the hosted site blog.
 *
 * @since 1.0.0
 *
 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
 * @global WP         $wp         Current WordPress environment instance.
 *
 * @param string $url Permalink to check.
 * @return int Post ID, or 0 on failure.
 */
function url_to_postid( $url ) {
	global $wp_rewrite;

	/**
	 * Filters the URL to derive the post ID from.
	 *
	 * @since 2.2.0
	 *
	 * @param string $url The URL to derive the post ID from.
	 */
	$url = apply_filters( 'url_to_postid', $url );

	$url_host = parse_url( $url, PHP_URL_HOST );

	if ( is_string( $url_host ) ) {
		$url_host = str_replace( 'www.', '', $url_host );
	} else {
		$url_host = '';
	}

	$home_url_host = parse_url( home_url(), PHP_URL_HOST );

	if ( is_string( $home_url_host ) ) {
		$home_url_host = str_replace( 'www.', '', $home_url_host );
	} else {
		$home_url_host = '';
	}

	// Bail early if the URL does not belong to this site.
	if ( $url_host && $url_host !== $home_url_host ) {
		return 0;
	}

	// First, check to see if there is a 'p=N' or 'page_id=N' to match against.
	if ( preg_match( '#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values ) ) {
		$id = absint( $values[2] );
		if ( $id ) {
			return $id;
		}
	}

	// Get rid of the #anchor.
	$url_split = explode( '#', $url );
	$url       = $url_split[0];

	// Get rid of URL ?query=string.
	$url_split = explode( '?', $url );
	$url       = $url_split[0];

	// Set the correct URL scheme.
	$scheme = parse_url( home_url(), PHP_URL_SCHEME );
	$url    = set_url_scheme( $url, $scheme );

	// Add 'www.' if it is absent and should be there.
	if ( str_contains( home_url(), '://www.' ) && ! str_contains( $url, '://www.' ) ) {
		$url = str_replace( '://', '://www.', $url );
	}

	// Strip 'www.' if it is present and shouldn't be.
	if ( ! str_contains( home_url(), '://www.' ) ) {
		$url = str_replace( '://www.', '://', $url );
	}

	if ( trim( $url, '/' ) === home_url() && 'page' === get_option( 'show_on_front' ) ) {
		$page_on_front = get_option( 'page_on_front' );

		if ( $page_on_front && get_post( $page_on_front ) instanceof WP_Post ) {
			return (int) $page_on_front;
		}
	}

	// Check to see if we are using rewrite rules.
	$rewrite = $wp_rewrite->wp_rewrite_rules();

	// Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options.
	if ( empty( $rewrite ) ) {
		return 0;
	}

	// Strip 'index.php/' if we're not using path info permalinks.
	if ( ! $wp_rewrite->using_index_permalinks() ) {
		$url = str_replace( $wp_rewrite->index . '/', '', $url );
	}

	if ( str_contains( trailingslashit( $url ), home_url( '/' ) ) ) {
		// Chop off http://domain.com/[path].
		$url = str_replace( home_url(), '', $url );
	} else {
		// Chop off /path/to/blog.
		$home_path = parse_url( home_url( '/' ) );
		$home_path = isset( $home_path['path'] ) ? $home_path['path'] : '';
		$url       = preg_replace( sprintf( '#^%s#', preg_quote( $home_path ) ), '', trailingslashit( $url ) );
	}

	// Trim leading and lagging slashes.
	$url = trim( $url, '/' );

	$request              = $url;
	$post_type_query_vars = array();

	foreach ( get_post_types( array(), 'objects' ) as $post_type => $t ) {
		if ( ! empty( $t->query_var ) ) {
			$post_type_query_vars[ $t->query_var ] = $post_type;
		}
	}

	// Look for matches.
	$request_match = $request;
	foreach ( (array) $rewrite as $match => $query ) {

		/*
		 * If the requesting file is the anchor of the match,
		 * prepend it to the path info.
		 */
		if ( ! empty( $url ) && ( $url !== $request ) && str_starts_with( $match, $url ) ) {
			$request_match = $url . '/' . $request;
		}

		if ( preg_match( "#^$match#", $request_match, $matches ) ) {

			if ( $wp_rewrite->use_verbose_page_rules && preg_match( '/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch ) ) {
				// This is a verbose page match, let's check to be sure about it.
				$page = get_page_by_path( $matches[ $varmatch[1] ] );
				if ( ! $page ) {
					continue;
				}

				$post_status_obj = get_post_status_object( $page->post_status );
				if ( ! $post_status_obj->public && ! $post_status_obj->protected
					&& ! $post_status_obj->private && $post_status_obj->exclude_from_search ) {
					continue;
				}
			}

			/*
			 * Got a match.
			 * Trim the query of everything up to the '?'.
			 */
			$query = preg_replace( '!^.+\?!', '', $query );

			// Substitute the substring matches into the query.
			$query = addslashes( WP_MatchesMapRegex::apply( $query, $matches ) );

			// Filter out non-public query vars.
			global $wp;
			parse_str( $query, $query_vars );
			$query = array();
			foreach ( (array) $query_vars as $key => $value ) {
				if ( in_array( (string) $key, $wp->public_query_vars, true ) ) {
					$query[ $key ] = $value;
					if ( isset( $post_type_query_vars[ $key ] ) ) {
						$query['post_type'] = $post_type_query_vars[ $key ];
						$query['name']      = $value;
					}
				}
			}

			// Resolve conflicts between posts with numeric slugs and date archive queries.
			$query = wp_resolve_numeric_slug_conflicts( $query );

			// Do the query.
			$query = new WP_Query( $query );
			if ( ! empty( $query->posts ) && $query->is_singular ) {
				return $query->post->ID;
			} else {
				return 0;
			}
		}
	}
	return 0;
}

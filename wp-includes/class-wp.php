<?php
/**
 * WordPress environment setup class.
 *
 * @package WordPress
 * @since 2.0.0
 */
class WP {
	/**
	 * Public query variables.
	 *
	 * Long list of public query variables.
	 *
	 * @since 2.0.0
	 * @var string[]
	 */
	public $public_query_vars = array( 'm', 'p', 'posts', 'w', 'cat', 'withcomments', 'withoutcomments', 's', 'search', 'exact', 'sentence', 'calendar', 'page', 'paged', 'more', 'tb', 'pb', 'author', 'order', 'orderby', 'year', 'monthnum', 'day', 'hour', 'minute', 'second', 'name', 'category_name', 'tag', 'feed', 'author_name', 'pagename', 'page_id', 'error', 'attachment', 'attachment_id', 'subpost', 'subpost_id', 'preview', 'robots', 'favicon', 'taxonomy', 'term', 'cpage', 'post_type', 'embed' );

	/**
	 * Private query variables.
	 *
	 * Long list of private query variables.
	 *
	 * @since 2.0.0
	 * @var string[]
	 */
	public $private_query_vars = array( 'offset', 'posts_per_page', 'posts_per_archive_page', 'showposts', 'nopaging', 'post_type', 'post_status', 'category__in', 'category__not_in', 'category__and', 'tag__in', 'tag__not_in', 'tag__and', 'tag_slug__in', 'tag_slug__and', 'tag_id', 'post_mime_type', 'perm', 'comments_per_page', 'post__in', 'post__not_in', 'post_parent', 'post_parent__in', 'post_parent__not_in', 'title', 'fields' );

	/**
	 * Extra query variables set by the user.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	public $extra_query_vars = array();

	/**
	 * Query variables for setting up the WordPress Query Loop.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $query_vars;

	/**
	 * String parsed to set the query variables.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $query_string;

	/**
	 * The request path, e.g. 2015/05/06.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $request;

	/**
	 * Rewrite rule the request matched.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $matched_rule;

	/**
	 * Rewrite query the request matched.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $matched_query;

	/**
	 * Whether already did the permalink.
	 *
	 * @since 2.0.0
	 * @var bool
	 */
	public $did_permalink = false;

	/**
	 * Adds a query variable to the list of public query variables.
	 *
	 * @since 2.1.0
	 *
	 * @param string $qv Query variable name.
	 */
	public function add_query_var( $qv ) {
		if ( ! in_array( $qv, $this->public_query_vars, true ) ) {
			$this->public_query_vars[] = $qv;
		}
	}

	/**
	 * Removes a query variable from a list of public query variables.
	 *
	 * @since 4.5.0
	 *
	 * @param string $name Query variable name.
	 */
	public function remove_query_var( $name ) {
		$this->public_query_vars = array_diff( $this->public_query_vars, array( $name ) );
	}

	/**
	 * Sets the value of a query variable.
	 *
	 * @since 2.3.0
	 *
	 * @param string $key   Query variable name.
	 * @param mixed  $value Query variable value.
	 */
	public function set_query_var( $key, $value ) {
		$this->query_vars[ $key ] = $value;
	}

	/**
	 * Parses the request to find the correct WordPress query.
	 *
	 * Sets up the query variables based on the request. There are also many
	 * filters and actions that can be used to further manipulate the result.
	 *
	 * @since 2.0.0
	 *
	 * @global WP_Rewrite $wp_rewrite WordPress rewrite component.
	 *
	 * @param array|string $extra_query_vars Set the extra query variables.
	 */
	public function parse_request( $extra_query_vars = '' ) {
		global $wp_rewrite;

		/**
		 * Filters whether to parse the request.
		 *
		 * @since 3.5.0
		 *
		 * @param bool         $bool             Whether or not to parse the request. Default true.
		 * @param WP           $this             Current WordPress environment instance.
		 * @param array|string $extra_query_vars Extra passed query variables.
		 */
		if ( ! apply_filters( 'do_parse_request', true, $this, $extra_query_vars ) ) {
			return;
		}

		$this->query_vars     = array();
		$post_type_query_vars = array();

		if ( is_array( $extra_query_vars ) ) {
			$this->extra_query_vars = & $extra_query_vars;
		} elseif ( ! empty( $extra_query_vars ) ) {
			parse_str( $extra_query_vars, $this->extra_query_vars );
		}
		// Process PATH_INFO, REQUEST_URI, and 404 for permalinks.

		// Fetch the rewrite rules.
		$rewrite = $wp_rewrite->wp_rewrite_rules();

		if ( ! empty( $rewrite ) ) {
			// If we match a rewrite rule, this will be cleared.
			$error               = '404';
			$this->did_permalink = true;

			$pathinfo         = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '';
			list( $pathinfo ) = explode( '?', $pathinfo );
			$pathinfo         = str_replace( '%', '%25', $pathinfo );

			list( $req_uri ) = explode( '?', $_SERVER['REQUEST_URI'] );
			$self            = $_SERVER['PHP_SELF'];
			$home_path       = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
			$home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

			/*
			 * Trim path info from the end and the leading home path from the front.
			 * For path info requests, this leaves us with the requesting filename, if any.
			 * For 404 requests, this leaves us with the requested permalink.
			 */
			$req_uri  = str_replace( $pathinfo, '', $req_uri );
			$req_uri  = trim( $req_uri, '/' );
			$req_uri  = preg_replace( $home_path_regex, '', $req_uri );
			$req_uri  = trim( $req_uri, '/' );
			$pathinfo = trim( $pathinfo, '/' );
			$pathinfo = preg_replace( $home_path_regex, '', $pathinfo );
			$pathinfo = trim( $pathinfo, '/' );
			$self     = trim( $self, '/' );
			$self     = preg_replace( $home_path_regex, '', $self );
			$self     = trim( $self, '/' );

			// The requested permalink is in $pathinfo for path info requests and
			// $req_uri for other requests.
			if ( ! empty( $pathinfo ) && ! preg_match( '|^.*' . $wp_rewrite->index . '$|', $pathinfo ) ) {
				$requested_path = $pathinfo;
			} else {
				// If the request uri is the index, blank it out so that we don't try to match it against a rule.
				if ( $req_uri == $wp_rewrite->index ) {
					$req_uri = '';
				}
				$requested_path = $req_uri;
			}
			$requested_file = $req_uri;

			$this->request = $requested_path;

			// Look for matches.
			$request_match = $requested_path;
			if ( empty( $request_match ) ) {
				// An empty request could only match against ^$ regex.
				if ( isset( $rewrite['$'] ) ) {
					$this->matched_rule = '$';
					$query              = $rewrite['$'];
					$matches            = array( '' );
				}
			} else {
				foreach ( (array) $rewrite as $match => $query ) {
					// If the requested file is the anchor of the match, prepend it to the path info.
					if ( ! empty( $requested_file ) && strpos( $match, $requested_file ) === 0 && $requested_file != $requested_path ) {
						$request_match = $requested_file . '/' . $requested_path;
					}

					if ( preg_match( "#^$match#", $request_match, $matches ) ||
						preg_match( "#^$match#", urldecode( $request_match ), $matches ) ) {

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

						// Got a match.
						$this->matched_rule = $match;
						break;
					}
				}
			}

			if ( isset( $this->matched_rule ) ) {
				// Trim the query of everything up to the '?'.
				$query = preg_replace( '!^.+\?!', '', $query );

				// Substitute the substring matches into the query.
				$query = addslashes( WP_MatchesMapRegex::apply( $query, $matches ) );

				$this->matched_query = $query;

				// Parse the query.
				parse_str( $query, $perma_query_vars );

				// If we're processing a 404 request, clear the error var since we found something.
				if ( '404' == $error ) {
					unset( $error, $_GET['error'] );
				}
			}

			// If req_uri is empty or if it is a request for ourself, unset error.
			if ( empty( $requested_path ) || $requested_file == $self || strpos( $_SERVER['PHP_SELF'], 'wp-admin/' ) !== false ) {
				unset( $error, $_GET['error'] );

				if ( isset( $perma_query_vars ) && strpos( $_SERVER['PHP_SELF'], 'wp-admin/' ) !== false ) {
					unset( $perma_query_vars );
				}

				$this->did_permalink = false;
			}
		}

		/**
		 * Filters the query variables allowed before processing.
		 *
		 * Allows (publicly allowed) query vars to be added, removed, or changed prior
		 * to executing the query. Needed to allow custom rewrite rules using your own arguments
		 * to work, or any other custom query variables you want to be publicly available.
		 *
		 * @since 1.5.0
		 *
		 * @param string[] $public_query_vars The array of allowed query variable names.
		 */
		$this->public_query_vars = apply_filters( 'query_vars', $this->public_query_vars );

		foreach ( get_post_types( array(), 'objects' ) as $post_type => $t ) {
			if ( is_post_type_viewable( $t ) && $t->query_var ) {
				$post_type_query_vars[ $t->query_var ] = $post_type;
			}
		}

		foreach ( $this->public_query_vars as $wpvar ) {
			if ( isset( $this->extra_query_vars[ $wpvar ] ) ) {
				$this->query_vars[ $wpvar ] = $this->extra_query_vars[ $wpvar ];
			} elseif ( isset( $_GET[ $wpvar ] ) && isset( $_POST[ $wpvar ] ) && $_GET[ $wpvar ] !== $_POST[ $wpvar ] ) {
				wp_die( __( 'A variable mismatch has been detected.' ), __( 'Sorry, you are not allowed to view this item.' ), 400 );
			} elseif ( isset( $_POST[ $wpvar ] ) ) {
				$this->query_vars[ $wpvar ] = $_POST[ $wpvar ];
			} elseif ( isset( $_GET[ $wpvar ] ) ) {
				$this->query_vars[ $wpvar ] = $_GET[ $wpvar ];
			} elseif ( isset( $perma_query_vars[ $wpvar ] ) ) {
				$this->query_vars[ $wpvar ] = $perma_query_vars[ $wpvar ];
			}

			if ( ! empty( $this->query_vars[ $wpvar ] ) ) {
				if ( ! is_array( $this->query_vars[ $wpvar ] ) ) {
					$this->query_vars[ $wpvar ] = (string) $this->query_vars[ $wpvar ];
				} else {
					foreach ( $this->query_vars[ $wpvar ] as $vkey => $v ) {
						if ( is_scalar( $v ) ) {
							$this->query_vars[ $wpvar ][ $vkey ] = (string) $v;
						}
					}
				}

				if ( isset( $post_type_query_vars[ $wpvar ] ) ) {
					$this->query_vars['post_type'] = $post_type_query_vars[ $wpvar ];
					$this->query_vars['name']      = $this->query_vars[ $wpvar ];
				}
			}
		}

		// Convert urldecoded spaces back into '+'.
		foreach ( get_taxonomies( array(), 'objects' ) as $taxonomy => $t ) {
			if ( $t->query_var && isset( $this->query_vars[ $t->query_var ] ) ) {
				$this->query_vars[ $t->query_var ] = str_replace( ' ', '+', $this->query_vars[ $t->query_var ] );
			}
		}

		// Don't allow non-publicly queryable taxonomies to be queried from the front end.
		if ( ! is_admin() ) {
			foreach ( get_taxonomies( array( 'publicly_queryable' => false ), 'objects' ) as $taxonomy => $t ) {
				/*
				 * Disallow when set to the 'taxonomy' query var.
				 * Non-publicly queryable taxonomies cannot register custom query vars. See register_taxonomy().
				 */
				if ( isset( $this->query_vars['taxonomy'] ) && $taxonomy === $this->query_vars['taxonomy'] ) {
					unset( $this->query_vars['taxonomy'], $this->query_vars['term'] );
				}
			}
		}

		// Limit publicly queried post_types to those that are 'publicly_queryable'.
		if ( isset( $this->query_vars['post_type'] ) ) {
			$queryable_post_types = get_post_types( array( 'publicly_queryable' => true ) );
			if ( ! is_array( $this->query_vars['post_type'] ) ) {
				if ( ! in_array( $this->query_vars['post_type'], $queryable_post_types, true ) ) {
					unset( $this->query_vars['post_type'] );
				}
			} else {
				$this->query_vars['post_type'] = array_intersect( $this->query_vars['post_type'], $queryable_post_types );
			}
		}

		// Resolve conflicts between posts with numeric slugs and date archive queries.
		$this->query_vars = wp_resolve_numeric_slug_conflicts( $this->query_vars );

		foreach ( (array) $this->private_query_vars as $var ) {
			if ( isset( $this->extra_query_vars[ $var ] ) ) {
				$this->query_vars[ $var ] = $this->extra_query_vars[ $var ];
			}
		}

		if ( isset( $error ) ) {
			$this->query_vars['error'] = $error;
		}

		/**
		 * Filters the array of parsed query variables.
		 *
		 * @since 2.1.0
		 *
		 * @param array $query_vars The array of requested query variables.
		 */
		$this->query_vars = apply_filters( 'request', $this->query_vars );

		/**
		 * Fires once all query variables for the current request have been parsed.
		 *
		 * @since 2.1.0
		 *
		 * @param WP $this Current WordPress environment instance (passed by reference).
		 */
		do_action_ref_array( 'parse_request', array( &$this ) );
	}

	/**
	 * Sends additional HTTP headers for caching, content type, etc.
	 *
	 * Sets the Content-Type header. Sets the 'error' status (if passed) and optionally exits.
	 * If showing a feed, it will also send Last-Modified, ETag, and 304 status if needed.
	 *
	 * @since 2.0.0
	 * @since 4.4.0 `X-Pingback` header is added conditionally after posts have been queried in handle_404().
	 */
	public function send_headers() {
		$headers       = array();
		$status        = null;
		$exit_required = false;

		if ( is_user_logged_in() ) {
			$headers = array_merge( $headers, wp_get_nocache_headers() );
		} elseif ( ! empty( $_GET['unapproved'] ) && ! empty( $_GET['moderation-hash'] ) ) {
			// Unmoderated comments are only visible for one minute via the moderation hash.
			$headers['Expires']       = gmdate( 'D, d M Y H:i:s', time() + MINUTE_IN_SECONDS );
			$headers['Cache-Control'] = 'max-age=60, must-revalidate';
		}
		if ( ! empty( $this->query_vars['error'] ) ) {
			$status = (int) $this->query_vars['error'];
			if ( 404 === $status ) {
				if ( ! is_user_logged_in() ) {
					$headers = array_merge( $headers, wp_get_nocache_headers() );
				}
				$headers['Content-Type'] = get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' );
			} elseif ( in_array( $status, array( 403, 500, 502, 503 ), true ) ) {
				$exit_required = true;
			}
		} elseif ( empty( $this->query_vars['feed'] ) ) {
			$headers['Content-Type'] = get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' );
		} else {
			// Set the correct content type for feeds.
			$type = $this->query_vars['feed'];
			if ( 'feed' === $this->query_vars['feed'] ) {
				$type = get_default_feed();
			}
			$headers['Content-Type'] = feed_content_type( $type ) . '; charset=' . get_option( 'blog_charset' );

			// We're showing a feed, so WP is indeed the only thing that last changed.
			if ( ! empty( $this->query_vars['withcomments'] )
				|| false !== strpos( $this->query_vars['feed'], 'comments-' )
				|| ( empty( $this->query_vars['withoutcomments'] )
					&& ( ! empty( $this->query_vars['p'] )
						|| ! empty( $this->query_vars['name'] )
						|| ! empty( $this->query_vars['page_id'] )
						|| ! empty( $this->query_vars['pagename'] )
						|| ! empty( $this->query_vars['attachment'] )
						|| ! empty( $this->query_vars['attachment_id'] )
					)
				)
			) {
				$wp_last_modified = mysql2date( 'D, d M Y H:i:s', get_lastcommentmodified( 'GMT' ), false );
			} else {
				$wp_last_modified = mysql2date( 'D, d M Y H:i:s', get_lastpostmodified( 'GMT' ), false );
			}

			if ( ! $wp_last_modified ) {
				$wp_last_modified = gmdate( 'D, d M Y H:i:s' );
			}

			$wp_last_modified .= ' GMT';

			$wp_etag                  = '"' . md5( $wp_last_modified ) . '"';
			$headers['Last-Modified'] = $wp_last_modified;
			$headers['ETag']          = $wp_etag;

			// Support for conditional GET.
			if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) {
				$client_etag = wp_unslash( $_SERVER['HTTP_IF_NONE_MATCH'] );
			} else {
				$client_etag = false;
			}

			$client_last_modified = empty( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ? '' : trim( $_SERVER['HTTP_IF_MODIFIED_SINCE'] );
			// If string is empty, return 0. If not, attempt to parse into a timestamp.
			$client_modified_timestamp = $client_last_modified ? strtotime( $client_last_modified ) : 0;

			// Make a timestamp for our most recent modification..
			$wp_modified_timestamp = strtotime( $wp_last_modified );

			if ( ( $client_last_modified && $client_etag ) ?
					( ( $client_modified_timestamp >= $wp_modified_timestamp ) && ( $client_etag == $wp_etag ) ) :
					( ( $client_modified_timestamp >= $wp_modified_timestamp ) || ( $client_etag == $wp_etag ) ) ) {
				$status        = 304;
				$exit_required = true;
			}
		}

		/**
		 * Filters the HTTP headers before they're sent to the browser.
		 *
		 * @since 2.8.0
		 *
		 * @param string[] $headers Associative array of headers to be sent.
		 * @param WP       $this    Current WordPress environment instance.
		 */
		$headers = apply_filters( 'wp_headers', $headers, $this );

		if ( ! empty( $status ) ) {
			status_header( $status );
		}

		// If Last-Modified is set to false, it should not be sent (no-cache situation).
		if ( isset( $headers['Last-Modified'] ) && false === $headers['Last-Modified'] ) {
			unset( $headers['Last-Modified'] );

			if ( ! headers_sent() ) {
				header_remove( 'Last-Modified' );
			}
		}

		if ( ! headers_sent() ) {
			foreach ( (array) $headers as $name => $field_value ) {
				header( "{$name}: {$field_value}" );
			}
		}

		if ( $exit_required ) {
			exit;
		}

		/**
		 * Fires once the requested HTTP headers for caching, content type, etc. have been sent.
		 *
		 * @since 2.1.0
		 *
		 * @param WP $this Current WordPress environment instance (passed by reference).
		 */
		do_action_ref_array( 'send_headers', array( &$this ) );
	}

	/**
	 * Sets the query string property based off of the query variable property.
	 *
	 * The {@see 'query_string'} filter is deprecated, but still works. Plugins should
	 * use the {@see 'request'} filter instead.
	 *
	 * @since 2.0.0
	 */
	public function build_query_string() {
		$this->query_string = '';
		foreach ( (array) array_keys( $this->query_vars ) as $wpvar ) {
			if ( '' != $this->query_vars[ $wpvar ] ) {
				$this->query_string .= ( strlen( $this->query_string ) < 1 ) ? '' : '&';
				if ( ! is_scalar( $this->query_vars[ $wpvar ] ) ) { // Discard non-scalars.
					continue;
				}
				$this->query_string .= $wpvar . '=' . rawurlencode( $this->query_vars[ $wpvar ] );
			}
		}

		if ( has_filter( 'query_string' ) ) {  // Don't bother filtering and parsing if no plugins are hooked in.
			/**
			 * Filters the query string before parsing.
			 *
			 * @since 1.5.0
			 * @deprecated 2.1.0 Use {@see 'query_vars'} or {@see 'request'} filters instead.
			 *
			 * @param string $query_string The query string to modify.
			 */
			$this->query_string = apply_filters_deprecated(
				'query_string',
				array( $this->query_string ),
				'2.1.0',
				'query_vars, request'
			);
			parse_str( $this->query_string, $this->query_vars );
		}
	}

	/**
	 * Set up the WordPress Globals.
	 *
	 * The query_vars property will be extracted to the GLOBALS. So care should
	 * be taken when naming global variables that might interfere with the
	 * WordPress environment.
	 *
	 * @since 2.0.0
	 *
	 * @global WP_Query     $wp_query     WordPress Query object.
	 * @global string       $query_string Query string for the loop.
	 * @global array        $posts        The found posts.
	 * @global WP_Post|null $post         The current post, if available.
	 * @global string       $request      The SQL statement for the request.
	 * @global int          $more         Only set, if single page or post.
	 * @global int          $single       If single page or post. Only set, if single page or post.
	 * @global WP_User      $authordata   Only set, if author archive.
	 */
	public function register_globals() {
		global $wp_query;

		// Extract updated query vars back into global namespace.
		foreach ( (array) $wp_query->query_vars as $key => $value ) {
			$GLOBALS[ $key ] = $value;
		}

		$GLOBALS['query_string'] = $this->query_string;
		$GLOBALS['posts']        = & $wp_query->posts;
		$GLOBALS['post']         = isset( $wp_query->post ) ? $wp_query->post : null;
		$GLOBALS['request']      = $wp_query->request;

		if ( $wp_query->is_single() || $wp_query->is_page() ) {
			$GLOBALS['more']   = 1;
			$GLOBALS['single'] = 1;
		}

		if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
			$GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
		}
	}

	/**
	 * Set up the current user.
	 *
	 * @since 2.0.0
	 */
	public function init() {
		wp_get_current_user();
	}

	/**
	 * Set up the Loop based on the query variables.
	 *
	 * @since 2.0.0
	 *
	 * @global WP_Query $wp_the_query WordPress Query object.
	 */
	public function query_posts() {
		global $wp_the_query;
		$this->build_query_string();
		$wp_the_query->query( $this->query_vars );
	}

	/**
	 * Set the Headers for 404, if nothing is found for requested URL.
	 *
	 * Issue a 404 if a request doesn't match any posts and doesn't match any object
	 * (e.g. an existing-but-empty category, tag, author) and a 404 was not already issued,
	 * and if the request was not a search or the homepage.
	 *
	 * Otherwise, issue a 200.
	 *
	 * This sets headers after posts have been queried. handle_404() really means "handle status".
	 * By inspecting the result of querying posts, seemingly successful requests can be switched to
	 * a 404 so that canonical redirection logic can kick in.
	 *
	 * @since 2.0.0
	 *
	 * @global WP_Query $wp_query WordPress Query object.
	 */
	public function handle_404() {
		global $wp_query;

		/**
		 * Filters whether to short-circuit default header status handling.
		 *
		 * Returning a non-false value from the filter will short-circuit the handling
		 * and return early.
		 *
		 * @since 4.5.0
		 *
		 * @param bool     $preempt  Whether to short-circuit default header status handling. Default false.
		 * @param WP_Query $wp_query WordPress Query object.
		 */
		if ( false !== apply_filters( 'pre_handle_404', false, $wp_query ) ) {
			return;
		}

		// If we've already issued a 404, bail.
		if ( is_404() ) {
			return;
		}

		$set_404 = true;

		// Never 404 for the admin, robots, or favicon.
		if ( is_admin() || is_robots() || is_favicon() ) {
			$set_404 = false;

			// If posts were found, check for paged content.
		} elseif ( $wp_query->posts ) {
			$content_found = true;

			if ( is_singular() ) {
				$post = isset( $wp_query->post ) ? $wp_query->post : null;

				// Only set X-Pingback for single posts that allow pings.
				if ( $post && pings_open( $post ) && ! headers_sent() ) {
					header( 'X-Pingback: ' . get_bloginfo( 'pingback_url', 'display' ) );
				}

				// Check for paged content that exceeds the max number of pages.
				$next = '<!--nextpage-->';
				if ( $post && ! empty( $this->query_vars['page'] ) ) {
					// Check if content is actually intended to be paged.
					if ( false !== strpos( $post->post_content, $next ) ) {
						$page          = trim( $this->query_vars['page'], '/' );
						$content_found = (int) $page <= ( substr_count( $post->post_content, $next ) + 1 );
					} else {
						$content_found = false;
					}
				}
			}

			// The posts page does not support the <!--nextpage--> pagination.
			if ( $wp_query->is_posts_page && ! empty( $this->query_vars['page'] ) ) {
				$content_found = false;
			}

			if ( $content_found ) {
				$set_404 = false;
			}

			// We will 404 for paged queries, as no posts were found.
		} elseif ( ! is_paged() ) {
			$author = get_query_var( 'author' );

			// Don't 404 for authors without posts as long as they matched an author on this site.
			if ( is_author() && is_numeric( $author ) && $author > 0 && is_user_member_of_blog( $author )
				// Don't 404 for these queries if they matched an object.
				|| ( is_tag() || is_category() || is_tax() || is_post_type_archive() ) && get_queried_object()
				// Don't 404 for these queries either.
				|| is_home() || is_search() || is_feed()
			) {
				$set_404 = false;
			}
		}

		if ( $set_404 ) {
			// Guess it's time to 404.
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		} else {
			status_header( 200 );
		}
	}

	/**
	 * Sets up all of the variables required by the WordPress environment.
	 *
	 * The action {@see 'wp'} has one parameter that references the WP object. It
	 * allows for accessing the properties and methods to further manipulate the
	 * object.
	 *
	 * @since 2.0.0
	 *
	 * @param string|array $query_args Passed to parse_request().
	 */
	public function main( $query_args = '' ) {
		$this->init();
		$this->parse_request( $query_args );
		$this->send_headers();
		$this->query_posts();
		$this->handle_404();
		$this->register_globals();

		/**
		 * Fires once the WordPress environment has been set up.
		 *
		 * @since 2.1.0
		 *
		 * @param WP $this Current WordPress environment instance (passed by reference).
		 */
		do_action_ref_array( 'wp', array( &$this ) );
	}
}

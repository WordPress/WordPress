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
	 * @access public
	 * @var array
	 */
	var $public_query_vars = array('m', 'p', 'posts', 'w', 'cat', 'withcomments', 'withoutcomments', 's', 'search', 'exact', 'sentence', 'debug', 'calendar', 'page', 'paged', 'more', 'tb', 'pb', 'author', 'order', 'orderby', 'year', 'monthnum', 'day', 'hour', 'minute', 'second', 'name', 'category_name', 'tag', 'feed', 'author_name', 'static', 'pagename', 'page_id', 'error', 'comments_popup', 'attachment', 'attachment_id', 'subpost', 'subpost_id', 'preview', 'robots', 'taxonomy', 'term', 'cpage', 'post_type');

	/**
	 * Private query variables.
	 *
	 * Long list of private query variables.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	var $private_query_vars = array('offset', 'posts_per_page', 'posts_per_archive_page', 'showposts', 'nopaging', 'post_type', 'post_status', 'category__in', 'category__not_in', 'category__and', 'tag__in', 'tag__not_in', 'tag__and', 'tag_slug__in', 'tag_slug__and', 'tag_id', 'post_mime_type', 'perm', 'comments_per_page', 'post__in', 'post__not_in');

	/**
	 * Extra query variables set by the user.
	 *
	 * @since 2.1.0
	 * @var array
	 */
	var $extra_query_vars = array();

	/**
	 * Query variables for setting up the WordPress Query Loop.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	var $query_vars;

	/**
	 * String parsed to set the query variables.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	var $query_string;

	/**
	 * Permalink or requested URI.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	var $request;

	/**
	 * Rewrite rule the request matched.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	var $matched_rule;

	/**
	 * Rewrite query the request matched.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	var $matched_query;

	/**
	 * Whether already did the permalink.
	 *
	 * @since 2.0.0
	 * @var bool
	 */
	var $did_permalink = false;

	/**
	 * Add name to list of public query variables.
	 *
	 * @since 2.1.0
	 *
	 * @param string $qv Query variable name.
	 */
	function add_query_var($qv) {
		if ( !in_array($qv, $this->public_query_vars) )
			$this->public_query_vars[] = $qv;
	}

	/**
	 * Set the value of a query variable.
	 *
	 * @since 2.3.0
	 *
	 * @param string $key Query variable name.
	 * @param mixed $value Query variable value.
	 */
	function set_query_var($key, $value) {
		$this->query_vars[$key] = $value;
	}

	/**
	 * Parse request to find correct WordPress query.
	 *
	 * Sets up the query variables based on the request. There are also many
	 * filters and actions that can be used to further manipulate the result.
	 *
	 * @since 2.0.0
	 *
	 * @param array|string $extra_query_vars Set the extra query variables.
	 */
	function parse_request($extra_query_vars = '') {
		global $wp_rewrite;

		$this->query_vars = array();
		$post_type_query_vars = array();

		if ( is_array($extra_query_vars) )
			$this->extra_query_vars = & $extra_query_vars;
		else if (! empty($extra_query_vars))
			parse_str($extra_query_vars, $this->extra_query_vars);

		// Process PATH_INFO, REQUEST_URI, and 404 for permalinks.

		// Fetch the rewrite rules.
		$rewrite = $wp_rewrite->wp_rewrite_rules();

		if ( ! empty($rewrite) ) {
			// If we match a rewrite rule, this will be cleared.
			$error = '404';
			$this->did_permalink = true;

			if ( isset($_SERVER['PATH_INFO']) )
				$pathinfo = $_SERVER['PATH_INFO'];
			else
				$pathinfo = '';
			$pathinfo_array = explode('?', $pathinfo);
			$pathinfo = str_replace("%", "%25", $pathinfo_array[0]);
			$req_uri = $_SERVER['REQUEST_URI'];
			$req_uri_array = explode('?', $req_uri);
			$req_uri = $req_uri_array[0];
			$self = $_SERVER['PHP_SELF'];
			$home_path = parse_url(home_url());
			if ( isset($home_path['path']) )
				$home_path = $home_path['path'];
			else
				$home_path = '';
			$home_path = trim($home_path, '/');

			// Trim path info from the end and the leading home path from the
			// front. For path info requests, this leaves us with the requesting
			// filename, if any. For 404 requests, this leaves us with the
			// requested permalink.
			$req_uri = str_replace($pathinfo, '', $req_uri);
			$req_uri = trim($req_uri, '/');
			$req_uri = preg_replace("|^$home_path|", '', $req_uri);
			$req_uri = trim($req_uri, '/');
			$pathinfo = trim($pathinfo, '/');
			$pathinfo = preg_replace("|^$home_path|", '', $pathinfo);
			$pathinfo = trim($pathinfo, '/');
			$self = trim($self, '/');
			$self = preg_replace("|^$home_path|", '', $self);
			$self = trim($self, '/');

			// The requested permalink is in $pathinfo for path info requests and
			//  $req_uri for other requests.
			if ( ! empty($pathinfo) && !preg_match('|^.*' . $wp_rewrite->index . '$|', $pathinfo) ) {
				$request = $pathinfo;
			} else {
				// If the request uri is the index, blank it out so that we don't try to match it against a rule.
				if ( $req_uri == $wp_rewrite->index )
					$req_uri = '';
				$request = $req_uri;
			}

			$this->request = $request;

			// Look for matches.
			$request_match = $request;
			if ( empty( $request_match ) ) {
				// An empty request could only match against ^$ regex
				if ( isset( $rewrite['$'] ) ) {
					$this->matched_rule = '$';
					$query = $rewrite['$'];
					$matches = array('');
				}
			} else if ( $req_uri != 'wp-app.php' ) {
				foreach ( (array) $rewrite as $match => $query ) {
					// If the requesting file is the anchor of the match, prepend it to the path info.
					if ( ! empty($req_uri) && strpos($match, $req_uri) === 0 && $req_uri != $request )
						$request_match = $req_uri . '/' . $request;

					if ( preg_match("#^$match#", $request_match, $matches) ||
						preg_match("#^$match#", urldecode($request_match), $matches) ) {

						if ( $wp_rewrite->use_verbose_page_rules && preg_match( '/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch ) ) {
							// this is a verbose page match, lets check to be sure about it
							if ( ! get_page_by_path( $matches[ $varmatch[1] ] ) )
						 		continue;
						}

						// Got a match.
						$this->matched_rule = $match;
						break;
					}
				}
			}

			if ( isset( $this->matched_rule ) ) {
				// Trim the query of everything up to the '?'.
				$query = preg_replace("!^.+\?!", '', $query);

				// Substitute the substring matches into the query.
				$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

				$this->matched_query = $query;

				// Parse the query.
				parse_str($query, $perma_query_vars);

				// If we're processing a 404 request, clear the error var
				// since we found something.
				unset( $_GET['error'] );
				unset( $error );
			}

			// If req_uri is empty or if it is a request for ourself, unset error.
			if ( empty($request) || $req_uri == $self || strpos($_SERVER['PHP_SELF'], 'wp-admin/') !== false ) {
				unset( $_GET['error'] );
				unset( $error );

				if ( isset($perma_query_vars) && strpos($_SERVER['PHP_SELF'], 'wp-admin/') !== false )
					unset( $perma_query_vars );

				$this->did_permalink = false;
			}
		}

		$this->public_query_vars = apply_filters('query_vars', $this->public_query_vars);

		foreach ( $GLOBALS['wp_post_types'] as $post_type => $t )
			if ( $t->query_var )
				$post_type_query_vars[$t->query_var] = $post_type;

		foreach ( $this->public_query_vars as $wpvar ) {
			if ( isset( $this->extra_query_vars[$wpvar] ) )
				$this->query_vars[$wpvar] = $this->extra_query_vars[$wpvar];
			elseif ( isset( $_POST[$wpvar] ) )
				$this->query_vars[$wpvar] = $_POST[$wpvar];
			elseif ( isset( $_GET[$wpvar] ) )
				$this->query_vars[$wpvar] = $_GET[$wpvar];
			elseif ( isset( $perma_query_vars[$wpvar] ) )
				$this->query_vars[$wpvar] = $perma_query_vars[$wpvar];

			if ( !empty( $this->query_vars[$wpvar] ) ) {
				if ( ! is_array( $this->query_vars[$wpvar] ) ) {
					$this->query_vars[$wpvar] = (string) $this->query_vars[$wpvar];
				} else {
					foreach ( $this->query_vars[$wpvar] as $vkey => $v ) {
						if ( !is_object( $v ) ) {
							$this->query_vars[$wpvar][$vkey] = (string) $v;
						}
					}
				}

				if ( isset($post_type_query_vars[$wpvar] ) ) {
					$this->query_vars['post_type'] = $post_type_query_vars[$wpvar];
					$this->query_vars['name'] = $this->query_vars[$wpvar];
				}
			}
		}

		// Convert urldecoded spaces back into +
		foreach ( $GLOBALS['wp_taxonomies'] as $taxonomy => $t )
			if ( $t->query_var && isset( $this->query_vars[$t->query_var] ) )
				$this->query_vars[$t->query_var] = str_replace( ' ', '+', $this->query_vars[$t->query_var] );

		// Limit publicly queried post_types to those that are publicly_queryable
		if ( isset( $this->query_vars['post_type']) ) {
			$queryable_post_types = get_post_types( array('publicly_queryable' => true) );
			if ( ! is_array( $this->query_vars['post_type'] ) ) {
				if ( ! in_array( $this->query_vars['post_type'], $queryable_post_types ) )
					unset( $this->query_vars['post_type'] );
			} else {
				$this->query_vars['post_type'] = array_intersect( $this->query_vars['post_type'], $queryable_post_types );
			}
		}

		foreach ( (array) $this->private_query_vars as $var) {
			if ( isset($this->extra_query_vars[$var]) )
				$this->query_vars[$var] = $this->extra_query_vars[$var];
		}

		if ( isset($error) )
			$this->query_vars['error'] = $error;

		$this->query_vars = apply_filters('request', $this->query_vars);

		do_action_ref_array('parse_request', array(&$this));
	}

	/**
	 * Send additional HTTP headers for caching, content type, etc.
	 *
	 * Sets the X-Pingback header, 404 status (if 404), Content-type. If showing
	 * a feed, it will also send last-modified, etag, and 304 status if needed.
	 *
	 * @since 2.0.0
	 */
	function send_headers() {
		$headers = array('X-Pingback' => get_bloginfo('pingback_url'));
		$status = null;
		$exit_required = false;

		if ( is_user_logged_in() )
			$headers = array_merge($headers, wp_get_nocache_headers());
		if ( !empty($this->query_vars['error']) && '404' == $this->query_vars['error'] ) {
			$status = 404;
			if ( !is_user_logged_in() )
				$headers = array_merge($headers, wp_get_nocache_headers());
			$headers['Content-Type'] = get_option('html_type') . '; charset=' . get_option('blog_charset');
		} else if ( empty($this->query_vars['feed']) ) {
			$headers['Content-Type'] = get_option('html_type') . '; charset=' . get_option('blog_charset');
		} else {
			// We're showing a feed, so WP is indeed the only thing that last changed
			if ( !empty($this->query_vars['withcomments'])
				|| ( empty($this->query_vars['withoutcomments'])
					&& ( !empty($this->query_vars['p'])
						|| !empty($this->query_vars['name'])
						|| !empty($this->query_vars['page_id'])
						|| !empty($this->query_vars['pagename'])
						|| !empty($this->query_vars['attachment'])
						|| !empty($this->query_vars['attachment_id'])
					)
				)
			)
				$wp_last_modified = mysql2date('D, d M Y H:i:s', get_lastcommentmodified('GMT'), 0).' GMT';
			else
				$wp_last_modified = mysql2date('D, d M Y H:i:s', get_lastpostmodified('GMT'), 0).' GMT';
			$wp_etag = '"' . md5($wp_last_modified) . '"';
			$headers['Last-Modified'] = $wp_last_modified;
			$headers['ETag'] = $wp_etag;

			// Support for Conditional GET
			if (isset($_SERVER['HTTP_IF_NONE_MATCH']))
				$client_etag = stripslashes(stripslashes($_SERVER['HTTP_IF_NONE_MATCH']));
			else $client_etag = false;

			$client_last_modified = empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? '' : trim($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			// If string is empty, return 0. If not, attempt to parse into a timestamp
			$client_modified_timestamp = $client_last_modified ? strtotime($client_last_modified) : 0;

			// Make a timestamp for our most recent modification...
			$wp_modified_timestamp = strtotime($wp_last_modified);

			if ( ($client_last_modified && $client_etag) ?
					 (($client_modified_timestamp >= $wp_modified_timestamp) && ($client_etag == $wp_etag)) :
					 (($client_modified_timestamp >= $wp_modified_timestamp) || ($client_etag == $wp_etag)) ) {
				$status = 304;
				$exit_required = true;
			}
		}

		$headers = apply_filters('wp_headers', $headers, $this);

		if ( ! empty( $status ) )
			status_header( $status );
		foreach( (array) $headers as $name => $field_value )
			@header("{$name}: {$field_value}");

		if ( $exit_required )
			exit();

		do_action_ref_array('send_headers', array(&$this));
	}

	/**
	 * Sets the query string property based off of the query variable property.
	 *
	 * The 'query_string' filter is deprecated, but still works. Plugins should
	 * use the 'request' filter instead.
	 *
	 * @since 2.0.0
	 */
	function build_query_string() {
		$this->query_string = '';
		foreach ( (array) array_keys($this->query_vars) as $wpvar) {
			if ( '' != $this->query_vars[$wpvar] ) {
				$this->query_string .= (strlen($this->query_string) < 1) ? '' : '&';
				if ( !is_scalar($this->query_vars[$wpvar]) ) // Discard non-scalars.
					continue;
				$this->query_string .= $wpvar . '=' . rawurlencode($this->query_vars[$wpvar]);
			}
		}

		// query_string filter deprecated. Use request filter instead.
		if ( has_filter('query_string') ) {  // Don't bother filtering and parsing if no plugins are hooked in.
			$this->query_string = apply_filters('query_string', $this->query_string);
			parse_str($this->query_string, $this->query_vars);
		}
	}

	/**
	 * Set up the WordPress Globals.
	 *
	 * The query_vars property will be extracted to the GLOBALS. So care should
	 * be taken when naming global variables that might interfere with the
	 * WordPress environment.
	 *
	 * @global string $query_string Query string for the loop.
	 * @global int $more Only set, if single page or post.
	 * @global int $single If single page or post. Only set, if single page or post.
	 *
	 * @since 2.0.0
	 */
	function register_globals() {
		global $wp_query;
		// Extract updated query vars back into global namespace.
		foreach ( (array) $wp_query->query_vars as $key => $value) {
			$GLOBALS[$key] = $value;
		}

		$GLOBALS['query_string'] = $this->query_string;
		$GLOBALS['posts'] = & $wp_query->posts;
		$GLOBALS['post'] = (isset($wp_query->post)) ? $wp_query->post : null;
		$GLOBALS['request'] = $wp_query->request;

		if ( is_single() || is_page() ) {
			$GLOBALS['more'] = 1;
			$GLOBALS['single'] = 1;
		}
	}

	/**
	 * Set up the current user.
	 *
	 * @since 2.0.0
	 */
	function init() {
		wp_get_current_user();
	}

	/**
	 * Set up the Loop based on the query variables.
	 *
	 * @uses WP::$query_vars
	 * @since 2.0.0
	 */
	function query_posts() {
		global $wp_the_query;
		$this->build_query_string();
		$wp_the_query->query($this->query_vars);
 	}

 	/**
 	 * Set the Headers for 404, if nothing is found for requested URL.
	 *
	 * Issue a 404 if a request doesn't match any posts and doesn't match
	 * any object (e.g. an existing-but-empty category, tag, author) and a 404 was not already
	 * issued, and if the request was not a search or the homepage.
	 *
	 * Otherwise, issue a 200.
	 *
	 * @since 2.0.0
 	 */
	function handle_404() {
		global $wp_query;

		if ( !is_admin() && ( 0 == count( $wp_query->posts ) ) && !is_404() && !is_robots() && !is_search() && !is_home() ) {
			// Don't 404 for these queries if they matched an object.
			if ( ( is_tag() || is_category() || is_tax() || is_author() || is_post_type_archive() ) && $wp_query->get_queried_object() && !is_paged() ) {
				if ( !is_404() )
					status_header( 200 );
				return;
			}
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		} elseif ( !is_404() ) {
			status_header( 200 );
		}
	}

	/**
	 * Sets up all of the variables required by the WordPress environment.
	 *
	 * The action 'wp' has one parameter that references the WP object. It
	 * allows for accessing the properties and methods to further manipulate the
	 * object.
	 *
	 * @since 2.0.0
	 *
	 * @param string|array $query_args Passed to {@link parse_request()}
	 */
	function main($query_args = '') {
		$this->init();
		$this->parse_request($query_args);
		$this->send_headers();
		$this->query_posts();
		$this->handle_404();
		$this->register_globals();
		do_action_ref_array('wp', array(&$this));
	}

}

/**
 * Helper class to remove the need to use eval to replace $matches[] in query strings.
 *
 * @since 2.9.0
 */
class WP_MatchesMapRegex {
	/**
	 * store for matches
	 *
	 * @access private
	 * @var array
	 */
	var $_matches;

	/**
	 * store for mapping result
	 *
	 * @access public
	 * @var string
	 */
	var $output;

	/**
	 * subject to perform mapping on (query string containing $matches[] references
	 *
	 * @access private
	 * @var string
	 */
	var $_subject;

	/**
	 * regexp pattern to match $matches[] references
	 *
	 * @var string
	 */
	var $_pattern = '(\$matches\[[1-9]+[0-9]*\])'; // magic number

	/**
	 * constructor
	 *
	 * @param string $subject subject if regex
	 * @param array  $matches data to use in map
	 * @return self
	 */
	function WP_MatchesMapRegex($subject, $matches) {
		$this->_subject = $subject;
		$this->_matches = $matches;
		$this->output = $this->_map();
	}

	/**
	 * Substitute substring matches in subject.
	 *
	 * static helper function to ease use
	 *
	 * @access public
	 * @param string $subject subject
	 * @param array  $matches data used for substitution
	 * @return string
	 */
	public static function apply($subject, $matches) {
		$oSelf = new WP_MatchesMapRegex($subject, $matches);
		return $oSelf->output;
	}

	/**
	 * do the actual mapping
	 *
	 * @access private
	 * @return string
	 */
	function _map() {
		$callback = array(&$this, 'callback');
		return preg_replace_callback($this->_pattern, $callback, $this->_subject);
	}

	/**
	 * preg_replace_callback hook
	 *
	 * @access public
	 * @param  array $matches preg_replace regexp matches
	 * @return string
	 */
	function callback($matches) {
		$index = intval(substr($matches[0], 9, -1));
		return ( isset( $this->_matches[$index] ) ? urlencode($this->_matches[$index]) : '' );
	}

}

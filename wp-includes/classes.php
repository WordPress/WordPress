<?php
/**
 * Holds Most of the WordPress classes.
 *
 * Some of the other classes are contained in other files. For example, the
 * WordPress cache is in cache.php and the WordPress roles API is in
 * capabilities.php. The third party libraries are contained in their own
 * separate files.
 *
 * @package WordPress
 */

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
	var $private_query_vars = array('offset', 'posts_per_page', 'posts_per_archive_page', 'showposts', 'nopaging', 'post_type', 'post_status', 'category__in', 'category__not_in', 'category__and', 'tag__in', 'tag__not_in', 'tag__and', 'tag_slug__in', 'tag_slug__and', 'tag_id', 'post_mime_type', 'perm', 'comments_per_page');

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
			// front.  For path info requests, this leaves us with the requesting
			// filename, if any.  For 404 requests, this leaves us with the
			// requested permalink.
			$req_uri = str_replace($pathinfo, '', rawurldecode($req_uri));
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
			foreach ( (array) $rewrite as $match => $query) {
				// Don't try to match against AtomPub calls
				if ( $req_uri == 'wp-app.php' )
					break;

				// If the requesting file is the anchor of the match, prepend it
				// to the path info.
				if ( (! empty($req_uri)) && (strpos($match, $req_uri) === 0) && ($req_uri != $request) )
					$request_match = $req_uri . '/' . $request;

				if ( preg_match("#^$match#", $request_match, $matches) ||
					preg_match("#^$match#", urldecode($request_match), $matches) ) {
					// Got a match.
					$this->matched_rule = $match;

					// Trim the query of everything up to the '?'.
					$query = preg_replace("!^.+\?!", '', $query);

					// Substitute the substring matches into the query.
					$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

					$this->matched_query = $query;

					// Parse the query.
					parse_str($query, $perma_query_vars);

					// If we're processing a 404 request, clear the error var
					// since we found something.
					if ( isset($_GET['error']) )
						unset($_GET['error']);

					if ( isset($error) )
						unset($error);

					break;
				}
			}

			// If req_uri is empty or if it is a request for ourself, unset error.
			if ( empty($request) || $req_uri == $self || strpos($_SERVER['PHP_SELF'], 'wp-admin/') !== false ) {
				if ( isset($_GET['error']) )
					unset($_GET['error']);

				if ( isset($error) )
					unset($error);

				if ( isset($perma_query_vars) && strpos($_SERVER['PHP_SELF'], 'wp-admin/') !== false )
					unset($perma_query_vars);

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

		// query_string filter deprecated.  Use request filter instead.
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
			if ( ( is_tag() || is_category() || is_tax() || is_author() ) && $wp_query->get_queried_object() && !is_paged() ) {
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

	/**
	 * PHP4 Constructor - Does nothing.
	 *
	 * Call main() method when ready to run setup.
	 *
	 * @since 2.0.0
	 *
	 * @return WP
	 */
	function WP() {
		// Empty.
	}
}

/**
 * WordPress Query class.
 *
 * Abstract class for handling advanced queries
 *
 * @package WordPress
 * @since 3.1.0
 */
class WP_Object_Query {

	/*
	 * Populates the $meta_query property
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @param array $qv The query variables
	 */
	function parse_meta_query( &$qv ) {
		$meta_query = array();

		// Simple query needs to be first for orderby=meta_value to work correctly
		foreach ( array( 'key', 'value', 'compare', 'type' ) as $key ) {
			if ( !empty( $qv[ "meta_$key" ] ) )
				$meta_query[0][ $key ] = $qv[ "meta_$key" ];
		}

		if ( !empty( $qv['meta_query'] ) && is_array( $qv['meta_query'] ) ) {
			$meta_query = array_merge( $meta_query, $qv['meta_query'] );
		}

		$qv['meta_query'] = $meta_query;
	}

	/*
	 * Used internally to generate an SQL string for searching across multiple meta key = value pairs
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @param array $meta_query List of metadata queries. A single query is an associative array:
	 * - 'key' string The meta key
	 * - 'value' string|array The meta value
	 * - 'compare' (optional) string How to compare the key to the value.
	 *		Possible values: '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'.
	 *		Default: '='
	 * - 'type' string (optional) The type of the value.
	 *		Possible values: 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'.
	 *		Default: 'CHAR'
	 *
	 * @param string $primary_table
	 * @param string $primary_id_column
	 * @param string $meta_table
	 * @param string $meta_id_column
	 * @return array( $join_sql, $where_sql )
	 */
	function get_meta_sql( $meta_query, $primary_table, $primary_id_column, $meta_table, $meta_id_column ) {
		global $wpdb;

		$clauses = array();

		$join = '';
		$where = '';
		$i = 0;
		foreach ( $meta_query as $q ) {
			$meta_key = isset( $q['key'] ) ? trim( $q['key'] ) : '';
			$meta_value = isset( $q['value'] ) ? $q['value'] : '';
			$meta_compare = isset( $q['compare'] ) ? strtoupper( $q['compare'] ) : '=';
			$meta_type = isset( $q['type'] ) ? strtoupper( $q['type'] ) : 'CHAR';

			if ( ! in_array( $meta_compare, array( '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ) ) )
				$meta_compare = '=';

			if ( 'NUMERIC' == $meta_type )
				$meta_type = 'SIGNED';
			elseif ( ! in_array( $meta_type, array( 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED' ) ) )
				$meta_type = 'CHAR';

			if ( empty( $meta_key ) && empty( $meta_value ) )
				continue;

			$alias = $i ? 'mt' . $i : $meta_table;

			$join .= "\nINNER JOIN $meta_table";
			$join .= $i ? " AS $alias" : '';
			$join .= " ON ($primary_table.$primary_id_column = $alias.$meta_id_column)";

			$i++;

			if ( !empty( $meta_key ) )
				$where .= $wpdb->prepare( " AND $alias.meta_key = %s", $meta_key );

			if ( in_array( $meta_compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ) ) ) {
				if ( ! is_array( $meta_value ) )
					$meta_value = preg_split( '/[,\s]+/', $meta_value );
			} else {
				$meta_value = trim( $meta_value );
			}

			if ( empty( $meta_value ) )
				continue;

			if ( 'IN' == substr( $meta_compare, -2) ) {
				$meta_field_types = substr( str_repeat( ',%s', count( $meta_value ) ), 1 );
				$meta_compare_string = "($meta_field_types)";
				unset( $meta_field_types );
			} elseif ( 'BETWEEN' == substr( $meta_compare, -7) ) {
				$meta_value = array_slice( $meta_value, 0, 2 );
				$meta_compare_string = '%s AND %s';
			} elseif ( 'LIKE' == substr( $meta_compare, -4 ) ) {
				$meta_value = '%' . like_escape( $meta_value ) . '%';
				$meta_compare_string = '%s';
			} else {
				$meta_compare_string = '%s';
			}
			$where .= $wpdb->prepare( " AND CAST($alias.meta_value AS {$meta_type}) {$meta_compare} {$meta_compare_string}", $meta_value );
			unset($meta_compare_string);
		}

		return array( $join, $where );
	}

	/*
	 * Used internally to generate an SQL string for searching across multiple taxonomies
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @param array $tax_query List of taxonomy queries. A single taxonomy query is an associative array:
	 * - 'taxonomy' string|array The taxonomy being queried
	 * - 'terms' string|array The list of terms
	 * - 'field' string (optional) Which term field is being used.
	 *		Possible values: 'term_id', 'slug' or 'name'
	 *		Default: 'slug'
	 * - 'operator' string (optional)
	 *		Possible values: 'IN' and 'NOT IN'.
	 *		Default: 'IN'
	 * - 'include_children' bool (optional) Whether to include child terms.
	 *		Default: true
	 *
	 * @param string $object_id_column
	 * @return string
	 */
	function get_tax_sql( $tax_query, $object_id_column ) {
		global $wpdb;

		$sql = array();
		foreach ( $tax_query as $query ) {
			if ( !isset( $query['include_children'] ) )
				$query['include_children'] = true;

			$query['do_query'] = false;

			$sql_single = get_objects_in_term( $query['terms'], $query['taxonomy'], $query );

			if ( empty( $sql_single ) )
				return ' AND 0 = 1';

			$sql[] = $sql_single;
		}

		if ( 1 == count( $sql ) ) {
			$ids = $wpdb->get_col( $sql[0] );
		} else {
			$r = "SELECT object_id FROM $wpdb->term_relationships WHERE 1=1";
			foreach ( $sql as $query )
				$r .= " AND object_id IN ($query)";

			$ids = $wpdb->get_col( $r );
		}

		if ( !empty( $ids ) )
			return " AND $object_id_column IN(" . implode( ', ', $ids ) . ")";
		else
			return ' AND 0 = 1';
	}

	/*
	 * Used internally to generate an SQL string for searching across multiple columns
	 *
	 * @access protected
	 * @since 3.1.0
	 *
	 * @param string $string
	 * @param array $cols
	 * @return string
	 */
	function get_search_sql( $string, $cols ) {
		$string = esc_sql( $string );

		$searches = array();
		foreach ( $cols as $col )
			$searches[] = "$col LIKE '%$string%'";

		return ' AND (' . implode(' OR ', $searches) . ')';
	}
}

/**
 * WordPress Error class.
 *
 * Container for checking for WordPress errors and error messages. Return
 * WP_Error and use {@link is_wp_error()} to check if this class is returned.
 * Many core WordPress functions pass this class in the event of an error and
 * if not handled properly will result in code errors.
 *
 * @package WordPress
 * @since 2.1.0
 */
class WP_Error {
	/**
	 * Stores the list of errors.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $errors = array();

	/**
	 * Stores the list of data for error codes.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $error_data = array();

	/**
	 * PHP4 Constructor - Sets up error message.
	 *
	 * If code parameter is empty then nothing will be done. It is possible to
	 * add multiple messages to the same code, but with other methods in the
	 * class.
	 *
	 * All parameters are optional, but if the code parameter is set, then the
	 * data parameter is optional.
	 *
	 * @since 2.1.0
	 *
	 * @param string|int $code Error code
	 * @param string $message Error message
	 * @param mixed $data Optional. Error data.
	 * @return WP_Error
	 */
	function WP_Error($code = '', $message = '', $data = '') {
		if ( empty($code) )
			return;

		$this->errors[$code][] = $message;

		if ( ! empty($data) )
			$this->error_data[$code] = $data;
	}

	/**
	 * Retrieve all error codes.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array List of error codes, if avaiable.
	 */
	function get_error_codes() {
		if ( empty($this->errors) )
			return array();

		return array_keys($this->errors);
	}

	/**
	 * Retrieve first error code available.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return string|int Empty string, if no error codes.
	 */
	function get_error_code() {
		$codes = $this->get_error_codes();

		if ( empty($codes) )
			return '';

		return $codes[0];
	}

	/**
	 * Retrieve all error messages or error messages matching code.
	 *
	 * @since 2.1.0
	 *
	 * @param string|int $code Optional. Retrieve messages matching code, if exists.
	 * @return array Error strings on success, or empty array on failure (if using codee parameter).
	 */
	function get_error_messages($code = '') {
		// Return all messages if no code specified.
		if ( empty($code) ) {
			$all_messages = array();
			foreach ( (array) $this->errors as $code => $messages )
				$all_messages = array_merge($all_messages, $messages);

			return $all_messages;
		}

		if ( isset($this->errors[$code]) )
			return $this->errors[$code];
		else
			return array();
	}

	/**
	 * Get single error message.
	 *
	 * This will get the first message available for the code. If no code is
	 * given then the first code available will be used.
	 *
	 * @since 2.1.0
	 *
	 * @param string|int $code Optional. Error code to retrieve message.
	 * @return string
	 */
	function get_error_message($code = '') {
		if ( empty($code) )
			$code = $this->get_error_code();
		$messages = $this->get_error_messages($code);
		if ( empty($messages) )
			return '';
		return $messages[0];
	}

	/**
	 * Retrieve error data for error code.
	 *
	 * @since 2.1.0
	 *
	 * @param string|int $code Optional. Error code.
	 * @return mixed Null, if no errors.
	 */
	function get_error_data($code = '') {
		if ( empty($code) )
			$code = $this->get_error_code();

		if ( isset($this->error_data[$code]) )
			return $this->error_data[$code];
		return null;
	}

	/**
	 * Append more error messages to list of error messages.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string|int $code Error code.
	 * @param string $message Error message.
	 * @param mixed $data Optional. Error data.
	 */
	function add($code, $message, $data = '') {
		$this->errors[$code][] = $message;
		if ( ! empty($data) )
			$this->error_data[$code] = $data;
	}

	/**
	 * Add data for error code.
	 *
	 * The error code can only contain one error data.
	 *
	 * @since 2.1.0
	 *
	 * @param mixed $data Error data.
	 * @param string|int $code Error code.
	 */
	function add_data($data, $code = '') {
		if ( empty($code) )
			$code = $this->get_error_code();

		$this->error_data[$code] = $data;
	}
}

/**
 * Check whether variable is a WordPress Error.
 *
 * Looks at the object and if a WP_Error class. Does not check to see if the
 * parent is also WP_Error, so can't inherit WP_Error and still use this
 * function.
 *
 * @since 2.1.0
 *
 * @param mixed $thing Check if unknown variable is WordPress Error object.
 * @return bool True, if WP_Error. False, if not WP_Error.
 */
function is_wp_error($thing) {
	if ( is_object($thing) && is_a($thing, 'WP_Error') )
		return true;
	return false;
}

/**
 * A class for displaying various tree-like structures.
 *
 * Extend the Walker class to use it, see examples at the below. Child classes
 * do not need to implement all of the abstract methods in the class. The child
 * only needs to implement the methods that are needed. Also, the methods are
 * not strictly abstract in that the parameter definition needs to be followed.
 * The child classes can have additional parameters.
 *
 * @package WordPress
 * @since 2.1.0
 * @abstract
 */
class Walker {
	/**
	 * What the class handles.
	 *
	 * @since 2.1.0
	 * @var string
	 * @access public
	 */
	var $tree_type;

	/**
	 * DB fields to use.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access protected
	 */
	var $db_fields;

	/**
	 * Max number of pages walked by the paged walker
	 *
	 * @since 2.7.0
	 * @var int
	 * @access protected
	 */
	var $max_pages = 1;

	/**
	 * Starts the list before the elements are added.
	 *
	 * Additional parameters are used in child classes. The args parameter holds
	 * additional values that may be used with the child class methods. This
	 * method is called at the start of the output list.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 */
	function start_lvl(&$output) {}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * Additional parameters are used in child classes. The args parameter holds
	 * additional values that may be used with the child class methods. This
	 * method finishes the list at the end of output of the elements.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 */
	function end_lvl(&$output)   {}

	/**
	 * Start the element output.
	 *
	 * Additional parameters are used in child classes. The args parameter holds
	 * additional values that may be used with the child class methods. Includes
	 * the element output also.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 */
	function start_el(&$output)  {}

	/**
	 * Ends the element output, if needed.
	 *
	 * Additional parameters are used in child classes. The args parameter holds
	 * additional values that may be used with the child class methods.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 */
	function end_el(&$output)    {}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. It is possible to set the
	 * max depth to include all depths, see walk() method.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @since 2.5.0
	 *
	 * @param object $element Data object
	 * @param array $children_elements List of elements to continue traversing.
	 * @param int $max_depth Max depth to traverse.
	 * @param int $depth Depth of current element.
	 * @param array $args
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element )
			return;

		$id_field = $this->db_fields['id'];

		//display this element
		if ( is_array( $args[0] ) )
			$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'start_el'), $cb_args);

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

			foreach( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'end_el'), $cb_args);
	}

	/**
	 * Display array of elements hierarchically.
	 *
	 * It is a generic function which does not assume any existing order of
	 * elements. max_depth = -1 means flatly display every element. max_depth =
	 * 0 means display all levels. max_depth > 0  specifies the number of
	 * display levels.
	 *
	 * @since 2.1.0
	 *
	 * @param array $elements
	 * @param int $max_depth
	 * @return string
	 */
	function walk( $elements, $max_depth) {

		$args = array_slice(func_get_args(), 2);
		$output = '';

		if ($max_depth < -1) //invalid parameter
			return $output;

		if (empty($elements)) //nothing to walk
			return $output;

		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		// flat display
		if ( -1 == $max_depth ) {
			$empty_array = array();
			foreach ( $elements as $e )
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			return $output;
		}

		/*
		 * need to display in hierarchical order
		 * separate elements into two buckets: top level and children elements
		 * children_elements is two dimensional array, eg.
		 * children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e) {
			if ( 0 == $e->$parent_field )
				$top_level_elements[] = $e;
			else
				$children_elements[ $e->$parent_field ][] = $e;
		}

		/*
		 * when none of the elements is top level
		 * assume the first one must be root of the sub elements
		 */
		if ( empty($top_level_elements) ) {

			$first = array_slice( $elements, 0, 1 );
			$root = $first[0];

			$top_level_elements = array();
			$children_elements  = array();
			foreach ( $elements as $e) {
				if ( $root->$parent_field == $e->$parent_field )
					$top_level_elements[] = $e;
				else
					$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		foreach ( $top_level_elements as $e )
			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );

		/*
		 * if we are displaying all levels, and remaining children_elements is not empty,
		 * then we got orphans, which should be displayed regardless
		 */
		if ( ( $max_depth == 0 ) && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans )
				foreach( $orphans as $op )
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
		 }

		 return $output;
	}

	/**
 	 * paged_walk() - produce a page of nested elements
 	 *
 	 * Given an array of hierarchical elements, the maximum depth, a specific page number,
 	 * and number of elements per page, this function first determines all top level root elements
 	 * belonging to that page, then lists them and all of their children in hierarchical order.
 	 *
 	 * @package WordPress
 	 * @since 2.7
 	 * @param int $max_depth = 0 means display all levels; $max_depth > 0 specifies the number of display levels.
 	 * @param int $page_num the specific page number, beginning with 1.
 	 * @return XHTML of the specified page of elements
 	 */
	function paged_walk( $elements, $max_depth, $page_num, $per_page ) {

		/* sanity check */
		if ( empty($elements) || $max_depth < -1 )
			return '';

		$args = array_slice( func_get_args(), 4 );
		$output = '';

		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		$count = -1;
		if ( -1 == $max_depth )
			$total_top = count( $elements );
		if ( $page_num < 1 || $per_page < 0  ) {
			// No paging
			$paging = false;
			$start = 0;
			if ( -1 == $max_depth )
				$end = $total_top;
			$this->max_pages = 1;
		} else {
			$paging = true;
			$start = ( (int)$page_num - 1 ) * (int)$per_page;
			$end   = $start + $per_page;
			if ( -1 == $max_depth )
				$this->max_pages = ceil($total_top / $per_page);
		}

		// flat display
		if ( -1 == $max_depth ) {
			if ( !empty($args[0]['reverse_top_level']) ) {
				$elements = array_reverse( $elements );
				$oldstart = $start;
				$start = $total_top - $end;
				$end = $total_top - $oldstart;
			}

			$empty_array = array();
			foreach ( $elements as $e ) {
				$count++;
				if ( $count < $start )
					continue;
				if ( $count >= $end )
					break;
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			}
			return $output;
		}

		/*
		 * separate elements into two buckets: top level and children elements
		 * children_elements is two dimensional array, eg.
		 * children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e) {
			if ( 0 == $e->$parent_field )
				$top_level_elements[] = $e;
			else
				$children_elements[ $e->$parent_field ][] = $e;
		}

		$total_top = count( $top_level_elements );
		if ( $paging )
			$this->max_pages = ceil($total_top / $per_page);
		else
			$end = $total_top;

		if ( !empty($args[0]['reverse_top_level']) ) {
			$top_level_elements = array_reverse( $top_level_elements );
			$oldstart = $start;
			$start = $total_top - $end;
			$end = $total_top - $oldstart;
		}
		if ( !empty($args[0]['reverse_children']) ) {
			foreach ( $children_elements as $parent => $children )
				$children_elements[$parent] = array_reverse( $children );
		}

		foreach ( $top_level_elements as $e ) {
			$count++;

			//for the last page, need to unset earlier children in order to keep track of orphans
			if ( $end >= $total_top && $count < $start )
					$this->unset_children( $e, $children_elements );

			if ( $count < $start )
				continue;

			if ( $count >= $end )
				break;

			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
		}

		if ( $end >= $total_top && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans )
				foreach( $orphans as $op )
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
		}

		return $output;
	}

	function get_number_of_root_elements( $elements ){

		$num = 0;
		$parent_field = $this->db_fields['parent'];

		foreach ( $elements as $e) {
			if ( 0 == $e->$parent_field )
				$num++;
		}
		return $num;
	}

	// unset all the children for a given top level element
	function unset_children( $e, &$children_elements ){

		if ( !$e || !$children_elements )
			return;

		$id_field = $this->db_fields['id'];
		$id = $e->$id_field;

		if ( !empty($children_elements[$id]) && is_array($children_elements[$id]) )
			foreach ( (array) $children_elements[$id] as $child )
				$this->unset_children( $child, $children_elements );

		if ( isset($children_elements[$id]) )
			unset( $children_elements[$id] );

	}
}

/**
 * Create HTML list of pages.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_Page extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this.
	 * @var array
	 */
	var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	/**
	 * @see Walker::start_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class='children'>\n";
	}

	/**
	 * @see Walker::end_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function end_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page. Used for padding.
	 * @param int $current_page Page ID.
	 * @param array $args
	 */
	function start_el(&$output, $page, $depth, $args, $current_page) {
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';

		extract($args, EXTR_SKIP);
		$css_class = array('page_item', 'page-item-'.$page->ID);
		if ( !empty($current_page) ) {
			$_current_page = get_page( $current_page );
			if ( isset($_current_page->ancestors) && in_array($page->ID, (array) $_current_page->ancestors) )
				$css_class[] = 'current_page_ancestor';
			if ( $page->ID == $current_page )
				$css_class[] = 'current_page_item';
			elseif ( $_current_page && $page->ID == $_current_page->post_parent )
				$css_class[] = 'current_page_parent';
		} elseif ( $page->ID == get_option('page_for_posts') ) {
			$css_class[] = 'current_page_parent';
		}

		$css_class = implode(' ', apply_filters('page_css_class', $css_class, $page));

		$output .= $indent . '<li class="' . $css_class . '"><a href="' . get_permalink($page->ID) . '" title="' . esc_attr( wp_strip_all_tags( apply_filters( 'the_title', $page->post_title, $page->ID ) ) ) . '">' . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after . '</a>';

		if ( !empty($show_date) ) {
			if ( 'modified' == $show_date )
				$time = $page->post_modified;
			else
				$time = $page->post_date;

			$output .= " " . mysql2date($date_format, $time);
		}
	}

	/**
	 * @see Walker::end_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 */
	function end_el(&$output, $page, $depth) {
		$output .= "</li>\n";
	}

}

/**
 * Create HTML dropdown list of pages.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_PageDropdown extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page in reference to parent pages. Used for padding.
	 * @param array $args Uses 'selected' argument for selected page to set selected HTML attribute for option element.
	 */
	function start_el(&$output, $page, $depth, $args) {
		$pad = str_repeat('&nbsp;', $depth * 3);

		$output .= "\t<option class=\"level-$depth\" value=\"$page->ID\"";
		if ( $page->ID == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$title = esc_html($page->post_title);
		$output .= "$pad$title";
		$output .= "</option>\n";
	}
}

/**
 * Create HTML list of categories.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_Category extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'category';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	/**
	 * @see Walker::start_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Used for tab indentation.
	 * @param array $args Will only append content if style argument value is 'list'.
	 */
	function start_lvl(&$output, $depth, $args) {
		if ( 'list' != $args['style'] )
			return;

		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * @see Walker::end_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of category. Used for tab indentation.
	 * @param array $args Will only append content if style argument value is 'list'.
	 */
	function end_lvl(&$output, $depth, $args) {
		if ( 'list' != $args['style'] )
			return;

		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int $depth Depth of category in reference to parents.
	 * @param array $args
	 */
	function start_el(&$output, $category, $depth, $args) {
		extract($args);

		$cat_name = esc_attr( $category->name );
		$cat_name = apply_filters( 'list_cats', $cat_name, $category );
		$link = '<a href="' . esc_attr( get_term_link($category) ) . '" ';
		if ( $use_desc_for_title == 0 || empty($category->description) )
			$link .= 'title="' . sprintf(__( 'View all posts filed under %s' ), $cat_name) . '"';
		else
			$link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
		$link .= '>';
		$link .= $cat_name . '</a>';

		if ( !empty($feed_image) || !empty($feed) ) {
			$link .= ' ';

			if ( empty($feed_image) )
				$link .= '(';

			$link .= '<a href="' . get_term_feed_link( $category->term_id, $category->taxonomy, $feed_type ) . '"';

			if ( empty($feed) ) {
				$alt = ' alt="' . sprintf(__( 'Feed for all posts filed under %s' ), $cat_name ) . '"';
			} else {
				$title = ' title="' . $feed . '"';
				$alt = ' alt="' . $feed . '"';
				$name = $feed;
				$link .= $title;
			}

			$link .= '>';

			if ( empty($feed_image) )
				$link .= $name;
			else
				$link .= "<img src='$feed_image'$alt$title" . ' />';

			$link .= '</a>';

			if ( empty($feed_image) )
				$link .= ')';
		}

		if ( !empty($show_count) )
			$link .= ' (' . intval($category->count) . ')';

		if ( !empty($show_date) )
			$link .= ' ' . gmdate('Y-m-d', $category->last_update_timestamp);

		if ( 'list' == $args['style'] ) {
			$output .= "\t<li";
			$class = 'cat-item cat-item-' . $category->term_id;
			if ( !empty($current_category) ) {
				$_current_category = get_term( $current_category, $category->taxonomy );
				if ( $category->term_id == $current_category )
					$class .=  ' current-cat';
				elseif ( $category->term_id == $_current_category->parent )
					$class .=  ' current-cat-parent';
			}
			$output .=  ' class="' . $class . '"';
			$output .= ">$link\n";
		} else {
			$output .= "\t$link<br />\n";
		}
	}

	/**
	 * @see Walker::end_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Not used.
	 * @param int $depth Depth of category. Not used.
	 * @param array $args Only uses 'list' for whether should append to output.
	 */
	function end_el(&$output, $page, $depth, $args) {
		if ( 'list' != $args['style'] )
			return;

		$output .= "</li>\n";
	}

}

/**
 * Create HTML dropdown list of Categories.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_CategoryDropdown extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'category';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int $depth Depth of category. Used for padding.
	 * @param array $args Uses 'selected', 'show_count', and 'show_last_update' keys, if they exist.
	 */
	function start_el(&$output, $category, $depth, $args) {
		$pad = str_repeat('&nbsp;', $depth * 3);

		$cat_name = apply_filters('list_cats', $category->name, $category);
		$output .= "\t<option class=\"level-$depth\" value=\"".$category->term_id."\"";
		if ( $category->term_id == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad.$cat_name;
		if ( $args['show_count'] )
			$output .= '&nbsp;&nbsp;('. $category->count .')';
		if ( $args['show_last_update'] ) {
			$format = 'Y-m-d';
			$output .= '&nbsp;&nbsp;' . gmdate($format, $category->last_update_timestamp);
		}
		$output .= "</option>\n";
	}
}

/**
 * Send XML response back to AJAX request.
 *
 * @package WordPress
 * @since 2.1.0
 */
class WP_Ajax_Response {
	/**
	 * Store XML responses to send.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $responses = array();

	/**
	 * PHP4 Constructor - Passes args to {@link WP_Ajax_Response::add()}.
	 *
	 * @since 2.1.0
	 * @see WP_Ajax_Response::add()
	 *
	 * @param string|array $args Optional. Will be passed to add() method.
	 * @return WP_Ajax_Response
	 */
	function WP_Ajax_Response( $args = '' ) {
		if ( !empty($args) )
			$this->add($args);
	}

	/**
	 * Append to XML response based on given arguments.
	 *
	 * The arguments that can be passed in the $args parameter are below. It is
	 * also possible to pass a WP_Error object in either the 'id' or 'data'
	 * argument. The parameter isn't actually optional, content should be given
	 * in order to send the correct response.
	 *
	 * 'what' argument is a string that is the XMLRPC response type.
	 * 'action' argument is a boolean or string that acts like a nonce.
	 * 'id' argument can be WP_Error or an integer.
	 * 'old_id' argument is false by default or an integer of the previous ID.
	 * 'position' argument is an integer or a string with -1 = top, 1 = bottom,
	 * html ID = after, -html ID = before.
	 * 'data' argument is a string with the content or message.
	 * 'supplemental' argument is an array of strings that will be children of
	 * the supplemental element.
	 *
	 * @since 2.1.0
	 *
	 * @param string|array $args Override defaults.
	 * @return string XML response.
	 */
	function add( $args = '' ) {
		$defaults = array(
			'what' => 'object', 'action' => false,
			'id' => '0', 'old_id' => false,
			'position' => 1,
			'data' => '', 'supplemental' => array()
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
		$position = preg_replace( '/[^a-z0-9:_-]/i', '', $position );

		if ( is_wp_error($id) ) {
			$data = $id;
			$id = 0;
		}

		$response = '';
		if ( is_wp_error($data) ) {
			foreach ( (array) $data->get_error_codes() as $code ) {
				$response .= "<wp_error code='$code'><![CDATA[" . $data->get_error_message($code) . "]]></wp_error>";
				if ( !$error_data = $data->get_error_data($code) )
					continue;
				$class = '';
				if ( is_object($error_data) ) {
					$class = ' class="' . get_class($error_data) . '"';
					$error_data = get_object_vars($error_data);
				}

				$response .= "<wp_error_data code='$code'$class>";

				if ( is_scalar($error_data) ) {
					$response .= "<![CDATA[$error_data]]>";
				} elseif ( is_array($error_data) ) {
					foreach ( $error_data as $k => $v )
						$response .= "<$k><![CDATA[$v]]></$k>";
				}

				$response .= "</wp_error_data>";
			}
		} else {
			$response = "<response_data><![CDATA[$data]]></response_data>";
		}

		$s = '';
		if ( is_array($supplemental) ) {
			foreach ( $supplemental as $k => $v )
				$s .= "<$k><![CDATA[$v]]></$k>";
			$s = "<supplemental>$s</supplemental>";
		}

		if ( false === $action )
			$action = $_POST['action'];

		$x = '';
		$x .= "<response action='{$action}_$id'>"; // The action attribute in the xml output is formatted like a nonce action
		$x .=	"<$what id='$id' " . ( false === $old_id ? '' : "old_id='$old_id' " ) . "position='$position'>";
		$x .=		$response;
		$x .=		$s;
		$x .=	"</$what>";
		$x .= "</response>";

		$this->responses[] = $x;
		return $x;
	}

	/**
	 * Display XML formatted responses.
	 *
	 * Sets the content type header to text/xml.
	 *
	 * @since 2.1.0
	 */
	function send() {
		header('Content-Type: text/xml');
		echo "<?xml version='1.0' standalone='yes'?><wp_ajax>";
		foreach ( (array) $this->responses as $response )
			echo $response;
		echo '</wp_ajax>';
		die();
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
	 * @param array  $matches data used for subsitution
	 * @return string
	 */
	function apply($subject, $matches) {
		$oSelf =& new WP_MatchesMapRegex($subject, $matches);
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

?>

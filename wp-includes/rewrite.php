<?php
/**
 * WordPress Rewrite API
 *
 * @package WordPress
 * @subpackage Rewrite
 */

/**
 * Add a straight rewrite rule.
 *
 * @see WP_Rewrite::add_rule() for long description.
 * @since 2.1.0
 *
 * @param string $regex Regular Expression to match request against.
 * @param string $redirect Page to redirect to.
 * @param string $after Optional, default is 'bottom'. Where to add rule, can also be 'top'.
 */
function add_rewrite_rule($regex, $redirect, $after = 'bottom') {
	global $wp_rewrite;
	$wp_rewrite->add_rule($regex, $redirect, $after);
}

/**
 * Add a new tag (like %postname%).
 *
 * Warning: you must call this on init or earlier, otherwise the query var
 * addition stuff won't work.
 *
 * @since 2.1.0
 *
 * @param string $tagname
 * @param string $regex
 */
function add_rewrite_tag($tagname, $regex) {
	//validation
	if ( strlen($tagname) < 3 || $tagname{0} != '%' || $tagname{strlen($tagname)-1} != '%' )
		return;

	$qv = trim($tagname, '%');

	global $wp_rewrite, $wp;
	$wp->add_query_var($qv);
	$wp_rewrite->add_rewrite_tag($tagname, $regex, $qv . '=');
}

/**
 * Add permalink structure.
 *
 * @see WP_Rewrite::add_permastruct()
 * @since 3.0.0
 *
 * @param string $name Name for permalink structure.
 * @param string $struct Permalink structure.
 * @param bool $with_front Prepend front base to permalink structure.
 */
function add_permastruct( $name, $struct, $with_front = true ) {
	global $wp_rewrite;
	return $wp_rewrite->add_permastruct( $name, $struct, $with_front );
}

/**
 * Add a new feed type like /atom1/.
 *
 * @since 2.1.0
 *
 * @param string $feedname
 * @param callback $function Callback to run on feed display.
 * @return string Feed action name.
 */
function add_feed($feedname, $function) {
	global $wp_rewrite;
	if ( ! in_array($feedname, $wp_rewrite->feeds) ) //override the file if it is
		$wp_rewrite->feeds[] = $feedname;
	$hook = 'do_feed_' . $feedname;
	// Remove default function hook
	remove_action($hook, $hook, 10, 1);
	add_action($hook, $function, 10, 1);
	return $hook;
}

/**
 * Remove rewrite rules and then recreate rewrite rules.
 *
 * @see WP_Rewrite::flush_rules()
 * @since 3.0.0
 *
 * @param bool $hard Whether to update .htaccess (hard flush) or just update
 * 	rewrite_rules transient (soft flush). Default is true (hard).
 */
function flush_rewrite_rules( $hard = true ) {
	global $wp_rewrite;
	$wp_rewrite->flush_rules( $hard );
}

//pseudo-places
/**
 * Endpoint Mask for default, which is nothing.
 *
 * @since 2.1.0
 */
define('EP_NONE', 0);

/**
 * Endpoint Mask for Permalink.
 *
 * @since 2.1.0
 */
define('EP_PERMALINK', 1);

/**
 * Endpoint Mask for Attachment.
 *
 * @since 2.1.0
 */
define('EP_ATTACHMENT', 2);

/**
 * Endpoint Mask for date.
 *
 * @since 2.1.0
 */
define('EP_DATE', 4);

/**
 * Endpoint Mask for year
 *
 * @since 2.1.0
 */
define('EP_YEAR', 8);

/**
 * Endpoint Mask for month.
 *
 * @since 2.1.0
 */
define('EP_MONTH', 16);

/**
 * Endpoint Mask for day.
 *
 * @since 2.1.0
 */
define('EP_DAY', 32);

/**
 * Endpoint Mask for root.
 *
 * @since 2.1.0
 */
define('EP_ROOT', 64);

/**
 * Endpoint Mask for comments.
 *
 * @since 2.1.0
 */
define('EP_COMMENTS', 128);

/**
 * Endpoint Mask for searches.
 *
 * @since 2.1.0
 */
define('EP_SEARCH', 256);

/**
 * Endpoint Mask for categories.
 *
 * @since 2.1.0
 */
define('EP_CATEGORIES', 512);

/**
 * Endpoint Mask for tags.
 *
 * @since 2.3.0
 */
define('EP_TAGS', 1024);

/**
 * Endpoint Mask for authors.
 *
 * @since 2.1.0
 */
define('EP_AUTHORS', 2048);

/**
 * Endpoint Mask for pages.
 *
 * @since 2.1.0
 */
define('EP_PAGES', 4096);

/**
 * Endpoint Mask for everything.
 *
 * @since 2.1.0
 */
define('EP_ALL', 8191);

/**
 * Add an endpoint, like /trackback/.
 *
 * The endpoints are added to the end of the request. So a request matching
 * "/2008/10/14/my_post/myep/", the endpoint will be "/myep/".
 *
 * Be sure to flush the rewrite rules (wp_rewrite->flush()) when your plugin gets
 * activated (register_activation_hook()) and deactivated (register_deactivation_hook())
 *
 * @since 2.1.0
 * @see WP_Rewrite::add_endpoint() Parameters and more description.
 * @uses $wp_rewrite
 *
 * @param unknown_type $name
 * @param unknown_type $places
 */
function add_rewrite_endpoint($name, $places) {
	global $wp_rewrite;
	$wp_rewrite->add_endpoint($name, $places);
}

/**
 * Filter the URL base for taxonomies.
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
	if ( !empty( $base ) ) {
		$base = preg_replace( '|^/index\.php/|', '', $base );
		$base = trim( $base, '/' );
	}
	return $base;
}

/**
 * Examine a url and try to determine the post ID it represents.
 *
 * Checks are supposedly from the hosted site blog.
 *
 * @since 1.0.0
 *
 * @param string $url Permalink to check.
 * @return int Post ID, or 0 on failure.
 */
function url_to_postid($url) {
	global $wp_rewrite;

	$url = apply_filters('url_to_postid', $url);

	// First, check to see if there is a 'p=N' or 'page_id=N' to match against
	if ( preg_match('#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values) )	{
		$id = absint($values[2]);
		if ( $id )
			return $id;
	}

	// Check to see if we are using rewrite rules
	$rewrite = $wp_rewrite->wp_rewrite_rules();

	// Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options
	if ( empty($rewrite) )
		return 0;

	// $url cleanup by Mark Jaquith
	// This fixes things like #anchors, ?query=strings, missing 'www.',
	// added 'www.', or added 'index.php/' that will mess up our WP_Query
	// and return a false negative

	// Get rid of the #anchor
	$url_split = explode('#', $url);
	$url = $url_split[0];

	// Get rid of URL ?query=string
	$url_split = explode('?', $url);
	$url = $url_split[0];

	// Add 'www.' if it is absent and should be there
	if ( false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.') )
		$url = str_replace('://', '://www.', $url);

	// Strip 'www.' if it is present and shouldn't be
	if ( false === strpos(home_url(), '://www.') )
		$url = str_replace('://www.', '://', $url);

	// Strip 'index.php/' if we're not using path info permalinks
	if ( !$wp_rewrite->using_index_permalinks() )
		$url = str_replace('index.php/', '', $url);

	if ( false !== strpos($url, home_url()) ) {
		// Chop off http://domain.com
		$url = str_replace(home_url(), '', $url);
	} else {
		// Chop off /path/to/blog
		$home_path = parse_url(home_url());
		$home_path = $home_path['path'];
		$url = str_replace($home_path, '', $url);
	}

	// Trim leading and lagging slashes
	$url = trim($url, '/');

	$request = $url;

	// Done with cleanup

	// Look for matches.
	$request_match = $request;
	foreach ( (array)$rewrite as $match => $query) {
		// If the requesting file is the anchor of the match, prepend it
		// to the path info.
		if ( !empty($url) && ($url != $request) && (strpos($match, $url) === 0) )
			$request_match = $url . '/' . $request;

		if ( preg_match("!^$match!", $request_match, $matches) ) {
			// Got a match.
			// Trim the query of everything up to the '?'.
			$query = preg_replace("!^.+\?!", '', $query);

			// Substitute the substring matches into the query.
			$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

			// Filter out non-public query vars
			global $wp;
			parse_str($query, $query_vars);
			$query = array();
			foreach ( (array) $query_vars as $key => $value ) {
				if ( in_array($key, $wp->public_query_vars) )
					$query[$key] = $value;
			}

			// Do the query
			$query = new WP_Query($query);
			if ( $query->is_single || $query->is_page )
				return $query->post->ID;
			else
				return 0;
		}
	}
	return 0;
}

/**
 * WordPress Rewrite Component.
 *
 * The WordPress Rewrite class writes the rewrite module rules to the .htaccess
 * file. It also handles parsing the request to get the correct setup for the
 * WordPress Query class.
 *
 * The Rewrite along with WP class function as a front controller for WordPress.
 * You can add rules to trigger your page view and processing using this
 * component. The full functionality of a front controller does not exist,
 * meaning you can't define how the template files load based on the rewrite
 * rules.
 *
 * @since 1.5.0
 */
class WP_Rewrite {
	/**
	 * Default permalink structure for WordPress.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $permalink_structure;

	/**
	 * Whether to add trailing slashes.
	 *
	 * @since 2.2.0
	 * @access private
	 * @var bool
	 */
	var $use_trailing_slashes;

	/**
	 * Customized or default category permalink base ( example.com/xx/tagname ).
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $category_base;

	/**
	 * Customized or default tag permalink base ( example.com/xx/tagname ).
	 *
	 * @since 2.3.0
	 * @access private
	 * @var string
	 */
	var $tag_base;

	/**
	 * Permalink request structure for categories.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $category_structure;

	/**
	 * Permalink request structure for tags.
	 *
	 * @since 2.3.0
	 * @access private
	 * @var string
	 */
	var $tag_structure;

	/**
	 * Permalink author request base ( example.com/author/authorname ).
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $author_base = 'author';

	/**
	 * Permalink request structure for author pages.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $author_structure;

	/**
	 * Permalink request structure for dates.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $date_structure;

	/**
	 * Permalink request structure for pages.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $page_structure;

	/**
	 * Search permalink base ( example.com/search/query ).
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $search_base = 'search';

	/**
	 * Permalink request structure for searches.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $search_structure;

	/**
	 * Comments permalink base.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $comments_base = 'comments';

	/**
	 * Feed permalink base.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $feed_base = 'feed';

	/**
	 * Comments feed request structure permalink.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $comments_feed_structure;

	/**
	 * Feed request structure permalink.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $feed_structure;

	/**
	 * Front URL path.
	 *
	 * The difference between the root property is that WordPress might be
	 * located at example/WordPress/index.php, if permalinks are turned off. The
	 * WordPress/index.php will be the front portion. If permalinks are turned
	 * on, this will most likely be empty or not set.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $front;

	/**
	 * Root URL path to WordPress (without domain).
	 *
	 * The difference between front property is that WordPress might be located
	 * at example.com/WordPress/. The root is the 'WordPress/' portion.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $root = '';

	/**
	 * Permalink to the home page.
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	var $index = 'index.php';

	/**
	 * Request match string.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var string
	 */
	var $matches = '';

	/**
	 * Rewrite rules to match against the request to find the redirect or query.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var array
	 */
	var $rules;

	/**
	 * Additional rules added external to the rewrite class.
	 *
	 * Those not generated by the class, see add_rewrite_rule().
	 *
	 * @since 2.1.0
	 * @access private
	 * @var array
	 */
	var $extra_rules = array(); //

	/**
	 * Additional rules that belong at the beginning to match first.
	 *
	 * Those not generated by the class, see add_rewrite_rule().
	 *
	 * @since 2.3.0
	 * @access private
	 * @var array
	 */
	var $extra_rules_top = array(); //

	/**
	 * Rules that don't redirect to WP's index.php.
	 *
	 * These rules are written to the mod_rewrite portion of the .htaccess.
	 *
	 * @since 2.1.0
	 * @access private
	 * @var array
	 */
	var $non_wp_rules = array(); //

	/**
	 * Extra permalink structures.
	 *
	 * @since 2.1.0
	 * @access private
	 * @var array
	 */
	var $extra_permastructs = array();

	/**
	 * Endpoints permalinks
	 *
	 * @since unknown
	 * @access private
	 * @var array
	 */
	var $endpoints;

	/**
	 * Whether to write every mod_rewrite rule for WordPress.
	 *
	 * This is off by default, turning it on might print a lot of rewrite rules
	 * to the .htaccess file.
	 *
	 * @since 2.0.0
	 * @access public
	 * @var bool
	 */
	var $use_verbose_rules = false;

	/**
	 * Whether to write every mod_rewrite rule for WordPress pages.
	 *
	 * @since 2.5.0
	 * @access public
	 * @var bool
	 */
	var $use_verbose_page_rules = true;

	/**
	 * Permalink structure search for preg_replace.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var array
	 */
	var $rewritecode =
		array(
					'%year%',
					'%monthnum%',
					'%day%',
					'%hour%',
					'%minute%',
					'%second%',
					'%postname%',
					'%post_id%',
					'%category%',
					'%tag%',
					'%author%',
					'%pagename%',
					'%search%'
					);

	/**
	 * Preg_replace values for the search, see {@link WP_Rewrite::$rewritecode}.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var array
	 */
	var $rewritereplace =
		array(
					'([0-9]{4})',
					'([0-9]{1,2})',
					'([0-9]{1,2})',
					'([0-9]{1,2})',
					'([0-9]{1,2})',
					'([0-9]{1,2})',
					'([^/]+)',
					'([0-9]+)',
					'(.+?)',
					'(.+?)',
					'([^/]+)',
					'([^/]+?)',
					'(.+)'
					);

	/**
	 * Search for the query to look for replacing.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var array
	 */
	var $queryreplace =
		array (
					'year=',
					'monthnum=',
					'day=',
					'hour=',
					'minute=',
					'second=',
					'name=',
					'p=',
					'category_name=',
					'tag=',
					'author_name=',
					'pagename=',
					's='
					);

	/**
	 * Supported default feeds.
	 *
	 * @since 1.5.0
	 * @access private
	 * @var array
	 */
	var $feeds = array ( 'feed', 'rdf', 'rss', 'rss2', 'atom' );

	/**
	 * Whether permalinks are being used.
	 *
	 * This can be either rewrite module or permalink in the HTTP query string.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return bool True, if permalinks are enabled.
	 */
	function using_permalinks() {
		return ! empty($this->permalink_structure);
	}

	/**
	 * Whether permalinks are being used and rewrite module is not enabled.
	 *
	 * Means that permalink links are enabled and index.php is in the URL.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return bool
	 */
	function using_index_permalinks() {
		if ( empty($this->permalink_structure) )
			return false;

		// If the index is not in the permalink, we're using mod_rewrite.
		if ( preg_match('#^/*' . $this->index . '#', $this->permalink_structure) )
			return true;

		return false;
	}

	/**
	 * Whether permalinks are being used and rewrite module is enabled.
	 *
	 * Using permalinks and index.php is not in the URL.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return bool
	 */
	function using_mod_rewrite_permalinks() {
		if ( $this->using_permalinks() && ! $this->using_index_permalinks() )
			return true;
		else
			return false;
	}

	/**
	 * Index for matches for usage in preg_*() functions.
	 *
	 * The format of the string is, with empty matches property value, '$NUM'.
	 * The 'NUM' will be replaced with the value in the $number parameter. With
	 * the matches property not empty, the value of the returned string will
	 * contain that value of the matches property. The format then will be
	 * '$MATCHES[NUM]', with MATCHES as the value in the property and NUM the
	 * value of the $number parameter.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param int $number Index number.
	 * @return string
	 */
	function preg_index($number) {
		$match_prefix = '$';
		$match_suffix = '';

		if ( ! empty($this->matches) ) {
			$match_prefix = '$' . $this->matches . '[';
			$match_suffix = ']';
		}

		return "$match_prefix$number$match_suffix";
	}

	/**
	 * Retrieve all page and attachments for pages URIs.
	 *
	 * The attachments are for those that have pages as parents and will be
	 * retrieved.
	 *
	 * @since 2.5.0
	 * @access public
	 *
	 * @return array Array of page URIs as first element and attachment URIs as second element.
	 */
	function page_uri_index() {
		global $wpdb;

		//get pages in order of hierarchy, i.e. children after parents
		$posts = get_page_hierarchy($wpdb->get_results("SELECT ID, post_name, post_parent FROM $wpdb->posts WHERE post_type = 'page'"));

		// If we have no pages get out quick
		if ( !$posts )
			return array( array(), array() );

		//now reverse it, because we need parents after children for rewrite rules to work properly
		$posts = array_reverse($posts, true);

		$page_uris = array();
		$page_attachment_uris = array();

		foreach ( $posts as $id => $post ) {
			// URL => page name
			$uri = get_page_uri($id);
			$attachments = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_name, post_parent FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = %d", $id ));
			if ( !empty($attachments) ) {
				foreach ( $attachments as $attachment ) {
					$attach_uri = get_page_uri($attachment->ID);
					$page_attachment_uris[$attach_uri] = $attachment->ID;
				}
			}

			$page_uris[$uri] = $id;
		}

		return array( $page_uris, $page_attachment_uris );
	}

	/**
	 * Retrieve all of the rewrite rules for pages.
	 *
	 * If the 'use_verbose_page_rules' property is false, then there will only
	 * be a single rewrite rule for pages for those matching '%pagename%'. With
	 * the property set to true, the attachments and the pages will be added for
	 * each individual attachment URI and page URI, respectively.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array
	 */
	function page_rewrite_rules() {
		$rewrite_rules = array();
		$page_structure = $this->get_page_permastruct();

		if ( ! $this->use_verbose_page_rules ) {
			$this->add_rewrite_tag('%pagename%', "(.+?)", 'pagename=');
			$rewrite_rules = array_merge($rewrite_rules, $this->generate_rewrite_rules($page_structure, EP_PAGES));
			return $rewrite_rules;
		}

		$page_uris = $this->page_uri_index();
		$uris = $page_uris[0];
		$attachment_uris = $page_uris[1];

		if ( is_array( $attachment_uris ) ) {
			foreach ( $attachment_uris as $uri => $pagename ) {
				$this->add_rewrite_tag('%pagename%', "($uri)", 'attachment=');
				$rewrite_rules = array_merge($rewrite_rules, $this->generate_rewrite_rules($page_structure, EP_PAGES));
			}
		}
		if ( is_array( $uris ) ) {
			foreach ( $uris as $uri => $pagename ) {
				$this->add_rewrite_tag('%pagename%', "($uri)", 'pagename=');
				$rewrite_rules = array_merge($rewrite_rules, $this->generate_rewrite_rules($page_structure, EP_PAGES));
			}
		}

		return $rewrite_rules;
	}

	/**
	 * Retrieve date permalink structure, with year, month, and day.
	 *
	 * The permalink structure for the date, if not set already depends on the
	 * permalink structure. It can be one of three formats. The first is year,
	 * month, day; the second is day, month, year; and the last format is month,
	 * day, year. These are matched against the permalink structure for which
	 * one is used. If none matches, then the default will be used, which is
	 * year, month, day.
	 *
	 * Prevents post ID and date permalinks from overlapping. In the case of
	 * post_id, the date permalink will be prepended with front permalink with
	 * 'date/' before the actual permalink to form the complete date permalink
	 * structure.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return bool|string False on no permalink structure. Date permalink structure.
	 */
	function get_date_permastruct() {
		if ( isset($this->date_structure) )
			return $this->date_structure;

		if ( empty($this->permalink_structure) ) {
			$this->date_structure = '';
			return false;
		}

		// The date permalink must have year, month, and day separated by slashes.
		$endians = array('%year%/%monthnum%/%day%', '%day%/%monthnum%/%year%', '%monthnum%/%day%/%year%');

		$this->date_structure = '';
		$date_endian = '';

		foreach ( $endians as $endian ) {
			if ( false !== strpos($this->permalink_structure, $endian) ) {
				$date_endian= $endian;
				break;
			}
		}

		if ( empty($date_endian) )
			$date_endian = '%year%/%monthnum%/%day%';

		// Do not allow the date tags and %post_id% to overlap in the permalink
		// structure. If they do, move the date tags to $front/date/.
		$front = $this->front;
		preg_match_all('/%.+?%/', $this->permalink_structure, $tokens);
		$tok_index = 1;
		foreach ( (array) $tokens[0] as $token) {
			if ( '%post_id%' == $token && ($tok_index <= 3) ) {
				$front = $front . 'date/';
				break;
			}
			$tok_index++;
		}

		$this->date_structure = $front . $date_endian;

		return $this->date_structure;
	}

	/**
	 * Retrieve the year permalink structure without month and day.
	 *
	 * Gets the date permalink structure and strips out the month and day
	 * permalink structures.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return bool|string False on failure. Year structure on success.
	 */
	function get_year_permastruct() {
		$structure = $this->get_date_permastruct($this->permalink_structure);

		if ( empty($structure) )
			return false;

		$structure = str_replace('%monthnum%', '', $structure);
		$structure = str_replace('%day%', '', $structure);

		$structure = preg_replace('#/+#', '/', $structure);

		return $structure;
	}

	/**
	 * Retrieve the month permalink structure without day and with year.
	 *
	 * Gets the date permalink structure and strips out the day permalink
	 * structures. Keeps the year permalink structure.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return bool|string False on failure. Year/Month structure on success.
	 */
	function get_month_permastruct() {
		$structure = $this->get_date_permastruct($this->permalink_structure);

		if ( empty($structure) )
			return false;

		$structure = str_replace('%day%', '', $structure);

		$structure = preg_replace('#/+#', '/', $structure);

		return $structure;
	}

	/**
	 * Retrieve the day permalink structure with month and year.
	 *
	 * Keeps date permalink structure with all year, month, and day.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return bool|string False on failure. Year/Month/Day structure on success.
	 */
	function get_day_permastruct() {
		return $this->get_date_permastruct($this->permalink_structure);
	}

	/**
	 * Retrieve the permalink structure for categories.
	 *
	 * If the category_base property has no value, then the category structure
	 * will have the front property value, followed by 'category', and finally
	 * '%category%'. If it does, then the root property will be used, along with
	 * the category_base property value.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return bool|string False on failure. Category permalink structure.
	 */
	function get_category_permastruct() {
		if ( isset($this->category_structure) )
			return $this->category_structure;

		if ( empty($this->permalink_structure) ) {
			$this->category_structure = '';
			return false;
		}

		if ( empty($this->category_base) )
			$this->category_structure = trailingslashit( $this->front . 'category' );
		else
			$this->category_structure = trailingslashit( '/' . $this->root . $this->category_base );

		$this->category_structure .= '%category%';

		return $this->category_structure;
	}

	/**
	 * Retrieve the permalink structure for tags.
	 *
	 * If the tag_base property has no value, then the tag structure will have
	 * the front property value, followed by 'tag', and finally '%tag%'. If it
	 * does, then the root property will be used, along with the tag_base
	 * property value.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return bool|string False on failure. Tag permalink structure.
	 */
	function get_tag_permastruct() {
		if ( isset($this->tag_structure) )
			return $this->tag_structure;

		if ( empty($this->permalink_structure) ) {
			$this->tag_structure = '';
			return false;
		}

		if ( empty($this->tag_base) )
			$this->tag_structure = trailingslashit( $this->front . 'tag' );
		else
			$this->tag_structure = trailingslashit( '/' . $this->root . $this->tag_base );

		$this->tag_structure .= '%tag%';

		return $this->tag_structure;
	}

	/**
	 * Retrieve extra permalink structure by name.
	 *
	 * @since unknown
	 * @access public
	 *
	 * @param string $name Permalink structure name.
	 * @return string|bool False if not found. Permalink structure string.
	 */
	function get_extra_permastruct($name) {
		if ( empty($this->permalink_structure) )
			return false;

		if ( isset($this->extra_permastructs[$name]) )
			return $this->extra_permastructs[$name];

		return false;
	}

	/**
	 * Retrieve the author permalink structure.
	 *
	 * The permalink structure is front property, author base, and finally
	 * '/%author%'. Will set the author_structure property and then return it
	 * without attempting to set the value again.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return string|bool False if not found. Permalink structure string.
	 */
	function get_author_permastruct() {
		if ( isset($this->author_structure) )
			return $this->author_structure;

		if ( empty($this->permalink_structure) ) {
			$this->author_structure = '';
			return false;
		}

		$this->author_structure = $this->front . $this->author_base . '/%author%';

		return $this->author_structure;
	}

	/**
	 * Retrieve the search permalink structure.
	 *
	 * The permalink structure is root property, search base, and finally
	 * '/%search%'. Will set the search_structure property and then return it
	 * without attempting to set the value again.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return string|bool False if not found. Permalink structure string.
	 */
	function get_search_permastruct() {
		if ( isset($this->search_structure) )
			return $this->search_structure;

		if ( empty($this->permalink_structure) ) {
			$this->search_structure = '';
			return false;
		}

		$this->search_structure = $this->root . $this->search_base . '/%search%';

		return $this->search_structure;
	}

	/**
	 * Retrieve the page permalink structure.
	 *
	 * The permalink structure is root property, and '%pagename%'. Will set the
	 * page_structure property and then return it without attempting to set the
	 * value again.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return string|bool False if not found. Permalink structure string.
	 */
	function get_page_permastruct() {
		if ( isset($this->page_structure) )
			return $this->page_structure;

		if (empty($this->permalink_structure)) {
			$this->page_structure = '';
			return false;
		}

		$this->page_structure = $this->root . '%pagename%';

		return $this->page_structure;
	}

	/**
	 * Retrieve the feed permalink structure.
	 *
	 * The permalink structure is root property, feed base, and finally
	 * '/%feed%'. Will set the feed_structure property and then return it
	 * without attempting to set the value again.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return string|bool False if not found. Permalink structure string.
	 */
	function get_feed_permastruct() {
		if ( isset($this->feed_structure) )
			return $this->feed_structure;

		if ( empty($this->permalink_structure) ) {
			$this->feed_structure = '';
			return false;
		}

		$this->feed_structure = $this->root . $this->feed_base . '/%feed%';

		return $this->feed_structure;
	}

	/**
	 * Retrieve the comment feed permalink structure.
	 *
	 * The permalink structure is root property, comment base property, feed
	 * base and finally '/%feed%'. Will set the comment_feed_structure property
	 * and then return it without attempting to set the value again.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return string|bool False if not found. Permalink structure string.
	 */
	function get_comment_feed_permastruct() {
		if ( isset($this->comment_feed_structure) )
			return $this->comment_feed_structure;

		if (empty($this->permalink_structure)) {
			$this->comment_feed_structure = '';
			return false;
		}

		$this->comment_feed_structure = $this->root . $this->comments_base . '/' . $this->feed_base . '/%feed%';

		return $this->comment_feed_structure;
	}

	/**
	 * Append or update tag, pattern, and query for replacement.
	 *
	 * If the tag already exists, replace the existing pattern and query for
	 * that tag, otherwise add the new tag, pattern, and query to the end of the
	 * arrays.
	 *
	 * @internal What is the purpose of this function again? Need to finish long
	 *           description.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $tag Append tag to rewritecode property array.
	 * @param string $pattern Append pattern to rewritereplace property array.
	 * @param string $query Append query to queryreplace property array.
	 */
	function add_rewrite_tag($tag, $pattern, $query) {
		$position = array_search($tag, $this->rewritecode);
		if ( false !== $position && null !== $position ) {
			$this->rewritereplace[$position] = $pattern;
			$this->queryreplace[$position] = $query;
		} else {
			$this->rewritecode[] = $tag;
			$this->rewritereplace[] = $pattern;
			$this->queryreplace[] = $query;
		}
	}

	/**
	 * Generate the rules from permalink structure.
	 *
	 * The main WP_Rewrite function for building the rewrite rule list. The
	 * contents of the function is a mix of black magic and regular expressions,
	 * so best just ignore the contents and move to the parameters.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $permalink_structure The permalink structure.
	 * @param int $ep_mask Optional, default is EP_NONE. Endpoint constant, see EP_* constants.
	 * @param bool $paged Optional, default is true. Whether permalink request is paged.
	 * @param bool $feed Optional, default is true. Whether for feed.
	 * @param bool $forcomments Optional, default is false. Whether for comments.
	 * @param bool $walk_dirs Optional, default is true. Whether to create list of directories to walk over.
	 * @param bool $endpoints Optional, default is true. Whether endpoints are enabled.
	 * @return array Rewrite rule list.
	 */
	function generate_rewrite_rules($permalink_structure, $ep_mask = EP_NONE, $paged = true, $feed = true, $forcomments = false, $walk_dirs = true, $endpoints = true) {
		//build a regex to match the feed section of URLs, something like (feed|atom|rss|rss2)/?
		$feedregex2 = '';
		foreach ( (array) $this->feeds as $feed_name)
			$feedregex2 .= $feed_name . '|';
		$feedregex2 = '(' . trim($feedregex2, '|') .  ')/?$';

		//$feedregex is identical but with /feed/ added on as well, so URLs like <permalink>/feed/atom
		//and <permalink>/atom are both possible
		$feedregex = $this->feed_base  . '/' . $feedregex2;

		//build a regex to match the trackback and page/xx parts of URLs
		$trackbackregex = 'trackback/?$';
		$pageregex = 'page/?([0-9]{1,})/?$';
		$commentregex = 'comment-page-([0-9]{1,})/?$';

		//build up an array of endpoint regexes to append => queries to append
		if ( $endpoints ) {
			$ep_query_append = array ();
			foreach ( (array) $this->endpoints as $endpoint) {
				//match everything after the endpoint name, but allow for nothing to appear there
				$epmatch = $endpoint[1] . '(/(.*))?/?$';
				//this will be appended on to the rest of the query for each dir
				$epquery = '&' . $endpoint[1] . '=';
				$ep_query_append[$epmatch] = array ( $endpoint[0], $epquery );
			}
		}

		//get everything up to the first rewrite tag
		$front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));
		//build an array of the tags (note that said array ends up being in $tokens[0])
		preg_match_all('/%.+?%/', $permalink_structure, $tokens);

		$num_tokens = count($tokens[0]);

		$index = $this->index; //probably 'index.php'
		$feedindex = $index;
		$trackbackindex = $index;
		//build a list from the rewritecode and queryreplace arrays, that will look something like
		//tagname=$matches[i] where i is the current $i
		for ( $i = 0; $i < $num_tokens; ++$i ) {
			if ( 0 < $i )
				$queries[$i] = $queries[$i - 1] . '&';
			else
				$queries[$i] = '';

			$query_token = str_replace($this->rewritecode, $this->queryreplace, $tokens[0][$i]) . $this->preg_index($i+1);
			$queries[$i] .= $query_token;
		}

		//get the structure, minus any cruft (stuff that isn't tags) at the front
		$structure = $permalink_structure;
		if ( $front != '/' )
			$structure = str_replace($front, '', $structure);

		//create a list of dirs to walk over, making rewrite rules for each level
		//so for example, a $structure of /%year%/%month%/%postname% would create
		//rewrite rules for /%year%/, /%year%/%month%/ and /%year%/%month%/%postname%
		$structure = trim($structure, '/');
		$dirs = $walk_dirs ? explode('/', $structure) : array( $structure );
		$num_dirs = count($dirs);

		//strip slashes from the front of $front
		$front = preg_replace('|^/+|', '', $front);

		//the main workhorse loop
		$post_rewrite = array();
		$struct = $front;
		for ( $j = 0; $j < $num_dirs; ++$j ) {
			//get the struct for this dir, and trim slashes off the front
			$struct .= $dirs[$j] . '/'; //accumulate. see comment near explode('/', $structure) above
			$struct = ltrim($struct, '/');

			//replace tags with regexes
			$match = str_replace($this->rewritecode, $this->rewritereplace, $struct);

			//make a list of tags, and store how many there are in $num_toks
			$num_toks = preg_match_all('/%.+?%/', $struct, $toks);

			//get the 'tagname=$matches[i]'
			$query = ( isset($queries) && is_array($queries) ) ? $queries[$num_toks - 1] : '';

			//set up $ep_mask_specific which is used to match more specific URL types
			switch ( $dirs[$j] ) {
				case '%year%':
					$ep_mask_specific = EP_YEAR;
					break;
				case '%monthnum%':
					$ep_mask_specific = EP_MONTH;
					break;
				case '%day%':
					$ep_mask_specific = EP_DAY;
					break;
			}

			//create query for /page/xx
			$pagematch = $match . $pageregex;
			$pagequery = $index . '?' . $query . '&paged=' . $this->preg_index($num_toks + 1);

			//create query for /comment-page-xx
			$commentmatch = $match . $commentregex;
			$commentquery = $index . '?' . $query . '&cpage=' . $this->preg_index($num_toks + 1);

			if ( get_option('page_on_front') ) {
				//create query for Root /comment-page-xx
				$rootcommentmatch = $match . $commentregex;
				$rootcommentquery = $index . '?' . $query . '&page_id=' . get_option('page_on_front') . '&cpage=' . $this->preg_index($num_toks + 1);
			}

			//create query for /feed/(feed|atom|rss|rss2|rdf)
			$feedmatch = $match . $feedregex;
			$feedquery = $feedindex . '?' . $query . '&feed=' . $this->preg_index($num_toks + 1);

			//create query for /(feed|atom|rss|rss2|rdf) (see comment near creation of $feedregex)
			$feedmatch2 = $match . $feedregex2;
			$feedquery2 = $feedindex . '?' . $query . '&feed=' . $this->preg_index($num_toks + 1);

			//if asked to, turn the feed queries into comment feed ones
			if ( $forcomments ) {
				$feedquery .= '&withcomments=1';
				$feedquery2 .= '&withcomments=1';
			}

			//start creating the array of rewrites for this dir
			$rewrite = array();
			if ( $feed ) //...adding on /feed/ regexes => queries
				$rewrite = array($feedmatch => $feedquery, $feedmatch2 => $feedquery2);
			if ( $paged ) //...and /page/xx ones
				$rewrite = array_merge($rewrite, array($pagematch => $pagequery));

			//only on pages with comments add ../comment-page-xx/
			if ( EP_PAGES & $ep_mask || EP_PERMALINK & $ep_mask || EP_NONE & $ep_mask )
				$rewrite = array_merge($rewrite, array($commentmatch => $commentquery));
			else if ( EP_ROOT & $ep_mask && get_option('page_on_front') )
				$rewrite = array_merge($rewrite, array($rootcommentmatch => $rootcommentquery));

			//do endpoints
			if ( $endpoints ) {
				foreach ( (array) $ep_query_append as $regex => $ep) {
					//add the endpoints on if the mask fits
					if ( $ep[0] & $ep_mask || $ep[0] & $ep_mask_specific )
						$rewrite[$match . $regex] = $index . '?' . $query . $ep[1] . $this->preg_index($num_toks + 2);
				}
			}

			//if we've got some tags in this dir
			if ( $num_toks ) {
				$post = false;
				$page = false;

				//check to see if this dir is permalink-level: i.e. the structure specifies an
				//individual post. Do this by checking it contains at least one of 1) post name,
				//2) post ID, 3) page name, 4) timestamp (year, month, day, hour, second and
				//minute all present). Set these flags now as we need them for the endpoints.
				if ( strpos($struct, '%postname%') !== false
						|| strpos($struct, '%post_id%') !== false
						|| strpos($struct, '%pagename%') !== false
						|| (strpos($struct, '%year%') !== false && strpos($struct, '%monthnum%') !== false && strpos($struct, '%day%') !== false && strpos($struct, '%hour%') !== false && strpos($struct, '%minute%') !== false && strpos($struct, '%second%') !== false)
						) {
					$post = true;
					if ( strpos($struct, '%pagename%') !== false )
						$page = true;
				}

				if ( ! $post ) {
					// For custom post types, we need to add on endpoints as well.
					foreach ( get_post_types( array('_builtin' => false ) ) as $ptype ) {
						if ( strpos($struct, "%$ptype%") !== false ) {
							$post = true;
							$page = false;
							break;
						}
					}
				}

				//if we're creating rules for a permalink, do all the endpoints like attachments etc
				if ( $post ) {
					//create query and regex for trackback
					$trackbackmatch = $match . $trackbackregex;
					$trackbackquery = $trackbackindex . '?' . $query . '&tb=1';
					//trim slashes from the end of the regex for this dir
					$match = rtrim($match, '/');
					//get rid of brackets
					$submatchbase = str_replace( array('(', ')'), '', $match);

					//add a rule for at attachments, which take the form of <permalink>/some-text
					$sub1 = $submatchbase . '/([^/]+)/';
					$sub1tb = $sub1 . $trackbackregex; //add trackback regex <permalink>/trackback/...
					$sub1feed = $sub1 . $feedregex; //and <permalink>/feed/(atom|...)
					$sub1feed2 = $sub1 . $feedregex2; //and <permalink>/(feed|atom...)
					$sub1comment = $sub1 . $commentregex; //and <permalink>/comment-page-xx
					//add an ? as we don't have to match that last slash, and finally a $ so we
					//match to the end of the URL

					//add another rule to match attachments in the explicit form:
					//<permalink>/attachment/some-text
					$sub2 = $submatchbase . '/attachment/([^/]+)/';
					$sub2tb = $sub2 . $trackbackregex; //and add trackbacks <permalink>/attachment/trackback
					$sub2feed = $sub2 . $feedregex;    //feeds, <permalink>/attachment/feed/(atom|...)
					$sub2feed2 = $sub2 . $feedregex2;  //and feeds again on to this <permalink>/attachment/(feed|atom...)
					$sub2comment = $sub2 . $commentregex; //and <permalink>/comment-page-xx

					//create queries for these extra tag-ons we've just dealt with
					$subquery = $index . '?attachment=' . $this->preg_index(1);
					$subtbquery = $subquery . '&tb=1';
					$subfeedquery = $subquery . '&feed=' . $this->preg_index(2);
					$subcommentquery = $subquery . '&cpage=' . $this->preg_index(2);

					//do endpoints for attachments
					if ( !empty($endpoints) ) {
						foreach ( (array) $ep_query_append as $regex => $ep ) {
							if ( $ep[0] & EP_ATTACHMENT ) {
								$rewrite[$sub1 . $regex] = $subquery . $ep[1] . $this->preg_index(2);
								$rewrite[$sub2 . $regex] = $subquery . $ep[1] . $this->preg_index(2);
							}
						}
					}

					//now we've finished with endpoints, finish off the $sub1 and $sub2 matches
					$sub1 .= '?$';
					$sub2 .= '?$';

					//allow URLs like <permalink>/2 for <permalink>/page/2
					$match = $match . '(/[0-9]+)?/?$';
					$query = $index . '?' . $query . '&page=' . $this->preg_index($num_toks + 1);
				} else { //not matching a permalink so this is a lot simpler
					//close the match and finalise the query
					$match .= '?$';
					$query = $index . '?' . $query;
				}

				//create the final array for this dir by joining the $rewrite array (which currently
				//only contains rules/queries for trackback, pages etc) to the main regex/query for
				//this dir
				$rewrite = array_merge($rewrite, array($match => $query));

				//if we're matching a permalink, add those extras (attachments etc) on
				if ( $post ) {
					//add trackback
					$rewrite = array_merge(array($trackbackmatch => $trackbackquery), $rewrite);

					//add regexes/queries for attachments, attachment trackbacks and so on
					if ( ! $page ) //require <permalink>/attachment/stuff form for pages because of confusion with subpages
						$rewrite = array_merge($rewrite, array($sub1 => $subquery, $sub1tb => $subtbquery, $sub1feed => $subfeedquery, $sub1feed2 => $subfeedquery, $sub1comment => $subcommentquery));
					$rewrite = array_merge(array($sub2 => $subquery, $sub2tb => $subtbquery, $sub2feed => $subfeedquery, $sub2feed2 => $subfeedquery, $sub2comment => $subcommentquery), $rewrite);
				}
			} //if($num_toks)
			//add the rules for this dir to the accumulating $post_rewrite
			$post_rewrite = array_merge($rewrite, $post_rewrite);
		} //foreach ($dir)
		return $post_rewrite; //the finished rules. phew!
	}

	/**
	 * Generate Rewrite rules with permalink structure and walking directory only.
	 *
	 * Shorten version of {@link WP_Rewrite::generate_rewrite_rules()} that
	 * allows for shorter list of parameters. See the method for longer
	 * description of what generating rewrite rules does.
	 *
	 * @uses WP_Rewrite::generate_rewrite_rules() See for long description and rest of parameters.
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $permalink_structure The permalink structure to generate rules.
	 * @param bool $walk_dirs Optional, default is false. Whether to create list of directories to walk over.
	 * @return array
	 */
	function generate_rewrite_rule($permalink_structure, $walk_dirs = false) {
		return $this->generate_rewrite_rules($permalink_structure, EP_NONE, false, false, false, $walk_dirs);
	}

	/**
	 * Construct rewrite matches and queries from permalink structure.
	 *
	 * Runs the action 'generate_rewrite_rules' with the parameter that is an
	 * reference to the current WP_Rewrite instance to further manipulate the
	 * permalink structures and rewrite rules. Runs the 'rewrite_rules_array'
	 * filter on the full rewrite rule array.
	 *
	 * There are two ways to manipulate the rewrite rules, one by hooking into
	 * the 'generate_rewrite_rules' action and gaining full control of the
	 * object or just manipulating the rewrite rule array before it is passed
	 * from the function.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array An associate array of matches and queries.
	 */
	function rewrite_rules() {
		$rewrite = array();

		if ( empty($this->permalink_structure) )
			return $rewrite;

		// robots.txt
		$robots_rewrite = array('robots\.txt$' => $this->index . '?robots=1');

		//Default Feed rules - These are require to allow for the direct access files to work with permalink structure starting with %category%
		$default_feeds = array(	'.*wp-atom.php$'	=>	$this->index . '?feed=atom',
								'.*wp-rdf.php$'		=>	$this->index . '?feed=rdf',
								'.*wp-rss.php$'		=>	$this->index . '?feed=rss',
								'.*wp-rss2.php$'	=>	$this->index . '?feed=rss2',
								'.*wp-feed.php$'	=>	$this->index . '?feed=feed',
								'.*wp-commentsrss2.php$'	=>	$this->index . '?feed=rss2&withcomments=1');

		// Post
		$post_rewrite = $this->generate_rewrite_rules($this->permalink_structure, EP_PERMALINK);
		$post_rewrite = apply_filters('post_rewrite_rules', $post_rewrite);

		// Date
		$date_rewrite = $this->generate_rewrite_rules($this->get_date_permastruct(), EP_DATE);
		$date_rewrite = apply_filters('date_rewrite_rules', $date_rewrite);

		// Root
		$root_rewrite = $this->generate_rewrite_rules($this->root . '/', EP_ROOT);
		$root_rewrite = apply_filters('root_rewrite_rules', $root_rewrite);

		// Comments
		$comments_rewrite = $this->generate_rewrite_rules($this->root . $this->comments_base, EP_COMMENTS, true, true, true, false);
		$comments_rewrite = apply_filters('comments_rewrite_rules', $comments_rewrite);

		// Search
		$search_structure = $this->get_search_permastruct();
		$search_rewrite = $this->generate_rewrite_rules($search_structure, EP_SEARCH);
		$search_rewrite = apply_filters('search_rewrite_rules', $search_rewrite);

		// Categories
		$category_rewrite = $this->generate_rewrite_rules($this->get_category_permastruct(), EP_CATEGORIES);
		$category_rewrite = apply_filters('category_rewrite_rules', $category_rewrite);

		// Tags
		$tag_rewrite = $this->generate_rewrite_rules($this->get_tag_permastruct(), EP_TAGS);
		$tag_rewrite = apply_filters('tag_rewrite_rules', $tag_rewrite);

		// Authors
		$author_rewrite = $this->generate_rewrite_rules($this->get_author_permastruct(), EP_AUTHORS);
		$author_rewrite = apply_filters('author_rewrite_rules', $author_rewrite);

		// Pages
		$page_rewrite = $this->page_rewrite_rules();
		$page_rewrite = apply_filters('page_rewrite_rules', $page_rewrite);

		// Extra permastructs
		foreach ( $this->extra_permastructs as $permastruct )
			$this->extra_rules_top = array_merge($this->extra_rules_top, $this->generate_rewrite_rules($permastruct, EP_NONE));

		// Put them together.
		if ( $this->use_verbose_page_rules )
			$this->rules = array_merge($this->extra_rules_top, $robots_rewrite, $default_feeds, $page_rewrite, $root_rewrite, $comments_rewrite, $search_rewrite, $category_rewrite, $tag_rewrite, $author_rewrite, $date_rewrite, $post_rewrite, $this->extra_rules);
		else
			$this->rules = array_merge($this->extra_rules_top, $robots_rewrite, $default_feeds, $root_rewrite, $comments_rewrite, $search_rewrite, $category_rewrite, $tag_rewrite, $author_rewrite, $date_rewrite, $post_rewrite, $page_rewrite, $this->extra_rules);

		do_action_ref_array('generate_rewrite_rules', array(&$this));
		$this->rules = apply_filters('rewrite_rules_array', $this->rules);

		return $this->rules;
	}

	/**
	 * Retrieve the rewrite rules.
	 *
	 * The difference between this method and {@link
	 * WP_Rewrite::rewrite_rules()} is that this method stores the rewrite rules
	 * in the 'rewrite_rules' option and retrieves it. This prevents having to
	 * process all of the permalinks to get the rewrite rules in the form of
	 * caching.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array Rewrite rules.
	 */
	function wp_rewrite_rules() {
		$this->rules = get_option('rewrite_rules');
		if ( empty($this->rules) ) {
			$this->matches = 'matches';
			$this->rewrite_rules();
			update_option('rewrite_rules', $this->rules);
		}

		return $this->rules;
	}

	/**
	 * Retrieve mod_rewrite formatted rewrite rules to write to .htaccess.
	 *
	 * Does not actually write to the .htaccess file, but creates the rules for
	 * the process that will.
	 *
	 * Will add  the non_wp_rules property rules to the .htaccess file before
	 * the WordPress rewrite rules one.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return string
	 */
	function mod_rewrite_rules() {
		if ( ! $this->using_permalinks() )
			return '';

		$site_root = parse_url(get_option('siteurl'));
		if ( isset( $site_root['path'] ) )
			$site_root = trailingslashit($site_root['path']);

		$home_root = parse_url(home_url());
		if ( isset( $home_root['path'] ) )
			$home_root = trailingslashit($home_root['path']);
		else
			$home_root = '/';

		$rules = "<IfModule mod_rewrite.c>\n";
		$rules .= "RewriteEngine On\n";
		$rules .= "RewriteBase $home_root\n";
		$rules .= "RewriteRule ^index\.php$ - [L]\n"; // Prevent -f checks on index.php.

		//add in the rules that don't redirect to WP's index.php (and thus shouldn't be handled by WP at all)
		foreach ( (array) $this->non_wp_rules as $match => $query) {
			// Apache 1.3 does not support the reluctant (non-greedy) modifier.
			$match = str_replace('.+?', '.+', $match);

			// If the match is unanchored and greedy, prepend rewrite conditions
			// to avoid infinite redirects and eclipsing of real files.
			//if ($match == '(.+)/?$' || $match == '([^/]+)/?$' ) {
				//nada.
			//}

			$rules .= 'RewriteRule ^' . $match . ' ' . $home_root . $query . " [QSA,L]\n";
		}

		if ( $this->use_verbose_rules ) {
			$this->matches = '';
			$rewrite = $this->rewrite_rules();
			$num_rules = count($rewrite);
			$rules .= "RewriteCond %{REQUEST_FILENAME} -f [OR]\n" .
				"RewriteCond %{REQUEST_FILENAME} -d\n" .
				"RewriteRule ^.*$ - [S=$num_rules]\n";

			foreach ( (array) $rewrite as $match => $query) {
				// Apache 1.3 does not support the reluctant (non-greedy) modifier.
				$match = str_replace('.+?', '.+', $match);

				// If the match is unanchored and greedy, prepend rewrite conditions
				// to avoid infinite redirects and eclipsing of real files.
				//if ($match == '(.+)/?$' || $match == '([^/]+)/?$' ) {
					//nada.
				//}

				if ( strpos($query, $this->index) !== false )
					$rules .= 'RewriteRule ^' . $match . ' ' . $home_root . $query . " [QSA,L]\n";
				else
					$rules .= 'RewriteRule ^' . $match . ' ' . $site_root . $query . " [QSA,L]\n";
			}
		} else {
			$rules .= "RewriteCond %{REQUEST_FILENAME} !-f\n" .
				"RewriteCond %{REQUEST_FILENAME} !-d\n" .
				"RewriteRule . {$home_root}{$this->index} [L]\n";
		}

		$rules .= "</IfModule>\n";

		$rules = apply_filters('mod_rewrite_rules', $rules);
		$rules = apply_filters('rewrite_rules', $rules);  // Deprecated

		return $rules;
	}

	/**
	 * Retrieve IIS7 URL Rewrite formatted rewrite rules to write to web.config file.
	 *
	 * Does not actually write to the web.config file, but creates the rules for
	 * the process that will.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @return string
	 */
	function iis7_url_rewrite_rules($add_parent_tags = false, $indent = "  ", $end_of_line = "\n") {

		if ( ! $this->using_permalinks() )
			return '';

		if ( !is_multisite() ) {
			$rules = '';
			$extra_indent = '';
			if ( $add_parent_tags ) {
				$rules .= "<configuration>".$end_of_line;
				$rules .= $indent."<system.webServer>".$end_of_line;
				$rules .= $indent.$indent."<rewrite>".$end_of_line;
				$rules .= $indent.$indent.$indent."<rules>".$end_of_line;
				$extra_indent = $indent.$indent.$indent.$indent;
			}

			$rules .= $extra_indent."<rule name=\"wordpress\" patternSyntax=\"Wildcard\">".$end_of_line;
			$rules .= $extra_indent.$indent."<match url=\"*\" />".$end_of_line;
			$rules .= $extra_indent.$indent.$indent."<conditions>".$end_of_line;
			$rules .= $extra_indent.$indent.$indent.$indent."<add input=\"{REQUEST_FILENAME}\" matchType=\"IsFile\" negate=\"true\" />".$end_of_line;
			$rules .= $extra_indent.$indent.$indent.$indent."<add input=\"{REQUEST_FILENAME}\" matchType=\"IsDirectory\" negate=\"true\" />".$end_of_line;
			$rules .= $extra_indent.$indent.$indent."</conditions>".$end_of_line;
			$rules .= $extra_indent.$indent."<action type=\"Rewrite\" url=\"index.php\" />".$end_of_line;
			$rules .= $extra_indent."</rule>";

			if ( $add_parent_tags ) {
				$rules .= $end_of_line.$indent.$indent.$indent."</rules>".$end_of_line;
				$rules .= $indent.$indent."</rewrite>".$end_of_line;
				$rules .= $indent."</system.webServer>".$end_of_line;
				$rules .= "</configuration>";
			}
		} else {
			$siteurl = get_option( 'siteurl' );
			$siteurl_len = strlen( $siteurl );
			if ( substr( WP_CONTENT_URL, 0, $siteurl_len ) == $siteurl && strlen( WP_CONTENT_URL ) > $siteurl_len )
				$content_path = substr( WP_CONTENT_URL, $siteurl_len + 1 );
			else
				$content_path = 'wp-content';
			$rules = '<rule name="wordpress - strip index.php" stopProcessing="false">
					<match url="^index.php/(.*)$" />
						<action type="Rewrite" url="{R:1}" />
					</rule>
					<rule name="wordpress - 1" stopProcessing="true">
						<match url="^(.*/)?files/$" />
						<action type="Rewrite" url="index.php" />
					</rule>
					<rule name="wordpress - 2" stopProcessing="true">
						<match url="^(.*/)?files/(.*)" />
						<conditions>
							<add input="{REQUEST_URI}" negate="true" pattern=".*' . $content_path . '/plugins.*"/>
						</conditions>
						<action type="Rewrite" url="wp-includes/ms-files.php?file={R:2}" appendQueryString="false" />
					</rule>
					<rule name="wordpress - 3" stopProcessing="true">
						<match url="^(.+)$" />
						<conditions>
							<add input="{REQUEST_URI}" pattern="^.*/wp-admin$" />
						</conditions>
						<action type="Redirect" url="{R:1}/" redirectType="Permanent" />
					</rule>
					<rule name="wordpress - 4" stopProcessing="true">
						<match url="."/>
						<conditions logicalGrouping="MatchAny">
							<add input="{REQUEST_FILENAME}" matchType="IsFile" pattern="" />
							<add input="{REQUEST_FILENAME}" matchType="IsDirectory" pattern="" />
						</conditions>
						<action type="None" />
					</rule>
					<rule name="wordpress - 5" stopProcessing="true">
						<match url="^([_0-9a-zA-Z-]+/)?(wp-.*)" />
						<action type="Rewrite" url="{R:2}" />
					</rule>
					<rule name="wordpress - 6" stopProcessing="true">
						<match url="^([_0-9a-zA-Z-]+/)?(.*\.php)$" />
						<action type="Rewrite" url="{R:2}" />
					</rule>
					<rule name="wordpress - 7" stopProcessing="true">
						<match url="." />
						<action type="Rewrite" url="index.php" />
					</rule>';
		}

		$rules = apply_filters('iis7_url_rewrite_rules', $rules);

		return $rules;
	}

	/**
	 * Add a straight rewrite rule.
	 *
	 * Any value in the $after parameter that isn't 'bottom' will be placed at
	 * the top of the rules.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string $regex Regular expression to match against request.
	 * @param string $redirect URL regex redirects to when regex matches request.
	 * @param string $after Optional, default is bottom. Location to place rule.
	 */
	function add_rule($regex, $redirect, $after = 'bottom') {
		//get everything up to the first ?
		$index = (strpos($redirect, '?') == false ? strlen($redirect) : strpos($redirect, '?'));
		$front = substr($redirect, 0, $index);
		if ( $front != $this->index ) { //it doesn't redirect to WP's index.php
			$this->add_external_rule($regex, $redirect);
		} else {
			if ( 'bottom' == $after)
				$this->extra_rules = array_merge($this->extra_rules, array($regex => $redirect));
			else
				$this->extra_rules_top = array_merge($this->extra_rules_top, array($regex => $redirect));
			//$this->extra_rules[$regex] = $redirect;
		}
	}

	/**
	 * Add a rule that doesn't redirect to index.php.
	 *
	 * Can redirect to any place.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string $regex Regular expression to match against request.
	 * @param string $redirect URL regex redirects to when regex matches request.
	 */
	function add_external_rule($regex, $redirect) {
		$this->non_wp_rules[$regex] = $redirect;
	}

	/**
	 * Add an endpoint, like /trackback/.
	 *
	 * To be inserted after certain URL types (specified in $places).
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string $name Name of endpoint.
	 * @param array $places URL types that endpoint can be used.
	 */
	function add_endpoint($name, $places) {
		global $wp;
		$this->endpoints[] = array ( $places, $name );
		$wp->add_query_var($name);
	}

	/**
	 * Add permalink structure.
	 *
	 * These are added along with the extra rewrite rules that are merged to the
	 * top.
	 *
	 * @since unknown
	 * @access public
	 *
	 * @param string $name Name for permalink structure.
	 * @param string $struct Permalink structure.
	 * @param bool $with_front Prepend front base to permalink structure.
	 */
	function add_permastruct($name, $struct, $with_front = true) {
		if ( $with_front )
			$struct = $this->front . $struct;
		$this->extra_permastructs[$name] = $struct;
	}

	/**
	 * Remove rewrite rules and then recreate rewrite rules.
	 *
	 * Calls {@link WP_Rewrite::wp_rewrite_rules()} after removing the
	 * 'rewrite_rules' option. If the function named 'save_mod_rewrite_rules'
	 * exists, it will be called.
	 *
	 * @since 2.0.1
	 * @access public
	 * @param $hard bool Whether to update .htaccess (hard flush) or just update rewrite_rules option (soft flush). Default is true (hard).
	 */
	function flush_rules($hard = true) {
		delete_option('rewrite_rules');
		$this->wp_rewrite_rules();
		if ( $hard && function_exists('save_mod_rewrite_rules') )
			save_mod_rewrite_rules();
		if ( $hard && function_exists('iis7_save_url_rewrite_rules') )
			iis7_save_url_rewrite_rules();
	}

	/**
	 * Sets up the object's properties.
	 *
	 * The 'use_verbose_page_rules' object property will be set to true if the
	 * permalink structure begins with one of the following: '%postname%', '%category%',
	 * '%tag%', or '%author%'.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	function init() {
		$this->extra_rules = $this->non_wp_rules = $this->endpoints = array();
		$this->permalink_structure = get_option('permalink_structure');
		$this->front = substr($this->permalink_structure, 0, strpos($this->permalink_structure, '%'));
		$this->root = '';
		if ( $this->using_index_permalinks() )
			$this->root = $this->index . '/';
		$this->category_base = get_option( 'category_base' );
		$this->tag_base = get_option( 'tag_base' );
		unset($this->category_structure);
		unset($this->author_structure);
		unset($this->date_structure);
		unset($this->page_structure);
		unset($this->search_structure);
		unset($this->feed_structure);
		unset($this->comment_feed_structure);
		$this->use_trailing_slashes = ( substr($this->permalink_structure, -1, 1) == '/' ) ? true : false;

		// Enable generic rules for pages if permalink structure doesn't begin with a wildcard.
		if ( preg_match("/^[^%]*%(?:postname|category|tag|author)%/", $this->permalink_structure) )
			 $this->use_verbose_page_rules = true;
		else
			$this->use_verbose_page_rules = false;
	}

	/**
	 * Set the main permalink structure for the blog.
	 *
	 * Will update the 'permalink_structure' option, if there is a difference
	 * between the current permalink structure and the parameter value. Calls
	 * {@link WP_Rewrite::init()} after the option is updated.
	 *
	 * Fires the 'permalink_structure_changed' action once the init call has
	 * processed passing the old and new values
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $permalink_structure Permalink structure.
	 */
	function set_permalink_structure($permalink_structure) {
		if ( $permalink_structure != $this->permalink_structure ) {
			update_option('permalink_structure', $permalink_structure);
			$this->init();
			do_action('permalink_structure_changed', $this->permalink_structure, $permalink_structure);
		}
	}

	/**
	 * Set the category base for the category permalink.
	 *
	 * Will update the 'category_base' option, if there is a difference between
	 * the current category base and the parameter value. Calls
	 * {@link WP_Rewrite::init()} after the option is updated.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $category_base Category permalink structure base.
	 */
	function set_category_base($category_base) {
		if ( $category_base != $this->category_base ) {
			update_option('category_base', $category_base);
			$this->init();
		}
	}

	/**
	 * Set the tag base for the tag permalink.
	 *
	 * Will update the 'tag_base' option, if there is a difference between the
	 * current tag base and the parameter value. Calls
	 * {@link WP_Rewrite::init()} after the option is updated.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param string $tag_base Tag permalink structure base.
	 */
	function set_tag_base( $tag_base ) {
		if ( $tag_base != $this->tag_base ) {
			update_option( 'tag_base', $tag_base );
			$this->init();
		}
	}

	/**
	 * PHP4 Constructor - Calls init(), which runs setup.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return WP_Rewrite
	 */
	function WP_Rewrite() {
		$this->init();
	}
}

?>

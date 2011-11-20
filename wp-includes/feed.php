<?php
/**
 * WordPress Feed API
 *
 * Many of the functions used in here belong in The Loop, or The Loop for the
 * Feeds.
 *
 * @package WordPress
 * @subpackage Feed
 */

/**
 * RSS container for the bloginfo function.
 *
 * You can retrieve anything that you can using the get_bloginfo() function.
 * Everything will be stripped of tags and characters converted, when the values
 * are retrieved for use in the feeds.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 1.5.1
 * @uses apply_filters() Calls 'get_bloginfo_rss' hook with two parameters.
 * @see get_bloginfo() For the list of possible values to display.
 *
 * @param string $show See get_bloginfo() for possible values.
 * @return string
 */
function get_bloginfo_rss($show = '') {
	$info = strip_tags(get_bloginfo($show));
	return apply_filters('get_bloginfo_rss', convert_chars($info), $show);
}

/**
 * Display RSS container for the bloginfo function.
 *
 * You can retrieve anything that you can using the get_bloginfo() function.
 * Everything will be stripped of tags and characters converted, when the values
 * are retrieved for use in the feeds.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 0.71
 * @uses apply_filters() Calls 'bloginfo_rss' hook with two parameters.
 * @see get_bloginfo() For the list of possible values to display.
 *
 * @param string $show See get_bloginfo() for possible values.
 */
function bloginfo_rss($show = '') {
	echo apply_filters('bloginfo_rss', get_bloginfo_rss($show), $show);
}

/**
 * Retrieve the default feed.
 *
 * The default feed is 'rss2', unless a plugin changes it through the
 * 'default_feed' filter.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5
 * @uses apply_filters() Calls 'default_feed' hook on the default feed string.
 *
 * @return string Default feed, or for example 'rss2', 'atom', etc.
 */
function get_default_feed() {
	$default_feed = apply_filters('default_feed', 'rss2');
	return 'rss' == $default_feed ? 'rss2' : $default_feed;
}

/**
 * Retrieve the blog title for the feed title.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.2.0
 * @uses apply_filters() Calls 'get_wp_title_rss' hook on title.
 * @uses wp_title() See function for $sep parameter usage.
 *
 * @param string $sep Optional.How to separate the title. See wp_title() for more info.
 * @return string Error message on failure or blog title on success.
 */
function get_wp_title_rss($sep = '&#187;') {
	$title = wp_title($sep, false);
	if ( is_wp_error( $title ) )
		return $title->get_error_message();
	$title = apply_filters('get_wp_title_rss', $title);
	return $title;
}

/**
 * Display the blog title for display of the feed title.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.2.0
 * @uses apply_filters() Calls 'wp_title_rss' on the blog title.
 * @see wp_title() $sep parameter usage.
 *
 * @param string $sep Optional.
 */
function wp_title_rss($sep = '&#187;') {
	echo apply_filters('wp_title_rss', get_wp_title_rss($sep));
}

/**
 * Retrieve the current post title for the feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.0.0
 * @uses apply_filters() Calls 'the_title_rss' on the post title.
 *
 * @return string Current post title.
 */
function get_the_title_rss() {
	$title = get_the_title();
	$title = apply_filters('the_title_rss', $title);
	return $title;
}

/**
 * Display the post title in the feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 0.71
 * @uses get_the_title_rss() Used to retrieve current post title.
 */
function the_title_rss() {
	echo get_the_title_rss();
}

/**
 * Retrieve the post content for feeds.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.9.0
 * @uses apply_filters() Calls 'the_content_feed' on the content before processing.
 * @see get_the_content()
 *
 * @param string $feed_type The type of feed. rss2 | atom | rss | rdf
 */
function get_the_content_feed($feed_type = null) {
	if ( !$feed_type )
		$feed_type = get_default_feed();

	$content = apply_filters('the_content', get_the_content());
	$content = str_replace(']]>', ']]&gt;', $content);
	return apply_filters('the_content_feed', $content, $feed_type);
}

/**
 * Display the post content for feeds.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.9.0
 * @uses apply_filters() Calls 'the_content_feed' on the content before processing.
 * @see get_the_content()
 *
 * @param string $feed_type The type of feed. rss2 | atom | rss | rdf
 */
function the_content_feed($feed_type = null) {
	echo get_the_content_feed($feed_type);
}

/**
 * Display the post excerpt for the feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 0.71
 * @uses apply_filters() Calls 'the_excerpt_rss' hook on the excerpt.
 */
function the_excerpt_rss() {
	$output = get_the_excerpt();
	echo apply_filters('the_excerpt_rss', $output);
}

/**
 * Display the permalink to the post for use in feeds.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.3.0
 * @uses apply_filters() Call 'the_permalink_rss' on the post permalink
 */
function the_permalink_rss() {
	echo esc_url( apply_filters('the_permalink_rss', get_permalink() ));
}

/**
 * Outputs the link to the comments for the current post in an xml safe way
 *
 * @since 3.0.0
 * @return none
 */
function comments_link_feed() {
	echo esc_url( get_comments_link() );
}

/**
 * Display the feed GUID for the current comment.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5.0
 *
 * @param int|object $comment_id Optional comment object or id. Defaults to global comment object.
 */
function comment_guid($comment_id = null) {
	echo esc_url( get_comment_guid($comment_id) );
}

/**
 * Retrieve the feed GUID for the current comment.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5.0
 *
 * @param int|object $comment_id Optional comment object or id. Defaults to global comment object.
 * @return bool|string false on failure or guid for comment on success.
 */
function get_comment_guid($comment_id = null) {
	$comment = get_comment($comment_id);

	if ( !is_object($comment) )
		return false;

	return get_the_guid($comment->comment_post_ID) . '#comment-' . $comment->comment_ID;
}

/**
 * Display the link to the comments.
 *
 * @since 1.5.0
 */
function comment_link() {
	echo esc_url( get_comment_link() );
}

/**
 * Retrieve the current comment author for use in the feeds.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.0.0
 * @uses apply_filters() Calls 'comment_author_rss' hook on comment author.
 * @uses get_comment_author()
 *
 * @return string Comment Author
 */
function get_comment_author_rss() {
	return apply_filters('comment_author_rss', get_comment_author() );
}

/**
 * Display the current comment author in the feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 1.0.0
 */
function comment_author_rss() {
	echo get_comment_author_rss();
}

/**
 * Display the current comment content for use in the feeds.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 1.0.0
 * @uses apply_filters() Calls 'comment_text_rss' filter on comment content.
 * @uses get_comment_text()
 */
function comment_text_rss() {
	$comment_text = get_comment_text();
	$comment_text = apply_filters('comment_text_rss', $comment_text);
	echo $comment_text;
}

/**
 * Retrieve all of the post categories, formatted for use in feeds.
 *
 * All of the categories for the current post in the feed loop, will be
 * retrieved and have feed markup added, so that they can easily be added to the
 * RSS2, Atom, or RSS1 and RSS0.91 RDF feeds.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.1.0
 * @uses apply_filters()
 *
 * @param string $type Optional, default is the type returned by get_default_feed().
 * @return string All of the post categories for displaying in the feed.
 */
function get_the_category_rss($type = null) {
	if ( empty($type) )
		$type = get_default_feed();
	$categories = get_the_category();
	$tags = get_the_tags();
	$the_list = '';
	$cat_names = array();

	$filter = 'rss';
	if ( 'atom' == $type )
		$filter = 'raw';

	if ( !empty($categories) ) foreach ( (array) $categories as $category ) {
		$cat_names[] = sanitize_term_field('name', $category->name, $category->term_id, 'category', $filter);
	}

	if ( !empty($tags) ) foreach ( (array) $tags as $tag ) {
		$cat_names[] = sanitize_term_field('name', $tag->name, $tag->term_id, 'post_tag', $filter);
	}

	$cat_names = array_unique($cat_names);

	foreach ( $cat_names as $cat_name ) {
		if ( 'rdf' == $type )
			$the_list .= "\t\t<dc:subject><![CDATA[$cat_name]]></dc:subject>\n";
		elseif ( 'atom' == $type )
			$the_list .= sprintf( '<category scheme="%1$s" term="%2$s" />', esc_attr( apply_filters( 'get_bloginfo_rss', get_bloginfo( 'url' ) ) ), esc_attr( $cat_name ) );
		else
			$the_list .= "\t\t<category><![CDATA[" . @html_entity_decode( $cat_name, ENT_COMPAT, get_option('blog_charset') ) . "]]></category>\n";
	}

	return apply_filters('the_category_rss', $the_list, $type);
}

/**
 * Display the post categories in the feed.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 0.71
 * @see get_the_category_rss() For better explanation.
 *
 * @param string $type Optional, default is the type returned by get_default_feed().
 */
function the_category_rss($type = null) {
	echo get_the_category_rss($type);
}

/**
 * Display the HTML type based on the blog setting.
 *
 * The two possible values are either 'xhtml' or 'html'.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.2.0
 */
function html_type_rss() {
	$type = get_bloginfo('html_type');
	if (strpos($type, 'xhtml') !== false)
		$type = 'xhtml';
	else
		$type = 'html';
	echo $type;
}

/**
 * Display the rss enclosure for the current post.
 *
 * Uses the global $post to check whether the post requires a password and if
 * the user has the password for the post. If not then it will return before
 * displaying.
 *
 * Also uses the function get_post_custom() to get the post's 'enclosure'
 * metadata field and parses the value to display the enclosure(s). The
 * enclosure(s) consist of enclosure HTML tag(s) with a URI and other
 * attributes.
 *
 * @package WordPress
 * @subpackage Template
 * @since 1.5.0
 * @uses apply_filters() Calls 'rss_enclosure' hook on rss enclosure.
 * @uses get_post_custom() To get the current post enclosure metadata.
 */
function rss_enclosure() {
	if ( post_password_required() )
		return;

	foreach ( (array) get_post_custom() as $key => $val) {
		if ($key == 'enclosure') {
			foreach ( (array) $val as $enc ) {
				$enclosure = explode("\n", $enc);

				//only get the the first element eg, audio/mpeg from 'audio/mpeg mpga mp2 mp3'
				$t = preg_split('/[ \t]/', trim($enclosure[2]) );
				$type = $t[0];

				echo apply_filters('rss_enclosure', '<enclosure url="' . trim(htmlspecialchars($enclosure[0])) . '" length="' . trim($enclosure[1]) . '" type="' . $type . '" />' . "\n");
			}
		}
	}
}

/**
 * Display the atom enclosure for the current post.
 *
 * Uses the global $post to check whether the post requires a password and if
 * the user has the password for the post. If not then it will return before
 * displaying.
 *
 * Also uses the function get_post_custom() to get the post's 'enclosure'
 * metadata field and parses the value to display the enclosure(s). The
 * enclosure(s) consist of link HTML tag(s) with a URI and other attributes.
 *
 * @package WordPress
 * @subpackage Template
 * @since 2.2.0
 * @uses apply_filters() Calls 'atom_enclosure' hook on atom enclosure.
 * @uses get_post_custom() To get the current post enclosure metadata.
 */
function atom_enclosure() {
	if ( post_password_required() )
		return;

	foreach ( (array) get_post_custom() as $key => $val ) {
		if ($key == 'enclosure') {
			foreach ( (array) $val as $enc ) {
				$enclosure = split("\n", $enc);
				echo apply_filters('atom_enclosure', '<link href="' . trim(htmlspecialchars($enclosure[0])) . '" rel="enclosure" length="' . trim($enclosure[1]) . '" type="' . trim($enclosure[2]) . '" />' . "\n");
			}
		}
	}
}

/**
 * Determine the type of a string of data with the data formatted.
 *
 * Tell whether the type is text, html, or xhtml, per RFC 4287 section 3.1.
 *
 * In the case of WordPress, text is defined as containing no markup,
 * xhtml is defined as "well formed", and html as tag soup (i.e., the rest).
 *
 * Container div tags are added to xhtml values, per section 3.1.1.3.
 *
 * @link http://www.atomenabled.org/developers/syndication/atom-format-spec.php#rfc.section.3.1
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5
 *
 * @param string $data Input string
 * @return array array(type, value)
 */
function prep_atom_text_construct($data) {
	if (strpos($data, '<') === false && strpos($data, '&') === false) {
		return array('text', $data);
	}

	$parser = xml_parser_create();
	xml_parse($parser, '<div>' . $data . '</div>', true);
	$code = xml_get_error_code($parser);
	xml_parser_free($parser);

	if (!$code) {
		if (strpos($data, '<') === false) {
			return array('text', $data);
		} else {
			$data = "<div xmlns='http://www.w3.org/1999/xhtml'>$data</div>";
			return array('xhtml', $data);
		}
	}

	if (strpos($data, ']]>') == false) {
		return array('html', "<![CDATA[$data]]>");
	} else {
		return array('html', htmlspecialchars($data));
	}
}

/**
 * Display the link for the currently displayed feed in a XSS safe way.
 *
 * Generate a correct link for the atom:self element.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5
 */
function self_link() {
	$host = @parse_url(home_url());
	$host = $host['host'];
	echo esc_url(
		'http'
		. ( (isset($_SERVER['https']) && $_SERVER['https'] == 'on') ? 's' : '' ) . '://'
		. $host
		. stripslashes($_SERVER['REQUEST_URI'])
		);
}

/**
 * Return the content type for specified feed type.
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.8.0
 */
function feed_content_type( $type = '' ) {
	if ( empty($type) )
		$type = get_default_feed();

	$types = array(
		'rss'  => 'application/rss+xml',
		'rss2' => 'application/rss+xml',
		'rss-http'  => 'text/xml',
		'atom' => 'application/atom+xml',
		'rdf'  => 'application/rdf+xml'
	);

	$content_type = ( !empty($types[$type]) ) ? $types[$type] : 'application/octet-stream';

	return apply_filters( 'feed_content_type', $content_type, $type );
}

/**
 * Build SimplePie object based on RSS or Atom feed from URL.
 *
 * @since 2.8
 *
 * @param string $url URL to retrieve feed
 * @return WP_Error|SimplePie WP_Error object on failure or SimplePie object on success
 */
function fetch_feed($url) {
	require_once (ABSPATH . WPINC . '/class-feed.php');

	$feed = new SimplePie();
	$feed->set_feed_url($url);
	$feed->set_cache_class('WP_Feed_Cache');
	$feed->set_file_class('WP_SimplePie_File');
	$feed->set_cache_duration(apply_filters('wp_feed_cache_transient_lifetime', 43200, $url));
	do_action_ref_array( 'wp_feed_options', array( &$feed, $url ) );
	$feed->init();
	$feed->handle_content_type();

	if ( $feed->error() )
		return new WP_Error('simplepie-error', $feed->error());

	return $feed;
}

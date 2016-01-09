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
 * @since 1.5.1
 * @see get_bloginfo() For the list of possible values to display.
 *
 * @param string $show See get_bloginfo() for possible values.
 * @return string
 */
function get_bloginfo_rss($show = '') {
	$info = strip_tags(get_bloginfo($show));
	/**
	 * Filter the bloginfo for use in RSS feeds.
	 *
	 * @since 2.2.0
	 *
	 * @see convert_chars()
	 * @see get_bloginfo()
	 *
	 * @param string $info Converted string value of the blog information.
	 * @param string $show The type of blog information to retrieve.
	 */
	return apply_filters( 'get_bloginfo_rss', convert_chars( $info ), $show );
}

/**
 * Display RSS container for the bloginfo function.
 *
 * You can retrieve anything that you can using the get_bloginfo() function.
 * Everything will be stripped of tags and characters converted, when the values
 * are retrieved for use in the feeds.
 *
 * @since 0.71
 * @see get_bloginfo() For the list of possible values to display.
 *
 * @param string $show See get_bloginfo() for possible values.
 */
function bloginfo_rss($show = '') {
	/**
	 * Filter the bloginfo for display in RSS feeds.
	 *
	 * @since 2.1.0
	 *
	 * @see get_bloginfo()
	 *
	 * @param string $rss_container RSS container for the blog information.
	 * @param string $show          The type of blog information to retrieve.
	 */
	echo apply_filters( 'bloginfo_rss', get_bloginfo_rss( $show ), $show );
}

/**
 * Retrieve the default feed.
 *
 * The default feed is 'rss2', unless a plugin changes it through the
 * 'default_feed' filter.
 *
 * @since 2.5.0
 *
 * @return string Default feed, or for example 'rss2', 'atom', etc.
 */
function get_default_feed() {
	/**
	 * Filter the default feed type.
	 *
	 * @since 2.5.0
	 *
	 * @param string $feed_type Type of default feed. Possible values include 'rss2', 'atom'.
	 *                          Default 'rss2'.
	 */
	$default_feed = apply_filters( 'default_feed', 'rss2' );
	return 'rss' == $default_feed ? 'rss2' : $default_feed;
}

/**
 * Retrieve the blog title for the feed title.
 *
 * @since 2.2.0
 * @since 4.4.0 The optional `$sep` parameter was deprecated and renamed to `$deprecated`.
 *
 * @param string $deprecated Unused..
 * @return string The document title.
 */
function get_wp_title_rss( $deprecated = '&#8211;' ) {
	if ( '&#8211;' !== $deprecated ) {
		/* translators: %s: 'document_title_separator' filter name */
		_deprecated_argument( __FUNCTION__, '4.4.0', sprintf( __( 'Use the %s filter instead.' ), '<code>document_title_separator</code>' ) );
	}

	/**
	 * Filter the blog title for use as the feed title.
	 *
	 * @since 2.2.0
	 * @since 4.4.0 The `$sep` parameter was deprecated and renamed to `$deprecated`.
	 *
	 * @param string $title      The current blog title.
	 * @param string $deprecated Unused.
	 */
	return apply_filters( 'get_wp_title_rss', wp_get_document_title(), $deprecated );
}

/**
 * Display the blog title for display of the feed title.
 *
 * @since 2.2.0
 * @since 4.4.0 The optional `$sep` parameter was deprecated and renamed to `$deprecated`.
 *
 * @param string $deprecated Unused.
 */
function wp_title_rss( $deprecated = '&#8211;' ) {
	if ( '&#8211;' !== $deprecated ) {
		/* translators: %s: 'document_title_separator' filter name */
		_deprecated_argument( __FUNCTION__, '4.4.0', sprintf( __( 'Use the %s filter instead.' ), '<code>document_title_separator</code>' ) );
	}

	/**
	 * Filter the blog title for display of the feed title.
	 *
	 * @since 2.2.0
	 * @since 4.4.0 The `$sep` parameter was deprecated and renamed to `$deprecated`.
	 *
	 * @see get_wp_title_rss()
	 *
	 * @param string $wp_title_rss The current blog title.
	 * @param string $deprecated   Unused.
	 */
	echo apply_filters( 'wp_title_rss', get_wp_title_rss(), $deprecated );
}

/**
 * Retrieve the current post title for the feed.
 *
 * @since 2.0.0
 *
 * @return string Current post title.
 */
function get_the_title_rss() {
	$title = get_the_title();

	/**
	 * Filter the post title for use in a feed.
	 *
	 * @since 1.2.0
	 *
	 * @param string $title The current post title.
	 */
	$title = apply_filters( 'the_title_rss', $title );
	return $title;
}

/**
 * Display the post title in the feed.
 *
 * @since 0.71
 */
function the_title_rss() {
	echo get_the_title_rss();
}

/**
 * Retrieve the post content for feeds.
 *
 * @since 2.9.0
 * @see get_the_content()
 *
 * @param string $feed_type The type of feed. rss2 | atom | rss | rdf
 * @return string The filtered content.
 */
function get_the_content_feed($feed_type = null) {
	if ( !$feed_type )
		$feed_type = get_default_feed();

	/** This filter is documented in wp-includes/post-template.php */
	$content = apply_filters( 'the_content', get_the_content() );
	$content = str_replace(']]>', ']]&gt;', $content);
	/**
	 * Filter the post content for use in feeds.
	 *
	 * @since 2.9.0
	 *
	 * @param string $content   The current post content.
	 * @param string $feed_type Type of feed. Possible values include 'rss2', 'atom'.
	 *                          Default 'rss2'.
	 */
	return apply_filters( 'the_content_feed', $content, $feed_type );
}

/**
 * Display the post content for feeds.
 *
 * @since 2.9.0
 *
 * @param string $feed_type The type of feed. rss2 | atom | rss | rdf
 */
function the_content_feed($feed_type = null) {
	echo get_the_content_feed($feed_type);
}

/**
 * Display the post excerpt for the feed.
 *
 * @since 0.71
 */
function the_excerpt_rss() {
	$output = get_the_excerpt();
	/**
	 * Filter the post excerpt for a feed.
	 *
	 * @since 1.2.0
	 *
	 * @param string $output The current post excerpt.
	 */
	echo apply_filters( 'the_excerpt_rss', $output );
}

/**
 * Display the permalink to the post for use in feeds.
 *
 * @since 2.3.0
 */
function the_permalink_rss() {
	/**
	 * Filter the permalink to the post for use in feeds.
	 *
	 * @since 2.3.0
	 *
	 * @param string $post_permalink The current post permalink.
	 */
	echo esc_url( apply_filters( 'the_permalink_rss', get_permalink() ) );
}

/**
 * Outputs the link to the comments for the current post in an xml safe way
 *
 * @since 3.0.0
 * @return none
 */
function comments_link_feed() {
	/**
	 * Filter the comments permalink for the current post.
	 *
	 * @since 3.6.0
	 *
	 * @param string $comment_permalink The current comment permalink with
	 *                                  '#comments' appended.
	 */
	echo esc_url( apply_filters( 'comments_link_feed', get_comments_link() ) );
}

/**
 * Display the feed GUID for the current comment.
 *
 * @since 2.5.0
 *
 * @param int|WP_Comment $comment_id Optional comment object or id. Defaults to global comment object.
 */
function comment_guid($comment_id = null) {
	echo esc_url( get_comment_guid($comment_id) );
}

/**
 * Retrieve the feed GUID for the current comment.
 *
 * @since 2.5.0
 *
 * @param int|WP_Comment $comment_id Optional comment object or id. Defaults to global comment object.
 * @return false|string false on failure or guid for comment on success.
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
 * @since 4.4.0 Introduced the `$comment` argument.
 *
 * @param int|WP_Comment $comment Optional. Comment object or id. Defaults to global comment object.
 */
function comment_link( $comment = null ) {
	/**
	 * Filter the current comment's permalink.
	 *
	 * @since 3.6.0
	 *
	 * @see get_comment_link()
	 *
	 * @param string $comment_permalink The current comment permalink.
	 */
	echo esc_url( apply_filters( 'comment_link', get_comment_link( $comment ) ) );
}

/**
 * Retrieve the current comment author for use in the feeds.
 *
 * @since 2.0.0
 *
 * @return string Comment Author
 */
function get_comment_author_rss() {
	/**
	 * Filter the current comment author for use in a feed.
	 *
	 * @since 1.5.0
	 *
	 * @see get_comment_author()
	 *
	 * @param string $comment_author The current comment author.
	 */
	return apply_filters( 'comment_author_rss', get_comment_author() );
}

/**
 * Display the current comment author in the feed.
 *
 * @since 1.0.0
 */
function comment_author_rss() {
	echo get_comment_author_rss();
}

/**
 * Display the current comment content for use in the feeds.
 *
 * @since 1.0.0
 */
function comment_text_rss() {
	$comment_text = get_comment_text();
	/**
	 * Filter the current comment content for use in a feed.
	 *
	 * @since 1.5.0
	 *
	 * @param string $comment_text The content of the current comment.
	 */
	$comment_text = apply_filters( 'comment_text_rss', $comment_text );
	echo $comment_text;
}

/**
 * Retrieve all of the post categories, formatted for use in feeds.
 *
 * All of the categories for the current post in the feed loop, will be
 * retrieved and have feed markup added, so that they can easily be added to the
 * RSS2, Atom, or RSS1 and RSS0.91 RDF feeds.
 *
 * @since 2.1.0
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
			$the_list .= sprintf( '<category scheme="%1$s" term="%2$s" />', esc_attr( get_bloginfo_rss( 'url' ) ), esc_attr( $cat_name ) );
		else
			$the_list .= "\t\t<category><![CDATA[" . @html_entity_decode( $cat_name, ENT_COMPAT, get_option('blog_charset') ) . "]]></category>\n";
	}

	/**
	 * Filter all of the post categories for display in a feed.
	 *
	 * @since 1.2.0
	 *
	 * @param string $the_list All of the RSS post categories.
	 * @param string $type     Type of feed. Possible values include 'rss2', 'atom'.
	 *                         Default 'rss2'.
	 */
	return apply_filters( 'the_category_rss', $the_list, $type );
}

/**
 * Display the post categories in the feed.
 *
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
 * @since 1.5.0
 */
function rss_enclosure() {
	if ( post_password_required() )
		return;

	foreach ( (array) get_post_custom() as $key => $val) {
		if ($key == 'enclosure') {
			foreach ( (array) $val as $enc ) {
				$enclosure = explode("\n", $enc);

				// only get the first element, e.g. audio/mpeg from 'audio/mpeg mpga mp2 mp3'
				$t = preg_split('/[ \t]/', trim($enclosure[2]) );
				$type = $t[0];

				/**
				 * Filter the RSS enclosure HTML link tag for the current post.
				 *
				 * @since 2.2.0
				 *
				 * @param string $html_link_tag The HTML link tag with a URI and other attributes.
				 */
				echo apply_filters( 'rss_enclosure', '<enclosure url="' . trim( htmlspecialchars( $enclosure[0] ) ) . '" length="' . trim( $enclosure[1] ) . '" type="' . $type . '" />' . "\n" );
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
 * @since 2.2.0
 */
function atom_enclosure() {
	if ( post_password_required() )
		return;

	foreach ( (array) get_post_custom() as $key => $val ) {
		if ($key == 'enclosure') {
			foreach ( (array) $val as $enc ) {
				$enclosure = explode("\n", $enc);
				/**
				 * Filter the atom enclosure HTML link tag for the current post.
				 *
				 * @since 2.2.0
				 *
				 * @param string $html_link_tag The HTML link tag with a URI and other attributes.
				 */
				echo apply_filters( 'atom_enclosure', '<link href="' . trim( htmlspecialchars( $enclosure[0] ) ) . '" rel="enclosure" length="' . trim( $enclosure[1] ) . '" type="' . trim( $enclosure[2] ) . '" />' . "\n" );
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
 * @since 2.5.0
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

	if (strpos($data, ']]>') === false) {
		return array('html', "<![CDATA[$data]]>");
	} else {
		return array('html', htmlspecialchars($data));
	}
}

/**
 * Displays Site Icon in atom feeds.
 *
 * @since 4.3.0
 *
 * @see get_site_icon_url()
 */
function atom_site_icon() {
	$url = get_site_icon_url( 32 );
	if ( $url ) {
		echo "<icon>$url</icon>\n";
	}
}

/**
 * Displays Site Icon in RSS2.
 *
 * @since 4.3.0
 */
function rss2_site_icon() {
	$rss_title = get_wp_title_rss();
	if ( empty( $rss_title ) ) {
		$rss_title = get_bloginfo_rss( 'name' );
	}

	$url = get_site_icon_url( 32 );
	if ( $url ) {
		echo '
<image>
	<url>' . convert_chars( $url ) . '</url>
	<title>' . $rss_title . '</title>
	<link>' . get_bloginfo_rss( 'url' ) . '</link>
	<width>32</width>
	<height>32</height>
</image> ' . "\n";
	}
}

/**
 * Display the link for the currently displayed feed in a XSS safe way.
 *
 * Generate a correct link for the atom:self element.
 *
 * @since 2.5.0
 */
function self_link() {
	$host = @parse_url(home_url());
	/**
	 * Filter the current feed URL.
	 *
	 * @since 3.6.0
	 *
	 * @see set_url_scheme()
	 * @see wp_unslash()
	 *
	 * @param string $feed_link The link for the feed with set URL scheme.
	 */
	echo esc_url( apply_filters( 'self_link', set_url_scheme( 'http://' . $host['host'] . wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
}

/**
 * Return the content type for specified feed type.
 *
 * @since 2.8.0
 */
function feed_content_type( $type = '' ) {
	if ( empty($type) )
		$type = get_default_feed();

	$types = array(
		'rss'  => 'application/rss+xml',
		'rss2' => 'application/rss+xml',
		'atom' => 'application/atom+xml',
		'rdf'  => 'application/rdf+xml'
	);

	$content_type = ( !empty($types[$type]) ) ? $types[$type] : 'application/octet-stream';

	/**
	 * Filter the content type for a specific feed type.
	 *
	 * @since 2.8.0
	 *
	 * @param string $content_type Content type indicating the type of data that a feed contains.
	 * @param string $type         Type of feed. Possible values include 'rss2', 'atom'.
	 *                             Default 'rss2'.
	 */
	return apply_filters( 'feed_content_type', $content_type, $type );
}

/**
 * Build SimplePie object based on RSS or Atom feed from URL.
 *
 * @since 2.8.0
 *
 * @param mixed $url URL of feed to retrieve. If an array of URLs, the feeds are merged
 * using SimplePie's multifeed feature.
 * See also {@link ​http://simplepie.org/wiki/faq/typical_multifeed_gotchas}
 *
 * @return WP_Error|SimplePie WP_Error object on failure or SimplePie object on success
 */
function fetch_feed( $url ) {
	require_once( ABSPATH . WPINC . '/class-feed.php' );

	$feed = new SimplePie();

	$feed->set_sanitize_class( 'WP_SimplePie_Sanitize_KSES' );
	// We must manually overwrite $feed->sanitize because SimplePie's
	// constructor sets it before we have a chance to set the sanitization class
	$feed->sanitize = new WP_SimplePie_Sanitize_KSES();

	$feed->set_cache_class( 'WP_Feed_Cache' );
	$feed->set_file_class( 'WP_SimplePie_File' );

	$feed->set_feed_url( $url );
	/** This filter is documented in wp-includes/class-feed.php */
	$feed->set_cache_duration( apply_filters( 'wp_feed_cache_transient_lifetime', 12 * HOUR_IN_SECONDS, $url ) );
	/**
	 * Fires just before processing the SimplePie feed object.
	 *
	 * @since 3.0.0
	 *
	 * @param object &$feed SimplePie feed object, passed by reference.
	 * @param mixed  $url   URL of feed to retrieve. If an array of URLs, the feeds are merged.
	 */
	do_action_ref_array( 'wp_feed_options', array( &$feed, $url ) );
	$feed->init();
	$feed->set_output_encoding( get_option( 'blog_charset' ) );
	$feed->handle_content_type();

	if ( $feed->error() )
		return new WP_Error( 'simplepie-error', $feed->error() );

	return $feed;
}

<?php
/**
 * Comment template functions
 *
 * These functions are meant to live inside of the WordPress loop.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Retrieve the author of the current comment.
 *
 * If the comment has an empty comment_author field, then 'Anonymous' person is
 * assumed.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'get_comment_author' hook on the comment author
 *
 * @return string The comment author
 */
function get_comment_author() {
	global $comment;
	if ( empty($comment->comment_author) ) {
		if (!empty($comment->user_id)){
			$user=get_userdata($comment->user_id);
			$author=$user->user_login;
		} else {
			$author = __('Anonymous');
		}
	} else {
		$author = $comment->comment_author;
	}
	return apply_filters('get_comment_author', $author);
}

/**
 * Displays the author of the current comment.
 *
 * @since 0.71
 * @uses apply_filters() Calls 'comment_author' on comment author before displaying
 */
function comment_author() {
	$author = apply_filters('comment_author', get_comment_author() );
	echo $author;
}

/**
 * Retrieve the email of the author of the current comment.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls the 'get_comment_author_email' hook on the comment author email
 * @uses $comment
 *
 * @return string The current comment author's email
 */
function get_comment_author_email() {
	global $comment;
	return apply_filters('get_comment_author_email', $comment->comment_author_email);
}

/**
 * Display the email of the author of the current global $comment.
 *
 * Care should be taken to protect the email address and assure that email
 * harvesters do not capture your commentors' email address. Most assume that
 * their email address will not appear in raw form on the blog. Doing so will
 * enable anyone, including those that people don't want to get the email
 * address and use it for their own means good and bad.
 *
 * @since 0.71
 * @uses apply_filters() Calls 'author_email' hook on the author email
 */
function comment_author_email() {
	echo apply_filters('author_email', get_comment_author_email() );
}

/**
 * Display the html email link to the author of the current comment.
 *
 * Care should be taken to protect the email address and assure that email
 * harvesters do not capture your commentors' email address. Most assume that
 * their email address will not appear in raw form on the blog. Doing so will
 * enable anyone, including those that people don't want to get the email
 * address and use it for their own means good and bad.
 *
 * @since 0.71
 * @uses apply_filters() Calls 'comment_email' hook for the display of the comment author's email
 * @uses get_comment_author_email_link() For generating the link
 * @global object $comment The current Comment row object
 *
 * @param string $linktext The text to display instead of the comment author's email address
 * @param string $before The text or HTML to display before the email link.
 * @param string $after The text or HTML to display after the email link.
 */
function comment_author_email_link($linktext='', $before='', $after='') {
	if ( $link = get_comment_author_email_link( $linktext, $before, $after ) )
		echo $link;
}

/**
 * Return the html email link to the author of the current comment.
 *
 * Care should be taken to protect the email address and assure that email
 * harvesters do not capture your commentors' email address. Most assume that
 * their email address will not appear in raw form on the blog. Doing so will
 * enable anyone, including those that people don't want to get the email
 * address and use it for their own means good and bad.
 *
 * @since 2.7
 * @uses apply_filters() Calls 'comment_email' hook for the display of the comment author's email
 * @global object $comment The current Comment row object
 *
 * @param string $linktext The text to display instead of the comment author's email address
 * @param string $before The text or HTML to display before the email link.
 * @param string $after The text or HTML to display after the email link.
 */
function get_comment_author_email_link($linktext='', $before='', $after='') {
	global $comment;
	$email = apply_filters('comment_email', $comment->comment_author_email);
	if ((!empty($email)) && ($email != '@')) {
	$display = ($linktext != '') ? $linktext : $email;
		$return  = $before;
		$return .= "<a href='mailto:$email'>$display</a>";
	 	$return .= $after;
		return $return;
	} else {
		return '';
	}
}

/**
 * Retrieve the html link to the url of the author of the current comment.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'get_comment_author_link' hook on the complete link HTML or author
 *
 * @return string Comment Author name or HTML link for author's URL
 */
function get_comment_author_link() {
	/** @todo Only call these functions when they are needed. Include in if... else blocks */
	$url    = get_comment_author_url();
	$author = get_comment_author();

	if ( empty( $url ) || 'http://' == $url )
		$return = $author;
	else
		$return = "<a href='$url' rel='external nofollow' class='url'>$author</a>";
	return apply_filters('get_comment_author_link', $return);
}

/**
 * Display the html link to the url of the author of the current comment.
 *
 * @since 0.71
 * @see get_comment_author_link() Echos result
 */
function comment_author_link() {
	echo get_comment_author_link();
}

/**
 * Retrieve the IP address of the author of the current comment.
 *
 * @since 1.5.0
 * @uses $comment
 * @uses apply_filters()
 *
 * @return unknown
 */
function get_comment_author_IP() {
	global $comment;
	return apply_filters('get_comment_author_IP', $comment->comment_author_IP);
}

/**
 * Display the IP address of the author of the current comment.
 *
 * @since 0.71
 * @see get_comment_author_IP() Echos Result
 */
function comment_author_IP() {
	echo get_comment_author_IP();
}

/**
 * Retrieve the url of the author of the current comment.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'get_comment_author_url' hook on the comment author's URL
 *
 * @return string
 */
function get_comment_author_url() {
	global $comment;
	return apply_filters('get_comment_author_url', $comment->comment_author_url);
}

/**
 * Display the url of the author of the current comment.
 *
 * @since 0.71
 * @uses apply_filters()
 * @uses get_comment_author_url() Retrieves the comment author's URL
 */
function comment_author_url() {
	echo apply_filters('comment_url', get_comment_author_url());
}

/**
 * Retrieves the HTML link of the url of the author of the current comment.
 *
 * $linktext parameter is only used if the URL does not exist for the comment
 * author. If the URL does exist then the URL will be used and the $linktext
 * will be ignored.
 *
 * Encapsulate the HTML link between the $before and $after. So it will appear
 * in the order of $before, link, and finally $after.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls the 'get_comment_author_url_link' on the complete HTML before returning.
 *
 * @param string $linktext The text to display instead of the comment author's email address
 * @param string $before The text or HTML to display before the email link.
 * @param string $after The text or HTML to display after the email link.
 * @return string The HTML link between the $before and $after parameters
 */
function get_comment_author_url_link( $linktext = '', $before = '', $after = '' ) {
	$url = get_comment_author_url();
	$display = ($linktext != '') ? $linktext : $url;
	$display = str_replace( 'http://www.', '', $display );
	$display = str_replace( 'http://', '', $display );
	if ( '/' == substr($display, -1) )
		$display = substr($display, 0, -1);
	$return = "$before<a href='$url' rel='external'>$display</a>$after";
	return apply_filters('get_comment_author_url_link', $return);
}

/**
 * Displays the HTML link of the url of the author of the current comment.
 *
 * @since 0.71
 * @see get_comment_author_url_link() Echos result
 *
 * @param string $linktext The text to display instead of the comment author's email address
 * @param string $before The text or HTML to display before the email link.
 * @param string $after The text or HTML to display after the email link.
 */
function comment_author_url_link( $linktext = '', $before = '', $after = '' ) {
	echo get_comment_author_url_link( $linktext, $before, $after );
}

/**
 * Generates semantic classes for each comment element
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list
 * @param int $comment_id An optional comment ID
 * @param int $post_id An optional post ID
 * @param bool $echo Whether comment_class should echo or return
 */
function comment_class( $class = '', $comment_id = null, $post_id = null, $echo = true ) {
	// Separates classes with a single space, collates classes for comment DIV
	$class = 'class="' . join( ' ', get_comment_class( $class, $comment_id, $post_id ) ) . '"';
	if ( $echo)
		echo $class;
	else
		return $class;
}

/**
 * Returns the classes for the comment div as an array
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list
 * @param int $comment_id An optional comment ID
 * @param int $post_id An optional post ID
 * @return array Array of classes
 */
function get_comment_class( $class = '', $comment_id = null, $post_id = null ) {
	global $comment_alt, $comment_depth, $comment_thread_alt;

	$comment = get_comment($comment_id);

	$classes = array();

	// Get the comment type (comment, trackback),
	$classes[] = ( empty( $comment->comment_type ) ) ? 'comment' : $comment->comment_type;

	// If the comment author has an id (registered), then print the log in name
	if ( $comment->user_id > 0 && $user = get_userdata($comment->user_id) ) {
		// For all registered users, 'byuser'
		$classes[] = 'byuser';
		$classes[] = 'comment-author-' . $user->user_nicename;
		// For comment authors who are the author of the post
		if ( $post = get_post($post_id) ) {
			if ( $comment->user_id === $post->post_author )
				$classes[] = 'bypostauthor';
		}
	}

	if ( empty($comment_alt) )
		$comment_alt = 0;
	if ( empty($comment_depth) )
		$comment_depth = 1;
	if ( empty($comment_thread_alt) )
		$comment_thread_alt = 0;

	if ( $comment_alt % 2 ) {
		$classes[] = 'odd';
		$classes[] = 'alt';
	} else {
		$classes[] = 'even';
	}

	$comment_alt++;

	// Alt for top-level comments
	if ( 1 == $comment_depth ) {
		if ( $comment_thread_alt % 2 ) {
			$classes[] = 'thread-odd';
			$classes[] = 'thread-alt';
		} else {
			$classes[] = 'thread-even';
		}
		$comment_thread_alt++;
	}

	$classes[] = "depth-$comment_depth";

	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}

	return apply_filters('comment_class', $classes, $class, $comment_id, $post_id);
}

/**
 * Retrieve the comment date of the current comment.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'get_comment_date' hook with the formated date and the $d parameter respectively
 * @uses $comment
 *
 * @param string $d The format of the date (defaults to user's config)
 * @return string The comment's date
 */
function get_comment_date( $d = '' ) {
	global $comment;
	if ( '' == $d )
		$date = mysql2date( get_option('date_format'), $comment->comment_date);
	else
		$date = mysql2date($d, $comment->comment_date);
	return apply_filters('get_comment_date', $date, $d);
}

/**
 * Display the comment date of the current comment.
 *
 * @since 0.71
 *
 * @param string $d The format of the date (defaults to user's config)
 */
function comment_date( $d = '' ) {
	echo get_comment_date( $d );
}

/**
 * Retrieve the excerpt of the current comment.
 *
 * Will cut each word and only output the first 20 words with '...' at the end.
 * If the word count is less than 20, then no truncating is done and no '...'
 * will appear.
 *
 * @since 1.5.0
 * @uses $comment
 * @uses apply_filters() Calls 'get_comment_excerpt' on truncated comment
 *
 * @return string The maybe truncated comment with 20 words or less
 */
function get_comment_excerpt() {
	global $comment;
	$comment_text = strip_tags($comment->comment_content);
	$blah = explode(' ', $comment_text);
	if (count($blah) > 20) {
		$k = 20;
		$use_dotdotdot = 1;
	} else {
		$k = count($blah);
		$use_dotdotdot = 0;
	}
	$excerpt = '';
	for ($i=0; $i<$k; $i++) {
		$excerpt .= $blah[$i] . ' ';
	}
	$excerpt .= ($use_dotdotdot) ? '...' : '';
	return apply_filters('get_comment_excerpt', $excerpt);
}

/**
 * Display the excerpt of the current comment.
 *
 * @since 1.2.0
 * @uses apply_filters() Calls 'comment_excerpt' hook before displaying excerpt
 */
function comment_excerpt() {
	echo apply_filters('comment_excerpt', get_comment_excerpt() );
}

/**
 * Retrieve the comment id of the current comment.
 *
 * @since 1.5.0
 * @uses $comment
 * @uses apply_filters() Calls the 'get_comment_ID' hook for the comment ID
 *
 * @return int The comment ID
 */
function get_comment_ID() {
	global $comment;
	return apply_filters('get_comment_ID', $comment->comment_ID);
}

/**
 * Displays the comment id of the current comment.
 *
 * @since 0.71
 * @see get_comment_ID() Echos Result
 */
function comment_ID() {
	echo get_comment_ID();
}

/**
 * Retrieve the link to a given comment.
 *
 * @since 1.5.0
 * @uses $comment
 *
 * @param object|string|int $comment Comment to retrieve.
 * @param array $args Optional args.
 * @return string The permalink to the given comment.
 */
function get_comment_link( $comment = null, $args = array() ) {
	global $wp_rewrite, $in_comment_loop;

	$comment = get_comment($comment);

	// Backwards compat
	if ( !is_array($args) ) {
		$page = $args;
		$args = array();
		$args['page'] = $page;
	}

	$defaults = array( 'type' => 'all', 'page' => '', 'per_page' => '', 'max_depth' => '' );
	$args = wp_parse_args( $args, $defaults );

	if ( '' === $args['per_page'] && get_option('page_comments') )
		$args['per_page'] = get_option('comments_per_page');

	if ( empty($args['per_page']) ) {
		$args['per_page'] = 0;
		$args['page'] = 0;
	}

	if ( $args['per_page'] ) {
		if ( '' == $args['page'] )
			$args['page'] = ( !empty($in_comment_loop) ) ? get_query_var('cpage') : get_page_of_comment( $comment->comment_ID, $args );

		if ( $wp_rewrite->using_permalinks() )
			$link = user_trailingslashit( trailingslashit( get_permalink( $comment->comment_post_ID ) ) . 'comment-page-' . $args['page'], 'comment' );
		else
			$link = add_query_arg( 'cpage', $args['page'], get_permalink( $comment->comment_post_ID ) );
	} else {
		$link = get_permalink( $comment->comment_post_ID );
	}

	return apply_filters( 'get_comment_link', $link . '#comment-' . $comment->comment_ID, $comment, $args );
}

/**
 * Retrieves the link to the current post comments.
 *
 * @since 1.5.0
 *
 * @return string The link to the comments
 */
function get_comments_link() {
	return get_permalink() . '#comments';
}

/**
 * Displays the link to the current post comments.
 *
 * @since 0.71
 *
 * @param string $deprecated Not Used
 * @param bool $deprecated Not Used
 */
function comments_link( $deprecated = '', $deprecated = '' ) {
	echo get_comments_link();
}

/**
 * Retrieve the amount of comments a post has.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls the 'get_comments_number' hook on the number of comments
 *
 * @param int $post_id The Post ID
 * @return int The number of comments a post has
 */
function get_comments_number( $post_id = 0 ) {
	global $id;
	$post_id = (int) $post_id;

	if ( !$post_id )
		$post_id = (int) $id;

	$post = get_post($post_id);
	if ( ! isset($post->comment_count) )
		$count = 0;
	else
		$count = $post->comment_count;

	return apply_filters('get_comments_number', $count);
}

/**
 * Display the language string for the number of comments the current post has.
 *
 * @since 0.71
 * @uses $id
 * @uses apply_filters() Calls the 'comments_number' hook on the output and number of comments respectively.
 *
 * @param string $zero Text for no comments
 * @param string $one Text for one comment
 * @param string $more Text for more than one comment
 * @param string $deprecated Not used.
 */
function comments_number( $zero = false, $one = false, $more = false, $deprecated = '' ) {
	global $id;
	$number = get_comments_number($id);

	if ( $number > 1 )
		$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments') : $more);
	elseif ( $number == 0 )
		$output = ( false === $zero ) ? __('No Comments') : $zero;
	else // must be one
		$output = ( false === $one ) ? __('1 Comment') : $one;

	echo apply_filters('comments_number', $output, $number);
}

/**
 * Retrieve the text of the current comment.
 *
 * @since 1.5.0
 * @uses $comment
 *
 * @return string The comment content
 */
function get_comment_text() {
	global $comment;
	return apply_filters('get_comment_text', $comment->comment_content);
}

/**
 * Displays the text of the current comment.
 *
 * @since 0.71
 * @uses apply_filters() Passes the comment content through the 'comment_text' hook before display
 * @uses get_comment_text() Gets the comment content
 */
function comment_text() {
	echo apply_filters('comment_text', get_comment_text() );
}

/**
 * Retrieve the comment time of the current comment.
 *
 * @since 1.5.0
 * @uses $comment
 * @uses apply_filter() Calls 'get_comment_time' hook with the formatted time, the $d parameter, and $gmt parameter passed.
 *
 * @param string $d Optional. The format of the time (defaults to user's config)
 * @param bool $gmt Whether to use the GMT date
 * @return string The formatted time
 */
function get_comment_time( $d = '', $gmt = false ) {
	global $comment;
	$comment_date = $gmt? $comment->comment_date_gmt : $comment->comment_date;
	if ( '' == $d )
		$date = mysql2date(get_option('time_format'), $comment_date);
	else
		$date = mysql2date($d, $comment_date);
	return apply_filters('get_comment_time', $date, $d, $gmt);
}

/**
 * Display the comment time of the current comment.
 *
 * @since 0.71
 *
 * @param string $d Optional. The format of the time (defaults to user's config)
 */
function comment_time( $d = '' ) {
	echo get_comment_time($d);
}

/**
 * Retrieve the comment type of the current comment.
 *
 * @since 1.5.0
 * @uses $comment
 * @uses apply_filters() Calls the 'get_comment_type' hook on the comment type
 *
 * @return string The comment type
 */
function get_comment_type() {
	global $comment;

	if ( '' == $comment->comment_type )
		$comment->comment_type = 'comment';

	return apply_filters('get_comment_type', $comment->comment_type);
}

/**
 * Display the comment type of the current comment.
 *
 * @since 0.71
 *
 * @param string $commenttxt The string to display for comment type
 * @param string $trackbacktxt The string to display for trackback type
 * @param string $pingbacktxt The string to display for pingback type
 */
function comment_type($commenttxt = false, $trackbacktxt = false, $pingbacktxt = false) {
    if ( false === $commenttxt ) $commenttxt = _x( 'Comment', 'noun' );
    if ( false === $trackbacktxt ) $trackbacktxt = __( 'Trackback' );
    if ( false === $pingbacktxt ) $pingbacktxt = __( 'Pingback' );
	$type = get_comment_type();
	switch( $type ) {
		case 'trackback' :
			echo $trackbacktxt;
			break;
		case 'pingback' :
			echo $pingbacktxt;
			break;
		default :
			echo $commenttxt;
	}
}

/**
 * Retrieve The current post's trackback URL.
 *
 * There is a check to see if permalink's have been enabled and if so, will
 * retrieve the pretty path. If permalinks weren't enabled, the ID of the
 * current post is used and appended to the correct page to go to.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'trackback_url' on the resulting trackback URL
 * @uses $id
 *
 * @return string The trackback URL after being filtered
 */
function get_trackback_url() {
	global $id;
	if ( '' != get_option('permalink_structure') ) {
		$tb_url = trailingslashit(get_permalink()) . user_trailingslashit('trackback', 'single_trackback');
	} else {
		$tb_url = get_option('siteurl') . '/wp-trackback.php?p=' . $id;
	}
	return apply_filters('trackback_url', $tb_url);
}

/**
 * Displays the current post's trackback URL.
 *
 * @since 0.71
 * @uses get_trackback_url() Gets the trackback url for the current post
 *
 * @param bool $deprecated Remove backwards compat in 2.5
 * @return void|string Should only be used to echo the trackback URL, use get_trackback_url() for the result instead.
 */
function trackback_url($deprecated = true) {
	if ($deprecated) echo get_trackback_url();
	else return get_trackback_url();
}

/**
 * Generates and displays the RDF for the trackback information of current post.
 *
 * @since 0.71
 *
 * @param int $deprecated Not used (Was $timezone = 0)
 */
function trackback_rdf($deprecated = '') {
	if (stripos($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator') === false) {
		echo '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
				xmlns:dc="http://purl.org/dc/elements/1.1/"
				xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
			<rdf:Description rdf:about="';
		the_permalink();
		echo '"'."\n";
		echo '    dc:identifier="';
		the_permalink();
		echo '"'."\n";
		echo '    dc:title="'.str_replace('--', '&#x2d;&#x2d;', wptexturize(strip_tags(get_the_title()))).'"'."\n";
		echo '    trackback:ping="'.get_trackback_url().'"'." />\n";
		echo '</rdf:RDF>';
	}
}

/**
 * Whether the current post is open for comments.
 *
 * @since 1.5.0
 * @uses $post
 *
 * @param int $post_id An optional post ID to check instead of the current post.
 * @return bool True if the comments are open
 */
function comments_open( $post_id=NULL ) {

	$_post = get_post($post_id);

	$open = ( 'open' == $_post->comment_status );
	return apply_filters( 'comments_open', $open, $post_id );
}

/**
 * Whether the current post is open for pings.
 *
 * @since 1.5.0
 * @uses $post
 *
 * @param int $post_id An optional post ID to check instead of the current post.
 * @return bool True if pings are accepted
 */
function pings_open( $post_id = NULL ) {

	$_post = get_post($post_id);

	$open = ( 'open' == $_post->ping_status );
	return apply_filters( 'pings_open', $open, $post_id );
}

/**
 * Displays form token for unfiltered comments.
 *
 * Will only display nonce token if the current user has permissions for
 * unfiltered html. Won't display the token for other users.
 *
 * The function was backported to 2.0.10 and was added to versions 2.1.3 and
 * above. Does not exist in versions prior to 2.0.10 in the 2.0 branch and in
 * the 2.1 branch, prior to 2.1.3. Technically added in 2.2.0.
 *
 * Backported to 2.0.10.
 *
 * @since 2.1.3
 * @uses $post Gets the ID of the current post for the token
 */
function wp_comment_form_unfiltered_html_nonce() {
	global $post;

	$post_id = 0;
	if ( !empty($post) )
		$post_id = $post->ID;

	if ( current_user_can('unfiltered_html') )
		wp_nonce_field('unfiltered-html-comment_' . $post_id, '_wp_unfiltered_html_comment', false);
}

/**
 * Loads the comment template specified in $file.
 *
 * Will not display the comments template if not on single post or page, or if
 * the post does not have comments.
 *
 * Uses the WordPress database object to query for the comments. The comments
 * are passed through the 'comments_array' filter hook with the list of comments
 * and the post ID respectively.
 *
 * The $file path is passed through a filter hook called, 'comments_template'
 * which includes the TEMPLATEPATH and $file combined. Tries the $filtered path
 * first and if it fails it will require the default comment themplate from the
 * default theme. If either does not exist, then the WordPress process will be
 * halted. It is advised for that reason, that the default theme is not deleted.
 *
 * @since 1.5.0
 * @global array $comment List of comment objects for the current post
 * @uses $wpdb
 * @uses $id
 * @uses $post
 * @uses $withcomments Will not try to get the comments if the post has none.
 *
 * @param string $file Optional, default '/comments.php'. The file to load
 * @param bool $separate_comments Optional, whether to separate the comments by comment type. Default is false.
 * @return null Returns null if no comments appear
 */
function comments_template( $file = '/comments.php', $separate_comments = false ) {
	global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity, $overridden_cpage;

	if ( ! (is_single() || is_page() || $withcomments) )
		return;

	if ( empty($file) )
		$file = '/comments.php';

	$req = get_option('require_name_email');
	$commenter = wp_get_current_commenter();
	extract($commenter, EXTR_SKIP);

	/** @todo Use API instead of SELECTs. */
	if ( $user_ID) {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date_gmt", $post->ID, $user_ID));
	} else if ( empty($comment_author) ) {
		$comments = get_comments( array('post_id' => $post->ID, 'status' => 'approve', 'order' => 'ASC') );
	} else {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date_gmt", $post->ID, wp_specialchars_decode($comment_author,ENT_QUOTES), $comment_author_email));
	}

	// keep $comments for legacy's sake
	$wp_query->comments = apply_filters( 'comments_array', $comments, $post->ID );
	$comments = &$wp_query->comments;
	$wp_query->comment_count = count($wp_query->comments);
	update_comment_cache($wp_query->comments);

	if ( $separate_comments ) {
		$wp_query->comments_by_type = &separate_comments($comments);
		$comments_by_type = &$wp_query->comments_by_type;
	}

	$overridden_cpage = FALSE;
	if ( '' == get_query_var('cpage') && get_option('page_comments') ) {
		set_query_var( 'cpage', 'newest' == get_option('default_comments_page') ? get_comment_pages_count() : 1 );
		$overridden_cpage = TRUE;
	}

	if ( !defined('COMMENTS_TEMPLATE') || !COMMENTS_TEMPLATE)
		define('COMMENTS_TEMPLATE', true);

	$include = apply_filters('comments_template', STYLESHEETPATH . $file );
	if ( file_exists( $include ) )
		require( $include );
	elseif ( file_exists( TEMPLATEPATH . $file ) )
		require( TEMPLATEPATH .  $file );
	else
		require( get_theme_root() . '/default/comments.php');
}

/**
 * Displays the JS popup script to show a comment.
 *
 * If the $file parameter is empty, then the home page is assumed. The defaults
 * for the window are 400px by 400px.
 *
 * For the comment link popup to work, this function has to be called or the
 * normal comment link will be assumed.
 *
 * @since 0.71
 * @global string $wpcommentspopupfile The URL to use for the popup window
 * @global int $wpcommentsjavascript Whether to use JavaScript or not. Set when function is called
 *
 * @param int $width Optional. The width of the popup window
 * @param int $height Optional. The height of the popup window
 * @param string $file Optional. Sets the location of the popup window
 */
function comments_popup_script($width=400, $height=400, $file='') {
	global $wpcommentspopupfile, $wpcommentsjavascript;

	if (empty ($file)) {
		$wpcommentspopupfile = '';  // Use the index.
	} else {
		$wpcommentspopupfile = $file;
	}

	$wpcommentsjavascript = 1;
	$javascript = "<script type='text/javascript'>\nfunction wpopen (macagna) {\n    window.open(macagna, '_blank', 'width=$width,height=$height,scrollbars=yes,status=yes');\n}\n</script>\n";
	echo $javascript;
}

/**
 * Displays the link to the comments popup window for the current post ID.
 *
 * Is not meant to be displayed on single posts and pages. Should be used on the
 * lists of posts
 *
 * @since 0.71
 * @uses $id
 * @uses $wpcommentspopupfile
 * @uses $wpcommentsjavascript
 * @uses $post
 *
 * @param string $zero The string to display when no comments
 * @param string $one The string to display when only one comment is available
 * @param string $more The string to display when there are more than one comment
 * @param string $css_class The CSS class to use for comments
 * @param string $none The string to display when comments have been turned off
 * @return null Returns null on single posts and pages.
 */
function comments_popup_link( $zero = false, $one = false, $more = false, $css_class = '', $none = false ) {
	global $id, $wpcommentspopupfile, $wpcommentsjavascript, $post;

    if ( false === $zero ) $zero = __( 'No Comments' );
    if ( false === $one ) $one = __( '1 Comment' );
    if ( false === $more ) $more = __( '% Comments' );
    if ( false === $none ) $none = __( 'Comments Off' );

	$number = get_comments_number( $id );

	if ( 0 == $number && !comments_open() && !pings_open() ) {
		echo '<span' . ((!empty($css_class)) ? ' class="' . $css_class . '"' : '') . '>' . $none . '</span>';
		return;
	}

	if ( post_password_required() ) {
		echo __('Enter your password to view comments');
		return;
	}

	echo '<a href="';
	if ( $wpcommentsjavascript ) {
		if ( empty( $wpcommentspopupfile ) )
			$home = get_option('home');
		else
			$home = get_option('siteurl');
		echo $home . '/' . $wpcommentspopupfile . '?comments_popup=' . $id;
		echo '" onclick="wpopen(this.href); return false"';
	} else { // if comments_popup_script() is not in the template, display simple comment link
		if ( 0 == $number )
			echo get_permalink() . '#respond';
		else
			comments_link();
		echo '"';
	}

	if ( !empty( $css_class ) ) {
		echo ' class="'.$css_class.'" ';
	}
	$title = esc_attr( get_the_title() );

	echo apply_filters( 'comments_popup_link_attributes', '' );

	echo ' title="' . sprintf( __('Comment on %s'), $title ) . '">';
	comments_number( $zero, $one, $more, $number );
	echo '</a>';
}

/**
 * Retrieve HTML content for reply to comment link.
 *
 * The default arguments that can be override are 'add_below', 'respond_id',
 * 'reply_text', 'login_text', and 'depth'. The 'login_text' argument will be
 * used, if the user must log in or register first before posting a comment. The
 * 'reply_text' will be used, if they can post a reply. The 'add_below' and
 * 'respond_id' arguments are for the JavaScript moveAddCommentForm() function
 * parameters.
 *
 * @since 2.7.0
 *
 * @param array $args Optional. Override default options.
 * @param int $comment Optional. Comment being replied to.
 * @param int $post Optional. Post that the comment is going to be displayed on.
 * @return string|bool|null Link to show comment form, if successful. False, if comments are closed.
 */
function get_comment_reply_link($args = array(), $comment = null, $post = null) {
	global $user_ID;

	$defaults = array('add_below' => 'comment', 'respond_id' => 'respond', 'reply_text' => __('Reply'),
		'login_text' => __('Log in to Reply'), 'depth' => 0, 'before' => '', 'after' => '');

	$args = wp_parse_args($args, $defaults);

	if ( 0 == $args['depth'] || $args['max_depth'] <= $args['depth'] )
		return;

	extract($args, EXTR_SKIP);

	$comment = get_comment($comment);
	$post = get_post($post);

	if ( !comments_open($post->ID) )
		return false;

	$link = '';

	if ( get_option('comment_registration') && !$user_ID )
		$link = '<a rel="nofollow" class="comment-reply-login" href="' . clean_url( wp_login_url( get_permalink() ) ) . '">' . $login_text . '</a>';
	else
		$link = "<a rel='nofollow' class='comment-reply-link' href='" . clean_url( add_query_arg( 'replytocom', $comment->comment_ID ) ) . "#" . $respond_id . "' onclick='return addComment.moveForm(\"$add_below-$comment->comment_ID\", \"$comment->comment_ID\", \"$respond_id\", \"$post->ID\")'>$reply_text</a>";
	return apply_filters('comment_reply_link', $before . $link . $after, $args, $comment, $post);
}

/**
 * Displays the HTML content for reply to comment link.
 *
 * @since 2.7.0
 * @see get_comment_reply_link() Echoes result
 *
 * @param array $args Optional. Override default options.
 * @param int $comment Optional. Comment being replied to.
 * @param int $post Optional. Post that the comment is going to be displayed on.
 * @return string|bool|null Link to show comment form, if successful. False, if comments are closed.
 */
function comment_reply_link($args = array(), $comment = null, $post = null) {
	echo get_comment_reply_link($args, $comment, $post);
}

/**
 * Retrieve HTML content for reply to post link.
 *
 * The default arguments that can be override are 'add_below', 'respond_id',
 * 'reply_text', 'login_text', and 'depth'. The 'login_text' argument will be
 * used, if the user must log in or register first before posting a comment. The
 * 'reply_text' will be used, if they can post a reply. The 'add_below' and
 * 'respond_id' arguments are for the JavaScript moveAddCommentForm() function
 * parameters.
 *
 * @since 2.7.0
 *
 * @param array $args Optional. Override default options.
 * @param int|object $post Optional. Post that the comment is going to be displayed on.  Defaults to current post.
 * @return string|bool|null Link to show comment form, if successful. False, if comments are closed.
 */
function get_post_reply_link($args = array(), $post = null) {
	global $user_ID;

	$defaults = array('add_below' => 'post', 'respond_id' => 'respond', 'reply_text' => __('Leave a Comment'),
		'login_text' => __('Log in to leave a Comment'), 'before' => '', 'after' => '');

	$args = wp_parse_args($args, $defaults);
	extract($args, EXTR_SKIP);
	$post = get_post($post);

	if ( !comments_open($post->ID) )
		return false;

	if ( get_option('comment_registration') && !$user_ID ) {
		$link = '<a rel="nofollow" href="' . wp_login_url( get_permalink() ) . '">' . $login_text . '</a>';
	} else {
		$link = "<a rel='nofollow' class='comment-reply-link' href='" . get_permalink($post->ID) . "#$respond_id' onclick='return addComment.moveForm(\"$add_below-$post->ID\", \"0\", \"$respond_id\", \"$post->ID\")'>$reply_text</a>";
	}
	return apply_filters('post_comments_link', $before . $link . $after, $post);
}

/**
 * Displays the HTML content for reply to post link.
 * @since 2.7.0
 * @see get_post_reply_link()
 *
 * @param array $args Optional. Override default options.
 * @param int|object $post Optional. Post that the comment is going to be displayed on.
 * @return string|bool|null Link to show comment form, if successful. False, if comments are closed.
 */
function post_reply_link($args = array(), $post = null) {
	echo get_post_reply_link($args, $post);
}

/**
 * Retrieve HTML content for cancel comment reply link.
 *
 * @since 2.7.0
 *
 * @param string $text Optional. Text to display for cancel reply link.
 */
function get_cancel_comment_reply_link($text = '') {
	if ( empty($text) )
		$text = __('Click here to cancel reply.');

	$style = isset($_GET['replytocom']) ? '' : ' style="display:none;"';
	$link = wp_specialchars( remove_query_arg('replytocom') ) . '#respond';
	return apply_filters('cancel_comment_reply_link', '<a rel="nofollow" id="cancel-comment-reply-link" href="' . $link . '"' . $style . '>' . $text . '</a>', $link, $text);
}

/**
 * Display HTML content for cancel comment reply link.
 *
 * @since 2.7.0
 *
 * @param string $text Optional. Text to display for cancel reply link.
 */
function cancel_comment_reply_link($text = '') {
	echo get_cancel_comment_reply_link($text);
}

/**
 * Output hidden input HTML for replying to comments.
 *
 * @since 2.7.0
 */
function comment_id_fields() {
	global $id;

	$replytoid = isset($_GET['replytocom']) ? (int) $_GET['replytocom'] : 0;
	echo "<input type='hidden' name='comment_post_ID' value='$id' id='comment_post_ID' />\n";
	echo "<input type='hidden' name='comment_parent' id='comment_parent' value='$replytoid' />\n";
}

/**
 * Display text based on comment reply status. Only affects users with Javascript disabled.
 *
 * @since 2.7.0
 *
 * @param string $noreplytext Optional. Text to display when not replying to a comment.
 * @param string $replytext Optional. Text to display when replying to a comment. Accepts "%s" for the author of the comment being replied to.
 * @param string $linktoparent Optional. Boolean to control making the author's name a link to their comment.
 */
function comment_form_title( $noreplytext = false, $replytext = false, $linktoparent = TRUE ) {
	global $comment;

	if ( false === $noreplytext ) $noreplytext = __( 'Leave a Reply' );
	if ( false === $replytext ) $replytext = __( 'Leave a Reply to %s' );

	$replytoid = isset($_GET['replytocom']) ? (int) $_GET['replytocom'] : 0;

	if ( 0 == $replytoid )
		echo $noreplytext;
	else {
		$comment = get_comment($replytoid);
		$author = ( $linktoparent ) ? '<a href="#comment-' . get_comment_ID() . '">' . get_comment_author() . '</a>' : get_comment_author();
		printf( $replytext, $author );
	}
}

/**
 * HTML comment list class.
 *
 * @package WordPress
 * @uses Walker
 * @since unknown
 */
class Walker_Comment extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since unknown
	 * @var string
	 */
	var $tree_type = 'comment';

	/**
	 * @see Walker::$db_fields
	 * @since unknown
	 * @var array
	 */
	var $db_fields = array ('parent' => 'comment_parent', 'id' => 'comment_ID');

	/**
	 * @see Walker::start_lvl()
	 * @since unknown
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of comment.
	 * @param array $args Uses 'style' argument for type of HTML list.
	 */
	function start_lvl(&$output, $depth, $args) {
		$GLOBALS['comment_depth'] = $depth + 1;

		switch ( $args['style'] ) {
			case 'div':
				break;
			case 'ol':
				echo "<ol class='children'>\n";
				break;
			default:
			case 'ul':
				echo "<ul class='children'>\n";
				break;
		}
	}

	/**
	 * @see Walker::end_lvl()
	 * @since unknown
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of comment.
	 * @param array $args Will only append content if style argument value is 'ol' or 'ul'.
	 */
	function end_lvl(&$output, $depth, $args) {
		$GLOBALS['comment_depth'] = $depth + 1;

		switch ( $args['style'] ) {
			case 'div':
				break;
			case 'ol':
				echo "</ol>\n";
				break;
			default:
			case 'ul':
				echo "</ul>\n";
				break;
		}
	}

	/**
	 * @see Walker::start_el()
	 * @since unknown
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $comment Comment data object.
	 * @param int $depth Depth of comment in reference to parents.
	 * @param array $args
	 */
	function start_el(&$output, $comment, $depth, $args) {
		$depth++;
		$GLOBALS['comment_depth'] = $depth;

		if ( !empty($args['callback']) ) {
			call_user_func($args['callback'], $comment, $args, $depth);
			return;
		}

		$GLOBALS['comment'] = $comment;
		extract($args, EXTR_SKIP);

		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}
?>
		<<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
		<?php if ( 'ul' == $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
		<?php endif; ?>
		<div class="comment-author vcard">
		<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['avatar_size'] ); ?>
		<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
		</div>
<?php if ($comment->comment_approved == '0') : ?>
		<em><?php _e('Your comment is awaiting moderation.') ?></em>
		<br />
<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'&nbsp;&nbsp;','') ?></div>

		<?php comment_text() ?>

		<div class="reply">
		<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		</div>
		<?php if ( 'ul' == $args['style'] ) : ?>
		</div>
		<?php endif; ?>
<?php
	}

	/**
	 * @see Walker::end_el()
	 * @since unknown
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $comment
	 * @param int $depth Depth of comment.
	 * @param array $args
	 */
	function end_el(&$output, $comment, $depth, $args) {
		if ( !empty($args['end-callback']) ) {
			call_user_func($args['end-callback'], $comment, $args, $depth);
			return;
		}
		if ( 'div' == $args['style'] )
			echo "</div>\n";
		else
			echo "</li>\n";
	}

}

/**
 * List comments
 *
 * Used in the comments.php template to list comments for a particular post
 *
 * @since 2.7.0
 * @uses Walker_Comment
 *
 * @param string|array $args Formatting options
 * @param array $comments Optional array of comment objects.  Defaults to $wp_query->comments
 */
function wp_list_comments($args = array(), $comments = null ) {
	global $wp_query, $comment_alt, $comment_depth, $comment_thread_alt, $overridden_cpage, $in_comment_loop;

	$in_comment_loop = true;

	$comment_alt = $comment_thread_alt = 0;
	$comment_depth = 1;

	$defaults = array('walker' => null, 'max_depth' => '', 'style' => 'ul', 'callback' => null, 'end-callback' => null, 'type' => 'all',
		'page' => '', 'per_page' => '', 'avatar_size' => 32, 'reverse_top_level' => null, 'reverse_children' => '');

	$r = wp_parse_args( $args, $defaults );

	// Figure out what comments we'll be looping through ($_comments)
	if ( null !== $comments ) {
		$comments = (array) $comments;
		if ( empty($comments) )
			return;
		if ( 'all' != $r['type'] ) {
			$comments_by_type = &separate_comments($comments);
			if ( empty($comments_by_type[$r['type']]) )
				return;
			$_comments = $comments_by_type[$r['type']];
		} else {
			$_comments = $comments;
		}
	} else {
		if ( empty($wp_query->comments) )
			return;
		if ( 'all' != $r['type'] ) {
			if ( empty($wp_query->comments_by_type) )
				$wp_query->comments_by_type = &separate_comments($wp_query->comments);
			if ( empty($wp_query->comments_by_type[$r['type']]) )
				return;
			$_comments = $wp_query->comments_by_type[$r['type']];
		} else {
			$_comments = $wp_query->comments;
		}
	}

	if ( '' === $r['per_page'] && get_option('page_comments') )
		$r['per_page'] = get_query_var('comments_per_page');

	if ( empty($r['per_page']) ) {
		$r['per_page'] = 0;
		$r['page'] = 0;
	}

	if ( '' === $r['max_depth'] ) {
		if ( get_option('thread_comments') )
			$r['max_depth'] = get_option('thread_comments_depth');
		else
			$r['max_depth'] = -1;
	}

	if ( '' === $r['page'] ) {
		if ( empty($overridden_cpage) ) {
			$r['page'] = get_query_var('cpage');
		} else {
			$threaded = ( -1 == $r['max_depth'] ) ? false : true;
			$r['page'] = ( 'newest' == get_option('default_comments_page') ) ? get_comment_pages_count($_comments, $r['per_page'], $threaded) : 1;
			set_query_var( 'cpage', $r['page'] );
		}
	}
	// Validation check
	$r['page'] = intval($r['page']);
	if ( 0 == $r['page'] && 0 != $r['per_page'] )
		$r['page'] = 1;

	if ( null === $r['reverse_top_level'] )
		$r['reverse_top_level'] = ( 'desc' == get_option('comment_order') ) ? TRUE : FALSE;

	extract( $r, EXTR_SKIP );

	if ( empty($walker) )
		$walker = new Walker_Comment;

	$walker->paged_walk($_comments, $max_depth, $page, $per_page, $r);
	$wp_query->max_num_comment_pages = $walker->max_pages;

	$in_comment_loop = false;
}

?>

<?php
/**
 * Handle Trackbacks and Pingbacks Sent to WordPress
 *
 * @since 0.71
 *
 * @package WordPress
 * @subpackage Trackbacks
 */

if (empty($wp)) {
	require_once( dirname( __FILE__ ) . '/wp-load.php' );
	wp( array( 'tb' => '1' ) );
}

// Always run as an unauthenticated user.
wp_set_current_user( 0 );

/**
 * Response to a trackback.
 *
 * Responds with an error or success XML message.
 *
 * @since 0.71
 *
 * @param mixed  $error         Whether there was an error.
 *                              Default '0'. Accepts '0' or '1', true or false.
 * @param string $error_message Error message if an error occurred.
 */
function trackback_response($error = 0, $error_message = '') {
	header('Content-Type: text/xml; charset=' . get_option('blog_charset') );
	if ($error) {
		echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		echo "<response>\n";
		echo "<error>1</error>\n";
		echo "<message>$error_message</message>\n";
		echo "</response>";
		die();
	} else {
		echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
		echo "<response>\n";
		echo "<error>0</error>\n";
		echo "</response>";
	}
}

// Trackback is done by a POST.
$request_array = 'HTTP_POST_VARS';

if ( !isset($_GET['tb_id']) || !$_GET['tb_id'] ) {
	$tb_id = explode('/', $_SERVER['REQUEST_URI']);
	$tb_id = intval( $tb_id[ count($tb_id) - 1 ] );
}

$tb_url  = isset($_POST['url'])     ? $_POST['url']     : '';
$charset = isset($_POST['charset']) ? $_POST['charset'] : '';

// These three are stripslashed here so they can be properly escaped after mb_convert_encoding().
$title     = isset($_POST['title'])     ? wp_unslash($_POST['title'])      : '';
$excerpt   = isset($_POST['excerpt'])   ? wp_unslash($_POST['excerpt'])    : '';
$blog_name = isset($_POST['blog_name']) ? wp_unslash($_POST['blog_name'])  : '';

if ($charset)
	$charset = str_replace( array(',', ' '), '', strtoupper( trim($charset) ) );
else
	$charset = 'ASCII, UTF-8, ISO-8859-1, JIS, EUC-JP, SJIS';

// No valid uses for UTF-7.
if ( false !== strpos($charset, 'UTF-7') )
	die;

// For international trackbacks.
if ( function_exists('mb_convert_encoding') ) {
	$title     = mb_convert_encoding($title, get_option('blog_charset'), $charset);
	$excerpt   = mb_convert_encoding($excerpt, get_option('blog_charset'), $charset);
	$blog_name = mb_convert_encoding($blog_name, get_option('blog_charset'), $charset);
}

// Now that mb_convert_encoding() has been given a swing, we need to escape these three.
$title     = wp_slash($title);
$excerpt   = wp_slash($excerpt);
$blog_name = wp_slash($blog_name);

if ( is_single() || is_page() )
	$tb_id = $posts[0]->ID;

if ( !isset($tb_id) || !intval( $tb_id ) )
	trackback_response(1, 'I really need an ID for this to work.');

if (empty($title) && empty($tb_url) && empty($blog_name)) {
	// If it doesn't look like a trackback at all.
	wp_redirect(get_permalink($tb_id));
	exit;
}

if ( !empty($tb_url) && !empty($title) ) {
	header('Content-Type: text/xml; charset=' . get_option('blog_charset') );

	if ( !pings_open($tb_id) )
		trackback_response(1, 'Sorry, trackbacks are closed for this item.');

	$title =  wp_html_excerpt( $title, 250, '&#8230;' );
	$excerpt = wp_html_excerpt( $excerpt, 252, '&#8230;' );

	$comment_post_ID = (int) $tb_id;
	$comment_author = $blog_name;
	$comment_author_email = '';
	$comment_author_url = $tb_url;
	$comment_content = "<strong>$title</strong>\n\n$excerpt";
	$comment_type = 'trackback';

	$dupe = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_author_url = %s", $comment_post_ID, $comment_author_url) );
	if ( $dupe )
		trackback_response(1, 'We already have a ping from that URL for this post.');

	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type');

	wp_new_comment($commentdata);
	$trackback_id = $wpdb->insert_id;

	/**
	 * Fires after a trackback is added to a post.
	 *
	 * @since 1.2.0
	 *
	 * @param int $trackback_id Trackback ID.
	 */
	do_action( 'trackback_post', $trackback_id );
	trackback_response( 0 );
}

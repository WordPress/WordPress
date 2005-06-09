<?php
require( dirname(__FILE__) . '/wp-config.php' );

$comment_post_ID = (int) $_POST['comment_post_ID'];

$status = $wpdb->get_row("SELECT post_status, comment_status FROM $wpdb->posts WHERE ID = '$comment_post_ID'");

if ( empty($status->comment_status) ) {
	do_action('comment_id_not_found', $comment_post_ID);
	exit;
} elseif ( 'closed' ==  $status->comment_status ) {
	do_action('comment_closed', $comment_post_ID);
	die( __('Sorry, comments are closed for this item.') );
} elseif ( 'draft' == $status->post_status ) {
	do_action('comment_on_draft', $comment_post_ID);
	exit;
}

$comment_author       = trim($_POST['author']);
$comment_author_email = trim($_POST['email']);
$comment_author_url   = trim($_POST['url']);
$comment_content      = trim($_POST['comment']);

// If the user is logged in
get_currentuserinfo();
if ( $user_ID ) :
	$comment_author       = addslashes($user_identity);
	$comment_author_email = addslashes($user_email);
	$comment_author_url   = addslashes($user_url);
else :
	if ( get_option('comment_registration') )
		die( __('Sorry, you must be logged in to post a comment.') );
endif;

$comment_type = '';

if ( get_settings('require_name_email') && !$user_ID ) {
	if ( 6 > strlen($comment_author_email) || '' == $comment_author )
		die( __('Error: please fill the required fields (name, email).') );
	elseif ( !is_email($comment_author_email))
		die( __('Error: please enter a valid email address.') );
}

if ( '' == $comment_content )
	die( __('Error: please type a comment.') );

$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'user_ID');

wp_new_comment($commentdata);

setcookie('comment_author_' . COOKIEHASH, stripslashes($comment_author), time() + 30000000, COOKIEPATH);
setcookie('comment_author_email_' . COOKIEHASH, stripslashes($comment_author_email), time() + 30000000, COOKIEPATH);
setcookie('comment_author_url_' . COOKIEHASH, stripslashes($comment_author_url), time() + 30000000, COOKIEPATH);

nocache_headers();

$location = (empty($_POST['redirect_to'])) ? $_SERVER["HTTP_REFERER"] : $_POST['redirect_to']; 

wp_redirect($location);
?>
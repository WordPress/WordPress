<?php
/**
 * Accepts file uploads from swfupload or other asynchronous upload methods.
 *
 * @package WordPress
 * @subpackage Administration
 */

define('WP_ADMIN', true);

if ( defined('ABSPATH') )
	require_once(ABSPATH . 'wp-load.php');
else
	require_once('../wp-load.php');

// Flash often fails to send cookies with the POST or upload, so we need to pass it in GET or POST instead
if ( is_ssl() && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
	$_COOKIE[SECURE_AUTH_COOKIE] = $_REQUEST['auth_cookie'];
elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
	$_COOKIE[AUTH_COOKIE] = $_REQUEST['auth_cookie'];
if ( empty($_COOKIE[LOGGED_IN_COOKIE]) && !empty($_REQUEST['logged_in_cookie']) )
	$_COOKIE[LOGGED_IN_COOKIE] = $_REQUEST['logged_in_cookie'];
unset($current_user);
require_once('./admin.php');

header('Content-Type: text/html; charset=' . get_option('blog_charset'));

if ( !current_user_can('upload_files') )
	wp_die(__('You do not have permission to upload files.'));

// just fetch the detail form for that attachment
if ( isset($_REQUEST['attachment_id']) && ($id = intval($_REQUEST['attachment_id'])) && $_REQUEST['fetch'] ) {
	$post = get_post( $id );
	if ( 'attachment' != $post->post_type )
		wp_die( __( 'Unknown post type.' ) );
	$post_type_object = get_post_type_object( 'attachment' );
	if ( ! current_user_can( $post_type_object->cap->edit_post, $id ) )
		wp_die( __( 'You are not allowed to edit this item.' ) );

	if ( 2 == $_REQUEST['fetch'] ) {
		add_filter('attachment_fields_to_edit', 'media_single_attachment_fields_to_edit', 10, 2);
		echo get_media_item($id, array( 'send' => false, 'delete' => true ));
	} else {
		add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2);
		echo get_media_item($id);
	}
	exit;
}

check_admin_referer('media-form');

$id = media_handle_upload('async-upload', $_REQUEST['post_id']);
if ( is_wp_error($id) ) {
	echo '<div class="error-div">
	<a class="dismiss" href="#" onclick="jQuery(this).parents(\'div.media-item\').slideUp(200, function(){jQuery(this).remove();});">' . __('Dismiss') . '</a>
	<strong>' . sprintf(__('&#8220;%s&#8221; has failed to upload due to an error'), esc_html($_FILES['async-upload']['name']) ) . '</strong><br />' .
	esc_html($id->get_error_message()) . '</div>';
	exit;
}

if ( $_REQUEST['short'] ) {
	// short form response - attachment ID only
	echo $id;
} else {
	// long form response - big chunk o html
	$type = $_REQUEST['type'];
	echo apply_filters("async_upload_{$type}", $id);
}

<?php

/* This accepts file uploads from swfupload or other asynchronous upload methods.

*/

if ( defined('ABSPATH') )
	require_once( ABSPATH . 'wp-config.php');
else
    require_once('../wp-config.php');

// Flash often fails to send cookies with the POST or upload, so we need to pass it in GET or POST instead
if ( empty($_COOKIE[AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
	$_COOKIE[AUTH_COOKIE] = $_REQUEST['auth_cookie'];
unset($current_user);
require_once('admin.php');

header('Content-Type: text/plain');

if ( !current_user_can('upload_files') )
	wp_die(__('You do not have permission to upload files.'));

$id = media_handle_upload('async-upload', $_REQUEST['post_id']);
if (is_wp_error($id)) {
	echo '<div id="media-upload-error">'.wp_specialchars($id->get_error_message()).'</div>';
	exit;
}

$type = $_REQUEST['type'];
echo apply_filters("async_upload_{$type}", $id);

?>

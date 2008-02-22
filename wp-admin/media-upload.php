<?php
require_once('admin.php');
wp_enqueue_script('swfupload');
wp_enqueue_script('swfupload-degrade');
wp_enqueue_script('swfupload-queue');
wp_enqueue_script('swfupload-handlers');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.'));

// IDs should be integers
$ID = isset($ID)? (int) $ID : 0;
$post_id = isset($post_id)? (int) $post_id : 0;

// Require an ID for the edit screen
if ( isset($action) && $action == 'edit' && !$ID )
	wp_die(__("You are not allowed to be here"));

// upload type: image, video, file, ..?
$type = @strval($_GET['type']);

// let the action code decide how to handle the request
do_action("media_upload_{$type}");

?>

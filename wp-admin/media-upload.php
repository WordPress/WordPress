<?php
require_once('admin.php');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.'));

// IDs should be integers
$ID = (int) $ID;
$post_id = (int) $post_id;

// Require an ID for the edit screen
if ( $action == 'edit' && !$ID )
	wp_die(__("You are not allowed to be here"));

// upload type: image, video, file, ..?
$type = @strval($_GET['type']);

// let the action code decide how to handle the request
do_action("media_upload_{$type}");

?>
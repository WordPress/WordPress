<?php

require_once('admin.php');

if ( isset( $_POST['action'] ) ) {
	if ( !isset( $_POST['rich_editing'] ) )
		$_POST['rich_editing'] = 'false';
	update_user_option( $current_user->id, 'rich_editing', $wpdb->escape($_POST['rich_editing']), true );
	do_action('personal_options_update');

	$goback = add_query_arg('updated', 'true', $_SERVER['HTTP_REFERER']);
	$goback = preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', $goback);
	wp_redirect($goback);
}

?>
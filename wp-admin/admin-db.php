<?php

function get_users_drafts( $user_id ) {
	global $wpdb;
	$user_id = (int) $user_id;
	$query = "SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'draft' AND post_author = $user_id ORDER BY ID DESC";
	$query = apply_filters('get_users_drafts', $query);
	return $wpdb->get_results( $query );
}

function get_others_drafts( $user_id ) {
	global $wpdb;
	$user = get_userdata( $user_id );
	$level_key = $wpdb->prefix . 'user_level';
	if ( 1 < $user->user_level ) {
		$editable = $wpdb->get_col("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$level_key' AND meta_value <= '$user->user_level' AND user_id != $user_id");
		if( is_array( $editable ) == false )
				$other_drafts = '';
		else {
			$editable = join(',', $editable);
			$other_drafts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'draft' AND post_author IN ($editable) ");
		}
	} else {
		$other_drafts = false;
	}
	return apply_filters('get_others_drafts', $other_drafts);
}

?>
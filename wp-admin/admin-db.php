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

	$editable = get_editable_user_ids( $user_id );
	
	if( !$editable ) {
		$other_drafts = '';
	} else {
		$editable = join(',', $editable);
		$other_drafts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'draft' AND post_author IN ($editable) AND post_author != '$user_id' ");
	}

	return apply_filters('get_others_drafts', $other_drafts);
}

function get_editable_authors( $user_id ) {
	global $wpdb;

	$editable = get_editable_user_ids( $user_id );

	if( !$editable ) {
		return false;
	} else {
		$editable = join(',', $editable);
		$authors = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE ID IN ($editable)" );
	}

	return apply_filters('get_editable_authors', $authors);
}

function get_editable_user_ids( $user_id, $exclude_zeros = true ) {
	global $wpdb;
	
	$user = new WP_User( $user_id );
	
	if ( ! $user->has_cap('edit_others_posts') ) {
			echo "no cap<br/>";
		if ( $user->has_cap('edit_posts') || $exclude_zeros == false )
			return array($user->id);
		else 
			return false;
	}

	$level_key = $wpdb->prefix . 'user_level';

	$query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$level_key'";
	if ( $exclude_zeros )
		$query .= " AND meta_value != '0'";
		
	return $wpdb->get_col( $query );
}

function get_author_user_ids() {
	global $wpdb;
	$level_key = $wpdb->prefix . 'user_level';

	$query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$level_key' AND meta_value != '0'";

	return $wpdb->get_col( $query );
}

function get_nonauthor_user_ids() {
	global $wpdb;
	$level_key = $wpdb->prefix . 'user_level';

	$query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$level_key' AND meta_value = '0'";

	return $wpdb->get_col( $query );
}

?>
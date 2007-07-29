<?php

function comment_exists($comment_author, $comment_date) {
	global $wpdb;

	return $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments
			WHERE comment_author = '$comment_author' AND comment_date = '$comment_date'");
}

function edit_comment() {
	global $user_ID;

	$comment_ID = (int) $_POST['comment_ID'];
	$comment_post_ID = (int) $_POST['comment_post_ID'];

	if (!current_user_can( 'edit_post', $comment_post_ID ))
		wp_die( __('You are not allowed to edit comments on this post, so you cannot edit this comment.' ));

	$_POST['comment_author'] = $_POST['newcomment_author'];
	$_POST['comment_author_email'] = $_POST['newcomment_author_email'];
	$_POST['comment_author_url'] = $_POST['newcomment_author_url'];
	$_POST['comment_approved'] = $_POST['comment_status'];
	$_POST['comment_content'] = $_POST['content'];
	$_POST['comment_ID'] = (int) $_POST['comment_ID'];

	if (!empty ( $_POST['edit_date'] ) ) {
		$aa = $_POST['aa'];
		$mm = $_POST['mm'];
		$jj = $_POST['jj'];
		$hh = $_POST['hh'];
		$mn = $_POST['mn'];
		$ss = $_POST['ss'];
		$jj = ($jj > 31 ) ? 31 : $jj;
		$hh = ($hh > 23 ) ? $hh -24 : $hh;
		$mn = ($mn > 59 ) ? $mn -60 : $mn;
		$ss = ($ss > 59 ) ? $ss -60 : $ss;
		$_POST['comment_date'] = "$aa-$mm-$jj $hh:$mn:$ss";
	}

	wp_update_comment( $_POST);
}

function get_comment_to_edit( $id ) {
	if ( !$comment = get_comment($id) )
		return false;

	$comment->comment_ID = (int) $comment->comment_ID;
	$comment->comment_post_ID = (int) $comment->comment_post_ID;

	$comment->comment_content = format_to_edit( $comment->comment_content );
	$comment->comment_content = apply_filters( 'comment_edit_pre', $comment->comment_content);

	$comment->comment_author = format_to_edit( $comment->comment_author );
	$comment->comment_author_email = format_to_edit( $comment->comment_author_email );
	$comment->comment_author_url = clean_url($comment->comment_author_url);
	$comment->comment_author_url = format_to_edit( $comment->comment_author_url );

	return $comment;
}

function get_pending_comments_num( $post_id ) {
	global $wpdb;
	$post_id = (int) $post_id;
	$pending = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = $post_id AND comment_approved = '0'" );
	return $pending;
}

?>
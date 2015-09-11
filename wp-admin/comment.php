<?php
/**
 * Comment Management Screen
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$parent_file = 'edit-comments.php';
$submenu_file = 'edit-comments.php';

/**
 * @global string $action
 */
global $action;
wp_reset_vars( array('action') );

if ( isset( $_POST['deletecomment'] ) )
	$action = 'deletecomment';

if ( 'cdc' == $action )
	$action = 'delete';
elseif ( 'mac' == $action )
	$action = 'approve';

if ( isset( $_GET['dt'] ) ) {
	if ( 'spam' == $_GET['dt'] )
		$action = 'spam';
	elseif ( 'trash' == $_GET['dt'] )
		$action = 'trash';
}

switch( $action ) {

case 'editcomment' :
	$title = __('Edit Comment');

	get_current_screen()->add_help_tab( array(
		'id'      => 'overview',
		'title'   => __('Overview'),
		'content' =>
			'<p>' . __( 'You can edit the information left in a comment if needed. This is often useful when you notice that a commenter has made a typographical error.' ) . '</p>' .
			'<p>' . __( 'You can also moderate the comment from this screen using the Status box, where you can also change the timestamp of the comment.' ) . '</p>'
	) );

	get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.wordpress.org/Administration_Screens#Comments" target="_blank">Documentation on Comments</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>' ) . '</p>'
	);

	wp_enqueue_script('comment');
	require_once( ABSPATH . 'wp-admin/admin-header.php' );

	$comment_id = absint( $_GET['c'] );

	if ( !$comment = get_comment( $comment_id ) )
		comment_footer_die( __( 'Invalid comment ID.' ) . sprintf(' <a href="%s">' . __('Go back') . '</a>.', 'javascript:history.go(-1)') );

	if ( !current_user_can( 'edit_comment', $comment_id ) )
		comment_footer_die( __('You are not allowed to edit this comment.') );

	if ( 'trash' == $comment->comment_approved )
		comment_footer_die( __('This comment is in the Trash. Please move it out of the Trash if you want to edit it.') );

	$comment = get_comment_to_edit( $comment_id );

	include( ABSPATH . 'wp-admin/edit-form-comment.php' );

	break;

case 'delete'  :
case 'approve' :
case 'trash'   :
case 'spam'    :

	$title = __('Moderate Comment');

	$comment_id = absint( $_GET['c'] );

	if ( !$comment = get_comment_to_edit( $comment_id ) ) {
		wp_redirect( admin_url('edit-comments.php?error=1') );
		die();
	}

	if ( !current_user_can( 'edit_comment', $comment->comment_ID ) ) {
		wp_redirect( admin_url('edit-comments.php?error=2') );
		die();
	}

	// No need to re-approve/re-trash/re-spam a comment.
	if ( $action == str_replace( '1', 'approve', $comment->comment_approved ) ) {
		wp_redirect( admin_url( 'edit-comments.php?same=' . $comment_id ) );
		die();
 	}

	require_once( ABSPATH . 'wp-admin/admin-header.php' );

	$formaction    = $action . 'comment';
	$nonce_action  = 'approve' == $action ? 'approve-comment_' : 'delete-comment_';
	$nonce_action .= $comment_id;

?>
<div class="wrap">

<h1><?php echo esc_html( $title ); ?></h1>

<?php
switch ( $action ) {
	case 'spam' :
		$caution_msg = __('You are about to mark the following comment as spam:');
		$button      = _x( 'Mark as Spam', 'comment' );
		break;
	case 'trash' :
		$caution_msg = __('You are about to move the following comment to the Trash:');
		$button      = __('Move to Trash');
		break;
	case 'delete' :
		$caution_msg = __('You are about to delete the following comment:');
		$button      = __('Permanently Delete Comment');
		break;
	default :
		$caution_msg = __('You are about to approve the following comment:');
		$button      = __('Approve Comment');
		break;
}

if ( $comment->comment_approved != '0' ) { // if not unapproved
	$message = '';
	switch ( $comment->comment_approved ) {
		case '1' :
			$message = __('This comment is currently approved.');
			break;
		case 'spam' :
			$message  = __('This comment is currently marked as spam.');
			break;
		case 'trash' :
			$message  = __('This comment is currently in the Trash.');
			break;
	}
	if ( $message ) {
		echo '<div class="notice notice-info"><p>' . $message . '</p></div>';
	}
}
?>
<p><strong><?php _e('Caution:'); ?></strong> <?php echo $caution_msg; ?></p>

<table class="form-table comment-ays">
<tr>
<th scope="row"><?php _e('Author'); ?></th>
<td><?php echo $comment->comment_author; ?></td>
</tr>
<?php if ( $comment->comment_author_email ) { ?>
<tr>
<th scope="row"><?php _e('Email'); ?></th>
<td><?php echo $comment->comment_author_email; ?></td>
</tr>
<?php } ?>
<?php if ( $comment->comment_author_url ) { ?>
<tr>
<th scope="row"><?php _e('URL'); ?></th>
<td><a href="<?php echo $comment->comment_author_url; ?>"><?php echo $comment->comment_author_url; ?></a></td>
</tr>
<?php } ?>
<tr>
	<th scope="row"><?php /* translators: column name or table row header */ _e( 'In Response To' ); ?></th>
	<td>
	<?php
		$post_id = $comment->comment_post_ID;
		if ( current_user_can( 'edit_post', $post_id ) ) {
			$post_link = "<a href='" . esc_url( get_edit_post_link( $post_id ) ) . "'>";
			$post_link .= esc_html( get_the_title( $post_id ) ) . '</a>';
		} else {
			$post_link = esc_html( get_the_title( $post_id ) );
		}
		echo $post_link;

		if ( $comment->comment_parent ) {
			$parent      = get_comment( $comment->comment_parent );
			$parent_link = esc_url( get_comment_link( $parent ) );
			$name        = get_comment_author( $parent );
			printf( ' | ' . __( 'In reply to <a href="%1$s">%2$s</a>.' ), $parent_link, $name );
		}
	?>
	</td>
</tr>
<tr>
	<th scope="row"><?php _e( 'Submitted on' ); ?></th>
	<td>
	<?php
		/* translators: 2: comment date, 3: comment time */
		printf( __( '<a href="%1$s">%2$s at %3$s</a>' ),
			esc_url( get_comment_link( $comment->comment_ID ) ),
			/* translators: comment date format. See http://php.net/date */
			get_comment_date( __( 'Y/m/d' ) ),
			get_comment_date( get_option( 'time_format' ) )
		);
	?>
	</td>
</tr>
<tr>
<th scope="row"><?php /* translators: field name in comment form */ _ex('Comment', 'noun'); ?></th>
<td><?php echo $comment->comment_content; ?></td>
</tr>
</table>

<form action="comment.php" method="get" class="comment-ays-submit">

<p>
	<?php submit_button( $button, 'primary', 'submit', false ); ?>
	<a href="<?php echo admin_url('edit-comments.php'); ?>" class="button-cancel"><?php esc_attr_e( 'Cancel' ); ?></a></td>
</p>

<?php wp_nonce_field( $nonce_action ); ?>
<input type="hidden" name="action" value="<?php echo esc_attr($formaction); ?>" />
<input type="hidden" name="c" value="<?php echo esc_attr($comment->comment_ID); ?>" />
<input type="hidden" name="noredir" value="1" />
</form>

</div>
<?php
	break;

case 'deletecomment'    :
case 'trashcomment'     :
case 'untrashcomment'   :
case 'spamcomment'      :
case 'unspamcomment'    :
case 'approvecomment'   :
case 'unapprovecomment' :
	$comment_id = absint( $_REQUEST['c'] );

	if ( in_array( $action, array( 'approvecomment', 'unapprovecomment' ) ) )
		check_admin_referer( 'approve-comment_' . $comment_id );
	else
		check_admin_referer( 'delete-comment_' . $comment_id );

	$noredir = isset($_REQUEST['noredir']);

	if ( !$comment = get_comment($comment_id) )
		comment_footer_die( __( 'Invalid comment ID.' ) . sprintf(' <a href="%s">' . __('Go back') . '</a>.', 'edit-comments.php') );
	if ( !current_user_can( 'edit_comment', $comment->comment_ID ) )
		comment_footer_die( __('You are not allowed to edit comments on this post.') );

	if ( '' != wp_get_referer() && ! $noredir && false === strpos(wp_get_referer(), 'comment.php') )
		$redir = wp_get_referer();
	elseif ( '' != wp_get_original_referer() && ! $noredir )
		$redir = wp_get_original_referer();
	elseif ( in_array( $action, array( 'approvecomment', 'unapprovecomment' ) ) )
		$redir = admin_url('edit-comments.php?p=' . absint( $comment->comment_post_ID ) );
	else
		$redir = admin_url('edit-comments.php');

	$redir = remove_query_arg( array('spammed', 'unspammed', 'trashed', 'untrashed', 'deleted', 'ids', 'approved', 'unapproved'), $redir );

	switch ( $action ) {
		case 'deletecomment' :
			wp_delete_comment( $comment_id );
			$redir = add_query_arg( array('deleted' => '1'), $redir );
			break;
		case 'trashcomment' :
			wp_trash_comment($comment_id);
			$redir = add_query_arg( array('trashed' => '1', 'ids' => $comment_id), $redir );
			break;
		case 'untrashcomment' :
			wp_untrash_comment($comment_id);
			$redir = add_query_arg( array('untrashed' => '1'), $redir );
			break;
		case 'spamcomment' :
			wp_spam_comment($comment_id);
			$redir = add_query_arg( array('spammed' => '1', 'ids' => $comment_id), $redir );
			break;
		case 'unspamcomment' :
			wp_unspam_comment($comment_id);
			$redir = add_query_arg( array('unspammed' => '1'), $redir );
			break;
		case 'approvecomment' :
			wp_set_comment_status( $comment_id, 'approve' );
			$redir = add_query_arg( array( 'approved' => 1 ), $redir );
			break;
		case 'unapprovecomment' :
			wp_set_comment_status( $comment_id, 'hold' );
			$redir = add_query_arg( array( 'unapproved' => 1 ), $redir );
			break;
	}

	wp_redirect( $redir );
	die;

case 'editedcomment' :

	$comment_id = absint( $_POST['comment_ID'] );
	$comment_post_id = absint( $_POST['comment_post_ID'] );

	check_admin_referer( 'update-comment_' . $comment_id );

	edit_comment();

	$location = ( empty( $_POST['referredby'] ) ? "edit-comments.php?p=$comment_post_id" : $_POST['referredby'] ) . '#comment-' . $comment_id;

	/**
	 * Filter the URI the user is redirected to after editing a comment in the admin.
	 *
	 * @since 2.1.0
	 *
	 * @param string $location The URI the user will be redirected to.
	 * @param int $comment_id The ID of the comment being edited.
	 */
	$location = apply_filters( 'comment_edit_redirect', $location, $comment_id );
	wp_redirect( $location );

	exit();

default:
	wp_die( __('Unknown action.') );

} // end switch

include( ABSPATH . 'wp-admin/admin-footer.php' );

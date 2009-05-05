<?php
/**
 * Comment Management Panel
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once('admin.php');

$parent_file = 'edit-comments.php';
$submenu_file = 'edit-comments.php';

wp_reset_vars( array('action') );

if ( isset( $_POST['deletecomment'] ) )
	$action = 'deletecomment';

/**
 * Display error message at bottom of comments.
 *
 * @param string $msg Error Message. Assumed to contain HTML and be sanitized.
 */
function comment_footer_die( $msg ) {  //
	echo "<div class='wrap'><p>$msg</p></div>";
	include('admin-footer.php');
	die;
}

switch( $action ) {

case 'editcomment' :
	$title = __('Edit Comment');

	wp_enqueue_script('comment');
	require_once('admin-header.php');

	$comment_id = absint( $_GET['c'] );

	if ( !$comment = get_comment( $comment_id ) )
		comment_footer_die( __('Oops, no comment with this ID.') . sprintf(' <a href="%s">'.__('Go back').'</a>!', 'javascript:history.go(-1)') );

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		comment_footer_die( __('You are not allowed to edit comments on this post.') );

	$comment = get_comment_to_edit( $comment_id );

	include('edit-form-comment.php');

	break;

case 'cdc' :
case 'mac' :

	require_once('admin-header.php');

	$comment_id = absint( $_GET['c'] );
	$formaction = 'cdc' == $action ? 'deletecomment' : 'approvecomment';
	$nonce_action = 'cdc' == $action ? 'delete-comment_' : 'approve-comment_';
	$nonce_action .= $comment_id;

	if ( !$comment = get_comment_to_edit( $comment_id ) )
		comment_footer_die( __('Oops, no comment with this ID.') . sprintf(' <a href="%s">'.__('Go back').'</a>!', 'edit.php') );

	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		comment_footer_die( 'cdc' == $action ? __('You are not allowed to delete comments on this post.') : __('You are not allowed to edit comments on this post, so you cannot approve this comment.') );
?>
<div class='wrap'>

<div class="narrow">
<?php
if ( 'spam' == $_GET['dt'] ) {
	$caution_msg = __('You are about to mark the following comment as spam:');
	$button = __('Spam Comment');
} elseif ( 'cdc' == $action ) {
	$caution_msg = __('You are about to delete the following comment:');
	$button = __('Delete Comment');
} else {
	$caution_msg = __('You are about to approve the following comment:');
	$button = __('Approve Comment');
}
?>

<p><strong><?php _e('Caution:'); ?></strong> <?php echo $caution_msg; ?></p>

<p><?php _e('Are you sure you want to do that?'); ?></p>

<form action='comment.php' method='get'>

<table width="100%">
<tr>
<td><input type='button' class="button" value='<?php esc_attr_e('No'); ?>' onclick="self.location='<?php echo admin_url('edit-comments.php'); ?>" /></td>
<td class="textright"><input type='submit' class="button" value='<?php echo esc_attr($button); ?>' /></td>
</tr>
</table>

<?php wp_nonce_field( $nonce_action ); ?>
<input type='hidden' name='action' value='<?php echo esc_attr($formaction); ?>' />
<?php if ( 'spam' == $_GET['dt'] ) { ?>
<input type='hidden' name='dt' value='spam' />
<?php } ?>
<input type='hidden' name='p' value='<?php echo esc_attr($comment->comment_post_ID); ?>' />
<input type='hidden' name='c' value='<?php echo esc_attr($comment->comment_ID); ?>' />
<input type='hidden' name='noredir' value='1' />
</form>

<table class="form-table" cellpadding="5">
<tr class="alt">
<th scope="row"><?php _e('Author'); ?></th>
<td><?php echo $comment->comment_author; ?></td>
</tr>
<?php if ( $comment->comment_author_email ) { ?>
<tr>
<th scope="row"><?php _e('E-mail'); ?></th>
<td><?php echo $comment->comment_author_email; ?></td>
</tr>
<?php } ?>
<?php if ( $comment->comment_author_url ) { ?>
<tr>
<th scope="row"><?php _e('URL'); ?></th>
<td><a href='<?php echo $comment->comment_author_url; ?>'><?php echo $comment->comment_author_url; ?></a></td>
</tr>
<?php } ?>
<tr>
<th scope="row" valign="top"><?php /* translators: field name in comment form */ echo _x('Comment', 'noun'); ?></th>
<td><?php echo $comment->comment_content; ?></td>
</tr>
</table>

</div>
</div>
<?php
	break;

case 'deletecomment' :
	$comment_id = absint( $_REQUEST['c'] );
	check_admin_referer( 'delete-comment_' . $comment_id );

	if ( isset( $_REQUEST['noredir'] ) )
		$noredir = true;
	else
		$noredir = false;

	if ( !$comment = get_comment( $comment_id ) )
		comment_footer_die( __('Oops, no comment with this ID.') . sprintf(' <a href="%s">'.__('Go back').'</a>!', 'edit-comments.php') );

	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		comment_footer_die( __('You are not allowed to edit comments on this post.') );

	if ( 'spam' == $_REQUEST['dt'] )
		wp_set_comment_status( $comment->comment_ID, 'spam' );
	else
		wp_delete_comment( $comment->comment_ID );

	if ( '' != wp_get_referer() && false == $noredir && false === strpos(wp_get_referer(), 'comment.php' ) )
		wp_redirect( wp_get_referer() );
	else if ( '' != wp_get_original_referer() && false == $noredir )
		wp_redirect( wp_get_original_referer() );
	else
		wp_redirect( admin_url('edit-comments.php') );

	die;
	break;

case 'unapprovecomment' :
	$comment_id = absint( $_GET['c'] );
	check_admin_referer( 'unapprove-comment_' . $comment_id );

	if ( isset( $_GET['noredir'] ) )
		$noredir = true;
	else
		$noredir = false;

	if ( !$comment = get_comment( $comment_id ) )
		comment_footer_die( __('Oops, no comment with this ID.') . sprintf(' <a href="%s">'.__('Go back').'</a>!', 'edit.php') );

	if ( !current_user_can( 'edit_post', $comment->comment_post_ID ) )
		comment_footer_die( __('You are not allowed to edit comments on this post, so you cannot disapprove this comment.') );

	wp_set_comment_status( $comment->comment_ID, 'hold' );

	if ( '' != wp_get_referer() && false == $noredir )
		wp_redirect( wp_get_referer() );
	else
		wp_redirect( admin_url('edit-comments.php?p=' . absint( $comment->comment_post_ID ) . '#comments') );

	exit();
	break;

case 'approvecomment' :
	$comment_id = absint( $_GET['c'] );
	check_admin_referer( 'approve-comment_' . $comment_id );

	if ( isset( $_GET['noredir'] ) )
		$noredir = true;
	else
		$noredir = false;

	if ( !$comment = get_comment( $comment_id ) )
		comment_footer_die( __('Oops, no comment with this ID.') . sprintf(' <a href="%s">'.__('Go back').'</a>!', 'edit.php') );

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		comment_footer_die( __('You are not allowed to edit comments on this post, so you cannot approve this comment.') );

	wp_set_comment_status( $comment->comment_ID, 'approve' );

	if ( '' != wp_get_referer() && false == $noredir )
		wp_redirect( wp_get_referer() );
	else
		wp_redirect( admin_url('edit-comments.php?p=' . absint( $comment->comment_post_ID ) . '#comments') );

	exit();
	break;

case 'editedcomment' :

	$comment_id = absint( $_POST['comment_ID'] );
	$comment_post_id = absint( $_POST['comment_post_ID'] );

	check_admin_referer( 'update-comment_' . $comment_id );

	edit_comment();

	$location = ( empty( $_POST['referredby'] ) ? "edit-comments.php?p=$comment_post_id" : $_POST['referredby'] ) . '#comment-' . $comment_id;
	$location = apply_filters( 'comment_edit_redirect', $location, $comment_id );
	wp_redirect( $location );

	exit();
	break;

default:
	wp_die( __('Unknown action.') );
	break;

} // end switch

include('admin-footer.php');

?>
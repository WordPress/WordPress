<?php
/**
 * Edit Comments Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

require_once( './includes/default-list-tables.php' );

$wp_list_table = new WP_Comments_Table;
$wp_list_table->check_permissions();

if ( isset( $_REQUEST['doaction'] ) ||  isset( $_REQUEST['doaction2'] ) || isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) {
	check_admin_referer( 'bulk-comments' );

	if ( ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) && !empty( $_REQUEST['pagegen_timestamp'] ) ) {
		$comment_status = $wpdb->escape( $_REQUEST['comment_status'] );
		$delete_time = $wpdb->escape( $_REQUEST['pagegen_timestamp'] );
		$comment_ids = $wpdb->get_col( "SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = '$comment_status' AND '$delete_time' > comment_date_gmt" );
		$doaction = 'delete';
	} elseif ( ( $_REQUEST['action'] != -1 || $_REQUEST['action2'] != -1 ) && isset( $_REQUEST['delete_comments'] ) ) {
		$comment_ids = $_REQUEST['delete_comments'];
		$doaction = ( $_REQUEST['action'] != -1 ) ? $_REQUEST['action'] : $_REQUEST['action2'];
	} elseif ( $_REQUEST['doaction'] == 'undo' && isset( $_REQUEST['ids'] ) ) {
		$comment_ids = array_map( 'absint', explode( ',', $_REQUEST['ids'] ) );
		$doaction = $_REQUEST['action'];
	} else {
		wp_redirect( wp_get_referer() );
	}

	$approved = $unapproved = $spammed = $unspammed = $trashed = $untrashed = $deleted = 0;
	$redirect_to = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'spammed', 'unspammed', 'approved', 'unapproved', 'ids' ), wp_get_referer() );

	foreach ( $comment_ids as $comment_id ) { // Check the permissions on each
		$_post_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = %d", $comment_id ) );

		if ( !current_user_can( 'edit_post', $_post_id ) )
			continue;

		switch ( $doaction ) {
			case 'approve' :
				wp_set_comment_status( $comment_id, 'approve' );
				$approved++;
				break;
			case 'unapprove' :
				wp_set_comment_status( $comment_id, 'hold' );
				$unapproved++;
				break;
			case 'spam' :
				wp_spam_comment( $comment_id );
				$spammed++;
				break;
			case 'unspam' :
				wp_unspam_comment( $comment_id );
				$unspammed++;
				break;
			case 'trash' :
				wp_trash_comment( $comment_id );
				$trashed++;
				break;
			case 'untrash' :
				wp_untrash_comment( $comment_id );
				$untrashed++;
				break;
			case 'delete' :
				wp_delete_comment( $comment_id );
				$deleted++;
				break;
		}
	}

	if ( $approved )
		$redirect_to = add_query_arg( 'approved', $approved, $redirect_to );
	if ( $unapproved )
		$redirect_to = add_query_arg( 'unapproved', $unapproved, $redirect_to );
	if ( $spammed )
		$redirect_to = add_query_arg( 'spammed', $spammed, $redirect_to );
	if ( $unspammed )
		$redirect_to = add_query_arg( 'unspammed', $unspammed, $redirect_to );
	if ( $trashed )
		$redirect_to = add_query_arg( 'trashed', $trashed, $redirect_to );
	if ( $untrashed )
		$redirect_to = add_query_arg( 'untrashed', $untrashed, $redirect_to );
	if ( $deleted )
		$redirect_to = add_query_arg( 'deleted', $deleted, $redirect_to );
	if ( $trashed || $spammed )
		$redirect_to = add_query_arg( 'ids', join( ',', $comment_ids ), $redirect_to );

	wp_redirect( $redirect_to );
	exit;
} elseif ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
	 wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
	 exit;
}

$wp_list_table->prepare_items();

wp_enqueue_script('admin-comments');
enqueue_comment_hotkeys_js();

if ( $post_id )
	$title = sprintf(__('Comments on &#8220;%s&#8221;'), wp_html_excerpt(_draft_or_post_title($post_id), 50));
else
	$title = __('Comments');

add_contextual_help( $current_screen, '<p>' . __('You can manage comments made on your site similar to the way you manage Posts and other content. This screen is customizable in the same ways as other management screens, and you can act on comments using the on-hover action links or the Bulk Actions.') . '</p>' .
	'<p>' . __('A yellow row means the comment is waiting for you to moderate it.') . '</p>' .
	'<p>' . __('In the Author column, in addition to the author&#8217;s name, email address, and blog URL, the commenter&#8217;s IP address is shown. Clicking on this link will show you all the comments made from this IP address.') . '</p>' .
	'<p>' . __('In the Comment column, above each comment it says &#8220;Submitted on,&#8221; followed by the date and time the comment was left on your site. Clicking on the date/time link will take you to that comment on your live site.') . '</p>' .
	'<p>' . __('In the In Response To column, there are three elements. The text is the name of the post that inspired the comment, and links to the post editor for that entry. The &#8220;#&#8221; permalink symbol below leads to that post on your live site. The small bubble with the number in it shows how many comments that post has received. If the bubble is gray, you have moderated all comments for that post. If it is blue, there are pending comments. Clicking the bubble will filter the comments screen to show only comments on that post.') . '</p>' .
	'<p>' . __('Many people take advantage of keyboard shortcuts to moderate their comments more quickly. Use the link below to learn more.') . '</p>' .
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="http://codex.wordpress.org/Administration_Panels#Comments" target="_blank">Comments Documentation</a>' ) . '</p>' .
	'<p>' . __( '<a href="http://codex.wordpress.org/Comment_Spam" target="_blank">Comment Spam Documentation</a>') . '</p>' .
	'<p>' . __( '<a href="http://codex.wordpress.org/Keyboard_Shortcuts" target="_blank">Keyboard Shortcuts Documentation</a>') . '</p>' .
	'<p>' . __( '<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);
require_once('./admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title );
if ( isset($_REQUEST['s']) && $_REQUEST['s'] )
	printf( '<span class="subtitle">' . sprintf( __( 'Search results for &#8220;%s&#8221;' ), wp_html_excerpt( esc_html( stripslashes( $_REQUEST['s'] ) ), 50 ) ) . '</span>' ); ?>
</h2>

<?php
if ( isset( $_REQUEST['error'] ) ) {
	$error = (int) $_REQUEST['error'];
	$error_msg = '';
	switch ( $error ) {
		case 1 :
			$error_msg = __( 'Oops, no comment with this ID.' );
			break;
		case 2 :
			$error_msg = __( 'You are not allowed to edit comments on this post.' );
			break;
	}
	if ( $error_msg )
		echo '<div id="moderated" class="error"><p>' . $error_msg . '</p></div>';
}

if ( isset($_REQUEST['approved']) || isset($_REQUEST['deleted']) || isset($_REQUEST['trashed']) || isset($_REQUEST['untrashed']) || isset($_REQUEST['spammed']) || isset($_REQUEST['unspammed']) || isset($_REQUEST['same']) ) {
	$approved  = isset( $_REQUEST['approved']  ) ? (int) $_REQUEST['approved']  : 0;
	$deleted   = isset( $_REQUEST['deleted']   ) ? (int) $_REQUEST['deleted']   : 0;
	$trashed   = isset( $_REQUEST['trashed']   ) ? (int) $_REQUEST['trashed']   : 0;
	$untrashed = isset( $_REQUEST['untrashed'] ) ? (int) $_REQUEST['untrashed'] : 0;
	$spammed   = isset( $_REQUEST['spammed']   ) ? (int) $_REQUEST['spammed']   : 0;
	$unspammed = isset( $_REQUEST['unspammed'] ) ? (int) $_REQUEST['unspammed'] : 0;
	$same      = isset( $_REQUEST['same'] )      ? (int) $_REQUEST['same']      : 0;

	if ( $approved > 0 || $deleted > 0 || $trashed > 0 || $untrashed > 0 || $spammed > 0 || $unspammed > 0 || $same > 0 ) {
		if ( $approved > 0 )
			$messages[] = sprintf( _n( '%s comment approved', '%s comments approved', $approved ), $approved );

		if ( $spammed > 0 ) {
			$ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : 0;
			$messages[] = sprintf( _n( '%s comment marked as spam.', '%s comments marked as spam.', $spammed ), $spammed ) . ' <a href="' . esc_url( wp_nonce_url( "edit-comments.php?doaction=undo&action=unspam&ids=$ids", "bulk-comments" ) ) . '">' . __('Undo') . '</a><br />';
		}

		if ( $unspammed > 0 )
			$messages[] = sprintf( _n( '%s comment restored from the spam', '%s comments restored from the spam', $unspammed ), $unspammed );

		if ( $trashed > 0 ) {
			$ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : 0;
			$messages[] = sprintf( _n( '%s comment moved to the Trash.', '%s comments moved to the Trash.', $trashed ), $trashed ) . ' <a href="' . esc_url( wp_nonce_url( "edit-comments.php?doaction=undo&action=untrash&ids=$ids", "bulk-comments" ) ) . '">' . __('Undo') . '</a><br />';
		}

		if ( $untrashed > 0 )
			$messages[] = sprintf( _n( '%s comment restored from the Trash', '%s comments restored from the Trash', $untrashed ), $untrashed );

		if ( $deleted > 0 )
			$messages[] = sprintf( _n( '%s comment permanently deleted', '%s comments permanently deleted', $deleted ), $deleted );

		if ( $same > 0 && $comment = get_comment( $same ) ) {
			switch ( $comment->comment_approved ) {
				case '1' :
					$messages[] = __('This comment is already approved.') . ' <a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$same" ) ) . '">' . __( 'Edit comment' ) . '</a>';
					break;
				case 'trash' :
					$messages[] = __( 'This comment is already in the Trash.' ) . ' <a href="' . esc_url( admin_url( 'edit-comments.php?comment_status=trash' ) ) . '"> ' . __( 'View Trash' ) . '</a>';
					break;
				case 'spam' :
					$messages[] = __( 'This comment is already marked as spam.' ) . ' <a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$same" ) ) . '">' . __( 'Edit comment' ) . '</a>';
					break;
			}
		}

		echo '<div id="moderated" class="updated"><p>' . implode( "<br/>\n", $messages ) . '</p></div>';
	}
}
?>

<form id="comments-form" action="" method="post">
<ul class="subsubsub">
<?php
$status_links = array();
$num_comments = ( $post_id ) ? wp_count_comments( $post_id ) : wp_count_comments();
//, number_format_i18n($num_comments->moderated) ), "<span class='comment-count'>" . number_format_i18n($num_comments->moderated) . "</span>"),
//, number_format_i18n($num_comments->spam) ), "<span class='spam-comment-count'>" . number_format_i18n($num_comments->spam) . "</span>")
$stati = array(
		'all' => _nx_noop('All', 'All', 'comments'), // singular not used
		'moderated' => _n_noop('Pending <span class="count">(<span class="pending-count">%s</span>)</span>', 'Pending <span class="count">(<span class="pending-count">%s</span>)</span>'),
		'approved' => _n_noop('Approved', 'Approved'), // singular not used
		'spam' => _n_noop('Spam <span class="count">(<span class="spam-count">%s</span>)</span>', 'Spam <span class="count">(<span class="spam-count">%s</span>)</span>'),
		'trash' => _n_noop('Trash <span class="count">(<span class="trash-count">%s</span>)</span>', 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>')
	);

if ( !EMPTY_TRASH_DAYS )
	unset($stati['trash']);

$link = 'edit-comments.php';
if ( !empty($comment_type) && 'all' != $comment_type )
	$link = add_query_arg( 'comment_type', $comment_type, $link );

foreach ( $stati as $status => $label ) {
	$class = ( $status == $comment_status ) ? ' class="current"' : '';

	if ( !isset( $num_comments->$status ) )
		$num_comments->$status = 10;
	$link = add_query_arg( 'comment_status', $status, $link );
	if ( $post_id )
		$link = add_query_arg( 'p', absint( $post_id ), $link );
	/*
	// I toyed with this, but decided against it. Leaving it in here in case anyone thinks it is a good idea. ~ Mark
	if ( !empty( $_REQUEST['s'] ) )
		$link = add_query_arg( 's', esc_attr( stripslashes( $_REQUEST['s'] ) ), $link );
	*/
	$status_links[] = "<li class='$status'><a href='$link'$class>" . sprintf(
		_n( $label[0], $label[1], $num_comments->$status ),
		number_format_i18n( $num_comments->$status )
	) . '</a>';
}

$status_links = apply_filters( 'comment_status_links', $status_links );

echo implode( " |</li>\n", $status_links) . '</li>';
unset($status_links);
?>
</ul>

<p class="search-box">
	<label class="screen-reader-text" for="comment-search-input"><?php _e( 'Search Comments' ); ?>:</label>
	<input type="text" id="comment-search-input" name="s" value="<?php _admin_search_query(); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Comments' ); ?>" class="button" />
</p>

<input type="hidden" name="mode" value="<?php echo esc_attr($mode); ?>" />
<?php if ( $post_id ) : ?>
<input type="hidden" name="p" value="<?php echo esc_attr( intval( $post_id ) ); ?>" />
<?php endif; ?>
<input type="hidden" name="comment_status" value="<?php echo esc_attr($comment_status); ?>" />
<input type="hidden" name="pagegen_timestamp" value="<?php echo esc_attr(current_time('mysql', 1)); ?>" />

<input type="hidden" name="_total" value="<?php echo esc_attr( $wp_list_table->get_pagination_arg('total_items') ); ?>" />
<input type="hidden" name="_per_page" value="<?php echo esc_attr( $wp_list_table->get_pagination_arg('per_page') ); ?>" />
<input type="hidden" name="_page" value="<?php echo esc_attr( $wp_list_table->get_pagination_arg('page') ); ?>" />

<?php if ( isset($_REQUEST['paged']) ) { ?>
	<input type="hidden" name="paged" value="<?php echo esc_attr( absint( $_REQUEST['paged'] ) ); ?>" />
<?php } ?>

<?php if ( $wp_list_table->has_items() ) { ?>

<?php $wp_list_table->display_table(); ?>

<br class="clear" />
</div>

</form>

<div id="ajax-response"></div>

<?php } elseif ( 'moderated' == $comment_status ) { ?>
<p><?php _e('No comments awaiting moderation&hellip; yet.') ?></p>
</div>
</form>

<?php } else { ?>
<p><?php _e('No comments found.') ?></p>
</div>
</form>

<?php } ?>
</div>

<?php
wp_comment_reply('-1', true, 'detail');
wp_comment_trashnotice();
include('./admin-footer.php'); ?>

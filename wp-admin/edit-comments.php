<?php
/**
 * Edit Comments Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Edit Comments');
wp_enqueue_script( 'admin-comments' );
wp_enqueue_script( 'admin-forms' );
wp_enqueue_script( 'jquery-table-hotkeys' );

if ( isset( $_POST['delete_all_spam'] ) || isset( $_POST['delete_all_spam2'] ) ) {
	check_admin_referer('bulk-spam-delete');

	$deleted_spam = $wpdb->query( "DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'" );
	wp_redirect('edit-comments.php?deleted=' . (int) $deleted_spam);
}

if ( isset($_REQUEST['delete_comments']) && isset($_REQUEST['action']) && ( -1 != $_REQUEST['action'] || -1 != $_REQUEST['action2'] ) ) {
	check_admin_referer('bulk-comments');
	$doaction = ( -1 != $_REQUEST['action'] ) ? $_REQUEST['action'] : $_REQUEST['action2'];

	$deleted = $approved = $unapproved = $spammed = 0;
	foreach ( (array) $_REQUEST['delete_comments'] as $comment_id) : // Check the permissions on each
		$comment_id = (int) $comment_id;
		$post_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = %d", $comment_id) );

		if ( !current_user_can('edit_post', $post_id) )
			continue;

		switch( $doaction ) {
			case 'markspam' :
				wp_set_comment_status($comment_id, 'spam');
				$spammed++;
				break;
			case 'delete' :
				wp_set_comment_status($comment_id, 'delete');
				$deleted++;
				break;
			case 'approve' :
				wp_set_comment_status($comment_id, 'approve');
				$approved++;
				break;
			case 'unapprove' :
				wp_set_comment_status($comment_id, 'hold');
				$unapproved++;
				break;
		}
	endforeach;

	$redirect_to = 'edit-comments.php?deleted=' . $deleted . '&approved=' . $approved . '&spam=' . $spammed . '&unapproved=' . $unapproved;
	if ( isset($_REQUEST['apage']) )
		$redirect_to = add_query_arg( 'apage', absint($_REQUEST['apage']), $redirect_to );
	if ( !empty($_REQUEST['mode']) )
		$redirect_to = add_query_arg('mode', $_REQUEST['mode'], $redirect_to);
	if ( !empty($_REQUEST['comment_status']) )
		$redirect_to = add_query_arg('comment_status', $_REQUEST['comment_status'], $redirect_to);
	if ( !empty($_REQUEST['s']) )
		$redirect_to = add_query_arg('s', $_REQUEST['s'], $redirect_to);
	wp_redirect( $redirect_to );
} elseif ( isset($_GET['_wp_http_referer']) && ! empty($_GET['_wp_http_referer']) ) {
	 wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 exit;
}

require_once('admin-header.php');

$mode = ( ! isset($_GET['mode']) || empty($_GET['mode']) ) ? 'detail' : attribute_escape($_GET['mode']);

$comment_status = !empty($_GET['comment_status']) ? attribute_escape($_GET['comment_status']) : '';

$comment_type = !empty($_GET['comment_type']) ? attribute_escape($_GET['comment_type']) : '';

$post_id = isset($_GET['p']) ? (int) $_GET['p'] : 0;

$search_dirty = ( isset($_GET['s']) ) ? $_GET['s'] : '';
$search = attribute_escape( $search_dirty ); ?>

<div id="edit-settings-wrap" class="hidden">
<h5><?php _e('Show on screen') ?></h5>
<form id="adv-settings" action="" method="get">
<div class="metabox-prefs">
<?php manage_columns_prefs('comment') ?>
<?php wp_nonce_field( 'hiddencolumns', 'hiddencolumnsnonce', false ); ?>
<br class="clear" />
</div></form>
</div>

<?php
if ( isset( $_GET['approved'] ) || isset( $_GET['deleted'] ) || isset( $_GET['spam'] ) ) {
	$approved = isset( $_GET['approved'] ) ? (int) $_GET['approved'] : 0;
	$deleted = isset( $_GET['deleted'] ) ? (int) $_GET['deleted'] : 0;
	$spam = isset( $_GET['spam'] ) ? (int) $_GET['spam'] : 0;

	if ( $approved > 0 || $deleted > 0 || $spam > 0 ) {
		echo '<div id="moderated" class="updated fade"><p>';

		if ( $approved > 0 ) {
			printf( __ngettext( '%s comment approved', '%s comments approved', $approved ), $approved );
			echo '<br />';
		}

		if ( $deleted > 0 ) {
			printf( __ngettext( '%s comment deleted', '%s comments deleted', $deleted ), $deleted );
			echo '<br />';
		}

		if ( $spam > 0 ) {
			printf( __ngettext( '%s comment marked as spam', '%s comments marked as spam', $spam ), $spam );
			echo '<br />';
		}

		echo '</p></div>';
	}
}
?>

<div class="wrap">

<ul class="subsubsub">
<?php
$status_links = array();
$num_comments = wp_count_comments();
$stati = array(
		'moderated' => sprintf(__ngettext('Awaiting Moderation (%s)', 'Awaiting Moderation (%s)', number_format_i18n($num_comments->moderated) ), "<span class='comment-count'>" . number_format_i18n($num_comments->moderated) . "</span>"),
		'approved' => _c('Approved|plural'),
		'spam' => sprintf(__ngettext('Spam (%s)', 'Spam (%s)', number_format_i18n($num_comments->spam) ), "<span class='spam-comment-count'>" . number_format_i18n($num_comments->spam) . "</span>")
	);
$class = ( '' === $comment_status ) ? ' class="current"' : '';
$status_links[] = "<li><a href=\"edit-comments.php\"$class>".__('Show All Comments')."</a>";
$type = ( !$comment_type && 'all' != $comment_type ) ? '' : "&amp;comment_type=$comment_type";
foreach ( $stati as $status => $label ) {
	$class = '';

	if ( $status == $comment_status )
		$class = ' class="current"';

	$status_links[] = "<li class='$status'><a href=\"edit-comments.php?comment_status=$status$type\"$class>$label</a>";
}

$status_links = apply_filters( 'comment_status_links', $status_links );

echo implode(' | </li>', $status_links) . '</li>';
unset($status_links);
?>
</ul>

<div class="filter">
<form id="list-filter" action="" method="get">
<?php if ( $comment_status ) echo "<input type='hidden' name='comment_status' value='$comment_status' />\n"; ?>
<select name="comment_type">
	<option value="all"><?php _e('Show all comment types'); ?></option>
<?php
	$comment_types = array(
		'comment' => __('Comments'),
		'pingback' => __('Pingbacks'),
		'trackback' => __('Trackbacks'),
	);

	foreach ( $comment_types as $type => $label ) {
		echo "	<option value='$type'";
		selected( $comment_type, $type );
		echo ">$label</option>\n";
	}
?>
</select>
<input type="submit" id="post-query-submit" value="<?php _e('Filter'); ?>" class="button-secondary" />
</form>
</div>

<form class="search-form" action="<?php echo $pagenow ?>" method="get">
<p class="search-box">
	<label class="hidden" for="post-search-input"><?php _e( 'Search Comments' ); ?>:</label>
	<input type="text" class="search-input" id="post-search-input" name="s" value="<?php _admin_search_query(); ?>" />
	<input type="submit" value="<?php _e( 'Search Comments' ); ?>" class="button" />
</p>
</form>

<?php
$comments_per_page = apply_filters('comments_per_page', 20, $comment_status);

if ( isset( $_GET['apage'] ) )
	$page = abs( (int) $_GET['apage'] );
else
	$page = 1;

$start = $offset = ( $page - 1 ) * $comments_per_page;

list($_comments, $total) = _wp_get_comment_list( $comment_status, $search_dirty, $start, $comments_per_page + 5, $post_id, $comment_type ); // Grab a few extra

$comments = array_slice($_comments, 0, $comments_per_page);
$extra_comments = array_slice($_comments, $comments_per_page);

$page_links = paginate_links( array(
	'base' => add_query_arg( 'apage', '%#%' ),
	'format' => '',
	'total' => ceil($total / $comments_per_page),
	'current' => $page
));

?>

<form id="comments-form" action="" method="post">
<input type="hidden" name="mode" value="<?php echo $mode; ?>" />
<input type="hidden" name="comment_status" value="<?php echo $comment_status; ?>" />

<div class="tablenav">

<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<select name="action">
<option value="-1" selected="selected"><?php _e('Actions') ?></option>
<?php if ( empty($comment_status) || 'approved' == $comment_status ): ?>
<option value="unapprove"><?php _e('Unapprove'); ?></option>
<?php endif; ?>
<?php if ( empty($comment_status) || 'moderated' == $comment_status ): ?>
<option value="approve"><?php _e('Approve'); ?></option>
<?php endif; ?>
<?php if ( 'spam' != $comment_status ): ?>
<option value="markspam"><?php _e('Mark as Spam'); ?></option>
<?php endif; ?>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" name="doaction" id="doaction" value="<?php _e('Apply'); ?>" class="button-secondary apply" />
<?php wp_nonce_field('bulk-comments'); ?>
<?php if ( isset($_GET['apage']) ) { ?>
	<input type="hidden" name="apage" value="<?php echo absint( $_GET['apage'] ); ?>" />
<?php }

if ( 'spam' == $comment_status ) {
	wp_nonce_field('bulk-spam-delete'); ?>
<input type="submit" name="delete_all_spam" value="<?php _e('Delete All Spam'); ?>" class="button-secondary apply" />
<?php } ?>
<?php do_action('manage_comments_nav', $comment_status); ?>
</div>

<br class="clear" />

</div>

<div class="clear"></div>

<?php if ( $comments ) { ?>
<table class="widefat">
<thead>
	<tr>
<?php print_column_headers('comment'); ?>
	</tr>
</thead>

<tfoot>
	<tr>
<?php print_column_headers('comment', false); ?>
	</tr>
</tfoot>

<tbody id="the-comment-list" class="list:comment">
<?php
	foreach ($comments as $comment)
		_wp_comment_row( $comment->comment_ID, $mode, $comment_status );
?>
</tbody>
<tbody id="the-extra-comment-list" class="list:comment" style="display: none;">
<?php
	foreach ($extra_comments as $comment)
		_wp_comment_row( $comment->comment_ID, $mode, $comment_status );
?>
</tbody>
</table>

<div class="tablenav">
<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<select name="action2">
<option value="-1" selected="selected"><?php _e('Actions') ?></option>
<?php if ( empty($comment_status) || 'approved' == $comment_status ): ?>
<option value="unapprove"><?php _e('Unapprove'); ?></option>
<?php endif; ?>
<?php if ( empty($comment_status) || 'moderated' == $comment_status ): ?>
<option value="approve"><?php _e('Approve'); ?></option>
<?php endif; ?>
<?php if ( 'spam' != $comment_status ): ?>
<option value="markspam"><?php _e('Mark as Spam'); ?></option>
<?php endif; ?>
<option value="delete"><?php _e('Delete'); ?></option>
</select>
<input type="submit" name="doaction2" id="doaction2" value="<?php _e('Apply'); ?>" class="button-secondary apply" />

<?php if ( 'spam' == $comment_status ) { ?>
<input type="submit" name="delete_all_spam2" value="<?php _e('Delete All Spam'); ?>" class="button-secondary apply" />
<?php } ?>
<?php do_action('manage_comments_nav', $comment_status); ?>
</div>

<br class="clear" />
</div>

</form>

<form id="get-extra-comments" method="post" action="" class="add:the-extra-comment-list:" style="display: none;">
	<input type="hidden" name="s" value="<?php echo $search; ?>" />
	<input type="hidden" name="mode" value="<?php echo $mode; ?>" />
	<input type="hidden" name="comment_status" value="<?php echo $comment_status; ?>" />
	<input type="hidden" name="page" value="<?php echo isset($_REQUEST['page']) ? absint( $_REQUEST['page'] ) : 1; ?>" />
	<?php wp_nonce_field( 'add-comment', '_ajax_nonce', false ); ?>
</form>

<div id="ajax-response"></div>
<?php
} elseif ( 'moderated' == $_GET['comment_status'] ) {
?>
<p>
<?php _e('No comments awaiting moderation&hellip; yet.') ?>
</p>
<?php
} else {
?>
<p>
<?php _e('No results found.') ?>
</p>
<?php
}
?>

</div>

<?php
wp_comment_reply('-1', true, 'detail');
include('admin-footer.php'); ?>

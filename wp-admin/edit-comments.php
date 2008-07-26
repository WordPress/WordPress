<?php
require_once('admin.php');

$title = __('Edit Comments');
$parent_file = 'edit-comments.php';
wp_enqueue_script( 'admin-comments' );
wp_enqueue_script('admin-forms');

if ( !empty( $_REQUEST['delete_comments'] ) ) {
	check_admin_referer('bulk-comments');

	$comments_deleted = $comments_approved = $comments_unapproved = $comments_spammed = 0;
	foreach ($_REQUEST['delete_comments'] as $comment) : // Check the permissions on each
		$comment = (int) $comment;
		$post_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = %d", $comment) );
		if ( !current_user_can('edit_post', $post_id) )
			continue;
		if ( !empty( $_REQUEST['spamit'] ) ) {
			wp_set_comment_status($comment, 'spam');
			$comments_spammed++;
		} elseif ( !empty( $_REQUEST['deleteit'] ) ) {
			wp_set_comment_status($comment, 'delete');
			$comments_deleted++;
		} elseif ( !empty( $_REQUEST['approveit'] ) ) {
			wp_set_comment_status($comment, 'approve');
			$comments_approved++;
		} elseif ( !empty( $_REQUEST['unapproveit'] ) ) {
			wp_set_comment_status($comment, 'hold');
			$comments_unapproved++;
		}
	endforeach;
	$redirect_to = basename( __FILE__ ) . '?deleted=' . $comments_deleted . '&approved=' . $comments_approved . '&spam=' . $comments_spammed . '&unapproved=' . $comments_unapproved;
	if ( isset($_REQUEST['apage']) )
		$redirect_to = add_query_arg( 'apage', absint($_REQUEST['apage']), $redirect_to );
	if ( !empty($_REQUEST['mode']) )
		$redirect_to = add_query_arg('mode', $_REQUEST['mode'], $redirect_to);
	if ( !empty($_REQUEST['comment_status']) )
		$redirect_to = add_query_arg('comment_status', $_REQUEST['comment_status'], $redirect_to);
	if ( !empty($_REQUEST['s']) )
		$redirect_to = add_query_arg('s', $_REQUEST['s'], $redirect_to);
	wp_redirect( $redirect_to );
} elseif ( !empty($_GET['_wp_http_referer']) ) {
	 wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
	 exit;
}

require_once('admin-header.php');

if ( empty($_GET['mode']) )
	$mode = 'detail';
else
	$mode = attribute_escape($_GET['mode']);

if ( isset($_GET['comment_status']) )
	$comment_status = attribute_escape($_GET['comment_status']);
else
	$comment_status = '';

if ( isset($_GET['s']) )
	$search_dirty = $_GET['s'];
else
	$search_dirty = '';
$search = attribute_escape( $search_dirty );
?>
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
<form id="posts-filter" action="" method="get">
<h2><?php _e('Manage Comments'); ?></h2>

<ul class="subsubsub">
<?php
$status_links = array();
$num_comments = wp_count_comments();
$stati = array('moderated' => sprintf(__ngettext('Awaiting Moderation (%s)', 'Awaiting Moderation (%s)', number_format_i18n($num_comments->moderated) ), "<span class='comment-count'>" . number_format_i18n($num_comments->moderated) . "</span>"), 'approved' => _c('Approved|plural'));
$class = ( '' === $comment_status ) ? ' class="current"' : '';
$status_links[] = "<li><a href=\"edit-comments.php\"$class>".__('Show All Comments')."</a>";
foreach ( $stati as $status => $label ) {
	$class = '';

	if ( $status == $comment_status )
		$class = ' class="current"';

	$status_links[] = "<li><a href=\"edit-comments.php?comment_status=$status\"$class>" . $label . '</a>';
}

$status_links = apply_filters( 'comment_status_links', $status_links );

echo implode(' | </li>', $status_links) . '</li>';
unset($status_links);
?>
</ul>

<p id="post-search">
	<label class="hidden" for="post-search-input"><?php _e( 'Search Comments' ); ?>:</label>
	<input type="text" id="post-search-input" name="s" value="<?php echo $search; ?>" />
	<input type="submit" value="<?php _e( 'Search Comments' ); ?>" class="button" />
</p>

<input type="hidden" name="mode" value="<?php echo $mode; ?>" />
<input type="hidden" name="comment_status" value="<?php echo $comment_status; ?>" />
</form>

<ul class="view-switch">
	<li <?php if ( 'detail' == $mode ) echo "class='current'" ?>><a href="<?php echo clean_url(add_query_arg('mode', 'detail', $_SERVER['REQUEST_URI'])) ?>"><?php _e('Detail View') ?></a></li>
	<li <?php if ( 'list' == $mode ) echo "class='current'" ?>><a href="<?php echo clean_url(add_query_arg('mode', 'list', $_SERVER['REQUEST_URI'])) ?>"><?php _e('List View') ?></a></li>
</ul>

<?php

$comments_per_page = apply_filters('comments_per_page', 20, $comment_status);

if ( isset( $_GET['apage'] ) )
	$page = abs( (int) $_GET['apage'] );
else
	$page = 1;

$start = $offset = ( $page - 1 ) * $comments_per_page;

list($_comments, $total) = _wp_get_comment_list( $comment_status, $search_dirty, $start, $comments_per_page + 5 ); // Grab a few extra

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

<div class="tablenav">

<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div class="alignleft">
<?php if ( 'approved' != $comment_status ): ?>
<input type="submit" value="<?php _e('Approve'); ?>" name="approveit" class="button-secondary" />
<?php endif; ?>
<input type="submit" value="<?php _e('Mark as Spam'); ?>" name="spamit" class="button-secondary" />
<?php if ( 'moderated' != $comment_status ): ?>
<input type="submit" value="<?php _e('Unapprove'); ?>" name="unapproveit" class="button-secondary" />
<?php endif; ?>
<input type="submit" value="<?php _e('Delete'); ?>" name="deleteit" class="button-secondary delete" />
<?php do_action('manage_comments_nav', $comment_status); ?>
<?php wp_nonce_field('bulk-comments'); ?>
<?php if ( isset($_GET['apage']) ) { ?>
	<input type="hidden" name="apage" value="<?php echo absint( $_GET['apage'] ); ?>" />
<?php } ?>
</div>

<br class="clear" />

</div>

<br class="clear" />
<?php
if ($comments) {
?>
<table class="widefat">
<thead>
  <tr>
    <th scope="col" class="check-column"><input type="checkbox" /></th>
    <th scope="col"><?php _e('Comment') ?></th>
    <th scope="col"><?php _e('Date') ?></th>
    <th scope="col" class="action-links"><?php _e('Actions') ?></th>
  </tr>
</thead>
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
} else  {
?>
<p>
<?php _e('No results found.') ?>
</p>
<?php
}
?>
<div class="tablenav">
<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>
<br class="clear" />
</div>

</div>

<?php include('admin-footer.php'); ?>

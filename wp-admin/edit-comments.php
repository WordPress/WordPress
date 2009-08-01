<?php
/**
 * Edit Comments Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( !current_user_can('edit_posts') )
	wp_die(__('Cheatin&#8217; uh?'));

wp_enqueue_script('admin-comments');
enqueue_comment_hotkeys_js();

$post_id = isset($_REQUEST['p']) ? (int) $_REQUEST['p'] : 0;

if ( isset($_REQUEST['doaction']) ||  isset($_REQUEST['doaction2']) || isset($_REQUEST['delete_all']) || isset($_REQUEST['delete_all2']) ) {
	check_admin_referer('bulk-comments');
	
	if ((isset($_REQUEST['delete_all']) || isset($_REQUEST['delete_all2'])) && !empty($_REQUEST['pagegen_timestamp'])) {
		$comment_status = $wpdb->escape($_REQUEST['comment_status']);
		$delete_time = $wpdb->escape($_REQUEST['pagegen_timestamp']);
		$comment_ids = $wpdb->get_col( "SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = '$comment_status' AND '$delete_time' > comment_date_gmt" );
		$doaction = 'delete';
	} elseif (($_REQUEST['action'] != -1 || $_REQUEST['action2'] != -1) && isset($_REQUEST['delete_comments'])) {
		$comment_ids = $_REQUEST['delete_comments'];
		$doaction = ($_REQUEST['action'] != -1) ? $_REQUEST['action'] : $_REQUEST['action2'];
	} else wp_redirect($_SERVER['HTTP_REFERER']);
	
	$approved = $unapproved = $spammed = $trashed = $untrashed = $deleted = 0;
	
	foreach ($comment_ids as $comment_id) { // Check the permissions on each
		$_post_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = %d", $comment_id) );

		if ( !current_user_can('edit_post', $_post_id) )
			continue;

		switch( $doaction ) {
			case 'approve' :
				wp_set_comment_status($comment_id, 'approve');
				$approved++;
				break;
			case 'unapprove' :
				wp_set_comment_status($comment_id, 'hold');
				$unapproved++;
				break;
			case 'markspam' :
				wp_set_comment_status($comment_id, 'spam');
				$spammed++;
				break;
			case 'trash' :
				wp_trash_comment($comment_id);
				$trashed++;
				break;
			case 'untrash' :
				wp_untrash_comment($comment_id);
				$untrashed++;
				break;
			case 'delete' :
				wp_delete_comment($comment_id);
				$deleted++;
				break;
		}
	}

	$redirect_to = 'edit-comments.php?approved=' . $approved . '&unapproved=' . $unapproved . '&spam=' . $spammed . '&trashed=' . $trashed . '&untrashed=' . $untrashed . '&deleted=' . $deleted;
	if ( $post_id )
		$redirect_to = add_query_arg( 'p', absint( $post_id ), $redirect_to );
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

if ( $post_id )
	$title = sprintf(__('Edit Comments on &#8220;%s&#8221;'), wp_html_excerpt(_draft_or_post_title($post_id), 50));
else
	$title = __('Edit Comments');

require_once('admin-header.php');

$mode = ( ! isset($_GET['mode']) || empty($_GET['mode']) ) ? 'detail' : esc_attr($_GET['mode']);

$comment_status = isset($_REQUEST['comment_status']) ? $_REQUEST['comment_status'] : 'all';
if ( !in_array($comment_status, array('all', 'moderated', 'approved', 'spam', 'trash')) )
	$comment_status = 'all';

$comment_type = !empty($_GET['comment_type']) ? esc_attr($_GET['comment_type']) : '';

$search_dirty = ( isset($_GET['s']) ) ? $_GET['s'] : '';
$search = esc_attr( $search_dirty ); ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title );
if ( isset($_GET['s']) && $_GET['s'] )
	printf( '<span class="subtitle">' . sprintf( __( 'Search results for &#8220;%s&#8221;' ), wp_html_excerpt( esc_html( stripslashes( $_GET['s'] ) ), 50 ) ) . '</span>' ); ?>
</h2>

<?php
if ( isset($_GET['approved']) || isset($_GET['deleted']) || isset($_GET['trashed']) || isset($_GET['untrashed']) || isset($_GET['spam']) ) {
	$approved = isset($_GET['approved']) ? (int) $_GET['approved'] : 0;
	$deleted = isset($_GET['deleted']) ? (int) $_GET['deleted'] : 0;
	$trashed = isset($_GET['trashed']) ? (int) $_GET['trashed'] : 0;
	$untrashed = isset($_GET['untrashed']) ? (int) $_GET['untrashed'] : 0;
	$spam = isset($_GET['spam']) ? (int) $_GET['spam'] : 0;

	if ( $approved > 0 || $deleted > 0 || $trashed > 0 || $untrashed > 0 || $spam > 0 ) {
		echo '<div id="moderated" class="updated fade"><p>';

		if ( $approved > 0 ) {
			printf( _n( '%s comment approved', '%s comments approved', $approved ), $approved );
			echo '<br />';
		}
		if ( $spam > 0 ) {
			printf( _n( '%s comment marked as spam', '%s comments marked as spam', $spam ), $spam );
			echo '<br />';
		}
		if ( $trashed > 0 ) {
			printf( _n( '%s comment moved to the trash', '%s comments moved to the trash', $trashed ), $trashed );
			echo '<br />';
		}
		if ( $untrashed > 0 ) {
			printf( _n( '%s comment removed from the trash', '%s comments removed from the trash', $untrashed ), $untrashed );
			echo '<br />';
		}
		if ( $deleted > 0 ) {
			printf( _n( '%s comment permanently deleted', '%s comments permanently deleted', $deleted ), $deleted );
			echo '<br />';
		}

		echo '</p></div>';
	}
}
?>

<form id="comments-form" action="" method="get">
<ul class="subsubsub">
<?php
$status_links = array();
$num_comments = ( $post_id ) ? wp_count_comments( $post_id ) : wp_count_comments();
//, number_format_i18n($num_comments->moderated) ), "<span class='comment-count'>" . number_format_i18n($num_comments->moderated) . "</span>"),
//, number_format_i18n($num_comments->spam) ), "<span class='spam-comment-count'>" . number_format_i18n($num_comments->spam) . "</span>")
$stati = array(
		'all' => _n_noop('All', 'All'), // singular not used
		'moderated' => _n_noop('Pending <span class="count">(<span class="pending-count">%s</span>)</span>', 'Pending <span class="count">(<span class="pending-count">%s</span>)</span>'),
		'approved' => _n_noop('Approved', 'Approved'), // singular not used
		'spam' => _n_noop('Spam <span class="count">(<span class="spam-count">%s</span>)</span>', 'Spam <span class="count">(<span class="spam-count">%s</span>)</span>'),
		'trash' => _n_noop('Trash <span class="count">(<span class="trash-count">%s</span>)</span>', 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>')
	);
$link = 'edit-comments.php';
if ( !empty($comment_type) && 'all' != $comment_type )
	$link = add_query_arg( 'comment_type', $comment_type, $link );
foreach ( $stati as $status => $label ) {
	$class = '';

	if ( $status == $comment_status )
		$class = ' class="current"';
	if ( !isset( $num_comments->$status ) )
		$num_comments->$status = 10;
	$link = add_query_arg( 'comment_status', $status, $link );
	if ( $post_id )
		$link = add_query_arg( 'p', absint( $post_id ), $link );
	/*
	// I toyed with this, but decided against it. Leaving it in here in case anyone thinks it is a good idea. ~ Mark
	if ( !empty( $_GET['s'] ) )
		$link = add_query_arg( 's', esc_attr( stripslashes( $_GET['s'] ) ), $link );
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

<?php
$comments_per_page = get_user_option('edit_comments_per_page');
if ( empty($comments_per_page) )
	$comments_per_page = 20;
$comments_per_page = apply_filters('comments_per_page', $comments_per_page, $comment_status);

if ( isset( $_GET['apage'] ) )
	$page = abs( (int) $_GET['apage'] );
else
	$page = 1;

$start = $offset = ( $page - 1 ) * $comments_per_page;

list($_comments, $total) = _wp_get_comment_list( $comment_status, $search_dirty, $start, $comments_per_page + 8, $post_id, $comment_type ); // Grab a few extra

$_comment_post_ids = array();
foreach ( $_comments as $_c ) {
	$_comment_post_ids[] = $_c->comment_post_ID;
}
$_comment_pending_count_temp = (array) get_pending_comments_num($_comment_post_ids);
foreach ( (array) $_comment_post_ids as $_cpid )
	$_comment_pending_count[$_cpid] = isset( $_comment_pending_count_temp[$_cpid] ) ? $_comment_pending_count_temp[$_cpid] : 0;
if ( empty($_comment_pending_count) )
	$_comment_pending_count = array();

$comments = array_slice($_comments, 0, $comments_per_page);
$extra_comments = array_slice($_comments, $comments_per_page);

$page_links = paginate_links( array(
	'base' => add_query_arg( 'apage', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => ceil($total / $comments_per_page),
	'current' => $page
));

?>

<input type="hidden" name="mode" value="<?php echo esc_attr($mode); ?>" />
<?php if ( $post_id ) : ?>
<input type="hidden" name="p" value="<?php echo esc_attr( intval( $post_id ) ); ?>" />
<?php endif; ?>
<input type="hidden" name="comment_status" value="<?php echo esc_attr($comment_status); ?>" />
<input type="hidden" name="pagegen_timestamp" value="<?php echo esc_attr(current_time('mysql', 1)); ?>" />

<div class="tablenav">

<?php if ( $page_links ) : ?>
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
	number_format_i18n( $start + 1 ),
	number_format_i18n( min( $page * $comments_per_page, $total ) ),
	'<span class="total-type-count">' . number_format_i18n( $total ) . '</span>',
	$page_links
); echo $page_links_text; ?></div>
<input type="hidden" name="_total" value="<?php echo esc_attr($total); ?>" />
<input type="hidden" name="_per_page" value="<?php echo esc_attr($comments_per_page); ?>" />
<input type="hidden" name="_page" value="<?php echo esc_attr($page); ?>" />
<?php endif; ?>

<div class="alignleft actions">
<select name="action">
<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
<?php if ( 'all' == $comment_status || 'approved' == $comment_status ): ?>
<option value="unapprove"><?php _e('Unapprove'); ?></option>
<?php endif; ?>
<?php if ( 'all' == $comment_status || 'moderated' == $comment_status || 'spam' == $comment_status ): ?>
<option value="approve"><?php _e('Approve'); ?></option>
<?php endif; ?>
<?php if ( 'all' == $comment_status || 'approved' == $comment_status || 'moderated' == $comment_status ): ?>
<option value="markspam"><?php _e('Mark as Spam'); ?></option>
<?php endif; ?>
<?php if ( 'trash' == $comment_status ): ?>
<option value="untrash"><?php _e('Restore'); ?></option>
<?php endif; ?>
<?php if ( 'trash' == $comment_status || 'spam' == $comment_status ): ?>
<option value="delete"><?php _e('Delete Permanently'); ?></option>
<?php else: ?>
<option value="trash"><?php _e('Move to Trash'); ?></option>
<?php endif; ?>
</select>
<input type="submit" name="doaction" id="doaction" value="<?php esc_attr_e('Apply'); ?>" class="button-secondary apply" />
<?php wp_nonce_field('bulk-comments'); ?>

<select name="comment_type">
	<option value="all"><?php _e('Show all comment types'); ?></option>
<?php
	$comment_types = apply_filters( 'admin_comment_types_dropdown', array(
		'comment' => __('Comments'),
		'pings' => __('Pings'),
	) );

	foreach ( $comment_types as $type => $label ) {
		echo "	<option value='" . esc_attr($type) . "'";
		selected( $comment_type, $type );
		echo ">$label</option>\n";
	}
?>
</select>
<input type="submit" id="post-query-submit" value="<?php esc_attr_e('Filter'); ?>" class="button-secondary" />

<?php if ( isset($_GET['apage']) ) { ?>
	<input type="hidden" name="apage" value="<?php echo esc_attr( absint( $_GET['apage'] ) ); ?>" />
<?php }

if ( ( 'spam' == $comment_status || 'trash' == $comment_status) && current_user_can ('moderate_comments') ) {
	wp_nonce_field('bulk-destroy', '_destroy_nonce');
    if ( 'spam' == $comment_status ) { ?>
		<input type="submit" name="delete_all" id="delete_all" value="<?php esc_attr_e('Empty Spam'); ?>" class="button-secondary apply" />
<?php } elseif ( 'trash' == $comment_status ) { ?>
		<input type="submit" name="delete_all" id="delete_all" value="<?php esc_attr_e('Empty Trash'); ?>" class="button-secondary apply" />
<?php }
} ?>
<?php do_action('manage_comments_nav', $comment_status); ?>
</div>

<br class="clear" />

</div>

<div class="clear"></div>

<?php if ( $comments ) { ?>
<table class="widefat comments fixed" cellspacing="0">
<thead>
	<tr>
<?php print_column_headers('edit-comments'); ?>
	</tr>
</thead>

<tfoot>
	<tr>
<?php print_column_headers('edit-comments', false); ?>
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
	echo "<div class='tablenav-pages'>$page_links_text</div>";
?>

<div class="alignleft actions">
<select name="action2">
<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
<?php if ( 'all' == $comment_status || 'approved' == $comment_status ): ?>
<option value="unapprove"><?php _e('Unapprove'); ?></option>
<?php endif; ?>
<?php if ( 'all' == $comment_status || 'moderated' == $comment_status || 'spam' == $comment_status ): ?>
<option value="approve"><?php _e('Approve'); ?></option>
<?php endif; ?>
<?php if ( 'all' == $comment_status || 'approved' == $comment_status || 'moderated' == $comment_status ): ?>
<option value="markspam"><?php _e('Mark as Spam'); ?></option>
<?php endif; ?>
<?php if ( 'trash' == $comment_status ): ?>
<option value="untrash"><?php _e('Restore'); ?></option>
<?php endif; ?>
<?php if ( 'trash' == $comment_status || 'spam' == $comment_status ): ?>
<option value="delete"><?php _e('Delete Permanently'); ?></option>
<?php else: ?>
<option value="trash"><?php _e('Move to Trash'); ?></option>
<?php endif; ?>
</select>
<input type="submit" name="doaction2" id="doaction2" value="<?php esc_attr_e('Apply'); ?>" class="button-secondary apply" />

<?php if ( 'spam' == $comment_status ) { ?>
<input type="submit" name="delete_all2" id="delete_all2" value="<?php esc_attr_e('Empty Spam'); ?>" class="button-secondary apply" />
<?php } elseif ( 'trash' == $comment_status ) { ?>
<input type="submit" name="delete_all2" id="delete_all2" value="<?php esc_attr_e('Empty Trash'); ?>" class="button-secondary apply" />
<?php } ?>
<?php do_action('manage_comments_nav', $comment_status); ?>
</div>

<br class="clear" />
</div>

</form>

<form id="get-extra-comments" method="post" action="" class="add:the-extra-comment-list:" style="display: none;">
	<input type="hidden" name="s" value="<?php echo esc_attr($search); ?>" />
	<input type="hidden" name="mode" value="<?php echo esc_attr($mode); ?>" />
	<input type="hidden" name="comment_status" value="<?php echo esc_attr($comment_status); ?>" />
	<input type="hidden" name="page" value="<?php echo esc_attr($page); ?>" />
	<input type="hidden" name="per_page" value="<?php echo esc_attr($comments_per_page); ?>" />
	<input type="hidden" name="p" value="<?php echo esc_attr( $post_id ); ?>" />
	<input type="hidden" name="comment_type" value="<?php echo esc_attr( $comment_type ); ?>" />
	<?php wp_nonce_field( 'add-comment', '_ajax_nonce', false ); ?>
</form>

<div id="ajax-response"></div>

<?php } elseif ( 'moderated' == $comment_status ) { ?>
<p><?php _e('No comments awaiting moderation&hellip; yet.') ?></p>
</form>

<?php } else { ?>
<p><?php _e('No results found.') ?></p>
</form>

<?php } ?>
</div>

<?php
wp_comment_reply('-1', true, 'detail');
include('admin-footer.php'); ?>

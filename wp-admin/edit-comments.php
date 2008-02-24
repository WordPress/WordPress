<?php
require_once('admin.php');

$title = __('Edit Comments');
$parent_file = 'edit-comments.php';
wp_enqueue_script( 'admin-comments' );
wp_enqueue_script('admin-forms');

if ( !empty( $_REQUEST['delete_comments'] ) ) :
	check_admin_referer('bulk-comments');

	$comments_deleted = $comments_approved = $comments_spammed = 0; 
	foreach ($_REQUEST['delete_comments'] as $comment) : // Check the permissions on each
		$comment = (int) $comment;
		$post_id = (int) $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = $comment");
		// $authordata = get_userdata( $wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = $post_id") );
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
		}
	endforeach;
	wp_redirect( basename( __FILE__ ) . '?deleted=' . $comments_deleted . '&approved=' . $comments_approved . '&spam=' . $comments_spammed );
endif;

require_once('admin-header.php');

if ( empty($_GET['mode']) )
	$mode = 'detail';
else
	$mode = attribute_escape($_GET['mode']);
?>

<div class="wrap">
<form id="posts-filter" action="" method="get">
<h2><?php _e('Manage Comments'); ?></h2>

<ul class="subsubsub">
<?php
if ( isset($_GET['comment_status']) )
	$comment_status = $_GET['comment_status'];
else
	$comment_status = '';
$status_links = array();
$num_comments = wp_count_comments();
$stati = array('moderated' => sprintf(__('Awaiting Moderation (%s)'), $num_comments->moderated), 'approved' => __('Approved'));
foreach ( $stati as $status => $label ) {
	$class = '';

	if ( $status == $comment_status )
		$class = ' class="current"';

	$status_links[] = "<li><a href=\"edit-comments.php?comment_status=$status\"$class>" . $label . '</a>';
}
$class = ( '' === $comment_status ) ? ' class="current"' : '';
$status_links[] = "<li><a href=\"edit-comments.php\"$class>".__('All Comments')."</a>";
echo implode(' |</li>', $status_links) . '</li>';
unset($status_links);
?>
</ul>

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

<p id="post-search">
	<input type="text" id="post-search-input" name="s" value="<?php if (isset($_GET['s'])) echo attribute_escape($_GET['s']); ?>" />
	<input type="submit" value="<?php _e( 'Search Comments' ); ?>" class="button" />
</p>

<input type="hidden" name="mode" value="<?php echo $mode; ?>" />

<p><a href="?mode=detail"><?php _e('Detail View') ?></a> | <a href="?mode=list"><?php _e('List View') ?></a></p>

<?php

if ( isset( $_GET['apage'] ) )
	$page = abs( (int) $_GET['apage'] );
else
	$page = 1;

$start = $offset = ( $page - 1 ) * 20;

list($_comments, $total) = _wp_get_comment_list( $comment_status, isset($_GET['s']) ? $_GET['s'] : false, $start, 25 ); // Grab a few extra

$comments = array_slice($_comments, 0, 20);
$extra_comments = array_slice($_comments, 20);

$page_links = paginate_links( array(
	'base' => add_query_arg( 'apage', '%#%' ),
	'format' => '',
	'total' => ceil($total / 20),
	'current' => $page
));

?>

<br style="clear:both;" />

<div class="tablenav">

<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>

<div style="float: left">
<input type="submit" value="<?php _e('Approve'); ?>" name="approveit" class="button-secondary" />
<input type="submit" value="<?php _e('Mark as Spam'); ?>" name="spamit" class="button-secondary" />
<input type="submit" value="<?php _e('Delete'); ?>" name="deleteit" class="button-secondary" />
<?php wp_nonce_field('bulk-comments'); ?>
</div>

<br style="clear:both;" />
</div>

<br style="clear:both;" />
<?php
if ($comments) {
?>
<table class="widefat">
<thead>
  <tr>
    <th scope="col" style="text-align: center"><input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));" /></th>
    <th scope="col"><?php _e('Comment') ?></th>
    <th scope="col"><?php _e('Date') ?></th>
    <th scope="col"><?php _e('Actions') ?></th>
  </tr>
</thead>
<tbody id="the-comment-list" class="list:comment">
<?php
	foreach ($comments as $comment) {
		$post = get_post($comment->comment_post_ID);
		$authordata = get_userdata($post->post_author);
		$comment_status = wp_get_comment_status($comment->comment_ID);
		$class = ('alternate' == $class) ? '' : 'alternate';
		$class .= ('unapproved' == $comment_status) ? ' unapproved' : '';
		$post_link = '<a href="' . get_comment_link() . '">' . get_the_title($comment->comment_post_ID) . '</a>';
		$author_url = get_comment_author_url();
		if ( 'http://' == $author_url )
			$author_url = '';
?>
  <tr id="comment-<?php echo $comment->comment_ID; ?>" class='<?php echo $class; ?>'>
    <td style="text-align: center; vertical-align: text-top"><?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) { ?><input type="checkbox" name="delete_comments[]" value="<?php echo $comment->comment_ID; ?>" /><?php } ?></td>
    <td style="vertical-align: text-top">
    <p><strong class="comment-author"><?php comment_author(); ?></strong><br />
    <?php if ( !empty($author_url) ) : ?> 
    <a href="<?php echo $author_url ?>"><?php echo $author_url; ?></a> |
    <?php endif; ?>
    <?php if ( !empty($comment->comment_author_email) ): ?>
    <?php comment_author_email_link() ?> |
    <?php endif; ?>
    <a href="edit-comments.php?s=<?php comment_author_IP() ?>&amp;mode=detail"><?php comment_author_IP() ?></a>
    <p><?php if ( 'list' == $mode ) comment_excerpt(); else comment_text(); ?></p>
   	<?php printf(__('From %1$s, %2$s at %3$s'), $post_link, get_the_time(get_option('date_format')), get_the_time()) ?>
    </td>
    <td style="vertical-align: text-top"><?php comment_date(); ?></td>
    <td style="vertical-align: text-top">
    <?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
    	echo "<a href='comment.php?action=editcomment&amp;c=$comment->comment_ID' class='edit'>" .  __('Edit') . "</a> | ";
		$url = clean_url( wp_nonce_url( "comment.php?action=deletecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ) );
		echo "<a href='$url' class='delete:the-comment-list:comment-$comment->comment_ID delete'>" . __('Delete') . "</a> ";
	}
	?>
	</td>
  </tr>
		<?php
		} // end foreach
	?></tbody>
</table>

<div id="ajax-response"></div>
<?php
} else {
?>
<p>
<strong><?php _e('No results found.') ?></strong>
</p>
<?php
}
?>
<div class="tablenav">
<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links</div>";
?>
<br style="clear:both;" />
</div>

<?php include('admin-footer.php'); ?>

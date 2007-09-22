<?php
require_once('admin.php');

$title = __('Posts');
$parent_file = 'edit.php';
wp_enqueue_script( 1 == $_GET['c'] ? 'admin-comments' : 'listman' );
require_once('admin-header.php');

$_GET['m']   = (int) $_GET['m'];
$_GET['cat'] = (int) $_GET['cat'];
$post_stati  = array(	//	array( adj, noun )
			'publish' => array(__('Published'), __('Published posts')),
			'future' => array(__('Scheduled'), __('Scheduled posts')),
			'pending' => array(__('Pending Review'), __('Pending posts')),
			'draft' => array(__('Draft'), _c('Drafts|manage posts header')),
			'private' => array(__('Private'), __('Private posts'))
		);

$avail_post_stati = $wpdb->get_col("SELECT DISTINCT post_status FROM $wpdb->posts WHERE post_type = 'post'");

$post_status_q = '';
$post_status_label = __('Posts');
if ( isset($_GET['post_status']) && in_array( $_GET['post_status'], array_keys($post_stati) ) ) {
	$post_status_label = $post_stati[$_GET['post_status']][1];
	$post_status_q = '&post_status=' . $_GET['post_status'];
}
?>

<div class="wrap">

<?php

if ( 'pending' === $_GET['post_status'] ) {
	$order = 'ASC';
	$orderby = 'modified';
} elseif ( 'draft' === $_GET['post_status'] ) {
	$order = 'DESC';
	$orderby = 'modified';
} else {
	$order = 'DESC';
	$orderby = 'date';
}

wp("what_to_show=posts$post_status_q&posts_per_page=15&order=$order&orderby=$orderby");

// define the columns to display, the syntax is 'internal name' => 'display name'
$posts_columns = array();
$posts_columns['id'] = '<div style="text-align: center">' . __('ID') . '</div>';
if ( 'draft' === $_GET['post_status'] )
	$posts_columns['modified'] = __('Modified');
elseif ( 'pending' === $_GET['post_status'] )
	$posts_columns['modified'] = __('Submitted');
else
	$posts_columns['date'] = __('When');
$posts_columns['title'] = __('Title');
$posts_columns['categories'] = __('Categories');
if ( !in_array($_GET['post_status'], array('pending', 'draft', 'future')) )
	$posts_columns['comments'] = '<div style="text-align: center">' . __('Comments') . '</div>';
$posts_columns['author'] = __('Author');

$posts_columns = apply_filters('manage_posts_columns', $posts_columns);

// you can not edit these at the moment
$posts_columns['control_view']   = '';
$posts_columns['control_edit']   = '';
$posts_columns['control_delete'] = '';

?>

<h2><?php
if ( is_single() ) {
	printf(__('Comments on %s'), apply_filters( "the_title", $post->post_title));
} else {
	if ( $post_listing_pageable && !is_archive() && !is_search() )
		$h2_noun = is_paged() ? sprintf(__( 'Previous %s' ), $post_status_label) : sprintf(__('Latest %s'), $post_status_label);
	else
		$h2_noun = $post_status_label;
	// Use $_GET instead of is_ since they can override each other
	$h2_author = '';
	$_GET['author'] = (int) $_GET['author'];
	if ( $_GET['author'] != 0 ) {
		if ( $_GET['author'] == '-' . $user_ID ) { // author exclusion
			$h2_author = ' ' . __('by other authors');
		} else {
			$author_user = get_userdata( get_query_var( 'author' ) );
			$h2_author = ' ' . sprintf(__('by %s'), wp_specialchars( $author_user->display_name ));
		}
	}
	$h2_search = isset($_GET['s'])   && $_GET['s']   ? ' ' . sprintf(__('matching &#8220;%s&#8221;'), wp_specialchars( get_search_query() ) ) : '';
	$h2_cat    = isset($_GET['cat']) && $_GET['cat'] ? ' ' . sprintf( __('in &#8220;%s&#8221;'), single_cat_title('', false) ) : '';
	$h2_month  = isset($_GET['m'])   && $_GET['m']   ? ' ' . sprintf( __('during %s'), single_month_title(' ', false) ) : '';
	printf( _c( '%1$s%2$s%3$s%4$s%5$s|You can reorder these: 1: Posts, 2: by {s}, 3: matching {s}, 4: in {s}, 5: during {s}' ), $h2_noun, $h2_author, $h2_search, $h2_cat, $h2_month );
}
?></h2>

<form name="searchform" id="searchform" action="" method="get">
	<fieldset><legend><?php _e('Search terms&hellip;'); ?></legend>
		<input type="text" name="s" id="s" value="<?php the_search_query(); ?>" size="17" />
	</fieldset>

	<fieldset><legend><?php _e('Status&hellip;'); ?></legend>
		<select name='post_status'>
			<option<?php selected( @$_GET['post_status'], 0 ); ?> value='0'><?php _e('Any'); ?></option>
<?php	foreach ( $post_stati as $status => $label ) : if ( !in_array($status, $avail_post_stati) ) continue; ?>
			<option<?php selected( @$_GET['post_status'], $status ); ?> value='<?php echo $status; ?>'><?php echo $label[0]; ?></option>
<?php	endforeach; ?>
		</select>
	</fieldset>

<?php
$editable_ids = get_editable_user_ids( $user_ID );
if ( $editable_ids && count( $editable_ids ) > 1 ) :
?>
	<fieldset><legend><?php _e('Author&hellip;'); ?></legend>
		<?php wp_dropdown_users( array('include' => $editable_ids, 'show_option_all' => __('Any'), 'name' => 'author', 'selected' => isset($_GET['author']) ? $_GET['author'] : 0) ); ?>
	</fieldset>

<?php
endif;

$arc_query = "SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'post' ORDER BY post_date DESC";

$arc_result = $wpdb->get_results( $arc_query );

$month_count = count($arc_result);

if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) { ?>

	<fieldset><legend><?php _e('Month&hellip;') ?></legend>
		<select name='m'>
			<option<?php selected( @$_GET['m'], 0 ); ?> value='0'><?php _e('Any'); ?></option>
		<?php
		foreach ($arc_result as $arc_row) {
			if ( $arc_row->yyear == 0 )
				continue;
			$arc_row->mmonth = zeroise($arc_row->mmonth, 2);

			if ( $arc_row->yyear . $arc_row->mmonth == $_GET['m'] )
				$default = ' selected="selected"';
			else
				$default = '';

			echo "<option$default value='$arc_row->yyear$arc_row->mmonth'>";
			echo $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear";
			echo "</option>\n";
		}
		?>
		</select>
	</fieldset>

<?php } ?>

	<fieldset><legend><?php _e('Category&hellip;') ?></legend>
		<?php wp_dropdown_categories('show_option_all='.__('All').'&hide_empty=1&hierarchical=1&show_count=1&selected='.$cat);?>
	</fieldset>
	<input type="submit" id="post-query-submit" value="<?php _e('Filter &#187;'); ?>" class="button" />
</form>

<?php do_action('restrict_manage_posts'); ?>

<br style="clear:both;" />

<?php include( 'edit-post-rows.php' ); ?>

<div id="ajax-response"></div>

<div class="navigation">
<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries')) ?></div>
<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;')) ?></div>
</div>

<?php

if ( 1 == count($posts) ) {

	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $id AND comment_approved != 'spam' ORDER BY comment_date");
	if ($comments) {
		update_comment_cache($comments);
	?>
<h3 id="comments"><?php _e('Comments') ?></h3>
<ol id="the-comment-list" class="commentlist">
<?php
$i = 0;
foreach ($comments as $comment) {

		++$i; $class = '';
		$post = get_post($comment->comment_post_ID);
		$authordata = get_userdata($post->post_author);
			$comment_status = wp_get_comment_status($comment->comment_ID);
			if ('unapproved' == $comment_status)
				$class .= ' unapproved';
			if ($i % 2)
				$class .= ' alternate';
			echo "<li id='comment-$comment->comment_ID' class='$class'>";
?>
<p><strong><?php comment_author() ?></strong> <?php if ($comment->comment_author_email) { ?>| <?php comment_author_email_link() ?> <?php } if ($comment->comment_author_url && 'http://' != $comment->comment_author_url) { ?> | <?php comment_author_url_link() ?> <?php } ?>| <?php _e('IP:') ?> <a href="edit-comments.php?s=<?php comment_author_IP() ?>&amp;mode=edit"><?php comment_author_IP() ?></a></p>

<?php comment_text() ?>

<p><?php comment_date(__('M j, g:i A')); ?> &#8212; [
<?php
if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
	echo " <a href='comment.php?action=editcomment&amp;c=".$comment->comment_ID."'>" . __('Edit') . '</a>';
	echo ' | <a href="' . wp_nonce_url('comment.php?action=deletecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) . '" onclick="return deleteSomething( \'comment\', ' . $comment->comment_ID . ', \'' . js_escape(sprintf(__("You are about to delete this comment by '%s'.\n'Cancel' to stop, 'OK' to delete."), $comment->comment_author)) . "', theCommentList );\">" . __('Delete') . '</a> ';
	if ( ('none' != $comment_status) && ( current_user_can('moderate_comments') ) ) {
		echo '<span class="unapprove"> | <a href="' . wp_nonce_url('comment.php?action=unapprovecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'unapprove-comment_' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\', theCommentList );">' . __('Unapprove') . '</a> </span>';
		echo '<span class="approve"> | <a href="' . wp_nonce_url('comment.php?action=approvecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'approve-comment_' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\', theCommentList );">' . __('Approve') . '</a> </span>';
	}
	echo " | <a href=\"" . wp_nonce_url("comment.php?action=deletecomment&amp;dt=spam&amp;p=" . $comment->comment_post_ID . "&amp;c=" . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) . "\" onclick=\"return deleteSomething( 'comment-as-spam', $comment->comment_ID, '" . js_escape(sprintf(__("You are about to mark as spam this comment by '%s'.\n'Cancel' to stop, 'OK' to mark as spam."), $comment->comment_author)) . "', theCommentList );\">" . __('Spam') . "</a> ";
}
?> ]
</p>
		</li>

<?php //end of the loop, don't delete
		} // end foreach
	echo '</ol>';
	}//end if comments
	?>
<?php } ?>
</div>

<?php include('admin-footer.php'); ?>

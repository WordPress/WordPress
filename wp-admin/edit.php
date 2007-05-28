<?php
require_once('admin.php');

$title = __('Posts');
$parent_file = 'edit.php';
wp_enqueue_script( 1 == $_GET['c'] ? 'admin-comments' : 'listman' );
require_once('admin-header.php');

$_GET['m']   = (int) $_GET['m'];
$_GET['cat'] = (int) $_GET['cat'];
$post_stati  = array(	//	array( adj, noun )
			'draft' => array(__('Draft'), _c('Drafts|manage posts header')),
			'future' => array(__('Scheduled'), __('Scheduled posts')),
			'private' => array(__('Private'), __('Private posts')),
			'publish' => array(__('Published'), __('Published posts'))
		);

$post_status_q = '';
$author_q = '';
$post_status_label = _c('Posts|manage posts header');
$post_listing_pageable = true;
if ( isset($_GET['post_status']) && in_array( $_GET['post_status'], array_keys($post_stati) ) ) {
	$post_status_label = $post_stati[$_GET['post_status']][1];
	$post_listing_pageable = false;
	$post_status_q = '&post_status=' . $_GET['post_status'];
	if ( in_array( $_GET['post_status'], array('draft', 'private') ) )
		$author_q = "&author=$user_ID";
	elseif ( 'publish' == $_GET['post_status'] );
		$post_listing_pageable = true;
}
?>

<div class="wrap">

<?php

wp("what_to_show=posts$author_q$post_status_q&posts_per_page=15&posts_per_archive_page=-1");

do_action('restrict_manage_posts');

// define the columns to display, the syntax is 'internal name' => 'display name'
$posts_columns = array(
	'id'         => '<div style="text-align: center">' . __('ID') . '</div>',
	'date'       => __('When'),
	'title'      => __('Title'),
	'categories' => __('Categories'),
	'comments'   => '<div style="text-align: center">' . __('Comments') . '</div>',
	'author'     => __('Author')
);
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
	$h2_search = isset($_GET['s'])   && $_GET['s']   ? ' ' . sprintf(__('matching &#8220;%s&#8221;'), wp_specialchars( get_search_query() ) ) : '';
	$h2_cat    = isset($_GET['cat']) && $_GET['cat'] ? ' ' . sprintf( __('in &#8220;%s&#8221;'), single_cat_title('', false) ) : '';
	$h2_month  = isset($_GET['m'])   && $_GET['m']   ? ' ' . sprintf( __('during %s'), single_month_title(' ', false) ) : '';
	printf( _c( '%1$s%2$s%3$s%4$s|manage posts header' ), $h2_noun, $h2_search, $h2_cat, $h2_month );
}
?></h2>

<form name="searchform" id="searchform" action="" method="get">
	<fieldset><legend><?php _e('Search terms&hellip;'); ?></legend> 
		<input type="text" name="s" id="s" value="<?php the_search_query(); ?>" size="17" /> 
	</fieldset>

	<fieldset><legend><?php _e('Post Type&hellip;'); ?></legend> 
		<select name='post_status'>
			<option<?php selected( @$_GET['post_status'], 0 ); ?> value='0'><?php _e('Any'); ?></option>
<?php	foreach ( $post_stati as $status => $label ) : ?>
			<option<?php selected( @$_GET['post_status'], $status ); ?> value='<?php echo $status; ?>'><?php echo $label[0]; ?></option>
<?php	endforeach; ?>
		</select>
	</fieldset>

<?php


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

<br style="clear:both;" />

<?php
if ( $post_status_q && ( false !== strpos($post_status_q, 'draft') || false !== strpos($post_status_q, 'private') ) ) {
	echo '<h3>' . __('Your Posts') . "</h3>\n";
	include( 'edit-post-rows.php' );

	$editable_ids = get_editable_user_ids( $user_ID );

	if ( $editable_ids && count($editable_ids) > 1 ) {
		$_editable_ids = join(',', array_diff($editable_ids, array($user_ID)));

		$post_status_q = "&post_status=" . $_GET['post_status'];

		unset($GLOBALS['day']); // setup_postdata does this
		wp("what_to_show=posts&author=$_editable_ids$post_status_q&posts_per_page=-1&posts_per_archive_page=-1");

		if ( have_posts() ) {
			echo '<h3>' . __("Others' Posts") . "</h3>\n";
			include( 'edit-post-rows.php' );
		}
	}

} else {
	include( 'edit-post-rows.php' );
}
?>

<div id="ajax-response"></div>

<div class="navigation">
<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries')) ?></div>
<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;')) ?></div>
</div>

<?php

if ( 1 == count($posts) ) {

	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $id AND comment_approved != 'spam' ORDER BY comment_date");
	if ($comments) {
	?>
<h3 id="comments"><?php _e('Comments') ?></h3>
<ol id="the-comment-list" class="commentlist">
<?php
$i = 0;
foreach ($comments as $comment) {

		++$i; $class = '';
		$authordata = get_userdata($wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = $comment->comment_post_ID"));
			$comment_status = wp_get_comment_status($comment->comment_ID);
			if ('unapproved' == $comment_status)
				$class .= ' unapproved';
			if ($i % 2)
				$class .= ' alternate';
			echo "<li id='comment-$comment->comment_ID' class='$class'>";
?>
<p><strong><?php comment_author() ?></strong> <?php if ($comment->comment_author_email) { ?>| <?php comment_author_email_link() ?> <?php } if ($comment->comment_author_url && 'http://' != $comment->comment_author_url) { ?> | <?php comment_author_url_link() ?> <?php } ?>| <?php _e('IP:') ?> <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></p>

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
?>
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

<?php
require_once('admin.php');

$title = __('Posts');
$parent_file = 'edit.php';
wp_enqueue_script( 1 == $_GET['c'] ? 'admin-comments' : 'listman' );
require_once('admin-header.php');

$_GET['m'] = (int) $_GET['m'];

$drafts = get_users_drafts( $user_ID );
$other_drafts = get_others_drafts( $user_ID);

if ($drafts || $other_drafts) {
?>
<div class="wrap">
<?php if ($drafts) { ?>
	<p><strong><?php _e('Your Drafts:') ?></strong>
	<?php
	$i = 0;
	foreach ($drafts as $draft) {
		if (0 != $i)
			echo ', ';
		$draft->post_title = apply_filters('the_title', stripslashes($draft->post_title));
		if ($draft->post_title == '')
			$draft->post_title = sprintf(__('Post #%s'), $draft->ID);
		echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='" . __('Edit this draft') . "'>$draft->post_title</a>";
		++$i;
		}
	?>
.</p>
<?php } ?>

<?php if ($other_drafts) { ?>
	<p><strong><?php _e('Other&#8217;s Drafts:') ?></strong>
	<?php
	$i = 0;
	foreach ($other_drafts as $draft) {
		if (0 != $i)
			echo ', ';
		$draft->post_title = apply_filters('the_title', stripslashes($draft->post_title));
		if ($draft->post_title == '')
			$draft->post_title = sprintf(__('Post #%s'), $draft->ID);
		echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='" . __('Edit this draft') . "'>$draft->post_title</a>";
		++$i;
		}
	?>
	.</p>

<?php } ?>

</div>
<?php } ?>

<div class="wrap">
<h2>
<?php

wp('what_to_show=posts&posts_per_page=15&posts_per_archive_page=-1');

if ( is_month() ) {
	single_month_title(' ');
} elseif ( is_search() ) {
	printf(__('Search for &#8220;%s&#8221;'), wp_specialchars($_GET['s']) );
} else {
	if ( is_single() )
		printf(__('Comments on %s'), $post->post_title);
	elseif ( ! is_paged() || get_query_var('paged') == 1 )
		_e('Last 15 Posts');
	else
		_e('Previous Posts');
}
?>
</h2>

<form name="searchform" id="searchform" action="" method="get">
  <fieldset> 
  <legend><?php _e('Search Posts&hellip;') ?></legend> 
  <input type="text" name="s" value="<?php if (isset($s)) echo attribute_escape($s); ?>" size="17" /> 
  <input type="submit" name="submit" value="<?php _e('Search') ?>" class="button" /> 
  </fieldset>
</form>

<?php $arc_result = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'post' ORDER BY post_date DESC");

if ( count($arc_result) ) { ?>

<form name="viewarc" id="viewarc" action="" method="get">
	<fieldset>
	<legend><?php _e('Browse Month&hellip;') ?></legend>
	<select name='m'>
	<?php
		foreach ($arc_result as $arc_row) {
			if ( $arc_row->yyear == 0 )
				continue;
			$arc_row->mmonth = zeroise($arc_row->mmonth, 2);

			if( isset($_GET['m']) && $arc_row->yyear . $arc_row->mmonth == (int) $_GET['m'] )
				$default = 'selected="selected"';
			else
				$default = null;

			echo "<option $default value='$arc_row->yyear$arc_row->mmonth'>";
			echo $wp_locale->get_month($arc_row->mmonth) . " $arc_row->yyear";
			echo "</option>\n";
		}
	?>
	</select>
		<input type="submit" name="submit" value="<?php _e('Show Month') ?>" class="button" /> 
	</fieldset>
</form>

<?php } ?>

<form name="viewcat" id="viewcat" action="" method="get">
	<fieldset>
	<legend><?php _e('Browse Category&hellip;') ?></legend>
	<?php wp_dropdown_categories('show_option_all='.__('All').'&hide_empty=1&hierarchical=1&show_count=1&selected='.$cat);?>
	<input type="submit" name="submit" value="<?php _e('Show Category') ?>" class="button" /> 
	</fieldset>
</form>

<?php do_action('restrict_manage_posts'); ?>

<br style="clear:both;" />

<?php
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

<table class="widefat">
	<thead>
	<tr>

<?php foreach($posts_columns as $column_display_name) { ?>
	<th scope="col"><?php echo $column_display_name; ?></th>
<?php } ?>

	</tr>
	</thead>
	<tbody id="the-list">
<?php
if ($posts) {
$bgcolor = '';
while (have_posts()) : the_post();
add_filter('the_title','wp_specialchars');
$class = ('alternate' == $class) ? '' : 'alternate';
?>
	<tr id='post-<?php echo $id; ?>' class='<?php echo $class; ?>'>

<?php

foreach($posts_columns as $column_name=>$column_display_name) {

	switch($column_name) {

	case 'id':
		?>
		<th scope="row" style="text-align: center"><?php echo $id ?></th>
		<?php
		break;

	case 'date':
		?>
		<td><?php if ( '0000-00-00 00:00:00' ==$post->post_modified ) _e('Unpublished'); else the_time(__('Y-m-d \<\b\r \/\> g:i:s a')); ?></td>
		<?php
		break;
	case 'title':
		?>
		<td><?php the_title() ?>
		<?php if ('private' == $post->post_status) _e(' - <strong>Private</strong>'); ?></td>
		<?php
		break;

	case 'categories':
		?>
		<td><?php the_category(','); ?></td>
		<?php
		break;

	case 'comments':
		?>
		<td style="text-align: center">
			<?php comments_number(__('0'), "<a href='edit.php?p=$id&amp;c=1'>" . __('1') . '</a>', "<a href='edit.php?p=$id&amp;c=1'>" . __('%') . '</a>') ?>
			</td>
		<?php
		break;

	case 'author':
		?>
		<td><?php the_author() ?></td>
		<?php
		break;

	case 'control_view':
		?>
		<td><a href="<?php the_permalink(); ?>" rel="permalink" class="edit"><?php _e('View'); ?></a></td>
		<?php
		break;

	case 'control_edit':
		?>
		<td><?php if ( current_user_can('edit_post',$post->ID) ) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td>
		<?php
		break;

	case 'control_delete':
		?>
		<td><?php if ( current_user_can('delete_post',$post->ID) ) { echo "<a href='" . wp_nonce_url("post.php?action=delete&amp;post=$id", 'delete-post_' . $post->ID) . "' class='delete' onclick=\"return deleteSomething( 'post', " . $id . ", '" . js_escape(sprintf(__("You are about to delete this post '%s'.\n'OK' to delete, 'Cancel' to stop."), get_the_title())) . "' );\">" . __('Delete') . "</a>"; } ?></td>
		<?php
		break;

	default:
		?>
		<td><?php do_action('manage_posts_custom_column', $column_name, $id); ?></td>
		<?php
		break;
	}
}
?>
	</tr> 
<?php
endwhile;
} else {
?>
  <tr style='background-color: <?php echo $bgcolor; ?>'> 
    <td colspan="8"><?php _e('No posts found.') ?></td> 
  </tr> 
<?php
} // end if ($posts)
?>
	</tbody>
</table>

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

<p><?php comment_date(__('M j, g:i A'));  ?> &#8212; [
<?php
if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
	echo " <a href='comment.php?action=editcomment&amp;c=".$comment->comment_ID."'>" .  __('Edit') . '</a>';
	echo ' | <a href="' . wp_nonce_url('comment.php?action=deletecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) . '" onclick="return deleteSomething( \'comment\', ' . $comment->comment_ID . ', \'' . js_escape(sprintf(__("You are about to delete this comment by '%s'.\n'Cancel' to stop, 'OK' to delete."), $comment->comment_author)) . "', theCommentList );\">" . __('Delete') . '</a> ';
	if ( ('none' != $comment_status) && ( current_user_can('moderate_comments') ) ) {
		echo '<span class="unapprove"> | <a href="' . wp_nonce_url('comment.php?action=unapprovecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'unapprove-comment_' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\', theCommentList );">' . __('Unapprove') . '</a> </span>';
		echo '<span class="approve"> | <a href="' . wp_nonce_url('comment.php?action=approvecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'approve-comment_' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\', theCommentList );">' . __('Approve') . '</a> </span>';
	}
	echo " | <a href=\"" . wp_nonce_url("comment.php?action=deletecomment&amp;dt=spam&amp;p=" . $comment->comment_post_ID . "&amp;c=" . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) . "\" onclick=\"return deleteSomething( 'comment-as-spam', $comment->comment_ID, '" . js_escape(sprintf(__("You are about to mark as spam this comment by '%s'.\n'Cancel' to stop, 'OK' to mark as spam."), $comment->comment_author))  . "', theCommentList );\">" . __('Spam') . "</a> ";
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
<?php
 include('admin-footer.php');
?>

<?php
require_once('admin.php');

$title = __('Edit Comments');
$parent_file = 'edit.php';
wp_enqueue_script( 'admin-comments' );

require_once('admin-header.php');
if (empty($_GET['mode'])) $mode = 'view';
else $mode = wp_specialchars($_GET['mode'], 1);
?>

<script type="text/javascript">
<!--
function checkAll(form)
{
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
	}
}

function getNumChecked(form)
{
	var num = 0;
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].checked == true)
				num++;
		}
	}
	return num;
}
//-->
</script>
<div class="wrap">
<h2><?php _e('Comments'); ?></h2>
<form name="searchform" action="" method="get" id="editcomments"> 
  <fieldset> 
  <legend><?php _e('Show Comments That Contain...') ?></legend> 
  <input type="text" name="s" value="<?php if (isset($_GET['s'])) echo wp_specialchars($_GET['s'], 1); ?>" size="17" /> 
  <input type="submit" name="submit" value="<?php _e('Search') ?>"  />  
  <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
  <?php _e('(Searches within comment text, e-mail, URI, and IP address.)') ?>
  </fieldset> 
</form>
<p><a href="?mode=view"><?php _e('View Mode') ?></a> | <a href="?mode=edit"><?php _e('Mass Edit Mode') ?></a></p>
<?php
if ( !empty( $_POST['delete_comments'] ) ) :
	check_admin_referer('bulk-comments');

	$i = 0;
	foreach ($_POST['delete_comments'] as $comment) : // Check the permissions on each
		$comment = (int) $comment;
		$post_id = $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = $comment");
		// $authordata = get_userdata( $wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = $post_id") );
		if ( current_user_can('edit_post', $post_id) ) {
			if ( !empty( $_POST['spam_button'] ) )
				wp_set_comment_status($comment, 'spam');
			else
				wp_set_comment_status($comment, 'delete');
			++$i;
		}
	endforeach;
	echo '<div style="background-color: rgb(207, 235, 247);" id="message" class="updated fade"><p>';
	if ( !empty( $_POST['spam_button'] ) )
		printf(__('%s comments marked as spam.'), $i);
	else
		printf(__('%s comments deleted.'), $i);
	echo '</p></div>';
endif;

if (isset($_GET['s'])) {
	$s = $wpdb->escape($_GET['s']);
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments  WHERE
		(comment_author LIKE '%$s%' OR
		comment_author_email LIKE '%$s%' OR
		comment_author_url LIKE ('%$s%') OR
		comment_author_IP LIKE ('%$s%') OR
		comment_content LIKE ('%$s%') ) AND
		comment_approved != 'spam'
		ORDER BY comment_date DESC");
} else {
	if ( isset($_GET['offset']) )
		$offset = (int) $_GET['offset'] * 20;
	else
		$offset = 0;

	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_approved = '0' OR comment_approved = '1' ORDER BY comment_date DESC LIMIT $offset,20");
}
if ('view' == $mode) {
	if ($comments) {
		if ($offset)
			$start = " start='$offset'";
		else
			$start = '';

		echo "<ol id='the-list' class='commentlist' $start>";
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

<p><?php comment_date('M j, g:i A');  ?> &#8212; [ 
<?php
if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
	echo " <a href='comment.php?action=editcomment&amp;comment=".$comment->comment_ID."\'>" .  __('Edit') . '</a>';
	echo ' | <a href="' . wp_nonce_url('comment.php?action=deletecomment&amp;p=' . $post->ID . '&amp;comment=' . $comment->comment_ID, 'delete-comment' . $comment->comment_ID) . '" onclick="return deleteSomething( \'comment\', ' . $comment->comment_ID . ', \'' . sprintf(__("You are about to delete this comment by &quot;%s&quot;.\\n&quot;Cancel&quot; to stop, &quot;OK&quot; to delete."), js_escape($comment->comment_author)) . "' );\">" . __('Delete') . '</a> ';
	if ( ('none' != $comment_status) && ( current_user_can('moderate_comments') ) ) {
		echo '<span class="unapprove"> | <a href="' . wp_nonce_url('comment.php?action=unapprovecomment&amp;p=' . $post->ID . '&amp;comment=' . $comment->comment_ID, 'unapprove-comment' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\' );">' . __('Unapprove') . '</a> </span>';
		echo '<span class="approve"> | <a href="' . wp_nonce_url('comment.php?action=approvecomment&amp;p=' . $post->ID . '&amp;comment=' . $comment->comment_ID, 'approve-comment' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\' );">' . __('Approve') . '</a> </span>';
	}
	echo " | <a href=\"comment.php?action=deletecomment&amp;delete_type=spam&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return deleteSomething( 'comment-as-spam', $comment->comment_ID, '" . sprintf(__("You are about to mark as spam this comment by &quot;%s&quot;.\\n&quot;Cancel&quot; to stop, &quot;OK&quot; to mark as spam."), js_escape( $comment->comment_author))  . "' );\">" . __('Spam') . "</a> ";
}
$post = get_post($comment->comment_post_ID);
$post_title = wp_specialchars( $post->post_title, 'double' );
$post_title = ('' == $post_title) ? "# $comment->comment_post_ID" : $post_title;
?>
 | <a href="<?php echo get_permalink($comment->comment_post_ID); ?>" title="<?php echo $post_title; ?>"><?php _e('View Post') ?></a> ]</p>
		</li>

<?php } // end foreach($comment) ?>
</ol>

<div id="ajax-response"></div>

<?php
	} else { //no comments to show

		?>
		<p>
        <strong><?php _e('No comments found.') ?></strong></p>

		<?php
	} // end if ($comments)
} elseif ('edit' == $mode) {

	if ($comments) {
		echo '<form name="deletecomments" id="deletecomments" action="" method="post"> ';
		wp_nonce_field('bulk-comments');
		echo '<table class="widefat">
<thead>
  <tr>
    <th scope="col"><input type="checkbox" onclick="checkAll(document.getElementById(\'deletecomments\'));" /></th>
    <th scope="col" style="text-align: left">' .  __('Name') . '</th>
    <th scope="col" style="text-align: left">' .  __('E-mail') . '</th>
    <th scope="col" style="text-align: left">' . __('IP') . '</th>
    <th scope="col" style="text-align: left">' . __('Comment Excerpt') . '</th>
	<th scope="col" colspan="3">' .  __('Actions') . '</th>
  </tr>
</thead>';
		foreach ($comments as $comment) {
		$authordata = get_userdata($wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = $comment->comment_post_ID"));
		$comment_status = wp_get_comment_status($comment->comment_ID);
		$class = ('alternate' == $class) ? '' : 'alternate';
		$class .= ('unapproved' == $comment_status) ? ' unapproved' : '';
?>
  <tr id="comment-<?php echo $comment->comment_ID; ?>" class='<?php echo $class; ?>'>
    <td><?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) { ?><input type="checkbox" name="delete_comments[]" value="<?php echo $comment->comment_ID; ?>" /><?php } ?></td>
    <td><?php comment_author_link() ?></td>
    <td><?php comment_author_email_link() ?></td>
    <td><a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></td>
    <td><?php comment_excerpt(); ?></td>
    <td>
    	<?php if ('unapproved' == $comment_status) { ?>
    		(Unapproved)
    	<?php } else { ?>
    		<a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php comment_ID() ?>" class="edit"><?php _e('View') ?></a>
    	<?php } ?>
    </td>
    <td><?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
	echo "<a href='comment.php?action=editcomment&amp;comment=$comment->comment_ID' class='edit'>" .  __('Edit') . "</a>"; } ?></td>
    <td><?php if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
		echo "<a href=\"comment.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return deleteSomething( 'comment', $comment->comment_ID, '" . sprintf(__("You are about to delete this comment by &quot;%s&quot;.\\n&quot;Cancel&quot; to stop, &quot;OK&quot; to delete."), js_escape( $comment->comment_author ))  . "' );\" class='delete'>" . __('Delete') . "</a> ";
		} ?></td>
  </tr>
		<?php 
		} // end foreach
	?></table>
<p class="submit"><input type="submit" name="delete_button" value="<?php _e('Delete Checked Comments &raquo;') ?>" onclick="var numchecked = getNumChecked(document.getElementById('deletecomments')); if(numchecked < 1) { alert('<?php _e("Please select some comments to delete"); ?>'); return false } return confirm('<?php printf(__("You are about to delete %s comments permanently \\n  \'Cancel\' to stop, \'OK\' to delete."), "' + numchecked + '"); ?>')" />
			<input type="submit" name="spam_button" value="<?php _e('Mark Checked Comments as Spam &raquo;') ?>" onclick="return confirm('<?php _e("You are about to mark these comments as spam \\n  \'Cancel\' to stop, \'OK\' to mark as spam.") ?>')" /></p>
  </form>
<div id="ajax-response"></div>
<?php
	} else {
?>
<p>
<strong><?php _e('No results found.') ?></strong>
</p>
<?php
	} // end if ($comments)
}
	?>

</div>

<?php include('admin-footer.php'); ?>

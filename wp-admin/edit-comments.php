<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Edit Comments');
$parent_file = 'edit.php';

require_once('admin-header.php');
if (empty($_GET['mode'])) $mode = 'view';
else $mode = $_GET['mode'];
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
//-->
</script>
<div class="wrap">
<form name="searchform" action="" method="get"> 
  <fieldset> 
  <legend><?php _e('Show Comments That Contain...') ?></legend> 
  <input type="text" name="s" value="<?php if (isset($s)) echo $s; ?>" size="17" /> 
  <input type="submit" name="submit" value="<?php _e('Search') ?>"  />  
  <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
  <?php _e('(Searches within comment text, e-mail, URI, and IP address.)') ?>
  </fieldset> 
</form>
<p><a href="?mode=view"><?php _e('View Mode') ?></a> | <a href="?mode=edit"><?php _e('Mass Edit Mode') ?></a></p>
<?php
if ( !empty( $_POST['delete_comments'] ) ) :
	$i = 0;
	foreach ($delete_comments as $comment) : // Check the permissions on each
		$comment = (int) $comment;
		$post_id = $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = $comment");
		$authordata = get_userdata( $wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = $post_id") );
		if ( ($user_level > $authordata->user_level) || ($user_login == $authordata->user_login) ) :
			$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_ID = $comment");
			++$i;
		endif;
	endforeach;
	echo "<div class='wrap'><p>" . sprintf(__('%s comments deleted.'), $i) . "</p></div>";
endif;

if (isset($_GET['s'])) {
	$s = $wpdb->escape($_GET['s']);
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments  WHERE
		comment_author LIKE '%$s%' OR
		comment_author_email LIKE '%$s%' OR
		comment_author_url LIKE ('%$s%') OR
		comment_author_IP LIKE ('%$s%') OR
		comment_content LIKE ('%$s%')
		ORDER BY comment_date DESC");
} else {
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments ORDER BY comment_date DESC LIMIT 20");
}
if ('view' == $mode) {
	if ($comments) {
		echo '<ol class="commentlist">';
		foreach ($comments as $comment) {
		$authordata = get_userdata($wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = $comment->comment_post_ID"));
			$comment_status = wp_get_comment_status($comment->comment_ID);
			if ('unapproved' == $comment_status) {
				echo '<li class="unapproved">';
			} else {
				echo '<li>';
			}
		?>		
        <p><strong><?php _e('Name:') ?></strong> <?php comment_author() ?> <?php if ($comment->comment_author_email) { ?>| <strong><?php _e('E-mail:') ?></strong> <?php comment_author_email_link() ?> <?php } if ($comment->comment_author_url) { ?> | <strong><?php _e('URI:') ?></strong> <?php comment_author_url_link() ?> <?php } ?>| <strong><?php _e('IP:') ?></strong> <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></p>
		
		<?php comment_text() ?>

        <p><?php _e('Posted'); echo ' '; comment_date('M j, g:i A'); ?> | <?php 
			if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
				echo "<a href=\"post.php?action=editcomment&amp;comment=".$comment->comment_ID."\">" . __('Edit Comment') . "</a>";
				echo " | <a href=\"post.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('" . sprintf(__("You are about to delete this comment by \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), $comment->comment_author) . "')\">" . __('Delete Comment') . "</a> &#8212; ";
			} // end if any comments to show
			// Get post title
			$post_title = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = $comment->comment_post_ID");
			$post_title = ('' == $post_title) ? "# $comment->comment_post_ID" : $post_title;
			?> <a href="post.php?action=edit&amp;post=<?php echo $comment->comment_post_ID; ?>"><?php printf(__('Edit Post &#8220;%s&#8221;'), stripslashes($post_title)); ?></a> | <a href="<?php echo get_permalink($comment->comment_post_ID); ?>"><?php _e('View Post') ?></a></p>
		</li>

		<?php 
		} // end foreach
	echo '</ol>';
	} else {

		?>
		<p>
        <strong><?php _e('No comments found.') ?></strong></p>
		
		<?php
	} // end if ($comments)
} elseif ('edit' == $mode) {

	if ($comments) {
		echo '<form name="deletecomments" id="deletecomments" action="" method="post"> 
		<table width="100%" cellpadding="3" cellspacing="3">
  <tr>
    <th scope="col">*</th>
    <th scope="col">' .  __('Name') . '</th>
    <th scope="col">' .  __('E-mail') . '</th>
    <th scope="col">' . __('IP') . '</th>
    <th scope="col">' . __('Comment Excerpt') . '</th>
	<th scope="col" colspan="3">' .  __('Actions') . '</th>
  </tr>';
		foreach ($comments as $comment) {
		$authordata = get_userdata($wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE ID = $comment->comment_post_ID"));
		$class = ('alternate' == $class) ? '' : 'alternate';
?>
  <tr class='<?php echo $class; ?>'>
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { ?><input type="checkbox" name="delete_comments[]" value="<?php echo $comment->comment_ID; ?>" /><?php } ?></td>
    <td><?php comment_author_link() ?></td>
    <td><?php comment_author_email_link() ?></td>
    <td><a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></td>
    <td><?php comment_excerpt(); ?></td>
    <td><a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php comment_ID() ?>" class="edit"><?php _e('View') ?></a></td>
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
	echo "<a href='post.php?action=editcomment&amp;comment=$comment->comment_ID' class='edit'>" .  __('Edit') . "</a>"; } ?></td>
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
            echo "<a href=\"post.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('" . sprintf(__("You are about to delete this comment by \'%s\'\\n  \'Cancel\' to stop, \'OK\' to delete."), $comment->comment_author) . "')\"    class='delete'>" . __('Delete') . "</a>"; } ?></td>
  </tr>
		<?php 
		} // end foreach
	?></table>
    <p><a href="javascript:;" onclick="checkAll(document.getElementById('deletecomments')); return false; "><?php _e('Invert Checkbox Selection') ?></a></p>
            <p style="text-align: right;"><input type="submit" name="Submit" value="<?php _e('Delete Checked Comments') ?>" onclick="return confirm('<?php _e("You are about to delete these comments permanently \\n  \'Cancel\' to stop, \'OK\' to delete.") ?>')" />	</p>
  </form>
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

<?php 
include('admin-footer.php');
?>

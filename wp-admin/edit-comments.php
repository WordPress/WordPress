<?php
$title = 'Edit Comments';
$parent_file = 'edit.php';
require_once('admin-header.php');
?>
<ul id="adminmenu2">
	<li><a href="edit.php">Posts</a></li>
	<li><a href="edit-comments.php" class="current">Comments</a></li>
	<li class="last"><a href="moderation.php">Awaiting Moderation</a></li>
</ul>

<div class="wrap">
<form name="searchform" action="" method="get"> 
  <fieldset> 
  <legend>Show Comments That Contain...</legend> 
  <input type="text" name="s" value="<?php echo $s; ?>" size="17" /> 
  <input type="submit" name="submit" value="Search"  /> 
  </fieldset> 
</form>
<p><a href="?mode=view">View Mode</a> | <a href="?mode=edit">Mass Edit Mode</a></p>
<?php
if ($s) {
	$s = $wpdb->escape($s);
	$comments = $wpdb->get_results("SELECT * FROM $tablecomments  WHERE
		comment_author LIKE '%$s%' OR
		comment_author_email LIKE '%$s%' OR
		comment_author_url LIKE ('%$s%') OR
		comment_author_IP LIKE ('%$s%') OR
		comment_content LIKE ('%$s%')
		ORDER BY comment_date");
} else {
	$comments = $wpdb->get_results("SELECT * FROM $tablecomments ORDER BY comment_date $commentorder LIMIT 20");
}
	if ($comments) {
		echo '<ol>';
		foreach ($comments as $comment) {
			$comment_status = wp_get_comment_status($comment->comment_ID);
			if ('unapproved' == $comment_status) {
				echo '<li class="unapproved" style="border-bottom: 1px solid #ccc;">';
			} else {
				echo '<li style="border-bottom: 1px solid #ccc;">';
			}
		?>		
		<p><strong>Name:</strong> <?php comment_author() ?> <?php if ($comment->comment_author_email) { ?>| <strong>Email:</strong> <?php comment_author_email_link() ?> <?php } if ($comment->comment_author_email) { ?> | <strong>URI:</strong> <?php comment_author_url_link() ?> <?php } ?>| <strong>IP:</strong> <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></p>
		
		<?php comment_text() ?>
		<p>Posted <?php comment_date('M j, g:i A') ?> | <?php 
			if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
				echo "<a href=\"post.php?action=editcomment&amp;comment=".$comment->comment_ID."\">Edit Comment</a>";
				echo " | <a href=\"post.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('You are about to delete this comment by \'".$comment->comment_author."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete Comment</a> &#8212; ";
			} // end if any comments to show
			// Get post title
			$post_title = $wpdb->get_var("SELECT post_title FROM $tableposts WHERE ID = $comment->comment_post_ID");
			$post_title = ('' == $post_title) ? "# $comment->comment_post_ID" : $post_title;
			?> <a href="post.php?action=edit&amp;post=<?php echo $comment->comment_post_ID; ?>">Edit Post &#8220;<?php echo stripslashes($post_title); ?>&#8221;</a> | <a href="<?php echo get_permalink($comment->comment_post_ID); ?>">View Post</a></p>
		</li>

		<?php 
		} // end foreach
	echo '</ol>';
	} else {

		?>
		<p>
		<strong>No results found.</strong>
		</p>
		
		<?php
	} // end if ($comments)

	?>

</div>

<?php 
include('admin-footer.php');
?>

<?php
$title = 'Edit Comments';
$parent_file = 'edit.php';
require_once('b2header.php');

if (!$showcomments) {
	if ($comments_per_page) {
		$showcomments=$comments_per_page;
	} else {
		$showcomments=10;
		$comments_per_page=$showcomments;
	}
} else {
	$comments_per_page = $showcomments;
}

if ((!empty($commentstart)) && (!empty($commentend)) && ($commentstart == $commentend)) {
	$p=$commentstart;
	$commentstart=0;
	$commentend=0;
}

if (!$commentstart) {
	$commentstart=0;
	$commentend=$showcomments;
}

$nextXstart=$commentend;
$nextXend=$commentend+$showcomments;

$previousXstart=($commentstart-$showcomments);
$previousXend=$commentstart;
if ($previousXstart < 0) {
	$previousXstart=0;
	$previousXend=$showcomments;
}

ob_start();
?>
<ul id="adminmenu2">
	<li><a href="edit.php">Latest Posts</a></li>
	<li><a href="edit-comments.php" class="current">Latest Comments</a></li>
	<li class="last"><a href="wp-moderation.php">Comments Awaiting Moderation</a></li>
</ul>

<div class="wrap">
<table width="100%">
  <tr>
    <td valign="top" width="200">
      Show comments:
    </td>
    <td>
      <table cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td colspan="2" align="center"><!-- show next/previous X comments -->
            <form name="previousXcomments" method="get" action="">
<?php
if ($previousXstart > 0) {
?>
              <input type="hidden" name="showcomments" value="<?php echo $showcomments; ?>" />
              <input type="hidden" name="commentstart" value="<?php echo $previousXstart; ?>" />
              <input type="hidden" name="commentend" value="<?php echo $previousXend; ?>" />
              <input type="submit" name="submitprevious" class="search" value="< <?php echo $showcomments ?>" />
<?php
}
?>
            </form>
          </td>
          <td>
            <form name="nextXcomments" method="get" action="">
              <input type="hidden" name="showcomments" value="<?php echo $showcomments; ?>" />
              <input type="hidden" name="commentstart" value="<?php echo $nextXstart; ?>" />
              <input type="hidden" name="commentend" value="<?php echo $nextXend; ?>" />
              <input type="submit" name="submitnext" class="search" value="<?php echo $showcomments ?> >" />
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td valign="top" width="200"><!-- show X first/last comments -->
      <form name="showXfirstlastcomments" method="get" action="">
        <input type="text" name="showcomments" value="<?php echo $showcomments ?>" style="width:40px;" /?>
<?php
if (empty($commentorder))
  $commentorder="DESC";
$i = $commentorder;
if ($i == "DESC")
 $besp_selected = "selected='selected'";
?>
        <select name="commentorder">
          <option value="DESC" <?php echo $besp_selected ?>>last comments</option>
<?php
$besp_selected = "";
if ($i == "ASC")
$besp_selected = "selected='selected'";
?>
          <option value="ASC" <?php echo $besp_selected?>>first comments</option>
        </select>&nbsp;
        <input type="submit" name="submitfirstlast" class="search" value="OK" />
      </form>
    </td>
    <td valign="top"><!-- show comment X to comment X -->
      <form name="showXfirstlastcomments" method="get" action="">
        <input type="text" name="commentstart" value="<?php echo $commentstart ?>" style="width:40px;" /?>&nbsp;to&nbsp;<input type="text" name="commentend" value="<?php echo $commentend ?>" style="width:40px;" /?>&nbsp;
        <select name="commentorder">
<?php
$besp_selected = "";
$i = $commentorder;
if ($i == "DESC")
  $besp_selected = "selected='selected'";
?>
          <option value="DESC" "<?php echo $besp_selected ?>">from the end</option>
<?php
$besp_selected = "";
if ($i == "ASC")
  $besp_selected = "selected='selected'";
?>        <option value="ASC" "<?php echo $besp_selected ?>">from the start</option>
        </select>&nbsp;
        <input type="submit" name="submitXtoX" class="search" value="OK" />
      </form>
    </td>
  </tr>
</table>
</div>
<?php
$comments_nav_bar = ob_get_contents();
ob_end_clean();
echo $comments_nav_bar;
?>

<div class="wrap">

	<?php
	$comments = $wpdb->get_results("SELECT * FROM $tablecomments
									ORDER BY comment_date $commentorder 
									LIMIT $commentstart, $commentend"
	                              );

// need to account for offet, etc.

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
				echo "<a href=\"wp-post.php?action=editcomment&amp;comment=".$comment->comment_ID."\">Edit Comment</a>";
				echo " | <a href=\"wp-post.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('You are about to delete this comment by \'".$comment->comment_author."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete</a> | ";
			} // end if any comments to show
			// Get post title
			$post_title = $wpdb->get_var("SELECT post_title FROM $tableposts WHERE ID = $comment->comment_post_ID");
			$post_title = ('' == $post_title) ? "# $comment->comment_post_ID" : $post_title;
			?> <a href="wp-post.php?action=edit&amp;post=<?php echo $comment->comment_post_ID; ?>">Edit Post &#8220;<?php echo $post_title; ?>&#8221;</a></p>
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
echo $comments_nav_bar; 
include('b2footer.php');
?>

<?php

require_once('../wp-config.php');

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

<h2 id="comments">Comments</h2>

<p class="anchors">Go to: <a href="b2edit.php#top">Post/Edit</a> | <a href="b2edit.php#posts">Posts</a> | <a href="b2edit.php#comments">Comments</a></p>

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
	// these lines are b2's "motor", do not alter nor remove them
//	include($abspath.'blog.header.php');

	$comments = $wpdb->get_results("SELECT * FROM $tablecomments "
	                              ."ORDER BY comment_date $commentorder "
	                              ."LIMIT $commentstart, $commentend"
	                              );

// need to account for offet, etc.

	if ($comments) {
		foreach ($comments as $comment) {
		?>		
		<p>
			<?php comment_date('Y/m/d') ?> @ <?php comment_time() ?> 
			[ 
			<?php 
			if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
				echo "<a href=\"b2edit.php?action=editcomment&amp;comment=".$comment->comment_ID."\">Edit</a>";
				echo " - <a href=\"b2edit.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('You are about to delete this comment by \'".$comment->comment_author."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete</a> - ";
			} // end if any comments to show
			?>
			<a href="b2edit.php?p=<?php echo $comment->comment_post_ID; ?>&c=1">View Post</a> ]
			<br />
			<strong><?php comment_author() ?> ( <?php comment_author_email_link() ?> / <?php comment_author_url_link() ?> )</strong> (IP: <?php comment_author_IP() ?>)
			<?php comment_text() ?>
		
		</p>

		<br />

		<?php 
		} // end foreach

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
// uncomment this to show the nav bar at the bottom as well
// echo $comments_nav_bar; 
?>
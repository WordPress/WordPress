<?php
$title = 'Latest Comments and Comment Queue';
require_once('b2header.php');

if (!$showcomments) {
	if ($comments_per_page) {
		$showcomments = $comments_per_page;
	} else {
		$showcomments = 30;
		$comments_per_page = $showcomments;
	}
} else {
	$comments_per_page = $showcomments;
}

if ((!empty($commentstart)) && (!empty($commentend)) && ($commentstart == $commentend)) {
	$p = $commentstart;
	$commentstart = 0;
	$commentend = 0;
}

if (!$commentstart) {
	$commentstart = 0;
	$commentend = $showcomments;
}

$nextXstart = $commentend;
$nextXend = $commentend+$showcomments;

$previousXstart = ($commentstart-$showcomments);
$previousXend = $commentstart;
if ($previousXstart < 0) {
	$previousXstart = 0;
	$previousXend = $showcomments;
}
ob_start();
?>

<div class="wrap">
      Show comments: <!-- show next/previous X comments -->
            
<?php
if ($previousXstart > 0) {
?>
<form name="previousXcomments" method="get" action="" style="float: left; margin: 5px;">
              <input type="hidden" name="showcomments" value="<?php echo $showcomments; ?>" />
              <input type="hidden" name="commentstart" value="<?php echo $previousXstart; ?>" />
              <input type="hidden" name="commentend" value="<?php echo $previousXend; ?>" />
              <input type="submit" name="submitprevious" class="search" value="< <?php echo $showcomments ?>" />
			  </form>
<?php
}
?>
            

            <form name="nextXcomments" method="get" action="" style="float: left; margin: 5px;">
              <input type="hidden" name="showcomments" value="<?php echo $showcomments; ?>" />
              <input type="hidden" name="commentstart" value="<?php echo $nextXstart; ?>" />
              <input type="hidden" name="commentend" value="<?php echo $nextXend; ?>" />
              <input type="submit" name="submitnext" class="search" value="<?php echo $showcomments ?> >" />
            </form>

<!-- show X first/last comments -->
      <form name="showXfirstlastcomments" method="get" action="" style="float: left;  margin: 5px;">
        <input type="text" name="showcomments" value="<?php echo $showcomments ?>" style="width:40px;" /?>
<?php
if (empty($commentorder))
  $commentorder = 'DESC';
$i = $commentorder;
if ($i == 'DESC')
 $besp_selected = "selected='selected'";
?>
        <select name="commentorder">
          <option value="DESC" <?php echo $besp_selected ?>>Last Comments</option>
<?php
$besp_selected = "";
if ($i == "ASC")
$besp_selected = "selected='selected'";
?>
          <option value="ASC" <?php echo $besp_selected?>>First Comments</option>
        </select>&nbsp;
        <input type="submit" name="submitfirstlast" class="search" value="OK" />
      </form>

<!-- show comment X to comment X -->
      <form name="showXfirstlastcomments" method="get" action="" style="float: left;  margin: 5px;">
        <input type="text" name="commentstart" value="<?php echo $commentstart ?>" style="width:40px;" /?>&nbsp;to&nbsp;<input type="text" name="commentend" value="<?php echo $commentend ?>" style="width:40px;" /?>&nbsp;
        <select name="commentorder">
<?php
$besp_selected = '';
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
	  <br style="clear: both;" />
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
		?>		
		<li style="border-bottom: 1px solid #ccc;">
		<p><strong>Name:</strong> <?php comment_author() ?> <?php if ($comment->comment_author_email) { ?>| <strong>Email:</strong> <?php comment_author_email_link() ?> <?php } if ($comment->comment_author_email) { ?> | <strong>URI:</strong> <?php comment_author_url_link() ?> <?php } ?>| <strong>IP:</strong> <?php comment_author_IP() ?></p>
		
		<?php comment_text() ?>
		<p>Posted <?php comment_date('M j, g:i A') ?> | <?php 
			if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
				echo "<a href=\"b2edit.php?action=editcomment&amp;comment=".$comment->comment_ID."\">Edit</a>";
				echo " | <a href=\"b2edit.php?action=deletecomment&amp;p=".$comment->comment_post_ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('You are about to delete this comment by \'".$comment->comment_author."\'\\n  \'Cancel\' to stop, \'OK\' to delete.')\">Delete</a> | ";
			} // end if any comments to show
			?> <a href="b2edit.php?p=<?php echo $comment->comment_post_ID; ?>">View Post</a></p>
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
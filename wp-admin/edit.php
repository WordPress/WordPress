<?php
$title = 'Posts';
require_once('admin-header.php');

?>
 <ul id="adminmenu2"> 
  <li><a href="edit.php" class="current">Posts</a></li> 
  <li><a href="edit-comments.php">Comments</a></li> 
  <li class="last"><a href="moderation.php">Awaiting Moderation</a></li> 
</ul> 
<?php
get_currentuserinfo();
$drafts = $wpdb->get_results("SELECT ID, post_title FROM $tableposts WHERE post_status = 'draft' AND post_author = $user_ID");
if ($drafts) {
	?> 
<div class="wrap"> 
  <p><strong>Your Drafts:</strong> 
    <?php
	$i = 0;
	foreach ($drafts as $draft) {
		if (0 != $i)
			echo ', ';
		$draft->post_title = stripslashes($draft->post_title);
		if ($draft->post_title == '')
			$draft->post_title = 'Post #'.$draft->ID;
		echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='Edit this draft'>$draft->post_title</a>";
		++$i;
		}
	?> 
    .</p> 
</div> 
<?php
}
?> 
<div class="wrap"> 
<form name="searchform" action="" method="get"> 
  <fieldset> 
  <legend>Show Posts That Contain...</legend> 
  <input type="text" name="s" value="<?php echo $s; ?>" size="17" /> 
  <input type="submit" name="submit" value="Search"  /> 
  </fieldset> 
</form> 
<table width="100%" cellpadding="3" cellspacing="3"> 
  <tr> 
    <th scope="col">ID</th> 
    <th scope="col">When</th> 
    <th scope="col">Title</th> 
    <th scope="col">Categories</th> 
    <th scope="col">Comments</th> 
    <th scope="col">Author</th> 
    <th scope="col">Edit</th> 
    <th scope="col">Delete</th> 
  </tr> 
  <?php
include(ABSPATH.'wp-blog-header.php');

if ($posts) {
foreach ($posts as $post) { start_wp();
$bgcolor = ('#eee' == $bgcolor) ? 'none' : '#eee';
?> 
  <tr style='background-color: <?php echo $bgcolor; ?>'> 
    <th scope="row"><?php echo $id ?></th> 
    <td><?php the_time('Y-m-d \<\b\r \/\> g:i:s a'); ?></td> 
    <td><a href="<?php permalink_link(); ?>" rel="permalink"> 
      <?php the_title() ?> 
      </a> 
      <?php if ('private' == $post->post_status) echo ' - <strong>Private</strong>'; ?></td> 
    <td><?php the_category(','); ?></td> 
    <td><a href="edit.php?p=<?php echo $id ?>&c=1"> 
      <?php comments_number('no comments', '1 comment', "% comments") ?> 
      </a></td> 
    <td><?php the_author() ?></td> 
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>Edit</a>"; } ?></td> 
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { echo "<a href='post.php?action=delete&amp;post=$id' class='delete' onclick=\"return confirm('You are about to delete this post \'".the_title('','',0)."\'\\n  \'OK\' to delete, \'Cancel\' to stop.')\">Delete</a>"; } ?></td> 
  </tr> 
<?php
}
} else {
?>
  <tr style='background-color: <?php echo $bgcolor; ?>'> 
    <td colspan="8">No posts found.</td> 
  </tr> 
<?php
} // end if ($posts)
?> 
</table> 
<?php
if (($withcomments) or ($single)) {

	$comments = $wpdb->get_results("SELECT * FROM $tablecomments WHERE comment_post_ID = $id ORDER BY comment_date");
	if ($comments) {
	?> 
<h3>Comments</h3> 
<ol id="comments"> 
<?php
foreach ($comments as $comment) {
$comment_status = wp_get_comment_status($comment->comment_ID);
?> 

<li <?php if ("unapproved" == $comment_status) echo "class='unapproved'"; ?> >
  <?php comment_date('Y-n-j') ?> 
  @
  <?php comment_time('g:m:s a') ?> 
  <?php 
			if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
				echo "[ <a href=\"post.php?action=editcomment&amp;comment=".$comment->comment_ID."\">Edit</a>";
				echo " - <a href=\"post.php?action=deletecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('You are about to delete this comment by \'".$comment->comment_author."\'\\n  \'OK\' to delete, \'Cancel\' to stop.')\">Delete</a> ";
				if ( ('none' != $comment_status) && ($user_level >= 3) ) {
					if ('approved' == wp_get_comment_status($comment->comment_ID)) {
						echo " - <a href=\"post.php?action=unapprovecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\">Unapprove</a> ";
					} else {
						echo " - <a href=\"post.php?action=approvecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\">Approve</a> ";
					}
				}
				echo "]";
			} // end if any comments to show
			?> 
  <br /> 
  <strong> 
  <?php comment_author() ?> 
  (
  <?php comment_author_email_link() ?> 
  /
  <?php comment_author_url_link() ?> 
  )</strong> (IP:
  <?php comment_author_IP() ?> 
  )
  <?php comment_text() ?> 

</li> 
<!-- /comment --> 
<?php //end of the loop, don't delete
		} // end foreach
	echo '</ol>';
	}//end if comments
	?>
	<p><a href="edit.php">Back to posts</a></p>
<?php } ?> 
</div> 
<?php 
 include('admin-footer.php');
?> 

<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Pages');
$parent_file = 'edit.php';
require_once('admin-header.php');

get_currentuserinfo();
?>

<div class="wrap"> 
    <?php echo "<p> <a href='post.php?action=createpage' title='" . __('Create a new page') . "'>Create New Page</a></p>";	?> 
</div> 

<div class="wrap"> 
<form name="searchform" action="" method="get" style="float: left; width: 20em;"> 
  <fieldset> 
  <legend><?php _e('Show Pages That Contain...') ?></legend> 
  <input type="text" name="s" value="<?php if (isset($s)) echo $s; ?>" size="17" /> 
  <input type="submit" name="submit" value="<?php _e('Search') ?>"  /> 
  </fieldset>
</form>

<br style="clear:both;" />

<table width="100%" cellpadding="3" cellspacing="3"> 
  <tr> 
    <th scope="col"><?php _e('ID') ?></th> 
    <th scope="col"><?php _e('When') ?></th> 
    <th scope="col"><?php _e('Title') ?></th> 
    <th scope="col"><?php _e('Author') ?></th> 
	<th scope="col"></th> 
    <th scope="col"></th> 
    <th scope="col"></th> 
  </tr> 
<?php

include(ABSPATH.'wp-blog-header.php');

if (isset($user_ID) && ('' != intval($user_ID))) {
    $posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static' AND post_author = $user_ID");
} else {
    $posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static'");
}

if ($posts) {
$bgcolor = '';
foreach ($posts as $post) { start_wp();
$class = ('alternate' == $class) ? '' : 'alternate';
?> 
  <tr class='<?php echo $class; ?>'> 
    <th scope="row"><?php echo $id ?></th> 
    <td><?php the_time('Y-m-d \<\b\r \/\> g:i:s a'); ?></td> 
    <td>
      <?php the_title() ?> 
    </td> 
    <td><?php the_author() ?></td> 
	<td><a href="<?php the_permalink(); ?>" rel="permalink" class="edit">View</a></td>
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td> 
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { echo "<a href='post.php?action=delete&amp;post=$id' class='delete' onclick=\"return confirm('" . sprintf(__("You are about to delete this post \'%s\'\\n  \'OK\' to delete, \'Cancel\' to stop."), the_title('','',0)) . "')\">" . __('Delete') . "</a>"; } ?></td> 
  </tr> 
<?php
}
} else {
?>
  <tr style='background-color: <?php echo $bgcolor; ?>'> 
    <td colspan="8"><?php _e('No pages found.') ?></td> 
  </tr> 
<?php
} // end if ($posts)
?> 
</table> 
<?php
if ( 1 == count($posts) ) {

	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $id ORDER BY comment_date");
	if ($comments) {
	?> 
<h3><?php _e('Comments') ?></h3> 
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
				echo "[ <a href=\"post.php?action=editcomment&amp;comment=".$comment->comment_ID."\">" .  __('Edit') . "</a>";
				echo " - <a href=\"post.php?action=deletecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('" . sprintf(__("You are about to delete this comment by \'%s\'\\n  \'OK\' to delete, \'Cancel\' to stop."), $comment->comment_author) . "')\">" . __('Delete') . "</a> ";
				if ( ('none' != $comment_status) && ($user_level >= 3) ) {
					if ('approved' == wp_get_comment_status($comment->comment_ID)) {
						echo " - <a href=\"post.php?action=unapprovecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\">" . __('Unapprove') . "</a> ";
					} else {
						echo " - <a href=\"post.php?action=approvecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\">" . __('Approve') . "</a> ";
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
<?php } ?> 
</div> 
<?php 
 include('admin-footer.php');
?> 

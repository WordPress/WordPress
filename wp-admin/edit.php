<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Posts');
require_once('admin-header.php');

?>
 <ul id="adminmenu2"> 
  <li><a href="edit.php" class="current"><?php _e('Posts') ?></a></li> 
  <li><a href="edit-comments.php"><?php _e('Comments') ?></a></li> 
  <li class="last"><a href="moderation.php"><?php _e('Awaiting Moderation') ?></a></li> 
</ul> 
<?php
get_currentuserinfo();
$drafts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'draft' AND post_author = $user_ID");
if ($drafts) {
	?> 
<div class="wrap"> 
    <p><strong><?php _e('Your Drafts:') ?></strong> 
    <?php
	$i = 0;
	foreach ($drafts as $draft) {
		if (0 != $i)
			echo ', ';
		$draft->post_title = stripslashes($draft->post_title);
		if ($draft->post_title == '')
			$draft->post_title = sprintf(__('Post #%s'), $draft->ID);
		echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='" . __('Edit this draft') . "'>$draft->post_title</a>";
		++$i;
		}
	?> 
    .</p> 
</div> 
<?php
}
?> 
<div class="wrap"> 
<?php
if( isset( $_GET['m'] ) )
{
	echo '<h2>' . $month[substr( $_GET['m'], 4, 2 )]." ".substr( $_GET['m'], 0, 4 )."</h2>";
}
?>

<form name="viewarc" action="" method="get" style="float: left; width: 20em;">
	<fieldset>
	<legend><?php _e('Show Posts From Month of...') ?></legend>
    <select name='m'>
	<?php
		$arc_result=$wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts ORDER BY post_date DESC");
		foreach ($arc_result as $arc_row) {			
			$arc_year  = $arc_row->yyear;
			$arc_month = $arc_row->mmonth;
			
			if( isset($_GET['m']) && $arc_year . zeroise($arc_month, 2) == $_GET['m'] )
				$default = 'selected="selected"';
			else
				$default = null;
			
			echo "<option $default value=\"" . $arc_year.zeroise($arc_month, 2) . '">';
			echo $month[zeroise($arc_month, 2)] . " $arc_year";
			echo "</option>\n";
		}
	?>
	</select>
		<input type="submit" name="submit" value="<?php _e('Show Month') ?>"  /> 
	</fieldset>
</form>
<form name="searchform" action="" method="get" style="float: left; width: 20em; margin-left: 3em;"> 
  <fieldset> 
  <legend><?php _e('Show Posts That Contain...') ?></legend> 
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
    <th scope="col"><?php _e('Categories') ?></th> 
    <th scope="col"><?php _e('Comments') ?></th> 
    <th scope="col"><?php _e('Author') ?></th> 
    <th scope="col"><?php _e('Edit') ?></th> 
    <th scope="col"><?php _e('Delete') ?></th> 
  </tr> 
<?php
if (empty($m)) $showposts = 15;
include(ABSPATH.'wp-blog-header.php');

if ($posts) {
$bgcolor = '';
foreach ($posts as $post) { start_wp();
$bgcolor = ('#eee' == $bgcolor) ? 'none' : '#eee';
?> 
  <tr style='background-color: <?php echo $bgcolor; ?>'> 
    <th scope="row"><?php echo $id ?></th> 
    <td><?php the_time('Y-m-d \<\b\r \/\> g:i:s a'); ?></td> 
    <td><a href="<?php the_permalink(); ?>" rel="permalink"> 
      <?php the_title() ?> 
      </a> 
    <?php if ('private' == $post->post_status) _e(' - <strong>Private</strong>'); ?></td> 
    <td><?php the_category(','); ?></td> 
    <td><a href="edit.php?p=<?php echo $id ?>&c=1"> 
      <?php comments_number(__('no comments'), __('1 comment'), __("% comments")) ?> 
      </a></td> 
    <td><?php the_author() ?></td> 
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td> 
    <td><?php if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) { echo "<a href='post.php?action=delete&amp;post=$id' class='delete' onclick=\"return confirm('" . sprintf(__("You are about to delete this post \'%s\'\\n  \'OK\' to delete, \'Cancel\' to stop."), the_title('','',0)) . "')\">" . __('Delete') . "</a>"; } ?></td> 
  </tr> 
<?php
}
} else {
?>
  <tr style='background-color: <?php echo $bgcolor; ?>'> 
    <td colspan="8"><?php _e('No posts found.') ?></td> 
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

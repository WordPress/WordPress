<?php
require_once('admin.php'); 
$title = __('Dashboard'); 
require_once('admin-header.php');

$today = current_time('mysql');
?>

<div class="wrap">
<div id="zeitgeist">
<h2><?php _e('Latest Activity'); ?></h2>
<?php
if ( $recentposts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_date_gmt < '$today' ORDER BY post_date DESC LIMIT 5") ) :
?>
<div>
<h3><?php _e('Posts'); ?></h3>
<ul>
<?php
foreach ($recentposts as $post) {
	if ($post->post_title == '')
		$post->post_title = sprintf(__('Post #%s'), $post->ID);
	echo "<li><a href='post.php?action=edit&amp;post=$post->ID'>";
	the_title();
	echo '</a></li>';
}
?>
</ul>
</div>
<?php endif; ?>

<?php
if ( $scheduled = $wpdb->get_results("SELECT ID, post_title, post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_date_gmt > '$today'") ) :
?> 
<div>
<h3><?php _e('Scheduled Entries:') ?></h3>
<ul>
<?php
foreach ($scheduled as $post) {
	if ($post->post_title == '')
		$post->post_title = sprintf(__('Post #%s'), $post->ID);
	echo "<li><a href='post.php?action=edit&amp;post=$post->ID' title='" . __('Edit this post') . "'>$post->post_title</a> in " . human_time_diff( time(), strtotime($post->post_date) )  . "</li>";
}
?> 
</ul>
</div>
<?php endif; ?>

<?php
if ( $comments = $wpdb->get_results("SELECT comment_author, comment_author_url, comment_ID, comment_post_ID FROM $wpdb->comments ORDER BY comment_date_gmt DESC LIMIT 5") ) :
?>
<div>
<h3><?php _e('Comments'); ?></h3>
<ul>
<?php 
foreach ($comments as $comment) {
	echo '<li>' . sprintf('%s on %s', get_comment_author_link(), '<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">' . get_the_title($comment->comment_post_ID) . '</a>');
	edit_comment_link(__("Edit"), ' <small>(', ')</small>'); 
	echo '</li>';
}
?>
</ul>
<?php 
if ( $numcomments = $wpdb->get_var("SELECT COUNT(*) FROM $tablecomments WHERE comment_approved = '0'") ) :
?>
<p><a href="moderation.php"><?php echo sprintf(__('There are comments in moderation (%s)'), number_format($numcomments) ); ?> &raquo;</a></p>
<?php endif; ?>
</div>

<?php endif; ?>

<div>
<h3><?php _e('Blog Stats'); ?></h3>
<?php
$numposts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish'");
if (0 < $numposts) $numposts = number_format($numposts); 

$numcomms = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");
if (0 < $numcomms) $numcomms = number_format($numcomms);

$numcats = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->categories");
if (0 < $numcats) $numcats = number_format($numcats);
?>
<p>There are currently <?php echo $numposts ?> <a href="edit.php" title="posts">posts</a> and <?php echo $numcomms ?> <a href="edit-comments.php" title="Comments">comments</a>, contained within <?php echo $numcats ?> <a href="categories.php" title="categories">categories</a>.</p>
</div>

</div>

<h2><?php _e('Dashboard'); ?></h2> 
<br clear="all" />
</div>
<div class="wrap">
<p>
		<strong>
		<?php _e('Your Drafts:') ?>
		</strong>
<br />
		<?php
get_currentuserinfo();
$drafts = $wpdb->get_results("SELECT ID, post_title FROM $tableposts WHERE post_status = 'draft' AND post_author = $user_ID");
if ($drafts) {
	?>
    <?php
	$i = 0;
	foreach ($drafts as $draft) {
		if (0 != $i)
			echo ', ';
		$draft->post_title = stripslashes($draft->post_title);
		if ($draft->post_title == '')
			$draft->post_title = sprintf(__('Post #%s'), $draft->ID);
		echo "<a href='post.php?action=edit&post=$draft->ID' title='" . __('Edit this draft') . "'>$draft->post_title</a>";
		++$i;
		}
		}else{
		 echo ('No Entries found.');
		 }
		?>
 	</p>
		</div>
<?php
require('./admin-footer.php');
?>
<?php
require_once('admin.php'); 
$title = __('Dashboard'); 
require_once('admin-header.php');
require_once (ABSPATH . WPINC . '/rss-functions.php');

$today = current_time('mysql', 1);
?>

<div class="wrap">
<div id="zeitgeist">
<h2><?php _e('Latest Activity'); ?></h2>
<?php
if ( $recentposts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_date_gmt < '$today' ORDER BY post_date DESC LIMIT 5") ) :
?>
<div>
<h3><?php _e('Posts'); ?> <a href="edit.php" title="<?php _e('More posts...'); ?>">&raquo;</a></h3>
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
if ( $scheduled = $wpdb->get_results("SELECT ID, post_title, post_date_gmt FROM $wpdb->posts WHERE post_status = 'publish' AND post_date_gmt > '$today'") ) :
?> 
<div>
<h3><?php _e('Scheduled Entries:') ?></h3>
<ul>
<?php
foreach ($scheduled as $post) {
	if ($post->post_title == '')
		$post->post_title = sprintf(__('Post #%s'), $post->ID);
	echo "<li>" . sprintf(__('%1$s in %2$s'), "<a href='post.php?action=edit&amp;post=$post->ID' title='" . __('Edit this post') . "'>$post->post_title</a>", human_time_diff( current_time('timestamp', 1), strtotime($post->post_date_gmt. ' GMT') ))  . "</li>";
}
?> 
</ul>
</div>
<?php endif; ?>

<?php
if ( $comments = $wpdb->get_results("SELECT comment_author, comment_author_url, comment_ID, comment_post_ID FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 5") ) :
?>
<div>
<h3><?php _e('Comments'); ?> <a href="edit-comments.php" title="<?php _e('More comments...'); ?>">&raquo;</a></h3>

<?php 
if ( $numcomments = $wpdb->get_var("SELECT COUNT(*) FROM $tablecomments WHERE comment_approved = '0'") ) :
?>
<p><strong><a href="moderation.php"><?php echo sprintf(__('Comments in moderation (%s)'), number_format($numcomments) ); ?> &raquo;</a></strong></p>
<?php endif; ?>
</div>

<?php endif; ?>

<ul>
<?php 
foreach ($comments as $comment) {
	echo '<li>' . sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">' . get_the_title($comment->comment_post_ID) . '</a>');
	edit_comment_link(__("Edit"), ' <small>(', ')</small>'); 
	echo '</li>';
}
?>
</ul>

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
<p><?php printf(__('There are currently %1$s <a href="%2$s" title="Posts">posts</a> and %3$s <a href="%4$s" title="Comments">comments</a>, contained within %5$s <a href="%6$s" title="categories">categories</a>.'), $numposts, 'edit.php',  $numcomms, 'edit-comments.php', $numcats, 'categories.php'); ?></p>
</div>

<?php
$rss = @fetch_rss('http://feeds.technorati.com/cosmos/rss/?url='. trailingslashit(get_option('home')) .'&partner=wordpress');
if ( isset($rss->items) && 0 != count($rss->items) ) {
?>
<div id="incominglinks">
<h3><?php _e('Incoming Links'); ?> <cite><a href="http://www.technorati.com/cosmos/search.html?url=<?php echo trailingslashit(get_option('home')); ?>&amp;partner=wordpress"><?php _e('More'); ?> &raquo;</a></cite></h3>
<ul>
<?php
$rss->items = array_slice($rss->items, 0, 10);
foreach ($rss->items as $item ) {
?>
	<li><a href="<?php echo wp_filter_kses($item['link']); ?>"><?php echo wp_specialchars($item['title']); ?></a></li>
<?php } ?>
</ul>
</div>
<?php } ?>

</div>

<h2><?php _e('Dashboard'); ?></h2>
<p><?php _e('Below is the latest news from the official WordPress development blog, click on a title to read the full entry.'); ?></p>
<?php
$rss = @fetch_rss('http://wordpress.org/development/feed/');
if ( isset($rss->items) && 0 != count($rss->items) ) {
?>
<h3>WordPress Development Blog</h3>
<?php
$rss->items = array_slice($rss->items, 0, 3);
foreach ($rss->items as $item ) {
?>
<h4><a href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a> &#8212; <?php echo human_time_diff( strtotime($item['pubdate'], time() ) ); ?> <?php _e('ago'); ?></h4>
<p><?php echo $item['description']; ?></p>
<?php
	}
}
?>


<?php
$rss = @fetch_rss('http://planet.wordpress.org/feed/');
if ( isset($rss->items) && 0 != count($rss->items) ) {
?>
<div id="planetnews">
<h3><?php _e('Other WordPress News'); ?> <a href="http://planet.wordpress.org/"><?php _e('more'); ?> &raquo;</a></h3>
<ul>
<?php
$rss->items = array_slice($rss->items, 0, 20);
foreach ($rss->items as $item ) {
?>
<li><a href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a></li>
<?php
	}
?>
</ul>
</div>
<?php
}
?>
<div style="clear: both">&nbsp;
<br clear="all" />
</div>
</div>

<?php
require('./admin-footer.php');
?>
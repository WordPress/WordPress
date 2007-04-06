<?php
require_once('admin.php'); 

function index_js() {
?>
<script type="text/javascript">
Event.observe( window, 'load', dashboard_init, false );
function dashboard_init() {
	var update1 = new Ajax.Updater( 'incominglinks', 'index-extra.php?jax=incominglinks' );
	var update2 = new Ajax.Updater( 'devnews', 'index-extra.php?jax=devnews' );
	var update3 = new Ajax.Updater( 'planetnews', 'index-extra.php?jax=planetnews'	);
}
</script>
<?php
}
add_action( 'admin_head', 'index_js' );
wp_enqueue_script('prototype');
wp_enqueue_script('interface');

$title = __('Dashboard'); 
$parent_file = 'index.php';
require_once('admin-header.php');

$today = current_time('mysql', 1);
?>

<div class="wrap">

<h2><?php _e('Welcome to WordPress'); ?></h2>

<div id="zeitgeist">
<h2><?php _e('Latest Activity'); ?></h2>

<div id="incominglinks"></div>

<?php
$comments = $wpdb->get_results("SELECT comment_author, comment_author_url, comment_ID, comment_post_ID FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 5");
$numcomments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'");

if ( $comments || $numcomments ) :
?>
<div>
<h3><?php _e('Comments'); ?> <a href="edit-comments.php" title="<?php _e('More comments...'); ?>">&raquo;</a></h3>

<?php if ( $numcomments ) : ?>
<p><strong><a href="moderation.php"><?php echo sprintf(__('Comments in moderation (%s)'), number_format($numcomments) ); ?> &raquo;</a></strong></p>
<?php endif; ?>

<ul>
<?php
if ( $comments ) {
foreach ($comments as $comment) {
	echo '<li>' . sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">' . apply_filters('the_title', get_the_title($comment->comment_post_ID)) . '</a>');
	edit_comment_link(__("Edit"), ' <small>(', ')</small>');
	echo '</li>';
}
}
?>
</ul>
</div>
<?php endif; ?>

<?php
if ( $recentposts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND " . get_private_posts_cap_sql('post') . " AND post_date_gmt < '$today' ORDER BY post_date DESC LIMIT 5") ) :
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
if ( $scheduled = $wpdb->get_results("SELECT ID, post_title, post_date_gmt FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'future' ORDER BY post_date ASC") ) :
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

<div>
<h3><?php _e('Blog Stats'); ?></h3>
<?php
$numposts = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'");
$numcomms = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");
$numcats  = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->categories");

$post_str = sprintf(__ngettext('%1$s <a href="%2$s" title="Posts">post</a>', '%1$s <a href="%2$s" title="Posts">posts</a>', $numposts), number_format($numposts), 'edit.php');
$comm_str = sprintf(__ngettext('%1$s <a href="%2$s" title="Comments">comment</a>', '%1$s <a href="%2$s" title="Comments">comments</a>', $numcomms), number_format($numcomms), 'edit-comments.php');
$cat_str  = sprintf(__ngettext('%1$s <a href="%2$s" title="Categories">category</a>', '%1$s <a href="%2$s" title="Categories">categories</a>', $numcats), number_format($numcats), 'categories.php');
?>

<p><?php printf(__('There are currently %1$s and %2$s, contained within %3$s.'), $post_str, $comm_str, $cat_str); ?></p>
</div>

<?php do_action('activity_box_end'); ?>
</div>

<p><?php _e('Use these links to get started:'); ?></p>

<ul>
<?php if ( current_user_can('edit_posts') ) : ?>
	<li><a href="post-new.php"><?php _e('Write a post'); ?></a></li>
<?php endif; ?>
	<li><a href="profile.php"><?php _e('Update your profile or change your password'); ?></a></li>
<?php if ( current_user_can('manage_links') ) : ?>
	<li><a href="link-add.php"><?php _e('Add a link to your blogroll'); ?></a></li>
<?php endif; ?>
<?php if ( current_user_can('switch_themes') ) : ?>
	<li><a href="themes.php"><?php _e('Change your site&#8217;s look or theme'); ?></a></li>
<?php endif; ?>
</ul>
<p><?php _e("Need help with WordPress? Please see our <a href='http://codex.wordpress.org/'>documentation</a> or visit the <a href='http://wordpress.org/support/'>support forums</a>."); ?></p>

<div id="devnews"></div>

<div id="planetnews"></div>

<div style="clear: both">&nbsp;
<br clear="all" />
</div>
</div>

<?php
require('./admin-footer.php');
?>

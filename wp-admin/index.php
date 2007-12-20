<?php
require_once('admin.php');

function index_js() {
?>
<script type="text/javascript">
	jQuery(function() {
		jQuery('#incominglinks').load('index-extra.php?jax=incominglinks');
		jQuery('#devnews').load('index-extra.php?jax=devnews');
//		jQuery('#planetnews').load('index-extra.php?jax=planetnews');
	});
</script>
<?php
}
add_action( 'admin_head', 'index_js' );

wp_enqueue_script( 'jquery' );

$title = __('Dashboard');
$parent_file = 'index.php';
require_once('admin-header.php');

$today = current_time('mysql', 1);
?>

<div class="wrap">

<h2><?php _e('Dashboard'); ?></h2>

<div id="rightnow">
<h3><?php _e('Right Now'); ?> <a href="post-new.php"><?php _e('Write a New Page'); ?></a> <a href="page-new.php"><?php _e('Write a New Post'); ?></a></h3>

<?php
// I'm not sure how to internationalize this, Nikolay?

$num_posts = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'" );

$num_pages = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish'" );

$num_drafts = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'draft'" );

$num_future = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'future'" );

$num_comments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");

$num_cats  = wp_count_terms('category');

$num_tags = wp_count_terms('post_tag');

$sentence = 'You have ';
if ( $num_posts )
	$sentence .= '<a href="edit.php">' . number_format( $num_posts ) . ' posts</a>, ';

if ( $num_pages )
	$sentence .= '<a href="edit-pages.php">' . number_format( $num_pages ) . ' pages</a>, ';

if ( $num_drafts )
	$sentence .= '<a href="edit.php?post_status=draft">' . number_format( $num_drafts ) . ' drafts</a>, ';

if ( $num_future )
	$sentence .= '<a href="edit.php?post_status=future">' . number_format( $num_future ) . ' scheduled posts</a>, ';

// There is always a category
$sentence .= 'contained within <a href="categories.php">' . number_format( $num_cats ) . ' categories</a> and ' . number_format( $num_tags ) . ' tags.';

?>
<p><?php echo $sentence; ?></p>
<?php
$ct = current_theme_info();
$sidebars_widgets = wp_get_sidebars_widgets();
$num_widgets = count( $sidebar_widgets );
?>
<p>You use the <?php echo $ct->title; ?> theme with <a href='widgets.php'><?php echo $num_widgets; ?> widgets</a>. <a href="themes.php">Change Theme</a>. You're using BetaPress TODO.</p>
<?php do_action( 'rightnow_end' ); ?>
<?php do_action( 'activity_box_end' ); ?>
</div>

<div id="dashboard-widgets">

<div class="dashboard-widget">
<div class="dashboard-widget-edit"><a href="">See All</a> | <a href="">Edit</a></div>
<h3>Recent Comments</h3>

<?php
$comments = $wpdb->get_results("SELECT comment_author, comment_author_url, comment_ID, comment_post_ID FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 5");
$numcomments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'");

if ( $comments || $numcomments ) :
?>

<?php if ( $numcomments ) : ?>
<p><strong><a href="moderation.php"><?php echo sprintf(__('Comments in moderation (%s) &raquo;'), number_format_i18n($numcomments) ); ?></a></strong></p>
<?php endif; ?>

<ul>
<?php
if ( $comments ) {
foreach ($comments as $comment) {
	echo '<li>' . sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">' . get_the_title($comment->comment_post_ID) . '</a>');
	edit_comment_link(__("Edit"), ' <small>(', ')</small>');
	echo '</li>';
}
}
?>
</ul>
<?php endif; ?>
</div>


<div class="dashboard-widget">
<?php
$more_link = apply_filters( 'dashboard_incoming_links_link', 'http://blogsearch.google.com/blogsearch?hl=en&scoring=d&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) );
?>
<div class="dashboard-widget-edit"><a href="<?php echo htmlspecialchars( $more_link ); ?>"><?php _e('See All'); ?></a> | <a href="">Edit</a></div>
<h3>Incoming Links</h3>

<div id="incominglinks"></div>
</div>

<div class="dashboard-widget">
<?php
$recentposts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND " . get_private_posts_cap_sql('post') . " AND post_date_gmt < '$today' ORDER BY post_date DESC LIMIT 5");
?>
<div class="dashboard-widget-edit"><a href="<?php echo htmlspecialchars( $more_link ); ?>"><?php _e('See All'); ?></a> | <a href="">Edit</a></div>
<h3>Recent Posts</h3>

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

<div class="dashboard-widget">
<div class="dashboard-widget-edit"><a href="<?php echo htmlspecialchars( $more_link ); ?>"><?php _e('See All'); ?></a> | <a href="">Edit</a> | <a href="">RSS</a></div>
<h3><?php echo apply_filters( 'dashboard_primary_title', __('Blog') ); ?></h3>

<div id="devnews"></div>
</div>

<?php do_action( 'dashboard_widgets' ); ?>

<p><a href="">Customize this page</a>.</p>

</div>


<div id="planetnews"></div>

<div style="clear: both">&nbsp;
<br clear="all" />
</div>
</div>

<?php
require('./admin-footer.php');
?>

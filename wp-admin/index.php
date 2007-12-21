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
<h3><?php _e('Right Now'); ?> <a href="post-new.php"><?php _e('Write a New Post'); ?></a> <a href="page-new.php"><?php _e('Write a New Page'); ?></a></h3>

<?php
$num_posts = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'" );

$num_pages = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish'" );

$num_drafts = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'draft'" );

$num_future = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'future'" );

$num_comments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '1'");

$num_cats  = wp_count_terms('category');

$num_tags = wp_count_terms('post_tag');

$post_type_texts = array();

if ( $num_posts ) {
	$post_type_texts[] = '<a href="edit.php">'.sprintf( __ngettext( '%d post', '%d posts', $num_posts ), number_format_i18n( $num_posts ) ).'</a>';
}
if ( $num_pages ) {
	$post_type_texts[] = '<a href="edit-pages.php">'.sprintf( __ngettext( '%d page', '%d pages', $num_pages ), number_format_i18n( $num_pages ) ).'</a>';
}
if ( $num_drafts ) {
	$post_type_texts[] = '<a href="edit.php?post_status=draft">'.sprintf( __ngettext( '%d draft', '%d drafts', $num_drafts ), number_format_i18n( $num_drafts ) ).'</a>';
}
if ( $num_future ) {
	$post_type_texts[] = '<a href="edit.php?post_status=future">'.sprintf( __ngettext( '%d scheduled post', '%d scheduled posts', $num_future ), number_format_i18n( $num_future ) ).'</a>';
}

$cats_text = '<a href="categories.php">'.sprintf( __ngettext( '%d category', '%d categories', $num_cats ), number_format_i18n( $num_cats ) ).'</a>';
$tags_text = sprintf( __ngettext( '%d tag', '%d tags', $num_tags ), number_format_i18n( $num_tags ) );

$post_type_text = implode(', ', $post_type_texts);

// There is always a category
$sentence = sprintf( __( 'You have %1$s, contained within %2$s and %3$s.' ), $post_type_text, $cats_text, $tags_text );

?>
<p><?php echo $sentence; ?></p>
<?php
$ct = current_theme_info();
$sidebars_widgets = wp_get_sidebars_widgets();
$num_widgets = array_reduce( $sidebars_widgets, create_function( '$prev, $curr', 'return $prev+count($curr);' ) );
$widgets_text = sprintf( __ngettext( '%d widget', '%d widgets', $num_widgets ), $num_widgets );
?>
<p><?php printf( __( 'You are using %1$s theme with %2$s.' ), $ct->title, $widgets_text ); ?> <a href="themes.php"><?php _e('Change Theme'); ?></a>. You're using BetaPress TODO.</p>
<p>
<?php do_action( 'rightnow_end' ); ?>
<?php do_action( 'activity_box_end' ); ?>
</div>

<div id="dashboard-widgets">

<div class="dashboard-widget">
<div class="dashboard-widget-edit"><a href=""><?php _e('See All'); ?></a> | <a href=""><?php _e('Edit'); ?></a></div>
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
<div class="dashboard-widget-edit"><a href="<?php echo htmlspecialchars( $more_link ); ?>"><?php _e('See All'); ?></a> | <a href=""><?php _e('Edit'); ?></a></div>
<h3><?php _e('Incoming Links'); ?></h3>

<div id="incominglinks"></div>
</div>

<div class="dashboard-widget">
<?php
$recentposts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND " . get_private_posts_cap_sql('post') . " AND post_date_gmt < '$today' ORDER BY post_date DESC LIMIT 5");
?>
<div class="dashboard-widget-edit"><a href="<?php echo htmlspecialchars( $more_link ); ?>"><?php _e('See All'); ?></a> | <a href=""><?php _e('Edit'); ?></a></div>
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
<div class="dashboard-widget-edit"><a href="<?php echo htmlspecialchars( $more_link ); ?>"><?php _e('See All'); ?></a> | <a href=""><?php _e('Edit'); ?></a> | <a href=""><?php _e('RSS'); ?></a></div>
<h3><?php echo apply_filters( 'dashboard_primary_title', __('Blog') ); ?></h3>

<div id="devnews"></div>
</div>

<?php do_action( 'dashboard_widgets' ); ?>

<p><a href=""><?php _e('Customize this page'); ?></a>.</p>

</div>


<div id="planetnews"></div>

<div style="clear: both">&nbsp;
<br clear="all" />
</div>
</div>

<?php
require('./admin-footer.php');
?>

<?php

require_once('admin.php');

require_once(ABSPATH . 'wp-admin/includes/dashboard.php');

wp_dashboard_setup();

function index_js() {
?>
<script type="text/javascript">
jQuery(function($) {
	var ajaxWidgets = {
		dashboard_incoming_links: 'incominglinks',
		dashboard_primary: 'devnews',
		dashboard_secondary: 'planetnews',
		dashboard_plugins: 'plugins'
	};
	$.each( ajaxWidgets, function(i,a) {
		var e = jQuery('#' + i + ' div.dashboard-widget-content').not('.dashboard-widget-control').find('.widget-loading');
		if ( e.size() ) { e.parent().load('index-extra.php?jax=' + a); }
	} );
});
</script>
<?php
}
add_action( 'admin_head', 'index_js' );

function index_css() {
	wp_admin_css( 'css/dashboard' );
}
add_action( 'admin_head', 'index_css' );

wp_enqueue_script( 'jquery' );

$title = __('Dashboard');
$parent_file = 'index.php';
require_once('admin-header.php');

$today = current_time('mysql', 1);
?>

<div class="wrap">

<h2><?php _e('Dashboard'); ?></h2>

<div id="rightnow">
<h3 class="reallynow">
	<span><?php _e('Right Now'); ?></span>

<?php if ( $can_edit_posts = current_user_can( 'edit_posts' ) ) : ?>
	<a href="post-new.php" class="rbutton"><strong><?php _e('Write a New Post'); ?></strong></a>
<?php endif; if ( $can_edit_pages = current_user_can( 'edit_pages' ) ) : ?>
	<a href="page-new.php" class="rbutton"><?php _e('Write a New Page'); ?></a>
<?php endif; ?>
	<br class="clear" />
</h3>

<?php
$num_posts = wp_count_posts( 'post' );
$num_pages = wp_count_posts( 'page' );

$num_cats  = wp_count_terms('category');

$num_tags = wp_count_terms('post_tag');

$post_type_texts = array();

if ( !empty($num_posts->publish) ) { // with feeds, anyone can tell how many posts there are.  Just unlink if !current_user_can
	$post_text = sprintf( __ngettext( '%s post', '%s posts', $num_posts->publish ), number_format_i18n( $num_posts->publish ) );
	$post_type_texts[] = $can_edit_posts ? "<a href='edit.php'>$post_text</a>" : $post_text;
}
if ( $can_edit_pages && !empty($num_pages->publish) ) { // how many pages is not exposed in feeds.  Don't show if !current_user_can
	$post_type_texts[] = '<a href="edit-pages.php">'.sprintf( __ngettext( '%s page', '%s pages', $num_pages->publish ), number_format_i18n( $num_pages->publish ) ).'</a>';
}
if ( $can_edit_posts && !empty($num_posts->draft) ) {
	$post_type_texts[] = '<a href="edit.php?post_status=draft">'.sprintf( __ngettext( '%s draft', '%s drafts', $num_posts->draft ), number_format_i18n( $num_posts->draft ) ).'</a>';
}
if ( $can_edit_posts && !empty($num_posts->future) ) {
	$post_type_texts[] = '<a href="edit.php?post_status=future">'.sprintf( __ngettext( '%s scheduled post', '%s scheduled posts', $num_posts->future ), number_format_i18n( $num_posts->future ) ).'</a>';
}

if ( current_user_can('publish_posts') && !empty($num_posts->pending) ) {
	$pending_text = sprintf( __ngettext( 'There is <a href="%1$s">%2$s post</a> pending your review.', 'There are <a href="%1$s">%2$s posts</a> pending your review.', $num_posts->pending ), 'edit.php?post_status=pending', number_format_i18n( $num_posts->pending ) );
} else {
	$pending_text = '';
}

$cats_text = sprintf( __ngettext( '%s category', '%s categories', $num_cats ), number_format_i18n( $num_cats ) );
$tags_text = sprintf( __ngettext( '%s tag', '%s tags', $num_tags ), number_format_i18n( $num_tags ) );
if ( current_user_can( 'manage_categories' ) ) {
	$cats_text = "<a href='categories.php'>$cats_text</a>";
	$tags_text = "<a href='edit-tags.php'>$tags_text</a>";
}

$post_type_text = implode(', ', $post_type_texts);

// There is always a category
$sentence = sprintf( __( 'You have %1$s, contained within %2$s and %3$s. %4$s' ), $post_type_text, $cats_text, $tags_text, $pending_text );
$sentence = apply_filters( 'dashboard_count_sentence', $sentence, $post_type_text, $cats_text, $tags_text, $pending_text );

?>
<p class="youhave"><?php echo $sentence; ?></p>
<?php
$ct = current_theme_info();
$sidebars_widgets = wp_get_sidebars_widgets();
$num_widgets = array_reduce( $sidebars_widgets, create_function( '$prev, $curr', 'return $prev+count($curr);' ) );
$widgets_text = sprintf( __ngettext( '%d widget', '%d widgets', $num_widgets ), $num_widgets );
if ( $can_switch_themes = current_user_can( 'switch_themes' ) )
	$widgets_text = "<a href='widgets.php'>$widgets_text</a>";
?>
<p class="youare">
	<?php printf( __( 'You are using %1$s theme with %2$s.' ), $ct->title, $widgets_text ); ?>
	<?php if ( $can_switch_themes ) : ?>
		<a href="themes.php" class="rbutton"><?php _e('Change Theme'); ?></a>
	<?php endif; ?>
	<?php update_right_now_message(); ?>
</p>

<?php do_action( 'rightnow_end' ); ?>
<?php do_action( 'activity_box_end' ); ?>
</div><!-- rightnow -->

<br class="clear" />

<div id="dashboard-widgets-wrap">

<?php wp_dashboard(); ?>


</div><!-- dashboard-widgets-wrap -->

</div><!-- wrap -->

<?php require('./admin-footer.php'); ?>

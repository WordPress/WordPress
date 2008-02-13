<?php

require_once('admin.php');

require( './includes/dashboard.php' );

wp_dashboard_setup();

function index_js() {
?>
<script type="text/javascript">
	jQuery(function() {
		jQuery('#dashboard_incoming_links div.dashboard-widget-content').not( '.dashboard-widget-control' ).load('index-extra.php?jax=incominglinks');
		jQuery('#dashboard_primary div.dashboard-widget-content').not( '.dashboard-widget-control' ).load('index-extra.php?jax=devnews');
		jQuery('#dashboard_secondary div.dashboard-widget-content').not( '.dashboard-widget-control' ).load('index-extra.php?jax=planetnews');
		jQuery('#dashboard_plugins div.dashboard-widget-content').not( '.dashboard-widget-control' ).html( 'TODO' );
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
<h3 class="reallynow"><?php _e('Right Now'); ?> <a href="post-new.php" class="rbutton"><?php _e('Write a New Post'); ?></a> <a href="page-new.php" class="rbutton"><?php _e('Write a New Page'); ?></a></h3>

<?php
$num_posts = wp_count_posts( 'post' );
$num_pages = wp_count_posts( 'page' );

$num_cats  = wp_count_terms('category');

$num_tags = wp_count_terms('post_tag');

$post_type_texts = array();

if ( !empty($num_posts->publish) ) {
	$post_type_texts[] = '<a href="edit.php">'.sprintf( __ngettext( '%s post', '%s posts', $num_posts->publish ), number_format_i18n( $num_posts->publish ) ).'</a>';
}
if ( !empty($num_pages->publish) ) {
	$post_type_texts[] = '<a href="edit-pages.php">'.sprintf( __ngettext( '%s page', '%s pages', $num_pages->publish ), number_format_i18n( $num_pages->publish ) ).'</a>';
}
if ( !empty($num_posts->draft) ) {
	$post_type_texts[] = '<a href="edit.php?post_status=draft">'.sprintf( __ngettext( '%s draft', '%s drafts', $num_posts->draft ), number_format_i18n( $num_posts->draft ) ).'</a>';
}
if ( !empty($num_posts->future) ) {
	$post_type_texts[] = '<a href="edit.php?post_status=future">'.sprintf( __ngettext( '%s scheduled post', '%s scheduled posts', $num_posts->future ), number_format_i18n( $num_posts->future ) ).'</a>';
}

$cats_text = '<a href="categories.php">'.sprintf( __ngettext( '%s category', '%s categories', $num_cats ), number_format_i18n( $num_cats ) ).'</a>';
$tags_text = sprintf( __ngettext( '%s tag', '%s tags', $num_tags ), number_format_i18n( $num_tags ) );

$post_type_text = implode(', ', $post_type_texts);

// There is always a category
$sentence = sprintf( __( 'You have %1$s, contained within %2$s and %3$s.' ), $post_type_text, $cats_text, $tags_text );

?>
<p class="youhave"><?php echo $sentence; ?></p>
<?php
$ct = current_theme_info();
$sidebars_widgets = wp_get_sidebars_widgets();
$num_widgets = array_reduce( $sidebars_widgets, create_function( '$prev, $curr', 'return $prev+count($curr);' ) );
$widgets_text = sprintf( __ngettext( '%d widget', '%d widgets', $num_widgets ), $num_widgets );
?>
<p><?php printf( __( 'You are using %1$s theme with %2$s.' ), $ct->title, "<a href='widgets.php'>$widgets_text</a>" ); ?> <a href="themes.php" class="rbutton"><?php _e('Change Theme'); ?></a> You're using BetaPress TODO.</p>
<?php do_action( 'rightnow_end' ); ?>
<?php do_action( 'activity_box_end' ); ?>
</div><!-- rightnow -->

<?php wp_dashboard(); ?>

</div><!-- wrap -->

<?php require('./admin-footer.php'); ?>

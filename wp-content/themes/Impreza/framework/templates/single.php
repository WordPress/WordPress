<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * The template for displaying all single posts and attachments
 */
$us_layout = US_Layout::instance();
get_header();

$post_type = get_post_type();
if ( $post_type == 'post' ) {
	$template_vars = array(
		'title' => us_get_option( 'titlebar_post_title', 'Blog' ),
	);
	us_load_template( 'templates/titlebar', $template_vars );
} elseif ( in_array( $post_type, us_get_option( 'custom_post_types_support', array() ) ) ) {
	us_load_template( 'templates/titlebar' );
}

$template_vars = array(
	'metas' => (array) us_get_option( 'post_meta', array() ),
	'show_tags' => in_array( 'tags', us_get_option( 'post_meta', array() ) ),
);

?>
<!-- MAIN -->
<div class="l-main">
	<div class="l-main-h i-cf">

		<main class="l-content" itemprop="mainContentOfPage">

			<?php do_action( 'us_before_single' ) ?>

			<?php
			while ( have_posts() ){
				the_post();

				us_load_template( 'templates/blog/single-post', $template_vars );
			}
			?>

			<?php do_action( 'us_after_single' ) ?>

		</main>

		<?php if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ): ?>
			<aside class="l-sidebar at_<?php echo $us_layout->sidebar_pos . ' ' . us_dynamic_sidebar_id(); ?>" itemscope="itemscope" itemtype="https://schema.org/WPSideBar">
				<?php us_dynamic_sidebar(); ?>
			</aside>
		<?php endif; ?>

	</div>
</div>

<?php get_footer(); ?>

<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Theme Meme
 */
?>
	<div class="col-md-4 site-sidebar" role="complementary">
		<?php do_action( 'before_sidebar' ); ?>
		<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>

			<?php
				the_widget( 'WP_Widget_Meta', '', 'before_title=<h3 class="widget-title">&after_title=</h3>' );
			?>

		<?php endif; ?>
	<!-- .site-sidebar --></div>
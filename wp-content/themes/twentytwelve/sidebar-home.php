<?php
/**
 * The sidebar containing the homepage widget areas.
 *
 * If no active widgets in either sidebar, they will be hidden completely.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

/*
 * The homepage widget area is triggered if any of the areas
 * have widgets. So let's check that first.
 *
 * If none of the sidebars have widgets, then let's bail early.
 */
if ( ! is_active_sidebar( 'sidebar-2' ) && ! is_active_sidebar( 'sidebar-3' ) )
	return;

// If we get this far, we have widgets. Let do this.
?>
<div id="secondary" <?php twentytwelve_homepage_sidebar_class(); ?> role="complementary">
	<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
	<div class="first home-widgets">
		<?php dynamic_sidebar( 'sidebar-2' ); ?>
	</div><!-- .first -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>
	<div class="second home-widgets">
		<?php dynamic_sidebar( 'sidebar-3' ); ?>
	</div><!-- .second -->
	<?php endif; ?>
</div><!-- #secondary -->
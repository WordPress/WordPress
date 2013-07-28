<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>
<?php
	if (   ! is_active_sidebar( 'sidebar-3' )
		&& ! is_active_sidebar( 'sidebar-4' )
		&& ! is_active_sidebar( 'sidebar-5' )
		&& ! is_active_sidebar( 'sidebar-6' )
		&& ! is_active_sidebar( 'sidebar-7' )
	)
		return;
?>
<div id="supplementary" <?php twentyfourteen_footer_sidebar_class(); ?>>
	<?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>
	<div id="footer-sidebar-one" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-3' ); ?>
	</div><!-- #first .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-4' ) ) : ?>
	<div id="footer-sidebar-two" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-4' ); ?>
	</div><!-- #second .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-5' ) ) : ?>
	<div id="footer-sidebar-three" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-5' ); ?>
	</div><!-- #third .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-6' ) ) : ?>
	<div id="footer-sidebar-four" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-6' ); ?>
	</div><!-- #fourth .widget-area -->
	<?php endif; ?>
	<?php if ( is_active_sidebar( 'sidebar-7' ) ) : ?>
	<div id="footer-sidebar-five" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-7' ); ?>
	</div><!-- #fourth .widget-area -->
	<?php endif; ?>
</div><!-- #supplementary -->

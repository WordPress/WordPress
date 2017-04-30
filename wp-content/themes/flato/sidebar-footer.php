<?php
/**
 * The widget areas in the footer.
 *
 * @package Theme Meme
 */
?>

<?php
	/* The footer widget area is triggered if any of the areas
	 * have widgets. So let's check that first.
	 *
	 * If none of the sidebars have widgets, then let's bail early.
	 */
	if (   ! is_active_sidebar( 'sidebar-2' )
		&& ! is_active_sidebar( 'sidebar-3' )
		&& ! is_active_sidebar( 'sidebar-4' )
		&& ! is_active_sidebar( 'sidebar-5' )
	)
	return;
?>

<div class="site-extra" role="complementary">
	<div class="clearfix container">
		<div class="row">
			<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
			<div id="widget-area-2" <?php themememe_extra_col_class(); ?>>
				<?php dynamic_sidebar( 'sidebar-2' ); ?>
			<!-- #widget-area-2 --></div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>
			<div id="widget-area-3" <?php themememe_extra_col_class(); ?>>
				<?php dynamic_sidebar( 'sidebar-3' ); ?>
			<!-- #widget-area-3 --></div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'sidebar-4' ) ) : ?>
			<div id="widget-area-4" <?php themememe_extra_col_class(); ?>>
				<?php dynamic_sidebar( 'sidebar-4' ); ?>
			<!-- #widget-area-4 --></div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'sidebar-5' ) ) : ?>
			<div id="widget-area-5" <?php themememe_extra_col_class(); ?>>
				<?php dynamic_sidebar( 'sidebar-5' ); ?>
			<!-- #widget-area-5 --></div>
			<?php endif; ?>
		</div>
	</div>
<!-- .site-extra --></div>
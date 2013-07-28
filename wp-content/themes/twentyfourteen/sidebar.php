<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>
<div id="secondary">
	<div id="secondary-top">
	<?php
		$description = get_bloginfo( 'description' );
		if ( ! empty ( $description ) ) : ?>
		<h2 class="site-description"><?php echo esc_html( $description ); ?></h2>
	<?php endif; ?>

	<?php if ( has_nav_menu( 'secondary' ) ) : ?>
		<nav role="navigation" class="site-navigation secondary-navigation">
			<?php wp_nav_menu( array( 'theme_location' => 'secondary' ) ); ?>
		</nav>
	<?php endif; ?>
	</div>

	<div id="secondary-bottom" class="widget-area" role="complementary">
	<?php do_action( 'before_sidebar' ); ?>

	<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>
		<aside id="categories" class="widget widget_categories">
			<h1 class="widget-title"><?php _e( 'Categories', 'twentyfourteen' ); ?></h1>
			<ul>
				<?php wp_list_categories( array( 'title_li' => '' ) ); ?>
			</ul>
		</aside>

		<aside id="archives" class="widget widget_archive">
			<h1 class="widget-title"><?php _e( 'Archives', 'twentyfourteen' ); ?></h1>
			<ul>
				<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
			</ul>
		</aside>
	<?php endif; // end sidebar widget area ?>
	</div><!-- .widget-area -->
</div><!-- #secondary -->

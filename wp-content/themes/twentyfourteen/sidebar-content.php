<?php
/**
 * The Content Sidebar.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>
<div id="content-sidebar" class="widget-area" role="complementary">
	<?php do_action( 'before_sidebar' ); ?>

	<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) : ?>
		<aside id="search" class="widget widget_search">
				<?php get_search_form(); ?>
		</aside>

		<aside id="link" class="widget widget_links">
			<h1 class="widget-title"><?php _e( 'Blogroll', 'twentyfourteen' ); ?></h1>
			<ul class="xoxo blogroll">
				<?php wp_list_bookmarks( array( 'title_li' => '', 'categorize' => 0 ) ); ?>
			</ul>
		</aside>

		<aside id="meta" class="widget">
			<h1 class="widget-title"><?php _e( 'Meta', 'twentyfourteen' ); ?></h1>
			<ul>
				<?php wp_register(); ?>
				<li><?php wp_loginout(); ?></li>
				<?php wp_meta(); ?>
			</ul>
		</aside>
	<?php endif; // end sidebar widget area ?>
</div><!-- #content-sidebar -->

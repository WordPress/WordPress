<?php
/**
 * The template for displaying all single post types
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 * @package Twenty8teen
 */

get_header(); ?>

	<main <?php twenty8teen_area_classes( 'main', 'site-main' ); ?>>
		<div id="content" <?php twenty8teen_area_classes( 'content', 'content-area' ); ?>>

			<?php
			if ( is_active_sidebar( 'content-widget-area' ) ) {
				dynamic_sidebar( 'content-widget-area' );
			}
			else {
				get_template_part( 'template-parts/content-loop' );
			}
			?>

		</div><!-- #content -->
		<?php get_sidebar(); ?>
	</main>

<?php

get_footer();

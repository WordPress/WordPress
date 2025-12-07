<?php
/**
 * The template for displaying all Page post type content
 *
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-page
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
				while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php

					get_template_part( 'loop-parts/entry', 'page' );
					get_template_part( 'loop-parts/comments', 'page' ); ?>

					</article><!-- #post-<?php the_ID(); ?> -->
				<?php

				endwhile; // End of the loop.
			}
			?>

		</div><!-- #content -->
		<?php get_sidebar(); ?>
	</main>

<?php

get_footer();

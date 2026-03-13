<?php
/**
 * Template Name: Taxonomy Root
 *
 * @package Twenty8teen
 */

get_header(); ?>

	<main <?php twenty8teen_area_classes( 'main', 'site-main' ); ?>>
		<div id="content" <?php twenty8teen_area_classes( 'content', 'content-area' ); ?>>

			<?php
			add_filter( 'the_content', 'twenty8teen_append_taxonomy', 11, 2 );

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

			remove_filter( 'the_content', 'twenty8teen_append_taxonomy', 11 );
			?>

		</div><!-- #content -->
		<?php get_sidebar(); ?>
	</main>

<?php

get_footer();

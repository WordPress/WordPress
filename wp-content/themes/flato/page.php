<?php
/**
 * The template for displaying all pages.
 *
 * @package Theme Meme
 */

get_header(); ?>

	<div class="row">
		<div class="col-md-8 content-area" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();
				?>

			<?php endwhile; ?>

		<!-- .content-area --></div>

		<?php get_sidebar(); ?>
	</div>

<?php get_footer(); ?>
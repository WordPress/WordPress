<?php
/**
 * Template Name: Full Width Page
 *
 * The template for displaying page with no sidebar.
 *
 * @package Theme Meme
 */

get_header(); ?>

	<div class="row">
		<div class="col-xs-12 content-area" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();
				?>

			<?php endwhile; ?>

		<!-- .content-area --></div>
	</div>

<?php get_footer(); ?>
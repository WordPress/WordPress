<?php
/**
 * The main template file.
 *
 * @package Theme Meme
 */

get_header(); ?>

	<div class="row">
		<div class="col-md-8 content-area" role="main">

		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					get_template_part( 'content', get_post_format() );
				?>

			<?php endwhile; ?>

			<?php themememe_content_nav( 'nav-below' ); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		<!-- .content-area --></div>

		<?php get_sidebar(); ?>
	</div>

<?php get_footer(); ?>
<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

get_header(); ?>

<?php if ( is_front_page() ) : ?>

	<div class="front-page-content-wrapper">
		<div class="front-page-content-main">

			<?php if ( twentyfourteen_has_featured_posts() ) : ?>
				<?php get_template_part( 'featured-content' ); ?>
			<?php endif; ?>

			<div class="front-page-content-area clearfix">

				<div id="primary" class="content-area no-sidebar">
					<div id="content" class="site-content" role="main">
					<?php
						if ( have_posts() ) :
							while ( have_posts() ) :
								the_post();
								twentyfourteen_get_template_part();
							endwhile;

							twentyfourteen_content_nav( 'nav-below' );
						else :
							get_template_part( 'no-results', 'index' );
						endif;
					?>
					</div><!-- #content .site-content -->
				</div><!-- #primary .content-area -->

				<?php get_template_part( 'recent-formatted-posts' ); ?>

			</div><!-- .front-page-content-area -->

		</div><!-- .front-page-content-main -->
	</div><!-- .front-page-content-wrapper -->

	<?php get_sidebar(); ?>

<?php else : ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php twentyfourteen_get_template_part(); ?>

			<?php endwhile; ?>

			<?php twentyfourteen_content_nav( 'nav-below' ); ?>

		<?php else : ?>

			<?php get_template_part( 'no-results', 'index' ); ?>

		<?php endif; ?>

		</div><!-- #content .site-content -->
	</div><!-- #primary .content-area -->

	<?php get_sidebar( 'content' ); ?>

	<?php get_sidebar(); ?>

<?php endif; // is_front_page() check ?>

<?php get_footer(); ?>

<?php
/**
 * Template Name: Full Width Page
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
					<div id="content" class="site-content full-width" role="main">
					<?php
						if ( have_posts() ) :
							while ( have_posts() ) :
								the_post();
								get_template_part( 'content', 'page' );
								comments_template( '', true );
							endwhile;

							twentyfourteen_content_nav( 'nav-below' );
						else :
							get_template_part( 'no-results', 'index' );
						endif;
					?>
					</div><!-- #content .site-content -->
				</div><!-- #primary .content-area -->

			</div><!-- .front-page-content-area -->

		</div><!-- .front-page-content-main -->
	</div><!-- .front-page-content-wrapper -->

<?php else : ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content full-width" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php comments_template( '', true ); ?>

			<?php endwhile; ?>

		</div><!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php endif; // is_front_page() check ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>

<?php
/**
 * The template for displaying the home page.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

get_header(); ?>

	<div class="front-page-content-wrapper">
		<div class="front-page-content-main">

			<?php if ( twentyfourteen_has_featured_posts() ) : ?>
				<?php get_template_part( 'featured-content' ); ?>
			<?php endif; ?>

			<div class="front-page-content-area">

				<div id="primary" class="content-area no-sidebar">
					<div id="content" class="site-content" role="main">
					<?php
						if ( have_posts() ) :
							while ( have_posts() ) :
								the_post();

								twentyfourteen_get_template_part();

								comments_template();
							endwhile;
							twentyfourteen_paging_nav();

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

<?php
get_sidebar();
get_footer();

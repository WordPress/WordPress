<?php
/**
 * Template Name: Full Width 
 * Full width template for Lighthouse theme
 *
 * Please browse readme.txt for credits and forking information
 * @package Lighthouse
 */

get_header(); ?>

		<div class="container">
            <div class="row">
				<div id="primary" class="col-md-12 content-area">
					<main id="main" class="site-main" role="main">

						<?php while ( have_posts() ) : the_post(); ?>

							<?php get_template_part( 'template-parts/content', 'page' ); ?>

							<?php
								// If comments are open or we have at least one comment, load up the comment template.
								if ( comments_open() || get_comments_number() ) :
									comments_template();
								endif;
							?>

						<?php endwhile; // End of the loop. ?>

					</main><!-- #main -->
				</div><!-- #primary -->

			</div> <!--.row-->            
        </div><!--.container-->
        <?php get_footer(); ?>

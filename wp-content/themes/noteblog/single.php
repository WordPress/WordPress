<?php
/**
 * The template for displaying all single posts.
 *
 * Please browse readme.txt for credits and forking information
 * @package noteblog
 */

get_header(); ?>

	<div class="container">
        <div class="row">
			<div id="primary" class="col-md-9 content-area">
				<main id="main" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'template-parts/content',get_post_format()); ?>
				</main><!-- #main -->				

				<div>
					<?php
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

						if(!comments_open())

					?>
				</div>			

				<div class="post-navigation">				
					<?php noteblog_post_navigation(); ?>
				</div>

				<?php endwhile; // End of the loop. ?>

				
			</div><!-- #primary -->

			<?php get_sidebar('sidebar-1'); ?>
		</div> <!--.row-->            
    </div><!--.container-->
    <?php get_footer(); ?>

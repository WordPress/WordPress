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
 * Please browse readme.txt for credits and forking information
 * @package noteblog
 */

get_header(); ?>

<div class="container">
	<div class="row">
		<div id="primary" class="col-md-12 content-area">
			<main id="main" class="site-main">


				<div class="article-grid-container">
					<?php if ( have_posts() ) : ?>

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>

							<?php

							$post_display_option = get_theme_mod('post_display_option','post-excerpt');

							if($post_display_option == 'post-excerpt'){
								get_template_part( 'template-parts/content','excerpt');
							}
							else{
								get_template_part( 'template-parts/content','excerpt');
							}

							?>

						<?php endwhile; ?>

						<?php noteblog_posts_navigation(); ?>

					<?php else : ?>

						<?php get_template_part( 'template-parts/content', 'none' ); ?>

					<?php endif; ?>
				</div>
			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!--row-->      

</div><!--.container-->
<?php get_footer(); ?>




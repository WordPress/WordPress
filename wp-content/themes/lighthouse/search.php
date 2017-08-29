<?php
/**
 * The template for displaying search results pages.
 *
 * Please browse readme.txt for credits and forking information
 * @package Lighthouse
 */

get_header(); ?>
		<div class="container">
            <div class="row">
				

					<?php if ( have_posts() ) : ?>

						<header class="search-page-header">
							<h3 class="search-page-title"><?php printf( esc_html__( 'Search Results for: %s', 'lighthouse' ), '<span>' . get_search_query() . '</span>' ); ?></h3>
						</header><!-- .page-header -->

					<section id="primary" class="col-md-9 content-area">
						<main id="main" class="site-main" role="main">

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>

							<?php
							/**
							 * Run the loop for the search to output the results.
							 * If you want to overload this in a child theme then include a file
							 * called content-search.php and that will be used instead.
							 */
							get_template_part( 'template-parts/content', 'search' );
							?>

						<?php endwhile; ?>

						<?php lighthouse_posts_navigation(); ?>

						</main><!-- #main -->
					</section><!-- #primary -->

					<?php else : ?>

					<section id="primary" class="col-md-9 content-area">
						<main id="main" class="site-main" role="main">

						<?php get_template_part( 'template-parts/content', 'none' ); ?>

						</main><!-- #main -->
					</section><!-- #primary -->


					<?php endif; ?>

						


				<?php get_sidebar('sidebar-1'); ?>

			</div> <!--.row-->            
        </div><!--.container-->
        <?php get_footer(); ?>

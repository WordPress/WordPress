<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
<<<<<<< HEAD
 * @since Twenty Nineteen 1.0
=======
 * @since 1.0.0
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

get_header();
?>

<<<<<<< HEAD
	<div id="primary" class="content-area">
=======
	<section id="primary" class="content-area">
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		<main id="main" class="site-main">

			<?php

<<<<<<< HEAD
			// Start the Loop.
=======
			/* Start the Loop */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/content/content', 'page' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}

<<<<<<< HEAD
			endwhile; // End the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
=======
			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</section><!-- #primary -->
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

<?php
get_footer();

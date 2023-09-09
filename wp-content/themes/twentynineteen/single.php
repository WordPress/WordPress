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

				get_template_part( 'template-parts/content/content', 'single' );

				if ( is_singular( 'attachment' ) ) {
					// Parent post navigation.
					the_post_navigation(
						array(
<<<<<<< HEAD
							/* translators: %s: Parent post link. */
=======
							/* translators: %s: parent post link */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
							'prev_text' => sprintf( __( '<span class="meta-nav">Published in</span><span class="post-title">%s</span>', 'twentynineteen' ), '%title' ),
						)
					);
				} elseif ( is_singular( 'post' ) ) {
					// Previous/next post navigation.
					the_post_navigation(
						array(
							'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next Post', 'twentynineteen' ) . '</span> ' .
<<<<<<< HEAD
								/* translators: Hidden accessibility text. */
								'<span class="screen-reader-text">' . __( 'Next post:', 'twentynineteen' ) . '</span> <br/>' .
								'<span class="post-title">%title</span>',
							'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous Post', 'twentynineteen' ) . '</span> ' .
								/* translators: Hidden accessibility text. */
=======
								'<span class="screen-reader-text">' . __( 'Next post:', 'twentynineteen' ) . '</span> <br/>' .
								'<span class="post-title">%title</span>',
							'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous Post', 'twentynineteen' ) . '</span> ' .
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
								'<span class="screen-reader-text">' . __( 'Previous post:', 'twentynineteen' ) . '</span> <br/>' .
								'<span class="post-title">%title</span>',
						)
					);
				}

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

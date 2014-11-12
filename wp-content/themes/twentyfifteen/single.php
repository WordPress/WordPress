<?php
/**
 * The template for displaying all single posts and attachments.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', get_post_format() ); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			?>

			<?php
				the_post_navigation( array(
					'next_text' => _x( '<span class="meta-nav">Next <span class="screen-reader-text">post:</span></span><span class="post-title">%title</span>', 'Next post link', 'twentyfifteen' ),
					'prev_text' => _x( '<span class="meta-nav">Previous <span class="screen-reader-text">post:</span></span><span class="post-title">%title</span>', 'Previous post link', 'twentyfifteen' )
				) );
			?>

		<?php endwhile; // end of the loop. ?>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
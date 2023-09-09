<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
<<<<<<< HEAD
					<?php _e( 'Search results for: ', 'twentynineteen' ); ?>
					<span class="page-description"><?php echo get_search_query(); ?></span>
				</h1>
=======
					<?php _e( 'Search results for:', 'twentynineteen' ); ?>
				</h1>
				<div class="page-description"><?php echo get_search_query(); ?></div>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			</header><!-- .page-header -->

			<?php
			// Start the Loop.
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
<<<<<<< HEAD
				 * called content-___.php (where ___ is the Post Format name) and that
				 * will be used instead.
=======
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				 */
				get_template_part( 'template-parts/content/content', 'excerpt' );

				// End the loop.
			endwhile;

			// Previous/next page navigation.
			twentynineteen_the_posts_navigation();

			// If no content, include the "No posts found" template.
		else :
			get_template_part( 'template-parts/content/content', 'none' );

		endif;
		?>
		</main><!-- #main -->
<<<<<<< HEAD
	</div><!-- #primary -->
=======
	</section><!-- #primary -->
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

<?php
get_footer();

<?php
/**
 * Template Name: Contributor Page
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>

<div id="main-content" class="main-content">

<?php
if ( is_front_page() && twentyfourteen_has_featured_posts() ) {
	// Include the featured content template.
	get_template_part( 'featured-content' );
}
?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php
				// Start the Loop.
			while ( have_posts() ) :
				the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php
				the_title( '<header class="entry-header"><h1 class="entry-title">', '</h1></header><!-- .entry-header -->' );

				// Output the authors list.
				twentyfourteen_list_authors();

				edit_post_link( __( 'Edit', 'twentyfourteen' ), '<footer class="entry-meta"><span class="edit-link">', '</span></footer>' );
			?>
			</article><!-- #post-## -->

			<?php
				// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
				endwhile;
			?>
		</div><!-- #content -->
	</div><!-- #primary -->
</div><!-- #main-content -->

<?php
get_sidebar();
get_footer();

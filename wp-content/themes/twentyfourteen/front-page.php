<?php
/**
 * The template for displaying the home page.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

get_header(); ?>

<div class="front-page-content-wrapper">

	<?php
		if ( twentyfourteen_has_featured_posts() )
			get_template_part( 'featured-content' );
	?>

		<div id="primary" class="content-area no-sidebar">
			<div id="content" class="site-content" role="main">
			<?php
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();

						get_template_part( 'content', get_post_format() );

						comments_template();
					endwhile;
					twentyfourteen_paging_nav();

				else :
					get_template_part( 'content', 'none' );

				endif;
			?>
			</div><!-- #content .site-content -->
		</div><!-- #primary .content-area -->

		<?php get_sidebar( 'ephemera' ); ?>

</div><!-- .front-page-content-wrapper -->

<?php
get_sidebar();
get_footer();

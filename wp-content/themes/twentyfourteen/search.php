<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

get_header(); ?>

<section id="primary" class="content-area">
	<div id="content" class="site-content" role="main">

		<?php if ( have_posts() ) : ?>

		<header class="page-header">
			<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'twentyfourteen' ), get_search_query() ); ?></h1>
		</header><!-- .page-header -->

		<?php
				while ( have_posts() ) :
					the_post();

					twentyfourteen_get_template_part();
				endwhile;
				twentyfourteen_paging_nav();

			else :
				get_template_part( 'no-results', 'search' );

			endif;
		?>

	</div><!-- #content -->
</section><!-- #primary -->

<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();

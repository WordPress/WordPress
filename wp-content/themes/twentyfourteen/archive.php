<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fourteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php
						if ( is_day() ) :
							printf( __( 'Day: %s', 'twentyfourteen' ), get_the_date() );

						elseif ( is_month() ) :
							printf( __( 'Month: %s', 'twentyfourteen' ), get_the_date( 'F Y' ) );

						elseif ( is_year() ) :
							printf( __( 'Year: %s', 'twentyfourteen' ), get_the_date( 'Y' ) );

						else :
							_e( 'Archives', 'twentyfourteen' );

						endif;
					?>
				</h1>
			</header><!-- .page-header -->

			<?php
					while ( have_posts() ) :
						the_post();

						get_template_part( 'content', get_post_format() );
					endwhile;
					twentyfourteen_paging_nav();

				else :
					get_template_part( 'content', 'none' );

				endif;
			?>
		</div><!-- #content -->
	</section><!-- #primary -->

<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();

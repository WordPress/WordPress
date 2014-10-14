<?php
/**
 * The template for displaying archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, Twenty Fifteen
 * already has tag.php for Tag archives, category.php for Category archives,
 * and author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php
						if ( is_category() ) :
							single_cat_title();

						elseif ( is_tag() ) :
							single_tag_title();

						elseif ( is_author() ) :
							printf( esc_html__( 'Author: %s', 'twentyfifteen' ), '<span class="vcard">' . get_the_author() . '</span>' );

						elseif ( is_day() ) :
							printf( esc_html__( 'Day: %s', 'twentyfifteen' ), get_the_date() );

						elseif ( is_month() ) :
							printf( esc_html__( 'Month: %s', 'twentyfifteen' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'twentyfifteen' ) ) );

						elseif ( is_year() ) :
							printf( esc_html__( 'Year: %s', 'twentyfifteen' ), get_the_date( _x( 'Y', 'yearly archives date format', 'twentyfifteen' ) ) );

						elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
							esc_html_e( 'Asides', 'twentyfifteen' );

						elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) :
							esc_html_e( 'Galleries', 'twentyfifteen' );

						elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
							esc_html_e( 'Images', 'twentyfifteen' );

						elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
							esc_html_e( 'Videos', 'twentyfifteen' );

						elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
							esc_html_e( 'Quotes', 'twentyfifteen' );

						elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
							esc_html_e( 'Links', 'twentyfifteen' );

						elseif ( is_tax( 'post_format', 'post-format-status' ) ) :
							esc_html_e( 'Statuses', 'twentyfifteen' );

						elseif ( is_tax( 'post_format', 'post-format-audio' ) ) :
							esc_html_e( 'Audios', 'twentyfifteen' );

						elseif ( is_tax( 'post_format', 'post-format-chat' ) ) :
							esc_html_e( 'Chats', 'twentyfifteen' );

						else :
							esc_html_e( 'Archives', 'twentyfifteen' );

						endif;
					?>
				</h1>
				<?php
					// Show an optional term description.
					$term_description = term_description();
					if ( ! empty( $term_description ) ) :
						printf( '<div class="taxonomy-description">%s</div>', $term_description );
					endif;
				?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'content', get_post_format() );
				?>

			<?php endwhile; ?>

			<?php twentyfifteen_paging_nav(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- .site-main -->
	</section><!-- .content-area -->

<?php get_footer(); ?>
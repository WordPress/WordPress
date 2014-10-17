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

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<nav id="image-navigation" class="navigation image-navigation">
						<div class="nav-links">
							<div class="nav-previous"><?php previous_image_link( false, esc_html__( 'Previous Image', 'twentyfifteen' ) ); ?></div><div class="nav-next"><?php next_image_link( false, esc_html__( 'Next Image', 'twentyfifteen' ) ); ?></div>
						</div><!-- .nav-links -->
					</nav><!-- .image-navigation -->

					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<div class="entry-attachment">
							<?php
								/**
								 * Filter the default Twenty Fifteen image attachment size.
								 *
								 * @since Twenty Fifteen 1.0
								 *
								 * @param string $image_size Image size. Default 'large'.
								 */
								$image_size = apply_filters( 'twentyfifteen_attachment_size', 'large' );
								echo wp_get_attachment_image( get_the_ID(), $image_size );
							?>

							<?php if ( has_excerpt() ) : ?>
								<div class="entry-caption">
									<?php the_excerpt(); ?>
								</div><!-- .entry-caption -->
							<?php endif; ?>
						</div><!-- .entry-attachment -->

						<?php
							the_content();
							wp_link_pages( array(
								'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'twentyfifteen' ) . '</span>',
								'after'       => '</div>',
								'link_before' => '<span>',
								'link_after'  => '</span>',
							) );
						?>
					</div><!-- .entry-content -->

					<footer class="entry-footer">
						<?php twentyfifteen_entry_meta(); ?>
						<?php edit_post_link( esc_html__( 'Edit', 'twentyfifteen' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-footer -->
				</article><!-- #post-## -->

				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				?>

				<?php twentyfifteen_post_nav(); ?>

			<?php endwhile; // end of the loop. ?>

		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
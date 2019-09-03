<?php
/**
 * The template for displaying image attachments
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php
				// Start the Loop.
			while ( have_posts() ) :
				the_post();
				?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'image-attachment' ); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>

				<div class="entry-meta">
					<?php
					/* translators: 1: Date, 2: Date, 3, Parent permalink, 4, Post title, 5: Post title. */
					$published_text = __( '<span class="attachment-meta">Published on <time class="entry-date" datetime="%1$s">%2$s</time> in <a href="%3$s" title="Return to %4$s" rel="gallery">%5$s</a></span>', 'twentythirteen' );
					$post_title     = get_the_title( $post->post_parent );
					if ( empty( $post_title ) || 0 == $post->post_parent ) {
						$published_text = '<span class="attachment-meta"><time class="entry-date" datetime="%1$s">%2$s</time></span>';
					}

						printf(
							$published_text,
							esc_attr( get_the_date( 'c' ) ),
							esc_html( get_the_date() ),
							esc_url( get_permalink( $post->post_parent ) ),
							esc_attr( strip_tags( $post_title ) ),
							$post_title
						);

						$metadata = wp_get_attachment_metadata();
						printf(
							'<span class="attachment-meta full-size-link"><a href="%1$s" title="%2$s">%3$s (%4$s &times; %5$s)</a></span>',
							esc_url( wp_get_attachment_url() ),
							esc_attr__( 'Link to full-size image', 'twentythirteen' ),
							__( 'Full resolution', 'twentythirteen' ),
							$metadata['width'],
							$metadata['height']
						);

						edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' );
					?>
					</div><!-- .entry-meta -->
				</header><!-- .entry-header -->

				<div class="entry-content">
					<nav id="image-navigation" class="navigation image-navigation" role="navigation">
					<span class="nav-previous"><?php previous_image_link( false, __( '<span class="meta-nav">&larr;</span> Previous', 'twentythirteen' ) ); ?></span>
						<span class="nav-next"><?php next_image_link( false, __( 'Next <span class="meta-nav">&rarr;</span>', 'twentythirteen' ) ); ?></span>
					</nav><!-- #image-navigation -->

					<div class="entry-attachment">
						<div class="attachment">
						<?php twentythirteen_the_attached_image(); ?>

						<?php if ( has_excerpt() ) : ?>
							<div class="entry-caption">
								<?php the_excerpt(); ?>
							</div>
							<?php endif; ?>
						</div><!-- .attachment -->
					</div><!-- .entry-attachment -->

					<?php if ( ! empty( $post->post_content ) ) : ?>
					<div class="entry-description">
						<?php the_content(); ?>
						<?php
						wp_link_pages(
							array(
								'before' => '<div class="page-links">' . __( 'Pages:', 'twentythirteen' ),
								'after'  => '</div>',
							)
						);
						?>
					</div><!-- .entry-description -->
					<?php endif; ?>

				</div><!-- .entry-content -->
			</article><!-- #post -->

				<?php comments_template(); ?>

			<?php endwhile; // End the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>

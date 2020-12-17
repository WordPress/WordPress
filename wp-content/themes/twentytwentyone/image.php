<?php
/**
 * The template for displaying image attachments
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header();

// Start the loop.
while ( have_posts() ) {
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header alignwide">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<figure class="wp-block-image">
				<?php
				/**
				 * Filter the default image attachment size.
				 *
				 * @param string $image_size Image size. Default 'large'.
				 */
				$image_size = apply_filters( 'twenty_twenty_one_attachment_size', 'full' );
				echo wp_get_attachment_image( get_the_ID(), $image_size );
				?>

				<?php if ( wp_get_attachment_caption() ) : ?>
					<figcaption class="wp-caption-text"><?php echo wp_kses_post( wp_get_attachment_caption() ); ?></figcaption>
				<?php endif; ?>
			</figure><!-- .wp-block-image -->

			<?php
			the_content();

			wp_link_pages(
				array(
					'before'   => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'twentytwentyone' ) . '">',
					'after'    => '</nav>',
					/* translators: %: page number. */
					'pagelink' => esc_html__( 'Page %', 'twentytwentyone' ),
				)
			);
			?>
		</div><!-- .entry-content -->

		<footer class="entry-footer default-max-width">
			<?php
			// Check if there is a parent, then add the published in link.
			if ( wp_get_post_parent_id( $post ) ) {
				echo '<span class="posted-on">';
				printf(
					/* translators: %s: parent post. */
					esc_html__( 'Published in %s', 'twentytwentyone' ),
					'<a href="' . esc_url( get_the_permalink( wp_get_post_parent_id( $post ) ) ) . '">' . esc_html( get_the_title( wp_get_post_parent_id( $post ) ) ) . '</a>'
				);
				echo '</span>';
			} else {
				// Edit post link.
				edit_post_link(
					sprintf(
						/* translators: %s: Name of current post. Only visible to screen readers. */
						esc_html__( 'Edit %s', 'twentytwentyone' ),
						'<span class="screen-reader-text">' . get_the_title() . '</span>'
					),
					'<span class="edit-link">',
					'</span>'
				);
			}

			// Retrieve attachment metadata.
			$metadata = wp_get_attachment_metadata();
			if ( $metadata ) {
				printf(
					'<span class="full-size-link"><span class="screen-reader-text">%1$s</span><a href="%2$s">%3$s &times; %4$s</a></span>',
					esc_html_x( 'Full size', 'Used before full size attachment link.', 'twentytwentyone' ), // phpcs:ignore WordPress.Security.EscapeOutput
					esc_url( wp_get_attachment_url() ),
					absint( $metadata['width'] ),
					absint( $metadata['height'] )
				);
			}

			if ( wp_get_post_parent_id( $post ) ) {
				// Edit post link.
				edit_post_link(
					sprintf(
						/* translators: %s: Name of current post. Only visible to screen readers. */
						esc_html__( 'Edit %s', 'twentytwentyone' ),
						'<span class="screen-reader-text">' . get_the_title() . '</span>'
					),
					'<span class="edit-link">',
					'</span><br>'
				);
			}
			?>
		</footer><!-- .entry-footer -->
	</article><!-- #post-<?php the_ID(); ?> -->
	<?php
	// If comments are open or there is at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
} // End the loop.

get_footer();

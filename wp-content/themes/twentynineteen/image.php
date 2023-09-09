<?php
/**
 * The template for displaying image attachments
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
		<main id="main" class="site-main">

			<?php
			// Start the Loop.
			while ( have_posts() ) :
				the_post();
				?>
=======
	<section id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php
				// Start the loop.
				while ( have_posts() ) :
					the_post();
			?>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<header class="entry-header">
<<<<<<< HEAD
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
=======
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
					</header><!-- .entry-header -->

					<div class="entry-content">

						<figure class="entry-attachment wp-block-image">
<<<<<<< HEAD
						<?php
							/**
							 * Filters the default twentynineteen image attachment size.
							 *
							 * @since Twenty Sixteen 1.0
							 *
							 * @param string $image_size Image size. Default 'large'.
							 */
							$image_size = apply_filters( 'twentynineteen_attachment_size', 'full' );

							echo wp_get_attachment_image( get_the_ID(), $image_size );
						?>
=======
							<?php
								/**
								 * Filter the default twentynineteen image attachment size.
								 *
								 * @since Twenty Sixteen 1.0
								 *
								 * @param string $image_size Image size. Default 'large'.
								 */
								$image_size = apply_filters( 'twentynineteen_attachment_size', 'full' );

								echo wp_get_attachment_image( get_the_ID(), $image_size );
							?>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

							<figcaption class="wp-caption-text"><?php the_excerpt(); ?></figcaption>

						</figure><!-- .entry-attachment -->

						<?php
<<<<<<< HEAD
						the_content();
						wp_link_pages(
							array(
								'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentynineteen' ) . '</span>',
								'after'       => '</div>',
								'link_before' => '<span>',
								'link_after'  => '</span>',
								/* translators: Hidden accessibility text. */
								'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'twentynineteen' ) . ' </span>%',
								'separator'   => '<span class="screen-reader-text">, </span>',
							)
						);
=======
							the_content();
							wp_link_pages(
								array(
									'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentynineteen' ) . '</span>',
									'after'       => '</div>',
									'link_before' => '<span>',
									'link_after'  => '</span>',
									'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'twentynineteen' ) . ' </span>%',
									'separator'   => '<span class="screen-reader-text">, </span>',
								)
							);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
						?>
					</div><!-- .entry-content -->

					<footer class="entry-footer">
<<<<<<< HEAD
					<?php
						// Retrieve attachment metadata.
						$metadata = wp_get_attachment_metadata();
					if ( $metadata ) {
						printf(
							'<span class="full-size-link"><span class="screen-reader-text">%1$s</span><a href="%2$s">%3$s &times; %4$s</a></span>',
							/* translators: Hidden accessibility text. */
							_x( 'Full size', 'Used before full size attachment link.', 'twentynineteen' ),
							esc_url( wp_get_attachment_url() ),
							absint( $metadata['width'] ),
							absint( $metadata['height'] )
						);
					}
					?>
=======
						<?php
							// Retrieve attachment metadata.
							$metadata = wp_get_attachment_metadata();
							if ( $metadata ) {
								printf(
									'<span class="full-size-link"><span class="screen-reader-text">%1$s</span><a href="%2$s">%3$s &times; %4$s</a></span>',
									_x( 'Full size', 'Used before full size attachment link.', 'twentynineteen' ),
									esc_url( wp_get_attachment_url() ),
									absint( $metadata['width'] ),
									absint( $metadata['height'] )
								);
							}
						?>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

						<?php twentynineteen_entry_footer(); ?>

					</footer><!-- .entry-footer -->
<<<<<<< HEAD
				</article><!-- #post-<?php the_ID(); ?> -->

				<?php
				// Parent post navigation.
				the_post_navigation(
					array(
						'prev_text' => _x( '<span class="meta-nav">Published in</span><br><span class="post-title">%title</span>', 'Parent post link', 'twentynineteen' ),
					)
				);

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
=======
				</article><!-- #post-## -->

				<?php
					// Parent post navigation.
					the_post_navigation(
						array(
							'prev_text' => _x( '<span class="meta-nav">Published in</span><br><span class="post-title">%title</span>', 'Parent post link', 'twentynineteen' ),
						)
					);

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

				// End the loop.
				endwhile;
			?>

		</main><!-- .site-main -->
<<<<<<< HEAD
	</div><!-- .content-area -->
=======
	</section><!-- .content-area -->
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

<?php
get_footer();

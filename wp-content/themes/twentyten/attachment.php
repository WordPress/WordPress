<?php
/**
 * The template used to display attachments.
 *
 * @package WordPress
 * @subpackage Twenty Ten
 * @since 3.0.0
 */
?>

<?php get_header(); ?>

		<div id="container">
			<div id="content">

<?php the_post(); ?>

				<p class="page-title"><a href="<?php echo get_permalink( $post->post_parent ); ?>" title="<?php printf( esc_attr__( 'Return to %s', 'twentyten' ), esc_html( get_the_title( $post->post_parent ), 1 ) ); ?>" rel="gallery">&larr; <?php echo get_the_title( $post->post_parent ); ?></a></p>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><?php the_title(); ?></h2>

					<div class="entry-meta">
						<?php
							printf(__('<span class="%1$s">By</span> %2$s', 'twentyten'),
								'meta-prep meta-prep-author',
								sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
									get_author_posts_url( get_the_author_meta( 'ID' ) ),
									sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
									get_the_author()
								)
							);
						?>
						<span class="meta-sep">|</span>
						<?php
							printf( __('<span class="%1$s">Published</span> %2$s', 'twentyten'),
								'meta-prep meta-prep-entry-date',
								sprintf( '<span class="entry-date"><abbr class="published" title="%1$s">%2$s</abbr></span>',
									esc_attr( get_the_time() ),
									get_the_date()
								)
							);
							if ( wp_attachment_is_image() ) {
								echo ' <span class="meta-sep">|</span> ';
								$size = getimagesize( wp_get_attachment_url() );
								printf( __( 'Full size is %s pixels', 'twentyten'),
									sprintf( '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
										wp_get_attachment_url(),
										esc_attr( __('Link to full-size image', 'twentyten') ),
										$size[0],
										$size[1]
									)
								);
							}							
						?>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-meta -->

					<div class="entry-content">
						<div class="entry-attachment">
<?php if ( wp_attachment_is_image() ) : ?>
						<p class="attachment"><a href="<?php echo twentyten_get_next_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php
							$attachment_size = apply_filters( 'twentyten_attachment_size', 900 );
							echo wp_get_attachment_image( $post->ID, array( $attachment_size, 9999 ) ); // filterable image width with, essentially, no limit for image height.
						?></a></p>

						<div id="nav-below" class="navigation">
							<div class="nav-previous"><?php previous_image_link( false ); ?></div>
							<div class="nav-next"><?php next_image_link( false ); ?></div>
						</div><!-- #nav-below -->
<?php else : ?>
						<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a>
<?php endif; ?>
						</div>
						<div class="entry-caption"><?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?></div>

<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>

					</div><!-- .entry-content -->

					<div class="entry-utility">
						<?php echo twentyten_posted_in(); ?>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), ' <span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-utility -->
				</div><!-- #post-<?php the_ID(); ?> -->

<?php comments_template(); ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_footer(); ?>

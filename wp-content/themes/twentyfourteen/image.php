<?php
/**
 * The template for displaying image attachments.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

$metadata = wp_get_attachment_metadata();

get_header();
?>

<section id="primary" class="content-area image-attachment">
	<div id="content" class="site-content" role="main">

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

				<div class="entry-meta">

					<span class="entry-date"><time class="entry-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>

					<span class="full-size-link"><a href="<?php echo wp_get_attachment_url(); ?>" title="Link to full-size image"><?php echo $metadata['width']; ?> &times; <?php echo $metadata['height']; ?></a></span>

					<span class="parent-post-link"><a href="<?php echo get_permalink( $post->post_parent ); ?>" title="Return to <?php echo esc_attr( get_the_title( $post->post_parent ) ); ?>" rel="gallery"><?php echo get_the_title( $post->post_parent ); ?></a></span>
					<?php edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .entry-meta -->
			</header><!-- .entry-header -->

			<div class="entry-content">
				<div class="entry-attachment">
					<div class="attachment">
						<?php twentyfourteen_the_attached_image(); ?>
					</div><!-- .attachment -->

					<?php if ( has_excerpt() ) : ?>
					<div class="entry-caption">
						<?php the_excerpt(); ?>
					</div><!-- .entry-caption -->
					<?php endif; ?>
				</div><!-- .entry-attachment -->

				<?php
					the_content();
					wp_link_pages( array(
						'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
					) );
				?>
			</div><!-- .entry-content -->

			<footer class="entry-meta">
				<?php
					if ( comments_open() && pings_open() ) : // Comments and trackbacks open
						printf( __( '<a class="comment-link" href="#respond" title="Post a comment">Post a comment</a> or leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'twentyfourteen' ), get_trackback_url() );
					elseif ( ! comments_open() && pings_open() ) : // Only trackbacks open
						printf( __( 'Comments are closed, but you can leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'twentyfourteen' ), get_trackback_url() );
					elseif ( comments_open() && ! pings_open() ) : // Only comments open
						_e( 'Trackbacks are closed, but you can <a class="comment-link" href="#respond" title="Post a comment">post a comment</a>.', 'twentyfourteen' );
					elseif ( ! comments_open() && ! pings_open() ) : // Comments and trackbacks closed
						_e( 'Both comments and trackbacks are currently closed.', 'twentyfourteen' );
					endif;
				?>
			</footer><!-- .entry-meta -->
		</article><!-- #post-## -->

		<nav id="image-navigation" class="navigation image-navigation">
			<div class="nav-links">
			<?php previous_image_link( false, __( '<div class="previous-image">Previous Image</div>', 'twentyfourteen' ) ); ?>
			<?php next_image_link( false, __( '<div class="next-image">Next Image</div>', 'twentyfourteen' ) ); ?>
			</div><!-- .nav-links -->
		</nav><!-- #image-navigation -->

		<?php comments_template(); ?>

	<?php endwhile; // end of the loop. ?>

	</div><!-- #content -->
</section><!-- #primary -->

<?php
get_sidebar();
get_footer();

<?php
/**
 * The template for displaying posts in the Status post format
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<?php
			the_content(
				sprintf(
					/* translators: %s: Post title. Only visible to screen readers. */
					__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'twentythirteen' ),
					the_title( '<span class="screen-reader-text">', '</span>', false )
				)
			);

			wp_link_pages(
				array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentythirteen' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
				)
			);
			?>
	</div><!-- .entry-content -->

	<footer class="entry-meta">
		<?php twentythirteen_entry_meta(); ?>
		<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>

		<?php if ( is_single() && get_the_author_meta( 'description' ) && is_multi_author() ) : ?>
			<?php get_template_part( 'author-bio' ); ?>
		<?php endif; ?>
	</footer><!-- .entry-meta -->
</article><!-- #post -->

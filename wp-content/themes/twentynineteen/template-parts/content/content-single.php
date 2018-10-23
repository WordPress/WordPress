<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php if ( ! twentynineteen_can_show_post_thumbnail() ) : ?>
	<header class="entry-header">
		<?php if ( ! is_page() ) : ?>
		<?php $discussion = twentynineteen_can_show_post_thumbnail() ? twentynineteen_get_discussion_data() : null; ?>
		<?php endif; ?>
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		<?php if ( ! is_page() ) : ?>
		<div class="<?php echo ( ! empty( $discussion ) && count( $discussion->authors ) > 0 ) ? 'entry-meta has-discussion' : 'entry-meta'; ?>">
			<?php twentynineteen_posted_by(); ?>
			<?php twentynineteen_posted_on(); ?>
			<span class="comment-count">
				<?php
				if ( ! empty( $discussion ) ) {
				twentynineteen_discussion_avatars_list( $discussion->authors );}
				?>
				<?php twentynineteen_comment_count(); ?>
			</span>
			<?php
			// Edit post link.
				edit_post_link(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers. */
							__( 'Edit <span class="screen-reader-text">%s</span>', 'twentynineteen' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						get_the_title()
					),
					'<span class="edit-link">' . twentynineteen_get_icon_svg( 'edit', 16 ),
					'</span>'
				);
			?>
		</div><!-- .meta-info -->
		<?php endif; ?>
	</header>
<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentynineteen' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'twentynineteen' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php twentynineteen_entry_footer(); ?>
	</footer><!-- .entry-footer -->

	<?php get_template_part( 'template-parts/post/author', 'info' ); ?>

</article><!-- #post-${ID} -->

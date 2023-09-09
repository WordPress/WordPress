<?php
/**
 * Displays the post header
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
<<<<<<< HEAD
 * @since Twenty Nineteen 1.0
=======
 * @since 1.0.0
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

$discussion = ! is_page() && twentynineteen_can_show_post_thumbnail() ? twentynineteen_get_discussion_data() : null; ?>

<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

<<<<<<< HEAD
<?php
if ( ! is_page() ) :
	?>
=======
<?php if ( ! is_page() ) : ?>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
<div class="entry-meta">
	<?php twentynineteen_posted_by(); ?>
	<?php twentynineteen_posted_on(); ?>
	<span class="comment-count">
		<?php
		if ( ! empty( $discussion ) ) {
			twentynineteen_discussion_avatars_list( $discussion->authors );
		}
		?>
		<?php twentynineteen_comment_count(); ?>
	</span>
	<?php
<<<<<<< HEAD
		// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Post title. Only visible to screen readers. */
=======
	// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers. */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
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
<<<<<<< HEAD
</div><!-- .entry-meta -->
	<?php
endif;
=======
</div><!-- .meta-info -->
<?php endif; ?>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

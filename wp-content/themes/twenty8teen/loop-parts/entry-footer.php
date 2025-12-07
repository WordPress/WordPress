<?php
/**
 * Template part for displaying the entry footer
 * @package Twenty8teen
 */

?>
	<footer <?php twenty8teen_attributes( 'footer', array(
		'class' => twenty8teen_widget_get_classes( 'entry-footer' )
		) ); ?>>
		<?php the_taxonomies( array(
			'before' => '<div class="tax-link-wrap">',
			'template' => '<span class="taxonomy-label">%s:</span> <span class="taxonomy-term-list">%l.</span>',
			'term_template' => '<a href="%1$s" rel="tag">%2$s</a>',
			'sep' => '<br />',
			'after' => '</div>',
		) ); ?>

		<?php
		if ( ! is_singular() ) {
			echo '<span class="comments-link-wrap">';
												// 0,  1,     more,   class,         disabled
			comments_popup_link( false, false, false, 'comments-link', '' );
			echo '</span>';
		}
		else if ( is_attachment() ) {
			twenty8teen_attachment_meta();
		}

		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post for edit link, only visible to screen readers, with space before and after */
				esc_html__( 'Edit%s', 'twenty8teen' ),
				'<span class="screen-reader-text"> "'
					. wp_strip_all_tags( get_the_title() ) . '" </span>'
			),
			' <span class="edit-link">',
			'</span>'
		);
		?>
	</footer><!-- .entry-footer -->

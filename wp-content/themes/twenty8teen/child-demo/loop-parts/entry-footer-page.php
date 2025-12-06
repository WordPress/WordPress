<?php
/**
 * Template part for displaying the entry footer on Pages
 * @package Twenty8teen
 */

?>
	<footer <?php twenty8teen_attributes( 'footer', array(
		'class' => twenty8teen_widget_get_classes( 'entry-footer' )
		) ); ?>>

		<?php
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
		<ul class="child-page-wrap titles-font">
		<?php
		wp_list_pages( array( 
			'child_of' => get_the_ID(), 
			'sort_column' => 'menu_order',
			'title_li' => '',
		) );
		?>
		</ul>
	</footer><!-- .entry-footer -->

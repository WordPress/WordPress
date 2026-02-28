<?php
/**
 * Template part for displaying the author biography
 * @package Twenty8teen
 */

$this_post_type = get_post_type();

if ( post_type_supports( $this_post_type, 'author' ) &&
	! in_array( $this_post_type, apply_filters( 'twenty8teen_posttypes_no_author', array( 'page' ) ) ) ) {
	if ( is_attachment() || is_single() ) : ?>

	<dl <?php twenty8teen_attributes( 'dl', array( 
		'class' => twenty8teen_widget_get_classes( 'author-bio author vcard' ) 
		) ); ?>>
		<dt>
			<?php
			echo twenty8teen_author_link(
				/* translators: %s: post author. */
				esc_html_x( 'Author: %s', 'post author', 'twenty8teen' ) )
				. ' ' . get_avatar( get_the_author_meta( 'user_email' ), 48 ) 
			?>
		</dt>
		<dd class="author-description note">
			<?php the_author_meta( 'description' ); ?>
		</dd><!-- .author-description -->
	</dl><!-- .author-bio -->

	<?php
	endif;
}

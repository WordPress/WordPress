<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both
 * the current comments and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Twenty8teen
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" <?php twenty8teen_area_classes( 'comments',
	twenty8teen_widget_get_classes( 'comments-area' ) ); ?>>

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) : ?>
		<h3 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			if ( 1 === $comment_count ) {
				esc_html_e( 'One comment', 'twenty8teen' );
			} else {
				printf(
				/* translators: 1: comment count number */
					esc_html( _nx( '%1$s Comment', '%1$s Comments', $comment_count,
						'comments title', 'twenty8teen' ) ),
					number_format_i18n( $comment_count )
				);
			}
			?>
		</h3><!-- .comments-title -->

		<?php the_comments_navigation(); ?>

		<ul class="comment-list">
			<?php
				wp_list_comments( array(
					'style'      => 'ul',
					'short_ping' => false,
					'avatar_size' => 48,
				) );
			?>
		</ul><!-- .comment-list -->

		<?php the_comments_navigation();

		if ( ! comments_open() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'twenty8teen' ); ?></p>
		<?php
		endif;

	endif; // Check for have_comments().

	comment_form();
	?>

</div><!-- #comments -->

<?php
/**
 * The template for displaying Current Discussion on posts
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
<<<<<<< HEAD
 * @since Twenty Nineteen 1.0
=======
 * @since 1.0.0
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

/* Get data from current discussion on post. */
$discussion    = twentynineteen_get_discussion_data();
$has_responses = $discussion->responses > 0;

if ( $has_responses ) {
<<<<<<< HEAD
	/* translators: %d: Number of comments. */
=======
	/* translators: %1(X comments)$s */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	$meta_label = sprintf( _n( '%d Comment', '%d Comments', $discussion->responses, 'twentynineteen' ), $discussion->responses );
} else {
	$meta_label = __( 'No comments', 'twentynineteen' );
}
?>

<div class="discussion-meta">
	<?php
	if ( $has_responses ) {
		twentynineteen_discussion_avatars_list( $discussion->authors );
	}
	?>
	<p class="discussion-meta-info">
		<?php echo twentynineteen_get_icon_svg( 'comment', 24 ); ?>
		<span><?php echo esc_html( $meta_label ); ?></span>
	</p>
</div><!-- .discussion-meta -->

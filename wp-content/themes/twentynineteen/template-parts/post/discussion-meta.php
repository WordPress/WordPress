<?php
/**
 * The template for displaying Current Discussion on posts
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

/* Get data from current discussion on post. */
$discussion = twentynineteen_get_discussion_data();

$comments_number = get_comments_number();
$has_responses   = $discussion->responses > 0;

if ( $has_responses ) {
	/* translators: %1(X responses)$s from %2(X others)$s */
	$meta_label = sprintf(
		'%1$s from %2$s.',
		sprintf( _n( '%d response', '%d responses', $discussion->responses, 'twentynineteen' ), $discussion->responses ),
		sprintf( _n( '%d other', '%d others', $discussion->commenters, 'twentynineteen' ), $discussion->commenters )
	);
} elseif ( $comments_number > 0 ) {
	/* Show comment count if not enough discussion information */

	$meta_label = sprintf( _n( '%d Comment', '%d Comments', $comments_number, 'twentynineteen' ), $comments_number );
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

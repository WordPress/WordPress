<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package WordPress
 * @subpackage Simright
 * @since Simright 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comment-wrap">
    <h2 class="comments-title">
        <?php
            $comments_number = get_comments_number();
            if ( '1' === $comments_number ) {
                /* translators: %s: post title */
                printf( _x( 'Comments(1)', 'comments title', 'simright' ), get_the_title() );
            } else {
                printf(
                    /* translators: 1: number of comments, 2: post title */
                    _nx(
                        'Comments(%1$s)',
                        'Comments(%1$s)',
                        $comments_number,
                        'comments title',
                        'simright'
                    ),
                    number_format_i18n( $comments_number ),
                    get_the_title()
                );
            }
        ?>
    </h2>
    <hr/>

<?php
    comment_form(
        array(
            'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
            'title_reply_after'  => '</h2>',
        )
    );
?>

	<?php if ( have_comments() ) : ?>
		
		<ul class="comment-list">
			<?php
				wp_list_comments(
					array(
						'style'       => 'ul',
						'short_ping'  => true,
						'avatar_size' => 32,
					)
				);
			?>
        </ul>
        <!-- .comment-list -->

		<?php the_comments_navigation(); ?>

	<?php endif; // Check for have_comments(). ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
	<p class="no-comments"><?php _e( 'Comments are closed.', 'simright' ); ?></p>
	<?php endif; ?>

</div><!-- .comments-area -->

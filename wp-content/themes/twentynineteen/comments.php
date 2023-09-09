<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
<<<<<<< HEAD
 * @since Twenty Nineteen 1.0
=======
 * @since 1.0.0
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
*/
if ( post_password_required() ) {
	return;
}

$discussion = twentynineteen_get_discussion_data();
?>

<div id="comments" class="<?php echo comments_open() ? 'comments-area' : 'comments-area comments-closed'; ?>">
	<div class="<?php echo $discussion->responses > 0 ? 'comments-title-wrap' : 'comments-title-wrap no-responses'; ?>">
		<h2 class="comments-title">
		<?php
		if ( comments_open() ) {
			if ( have_comments() ) {
				_e( 'Join the Conversation', 'twentynineteen' );
			} else {
				_e( 'Leave a comment', 'twentynineteen' );
			}
		} else {
<<<<<<< HEAD
			if ( '1' === (string) $discussion->responses ) {
				/* translators: %s: Post title. */
				printf( _x( 'One reply on &ldquo;%s&rdquo;', 'comments title', 'twentynineteen' ), get_the_title() );
			} else {
				printf(
					/* translators: 1: Number of comments, 2: Post title. */
=======
			if ( '1' == $discussion->responses ) {
				/* translators: %s: post title */
				printf( _x( 'One reply on &ldquo;%s&rdquo;', 'comments title', 'twentynineteen' ), get_the_title() );
			} else {
				printf(
					/* translators: 1: number of comments, 2: post title */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
					_nx(
						'%1$s reply on &ldquo;%2$s&rdquo;',
						'%1$s replies on &ldquo;%2$s&rdquo;',
						$discussion->responses,
						'comments title',
						'twentynineteen'
					),
					number_format_i18n( $discussion->responses ),
					get_the_title()
				);
			}
		}
		?>
		</h2><!-- .comments-title -->
		<?php
<<<<<<< HEAD
		// Only show discussion meta information when comments are open and available.
=======
			// Only show discussion meta information when comments are open and available.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		if ( have_comments() && comments_open() ) {
			get_template_part( 'template-parts/post/discussion', 'meta' );
		}
		?>
<<<<<<< HEAD
	</div><!-- .comments-title-wrap -->
=======
	</div><!-- .comments-title-flex -->
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	<?php
	if ( have_comments() ) :

		// Show comment form at top if showing newest comments at the top.
		if ( comments_open() ) {
			twentynineteen_comment_form( 'desc' );
		}

		?>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'walker'      => new TwentyNineteen_Walker_Comment(),
					'avatar_size' => twentynineteen_get_avatar_size(),
					'short_ping'  => true,
					'style'       => 'ol',
				)
			);
			?>
		</ol><!-- .comment-list -->
		<?php

<<<<<<< HEAD
		// Show comment navigation.
		if ( have_comments() ) :
			$prev_icon = twentynineteen_get_icon_svg( 'chevron_left', 22 );
			$next_icon = twentynineteen_get_icon_svg( 'chevron_right', 22 );
			the_comments_navigation(
				array(
					'prev_text' => sprintf(
						'%1$s <span class="nav-prev-text">%2$s</span>',
						$prev_icon,
						/* translators: Comments navigation link text. The secondary-text element is hidden on small screens. */
						__( '<span class="primary-text">Previous</span> <span class="secondary-text">Comments</span>', 'twentynineteen' )
					),
					'next_text' => sprintf(
						'<span class="nav-next-text">%1$s</span> %2$s',
						/* translators: Comments navigation link text. The secondary-text element is hidden on small screens. */
						__( '<span class="primary-text">Next</span> <span class="secondary-text">Comments</span>', 'twentynineteen' ),
						$next_icon
					),
=======
		// Show comment navigation
		if ( have_comments() ) :
			$prev_icon     = twentynineteen_get_icon_svg( 'chevron_left', 22 );
			$next_icon     = twentynineteen_get_icon_svg( 'chevron_right', 22 );
			$comments_text = __( 'Comments', 'twentynineteen' );
			the_comments_navigation(
				array(
					'prev_text' => sprintf( '%s <span class="nav-prev-text"><span class="primary-text">%s</span> <span class="secondary-text">%s</span></span>', $prev_icon, __( 'Previous', 'twentynineteen' ), __( 'Comments', 'twentynineteen' ) ),
					'next_text' => sprintf( '<span class="nav-next-text"><span class="primary-text">%s</span> <span class="secondary-text">%s</span></span> %s', __( 'Next', 'twentynineteen' ), __( 'Comments', 'twentynineteen' ), $next_icon ),
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				)
			);
		endif;

		// Show comment form at bottom if showing newest comments at the bottom.
		if ( comments_open() && 'asc' === strtolower( get_option( 'comment_order', 'asc' ) ) ) :
			?>
<<<<<<< HEAD
			<div class="comment-form-flex comment-form-wrapper">
				<h2 class="comments-title"><?php _e( 'Leave a comment', 'twentynineteen' ); ?></h2>
				<?php twentynineteen_comment_form( 'asc' ); ?>
=======
			<div class="comment-form-flex">
				<span class="screen-reader-text"><?php _e( 'Leave a comment', 'twentynineteen' ); ?></span>
				<?php twentynineteen_comment_form( 'asc' ); ?>
				<h2 class="comments-title" aria-hidden="true"><?php _e( 'Leave a comment', 'twentynineteen' ); ?></h2>
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			</div>
			<?php
		endif;

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() ) :
			?>
			<p class="no-comments">
				<?php _e( 'Comments are closed.', 'twentynineteen' ); ?>
			</p>
			<?php
		endif;

	else :

		// Show comment form.
		twentynineteen_comment_form( true );

	endif; // if have_comments();
	?>
</div><!-- #comments -->

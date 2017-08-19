<?php
/**
 * The template for displaying Comments.
 *
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				if( 1 == get_comments_number() ) {
					printf( __( 'One thought on &ldquo;%2$s&rdquo;', 'travelify' ), number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
				}
				else {
					printf( __( '%1$s thoughts on &ldquo;%2$s&rdquo;', 'travelify' ), number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
				}
			?>
		</h2>

		<ol class="commentlist">
			<?php wp_list_comments( array( 'callback' => 'travelify_comment', 'style' => 'ol' ) ); ?>
		</ol><!-- .commentlist -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<ul class="default-wp-page clearfix">
			<h1 class="assistive-text section-heading"><?php _e( 'Comment navigation', 'travelify' ); ?></h1>
			<li class="previous"><?php previous_comments_link( __( '&larr; Older Comments', 'travelify' ) ); ?></li>
			<li class="next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'travelify' ) ); ?></li>
		</ul>
		<?php endif; // check for comment navigation ?>

	<?php // If comments are closed and there are comments, let's leave a little note.
		elseif ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'travelify' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments .comments-area -->
<?php
/**
 * The template for displaying Comments.
 *
 * @package Theme Meme
 */

if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h3 class="widget-title comments-title">
			<?php
				printf( _nx( '1 Response', '%1$s Responses', get_comments_number(), '', 'themememe' ),
					number_format_i18n( get_comments_number() ) );
			?>
		</h3>

		<ol class="comment-list">
			<?php
				wp_list_comments( array( 'callback' => 'themememe_comment', 'avatar_size' => 40 ) );
			?>
		<!-- .comment-list --></ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<nav class="clearfix comment-navigation" role="navigation">
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'themememe' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'themememe' ) ); ?></div>
		<!-- .comment-navigation --></nav>
		<?php endif; ?>

	<?php endif; ?>

	<?php
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php _e( 'Comments are closed.', 'themememe' ); ?></p>
	<?php endif; ?>

	<?php
		$req = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );

		$args = array(
			'comment_field' =>	'<div class="form-group comment-form-comment">' .
				'<textarea id="comment" class="form-control" name="comment" cols="45" rows="4" aria-required="true" placeholder="'. __( 'Message...', 'themememe' ) .'">' .
				'</textarea></div>',

			'fields' => apply_filters( 'comment_form_default_fields', array(
				'author' =>
					'<div class="form-group comment-form-author">' .
					'<input id="author" class="form-control" name="author" type="text" placeholder="'. __( 'Name', 'themememe' ) . ( $req ? '*' : '' ) .'" value="' . esc_attr( $commenter['comment_author'] ) .
					'" size="30"' . $aria_req . ' /></div>',

				'email' =>
					'<div class="form-group comment-form-email">' .
					'<input id="email" class="form-control" name="email" type="text" placeholder="'. __( 'Email', 'themememe' ) . ( $req ? '*' : '' ) .'" value="' . esc_attr(	$commenter['comment_author_email'] ) .
					'" size="30"' . $aria_req . ' /></div>',

				'url' =>
					'<div class="form-group comment-form-url">' .
					'<input id="url" class="form-control" name="url" type="text" placeholder="'. __( 'Website', 'themememe' ) .'" value="' . esc_attr( $commenter['comment_author_url'] ) .
					'" size="30" /></div>'
				)
			),
		);

		comment_form( $args );
	?>


<!-- #comments --></div>
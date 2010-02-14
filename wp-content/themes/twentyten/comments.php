			<div id="comments">
<?php
	// Do not delete these lines
	$req = get_option( 'require_name_email' ); // Checks if fields are required.
	if ( 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) )
		die ( __( 'Please do not load this page directly. Thanks!', 'twentyten' ) );
	if ( post_password_required() ) :
?>
				<div class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'twentyten' ); ?></div>
			</div><!-- .comments -->
<?php
		return;
	endif;
?>

<?php
	// You can start editing here -- including this comment!
?>

<?php if ( have_comments() ) : ?>
			<h3 id="comments-title"><?php comments_number( __('No Responses to', 'twentyten'), __('One Response to', 'twentyten'), __('% Responses to', 'twentyten') );?>  <em><?php the_title(); ?></em></h3>

<?php $total_pages = get_comment_pages_count(); if ( $total_pages > 1 ) : // are there comments to navigate through ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __('&larr; Older Comments', 'twentyten') ); ?></div>
				<div class="nav-next"><?php next_comments_link( __('Newer Comments &rarr;', 'twentyten') ); ?></div>
			</div>
<?php endif; // check for comment navigation ?>

			<ol class="commentlist">
				<?php wp_list_comments( array('callback' => 'twentyten_comment') ); ?>
			</ol>

<?php $total_pages = get_comment_pages_count(); if ( $total_pages > 1 ) : // are there comments to navigate through ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __('&larr; Older Comments', 'twentyten') ); ?></div>
				<div class="nav-next"><?php next_comments_link( __('Newer Comments &rarr;', 'twentyten') ); ?></div>
			</div>
<?php endif; // check for comment navigation ?>

<?php else : // this is displayed if there are no comments so far ?>

<?php if ( comments_open() ) : // If comments are open, but there are no comments ?>

<?php else : // if comments are closed ?>

		<p class="nocomments"><?php _e('Comments are closed.', 'twentyten'); ?></p>

<?php endif; ?>
<?php endif; ?>

<?php comment_form(); ?>

</div><!-- #comments -->
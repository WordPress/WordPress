			<div id="comments">
<?php
	// Do not delete these lines
	$req = get_option('require_name_email'); // Checks if fields are required.
	if ( 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) )
		die ( 'Please do not load this page directly. Thanks!' );
	if ( ! empty($post->post_password) ) :
		if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password ) :
?>
				<div class="nopassword"><?php _e('This post is password protected. Enter the password to view any comments.', 'twentyten') ?></div>
			</div><!-- .comments -->
<?php
		return;
	endif;
endif;
?>

<?php
	// You can start editing here -- including this comment!
?>

<?php if ( have_comments() ) : ?>
			<h3 id="comments-title"><?php comments_number( __('No Responses to', 'twentyten'), __('One Response to', 'twentyten'), __('% Responses to', 'twentyten') );?>  <em><?php the_title(); ?></em></h3>

<?php $total_pages = get_comment_pages_count(); if ( $total_pages > 1 ) : // are there comments to navigate through ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __('&larr; Older Comments', 'twentyten') ) ?></div>
				<div class="nav-next"><?php next_comments_link( __('Newer Comments &rarr;', 'twentyten') ) ?></div>
			</div>
<?php endif; // check for comment navigation ?>

			<ol class="commentlist">
			<?php wp_list_comments( array('callback' => 'twentyten_comment') ); ?>
			</ol>

<?php $total_pages = get_comment_pages_count(); if ( $total_pages > 1 ) : // are there comments to navigate through ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __('&larr; Older Comments', 'twentyten') ) ?></div>
				<div class="nav-next"><?php next_comments_link( __('Newer Comments &rarr;', 'twentyten') ) ?></div>
			</div>
<?php endif; // check for comment navigation ?>

<?php else : // this is displayed if there are no comments so far ?>

<?php if ( comments_open() ) : // If comments are open, but there are no comments ?>

<?php else : // if comments are closed ?>

		<p class="nocomments"><?php _e('Comments are closed.', 'twentyten'); ?></p>

<?php endif; ?>
<?php endif; ?>

<?php if ( comments_open() ) : ?>

			<div id="respond">

				<h3 id="reply-title"><?php comment_form_title( __('Leave a Reply', 'twentyten'), __('Leave a Reply to %s', 'twentyten') ); ?> <small><?php cancel_comment_reply_link( __('Cancel reply', 'twentyten') ); ?></small></h3>

<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
				<p><?php printf( __('You must be <a href="%s">logged in</a> to post a comment.', 'twentyten'), wp_login_url( get_permalink() ) ); ?></p>

<?php else : // here's the big comment form ?>
				<form action="<?php echo site_url('wp-comments-post.php'); ?>" method="post" id="commentform">

	<?php if ( $user_ID ) : ?>
					<p id="login"><?php printf(__('<span class="loggedin">Logged in as <a href="%1$s" title="Logged in as %2$s">%2$s</a>.</span> <span class="logout"><a href="%3$s" title="Log out of this account">Log out?</a></span>', 'twentyten'),
						admin_url('profile.php'),
						wp_specialchars($user_identity, true),
						wp_logout_url(get_permalink()) ) ?></p>

	<?php else : ?>

					<p id="comment-notes"><?php _e('Your email is <em>never</em> published nor shared.', 'twentyten') ?> <?php if ($req) _e('Required fields are marked <span class="required">*</span>', 'twentyten') ?></p>


					<div id="form-section-author" class="form-section">
						<div class="form-label"><label for="author"><?php _e('Name', 'twentyten') ?></label> <?php if ($req) _e('<span class="required">*</span>', 'twentyten') ?></div>
						<div class="form-input"><input id="author" name="author" type="text" value="<?php echo $comment_author ?>" size="30" tabindex="3" /></div>
					</div><!-- #form-section-author .form-section -->

					<div id="form-section-email" class="form-section">
						<div class="form-label"><label for="email"><?php _e('Email', 'twentyten') ?></label> <?php if ($req) _e('<span class="required">*</span>', 'twentyten') ?></div>
						<div class="form-input"><input id="email" name="email" type="text" value="<?php echo $comment_author_email ?>" size="30" tabindex="4" /></div>
					</div><!-- #form-section-email .form-section -->

					<div id="form-section-url" class="form-section">
						<div class="form-label"><label for="url"><?php _e('Website', 'twentyten') ?></label></div>
						<div class="form-input"><input id="url" name="url" type="text" value="<?php echo $comment_author_url ?>" size="30" tabindex="5" /></div>
					</div><!-- #form-section-url .form-section -->

	<?php endif; // if ( $user_ID ) ?>

					<div id="form-section-comment" class="form-section">
						<div class="form-label"><label for="comment"><?php _e('Comment', 'twentyten') ?></label></div>
						<div class="form-textarea"><textarea id="comment" name="comment" cols="45" rows="8" tabindex="6"></textarea></div>
					</div><!-- #form-section-comment .form-section -->

					<div id="form-allowed-tags" class="form-section">
						<p><span><?php _e('You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:', 'twentyten') ?></span> <code><?php echo allowed_tags(); ?></code></p>
					</div>

					<?php do_action('comment_form', $post->ID); ?>

					<div class="form-submit"><input id="submit" name="submit" type="submit" value="<?php _e('Post Comment', 'twentyten') ?>" tabindex="7" /><input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" /></div>

<?php comment_id_fields(); ?>


				</form>

	<?php endif; // If registration required and not logged in ?>
			</div> <!-- #respond -->

<?php endif; // if you delete this the sky will fall on your head ?>
			</div><!-- #comments -->

<?php $ultimatemember->shortcodes->loop = $ultimatemember->query->make('post_type=comment&number=10&offset=0&user_id=' . um_user('ID') ); ?>

<?php if ( $ultimatemember->shortcodes->loop ) { ?>
			
	<?php $ultimatemember->shortcodes->load_template('profile/comments-single'); ?>
	
	<div class="um-ajax-items">
	
		<!--Ajax output-->
		
		<?php if ( count($ultimatemember->shortcodes->loop) >= 10 ) { ?>
		
		<div class="um-load-items">
			<a href="#" class="um-ajax-paginate um-button" data-hook="um_load_comments" data-args="comment,10,10,<?php echo um_user('ID'); ?>"><?php _e('load more comments','ultimate-member'); ?></a>
		</div>
		
		<?php } ?>
		
	</div>
		
<?php } else { ?>

	<div class="um-profile-note"><span><?php echo ( um_profile_id() == get_current_user_id() ) ? __('You have not made any comments.','ultimate-member') : __('This user has not made any comments.','ultimate-member'); ?></span></div>
	
<?php } ?>
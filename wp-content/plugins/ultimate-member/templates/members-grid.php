<div class="um-members">
			
	<div class="um-gutter-sizer"></div>
	
	<?php $i = 0; foreach( um_members('users_per_page') as $member) { $i++; um_fetch_user( $member ); ?>
			
	<div class="um-member um-role-<?php echo um_user('role'); ?> <?php echo um_user('account_status'); ?> <?php if ($cover_photos) { echo 'with-cover'; } ?>">
				
		<span class="um-member-status <?php echo um_user('account_status'); ?>"><?php echo um_user('account_status_name'); ?></span>
					
		<?php
		if ($cover_photos) {
			$sizes = um_get_option('cover_thumb_sizes');
			if ( $ultimatemember->mobile->isTablet() ) {
				$cover_size = $sizes[1];
			} else {
				$cover_size = $sizes[0];
			}
		?>

		<div class="um-member-cover" data-ratio="<?php echo um_get_option('profile_cover_ratio'); ?>">
			<div class="um-member-cover-e"><a href="<?php echo um_user_profile_url(); ?>" title="<?php echo esc_attr(um_user('display_name')); ?>"><?php echo um_user('cover_photo', $cover_size); ?></a></div>
		</div>

		<?php } ?>
		
		<?php if ($profile_photo) {
			$default_size = str_replace( 'px', '', um_get_option('profile_photosize') );
			$corner = um_get_option('profile_photocorner');
		?>
		<div class="um-member-photo radius-<?php echo $corner; ?>"><a href="<?php echo um_user_profile_url(); ?>" title="<?php echo esc_attr(um_user('display_name')); ?>"><?php echo get_avatar( um_user('ID'), $default_size ); ?></a></div>
		<?php } ?>
					
					<div class="um-member-card <?php if (!$profile_photo) { echo 'no-photo'; } ?>">
						
						<?php if ( $show_name ) { ?>
						<div class="um-member-name"><a href="<?php echo um_user_profile_url(); ?>" title="<?php echo esc_attr(um_user('display_name')); ?>"><?php echo um_user('display_name', 'html'); ?></a></div>
						<?php } ?>
						
						<?php do_action('um_members_just_after_name', um_user('ID'), $args); ?>
						
						<?php do_action('um_members_after_user_name', um_user('ID'), $args); ?>
						
						<?php
						if ( $show_tagline && is_array( $tagline_fields ) ) {
							
							um_fetch_user( $member );

							foreach( $tagline_fields as $key ) {
								if ( $key && um_filtered_value( $key ) ) {
									$value = um_filtered_value( $key );


						?>
						
						<div class="um-member-tagline um-member-tagline-<?php echo $key;?>"><?php echo $value; ?></div>
						
						<?php
								} // end if
							} // end foreach
						} // end if $show_tagline
						?>
						
						<?php if ( $show_userinfo ) { ?>
						
						<div class="um-member-meta-main">
						
							<?php if ( $userinfo_animate ) { ?>
							<div class="um-member-more"><a href="#"><i class="um-faicon-angle-down"></i></a></div>
							<?php } ?>
							
							<div class="um-member-meta <?php if ( !$userinfo_animate ) { echo 'no-animate'; } ?>">
							
								<?php foreach( $reveal_fields as $key ) {
										if ( $key && um_filtered_value( $key ) ) {
											$value = um_filtered_value( $key );
											
								?>
								
								<div class="um-member-metaline um-member-metaline-<?php echo $key; ?>"><span><strong><?php echo $ultimatemember->fields->get_label( $key ); ?>:</strong> <?php echo $value; ?></span></div>
								
								<?php 
									}
								} 
								?>
								
								<?php if ( $show_social ) { ?>
								<div class="um-member-connect">
								
									<?php $ultimatemember->fields->show_social_urls(); ?>

								</div>
								<?php } ?>
								
							</div>

							<div class="um-member-less"><a href="#"><i class="um-faicon-angle-up"></i></a></div>
						
						</div>
						
						<?php } ?>
						
					</div>
					
	</div>
				
	<?php 
	um_reset_user_clean();
	} // end foreach

	um_reset_user();
	?>

	<div class="um-clear"></div>

</div>

<div class="um-admin-metabox">

	<div class="">
	
		<p>
			<label class="um-admin-half"><?php _e('Action to be taken after login','ultimate-member'); ?> <?php $this->tooltip( __('Select what happens when a user with this role logins to your site','ultimate-member') ); ?></label>
			<span class="um-admin-half">
			
				<select name="_um_after_login" id="_um_after_login" class="umaf-selectjs um-adm-conditional" style="width: 300px" data-cond1="redirect_url" data-cond1-show="_um_after_login">
					<option value="redirect_profile" <?php selected('redirect_profile', $ultimatemember->query->get_meta_value('_um_after_login') ); ?>><?php _e('Redirect to profile','ultimate-member'); ?></option>
					<option value="redirect_url" <?php selected('redirect_url', $ultimatemember->query->get_meta_value('_um_after_login') ); ?>><?php _e('Redirect to URL','ultimate-member'); ?></option>
					<option value="refresh" <?php selected('refresh', $ultimatemember->query->get_meta_value('_um_after_login') ); ?>><?php _e('Refresh active page','ultimate-member'); ?></option>
					<option value="redirect_admin" <?php selected('redirect_admin', $ultimatemember->query->get_meta_value('_um_after_login') ); ?>><?php _e('Redirect to WordPress Admin','ultimate-member'); ?></option>
				</select>

			</span>
		</p><div class="um-admin-clear"></div>
		
		<p class="_um_after_login">
			<label class="um-admin-half" for="_um_login_redirect_url"><?php _e('Set Custom Redirect URL','ultimate-member'); ?> <?php $this->tooltip( __('Set a url to redirect this user role to after they login with their account','ultimate-member') ); ?></label>
			<span class="um-admin-half">
				
				<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_redirect_url', null, 'na'); ?>" name="_um_login_redirect_url" id="_um_login_redirect_url" />
			
			</span>
		</p><div class="um-admin-clear"></div>
		
	</div>
	
	<div class="um-admin-clear"></div>
	
</div>
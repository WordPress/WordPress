<div class="um-admin-metabox">

	<p><label for="_um_login_after_login"><?php _e('Redirection after Login','ultimate-member'); ?> <?php $this->tooltip('Change this If you want to override role redirection settings after login only.', 'e'); ?></label>
		<select name="_um_login_after_login" id="_um_login_after_login" class="umaf-selectjs um-adm-conditional" style="width: 100%"  data-cond1="redirect_url" data-cond1-show="_um_login_after_login">

			<option value="0" <?php selected('0', $ultimatemember->query->get_meta_value('_um_login_after_login', null, 0 ) ); ?>><?php _e('Default','ultimate-member'); ?></option>
			<option value="redirect_profile" <?php selected('redirect_profile', $ultimatemember->query->get_meta_value('_um_login_after_login', null, 0 ) ); ?>>Redirect to profile</option>
			<option value="redirect_url" <?php selected('redirect_url', $ultimatemember->query->get_meta_value('_um_login_after_login', null, 0 ) ); ?>>Redirect to URL</option>
			<option value="refresh" <?php selected('refresh', $ultimatemember->query->get_meta_value('_um_login_after_login', null, 0 ) ); ?>>Refresh active page</option>
			<option value="redirect_admin" <?php selected('redirect_admin', $ultimatemember->query->get_meta_value('_um_login_after_login', null, 0 ) ); ?>>Redirect to WordPress Admin</option>
			
		</select>
	</p>
	
	<p class="_um_login_after_login">
		<label for="_um_login_redirect_url"><?php _e('Set Custom Redirect URL','ultimate-member'); ?> <?php $this->tooltip('', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_redirect_url', null, 'na'); ?>" name="_um_login_redirect_url" id="_um_login_redirect_url" />
	</p>
	
</div>
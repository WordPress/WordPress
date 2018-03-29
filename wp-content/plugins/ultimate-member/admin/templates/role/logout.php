<div class="um-admin-metabox">

	<div class="">
	
		<p>
			<label class="um-admin-half"><?php _e('Action to be taken after logout','ultimate-member'); ?> <?php $this->tooltip( __('Select what happens when a user with this role logouts of your site','ultimate-member') ); ?></label>
			<span class="um-admin-half">
			
				<select name="_um_after_logout" id="_um_after_logout" class="umaf-selectjs um-adm-conditional" style="width: 300px" data-cond1="redirect_url" data-cond1-show="_um_after_logout">
					<option value="redirect_home" <?php selected('redirect_home', $ultimatemember->query->get_meta_value('_um_after_logout') ); ?>><?php _e('Go to Homepage','ultimate-member'); ?></option>
					<option value="redirect_url" <?php selected('redirect_url', $ultimatemember->query->get_meta_value('_um_after_logout') ); ?>><?php _e('Go to Custom URL','ultimate-member'); ?></option>
				</select>

			</span>
		</p><div class="um-admin-clear"></div>
		
		<p class="_um_after_logout">
			<label class="um-admin-half" for="_um_logout_redirect_url"><?php _e('Set Custom Redirect URL','ultimate-member'); ?> <?php $this->tooltip( __('Set a url to redirect this user role to after they logout from site','ultimate-member') ); ?></label>
			<span class="um-admin-half">
				
				<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_logout_redirect_url', null, 'na'); ?>" name="_um_logout_redirect_url" id="_um_logout_redirect_url" />
			
			</span>
		</p><div class="um-admin-clear"></div>
		
	</div>
	
	<div class="um-admin-clear"></div>
	
</div>
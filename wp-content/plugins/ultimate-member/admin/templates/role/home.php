<div class="um-admin-metabox">

	<div class="">
	
		<p>
			<label class="um-admin-half"><?php _e('Can view default homepage?','ultimate-member'); ?> <?php $this->tooltip( __('Allow this user role to view your site\'s homepage','ultimate-member') ); ?></label>
			<span class="um-admin-half">
			
				<?php $this->ui_on_off('_um_default_homepage', 1, true, 1, 'xxx', 'redirect-home-url'); ?>
				
			</span>
		</p><div class="um-admin-clear"></div>
			
		<p class="redirect-home-url">
			<label class="um-admin-half" for="_um_redirect_homepage"><?php _e('Custom Homepage Redirect','ultimate-member'); ?> <?php $this->tooltip( __('Set a url to redirect this user role to if they try to view your site\'s homepage ','ultimate-member') ); ?></label>
			<span class="um-admin-half">
				
				<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_redirect_homepage', null, 'na'); ?>" name="_um_redirect_homepage" id="_um_redirect_homepage" />
			
			</span>
		</p><div class="um-admin-clear"></div>
		
	</div>
	
	<div class="um-admin-clear"></div>
	
</div>
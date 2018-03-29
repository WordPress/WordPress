<div class="um-admin-metabox">

	<div class="">
		
		<p>
			<label class="um-admin-half"><?php _e('Number of profiles per page','ultimate-member'); ?> <?php $this->tooltip( __('Number of profiles to appear on page for standard users') ); ?></label>
			<span class="um-admin-half">
			
				<input type="text" name="_um_profiles_per_page" id="_um_profiles_per_page" value="<?php echo $ultimatemember->query->get_meta_value('_um_profiles_per_page', null, 12); ?>" class="small" />
			
			</span>
		</p><div class="um-admin-clear"></div>
		
		<p>
			<label class="um-admin-half"><?php _e('Number of profiles per page (for Mobiles & Tablets)','ultimate-member'); ?> <?php $this->tooltip( __('Number of profiles to appear on page for mobile users') ); ?></label>
			<span class="um-admin-half">
			
				<input type="text" name="_um_profiles_per_page_mobile" id="_um_profiles_per_page_mobile" value="<?php echo $ultimatemember->query->get_meta_value('_um_profiles_per_page_mobile', null, 8); ?>" class="small" />
			
			</span>
		</p><div class="um-admin-clear"></div>
		
		<p>
			<label class="um-admin-half"><?php _e('Maximum number of profiles','ultimate-member'); ?> <?php $this->tooltip( __('Use this setting to control the maximum number of profiles to appear in this directory. Leave blank to disable this limit','ultimate-member') ); ?></label>
			<span class="um-admin-half">
				
				<input type="text" name="_um_max_users" id="_um_max_users" value="<?php echo $ultimatemember->query->get_meta_value('_um_max_users', null, 'na' ); ?>" class="small" />
				
			</span>
		</p><div class="um-admin-clear"></div>

	</div>
	
	<div class="um-admin-clear"></div>
	
</div>
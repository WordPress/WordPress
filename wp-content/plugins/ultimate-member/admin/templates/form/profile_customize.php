<div class="um-admin-metabox">

	<p>
		<label><?php _e('Use global settings?','ultimate-member'); ?> <?php $this->tooltip('Switch to no if you want to customize this form settings, styling &amp; appearance', 'e'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_profile_use_globals', 1, true, 1, 'xxx', 'profile-customize'); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	<div class="profile-customize">
	
	<p><label for="_um_profile_role"><?php _e('Make this profile role-specific','ultimate-member'); ?></label>
		<select name="_um_profile_role" id="_um_profile_role" class="umaf-selectjs" style="width: 100%">
			
			<?php foreach($ultimatemember->query->get_roles( $add_default = 'All roles' ) as $key => $value) { ?>
			
			<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_profile_role', null, um_get_option('profile_role') ) ); ?>><?php echo $value; ?></option>
			
			<?php } ?>
			
		</select>
	</p>
	
	<p><label for="_um_profile_template"><?php _e('Template','ultimate-member'); ?></label>
		<select name="_um_profile_template" id="_um_profile_template" class="umaf-selectjs" style="width: 100%">

			<?php foreach($ultimatemember->shortcodes->get_templates( 'profile' ) as $key => $value) { ?>
			
			<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_profile_template', null, um_get_option('profile_template') ) ); ?>><?php echo $value; ?></option>
			
			<?php } ?>
			
		</select>
	</p>
	
	<p><label for="_um_profile_max_width"><?php _e('Max. Width (px)','ultimate-member'); ?> <?php $this->tooltip('The maximum width of shortcode in pixels e.g. 600px', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_max_width', null, um_get_option('profile_max_width') ); ?>" name="_um_profile_max_width" id="_um_profile_max_width" />
	</p>
	
	<p><label for="_um_profile_area_max_width"><?php _e('Profile Area Max. Width (px)','ultimate-member'); ?> <?php $this->tooltip('The maximum width of the profile area inside profile (below profile header)', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_area_max_width', null, um_get_option('profile_area_max_width') ); ?>" name="_um_profile_area_max_width" id="_um_profile_area_max_width" />
	</p>
	
	<p><label for="_um_profile_align"><?php _e('Alignment','ultimate-member'); ?> <?php $this->tooltip('The shortcode is centered by default unless you specify otherwise here', 'e'); ?></label>
		<select name="_um_profile_align" id="_um_profile_align" class="umaf-selectjs" style="width: 100%">

			<option value="center" <?php selected('center', $ultimatemember->query->get_meta_value('_um_profile_align', null, um_get_option('profile_align') ) ); ?>>Centered</option>
			<option value="left" <?php selected('left', $ultimatemember->query->get_meta_value('_um_profile_align', null, um_get_option('profile_align') ) ); ?>>Left aligned</option>
			<option value="right" <?php selected('right', $ultimatemember->query->get_meta_value('_um_profile_align', null, um_get_option('profile_align') ) ); ?>>Right aligned</option>
			
		</select>
	</p>
	
	<p><label for="_um_profile_icons"><?php _e('Field Icons','ultimate-member'); ?> <?php $this->tooltip('Whether to show field icons and where to show them relative to the field', 'e'); ?></label>
		<select name="_um_profile_icons" id="_um_profile_icons" class="umaf-selectjs" style="width: 100%">

			<option value="field" <?php selected('field', $ultimatemember->query->get_meta_value('_um_profile_icons', null, um_get_option('profile_icons') ) ); ?>>Show inside text field</option>
			<option value="label" <?php selected('label', $ultimatemember->query->get_meta_value('_um_profile_icons', null, um_get_option('profile_icons') ) ); ?>>Show with label</option>
			<option value="off" <?php selected('off', $ultimatemember->query->get_meta_value('_um_profile_icons', null, um_get_option('profile_icons') ) ); ?>>Turn off</option>
			
		</select>
	</p>

	<p><label for="_um_profile_primary_btn_word"><?php _e('Primary Button Text','ultimate-member'); ?> <?php $this->tooltip('Customize the button text', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_primary_btn_word', null, um_get_option('profile_primary_btn_word') ); ?>" name="_um_profile_primary_btn_word" id="_um_profile_primary_btn_word" />
	</p>

	<p><label for="_um_profile_primary_btn_color"><?php _e('Primary Button Color','ultimate-member'); ?> <?php $this->tooltip('Override the default primary button color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_primary_btn_color', null, um_get_option('primary_btn_color') ); ?>" class="um-admin-colorpicker" name="_um_profile_primary_btn_color" id="_um_profile_primary_btn_color" data-default-color="<?php echo um_get_option('primary_btn_color'); ?>" />
	</p>
	
	<p><label for="_um_profile_primary_btn_hover"><?php _e('Primary Button Hover Color','ultimate-member'); ?> <?php $this->tooltip('Override the default primary button hover color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_primary_btn_hover', null, um_get_option('primary_btn_hover') ); ?>" class="um-admin-colorpicker" name="_um_profile_primary_btn_hover" id="_um_profile_primary_btn_hover" data-default-color="<?php echo um_get_option('primary_btn_hover'); ?>" />
	</p>
	
	<p><label for="_um_profile_primary_btn_text"><?php _e('Primary Button Text Color','ultimate-member'); ?> <?php $this->tooltip('Override the default primary button text color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_primary_btn_text', null, um_get_option('primary_btn_text') ); ?>" class="um-admin-colorpicker" name="_um_profile_primary_btn_text" id="_um_profile_primary_btn_text" data-default-color="<?php echo um_get_option('primary_btn_text'); ?>" />
	</p>
	
	<p>
		<label><?php _e('Show Secondary Button','ultimate-member'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_profile_secondary_btn', um_get_option('profile_secondary_btn'), true, 1, 'profile-secondary-btn', 'xxx'); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	<p class="profile-secondary-btn"><label for="_um_profile_secondary_btn_word"><?php _e('Secondary Button Text','ultimate-member'); ?> <?php $this->tooltip('Customize the button text', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_secondary_btn_word', null, um_get_option('profile_secondary_btn_word') ); ?>" name="_um_profile_secondary_btn_word" id="_um_profile_secondary_btn_word" />
	</p>
	
	<p class="profile-secondary-btn"><label for="_um_profile_secondary_btn_color"><?php _e('Secondary Button Color','ultimate-member'); ?> <?php $this->tooltip('Override the default secondary button color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_secondary_btn_color', null, um_get_option('secondary_btn_color') ); ?>" class="um-admin-colorpicker" name="_um_profile_secondary_btn_color" id="_um_profile_secondary_btn_color" data-default-color="<?php echo um_get_option('secondary_btn_color'); ?>" />
	</p>
	
	<p class="profile-secondary-btn"><label for="_um_profile_secondary_btn_hover"><?php _e('Secondary Button Hover Color','ultimate-member'); ?> <?php $this->tooltip('Override the default secondary button hover color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_secondary_btn_hover', null, um_get_option('secondary_btn_hover') ); ?>" class="um-admin-colorpicker" name="_um_profile_secondary_btn_hover" id="_um_profile_secondary_btn_hover" data-default-color="<?php echo um_get_option('secondary_btn_hover'); ?>" />
	</p>
	
	<p class="profile-secondary-btn"><label for="_um_profile_secondary_btn_text"><?php _e('Secondary Button Text Color','ultimate-member'); ?> <?php $this->tooltip('Override the default secondary button text color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_secondary_btn_text', null, um_get_option('secondary_btn_text') ); ?>" class="um-admin-colorpicker" name="_um_profile_secondary_btn_text" id="_um_profile_secondary_btn_text" data-default-color="<?php echo um_get_option('secondary_btn_text'); ?>" />
	</p>
	
	<p><label for="_um_profile_main_bg"><?php _e('Base Background Color','ultimate-member'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_main_bg', null, um_get_option('profile_main_bg') ); ?>" class="um-admin-colorpicker" name="_um_profile_main_bg" id="_um_profile_main_bg" data-default-color="<?php echo um_get_option('profile_main_bg'); ?>" />
	</p>

	<p><label for="_um_profile_main_text_color"><?php _e('Base Text Color','ultimate-member'); ?> <?php $this->tooltip('Override the default form text color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_main_text_color', null, um_get_option('profile_main_text_color') ); ?>" class="um-admin-colorpicker" name="_um_profile_main_text_color" id="_um_profile_main_text_color" data-default-color="<?php echo um_get_option('profile_main_text_color'); ?>" />
	</p>
	
	<p><label for="_um_profile_cover_enabled"><?php _e('Enable Cover Photos','ultimate-member'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_profile_cover_enabled', um_get_option('profile_cover_enabled') , true, 1, 'cover-photo-opts', 'xxx'); ?>
				
		</span>
	</p>

	<p class="cover-photo-opts"><label for="_um_profile_cover_ratio"><?php _e('Cover photo ratio','ultimate-member'); ?> <?php $this->tooltip('The shortcode is centered by default unless you specify otherwise here', 'e'); ?></label>
		<select name="_um_profile_cover_ratio" id="_um_profile_cover_ratio" class="umaf-selectjs" style="width: 100%">

			<option value="2.7:1" <?php selected('2.7:1', $ultimatemember->query->get_meta_value('_um_profile_cover_ratio', null, um_get_option('profile_cover_ratio') ) ); ?>>2.7:1</option>
			<option value="2.2:1" <?php selected('2.2:1', $ultimatemember->query->get_meta_value('_um_profile_cover_ratio', null, um_get_option('profile_cover_ratio') ) ); ?>>2.2:1</option>
			<option value="3.2:1" <?php selected('3.2:1', $ultimatemember->query->get_meta_value('_um_profile_cover_ratio', null, um_get_option('profile_cover_ratio') ) ); ?>>3.2:1</option>
			
		</select>
	</p>
	
	<p><label for="_um_profile_photosize"><?php _e('Profile Photo Size','ultimate-member'); ?> <?php $this->tooltip('Set the profile photo size in pixels here', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_photosize', null, um_get_option('profile_photosize') ); ?>" name="_um_profile_photosize" id="_um_profile_photosize" />
	</p>
	
	<p><label for="_um_profile_photocorner"><?php _e('Profile Photo Style','ultimate-member'); ?> <?php $this->tooltip('Control the roundness/appearance of profile photo in the header area', 'e'); ?></label>
		<select name="_um_profile_photocorner" id="_um_profile_photocorner" class="umaf-selectjs" style="width: 100%">

			<option value="1" <?php selected('1', $ultimatemember->query->get_meta_value('_um_profile_photocorner', null, um_get_option('profile_photocorner') ) ); ?>><?php _e('Circle','ultimate-member'); ?></option>
			<option value="2" <?php selected('2', $ultimatemember->query->get_meta_value('_um_profile_photocorner', null, um_get_option('profile_photocorner') ) ); ?>><?php _e('Rounded Corners','ultimate-member'); ?></option>
			<option value="3" <?php selected('3', $ultimatemember->query->get_meta_value('_um_profile_photocorner', null, um_get_option('profile_photocorner') ) ); ?>><?php _e('Square','ultimate-member'); ?></option>
			
		</select>
	</p>
       
	<p><label for="_um_profile_photo_required"><?php _e('Make Profile Photo Required','ultimate-member'); ?><?php $this->tooltip('Require user to update a profile photo when updating their profile', 'e'); ?></label>
	    <span>
	        
	        <?php $this->ui_on_off('_um_profile_photo_required'); ?>
	            
	    </span>
	</p>

	<p><label for="_um_profile_header_bg"><?php _e('Header Background Color','ultimate-member'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_header_bg', null, um_get_option('profile_header_bg') ); ?>" class="um-admin-colorpicker" name="_um_profile_header_bg" id="_um_profile_header_bg" data-default-color="<?php echo um_get_option('profile_header_bg'); ?>" />
	</p>
	
	<p><label for="_um_profile_header_text"><?php _e('Header Meta Text Color','ultimate-member'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_header_text', null, um_get_option('profile_header_text') ); ?>" class="um-admin-colorpicker" name="_um_profile_header_text" id="_um_profile_header_text" data-default-color="<?php echo um_get_option('profile_header_text'); ?>" />
	</p>
	
	<p><label for="_um_profile_header_link_color"><?php _e('Header Link Color','ultimate-member'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_header_link_color', null, um_get_option('profile_header_link_color') ); ?>" class="um-admin-colorpicker" name="_um_profile_header_link_color" id="_um_profile_header_link_color" data-default-color="<?php echo um_get_option('profile_header_link_color'); ?>" />
	</p>
	
	<p><label for="_um_profile_header_link_hcolor"><?php _e('Header Link Hover','ultimate-member'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_header_link_hcolor', null, um_get_option('profile_header_link_hcolor') ); ?>" class="um-admin-colorpicker" name="_um_profile_header_link_hcolor" id="_um_profile_header_link_hcolor" data-default-color="<?php echo um_get_option('profile_header_link_hcolor'); ?>" />
	</p>
	
	<p><label for="_um_profile_header_icon_color"><?php _e('Header Icon Link Color','ultimate-member'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_header_icon_color', null, um_get_option('profile_header_icon_color') ); ?>" class="um-admin-colorpicker" name="_um_profile_header_icon_color" id="_um_profile_header_icon_color" data-default-color="<?php echo um_get_option('profile_header_icon_color'); ?>" />
	</p>
	
	<p><label for="_um_profile_header_icon_hcolor"><?php _e('Header Icon Link Hover','ultimate-member'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_profile_header_icon_hcolor', null, um_get_option('profile_header_icon_hcolor') ); ?>" class="um-admin-colorpicker" name="_um_profile_header_icon_hcolor" id="_um_profile_header_icon_hcolor" data-default-color="<?php echo um_get_option('profile_header_icon_hcolor'); ?>" />
	</p>
	
	<p>
		<label><?php _e('Show display name in profile header?','ultimate-member'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_profile_show_name', 1 ); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	<p>
		<label><?php _e('Show social links in profile header?','ultimate-member'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_profile_show_social_links', 0 ); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	<p>
		<label><?php _e('Show user description in profile header?','ultimate-member'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_profile_show_bio', 1 ); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	</div>
	
</div>
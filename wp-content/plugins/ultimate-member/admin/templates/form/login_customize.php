<div class="um-admin-metabox">

	<p>
		<label><?php _e('Use global settings?','ultimate-member'); ?> <?php $this->tooltip('Switch to no if you want to customize this form settings, styling &amp; appearance', 'e'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_login_use_globals', 1, true, 1, 'xxx', 'login-customize'); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	<div class="login-customize">
	
	<p><label for="_um_login_template"><?php _e('Template','ultimate-member'); ?></label>
		<select name="_um_login_template" id="_um_login_template" class="umaf-selectjs" style="width: 100%">

			<?php foreach($ultimatemember->shortcodes->get_templates( 'login' ) as $key => $value) { ?>
			
			<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_login_template', null, um_get_option('login_template') ) ); ?>><?php echo $value; ?></option>
			
			<?php } ?>
			
		</select>
	</p>
	
	<p><label for="_um_login_max_width"><?php _e('Max. Width (px)','ultimate-member'); ?> <?php $this->tooltip('The maximum width of shortcode in pixels e.g. 600px', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_max_width', null, um_get_option('login_max_width') ); ?>" name="_um_login_max_width" id="_um_login_max_width" />
	</p>
	
	<p><label for="_um_login_align"><?php _e('Alignment','ultimate-member'); ?> <?php $this->tooltip('The shortcode is centered by default unless you specify otherwise here', 'e'); ?></label>
		<select name="_um_login_align" id="_um_login_align" class="umaf-selectjs" style="width: 100%">

			<option value="center" <?php selected('center', $ultimatemember->query->get_meta_value('_um_login_align', null, um_get_option('login_align') ) ); ?>>Centered</option>
			<option value="left" <?php selected('left', $ultimatemember->query->get_meta_value('_um_login_align', null, um_get_option('login_align') ) ); ?>>Left aligned</option>
			<option value="right" <?php selected('right', $ultimatemember->query->get_meta_value('_um_login_align', null, um_get_option('login_align') ) ); ?>>Right aligned</option>
			
		</select>
	</p>
	
	<p><label for="_um_login_icons"><?php _e('Field Icons','ultimate-member'); ?> <?php $this->tooltip('Whether to show field icons and where to show them relative to the field', 'e'); ?></label>
		<select name="_um_login_icons" id="_um_login_icons" class="umaf-selectjs" style="width: 100%">

			<option value="field" <?php selected('field', $ultimatemember->query->get_meta_value('_um_login_icons', null, um_get_option('login_icons') ) ); ?>>Show inside text field</option>
			<option value="label" <?php selected('label', $ultimatemember->query->get_meta_value('_um_login_icons', null, um_get_option('login_icons') ) ); ?>>Show with label</option>
			<option value="off" <?php selected('off', $ultimatemember->query->get_meta_value('_um_login_icons', null, um_get_option('login_icons') ) ); ?>>Turn off</option>
			
		</select>
	</p>
	
	<p><label for="_um_login_primary_btn_word"><?php _e('Primary Button Text','ultimate-member'); ?> <?php $this->tooltip('Customize the button text', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_primary_btn_word', null, um_get_option('login_primary_btn_word') ); ?>" name="_um_login_primary_btn_word" id="_um_login_primary_btn_word" />
	</p>

	<p><label for="_um_login_primary_btn_color"><?php _e('Primary Button Color','ultimate-member'); ?> <?php $this->tooltip('Override the default primary button color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_primary_btn_color', null, um_get_option('primary_btn_color') ); ?>" class="um-admin-colorpicker" name="_um_login_primary_btn_color" id="_um_login_primary_btn_color" data-default-color="<?php echo um_get_option('primary_btn_color'); ?>" />
	</p>
	
	<p><label for="_um_login_primary_btn_hover"><?php _e('Primary Button Hover Color','ultimate-member'); ?> <?php $this->tooltip('Override the default primary button hover color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_primary_btn_hover', null, um_get_option('primary_btn_hover') ); ?>" class="um-admin-colorpicker" name="_um_login_primary_btn_hover" id="_um_login_primary_btn_hover" data-default-color="<?php echo um_get_option('primary_btn_hover'); ?>" />
	</p>
	
	<p><label for="_um_login_primary_btn_text"><?php _e('Primary Button Text Color','ultimate-member'); ?> <?php $this->tooltip('Override the default primary button text color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_primary_btn_text', null, um_get_option('primary_btn_text') ); ?>" class="um-admin-colorpicker" name="_um_login_primary_btn_text" id="_um_login_primary_btn_text" data-default-color="<?php echo um_get_option('primary_btn_text'); ?>" />
	</p>
	
	<p>
		<label><?php _e('Show Secondary Button','ultimate-member'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_login_secondary_btn', um_get_option('login_secondary_btn'), true, 1, 'login-secondary-btn', 'xxx'); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	<p class="login-secondary-btn"><label for="_um_login_secondary_btn_word"><?php _e('Secondary Button Text','ultimate-member'); ?> <?php $this->tooltip('Customize the button text', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_secondary_btn_word', null, um_get_option('login_secondary_btn_word') ); ?>" name="_um_login_secondary_btn_word" id="_um_login_secondary_btn_word" />
	</p>
	
	<p class="login-secondary-btn"><label for="_um_login_secondary_btn_color"><?php _e('Secondary Button Color','ultimate-member'); ?> <?php $this->tooltip('Override the default secondary button color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_secondary_btn_color', null, um_get_option('secondary_btn_color') ); ?>" class="um-admin-colorpicker" name="_um_login_secondary_btn_color" id="_um_login_secondary_btn_color" data-default-color="<?php echo um_get_option('secondary_btn_color'); ?>" />
	</p>
	
	<p class="login-secondary-btn"><label for="_um_login_secondary_btn_hover"><?php _e('Secondary Button Hover Color','ultimate-member'); ?> <?php $this->tooltip('Override the default secondary button hover color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_secondary_btn_hover', null, um_get_option('secondary_btn_hover') ); ?>" class="um-admin-colorpicker" name="_um_login_secondary_btn_hover" id="_um_login_secondary_btn_hover" data-default-color="<?php echo um_get_option('secondary_btn_hover'); ?>" />
	</p>
	
	<p class="login-secondary-btn"><label for="_um_login_secondary_btn_text"><?php _e('Secondary Button Text Color','ultimate-member'); ?> <?php $this->tooltip('Override the default secondary button text color', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_login_secondary_btn_text', null, um_get_option('secondary_btn_text') ); ?>" class="um-admin-colorpicker" name="_um_login_secondary_btn_text" id="_um_login_secondary_btn_text" data-default-color="<?php echo um_get_option('secondary_btn_text'); ?>" />
	</p>
	
	<p>
		<label><?php _e('Show Forgot Password Link?','ultimate-member'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_login_forgot_pass_link', um_get_option('login_forgot_pass_link') ); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	<p>
		<label><?php _e('Show "Remember Me"?','ultimate-member'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_login_show_rememberme', um_get_option('login_show_rememberme') ); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	</div>
	
</div>
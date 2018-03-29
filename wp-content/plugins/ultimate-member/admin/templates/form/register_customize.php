<div class="um-admin-metabox">

	<p>
		<label><?php _e('Use global settings?','ultimate-member'); ?> <?php $this->tooltip( __('Switch to no if you want to customize this form settings, styling &amp; appearance','ultimate-member'), 'e'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_register_use_globals', 1, true, 1, 'xxx', 'register-customize'); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	<div class="register-customize">
	
	<p><label for="_um_register_role"><?php _e('Assign role to form','ultimate-member'); ?></label>
		<select name="_um_register_role" id="_um_register_role" class="umaf-selectjs" style="width: 100%">
			
			<?php foreach($ultimatemember->query->get_roles( $add_default = 'Default' ) as $key => $value) { ?>
			
			<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_register_role', null, um_get_option('register_role') ) ); ?>><?php echo $value; ?></option>
			
			<?php } ?>
			
		</select>
	</p>
	
	<p><label for="_um_register_template"><?php _e('Template','ultimate-member'); ?></label>
		<select name="_um_register_template" id="_um_register_template" class="umaf-selectjs" style="width: 100%">

			<?php foreach($ultimatemember->shortcodes->get_templates( 'register' ) as $key => $value) { ?>
			
			<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_register_template', null, um_get_option('register_template') ) ); ?>><?php echo $value; ?></option>
			
			<?php } ?>
			
		</select>
	</p>
	
	<p><label for="_um_register_max_width"><?php _e('Max. Width (px)','ultimate-member'); ?> <?php $this->tooltip('The maximum width of shortcode in pixels e.g. 600px', 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_register_max_width', null, um_get_option('register_max_width') ); ?>" name="_um_register_max_width" id="_um_register_max_width" />
	</p>
	
	<p><label for="_um_register_align"><?php _e('Alignment','ultimate-member'); ?> <?php $this->tooltip( __('The shortcode is centered by default unless you specify otherwise here','ultimate-member'), 'e'); ?></label>
		<select name="_um_register_align" id="_um_register_align" class="umaf-selectjs" style="width: 100%">

			<option value="center" <?php selected('center', $ultimatemember->query->get_meta_value('_um_register_align', null, um_get_option('register_align') ) ); ?>>Centered</option>
			<option value="left" <?php selected('left', $ultimatemember->query->get_meta_value('_um_register_align', null, um_get_option('register_align') ) ); ?>>Left aligned</option>
			<option value="right" <?php selected('right', $ultimatemember->query->get_meta_value('_um_register_align', null, um_get_option('register_align') ) ); ?>>Right aligned</option>
			
		</select>
	</p>
	
	<p><label for="_um_register_icons"><?php _e('Field Icons','ultimate-member'); ?> <?php $this->tooltip( __('Whether to show field icons and where to show them relative to the field','ultimate-member'), 'e'); ?></label>
		<select name="_um_register_icons" id="_um_register_icons" class="umaf-selectjs" style="width: 100%">

			<option value="field" <?php selected('field', $ultimatemember->query->get_meta_value('_um_register_icons', null, um_get_option('register_icons') ) ); ?>>Show inside text field</option>
			<option value="label" <?php selected('label', $ultimatemember->query->get_meta_value('_um_register_icons', null, um_get_option('register_icons') ) ); ?>>Show with label</option>
			<option value="off" <?php selected('off', $ultimatemember->query->get_meta_value('_um_register_icons', null, um_get_option('register_icons') ) ); ?>>Turn off</option>
			
		</select>
	</p>
	
	<p><label for="_um_register_primary_btn_word"><?php _e('Primary Button Text','ultimate-member'); ?> <?php $this->tooltip( __('Customize the button text','ultimate-member'), 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_register_primary_btn_word', null, um_get_option('register_primary_btn_word') ); ?>" name="_um_register_primary_btn_word" id="_um_register_primary_btn_word" />
	</p>

	<p><label for="_um_register_primary_btn_color"><?php _e('Primary Button Color','ultimate-member'); ?> <?php $this->tooltip(__('Override the default primary button color','ultimate-member'), 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_register_primary_btn_color', null, um_get_option('primary_btn_color') ); ?>" class="um-admin-colorpicker" name="_um_register_primary_btn_color" id="_um_register_primary_btn_color" data-default-color="<?php echo um_get_option('primary_btn_color'); ?>" />
	</p>
	
	<p><label for="_um_register_primary_btn_hover"><?php _e('Primary Button Hover Color','ultimate-member'); ?> <?php $this->tooltip(__('Override the default primary button hover color','ultimate-member'), 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_register_primary_btn_hover', null, um_get_option('primary_btn_hover') ); ?>" class="um-admin-colorpicker" name="_um_register_primary_btn_hover" id="_um_register_primary_btn_hover" data-default-color="<?php echo um_get_option('primary_btn_hover'); ?>" />
	</p>
	
	<p><label for="_um_register_primary_btn_text"><?php _e('Primary Button Text Color','ultimate-member'); ?> <?php $this->tooltip(__('Override the default primary button text color','ultimate-member'), 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_register_primary_btn_text', null, um_get_option('primary_btn_text') ); ?>" class="um-admin-colorpicker" name="_um_register_primary_btn_text" id="_um_register_primary_btn_text" data-default-color="<?php echo um_get_option('primary_btn_text'); ?>" />
	</p>
	
	<p>
		<label><?php _e('Show Secondary Button','ultimate-member'); ?></label>
		<span>
			
			<?php $this->ui_on_off('_um_register_secondary_btn', um_get_option('register_secondary_btn'), true, 1, 'register-secondary-btn', 'xxx'); ?>
				
		</span>
	</p><div class="um-admin-clear"></div>
	
	<p class="register-secondary-btn"><label for="_um_register_secondary_btn_word"><?php _e('Secondary Button Text','ultimate-member'); ?> <?php $this->tooltip( __('Customize the button text','ultimate-member'), 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_register_secondary_btn_word', null, um_get_option('register_secondary_btn_word') ); ?>" name="_um_register_secondary_btn_word" id="_um_register_secondary_btn_word" />
	</p>
	
	<p class="register-secondary-btn"><label for="_um_register_secondary_btn_color"><?php _e('Secondary Button Color','ultimate-member'); ?> <?php $this->tooltip( __('Override the default secondary button color','ultimate-member'), 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_register_secondary_btn_color', null, um_get_option('secondary_btn_color') ); ?>" class="um-admin-colorpicker" name="_um_register_secondary_btn_color" id="_um_register_secondary_btn_color" data-default-color="<?php echo um_get_option('secondary_btn_color'); ?>" />
	</p>
	
	<p class="register-secondary-btn"><label for="_um_register_secondary_btn_hover"><?php _e('Secondary Button Hover Color','ultimate-member'); ?> <?php $this->tooltip( __('Override the default secondary button hover color','ultimate-member'), 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_register_secondary_btn_hover', null, um_get_option('secondary_btn_hover') ); ?>" class="um-admin-colorpicker" name="_um_register_secondary_btn_hover" id="_um_register_secondary_btn_hover" data-default-color="<?php echo um_get_option('secondary_btn_hover'); ?>" />
	</p>
	
	<p class="register-secondary-btn"><label for="_um_register_secondary_btn_text"><?php _e('Secondary Button Text Color','ultimate-member'); ?> <?php $this->tooltip( __('Override the default secondary button text color','ultimate-member'), 'e'); ?></label>
		<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_register_secondary_btn_text', null, um_get_option('secondary_btn_text') ); ?>" class="um-admin-colorpicker" name="_um_register_secondary_btn_text" id="_um_register_secondary_btn_text" data-default-color="<?php echo um_get_option('secondary_btn_text'); ?>" />
	</p>
	
	</div>

</div>
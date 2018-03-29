<div class="um-admin-metabox">

	<p><label for="_um_directory_template"><?php _e('Template','ultimate-member'); ?></label>
		<select name="_um_directory_template" id="_um_directory_template" class="umaf-selectjs" style="width: 100%">

			<?php foreach($ultimatemember->shortcodes->get_templates( 'members' ) as $key => $value) { ?>
			
			<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_directory_template', null, um_get_option('directory_template') ) ); ?>><?php echo $value; ?></option>
			
			<?php } ?>
			
		</select>
	</p>
	
</div>
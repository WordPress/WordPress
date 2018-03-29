<div class="um-admin-metabox">

	<div class="">
		
		<p>
			<label class="um-admin-half"><?php _e('Enable Profile Photo','ultimate-member'); ?></label>
			<span class="um-admin-half">
			
				<?php $this->ui_on_off('_um_profile_photo', 1); ?>
				
			</span>
		</p><div class="um-admin-clear"></div>
		
		<p>
			<label class="um-admin-half"><?php _e('Enable Cover Photo','ultimate-member'); ?> <?php $this->tooltip('If turned on, the users cover photo will appear in the directory'); ?></label>
			<span class="um-admin-half">
			
				<?php $this->ui_on_off('_um_cover_photos', 1); ?>
				
			</span>
		</p><div class="um-admin-clear"></div>
		
		<p>
			<label class="um-admin-half"><?php _e('Show display name','ultimate-member'); ?></label>
			<span class="um-admin-half">
			
				<?php $this->ui_on_off('_um_show_name', 1, true, 1, 'name-options', 'xxx'); ?>
				
			</span>
		</p><div class="um-admin-clear"></div>
		
		<p>
			<label class="um-admin-half"><?php _e('Show tagline below profile name','ultimate-member'); ?></label>
			<span class="um-admin-half">
			
				<?php $this->ui_on_off('_um_show_tagline', 0, true, 1, 'tagline-options', 'xxx'); ?>
				
			</span>
		</p><div class="um-admin-clear"></div>
		
		<p class="tagline-options">
			<label class=""><?php _e('Choose field(s) to display in tagline','ultimate-member'); ?></label>
				
				<?php
				
				$meta_test = get_post_meta( get_the_ID(), '_um_tagline_fields', true );
				$i = 0;
				if ( is_array( $meta_test ) ) { 
					foreach( $meta_test as $val ) { $i++;
				?>
				
				<span class="um-admin-field">
				
				<select name="_um_tagline_fields[]" id="_um_tagline_fields" class="umaf-selectjs" style="width: 300px" data-placeholder="Choose a field">
					<?php foreach($ultimatemember->builtin->all_user_fields() as $key => $arr) { ?>
					<option value="<?php echo $key; ?>" <?php selected($key, $val ); ?>><?php echo isset( $arr['title'] ) ? $arr['title'] : ''; ?></option>
					<?php } ?>	
				</select>
				
				<?php if ( $i == 1 ) { ?>
				<a href="#" class="um-admin-clone button um-admin-tipsy-n" title="New Field"><i class="um-icon-plus" style="margin-right:0!important"></i></a>
				<?php } else { ?>
				<a href="#" class="um-admin-clone-remove button um-admin-tipsy-n" title="Remove Field"><i class="um-icon-close" style="margin-right:0!important"></i></a>
				<?php } ?>
				
				</span>
				
				<?php }
				
				} else {
				?>
			
				<span class="um-admin-field">
				
				<select name="_um_tagline_fields[]" id="_um_tagline_fields" class="umaf-selectjs" style="width: 300px" data-placeholder="Choose a field">
					<?php foreach($ultimatemember->builtin->all_user_fields() as $key => $arr) { ?>
					<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_tagline_fields', $key) ); ?>><?php echo isset( $arr['title'] ) ? $arr['title'] : ''; ?></option>
					<?php } ?>	
				</select>
				
				<a href="#" class="um-admin-clone button um-admin-tipsy-n" title="New Field"><i class="um-icon-plus" style="margin-right:0!important"></i></a>
				
				</span>
				
				<?php } ?>

		</p><div class="um-admin-clear"></div>
		
		<p>
			<label class="um-admin-half"><?php _e('Show extra user information below tagline?','ultimate-member'); ?></label>
			<span class="um-admin-half">
			
				<?php $this->ui_on_off('_um_show_userinfo', 0, true, 1, 'reveal-options', 'xxx'); ?>
				
			</span>
		</p><div class="um-admin-clear"></div>
		
		<p class="reveal-options">
			<label class="um-admin-half"><?php _e('Enable reveal section transition by default','ultimate-member'); ?></label>
			<span class="um-admin-half">
			
				<?php $this->ui_on_off('_um_userinfo_animate', 1); ?>
				
			</span>
		</p><div class="um-admin-clear"></div>
		
		<p class="reveal-options">
			<label class=""><?php _e('Choose field(s) to display in reveal section','ultimate-member'); ?></label>
				
				<?php
				
				$meta_test = get_post_meta( get_the_ID(), '_um_reveal_fields', true );
				$i = 0;
				if ( is_array( $meta_test ) ) { 
					foreach( $meta_test as $val ) { $i++;
				?>
				
				<span class="um-admin-field">
				
				<select name="_um_reveal_fields[]" id="_um_reveal_fields" class="umaf-selectjs" style="width: 300px" data-placeholder="Choose a field">
					<?php foreach($ultimatemember->builtin->all_user_fields() as $key => $arr) { ?>
					<option value="<?php echo $key; ?>" <?php selected($key, $val ); ?>><?php echo isset( $arr['title'] ) ? $arr['title'] : ''; ?></option>
					<?php } ?>	
				</select>
				
				<?php if ( $i == 1 ) { ?>
				<a href="#" class="um-admin-clone button um-admin-tipsy-n" title="New Field"><i class="um-icon-plus" style="margin-right:0!important"></i></a>
				<?php } else { ?>
				<a href="#" class="um-admin-clone-remove button um-admin-tipsy-n" title="Remove Field"><i class="um-icon-close" style="margin-right:0!important"></i></a>
				<?php } ?>
				
				</span>
				
				<?php }
				
				} else {
				?>
			
				<span class="um-admin-field">
				
				<select name="_um_reveal_fields[]" id="_um_reveal_fields" class="umaf-selectjs" style="width: 300px" data-placeholder="Choose a field">
					<?php foreach($ultimatemember->builtin->all_user_fields() as $key => $arr) { ?>
					<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_reveal_fields', $key) ); ?>><?php echo isset( $arr['title'] ) ? $arr['title'] : ''; ?></option>
					<?php } ?>	
				</select>
				
				<a href="#" class="um-admin-clone button um-admin-tipsy-n" title="New Field"><i class="um-icon-plus" style="margin-right:0!important"></i></a>
				
				</span>
				
				<?php } ?>

		</p><div class="um-admin-clear"></div>
		
		<p class="reveal-options">
			<label class="um-admin-half"><?php _e('Show social connect icons','ultimate-member'); ?></label>
			<span class="um-admin-half">
			
				<?php $this->ui_on_off('_um_show_social', 0); ?>
				
			</span>
		</p><div class="um-admin-clear"></div>

	</div>
	
	<div class="um-admin-clear"></div>
	
</div>
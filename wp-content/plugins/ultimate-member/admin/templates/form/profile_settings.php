<div class="um-admin-metabox">

	<p>
			<label class=""><?php _e('Field(s) to show in user meta','ultimate-member'); ?></label>
				
				<?php
				
				$meta_test = get_post_meta( get_the_ID(), '_um_profile_metafields', true );
				$i = 0;
				if ( is_array( $meta_test ) ) { 
					foreach( $meta_test as $val ) { $i++;
				?>
				
				<span class="um-admin-field">
				
				<select name="_um_profile_metafields[]" id="_um_profile_metafields" class="umaf-selectjs" style="width: 200px" data-placeholder="Choose a field">
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
				
				<select name="_um_profile_metafields[]" id="_um_profile_metafields" class="umaf-selectjs" style="width: 200px" data-placeholder="Choose a field">
					<?php foreach($ultimatemember->builtin->all_user_fields() as $key => $arr) { ?>
					<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_profile_metafields', $key) ); ?>><?php echo isset( $arr['title'] ) ? $arr['title'] : ''; ?></option>
					<?php } ?>	
				</select>
				
				<a href="#" class="um-admin-clone button um-admin-tipsy-n" title="New Field"><i class="um-icon-plus" style="margin-right:0!important"></i></a>
				
				</span>
				
				<?php } ?>

	</p><div class="um-admin-clear"></div>
	
</div>
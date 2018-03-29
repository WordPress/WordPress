<div class="um-admin-metabox">

	<div class="">
		
		<?php if ( $ultimatemember->query->has_post_meta('_um_core', 'admin' ) ) { ?>
		<p class="disabled-on-off">
			<label class="um-admin-half"><?php _e('Can access wp-admin?','ultimate-member'); ?> <?php $this->tooltip( __('The core admin role must always have access to wp-admin / WordPress backend','ultimate-member') ); ?></label>
			<span class="um-admin-half"><?php $this->ui_on_off('_um_can_access_wpadmin', 1); ?></span>
		</p><div class="um-admin-clear"></div>
		<p>
			<label class="um-admin-half"><?php _e('Force hiding adminbar in frontend?','ultimate-member'); ?> <?php $this->tooltip( __('Show/hide the adminbar on frontend','ultimate-member') ); ?></label>
			<span class="um-admin-half"><?php $this->ui_on_off('_um_can_not_see_adminbar', 0); ?></span>
		</p><div class="um-admin-clear"></div>
		<?php } else { ?>
		<p>
			<label class="um-admin-half"><?php _e('Can access wp-admin?','ultimate-member'); ?> <?php $this->tooltip( __('Allow this role to access the admin dashboard. If turned on the WordPress toolbar will appear at top of the page.','ultimate-member') ); ?></label>
			<span class="um-admin-half"><?php $this->ui_on_off('_um_can_access_wpadmin', 0); ?></span>
		</p><div class="um-admin-clear"></div>
		<p>
			<label class="um-admin-half"><?php _e('Force hiding adminbar in frontend?','ultimate-member'); ?> <?php $this->tooltip( __('Show/hide the adminbar on frontend','ultimate-member') ); ?></label>
			<span class="um-admin-half"><?php $this->ui_on_off('_um_can_not_see_adminbar', 1); ?></span>
		</p><div class="um-admin-clear"></div>
		<?php } ?>

		<p>
			<label class="um-admin-half"><?php _e('Can edit other member accounts?','ultimate-member'); ?> <?php $this->tooltip( __('Allow this role to edit accounts of other members','ultimate-member') ); ?></label>
			<span class="um-admin-half"><?php $this->ui_on_off('_um_can_edit_everyone', 0, true, 1, 'edit-roles', 'xxx'); ?></span>
		</p><div class="um-admin-clear"></div>

		<p class="edit-roles">
			<label class="um-admin-half"><?php _e('Can edit these user roles only','ultimate-member'); ?> <?php $this->tooltip( __('Which roles that role can edit, choose none to allow role to edit all member roles','ultimate-member') ); ?></label>
			<span class="um-admin-half">
		
				<select multiple="multiple" name="_um_can_edit_roles[]" id="_um_can_edit_roles" class="umaf-selectjs" style="width: 300px">
					<?php foreach($ultimatemember->query->get_roles() as $key => $value) { ?>
					<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_can_edit_roles', $key) ); ?>><?php echo $value; ?></option>
					<?php } ?>	
				</select>
			
			</span>
		</p><div class="um-admin-clear"></div>

		<p>
			<label class="um-admin-half"><?php _e('Can delete other member accounts?','ultimate-member'); ?> <?php $this->tooltip( __('Allow this role to edit the profile fields of certain roles only','ultimate-member') ); ?></label>
			<span class="um-admin-half"><?php $this->ui_on_off('_um_can_delete_everyone', 0, true, 1, 'delete-roles', 'xxx'); ?></span>
		</p><div class="um-admin-clear"></div>
		
		<p class="delete-roles">
			<label class="um-admin-half"><?php _e('Can delete these user roles only','ultimate-member'); ?> <?php $this->tooltip( __('Which roles that role can delete, choose none to allow role to delete all member roles','ultimate-member') ); ?></label>
			<span class="um-admin-half">
		
				<select multiple="multiple" name="_um_can_delete_roles[]" id="_um_can_delete_roles" class="umaf-selectjs" style="width: 300px">
					<?php foreach($ultimatemember->query->get_roles() as $key => $value) { ?>
					<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_can_delete_roles', $key) ); ?>><?php echo $value; ?></option>
					<?php } ?>	
				</select>
			
			</span>
		</p><div class="um-admin-clear"></div>
		
	</div>
	
	<div class="um-admin-clear"></div>
	
</div>
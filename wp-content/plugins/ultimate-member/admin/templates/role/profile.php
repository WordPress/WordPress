<div class="um-admin-metabox">

	<div class="">
		
		<p>
			<label class="um-admin-half"><?php _e('Can view other member profiles?','ultimate-member'); ?> <?php $this->tooltip( __('Can this role view all member profiles?', 'ultimate-member') ); ?></label>
			<span class="um-admin-half"><?php $this->ui_on_off('_um_can_view_all', 1, true, 1, 'view-roles', 'xxx'); ?></span>
		</p><div class="um-admin-clear"></div>
		
		<p class="view-roles">
			<label class="um-admin-half"><?php _e('Can view these user roles only','ultimate-member'); ?> <?php $this->tooltip( __('Which roles that role can view, choose none to allow role to view all member roles', 'ultimate-member') ); ?></label>
			<span class="um-admin-half">
		
				<select multiple="multiple" name="_um_can_view_roles[]" id="_um_can_view_roles" class="umaf-selectjs" style="width: 300px">
					<?php foreach($ultimatemember->query->get_roles() as $key => $value) { ?>
					<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_can_view_roles', $key) ); ?>><?php echo $value; ?></option>
					<?php } ?>	
				</select>
			
			</span>
		</p><div class="um-admin-clear"></div>
	
		<p>
			<label class="um-admin-half"><?php _e('Can make their profile private?','ultimate-member'); ?> <?php $this->tooltip( __('Can this role make their profile private?','ultimate-member') ); ?></label>
			<span class="um-admin-half"><?php $this->ui_on_off('_um_can_make_private_profile'); ?></span>
		</p><div class="um-admin-clear"></div>
		
		<p>
			<label class="um-admin-half"><?php _e('Can view/access private profiles?','ultimate-member'); ?> <?php $this->tooltip( __('Can this role view private profiles?','ultimate-member') ); ?></label>
			<span class="um-admin-half"><?php $this->ui_on_off('_um_can_access_private_profile'); ?></span>
		</p><div class="um-admin-clear"></div>
		
	</div>
	
	<div class="um-admin-clear"></div>
	
</div>
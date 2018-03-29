<?php

	$meta = get_post_custom( get_the_ID() );
	foreach( $meta as $k => $v ) {
		if ( strstr( $k, '_um_' ) && !is_array( $v[0] ) ) {
			//print "'$k' => '" . $v[0] . "',<br />";
		}
	}

	$show_these_users = get_post_meta( get_the_ID(), '_um_show_these_users', true );
	if ( $show_these_users ) {
		$show_these_users = implode("\n", str_replace("\r", "", $show_these_users));
	}

?>

<div class="um-admin-metabox">

	<div class="">

		<input type="hidden" name="_um_mode" id="_um_mode" value="directory" />

		<p>
			<label class="um-admin-half"><?php _e('User Roles to Display','ultimate-member'); ?> <?php $this->tooltip('If you do not want to show all members, select only user roles to appear in this directory'); ?></label>
			<span class="um-admin-half">

				<select multiple="multiple" name="_um_roles[]" id="_um_roles" class="umaf-selectjs" style="width: 300px">
					<?php foreach($ultimatemember->query->get_roles() as $key => $value) { ?>
					<option value="<?php echo $key; ?>" <?php selected($key, $ultimatemember->query->get_meta_value('_um_roles', $key ) ); ?>><?php echo $value; ?></option>
					<?php } ?>
				</select>

			</span>
		</p><div class="um-admin-clear"></div>

		<p>
			<label class="um-admin-half"><?php _e('Only show members who have uploaded a profile photo','ultimate-member'); ?><?php $this->tooltip('If \'Use Gravatars\' as profile photo is enabled, this option is ignored'); ?></label>
			<span class="um-admin-half">

				<?php $this->ui_on_off('_um_has_profile_photo'); ?>

			</span>
		</p><div class="um-admin-clear"></div>

		<p>
			<label class="um-admin-half"><?php _e('Only show members who have uploaded a cover photo','ultimate-member'); ?></label>
			<span class="um-admin-half">

				<?php $this->ui_on_off('_um_has_cover_photo'); ?>

			</span>
		</p><div class="um-admin-clear"></div>

		<p>
			<label class="um-admin-half"><?php _e('Sort users by','ultimate-member'); ?> <?php $this->tooltip('Sort users by a specific parameter in the directory'); ?></label>
			<span class="um-admin-half">

				<select name="_um_sortby" id="_um_sortby" class="umaf-selectjs um-adm-conditional" style="width: 300px" data-cond1='other' data-cond1-show='custom-field'>
					<option value="user_registered_desc" <?php selected('user_registered_desc', $ultimatemember->query->get_meta_value('_um_sortby') ); ?>><?php _e('New users first','ultimate-member'); ?></option>
					<option value="user_registered_asc" <?php selected('user_registered_asc', $ultimatemember->query->get_meta_value('_um_sortby') ); ?>><?php _e('Old users first','ultimate-member'); ?></option>
					<option value="last_login" <?php selected('last_login', $ultimatemember->query->get_meta_value('_um_sortby') ); ?>><?php _e('Last login','ultimate-member'); ?></option>
					<option value="display_name" <?php selected('display_name', $ultimatemember->query->get_meta_value('_um_sortby') ); ?>><?php _e('Display Name','ultimate-member'); ?></option>
					<option value="first_name" <?php selected('first_name', $ultimatemember->query->get_meta_value('_um_sortby') ); ?>><?php _e('First Name','ultimate-member'); ?></option>
					<option value="last_name" <?php selected('last_name', $ultimatemember->query->get_meta_value('_um_sortby') ); ?>><?php _e('Last Name','ultimate-member'); ?></option>
					<option value="random" <?php selected('random', $ultimatemember->query->get_meta_value('_um_sortby') ); ?>><?php _e('Random','ultimate-member'); ?></option>
					<option value="other" <?php selected('other', $ultimatemember->query->get_meta_value('_um_sortby') ); ?>><?php _e('Other (custom field)','ultimate-member'); ?></option>
					<?php do_action('um_admin_directory_sort_users_select', '_um_sortby'); ?>
				</select>

			</span>
		</p><div class="um-admin-clear"></div>

		<p class="custom-field">
			<label class="um-admin-half"><?php _e('Meta key','ultimate-member'); ?> <?php $this->tooltip('To sort by a custom field, enter the meta key of field here'); ?></label>
			<span class="um-admin-half">

				<input type="text" name="_um_sortby_custom" id="_um_sortby_custom" value="<?php echo $ultimatemember->query->get_meta_value('_um_sortby_custom', null, 'na' ); ?>" />

			</span>
		</p><div class="um-admin-clear"></div>

		<p>
			<label class="um-admin-half"><?php _e('Only show specific users (Enter one username per line)','ultimate-member'); ?></label>
			<span class="um-admin-half">

				<textarea name="_um_show_these_users" id="_um_show_these_users"><?php echo $show_these_users; ?></textarea>

			</span>
		</p><div class="um-admin-clear"></div>

		<?php do_action('um_admin_extend_directory_options_general', $this); ?>

	</div>

	<div class="um-admin-clear"></div>

</div>

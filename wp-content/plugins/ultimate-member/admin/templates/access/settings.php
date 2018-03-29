	<?php

		$metabox = new UM_Admin_Metabox();

		do_action('um_admin_before_access_settings', $metabox);

	?>

	<h4><?php _e('Apply custom access settings?','ultimate-member'); ?> <?php $this->tooltip( __('Switch to yes to override global access settings','ultimate-member'), 'e'); ?></h4>

	<p>
		<span><?php $metabox->ui_on_off('_um_custom_access_settings', 0, true, 1, '_um_custom_access_settings', 'xxx'); ?>	</span>
	</p>

	<div class="_um_custom_access_settings">

		<h4><?php _e('Content Availability','ultimate-member'); ?> <?php $this->tooltip( __('Who can access this content?','ultimate-member'), 'e'); ?></h4>

		<p class="um-conditional-radio-group description" data-cond1="2" data-cond1-show="um-admin-access-roles" data-cond2="1" data-cond2-show="um-admin-access-loggedout">

			<?php $value = get_post_meta($post->ID, '_um_accessible', true); ?>

			<label><input type="radio" name="_um_accessible" value="0" <?php if (!isset($value) || $value == 0 ) echo 'checked="checked"'; ?> /> <?php _e('Content accessible to Everyone','ultimate-member'); ?></label><br />
			<label><input type="radio" name="_um_accessible" value="1" <?php if (isset($value)) checked(1, $value); ?> /> <?php _e('Content accessible to Logged Out Users','ultimate-member'); ?></label><br />
			<label><input type="radio" name="_um_accessible" value="2" <?php if (isset($value)) checked(2, $value); ?> /> <?php _e('Content accessible to Logged In Users','ultimate-member'); ?></label>

			<?php do_action( 'um_admin_extend_access_settings' ); ?>

		</p>

		<div class="um-admin-access-loggedout">

			<h4><label for="_um_access_redirect2"><?php _e('Redirect URL','ultimate-member'); ?></label> <?php $this->tooltip( __('This is the URL that user is redirected to If he is not permitted to view this content','ultimate-member'), 'e'); ?></h4>

			<p class="description">

				<?php $value = get_post_meta($post->ID, '_um_access_redirect2', true); ?>

				<input type="text" name="_um_access_redirect2" id="_um_access_redirect2" value="<?php if ( isset( $value ) ) echo $value; ?>" class="widefat" />

			</p>

		</div>

		<div class="um-admin-access-roles">

			<h4><?php _e('Select the member roles that can see this content?','ultimate-member'); ?> <?php $this->tooltip( __('If you do not select any role, all members will be able to view this content','ultimate-member'), 'e'); ?></h4>

			<p class="description">

				<?php $value = get_post_meta($post->ID, '_um_access_roles', true); ?>

				<input type="hidden" name="_um_access_roles[]" value="0">
				<?php foreach($ultimatemember->query->get_roles() as $role_id => $role) { ?>
				<label><input type="checkbox" name="_um_access_roles[]" value="<?php echo $role_id; ?>" <?php if (  ( isset( $value ) && is_array( $value ) && in_array($role_id, $value ) ) || ( isset( $value ) && $role_id == $value ) ) echo 'checked="checked"'; ?> /> <?php echo $role; ?></label><br />
				<?php } ?>

			</p>

			<h4><label for="_um_access_redirect"><?php _e('Redirect URL','ultimate-member'); ?></label> <?php $this->tooltip( __('This is the URL that user is redirected to If he is not permitted to view this content','ultimate-member'), 'e'); ?></h4>

			<p class="description">

				<?php $value = get_post_meta($post->ID, '_um_access_redirect', true); ?>

				<input type="text" name="_um_access_redirect" id="_um_access_redirect" value="<?php if ( isset( $value ) ) echo $value; ?>" class="widefat" />

			</p>

		</div>

	</div>

<div class="um-admin-metabox">

	<div class="">

		<p>
			<label class="um-admin-half"><?php _e('Registration Status','ultimate-member'); ?> <?php $this->tooltip( __('Select the status you would like this user role to have after they register on your site','ultimate-member') ); ?></label>
			<span class="um-admin-half">

				<select name="_um_status" id="_um_status" class="umaf-selectjs um-adm-conditional" style="width: 300px"
				data-cond1="approved" data-cond1-show="approved"
				data-cond2="checkmail" data-cond2-show="checkmail"
				data-cond3="pending" data-cond3-show="pending">
					<option value="approved" <?php selected('approved', $ultimatemember->query->get_meta_value('_um_status') ); ?>><?php _e('Auto Approve','ultimate-member'); ?></option>
					<option value="checkmail" <?php selected('checkmail', $ultimatemember->query->get_meta_value('_um_status') ); ?>><?php _e('Require Email Activation','ultimate-member'); ?></option>
					<option value="pending" <?php selected('pending', $ultimatemember->query->get_meta_value('_um_status') ); ?>><?php _e('Require Admin Review','ultimate-member'); ?></option>
				</select>

			</span>
		</p><div class="um-admin-clear"></div>

		<!-- Automatic Approval Settings -->

		<div class="approved">
		<p>
			<label class="um-admin-half"><?php _e('Action to be taken after registration','ultimate-member'); ?> <?php $this->tooltip( __('Select what action is taken after a person registers on your site. Depending on the status you can redirect them to their profile, a custom url or show a custom message','ultimate-member') ); ?></label>
			<span class="um-admin-half">

				<select name="_um_auto_approve_act" id="_um_auto_approve_act" class="umaf-selectjs um-adm-conditional" style="width: 300px" data-cond1="redirect_url" data-cond1-show="_um_auto_approve_act">
					<option value="redirect_profile" <?php selected('redirect_profile', $ultimatemember->query->get_meta_value('_um_auto_approve_act') ); ?>><?php _e('Redirect to profile','ultimate-member'); ?></option>
					<option value="redirect_url" <?php selected('redirect_url', $ultimatemember->query->get_meta_value('_um_auto_approve_act') ); ?>><?php _e('Redirect to URL','ultimate-member'); ?></option>
				</select>

			</span>
		</p><div class="um-admin-clear"></div>

		<p class="_um_auto_approve_act">
			<label class="um-admin-half" for="_um_auto_approve_url"><?php _e('Set Custom Redirect URL','ultimate-member'); ?></label>
			<span class="um-admin-half">

				<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_auto_approve_url', null, 'na'); ?>" name="_um_auto_approve_url" id="_um_auto_approve_url" />

			</span>
		</p><div class="um-admin-clear"></div>
		</div>

		<!-- Automatic Approval Settings -->

		<!-- Email Approval Settings -->

		<div class="checkmail">

		<p>
			<label class="um-admin-half"><?php _e('Login user after validating the activation link?','ultimate-member'); ?> <?php $this->tooltip( __('Login the user after validating the activation link','ultimate-member') ); ?></label>
			<span class="um-admin-half">

				<?php $this->ui_on_off('_um_login_email_activate', 0); ?>

			</span>
		</p><div class="um-admin-clear"></div>

		<p>
			<label class="um-admin-half"><?php _e('Action to be taken after registration','ultimate-member'); ?> <?php $this->tooltip( __('Select what action is taken after a person registers on your site. Depending on the status you can redirect them to their profile, a custom url or show a custom message','ultimate-member') ); ?></label>
			<span class="um-admin-half">

				<select name="_um_checkmail_action" id="_um_checkmail_action" class="umaf-selectjs um-adm-conditional" style="width: 300px"
				data-cond1="show_message" data-cond1-show="_um_checkmail_action-1"
				data-cond2="redirect_url" data-cond2-show="_um_checkmail_action-2">
					<option value="show_message" <?php selected('show_message', $ultimatemember->query->get_meta_value('_um_checkmail_action') ); ?>><?php _e('Show custom message','ultimate-member'); ?></option>
					<option value="redirect_url" <?php selected('redirect_url', $ultimatemember->query->get_meta_value('_um_checkmail_action') ); ?>><?php _e('Redirect to URL','ultimate-member'); ?></option>
				</select>

			</span>
		</p><div class="um-admin-clear"></div>

		<p class="_um_checkmail_action-1">
			<label class="um-admin-half"><?php _e('Personalize the custom message','ultimate-member'); ?></label>
			<span class="um-admin-half">

				<textarea name="_um_checkmail_message" id="_um_checkmail_message"><?php echo $ultimatemember->query->get_meta_value('_um_checkmail_message', null, __('Thank you for registering. Before you can login we need you to activate your account by clicking the activation link in the email we just sent you.','ultimate-member') ); ?></textarea>

			</span>
		</p><div class="um-admin-clear"></div>

		<p class="_um_checkmail_action-2">
			<label class="um-admin-half" for="_um_checkmail_url"><?php _e('Set Custom Redirect URL','ultimate-member'); ?></label>
			<span class="um-admin-half">

				<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_checkmail_url', null, 'na'); ?>" name="_um_checkmail_url" id="_um_checkmail_url" />

			</span>
		</p><div class="um-admin-clear"></div>

		<p>
			<label class="um-admin-half" for="_um_url_email_activate"><?php _e('URL redirect after e-mail activation','ultimate-member'); ?> <?php $this->tooltip( __('If you want users to go to a specific page other than login page after e-mail activation, enter the URL here.','ultimate-member') ); ?></label>
			<span class="um-admin-half">

				<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_url_email_activate', null, 'na'); ?>" name="_um_url_email_activate" id="_um_url_email_activate" />

			</span>
		</p><div class="um-admin-clear"></div>

		</div>

		<!-- Email Approval Settings -->

		<!-- Moderator Approval Settings -->

		<div class="pending">
		<p>
			<label class="um-admin-half"><?php _e('Action to be taken after registration','ultimate-member'); ?> <?php $this->tooltip( __('Select what action is taken after a person registers on your site. Depending on the status you can redirect them to their profile, a custom url or show a custom message','ultimate-member') ); ?></label>
			<span class="um-admin-half">

				<select name="_um_pending_action" id="_um_pending_action" class="umaf-selectjs um-adm-conditional" style="width: 300px"
				data-cond1="show_message" data-cond1-show="_um_pending_action-1"
				data-cond2="redirect_url" data-cond2-show="_um_pending_action-2">
					<option value="show_message" <?php selected('show_message', $ultimatemember->query->get_meta_value('_um_pending_action') ); ?>><?php _e('Show custom message','ultimate-member'); ?></option>
					<option value="redirect_url" <?php selected('redirect_url', $ultimatemember->query->get_meta_value('_um_pending_action') ); ?>><?php _e('Redirect to URL','ultimate-member'); ?></option>
				</select>

			</span>
		</p><div class="um-admin-clear"></div>

		<p class="_um_pending_action-1">
			<label class="um-admin-half"><?php _e('Personalize the custom message','ultimate-member'); ?></label>
			<span class="um-admin-half">

				<textarea name="_um_pending_message" id="_um_pending_message"><?php echo $ultimatemember->query->get_meta_value('_um_pending_message', null, __('Thank you for applying for membership to our site. We will review your details and send you an email letting you know whether your application has been successful or not.','ultimate-member') ); ?></textarea>

			</span>
		</p><div class="um-admin-clear"></div>

		<p class="_um_pending_action-2">
			<label class="um-admin-half" for="_um_pending_url"><?php _e('Set Custom Redirect URL','ultimate-member'); ?></label>
			<span class="um-admin-half">

				<input type="text" value="<?php echo $ultimatemember->query->get_meta_value('_um_pending_url', null, 'na'); ?>" name="_um_pending_url" id="_um_pending_url" />

			</span>
		</p><div class="um-admin-clear"></div>
		</div>

		<!-- Moderator Approval Settings -->

	</div>

	<div class="um-admin-clear"></div>

</div>

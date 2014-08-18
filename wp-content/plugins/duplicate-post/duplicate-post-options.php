<?php
/**
 * Add an option page
 */
if ( is_admin() ){ // admin actions
	add_action('admin_menu', 'duplicate_post_menu');
	add_action( 'admin_init', 'duplicate_post_register_settings');
}

function duplicate_post_register_settings() { // whitelist options
	register_setting( 'duplicate_post_group', 'duplicate_post_copydate');
	register_setting( 'duplicate_post_group', 'duplicate_post_copyexcerpt');
	register_setting( 'duplicate_post_group', 'duplicate_post_copyattachments');
	register_setting( 'duplicate_post_group', 'duplicate_post_copychildren');
	register_setting( 'duplicate_post_group', 'duplicate_post_copystatus');
	register_setting( 'duplicate_post_group', 'duplicate_post_blacklist');
	register_setting( 'duplicate_post_group', 'duplicate_post_taxonomies_blacklist');
	register_setting( 'duplicate_post_group', 'duplicate_post_title_prefix');
	register_setting( 'duplicate_post_group', 'duplicate_post_title_suffix');
	register_setting( 'duplicate_post_group', 'duplicate_post_roles');
	register_setting( 'duplicate_post_group', 'duplicate_post_show_row');
	register_setting( 'duplicate_post_group', 'duplicate_post_show_adminbar');
	register_setting( 'duplicate_post_group', 'duplicate_post_show_submitbox');
}


function duplicate_post_menu() {
	add_options_page(__("Duplicate Post Options", DUPLICATE_POST_I18N_DOMAIN), __("Duplicate Post", DUPLICATE_POST_I18N_DOMAIN), 'manage_options', 'duplicatepost', 'duplicate_post_options');
}

function duplicate_post_options() {

	if ( current_user_can( 'edit_users' ) && (isset($_GET['settings-updated'])  && $_GET['settings-updated'] == true)){
		global $wp_roles;
		$roles = $wp_roles->get_names();

		$dp_roles = get_option('duplicate_post_roles');
		if ( $dp_roles == "" ) $dp_roles = array();

		foreach ($roles as $name => $display_name){
			$role = get_role($name);

			// role should have at least edit_posts capability
			if ( !$role->has_cap('edit_posts') ) continue;

			/* If the role doesn't have the capability and it was selected, add it. */
			if ( !$role->has_cap( 'copy_posts' )  && in_array($name, $dp_roles) )
			$role->add_cap( 'copy_posts' );

			/* If the role has the capability and it wasn't selected, remove it. */
			elseif ( $role->has_cap( 'copy_posts' ) && !in_array($name, $dp_roles) )
			$role->remove_cap( 'copy_posts' );
		}
	}

	?>
<div class="wrap">
	<div id="icon-options-general" class="icon32">
		<br>
	</div>
	<h2>
	<?php _e("Duplicate Post Options", DUPLICATE_POST_I18N_DOMAIN); ?>
	</h2>

	<div
		style="border: solid 1px #aaaaaa; background-color: #eeeeee; margin: 9px 15px 4px 0; padding: 5px; text-align: center; font-weight: bold; float: left;">
		<a href="http://lopo.it/duplicate-post-plugin"><?php _e('Visit plugin site'); ?>
		</a> - <a href="http://lopo.it/duplicate-post-plugin"><?php _e('Translate', DUPLICATE_POST_I18N_DOMAIN); ?>
		</a> - <a href="http://lopo.it/duplicate-post-plugin"><?php _e('Donate', DUPLICATE_POST_I18N_DOMAIN); ?> (10¢)
		</a> <form style="display: inline-block; vertical-align: middle;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
"><input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1"></form>
			
		
	</div>

	<form method="post" action="options.php">
	<?php settings_fields('duplicate_post_group'); ?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row"><?php _e("Copy post/page date also", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><input type="checkbox" name="duplicate_post_copydate" value="1" <?php  if(get_option('duplicate_post_copydate') == 1) echo 'checked="checked"'; ?>"/>
					<span class="description"><?php _e("Normally, the new copy has its publication date set to current time: check the box to copy the original post/page date", DUPLICATE_POST_I18N_DOMAIN); ?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Copy post/page status", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><input type="checkbox" name="duplicate_post_copystatus"
					value="1" <?php  if(get_option('duplicate_post_copystatus') == 1) echo 'checked="checked"'; ?>"/>
					<span class="description"><?php _e("Copy the original post status (draft, published, pending) when cloning from the post list.", DUPLICATE_POST_I18N_DOMAIN); ?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Copy excerpt", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><input type="checkbox" name="duplicate_post_copyexcerpt"
					value="1" <?php  if(get_option('duplicate_post_copyexcerpt') == 1) echo 'checked="checked"'; ?>"/>
					<span class="description"><?php _e("Copy the excerpt from the original post/page", DUPLICATE_POST_I18N_DOMAIN); ?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Copy attachments", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><input type="checkbox" name="duplicate_post_copyattachments"
					value="1" <?php  if(get_option('duplicate_post_copyattachments') == 1) echo 'checked="checked"'; ?>"/>
					<span class="description"><?php _e("Copy the attachments from the original post/page", DUPLICATE_POST_I18N_DOMAIN); ?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Copy children", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><input type="checkbox" name="duplicate_post_copychildren"
					value="1" <?php  if(get_option('duplicate_post_copychildren') == 1) echo 'checked="checked"'; ?>"/>
					<span class="description"><?php _e("Copy the children from the original post/page", DUPLICATE_POST_I18N_DOMAIN); ?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Do not copy these fields", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><input type="text" name="duplicate_post_blacklist"
					value="<?php echo get_option('duplicate_post_blacklist'); ?>" /> <span
					class="description"><?php _e("Comma-separated list of meta fields that must not be copied", DUPLICATE_POST_I18N_DOMAIN); ?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Do not copy these taxonomies", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><div
						style="height: 100px; width: 300px; padding: 5px; overflow: auto; border: 1px solid #ccc">
						<?php $taxonomies=get_taxonomies(array('public' => true),'objects');
						$taxonomies_blacklist = get_option('duplicate_post_taxonomies_blacklist');
						if ($taxonomies_blacklist == "") $taxonomies_blacklist = array();
						foreach ($taxonomies as $taxonomy ) : ?>
						<label style="display: block;"> <input type="checkbox"
							name="duplicate_post_taxonomies_blacklist[]"
							value="<?php echo $taxonomy->name?>"
							<?php if(in_array($taxonomy->name,$taxonomies_blacklist)) echo 'checked="checked"'?> />
							<?php echo $taxonomy->labels->name?> </label>
							<?php endforeach; ?>
					</div> <span class="description"><?php _e("Select the taxonomies you don't want to be copied", DUPLICATE_POST_I18N_DOMAIN); ?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Title prefix", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><input type="text" name="duplicate_post_title_prefix"
					value="<?php echo get_option('duplicate_post_title_prefix'); ?>" />
					<span class="description"><?php _e("Prefix to be added before the original title, e.g. \"Copy of\" (blank for no prefix)", DUPLICATE_POST_I18N_DOMAIN); ?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Title suffix", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><input type="text" name="duplicate_post_title_suffix"
					value="<?php echo get_option('duplicate_post_title_suffix'); ?>" />
					<span class="description"><?php _e("Suffix to be added after the original title, e.g. \"(dup)\" (blank for no suffix)", DUPLICATE_POST_I18N_DOMAIN); ?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Roles allowed to copy", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><div
						style="height: 100px; width: 300px; padding: 5px; overflow: auto; border: 1px solid #ccc">
						<?php	global $wp_roles;
						$roles = $wp_roles->get_names();
						foreach ($roles as $name => $display_name): $role = get_role($name);
						if ( !$role->has_cap('edit_posts') ) continue; ?>
						<label style="display: block;"> <input type="checkbox"
							name="duplicate_post_roles[]" value="<?php echo $name ?>"
							<?php if($role->has_cap('copy_posts')) echo 'checked="checked"'?> />
							<?php echo translate_user_role($display_name); ?> </label>
							<?php endforeach; ?>
					</div> <span class="description"><?php _e("Warning: users will be able to copy all posts, even those of other users", DUPLICATE_POST_I18N_DOMAIN); ?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Show links in", DUPLICATE_POST_I18N_DOMAIN); ?>
				</th>
				<td><label style="display: block"><input type="checkbox"
						name="duplicate_post_show_row" value="1" <?php  if(get_option('duplicate_post_show_row') == 1) echo 'checked="checked"'; ?>"/>
						<?php _e("Post list", DUPLICATE_POST_I18N_DOMAIN); ?> </label> <label
					style="display: block"><input type="checkbox"
						name="duplicate_post_show_submitbox" value="1" <?php  if(get_option('duplicate_post_show_submitbox') == 1) echo 'checked="checked"'; ?>"/>
						<?php _e("Edit screen", DUPLICATE_POST_I18N_DOMAIN); ?> </label> <label
					style="display: block"><input type="checkbox"
						name="duplicate_post_show_adminbar" value="1" <?php  if(get_option('duplicate_post_show_adminbar') == 1) echo 'checked="checked"'; ?>"/>
						<?php _e("Admin bar", DUPLICATE_POST_I18N_DOMAIN); ?> (WP 3.1+)</label>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary"
				value="<?php _e('Save Changes', DUPLICATE_POST_I18N_DOMAIN) ?>" />
		</p>

	</form>
</div>
<?php
}
?>
<?php
require_once('./admin.php');

$title = __('General Settings');
$parent_file = 'options-general.php';

include('./admin-header.php');
?>

<div class="wrap">
<h2><?php _e('General Settings') ?></h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="blogname"><?php _e('Blog Title') ?></label></th>
<td><input name="blogname" type="text" id="blogname" value="<?php form_option('blogname'); ?>" size="40" /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="blogdescription"><?php _e('Tagline') ?></label></th>
<td><input name="blogdescription" type="text" id="blogdescription" style="width: 95%" value="<?php form_option('blogdescription'); ?>" size="45" />
<br />
<?php _e('In a few words, explain what this blog is about.') ?></td>
</tr>
<tr valign="top">
<th scope="row"><label for="siteurl"><?php _e('WordPress address (URL)') ?></label></th>
<td><input name="siteurl" type="text" id="siteurl" value="<?php form_option('siteurl'); ?>" size="40" class="code<?php if ( defined( 'WP_SITEURL' ) ) : ?> disabled" disabled="disabled"<?php else: ?>"<?php endif; ?> /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="home"><?php _e('Blog address (URL)') ?></label></th>
<td><input name="home" type="text" id="home" value="<?php form_option('home'); ?>" size="40" class="code<?php if ( defined( 'WP_HOME' ) ) : ?> disabled" disabled="disabled"<?php else: ?>"<?php endif; ?> /><br /><?php _e('Enter the address here if you want your blog homepage <a href="http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory">to be different from the directory</a> you installed WordPress.'); ?></td>
</tr>
<tr valign="top">
<th scope="row"><label for="admin_email"><?php _e('E-mail address') ?> </label></th>
<td><input name="admin_email" type="text" id="admin_email" value="<?php form_option('admin_email'); ?>" size="40" class="code" />
<br />
<?php _e('This address is used for admin purposes, like new user notification.') ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Membership') ?></th>
<td> <fieldset><legend class="hidden"><?php _e('Membership') ?></legend><label for="users_can_register">
<input name="users_can_register" type="checkbox" id="users_can_register" value="1" <?php checked('1', get_option('users_can_register')); ?> />
<?php _e('Anyone can register') ?></label><br />
<label for="comment_registration">
<input name="comment_registration" type="checkbox" id="comment_registration" value="1" <?php checked('1', get_option('comment_registration')); ?> />
<?php _e('Users must be registered and logged in to comment') ?>
</label>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><label for="default_role"><?php _e('New User Default Role') ?></label></th>
<td>
<select name="default_role" id="default_role"><?php wp_dropdown_roles( get_option('default_role') ); ?></select>
</td>
</tr>
<tr>
<th scope="row"><label for="gmt_offset"><?php _e('Timezone') ?> </label></th>
<td>
<select name="gmt_offset" id="gmt_offset">
<?php
$current_offset = get_option('gmt_offset');
$offset_range = array (-12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
	0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14);
foreach ( $offset_range as $offset ) {
	if ( 0 < $offset )
		$offset_name = '+' . $offset;
	elseif ( 0 == $offset )
		$offset_name = '';
	else
		$offset_name = (string) $offset;

	$offset_name = str_replace(array('.25','.5','.75'), array(':15',':30',':45'), $offset_name);

	$selected = '';
	if ( $current_offset == $offset ) {
		$selected = " selected='selected'";
		$current_offset_name = $offset_name;
	}
	echo "<option value=\"$offset\"$selected>" . sprintf(__('UTC %s'), $offset_name) . '</option>';
}
?>
</select>
<?php _e('hours') ?><br />
<?php printf(__('<abbr title="Coordinated Universal Time">UTC</abbr> time is <code>%s</code>'), gmdate(__('Y-m-d G:i:s'))); ?><br />
<?php if ($current_offset) printf(__('UTC %1$s is <code>%2$s</code>'), $current_offset_name, gmdate(__('Y-m-d G:i:s'), current_time('timestamp'))); ?><br />
<?php _e('Unfortunately, you have to manually update this for Daylight Savings Time. Lame, we know, but will be fixed in the future.'); ?>
</td>
</tr>
<tr>
<th scope="row"><label for="date_format"><?php _e('Date Format') ?></label></th>
<td><input name="date_format" type="text" id="date_format" size="30" value="<?php form_option('date_format'); ?>" /><br />
<?php _e('Output:') ?> <strong><?php echo mysql2date(get_option('date_format'), current_time('mysql')); ?></strong></td>
</tr>
<tr>
<th scope="row"><label for="time_format"><?php _e('Time Format') ?></label></th>
<td><input name="time_format" type="text" id="time_format" size="30" value="<?php form_option('time_format'); ?>" /><br />
<?php _e('Output:') ?> <strong><?php echo gmdate(get_option('time_format'), current_time('timestamp')); ?></strong><br />
<?php _e('<a href="http://codex.wordpress.org/Formatting_Date_and_Time">Documentation on date formatting</a>. Click "Save Changes" to update sample output.') ?></td>
</tr>
<tr>
<th scope="row"><label for="start_of_week"><?php _e('Week Starts On') ?></label></th>
<td><select name="start_of_week" id="start_of_week">
<?php
for ($day_index = 0; $day_index <= 6; $day_index++) :
	$selected = (get_option('start_of_week') == $day_index) ? 'selected="selected"' : '';
	echo "\n\t<option value='$day_index' $selected>" . $wp_locale->get_weekday($day_index) . '</option>';
endfor;
?>
</select></td>
</tr>
</table>

<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="<?php if ( ! defined( 'WP_SITEURL' ) ) echo 'siteurl,'; if ( ! defined( 'WP_HOME' ) ) echo 'home,'; ?>blogname,blogdescription,admin_email,users_can_register,gmt_offset,date_format,time_format,start_of_week,comment_registration,default_role" />
</p>
</form>

</div>

<?php include('./admin-footer.php') ?>

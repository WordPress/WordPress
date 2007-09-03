<?php
require_once('./admin.php');

$title = __('General Options');
$parent_file = 'options-general.php';

include('./admin-header.php');
?>

<div class="wrap">
<h2><?php _e('General Options') ?></h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>
<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" /></p>
<table class="optiontable">
<tr valign="top">
<th scope="row"><?php _e('Blog title:') ?></th>
<td><input name="blogname" type="text" id="blogname" value="<?php form_option('blogname'); ?>" size="40" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Tagline:') ?></th>
<td><input name="blogdescription" type="text" id="blogdescription" style="width: 95%" value="<?php form_option('blogdescription'); ?>" size="45" />
<br />
<?php _e('In a few words, explain what this blog is about.') ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('WordPress address (URL):') ?></th>
<td><input name="siteurl" type="text" id="siteurl" value="<?php form_option('siteurl'); ?>" size="40" class="code<?php if ( defined( 'WP_SITEURL' ) ) : ?> disabled" disabled="disabled"<?php else: ?>"<?php endif; ?> /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Blog address (URL):') ?></th>
<td><input name="home" type="text" id="home" value="<?php form_option('home'); ?>" size="40" class="code<?php if ( defined( 'WP_HOME' ) ) : ?> disabled" disabled="disabled"<?php else: ?>"<?php endif; ?> /><br /><?php _e('Enter the address here if you want your blog homepage <a href="http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory">to be different from the directory</a> you installed WordPress.'); ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('E-mail address:') ?> </th>
<td><input name="admin_email" type="text" id="admin_email" value="<?php form_option('admin_email'); ?>" size="40" class="code" />
<br />
<?php _e('This address is used only for admin purposes.') ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Membership:') ?></th>
<td> <label for="users_can_register">
<input name="users_can_register" type="checkbox" id="users_can_register" value="1" <?php checked('1', get_option('users_can_register')); ?> />
<?php _e('Anyone can register') ?></label><br />
<label for="comment_registration">
<input name="comment_registration" type="checkbox" id="comment_registration" value="1" <?php checked('1', get_option('comment_registration')); ?> />
<?php _e('Users must be registered and logged in to comment') ?>
</label>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('New User Default Role:') ?></th>
<td><label for="default_role">
<select name="default_role" id="default_role"><?php wp_dropdown_roles( get_option('default_role') ); ?></select></label>
</td>
</tr>
</table>
<fieldset class="options">
<legend><?php _e('Date and Time') ?></legend>
<table class="optiontable">
<tr>
<th scope="row"><?php _e('<abbr title="Coordinated Universal Time">UTC</abbr> time is:') ?> </th>
<td><code><?php echo gmdate(__('Y-m-d g:i:s a')); ?></code></td>
</tr>
<tr>
<th scope="row"><?php _e('Times in the blog should differ by:') ?> </th>
<td><input name="gmt_offset" type="text" id="gmt_offset" size="2" value="<?php form_option('gmt_offset'); ?>" />
<?php _e('hours') ?> (<?php _e('Your timezone offset, for example <code>-6</code> for Central Time.'); ?>)</td>
</tr>
<tr>
<th scope="row"><?php _e('Default date format:') ?></th>
<td><input name="date_format" type="text" id="date_format" size="30" value="<?php form_option('date_format'); ?>" /><br />
<?php _e('Output:') ?> <strong><?php echo mysql2date(get_option('date_format'), current_time('mysql')); ?></strong></td>
</tr>
<tr>
<th scope="row"><?php _e('Default time format:') ?></th>
<td><input name="time_format" type="text" id="time_format" size="30" value="<?php form_option('time_format'); ?>" /><br />
<?php _e('Output:') ?> <strong><?php echo gmdate(get_option('time_format'), current_time('timestamp')); ?></strong></td>
</tr>
<tr>
<th scope="row">&nbsp;</th>
<td><?php _e('<a href="http://codex.wordpress.org/Formatting_Date_and_Time">Documentation on date formatting</a>. Click "Update options" to update sample output.') ?> </td>
</tr>
<tr>
<th scope="row"><?php _e('Weeks in the calendar should start on:') ?></th>
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
</fieldset>

<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="<?php if ( ! defined( 'WP_SITEURL' ) ) echo 'siteurl,'; if ( ! defined( 'WP_HOME' ) ) echo 'home,'; ?>blogname,blogdescription,admin_email,users_can_register,gmt_offset,date_format,time_format,start_of_week,comment_registration,default_role" />
</p>
</form>

</div>

<?php include('./admin-footer.php') ?>

<?php
/**
 * Miscellaneous settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Miscellaneous Settings');
$parent_file = 'options-general.php';

include('admin-header.php');

?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo wp_specialchars( $title ); ?></h2>

<form method="post" action="options.php">
<?php settings_fields('misc'); ?>

<h3><?php _e('Uploading Files'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="upload_path"><?php _e('Store uploads in this folder'); ?></label></th>
<td><input name="upload_path" type="text" id="upload_path" value="<?php echo attribute_escape(str_replace(ABSPATH, '', get_option('upload_path'))); ?>" class="regular-text code" />
<span class="setting-description"><?php _e('Default is <code>wp-content/uploads</code>'); ?></span>
</td>
</tr>

<tr valign="top">
<th scope="row"><label for="upload_url_path"><?php _e('Full URL path to files'); ?></label></th>
<td><input name="upload_url_path" type="text" id="upload_url_path" value="<?php echo attribute_escape( get_option('upload_url_path')); ?>" class="regular-text code" />
<span class="setting-description"><?php _e('Configuring this is optional by default it should be blank'); ?></span>
</td>
</tr>

<tr>
<th scope="row" colspan="2" class="th-full">
<label for="uploads_use_yearmonth_folders">
<input name="uploads_use_yearmonth_folders" type="checkbox" id="uploads_use_yearmonth_folders" value="1"<?php checked('1', get_option('uploads_use_yearmonth_folders')); ?> />
<?php _e('Organize my uploads into month- and year-based folders'); ?>
</label>
</th>
</tr>
<?php do_settings_fields('misc', 'default'); ?>
</table>

<table class="form-table">

<tr>
<th scope="row" class="th-full">
<label for="use_linksupdate">
<input name="use_linksupdate" type="checkbox" id="use_linksupdate" value="1"<?php checked('1', get_option('use_linksupdate')); ?> />
<?php _e('Track Links&#8217; Update Times') ?>
</label>
</th>
</tr>
<tr>

<th scope="row" class="th-full">
<label for="hack_file">
<input type="checkbox" id="hack_file" name="hack_file" value="1"<?php checked('1', get_option('hack_file')); ?> />
<?php _e('Use legacy <code>my-hacks.php</code> file support') ?>
</label>
</th>
</tr>

</table>

<?php do_settings_sections('misc'); ?>

<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>

<?php include('./admin-footer.php'); ?>

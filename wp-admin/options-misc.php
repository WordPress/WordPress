<?php
require_once('admin.php');

$title = __('Miscellaneous Options');
$parent_file = 'options-general.php';

include('admin-header.php');

?>
 
<div class="wrap"> 
<h2><?php _e('Miscellaneous Options') ?></h2> 
<form method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>
<fieldset class="options">
<legend><?php _e('Uploading'); ?></legend>
<table class="editform optiontable">
<tr valign="top">
<th scope="row"><?php _e('Store uploads in this folder'); ?>:</th>
<td><input name="upload_path" type="text" id="upload_path" class="code" value="<?php echo wp_specialchars(str_replace(ABSPATH, '', get_option('upload_path')), 1); ?>" size="40" />
<br />
<?php _e('Default is <code>wp-content/uploads</code>'); ?>
</td>
</tr>
<tr>
<td></td>
<td>
<label for="uploads_use_yearmonth_folders">
<input name="uploads_use_yearmonth_folders" type="checkbox" id="uploads_use_yearmonth_folders" value="1" <?php checked('1', get_option('uploads_use_yearmonth_folders')); ?> />
<?php _e('Organize my uploads into month- and year-based folders'); ?>
</label>
</td>
</tr>
</table>
</fieldset>

<p><input name="use_linksupdate" type="checkbox" id="use_linksupdate" value="1" <?php checked('1', get_option('use_linksupdate')); ?> />
<label for="use_linksupdate"><?php _e('Track Bookmarks&#8217; Update Times') ?></label></p>
<p>
<label><input type="checkbox" name="hack_file" value="1" <?php checked('1', get_option('hack_file')); ?> /> <?php _e('Use legacy <code>my-hacks.php</code> file support') ?></label>
</p>

<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="hack_file,use_linksupdate,uploads_use_yearmonth_folders,upload_path" /> 
<input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" />
</p>
</form> 
</div>

<?php include('./admin-footer.php'); ?>
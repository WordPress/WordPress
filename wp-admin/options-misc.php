<?php
require_once('admin.php');

$title = __('Miscellaneous Options');
$parent_file = 'options-general.php';

include('admin-header.php');

?>
 
<div class="wrap"> 
<h2><?php _e('Miscellaneous Options') ?></h2> 
<form method="post" action="options.php">

<fieldset class="options">
<legend><?php _e('Uploading'); ?></legend>
<table width="100%" cellspacing="2" cellpadding="5" class="editform">
	<tr valign="top"><th scope="row" width="33%"><?php _e('Organize uploads:'); ?></th>
	<td>
		<label for="uploads_use_yearmonth_folders">
			<input name="uploads_use_yearmonth_folders" type="checkbox" id="uploads_use_yearmonth_folders" value="1" <?php checked('1', get_settings('uploads_use_yearmonth_folders')); ?> />
			<?php _e('Organize my uploads into month- and year-based folders'); ?>
		</label>
	</td></tr>
	<tr valign="top"><th scope="row"><?php _e('Store uploads in this folder (default is wp-content/uploads):'); ?></th>
	<td>
		<input name="fileupload_realpath" type="text" id="fileupload_realpath" value="<?php echo str_replace(ABSPATH, '', get_settings('fileupload_realpath')); ?>" size="40" />
	</td></tr>
</table>
</fieldset>

<p><input name="use_linksupdate" type="checkbox" id="use_linksupdate" value="1" <?php checked('1', get_settings('use_linksupdate')); ?> />
<label for="use_linksupdate"><?php _e('Track Links&#8217; Update Times') ?></label></p>
<p>
<label><input type="checkbox" name="hack_file" value="1" <?php checked('1', get_settings('hack_file')); ?> /> <?php _e('Use legacy <code>my-hacks.php</code> file support') ?></label>
</p>

<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="hack_file,use_linksupdate,uploads_use_yearmonth_folders,fileupload_realpath" /> 
	<input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
</p>
</form> 
</div>

<?php include('./admin-footer.php'); ?>
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
<legend>
<input name="use_fileupload" type="checkbox" id="use_fileupload" value="1" <?php checked('1', get_settings('use_fileupload')); ?> />
<label for="use_fileupload"><?php _e('Allow File Uploads') ?></label></legend>
<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
<tr> 
<th width="33%" valign="top" scope="row"><?php _e('Destination directory:') ?> </th> 
<td>
<input name="fileupload_realpath" type="text" id="fileupload_realpath" value="<?php form_option('fileupload_realpath'); ?>" size="50" /><br />
<?php printf(__('Recommended: <code>%s</code>'), ABSPATH . 'wp-content') ?>

</td> 
</tr> 
<tr>
<th valign="top" scope="row"><?php _e('URI of this directory:') ?> </th>
<td>          
<input name="fileupload_url" type="text" id="fileupload_url" value="<?php form_option('fileupload_url'); ?>" size="50" /><br />
<?php printf(__('Recommended: <code>%s</code>'), get_settings('siteurl') . '/wp-content') ?>
</td>
</tr>
<tr>
<th scope="row"><?php _e('Maximum size:') ?> </th>
<td><input name="fileupload_maxk" type="text" id="fileupload_maxk" value="<?php form_option('fileupload_maxk'); ?>" size="4" /> 
<?php _e('Kilobytes (KB)') ?></td>
</tr>
<tr>
<th valign="top" scope="row"><?php _e('Allowed file extensions:') ?></th>
<td><input name="fileupload_allowedtypes" type="text" id="fileupload_allowedtypes" value="<?php form_option('fileupload_allowedtypes'); ?>" size="40" />
<br />
<?php _e('Recommended: <code>jpg jpeg png gif</code>') ?></td>
</tr>
<tr>
<th scope="row"><?php _e('Minimum level to upload:') ?></th>
<td><select name="fileupload_minlevel" id="fileupload_minlevel">
<?php
for ($i = 1; $i < 11; $i++) {
if ($i == get_settings('fileupload_minlevel')) $selected = " selected='selected'";
else $selected = '';
echo "\n\t<option value='$i' $selected>$i</option>";
}
?>
</select></td>
</tr>
</table> 
</fieldset>
<p><input name="use_linksupdate" type="checkbox" id="use_linksupdate" value="1" <?php checked('1', get_settings('use_linksupdate')); ?> />
<label for="use_linksupdate"><?php _e('Track Links&#8217; Update Times') ?></label></p>
<p>
<label><input type="checkbox" name="hack_file" value="1" <?php checked('1', get_settings('hack_file')); ?> /> <?php _e('Use legacy <code>my-hacks.php</code> file support') ?></label>
</p>
<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="hack_file,use_fileupload,fileupload_realpath,fileupload_url,fileupload_allowedtypes,fileupload_maxk,fileupload_maxk,fileupload_minlevel,use_geo_positions,use_linksupdate" /> 
	<input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
</p>
</form> 
</div>

<?php include('./admin-footer.php'); ?>
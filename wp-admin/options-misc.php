<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Miscellaneous Options');
include('options-head.php');
?>
 
<div class="wrap"> 
<h2><?php _e('Miscellaneous Options') ?></h2> 
<form name="miscoptions" method="post" action="options.php"> 
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="'hack_file','use_fileupload','fileupload_realpath','fileupload_url','fileupload_allowedtypes','fileupload_maxk','fileupload_maxk','fileupload_minlevel','use_geo_positions','use_linksupdate','weblogs_xml_url','links_updated_date_format','links_recently_updated_prepend','links_recently_updated_append'" /> 
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
	<fieldset class="options">
	<legend>
	<input name="use_linksupdate" type="checkbox" id="use_linksupdate" value="1" <?php checked('1', get_settings('use_linksupdate')); ?> />
	<label for="use_linksupdate"><?php _e('Track Link&#8217;s Update Times') ?></label></legend>
	<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
	<tr> 
	<th width="33%" valign="top" scope="row"><?php _e('Update file:') ?> </th> 
	<td>
	<input name="weblogs_xml_url" type="text" id="weblogs_xml_url" value="<?php form_option('weblogs_xml_url'); ?>" size="50" /><br />
	<?php __('Recommended: <code>http://static.wordpress.org/changes.xml</code>') ?>
	
	</td> 
	</tr> 
	<tr>
	<th valign="top" scope="row"><?php _e('Updated link time format:') ?> </th>
	<td>          
	<input name="links_updated_date_format" type="text" id="links_updated_date_format" value="<?php form_option('links_updated_date_format'); ?>" size="50" />
	</td>
	</tr>
	<tr>
	<th scope="row"><?php _e('Prepend updated with:') ?> </th>
	<td><input name="links_recently_updated_prepend" type="text" id="links_recently_updated_prepend" value="<?php form_option('links_recently_updated_prepend'); ?>" size="50" /></td>
	</tr>
	<tr>
	<th valign="top" scope="row"><?php _e('Append updated with:') ?></th>
	<td><input name="links_recently_updated_append" type="text" id="links_recently_updated_append" value="<?php form_option('links_recently_updated_append'); ?>" size="50" /></td>
	</tr>
	</table>
	<p><?php printf(__('A link is "recent" if it has been updated in the past %s minutes.'), '<input name="links_recently_updated_time" type="text" id="links_recently_updated_time" size="3" value="' . get_settings('links_recently_updated_time'). '" />' ) ?></p>
	</fieldset>

	<p>
	<label><input type="checkbox" name="hack_file" value="1" <?php checked('1', get_settings('hack_file')); ?> /> <?php _e('Use legacy <code>my-hacks.php</code> file support') ?></label>
	</p>
	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
	</p>
</form> 
</div>

<?php include('./admin-footer.php'); ?>
<?php
require_once('../wp-includes/wp-l10n.php');

$title = 'Miscellaneous Options';
$parent_file = 'options-general.php';

function add_magic_quotes($array) {
	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	return $array;
}

if (!get_magic_quotes_gpc()) {
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

$wpvarstoreset = array('action','standalone', 'option_group_id');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}


$standalone = 0;
include_once('admin-header.php');
include('options-head.php');
?>
 
<div class="wrap"> 
  <h2><?php _e('Miscellaneous Options') ?></h2> 
  <form name="form1" method="post" action="options.php"> 
    <input type="hidden" name="action" value="update" /> 
	<input type="hidden" name="action" value="update" /> 		<input type="hidden" name="page_options" value="'hack_file','use_fileupload','fileupload_realpath','fileupload_url','fileupload_allowedtypes','fileupload_maxk','fileupload_maxk','fileupload_minlevel','use_geo_positions','default_geourl_lat','default_geourl_lon','use_default_geourl'" /> 
<fieldset class="options">
<legend>
<input name="use_fileupload" type="checkbox" id="use_fileupload" value="1" <?php checked('1', get_settings('use_fileupload')); ?> />
<label for="use_fileupload"><?php _e('Allow File Uploads') ?></label></legend>
    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr> 
        <th width="33%" valign="top" scope="row"><?php _e('Destination directory:') ?> </th> 
        <td>
        	<input name="fileupload_realpath" type="text" id="fileupload_realpath" value="<?php echo get_settings('fileupload_realpath'); ?>" size="50" /><br />
<?php printf(__('Recommended: <code>%s</code>'), ABSPATH . 'wp-content') ?>
  
        	</td> 
      </tr> 
      <tr>
        <th valign="top" scope="row"><?php _e('URI of this directory:') ?> </th>
        <td>          
        	<input name="fileupload_url" type="text" id="fileupload_url" value="<?php echo get_settings('fileupload_url'); ?>" size="50" /><br />
<?php printf(__('Recommended: <code>%s</code>'), get_settings('siteurl') . '/wp-content') ?>
        </td>
      </tr>
      <tr>
        <th scope="row"><?php _e('Maximum size:') ?> </th>
      	<td><input name="fileupload_maxk" type="text" id="fileupload_maxk" value="<?php echo get_settings('fileupload_maxk'); ?>" size="4" /> 
        <?php _e('Kilobytes (KB)') ?></td>
      	</tr>
      <tr>
        <th valign="top" scope="row"><?php _e('Allowed file extensions:') ?></th>
      	<td><input name="fileupload_allowedtypes" type="text" id="fileupload_allowedtypes" value="<?php echo get_settings('fileupload_allowedtypes'); ?>" size="40" />
      		<br />
         <?php _e('Recommended: <code>jpg jpeg png gif </code>') ?></td>
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
<input name="use_geo_positions" type="checkbox" id="use_geo_positions" value="1" <?php checked('1', get_settings('use_geo_positions')); ?> />
<label for="use_geo_positions"><?php _e('Use Geographic Tracking Features') ?></label></legend>
    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr> 
        <th width="33%" valign="top" scope="row"><?php _e('Default latitude:') ?> </th> 
        <td>
        	<input name="default_geourl_lat" type="text" id="default_geourl_lat" value="<?php echo get_settings('default_geourl_lat'); ?>" size="50" />
        	</td> 
      </tr> 
      <tr>
        <th valign="top" scope="row"><?php _e('Default longitude:') ?> </th>
        <td>          
        	<input name="default_geourl_lon" type="text" id="default_geourl_lon" value="<?php echo get_settings('default_geourl_lon'); ?>" size="50" />
        </td>
      </tr>
      <tr>
        <th scope="row">  </th>
      	<td><label>
			<input type="checkbox" name="use_default_geourl" value="1" <?php checked('1', get_settings('use_default_geourl')); ?> /> 
			<?php _e('Use default location values if none specified.') ?></label></td>
      	</tr>

    </table> 
</fieldset>
		<p>
			<label>
			<input type="checkbox" name="hack_file" value="1" <?php checked('1', get_settings('hack_file')); ?> /> 
			<?php _e('Use legacy <code>my-hacks.php</code> file support') ?></label>
		</p>
    <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
    </p>
  </form> 
</div> 
<?php include("admin-footer.php") ?>
<?php
$title = 'Miscellaneous Options';

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
	$HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
	$HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$wpvarstoreset = array('action','standalone', 'option_group_id');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($HTTP_POST_VARS["$wpvar"])) {
			if (empty($HTTP_GET_VARS["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $HTTP_GET_VARS["$wpvar"];
			}
		} else {
			$$wpvar = $HTTP_POST_VARS["$wpvar"];
		}
	}
}


$standalone = 0;
include_once('admin-header.php');
include('options-head.php');
?>
 
<div class="wrap"> 
  <h2>Miscellaneous Options</h2> 
  <form name="form1" method="post" action="options.php"> 
    <input type="hidden" name="action" value="update" /> 
	<input type="hidden" name="action" value="update" /> 		<input type="hidden" name="page_options" value="'hack_file','use_fileupload','fileupload_realpath','fileupload_url','fileupload_allowedtypes','fileupload_maxk','fileupload_maxk'" /> 
<fieldset class="options">
<legend>
<input name="use_fileupload" type="checkbox" id="use_fileupload" value="1" <?php checked('1', get_settings('use_fileupload')); ?> />
<label for="use_fileupload">Allow File Uploads</label></legend>
    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr> 
        <th width="33%" valign="top" scope="row"> Destination directory: </th> 
        <td>
        	<input name="fileupload_realpath" type="text" id="fileupload_realpath" value="<?php echo get_settings('fileupload_realpath'); ?>" size="50" /><br />
Recommended: <code><?php echo ABSPATH . 'wp-content'; ?></code>
  
        	</td> 
      </tr> 
      <tr>
        <th valign="top" scope="row">URI of this directory: </th>
        <td>          
        	<input name="fileupload_url" type="text" id="fileupload_url" value="<?php echo get_settings('fileupload_url'); ?>" size="50" /><br />
Recommended: <code><?php echo get_settings('siteurl') . '/wp-content'; ?></code>
        </td>
      </tr>
      <tr>
      	<th scope="row">Maximum size: </th>
      	<td><input name="fileupload_maxk" type="text" id="fileupload_maxk" value="<?php echo get_settings('fileupload_maxk'); ?>" size="4"> 
      		Kilobytes (KB)</td>
      	</tr>
      <tr>
      	<th valign="top" scope="row">Allowed file extensions:</th>
      	<td><input name="fileupload_allowedtypes" type="text" id="fileupload_allowedtypes" value="<?php echo get_settings('fileupload_allowedtypes'); ?>" size="40">
      		<br>
      		Recommended: <code>jpg jpeg png gif </code></td>
      	</tr>
      <tr>
      	<th scope="row">Minimum level to upload:</th>
      	<td><select name="fileupload_minlevel" id="fileupload_minlevel">
<?php
for ($i = 0; $i < 11; $i++) {
if ($i == get_settings('fileupload_minlevel')) $selected = " selected='selected'";
else $selected = '';
	echo "\n\t<option value='$i' $selected>$i</option>";
}
?>
      		</select></td>
      	</tr>
    </table> 
</fieldset>
		<p>
			<label>
			<input type="checkbox" name="hack_file" value="1" <?php checked('1', get_settings('hack_file')); ?> /> 
			Use legacy <code>my-hacks.php</code> file support</label>
		</p>
    <p style="text-align: right;">
      <input type="submit" name="Submit" value="Update Options" />
    </p>
  </form> 
</div> 
<?php include("admin-footer.php") ?>
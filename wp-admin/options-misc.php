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
	<input type="hidden" name="action" value="update" /> 		<input type="hidden" name="page_options" value="'hack_file' " /> 
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
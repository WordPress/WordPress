<?php
$title = 'General Options';

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
  <h2>General Options</h2> 
  <form name="form1" method="post" action="options.php"> 
    <input type="hidden" name="action" value="update" /> 
	<input type="hidden" name="action" value="update" /> <input type="hidden" name="page_options" value="'blogname','blogdescription','siteurl','admin_email','users_can_register','new_users_can_blog','gmt_offset','date_format','time_format'" /> 
    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr valign="top"> 
        <th width="33%" scope="row">Weblog title: </th> 
        <td><input name="blogname" type="text" id="blogname" value="<?php echo get_settings('blogname'); ?>" size="40" /></td> 
      </tr> 
      <tr valign="top"> 
        <th scope="row">Tagline:</th> 
        <td><input name="blogdescription" type="text" id="blogdescription" style="width: 95%" value="<?php echo get_settings('blogdescription'); ?>" size="45" />
        <br />
In a few words, explain what this weblog is about.</td> 
      </tr> 
      <tr valign="top"> 
        <th scope="row">Web address (URI): </th> 
        <td><input name="siteurl" type="text" id="siteurl" value="<?php echo get_settings('siteurl'); ?>" size="40" class="code" /></td> 
      </tr> 
      <tr valign="top"> 
        <th scope="row">E-mail address: </th> 
        <td><input name="admin_email" type="text" id="admin_email" value="<?php echo get_settings('admin_email'); ?>" size="40" class="code" />
        <br />
This address is used only for admin purposes. </td> 
      </tr>
      <tr valign="top"> 
        <th scope="row">Membership:</th> 
        <td> <label for="users_can_register"> 
          <input name="users_can_register" type="checkbox" id="users_can_register" value="1" <?php checked('1', get_settings('users_can_register')); ?> /> 
          Anyone can register</label> 
          <br /> 
          <label for="new_users_can_blog"> 
          <input name="new_users_can_blog" type="checkbox" id="new_users_can_blog" value="1" <?php checked('1', get_settings('new_users_can_blog')); ?> /> 
          Any registered member can publish articles </label></td> 
      </tr> 
    </table> 
    <fieldset class="options"> 
    <legend>Date and Time</legend> 
	    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr> 
        <th scope="row" width="33%"><acronym title="Greenwich Meridian Time">GMT</acronym> time is: </th> 
        <td><code><?php echo gmdate('Y-m-d g:i:s a'); ?></code></td> 
      </tr>
      <tr>
        <th scope="row">Times in the weblog should differ by: </th>
        <td><input name="gmt_offset" type="text" id="gmt_offset" size="2" value="<?php echo get_settings('gmt_offset'); ?>" /> 
        hours </td>
      </tr>
      <tr>
      	<th scope="row">&nbsp;</th>
      	<td>The following use the same syntax as the <a href="http://php.net/date">PHP <code>date()</code> function</a>. </td>
      	</tr>
      <tr>
      	<th scope="row">Default date format:</th>
      	<td><input name="date_format" type="text" id="date_format" size="30" value="<?php echo get_settings('date_format'); ?>" /></td>
      	</tr>
      <tr>
      	<th scope="row">Default time format:</th>
      	<td><input name="time_format" type="text" id="time_format" size="30" value="<?php echo get_settings('time_format'); ?>" /></td>
      	</tr> 
</table>
    </fieldset> 
    <p style="text-align: right;">
      <input type="submit" name="Submit" value="Update Options" />
    </p>
  </form> 
</div> 
<?php include("admin-footer.php") ?>
<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('General Options');

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
  <h2><?php _e('General Options') ?></h2> 
  <form name="form1" method="post" action="options.php"> 
    <input type="hidden" name="action" value="update" /> 
	<input type="hidden" name="action" value="update" /> <input type="hidden" name="page_options" value="'blogname','blogdescription','siteurl','admin_email','users_can_register','new_users_can_blog','gmt_offset','date_format','time_format','home'" /> 
    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr valign="top"> 
        <th width="33%" scope="row"><?php _e('Weblog title:') ?></th> 
        <td><input name="blogname" type="text" id="blogname" value="<?php echo get_settings('blogname'); ?>" size="40" /></td> 
      </tr> 
      <tr valign="top"> 
        <th scope="row"><?php _e('Tagline:') ?></th> 
        <td><input name="blogdescription" type="text" id="blogdescription" style="width: 95%" value="<?php echo get_settings('blogdescription'); ?>" size="45" />
        <br />
<?php _e('In a few words, explain what this weblog is about.') ?></td> 
      </tr> 
      <tr valign="top"> 
        <th scope="row"><?php _e('WordPress address (URI):') ?></th> 
        <td><input name="siteurl" type="text" id="siteurl" value="<?php echo get_settings('siteurl'); ?>" size="40" class="code" /></td> 
      </tr> 
      <tr valign="top">
      	<th scope="row"><?php _e('Blog address (URI):') ?></th>
      	<td><input name="home" type="text" id="home" value="<?php echo get_settings('home'); ?>" size="40" class="code" /><br /><?php _e('If you want your blog homepage to be different than the directory you installed WordPress in, enter that address here. '); ?></td>
      	</tr>
      <tr valign="top"> 
        <th scope="row"><?php _e('E-mail address:') ?> </th> 
        <td><input name="admin_email" type="text" id="admin_email" value="<?php echo get_settings('admin_email'); ?>" size="40" class="code" />
        <br />
<?php _e('This address is used only for admin purposes.') ?></td> 
      </tr>
      <tr valign="top"> 
        <th scope="row"><?php _e('Membership:') ?></th> 
        <td> <label for="users_can_register"> 
          <input name="users_can_register" type="checkbox" id="users_can_register" value="1" <?php checked('1', get_settings('users_can_register')); ?> /> 
          <?php _e('Anyone can register') ?></label> 
          <br /> 
          <label for="new_users_can_blog"> 
          <input name="new_users_can_blog" type="checkbox" id="new_users_can_blog" value="1" <?php checked('1', get_settings('new_users_can_blog')); ?> /> 
          <?php _e('Any registered member can publish articles') ?> </label></td> 
      </tr> 
    </table> 
    <fieldset class="options"> 
    <legend><?php _e('Date and Time') ?></legend> 
	    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr> 
          <th scope="row" width="33%"><?php _e('<acronym title="Greenwich Meridian Time">GMT</acronym> time is:') ?> </th> 
        <td><code><?php echo gmdate('Y-m-d g:i:s a'); ?></code></td> 
      </tr>
      <tr>
        <th scope="row"><?php _e('Times in the weblog should differ by:') ?> </th>
        <td><input name="gmt_offset" type="text" id="gmt_offset" size="2" value="<?php echo get_settings('gmt_offset'); ?>" /> 
        <?php _e('hours') ?> </td>
      </tr>
      <tr>
      	<th scope="row">&nbsp;</th>
      	<td><?php _e('The following use the same syntax as the <a href="http://php.net/date">PHP <code>date()</code> function</a>. Save option to update sample output.') ?> </td>
      	</tr>
      <tr>
      	<th scope="row"><?php _e('Default date format:') ?></th>
      	<td><input name="date_format" type="text" id="date_format" size="30" value="<?php echo get_settings('date_format'); ?>" /><br />
<?php _e('Output:') ?> <strong><?php echo gmdate(get_settings('date_format'), current_time('timestamp')); ?></strong></td>
      	</tr>
      <tr>
        <th scope="row"><?php _e('Default time format:') ?></th>
      	<td><input name="time_format" type="text" id="time_format" size="30" value="<?php echo get_settings('time_format'); ?>" /><br />
<?php _e('Output:') ?> <strong><?php echo gmdate(get_settings('time_format'), current_time('timestamp')); ?></strong></td>
      	</tr> 
</table>
<pre><?php var_dump($cache_settings); ?></pre>
    </fieldset> 
    <p style="text-align: right;">
      <input type="submit" name="Submit" value="<?php _e('Update Options') ?>" />
    </p>
  </form> 
</div> 
<?php include("admin-footer.php") ?>
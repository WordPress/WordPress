<?php
require_once('admin.php');

$title = __('General Options');
$parent_file = 'options-general.php';

include('admin-header.php');
?>
 
<div class="wrap"> 
  <h2><?php _e('General Options') ?></h2> 
  <form name="form1" method="post" action="options.php"> 
    <input type="hidden" name="action" value="update" /> 
	<input type="hidden" name="action" value="update" /> <input type="hidden" name="page_options" value="'blogname','blogdescription','siteurl','admin_email','users_can_register','gmt_offset','date_format','time_format','home','start_of_week'" /> 
    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr valign="top"> 
        <th width="33%" scope="row"><?php _e('Weblog title:') ?></th> 
        <td><input name="blogname" type="text" id="blogname" value="<?php form_option('blogname'); ?>" size="40" /></td> 
      </tr> 
      <tr valign="top"> 
        <th scope="row"><?php _e('Tagline:') ?></th> 
        <td><input name="blogdescription" type="text" id="blogdescription" style="width: 95%" value="<?php form_option('blogdescription'); ?>" size="45" />
        <br />
<?php _e('In a few words, explain what this weblog is about.') ?></td> 
      </tr> 
      <tr valign="top"> 
        <th scope="row"><?php _e('WordPress address (URI):') ?></th> 
        <td><input name="siteurl" type="text" id="siteurl" value="<?php form_option('siteurl'); ?>" size="40" class="code" /></td> 
      </tr> 
      <tr valign="top">
      	<th scope="row"><?php _e('Blog address (URI):') ?></th>
      	<td><input name="home" type="text" id="home" value="<?php form_option('home'); ?>" size="40" class="code" /><br /><?php _e('If you want your blog homepage to be different than the directory you installed WordPress in, enter that address here. '); ?></td>
      	</tr>
      <tr valign="top"> 
        <th scope="row"><?php _e('E-mail address:') ?> </th> 
        <td><input name="admin_email" type="text" id="admin_email" value="<?php form_option('admin_email'); ?>" size="40" class="code" />
        <br />
<?php _e('This address is used only for admin purposes.') ?></td> 
      </tr>
      <tr valign="top"> 
        <th scope="row"><?php _e('Membership:') ?></th> 
        <td> <label for="users_can_register"> 
          <input name="users_can_register" type="checkbox" id="users_can_register" value="1" <?php checked('1', get_settings('users_can_register')); ?> /> 
          <?php _e('Anyone can register') ?></label> 
</td> 
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
        <td><input name="gmt_offset" type="text" id="gmt_offset" size="2" value="<?php form_option('gmt_offset'); ?>" /> 
        <?php _e('hours') ?> </td>
      </tr>
      <tr>
      	<th scope="row">&nbsp;</th>
      	<td><?php _e('The following use the same syntax as the <a href="http://php.net/date">PHP <code>date()</code> function</a>. Save option to update sample output.') ?> </td>
      	</tr>
      <tr>
      	<th scope="row"><?php _e('Default date format:') ?></th>
      	<td><input name="date_format" type="text" id="date_format" size="30" value="<?php form_option('date_format'); ?>" /><br />
<?php _e('Output:') ?> <strong><?php echo mysql2date(get_settings('date_format'), current_time('mysql')); ?></strong></td>
      	</tr>
      <tr>
        <th scope="row"><?php _e('Default time format:') ?></th>
      	<td><input name="time_format" type="text" id="time_format" size="30" value="<?php form_option('time_format'); ?>" /><br />
<?php _e('Output:') ?> <strong><?php echo gmdate(get_settings('time_format'), current_time('timestamp')); ?></strong></td>
      	</tr> 
      <tr>
        <th scope="row"><?php _e('Weeks in the calendar should start on:') ?></th>
        <td><select name="start_of_week" id="start_of_week">
	<?php
for ($day_index = 0; $day_index <= 6; $day_index++) :
	if ($day_index == get_settings('start_of_week')) $selected = " selected='selected'";
	else $selected = '';
echo "\n\t<option value='$day_index' $selected>$weekday[$day_index]</option>";
endfor;
?>
</select></td>
       		</tr>

</table>

    </fieldset> 
    <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
    </p>
  </form> 
</div> 
<?php include("admin-footer.php") ?>

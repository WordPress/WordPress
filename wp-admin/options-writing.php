<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Writing Options');
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
  <h2>Writing Options</h2> 
  <form name="form1" method="post" action="options.php"> 
    <input type="hidden" name="action" value="update" /> 
    <input type="hidden" name="page_options" value="'default_post_edit_rows','use_smilies','use_balanceTags','advanced_edit','ping_sites','mailserver_url', 'mailserver_port','mailserver_login','mailserver_pass','default_category'" /> 
    <table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
      <tr valign="top">
        <th scope="row"> <?php _e('When starting a post, show:') ?> </th>
        <td><?php get_settings('advanced_edit') ?><label>
          <input name="advanced_edit" type="radio" value="0" <?php checked('0', get_settings('advanced_edit')); ?> />
<?php _e('Simple controls') ?></label>
          <br />
          <label>
          <input name="advanced_edit" type="radio" value="1" <?php checked('1', get_settings('advanced_edit')); ?> />
<?php _e('Advanced controls') ?></label>
          <label for="advanced_edit"></label></td>
      </tr>
      <tr valign="top"> 
        <th width="33%" scope="row"> Size of the writing box:</th> 
        <td><input name="default_post_edit_rows" type="text" id="default_post_edit_rows" value="<?php echo get_settings('default_post_edit_rows'); ?>" size="2" style="width: 1.5em; " /> 
          lines </td> 
      </tr> 
      <tr valign="top">
        <th scope="row"><?php _e('Formatting:') ?></th>
        <td>          <label for="label">
          <input name="use_smilies" type="checkbox" id="label" value="1" <?php checked('1', get_settings('use_smilies')); ?> />
          <?php _e('Convert emoticons like <code>:-)</code> and <code>:-P</code> to graphics on display') ?></label> <br />          <label for="label2">
  <input name="use_balanceTags" type="checkbox" id="label2" value="1" <?php checked('1', get_settings('use_balanceTags')); ?> />
          <?php _e('WordPress should correct invalidly nested XHTML automatically') ?></label></td>
      </tr>
    </table> 
    <fieldset class="options">
	<legend><?php _e('Update Services') ?></legend>
          <p><?php printf(__('Enter the sites that you would like to notify when you publish a new post. For a list of some recommended sites to ping please see <a href="%s">Update Services</a> on the wiki. Separate multiple URIs by line breaks.'), 'http://wiki.wordpress.org/index.php/UpdateServices') ?></p>
	
	<textarea name="ping_sites" id="ping_sites" style="width: 98%;"><?php echo get_settings('ping_sites'); ?></textarea>
	</fieldset>
    <fieldset class="options">
	<legend><?php _e('Writing by Email') ?></legend>
	<p><?php printf(__('To post to WordPress by email you must set up a secret email account with POP3 access. Any mail received at this address will be posted, so it&#8217;s a good idea to keep this address very secret. Here are three random strings you could use: <code>%s</code>, <code>%s</code>, <code>%s</code>.'), substr(md5(uniqid(microtime())),0,5), substr(md5(uniqid(microtime())),0,5), substr(md5(uniqid(microtime())),0,5)) ?></p>
	
		<table width="100%" cellspacing="2" cellpadding="5" class="editform">
        	<tr valign="top">
                <th scope="row"><?php _e('Mail server:') ?></th>
        		<td><input name="mailserver_url" type="text" id="mailserver_url" value="<?php echo get_settings('mailserver_url'); ?>" size="40" />
                <label for="port"><?php _e('Port:') ?></label> 
				<input name="mailserver_port" type="text" id="mailserver_port" value="<?php echo get_settings('mailserver_port'); ?>" size="6" />
       			</td>
       		</tr>
        	<tr valign="top">
                <th width="33%" scope="row"><?php _e('Login name:') ?></th>
        		<td><input name="mailserver_login" type="text" id="mailserver_login" value="<?php echo get_settings('mailserver_login'); ?>" size="40" /></td>
       		</tr>
        	<tr valign="top">
                <th scope="row"><?php _e('Password:') ?></th>
        		<td>
        			<input name="mailserver_pass" type="text" id="mailserver_pass" value="<?php echo get_settings('mailserver_pass'); ?>" size="40" />
        		</td>
       		</tr>
        	<tr valign="top">
                <th scope="row"><?php _e('Usual category:') ?></th>
        		<td><select name="default_category" id="default_category">
<?php
$categories = $wpdb->get_results("SELECT * FROM $tablecategories ORDER BY cat_name");
foreach ($categories as $category) :
if ($category->cat_ID == get_settings('default_category')) $selected = " selected='selected'";
else $selected = '';
	echo "\n\t<option value='$category->cat_ID' $selected>$category->cat_name</option>";
endforeach;
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

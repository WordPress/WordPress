<?php

require_once('admin.php');

if ( !current_user_can('edit_plugins') )
                wp_die('<p>'.__('You do not have sufficient permissions to update plugins for this blog.').'</p>');

function request_filesystem_credentials($form_post, $type = '') {
	if ( empty($type) )
		$type = get_filesystem_method();

	if ( 'direct' == $type )
		return array();

	if ( !empty($_POST['password']) && !empty($_POST['username']) && !empty($_POST['hostname']) ) {
		$credentials = array('hostname' => $_POST['hostname'], 'username' => $_POST['username'],
			'password' => $_POST['password'], 'ssl' => $_POST['ssl']);
		$stored_credentials = $credentials;
		unset($stored_credentials['password']);
		update_option('ftp_credentials', $stored_credentials);
		return $credentials;
	}
	$hostname = '';
	$username = '';
	$password = '';
	$ssl = '';
	if ( $credentials = get_option('ftp_credentials') )
		extract($credentials, EXTR_OVERWRITE);
?>
<form action="<?php echo $form_post ?>" method="post">
<div class="wrap">
<h2><?php _e('FTP Connection Information') ?></h2>
<p><?php _e('To perform the requested update, FTP connection information is required.') ?></p>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Hostname:') ?></th>
<td><input name="hostname" type="text" id="hostname" value="<?php echo attribute_escape($hostname) ?>" size="40" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Username:') ?></th>
<td><input name="username" type="text" id="username" value="<?php echo attribute_escape($username) ?>" size="40" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Password:') ?></th>
<td><input name="password" type="text" id="password" value="<?php echo attribute_escape($password) ?>" size="40" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Use SSL:') ?></th>
<td>
<select name="ssl" id="ssl">
<?php
foreach ( array(0 => __('No'), 1 => __('Yes')) as $key => $value ) :
	$selected = ($ssl == $value) ? 'selected="selected"' : '';
	echo "\n\t<option value='$key' $selected>" . $value . '</option>';
endforeach;
?>
</select>
</td>
</tr>
</table>
<p class="submit">
<input type="submit" name="submit" value="<?php _e('Proceed'); ?>" />
</p>
</div>
</form>
<?php
	return false;
}

function show_message($message) {
	if( is_wp_error($message) ){
		if( $message->get_error_data() )
			$message = $message->get_error_message() . ': ' . $message->get_error_data();
		else 
			$message = $message->get_error_message();
	}
	echo "<p>$message</p>";
}

function do_plugin_upgrade($plugin) {
	global $wp_filesystem;

	$credentials = request_filesystem_credentials("update.php?action=upgrade-plugin&plugin=$plugin");
	if ( false === $credentials )
		return;
	echo '<div class="wrap">';
	echo '<h2>' . __('Upgrade Plugin') . '</h2>';
	WP_Filesystem($credentials);
	// TODO: look for auth and connect error codes and direct back to credentials form.
	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
			show_message($message);
		echo '</div>';
		return;
	}

	$result = wp_update_plugin($plugin, 'show_message');

	if ( is_wp_error($result) )
		show_message($result);
	else
		echo __('Plugin upgraded successfully');
	echo '</div>';
}

if ( isset($_GET['action']) ) {
	if ( isset($_GET['plugin']) )
		$plugin = trim($_GET['plugin']);

	if ( 'upgrade-plugin' == $_GET['action'] ) {
		//check-admin_referer('upgrade-plugin_' . $plugin);
		$title = __('Upgrade Plugin');
		$parent_file = 'plugins.php';
		require_once('admin-header.php');
		do_plugin_upgrade($plugin);
		include('admin-footer.php');
	}

}

?>
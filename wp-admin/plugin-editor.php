<?php
require_once('admin.php');

$title = __("Edit Plugins");
$parent_file = 'plugins.php';

wp_reset_vars(array('action', 'redirect', 'profile', 'error', 'warning', 'a', 'file'));

wp_admin_css( 'theme-editor' );

$plugins = get_plugins();
$plugin_files = array_keys($plugins);

if (empty($file))
	$file = $plugin_files[0];

$file = validate_file_to_edit($file, $plugin_files);
$real_file = WP_PLUGIN_DIR . '/' . $file;

switch($action) {

case 'update':

	check_admin_referer('edit-plugin_' . $file);

	if ( !current_user_can('edit_plugins') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit templates for this blog.').'</p>');

	$newcontent = stripslashes($_POST['newcontent']);
	if ( is_writeable($real_file) ) {
		$f = fopen($real_file, 'w+');
		fwrite($f, $newcontent);
		fclose($f);

		// Deactivate so we can test it.
		if ( is_plugin_active($file) || isset($_POST['phperror']) ) {
			if ( is_plugin_active($file) )
				deactivate_plugins($file, true);
			wp_redirect(add_query_arg('_wpnonce', wp_create_nonce('edit-plugin-test_' . $file), "plugin-editor.php?file=$file&liveupdate=1"));
			exit;
		}
		wp_redirect("plugin-editor.php?file=$file&a=te");
	} else {
		wp_redirect("plugin-editor.php?file=$file");
	}
	exit;

break;

default:

	if ( !current_user_can('edit_plugins') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit plugins for this blog.').'</p>');

	if ( isset($_GET['liveupdate']) ) {
		check_admin_referer('edit-plugin-test_' . $file);

		$error = validate_plugin($file);
		if( is_wp_error($error) )
			wp_die( $error );

		if ( ! is_plugin_active($file) )
			activate_plugin($file, "plugin-editor.php?file=$file&phperror=1");// we'll override this later if the plugin can be included without fatal error

		wp_redirect("plugin-editor.php?file=$file&a=te");
		exit;
	}

	require_once('admin-header.php');

	update_recently_edited(WP_PLUGIN_DIR . '/' . $file);

	if ( ! is_file($real_file) )
		$error = 1;

	if ( ! $error )
		$content = htmlspecialchars(file_get_contents($real_file));

	?>
<?php if (isset($_GET['a'])) : ?>
 <div id="message" class="updated fade"><p><?php _e('File edited successfully.') ?></p></div>
<?php elseif (isset($_GET['phperror'])) : ?>
 <div id="message" class="updated fade"><p><?php _e('This plugin has been deactivated because your changes resulted in a <strong>fatal error</strong>.') ?></p>
	<?php
		if ( wp_verify_nonce($_GET['_error_nonce'], 'plugin-activation-error_' . $file) ) { ?>
	<iframe style="border:0" width="100%" height="70px" src="<?php bloginfo('wpurl'); ?>/wp-admin/plugins.php?action=error_scrape&amp;plugin=<?php echo attribute_escape($file); ?>&amp;_wpnonce=<?php echo attribute_escape($_GET['_error_nonce']); ?>"></iframe>
	<?php } ?>
</div>
<?php endif; ?>
 <div class="wrap">
<div class="bordertitle">
	<h2><?php _e('Plugin Editor'); ?></h2>
</div>
<div class="tablenav">
<div class="alignleft">
<big><strong><?php
	if ( is_plugin_active($file) ) {
		if ( is_writeable($real_file) )
			echo sprintf(__('Editing <strong>%s</strong> (active)'), $file);
		else
			echo sprintf(__('Browsing <strong>%s</strong> (active)'), $file);
	} else {
		if ( is_writeable($real_file) )
			echo sprintf(__('Editing <strong>%s</strong> (inactive)'), $file);
		else
			echo sprintf(__('Browsing <strong>%s</strong> (inactive)'), $file);
	}
	?></strong></big>
</div>
<br class="clear" />
</div>
<br class="clear" />
	<div id="templateside">
	<h3 id="bordertitle"><?php _e('Plugin Files'); ?></h3>

	<h4><?php _e('Plugins'); ?></h4>
	<ul>
<?php foreach($plugin_files as $plugin_file) : ?>
		<li><a href="plugin-editor.php?file=<?php echo $plugin_file; ?>"><?php echo $plugins[$plugin_file]['Name']; ?></a></li>
<?php endforeach; ?>
	</ul>
	</div>
<?php	if ( ! $error ) { ?>
	<form name="template" id="template" action="plugin-editor.php" method="post">
	<?php wp_nonce_field('edit-plugin_' . $file) ?>
		<div><textarea cols="70" rows="25" name="newcontent" id="newcontent" tabindex="1"><?php echo $content ?></textarea>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="file" value="<?php echo $file ?>" />
		</div>
<?php if ( is_writeable($real_file) ) : ?>
	<?php if ( in_array($file, (array) get_option('active_plugins')) ) { ?>
		<p><?php _e('<strong>Warning:</strong> Making changes to active plugins is not recommended.  If your changes cause a fatal error, the plugin will be automatically deactivated.'); ?></p>
	<?php } ?>
	<p class="submit">
	<?php
		if ( isset($_GET['phperror']) )
			echo "<input type='hidden' name='phperror' value='1' /><input type='submit' name='submit' value='" . __('Update File and Attempt to Reactivate') . "' tabindex='2' />";
		else
			echo "<input type='submit' name='submit' value='" . __('Update File') . "' tabindex='2' />";
	?>
	</p>
<?php else : ?>
	<p><em><?php _e('You need to make this file writable before you can save your changes. See <a href="http://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.'); ?></em></p>
<?php endif; ?>
 </form>
<?php
	} else {
		echo '<div class="error"><p>' . __('Oops, no such file exists! Double check the name and try again, merci.') . '</p></div>';
	}
?>
<div class="clear"> &nbsp; </div>
</div>
<?php
	break;
}
include("admin-footer.php") ?>
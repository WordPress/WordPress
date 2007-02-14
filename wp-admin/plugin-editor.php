<?php
require_once('admin.php');

$title = __("Edit Plugins");
$parent_file = 'plugins.php';

wp_reset_vars(array('action', 'redirect', 'profile', 'error', 'warning', 'a', 'file'));

$plugins = get_plugins();
$plugin_files = array_keys($plugins);

if (empty($file)) {
	$file = $plugin_files[0];
}

$file = validate_file_to_edit($file, $plugin_files);
$real_file = get_real_file_to_edit( PLUGINDIR . "/$file");

switch($action) {

case 'update':

	check_admin_referer('edit-plugin_' . $file);

	if ( !current_user_can('edit_plugins') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit templates for this blog.').'</p>');

	$newcontent = stripslashes($_POST['newcontent']);
	if (is_writeable($real_file)) {
		$f = fopen($real_file, 'w+');
		fwrite($f, $newcontent);
		fclose($f);

		// Deactivate so we can test it.
		$current = get_option('active_plugins');
		if ( in_array($file, $current) || isset($_POST['phperror']) ) {
			if ( in_array($file, $current) ) {
				array_splice($current, array_search( $file, $current), 1 ); // Array-fu!
				update_option('active_plugins', $current);
			}
			wp_redirect(add_query_arg('_wpnonce', wp_create_nonce('edit-plugin-test_' . $file), "plugin-editor.php?file=$file&liveupdate=1"));
			exit();
		}
		wp_redirect("plugin-editor.php?file=$file&a=te");
	} else {
		wp_redirect("plugin-editor.php?file=$file");
	}

	exit();

break;

default:

	if ( !current_user_can('edit_plugins') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit plugins for this blog.').'</p>');

	if ( $_GET['liveupdate'] ) {
		check_admin_referer('edit-plugin-test_' . $file);
		$current = get_option('active_plugins');
		$plugin = $file;
		if ( validate_file($plugin) )
			wp_die(__('Invalid plugin.'));
		if ( ! file_exists(ABSPATH . PLUGINDIR . '/' . $plugin) )
			wp_die(__('Plugin file does not exist.'));
		if (!in_array($plugin, $current)) {
			wp_redirect("plugin-editor.php?file=$file&phperror=1"); // we'll override this later if the plugin can be included without fatal error
			@include(ABSPATH . PLUGINDIR . '/' . $plugin);
			$current[] = $plugin;
			sort($current);
			update_option('active_plugins', $current);
		}
		wp_redirect("plugin-editor.php?file=$file&a=te");
	}

	require_once('admin-header.php');

	update_recently_edited(PLUGINDIR . "/$file");

	if (!is_file($real_file))
		$error = 1;

	if (!$error) {
		$f = fopen($real_file, 'r');
		$content = fread($f, filesize($real_file));
		$content = htmlspecialchars($content);
	}

	?>
<?php if (isset($_GET['a'])) : ?>
 <div id="message" class="updated fade"><p><?php _e('File edited successfully.') ?></p></div>
<?php elseif (isset($_GET['phperror'])) : ?>
 <div id="message" class="updated fade"><p><?php _e('This plugin has been deactivated because your changes resulted in a <strong>fatal error</strong>.') ?></p></div>
<?php endif; ?>
 <div class="wrap">
	<?php
	if ( in_array($file, (array) get_option('active_plugins')) ) {
		if (is_writeable($real_file)) {
			echo '<h2>' . sprintf(__('Editing <strong>%s</strong> (active)'), $file) . '</h2>';
		} else {
		echo '<h2>' . sprintf(__('Browsing <strong>%s</strong> (active)'), $file) . '</h2>';
		}
	} else {
		if (is_writeable($real_file)) {
			echo '<h2>' . sprintf(__('Editing <strong>%s</strong> (inactive)'), $file) . '</h2>';
		} else {
		echo '<h2>' . sprintf(__('Browsing <strong>%s</strong> (inactive)'), $file) . '</h2>';
		}
	}
	?>
	<div id="templateside">
<h3><?php _e('Plugin files') ?></h3>

<?php
if ($plugin_files) :
?>
	<ul>
	<?php foreach($plugin_files as $plugin_file) : ?>
		 <li><a href="plugin-editor.php?file=<?php echo "$plugin_file"; ?>"><?php echo $plugins[$plugin_file]['Name']; ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
</div>
<?php	if (!$error) { ?>
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
			echo "<input type='hidden' name='phperror' value='1' /><input type='submit' name='submit' value='" . __('Update File and Attempt to Reactivate &raquo;') . "' tabindex='2' />";
		else
			echo "<input type='submit' name='submit' value='" . __('Update File &raquo;') . "' tabindex='2' />";
	?>
	</p>
<?php else : ?>
	<p><em><?php _e('If this file were writable you could edit it.'); ?></em></p>
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

<?php
require_once('admin.php');

if ( isset($_GET['action']) ) {
	check_admin_referer();

	if ('activate' == $_GET['action']) {
		$current = get_settings('active_plugins');
		if (!in_array($_GET['plugin'], $current)) {
			$current[] = trim( $_GET['plugin'] );
		}
		sort($current);
		update_option('active_plugins', $current);
		header('Location: plugins.php?activate=true');
	}
	
	if ('deactivate' == $_GET['action']) {
		$current = get_settings('active_plugins');
		array_splice($current, array_search( $_GET['plugin'], $current), 1 ); // Array-fu!
		update_option('active_plugins', $current);
		header('Location: plugins.php?deactivate=true');
	}
}

$title = __('Manage Plugins');
require_once('admin-header.php');

// Clean up options
// If any plugins don't exist, axe 'em

$check_plugins = get_settings('active_plugins');
foreach ($check_plugins as $check_plugin) {
	if (!file_exists(ABSPATH . 'wp-content/plugins/' . $check_plugin)) {
			$current = get_settings('active_plugins');
			unset($current[$_GET['plugin']]);
			update_option('active_plugins', $current);
	}
}
?>

<?php if (isset($_GET['activate'])) : ?>
<div class="updated"><p><?php _e('Plugin <strong>activated</strong>.') ?></p>
</div>
<?php endif; ?>
<?php if (isset($_GET['deactivate'])) : ?>
<div class="updated"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p>
</div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins are files you usually download separately from WordPress that add functionality. To install a plugin you generally just need to put the plugin file into your <code>wp-content/plugins</code> directory. Once a plugin is installed, you may activate it or deactivate it here. If something goes wrong with a plugin and you can&#8217;t use WordPress, delete that plugin from the <code>wp-content/plugins</code> directory and it will be automatically deactivated.'); ?></p>
<?php
// Files in wp-content/plugins directory
$plugins_dir = @ dir(ABSPATH . 'wp-content/plugins');
if ($plugins_dir) {
	while(($file = $plugins_dir->read()) !== false) {
	  if ( !preg_match('|^\.+$|', $file) && preg_match('|\.php$|', $file) ) 
		$plugin_files[] = $file;
	}
}

if ( get_settings('active_plugins') )
	$current_plugins = get_settings('active_plugins');

if (!$plugins_dir || !$plugin_files) {
	_e("<p>Couldn't open plugins directory or there are no plugins available.</p>"); // TODO: make more helpful
} else {
?>
<table width="100%" cellpadding="3" cellspacing="3">
	<tr>
		<th><?php _e('Plugin'); ?></th>
		<th><?php _e('Version'); ?></th>
		<th><?php _e('Author'); ?></th>
		<th><?php _e('Description'); ?></th>
		<th><?php _e('Action'); ?></th>
	</tr>
<?php
	sort($plugin_files); // Alphabetize by filename. Better way?
	$style = '';
	foreach($plugin_files as $plugin_file) {
		$plugin_data = implode('', file(ABSPATH . '/wp-content/plugins/' . $plugin_file));
		preg_match("|Plugin Name:(.*)|i", $plugin_data, $plugin_name);
		preg_match("|Plugin URI:(.*)|i", $plugin_data, $plugin_uri);
		preg_match("|Description:(.*)|i", $plugin_data, $description);
		preg_match("|Author:(.*)|i", $plugin_data, $author_name);
		preg_match("|Author URI:(.*)|i", $plugin_data, $author_uri);
		if ( preg_match("|Version:(.*)|i", $plugin_data, $version) )
			$version = $version[1];
		else
			$version ='';

		$description = wptexturize($description[1]);

		if ('' == $plugin_uri) {
			$plugin = $plugin_name[1];
		} else {
			$plugin = __("<a href='{$plugin_uri[1]}' title='Visit plugin homepage'>{$plugin_name[1]}</a>");
		}

		if ('' == $author_uri) {
			$author = $author_name[1];
		} else {
			$author = __("<a href='{$author_uri[1]}' title='Visit author homepage'>{$author_name[1]}</a>");
		}



		$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';

		if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
			$action = __("<a href='plugins.php?action=deactivate&amp;plugin=$plugin_file' title='Deactivate this plugin' class='delete'>Deactivate</a>");
			$plugin = __("<strong>$plugin</strong>");
		} else {
			$action = __("<a href='plugins.php?action=activate&amp;plugin=$plugin_file' title='Activate this plugin' class='edit'>Activate</a>");
		}
		echo "
	<tr $style>
		<td>$plugin</td>
		<td>$version</td>
		<td>$author</td>
		<td>$description</td>
		<td>$action</td>
	</tr>";
	}
?>

</table>
<?php
}
?>
</div>

<?php
include('admin-footer.php');
?>
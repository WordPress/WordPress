<?php

if ($_GET['action']) {
	$standalone = 1;
	require_once('admin-header.php');
	if ('activate' == $_GET['action']) {
		$current = "\n" . get_settings('active_plugins') . "\n";
		$current = preg_replace("|(\n)+\s*|", "\n", $current);
		$current = trim($current) . "\n " . trim($_GET['plugin']);
		$current = trim($current);
		$current = preg_replace("|\n\s*|", "\n", $current); // I don't know where this is coming from
		update_option('active_plugins', $current);
		header('Location: plugins.php?activate=true');
	}
	
	if ('deactivate' == $_GET['action']) {
		$current = "\n" . get_settings('active_plugins') . "\n";
		$current = str_replace("\n" . $_GET['plugin'], '', $current);
		$current = preg_replace("|(\n)+\s*|", "\n", $current);
		update_option('active_plugins', trim($current));
		header('Location: plugins.php?deactivate=true');
	}
}

$title = 'Manage Plugins';
require_once('admin-header.php');

if ($user_level < 9) // Must be at least level 9
	die ("Sorry, you must be at least a level 8 user to modify plugins.");

// Clean up options
// if any files are in the option that don't exist, axe 'em

$check_plugins = explode("\n", (get_settings('active_plugins')));
foreach ($check_plugins as $check_plugin) {
	if (!file_exists(ABSPATH . 'wp-content/plugins/' . $check_plugin)) {
			$current = get_settings('active_plugins') . "\n";
			$current = str_replace($check_plugin . "\n", '', $current);
			$current = preg_replace("|\n+|", "\n", $current);
			update_option('active_plugins', trim($current));
	}
}



?>

<?php if ($_GET['activate']) : ?>
<div class="updated"><p>Plugin <strong>activated</strong>.</p>
</div>
<?php endif; ?>
<?php if ($_GET['deactivate']) : ?>
<div class="updated"><p>Plugin <strong>deactivated</strong>.</p>
</div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins are files you usually download seperately from WordPress that add functionality. To install a plugin you generally just need to put the plugin file into your <code>wp-content/plugins</code> directory. Once a plugin is installed, you may activate it or deactivate it here.'); ?></p>
<?php
// Files in wp-content/plugins directory
$plugins_dir = @ dir(ABSPATH . 'wp-content/plugins');
if ($plugins_dir) {
	while(($file = $plugins_dir->read()) !== false) {
	  if ( !preg_match('|^\.+$|', $file) && preg_match('|\.php$|', $file) ) 
		$plugin_files[] = $file;
	}
}

if ('' != trim(get_settings('active_plugins'))) {
	$current_plugins = explode("\n", (get_settings('active_plugins')));
}

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
	foreach($plugin_files as $plugin_file) {
		$plugin_data = implode('', file(ABSPATH . '/wp-content/plugins/' . $plugin_file));
		preg_match("|Plugin Name:(.*)|i", $plugin_data, $plugin_name);
		preg_match("|Plugin URI:(.*)|i", $plugin_data, $plugin_uri);
		preg_match("|Description:(.*)|i", $plugin_data, $description);
		preg_match("|Author:(.*)|i", $plugin_data, $author_name);
		preg_match("|Author URI:(.*)|i", $plugin_data, $author_uri);
		preg_match("|Version:(.*)|i", $plugin_data, $version);
		$description = wptexturize(wp_filter_kses($description[1]));

		if ('' == $plugin_uri) {
			$plugin = $plugin_name[1];
		} else {
			$plugin = wp_filter_kses( __("<a href='{$plugin_uri[1]}' title='Visit plugin homepage'>{$plugin_name[1]}</a>") );
		}

		if ('' == $author_uri) {
			$author = $author_name[1];
		} else {
			$author = wp_filter_kses( __("<a href='{$author_uri[1]}' title='Visit author homepage'>{$author_name[1]}</a>") );
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
		<td>{$version[1]}</td>
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
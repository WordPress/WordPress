<?php
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


if ('activate' == $_GET['action']) {
	$current = "\n" . get_settings('active_plugins') . "\n";
	$current = preg_replace("|(\n)+\s*|", "\n", $current);
	$current = trim($current) . "\n " . trim($_GET['plugin']);
	$current = trim($current);
	$current = preg_replace('|\n\s*|', '\n', $current); // I don't know where this is coming from
	update_option('active_plugins', $current);
	header('Location: plugins.php');
}

if ('deactivate' == $_GET['action']) {
	$current = "\n" . get_settings('active_plugins') . "\n";
	$current = str_replace("\n" . $_GET['plugin'], '', $current);
	$current = preg_replace("|(\n)+\s*|", "\n", $current);
	update_option('active_plugins', trim($current));
	header('Location: plugins.php');
}

?>
<div class="wrap">
<?php
// Files in wp-content/plugins directory
$plugins_dir = @ dir(ABSPATH . 'wp-content/plugins');
if ($plugins_dir) {
	while(($file = $plugins_dir->read()) !== false) {
	  if (!preg_match('|^\.+$|', $file)) $plugin_files[] = $file;
	}
}

if ('' != trim(get_settings('active_plugins'))) {
	$current_plugins = explode("\n", (get_settings('active_plugins')));
}

if (!$plugins_dir || !$plugin_files) {
	echo "<p>Couldn't open plugins directory or there are no plugins available.</p>"; // TODO: make more helpful
} else {
?>
<table width="100%" cellpadding="3" cellspacing="3">
	<tr>
		<th>Plugin</th>
		<th>Author</th>
		<th>Description</th>
		<th>Action</th>
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
		$description = wptexturize(wp_filter_kses($description[1]));

		if ('' == $plugin_uri) {
			$plugin = $plugin_name[1];
		} else {
			$plugin = wp_filter_kses("<a href='{$plugin_uri[1]}' title='Visit plugin homepage'>{$plugin_name[1]}</a>");
		}

		if ('' == $author_uri) {
			$author = $author_name[1];
		} else {
			$author = wp_filter_kses("<a href='{$author_uri[1]}' title='Visit author homepage'>{$author_name[1]}</a>");
		}



		$style = ('class="alternate"' == $style) ? '' : 'class="alternate"';

		if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
			$action = "<a href='plugins.php?action=deactivate&amp;plugin=$plugin_file' title='Deactivate this plugin' class='delete'>Deactivate</a>";
		} else {
			$action = "<a href='plugins.php?action=activate&amp;plugin=$plugin_file' title='Activate this plugin' class='edit'>Activate</a>";
		}
		echo "
	<tr $style>
		<td>$plugin</td>
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

<?php
$title = 'Manage Plugins';

require_once('admin-header.php');

if ($user_level == 0) //Checks to see if user has logged in
	die ("Cheatin' uh ?");

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
	$current_plugins = unserialize(get_settings('active_plugins'));
}

/*
Plugin Name: matt's cool plugin
Plugin URI: http://photomatt.net/plugins/cool-plugin
Description: blah blah blah anything until a newline
Author: photo matt
Author URI: http://photomatt.net
*/ 

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
		echo "
	<tr $style>
		<td>$plugin</td>
		<td>$author</td>
		<td>$description</td>
		<td><a href='' class='edit'>activate</a></td>
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

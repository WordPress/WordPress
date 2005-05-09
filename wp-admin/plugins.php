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

// Sanity check.  If the active plugin list is not an array, make it an
// empty array.
if ( !is_array($check_plugins) ) {
	$check_plugins = array();
	update_option('active_plugins', $check_plugins);	
}

// If a plugin file does not exist, remove it from the list of active
// plugins.
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

if ( get_settings('active_plugins') )
	$current_plugins = get_settings('active_plugins');

$plugins = get_plugins();

if (empty($plugins)) {
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
	$style = '';
	foreach($plugins as $plugin_file => $plugin_data) {
		$style = ('class="alternate"' == $style|| 'class="alternate active"' == $style) ? '' : 'alternate';

		if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
			$action = "<a href='plugins.php?action=deactivate&amp;plugin=$plugin_file' title='".__('Deactivate this plugin')."' class='delete'>".__('Deactivate')."</a>";
			$plugin_data['Title'] = "<strong>{$plugin_data['Title']}</strong>";
			$style .= $style == 'alternate' ? ' active' : 'active';
		} else {
			$action = "<a href='plugins.php?action=activate&amp;plugin=$plugin_file' title='".__('Activate this plugin')."' class='edit'>".__('Activate')."</a>";
		}
		$plugin_data['Description'] = wp_kses($plugin_data['Description'], array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array()) ); ;
		if ($style != '') $style = 'class="' . $style . '"';
		echo "
	<tr $style>
		<td class=\"name\">{$plugin_data['Title']}</td>
		<td class=\"vers\">{$plugin_data['Version']}</td>
		<td class=\"auth\">{$plugin_data['Author']}</td>
		<td class=\"desc\">{$plugin_data['Description']}</td>
		<td class=\"togl\">$action</td>
	</tr>";
	}
?>

</table>
<?php
}
?>

<h2><?php _e('Get More Plugins'); ?></h2>
<p><?php _e('You can find additional plugins for your site in the <a href="http://wordpress.org/extend/plugins/">WordPress plugin directory</a>. To install a plugin you generally just need to upload the plugin file into your <code>wp-content/plugins</code> directory. Once a plugin is uploaded, you may activate it here.'); ?></p>

</div>

<?php
include('admin-footer.php');
?>

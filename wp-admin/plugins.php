<?php
require_once('admin.php');

if ( isset($_GET['action']) ) {
	if ('activate' == $_GET['action']) {
		check_admin_referer('activate-plugin' . $_GET['plugin']);
		$current = get_settings('active_plugins');
		if (!in_array($_GET['plugin'], $current)) {
			$current[] = trim( $_GET['plugin'] );
			sort($current);
			update_option('active_plugins', $current);
			include(ABSPATH . 'wp-content/plugins/' . trim( $_GET['plugin'] ));
			do_action('activate_' . trim( $_GET['plugin'] ));
		}
		header('Location: plugins.php?activate=true');
	} else if ('deactivate' == $_GET['action']) {
		check_admin_referer('deactivate-plugin' . $_GET['plugin']);
		$current = get_settings('active_plugins');
		array_splice($current, array_search( $_GET['plugin'], $current), 1 ); // Array-fu!
		update_option('active_plugins', $current);
		do_action('deactivate_' . trim( $_GET['plugin'] ));
		header('Location: plugins.php?deactivate=true');
	}
	exit;
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
			$key = array_search($check_plugin, $current);
			if ( false !== $key && NULL !== $key ) {
				unset($current[$key]);
				update_option('active_plugins', $current);
			}
	}
}
?>

<?php if (isset($_GET['activate'])) : ?>
<div id="message" class="updated fade"><p><?php _e('Plugin <strong>activated</strong>.') ?></p>
</div>
<?php endif; ?>
<?php if (isset($_GET['deactivate'])) : ?>
<div id="message" class="updated fade"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p>
</div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.'); ?></p>
<?php

if ( get_settings('active_plugins') )
	$current_plugins = get_settings('active_plugins');

$plugins = get_plugins();

if (empty($plugins)) {
	echo '<p>';
	_e("Couldn't open plugins directory or there are no plugins available."); // TODO: make more helpful
	echo '</p>';
} else {
?>
<table class="widefat">
	<thead>
	<tr>
		<th style="text-align: left"><?php _e('Plugin'); ?></th>
		<th><?php _e('Version'); ?></th>
		<th style="text-align: left"><?php _e('Description'); ?></th>
		<th><?php _e('Action'); ?></th>
	</tr>
	</thead>
<?php
	$style = '';

	function sort_plugins($plug1, $plug2) {
		return strnatcasecmp($plug1['Name'], $plug2['Name']);
	}

	uksort($plugins, 'sort_plugins');

	foreach($plugins as $plugin_file => $plugin_data) {
		$style = ('class="alternate"' == $style|| 'class="alternate active"' == $style) ? '' : 'alternate';

		if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
			$action = "<a href='" . wp_nonce_url("plugins.php?action=deactivate&amp;plugin=$plugin_file", 'deactivate-plugin' . $plugin_file) . "' title='".__('Deactivate this plugin')."' class='delete'>".__('Deactivate')."</a>";
			$plugin_data['Title'] = "<strong>{$plugin_data['Title']}</strong>";
			$style .= $style == 'alternate' ? ' active' : 'active';
		} else {
			$action = "<a href='" . wp_nonce_url("plugins.php?action=activate&amp;plugin=$plugin_file", 'activate-plugin' . $plugin_file) . "' title='".__('Activate this plugin')."' class='edit'>".__('Activate')."</a>";
		}
		$plugin_data['Description'] = wp_kses($plugin_data['Description'], array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array()) ); ;
		if ($style != '') $style = 'class="' . $style . '"';
		echo "
	<tr $style>
		<td class='name'>{$plugin_data['Title']}</td>
		<td class='vers'>{$plugin_data['Version']}</td>
		<td class='desc'>{$plugin_data['Description']} <cite>".sprintf(__('By %s'), $plugin_data['Author']).".</cite></td>
		<td class='togl'>$action</td>
	</tr>";
	}
?>

</table>
<?php
}
?>

<p><?php _e('If something goes wrong with a plugin and you can&#8217;t use WordPress, delete or rename that file in the <code>wp-content/plugins</code> directory and it will be automatically deactivated.'); ?></p>

<h2><?php _e('Get More Plugins'); ?></h2>
<p><?php _e('You can find additional plugins for your site in the <a href="http://wordpress.org/extend/plugins/">WordPress plugin directory</a>. To install a plugin you generally just need to upload the plugin file into your <code>wp-content/plugins</code> directory. Once a plugin is uploaded, you may activate it here.'); ?></p>

</div>

<?php
include('admin-footer.php');
?>

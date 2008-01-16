<?php
require_once('admin.php');

if ( isset($_GET['action']) ) {
	if ( isset($_GET['plugin']) )
		$plugin = trim($_GET['plugin']);

	if ( 'activate' == $_GET['action'] ) {
		check_admin_referer('activate-plugin_' . $_GET['plugin']);
		$result = activate_plugin($_GET['plugin'], 'plugins.php?error=true&plugin=' . $plugin);
		if ( is_wp_error( $result ) )
			wp_die( $result->get_error_message() );
		wp_redirect('plugins.php?activate=true'); // overrides the ?error=true one above
	} elseif ( 'error_scrape' == $_GET['action'] ) {
		check_admin_referer('plugin-activation-error_' . $plugin);
		$valid = validate_plugin($plugin);
		if ( is_wp_error($valid) )
			wp_die($valid);
		include(ABSPATH . PLUGINDIR . '/' . $plugin);
	} elseif ( 'deactivate' == $_GET['action'] ) {
		check_admin_referer('deactivate-plugin_' . $_GET['plugin']);
		deactivate_plugins($_GET['plugin']);
		wp_redirect('plugins.php?deactivate=true');
	} elseif ( 'deactivate-all' == $_GET['action'] ) {
		check_admin_referer('deactivate-all');
		deactivate_all_plugins();
		wp_redirect('plugins.php?deactivate-all=true');
	} elseif ('reactivate-all' == $_GET['action']) {
		check_admin_referer('reactivate-all');
		reactivate_all_plugins('plugins.php?errors=true');
		wp_redirect('plugins.php?reactivate-all=true'); // overrides the ?error=true one above
	}

	exit;
}

$title = __('Manage Plugins');
require_once('admin-header.php');

validate_active_plugins();

?>

<?php if ( isset($_GET['error']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin could not be activated because it triggered a <strong>fatal error</strong>.') ?></p>
	<?php
		$plugin = trim($_GET['plugin']);
		if ( wp_verify_nonce($_GET['_error_nonce'], 'plugin-activation-error_' . $plugin) && 1 == strtolower(ini_get('display_errors'))) { ?>
	<iframe style="border:0" width="100%" height="70px" src="<?php bloginfo('wpurl'); ?>/wp-admin/plugins.php?action=error_scrape&amp;plugin=<?php echo attribute_escape($plugin); ?>&amp;_wpnonce=<?php echo attribute_escape($_GET['_error_nonce']); ?>"></iframe>
	<?php
		}
	?>
	</div>
<?php elseif ( isset($_GET['errors']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Some plugins could not be reactivated because they triggered a <strong>fatal error</strong>.') ?></p></div>
<?php elseif ( isset($_GET['activate']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin <strong>activated</strong>.') ?></p></div>
<?php elseif ( isset($_GET['deactivate']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p></div>
<?php elseif (isset($_GET['deactivate-all'])) : ?>
	<div id="message" class="updated fade"><p><?php _e('All plugins <strong>deactivated</strong>.'); ?></p></div>
<?php elseif (isset($_GET['reactivate-all'])) : ?>
	<div id="message" class="updated fade"><p><?php _e('All plugins <strong>reactivated</strong>.'); ?></p></div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.'); ?></p>
<?php

if ( get_option('active_plugins') )
	$current_plugins = get_option('active_plugins');

$plugins = get_plugins();

if (empty($plugins)) {
	echo '<p>';
	_e("Couldn&#8217;t open plugins directory or there are no plugins available."); // TODO: make more helpful
	echo '</p>';
} else {
?>
<table class="widefat plugins">
	<thead>
	<tr>
		<th><?php _e('Plugin'); ?></th>
		<th style="text-align: center"><?php _e('Version'); ?></th>
		<th><?php _e('Description'); ?></th>
		<th style="text-align: center"<?php if ( current_user_can('edit_plugins') ) echo ' colspan="2"'; ?>><?php _e('Action'); ?></th>
	</tr>
	</thead>
<?php
	$style = '';

	foreach($plugins as $plugin_file => $plugin_data) {
		$style = ('class="alternate"' == $style|| 'class="alternate active"' == $style) ? '' : 'alternate';

		if (!empty($current_plugins) && in_array($plugin_file, $current_plugins)) {
			$toggle = "<a href='" . wp_nonce_url("plugins.php?action=deactivate&amp;plugin=$plugin_file", 'deactivate-plugin_' . $plugin_file) . "' title='".__('Deactivate this plugin')."' class='delete'>".__('Deactivate')."</a>";
			$plugin_data['Title'] = "<strong>{$plugin_data['Title']}</strong>";
			$style .= $style == 'alternate' ? ' active' : 'active';
		} else {
			$toggle = "<a href='" . wp_nonce_url("plugins.php?action=activate&amp;plugin=$plugin_file", 'activate-plugin_' . $plugin_file) . "' title='".__('Activate this plugin')."' class='edit'>".__('Activate')."</a>";
		}

		$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());

		// Sanitize all displayed data
		$plugin_data['Title']       = wp_kses($plugin_data['Title'], $plugins_allowedtags);
		$plugin_data['Version']     = wp_kses($plugin_data['Version'], $plugins_allowedtags);
		$plugin_data['Description'] = wp_kses($plugin_data['Description'], $plugins_allowedtags);
		$plugin_data['Author']      = wp_kses($plugin_data['Author'], $plugins_allowedtags);

		if ( $style != '' )
			$style = 'class="' . $style . '"';
		if ( is_writable(ABSPATH . PLUGINDIR . '/' . $plugin_file) )
			$edit = "<a href='plugin-editor.php?file=$plugin_file' title='".__('Open this file in the Plugin Editor')."' class='edit'>".__('Edit')."</a>";
		else
			$edit = '';

		$author = ( empty($plugin_data['Author']) ) ? '' :  ' <cite>' . sprintf( __('By %s'), $plugin_data['Author'] ) . '.</cite>';

		echo "
	<tr $style>
		<td class='name'>{$plugin_data['Title']}</td>
		<td class='vers'>{$plugin_data['Version']}</td>
		<td class='desc'><p>{$plugin_data['Description']}$author</p></td>
		<td class='togl'>$toggle</td>";
		if ( current_user_can('edit_plugins') )
		echo "
		<td>$edit</td>";
		echo"
	</tr>";
	do_action( 'after_plugin_row', $plugin_file );
	}
?>

<tr>
	<td colspan="3">&nbsp;</td>
	<td colspan="2" style="width:12em;">
	<?php 
	$active = get_option('active_plugins');
	$inactive = get_option('deactivated_plugins');
	if ( !empty($active) ) {
	?>
	<a href="<?php echo wp_nonce_url('plugins.php?action=deactivate-all', 'deactivate-all'); ?>" class="delete"><?php _e('Deactivate All Plugins'); ?></a>
	<?php 
	} elseif ( empty($active) && !empty($inactive) ) {
	?>
	<a href="<?php echo wp_nonce_url('plugins.php?action=reactivate-all', 'reactivate-all'); ?>" class="delete"><?php _e('Reactivate All Plugins'); ?></a>
	<?php
	} // endif active/inactive plugin check
	?>
</tr>

</table>
<?php
}
?>

<p><?php printf(__('If something goes wrong with a plugin and you can&#8217;t use WordPress, delete or rename that file in the <code>%s</code> directory and it will be automatically deactivated.'), PLUGINDIR); ?></p>

<h2><?php _e('Get More Plugins'); ?></h2>
<p><?php _e('You can find additional plugins for your site in the <a href="http://wordpress.org/extend/plugins/">WordPress plugin directory</a>.'); ?></p>
<p><?php printf(__('To install a plugin you generally just need to upload the plugin file into your <code>%s</code> directory. Once a plugin is uploaded, you may activate it here.'), PLUGINDIR); ?></p>

</div>

<?php
include('admin-footer.php');
?>

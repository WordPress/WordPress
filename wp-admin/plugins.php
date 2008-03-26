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
		error_reporting( E_ALL ^ E_NOTICE );
		@ini_set('display_errors', true); //Ensure that Fatal errors are displayed.
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
		if ( wp_verify_nonce($_GET['_error_nonce'], 'plugin-activation-error_' . $plugin) ) { ?>
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
	<div id="message" class="updated fade"><p><?php _e('Plugins <strong>reactivated</strong>.'); ?></p></div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.'); ?></p>
<?php

$plugins = get_plugins();

if (empty($plugins)) {
	echo '<p>';
	_e("Couldn&#8217;t open plugins directory or there are no plugins available."); // TODO: make more helpful
	echo '</p>';
} else {
?>

<div class="tablenav">
	<div class="alignleft">
	<?php
	$active = get_option('active_plugins');
	$inactive = get_option('deactivated_plugins');
	if ( !empty($active) ) {
	?>
	<a class="button-secondary" href="<?php echo wp_nonce_url('plugins.php?action=deactivate-all', 'deactivate-all'); ?>" class="delete"><?php _e('Deactivate All Plugins'); ?></a>
	<?php
	} elseif ( empty($active) && !empty($inactive) ) {
	?>
	<a class="button-secondary" href="<?php echo wp_nonce_url('plugins.php?action=reactivate-all', 'reactivate-all'); ?>" class="delete"><?php _e('Reactivate Plugins'); ?></a>
	<?php
	} // endif active/inactive plugin check
	?>
	</div>
	<br class="clear" />
</div>

<br class="clear" />

<table class="widefat">
	<thead>
	<tr>
		<th><?php _e('Plugin'); ?></th>
		<th class="num"><?php _e('Version'); ?></th>
		<th><?php _e('Description'); ?></th>
		<th class="status"><?php _e('Status') ?></th>
		<th class="action-links"><?php _e('Action'); ?></th>
	</tr>
	</thead>
	<tbody id="plugins">
<?php
	foreach($plugins as $plugin_file => $plugin_data) {
		$action_links = array();
		
		$style = '';

		if ( is_plugin_active($plugin_file) ) {
			$action_links[] = "<a href='" . wp_nonce_url("plugins.php?action=deactivate&amp;plugin=$plugin_file", 'deactivate-plugin_' . $plugin_file) . "' title='".__('Deactivate this plugin')."' class='delete'>".__('Deactivate')."</a>";
			$style = 'active';
		} else {
			$action_links[] = "<a href='" . wp_nonce_url("plugins.php?action=activate&amp;plugin=$plugin_file", 'activate-plugin_' . $plugin_file) . "' title='".__('Activate this plugin')."' class='edit'>".__('Activate')."</a>";
		}
		if ( current_user_can('edit_plugins') && is_writable(ABSPATH . PLUGINDIR . '/' . $plugin_file) )
			$action_links[] = "<a href='plugin-editor.php?file=$plugin_file' title='".__('Open this file in the Plugin Editor')."' class='edit'>".__('Edit')."</a>";

		$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());

		// Sanitize all displayed data
		$plugin_data['Title']       = wp_kses($plugin_data['Title'], $plugins_allowedtags);
		$plugin_data['Version']     = wp_kses($plugin_data['Version'], $plugins_allowedtags);
		$plugin_data['Description'] = wp_kses($plugin_data['Description'], $plugins_allowedtags);
		$plugin_data['Author']      = wp_kses($plugin_data['Author'], $plugins_allowedtags);
		$author = ( empty($plugin_data['Author']) ) ? '' :  ' <cite>' . sprintf( __('By %s'), $plugin_data['Author'] ) . '.</cite>';

		if ( $style != '' )
			$style = ' class="' . $style . '"';

		$action_links = apply_filters('plugin_action_links', $action_links, $plugin_file, $plugin_info);

		echo "
	<tr$style>
		<td class='name'>{$plugin_data['Title']}</td>
		<td class='vers'>{$plugin_data['Version']}</td>
		<td class='desc'><p>{$plugin_data['Description']}$author</p></td>
		<td class='status'>";
		if ( is_plugin_active($plugin_file) )
			echo  __('<span class="active">Active</span>');
		else
			_e('<span class="inactive">Inactive</span>');
		echo "</td>
		<td class='togl action-links'>$toggle";  
		if ( !empty($action_links) )
			echo implode(' | ', $action_links);
		echo "</td> 
	</tr>";
	do_action( 'after_plugin_row', $plugin_file );
	}
?>
	</tbody>
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

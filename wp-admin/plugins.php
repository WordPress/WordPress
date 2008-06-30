<?php
require_once('admin.php');

$action = '';
foreach( array('activate-selected', 'deactivate-selected', 'delete-selected', 'clear-recent-list') as $action_key ) {
	if( isset($_POST[$action_key]) ) {
		$action = $action_key;
		break;
	}
}

if( isset($_GET['action']) )
	$action = $_GET['action'];

$plugin = $_REQUEST['plugin'];

if( !empty($action) ) {
	switch( $action ) {
		case 'activate':
			check_admin_referer('activate-plugin_' . $plugin);
			$result = activate_plugin($plugin, 'plugins.php?error=true&plugin=' . $plugin);
			if ( is_wp_error( $result ) )
				wp_die( $result->get_error_message() );
			$recent = (array)get_option('recently_activated');
			if( isset($recent[ $plugin ]) ){
				unset($recent[ $plugin ]);
				update_option('recently_activated', $recent);
			}
			wp_redirect('plugins.php?activate=true'); // overrides the ?error=true one above
			exit;
			break;
		case 'activate-selected':
			check_admin_referer('bulk-manage-plugins');
			activate_plugins($_POST['checked'], 'plugins.php?error=true');

			$recent = (array)get_option('recently_activated');
			foreach( (array)$_POST['checked'] as $plugin => $time) {
				if( isset($recent[ $plugin ]) )
					unset($recent[ $plugin ]);
			}
			if( $recent != get_option('recently_activated') ) //If array changed, update it.
				update_option('recently_activated', $recent);

			wp_redirect('plugins.php?activate-multi=true');
			exit;
			break;
		case 'error_scrape':
			check_admin_referer('plugin-activation-error_' . $plugin);
			$valid = validate_plugin($plugin);
			if ( is_wp_error($valid) )
				wp_die($valid);
			error_reporting( E_ALL ^ E_NOTICE );
			@ini_set('display_errors', true); //Ensure that Fatal errors are displayed.
			include(WP_PLUGIN_DIR . '/' . $plugin);
			exit;
			break;
		case 'deactivate':
			check_admin_referer('deactivate-plugin_' . $plugin);
			deactivate_plugins($plugin);
			update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));
			wp_redirect('plugins.php?deactivate=true');
			exit;
			break;
		case 'deactivate-selected':
			check_admin_referer('bulk-manage-plugins');
			deactivate_plugins($_POST['checked']);
			$deactivated = array();
			foreach( (array)$_POST['checked'] as $plugin )
				$deactivated[ $plugin ] = time();
			update_option('recently_activated', $deactivated + (array)get_option('recently_activated'));
			wp_redirect('plugins.php?deactivate-multi=true');
			exit;
			break;
		case 'delete-selected':
			if( ! current_user_can('delete_plugins') )
				wp_die(__('You do not have sufficient permissions to delete plugins for this blog.'));
			
			check_admin_referer('bulk-manage-plugins');
			
			$plugins = $_REQUEST['checked']; //$_POST = from the plugin form; $_GET = from the FTP details screen.
			include(ABSPATH . 'wp-admin/update.php');

			$title = __('Delete Plugin');
			$parent_file = 'plugins.php';
			
			if( ! isset($_REQUEST['verify-delete']) ) {
				wp_enqueue_script('jquery');
				require_once('admin-header.php');
				?>
			<div class="wrap">
				<h2><?php _e('Delete Plugin(s)'); ?></h2>		
				<?php
					$files_to_delete = $plugin_info = array();
					foreach( (array) $plugins as $plugin ) {
						if( '.' == dirname($plugin) ) {
							$files_to_delete[] = WP_PLUGIN_DIR . '/' . $plugin;
							if( $data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin) )
								$plugin_info[ $plugin ] = $data;
						} else {
							//Locate all the files in that folder:
							$files = list_files( WP_PLUGIN_DIR . '/' . dirname($plugin) );
							if( $files ) {
								$files_to_delete = array_merge($files_to_delete, $files);
							}
							//Get plugins list from that folder
							if ( $folder_plugins = get_plugins( '/' . dirname($plugin)) )
								$plugin_info = array_merge($plugin_info, $folder_plugins);
						}
					}
				?>
				<p><?php _e('Deleting the selected plugins will remove the following plugin(s) and their files:'); ?></p>
				<p>
					<ul>
						<?php 
						foreach( $plugin_info as $plugin )
							echo '<li>', $plugin['Title'], ' ', __('By'), ' ', $plugin['Author'], '</li>';
						?>
					</ul>
				</p>
				<p><?php _e('Are you sure you wish to delete these files?') ?></p>
				<form method="post" action="<?php echo clean_url($_SERVER['REQUEST_URI']); ?>" style="display:inline;">
					<input type="hidden" name="verify-delete" value="1" />
					<input type="hidden" name="delete-selected" value="1" />
					<?php
						foreach( (array)$plugins as $plugin ) {
							$plugin = attribute_escape($plugin);
							echo "<input type='hidden' name='checked[]' value='$plugin' />";
						}
					?>
					<?php wp_nonce_field('bulk-manage-plugins') ?>
					<input type="submit" name="submit" value="<?php _e('Yes, Delete these files') ?>" class="button" />
				</form>
				<form method="post" action="<?php echo clean_url(wp_get_referer()); ?>" style="display:inline;">
					<input type="submit" name="submit" value="<?php _e('No, Return me to the plugin list') ?>" class="button" />
				</form>

				<p><a href="#" onclick="jQuery('#files-list').toggle(); return false;"><?php _e('Click to view entire list of files which will be deleted'); ?></a></p>
				<div id="files-list" style="display:none;">
					<ul>
					<?php
						foreach( (array)$files_to_delete as $file ) {
							$file = str_replace(ABSPATH, '', $file);
							echo "<li>$file</li>";
						}
					?>
					</ul>
				</div>				
			</div>
				<?php
				require_once('admin-footer.php');
				exit;
			} //Endif verify-delete
			$delete_result = delete_plugins($plugins);

			wp_cache_delete('plugins', 'plugins');
			break;
		case 'clear-recent-list':
			update_option('recently_activated', array());
			break;
	}
}

wp_enqueue_script('admin-forms');

$title = __('Manage Plugins');
require_once('admin-header.php');

validate_active_plugins();

?>

<?php if ( isset($_GET['error']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin could not be activated because it triggered a <strong>fatal error</strong>.') ?></p>
	<?php
		if ( wp_verify_nonce($_GET['_error_nonce'], 'plugin-activation-error_' . $plugin) ) { ?>
	<iframe style="border:0" width="100%" height="70px" src="<?php echo admin_url('plugins.php?action=error_scrape&amp;plugin=' . attribute_escape($plugin) . '&amp;_wpnonce=' . attribute_escape($_GET['_error_nonce'])); ?>"></iframe>
	<?php
		}
	?>
	</div>
<?php elseif ( 'delete-selected' == $action ) :
		if ( is_wp_error($delete_result) ) : ?>
		<div id="message" class="updated fade"><p><?php printf( __('Plugin could not be deleted due to an error: %s'), $delete_result->get_error_message() ); ?></p></div>
		<?php else : ?>
		<div id="message" class="updated fade"><p><?php _e('The selected plugins have been <strong>deleted</strong>.'); ?></p></div>
		<?php endif; ?>
<?php elseif ( isset($_GET['activate']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin <strong>activated</strong>.') ?></p></div>
<?php elseif (isset($_GET['activate-multi'])) : ?>
	<div id="message" class="updated fade"><p><?php _e('Selected plugins <strong>activated</strong>.'); ?></p></div>
<?php elseif ( isset($_GET['deactivate']) ) : ?>
	<div id="message" class="updated fade"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p></div>
<?php elseif (isset($_GET['deactivate-multi'])) : ?>
	<div id="message" class="updated fade"><p><?php _e('Selected plugins <strong>deactivated</strong>.'); ?></p></div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Plugin Management'); ?></h2>
<p><?php _e('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.'); ?></p>
<?php

$all_plugins = get_plugins();
$active_plugins = array();
$inactive_plugins = array();
$recent_plugins = array();
$recently_activated = (array)get_option('recently_activated');

//Clean out any plugins which were deactivated over a week ago.
foreach( $recently_activated as $key => $time )
	if( $time + (7*24*60*60) < time() ) //1 week
		unset($recently_activated[ $key ]);
if( $recently_activated != get_option('recently_activated') ) //If array changed, update it.
	update_option('recently_activated', $recently_activated);

$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());

foreach( (array)$all_plugins as $plugin_file => $plugin_data) {

	// Sanitize all displayed data
	$plugin_data['Title']       = wp_kses($plugin_data['Title'], $plugins_allowedtags);
	$plugin_data['Version']     = wp_kses($plugin_data['Version'], $plugins_allowedtags);
	$plugin_data['Description'] = wp_kses($plugin_data['Description'], $plugins_allowedtags);
	$plugin_data['Author']      = wp_kses($plugin_data['Author'], $plugins_allowedtags);
	if( ! empty($plugin_data['Author']) )
		$plugin_data['Description'] .= ' <cite>' . sprintf( __('By %s'), $plugin_data['Author'] ) . '.</cite>';

	//Filter into individual sections
	if ( is_plugin_active($plugin_file) ) {
		$active_plugins[ $plugin_file ] = $plugin_data;
	} else {
		if ( isset( $recently_activated[ $plugin_file ] ) ) //Was the plugin recently activated?
			$recent_plugins[ $plugin_file ] = $plugin_data;
		else
			$inactive_plugins[ $plugin_file ] = $plugin_data;
	}
}

?>

<?php
function print_plugins_table($plugins, $context = '') {
?>
<table class="widefat" id="<?php echo $context ?>-plugins-table">
	<thead>
	<tr>
		<th scope="col" class="check-column"><input type="checkbox" /></th>
		<th scope="col"><?php _e('Plugin'); ?></th>
		<th scope="col" class="num"><?php _e('Version'); ?></th>
		<th scope="col"><?php _e('Description'); ?></th>
		<th scope="col" class="action-links"><?php _e('Action'); ?></th>
	</tr>
	</thead>
	<tbody class="plugins">
<?php

	if( empty($plugins) ) {
		echo '<tr>
			<td colspan="6">' . __('No plugins to show') . '</td>
		</tr>';
	}
	foreach( (array)$plugins as $plugin_file => $plugin_data) {
		$action_links = array();

		if( 'active' == $context )
			$action_links[] = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . $plugin_file, 'deactivate-plugin_' . $plugin_file) . '" title="' . __('Deactivate this plugin') . '" class="delete">' . __('Deactivate') . '</a>';
		else //Available or Recently deactivated
			$action_links[] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file) . '" title="' . __('Activate this plugin') . '" class="edit">' . __('Activate') . '</a>';

		if ( current_user_can('edit_plugins') && is_writable(WP_PLUGIN_DIR . '/' . $plugin_file) )
			$action_links[] = '<a href="plugin-editor.php?file=' . $plugin_file . '" title="' . __('Open this file in the Plugin Editor') . '" class="edit">' . __('Edit') . '</a>';

		$action_links = apply_filters('plugin_action_links', $action_links, $plugin_file, $plugin_data, $context);

		echo "
	<tr class='$context'>
		<th scope='row' class='check-column'><input type='checkbox' name='checked[]' value='" . attribute_escape($plugin_file) . "' /></th>
		<td class='name'>{$plugin_data['Title']}</td>
		<td class='vers'>{$plugin_data['Version']}</td>
		<td class='desc'><p>{$plugin_data['Description']}</p></td>
		<td class='togl action-links'>";  
		if ( !empty($action_links) )
			echo implode(' | ', $action_links);
		echo '</td> 
	</tr>';
		do_action( 'after_plugin_row', $plugin_file, $plugin_data, $context );
	}
?>
	</tbody>
</table>
<?php 
} //End print_plugins_table()
?>

<?php if ( ! empty($active_plugins) ) : ?>
<h3 id="currently-active"><?php _e('Currently Active Plugins') ?></h3>
<form method="post" action="<?php echo admin_url('plugins.php') ?>">
<?php wp_nonce_field('bulk-manage-plugins') ?>

<div class="tablenav">
	<div class="alignleft">
		<input type="submit" name="deactivate-selected" value="<?php _e('Deactivate') ?>" class="button-secondary" />
	</div>
</div>
<br class="clear" />
<?php print_plugins_table($active_plugins, 'active') ?>
</form>

<p><?php printf(__('If something goes wrong with a plugin and you can&#8217;t use WordPress, delete or rename that file in the <code>%s</code> directory and it will be automatically deactivated.'), WP_PLUGIN_DIR); ?></p>
<?php endif; ?>

<?php if ( ! empty($recent_plugins) ) : ?>
<h3 id="recent-plugins"><?php _e('Recently Active Plugins') ?></h3>
<p><?php _e('The following plugins were recently active. When a plugin has been inactive for more than 7 days it will be moved to the Inactive plugin list.') ?></p>
<form method="post" action="<?php echo admin_url('plugins.php') ?>">
<?php wp_nonce_field('bulk-manage-plugins') ?>

<div class="tablenav">
	<div class="alignleft">
		<input type="submit" name="activate-selected" value="<?php _e('Activate') ?>" class="button-secondary" />
<?php if( current_user_can('delete_plugins') ) : ?>
		<input type="submit" name="delete-selected" value="<?php _e('Delete') ?>" class="button-secondary" />
<?php endif; ?>
		<input type="submit" name="clear-recent-list" value="<?php _e('Clear List') ?>" class="button-secondary" />
	</div>
</div>
<br class="clear" />
<?php print_plugins_table($recent_plugins, 'recent') ?>
</form>
<?php endif; ?>

<?php if ( ! empty($inactive_plugins) ) : ?>
<h3 id="inactive-plugins"><?php _e('Inactive Plugins') ?></h3>
<form method="post" action="<?php echo admin_url('plugins.php') ?>">
<?php wp_nonce_field('bulk-manage-plugins') ?>

<div class="tablenav">
	<div class="alignleft">
		<input type="submit" name="activate-selected" value="<?php _e('Activate') ?>" class="button-secondary" />
<?php if( current_user_can('delete_plugins') ) : ?>
		<input type="submit" name="delete-selected" value="<?php _e('Delete') ?>" class="button-secondary" />
<?php endif; ?>
	</div>
</div>
<br class="clear" />
<?php print_plugins_table($inactive_plugins, 'inactive') ?>
</form>
<?php endif; ?>

<?php if ( empty($all_plugins) ) : ?>
<p><?php _e('You do not appear to have any plugins available at this time.') ?></p>
<?php endif; ?>

<h2><?php _e('Get More Plugins'); ?></h2>
<p><?php _e('You can find additional plugins for your site in the <a href="http://wordpress.org/extend/plugins/">WordPress plugin directory</a>.'); ?></p>
<p><?php printf(__('To install a plugin you generally just need to upload the plugin file into your <code>%s</code> directory. Once a plugin is uploaded, you may activate it here.'), WP_PLUGIN_DIR); ?></p>

</div>

<?php
include('admin-footer.php');
?>

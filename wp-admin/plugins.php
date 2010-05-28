<?php
/**
 * Plugins administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! current_user_can( 'activate_plugins' ) )
	wp_die( __( 'You do not have sufficient permissions to manage plugins for this site.' ) );

if ( isset($_POST['clear-recent-list']) )
	$action = 'clear-recent-list';
elseif ( !empty($_REQUEST['action']) )
	$action = $_REQUEST['action'];
elseif ( !empty($_REQUEST['action2']) )
	$action = $_REQUEST['action2'];
else
	$action = false;

$plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';

$default_status = get_user_option('plugins_last_view');
if ( empty($default_status) )
	$default_status = 'all';
$status = isset($_REQUEST['plugin_status']) ? $_REQUEST['plugin_status'] : $default_status;
if ( !in_array($status, array('all', 'active', 'inactive', 'recent', 'upgrade', 'network', 'mustuse', 'dropins', 'search')) )
	$status = 'all';
if ( $status != $default_status && 'search' != $status )
	update_user_meta($current_user->ID, 'plugins_last_view', $status);

$page = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;

//Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$_SERVER['REQUEST_URI'] = remove_query_arg(array('error', 'deleted', 'activate', 'activate-multi', 'deactivate', 'deactivate-multi', '_error_nonce'), $_SERVER['REQUEST_URI']);

if ( !empty($action) ) {
	$network_wide = false;
	if ( ( isset( $_GET['networkwide'] ) || 'network-activate-selected' == $action ) && is_multisite() && current_user_can( 'manage_network_plugins' ) )
		$network_wide = true;

	switch ( $action ) {
		case 'activate':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to activate plugins for this site.'));

			check_admin_referer('activate-plugin_' . $plugin);

			$result = activate_plugin($plugin, 'plugins.php?error=true&plugin=' . $plugin, $network_wide);
			if ( is_wp_error( $result ) ) {
				if ( 'unexpected_output' == $result->get_error_code() ) {
					$redirect = 'plugins.php?error=true&charsout=' . strlen($result->get_error_data()) . '&plugin=' . $plugin;
					wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('plugin-activation-error_' . $plugin), $redirect));
					exit;
				} else {
					wp_die($result);
				}
			}

			$recent = (array)get_option('recently_activated');
			if ( isset($recent[ $plugin ]) ) {
				unset($recent[ $plugin ]);
				update_option('recently_activated', $recent);
			}
			if ( isset($_GET['from']) && 'import' == $_GET['from'] ) {
				wp_redirect("import.php?import=" . str_replace('-importer', '', dirname($plugin)) ); // overrides the ?error=true one above and redirects to the Imports page, striping the -importer suffix
			} else {
				wp_redirect("plugins.php?activate=true&plugin_status=$status&paged=$page"); // overrides the ?error=true one above
			}
			exit;
			break;
		case 'activate-selected':
		case 'network-activate-selected':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to activate plugins for this site.'));

			check_admin_referer('bulk-manage-plugins');

			$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			$plugins = array_filter($plugins, create_function('$plugin', 'return !is_plugin_active($plugin);') ); // Only activate plugins which are not already active.
			if ( empty($plugins) ) {
				wp_redirect("plugins.php?plugin_status=$status&paged=$page");
				exit;
			}

			activate_plugins($plugins, 'plugins.php?error=true', $network_wide);

			$recent = (array)get_option('recently_activated');
			foreach ( $plugins as $plugin => $time)
				if ( isset($recent[ $plugin ]) )
					unset($recent[ $plugin ]);

			update_option('recently_activated', $recent);

			wp_redirect("plugins.php?activate-multi=true&plugin_status=$status&paged=$page");
			exit;
			break;
		case 'update-selected' :

			check_admin_referer( 'bulk-manage-plugins' );

			if ( isset( $_GET['plugins'] ) )
				$plugins = explode( ',', $_GET['plugins'] );
			elseif ( isset( $_POST['checked'] ) )
				$plugins = (array) $_POST['checked'];
			else
				$plugins = array();

			$title = __( 'Upgrade Plugins' );
			$parent_file = 'plugins.php';

			require_once( './admin-header.php' );

			echo '<div class="wrap">';
			screen_icon();
			echo '<h2>' . esc_html( $title ) . '</h2>';


			$url = 'update.php?action=update-selected&amp;plugins=' . urlencode( join(',', $plugins) );
			$url = wp_nonce_url($url, 'bulk-update-plugins');

			echo "<iframe src='$url' style='width: 100%; height:100%; min-height:850px;'></iframe>";
			echo '</div>';
			require_once( './admin-footer.php' );
			exit;
			break;
		case 'error_scrape':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to activate plugins for this site.'));

			check_admin_referer('plugin-activation-error_' . $plugin);

			$valid = validate_plugin($plugin);
			if ( is_wp_error($valid) )
				wp_die($valid);

			if ( ! WP_DEBUG ) {
				if ( defined('E_RECOVERABLE_ERROR') )
					error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);
				else
					error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING);
			}

			@ini_set('display_errors', true); //Ensure that Fatal errors are displayed.
			// Go back to "sandbox" scope so we get the same errors as before
			function plugin_sandbox_scrape( $plugin ) {
				include( WP_PLUGIN_DIR . '/' . $plugin );
			}
			plugin_sandbox_scrape( $plugin );
			do_action('activate_' . $plugin);
			exit;
			break;
		case 'deactivate':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.'));

			check_admin_referer('deactivate-plugin_' . $plugin);
			deactivate_plugins($plugin);
			update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));
			if (headers_sent())
				echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=plugins.php?deactivate=true&plugin_status=$status&paged=$page" ) . "' />";
			else
				wp_redirect("plugins.php?deactivate=true&plugin_status=$status&paged=$page");
			exit;
			break;
		case 'deactivate-selected':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.'));

			check_admin_referer('bulk-manage-plugins');

			$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			$plugins = array_filter($plugins, 'is_plugin_active'); //Do not deactivate plugins which are already deactivated.
			if ( empty($plugins) ) {
				wp_redirect("plugins.php?plugin_status=$status&paged=$page");
				exit;
			}

			deactivate_plugins($plugins);

			$deactivated = array();
			foreach ( $plugins as $plugin )
				$deactivated[ $plugin ] = time();

			update_option('recently_activated', $deactivated + (array)get_option('recently_activated'));
			wp_redirect("plugins.php?deactivate-multi=true&plugin_status=$status&paged=$page");
			exit;
			break;
		case 'delete-selected':
			if ( ! current_user_can('delete_plugins') )
				wp_die(__('You do not have sufficient permissions to delete plugins for this site.'));

			check_admin_referer('bulk-manage-plugins');

			//$_POST = from the plugin form; $_GET = from the FTP details screen.
			$plugins = isset( $_REQUEST['checked'] ) ? (array) $_REQUEST['checked'] : array();
			$plugins = array_filter($plugins, create_function('$plugin', 'return !is_plugin_active($plugin);') ); //Do not allow to delete Activated plugins.
			if ( empty($plugins) ) {
				wp_redirect("plugins.php?plugin_status=$status&paged=$page");
				exit;
			}

			include(ABSPATH . 'wp-admin/update.php');

			$parent_file = 'plugins.php';

			if ( ! isset($_REQUEST['verify-delete']) ) {
				wp_enqueue_script('jquery');
				require_once('./admin-header.php');
				?>
			<div class="wrap">
				<?php
					$files_to_delete = $plugin_info = array();
					foreach ( (array) $plugins as $plugin ) {
						if ( '.' == dirname($plugin) ) {
							$files_to_delete[] = WP_PLUGIN_DIR . '/' . $plugin;
							if( $data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin) ) {
								$plugin_info[ $plugin ] = $data;
								$plugin_info[ $plugin ]['is_uninstallable'] = is_uninstallable_plugin( $plugin );
							}
						} else {
							// Locate all the files in that folder
							$files = list_files( WP_PLUGIN_DIR . '/' . dirname($plugin) );
							if ( $files ) {
								$files_to_delete = array_merge($files_to_delete, $files);
							}
							// Get plugins list from that folder
							if ( $folder_plugins = get_plugins( '/' . dirname($plugin)) ) {
								foreach( $folder_plugins as $plugin_file => $data ) {
									$plugin_info[ $plugin_file ] = $data;
									$plugin_info[ $plugin_file ]['is_uninstallable'] = is_uninstallable_plugin( $plugin );
								}
							}
						}
					}
					screen_icon();
					$plugins_to_delete = count( $plugin_info );
					echo '<h2>' . _n( 'Delete Plugin', 'Delete Plugins', $plugins_to_delete ) . '</h2>';
				?>
				<p><?php echo _n( 'You are about to remove the following plugin:', 'You are about to remove the following plugins:', $plugins_to_delete ); ?></p>
					<ul class="ul-disc">
						<?php
						$data_to_delete = false;
						foreach ( $plugin_info as $plugin ) {
							if ( $plugin['is_uninstallable'] ) {
								/* translators: 1: plugin name, 2: plugin author */
								echo '<li>', sprintf( __( '<strong>%1$s</strong> by <em>%2$s</em> (will also <strong>delete its data</strong>)' ), $plugin['Name'], $plugin['Author'] ), '</li>';
								$data_to_delete = true;
							} else {
								/* translators: 1: plugin name, 2: plugin author */
								echo '<li>', sprintf( __('<strong>%1$s</strong> by <em>%2$s</em>' ), $plugin['Name'], $plugin['Author'] ), '</li>';
							}
						}
						?>
					</ul>
				<p><?php
				if ( $data_to_delete )
					_e('Are you sure you wish to delete these files and data?');
				else
					_e('Are you sure you wish to delete these files?');
				?></p>
				<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" style="display:inline;">
					<input type="hidden" name="verify-delete" value="1" />
					<input type="hidden" name="action" value="delete-selected" />
					<?php
						foreach ( (array)$plugins as $plugin )
							echo '<input type="hidden" name="checked[]" value="' . esc_attr($plugin) . '" />';
					?>
					<?php wp_nonce_field('bulk-manage-plugins') ?>
					<input type="submit" name="submit" value="<?php $data_to_delete ? esc_attr_e('Yes, Delete these files and data') : esc_attr_e('Yes, Delete these files') ?>" class="button" />
				</form>
				<form method="post" action="<?php echo esc_url(wp_get_referer()); ?>" style="display:inline;">
					<input type="submit" name="submit" value="<?php esc_attr_e('No, Return me to the plugin list') ?>" class="button" />
				</form>

				<p><a href="#" onclick="jQuery('#files-list').toggle(); return false;"><?php _e('Click to view entire list of files which will be deleted'); ?></a></p>
				<div id="files-list" style="display:none;">
					<ul class="code">
					<?php
						foreach ( (array)$files_to_delete as $file )
							echo '<li>' . str_replace(WP_PLUGIN_DIR, '', $file) . '</li>';
					?>
					</ul>
				</div>
			</div>
				<?php
				require_once('./admin-footer.php');
				exit;
			} //Endif verify-delete
			$delete_result = delete_plugins($plugins);

			set_transient('plugins_delete_result_'.$user_ID, $delete_result); //Store the result in a cache rather than a URL param due to object type & length
			wp_redirect("plugins.php?deleted=true&plugin_status=$status&paged=$page");
			exit;
			break;
		case 'clear-recent-list':
			update_option('recently_activated', array());
			break;
	}
}

wp_enqueue_script('plugin-install');
add_thickbox();

add_contextual_help($current_screen,
	'<p>' . __('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.') . '</p>' .
	'<p>' . sprintf(__('You can find additional plugins for your site by using the <a href="%1$s">Plugin Browser/Installer</a> functionality or by browsing the <a href="%2$s">WordPress Plugin Directory</a> directly and installing new plugins manually. To manually install a plugin you generally just need to upload the plugin file into your <code>/wp-content/plugins</code> directory. Once a plugin has been installed, you can activate it here.'), 'plugin-install.php', 'http://wordpress.org/extend/plugins/') . '</p>' .
	'<p>' . __('Most of the time, plugins play nicely with the core of WordPress and with other plugins. Sometimes, though, a plugin&#8217;s code will get in the way of another plugin, causing compatibility issues. If your site starts doing strange things, this may be the problem. Try deactivating all your plugins and re-activating them in various combinations until you isolate which one(s) caused the issue.') . '</p>' .
	'<p>' . sprintf( __('If something goes wrong with a plugin and you can&#8217;t use WordPress, delete or rename that file in the <code>%s</code> directory and it will be automatically deactivated.'), WP_PLUGIN_DIR) . '</p>' .	
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Managing_Plugins#Plugin_Management">Documentation on Managing Plugins</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/">Support Forums</a>') . '</p>'
);

if ( is_multisite() && is_super_admin() ) {
	$menu_perms = get_site_option('menu_items', array());
	if ( empty($menu_perms['plugins']) )
		add_action( 'admin_notices', '_admin_notice_multisite_activate_plugins_page' );
	unset($menu_perms);
}

$title = __('Plugins');

require_once('./admin-header.php');

$invalid = validate_active_plugins();
if ( !empty($invalid) )
	foreach ( $invalid as $plugin_file => $error )
		echo '<div id="message" class="error"><p>' . sprintf(__('The plugin <code>%s</code> has been <strong>deactivated</strong> due to an error: %s'), esc_html($plugin_file), $error->get_error_message()) . '</p></div>';
?>

<?php if ( isset($_GET['error']) ) :

	if ( isset($_GET['charsout']) )
		$errmsg = sprintf(__('The plugin generated %d characters of <strong>unexpected output</strong> during activation.  If you notice &#8220;headers already sent&#8221; messages, problems with syndication feeds or other issues, try deactivating or removing this plugin.'), $_GET['charsout']);
	else
		$errmsg = __('Plugin could not be activated because it triggered a <strong>fatal error</strong>.');
	?>
	<div id="message" class="updated"><p><?php echo $errmsg; ?></p>
	<?php
		if ( !isset($_GET['charsout']) && wp_verify_nonce($_GET['_error_nonce'], 'plugin-activation-error_' . $plugin) ) { ?>
	<iframe style="border:0" width="100%" height="70px" src="<?php echo admin_url('plugins.php?action=error_scrape&amp;plugin=' . esc_attr($plugin) . '&amp;_wpnonce=' . esc_attr($_GET['_error_nonce'])); ?>"></iframe>
	<?php
		}
	?>
	</div>
<?php elseif ( isset($_GET['deleted']) ) :
		$delete_result = get_transient('plugins_delete_result_'.$user_ID);
		delete_transient('plugins_delete_result'); //Delete it once we're done.

		if ( is_wp_error($delete_result) ) : ?>
		<div id="message" class="updated"><p><?php printf( __('Plugin could not be deleted due to an error: %s'), $delete_result->get_error_message() ); ?></p></div>
		<?php else : ?>
		<div id="message" class="updated"><p><?php _e('The selected plugins have been <strong>deleted</strong>.'); ?></p></div>
		<?php endif; ?>
<?php elseif ( isset($_GET['activate']) ) : ?>
	<div id="message" class="updated"><p><?php _e('Plugin <strong>activated</strong>.') ?></p></div>
<?php elseif (isset($_GET['activate-multi'])) : ?>
	<div id="message" class="updated"><p><?php _e('Selected plugins <strong>activated</strong>.'); ?></p></div>
<?php elseif ( isset($_GET['deactivate']) ) : ?>
	<div id="message" class="updated"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p></div>
<?php elseif (isset($_GET['deactivate-multi'])) : ?>
	<div id="message" class="updated"><p><?php _e('Selected plugins <strong>deactivated</strong>.'); ?></p></div>
<?php elseif ( 'update-selected' == $action ) : ?>
	<div id="message" class="updated"><p><?php _e('No out of date plugins were selected.'); ?></p></div>
<?php endif; ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); if ( current_user_can('install_plugins') ) { ?> <a href="plugin-install.php" class="button add-new-h2"><?php echo esc_html_x('Add New', 'plugin'); ?></a><?php } ?></h2>

<?php

$all_plugins = apply_filters( 'all_plugins', get_plugins() );
$search_plugins = array();
$active_plugins = array();
$inactive_plugins = array();
$recent_plugins = array();
$recently_activated = get_option('recently_activated', array());
$upgrade_plugins = array();
$network_plugins = array();
$mustuse_plugins = $dropins_plugins = array();
if ( ! is_multisite() || current_user_can('manage_network_plugins') ) {
	if ( apply_filters( 'show_advanced_plugins', true, 'mustuse' ) )
		$mustuse_plugins = get_mu_plugins();
	if ( apply_filters( 'show_advanced_plugins', true, 'dropins' ) )
		$dropins_plugins = get_dropins();
}

set_transient( 'plugin_slugs', array_keys($all_plugins), 86400 );

// Clean out any plugins which were deactivated over a week ago.
foreach ( $recently_activated as $key => $time )
	if ( $time + (7*24*60*60) < time() ) //1 week
		unset($recently_activated[ $key ]);
if ( $recently_activated != get_option('recently_activated') ) //If array changed, update it.
	update_option('recently_activated', $recently_activated);
$current = get_site_transient( 'update_plugins' );

foreach ( array( 'all_plugins', 'mustuse_plugins', 'dropins_plugins' ) as $plugin_array_name) {
	foreach ( (array) $$plugin_array_name as $plugin_file => $plugin_data ) {
		// Translate, Apply Markup, Sanitize HTML
		$plugin_data = _get_plugin_data_markup_translate($plugin_file, $plugin_data, false, true);
		${$plugin_array_name}[ $plugin_file ] = $plugin_data;
	}
}
unset( $plugin_array_name );

foreach ( (array) $all_plugins as $plugin_file => $plugin_data) {
	// Filter into individual sections
	if ( is_multisite() && is_network_only_plugin( $plugin_file ) && !current_user_can( 'manage_network_plugins' ) ) {
		unset( $all_plugins[ $plugin_file ] );
		continue;
	} elseif ( is_plugin_active_for_network($plugin_file) ) {
		$network_plugins[ $plugin_file ] = $plugin_data;
	} elseif ( is_plugin_active($plugin_file) ) {
		$active_plugins[ $plugin_file ] = $plugin_data;
	} else {
		if ( isset( $recently_activated[ $plugin_file ] ) ) // Was the plugin recently activated?
			$recent_plugins[ $plugin_file ] = $plugin_data;
		$inactive_plugins[ $plugin_file ] = $plugin_data;
	}

	if ( isset( $current->response[ $plugin_file ] ) )
		$upgrade_plugins[ $plugin_file ] = $plugin_data;
}

if ( !current_user_can('update_plugins') )
	$upgrade_plugins = array();

$total_all_plugins = count($all_plugins);
$total_inactive_plugins = count($inactive_plugins);
$total_active_plugins = count($active_plugins);
$total_recent_plugins = count($recent_plugins);
$total_upgrade_plugins = count($upgrade_plugins);
$total_network_plugins = count($network_plugins);
$total_mustuse_plugins = count($mustuse_plugins);
$total_dropins_plugins = count($dropins_plugins);

// Searching.
if ( !empty($_GET['s']) ) {
	function _search_plugins_filter_callback($plugin) {
		static $term;
		if ( is_null($term) )
			$term = stripslashes($_GET['s']);
		if ( 	stripos($plugin['Name'], $term) !== false ||
				stripos($plugin['Description'], $term) !== false ||
				stripos($plugin['Author'], $term) !== false ||
				stripos($plugin['PluginURI'], $term) !== false ||
				stripos($plugin['AuthorURI'], $term) !== false ||
				stripos($plugin['Version'], $term) !== false )
			return true;
		else
			return false;
	}
	$status = 'search';
	$search_plugins = array_filter($all_plugins, '_search_plugins_filter_callback');
	$total_search_plugins = count($search_plugins);
}

$plugin_array_name = "${status}_plugins";
if ( empty($$plugin_array_name) && !in_array($status, array('all', 'search')) ) {
	$status = 'all';
	$plugin_array_name = "${status}_plugins";
}

$plugins = &$$plugin_array_name;

// Paging.
$total_this_page = "total_{$status}_plugins";
$total_this_page = $$total_this_page;
$plugins_per_page = (int) get_user_option( 'plugins_per_page' );
if ( empty( $plugins_per_page ) || $plugins_per_page < 1 )
	$plugins_per_page = 999;
$plugins_per_page = apply_filters( 'plugins_per_page', $plugins_per_page );

$start = ($page - 1) * $plugins_per_page;

$page_links = paginate_links( array(
	'base' => add_query_arg( 'paged', '%#%' ),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => ceil($total_this_page / $plugins_per_page),
	'current' => $page
));
$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
	number_format_i18n( $start + 1 ),
	number_format_i18n( min( $page * $plugins_per_page, $total_this_page ) ),
	'<span class="total-type-count">' . number_format_i18n( $total_this_page ) . '</span>',
	$page_links
);

/**
 * @ignore
 *
 * @param array $plugins
 * @param string $context
 */
function print_plugins_table($plugins, $context = '') {
	global $page;
	$checkbox = ! in_array( $context, array( 'mustuse', 'dropins' ) ) ? '<input type="checkbox" />' : '';
?>
<table class="widefat" cellspacing="0" id="<?php echo $context ?>-plugins-table">
	<thead>
	<tr>
		<th scope="col" class="manage-column check-column"><?php echo $checkbox; ?></th>
		<th scope="col" class="manage-column"><?php _e('Plugin'); ?></th>
		<th scope="col" class="manage-column"><?php _e('Description'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column check-column"><?php echo $checkbox; ?></th>
		<th scope="col" class="manage-column"><?php _e('Plugin'); ?></th>
		<th scope="col" class="manage-column"><?php _e('Description'); ?></th>
	</tr>
	</tfoot>

	<tbody class="plugins">
<?php

	if ( empty($plugins) ) {
		echo '<tr>
			<td colspan="3">' . __('No plugins to show') . '</td>
		</tr>';
	}
	foreach ( (array)$plugins as $plugin_file => $plugin_data) {
		// preorder
		$actions = array(
			'network_deactivate' => '', 'deactivate' => '',
			'network_only' => '', 'activate' => '',
			'network_activate' => '',
			'edit' => '',
			'delete' => '',
		);

		if ( 'mustuse' == $context ) {
			$is_active = true;
		} elseif ( 'dropins' == $context ) {
			$dropins = _get_dropins();
			$plugin_name = $plugin_file;
			if ( $plugin_file != $plugin_data['Name'] )
				$plugin_name .= '<br/>' . $plugin_data['Name'];
			if ( true === ( $dropins[ $plugin_file ][1] ) ) { // Doesn't require a constant
				$is_active = true;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
			} elseif ( constant( $dropins[ $plugin_file ][1] ) ) { // Constant is true
				$is_active = true;
				$description = '<p><strong>' . $dropins[ $plugin_file ][0] . '</strong></p>';
			} else {
				$is_active = false;
				$description = '<strong>' . $dropins[ $plugin_file ][0] . ' <span class="attention">' . __('Inactive:') . '</span></strong> ' . sprintf( __( 'Requires <code>%s</code> in <code>wp-config.php</code>.' ), "define('" . $dropins[ $plugin_file ][1] . "', true);" ) . '</p>';
			}
			$description .= '<p>' . $plugin_data['Description'] . '</p>';
		} else {
			$is_active_for_network = is_plugin_active_for_network($plugin_file);
			$is_active = $is_active_for_network || is_plugin_active( $plugin_file );
			if ( $is_active_for_network && !is_super_admin() )
				continue;

			if ( $is_active ) {
				if ( $is_active_for_network ) {
					if ( is_super_admin() )
						$actions['network_deactivate'] = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;networkwide=1&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page, 'deactivate-plugin_' . $plugin_file) . '" title="' . __('Deactivate this plugin') . '">' . __('Network Deactivate') . '</a>';
				} else {
					$actions['deactivate'] = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page, 'deactivate-plugin_' . $plugin_file) . '" title="' . __('Deactivate this plugin') . '">' . __('Deactivate') . '</a>';
				}
			} else {
				if ( is_multisite() && is_network_only_plugin( $plugin_file ) )
					$actions['network_only'] = '<span title="' . __('This plugin can only be activated for all sites in a network') . '">' . __('Network Only') . '</span>';
				else
					$actions['activate'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page, 'activate-plugin_' . $plugin_file) . '" title="' . __('Activate this plugin') . '" class="edit">' . __('Activate') . '</a>';

				if ( is_multisite() && current_user_can( 'manage_network_plugins' ) )
					$actions['network_activate'] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;networkwide=1&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page, 'activate-plugin_' . $plugin_file) . '" title="' . __('Activate this plugin for all sites in this network') . '" class="edit">' . __('Network Activate') . '</a>';

				if ( current_user_can('delete_plugins') )
					$actions['delete'] = '<a href="' . wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page, 'bulk-manage-plugins') . '" title="' . __('Delete this plugin') . '" class="delete">' . __('Delete') . '</a>';
			} // end if $is_active

			if ( current_user_can('edit_plugins') && is_writable(WP_PLUGIN_DIR . '/' . $plugin_file) )
				$actions['edit'] = '<a href="plugin-editor.php?file=' . $plugin_file . '" title="' . __('Open this file in the Plugin Editor') . '" class="edit">' . __('Edit') . '</a>';
		} // end if $context

		$actions = apply_filters( 'plugin_action_links', array_filter( $actions ), $plugin_file, $plugin_data, $context );
		$actions = apply_filters( "plugin_action_links_$plugin_file", $actions, $plugin_file, $plugin_data, $context );

		$class = $is_active ? 'active' : 'inactive';
		$checkbox = in_array( $context, array( 'mustuse', 'dropins' ) ) ? '' : "<input type='checkbox' name='checked[]' value='" . esc_attr($plugin_file) . "' />";
		if ( 'dropins' != $context ) {
			$description = '<p>' . $plugin_data['Description'] . '</p>';
			$plugin_name = $plugin_data['Name'];
		}
		echo "
	<tr class='$class'>
		<th scope='row' class='check-column'>$checkbox</th>
		<td class='plugin-title'><strong>$plugin_name</strong></td>
		<td class='desc'>$description</td>
	</tr>
	<tr class='$class second'>
		<td></td>
		<td class='plugin-title'>";
		echo '<div class="row-actions-visible">';
		foreach ( $actions as $action => $link ) {
			$sep = end($actions) == $link ? '' : ' | ';
			echo "<span class='$action'>$link$sep</span>";
		}
		echo "</div></td>
		<td class='desc'>";
		$plugin_meta = array();
		if ( !empty($plugin_data['Version']) )
			$plugin_meta[] = sprintf(__('Version %s'), $plugin_data['Version']);
		if ( !empty($plugin_data['Author']) ) {
			$author = $plugin_data['Author'];
			if ( !empty($plugin_data['AuthorURI']) )
				$author = '<a href="' . $plugin_data['AuthorURI'] . '" title="' . __( 'Visit author homepage' ) . '">' . $plugin_data['Author'] . '</a>';
			$plugin_meta[] = sprintf( __('By %s'), $author );
		}
		if ( ! empty($plugin_data['PluginURI']) )
			$plugin_meta[] = '<a href="' . $plugin_data['PluginURI'] . '" title="' . __( 'Visit plugin site' ) . '">' . __('Visit plugin site') . '</a>';

		$plugin_meta = apply_filters('plugin_row_meta', $plugin_meta, $plugin_file, $plugin_data, $context);
		echo implode(' | ', $plugin_meta);
		echo "</td>
	</tr>\n";

		do_action( 'after_plugin_row', $plugin_file, $plugin_data, $context );
		do_action( "after_plugin_row_$plugin_file", $plugin_file, $plugin_data, $context );
	}
?>
	</tbody>
</table>
<?php
} //End print_plugins_table()

/**
 * @ignore
 *
 * @param string $context
 */
function print_plugin_actions($context, $field_name = 'action' ) {
	if ( in_array( $context, array( 'mustuse', 'dropins' ) ) )
		return;
?>
	<div class="alignleft actions">
		<select name="<?php echo $field_name; ?>">
			<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
	<?php if ( 'active' != $context ) : ?>
			<option value="activate-selected"><?php _e('Activate'); ?></option>
	<?php endif; ?>
	<?php if ( is_multisite() && 'network' != $context ) : ?>
			<option value="network-activate-selected"><?php _e('Network Activate'); ?></option>
	<?php endif; ?>
	<?php if ( 'inactive' != $context && 'recent' != $context ) : ?>
			<option value="deactivate-selected"><?php _e('Deactivate'); ?></option>
	<?php endif; ?>
	<?php if ( current_user_can( 'update_plugins' ) ) : ?>
			<option value="update-selected"><?php _e( 'Upgrade' ); ?></option>
	<?php endif; ?>
	<?php if ( current_user_can('delete_plugins') && ( 'active' != $context ) ) : ?>
			<option value="delete-selected"><?php _e('Delete'); ?></option>
	<?php endif; ?>
		</select>
		<input type="submit" name="doaction_active" value="<?php esc_attr_e('Apply'); ?>" class="button-secondary action" />
	<?php if ( 'recent' == $context ) : ?>
		<input type="submit" name="clear-recent-list" value="<?php esc_attr_e('Clear List') ?>" class="button-secondary" />
	<?php endif; ?>
	</div>
<?php
}
?>

<form method="get" action="">
<p class="search-box">
	<label class="screen-reader-text" for="plugin-search-input"><?php _e( 'Search Plugins' ); ?>:</label>
	<input type="text" id="plugin-search-input" name="s" value="<?php _admin_search_query(); ?>" />
	<input type="submit" value="<?php esc_attr_e( 'Search Installed Plugins' ); ?>" class="button" />
</p>
</form>

<?php do_action( 'pre_current_active_plugins', $all_plugins ) ?>

<form method="post" action="<?php echo admin_url('plugins.php') ?>">
<?php wp_nonce_field('bulk-manage-plugins') ?>
<input type="hidden" name="plugin_status" value="<?php echo esc_attr($status) ?>" />
<input type="hidden" name="paged" value="<?php echo esc_attr($page) ?>" />

<ul class="subsubsub">
<?php
$status_links = array();
$class = ( 'all' == $status ) ? ' class="current"' : '';
$status_links[] = "<li><a href='plugins.php?plugin_status=all' $class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_all_plugins, 'plugins' ), number_format_i18n( $total_all_plugins ) ) . '</a>';
if ( ! empty($active_plugins) ) {
	$class = ( 'active' == $status ) ? ' class="current"' : '';
	$status_links[] = "<li><a href='plugins.php?plugin_status=active' $class>" . sprintf( _n( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', $total_active_plugins ), number_format_i18n( $total_active_plugins ) ) . '</a>';
}
if ( ! empty($recent_plugins) ) {
	$class = ( 'recent' == $status ) ? ' class="current"' : '';
	$status_links[] = "<li><a href='plugins.php?plugin_status=recent' $class>" . sprintf( _n( 'Recently Active <span class="count">(%s)</span>', 'Recently Active <span class="count">(%s)</span>', $total_recent_plugins ), number_format_i18n( $total_recent_plugins ) ) . '</a>';
}
if ( ! empty($inactive_plugins) ) {
	$class = ( 'inactive' == $status ) ? ' class="current"' : '';
	$status_links[] = "<li><a href='plugins.php?plugin_status=inactive' $class>" . sprintf( _n( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', $total_inactive_plugins ), number_format_i18n( $total_inactive_plugins ) ) . '</a>';
}
if ( ! empty($network_plugins) ) {
	$class = ( 'network' == $status ) ? ' class="current"' : '';
	$status_links[] = "<li><a href='plugins.php?plugin_status=network' $class>" . sprintf( _n( 'Network <span class="count">(%s)</span>', 'Network <span class="count">(%s)</span>', $total_network_plugins ), number_format_i18n( $total_network_plugins ) ) . '</a>';
}
if ( ! empty($mustuse_plugins) ) {
	$class = ( 'mustuse' == $status ) ? ' class="current"' : '';
	$status_links[] = "<li><a href='plugins.php?plugin_status=mustuse' $class>" . sprintf( _n( 'Must-Use <span class="count">(%s)</span>', 'Must-Use <span class="count">(%s)</span>', $total_mustuse_plugins ), number_format_i18n( $total_mustuse_plugins ) ) . '</a>';
}
if ( ! empty($dropins_plugins) ) {
	$class = ( 'dropins' == $status ) ? ' class="current"' : '';
	$status_links[] = "<li><a href='plugins.php?plugin_status=dropins' $class>" . sprintf( _n( 'Drop-ins <span class="count">(%s)</span>', 'Drop-ins <span class="count">(%s)</span>', $total_dropins_plugins ), number_format_i18n( $total_dropins_plugins ) ) . '</a>';
}
if ( ! empty($upgrade_plugins) ) {
	$class = ( 'upgrade' == $status ) ? ' class="current"' : '';
	$status_links[] = "<li><a href='plugins.php?plugin_status=upgrade' $class>" . sprintf( _n( 'Upgrade Available <span class="count">(%s)</span>', 'Upgrade Available <span class="count">(%s)</span>', $total_upgrade_plugins ), number_format_i18n( $total_upgrade_plugins ) ) . '</a>';
}
if ( ! empty($search_plugins) ) {
	$class = ( 'search' == $status ) ? ' class="current"' : '';
	$term = isset($_REQUEST['s']) ? urlencode(stripslashes($_REQUEST['s'])) : '';
	$status_links[] = "<li><a href='plugins.php?s=$term' $class>" . sprintf( _n( 'Search Results <span class="count">(%s)</span>', 'Search Results <span class="count">(%s)</span>', $total_search_plugins ), number_format_i18n( $total_search_plugins ) ) . '</a>';
}
echo implode( " |</li>\n", $status_links ) . '</li>';
unset( $status_links );
?>
</ul>

<?php
if ( 'mustuse' == $status )
	echo '<div class="clear"><p>' . __( 'Files in the <code>wp-content/mu-plugins</code> directory are executed automatically.' ) . '</p>';
elseif ( 'dropins' == $status )
	echo '<div class="clear"><p>' . __( 'Drop-ins are advanced plugins in the <code>wp-content</code> directory that replace WordPress functionality when present.' ) . '</p>';

if ( !empty( $plugins ) && ( ! in_array( $status, array( 'mustuse', 'dropins' ) ) || $page_links ) ) :
?>
<div class="tablenav">
<?php
if ( $page_links )
	echo '<div class="tablenav-pages">', $page_links_text, '</div>';

print_plugin_actions($status);
?>
</div>
<div class="clear"></div>
<?php
endif;

if ( $total_this_page > $plugins_per_page )
	$plugins = array_slice($plugins, $start, $plugins_per_page);

print_plugins_table($plugins, $status);

if ( !empty( $plugins ) && ! in_array( $status, array( 'mustuse', 'dropins' ) ) || $page_links ) {
?>
<div class="tablenav">
<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links_text</div>";

print_plugin_actions($status, "action2");
?>
</div>
<?php } elseif ( ! empty( $all_plugins ) ) { ?>
<p><?php __( 'No plugins found.' ); ?></p>
<?php } ?>
</form>

<?php if ( empty($all_plugins) ) : ?>
<br class="clear" />
<p><?php _e('You do not appear to have any plugins available at this time.') ?></p>
<?php endif; ?>

</div>

<?php
include('./admin-footer.php');
?>

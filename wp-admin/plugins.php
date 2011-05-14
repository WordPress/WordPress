<?php
/**
 * Plugins administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( is_multisite() ) {
	$menu_perms = get_site_option( 'menu_items', array() );

	if ( empty( $menu_perms['plugins'] ) && ! is_super_admin() )
		wp_die( __( 'Cheatin&#8217; uh?' ) );
}

if ( !current_user_can('activate_plugins') )
	wp_die( __( 'You do not have sufficient permissions to manage plugins for this site.' ) );

$wp_list_table = _get_list_table('WP_Plugins_List_Table');
$pagenum = $wp_list_table->get_pagenum();

$action = $wp_list_table->current_action();

$plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
$s = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';

// Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$_SERVER['REQUEST_URI'] = remove_query_arg(array('error', 'deleted', 'activate', 'activate-multi', 'deactivate', 'deactivate-multi', '_error_nonce'), $_SERVER['REQUEST_URI']);

if ( $action ) {
	$network_wide = false;
	if ( ( isset( $_GET['networkwide'] ) || 'network-activate-selected' == $action ) && is_multisite() && current_user_can( 'manage_network_plugins' ) )
		$network_wide = true;

	switch ( $action ) {
		case 'activate':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to activate plugins for this site.'));

			check_admin_referer('activate-plugin_' . $plugin);

			$result = activate_plugin($plugin, self_admin_url('plugins.php?error=true&plugin=' . $plugin), $network_wide);
			if ( is_wp_error( $result ) ) {
				if ( 'unexpected_output' == $result->get_error_code() ) {
					$redirect = self_admin_url('plugins.php?error=true&charsout=' . strlen($result->get_error_data()) . '&plugin=' . $plugin . "&plugin_status=$status&paged=$page&s=$s");
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
				wp_redirect( self_admin_url("import.php?import=" . str_replace('-importer', '', dirname($plugin))) ); // overrides the ?error=true one above and redirects to the Imports page, stripping the -importer suffix
			} else {
				wp_redirect( self_admin_url("plugins.php?activate=true&plugin_status=$status&paged=$page&s=$s") ); // overrides the ?error=true one above
			}
			exit;
			break;
		case 'activate-selected':
		case 'network-activate-selected':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to activate plugins for this site.'));

			check_admin_referer('bulk-plugins');

			$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();

			// Only activate plugins which are not already active.
			$check = $network_wide ? 'is_plugin_active_for_network' : 'is_plugin_active';
			foreach ( $plugins as $i => $plugin )
				if ( $check( $plugin ) )
					unset( $plugins[ $i ] );

			if ( empty($plugins) ) {
				wp_redirect( self_admin_url("plugins.php?plugin_status=$status&paged=$page&s=$s") );
				exit;
			}

			activate_plugins($plugins, self_admin_url('plugins.php?error=true'), $network_wide);

			$recent = (array)get_option('recently_activated');
			foreach ( $plugins as $plugin => $time)
				if ( isset($recent[ $plugin ]) )
					unset($recent[ $plugin ]);

			update_option('recently_activated', $recent);

			wp_redirect( self_admin_url("plugins.php?activate-multi=true&plugin_status=$status&paged=$page&s=$s") );
			exit;
			break;
		case 'update-selected' :

			check_admin_referer( 'bulk-plugins' );

			if ( isset( $_GET['plugins'] ) )
				$plugins = explode( ',', $_GET['plugins'] );
			elseif ( isset( $_POST['checked'] ) )
				$plugins = (array) $_POST['checked'];
			else
				$plugins = array();

			$title = __( 'Update Plugins' );
			$parent_file = 'plugins.php';

			require_once(ABSPATH . 'wp-admin/admin-header.php');

			echo '<div class="wrap">';
			screen_icon();
			echo '<h2>' . esc_html( $title ) . '</h2>';


			$url = self_admin_url('update.php?action=update-selected&amp;plugins=' . urlencode( join(',', $plugins) ));
			$url = wp_nonce_url($url, 'bulk-update-plugins');

			echo "<iframe src='$url' style='width: 100%; height:100%; min-height:850px;'></iframe>";
			echo '</div>';
			require_once(ABSPATH . 'wp-admin/admin-footer.php');
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
				error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
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
			if ( headers_sent() )
				echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=plugins.php?deactivate=true&plugin_status=$status&paged=$page&s=$s" ) . "' />";
			else
				wp_redirect( self_admin_url("plugins.php?deactivate=true&plugin_status=$status&paged=$page&s=$s") );
			exit;
			break;
		case 'deactivate-selected':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.'));

			check_admin_referer('bulk-plugins');

			$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			$plugins = array_filter($plugins, 'is_plugin_active'); //Do not deactivate plugins which are already deactivated.
			if ( empty($plugins) ) {
				wp_redirect( self_admin_url("plugins.php?plugin_status=$status&paged=$page&s=$s") );
				exit;
			}

			deactivate_plugins($plugins);

			$deactivated = array();
			foreach ( $plugins as $plugin )
				$deactivated[ $plugin ] = time();

			update_option('recently_activated', $deactivated + (array)get_option('recently_activated'));
			wp_redirect( self_admin_url("plugins.php?deactivate-multi=true&plugin_status=$status&paged=$page&s=$s") );
			exit;
			break;
		case 'delete-selected':
			if ( ! current_user_can('delete_plugins') )
				wp_die(__('You do not have sufficient permissions to delete plugins for this site.'));

			check_admin_referer('bulk-plugins');

			//$_POST = from the plugin form; $_GET = from the FTP details screen.
			$plugins = isset( $_REQUEST['checked'] ) ? (array) $_REQUEST['checked'] : array();
			if ( empty( $plugins ) ) {
				wp_redirect( self_admin_url("plugins.php?plugin_status=$status&paged=$page&s=$s") );
				exit;
			}

			$plugins = array_filter($plugins, 'is_plugin_inactive'); // Do not allow to delete Activated plugins.
			if ( empty( $plugins ) ) {
				wp_redirect( self_admin_url( "plugins.php?error=true&main=true&plugin_status=$status&paged=$page&s=$s" ) );
				exit;
			}

			include(ABSPATH . 'wp-admin/update.php');

			$parent_file = 'plugins.php';

			if ( ! isset($_REQUEST['verify-delete']) ) {
				wp_enqueue_script('jquery');
				require_once(ABSPATH . 'wp-admin/admin-header.php');
				?>
			<div class="wrap">
				<?php
					$files_to_delete = $plugin_info = array();
					$have_non_network_plugins = false;
					foreach ( (array) $plugins as $plugin ) {
						if ( '.' == dirname($plugin) ) {
							$files_to_delete[] = WP_PLUGIN_DIR . '/' . $plugin;
							if( $data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin) ) {
								$plugin_info[ $plugin ] = $data;
								$plugin_info[ $plugin ]['is_uninstallable'] = is_uninstallable_plugin( $plugin );
								if ( ! $plugin_info[ $plugin ]['Network'] )
									$have_non_network_plugins = true;
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
									$plugin_info[ $plugin_file ] = _get_plugin_data_markup_translate( $plugin_file, $data );
									$plugin_info[ $plugin_file ]['is_uninstallable'] = is_uninstallable_plugin( $plugin );
									if ( ! $plugin_info[ $plugin_file ]['Network'] )
										$have_non_network_plugins = true;
								}
							}
						}
					}
					screen_icon();
					$plugins_to_delete = count( $plugin_info );
					echo '<h2>' . _n( 'Delete Plugin', 'Delete Plugins', $plugins_to_delete ) . '</h2>';
				?>
				<?php if ( $have_non_network_plugins && is_network_admin() ) : ?>
				<div class="error"><p><strong><?php _e( 'Caution:' ); ?></strong> <?php echo _n( 'This plugin may be active on other sites in the network.', 'These plugins may be active on other sites in the network.', $plugins_to_delete ); ?></p></div>
				<?php endif; ?>
				<p><?php echo _n( 'You are about to remove the following plugin:', 'You are about to remove the following plugins:', $plugins_to_delete ); ?></p>
					<ul class="ul-disc">
						<?php
						$data_to_delete = false;
						foreach ( $plugin_info as $plugin ) {
							if ( $plugin['is_uninstallable'] ) {
								/* translators: 1: plugin name, 2: plugin author */
								echo '<li>', sprintf( __( '<strong>%1$s</strong> by <em>%2$s</em> (will also <strong>delete its data</strong>)' ), esc_html($plugin['Name']), esc_html($plugin['AuthorName']) ), '</li>';
								$data_to_delete = true;
							} else {
								/* translators: 1: plugin name, 2: plugin author */
								echo '<li>', sprintf( __('<strong>%1$s</strong> by <em>%2$s</em>' ), esc_html($plugin['Name']), esc_html($plugin['AuthorName']) ), '</li>';
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
						foreach ( (array) $plugins as $plugin )
							echo '<input type="hidden" name="checked[]" value="' . esc_attr($plugin) . '" />';
					?>
					<?php wp_nonce_field('bulk-plugins') ?>
					<?php submit_button( $data_to_delete ? __( 'Yes, Delete these files and data' ) : __( 'Yes, Delete these files' ), 'button', 'submit', false ); ?>
				</form>
				<form method="post" action="<?php echo esc_url(wp_get_referer()); ?>" style="display:inline;">
					<?php submit_button( __( 'No, Return me to the plugin list' ), 'button', 'submit', false ); ?>
				</form>

				<p><a href="#" onclick="jQuery('#files-list').toggle(); return false;"><?php _e('Click to view entire list of files which will be deleted'); ?></a></p>
				<div id="files-list" style="display:none;">
					<ul class="code">
					<?php
						foreach ( (array)$files_to_delete as $file )
							echo '<li>' . esc_html(str_replace(WP_PLUGIN_DIR, '', $file)) . '</li>';
					?>
					</ul>
				</div>
			</div>
				<?php
				require_once(ABSPATH . 'wp-admin/admin-footer.php');
				exit;
			} //Endif verify-delete
			$delete_result = delete_plugins($plugins);

			set_transient('plugins_delete_result_' . $user_ID, $delete_result); //Store the result in a cache rather than a URL param due to object type & length
			wp_redirect( self_admin_url("plugins.php?deleted=true&plugin_status=$status&paged=$page&s=$s") );
			exit;
			break;
		case 'clear-recent-list':
			update_option('recently_activated', array());
			break;
	}
}

$wp_list_table->prepare_items();

wp_enqueue_script('plugin-install');
add_thickbox();

add_screen_option( 'per_page', array('label' => _x( 'Plugins', 'plugins per page (screen options)' )) );

add_contextual_help($current_screen,
	'<p>' . __('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.') . '</p>' .
	'<p>' . sprintf(__('You can find additional plugins for your site by using the <a href="%1$s">Plugin Browser/Installer</a> functionality or by browsing the <a href="%2$s" target="_blank">WordPress Plugin Directory</a> directly and installing new plugins manually. To manually install a plugin you generally just need to upload the plugin file into your <code>/wp-content/plugins</code> directory. Once a plugin has been installed, you can activate it here.'), 'plugin-install.php', 'http://wordpress.org/extend/plugins/') . '</p>' .
	'<p>' . __('Most of the time, plugins play nicely with the core of WordPress and with other plugins. Sometimes, though, a plugin&#8217;s code will get in the way of another plugin, causing compatibility issues. If your site starts doing strange things, this may be the problem. Try deactivating all your plugins and re-activating them in various combinations until you isolate which one(s) caused the issue.') . '</p>' .
	'<p>' . sprintf( __('If something goes wrong with a plugin and you can&#8217;t use WordPress, delete or rename that file in the <code>%s</code> directory and it will be automatically deactivated.'), WP_PLUGIN_DIR) . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Managing_Plugins#Plugin_Management" target="_blank">Documentation on Managing Plugins</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

$title = __('Plugins');
$parent_file = 'plugins.php';

require_once(ABSPATH . 'wp-admin/admin-header.php');

$invalid = validate_active_plugins();
if ( !empty($invalid) )
	foreach ( $invalid as $plugin_file => $error )
		echo '<div id="message" class="error"><p>' . sprintf(__('The plugin <code>%s</code> has been <strong>deactivated</strong> due to an error: %s'), esc_html($plugin_file), $error->get_error_message()) . '</p></div>';
?>

<?php if ( isset($_GET['error']) ) :

	if ( isset( $_GET['main'] ) )
		$errmsg = __( 'You cannot delete a plugin while it is active on the main site.' );
	elseif ( isset($_GET['charsout']) )
		$errmsg = sprintf(__('The plugin generated %d characters of <strong>unexpected output</strong> during activation.  If you notice &#8220;headers already sent&#8221; messages, problems with syndication feeds or other issues, try deactivating or removing this plugin.'), $_GET['charsout']);
	else
		$errmsg = __('Plugin could not be activated because it triggered a <strong>fatal error</strong>.');
	?>
	<div id="message" class="updated"><p><?php echo $errmsg; ?></p>
	<?php
		if ( !isset( $_GET['main'] ) && !isset($_GET['charsout']) && wp_verify_nonce($_GET['_error_nonce'], 'plugin-activation-error_' . $plugin) ) { ?>
	<iframe style="border:0" width="100%" height="70px" src="<?php echo 'plugins.php?action=error_scrape&amp;plugin=' . esc_attr($plugin) . '&amp;_wpnonce=' . esc_attr($_GET['_error_nonce']); ?>"></iframe>
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
<h2><?php echo esc_html( $title );
if ( ( ! is_multisite() || is_network_admin() ) && current_user_can('install_plugins') ) { ?>
<a href="<?php echo self_admin_url( 'plugin-install.php' ); ?>" class="add-new-h2"><?php echo esc_html_x('Add New', 'plugin'); ?></a>
<?php }
if ( $s )
	printf( '<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html( $s ) ); ?>
</h2>

<?php do_action( 'pre_current_active_plugins', $plugins['all'] ) ?>

<?php $wp_list_table->views(); ?>

<form method="post" action="">

<?php $wp_list_table->search_box( __( 'Search Plugins' ), 'plugin' ); ?>

<input type="hidden" name="plugin_status" value="<?php echo esc_attr($status) ?>" />
<input type="hidden" name="paged" value="<?php echo esc_attr($page) ?>" />

<?php $wp_list_table->display(); ?>
</form>

</div>

<?php
include(ABSPATH . 'wp-admin/admin-footer.php');

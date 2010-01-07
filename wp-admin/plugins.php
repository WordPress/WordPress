<?php
/**
 * Plugins administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( ! current_user_can('activate_plugins') )
	wp_die(__('You do not have sufficient permissions to manage plugins for this blog.'));

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
if ( !in_array($status, array('all', 'active', 'inactive', 'recent', 'upgrade', 'search')) )
	$status = 'all';
if ( $status != $default_status && 'search' != $status )
	update_usermeta($current_user->ID, 'plugins_last_view', $status);

$page = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;

//Clean up request URI from temporary args for screen options/paging uri's to work as expected.
$_SERVER['REQUEST_URI'] = remove_query_arg(array('error', 'deleted', 'activate', 'activate-multi', 'deactivate', 'deactivate-multi', '_error_nonce'), $_SERVER['REQUEST_URI']);

if ( !empty($action) ) {
	switch ( $action ) {
		case 'activate':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to activate plugins for this blog.'));

			check_admin_referer('activate-plugin_' . $plugin);

			$result = activate_plugin($plugin, 'plugins.php?error=true&plugin=' . $plugin);
			if ( is_wp_error( $result ) )
				wp_die($result);

			$recent = (array)get_option('recently_activated');
			if ( isset($recent[ $plugin ]) ) {
				unset($recent[ $plugin ]);
				update_option('recently_activated', $recent);
			}

			wp_redirect("plugins.php?activate=true&plugin_status=$status&paged=$page"); // overrides the ?error=true one above
			exit;
			break;
		case 'activate-selected':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to activate plugins for this blog.'));

			check_admin_referer('bulk-manage-plugins');

			$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			$plugins = array_filter($plugins, create_function('$plugin', 'return !is_plugin_active($plugin);') ); //Only activate plugins which are not already active.
			if ( empty($plugins) ) {
				wp_redirect("plugins.php?plugin_status=$status&paged=$page");
				exit;
			}

			activate_plugins($plugins, 'plugins.php?error=true');

			$recent = (array)get_option('recently_activated');
			foreach ( $plugins as $plugin => $time)
				if ( isset($recent[ $plugin ]) )
					unset($recent[ $plugin ]);

			update_option('recently_activated', $recent);

			wp_redirect("plugins.php?activate-multi=true&plugin_status=$status&paged=$page");
			exit;
			break;
		case 'error_scrape':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to activate plugins for this blog.'));

			check_admin_referer('plugin-activation-error_' . $plugin);

			$valid = validate_plugin($plugin);
			if ( is_wp_error($valid) )
				wp_die($valid);

			if ( defined('E_RECOVERABLE_ERROR') )
				error_reporting(E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);
			else
				error_reporting(E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING);

			@ini_set('display_errors', true); //Ensure that Fatal errors are displayed.
			include(WP_PLUGIN_DIR . '/' . $plugin);
			do_action('activate_' . $plugin);
			exit;
			break;
		case 'deactivate':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to deactivate plugins for this blog.'));

			check_admin_referer('deactivate-plugin_' . $plugin);
			deactivate_plugins($plugin);
			update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));
			wp_redirect("plugins.php?deactivate=true&plugin_status=$status&paged=$page");
			exit;
			break;
		case 'deactivate-selected':
			if ( ! current_user_can('activate_plugins') )
				wp_die(__('You do not have sufficient permissions to deactivate plugins for this blog.'));

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
				wp_die(__('You do not have sufficient permissions to delete plugins for this blog.'));

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
				require_once('admin-header.php');
				?>
			<div class="wrap">
				<h2><?php _e('Delete Plugin(s)'); ?></h2>
				<?php
					$files_to_delete = $plugin_info = array();
					foreach ( (array) $plugins as $plugin ) {
						if ( '.' == dirname($plugin) ) {
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
					<ul class="ul-disc">
						<?php
						foreach ( $plugin_info as $plugin )
							echo '<li>', sprintf(__('<strong>%s</strong> by <em>%s</em>'), $plugin['Name'], $plugin['Author']), '</li>';
						?>
					</ul>
				<p><?php _e('Are you sure you wish to delete these files?') ?></p>
				<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" style="display:inline;">
					<input type="hidden" name="verify-delete" value="1" />
					<input type="hidden" name="action" value="delete-selected" />
					<?php
						foreach ( (array)$plugins as $plugin )
							echo '<input type="hidden" name="checked[]" value="' . esc_attr($plugin) . '" />';
					?>
					<?php wp_nonce_field('bulk-manage-plugins') ?>
					<input type="submit" name="submit" value="<?php esc_attr_e('Yes, Delete these files') ?>" class="button" />
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
				require_once('admin-footer.php');
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

$help = '<p>' . __('Plugins extend and expand the functionality of WordPress. Once a plugin is installed, you may activate it or deactivate it here.') . '</p>';
$help .= '<p>' . sprintf(__('If something goes wrong with a plugin and you can&#8217;t use WordPress, delete or rename that file in the <code>%s</code> directory and it will be automatically deactivated.'), WP_PLUGIN_DIR) . '</p>';
$help .= '<p>' . sprintf(__('You can find additional plugins for your site by using the new <a href="%1$s">Plugin Browser/Installer</a> functionality or by browsing the <a href="http://wordpress.org/extend/plugins/">WordPress Plugin Directory</a> directly and installing manually.  To <em>manually</em> install a plugin you generally just need to upload the plugin file into your <code>%2$s</code> directory.  Once a plugin has been installed, you may activate it here.'), 'plugin-install.php', WP_PLUGIN_DIR) . '</p>';

add_contextual_help('plugins', $help);

if ( is_multisite() && is_super_admin() ) {
	$menu_perms = get_site_option('menu_items', array());
	if ( !$menu_perms['plugins'] ) {
		add_action( 'admin_notices', '_admin_notice_multisite_activate_plugins_page' );
	}
}

$title = __('Manage Plugins');
require_once('admin-header.php');

$invalid = validate_active_plugins();
if ( !empty($invalid) )
	foreach ( $invalid as $plugin_file => $error )
		echo '<div id="message" class="error"><p>' . sprintf(__('The plugin <code>%s</code> has been <strong>deactivated</strong> due to an error: %s'), esc_html($plugin_file), $error->get_error_message()) . '</p></div>';
?>

<?php if ( isset($_GET['error']) ) : ?>
	<div id="message" class="updated"><p><?php _e('Plugin could not be activated because it triggered a <strong>fatal error</strong>.') ?></p>
	<?php
		if ( wp_verify_nonce($_GET['_error_nonce'], 'plugin-activation-error_' . $plugin) ) { ?>
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
<?php endif; ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?> <a href="plugin-install.php" class="button add-new-h2"><?php echo esc_html_x('Add New', 'plugin'); ?></a></h2>

<?php

$all_plugins = get_plugins();
$search_plugins = array();
$active_plugins = array();
$inactive_plugins = array();
$recent_plugins = array();
$recently_activated = get_option('recently_activated', array());
$upgrade_plugins = array();

set_transient( 'plugin_slugs', array_keys($all_plugins), 86400 );

// Clean out any plugins which were deactivated over a week ago.
foreach ( $recently_activated as $key => $time )
	if ( $time + (7*24*60*60) < time() ) //1 week
		unset($recently_activated[ $key ]);
if ( $recently_activated != get_option('recently_activated') ) //If array changed, update it.
	update_option('recently_activated', $recently_activated);
$current = get_transient( 'update_plugins' );

foreach ( (array)$all_plugins as $plugin_file => $plugin_data) {

	//Translate, Apply Markup, Sanitize HTML
	$plugin_data = _get_plugin_data_markup_translate($plugin_file, $plugin_data, false, true);
	$all_plugins[ $plugin_file ] = $plugin_data;

	//Filter into individual sections
	if ( is_plugin_active($plugin_file) ) {
		$active_plugins[ $plugin_file ] = $plugin_data;
	} else {
		if ( isset( $recently_activated[ $plugin_file ] ) ) // Was the plugin recently activated?
			$recent_plugins[ $plugin_file ] = $plugin_data;
		$inactive_plugins[ $plugin_file ] = $plugin_data;
	}

    if ( isset( $current->response[ $plugin_file ] ) )
        $upgrade_plugins[ $plugin_file ] = $plugin_data;
}

$total_all_plugins = count($all_plugins);
$total_inactive_plugins = count($inactive_plugins);
$total_active_plugins = count($active_plugins);
$total_recent_plugins = count($recent_plugins);
$total_upgrade_plugins = count($upgrade_plugins);

//Searching.
if ( isset($_GET['s']) ) {
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
if ( empty($$plugin_array_name) && $status != 'all' ) {
	$status = 'all';
	$plugin_array_name = "${status}_plugins";
}

$plugins = &$$plugin_array_name;

//Paging.
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
?>
<table class="widefat" cellspacing="0" id="<?php echo $context ?>-plugins-table">
	<thead>
	<tr>
		<th scope="col" class="manage-column check-column"><input type="checkbox" /></th>
		<th scope="col" class="manage-column"><?php _e('Plugin'); ?></th>
		<th scope="col" class="manage-column"><?php _e('Description'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column check-column"><input type="checkbox" /></th>
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
		$actions = array();
		$is_active = is_plugin_active($plugin_file);

		if ( $is_active )
			$actions[] = '<a href="' . wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page, 'deactivate-plugin_' . $plugin_file) . '" title="' . __('Deactivate this plugin') . '">' . __('Deactivate') . '</a>';
		else
			$actions[] = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page, 'activate-plugin_' . $plugin_file) . '" title="' . __('Activate this plugin') . '" class="edit">' . __('Activate') . '</a>';

		if ( current_user_can('edit_plugins') && is_writable(WP_PLUGIN_DIR . '/' . $plugin_file) )
			$actions[] = '<a href="plugin-editor.php?file=' . $plugin_file . '" title="' . __('Open this file in the Plugin Editor') . '" class="edit">' . __('Edit') . '</a>';

		if ( ! $is_active && current_user_can('delete_plugins') )
			$actions[] = '<a href="' . wp_nonce_url('plugins.php?action=delete-selected&amp;checked[]=' . $plugin_file . '&amp;plugin_status=' . $context . '&amp;paged=' . $page, 'bulk-manage-plugins') . '" title="' . __('Delete this plugin') . '" class="delete">' . __('Delete') . '</a>';

		$actions = apply_filters( 'plugin_action_links', $actions, $plugin_file, $plugin_data, $context );
		$actions = apply_filters( "plugin_action_links_$plugin_file", $actions, $plugin_file, $plugin_data, $context );
		$action_count = count($actions);
		$class = $is_active ? 'active' : 'inactive';
		echo "
	<tr class='$class'>
		<th scope='row' class='check-column'><input type='checkbox' name='checked[]' value='" . esc_attr($plugin_file) . "' /></th>
		<td class='plugin-title'><strong>{$plugin_data['Name']}</strong></td>
		<td class='desc'><p>{$plugin_data['Description']}</p></td>
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
?>
	<div class="alignleft actions">
		<select name="<?php echo $field_name; ?>">
			<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
	<?php if ( 'active' != $context ) : ?>
			<option value="activate-selected"><?php _e('Activate'); ?></option>
	<?php endif; ?>
	<?php if ( 'inactive' != $context && 'recent' != $context ) : ?>
			<option value="deactivate-selected"><?php _e('Deactivate'); ?></option>
	<?php endif; ?>
	<?php if ( current_user_can('delete_plugins') && ( 'active' != $context ) ) : ?>
			<option value="delete-selected"><?php _e('Delete'); ?></option>
	<?php endif; ?>
		</select>
		<input type="submit" name="doaction_active" value="<?php esc_attr_e('Apply'); ?>" class="button-secondary action" />
	<?php if( 'recent' == $context ) : ?>
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
	<input type="submit" value="<?php esc_attr_e( 'Search Plugins' ); ?>" class="button" />
</p>
</form>

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

<div class="tablenav">
<?php
if ( $page_links )
	echo '<div class="tablenav-pages">', $page_links_text, '</div>';

print_plugin_actions($status);
?>
</div>
<div class="clear"></div>
<?php
	if ( $total_this_page > $plugins_per_page )
		$plugins = array_slice($plugins, $start, $plugins_per_page);

	print_plugins_table($plugins, $status);
?>
<div class="tablenav">
<?php
if ( $page_links )
	echo "<div class='tablenav-pages'>$page_links_text</div>";

print_plugin_actions($status, "action2");
?>
</div>
</form>

<?php if ( empty($all_plugins) ) : ?>
<p><?php _e('You do not appear to have any plugins available at this time.') ?></p>
<?php endif; ?>

</div>

<?php
include('admin-footer.php');
?>

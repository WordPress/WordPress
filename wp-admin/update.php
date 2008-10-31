<?php
/**
 * Update Plugin / Core administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( ! current_user_can('update_plugins') )
	wp_die(__('You do not have sufficient permissions to update plugins for this blog.'));

/**
 * Plugin upgrade display.
 *
 * @since 2.5
 *
 * @param string $plugin Plugin
 */
function do_plugin_upgrade($plugin) {
	global $wp_filesystem;

	$url = wp_nonce_url("update.php?action=upgrade-plugin&plugin=$plugin", "upgrade-plugin_$plugin");
	if ( false === ($credentials = request_filesystem_credentials($url)) )
		return;

	if ( ! WP_Filesystem($credentials) ) {
		$error = true;
		if ( is_object($wp_filesystem) && $wp_filesystem->errors->get_error_code() )
			$error = $wp_filesystem->errors;
		request_filesystem_credentials($url, '', $error); //Failed to connect, Error and request again
		return;
	}

	echo '<div class="wrap">';
	echo '<h2>' . __('Upgrade Plugin') . '</h2>';
	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
			show_message($message);
		echo '</div>';
		return;
	}

	$was_activated = is_plugin_active($plugin); //Check now, It'll be deactivated by the next line if it is

	$result = wp_update_plugin($plugin, 'show_message');

	if ( is_wp_error($result) ) {
		show_message($result);
		show_message( __('Plugin upgrade Failed') );
	} else {
		$plugin_file = $result;
		show_message( __('Plugin upgraded successfully') );
		if( $result && $was_activated ){
			show_message(__('Attempting reactivation of the plugin'));
			echo '<iframe style="border:0" width="100%" height="170px" src="' . wp_nonce_url('update.php?action=activate-plugin&plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file) .'"></iframe>';
		}
		$update_actions =  array(
			'activate_plugin' => '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file) . '" title="' . attribute_escape(__('Activate this plugin')) . '" target="_parent">' . __('Activate Plugin') . '</a>',
			'plugins_page' => '<a href="' . admin_url('plugins.php') . '" title="' . attribute_escape(__('Goto plugins page')) . '" target="_parent">' . __('Return to Plugins page') . '</a>'
		);
		if ( $was_activated )
			unset( $update_actions['activate_plugin'] );

		$update_actions = apply_filters('update_plugin_complete_actions', $update_actions, $plugin_file);
		if ( ! empty($update_actions) )
			show_message('<strong>' . __('Actions:') . '</strong> ' . implode(' | ', (array)$update_actions));
	}
	echo '</div>';
}

/**
 * Theme upgrade display.
 *
 * @since 2.5
 *
 * @param string $plugin Plugin
 */
function do_theme_upgrade($theme) {
	global $wp_filesystem;

	$url = wp_nonce_url('update.php?action=upgrade-theme&theme=' . urlencode($theme), 'upgrade-plugin_' . urlencode($theme));
	if ( false === ($credentials = request_filesystem_credentials($url)) )
		return;

	if ( ! WP_Filesystem($credentials) ) {
		$error = true;
		if ( is_object($wp_filesystem) && $wp_filesystem->errors->get_error_code() )
			$error = $wp_filesystem->errors;
		request_filesystem_credentials($url, '', $error); //Failed to connect, Error and request again
		return;
	}

	echo '<div class="wrap">';
	echo '<h2>' . __('Upgrade Theme') . '</h2>';
	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
			show_message($message);
		echo '</div>';
		return;
	}

	//TODO: Is theme currently active?
	$was_current = false; //is_plugin_active($plugin); //Check now, It'll be deactivated by the next line if it is

	$result = wp_update_theme($theme, 'show_message');

	if ( is_wp_error($result) ) {
		show_message($result);
		show_message( __('Installation Failed') );
	} else {
		//Result is the new plugin file relative to WP_PLUGIN_DIR
		show_message( __('Theme upgraded successfully') );
		if( $result && $was_current ){
			show_message(__('Setting theme as Current'));
			//TODO: Actually set it as active again.
			//echo '<iframe style="border:0" width="100%" height="170px" src="' . wp_nonce_url('update.php?action=activate-plugin&plugin=' . $result, 'activate-plugin_' . $result) .'"></iframe>';
		}
	}
	echo '</div>';
}

function list_core_update( $update ) {
	$version_string = 'en_US' == $update->locale?
			$update->current : sprintf("%s&ndash;<strong>%s</strong>", $update->current, $update->locale); 
	if ( 'development' == $update->response ) {
		$message = __('You are using a development version of WordPress.  You can upgrade to the latest nightly build automatically or download the nightly build and install it manually:');
		$submit = __('Download nightly build');
	} else {
		$message = 	sprintf(__('You can upgrade to version %s automatically or download the package and install it manually:'), $version_string);
		$submit = sprintf(__('Download %s'), $version_string);
	}

	echo '<p>';
	echo $message;
	echo '</p>';
	echo '<form method="post" action="update.php?action=do-core-upgrade" name="upgrade" class="upgrade">';
	wp_nonce_field('upgrade-core');
	echo '<p>';
	echo '<input id="upgrade" class="button" type="submit" value="' . __('Upgrade Automatically') . '" name="upgrade" />&nbsp;';
	echo '<input name="version" value="'.$update->current.'" type="hidden"/>';
	echo '<input name="locale" value="'.$update->locale.'" type="hidden"/>';
	echo '<a href="' . $update->package . '" class="button">' . $submit . '</a>&nbsp;';
	if ( 'en_US' != $update->locale )
		if ( !isset( $update->dismissed ) || !$update->dismissed )
			echo '<input id="dismiss" class="button" type="submit" value="' . attribute_escape(__('Hide this update')) . '" name="dismiss" />';
		else
			echo '<input id="undismiss" class="button" type="submit" value="' . attribute_escape(__('Bring back this update')) . '" name="undismiss" />';
	echo '</p>';
	echo '</form>';
	
}

function dismissed_updates() {
	$dismissed = get_core_updates( array( 'dismissed' => true, 'available' => false ) );
	if ( $dismissed ) {
		
		$show_text = js_escape(__('Show hidden updates'));
		$hide_text = js_escape(__('Hide hidden updates'));
	?>
	<script type="text/javascript">
		
		jQuery(function($) {
			$('dismissed-updates').show();
			$('#show-dismissed').toggle(function(){$(this).text('<?php echo $hide_text; ?>');}, function() {$(this).text('<?php echo $show_text; ?>')});
			$('#show-dismissed').click(function() { $('#dismissed-updates').toggle('slow');});
		});
	</script>
	<?php
		echo '<p class="hide-if-no-js"><a id="show-dismissed" href="#">'.__('Show hidden updates').'</a></p>';
		echo '<ul id="dismissed-updates" class="core-updates dismissed">';
		foreach($dismissed as $update) {
			echo '<li>';
			list_core_update( $update );
			echo '</li>';
		}
		echo '</ul>';
	}	
}

/**
 * Display upgrade WordPress for downloading latest or upgrading automatically form.
 *
 * @since 2.7
 *
 * @return null
 */
function core_upgrade_preamble() {
	$updates = get_core_updates();
	
	echo '<div class="wrap">';
	echo '<h2>' . __('Upgrade WordPress') . '</h2>';

	if ( !isset($updates[0]->response) || 'latest' == $updates[0]->response ) {
		echo '<h3>';
		_e('You have the latest version of WordPress. You do not need to upgrade');
		echo '</h3>';
		dismissed_updates();
		echo '</div>';
		return;
	}

	echo '<div class="updated fade"><p>';
	_e('<strong>Important:</strong> before upgrading, please <a href="http://codex.wordpress.org/WordPress_Backups">backup your database and files</a>.');  
	echo '</p></div>';
	
	echo '<h3 class="response">';
	_e( 'There is a new version of WordPress available for upgrade' );
	echo '</h3>';
	echo '<ul class="core-updates">';
	$alternate = true;
	foreach( $updates as $update ) {
		$class = $alternate? ' class="alternate"' : '';
		$alternate = !$alternate;
		echo "<li $class>";
		list_core_update( $update );
		echo '</li>';
	}
	echo '</ul>';
	dismissed_updates();
	echo '</div>';
}


/**
 * Upgrade WordPress core display.
 *
 * @since 2.7
 *
 * @return null
 */
function do_core_upgrade() {
	global $wp_filesystem;
	
	$url = wp_nonce_url('update.php?action=do-core-upgrade', 'upgrade-core');
	if ( false === ($credentials = request_filesystem_credentials($url)) )
		return;
		
	$version = isset( $_POST['version'] )? $_POST['version'] : false;
	$locale = isset( $_POST['locale'] )? $_POST['locale'] : 'en_US';
	$update = find_core_update( $version, $locale );
	if ( !$update )
		return;
		

	if ( ! WP_Filesystem($credentials) ) {
		request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
		return;
	}

	echo '<div class="wrap">';
	echo '<h2>' . __('Upgrade WordPress') . '</h2>';
	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
			show_message($message);
		echo '</div>';
		return;
	}

	$result = wp_update_core($update, 'show_message');

	if ( is_wp_error($result) ) {
		show_message($result);
		if ('up_to_date' != $result->get_error_code() )
			show_message( __('Installation Failed') );
	} else {
		show_message( __('WordPress upgraded successfully') );
	}
	echo '</div>';
}

function do_dismiss_core_update() {
	$version = isset( $_POST['version'] )? $_POST['version'] : false;
	$locale = isset( $_POST['locale'] )? $_POST['locale'] : 'en_US';
	$update = find_core_update( $version, $locale );
	if ( !$update )
		return;
	dismiss_core_update( $update );
	wp_redirect( wp_nonce_url('update.php?action=upgrade-core', 'upgrade-core') );
}

function do_undismiss_core_update() {
	$version = isset( $_POST['version'] )? $_POST['version'] : false;
	$locale = isset( $_POST['locale'] )? $_POST['locale'] : 'en_US';
	$update = find_core_update( $version, $locale );
	if ( !$update )
		return;
	undismiss_core_update( $version, $locale );
	wp_redirect( wp_nonce_url('update.php?action=upgrade-core', 'upgrade-core') );
}

if ( isset($_GET['action']) ) {
	$plugin = isset($_GET['plugin']) ? trim($_GET['plugin']) : '';
	$theme = isset($_REQUEST['theme']) ? urldecode($_REQUEST['theme']) : '';
	$action = isset($_GET['action']) ? $_GET['action'] : '';
	
	if ( 'upgrade-plugin' == $action ) {
		check_admin_referer('upgrade-plugin_' . $plugin);
		$title = __('Upgrade Plugin');
		$parent_file = 'index.php';
		require_once('admin-header.php');
		do_plugin_upgrade($plugin);
		include('admin-footer.php');
	} elseif ('activate-plugin' == $action ) {
		check_admin_referer('activate-plugin_' . $plugin);
		if( ! isset($_GET['failure']) && ! isset($_GET['success']) ) {
			wp_redirect( 'update.php?action=activate-plugin&failure=true&plugin=' . $plugin . '&_wpnonce=' . $_GET['_wpnonce'] );
			activate_plugin($plugin);
			wp_redirect( 'update.php?action=activate-plugin&success=true&plugin=' . $plugin . '&_wpnonce=' . $_GET['_wpnonce'] );
			die();
		}
		iframe_header( __('Plugin Reactivation'), true );
		if( isset($_GET['success']) )
			echo '<p>' . __('Plugin reactivated successfully.') . '</p>';

		if( isset($_GET['failure']) ){
			echo '<p>' . __('Plugin failed to reactivate due to a fatal error.') . '</p>';
			error_reporting( E_ALL ^ E_NOTICE );
			@ini_set('display_errors', true); //Ensure that Fatal errors are displayed.
			include(WP_PLUGIN_DIR . '/' . $plugin);
		}
		iframe_footer();
	} elseif ( 'upgrade-core' == $action ) {
		$title = __('Upgrade WordPress');
		$parent_file = 'index.php';
		require_once('admin-header.php');
		core_upgrade_preamble();
		include('admin-footer.php');
	} elseif ( 'do-core-upgrade' == $action ) {
		check_admin_referer('upgrade-core');
		$title = __('Upgrade WordPress');
		$parent_file = 'index.php';
		// do the (un)dismiss actions before headers,
		// so that they can redirect
		if ( isset( $_POST['dismiss'] ) )
			do_dismiss_core_update();
		elseif ( isset( $_POST['undismiss'] ) )
			do_undismiss_core_update();
		require_once('admin-header.php');
		if ( isset( $_POST['upgrade'] ) )
			do_core_upgrade();
		include('admin-footer.php');
	} elseif ( 'upgrade-theme' == $action ) {	
		check_admin_referer('upgrade-theme_' . $theme);
		$title = __('Upgrade Theme');
		$parent_file = 'themes.php';
		require_once('admin-header.php');
		do_theme_upgrade($theme);
		include('admin-footer.php');
	}
}

?>

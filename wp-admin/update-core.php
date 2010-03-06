<?php
/**
 * Update Core administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( ! current_user_can('update_plugins') )
	wp_die(__('You do not have sufficient permissions to update plugins for this blog.'));

function list_core_update( $update ) {
	global $wp_local_package, $wpdb;
	$version_string = ('en_US' == $update->locale && 'en_US' == get_locale() ) ?
			$update->current : sprintf("%s&ndash;<strong>%s</strong>", $update->current, $update->locale);
	$current = false;
	if ( !isset($update->response) || 'latest' == $update->response )
		$current = true;
	$submit = __('Update Automatically');
	$form_action = 'update-core.php?action=do-core-upgrade';
	$php_version    = phpversion();
	$mysql_version  = $wpdb->db_version();
	$show_buttons = true;
	if ( 'development' == $update->response ) {
		$message = __('You are using a development version of WordPress.  You can update to the latest nightly build automatically or download the nightly build and install it manually:');
		$download = __('Download nightly build');
	} else {
		if ( $current ) {
			$message = sprintf(__('You have the latest version of WordPress. You do not need to update. However, if you want to re-install version %s, you can do so automatically or download the package and re-install manually:'), $version_string);
			$submit = __('Re-install Automatically');
			$form_action = 'update-core.php?action=do-core-reinstall';
		} else {
			$php_compat     = version_compare( $php_version, $update->php_version, '>=' );
			$mysql_compat   = version_compare( $mysql_version, $update->mysql_version, '>=' ) || file_exists( WP_CONTENT_DIR . '/db.php' );
			if ( !$mysql_compat && !$php_compat )
				$message = sprintf( __('You cannot update because <a href="http://codex.wordpress.org/Version_%1$s">WordPress %1$s</a> requires PHP version %2$s or higher and MySQL version %3$s or higher. You are running PHP version %4$s and MySQL version %5$s.'), $update->current, $update->php_version, $update->mysql_version, $php_version, $mysql_version );
			elseif ( !$php_compat )
				$message = sprintf( __('You cannot update because <a href="http://codex.wordpress.org/Version_%1$s">WordPress %1$s</a> requires PHP version %2$s or higher. You are running version %3$s.'), $update->current, $update->php_version, $php_version );
			elseif ( !$mysql_compat )
				$message = sprintf( __('You cannot update because <a href="http://codex.wordpress.org/Version_%1$s">WordPress %1$s</a> requires MySQL version %2$s or higher. You are running version %3$s.'), $update->current, $update->mysql_version, $mysql_version );
			else
				$message = 	sprintf(__('You can update to <a href="http://codex.wordpress.org/Version_%1$s">WordPress %1$s</a> automatically or download the package and install it manually:'), $version_string);
			if ( !$mysql_compat || !$php_compat )
				$show_buttons = false;
		}
		$download = sprintf(__('Download %s'), $version_string);
	}

	echo '<p>';
	echo $message;
	echo '</p>';
	echo '<form method="post" action="' . $form_action . '" name="upgrade" class="upgrade">';
	wp_nonce_field('upgrade-core');
	echo '<p>';
	echo '<input name="version" value="'. esc_attr($update->current) .'" type="hidden"/>';
	echo '<input name="locale" value="'. esc_attr($update->locale) .'" type="hidden"/>';
	if ( $show_buttons ) {
		echo '<input id="upgrade" class="button" type="submit" value="' . esc_attr($submit) . '" name="upgrade" />&nbsp;';
		echo '<a href="' . esc_url($update->package) . '" class="button">' . $download . '</a>&nbsp;';
	}
	if ( 'en_US' != $update->locale )
		if ( !isset( $update->dismissed ) || !$update->dismissed )
			echo '<input id="dismiss" class="button" type="submit" value="' . esc_attr__('Hide this update') . '" name="dismiss" />';
		else
			echo '<input id="undismiss" class="button" type="submit" value="' . esc_attr__('Bring back this update') . '" name="undismiss" />';
	echo '</p>';
	if ( 'en_US' != $update->locale && ( !isset($wp_local_package) || $wp_local_package != $update->locale ) )
	    echo '<p class="hint">'.__('This localized version contains both the translation and various other localization fixes. You can skip upgrading if you want to keep your current translation.').'</p>';
	else if ( 'en_US' == $update->locale && get_locale() != 'en_US' ) {
	    echo '<p class="hint">'.sprintf( __('You are about to install WordPress %s <strong>in English.</strong> There is a chance this upgrade will break your translation. You may prefer to wait for the localized version to be released.'), $update->current ).'</p>';
	}
	echo '</form>';

}

function dismissed_updates() {
	$dismissed = get_core_updates( array( 'dismissed' => true, 'available' => false ) );
	if ( $dismissed ) {

		$show_text = esc_js(__('Show hidden updates'));
		$hide_text = esc_js(__('Hide hidden updates'));
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
		foreach( (array) $dismissed as $update) {
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
	global $upgrade_error;

	$updates = get_core_updates();
?>
	<div class="wrap">
	<?php screen_icon('tools'); ?>
	<h2><?php _e('WordPress Updates'); ?></h2>
<?php
	if ( $upgrade_error ) {
		echo '<div class="error"><p>';
		_e('Please select one or more plugins to upgrade.');
		echo '</p></div>';
	}

	if ( !isset($updates[0]->response) || 'latest' == $updates[0]->response ) {
		echo '<h3>';
		_e('You have the latest version of WordPress. You do not need to update');
		echo '</h3>';
	} else {
		echo '<div class="updated"><p>';
		_e('<strong>Important:</strong> before updating, please <a href="http://codex.wordpress.org/WordPress_Backups">backup your database and files</a>.');
		echo '</p></div>';

		echo '<h3 class="response">';
		_e( 'There is a new version of WordPress available for update' );
		echo '</h3>';
	}

	echo '<ul class="core-updates">';
	$alternate = true;
	foreach( (array) $updates as $update ) {
		$class = $alternate? ' class="alternate"' : '';
		$alternate = !$alternate;
		echo "<li $class>";
		list_core_update( $update );
		echo '</li>';
	}
	echo '</ul>';
	dismissed_updates();

	list_plugin_updates();
	list_theme_updates();
	do_action('core_upgrade_preamble');
	echo '</div>';
}

function list_plugin_updates() {
	global $wp_version;

	$cur_wp_version = preg_replace('/-.*$/', '', $wp_version);

	require_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
	$plugins = get_plugin_updates();
	if ( empty($plugins) )
		return;
	$form_action = 'update-core.php?action=do-plugin-upgrade';

	$core_updates = get_core_updates();
	if ( !isset($core_updates[0]->response) || 'latest' == $core_updates[0]->response || 'development' == $core_updates[0]->response || version_compare( $core_updates[0]->current, $cur_wp_version, '=') )
		$core_update_version = false;
	else
		$core_update_version = $core_updates[0]->current;
	?>
<h3><?php _e('Plugins'); ?></h3>
<p><?php _e('The following plugins have new versions available. Check the ones you want to update and then click "Update Plugins".'); ?></p>
<form method="post" action="<?php echo $form_action; ?>" name="upgrade-plugins" class="upgrade">
<?php wp_nonce_field('upgrade-core'); ?>
<p><input id="upgrade-plugins" class="button" type="submit" value="<?php esc_attr_e('Update Plugins'); ?>" name="upgrade" /></p>
<table class="widefat" cellspacing="0" id="update-plugins-table">
	<thead>
	<tr>
		<th scope="col" class="manage-column check-column"><input type="checkbox" /></th>
		<th scope="col" class="manage-column"><?php _e('Select All'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column check-column"><input type="checkbox" /></th>
		<th scope="col" class="manage-column"><?php _e('Select All'); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
<?php
	foreach ( (array) $plugins as $plugin_file => $plugin_data) {
		$info = plugins_api('plugin_information', array('slug' => $plugin_data->update->slug ));
		// Get plugin compat for running version of WordPress.
		if ( isset($info->tested) && version_compare($info->tested, $cur_wp_version, '>=') ) {
			$compat = '<br />' . sprintf(__('Compatibility with WordPress %1$s: 100%% (according to its author)'), $cur_wp_version);
		} elseif ( isset($info->compatibility[$cur_wp_version][$plugin_data->update->new_version]) ) {
			$compat = $info->compatibility[$cur_wp_version][$plugin_data->update->new_version];
			$compat = '<br />' . sprintf(__('Compatibility with WordPress %1$s: %2$d%% (%3$d "works" votes out of %4$d total)'), $cur_wp_version, $compat[0], $compat[2], $compat[1]);
		} else {
			$compat = '<br />' . sprintf(__('Compatibility with WordPress %1$s: Unknown'), $cur_wp_version);
		}
		// Get plugin compat for updated version of WordPress.
		if ( $core_update_version ) {
			if ( isset($info->compatibility[$core_update_version][$plugin_data->update->new_version]) ) {
				$update_compat = $info->compatibility[$core_update_version][$plugin_data->update->new_version];
				$compat .= '<br />' . sprintf(__('Compatibility with WordPress %1$s: %2$d%% (%3$d "works" votes out of %4$d total)'), $core_update_version, $update_compat[0], $update_compat[2], $update_compat[1]);
			} else {
				$compat .= '<br />' . sprintf(__('Compatibility with WordPress %1$s: Unknown'), $core_update_version);
			}
		}
		// Get the upgrade notice for the new plugin version.
		if ( isset($plugin_data->update->upgrade_notice) ) {
			$upgrade_notice = '<br />' . strip_tags($plugin_data->update->upgrade_notice);
		} else {
			$upgrade_notice = '';
		}
		echo "
	<tr class='active'>
		<th scope='row' class='check-column'><input type='checkbox' name='checked[]' value='" . esc_attr($plugin_file) . "' /></th>
		<td class='plugin-title'><strong>{$plugin_data->Name}</strong>" . sprintf(__('You are running version %1$s. Update to %2$s.'), $plugin_data->Version, $plugin_data->update->new_version) . $compat . $upgrade_notice . "</td>
	</tr>";
	}
?>
	</tbody>
</table>
<p><input id="upgrade-plugins-2" class="button" type="submit" value="<?php esc_attr_e('Update Plugins'); ?>" name="upgrade" /></p>
</form>
<?php
}

function list_theme_updates() {
	$themes = get_theme_updates();
	if ( empty($themes) )
		return;

	$form_action = 'update-core.php?action=do-theme-upgrade';

?>
<h3><?php _e('Themes'); ?></h3>
<p><?php _e('The following themes have new versions available. Check the ones you want to update and then click "Update Themes".'); ?></p>
<p><?php _e('<strong>Please Note:</strong> Any customizations you have made to the Themes files will be lost. Please consider using <a href="%s">Child Themes</a> for modifications.'); ?></p>
<form method="post" action="<?php echo $form_action; ?>" name="upgrade-themes" class="upgrade">
<?php wp_nonce_field('upgrade-core'); ?>
<p><input id="upgrade-themes" class="button" type="submit" value="<?php esc_attr_e('Update Themes'); ?>" name="upgrade" /></p>
<table class="widefat" cellspacing="0" id="update-themes-table">
	<thead>
	<tr>
		<th scope="col" class="manage-column check-column"><input type="checkbox" /></th>
		<th scope="col" class="manage-column"><?php _e('Theme'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column check-column"><input type="checkbox" /></th>
		<th scope="col" class="manage-column"><?php _e('Theme'); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
<?php
	foreach ( (array) $themes as $stylesheet => $theme_data) {
		$screenshot = $theme_data->{'Theme Root URI'} . '/' . $stylesheet . '/' . $theme_data->Screenshot;
		
		echo "
	<tr class='active'>
		<th scope='row' class='check-column'><input type='checkbox' name='checked[]' value='" . esc_attr($stylesheet) . "' /></th>
		<td class='plugin-title'><img src='$screenshot' width='64' height='64' style='float:left; padding: 5px' /><strong>{$theme_data->Name}</strong>" .  sprintf(__('You are running version %1$s. Update to %2$s.'), $theme_data->Version, $theme_data->update['new_version']) . "</td>
	</tr>";
	}
?>
	</tbody>
</table>
<p><input id="upgrade-themes-2" class="button" type="submit" value="<?php esc_attr_e('Update Themes'); ?>" name="upgrade" /></p>
</form>
<?php
}

/**
 * Upgrade WordPress core display.
 *
 * @since 2.7
 *
 * @return null
 */
function do_core_upgrade( $reinstall = false ) {
	global $wp_filesystem;

	if ( $reinstall )
		$url = 'update-core.php?action=do-core-reinstall';
	else
		$url = 'update-core.php?action=do-core-upgrade';
	$url = wp_nonce_url($url, 'upgrade-core');
	if ( false === ($credentials = request_filesystem_credentials($url, '', false, ABSPATH)) )
		return;

	$version = isset( $_POST['version'] )? $_POST['version'] : false;
	$locale = isset( $_POST['locale'] )? $_POST['locale'] : 'en_US';
	$update = find_core_update( $version, $locale );
	if ( !$update )
		return;


	if ( ! WP_Filesystem($credentials, ABSPATH) ) {
		request_filesystem_credentials($url, '', true, ABSPATH); //Failed to connect, Error and request again
		return;
	}
?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Update WordPress'); ?></h2>
<?php
	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
			show_message($message);
		echo '</div>';
		return;
	}

	if ( $reinstall )
		$update->response = 'reinstall';

	$result = wp_update_core($update, 'show_message');

	if ( is_wp_error($result) ) {
		show_message($result);
		if ('up_to_date' != $result->get_error_code() )
			show_message( __('Installation Failed') );
	} else {
		show_message( __('WordPress updated successfully') );
		show_message( '<strong>' . __('Actions:') . '</strong> <a href="' . esc_url( admin_url() ) . '">' . __('Go to Dashboard') . '</a>' );
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
	wp_redirect( wp_nonce_url('update-core.php?action=upgrade-core', 'upgrade-core') );
}

function do_undismiss_core_update() {
	$version = isset( $_POST['version'] )? $_POST['version'] : false;
	$locale = isset( $_POST['locale'] )? $_POST['locale'] : 'en_US';
	$update = find_core_update( $version, $locale );
	if ( !$update )
		return;
	undismiss_core_update( $version, $locale );
	wp_redirect( wp_nonce_url('update-core.php?action=upgrade-core', 'upgrade-core') );
}

function no_update_actions($actions) {
	return '';
}

$action = isset($_GET['action']) ? $_GET['action'] : 'upgrade-core';

$upgrade_error = false;
if ( 'do-plugin-upgrade' == $action && !isset($_GET['plugins']) && !isset($_POST['checked']) ) {
	$upgrade_error = true;
	$action = 'upgrade-core';
}

$title = __('Upgrade WordPress');
$parent_file = 'tools.php';

if ( 'upgrade-core' == $action ) {
	wp_version_check();
	require_once('admin-header.php');
	core_upgrade_preamble();
} elseif ( 'do-core-upgrade' == $action || 'do-core-reinstall' == $action ) {
	check_admin_referer('upgrade-core');

	// do the (un)dismiss actions before headers,
	// so that they can redirect
	if ( isset( $_POST['dismiss'] ) )
		do_dismiss_core_update();
	elseif ( isset( $_POST['undismiss'] ) )
		do_undismiss_core_update();

	require_once('admin-header.php');
	if ( 'do-core-reinstall' == $action )
		$reinstall = true;
	else
		$reinstall = false;

	if ( isset( $_POST['upgrade'] ) )
		do_core_upgrade($reinstall);

} elseif ( 'do-plugin-upgrade' == $action ) {
	check_admin_referer('upgrade-core');

	if ( isset( $_GET['plugins'] ) ) {
		$plugins = explode( ',', $_GET['plugins'] );
	} elseif ( isset( $_POST['checked'] ) ) {
		$plugins = (array) $_POST['checked'];
	} else {
		wp_redirect('update-core.php');
		exit;
	}

	$url = 'update.php?action=update-selected&plugins=' . urlencode(implode(',', $plugins));
	$url = wp_nonce_url($url, 'bulk-update-plugins');

	$title = __('Update Plugins');

	require_once('admin-header.php');
	echo '<div class="wrap">';
	screen_icon('plugins');
	echo '<h2>' . esc_html__('Update Plugins') . '</h2>';
	echo "<iframe src='$url' style='width: 100%; height:100%; min-height:850px;'></iframe>";
	echo '</div>';
} elseif ( 'do-theme-upgrade' == $action ) {
	check_admin_referer('upgrade-core');

	if ( isset( $_GET['themes'] ) ) {
		$themes = explode( ',', $_GET['themes'] );
	} elseif ( isset( $_POST['checked'] ) ) {
		$themes = (array) $_POST['checked'];
	} else {
		wp_redirect('update-core.php');
		exit;
	}

	$url = 'update.php?action=update-selected-themes&themes=' . urlencode(implode(',', $themes));
	$url = wp_nonce_url($url, 'bulk-update-themes');

	$title = __('Update Themes');

	require_once('admin-header.php');
	echo '<div class="wrap">';
	screen_icon('themes');
	echo '<h2>' . esc_html__('Update Themes') . '</h2>';
	echo "<p>@TODO: Sorry, This part of the functionality hasnt been written yet.</p>";
	echo "<iframe src='$url' style='width: 100%; height:100%; min-height:850px;'></iframe>";
	echo '</div>';
}

include('admin-footer.php');

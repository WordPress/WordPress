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
	global $wp_local_package;
	$version_string = ('en_US' == $update->locale && 'en_US' == get_locale() ) ?
			$update->current : sprintf("%s&ndash;<strong>%s</strong>", $update->current, $update->locale);
	$current = false;
	if ( !isset($update->response) || 'latest' == $update->response )
		$current = true;
	$submit = __('Upgrade Automatically');
	$form_action = 'update-core.php?action=do-core-upgrade';
	if ( 'development' == $update->response ) {
		$message = __('You are using a development version of WordPress.  You can upgrade to the latest nightly build automatically or download the nightly build and install it manually:');
		$download = __('Download nightly build');
	} else {
		if ( $current ) {
			$message = sprintf(__('You have the latest version of WordPress. You do not need to upgrade. However, if you want to re-install version %s, you can do so automatically or download the package and re-install manually:'), $version_string);
			$submit = __('Re-install Automatically');
			$form_action = 'update-core.php?action=do-core-reinstall';
		} else {
			$message = 	sprintf(__('You can upgrade to version %s automatically or download the package and install it manually:'), $version_string);
		}
		$download = sprintf(__('Download %s'), $version_string);
	}

	echo '<p>';
	echo $message;
	echo '</p>';
	echo '<form method="post" action="' . $form_action . '" name="upgrade" class="upgrade">';
	wp_nonce_field('upgrade-core');
	echo '<p>';
	echo '<input id="upgrade" class="button" type="submit" value="' . esc_attr($submit) . '" name="upgrade" />&nbsp;';
	echo '<input name="version" value="'. esc_attr($update->current) .'" type="hidden"/>';
	echo '<input name="locale" value="'. esc_attr($update->locale) .'" type="hidden"/>';
	echo '<a href="' . esc_url($update->package) . '" class="button">' . $download . '</a>&nbsp;';
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
	$updates = get_core_updates();
?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Upgrade WordPress'); ?></h2>
<?php
	if ( !isset($updates[0]->response) || 'latest' == $updates[0]->response ) {
		echo '<h3>';
		_e('You have the latest version of WordPress. You do not need to upgrade');
		echo '</h3>';
	} else {
		echo '<div class="updated fade"><p>';
		_e('<strong>Important:</strong> before upgrading, please <a href="http://codex.wordpress.org/WordPress_Backups">backup your database and files</a>.');
		echo '</p></div>';

		echo '<h3 class="response">';
		_e( 'There is a new version of WordPress available for upgrade' );
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
	echo '</div>';

	list_plugin_updates();
	//list_theme_updates();
}

function list_plugin_updates() {
	$plugins = get_plugin_updates();
	if ( empty($plugins) )
		return;
	$form_action = '';
	?>
<h3><?php _e('Plugins'); ?></h3>
<form method="post" action="<?php $form_action; ?>" name="upgrade-plugins" class="upgrade">
<?php wp_nonce_field('upgrade-core'); ?>
<p><input id="upgrade-plugins" class="button" type="submit" value="<?php esc_attr_e('Upgrade Plugins'); ?>" name="upgrade" /></p>
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
		echo "
	<tr class='active'>
		<th scope='row' class='check-column'><input type='checkbox' name='checked[]' value='" . esc_attr($plugin_file) . "' /></th>
		<td class='plugin-title'><strong>{$plugin_data->Name}</strong>" . sprintf(__('You are running version %1$s. Upgrade to %2$s.'), $plugin_data->Version, $plugin_data->update->new_version) . "</td>
	</tr>";
	}
?>
	</tbody>
</table>
<p><input id="upgrade-plugins-2" class="button" type="submit" value="<?php esc_attr_e('Upgrade Plugins'); ?>" name="upgrade" /></p>
</form>
<?php
}

function list_theme_updates() {
	$themes = get_theme_updates();
	if ( empty($themes) )
		return;
?>
<h3><?php _e('Themes'); ?></h3>
<table class="widefat" cellspacing="0" id="update-themes-table">
	<thead>
	<tr>
		<th scope="col" class="manage-column check-column"><input type="checkbox" /></th>
		<th scope="col" class="manage-column"><?php _e('Name'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column check-column"><input type="checkbox" /></th>
		<th scope="col" class="manage-column"><?php _e('Name'); ?></th>
	</tr>
	</tfoot>
	<tbody class="plugins">
<?php
	foreach ( (array) $themes as $stylesheet => $theme_data) {
		echo "
	<tr class='active'>
		<th scope='row' class='check-column'><input type='checkbox' name='checked[]' value='" . esc_attr($stylesheet) . "' /></th>
		<td class='plugin-title'><strong>{$theme_data->Name}</strong></td>
	</tr>";
	}
?>
	</tbody>
</table>
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
	<h2><?php _e('Upgrade WordPress'); ?></h2>
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

$action = isset($_GET['action']) ? $_GET['action'] : 'upgrade-core';

if ( 'upgrade-core' == $action ) {
	wp_version_check();
	$title = __('Upgrade WordPress');
	$parent_file = 'tools.php';
	require_once('admin-header.php');
	core_upgrade_preamble();
	include('admin-footer.php');
} elseif ( 'do-core-upgrade' == $action || 'do-core-reinstall' == $action ) {
	check_admin_referer('upgrade-core');
	$title = __('Upgrade WordPress');
	$parent_file = 'tools.php';
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
	include('admin-footer.php');

}?>

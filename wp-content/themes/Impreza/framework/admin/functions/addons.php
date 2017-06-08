<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! current_user_can( 'install_plugins' ) ) {
	return;
}

$js_composer_path = 'js_composer';
$installed_plugins = get_plugins();
$keys = array_keys( $installed_plugins );

foreach ( $keys as $key ) {
	if ( preg_match( '|^' . $js_composer_path . '/|', $key ) ) {
		$js_composer_path = $key;
		break;
	}
}

if ( ( ! get_option( 'us_dismiss_addons_install_notice' ) ) AND ( ! isset( $installed_plugins[ $js_composer_path ] ) ) ) {
	add_action( 'admin_notices', 'us_js_composer_install_admin_notice' );
}
if ( ( ! get_option( 'us_dismiss_addons_activate_notice' ) ) AND ( isset( $installed_plugins[ $js_composer_path ] ) ) AND  is_plugin_inactive( $js_composer_path ) ) {
	add_action( 'admin_notices', 'us_js_composer_activate_admin_notice' );
}


function us_js_composer_install_admin_notice() {
	?>
	<div class="notice notice-warning us-addons-notice for-installing is-dismissible" >
		<p><?php echo sprintf( __( 'This theme requires %s plugin to be installed.', 'us' ), '<strong>Visual Composer</strong>' ); ?>
		<a href="<?php echo admin_url('admin.php?page=us-addons') ?>"><?php _e( 'Go to Install', 'us' ); ?></a></p>
	</div>
	<?php
}

function us_js_composer_activate_admin_notice() {
	?>
	<div class="notice notice-warning us-addons-notice for-activating is-dismissible" >
		<p><?php echo sprintf( __( 'This theme requires %s plugin to be activated.', 'us' ), '<strong>Visual Composer</strong>' ); ?>
		<a href="<?php echo admin_url('admin.php?page=us-addons') ?>"><strong><?php _e( 'Go to Activate', 'us' ); ?></strong></a></p>
	</div>
	<?php
}

add_action( 'admin_print_scripts', 'us_admin_addons_assets', 99 );

function us_admin_addons_assets() {
?>
<script>
jQuery(document).on( 'click', '.us-addons-notice .notice-dismiss', function() {
	var $notice = jQuery(this).closest('.us-addons-notice'),
		pluginAction = ($notice.hasClass('for-activating'))?'activate':'install';
	jQuery.ajax({
		url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
		data: {
			action: 'us_dismiss_addons_notice',
			pluginAction: pluginAction
		}
	});

});
</script>
<?php
}

add_action( 'wp_ajax_us_dismiss_addons_notice', 'us_dismiss_addons_notice' );

function us_dismiss_addons_notice() {
	if ( $_GET['pluginAction'] == 'activate') {
		update_option( 'us_dismiss_addons_activate_notice', 1 );
	} elseif ( $_GET['pluginAction'] == 'install') {
		update_option( 'us_dismiss_addons_install_notice', 1 );
	}


}


add_action( 'admin_menu', 'us_add_addons_page', 20 );
function us_add_addons_page() {
	add_submenu_page( 'us-theme-options', US_THEMENAME . ': ' .  __( 'Addons', 'us' ), __( 'Addons', 'us' ), 'manage_options', 'us-addons', 'us_addons_page', 11 );
}

function us_addons_page() {
	$plugins = us_config( 'addons' );

	foreach( $plugins as $i => $plugin ) {
		if ( empty( $plugins[$i]['version'] ) OR empty( $plugins[$i]['source'] ) ) {
			$plugins = us_api_addons( $plugins, TRUE );
			break;
		}
	}

	$installed_plugins = get_plugins();
	$us_template_directory_uri = get_template_directory_uri();
	?>
	<div class="wrap about-wrap us-addons">

		<h1><?php echo US_THEMENAME . '<strong> ' .  __( 'Addons', 'us' ); ?></strong></h1>

		<div class="about-text"><?php _e( 'Premium plugins which are available for free with the theme!', 'us'); ?></div>

		<div class="us-addons-list">
		<?php foreach( $plugins as $plugin ) {

			$keys = array_keys( get_plugins() );

			$plugin['file_path'] = $plugin['slug'];
			foreach ( $keys as $key ) {
				if ( preg_match( '|^' . $plugin['slug'] . '/|', $key ) ) {
					$plugin['file_path'] = $key;
					break;
				}
			}

			$classes = ' ' . $plugin['slug'];
			$link_classes = '';
			$link_atts = '';
			$action = '';
			$link = '';
			if ( is_plugin_active( $plugin['file_path'] ) ) {
				$classes .= ' status_active';
				$status = __( 'Installed & Activated', 'us' );

			} elseif ( ! isset( $installed_plugins[ $plugin['file_path'] ] ) ) {
				if ( $plugin['source'] == '' ) {
					$classes .= ' status_locked';
					$status = __( 'Plugin Locked', 'us' );
					$action = __( 'Activate Theme to Unlock', 'us' );
					$link = esc_url( add_query_arg( array(
						'page' => urlencode( 'us-home' ),
					), network_admin_url( 'admin.php' ) ) . '#activation' );

				} else {
					$classes .= ' status_notinstalled';
					$status = __( 'Available to Install', 'us' );
					$action = __( 'Install Plugin', 'us' );
					$link = 'javascript:void(0);';
					$link_classes .= ' button-primary action-button';
					$link_atts = ' data-plugin="' . $plugin['slug'] . '" data-action="install"';
				}

			} elseif ( is_plugin_inactive( $plugin['file_path'] ) ) {
				$classes .= ' status_notactive';
				$status = __( 'Installed But Not Activated', 'us' );
				$action = __( 'Activate Plugin', 'us' );
				$link = 'javascript:void(0);';
				$link_classes .= ' button-primary action-button';
				$link_atts = ' data-plugin="' . $plugin['slug'] . '" data-action="activate"';

			}
		?>
			<div class="us-addon<?php echo $classes; ?>">
				<div class="us-addon-content">
					<span class="us-addon-icon"><img src="<?php echo $us_template_directory_uri . '/framework/admin/img/' . $plugin['slug']; ?>.png" alt=""></span>
					<h3 class="us-addon-title"><?php echo $plugin['name'] ?></h3>
					<p class="us-addon-desc"><?php echo $plugin['description']; ?></p>
				</div>
				<div class="us-addon-control">
					<div class="us-addon-status"><i class="g-preloader type_1"></i><span><?php echo $status; ?></span></div>
					<?php if ( $action != '' AND $link != '' ) { ?>
					<a class="button<?php echo $link_classes; ?>" href="<?php echo $link; ?>" <?php echo $link_atts; ?>><?php echo $action; ?></a>
					<?php } ?>
				</div>
			</div>

		<?php } ?>
		</div>

	</div>
	<script>
		jQuery(function($){
			var isRunning = false;
			$('.action-button').click(function(){
				if (isRunning) return;
				isRunning = true;
				$('.us-addons-list').addClass('disable-buttons');
				var plugin = $(this).attr('data-plugin'),
					action = $(this).attr('data-action'),
					$tile = $(this).closest('.us-addon'),
					$status = $tile.find('.us-addon-status > span'),
					$button = $(this);

				$button.hide();
				$tile.removeClass(function (index, css) {
					return (css.match (/(^|\s)status_\S+/g) || []).join(' ');
				});
				if (action == 'install'){
					$tile.addClass('status_installing');
					$status.html('<?php esc_js(  _e( 'Installing...', 'us' ) ); ?>');
				} else {
					$tile.addClass('status_activating');
					$status.html('<?php esc_js( _e( 'Activating...', 'us' ) ); ?>');
				}
				$.ajax({
					type: 'POST',
					url: '<?php echo admin_url('admin-ajax.php'); ?>',
					data: {
						action: 'us_ajax_addons_' + action,
						plugin: plugin
					},
					success: function(data) {
						isRunning = false;
						$('.us-addons-list').removeClass('disable-buttons');
						$tile.removeClass(function (index, css) {
							return (css.match (/(^|\s)status_\S+/g) || []).join(' ');
						});
						if (data != undefined && data.success) {
							$tile.addClass('status_active');
							$status.html('<?php esc_js( _e( 'Installed & Activated', 'us' ) ); ?>');
						} else {
							$tile.addClass('status_error');
							if (data != undefined && data.data != undefined && data.data.message != undefined) {
								$status.html(data.data.message);
							} else {
								$status.html('<?php echo  esc_js( us_translate_with_external_domain( 'An error has occurred. Please reload the page and try again.' ) ); ?>');
							}
						}
					}
				});
				return false;
			});
		});
	</script>

	<?php
}


add_action( 'wp_ajax_us_ajax_addons_install', 'us_ajax_addons_install' );
add_action( 'wp_ajax_us_ajax_addons_activate', 'us_ajax_addons_activate' );

function us_ajax_addons_activate() {

	if ( ! isset( $_POST['plugin'] ) || ! $_POST['plugin'] ) {
		wp_send_json_error( array( 'message' =>  us_translate_with_external_domain( 'An error has occurred. Please reload the page and try again.' ) ) );
	}

	$result = us_activate_plugin();

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	wp_send_json_success( array( 'plugin' => $_POST['plugin'] ) );

}

function us_activate_plugin() {
	if ( empty( $_POST['plugin'] ) ) {
		return false;
	}

	$plugins = us_config( 'addons', array() );

	if ( empty( $plugins ) ) {
		return false;
	}

	$_plugins = array();
	foreach ( $plugins as $i => $plugin ) {
		$_plugins[ $plugin['slug'] ] = $plugin;
	}

	$plugins = $_plugins;

	$slug = urldecode( $_POST['plugin'] );

	if ( ! isset( $plugins[ $slug ] ) ) {
		return false;
	}

	$plugin = $plugins[ $slug ];

	$plugin_data = get_plugins( '/' . $plugin['slug'] ); // Retrieve all plugins.
	$plugin_file = array_keys( $plugin_data ); // Retrieve all plugin files from installed plugins.

	$plugin_to_activate = $plugin['slug'] . '/' . $plugin_file[0]; // Match plugin slug with appropriate plugin file.
	ob_start();
	$activate = activate_plugin( $plugin_to_activate ); // Activate the plugin.
	ob_get_clean();

	if ( is_wp_error( $activate ) ) {
		return $activate;
	} else {
		return true;
	}
}

function us_ajax_addons_install() {

	if ( ! isset( $_POST['plugin'] ) || ! $_POST['plugin'] ) {
		wp_send_json_error( array( 'message' => us_translate_with_external_domain( 'An error has occurred. Please reload the page and try again.' ) ) );
	}

	$result = us_install_plugin();

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	wp_send_json_success( array( 'plugin' => $_POST['plugin'] ) );

}

function us_install_plugin() {
	if ( empty( $_POST['plugin'] ) ) {
		return false;
	}

	$plugins = us_config( 'addons', array() );

	foreach( $plugins as $i => $plugin ) {
		if ( empty( $plugins[$i]['version'] ) OR empty( $plugins[$i]['source'] ) ) {
			unset( $plugins[$i] );
		}
	}

	if ( empty( $plugins ) ) {
		return false;
	}

	$_plugins = array();
	foreach ( $plugins as $i => $plugin ) {
		$_plugins[ $plugin['slug'] ] = $plugin;
	}

	$plugins = $_plugins;

	$slug = urldecode( $_POST['plugin'] );

	if ( ! isset( $plugins[ $slug ] ) ) {
		return false;
	}

	$plugin = $plugins[ $slug ];

	if ( file_exists( WP_PLUGIN_DIR . '/' . $slug ) ) {
		return us_activate_plugin();
	}

	if ( ! filesystem_permission_check() ) {
		return new WP_Error( 'us-addons', __( 'Please adjust file permissions to allow plugins installation', 'us' ) );
	}

	$us_template_directory = get_template_directory();

	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once $us_template_directory . '/framework/admin/functions/addons-update-skin.php';

	$skin = new US_Plugin_Upgrader_Skin( array( 'plugin' => $slug ) );
	$upgrader = new Plugin_Upgrader( $skin );
	$install_result = $upgrader->install( $plugin['source'] );

	if ( is_wp_error( $install_result ) ) {
		return $install_result;
	}

	if ( ! $install_result ) {
		return new WP_Error( 'plugin_error', us_translate_with_external_domain( 'An error has occurred. Please reload the page and try again.' ) );
	}

	ob_start();
	$activate = activate_plugin( $upgrader->plugin_info() );
	ob_get_clean();
	if ( is_wp_error( $activate ) ) {
		return $activate;
	}

	return $skin->result;

}

function filesystem_permission_check() {
	ob_start();
	$creds = request_filesystem_credentials( '', '', false, false, null );
	ob_get_clean();

	// Abort if permissions were not available.
	if ( ! WP_Filesystem( $creds ) )
		return false;

	return true;
}

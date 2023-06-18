<?php
/**
 * Load lite-specific scripts here.
 *
 * @package WPCode
 */

add_action( 'admin_enqueue_scripts', 'wpcode_admin_scripts_global_lite' );
add_action( 'admin_head', 'wpcode_listen_for_deploy_message' );

/**
 * Load version-specific global scripts.
 *
 * @return void
 */
function wpcode_admin_scripts_global_lite() {
	// Don't load global admin scripts if headers & footers mode is enabled.
	if ( wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
		return;
	}
	wpcode_admin_scripts_global();
}

/**
 * This is loaded after the plugin is activated and if the installation process was initiated
 * from the WPCode Library site it will redirect the user to the appropriate page to continue
 * that process.
 *
 * @return void
 */
function wpcode_listen_for_deploy_message() {
	// Load this only in the plugins list.
	$screen  = get_current_screen();
	$screens = array(
		'plugins',
		'plugin-install',
	);
	if ( ! isset( $screen->id ) || ! in_array( $screen->id, $screens, true ) ) {
		return;
	}
	$click_page = add_query_arg(
		array(
			'page' => 'wpcode-click',
		),
		admin_url( 'admin.php' )
	)
	?>
	<script>
		if ( window.opener ) {
			window.opener.postMessage(
				'wpcode-plugin-installed',
				'<?php echo esc_url( wpcode()->library_auth->library_url ); ?>'
			);
		}
		window.addEventListener(
			'message',
			( event ) => {
				if ( !event.isTrusted || '<?php echo esc_url( wpcode()->library_auth->library_url ); ?>' !== event.origin || 'wpcode-show-connect' !== event.data ) {
					return;
				}
				window.location.href = '<?php echo esc_url( $click_page ); ?>&message=wpcode-deploy';
			},
			false
		);
	</script>
	<?php
}

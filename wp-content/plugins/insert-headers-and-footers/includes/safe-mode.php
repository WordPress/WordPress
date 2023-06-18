<?php
/**
 * Safe mode query var logic.
 *
 * @package WPCode
 */

add_action( 'plugins_loaded', 'wpcode_maybe_enable_safe_mode' );
add_filter( 'wpcode_do_auto_insert', 'wpcode_maybe_prevent_execution' );

/**
 * Simple check to see if we should be adding safe-mode logic.
 *
 * @return void
 */
function wpcode_maybe_enable_safe_mode() {
	if ( ! isset( $_GET['wpcode-safe-mode'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	// If we're in safe mode, let's make sure all URLs keep the param until we are safe to get out.
	add_filter( 'home_url', 'wpcode_keep_safe_mode' );
	add_filter( 'admin_url', 'wpcode_keep_safe_mode' );
	add_filter( 'site_url', 'wpcode_keep_safe_mode_login', 10, 3 );
	// The admin menu doesn't offer a hook to change all the menu links so we do it with JS.
	add_action( 'admin_footer', 'wpcode_keep_safe_mode_admin_menu' );
	// Show a notice informing the user we're in safe mode and offer a way to get out.
	add_action( 'admin_notices', 'wpcode_safe_mode_notice' );
	add_action( 'wpcode_admin_notices', 'wpcode_safe_mode_notice' );
}

/**
 * Make sure the URL keeps the safe mode variable.
 *
 * @param string $url The home or admin base URL.
 *
 * @return string
 */
function wpcode_keep_safe_mode( $url ) {
	return add_query_arg( 'wpcode-safe-mode', 1, $url );
}

/**
 * Force safe mode to all URLs displayed in the admin so we can keep navigating
 * using safe mode as there's no hook in WP to change the main admin menu.
 *
 * @return void
 */
function wpcode_keep_safe_mode_admin_menu() {
	// There's no reliable way to filter all the admin menu links so we have to force them via JS.
	// There's also a notice being added to allow users to "exit" safe mode.
	?>
	<script type="text/javascript">
		[...document.querySelectorAll( 'a:not(.wpcode-safe-mode)' )].forEach( e => {
			const url = new URL( e.href );
			url.searchParams.set( 'wpcode-safe-mode', '1' );
			e.href = url.toString();
		} );
	</script>
	<?php
}

/**
 * Show a notice informing the user we're in safe mode and offer a way to get out.
 *
 * @return void
 */
function wpcode_safe_mode_notice() {
	?>
	<div class="notice notice-warning">
		<p><?php esc_html_e( 'WPCode is in Safe Mode which means no snippets are getting executed. Please disable any snippets that have caused errors and when done click the button below to exit safe mode.', 'insert-headers-and-footers' ); ?></p>
		<p><?php esc_html_e( 'The link will open in a new window so if you are still encountering issues you safely can return to this tab and make further adjustments', 'insert-headers-and-footers' ); ?></p>
		<p>
			<a class="button button-secondary wpcode-safe-mode" href="<?php echo esc_url( remove_query_arg( 'wpcode-safe-mode' ) ); ?>" target="_blank"><?php esc_html_e( 'Exit safe mode', 'insert-headers-and-footers' ); ?></a>
		</p>
	</div>
	<?php
}

/**
 * Let's check if we're in the admin or if the current user can manage
 * snippets before allowing them to see the site with snippets disabled.
 *
 * @param bool $execute Execute snippets or not.
 *
 * @return mixed
 */
function wpcode_maybe_prevent_execution( $execute ) {
	if ( ! isset( $_GET['wpcode-safe-mode'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return $execute;
	}

	if ( wpcode_is_wplogin() || current_user_can( 'wpcode_activate_snippets' ) ) {
		return false;
	}

	return $execute;
}

/**
 * Checks schema passed to site_url and adds the safe mode query param
 * so we can login using safe mode.
 *
 * @param string $url The site_url already processed.
 * @param string $path The path that was added to the URL.
 * @param string $scheme The scheme that was requested.
 *
 * @return string
 */
function wpcode_keep_safe_mode_login( $url, $path, $scheme ) {
	if ( 'login_post' !== $scheme ) {
		return $url;
	}

	return add_query_arg( 'wpcode-safe-mode', 1, $url );
}

/**
 * Helper function that checks if we are on the login screen
 * to allow admins to attempt to log in and disable snippets
 * without having to edit code.
 *
 * @return bool
 */
function wpcode_is_wplogin() {
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return false;
	}

	return false !== stripos( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), strrchr( wp_login_url(), '/' ) );
}

<?php
/**
 * PluginsInstaller
 *
 * Installer to allow plugin installation via URL query.
 */

namespace Automattic\WooCommerce\Admin;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\API\Plugins;
use Automattic\WooCommerce\Admin\Features\TransientNotices;

/**
 * Class PluginsInstaller
 */
class PluginsInstaller {

	/**
	 * Constructor
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'possibly_install_activate_plugins' ) );
	}

	/**
	 * Check if an install or activation is being requested via URL query.
	 */
	public static function possibly_install_activate_plugins() {
		/* phpcs:disable WordPress.Security.NonceVerification.Recommended */
		if (
			! isset( $_GET['plugin_action'] ) ||
			! isset( $_GET['plugins'] ) ||
			! current_user_can( 'install_plugins' ) ||
			! isset( $_GET['nonce'] )
		) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_GET['nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'install-plugin' ) ) {
			wp_nonce_ays( 'install-plugin' );
		}

		$plugins       = sanitize_text_field( wp_unslash( $_GET['plugins'] ) );
		$plugin_action = sanitize_text_field( wp_unslash( $_GET['plugin_action'] ) );
		/* phpcs:enable WordPress.Security.NonceVerification.Recommended */

		$plugins_api     = new Plugins();
		$install_result  = null;
		$activate_result = null;

		switch ( $plugin_action ) {
			case 'install':
				$install_result = $plugins_api->install_plugins( array( 'plugins' => $plugins ) );
				break;
			case 'activate':
				$activate_result = $plugins_api->activate_plugins( array( 'plugins' => $plugins ) );
				break;
			case 'install-activate':
				$install_result  = $plugins_api->install_plugins( array( 'plugins' => $plugins ) );
				$activate_result = $plugins_api->activate_plugins( array( 'plugins' => implode( ',', $install_result['data']['installed'] ) ) );
				break;
		}

		self::cache_results( $plugins, $install_result, $activate_result );
		self::redirect_to_referer();
	}

	/**
	 * Display the results of installation and activation on the page.
	 *
	 * @param string $plugins Comma separated list of plugins.
	 * @param array  $install_result Result of installation.
	 * @param array  $activate_result Result of activation.
	 */
	public static function cache_results( $plugins, $install_result, $activate_result ) {
		if ( ! $install_result && ! $activate_result ) {
			return;
		}

		if ( is_wp_error( $install_result ) || is_wp_error( $activate_result ) ) {
			$message = $activate_result ? $activate_result->get_error_message() : $install_result->get_error_message();
		} else {
			$message = $activate_result ? $activate_result['message'] : $install_result['message'];
		}

		TransientNotices::add(
			array(
				'user_id' => get_current_user_id(),
				'id'      => 'plugin-installer-' . str_replace( ',', '-', $plugins ),
				'status'  => 'success',
				'content' => $message,
			)
		);
	}

	/**
	 * Redirect back to the referring page if one exists.
	 */
	public static function redirect_to_referer() {
		$referer = wp_get_referer();
		if ( $referer && 0 !== strpos( $referer, wp_login_url() ) ) {
			wp_safe_redirect( $referer );
			exit();
		}

		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		$url = remove_query_arg( 'plugin_action', wp_unslash( $_SERVER['REQUEST_URI'] ) ); // phpcs:ignore sanitization ok.
		$url = remove_query_arg( 'plugins', $url );
		wp_safe_redirect( $url );
		exit();
	}
}

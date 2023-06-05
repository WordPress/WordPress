<?php
/**
 * WC_CLI_COM_Command class file.
 *
 * @package WooCommerce\CLI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Plugin_Command' ) ) {
	exit;
}

/**
 * Allows to interact with extensions from WCCOM marketplace via CLI.
 *
 * @version 6.8
 * @package WooCommerce
 */
class WC_CLI_COM_Extension_Command extends Plugin_Command {
	/**
	 * Registers a commands for managing WooCommerce.com extensions.
	 */
	public static function register_commands() {
		WP_CLI::add_command( 'wc com extension', 'WC_CLI_COM_Extension_Command' );
	}

	/**
	 * Installs one or more plugins from wccom marketplace.
	 *
	 * ## OPTIONS
	 *
	 * <extension>...
	 * : One or more plugins to install. Accepts a plugin slug.
	 *
	 * [--force]
	 * : If set, the command will overwrite any installed version of the plugin, without prompting
	 * for confirmation.
	 *
	 * [--activate]
	 * : If set, the plugin will be activated immediately after install.
	 *
	 * [--activate-network]
	 * : If set, the plugin will be network activated immediately after install
	 *
	 * [--insecure]
	 * : Retry downloads without certificate validation if TLS handshake fails. Note: This makes the request vulnerable to a MITM attack.
	 *
	 * ## EXAMPLES
	 *
	 *     # Install the latest version from woocommerce.com and activate
	 *     $ wp wc com extension install automatewoo --activate
	 *     Downloading install package from http://s3.amazonaws.com/bucketname/automatewoo.zip?AWSAccessKeyId=123&Expires=456&Signature=abcdef......
	 *     Using cached file '/home/vagrant/.wp-cli/cache/plugin/automatewoo.zip'...
	 *     Unpacking the package...
	 *     Installing the plugin...
	 *     Plugin installed successfully.
	 *     Activating 'automatewoo'...
	 *     Plugin 'automatewoo' activated.
	 *     Success: Installed 1 of 1 plugins.
	 *
	 *     # Forcefully re-install an installed plugin
	 *     $ wp wc com extension install automatewoo --force
	 *     Downloading install package from http://s3.amazonaws.com/bucketname/automatewoo.zip?AWSAccessKeyId=123&Expires=456&Signature=abcdef...
	 *     Unpacking the package...
	 *     Installing the plugin...
	 *     Removing the old version of the plugin...
	 *     Plugin updated successfully
	 *     Success: Installed 1 of 1 plugins.
	 *
	 * @param array $args WP-CLI positional arguments.
	 * @param array $assoc_args WP-CLI associative arguments.
	 */
	public function install( $args, $assoc_args ) {
		$subscriptions         = WC_Helper_Updater::get_available_extensions_downloads_data();
		$extension             = reset( $args );
		$extension_package_url = null;

		// Remove `--version` as we don't support it.
		unset( $assoc_args['version'] );

		// Filter by slug.
		foreach ( $subscriptions as $subscription ) {
			if ( $subscription['slug'] === $extension && ! is_null( $subscription['package'] ) ) {

				$extension_package_url = $subscription['package'];
				break;
			}
		}

		// No package found.
		if ( is_null( $extension_package_url ) ) {
			WP_CLI::warning( sprintf( 'We couldn\'t find a Subscription for \'%s\'', $extension ) );
			WP_CLI\Utils\report_batch_operation_results( $this->item_type, 'install', count( $args ), 0, 1 );

			return;
		}

		parent::install( array( $extension_package_url ), $assoc_args );
	}
}

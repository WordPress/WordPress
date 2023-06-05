<?php
/**
 * WC_CLI_COM_Command class file.
 *
 * @package WooCommerce\CLI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allows to interact with extensions from WCCOM marketplace via CLI.
 *
 * @version 6.8
 * @package WooCommerce
 */
class WC_CLI_COM_Command {
	const APPLICATION_PASSWORD_SECTION_URL = 'https://woocommerce.com/my-account/#application-passwords';

	/**
	 * Registers a commands for managing WooCommerce.com extensions.
	 */
	public static function register_commands() {
		WP_CLI::add_command( 'wc com extension list', array( 'WC_CLI_COM_Command', 'list_extensions' ) );
		WP_CLI::add_command( 'wc com disconnect', array( 'WC_CLI_COM_Command', 'disconnect' ) );
		WP_CLI::add_command( 'wc com connect', array( 'WC_CLI_COM_Command', 'connect' ) );
	}

	/**
	 * List extensions owned by the connected site
	 *
	 * [--format]
	 * : If set, the command will use the specified format. Possible values are table, json, csv and yaml. By default the table format will be used.
	 *
	 * [--fields]
	 * : If set, the command will show only the specified fields instead of showing all the fields in the output.
	 *
	 * ## EXAMPLES
	 *
	 *     # List extensions owned by the connected site in table format with all the fields
	 *     $ wp wc com extension list
	 *
	 *     # List the product slug of the extension owned by the connected site in csv format
	 *     $ wp wc com extension list --format=csv --fields=product_slug
	 *
	 * @param  array $args  WP-CLI positional arguments.
	 * @param  array $assoc_args  WP-CLI associative arguments.
	 */
	public static function list_extensions( array $args, array $assoc_args ) {
		$data = WC_Helper::get_subscriptions();

		$data = array_values( $data );

		$formatter = new \WP_CLI\Formatter(
			$assoc_args,
			array(
				'product_slug',
				'product_name',
				'auto_renew',
				'expires_on',
				'expired',
				'sites_max',
				'sites_active',
				'maxed',
			)
		);

		$data = array_map(
			function( $item ) {
				$product_slug      = '';
				$product_url_parts = explode( '/', $item['product_url'] );
				if ( count( $product_url_parts ) > 2 ) {
					$product_slug = $product_url_parts[ count( $product_url_parts ) - 2 ];
				}
				return array(
					'product_slug' => $product_slug,
					'product_name' => htmlspecialchars_decode( $item['product_name'] ),
					'auto_renew'   => $item['autorenew'] ? 'On' : 'Off',
					'expires_on'   => gmdate( 'Y-m-d', $item['expires'] ),
					'expired'      => $item['expired'] ? 'Yes' : 'No',
					'sites_max'    => $item['sites_max'],
					'sites_active' => $item['sites_active'],
					'maxed'        => $item['maxed'] ? 'Yes' : 'No',
				);
			},
			$data
		);

		$formatter->display_items( $data );
	}

	/**
	 * ## OPTIONS
	 *
	 * [--yes]
	 * : Do not prompt for confirmation.
	 *
	 * ## EXAMPLES
	 *
	 *     # Disconnect from site.
	 *     $ wp wc com disconnect
	 *
	 *     # Disconnect without prompt for confirmation.
	 *     $ wp wc com disconnect --yes
	 *
	 * @param array $args Positional arguments to include when calling the command.
	 * @param array $assoc_args Associative arguments to include when calling the command.

	 * @return void
	 * @throws \WP_CLI\ExitException If WP_CLI::$capture_exit is true.
	 */
	public static function disconnect( array $args, array $assoc_args ) {
		if ( ! WC_Helper::is_site_connected() ) {
			WP_CLI::error( __( 'Your store is not connected to WooCommerce.com. Run `wp wc com connect` command.', 'woocommerce' ) );
		}

		WP_CLI::confirm( __( 'Are you sure you want to disconnect your store from WooCommerce.com?', 'woocommerce' ), $assoc_args );
		WC_Helper::disconnect();
		WP_CLI::success( __( 'You have successfully disconnected your store from WooCommerce.com', 'woocommerce' ) );
	}

	/**
	 * Connects to WooCommerce.com with application-password.
	 *
	 * [--password]
	 * : If set, password won't be prompt.
	 *
	 * [--force]
	 * : If set, site will be disconnected and a new connection will be forced.
	 *
	 * ## EXAMPLES
	 *
	 *     # Connect to WCCOM using password.
	 *     $ wp wc com connect
	 *
	 *     # force connecting to WCCOM even if site is already connected.
	 *     $ wp wc com connect --force
	 *
	 *     # Pass password to comman.
	 *     $ wp wc com connect --password=PASSWORD
	 *
	 * @param array $args Positional arguments to include when calling the command.
	 * @param array $assoc_args Associative arguments to include when calling the command.
	 *
	 * @return void
	 * @throws \WP_CLI\ExitException If WP_CLI::$capture_exit is true.
	 */
	public static function connect( array $args, array $assoc_args ) {
		$password = \WP_CLI\Utils\get_flag_value( $assoc_args, 'password' );
		$force    = \WP_CLI\Utils\get_flag_value( $assoc_args, 'force', false );

		if ( WC_Helper::is_site_connected() ) {
			if ( $force ) {
				WC_Helper::disconnect();
			} else {
				WP_CLI::error( __( 'Your store is already connected.', 'woocommerce' ) );

				return;
			}
		}

		if ( empty( $password ) ) {
			// translators: %s is the URL for the application-password section in WooCommerce.com.
			WP_CLI::log( sprintf( __( 'If you don\'t have an application password (not your account password), generate a password from %s', 'woocommerce' ), esc_url( self::APPLICATION_PASSWORD_SECTION_URL ) ) );
			$password = self::ask( __( 'Connection password:', 'woocommerce' ) );
		}
		$password = sanitize_text_field( $password );
		if ( empty( $password ) ) {
			// translators: %s is the URL for the application-password section in WooCommerce.com.
			WP_CLI::error( sprintf( __( 'Invalid password. Generate a new one from %s.', 'woocommerce' ), esc_url( self::APPLICATION_PASSWORD_SECTION_URL ) ) );
		}

		$auth = WC_Helper::connect_with_password( $password );
		if ( is_wp_error( $auth ) ) {
			WP_CLI::error( $auth->get_error_message() );
		}

		if ( WC_Helper::is_site_connected() ) {
			WP_CLI::success( __( 'Store connected successfully.', 'woocommerce' ) );
		}
	}

	/**
	 * We are asking a question and returning an answer as a string.
	 *
	 * @param  string $question The question being prompt.
	 *
	 * @return string
	 */
	protected static function ask( $question ) {
		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_fwrite
		// Adding space to question and showing it.
		fwrite( STDOUT, $question . ' ' );

		return trim( fgets( STDIN ) );
		// phpcs:enable
	}
}
